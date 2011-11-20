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
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');

/**
 * defines a state of an automaton when running
 * used when matching a string
 */
class processing_state {
    public $state;                             // a reference to the state which automaton is in
    public $matchcnt;                          // the number of characters matched
    public $isfullmatch;                       // is the match full
    public $next;                              // the next possible character
    public $subpatt_index_first = array();     // key = subpattern number
    public $subpatt_index_last = array();      // key = subpattern number
    public $subpatt_captured = array();        // an array containing subpatterns captured at the moment
    public $subpatt_index_first_old = array(); // for matching quantified subpatterns like (...)*
    public $subpatt_index_last_old = array();  // for matching quantified subpatterns like (...)*
    public $firsttransition;                   // the first transition of a path
    public $backreftransition;                 // != null if the last transition matched is a backreference
    public $backrefmatchlen;                   // length of the last match

    public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_next,
                                $_subpatt_index_first, $_subpatt_index_last, $_subpatt_captured,
                                $_subpatt_index_first_old, $_subpatt_index_last_old,
                                $_firsttransition, $_backreftransition, $_backrefmatchlen) {
        $this->state = $_state;
        $this->matchcnt = $_matchcnt;
        $this->isfullmatch = $_isfullmatch;
        $this->next = $_next;
        $this->subpatt_index_first = $_subpatt_index_first;
        $this->subpatt_index_last = $_subpatt_index_last;
        $this->subpatt_captured = $_subpatt_captured;
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
        case 'node_assert':
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

    function is_node_acceptable($pregnode) {
        switch ($pregnode->name()) {
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return true;
            break;
        }
        return false;
    }

    /**
     * checks if new result is better than old result
     * @param oldres - old result, an object of processing_state
     * @param newres - new result, an object of processing_state
     * @return - true if new result is more suitable
     */
    public function is_new_result_more_suitable(&$oldres, &$newres) {
        if (($oldres->state != $this->automaton->endstate && $newres->matchcnt >= $oldres->matchcnt) ||                                                    // new match is longer
            ($newres->state == $this->automaton->endstate && $oldres->state != $this->automaton->endstate) ||                                              // new match is full
            ($newres->state == $this->automaton->endstate && $oldres->state == $this->automaton->endstate && $newres->matchcnt >= $oldres->matchcnt)) {    // new match is full and longer
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns the minimal number of characters left for matching
     * @param str - string being matched
     * @param startpos - start position of matching
     * @param laststate - the last state of the automaton, an object of processing_state
     * @return - number of characters left for matching
     */
    public function determine_characters_left($str, $startpos, $laststate) {
        $curstates = array();    // states which the automaton is in
        $results = array();      // different paths to the end state
        if ($laststate->backrefmatchlen == 0) {
            array_push($curstates, $laststate);
        } else {
            $transition = $laststate->backreftransition;
            $length = $laststate->subpatt_index_last[$transition->pregleaf->number] - $laststate->subpatt_index_first[$transition->pregleaf->number] + 1 - $laststate->backrefmatchlen;
            $newstate = new processing_state($transition->state, $laststate->matchcnt + $length, false, '',
                                             $laststate->subpatt_index_first, $laststate->subpatt_index_last, $laststate->subpatt_captured,
                                             $laststate->subpatt_index_first_old, $laststate->subpatt_index_last_old,
                                             $transition, null, 0);
            $newstate->next = $newstate->firsttransition->pregleaf->next_character($str, $startpos + $laststate->matchcnt, $laststate->backrefmatchlen);
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                if (count($curstate->state->next) == 0) {
                    $results[] = $curstate;
                }
                foreach ($curstate->state->next as $next) {
                    if (!$next->loops) {
                        $skip = false;
                        if (is_a($next->pregleaf, 'preg_leaf_backref')) {
                            // only generated subpatterns can be passed
                            if ($curstate->subpatt_index_last[$next->pregleaf->number] > -2) {
                                $length = $curstate->subpatt_index_last[$next->pregleaf->number] - $curstate->subpatt_index_first[$next->pregleaf->number] + 1;
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $next->pregleaf->consumes();
                        }
                        if (!$skip) {
                            $newstate = new processing_state($next->state, $curstate->matchcnt + $length, false, $curstate->next,
                                                             $curstate->subpatt_index_first, $curstate->subpatt_index_last, $curstate->subpatt_captured,
                                                             $curstate->subpatt_index_first_old, $curstate->subpatt_index_last_old,
                                                             $curstate->firsttransition, null, 0);
                            if ($newstate->firsttransition == null && ($next->pregleaf->consumes() || is_a($next->pregleaf, 'preg_leaf_backref'))) {
                                $newstate->firsttransition = $next;
                                $newstate->next = $newstate->firsttransition->pregleaf->next_character($str, $startpos + $laststate->matchcnt, $laststate->backrefmatchlen);
                            }
                            // save subpattern indexes
                            foreach ($next->subpatt_start as $key=>$subpatt) {
                                if ($newstate->subpatt_index_first[$key] == -1) {
                                    $newstate->subpatt_index_first[$key] = $curstate->matchcnt + $length - 1;    // saving to index_first for backreference capturing
                                }
                            }
                            foreach ($next->subpatt_end as $key=>$subpatt) {
                                if ($newstate->subpatt_index_last[$key] == -2) {
                                    $newstate->subpatt_index_last[$key] = $curstate->matchcnt + $length - 1;    // saving to index_last
                                }
                            }
                            array_push($newstates, $newstate);
                        }
                    }
                }
            }
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        $result = null;
        foreach ($results as $curres) {
            if ($result === null || $result->matchcnt > $curres->matchcnt || ($result->next ==='' && $curres->next !== '')) {
                $result = $curres;
            }
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

        $this->reset_subpattern_indexes();
        // initial state with nothing captured
        $result = new processing_state($this->automaton->startstate, 0, false, '',
                                       $this->index_first, $this->index_last, array(),
                                       $this->index_first, $this->index_last,
                                       null, null, 0);
        foreach ($result->subpatt_index_first as $key=>$subpatt) {
            $result->subpatt_captured[$key] = false;
        }
        array_push($curstates, $result);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $curstate = array_pop($curstates);
                // saving the result
                if ($this->is_new_result_more_suitable(&$result, &$curstate)) {
                    $result = $curstate;
                }
                // kill epsilon-cycles
                $skip = false;
                if ($curstate->state->startsinfinitequant) {
                    // skipstates is sorted by matchcnt because transitions add characters
                    for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $curstate->matchcnt <= $skipstates[$i]->matchcnt; $i--)
                        if ($skipstates[$i]->state === $curstate->state && $skipstates[$i]->matchcnt == $curstate->matchcnt) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $curstate);
                    }
                }
                // iterate over all transitions
                if (!$skip) {
                    $this->index_first = $curstate->subpatt_index_first;
                    $this->index_last = $curstate->subpatt_index_last;
                    foreach ($curstate->state->next as $transition) {
                        $pos = $curstate->matchcnt;
                        $length = 0;
                        if ($transition->pregleaf->match($str, $startpos + $pos, &$length, !$transition->pregleaf->caseinsensitive )) {
                            // create a new state
                            $newstate = new processing_state($transition->state, $pos + $length, false, '',
                                                             $curstate->subpatt_index_first, $curstate->subpatt_index_last, $curstate->subpatt_captured,
                                                             $curstate->subpatt_index_first_old, $curstate->subpatt_index_last_old,
                                                             null, null, 0);
                            // set start indexes of subpatterns
                            foreach ($transition->subpatt_start as $key=>$subpatt) {
                                if ($newstate->subpatt_index_first[$key] < 0) {
                                    $newstate->subpatt_index_first[$key] = $startpos + $pos;
                                }
                            }
                            // set end indexes of subpatterns
                            foreach ($transition->subpatt_end as $key=>$subpatt) {
                                if ($newstate->subpatt_index_first[$key] >= 0 && !$newstate->subpatt_captured[$key]) {
                                    $newstate->subpatt_index_last[$key] = $startpos + $pos + $length - 1;
                                }
                            }
                            // if we are out of a subpattern, then stop matching it
                            foreach ($newstate->subpatt_index_first as $key=>$subpatt) {
                                // looking at matched subpatterns
                                if ($subpatt != -1 && !array_key_exists($key, $transition->belongs_to_subpatt)) {
                                    $newstate->subpatt_captured[$key] = true;
                                }
                            }
                            // save the state
                            array_push($newstates, $newstate);
                        } else if ($length > 0) {    // (length > 0) equals to (transition->pregleaf is a backreference)
                            $curstate->matchcnt += $length;
                            $curstate->backreftransition = $transition;
                            $curstate->backrefmatchlen = $length;
                            if ($this->is_new_result_more_suitable(&$result, &$curstate)) {
                                $result = $curstate;
                            }
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
        $result->isfullmatch = ($result->state == $this->automaton->endstate);
        if ($result->matchcnt > 0) {
            $result->subpatt_index_first[0] = $startpos;
            $result->subpatt_index_last[0] = $startpos + $result->matchcnt - 1;
        } else {
            $textlib = textlib_get_instance();
            $result->subpatt_index_first[0] = $textlib->strlen($str);
            $result->subpatt_index_last[0] = $result->subpatt_index_first[0] - 1;
        }
        return $result;
    }

    /**
    * do real matching
    * @param str a string to match
    */
    function match_inner($str) {
        $result = new processing_state($this->automaton->startstate, 0, false, '',
                                       array(), array(), array(),
                                       array(), array(),
                                       null, null, 0);
        $startpos = 0;
        $textlib = textlib_get_instance();
        $len = $textlib->strlen($str);
        // match from all indexes
        for ($j = 0; $j < $len && !$result->isfullmatch; $j++) {
            $tmp = $this->match_from_pos($str, $j);
            if ($this->is_new_result_more_suitable(&$result, &$tmp)) {
                $result = $tmp;
                $startpos = $j;
            }
        }
        // save the result
        $this->is_match = ($result->matchcnt > 0);
        $this->full = $result->isfullmatch;
        foreach ($result->subpatt_index_last as $key=>$subpatt) {
            if ($subpatt != -2) {
                $this->index_first[$key] = $result->subpatt_index_first[$key];
                $this->index_last[$key] = $result->subpatt_index_last[$key];
            }
        }

        // generate a character
        if (!$result->isfullmatch) {
            $path = $this->determine_characters_left($str, $startpos, $result);
            if ($path !== null) {
                $this->next = $path->next;
                $this->left = $path->matchcnt - $result->matchcnt;
            } else {
                $this->next = '';
                $this->left = 10000000;    // the end state is unreachable
            }
        } else {
            $this->next = '';
            $this->left = 0;
        }
    }

    public function __construct($regex = null, $modifiers = null) {
        global $CFG;
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }
        if (isset($CFG->nfastatelimit)) {
            $this->statelimit = $CFG->nfastatelimit;
        } else {
            $this->statelimit = 250;
        }
        if (isset($CFG->nfatransitionlimit)) {
            $this->transitionlimit = $CFG->nfatransitionlimit;
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