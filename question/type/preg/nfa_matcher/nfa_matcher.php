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
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

/**
 * Represents an execution state of an nfa.
 */
class qtype_preg_nfa_exec_state implements qtype_preg_matcher_state {

    public $options;

    // The nfa being executed.
    public $automaton;

    // The corresponding nfa state.
    public $state;

    // 2-dimensional array of matches; 1st is subpattern number; 2nd is repetitions of the subpattern.
    // Each subpattern is initialized with (-1,-1) at start.
    public $matches;

    // Starting position of the match.
    public $startpos;

    // Length of the match.
    public $length;

    // Array used mostly for disambiguation when there are duplicate subpexpressions numbers.
    public $subexpr_to_subpatt;

    // Is this a full match?
    public $full;

    // How many characters left for full match?
    public $left;

    // Match extension in case of partial match. An object of this same class.
    public $extendedmatch;

    // String being captured and/or generated.
    public $str;

    // The last transition matched.
    public $last_transition;

    // Length of the last match.
    public $last_match_len;

    public function __clone() {
        $this->str = clone $this->str;  // Needs to be cloned for correct string generation.
    }

    public static function empty_subpatt_match() {
        return array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    public static function is_being_captured($index, $length) {
        return ($index != qtype_preg_matching_results::NO_MATCH_FOUND && $length == qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    // Returns the current match for the given subpattern number. If there was no attemt to match, returns null.
    public function current_match($subpatt) {
        if (!array_key_exists($subpatt, $this->matches)) {
            return null;
        }
        return end($this->matches[$subpatt]);
    }

    // Sets the current match for the given subpattern number.
    public function set_current_match($subpatt, $index, $length) {
        $count = count($this->matches[$subpatt]);
        $this->matches[$subpatt][$count - 1] = array($index, $length);
    }

    // Returns the last match for the given subpattern number. This function has different behaviour in PCRE and POSIX mode.
    // If there was no attemt to match, returns null.
    public function last_match($subpatt) {
        if ($this->options->mode == qtype_preg_handling_options::MODE_POSIX) {
            return $this->current_match($subpatt);
        }

        if (!array_key_exists($subpatt, $this->matches)) {
            return null;
        }

        $matches = $this->matches[$subpatt];
        $count = count($matches);

        // It's a tricky part. PCRE uses last successful match for situations like "(a|b\1)*" and string "ababbabbba".
        // Hence we need to iterate from the last to the first repetitions until a match found.
        for ($i = $count - 1; $i >= 0; $i--) {
            $cur = $matches[$i];
            if ($cur[0] != qtype_preg_matching_results::NO_MATCH_FOUND && $cur[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                return $cur;
            }
        }
        return self::empty_subpatt_match();
    }

    // Checks if this state equals another
    public function equals($to) {
        if ($this->state !== $to->state) {
            return false;
        }
        foreach ($this->matches as $key => $repetitions) {
            if ($this->current_match($key) !== $to->current_match($key)) {
                return false;
            }
        }
        return true;
    }

    /**********************************************************************/

    public function find_dup_subexpr_match($subexpr) {
        if (!array_key_exists($subexpr, $this->subexpr_to_subpatt)) {
            // Can get here when {0} occurs in the regex.
            return self::empty_subpatt_match();
        }
        $subpatt = $this->subexpr_to_subpatt[$subexpr];
        $last = $this->last_match($subpatt);
        if (!self::is_being_captured($last[0], $last[1])) {
            return $last;
        }
        return self::empty_subpatt_match();
    }

    public function index_first($subexpr = 0) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[0];
    }

    public function length($subexpr = 0) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[1];
    }

    public function is_subexpr_captured($subexpr) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[1] != qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    public function to_matching_results($max_subexpr, $subexpr_map) {
        $index = array();
        $length = array();
        for ($subexpr = 0; $subexpr <= $max_subexpr; $subexpr++) {
            if (!array_key_exists($subexpr, $this->subexpr_to_subpatt)) {
                // Can get here when {0} occurs in the regex.
                $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
            } else {
                $subpatt = $this->subexpr_to_subpatt[$subexpr];
                $match = $this->last_match($subpatt);
                if ($match[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $index[$subexpr] = $match[0];
                    $length[$subexpr] = $match[1];
                } else {
                    $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                    $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                }
            }
        }
        $index[0] = $this->startpos;
        $length[0] = $this->length;
        $result = new qtype_preg_matching_results($this->full, $index, $length, $this->left, $this->extendedmatch);
        $result->set_source_info($this->str, $max_subexpr, $subexpr_map);
        return $result;
    }

    /**********************************************************************/

    /**
     * Resets the given subpattern to no match.
     */
    public function begin_subpatt_iteration($node, $skipwholematch) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->begin_subpatt_iteration($operand, $skipwholematch);
            }
        }
        if ($node->subpattern == -1) {
            return;
        }

        $cur = $this->current_match($node->subpattern);

        if ($cur === null) {
            // Very first iteration.
            $this->matches[$node->subpattern] = array(self::empty_subpatt_match());
        } else {
            // There were some iterations. Start a new iteration only if the last wasn't NOMATCH.
            if (!($cur[0] == qtype_preg_matching_results::NO_MATCH_FOUND && $cur[1] == qtype_preg_matching_results::NO_MATCH_FOUND) && !($skipwholematch && $node->subpattern == 0)) {
                $this->matches[$node->subpattern][] = self::empty_subpatt_match();
            }
        }
    }

