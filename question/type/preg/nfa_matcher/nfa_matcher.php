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
    public $captured_transitions;

    public function __construct($full, $index_first, $length, $index_first_new, $length_new, $left, $extendedmatch,
                                &$state, $last_transition, $last_match_len, $captured_transitions, $sourceobj) {
        parent::__construct($full, $index_first, $length, $left, $extendedmatch);
        $this->state = $state;
        $this->index_first_new = $index_first_new;
        $this->length_new = $length_new;
        $this->last_transition = $last_transition;
        $this->last_match_len = $last_match_len;
        $this->captured_transitions = $captured_transitions;
        $this->set_source_info($sourceobj->str, $sourceobj->maxsubpatt, $sourceobj->subpatternmap);
    }

    public function equals($to) {
        return ($this->state === $to->state &&
                $this->index_first == $to->index_first &&
                $this->length == $to->length);
    }

    /**
     * Resets the given subpattern to no match. In PCRE mode also resets all inner subpatterns.
     */
    public function reset_subpattern($number, $nested, $mode = qtype_preg_handling_options::MODE_PCRE) {
        $numbers = array($number);
        if ($mode == qtype_preg_handling_options::MODE_PCRE) {
            $numbers = array_merge($numbers, $nested);
        }
        foreach ($numbers as $num) {
            $this->index_first_new[$num] = self::NO_MATCH_FOUND;
            $this->length_new[$num] = self::NO_MATCH_FOUND;
        }
    }

    /**
     * Returns 1 if this beats other, -1 if other beats this, 0 otherwise.
     */
    public function leftmost_longest($other) {
        foreach ($this->index_first as $key => $ind_this) {
            if ($key == 0) {
                continue;
            }
            $ind_that = $other->index_first[$key];
            $len_this = $this->length[$key];
            $len_that = $other->length[$key];
            if (($ind_this !== self::NO_MATCH_FOUND && $ind_that === self::NO_MATCH_FOUND) ||
                ($ind_this !== self::NO_MATCH_FOUND && $ind_this < $ind_that) ||
                ($ind_this !== self::NO_MATCH_FOUND && $ind_this === $ind_that && $len_this > $len_that)) {
                return 1;
            }
            if (($ind_that !== self::NO_MATCH_FOUND && $ind_this === self::NO_MATCH_FOUND) ||
                ($ind_that !== self::NO_MATCH_FOUND && $ind_that < $ind_this) ||
                ($ind_that !== self::NO_MATCH_FOUND && $ind_that === $ind_this && $len_that > $len_this)) {
                return -1;
            }
        }
        return 0;
    }

    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {
        $parentresult = parent::worse_than($other, $orequal, $longestmatch, $areequal);
        if ($areequal === false) {
            return $parentresult;
        }

        // Leftmost rule.
        $leftmost = $this->leftmost_longest($other);
        if ($leftmost == -1) {
            return true;
        } else if ($leftmost == 1) {
            return false;
        }

        if ($areequal !== null) {
            $areequal = true;
        }

        return false;
    }

    /**
     * Writes subpatterns start\end information to this state.
     */
    public function write_subpatt_info($transition, $pos, $matchlen, $options) {
        if ($options !== null && !$options->capturesubpatterns) {
            return;
        }
        // Reset all found subpatterns to no match.
        foreach ($transition->subpatt_start as $node) {
            $this->reset_subpattern($node->number, $node->nested, $options->mode);
        }

        // Set start indexes of subpatterns.
        foreach ($transition->subpatt_start as $node) {
            $this->index_first_new[$node->number] = $pos;
        }
        // Set length of subpatterns.
        foreach ($transition->subpatt_end as $node) {
            if ($this->index_first_new[$node->number] != self::NO_MATCH_FOUND) {
                $this->length_new[$node->number] = $pos - $this->index_first_new[$node->number] + $matchlen;
                // Replace old results with new results.
                $this->index_first[$node->number] = $this->index_first_new[$node->number];
                $this->length[$node->number] = $this->length_new[$node->number];
            }
        }
    }

    public function concat_chr($char) {
        $this->str->concatenate($char);
    }

    public function dump() {
        foreach ($this->index_first as $key => $index) {
            $length = $this->length[$key];
            echo $key . ': (' . $index . ', ' . $length . ') ';
        }
        echo "\n";
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
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_nfa_processing_state startstate state to go from.
     * @param qtype_poasquestion_string str string being matched.
     * @param int startpos start position of matching.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    public function epsilon_closure($startstate, $str, $startpos) {
        $curstates = array($startstate);
        $result = array($startstate);

        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $startpos + $curstate->length[0];
                $length = 0;
                if ($transition->pregleaf->consumes($curstate) ||
                    !$transition->pregleaf->match($str, $curpos, $length, $curstate)) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $newstate->full = false;
                $newstate->state = $transition->to;
                $newstate->last_transition = $transition;
                $newstate->length[0] += $length;
                $newstate->last_match_len = $length;
                $newstate->captured_transitions[$transition->number] = true;
                $newstate->write_subpatt_info($transition, $curpos, $length, $this->options);

                // Does this state with same subpatt indexes exist in the result?
                $exists = false;
                foreach ($result as $res) {
                    if ($res->equals($newstate)) {
                        $exists = true;
                        break;
                    }
                }

                // If not, add it.
                if (!$exists) {
                    $curstates[] = $newstate;
                    $result[] = $newstate;
                }
            }
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
                    // TODO: epsilon-closure.
                    $states[$endstateid] = clone $laststate;
                    $states[$endstateid]->state = $this->automaton->end_state();
                    $states[$endstateid]->full = true;
                    $states[$endstateid]->left = 0;
                    break;
                }
            }
            // Anyway, try the other paths to complete match.
            $closure = $this->epsilon_closure($laststate, $str, $startpos);
            foreach ($closure as $curclosure) {
                $states[$curclosure->state->number] = $curclosure;
                $curstates[] = $curclosure->state->number;
            }
        } else {
            // The last partially matched transition was a backreference and we can only continue from this transition.
            $remlength = $laststate->length[$lasttransition->pregleaf->number] - $laststate->last_match_len;

            $newstate = clone $laststate;
            $newstate->state = $lasttransition->to;
            $newstate->length[0] += $remlength;
            $newstate->last_transition = $lasttransition;
            $newstate->last_match_len = $laststate->length[$lasttransition->pregleaf->number];
            $newstate->captured_transitions[$lasttransition->number] = true;
            //$newstate->write_subpatt_info($transition, $curpos, $length, $this->options);   // TODO: is it needed?

            // Re-write the string with correct characters.
            $newchr = $lasttransition->pregleaf->next_character($newstate->str(), $startpos + $laststate->length[0],
                                                                $laststate->last_match_len, $laststate);
            $newstate->concat_chr($newchr);

            $closure = $this->epsilon_closure($newstate, $str, $startpos);
            foreach ($closure as $curclosure) {
                $states[$curclosure->state->number] = $curclosure;
                $curstates[] = $curclosure->state->number;
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
                        /*$newstate = clone $curstateobj;
                        $newstate->state = $transition->to;
                        $newstate->length[0] += $length;
                        $newstate->last_transition = $transition;
                        $newstate->last_match_len = $length;
                        $newstate->write_subpatt_info($transition, $startpos + $curstateobj->length[0], $length, $this->options);
                        $newstate->left = -1;   TODO: replace to this.
                        captured_transitions???
                        $newstate->extendedmatch = null;*/

                        $newstate = new qtype_preg_nfa_processing_state(false, $curstateobj->index_first, $curstateobj->length,
                                                                        $curstateobj->index_first_new, $curstateobj->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $transition, $length, $curstateobj->captured_transitions, $curstateobj);
                        $newstate->length[0] += $length;
                        $newstate->write_subpatt_info($transition, $startpos + $curstateobj->length[0], $length, $this->options);

                        // Generate a next character.
                        if ($length > 0) {
                            $newchr = $transition->pregleaf->next_character($newstate->str(), $startpos + $newstate->length[0],
                                                                            0, $curstateobj);
                            $newstate->concat_chr($newchr);
                        }

                        // Saving the current result.
                        $closure = $this->epsilon_closure($newstate, $str, $startpos);
                        foreach ($closure as $curclosure) {
                            if ($curclosure->state === $this->automaton->end_state()) {
                                $curclosure->full = true;
                                $curclosure->left = 0;
                            }
                            $reached[] = $curclosure;
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
                                                                       $curstate, null, 0, array(), $result);
                $states[$curnum]->length[0] = 0;
            }
        }

        // Generating initial states.
        $closure = $this->epsilon_closure($states[$this->automaton->start_state()->number], $str, $startpos);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state->number] = $curclosure;
            $curstates[] = $curclosure->state->number;
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
                    $curpos = $startpos + $curstateobj->length[0];
                    $length = 0;
                    if ($transition->pregleaf->match($str, $curpos, $length, $curstateobj)) {
                        // Create a new state.
                        $newstate = clone $curstateobj;
                        $newstate->full = false;
                        $newstate->state = $transition->to;
                        $newstate->last_transition = $transition;
                        $newstate->length[0] += $length;
                        $newstate->last_match_len = $length;
                        $newstate->captured_transitions[$transition->number] = true;
                        $newstate->write_subpatt_info($transition, $curpos, $length, $this->options);

                        // Saving the current result.
                        $closure = $this->epsilon_closure($newstate, $str, $startpos);
                        foreach ($closure as $curclosure) {
                            if ($curclosure->state === $this->automaton->end_state()) {
                                $curclosure->full = true;
                                $curclosure->left = 0;
                                $fullmatchfound = true;
                            }
                            $reached[] = $curclosure;
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
        foreach ($states as $curresult) {
            $areequal = false;
            if ($curresult !== null && $result->worse_than($curresult, false, false, $areequal)) {
                $result = $curresult;
                $result->index_first[0] = $startpos;    // It's guaranteed that result->is_match() === true.
            }
        }
        if (!$fullmatchfound) {
            foreach ($partialmatches as $curresult) {
                $areequal = false;
                if ($curresult !== null && $result->worse_than($curresult, false, false, $areequal)) {
                    $result = $curresult;
                    $result->index_first[0] = $startpos;    // It's guaranteed that result->is_match() === true.
                }
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
            $transitioncounter = 0;
            $node->create_automaton($this, $result, $stack, $transitioncounter);
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
