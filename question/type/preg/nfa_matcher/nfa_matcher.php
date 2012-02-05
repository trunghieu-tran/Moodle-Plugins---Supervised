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
    public $index_first_new = array(); // indexes of subpatterns defenetly captured. it's used, for example, when matching quantified subpatterns like (...)*
    public $length_new = array();      // same as previous field
    public $backref_transition;        // != null if the last transition matched is a backreference
    public $backref_match_len;         // length of the last match

    public function __construct($full, $index_first, $length, $index_first_new, $length_new, $left, $extendedmatch,
                                &$state, $backref_transition, $backref_match_len, $sourceobj) {
        parent::__construct($full, $index_first, $length, $left, $extendedmatch);
        $this->state = $state;
        $this->index_first_new = $index_first_new;
        $this->length_new = $length_new;
        $this->backref_transition = $backref_transition;
        $this->backref_match_len = $backref_match_len;
        $this->str = $sourceobj->str;
        $this->maxsubpatt = $sourceobj->maxsubpatt;
        $this->subpatternmap = $sourceobj->subpatternmap;
        $this->lexemcount = $sourceobj->lexemcount;
    }

    public function concatenate_char_to_str($char) {
        $this->str .= $char;
    }
}

class qtype_preg_nfa_matcher extends qtype_preg_matcher {