    /**
     * Checks if this state contains null iterations, for example \b*. Such states should be skipped during matching.
     */
    public function has_null_iterations() {
        foreach ($this->matches as $subpatt => $repetitions) {
            $count = count($repetitions);
            if ($count < 2) {
                continue;
            }

            $penult = $repetitions[$count - 2];
            $last = $repetitions[$count - 1];
            if ($last[1] != qtype_preg_matching_results::NO_MATCH_FOUND && $penult == $last) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if this beats other, false if other beats this; for equal states returns false.
     */
    public function leftmost_longest($other) {
        // Check for full match.
        if ($this->full && !$other->full) {
            return true;
        } else if (!$this->full && $other->full) {
            return false;
        }

        // Iterate over all subpatterns skipping the first which is the whole expression.
        for ($i = 1; $i <= $this->automaton->max_subpatt(); $i++) {
            $this_match = array_key_exists($i, $this->matches) ? $this->matches[$i] : array(self::empty_subpatt_match());
            $other_match = array_key_exists($i, $other->matches) ? $other->matches[$i] : array(self::empty_subpatt_match());

            // Less number of iterations means that there is a longer match without epsilons.
            $this_count = count($this_match);
            $other_count = count($other_match);
            if ($this_count < $other_count) {
                return true;
            } else if ($other_count < $this_count) {
                return false;
            }

            // Iterate over all repetitions.
            for ($j = 0; $j < $this_count; $j++) {
                $this_index = $this_match[$j][0];
                $this_length = $this_match[$j][1];
                $other_index = $other_match[$j][0];
                $other_length = $other_match[$j][1];

                // Continue if both iterations have no match.
                if ($this_index == qtype_preg_matching_results::NO_MATCH_FOUND && $other_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    continue;
                }

                // Match existance.
                if ($other_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return true;
                } else if ($this_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return false;
                }

                // Leftmost.
                if ($this_index < $other_index) {
                    return true;
                } else if ($other_index < $this_index) {
                    return false;
                }

                // Longest.
                if ($this_length > $other_length) {
                    return true;
                } else if ($other_length > $this_length) {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Writes subpatterns start\end information to this state.
     */
    public function write_subpatt_info($transition, $pos, $matchlen, $options) {
        // Begin a new iteration of a subpattern. In fact, we can call the method for
        // the subpattern with minimal number; all "bigger" subpatterns will be reset recursively.
        if ($transition->min_subpatt_node != null) {
            $this->begin_subpatt_iteration($transition->min_subpatt_node, true);
        }

        // Set matches to (pos, -1) for the new iteration.
        foreach ($transition->subpatt_start as $node) {
            $this->set_current_match($node->subpattern, $pos, qtype_preg_matching_results::NO_MATCH_FOUND);
        }

        // Set matches to (pos, length) for the ending iterations.
        foreach ($transition->subpatt_end as $node) {
            $current_match = $this->current_match($node->subpattern);
            $index = $current_match[0];
            if ($index != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $this->set_current_match($node->subpattern, $index, $pos - $index + $matchlen);
            }
        }

        // Some stuff for subexpressions.
        foreach ($transition->subexpr_start as $node) {
            $this->subexpr_to_subpatt[$node->number] = $node->subpattern;
        }
    }
}

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
    public function create_initial_state($state, $root, $str, $startpos) {
        $result = new qtype_preg_nfa_exec_state();
        $result->options = $this->options;
        $result->automaton = $this->automaton;
        $result->state = $state;

        $result->matches = array();
        $result->subexpr_to_subpatt = array(0 => $root->subpattern);
        $result->startpos = $startpos;
        $result->length = 0;

        $result->full = false;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;

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
     * @param qtype_poasquestion_string str string being matched.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    public function epsilon_closure($startstates, $str) {
        $curstates = $startstates;
        $result = $startstates;

        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $curstate->startpos + $curstate->length;
                $length = 0;
                if ($transition->quant == qtype_preg_nfa_transition::QUANT_LAZY ||
                    $transition->pregleaf->consumes($curstate) ||
                    !$transition->pregleaf->match($str, $curpos, $length, $curstate)) {
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
                $newstate->write_subpatt_info($transition, $curpos, $length, $this->options);

                // Resolve ambiguities if any.
                $number = $newstate->state->number;
                if (!array_key_exists($number, $result) || $newstate->leftmost_longest($result[$number])) {
                    $result[$number] = $newstate;
                    $curstates[] = $newstate;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str string being matched.
     * @param qtype_preg_nfa_exec_state laststate - the last state matched.
     * @param bool fulllastmatch - was the last transition captured fully, not partially?
     * @return object of qtype_preg_nfa_exec_state.
     */
    public function determine_characters_left($str, $laststate, $fulllastmatch) {
        $states = array();       // Objects of qtype_preg_nfa_exec_state.
        $curstates = array();    // States which the automaton is in.
        $endstate = $this->automaton->end_state();

        // Create an array of processing states for all nfa states (the only state where match was stopped, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate->number] = null;
        }

        $resumestate = null;

        if ($laststate->last_transition === null || $fulllastmatch) {
            // There was no match at all, or the last transition was fully-matched.
            $curpos = $laststate->startpos + $laststate->length;

            // Check if a $ assertion before the eps-closure of the end state. Then it's possible to remove few characters.
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->is_loop || $transition->pregleaf->subtype != qtype_preg_leaf_assert::SUBTYPE_DOLLAR) {
                    continue;
                }
                $closure = $this->epsilon_closure(array($laststate->state->number => $laststate), $str);   // TODO!!!
                foreach ($closure as $curclosure) {
                    if ($curclosure === $endstate) {
                        // The end state is reachable; return it immediately.
                        $result = clone $laststate;
                        $result->state = $curclosure;
                        $result->full = true;
                        $result->left = 0;
                        $result->extendedmatch = null;
                        $result->last_transition = $transition;
                        $result->last_match_len = 0;
                        $result->write_subpatt_info($transition, $curpos, 0, $this->options);
                        return $result;
                    }
                }
            }

            // Well, there were no $ fails at the end. Try the other paths to complete match.
            $resumestate = $laststate;
        } else {
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
            $resumestate->write_subpatt_info($laststate->last_transition, $prevpos, $backref_length, $this->options);

            // Re-write the string with correct characters.
            $newchr = $laststate->last_transition->pregleaf->next_character($resumestate->str, $prevpos, $laststate->last_match_len, $laststate);
            $resumestate->str->concatenate($newchr);
        }

        $closure = $this->epsilon_closure(array($resumestate->state->number => $resumestate), $str);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state->number] = $curclosure;
            $curstates[] = $curclosure->state->number;
        }

        while (count($curstates) != 0) {
            $reached = array();
            while (count($curstates) != 0) {
                $curstate = $states[array_pop($curstates)];
                foreach ($curstate->state->outgoing_transitions() as $transition) {
                    // Check for anchors.
                    // ^ is only valid on start position and thus can only be matched, but can't generate strings.
                    // $ is only valid at the end of regex. TODO: what's with eps-closure?
                    $circumflex = $transition->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX;
                    $dollar = $transition->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR && $transition->to !== $endstate;
                    if ($circumflex || $dollar) {
                        continue;
                    }

                    // Only generated subpatterns can be passed.
                    $length = $transition->pregleaf->consumes($curstate);
                    if ($length == qtype_preg_matching_results::NO_MATCH_FOUND) {
                        continue;
                    }

                    // Is it longer than existing one?
                    if ($states[$endstate->number] !== null && $curstate->length + $length > $states[$endstate->number]->length) {
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
                    $newstate->write_subpatt_info($transition, $newstate->startpos + $curstate->length, $length, $this->options);

                    // Generate a next character.
                    if ($length > 0) {
                        $newchr = $transition->pregleaf->next_character($newstate->str, $newstate->startpos + $newstate->length, 0, $curstate);
                        $newstate->str->concatenate($newchr);
                    }

                    // Save all reached states.
                    $closure = $this->epsilon_closure(array($newstate->state->number => $newstate), $str);
                    foreach ($closure as $curclosure) {
                        $number = $curclosure->state->number;
                        if (!array_key_exists($number, $reached) || $reached[$number]->length > $newstate->length) {
                            $reached[$number] = $curclosure;
                        }
                    }
                }
            }

            // Replace curstates with reached.
            foreach ($reached as $curstate) {
                $number = $curstate->state->number;
                if ($states[$number] === null || $states[$number]->length > $curstate->length) {
                    $states[$number] = $curstate;
                    $curstates[] = $number;
                }
            }
        }
        return $states[$endstate->number];
    }

    /**
     * This method should be used if there are backreferences in the regex.
     * Returns array of all possible matches.
     */
    protected function match_brute_force($str, $startpos) {
        $fullmatches = array();       // Possible full matches.
        $partialmatches = array();    // Possible partial matches.

if (1 == 0) {
    $styleprovider = new qtype_preg_dot_style_provider();
    $dotscript = $this->ast_root->dot_script($styleprovider);
    $this->automaton->draw('png', '/home/user/automaton.png');
    self::execute_dot($dotscript, 'png', '/home/user/ast.png');
}

        $curstates = array($this->create_initial_state($this->automaton->start_state(), $this->ast_root, $str, $startpos));    // States which the automaton is in at the current wave front.
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
                    $newstate->write_subpatt_info($transition, $curpos, $length, $this->options);

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
                } else if (count($fullmatches) == 0) {    // Transition not matched, save the partial match.
                    // If a backreference matched partially - set corresponding fields.
                    $partialmatch = clone $curstate;
                    $fulllastmatch = true;
                    if ($length > 0) {
                        $partialmatch->length += $length;
                        $partialmatch->last_transition = $transition;
                        $partialmatch->last_match_len = $length;
                        $fulllastmatch = false;
                    }

                    $partialmatch->str = $partialmatch->str->substring(0, $startpos + $partialmatch->length);

                    $path = null;
                    // TODO: if ($this->options === null || $this->options->extensionneeded).
                    $path = $this->determine_characters_left($str, $partialmatch, $fulllastmatch);
                    if ($path !== null) {
                        $partialmatch->left = $path->length - $partialmatch->length;
                        $partialmatch->extendedmatch = $path->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
                    }
                    // Finally, save the possible partial match.
                    $partialmatches[] = $partialmatch;
                }
            }

            // If there's no full match yet and no curstates remain, try the lazy ones.
            if (count($fullmatches) == 0 && count($curstates) == 0 && count($lazystates) > 0) {
                $curstates[] = array_pop($lazystates);
            }
        }

        $result = array();
        foreach ($fullmatches as $match) {
            $result[] = $match->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
        }
        if (count($fullmatches) == 0) {
            foreach ($partialmatches as $match) {
                $result[] = $match->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
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
                $initial = $this->create_initial_state($curstate, $this->ast_root, $str, $startpos);
                $states[$curstate->number] = $initial;
            } else {
                $states[$curstate->number] = null;
            }
        }

        // Get an epsilon-closure of the initial state.
        $closure = $this->epsilon_closure(array($startstate->number => $states[$startstate->number]), $str);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state->number] = $curclosure;
            $curstates[] = $curclosure->state->number;
        }

        // Do search.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with newstates by the end of this loop.
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
                        $newstate->write_subpatt_info($transition, $curpos, $length, $this->options);

                        // Save the current result.
                        if ($transition->quant == qtype_preg_nfa_transition::QUANT_LAZY) {
                            $lazystates[] = $newstate;
                        } else {
                            $number = $newstate->state->number;
                            if (!array_key_exists($number, $reached) || $newstate->leftmost_longest($reached[$number])) {
                                $reached[$number] = $newstate;
                            }
                        }
                    } else if ($states[$endstate->number] == null) {    // Transition not matched, save the partial match.
                        // If a backreference matched partially - set corresponding fields.
                        $partialmatch = clone $curstate;
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $partialmatch->length += $length;
                            $partialmatch->last_transition = $transition;
                            $partialmatch->last_match_len = $length;
                            $fulllastmatch = false;
                        }

