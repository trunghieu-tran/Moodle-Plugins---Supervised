<?php

/**
 * Defines NFA matcher class
 *
 * @copyright &copy; 2012  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

/**
 * Represents a state of an automaton when running.
 */
class qtype_preg_nfa_processing_state extends qtype_preg_matching_results implements qtype_preg_matcher_state {
    public $state;               // A reference to the state which automaton is in.
    public $index_first_new;     // Indexes of subpatterns being captured. Subpatterns which are already captured are stored in index_first.
    public $length_new;          // Same as previous field.
    public $last_transition;     // The last transition matched.
    public $last_match_len;      // Length of the last match.

    public function __construct($full, $index_first, $length, $index_first_new, $length_new, $left, $extendedmatch,
                                &$state, $last_transition, $last_match_len, $sourceobj) {
        parent::__construct($full, $index_first, $length, $left, $extendedmatch);
        $this->state = $state;
        $this->index_first_new = $index_first_new;
        $this->length_new = $length_new;
        $this->last_transition = $last_transition;
        $this->last_match_len = $last_match_len;
        $this->set_source_info($sourceobj->str, $sourceobj->maxsubpatt, $sourceobj->subpatternmap);
    }

    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {
        $parentresult = parent::worse_than($other, $orequal, $longestmatch, $areequal);
        if ($areequal === false) {
            return $parentresult;
        }

        // Leftmost rule.
        foreach ($this->index_first as $key => $index1) {
            if ($key === 0) {
                continue;
            }

            $index2 = $other->index_first[$key];
            $length1 = $this->length[$key];
            $length2 = $other->length[$key];

            // Subexpressions starting earlier take priority over ones starting later.
            /*if ($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 === qtype_preg_matching_results::NO_MATCH_FOUND) {
                return true;
            }
            if ($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 === qtype_preg_matching_results::NO_MATCH_FOUND) {
                return false;
            }

            $repeating2 = ($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 >= $index1 + $length1);
            $repeating1 = ($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 >= $index2 + $length2);

            // Subexpressions also correspond the leftmost-longest rule.
            if (($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 < $index1 && !$repeating1) ||
                ($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 === $index1 && $length2 > $length1)) {
                return true;
            }
            if (($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 < $index2 && !$repeating2) ||
                ($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 === $index2 && $length1 > $length2)) {
                return false;
            }*/

            // Leftmost-longest rule.
            if (($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                ($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 < $index1) ||
                ($index2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 === $index1 && $length2 > $length1)) {
                return true;
            }
            if (($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index2 === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                ($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 < $index2) ||
                ($index1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $index1 === $index2 && $length1 > $length2)) {
                return false;
            }

            // Repeating rule.
            if ($this->index_first[$key] < $other->index_first[$key]) {
                return true;
            }
            if ($this->index_first[$key] > $other->index_first[$key]) {
                return false;
            }
        }
        return false;

    }

    public function concatenate_char_to_str($char) {
        $this->str->concatenate($char);
    }

}

class qtype_preg_nfa_matcher extends qtype_preg_matcher {

    public $automaton;    // An NFA corresponding to the given regex.

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
            return 'qtype_preg_nfa_' . $pregname;
            break;
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return 'qtype_preg_nfa_leaf';
            break;
        }

        return parent::get_engine_node_name($pregname);
    }