    public $automaton;    // an nfa corresponding to the given regex

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
        $skipstates = array();
        $result = null;
        if ($laststate->backref_match_len == 0) {    // The last transition was not a backreference.
            // Check if an asserion $ failed the match and it's possible to remove a few characters
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to === $this->automaton->end_state()) {
                    $result = clone $laststate;
                    $result->left = 0;
                    $result->full = true;
                    break;
                }
            }
            // Anyway, try the other paths to complete match
            array_push($curstates, $laststate);
        } else {
            // The last partially matched transition was a backreference and we can only continue from this transition
            $length = $laststate->length[$laststate->backref_transition->pregleaf->number] - $laststate->backref_match_len;    // Number of characters left for this backreference
            $newstate = new qtype_preg_nfa_processing_state(false, $laststate->index_first, $laststate->length, $laststate->index_first_new, $laststate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                            $laststate->backref_transition->to, null, 0, $laststate);
            // re-write the string with correct characters
            $newstate->concatenate_char_to_str($laststate->backref_transition->pregleaf->next_character($str, $startpos + $laststate->length[0], $laststate->backref_match_len, $laststate));
            $newstate->length[0] += $length;
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                // If we have reached the end state and still have no result, or if the old result is worse than new - save the reached result
                if ($curstate->state === $this->automaton->end_state() && ($result === null || ($result !== null && $result->length[0] > $curstate->length[0]))) {
                    $result = clone $curstate;
                    $result->left = 0;
                    $result->full = true;
                }
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    // check for anchors
                    $skip = (($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $curstate->length[0] > 0) ||                        // ^ in the middle
                             ($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to !== $this->automaton->end_state()));    // $ in the middle
                    foreach ($skipstates as $skipstate) {
                        if ($skipstate->state === $curstate->state/* &&
                            $skipstate->index_first === $curstate->index_first &&
                            $skipstate->length === $curstate->length &&
                            $skipstate->index_first_new === $curstate->index_first_new &&
                            $skipstate->length_new === $curstate->length_new*/) {
                            $skip = true;
                            break;
                        }
                    }
                    if (!$skip) {
                        if (is_a($transition->pregleaf, 'preg_leaf_backref')) {        // only generated subpatterns can be passed
                            if (array_key_exists($transition->pregleaf->number, $curstate->length) && $curstate->length[$transition->pregleaf->number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $length = $curstate->length[$transition->pregleaf->number];
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $transition->pregleaf->consumes($curstate);
                        }
                    }
                    if (!$skip) {
                        $skip = ($result !== null && $curstate->length[0] + $length > $result->length[0]);        // is it longer then an existing one?
                    }
                    if (!$skip) {
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_new, $curstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, 0, $curstate);
                        // generate a next character
                        if ($length > 0) {
                            $newstate->concatenate_char_to_str($transition->pregleaf->next_character($str, $startpos + $newstate->length[0], 0, $curstate));
                        }
                        $newstate->length[0] += $length;

                        // get subpattern info from transition
                        $subpatt_start = array();
                        $subpatt_end = array();
                        foreach ($transition->tags as $value) {
                            if ($value % 2 == 0) {
                                $subpatt_start[] = $value / 2;
                            } else {
                                $subpatt_end[] = ($value - 1) / 2;
                            }
                        }
                        // set start indexes of subpatterns
                        foreach ($subpatt_start as $number) {
                            $newstate->index_first_new[$number] = $startpos + $curstate->length[0];
                            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
                        }
                        // set end indexes of subpatterns
                        foreach ($subpatt_end as $number) {
                            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $newstate->length_new[$number] = $startpos + $curstate->length[0] - $newstate->index_first_new[$number] + $length;
                                // rewrite old results of subpattern capturing
                                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                                $newstate->length[$number] = $newstate->length_new[$number];
                            }
                        }
                        array_push($newstates, $newstate);
                    }
                }
                $skipstates[] = $curstate;
            }
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        if ($result !== null && $result->is_match()) {
            $result->index_first[0] = $startpos;
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

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);
        $result->invalidate_match();

        // initial state with nothing captured
        $initialstate = new qtype_preg_nfa_processing_state(false, $result->index_first, $result->length, $result->index_first, $result->length, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                            $this->automaton->start_state(), null, 0, $result);
        $initialstate->length[0] = 0;
        array_push($curstates, $initialstate);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $curstate = array_pop($curstates);
                // saving the current result
                if ($curstate->state === $this->automaton->end_state()) {
                    $curstate->full = true;
                    $curstate->left = 0;
                    $fullmatchfound = true;
                    array_push($results, $curstate);
                }

                // iterate over all transitions
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if ($transition->pregleaf->match($str, $startpos + $curstate->length[0], &$length, !$transition->pregleaf->caseinsensitive, $curstate)) {
                        // create a new state
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_new, $curstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, 0, $curstate);
                        $newstate->length[0] += $length;
                        // get subpattern info
                        $subpatt_start = array();
                        $subpatt_end = array();
                        foreach ($transition->tags as $value) {
                            if ($value % 2 == 0) {
                                $subpatt_start[] = $value / 2;
                            } else {
                                $subpatt_end[] = ($value - 1) / 2;
                            }
                        }
                        // set start indexes of subpatterns
                        foreach ($subpatt_start as $number) {
                            $newstate->index_first_new[$number] = $startpos + $curstate->length[0];
                            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
                        }
                        // set end indexes of subpatterns
                        foreach ($subpatt_end as $number) {
                            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $newstate->length_new[$number] = $startpos + $curstate->length[0] - $newstate->index_first_new[$number] + $length;
                                // rewrite old results of subpattern capturing
                                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                                $newstate->length[$number] = $newstate->length_new[$number];
                            }
                        }
                        // the new state is calculated, check for zero-length loops
                        $skip = false;
                        if ($length === 0) {
                            foreach ($skipstates as $skipstate) {
                                // does this state already exists?
                                if ($skipstate->state === $curstate->state && $skipstate->index_first === $curstate->index_first && $skipstate->length === $curstate->length &&
                                    $skipstate->index_first_new === $curstate->index_first_new && $skipstate->length_new === $curstate->length_new) {
                                    $skip = true;
                                    break;
                                }
                            }
                            // if not, save it
                            if (!$skip) {
                                array_push($skipstates, $curstate);
                            }
                        }
                        if (!$skip) {
                            array_push($newstates, $newstate);
                        }
                    } else if (!$fullmatchfound) {    // transition not matched, save the partial match
                        // if a backreference matched partially - set corresponding fields
                        if ($length > 0) {
                            $curstate->length[0] += $length;
                            $curstate->backref_transition = $transition;
                            $curstate->backref_match_len = $length;
                        }
                        // go to the end state
                        $pathstart = clone $curstate;
                        $pathstart->set_source_info(substr($curstate->str(), 0, $startpos + $curstate->length[0]), $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);

                        $path = $this->determine_characters_left($str, $startpos, $pathstart);
                        if ($path !== null) {
                            $curstate->left = $path->length[0] - $curstate->length[0];
                            $curstate->extendedmatch = new qtype_preg_matching_results($path->full, $path->index_first, $path->length, $path->left);
                            $curstate->extendedmatch->set_source_info($path->str(), $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);
                        }
                        // finally, save the possible partial match
                        array_push($results, $curstate);
                    }
                }
            }
            // replace curstates with newstates
            $curstates = $newstates;
        }
        // find the best result
        foreach ($results as $curresult) {
            if ($result->worse_than($curresult)) {
                $result = $curresult;
                $result->index_first[0] = $startpos;    // it's guaranteed that result->is_match() == true
            }
        }
        return new qtype_preg_matching_results($result->full, $result->index_first, $result->length, $result->left, $result->extendedmatch);
    }

    public function __construct($regex = null, $modifiers = null) {
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }
        $this->automaton = new qtype_preg_nondeterministic_fa();
        try {
            $stack = array();
            $priority_counter = 0;
            $this->dst_root->create_automaton(&$this, &$this->automaton, &$stack, &$priority_counter);
        }
        catch (Exception $e) {
            // TODO
            //$this->errors[] = new qtype_preg_too_complex_error($regex, $this, array('start' => $errornode->pregnode->indfirst, 'end' => $errornode->pregnode->indlast));
        }
    }

}

?>