                        $partialmatch->str = $partialmatch->str->substring(0, $startpos + $partialmatch->length);

                        $path = null;
                        // TODO: if ($this->options === null || $this->options->extensionneeded).
                        $path = $this->determine_characters_left($str, $partialmatch, $fulllastmatch);
                        if ($path !== null) {
                            $partialmatch->left = $path->length - $partialmatch->length;
                            $partialmatch->extendedmatch = $path->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
                        }
                        // Finally, save the possible partial match.
                        $partialmatches[] = $partialmatch;
                    }
                }
            }

            // If there's no full match yet and no states were reached, try the lazy ones.
            if ($states[$endstate->number] == null && count($reached) == 0 && count($lazystates) > 0) {
                $reached[] = array_pop($lazystates);
            }

            $reached = $this->epsilon_closure($reached, $str);

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
            $result[] = $endstatematch->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
        } else {
            foreach ($partialmatches as $match) {
                $result[] = $match->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
            }
        }
        return $result;
    }

    public function match_from_pos($str, $startpos) {
        $backrefs = count($this->get_backrefs()) > 0;
        $possiblematches = array();

        // Find all possible matches. Using the fast match method if there are no backreferences.
        if ($backrefs) {
            $possiblematches = $this->match_brute_force($str, $startpos);
        } else {
            $possiblematches = $this->match_fast($str, $startpos);
        }

        // Choose the best one.
        $result = null;
        foreach ($possiblematches as $match) {
            if ($result === null || $result->worse_than($match)) {
                $result = $match;
            }
        }
        if ($result === null) {
            return $this->create_nomatch_result($str);
        }
        return $result;
    }

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $node - object of nfa_preg_node child class.
     * @param $isassertion - will the result be a lookaround-assertion automaton.
     * @return - object of qtype_preg_nfa in case of success, false otherwise.
     */
    public function build_nfa($node, $isassertion = false) {
        $result = new qtype_preg_nfa($this->parser->get_max_subpatt(), $this->get_max_subexpr());

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
            // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
            // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
            // $this->automaton->on_subexpr_added($this->ast_root);
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
