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

    // The nfa being executed.
    public $automaton;

    // The corresponding nfa state.
    public $state;

    // 2-dimensional array of matches; 1st is subpattern number; 2nd is repetitions of the subpattern.
    // Each subpattern is initialized with (-1,-1) at start.
    public $matches;

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

    // Returns the last match for the given subpattern number.
    public function last_match($subpatt) {
        $matches = $this->matches[$subpatt];
        $count = count($matches);

        // It's a tricky part. There can be situations like "(a|b\1)*" and string "ababbabbba".
        // Hence it's not enough to remember only the last match, but we also need to know the penult match.
        $result = $matches[$count - 1];
        if ($result[0] != qtype_preg_matching_results::NO_MATCH_FOUND && $result[1] == qtype_preg_matching_results::NO_MATCH_FOUND && $count > 1) {
            $result = $matches[$count - 2];
        }
        if ($result[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
            return $result;
        }
        return array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    // Returns the current match for the given subpattern number.
    public function current_match($subpatt) {
        $matches = $this->matches[$subpatt];
        return end($matches);
    }

    // Sets the current match for the given subpattern number.
    public function set_current_match($subpatt, $index, $length) {
        $count = count($this->matches[$subpatt]);
        $this->matches[$subpatt][$count - 1] = array($index, $length);
    }

    // Increases the whole match (0-subexpression and 1-subpattern) length with the given value.
    public function increase_match_length($delta) {
        $this->matches[1][0][1] += $delta; // Subexpression 1; we need the only 1st repetition; length is at index 1.
    }

    // Checks if this state equals another
    public function equals($to) {
        if ($this->state !== $to->state) {
            return false;
        }
        foreach ($this->matches as $key => $repetitions) {
            if ($this->current_match($key) != $to->current_match($key)) {
                return false;
            }
        }
        return true;
    }

    /**********************************************************************/

    public function find_dup_subexpr_match($subexpr) {
        if (!array_key_exists($subexpr, $this->subexpr_to_subpatt)) {
            // Can get here when {0} occurs in the regex.
            return array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
        }
        $subpatt = $this->subexpr_to_subpatt[$subexpr];
        $lastmatch = $this->last_match($subpatt);
        if ($lastmatch[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
            return $lastmatch;
        }
        return array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    public function index_first($subexpr = 0) {
        $lastmatch = $this->find_dup_subexpr_match($subexpr);
        return $lastmatch[0];
    }

    public function length($subexpr = 0) {
        $lastmatch = $this->find_dup_subexpr_match($subexpr);
        return $lastmatch[1];
    }

    public function is_subexpr_captured($subexpr) {
        return $this->length($subexpr) != qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    public function to_matching_results($max_subpattern, $subexpr_map) {
        $index = array();
        $length = array();
        for ($subexpr = 0; $subexpr <= $this->automaton->max_subexpr(); $subexpr++) {
            if (!array_key_exists($subexpr, $this->subexpr_to_subpatt)) {
                // Can get here when {0} occurs in the regex.
                $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
            } else {
                $subpatt = $this->subexpr_to_subpatt[$subexpr];
                $match = $this->current_match($subpatt);
                if ($match[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $index[$subexpr] = $match[0];
                    $length[$subexpr] = $match[1];
                } else {
                    $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                    $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                }
            }
        }
        $result = new qtype_preg_matching_results($this->full, $index, $length, $this->left, $this->extendedmatch);
        $result->set_source_info($this->str, $max_subpattern, $subexpr_map);
        return $result;
    }

    /**********************************************************************/

    /**
     * Resets the given subpattern to no match. In POSIX mode also resets all inner subpatterns.
     */
    public function begin_subpatt_iteration($node, $startpos, $skipwholematch, $mode = qtype_preg_handling_options::MODE_PCRE) {
        if (/*$mode == qtype_preg_handling_options::MODE_POSIX &&*/ is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->begin_subpatt_iteration($operand, $startpos, $skipwholematch, $mode);
            }
        }
        if ($node->subpattern != -1 && !($skipwholematch && $node->subpattern == 1)) {
            $this->matches[$node->subpattern][] = array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
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

            $pre_last = $repetitions[$count - 2];
            $last = $repetitions[$count - 1];
            if ($last[1] != qtype_preg_matching_results::NO_MATCH_FOUND && $pre_last == $last) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns 1 if this beats other, -1 if other beats this, 0 otherwise.
     */
    public function leftmost_longest($other) {
        // Iterate over all subpatterns.
        for ($i = 1; $i <= $this->automaton->max_subpatt(); $i++) {
            if (/*$i == 1 ||*/ !array_key_exists($i, $this->matches)) {
                continue;
            }

            $this_match = $this->matches[$i];
            $other_match = $other->matches[$i];

            // Any match found beats nomatch.
            $this_last = $this->current_match($i);
            $other_last = $other->current_match($i);
            if ($this_last[1] != qtype_preg_matching_results::NO_MATCH_FOUND && $other_last[1] == qtype_preg_matching_results::NO_MATCH_FOUND) {
                return 1;
            } else if ($other_last[1] != qtype_preg_matching_results::NO_MATCH_FOUND && $this_last[1] == qtype_preg_matching_results::NO_MATCH_FOUND) {
                return -1;
            }

            // Less number of iterations means that there were a longer match without epsilons.
            $this_count = count($this_match);
            $other_count = count($other_match);
            if ($this_count < $other_count) {
                return 1;
            } else if ($other_count < $this_count) {
                return -1;
            }

            // Iterate over all repetitions.
            for ($j = 0; $j < $this_count; $j++) {
                $this_index = $this_match[$j][0];
                $this_length = $this_match[$j][1];
                $other_index = $other_match[$j][0];
                $other_length = $other_match[$j][1];
                if (($this_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $other_index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                    ($this_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $this_index < $other_index) ||
                    ($this_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $this_index === $other_index && $this_length > $other_length)) {
                    return 1;
                }
                if (($other_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $this_index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                    ($other_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $other_index < $this_index) ||
                    ($other_index !== qtype_preg_matching_results::NO_MATCH_FOUND && $other_index === $this_index && $other_length > $this_length)) {
                    return -1;
                }
            }
        }
        return 0;
    }

    public function worse_than($other) {
        // Full match.
        if ($this->full && !$other->full) {
            return false;
        } else if (!$this->full && $other->full) {
            return true;
        }

        // Leftmost rule.
        $leftmost = $this->leftmost_longest($other);
        if ($leftmost == 1) {
            return false;
        } else if ($leftmost == -1) {
            return true;
        }

        // Equal matches.
        return false;
    }

    /**
     * Writes subpatterns start\end information to this state.
     */
    public function write_subpatt_info($transition, $startpos, $pos, $matchlen, $options) {
        if ($options !== null && !$options->capturesubexpressions) {
            return;
        }

        // Begin a new iteration of a subpattern. In fact, we can call the method for
        // the subpattern with minimal number; all "bigger" subpatterns will be reset recursively.
        if ($transition->min_subpatt_node != null) {
            $this->begin_subpatt_iteration($transition->min_subpatt_node, $startpos, true, $options->mode);
        }

        // Set matches to (pos, -1) for the new iteration.
        foreach ($transition->subpatt_start as $node) {
            if ($node->subpattern != 1) {
                $this->set_current_match($node->subpattern, $pos, qtype_preg_matching_results::NO_MATCH_FOUND);
            }
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

    protected function get_engine_node_name($pregname) {
        switch($pregname) {
            case 'node_finite_quant':
            case 'node_infinite_quant':
            case 'node_concat':
            case 'node_alt':
            case 'node_subexpr':
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
                return get_string($pregnode->name(), 'qtype_preg');
        }
    }

    /**
     * Creates a processing state object for the given state filled with "nomatch" values.
     */
    public function create_initial_state($state, $root, $str, $startpos) {
        $result = new qtype_preg_nfa_exec_state();
        $result->automaton = $this->automaton;
        $result->state = $state;

        $result->matches = array();
        $result->subexpr_to_subpatt = array(0 => $root->subpattern);
        $result->begin_subpatt_iteration($root, $startpos, false/*, $mode*/);  // TODO: mode
        $result->set_current_match($root->subpattern, $startpos, 0);

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
     * @param qtype_preg_nfa_exec_state startstate state to go from.
     * @param qtype_poasquestion_string str string being matched.
     * @param int startpos start position of matching.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    public function epsilon_closure($startstate, $str, $startpos) {
        $curstates = array($startstate);
        $result = array($startstate->state->number => $startstate);

        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $startpos + $curstate->length();
                $length = 0;
                if ($transition->pregleaf->consumes($curstate) ||
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

                $newstate->increase_match_length($length);
                $newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);

                // Resolve ambiguities if any.
                if (array_key_exists($newstate->state->number, $result)) {
                    $existing = $result[$newstate->state->number];
                    if ($existing->worse_than($newstate)) {
                        $result[$newstate->state->number] = $newstate;
                        $curstates[] = $newstate;
                    }
                } else {
                    $result[$newstate->state->number] = $newstate;
                    $curstates[] = $newstate;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion_string str string being matched.
     * @param int startpos - start position of matching.
     * @param qtype_preg_nfa_exec_state laststate - the last state matched.
     * @param bool fulllastmatch - was the last transition captured fully, not partially?
     * @return object of qtype_preg_nfa_exec_state.
     */
    public function determine_characters_left($str, $startpos, $laststate, $fulllastmatch) {
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
            $curpos = $startpos + $laststate->length();

            // Check if a $ assertion before the eps-closure of the end state. Then it's possible to remove few characters.
            foreach ($laststate->state->outgoing_transitions() as $transition) {
                if ($transition->is_loop || $transition->pregleaf->subtype != qtype_preg_leaf_assert::SUBTYPE_DOLLAR) {
                    continue;
                }
                $closure = $this->epsilon_closure(/*$transition->to*/$laststate, $str, $startpos);   // TODO!!!
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
                        $result->write_subpatt_info($transition, $startpos, $curpos, 0, $this->options);
                        return $result;
                    }
                }
            }

            // Well, there were no $ fails at the end. Try the other paths to complete match.
            $resumestate = $laststate;
        } else {
            // The last transition was a partially matched backreference; we can only continue from this transition.
            $backref_length = $laststate->length($laststate->last_transition->pregleaf->number);
            $prevpos = $startpos + $laststate->length();

            $resumestate = clone $laststate;
            $resumestate->state = $laststate->last_transition->to;
            $resumestate->full = ($resumestate->state === $endstate);
            $resumestate->left = $resumestate->full ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
            $resumestate->extendedmatch = null;
            $resumestate->last_transition = $laststate->last_transition;
            $resumestate->last_match_len = $backref_length;

            $resumestate->increase_match_length($backref_length - $laststate->last_match_len);
            $resumestate->write_subpatt_info($laststate->last_transition, $startpos, $prevpos, $backref_length, $this->options);

            // Re-write the string with correct characters.
            $newchr = $laststate->last_transition->pregleaf->next_character($resumestate->str, $prevpos, $laststate->last_match_len, $laststate);
            $resumestate->str->concatenate($newchr);
        }

        $closure = $this->epsilon_closure($resumestate, $str, $startpos);
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

                    $length = $transition->pregleaf->consumes($curstate);

                    // Only generated subpatterns can be passed.
                    if ($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
                        $length = $this->length($transition->pregleaf->number);
                    }

                    if ($length == qtype_preg_matching_results::NO_MATCH_FOUND) {
                        continue;
                    }

                    // Is it longer then an existing one?
                    if ($states[$endstate->number] !== null && $curstate->length() + $length > $states[$endstate->number]->length()) {
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

                    $newstate->increase_match_length($length);
                    $newstate->write_subpatt_info($transition, $startpos, $startpos + $curstate->length(), $length, $this->options);

                    // Generate a next character.
                    if ($length > 0) {
                        $newchr = $transition->pregleaf->next_character($newstate->str, $startpos + $newstate->length(), 0, $curstate);
                        $newstate->str->concatenate($newchr);
                    }

                    // Saving the current result.
                    $closure = $this->epsilon_closure($newstate, $str, $startpos);
                    foreach ($closure as $curclosure) {
                        $reached[] = $curclosure;
                    }
                }
            }

            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                $curnum = $curstate->state->number;
                if ($states[$curnum] === null || $states[$curnum]->length() > $curstate->length()) {
                    $states[$curnum] = $curstate;
                    $newstates[] = $curnum;
                }
            }
            $curstates = $newstates;
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
        $fullmatchfound = false;      // If a full match found, no need to store partial matches.

if (1 == 0) {
    $styleprovider = new qtype_preg_dot_style_provider();
    $dotscript = $this->ast_root->dot_script($styleprovider);
    $this->automaton->draw('png', '/home/user/automaton.png');
    self::execute_dot($dotscript, 'png', '/home/user/ast.png');
}

        $curstates = array($this->create_initial_state($this->automaton->start_state(), $this->ast_root, $str, $startpos));    // States which the automaton is in at the current wave front.

        // Do search.
        while (count($curstates) != 0) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            foreach ($curstate->state->outgoing_transitions() as $transition) {
                $curpos = $startpos + $curstate->length();
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

                    $newstate->increase_match_length($length);
                    $newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);

                    // Saving the current match.
                    if (!$newstate->has_null_iterations()) {
                        $curstates[] = $newstate;
                        if ($newstate->full) {
                            $fullmatches[] = $newstate;
                        }
                    }
                } else if (!$fullmatchfound) {    // Transition not matched, save the partial match.
                    // If a backreference matched partially - set corresponding fields.
                    $partialmatch = clone $curstate;
                    $fulllastmatch = true;
                    if ($length > 0) {
                        $partialmatch->increase_match_length($length);
                        $partialmatch->last_transition = $transition;
                        $partialmatch->last_match_len = $length;
                        $fulllastmatch = false;
                    }

                    $partialmatch->str = $partialmatch->str->substring(0, $startpos + $partialmatch->length());

                    $path = null;
                    // TODO: if ($this->options === null || $this->options->extensionneeded).
                    $path = null;//$this->determine_characters_left($str, $startpos, $partialmatch, $fulllastmatch);
                    if ($path !== null) {
                        $partialmatch->left = $path->length() - $partialmatch->length();
                        $partialmatch->extendedmatch = $path->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
                    }
                    // Finally, save the possible partial match.
                    $partialmatches[] = $partialmatch;
                }
            }
        }

        $result = array();
        foreach ($fullmatches as $match) {
            $result[] = $match->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
        }
        if (!$fullmatchfound) {
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
        $partialmatches = array();   // Possible partial matches.
        $fullmatchfound = false;

        // Create an array of processing states for all nfa states (the only initial state, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            if ($curstate === $this->automaton->start_state()) {
                $initial = $this->create_initial_state($curstate, $this->ast_root, $str, $startpos);
                $states[$curstate->number] = $initial;
            } else {
                $states[$curstate->number] = null;
            }
        }

        // Get an epsilon-closure of the initial state. TODO: ambiguities?
        $closure = $this->epsilon_closure($states[$this->automaton->start_state()->number], $str, $startpos);
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
                    $curpos = $startpos + $curstate->length();
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

                        $newstate->increase_match_length($length);
                        $newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);

                        // Saving the current result.
                        $closure = $this->epsilon_closure($newstate, $str, $startpos);
                        foreach ($closure as $curclosure) {
                            if ($curclosure->full) {
                                $fullmatchfound = true;
                            }
                            $reached[] = $curclosure;
                        }
                    } else if (!$fullmatchfound) {    // Transition not matched, save the partial match.
                        // If a backreference matched partially - set corresponding fields.
                        $partialmatch = clone $curstate;
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $partialmatch->increase_match_length($length);
                            $partialmatch->last_transition = $transition;
                            $partialmatch->last_match_len = $length;
                            $fulllastmatch = false;
                        }

                        $partialmatch->str = $partialmatch->str->substring(0, $startpos + $partialmatch->length());

                        $path = null;
                        // TODO: if ($this->options === null || $this->options->extensionneeded).
                        $path = $this->determine_characters_left($str, $startpos, $partialmatch, $fulllastmatch);
                        if ($path !== null) {
                            $partialmatch->left = $path->length() - $partialmatch->length();
                            $partialmatch->extendedmatch = $path->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
                        }
                        // Finally, save the possible partial match.
                        $partialmatches[] = $partialmatch;
                    }
                }
            }
            // Resolve ambiguities between the reached states.
            foreach ($reached as $key1 => $reached1) {
                foreach ($reached as $key2 => $reached2) {
                    if ($reached1 == null || $reached2 == null || $reached1->state !== $reached2->state) {
                        continue;
                    }
                    if ($reached1->worse_than($reached2)) {
                        $reached[$key1] = null;
                    } else if ($reached2->worse_than($reached1)) {
                        $reached[$key2] = null;
                    }
                }
            }

            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                if ($curstate == null) {
                    continue;
                }
                // Currently stored state needs replacement if it's null, or if it's not the same as the new state.
                // In fact, the second check prevents from situations like \b*
                if ($states[$curstate->state->number] === null || !$states[$curstate->state->number]->equals($curstate)) {
                    $states[$curstate->state->number] = $curstate;
                    $newstates[$curstate->state->number] = true;
                }
            }
            $curstates = array_keys($newstates);
        }

        // Return array of all possible matches.
        $result = array();
        foreach ($states as $match) {
            if ($match !== null) {
                $result[] = $match->to_matching_results($this->get_max_subexpr(), $this->get_subexpr_map());
            }
        }
        if (!$fullmatchfound) {
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
        $result = new qtype_preg_nfa($this->parser->get_max_subpatt(), $this->get_max_subexpr(), $this->get_subexpr_map());

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
            // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
            // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
            // $this->automaton->on_subexpr_added($this->ast_root);
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
