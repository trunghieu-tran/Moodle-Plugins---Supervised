<?php
/**
 * Defines NFA matcher class
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher/nfa_preg_nodes.php');

/**
 * defines a state of an automaton when running
 * used when matching a string
 */
class preg_nfa_processing_state {
    public $state;                             // a reference to the state which automaton is in
    public $matchcnt;                          // the number of characters matched
    public $ismatch;                           // is there a match?
    public $isfullmatch;                       // is the match full
    public $next;                              // the next possible character
    public $left;                              // number of characters left to complete match
    public $subpatt_index_first = array();     // key = subpattern number
    public $subpatt_index_last = array();      // key = subpattern number
    public $subpatt_index_first_old = array(); // indexes of subpatterns defenetly captured. it's used, for example, when matching quantified subpatterns like (...)*
    public $subpatt_index_last_old = array();  // same as previous field
    public $firsttransition;                   // the first transition of a path
    public $backreftransition;                 // != null if the last transition matched is a backreference
    public $backrefmatchlen;                   // length of the last match

    public function __construct(&$_state, $_matchcnt, $_ismatch, $_isfullmatch, $_next, $_left,
                                $_subpatt_index_first, $_subpatt_index_last,
                                $_subpatt_index_first_old, $_subpatt_index_last_old,
                                $_firsttransition, $_backreftransition, $_backrefmatchlen) {
        $this->state = $_state;
        $this->matchcnt = $_matchcnt;
        $this->ismatch = $_ismatch;
        $this->isfullmatch = $_isfullmatch;
        $this->next = $_next;
        $this->left = $_left;
        $this->subpatt_index_first = $_subpatt_index_first;
        $this->subpatt_index_last = $_subpatt_index_last;
        $this->subpatt_index_first_old = $_subpatt_index_first_old;
        $this->subpatt_index_last_old = $_subpatt_index_last_old;
        $this->firsttransition = $_firsttransition;
        $this->backreftransition = $_backreftransition;
        $this->backrefmatchlen = $_backrefmatchlen;
    }
}

class nfa_preg_matcher extends preg_matcher {

    private $statelimit;
    private $transitionlimit;
    public $automaton;    // an nfa corresponding to the given regex

    public function get_state_limit() {
        return $this->statelimit;
    }

    public function get_transition_limit() {
        return $this->transitionlimit;
    }

    /**
    * returns prefix for engine specific classes
    */
    protected function node_prefix() {
        return 'nfa';
    }

    public function name() {
        return 'nfa_preg_matcher';
    }

    protected function get_engine_node_name($pregname) {
        switch($pregname) {
        case 'node_finite_quant':
        case 'node_infinite_quant':
        case 'node_concat':
        case 'node_alt':
        case 'node_subpatt':
            return 'nfa_preg_'.$pregname;
            break;
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return 'nfa_preg_leaf';
            break;
        }

        return parent::get_engine_node_name($pregname);
    }

