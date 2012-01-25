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
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

/**
 * defines a state of an automaton when running
 * used when matching a string
 */

class qtype_preg_nfa_processing_state extends qtype_preg_matching_results {
    public $state;                     // a reference to the state which automaton is in
    public $index_first_old = array(); // indexes of subpatterns defenetly captured. it's used, for example, when matching quantified subpatterns like (...)*
    public $length_old = array();      // same as previous field
    public $first_transition;          // the first transition of a path
    public $backref_transition;        // != null if the last transition matched is a backreference
    public $backref_match_len;         // length of the last match

    public function __construct($full, $index_first, $length, $index_first_old, $length_old, $left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                $correctending = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER, $correctendingcomplete = false, $correctendingstart =  qtype_preg_matching_results::NO_MATCH_FOUND,
                                &$state, $first_transition, $backref_transition, $backref_match_len) {
        parent::__construct($full, $index_first, $length, $left, $correctending, $correctendingcomplete, $correctendingstart);
        $this->state = $state;
        $this->index_first_old = $index_first_old;
        $this->length_old = $length_old;
        $this->first_transition = $first_transition;
        $this->backref_transition = $backref_transition;
        $this->backref_match_len = $backref_match_len;
    }
}

class qtype_preg_nfa_matcher extends qtype_preg_matcher {

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
        return 'nfa_matcher';
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
        case qtype_preg_matcher::PARTIAL_MATCHING:
        case qtype_preg_matcher::CORRECT_ENDING:
        case qtype_preg_matcher::CHARACTERS_LEFT:
        case qtype_preg_matcher::SUBPATTERN_CAPTURING:
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

