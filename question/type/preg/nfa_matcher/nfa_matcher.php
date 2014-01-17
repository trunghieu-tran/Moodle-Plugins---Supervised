<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines NFA matcher class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

/**
 * Represents a state of an automaton when running.
 */
class qtype_preg_nfa_processing_state extends qtype_preg_matching_results implements qtype_preg_matcher_state {
    public $state;           // A reference to the state which automaton is in.
    public $index_first_new; // Indexes of subpatterns being captured. Already captured subpatterns are stored in index_first.
    public $length_new;      // Same as previous field.
    public $last_transition; // The last transition matched.
    public $last_match_len;  // Length of the last match.

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
        foreach ($this->index_first as $key => $ind1) {
            if ($key === 0) {
                continue;
            }

            $ind2 = $other->index_first[$key];
            $len1 = $this->length[$key];
            $len2 = $other->length[$key];

            // Leftmost-longest rule.
            if (($ind2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind1 === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                ($ind2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind2 < $ind1) ||
                ($ind2 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind2 === $ind1 && $len2 > $len1)) {
                return true;
            }
            if (($ind1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind2 === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                ($ind1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind1 < $ind2) ||
                ($ind1 !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind1 === $ind2 && $len1 > $len2)) {
                return false;
            }
        }
        return false;
    }

    public function concat_chr($char) {
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
            default:
                return false;
        }
    }

    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return true;
            default:
                return get_string($pregnode->name(), 'qtype_preg');
        }
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
        if ($this->options !== null && !$this->options->capturesubpatterns) {
            return;
        }
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
        $startstateclone = new qtype_preg_nfa_processing_state(false, $startstate->index_first, $startstate->length,
                                                               $startstate->index_first_new, $startstate->length_new,
                                                               qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                               $startstate->state, $startstate->last_transition,
                                                               $startstate->last_match_len, $startstate);
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
                    if (!$transition->pregleaf->consumes($curstate) &&
                        $transition->pregleaf->match($str, $startpos + $curstate->length[0], $length, $curstate)) {
                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstate->index_first, $curstate->length,
                                                                        $curstate->index_first_new, $curstate->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
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
     * @param qtype_preg_nfa_processing_state laststate - the last state matched.
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
                if ($transition->pregleaf->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR &&
                    $transition->to === $this->automaton->end_state()) {
                    // The accepting state is reachable.
                    // TODO: zero-length closure.
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
            $remlength = $laststate->length[$lasttransition->pregleaf->number] - $laststate->last_match_len;
            $newstate = new qtype_preg_nfa_processing_state(false, $laststate->index_first, $laststate->length,
                                                            $laststate->index_first_new, $laststate->length_new,
                                                            qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                            $lasttransition->to, $laststate->last_transition,
                                                            $laststate->length[$lasttransition->pregleaf->number], $laststate);
            // Re-write the string with correct characters.
            $newchr = $lasttransition->pregleaf->next_character($newstate->str(), $startpos + $laststate->length[0],
                                                                $laststate->last_match_len, $laststate);
            $newstate->concat_chr($newchr);
            $newstate->length[0] += $remlength;

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
                $curstateobj = $states[$curstate];
                foreach ($curstateobj->state->outgoing_transitions() as $transition) {
                    // Check for anchors.
                    $subtype = $transition->pregleaf->subtype;
                    $dest = $transition->to;
                    $circfl = $subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX && $curstateobj->length[0] > 0;
                    $dollar = $subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR && $dest !== $this->automaton->end_state();
                    $skip = $circfl || $dollar;

                    if (!$skip) {
                        // Only generated subpatterns can be passed.
                        if (is_a($transition->pregleaf, 'qtype_preg_leaf_backref')) {
                            $number = $transition->pregleaf->number;
                            if (array_key_exists($number, $curstateobj->length) &&
                                $curstateobj->length[$number] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                                // Calculate length for the backreference.
                                $length = $curstateobj->length[$number];
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $transition->pregleaf->consumes($curstateobj);
                        }
                    }

                    // Is it longer then an existing one?
                    $skip |= ($states[$endstateid] !== null && $curstateobj->length[0] + $length > $states[$endstateid]->length[0]);

                    if (!$skip) {
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstateobj->index_first, $curstateobj->length,
                                                                        $curstateobj->index_first_new, $curstateobj->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, $length, $curstateobj);
                        $newstate->last_transition = $transition;
                        // Generate a next character.
                        if ($length > 0) {
                            $newchr = $transition->pregleaf->next_character($newstate->str(), $startpos + $newstate->length[0],
                                                                            0, $states[$curstate]);
                            $newstate->concat_chr($newchr);
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
                $curnum = $curstate->state->number;
                if ($states[$curnum] === null || $states[$curnum]->length[0] > $curstate->length[0]) {
                    $states[$curnum] = $curstate;
                    $newstates[] = $curnum;
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

        $maxsubpatt = $this->get_max_subpattern();
        $subpattmap = $this->get_subpattern_map();

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $maxsubpatt, $subpattmap);
        $result->invalidate_match();

        // Creating identifiers for states.
        foreach ($this->automaton->get_states() as $curstate) {
            $curnum = $curstate->number;
            $states[$curnum] = null;
            if ($curstate === $this->automaton->start_state()) {
                $states[$curnum] = new qtype_preg_nfa_processing_state(false, $result->index_first, $result->length,
                                                                       $result->index_first, $result->length,
                                                                       qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                       $curstate, null, 0, $result);
                $states[$curnum]->length[0] = 0;
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
                $curstateobj = $states[$curstate];
                foreach ($curstateobj->state->outgoing_transitions() as $transition) {
                    $length = 0;
                    if ($transition->pregleaf->match($str, $startpos + $curstateobj->length[0], $length, $curstateobj)) {
                        // Create a new state.
                        $newstate = new qtype_preg_nfa_processing_state(false, $curstateobj->index_first, $curstateobj->length,
                                                                        $curstateobj->index_first_new, $curstateobj->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, null, $length, $curstateobj);
                        $newstate->last_transition = $transition;
                        $newstate->length[0] += $length;
                        $this->write_tag_values($newstate, $curstateobj, $transition, $startpos, $length);
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
                        $newres = clone $curstateobj;
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $newres->length[0] += $length;
                            $newres->last_transition = $transition;
                            $newres->last_match_len = $length;
                            $fulllastmatch = false;
                        }
                        $newres->set_source_info($newres->str()->substring(0, $startpos + $newres->length[0]),
                                                 $maxsubpatt, $subpattmap);

                        $path = null;
                        // TODO: if ($this->options === null || $this->options->extensionneeded).
                        $path = $this->determine_characters_left($str, $startpos, $newres, $fulllastmatch);
                        if ($path !== null) {
                            $newres->left = $path->length[0] - $newres->length[0];
                            $newres->extendedmatch = new qtype_preg_matching_results($path->full, $path->index_first,
                                                                                     $path->length, $path->left);

                            $newres->extendedmatch->set_source_info($path->str(), $maxsubpatt, $subpattmap);
                        }
                        // Finally, save the possible partial match.
                        $partialmatches[] = $newres;
                    }
                }
            }
            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                $areequal = false;
                if ($states[$curstate->state->number] === null ||
                    $states[$curstate->state->number]->worse_than($curstate, false, false, $areequal)) {
                    // Currently stored state needs replacement.
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
        return new qtype_preg_matching_results($result->full, $result->index_first, $result->length,
                                               $result->left, $result->extendedmatch);
    }

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $node - object of nfa_preg_node child class.
     * @param $isassertion - will the result be a lookaround-assertion automaton.
     * @return - object of qtype_preg_nondeterministic_fa in case of success, false otherwise.
     */
    public function build_nfa($node, $isassertion = false) {
        $result = new qtype_preg_nondeterministic_fa();

        // The create_automaton() can throw an exception in case of too large finite automaton.
        try {
            $stack = array();
            $node->create_automaton($this, $result, $stack);
            /*
            if (!$isassertion) {
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
            } else {
                // TODO - all transitions should not consume characters.
            }*/
            $result->numerate_states();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function __construct($regex = null, $modifiers = null, $options = null) {
        parent::__construct($regex, $modifiers, $options);
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