    /**
    * returns true for supported capabilities
    * @param capability the capability in question
    * @return bool is capanility supported
    */
    public function is_supporting($capability) {
        switch($capability) {
        case preg_matcher::PARTIAL_MATCHING:
        case preg_matcher::NEXT_CHARACTER:
        case preg_matcher::CHARACTERS_LEFT:
        case preg_matcher::SUBPATTERN_CAPTURING:
            return true;
            break;
        }
        return false;
    }

    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->name()) {
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return true;
            break;
        }
        return get_string($pregnode->name(), 'qtype_preg');
    }

    public function count_subpattends_captured($procstate) {
        $cnt = 0;
        foreach ($procstate->subpatt_index_last_old as $index) {
            if ($index >= -1) {
                $cnt++;
            }
        }
        return $cnt;
    }

    /**
     * checks if new result is better than old result
     * @param oldres - old result, an object of preg_nfa_processing_state
     * @param newres - new result, an object of preg_nfa_processing_state
     * @param strict - if true, old result is replaced by a new result with the same match length
     * @return - true if new result is more suitable
     */
    public function is_new_result_more_suitable(&$oldres, &$newres, $strict = true) {
        // check the fields decreasing their priority
        // the first is 'fullness'
        $result = $newres->isfullmatch && !$oldres->isfullmatch;
        // the second is match existance
        if (!$result) {
            $result = $newres->ismatch && !$oldres->ismatch;
        }
        // the third is match length
        if (!$result) {
            if ($strict) {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&    // both of results have the same 'fullness' but new match is longer
                           $newres->matchcnt > $oldres->matchcnt);
            } else {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&
                           $newres->matchcnt >= $oldres->matchcnt);
            }
        }
        // the fourth is characters left to complete match
        if (!$result) {
            if ($strict) {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&    // both of results have the same 'fullness' but new match is longer
                           $newres->matchcnt == $oldres->matchcnt &&
                           $newres->left < $oldres->left);
            } else {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&
                           $newres->matchcnt == $oldres->matchcnt &&
                           $newres->left <= $oldres->left);
            }
        }
        // the last is number of subpatterns captured
        if (!$result) {
            if ($strict) {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&    // same length but more subpatterns captured
                            $newres->matchcnt == $oldres->matchcnt &&
                            $newres->left == $oldres->left &&
                            $this->count_subpattends_captured($newres) > $this->count_subpattends_captured($oldres));
            } else {
                $result = ($oldres->isfullmatch == $newres->isfullmatch &&    // same length but more subpatterns captured
                            $newres->matchcnt == $oldres->matchcnt &&
                            $newres->left == $oldres->left &&
                            $this->count_subpattends_captured($newres) >= $this->count_subpattends_captured($oldres));
            }
        }
        return $result;
    }

    /**
     * returns the minimal number of characters left for matching
     * @param str - string being matched
     * @param startpos - start position of matching
     * @param laststate - the last state of the automaton, an object of preg_nfa_processing_state
     * @return - number of characters left for matching
     */
    public function determine_characters_left($str, $startpos, $laststate) {
        $curstates = array();    // states which the automaton is in
        $result = null;
        if ($laststate->backrefmatchlen == 0) {
            // check if an asserion $ failed the match
            foreach ($laststate->state->next as $next) {
                if ($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $next->state === $this->automaton->endstate) {
                    $laststate->next = '';
                    $laststate->left = 0;
                    return $laststate;
                }
            }
            array_push($curstates, $laststate);
        } else {
            $transition = $laststate->backreftransition;
            $length = $laststate->subpatt_index_last[$transition->pregleaf->number] - $laststate->subpatt_index_first[$transition->pregleaf->number] + 1 - $laststate->backrefmatchlen;
            $newstate = new preg_nfa_processing_state($transition->state, $laststate->matchcnt + $length, $laststate->ismatch, false, '', 10000000,
                                             $laststate->subpatt_index_first, $laststate->subpatt_index_last, $laststate->subpatt_index_first_old, $laststate->subpatt_index_last_old,
                                             $transition, null, 0);
            $newstate->next = $newstate->firsttransition->pregleaf->next_character($str, $startpos + $laststate->matchcnt, $laststate->backrefmatchlen);
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                if ($curstate->state === $this->automaton->endstate && $curstate->next !== '' && ($result === null || ($result !== null && $result->matchcnt > $curstate->matchcnt))) {
                    $result = $curstate;
                    $result->left = $result->matchcnt - $laststate->matchcnt;
                } else {
                    foreach ($curstate->state->next as $next) {
                        if (!$next->loops) {
                            $skip = false;
                            // check for anchors
                            
                            if (($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $curstate->ismatch) ||        // ^ in the middle
                                ($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $next->state !== $this->automaton->endstate)) {    // $ in the middle
                                    $skip = true;
                            }
                            if (!$skip) {
                                if (is_a($next->pregleaf, 'preg_leaf_backref')) {
                                    // only generated subpatterns can be passed
                                    if (array_key_exists($next->pregleaf->number, $curstate->subpatt_index_last_old) && $curstate->subpatt_index_last_old[$next->pregleaf->number] > -2) {
                                        $length = $curstate->subpatt_index_last_old[$next->pregleaf->number] - $curstate->subpatt_index_first_old[$next->pregleaf->number] + 1;
                                    } else {
                                        $skip = true;
                                    }
                                } else {
                                    $length = $next->pregleaf->consumes();
                                }
                            }
                            // check for length
                            if (!$skip) {
                                $newstate = new preg_nfa_processing_state($next->state, $curstate->matchcnt + $length, $curstate->ismatch, false, $curstate->next, 10000000,
                                                                     $curstate->subpatt_index_first, $curstate->subpatt_index_last, $curstate->subpatt_index_first_old, $curstate->subpatt_index_last_old,
                                                                     $curstate->firsttransition, null, 0);
                                if ($result !== null && $newstate->matchcnt > $result->matchcnt) {
                                    $skip = true;
                                }
                            }
                            if (!$skip) {
                                // generate a next character
                                if ($newstate->firsttransition == null && ($next->pregleaf->consumes() || is_a($next->pregleaf, 'preg_leaf_backref'))) {
                                    $newstate->firsttransition = $next;
                                    $newstate->next = $newstate->firsttransition->pregleaf->next_character($str, $startpos + $laststate->matchcnt, $laststate->backrefmatchlen);
                                }
                                // save subpattern indexes for backreference capturing
                                foreach ($next->subpatt_start as $key=>$subpatt) {
                                    // if this subpattern was captured, save it to be on the safe side
                                    if ($newstate->subpatt_index_last[$key] >= -1) {
                                        $newstate->subpatt_index_first_old[$key] = $newstate->subpatt_index_first[$key];
                                        $newstate->subpatt_index_last_old[$key] = $newstate->subpatt_index_last[$key];
                                    }
                                    $newstate->subpatt_index_first[$key] = $curstate->matchcnt;
                                    $newstate->subpatt_index_last[$key] = -2;
                                }
                                foreach ($next->subpatt_end as $key=>$subpatt) {
                                    if ($newstate->subpatt_index_first[$key] >= 0) {
                                        $newstate->subpatt_index_last[$key] = $curstate->matchcnt + $length - 1;
                                        // rewrite old results of subpattern capturing
                                        $newstate->subpatt_index_first_old[$key] = $newstate->subpatt_index_first[$key];
                                        $newstate->subpatt_index_last_old[$key] = $newstate->subpatt_index_last[$key];
                                    }
                                }
                                array_push($newstates, $newstate);
                            }
                        }
                    }
                }
            }
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        return $result;
    }

    /**
     * returns the longest match using a string as input. matching is proceeded from a given start position
     * @param str - the original input string
     * @param startpos - index of the start position to match
     * @param cs - is matching case sensitive
     * @return - the longest character sequence matched
     */
    public function match_from_pos($str, $startpos) {
        $curstates = array();    // states which the automaton is in
        $skipstates = array();   // contains states where infinite quantifiers start. it's used to protect from loops like ()*
        $results = array();      // possible matches
        $fullmatchfound = false;

        $this->reset_subpattern_indexes();
        // initial state with nothing captured
        $initialstate = new preg_nfa_processing_state($this->automaton->startstate, 0, false, false, '', 10000000,
                                                      $this->index_first, $this->index_last, $this->index_first, $this->index_last,
                                                      null, null, 0);
        array_push($curstates, $initialstate);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $curstate = array_pop($curstates);
                // saving the current result
                if ($curstate->matchcnt > 0 /*$curstate->state !== $this->automaton->startstate*/) {
                    $curstate->ismatch = true;
                }
                if ($curstate->state === $this->automaton->endstate) {
                    $curstate->ismatch = true;
                    $curstate->isfullmatch = true;
                    $curstate->left = 0;
                    $fullmatchfound = true;
                    array_push($results, $curstate);
                }
                // kill epsilon-cycles
                $skip = false;
                if ($curstate->state->startsinfinitequant) {
                    // skipstates is sorted by matchcnt because transitions add characters
                    for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $curstate->matchcnt <= $skipstates[$i]->matchcnt; $i--)
                        if ($skipstates[$i]->state === $curstate->state && $skipstates[$i]->matchcnt == $curstate->matchcnt /*&&
                            $skipstates[$i]->subpatt_index_last_old == $curstate->subpatt_index_last_old &&
                            $skipstates[$i]->subpatt_index_first_old == $curstate->subpatt_index_first_old*/) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $curstate);
                    }
                }
                // iterate over all transitions
                if (!$skip) {
                    $this->index_first = $curstate->subpatt_index_first_old;
                    $this->index_last = $curstate->subpatt_index_last_old;
                    foreach ($curstate->state->next as $transition) {
                        $pos = $curstate->matchcnt;
                        $length = 0;
                        if ($transition->pregleaf->match($str, $startpos + $pos, &$length, !$transition->pregleaf->caseinsensitive)) {
                            // create a new state
                            $newstate = new preg_nfa_processing_state($transition->state, $pos + $length, false, false, '', 10000000,
                                                             $curstate->subpatt_index_first, $curstate->subpatt_index_last, $curstate->subpatt_index_first_old, $curstate->subpatt_index_last_old,
                                                             null, null, 0);
                            // set start indexes of subpatterns
                            foreach ($transition->subpatt_start as $key=>$subpatt) {
                                // if this subpattern was captured, save it to be on the safe side
                                if ($newstate->subpatt_index_last[$key] >= -1) {
                                    $newstate->subpatt_index_first_old[$key] = $newstate->subpatt_index_first[$key];
                                    $newstate->subpatt_index_last_old[$key] = $newstate->subpatt_index_last[$key];
                                }
                                $newstate->subpatt_index_first[$key] = $startpos + $pos;
                                $newstate->subpatt_index_last[$key] = -2;
                            }
                            // set end indexes of subpatterns
                            foreach ($transition->subpatt_end as $key=>$subpatt) {
                                if ($newstate->subpatt_index_first[$key] >= 0) {
                                    $newstate->subpatt_index_last[$key] = $startpos + $pos + $length - 1;
                                    // rewrite old results of subpattern capturing
                                    $newstate->subpatt_index_first_old[$key] = $newstate->subpatt_index_first[$key];
                                    $newstate->subpatt_index_last_old[$key] = $newstate->subpatt_index_last[$key];
                                }
                            }
                            // if we are out of a subpattern, then stop matching it and rewrite old results of subpattern capturing
                            /*foreach ($newstate->subpatt_index_last as $key=>$subpatt) {
                                // do it for matched subpatterns only
                                if ($subpatt >= -1 && !array_key_exists($key, $transition->belongs_to_subpatt)) {
                                    $newstate->subpatt_index_first_old[$key] = $newstate->subpatt_index_first[$key];
                                    $newstate->subpatt_index_last_old[$key] = $newstate->subpatt_index_last[$key];
                                }
                            }*/
                            // save the state
                            array_push($newstates, $newstate);
                        } else if (!$fullmatchfound) {    // transition not matched, save the partial match
                            // if a backreference matched partially - set corresponding fields
                            if ($length > 0) {
                                $curstate->ismatch = true;
                                $curstate->matchcnt += $length;
                                $curstate->backreftransition = $transition;
                                $curstate->backrefmatchlen = $length;
                            }
                            // go to the end state
                            $path = $this->determine_characters_left($str, $startpos, $curstate);
                            if ($path !== null) {
                                $curstate->next = $path->next;
                                $curstate->left = $path->left;
                            }
                            // finally, save the possible partial match
                            array_push($results, $curstate);
                        }
                    }
                    $this->reset_subpattern_indexes();
                }
            }
            // replace curstates with newstates
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        $result = null;
        foreach ($results as $curresult) {
            if ($result == null || ($this->is_new_result_more_suitable(&$result, &$curresult))) {
                $result = $curresult;
            }
        }
        // set the results
        if ($result->ismatch) {
            $result->subpatt_index_first[0] = $startpos;
            $result->subpatt_index_last[0] = $startpos + $result->matchcnt - 1;
        } else {
            $textlib = textlib_get_instance();
            $result->subpatt_index_first[0] = $textlib->strlen($str);
            $result->subpatt_index_last[0] = $result->subpatt_index_first[0] - 1;
        }
        $result->subpatt_index_first_old[0] = $result->subpatt_index_first[0];
        $result->subpatt_index_last_old[0] = $result->subpatt_index_last[0];
        return $result;
    }

    /**
    * do real matching
    * @param str a string to match
    */
    function match_inner($str) {
        $result = new preg_nfa_processing_state($this->automaton->startstate, 0, false, false, '', 10000000,
                                       $this->index_first, $this->index_last, $this->index_first, $this->index_last,
                                       null, null, 0);
        $startpos = 0;
        $textlib = textlib_get_instance();
        $len = $textlib->strlen($str);
        // match from all indexes
        $rightborder = $len;
        if ($str === '') {
            $rightborder = 1;
        }
        for ($j = 0; $j < $rightborder && !$result->isfullmatch; $j++) {
            $tmp = $this->match_from_pos($str, $j);
            if ($this->is_new_result_more_suitable(&$result, &$tmp)) {
                $result = $tmp;
                $startpos = $j;
            }
        }
        // save the result
        $this->is_match = $result->ismatch;
        $this->full = $result->isfullmatch;
        foreach ($result->subpatt_index_last_old as $key=>$subpatt) {
            if ($subpatt >= -1) {
                $this->index_first[$key] = $result->subpatt_index_first_old[$key];
                $this->index_last[$key] = $result->subpatt_index_last_old[$key];
            }
        }
        $this->next = $result->next;
        $this->left = $result->left;
    }

    public function __construct($regex = null, $modifiers = null) {
        global $CFG;
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }
        if (isset($CFG->qtype_preg_nfastatelimit)) {
            $this->statelimit = $CFG->qtype_preg_nfastatelimit;
        } else {
            $this->statelimit = 250;
        }
        if (isset($CFG->qtype_preg_nfatransitionlimit)) {
            $this->transitionlimit = $CFG->qtype_preg_nfatransitionlimit;
        } else {
            $this->transitionlimit = 250;
        }

        $stack = array();
        $statecount = 0;
        $transitioncount = 0;
        $errornode = $this->dst_root->create_automaton(&$this, &$stack, &$statecount, &$transitioncount);
        if ($errornode != null) {
            $this->errors[] = new preg_too_complex_error($regex, $this, array('start' => $errornode->pregnode->indfirst, 'end' => $errornode->pregnode->indlast));
            return;
        }
        $this->automaton = array_pop($stack);
        //$this->automaton->append_endeps();
        //$this->automaton->replace_eps_transitions();
        //$this->automaton->merge_simple_assertions();
        //$this->automaton->delete_unreachable_states();
    }

}

?>