    /**
     * returns the minimal number of characters left for matching
     * @param str - string being matched
     * @param startpos - start position of matching
     * @param laststate - the last state of the automaton, an object of qtype_preg_nfa_processing_state
     * @return - number of characters left for matching
     */
    public function determine_characters_left($str, $startpos, $laststate) {
        $curstates = array();    // states which the automaton is in
        $result = null;
        if ($laststate->backref_match_len == 0) {    // The last transition was not a backreference.
            // Check if an asserion $ failed the match and it's possible to remove a few characters
            foreach ($laststate->state->next as $next) {
                if ($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $next->state === $this->automaton->endstate) {
                    $result = $laststate;
                    // TODO set all fieldss
                    $result->left = 0;
                    $result->correctending = qtype_preg_matching_results::DELETE_TAIL;
                    break;
                }
            }
            // Anyway, try the other paths to complete match
            array_push($curstates, $laststate);
        } else {
            // The last partially matched transition was a backreference and we can only continue from this transition
            $length = $laststate->length[$laststate->backref_transition->pregleaf->number] - $laststate->backref_match_len;    // Number of characters left for this backreference
            $newstate = new qtype_preg_nfa_processing_state(false, $laststate->index_first, $laststate->length, $laststate->index_first_old, $laststate->length_old, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                                            qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER, false, qtype_preg_matching_results::NO_MATCH_FOUND,
                                                            $laststate->backref_transition->state, $laststate->backref_transition, null, 0);
            $newstate->length[0] += $length;
            $newstate->correctending = $newstate->first_transition->pregleaf->next_character($str, $startpos + $laststate->length[0], $laststate->backref_match_len);
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                // If we have reached the end state and still have no result, or if the old result is worst than new - save the reached result
                if ($curstate->state === $this->automaton->endstate && $curstate->correctending !== '' && ($result === null || ($result !== null && $result->length[0] > $curstate->length[0]))) {
                    $result = $curstate;
                    $result->left = $result->length[0] - $laststate->length[0];
                } else {
                    foreach ($curstate->state->next as $next) {
                        if (!$next->loops) {
                            $skip = false;
                            // check for anchors
                            if (($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $curstate->length[0] > 0) ||        // ^ in the middle
                                ($next->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $next->state !== $this->automaton->endstate)) {    // $ in the middle
                                    $skip = true;
                            }
                            if (!$skip) {
                                if (is_a($next->pregleaf, 'preg_leaf_backref')) {
                                    // only generated subpatterns can be passed
                                    if (array_key_exists($next->pregleaf->number, $curstate->length_old) && $curstate->length_old[$next->pregleaf->number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                        $length = $curstate->length_old[$next->pregleaf->number];
                                    } else {
                                        $skip = true;
                                    }
                                } else {
                                    $length = $next->pregleaf->consumes();
                                }
                            }
                            // check for length
                            if (!$skip) {
                                $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_old, $curstate->length_old, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                                                                $curstate->correctending, $curstate->correctendingcomplete, $curstate->correctendingstart, $next->state, $curstate->first_transition, null, 0);
                                $newstate->length[0] += $length;
                                if ($result !== null && $newstate->length[0] > $result->length[0]) {
                                    $skip = true;
                                }
                            }
                            if (!$skip) {
                                // generate a next character
                                if ($newstate->first_transition == null && ($next->pregleaf->consumes() || is_a($next->pregleaf, 'preg_leaf_backref'))) {
                                    $newstate->first_transition = $next;
                                    $newstate->correctending = $newstate->first_transition->pregleaf->next_character($str, $startpos + $laststate->length[0], $laststate->backref_match_len);
                                }
                                // save subpattern indexes for backreference capturing
                                foreach ($next->subpatt_start as $key=>$subpatt) {
                                    $newstate->index_first[$key] = $startpos + $curstate->length[0];
                                    $newstate->length[$key] = qtype_preg_matching_results::NO_MATCH_FOUND;
                                }
                                foreach ($next->subpatt_end as $key=>$subpatt) {
                                    if ($newstate->index_first[$key] >= 0) {
                                        $newstate->length[$key] = $startpos + $curstate->length[0] - $newstate->index_first[$key] + $length;
                                        // rewrite old results of subpattern capturing
                                        $newstate->index_first_old[$key] = $newstate->index_first[$key];
                                        $newstate->length_old[$key] = $newstate->length[$key];
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
        if ($result !== null) {
            $result->correctendingstart = $startpos + $result->length[0];
            $result->correctendingcomplete = true;
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

        $this->matchresults->invalidate_match($this->maxsubpatt);
        $this->matchresults->length[0] = 0;
        // initial state with nothing captured
        $initialstate = new qtype_preg_nfa_processing_state(false, $this->matchresults->index_first, $this->matchresults->length, $this->matchresults->index_first, $this->matchresults->length, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                                            qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER, false, qtype_preg_matching_results::NO_MATCH_FOUND, $this->automaton->startstate, null, null, 0);
        array_push($curstates, $initialstate);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $curstate = array_pop($curstates);
                // saving the current result
                if ($curstate->state === $this->automaton->endstate) {
                    $curstate->full = true;
                    $curstate->left = 0;
                    $fullmatchfound = true;
                    array_push($results, $curstate);
                }
                // kill epsilon-cycles
                $skip = false;
                if ($curstate->state->startsinfinitequant) {
                    // skipstates is sorted by length[0] because transitions add characters
                    for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $curstate->length[0] <= $skipstates[$i]->length[0]; $i--)
                        if ($skipstates[$i]->state === $curstate->state &&
                            $skipstates[$i]->index_first == $curstate->index_first &&
                            $skipstates[$i]->length == $curstate->length &&
                            $skipstates[$i]->index_first_old == $curstate->index_first_old &&
                            $skipstates[$i]->length_old == $curstate->length_old) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $curstate);
                    }
                }
                // iterate over all transitions
                if (!$skip) {
                    $this->matchresults->index_first = $curstate->index_first_old;
                    $this->matchresults->length = $curstate->length_old;
                    foreach ($curstate->state->next as $transition) {
                        $curlen = $curstate->length[0];
                        $length = 0;
                        if ($transition->pregleaf->match($str, $startpos + $curlen, &$length, !$transition->pregleaf->caseinsensitive)) {
                            // create a new state
                            $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_old, $curstate->length_old, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                                                            qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER, false, qtype_preg_matching_results::NO_MATCH_FOUND, $transition->state, null, null, 0);
                            $newstate->length[0] += $length;
                            $newstate->length_old[0] = $newstate->length[0];
                            // set start indexes of subpatterns
                            foreach ($transition->subpatt_start as $key=>$subpatt) {
                                $newstate->index_first[$key] = $startpos + $curlen;
                                $newstate->length[$key] = qtype_preg_matching_results::NO_MATCH_FOUND;
                            }
                            // set end indexes of subpatterns
                            foreach ($transition->subpatt_end as $key=>$subpatt) {
                                if ($newstate->index_first[$key] >= 0) {
                                    $newstate->length[$key] = $startpos + $curlen - $newstate->index_first[$key] + $length;
                                    // rewrite old results of subpattern capturing
                                    $newstate->index_first_old[$key] = $newstate->index_first[$key];
                                    $newstate->length_old[$key] = $newstate->length[$key];
                                }
                            }
                            // save the state
                            array_push($newstates, $newstate);
                        } else if (!$fullmatchfound) {    // transition not matched, save the partial match
                            // if a backreference matched partially - set corresponding fields
                            if ($length > 0) {
                                $curstate->length[0] += $length;
                                $curstate->length_old[0] = $curstate->length[0];
                                $curstate->backref_transition = $transition;
                                $curstate->backref_match_len = $length;
                            }
                            // go to the end state
                            $path = $this->determine_characters_left($str, $startpos, $curstate);
                            if ($path !== null) {
                                $curstate->correctendingstart = $path->correctendingstart;
                                $curstate->correctending = $path->correctending;
                                $curstate->correctendingcomplete = $path->correctendingcomplete;
                                $curstate->left = $path->left;
                            }
                            // finally, save the possible partial match
                            array_push($results, $curstate);
                        }
                    }
                    $this->matchresults->invalidate_match($this->maxsubpatt);
                    $this->matchresults->length[0] = 0;
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
            if ($result == null || $result->worse_than($curresult)) {
                $result = $curresult;
            }
        }
        if ($result !== null && $result->is_match()) {
            $result->index_first[0] = $startpos;
            $result->index_first_old[0] = $result->index_first[0];
        }
        return new qtype_preg_matching_results($result->full, $result->index_first_old, $result->length_old, $result->left, $result->correctending, $result->correctendingcomplete, $result->correctendingstart);
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
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this, array('start' => $errornode->pregnode->indfirst, 'end' => $errornode->pregnode->indlast));
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