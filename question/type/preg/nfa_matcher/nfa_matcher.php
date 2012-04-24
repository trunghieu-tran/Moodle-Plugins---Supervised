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
 * Represents a state of an automaton when running.
 */
class qtype_preg_nfa_processing_state extends qtype_preg_matching_results implements qtype_preg_matcher_state {
    public $state;               // A reference to the state which automaton is in.
    public $index_first_new;     // Indexes of subpatterns being captured. Subpatterns which are already captured are stored in index_first.
    public $length_new;          // Same as previous field.
    public $last_transitions;    // An array of previous transitions.
    public $last_match_len;      // Length of the last match.

    public function __construct($full, $index_first, $length, $index_first_new, $length_new, $left, $extendedmatch,
                                &$state, $last_transitions, $last_match_len, $sourceobj) {
        parent::__construct($full, $index_first, $length, $left, $extendedmatch);
        $this->state = $state;
        $this->index_first_new = $index_first_new;
        $this->length_new = $length_new;
        $this->last_transitions = $last_transitions;
        $this->last_match_len = $last_match_len;
        $this->str = $sourceobj->str;
        $this->maxsubpatt = $sourceobj->maxsubpatt;
        $this->subpatternmap = $sourceobj->subpatternmap;
        $this->lexemcount = $sourceobj->lexemcount;
    }

    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {
        $parentresult = parent::worse_than($other, $orequal, $longestmatch, $areequal);
        if ($areequal === true) {
            // Leftmost rule
            foreach ($this->index_first as $key=>$value) {
                if ($key !== 0 && $value === qtype_preg_matching_results::NO_MATCH_FOUND && $other->index_first[$key] !== qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return true;
                } else if ($key !== 0 && $value !== qtype_preg_matching_results::NO_MATCH_FOUND && $other->index_first[$key] === qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return false;
                }
            }
            // Repeating rule
            foreach ($this->length as $key=>$value) {
                if ($key !== 0 && $this->index_first[$key] < $other->index_first[$key]) {
                    return true;
                } else if ($key !== 0 && $this->index_first[$key] > $other->index_first[$key]) {
                    return false;
                }
            }
        } else {
            return $parentresult;
        }
    }

    public function concatenate_char_to_str($char) {
        $this->str .= $char;
    }

}

class qtype_preg_nfa_matcher extends qtype_preg_matcher {

    public $automaton;    // An nfa corresponding to the given regex.

