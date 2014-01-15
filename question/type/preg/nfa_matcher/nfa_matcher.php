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
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_exec_state.php');

class qtype_preg_nfa_matcher extends qtype_preg_matcher {

    public $automaton = null;          // An NFA corresponding to the given regex.

    protected $nestingmap = array();   // Array (subpatt number => nested qtype_preg_node objects)

    public function name() {
        return 'nfa_matcher';
    }

    protected function get_engine_node_name($nodetype, $nodesubtype) {
        switch($nodetype) {
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_CONCAT:
            case qtype_preg_node::TYPE_NODE_ALT:
            case qtype_preg_node::TYPE_NODE_SUBEXPR:
            case qtype_preg_node::TYPE_NODE_COND_SUBEXPR:
                return 'qtype_preg_nfa_' . $nodetype;
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
                return 'qtype_preg_nfa_leaf';
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
            case qtype_preg_node::TYPE_NODE_ERROR:
                return true;
            default:
                return get_string($pregnode->type, 'qtype_preg');
        }
    }

    /**
     * Creates a processing state object for the given state filled with "nomatch" values.
     */
    protected function create_initial_state($state, $str, $startpos) {
        $result = new qtype_preg_nfa_exec_state();
        $result->matcher = $this;
        $result->state = $state;

        $result->matches = array();
        $result->subexpr_to_subpatt = array(0 => $this->astroot->subpattern);
        $result->startpos = $startpos;
        $result->length = 0;

        $result->flags = 0x00;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;
        $result->backtrack_states = array();

        return $result;
    }

