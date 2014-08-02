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
 * Defines FA matcher class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_exec_state.php');

class qtype_preg_fa_matcher extends qtype_preg_matcher {

    // FA corresponding to the regex
    public $automaton = null;   // for testing purposes

    // Map of nested subpatterns:  (subpatt number => nested qtype_preg_node objects)
    protected $nestingmap = array();

    // States to backtrack to when generating extensions
    protected $backtrackstates = array();

    // Should we generate extensions for each match before choosing the best one?
    protected $generateextensionforeachmatch = false;

    // Should we call bruteforce method to find a match?
    protected $bruteforcematch = false;

    // Should we call bruteforce method to generate a partial match extension?
    protected $bruteforcegeneration = false;

    public function name() {
        return 'fa_matcher';
    }

    protected function get_engine_node_name($nodetype, $nodesubtype) {
        switch($nodetype) {
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_CONCAT:
            case qtype_preg_node::TYPE_NODE_ALT:
            case qtype_preg_node::TYPE_NODE_SUBEXPR:
            case qtype_preg_node::TYPE_NODE_COND_SUBEXPR:
                return 'qtype_preg_fa_' . $nodetype;
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
            //case qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL:
                return 'qtype_preg_fa_leaf';
        }

        return parent::get_engine_node_name($nodetype, $nodesubtype);
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
            case qtype_preg_matcher::SUBEXPRESSION_CAPTURING:
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
            //case qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return true;
            default:
                return get_string($pregnode->type, 'qtype_preg');
        }
    }

    /**
     * Creates a processing state object for the given state filled with "nomatch" values.
     */
    protected function create_initial_state($state, $str, $startpos, $prevlevelstate) {
        if ($prevlevelstate !== null) {
            $result = clone $prevlevelstate;
            $result->recursionlevel = $prevlevelstate->recursionlevel + 1;
            $result->state = $state;
            return $result;
        }

        $result = new qtype_preg_fa_exec_state();
        $result->matcher = $this;
        $result->recursionlevel = 0;
        $result->state = $state;

        $result->matches = array();
        $result->subexpr_to_subpatt = array(0 => $this->astroot);   // Remember this explicitly
        $result->startpos = $startpos;
        $result->length = 0;

        $result->flags = 0x00;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;
        $result->backtrack_states = array();
        if (in_array($state, $this->backtrackstates)) {
            $result->backtrack_states[] = $result;
        }

        return $result;
    }

    protected function create_nomatch_result($str) {
        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_map());
        $result->invalidate_match();
        return $result;
    }

    protected function before_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr = 0) {
        //$newstate->write_tag_values($transition, $curpos, $length);
    }

    /**
     * Updates all fields in the newstate after a transition match
     */
    protected function after_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr = 0) {
        $endstates = $this->automaton->end_states($subexpr);

        $newstate->state = $transition->to;

        $newstate->set_full(in_array($newstate->state, $endstates));
        if ($transition->is_start_anchor()) {
            $newstate->set_flag(qtype_preg_fa_exec_state::FLAG_VISITED_START_ANCHOR);
        }
        if ($transition->is_end_anchor()) {
            $newstate->set_flag(qtype_preg_fa_exec_state::FLAG_VISITED_END_ANCHOR);
        }
        $newstate->left = $newstate->is_full() ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $newstate->last_transition = $transition;
        $newstate->last_match_len = $length;

        $newstate->length += $length;
        $newstate->write_tag_values($transition, qtype_preg_fa_transition::TAG_POS_AT, $curpos, $length);
        //$newstate->write_tag_values($transition, qtype_preg_fa_transition::TAG_POS_AFTER, $curpos, $length);

        if (in_array($transition->to, $this->backtrackstates)) {
            $newstate->backtrack_states[] = $curstate;
        }
    }

    /**
     * Check if a fa_exec_state instance has
     */
    protected function is_state_ok_for_subexpr_call($state, $subexpr = 0) {
        if ($subexpr == 0) {
            return true;
        }
        return $state->is_subexpr_opened($subexpr) && !$state->has_duplicate_subexpression();
    }

    /**
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_fa_exec_state startstates states to go from.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    protected function epsilon_closure($startstates, $subexpr = 0) {
        $curstates = $startstates;
        $result = array(qtype_preg_fa_transition::GREED_LAZY => array(),
                        qtype_preg_fa_transition::GREED_GREEDY => $startstates
                        );
        while (!empty($curstates)) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
            foreach ($transitions as $transition) {
                if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                    // If transition has zero greediness ({0}-quantified) it should be skipped unless
                    // matching called explicitly via $subexpr > 0
                    continue;
                }

                $curpos = $curstate->startpos + $curstate->length;
                $length = 0;
                if ($transition->pregleaf->subtype != qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $this->before_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);
                $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);

                // Resolve ambiguities if any.
                $number = $newstate->state;
                $key = $transition->greediness == qtype_preg_fa_transition::GREED_LAZY
                     ? qtype_preg_fa_transition::GREED_LAZY
                     : qtype_preg_fa_transition::GREED_GREEDY;
                if (!isset($result[$key][$number]) || $newstate->leftmost_longest($result[$key][$number])) {
                    $result[$key][$number] = $newstate;
                    if ($key != qtype_preg_fa_transition::GREED_LAZY) {
                        $curstates[] = $newstate;
                    }
                }
            }
        }
        return $result;
    }

    protected function get_resume_state($str, $laststate, $subexpr = 0) {
        $endstates = $this->automaton->end_states($subexpr);

        if ($laststate->last_match_len > 0 && $laststate->last_transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            // The last transition was a partially matched backreference; we can only continue from this transition.
            $backref_length = $laststate->length($laststate->last_transition->pregleaf->number);
            $prevpos = $laststate->startpos + $laststate->length - $laststate->last_match_len;

            $resumestate = clone $laststate;
            $this->before_transition_matched($laststate, $resumestate, $laststate->last_transition, $prevpos, $backref_length, $subexpr);
            $this->after_transition_matched($laststate, $resumestate, $laststate->last_transition, $prevpos, $backref_length, $subexpr);
            $resumestate->length -= $laststate->last_match_len; // Backreference was partially matched

            // Re-write the string with correct characters.
            list($flag, $newchr) = $laststate->last_transition->pregleaf->next_character($str, $resumestate->str, $prevpos, $laststate->last_match_len, $laststate);
            if ($newchr != null) {
                $resumestate->str->concatenate($newchr);
            }

            return $resumestate;
        }

        // There was no match at all, or the last transition was fully-matched.
        $curpos = $laststate->startpos + $laststate->length;

        // Check for a \Z \z or $ assertion before the eps-closure of the end state. Then it's possible to remove few characters.
        $transitions = $this->automaton->get_adjacent_transitions($laststate->state, true);
        foreach ($transitions as $transition) {
            if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                // If transition has zero greediness ({0}-quantified) it should be skipped unless
                // matching called explicitly via $subexpr > 0
                continue;
            }
            if ($transition->loopsback || !($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $transition->pregleaf->is_end_anchor())) {
                continue;
            }
            $closure = $this->epsilon_closure(array($laststate->state => $laststate), $subexpr);
            $closure = array_merge($closure[qtype_preg_fa_transition::GREED_LAZY], $closure[qtype_preg_fa_transition::GREED_GREEDY]);
            foreach ($closure as $curclosure) {
                if (in_array($curclosure->state, $endstates)) {
                    // The end state is reachable; return it immediately.
                    $result = clone $laststate;
                    $this->before_transition_matched($laststate, $result, $transition, $curpos, 0, $subexpr);
                    $this->after_transition_matched($laststate, $result, $transition, $curpos, 0, $subexpr);
                    return $result;
                }
            }
        }

        return $laststate;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str - original string that was matched.
     * @param qtype_preg_fa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_fa_exec_state.
     */
    protected function generate_extension_brute_force($str, $laststate, $subexpr = 0) {
        $endstates = $this->automaton->end_states($subexpr);
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state, $endstates)) {
            return $resumestate;
        }

        $curstates = array($resumestate);
        $result = null;

        while (!empty($curstates)) {
            $curstate = array_pop($curstates);
            if (in_array($curstate->state, $endstates) && ($result === null || $result->length > $curstate->length)) {
                $result = $curstate;
            }
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
            foreach ($transitions as $transition) {
                if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                    // If transition has zero greediness ({0}-quantified) it should be skipped unless
                    // matching called explicitly via $subexpr > 0
                    continue;
                }
                // Skip loops.
                if ($transition->loopsback) {
                    continue;
                }

                // Only generated subpatterns can be passed.
                $length = $transition->pregleaf->consumes($curstate);
                if ($length == qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                    continue;
                }

                if ($length > 0 && $curstate->is_flag_set(qtype_preg_fa_exec_state::FLAG_VISITED_END_ANCHOR)) {
                    continue;
                }

                // Is it longer than existing one?
                if ($result !== null && $curstate->length + $length > $result->length) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $this->before_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length, $subexpr);
                $this->after_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length, $subexpr);

                // Generate a next character.
                //if ($length > 0) {
                    $prevpos = $newstate->startpos + $newstate->length - $length;
                    list($flag, $newchr) = $transition->pregleaf->next_character($str, $newstate->str, $prevpos, 0, $curstate);
                    if ($newchr != null) {
                        $newstate->str->concatenate($newchr);
                    }
                //}

                // Save the new state.
                if ($flag != qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE) {
                    $curstates[] = $newstate;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str - original string that was matched.
     * @param qtype_preg_fa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_fa_exec_state.
     */
    protected function generate_extension_fast($str, $laststate, $subexpr = 0) {
        $endstates = $this->automaton->end_states($subexpr);
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state, $endstates)) {
            return $resumestate;
        }

        $states = array();
        $curstates = array();

        // Create an array of processing states for all fa states (the only resumestate, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate] = $curstate === $resumestate->state
                               ? $resumestate
                               : null;
        }

        // Get an epsilon-closure of the resume state.
        $closure = $this->epsilon_closure(array($resumestate->state => $resumestate), $subexpr);
        $closure = array_merge($closure[qtype_preg_fa_transition::GREED_LAZY], $closure[qtype_preg_fa_transition::GREED_GREEDY]);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state] = $curclosure;
            $curstates[] = $curclosure->state;
        }

        $result = null;

        // Do search.
        while (!empty($curstates)) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                if ($curstate->is_full()) {
                    if ($result === null || $curstate->leftmost_shortest($result)) {
                        $result = $curstate;
                    }
                }
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
                foreach ($transitions as $transition) {
                    if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                        // If transition has zero greediness ({0}-quantified) it should be skipped unless
                        // matching called explicitly via $subexpr > 0
                        continue;
                    }
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    // Skip loops.
                    if ($transition->loopsback) {
                        continue;
                    }

                    // Only generated subpatterns can be passed.
                    $length = $transition->pregleaf->consumes($curstate);
                    if ($length == qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                        continue;
                    }

                    if ($length > 0 && $curstate->is_flag_set(qtype_preg_fa_exec_state::FLAG_VISITED_END_ANCHOR)) {
                        continue;
                    }

                    // Is it longer than existing one?
                    if ($result !== null && $curstate->length + $length > $result->length) {
                        continue;
                    }

                    // Create a new state.
                    $newstate = clone $curstate;
                    $this->before_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length, $subexpr);
                    $this->after_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length, $subexpr);

                    // Generate a next character.
                    //if ($length > 0) {
                        $prevpos = $newstate->startpos + $newstate->length - $length;
                        list($flag, $newchr) = $transition->pregleaf->next_character($str, $newstate->str, $prevpos, 0, $curstate);
                        if ($newchr != null) {
                            $newstate->str->concatenate($newchr);
                        }
                    //}

                    // Save the current result.
                    $number = $newstate->state;
                    if ($flag != qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE && (!isset($reached[$number]) || $newstate->leftmost_shortest($reached[$number]))) {
                        $reached[$number] = $newstate;
                    }
                }
            }

            $reached = $this->epsilon_closure($reached, $subexpr);
            $reached = array_merge($reached[qtype_preg_fa_transition::GREED_LAZY], $reached[qtype_preg_fa_transition::GREED_GREEDY]);

            // Replace curstates with reached.
            foreach ($reached as $curstate) {
                // Currently stored state needs replacement if it's null, or if it's worse than the new state.
                if ($states[$curstate->state] === null || $curstate->leftmost_shortest($states[$curstate->state])) {
                    $states[$curstate->state] = $curstate;
                    $curstates[] = $curstate->state;
                }
            }
        }
        return $result;
    }

    /**
     * This method should be used if there are backreferences in the regex.
     * Returns array of all possible matches.
     */
    protected function match_brute_force($str, $startpos, $subexpr = 0, $prevlevelstate = null) {
        $fullmatches = array();       // Possible full matches.
        $partialmatches = array();    // Possible partial matches.

        $curstates = array();    // States which the automaton is in at the current wave front.
        $lazystates = array();   // States reached lazily.

        foreach ($this->automaton->start_states($subexpr) as $state) {
            $curstates[] = $this->create_initial_state($state, $str, $startpos, $prevlevelstate);
        }

        // Do search.
        $firststep = true;
        while (!empty($curstates)) {
            $reached = array();
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                if ($curstate->is_full()) {
                    $fullmatches[] = $curstate;
                }
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
                foreach ($transitions as $transition) {
                    if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                        // If transition has zero greediness ({0}-quantified) it should be skipped unless
                        // matching called explicitly via $subexpr > 0
                        continue;
                    }
                    $curpos = $startpos + $curstate->length;
                    $length = 0;
                    //echo "trying $transition at pos $curpos (recursion level: $curstate->recursionlevel)\n";

                    $newstate = clone $curstate;
                    $this->before_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);

                    $matcherstateobj = $transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL
                                     ? clone $newstate
                                     : $newstate;

                    if ($transition->pregleaf->match($str, $curpos, $length, $matcherstateobj)) {
                        // Create a new state.
                        $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);
                        //$tmp = core_text::substr($str, $curpos, $length);
                        //echo "MATCHED $transition with '$tmp' at pos $curpos (recursion level: $curstate->recursionlevel)\n";
                        //echo "total length is {$curstate->length} : {$newstate->length}\n\n";

                        // Additional filtering for subexpression calls
                        $skip = $firststep && !$this->is_state_ok_for_subexpr_call($newstate, $subexpr);

                        // Save the current match.
                        if (!$skip && !($transition->loopsback && $newstate->has_null_iterations())) {
                            if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                                $lazystates[] = $newstate;
                            } else {
                                $reached[] = $newstate;
                            }
                        }
                    } else if (empty($fullmatches) && $subexpr == 0) {
                        //echo "not matched, partial match length is $length\n";
                        // Transition not matched, save the partial match.
                        $newstate->length += $length;
                        $newstate->last_transition = $transition;
                        $newstate->last_match_len = $length;
                        $partialmatches[] = $newstate;
                    }
                }

                // If there's no full match yet and no states reached, try the lazy ones.
                if (empty($fullmatches) && empty($reached) && !empty($lazystates)) {
                    $reached[] = array_pop($lazystates);
                }
            }
            $curstates = $reached;
            $firststep = false;
        }

        // Return array of all possible matches.
        $result = array();
        foreach ($fullmatches as $match) {
            $result[] = $match;
        }
        if (empty($fullmatches)) {
            foreach ($partialmatches as $partialmatch) {
                $result[] = $partialmatch;
            }
        }
        return $result;
    }

    /**
     * This method should be used if there are no backreferences in the regex.
     * Returns array of all possible matches.
     */
    protected function match_fast($str, $startpos, $subexpr = 0, $prevlevelstate = null) {
        $states = array();           // Objects of qtype_preg_fa_exec_state.
        $curstates = array();        // Numbers of states which the automaton is in at the current wave front.
        $lazystates = array();       // States (objects!) reached lazily.
        $partialmatches = array();   // Possible partial matches.

        $startstates = $this->automaton->start_states($subexpr);
        $endstates = $this->automaton->end_states($subexpr);

        $endstatereached = false;

        // Create an array of processing states for all fa states (the only initial state, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate] = in_array($curstate, $startstates)
                               ? $this->create_initial_state($curstate, $str, $startpos, $prevlevelstate)
                               : null;
        }

        // Get an epsilon-closure of the initial state.
        foreach ($states as $state) {
            if ($state !== null) {
                $curstates[] = $state;
            }
        }

        $closure = $this->epsilon_closure($curstates, $subexpr);
        $lazystates = array_merge($lazystates, $closure[qtype_preg_fa_transition::GREED_LAZY]);
        $closure = $closure[qtype_preg_fa_transition::GREED_GREEDY];
        $curstates = array();
        foreach ($closure as $curclosure) {
            $states[$curclosure->state] = $curclosure;
            $curstates[] = $curclosure->state;
            $endstatereached = $endstatereached || $curclosure->is_full();
        }

        // Do search.
        $firststep = true;
        while (!empty($curstates)) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
                foreach ($transitions as $transition) {
                    if ($transition->greediness == qtype_preg_fa_transition::GREED_ZERO && $subexpr == 0) {
                        // If transition has zero greediness ({0}-quantified) it should be skipped unless
                        // matching called explicitly via $subexpr > 0
                        continue;
                    }
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    $curpos = $startpos + $curstate->length;
                    $length = 0;
                    //echo "trying $transition at pos $curpos (recursion level: $curstate->recursionlevel)\n";

                    $newstate = clone $curstate;
                    $this->before_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);

                    $matcherstateobj = $transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL
                                     ? clone $newstate
                                     : $newstate;

                    if ($transition->pregleaf->match($str, $curpos, $length, $matcherstateobj)) {
                        // Create a new state.
                        $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length, $subexpr);

                        $endstatereached = $endstatereached || $newstate->is_full();
                        //$tmp = core_text::substr($str, $curpos, $length);
                        //echo "MATCHED $transition with '$tmp' at pos $curpos (recursion level: $curstate->recursionlevel)\n";
                        //echo "total length is {$curstate->length} : {$newstate->length}\n\n";
                        // Save the current result.
                        if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                            // Additional filtering for subexpression calls
                            $skip = $firststep && !$this->is_state_ok_for_subexpr_call($newstate, $subexpr);
                            if (!$skip) {
                                $lazystates[] = $newstate;
                            }
                        } else {
                            $number = $newstate->state;
                            if ((!isset($reached[$number]) || $newstate->leftmost_longest($reached[$number])) &&                    // $reached contains a worse state
                                ($states[$newstate->state] === null || $newstate->leftmost_longest($states[$newstate->state]))) {   // $states does not contain a better state
                                $reached[$number] = $newstate;
                            }
                        }
                    } else if (!$endstatereached && $subexpr == 0) {
                        //echo "not matched, partial match length is $length\n";
                        // Transition not matched, save the partial match.
                        $newstate->length += $length;
                        $newstate->last_transition = $transition;
                        $newstate->last_match_len = $length;
                        $partialmatches[] = $newstate;
                    }
                }
            }

            // If there's no full match yet and no states reached, try the lazy ones.
            if (!$endstatereached && empty($reached) && !empty($lazystates)) {
                $reached[] = array_pop($lazystates);
            }

            $reached = $this->epsilon_closure($reached, $subexpr);
            $lazystates = array_merge($lazystates, $reached[qtype_preg_fa_transition::GREED_LAZY]);
            $reached = $reached[qtype_preg_fa_transition::GREED_GREEDY];

            // Replace curstates with reached.
            foreach ($reached as $newstate) {
                // Additional filtering for subexpression calls
                $skip = $firststep && !$this->is_state_ok_for_subexpr_call($newstate, $subexpr);
                // Currently stored state needs replacement if it's null, or if it's worse than the new state.
                if (!$skip && ($states[$newstate->state] === null || $newstate->leftmost_longest($states[$newstate->state]))) {
                    $states[$newstate->state] = $newstate;
                    $curstates[] = $newstate->state;
                    $endstatereached = $endstatereached || $newstate->is_full();
                }
            }
            $firststep = false;
        }

        // Return array of all possible matches.
        $result = array();
        foreach ($endstates as $endstate) {
            if ($states[$endstate] !== null) {
                $result[] = $states[$endstate];
            }
        }
        if (empty($result)) {
            foreach ($partialmatches as $partialmatch) {
                $result[] = $partialmatch;
            }
        }
        return $result;
    }

    protected function generate_extensions($matches, $str, $startpos, $subexpr = 0, $prevlevelstate = null) {
        foreach ($matches as $match) {
            $match->extendedmatch = null;
            // Try each backtrack state and choose the shortest one.
            $match->backtrack_states = array_merge(array($match), $match->backtrack_states);
            foreach ($match->backtrack_states as $backtrack) {
                $backtrack->str = $backtrack->str->substring(0, $startpos + $backtrack->length);

                $tmp = $this->bruteforcegeneration
                     ? $this->generate_extension_brute_force($str, $backtrack, $subexpr)
                     : $this->generate_extension_fast($str, $backtrack, $subexpr);

                if ($tmp === null) {
                    continue;
                }

                // Calculate 'left'.
                $prefixlen = $startpos;
                while ($prefixlen < $match->str->length() && $prefixlen < $tmp->str->length() &&
                       $match->str[$prefixlen] == $tmp->str[$prefixlen]) {
                    $prefixlen++;
                }
                $left = $tmp->str->length() - $prefixlen;
                // Choose the best one by:
                // 1) minimizing length of the generated extension
                // 2) minimizing abs(extension->length - match->length)
                if ($match->extendedmatch === null) {
                    $match->extendedmatch = $tmp;
                    $match->left = $left;
                } else if (($match->left > $left) ||
                           ($match->left == $left && abs($match->extendedmatch->length - $match->length) > abs($tmp->length - $match->length))) {
                    $match->extendedmatch = $tmp;
                    $match->left = $left;
                }
            }
        }
    }

    public function match_from_pos_internal($str, $startpos, $subexpr = 0, $prevlevelstate = null) {
        //$recursionlevel = $prevlevelstate == null ? 0 : $prevlevelstate->recursionlevel + 1;
        //echo "======================== $recursionlevel\n";

        if ($prevlevelstate !== null && $prevlevelstate->recursionlevel > 3) {
            return $this->create_initial_state(null, $str, $startpos, $prevlevelstate);
        }

        // Find all possible matches. Using the fast match method if there are no backreferences.
        $possiblematches = $this->bruteforcematch
                         ? $this->match_brute_force($str, $startpos, $subexpr, $prevlevelstate)
                         : $this->match_fast($str, $startpos, $subexpr, $prevlevelstate);

        if (empty($possiblematches)) {
            return $this->create_initial_state(null, $str, $startpos, $prevlevelstate);
        }

        // Check if a full match was found.
        $fullmatchexists = false;
        foreach ($possiblematches as $match) {
            if ($match->is_full()) {
                $fullmatchexists = true;
                break;
            }
        }

        // If there was no full match, generate extensions for each partial match.
        if (!$fullmatchexists && $this->generateextensionforeachmatch && $this->options->extensionneeded) {
            $this->generate_extensions($possiblematches, $str, $startpos, $subexpr, $prevlevelstate);
        }

        // Choose the best one.
        $result = array_pop($possiblematches);
        foreach ($possiblematches as $match) {
            if ($match->leftmost_longest($result, false)) {
                $result = $match;
            }
        }

        if (!$fullmatchexists && !$this->generateextensionforeachmatch && $this->options->extensionneeded) {
            $this->generate_extensions(array($result), $str, $startpos, $subexpr, $prevlevelstate);
        }

        return $result;
    }

    public function match_from_pos($str, $startpos, $subexpr = 0, $prevlevelstate = null) {
        $result = $this->match_from_pos_internal($str, $startpos);

        if ($result->extendedmatch !== null) {
            $result->extendedmatch = $result->extendedmatch->to_matching_results();
        }

        return $result->to_matching_results();
    }

    public function get_nested_nodes($subpatt) {
        return array_key_exists($subpatt, $this->nestingmap) ? $this->nestingmap[$subpatt] : array();
    }

    protected function calculate_nesting_map($node, $currentkeys = array()) {
        if (is_a($node, 'qtype_preg_leaf')) {
            return;
        }
        foreach ($node->operands as $operand) {
            $newkeys = $operand->subpattern === -1
                     ? $currentkeys
                     : array_merge($currentkeys, array($operand->subpattern));

            $this->calculate_nesting_map($operand, $newkeys);

            if ($operand->subpattern === -1) {
                continue;
            }

            foreach ($currentkeys as $subpatt) {
                $this->nestingmap[$subpatt][] = $operand;
            }
        }
    }

    protected function calculate_backtrackstates() {
        $this->backtrackstates = $this->automaton->calculate_backtrack_states();
    }

    /**
     * Check if it's necessary to generate extensions for each possible match. For now the only such situation
     * is when a transition ending a quantifier has non-empty intersection with next transitions.
     */
    protected function calculate_generateextensionforeachmatch() {
        $this->generateextensionforeachmatch = !empty($this->backtrackstates);
    }

    protected function calculate_bruteforce() {
        foreach ($this->get_nodes_with_subexpr_refs() as $node) {
            // Recursion leafs are kinda subexpr references but they don't cause bruteforce
            if ($node->type != qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
                $this->bruteforcematch = true;
                $this->bruteforcegeneration = true;
                break;
            }
        }

        $this->bruteforcegeneration = $this->bruteforcegeneration && !empty($this->backtrackstates);
    }

    /**
     * Constructs an FA corresponding to the given node.
     * @return - object of qtype_preg_fa in case of success, null otherwise.
     */
    protected function build_fa() {
        $result = new qtype_preg_fa($this, $this->get_nodes_with_subexpr_refs());

        // The create_automaton() can throw an exception in case of too large finite automaton.
        //try {
            $stack = array();
            $this->dstroot->create_automaton($result, $stack);
            $body = array_pop($stack);
            $result->calculate_subexpr_start_and_end_states();
            //printf($result->fa_to_dot() . "\n");
            //$result->remove_unreachable_states();     TODO27
            //printf($result->fa_to_dot() . "\n");
            //var_dump($result->start_states());
            //var_dump($result->end_states());
            //$result->merge_uncapturing_transitions(qtype_preg_fa_transition::TYPE_TRANSITION_BOTH);
        //} catch (Exception $e) {
          //  $result = null;
        //}
        return $result;
    }

    public function __construct($regex = null, $options = null) {
        parent::__construct($regex, $options);

        if (!isset($regex) || !empty($this->errors)) {
            return;
        }

        $this->automaton = self::build_fa();
        if ($this->automaton === null) {
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
            return;
        }

        $this->calculate_nesting_map($this->astroot, array($this->astroot->subpattern));
        $this->calculate_backtrackstates();
        $this->calculate_generateextensionforeachmatch();
        $this->calculate_bruteforce();

        // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
        // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
        // $this->automaton->on_subexpr_added($this->astroot);
    }
}
