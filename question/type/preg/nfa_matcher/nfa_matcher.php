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

    public $automaton;    // An NFA corresponding to the given regex.

    public function name() {
        return 'nfa_matcher';
    }

    protected function get_engine_node_name($nodetype) {
        switch($nodetype) {
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_CONCAT:
            case qtype_preg_node::TYPE_NODE_ALT:
            case qtype_preg_node::TYPE_NODE_SUBEXPR:
                return 'qtype_preg_nfa_' . $nodetype;
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
                return 'qtype_preg_nfa_leaf';
        }

        return parent::get_engine_node_name($nodetype);
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
    public function create_initial_state($state, $str, $startpos) {
        $result = new qtype_preg_nfa_exec_state();
        $result->matcher = $this;
        $result->state = $state;

        $result->matches = array();
        $result->subexpr_to_subpatt = array(0 => $this->ast_root->subpattern);
        $result->startpos = $startpos;
        $result->length = 0;

        $result->full = false;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;
        $result->backtrack_states = array();

        return $result;
    }

    public function create_nomatch_result($str) {
        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_map());
        $result->invalidate_match();
        return $result;
    }

    /**
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_nfa_exec_state startstates states to go from.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    public function epsilon_closure($startstates) {
        $curstates = $startstates;
        $result = $startstates;

        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $curstate->startpos + $curstate->length;
                $length = 0;
                if ($transition->pregleaf->subtype != qtype_preg_leaf_meta::SUBTYPE_EMPTY ||
                    $transition->quant == qtype_preg_nfa_transition::QUANT_LAZY) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $newstate->state = $transition->to;

                $newstate->full = ($newstate->state === $this->automaton->end_state());
                $newstate->left = $newstate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
                $newstate->extendedmatch = null;
                $newstate->last_transition = $transition;
                $newstate->last_match_len = $length;

                $newstate->length += $length;
                $newstate->write_subpatt_info($transition, $curpos, $length);

                if ($transition->causes_backtrack()) {
                    $newstate->backtrack_states[] = $curstate;
                }

                // Resolve ambiguities if any.
                $number = $newstate->state->number;
                if (!isset($result[$number]) || $newstate->leftmost_longest($result[$number])) {
                    $result[$number] = $newstate;
                    $curstates[] = $newstate;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_preg_nfa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_nfa_exec_state.
     */
    public function generate_extension($laststate) {
        $endstate = $this->automaton->end_state();
        $resumestate = null;

        if ($laststate->last_match_len > 0 && $laststate->last_transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            // The last transition was a partially matched backreference; we can only continue from this transition.
            $backref_length = $laststate->length($laststate->last_transition->pregleaf->number);
            $prevpos = $laststate->startpos + $laststate->length - $laststate->last_match_len;

            $resumestate = clone $laststate;
            $resumestate->state = $laststate->last_transition->to;
            $resumestate->full = ($resumestate->state === $endstate);
            $resumestate->left = $resumestate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
            $resumestate->extendedmatch = null;
            $resumestate->last_transition = $laststate->last_transition;
            $resumestate->last_match_len = $backref_length;

            $resumestate->length += $backref_length - $laststate->last_match_len;
            $resumestate->write_subpatt_info($laststate->last_transition, $prevpos, $backref_length);

            // Re-write the string with correct characters.
            $newchr = $laststate->last_transition->pregleaf->next_character($resumestate->str, $prevpos, $laststate->last_match_len, $laststate);
            $resumestate->str->concatenate($newchr);
        } else {
            // There was no match at all, or the last transition was fully-matched.
            $curpos = $laststate->startpos + $laststate->length;

            // Check for a \Z \z or $ assertion before the eps-closure of the end state. Then it's possible to remove few characters.
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->is_loop || !($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $transition->pregleaf->is_end_anchor())) {
                    continue;
                }
                $closure = $this->epsilon_closure(array($laststate->state->number => $laststate));
                foreach ($closure as $curclosure) {
                    if ($curclosure->state === $endstate) {
                        // The end state is reachable; return it immediately.
                        $result = clone $laststate;
                        $result->state = $curclosure;
                        $result->full = true;
                        $result->left = 0;
                        $result->extendedmatch = null;
                        $result->last_transition = $transition;
                        $result->last_match_len = 0;
                        $result->write_subpatt_info($transition, $curpos, 0);
                        return $result;
                    }
                }
            }

            // Well, there were no $ fails at the end. Try the other paths to complete match.
            $resumestate = $laststate;
        }

        $curstates = array($resumestate);
        $result = null;

        while (count($curstates) != 0) {
            $curstate = array_pop($curstates);
            if ($curstate->state === $endstate && ($result === null || $result->length > $curstate->length)) {
                $result = $curstate;
            }
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                // Check loops and for anchors.
                // \A and ^ are only valid on start position and thus can only be matched, but can't generate strings.
                // \Z \z and $ are only valid at the end of regex. TODO: what's with eps-closure?
                $assert = $transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT;
                $anchorstart = $assert && $transition->pregleaf->is_start_anchor();
                $anchorend = $assert && $transition->pregleaf->is_end_anchor() && $transition->to !== $endstate;
                if ($transition->is_loop || $anchorstart || $anchorend) {
                    continue;
                }

                // Only generated subpatterns can be passed.
                $length = $transition->pregleaf->consumes($curstate);
                if ($length == qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                    continue;
                }

                // Is it longer than existing one?
                if ($result !== null && $curstate->length + $length > $result->length) {
                    continue;
                }

                // Create a new state.
                $newstate = clone $curstate;
                $newstate->state = $transition->to;

                $newstate->full = ($newstate->state === $endstate);
                $newstate->left = $newstate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
                $newstate->extendedmatch = null;

                $newstate->last_transition = $transition;
                $newstate->last_match_len = $length;

                $newstate->length += $length;
                $newstate->write_subpatt_info($transition, $newstate->startpos + $curstate->length, $length);

                // Generate a next character.
                if ($length > 0) {
                    $newchr = $transition->pregleaf->next_character($newstate->str, $newstate->startpos + $newstate->length, 0, $curstate);
                    $newstate->str->concatenate($newchr);
                }

                // Save the new state.
                $curstates[] = $newstate;
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

        // $this->automaton->draw('svg', '/home/user/automaton.svg');

        $curstates = array($this->create_initial_state($this->automaton->start_state(), $str, $startpos));    // States which the automaton is in at the current wave front.
        $lazystates = array();       // States reached lazily.

        // Do search.
        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $startpos + $curstate->length;
                $length = 0;
                if ($transition->pregleaf->match($str, $curpos, $length, $curstate)) {

                    // Create a new state.
                    $newstate = clone $curstate;
                    $newstate->state = $transition->to;

                    $newstate->full = ($newstate->state === $this->automaton->end_state());
                    $newstate->left = $newstate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
                    $newstate->extendedmatch = null;
                    $newstate->last_transition = $transition;
                    $newstate->last_match_len = $length;

                    $newstate->length += $length;
                    $newstate->write_subpatt_info($transition, $curpos, $length);

                    if ($transition->causes_backtrack()) {
                        $newstate->backtrack_states[] = $curstate;
                    }

                    // Save the current match.
                    if (!($transition->is_loop && $newstate->has_null_iterations())) {
                        if ($transition->quant == qtype_preg_nfa_transition::QUANT_LAZY) {
                            $lazystates[] = $newstate;
                        } else {
                            $curstates[] = $newstate;
                        }
                        if ($newstate->full) {
                            $fullmatches[] = $newstate;
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
    public function match_fast($str, $startpos) {
        $states = array();           // Objects of qtype_preg_nfa_exec_state.
        $curstates = array();        // Numbers of states which the automaton is in at the current wave front.
        $lazystates = array();       // States (objects!) reached lazily.
        $partialmatches = array();   // Possible partial matches.
        $startstate = $this->automaton->start_state();
        $endstate = $this->automaton->end_state();

        // Create an array of processing states for all nfa states (the only initial state, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            if ($curstate === $this->automaton->start_state()) {
                $initial = $this->create_initial_state($curstate, $str, $startpos);
                $states[$curstate->number] = $initial;
            } else {
                $states[$curstate->number] = null;
            }
        }

        // Get an epsilon-closure of the initial state.
        $closure = $this->epsilon_closure(array($startstate->number => $states[$startstate->number]));
        foreach ($closure as $curclosure) {
            $states[$curclosure->state->number] = $curclosure;
            $curstates[] = $curclosure->state->number;
        }

        // Do search.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    $curpos = $startpos + $curstate->length;
                    $length = 0;
                    if ($transition->pregleaf->match($str, $curpos, $length, $curstate)) {

                        // Create a new state.
                        $newstate = clone $curstate;
                        $newstate->state = $transition->to;

                        $newstate->full = ($newstate->state === $endstate);
                        $newstate->left = $newstate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
                        $newstate->extendedmatch = null;
                        $newstate->last_transition = $transition;
                        $newstate->last_match_len = $length;

                        $newstate->length += $length;
                        $newstate->write_subpatt_info($transition, $curpos, $length);

                        if ($transition->causes_backtrack()) {
                            $newstate->backtrack_states[] = $curstate;
                        }

                        // Save the current result.
                        if ($transition->quant == qtype_preg_nfa_transition::QUANT_LAZY) {
                            $lazystates[] = $newstate;
                        } else {
                            $number = $newstate->state->number;
                            if (!isset($reached[$number]) || $newstate->leftmost_longest($reached[$number])) {
                                $reached[$number] = $newstate;
                            }
                        }
                    } else if ($states[$endstate->number] == null) {
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
            if ($states[$endstate->number] == null && count($reached) == 0 && count($lazystates) > 0) {
                $reached[] = array_pop($lazystates);
            }

            $reached = $this->epsilon_closure($reached);

            // Replace curstates with reached.
            foreach ($reached as $curstate) {
                // Currently stored state needs replacement if it's null, or if it's not the same as the new state.
                // In fact, the second check prevents from situations like \b*
                if ($states[$curstate->state->number] === null || !$states[$curstate->state->number]->equals($curstate)) {
                    $states[$curstate->state->number] = $curstate;
                    $curstates[] = $curstate->state->number;
                }
            }
        }

        // Return array of all possible matches.
        $result = array();
        $endstatematch = $states[$endstate->number];
        if ($endstatematch !== null) {
            $result[] = $endstatematch;
        } else {
            foreach ($partialmatches as $partialmatch) {
                $result[] = $partialmatch;
            }
        }
        return $result;
    }

    public function match_from_pos($str, $startpos) {
        $possiblematches = array();

        // Find all possible matches. Using the fast match method if there are no backreferences.
        if (count($this->get_backrefs()) > 0) {
            $possiblematches = $this->match_brute_force($str, $startpos);
        } else {
            $possiblematches = $this->match_fast($str, $startpos);
        }

        // Choose the best one.
        $result = null;
        foreach ($possiblematches as $match) {
            if ($result === null || $match->leftmost_longest($result)) {
                $result = $match;
            }
        }

        if ($result === null) {
            return $this->create_nomatch_result($str);
        }

        // Generate an extension for partial matches.
        $result->extendedmatch = null;
        if (!$result->full /*&& ($this->options === null || $this->options->extensionneeded)*/) {   // TODO
            // Try each backtrack state and choose the shortest one.
            $result->backtrack_states = array_merge(array($result), $result->backtrack_states);
            foreach ($result->backtrack_states as $backtrack) {
                $backtrack->str = $backtrack->str->substring(0, $startpos + $backtrack->length);
                $tmp = $this->generate_extension($backtrack);
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

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $ast_node - object of qtype_preg_node child class.
     * @param $dst_node - object of qtype_preg_nfa_node child class.
     * @return - object of qtype_preg_nfa in case of success, false otherwise.
     */
    public function build_nfa($ast_node, $dst_node) {
        $result = new qtype_preg_nfa($ast_node, $this->parser->get_max_subpatt(), $this->get_max_subexpr(), $this->get_backrefs());

        // The create_automaton() can throw an exception in case of too large finite automaton.
        try {
            $stack = array();
            $dst_node->create_automaton($this, $result, $stack);
            $result->numerate_states();
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

        $nfa = self::build_nfa($this->ast_root, $this->dst_root);
        if ($nfa !== false) {
            $this->automaton = $nfa;
            // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
            // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
            // $this->automaton->on_subexpr_added($this->ast_root);
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