    protected function create_nomatch_result($str) {
        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_map());
        $result->invalidate_match();
        return $result;
    }

    /**
     * Updates all fields in the newstate after a transition match
     */
    protected function after_transition_matched($curstate, $newstate, $transition, $curpos, $length) {
        $endstates = $this->automaton->end_states();

        $newstate->state = $transition->to;

        $newstate->set_full(in_array($newstate->state, $endstates));
        if ($transition->is_start_anchor()) {
            $newstate->set_flag(qtype_preg_nfa_exec_state::FLAG_VISITED_START_ANCHOR);
        }
        if ($transition->is_end_anchor()) {
            $newstate->set_flag(qtype_preg_nfa_exec_state::FLAG_VISITED_END_ANCHOR);
        }
        $newstate->left = $newstate->is_full() ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $newstate->last_transition = $transition;
        $newstate->last_match_len = $length;

        $newstate->length += $length;
        $newstate->write_subpatt_info($transition, $curpos, $length);

        if ($transition->causes_backtrack()) {
            $newstate->backtrack_states[] = $curstate;
        }
    }

    /**
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_nfa_exec_state startstates states to go from.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    protected function epsilon_closure($startstates) {
        $curstates = $startstates;
        $result = array('lazy' => array(),
                        'greedy' => $startstates
                        );

        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
            foreach ($transitions as $transition) {
                $curpos = $curstate->startpos + $curstate->length;
                $length = 0;
                if ($transition->pregleaf->subtype != qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length);

                // Resolve ambiguities if any.
                $number = $newstate->state;
                $key = $transition->greediness == qtype_preg_fa_transition::GREED_LAZY
                     ? 'lazy'
                     : 'greedy';
                if (!isset($result[$key][$number]) || $newstate->leftmost_longest($result[$key][$number])) {
                    $result[$key][$number] = $newstate;
                    if ($key != 'lazy') {
                        $curstates[] = $newstate;
                    }
                }
            }
        }
        return $result;
    }

    protected function get_resume_state($str, $laststate) {
        $endstates = $this->automaton->end_states();

        if ($laststate->last_match_len > 0 && $laststate->last_transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            // The last transition was a partially matched backreference; we can only continue from this transition.
            $backref_length = $laststate->length($laststate->last_transition->pregleaf->number);
            $prevpos = $laststate->startpos + $laststate->length - $laststate->last_match_len;

            $resumestate = clone $laststate;
            $this->after_transition_matched($laststate, $resumestate, $laststate->last_transition, $prevpos, $backref_length);
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
            if ($transition->is_loop || !($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $transition->pregleaf->is_end_anchor())) {
                continue;
            }
            $closure = $this->epsilon_closure(array($laststate->state => $laststate));
            $closure = array_merge($closure['lazy'], $closure['greedy']);
            foreach ($closure as $curclosure) {
                if (in_array($curclosure->state, $endstates)) {
                    // The end state is reachable; return it immediately.
                    $result = clone $laststate;
                    $this->after_transition_matched($laststate, $result, $transition, $curpos, 0);
                    return $result;
                }
            }
        }

        return $laststate;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str - original string that was matched.
     * @param qtype_preg_nfa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_nfa_exec_state.
     */
    protected function generate_extension_brute_force($str, $laststate) {
        $endstates = $this->automaton->end_states();
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state, $endstates)) {
            return $resumestate;
        }

        $curstates = array($resumestate);
        $result = null;

        while (count($curstates) != 0) {
            $curstate = array_pop($curstates);
            if (in_array($curstate->state, $endstates) && ($result === null || $result->length > $curstate->length)) {
                $result = $curstate;
            }
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
            foreach ($transitions as $transition) {
                // Skip loops.
                if ($transition->is_loop) {
                    continue;
                }

                // Only generated subpatterns can be passed.
                $length = $transition->pregleaf->consumes($curstate);
                if ($length == qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                    continue;
                }

                if ($length > 0 && $curstate->is_flag_set(qtype_preg_nfa_exec_state::FLAG_VISITED_END_ANCHOR)) {
                    continue;
                }

                // Is it longer than existing one?
                if ($result !== null && $curstate->length + $length > $result->length) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $this->after_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length);

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
     * @param qtype_preg_nfa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_nfa_exec_state.
     */
    protected function generate_extension_fast($str, $laststate) {
        $endstates = $this->automaton->end_states();
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state, $endstates)) {
            return $resumestate;
        }

        $states = array();
        $curstates = array();

        // Create an array of processing states for all nfa states (the only resumestate, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate] = $curstate === $resumestate->state
                               ? $resumestate
                               : null;
        }

        // Get an epsilon-closure of the resume state.
        $closure = $this->epsilon_closure(array($resumestate->state => $resumestate));
        $closure = array_merge($closure['lazy'], $closure['greedy']);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state] = $curclosure;
            $curstates[] = $curclosure->state;
        }

        $result = null;

        // Do search.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                if ($curstate->is_full()) {
                    if ($result === null || $curstate->leftmost_shortest($result)) {
                        $result = $curstate;
                    }
                }
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
                foreach ($transitions as $transition) {
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    // Skip loops.
                    if ($transition->is_loop) {
                        continue;
                    }

                    // Only generated subpatterns can be passed.
                    $length = $transition->pregleaf->consumes($curstate);
                    if ($length == qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                        continue;
                    }

                    if ($length > 0 && $curstate->is_flag_set(qtype_preg_nfa_exec_state::FLAG_VISITED_END_ANCHOR)) {
                        continue;
                    }

                    // Is it longer than existing one?
                    if ($result !== null && $curstate->length + $length > $result->length) {
                        continue;
                    }

                    // Create a new state.
                    $newstate = clone $curstate;
                    $this->after_transition_matched($curstate, $newstate, $transition, $newstate->startpos + $curstate->length, $length);

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

            $reached = $this->epsilon_closure($reached);
            $reached = array_merge($reached['lazy'], $reached['greedy']);

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
    protected function match_brute_force($str, $startpos) {
        $fullmatches = array();       // Possible full matches.
        $partialmatches = array();    // Possible partial matches.

        $curstates = array();    // States which the automaton is in at the current wave front.
        $lazystates = array();   // States reached lazily.

        foreach ($this->automaton->start_states() as $state) {
            $curstates[] = $this->create_initial_state($state, $str, $startpos);
        }

        // Do search.
        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            if ($curstate->is_full()) {
                $fullmatches[] = $curstate;
            }
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
            foreach ($transitions as $transition) {
                $curpos = $startpos + $curstate->length;
                $length = 0;
                if ($transition->pregleaf->match($str, $curpos, $length, $curstate)) {
                    // Create a new state.
                    $newstate = clone $curstate;
                    $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length);

                    // Save the current match.
                    if (!($transition->is_loop && $newstate->has_null_iterations())) {
                        if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                            $lazystates[] = $newstate;
                        } else {
                            $curstates[] = $newstate;
                        }
                    }
                } else if (count($fullmatches) == 0) {
                    // Transition not matched, save the partial match.
                    $partialmatch = clone $curstate;
                    $partialmatch->length += $length;
                    $partialmatch->last_transition = $transition;
                    $partialmatch->last_match_len = $length;
                    $partialmatches[] = $partialmatch;
                }
            }

            // If there's no full match yet and no curstates remain, try the lazy ones.
            if (count($fullmatches) == 0 && count($curstates) == 0 && count($lazystates) > 0) {
                $curstates[] = array_pop($lazystates);
            }
        }

        // Return array of all possible matches.
        $result = array();
        foreach ($fullmatches as $match) {
            $result[] = $match;
        }
        if (count($fullmatches) == 0) {
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
    protected function match_fast($str, $startpos) {
        $states = array();           // Objects of qtype_preg_nfa_exec_state.
        $curstates = array();        // Numbers of states which the automaton is in at the current wave front.
        $lazystates = array();       // States (objects!) reached lazily.
        $partialmatches = array();   // Possible partial matches.

        $startstates = $this->automaton->start_states();
        $endstates = $this->automaton->end_states();

        $endstatereached = false;

        // Create an array of processing states for all nfa states (the only initial state, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate] = in_array($curstate, $startstates)
                               ? $this->create_initial_state($curstate, $str, $startpos)
                               : null;
        }

        // Get an epsilon-closure of the initial state.
        foreach ($states as $state) {
            if ($state !== null) {
                $curstates[] = $state;
            }
        }
        $closure = $this->epsilon_closure($curstates);
        $lazystates = array_merge($lazystates, $closure['lazy']);
        $closure = $closure['greedy'];
        $curstates = array();
        foreach ($closure as $curclosure) {
            $states[$curclosure->state] = $curclosure;
            $curstates[] = $curclosure->state;
            $endstatereached = $endstatereached || $curclosure->is_full();
        }

        // Do search.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state, true);
                foreach ($transitions as $transition) {
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    $curpos = $startpos + $curstate->length;
                    $length = 0;
                    if ($transition->pregleaf->match($str, $curpos, $length, $curstate)) {

                        // Create a new state.
                        $newstate = clone $curstate;
                        $this->after_transition_matched($curstate, $newstate, $transition, $curpos, $length);

                        $endstatereached = $endstatereached || $newstate->is_full();

                        // Save the current result.
                        if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                            $lazystates[] = $newstate;
                        } else {
                            $number = $newstate->state;
                            if (!isset($reached[$number]) || $newstate->leftmost_longest($reached[$number])) {
                                $reached[$number] = $newstate;
                            }
                        }
                    } else if (!$endstatereached) {
                        // Transition not matched, save the partial match.
                        $partialmatch = clone $curstate;
                        $partialmatch->length += $length;
                        $partialmatch->last_transition = $transition;
                        $partialmatch->last_match_len = $length;
                        $partialmatches[] = $partialmatch;
                    }
                }
            }

            // If there's no full match yet and no states were reached, try the lazy ones.
            if (!$endstatereached && count($reached) == 0 && count($lazystates) > 0) {
                $reached[] = array_pop($lazystates);
            }

            $reached = $this->epsilon_closure($reached);
            $lazystates = array_merge($lazystates, $reached['lazy']);
            $reached = $reached['greedy'];

            // Replace curstates with reached.
            foreach ($reached as $curstate) {
                // Currently stored state needs replacement if it's null, or if it's worse than the new state.
                if ($states[$curstate->state] === null || $curstate->leftmost_longest($states[$curstate->state])) {
                    $states[$curstate->state] = $curstate;
                    $curstates[] = $curstate->state;
                    $endstatereached = $endstatereached || $curstate->is_full();
                }
            }
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

    public function match_from_pos($str, $startpos) {
        $bruteforce = count($this->get_nodes_with_subexpr_refs()) > 0;

        // Find all possible matches. Using the fast match method if there are no backreferences.
        $possiblematches = $bruteforce
                         ? $this->match_brute_force($str, $startpos)
                         : $this->match_fast($str, $startpos);

        // Choose the best one.
        $result = null;
        foreach ($possiblematches as $match) {
            if ($result === null || $match->leftmost_longest($result, false)) {
                $result = $match;
            }
        }

        if ($result === null) {
            return $this->create_nomatch_result($str);
        }

        // Generate an extension for partial matches.
        $result->extendedmatch = null;
        if (!$result->is_full() /*&& ($this->options === null || $this->options->extensionneeded)*/) {   // TODO
            // Try each backtrack state and choose the shortest one.
            $result->backtrack_states = array_merge(array($result), $result->backtrack_states);
            foreach ($result->backtrack_states as $backtrack) {
                $backtrack->str = $backtrack->str->substring(0, $startpos + $backtrack->length);

                $tmp = $bruteforce
                     ? $this->generate_extension_brute_force($str, $backtrack)
                     : $this->generate_extension_fast($str, $backtrack);

                if ($tmp === null) {
                    continue;
                }

                // Calculate 'left'.
                $prefixlen = $startpos;
                while ($prefixlen < $result->str->length() && $prefixlen < $tmp->str->length() &&
                       $result->str[$prefixlen] == $tmp->str[$prefixlen]) {
                    $prefixlen++;
                }
                $left = $tmp->str->length() - $prefixlen;
                // Choose the best one by:
                // 1) minimizing length of the generated extension
                // 2) minimizing abs(extension->length - result->length)
                if ($result->extendedmatch === null) {
                    $result->extendedmatch = $tmp;
                    $result->left = $left;
                } else if (($result->left > $left) ||
                           ($result->left == $left && abs($result->extendedmatch->length - $result->length) > abs($tmp->length - $result->length))) {
                    $result->extendedmatch = $tmp;
                    $result->left = $left;
                }
            }
        }

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

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $ast_node - object of qtype_preg_node child class.
     * @param $dst_node - object of qtype_preg_nfa_node child class.
     * @return - object of qtype_preg_nfa in case of success, false otherwise.
     */
    protected function build_nfa($ast_node, $dst_node) {
        $result = new qtype_preg_nfa($ast_node, $this->parser->get_max_subpatt(), $this->get_max_subexpr(), $this->get_nodes_with_subexpr_refs());

        // The create_automaton() can throw an exception in case of too large finite automaton.
        try {
            $stack = array();
            $dst_node->create_automaton($result, $stack);
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function __construct($regex = null, $options = null) {
        parent::__construct($regex, $options);

        if (!isset($regex) || !empty($this->errors)) {
            return;
        }

        $nfa = self::build_nfa($this->astroot, $this->dstroot);
        if ($nfa !== false) {
            $this->automaton = $nfa;
            $this->nestingmap = array();
            $this->calculate_nesting_map($this->astroot, array($this->astroot->subpattern));
            // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
            // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
            // $this->automaton->on_subexpr_added($this->astroot);
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