    /**
     * Returns prefix for the NFA engine class.
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

    public function zero_length_closure($startstate, $str, $startpos) {
        $startstateclone = new qtype_preg_nfa_processing_state(false, $startstate->index_first, $startstate->length, $startstate->index_first_new, $startstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                               $startstate->state, $startstate->last_transitions, $startstate->last_match_len, $startstate);

        $result = array($startstateclone);

        $curstates = array($startstateclone);
        while (count($curstates) != 0) {
            $newstates = array();
            // We'll replace curstates with newstates by the end of this cycle.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if (((is_a($transition->pregleaf, 'preg_leaf_backref') && array_key_exists($transition->pregleaf->number, $curstate->length) && $curstate->length[$transition->pregleaf->number] == 0) ||
                         (!is_a($transition->pregleaf, 'preg_leaf_backref') && !$transition->pregleaf->consumes())) &&
                         $transition->pregleaf->match($str, $startpos + $curstate->length[0], &$length, !$transition->pregleaf->caseinsensitive, $curstate)) {

                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_new, $curstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $curstate->last_transitions, $length, $curstate);
                        $newstate->last_transitions[] = $transition;
                        $newstate->length[0] += $length;
                        // Get subpattern info.
                        $subpatt_start = array();
                        $subpatt_end = array();
                        foreach ($transition->tags as $value) {
                            if ($value % 2 == 0) {
                                $subpatt_start[] = $value / 2;
                            } else {
                                $subpatt_end[] = ($value - 1) / 2;
                            }
                        }
                        // Set start indexes of subpatterns.
                        foreach ($subpatt_start as $number) {
                            $newstate->index_first_new[$number] = $startpos + $curstate->length[0];
                            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
                        }
                        // Set end indexes of subpatterns.
                        foreach ($subpatt_end as $number) {
                            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $newstate->length_new[$number] = $startpos + $curstate->length[0] - $newstate->index_first_new[$number] + $length;
                                // Rewrite old results of subpattern capturing.
                                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                                $newstate->length[$number] = $newstate->length_new[$number];
                            }
                        }
                        $skip = false;
                        foreach ($result as $skipstate) {
                            // Does this state already exist?
                            if ($skipstate->state === $newstate->state && $skipstate->index_first === $newstate->index_first && $skipstate->length === $newstate->length &&
                                $skipstate->index_first_new === $newstate->index_first_new && $skipstate->length_new === $newstate->length_new) {
                                $skip = true;
                                break;
                            }
                        }
                        // If not, save it.
                        if (!$skip) {
                            array_push($newstates, $newstate);
                            array_push($result, $newstate);
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
     * @param startpos - start position of matching.
     * @param laststate - the last state of the automaton, an object of qtype_preg_nfa_processing_state.
     * @param fulllastmatch - was the last transition captured fully, not partially?
     * @return - an object of qtype_preg_nfa_processing_state.
     */
    public function determine_characters_left($startpos, $laststate, $fulllastmatch) {
        $curstates = array();    // States which the automaton is in.
        $skipstates = array();
        $result = null;

        $lasttransition = null;
        if (count($laststate->last_transitions) != 0) {
            $lasttransition = $laststate->last_transitions[count($laststate->last_transitions) - 1];
        }
        if ($lasttransition === null || $fulllastmatch) {    // The last transition was fully-matched.
            // Check if an asserion $ failed the match and it's possible to remove a few characters.
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to === $this->automaton->end_state()) {
                    $result = clone $laststate;
                    $result->left = 0;
                    $result->full = true;
                    break;
                }
            }
            // Anyway, try the other paths to complete match.
            array_push($curstates, $laststate);
        } else {
            // The last partially matched transition was a backreference and we can only continue from this transition.
            $length = $laststate->length[$lasttransition->pregleaf->number] - $laststate->last_match_len;    // Number of characters left for this backreference.
            $newstate = new qtype_preg_nfa_processing_state(false, $laststate->index_first, $laststate->length, $laststate->index_first_new, $laststate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                            $lasttransition->to, $laststate->last_transitions, $laststate->length[$lasttransition->pregleaf->number], $laststate);
            // Re-write the string with correct characters.
            $newstate->concatenate_char_to_str($lasttransition->pregleaf->next_character($newstate->str(), $startpos + $laststate->length[0], $laststate->last_match_len, $laststate));
            $newstate->length[0] += $length;
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $curstate = array_pop($curstates);
                // If we have reached the end state and still have no result, or if the old result is worse than new - save the reached result.
                if ($curstate->state === $this->automaton->end_state() && ($result === null || ($result !== null && $result->length[0] > $curstate->length[0]))) {
                    $result = clone $curstate;
                    $result->left = 0;
                    $result->full = true;
                }
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    // Check for anchors.
                    $skip = (($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $curstate->length[0] > 0) ||                        // ^ in the middle.
                             ($transition->pregleaf->subtype == preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to !== $this->automaton->end_state()));    // $ in the middle.
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
                        if (is_a($transition->pregleaf, 'preg_leaf_backref')) {        // Only generated subpatterns can be passed.
                            if (array_key_exists($transition->pregleaf->number, $curstate->length) && $curstate->length[$transition->pregleaf->number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $length = $curstate->length[$transition->pregleaf->number];
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $transition->pregleaf->consumes($curstate);
                        }
                    }

                    $skip |= ($result !== null && $curstate->length[0] + $length > $result->length[0]);        // Is it longer then an existing one?

                    if (!$skip) {
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length, $curstate->index_first_new, $curstate->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $curstate->last_transitions, $length, $curstate);
                        $newstate->last_transitions[] = $transition;
                        // Generate a next character.
                        if ($length > 0) {
                            $newstate->concatenate_char_to_str($transition->pregleaf->next_character($newstate->str(), $startpos + $newstate->length[0], 0, $curstate));
                        }
                        $newstate->length[0] += $length;

                        // Get subpattern info from transition.
                        $subpatt_start = array();
                        $subpatt_end = array();
                        foreach ($transition->tags as $value) {
                            if ($value % 2 == 0) {
                                $subpatt_start[] = $value / 2;
                            } else {
                                $subpatt_end[] = ($value - 1) / 2;
                            }
                        }
                        // Set start indexes of subpatterns.
                        foreach ($subpatt_start as $number) {
                            $newstate->index_first_new[$number] = $startpos + $curstate->length[0];
                            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
                        }
                        // Set end indexes of subpatterns.
                        foreach ($subpatt_end as $number) {
                            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $newstate->length_new[$number] = $startpos + $curstate->length[0] - $newstate->index_first_new[$number] + $length;
                                // Rewrite old results of subpattern capturing.
                                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                                $newstate->length[$number] = $newstate->length_new[$number];
                            }
                        }
                        array_push($newstates, $newstate);
                    }
                }
                $skipstates[] = $curstate;
            }
            $curstates = $newstates;
        }
        if ($result !== null && $result->is_match()) {
            $result->index_first[0] = $startpos;
        }
        return $result;
    }

    /**
     * Returns the longest match using a string as input. Matching is proceeded from a given start position.
     * @param str - the original input string.
     * @param startpos - index of the start position to match.
     * @return - the longest character sequence matched.
     */
    public function match_from_pos($str, $startpos) {
        $states = array();
        $curstates = array();        // States which the automaton is in.
        $partialmatches = array();   // Possible partial matches.
        $fullmatchfound = false;

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);
        $result->invalidate_match();

        // Creating identifiers for states
        $idcounter = 0;
        $initialstateindex = -1;
        $endstateindex = -1;
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$idcounter] = null;
            if ($curstate === $this->automaton->start_state()) {
                $states[$idcounter] = new qtype_preg_nfa_processing_state(false, $result->index_first, $result->length, $result->index_first, $result->length, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                          $curstate, array(), 0, $result);
                $states[$idcounter]->length[0] = 0;
                $initialstateindex = $idcounter;
            }
            if ($curstate === $this->automaton->end_state()) {
                $endstateindex = $idcounter;
            }
            $curstate->id = $idcounter;
            $idcounter++;
        }
        // TODO: zero-length closure
        $zrc = $this->zero_length_closure($states[$initialstateindex], $str, $startpos);
        foreach ($zrc as $curstatezrc) {
            $states[$curstatezrc->state->id] = $curstatezrc;
            array_push($curstates, $curstatezrc->state->id);
        }

        // Initial state with nothing captured.
        //array_push($curstates, $initialstateindex);
        while (count($curstates) != 0) {
            $newstates = array();
            $reached = array();
            // We'll replace curstates with newstates by the end of this cycle.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                foreach ($states[$curstate]->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if ($transition->pregleaf->match($str, $startpos + $states[$curstate]->length[0], &$length, !$transition->pregleaf->caseinsensitive, $states[$curstate])) {
                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $states[$curstate]->index_first, $states[$curstate]->length, $states[$curstate]->index_first_new, $states[$curstate]->length_new, qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $states[$curstate]->last_transitions, $length, $states[$curstate]);
                        $newstate->last_transitions[] = $transition;
                        $newstate->length[0] += $length;
                        // Get subpattern info.
                        $subpatt_start = array();
                        $subpatt_end = array();
                        foreach ($transition->tags as $value) {
                            if ($value % 2 == 0) {
                                $subpatt_start[] = $value / 2;
                            } else {
                                $subpatt_end[] = ($value - 1) / 2;
                            }
                        }
                        // Set start indexes of subpatterns.
                        foreach ($subpatt_start as $number) {
                            $newstate->index_first_new[$number] = $startpos + $states[$curstate]->length[0];
                            $newstate->length_new[$number] = qtype_preg_matching_results::NO_MATCH_FOUND;
                        }
                        // Set end indexes of subpatterns.
                        foreach ($subpatt_end as $number) {
                            if ($newstate->index_first_new[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                $newstate->length_new[$number] = $startpos + $states[$curstate]->length[0] - $newstate->index_first_new[$number] + $length;
                                // Rewrite old results of subpattern capturing.
                                $newstate->index_first[$number] = $newstate->index_first_new[$number];
                                $newstate->length[$number] = $newstate->length_new[$number];
                            }
                        }
                        // Saving the current result.
                        //array_push($reached, $newstate);
                        $zrc = $this->zero_length_closure($newstate, $str, $startpos);
                        foreach ($zrc as $curstatezrc) {
                            if ($curstatezrc->state === $this->automaton->end_state()) {
                                $curstatezrc->full = true;
                                $curstatezrc->left = 0;
                                $fullmatchfound = true;
                            }
                            array_push($reached, $curstatezrc);
                        }
                    } else if (!$fullmatchfound) {    // Transition not matched, save the partial match.
                        // If a backreference matched partially - set corresponding fields.
                        $newresult = clone $states[$curstate];
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $newresult->length[0] += $length;
                            $newresult->last_transitions[] = $transition;
                            $newresult->last_match_len = $length;
                            $fulllastmatch = false;
                        }
                        // Go to the end state.
                        $newresult->set_source_info(substr($newresult->str(), 0, $startpos + $newresult->length[0]), $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);

                        $path = $this->determine_characters_left($startpos, $newresult, $fulllastmatch);
                        if ($path !== null) {
                            $newresult->left = $path->length[0] - $newresult->length[0];
                            $newresult->extendedmatch = new qtype_preg_matching_results($path->full, $path->index_first, $path->length, $path->left);
                            $newresult->extendedmatch->set_source_info($path->str(), $this->maxsubpatt, $this->subpatternmap, $this->lexemcount);
                        }
                        // Finally, save the possible partial match.
                        array_push($partialmatches, $newresult);
                    }
                }
            }
            // Replace curstates with newstates.
            foreach ($reached as $curstate) {
                $areequal = false;
                if ($states[$curstate->state->id] === null || $states[$curstate->state->id]->worse_than($curstate, false, false, &$areequal)) {
                    $states[$curstate->state->id] = $curstate;
                    array_push($newstates, $curstate->state->id);
                }
            }
            $curstates = $newstates;
        }
        // Find the best result.
        $matches = array_merge($states, $partialmatches);
        foreach ($matches as $curresult) {
            $eq = false;
            if ($curresult !== null && $result->worse_than($curresult, false, false, &$eq)) {
                $result = $curresult;
                $result->index_first[0] = $startpos;    // It's guaranteed that result->is_match() == true.
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