    /**
     * Returns true for supported capabilities.
     * @param capability the capability in question.
     * @return bool is capanility supported.
     */
    public function is_supporting($capability) {
        switch($capability) {
        case qtype_preg_matcher::PARTIAL_MATCHING:
        case qtype_preg_matcher::CORRECT_ENDING:
        case qtype_preg_matcher::CHARACTERS_LEFT:
        case qtype_preg_matcher::SUBPATTERN_CAPTURING:
        case qtype_preg_matcher::CORRECT_ENDING_ALWAYS_FULL:
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
     * Writes new tag values to the new state reached from prevstate.
     * @param newstate reference to the new state.
     * @param prevstate previous state.
     * @param transition a transition from prevstate to newstate.
     * @param startpos start position of matching.
     * @param length number of characters consumed by transition.
     */
    private function write_tag_values(&$newstate, $prevstate, $transition, $startpos, $length) {
        // Get subpattern indexes.
        $subpatt_start = array();
        $subpatt_end = array();
        foreach ($transition->tags as $value) {
            if ($value % 2 === 0) {
                $subpatt_start[] = $value / 2;
            } else {
                $subpatt_end[] = ($value - 1) / 2;
            }
        }
        // Set start indexes of subpatterns.
        foreach ($subpatt_start as $number) {
            $newstate->index_first_new[$number] = $startpos + $prevstate->length[0];
            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
        }
        // Set end indexes of subpatterns.
        foreach ($subpatt_end as $number) {
            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $newstate->length_new[$number] = $startpos + $prevstate->length[0] - $newstate->index_first_new[$number] + $length;
                // Replace old results with new results.
                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                $newstate->length[$number] = $newstate->length_new[$number];
            }
        }
    }

    /**
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_nfa_processing_state startstate state to go from.
     * @param qtype_poasquestion_string str string being matched.
     * @param int startpos start position of matching.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    public function zero_length_closure($startstate, $str, $startpos) {
        $startstateclone = new qtype_preg_nfa_processing_state(false, $startstate->index_first, $startstate->length, $startstate->index_first_new,
                                                               $startstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                               $startstate->state, $startstate->last_transition, $startstate->last_match_len, $startstate);
        $result = array($startstateclone);
        $curstates = array($startstateclone);
        while (count($curstates) != 0) {
            $newstates = array();
            // We'll replace curstates with newstates by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if (!$transition->pregleaf->consumes($curstate) && $transition->pregleaf->match($str, $startpos + $curstate->length[0], $length, !$transition->pregleaf->caseinsensitive, $curstate)) {
                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_new,
                                                                        $curstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, $length, $curstate);
                        $newstate->last_transition = $transition;
                        $newstate->length[0] += $length;
                        $this->write_tag_values($newstate, $curstate, $transition, $startpos, $length);
                        if (!array_key_exists($newstate->state->number, $result)) {
                            $newstates[] = $newstate;
                            $result[$newstate->state->number] = $newstate;
                        }
                    }
                }
            }
            // Replace curstates with newstates.
            $curstates = $newstates;
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str string being matched.
     * @param int startpos - start position of matching.
     * @param qtype_preg_nfa_processing_state laststate - the last state of the automaton, an object of qtype_preg_nfa_processing_state.
     * @param bool fulllastmatch - was the last transition captured fully, not partially?
     * @return - an object of qtype_preg_nfa_processing_state.
     */
    public function determine_characters_left($str, $startpos, $laststate, $fulllastmatch) {
        $states = array();       // Objects of qtype_preg_nfa_processing_state.
        $curstates = array();    // States which the automaton is in.

        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate->number] = null;
        }
        $endstateid = $this->automaton->end_state()->number;
        $lasttransition = $laststate->last_transition;

        if ($lasttransition === null || $fulllastmatch) {    // The last transition was fully-matched.
            // Check if an asserion $ failed the match and it's possible to remove a few characters.
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->pregleaf->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to === $this->automaton->end_state()) {
                    $states[$endstateid] = clone $laststate;
                    $states[$endstateid]->state = $this->automaton->end_state();
                    $states[$endstateid]->full = true;
                    $states[$endstateid]->left = 0;
                    break;
                }
            }
            // Anyway, try the other paths to complete match.
            $zlc = $this->zero_length_closure($laststate, $str, $startpos);
            foreach ($zlc as $curstatezlc) {
                $states[$curstatezlc->state->number] = $curstatezlc;
                $curstates[] = $curstatezlc->state->number;
            }
        } else {
            // The last partially matched transition was a backreference and we can only continue from this transition.
            $length = $laststate->length[$lasttransition->pregleaf->number] - $laststate->last_match_len;    // Number of characters left for this backreference.
            $newstate = new qtype_preg_nfa_processing_state(false, $laststate->index_first, $laststate->length, $laststate->index_first_new, $laststate->length_new,
                                                            qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                            $lasttransition->to, $laststate->last_transition, $laststate->length[$lasttransition->pregleaf->number], $laststate);
            // Re-write the string with correct characters.
            $newstate->concatenate_char_to_str($lasttransition->pregleaf->next_character($newstate->str(), $startpos + $laststate->length[0], $laststate->last_match_len, $laststate));
            $newstate->length[0] += $length;

            $zlc = $this->zero_length_closure($newstate, $str, $startpos);
            foreach ($zlc as $curstatezlc) {
                $states[$curstatezlc->state->number] = $curstatezlc;
                $curstates[] = $curstatezlc->state->number;
            }
        }
        while (count($curstates) != 0) {
            $reached = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                foreach ($states[$curstate]->state->outgoing_transitions() as $transition) {
                    // Check for anchors.
                    $skip = (($transition->pregleaf->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $states[$curstate]->length[0] > 0) ||               // ^ in the middle.
                             ($transition->pregleaf->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to !== $this->automaton->end_state()));    // $ in the middle.


                    if (!$skip) {
                        if (is_a($transition->pregleaf, 'qtype_preg_leaf_backref')) {        // Only generated subpatterns can be passed.
                            if (array_key_exists($transition->pregleaf->number, $states[$curstate]->length) && $states[$curstate]->length[$transition->pregleaf->number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $length = $states[$curstate]->length[$transition->pregleaf->number];
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $transition->pregleaf->consumes($states[$curstate]);
                        }
                    }

                    $skip |= ($states[$endstateid] !== null && $states[$curstate]->length[0] + $length > $states[$endstateid]->length[0]); // Is it longer then an existing one?

                    if (!$skip) {
                        $newstate = new qtype_preg_nfa_processing_state(false, $states[$curstate]->index_first, $states[$curstate]->length, $states[$curstate]->index_first_new,
                                                                        $states[$curstate]->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, $length, $states[$curstate]);
                        $newstate->last_transition = $transition;
                        // Generate a next character.
                        if ($length > 0) {
                            $newstate->concatenate_char_to_str($transition->pregleaf->next_character($newstate->str(), $startpos + $newstate->length[0], 0, $states[$curstate]));
                        }
                        $newstate->length[0] += $length;
                        $this->write_tag_values($newstate, $states[$curstate], $transition, $startpos, $length);
                        // Saving the current result.
                        $zlc = $this->zero_length_closure($newstate, $str, $startpos);
                        foreach ($zlc as $curstatezlc) {
                            if ($curstatezlc->state === $this->automaton->end_state()) {
                                $curstatezlc->full = true;
                                $curstatezlc->left = 0;
                            }
                            $reached[] = $curstatezlc;
                        }
                    }
                }
            }
            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                if ($states[$curstate->state->number] === null || $states[$curstate->state->number]->length[0] > $curstate->length[0]) {
                    $states[$curstate->state->number] = $curstate;
                    $newstates[] = $curstate->state->number;
                }
            }
            $curstates = $newstates;
        }
        if ($states[$endstateid] !== null && $states[$endstateid]->is_match()) {
            $states[$endstateid]->index_first[0] = $startpos;
        }
        return $states[$endstateid];
    }

    /**
     * Returns the longest match using a string as input. Matching is proceeded from a given start position.
     * @param qtype_poasquestion_string str - the original input string.
     * @param int startpos - index of the start position to match.
     * @return - the longest character sequence matched.
     */
    public function match_from_pos($str, $startpos) {
        $states = array();           // Objects of qtype_preg_nfa_processing_state.
        $curstates = array();        // States which the automaton is in.
        $partialmatches = array();   // Possible partial matches.
        $fullmatchfound = false;

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subpattern(), $this->get_subpattern_map());
        $result->invalidate_match();

        // Creating identifiers for states
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate->number] = null;
            if ($curstate === $this->automaton->start_state()) {
                $states[$curstate->number] = new qtype_preg_nfa_processing_state(false, $result->index_first, $result->length, $result->index_first, $result->length,
                                                                                 qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                                 $curstate, null, 0, $result);
                $states[$curstate->number]->length[0] = 0;
            }
        }

        // Generating initial states.
        $zlc = $this->zero_length_closure($states[$this->automaton->start_state()->number], $str, $startpos);
        foreach ($zlc as $curstatezlc) {
            $states[$curstatezlc->state->number] = $curstatezlc;
            $curstates[] = $curstatezlc->state->number;
        }
        // Searching.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with newstates by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                foreach ($states[$curstate]->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if ($transition->pregleaf->match($str, $startpos + $states[$curstate]->length[0], $length, !$transition->pregleaf->caseinsensitive, $states[$curstate])) {
                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $states[$curstate]->index_first, $states[$curstate]->length, $states[$curstate]->index_first_new,
                                                                        $states[$curstate]->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, $length, $states[$curstate]);
                        $newstate->last_transition = $transition;
                        $newstate->length[0] += $length;
                        $this->write_tag_values($newstate, $states[$curstate], $transition, $startpos, $length);
                        // Saving the current result.
                        $zlc = $this->zero_length_closure($newstate, $str, $startpos);
                        foreach ($zlc as $curstatezlc) {
                            if ($curstatezlc->state === $this->automaton->end_state()) {
                                $curstatezlc->full = true;
                                $curstatezlc->left = 0;
                                $fullmatchfound = true;
                            }
                            $reached[] = $curstatezlc;
                        }
                    } else if (!$fullmatchfound) {    // Transition not matched, save the partial match.
                        // If a backreference matched partially - set corresponding fields.
                        $newresult = clone $states[$curstate];
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $newresult->length[0] += $length;
                            $newresult->last_transition = $transition;
                            $newresult->last_match_len = $length;
                            $fulllastmatch = false;
                        }
                        $newresult->set_source_info($newresult->str()->substring(0, $startpos + $newresult->length[0]), $this->get_max_subpattern(), $this->get_subpattern_map());

                        $path = $this->determine_characters_left($str, $startpos, $newresult, $fulllastmatch);
                        if ($path !== null) {
                            $newresult->left = $path->length[0] - $newresult->length[0];
                            $newresult->extendedmatch = new qtype_preg_matching_results($path->full, $path->index_first, $path->length, $path->left);
                            $newresult->extendedmatch->set_source_info($path->str(), $this->get_max_subpattern(), $this->get_subpattern_map());
                        }
                        // Finally, save the possible partial match.
                        $partialmatches[] = $newresult;
                    }
                }
            }
            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                $areequal = false;
                if ($states[$curstate->state->number] === null || $states[$curstate->state->number]->worse_than($curstate, false, false, $areequal)) {
                    $states[$curstate->state->number] = $curstate;
                    $newstates[] = $curstate->state->number;
                }
            }
            $curstates = $newstates;
        }
        // Find the best result.
        foreach ($states as $tmp) {
            if ($tmp !== null) {
                $tmp->state = null;
                $tmp->last_transition = null;
            }
        }
        foreach ($partialmatches as $tmp) {
            if ($tmp !== null) {
                $tmp->state = null;
                $tmp->last_transition = null;
            }
        }
        foreach ($states as $curresult) {
            $eq = false;
            if ($curresult !== null && $result->worse_than($curresult, false, false, $eq)) {
                $result = $curresult;
                $result->index_first[0] = $startpos;    // It's guaranteed that result->is_match() === true.
            }
        }
        foreach ($partialmatches as $curresult) {
            $eq = false;
            if ($curresult !== null && $result->worse_than($curresult, false, false, $eq)) {
                $result = $curresult;
                $result->index_first[0] = $startpos;    // It's guaranteed that result->is_match() === true.
            }
        }
        return new qtype_preg_matching_results($result->full, $result->index_first, $result->length, $result->left, $result->extendedmatch);
    }

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $node - object of nfa_preg_node child class.
     * @param $isassertion - will the result be a lookaround-assertion automaton.
     * @return - object of qtype_preg_nondeterministic_fa in case of success, false otherwise.
     */
    public function build_nfa($node, $isassertion = false) {
        $result = new qtype_preg_nondeterministic_fa();

        // create_automaton() can throw an exception in case of too large finite automaton (see qtype_preg_finite_automaton::set_limits()).
        try {
            $stack = array();
            $node->create_automaton($this, $result, $stack);

            if (!$isassertion) {
                /*
                // Add a dummy transitions to the beginning of the NFA.
                $start = new qtype_preg_fa_state($result);
                $epsleaf = new qtype_preg_leaf_meta;
                $epsleaf->subtype = qtype_preg_leaf_meta::SUBTYPE_EMPTY;
                $start->add_transition(new qtype_preg_nfa_transition($start, $epsleaf, $result->start_state(), false));
                $result->add_state($start);
                $result->set_start_state($start);

                // Add a dummy transitions to the end of the NFA.
                $end = new qtype_preg_fa_state($result);
                $epsleaf = new qtype_preg_leaf_meta;
                $epsleaf->subtype = qtype_preg_leaf_meta::SUBTYPE_EMPTY;
                $result->end_state()->add_transition(new qtype_preg_nfa_transition($result->end_state(), $epsleaf, $end, false));
                $result->add_state($end);
                $result->set_end_state($end);
                */
            } else {
                // TODO - all transitions should not consume characters.
            }
            $result->numerate_states();
        }
        catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function __construct($regex = null, $modifiers = null) {
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }

        $nfa = self::build_nfa($this->dst_root);
        if ($nfa !== false) {
            $this->automaton = $nfa;
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
