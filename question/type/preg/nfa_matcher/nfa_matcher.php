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
 * Represents a state of an automaton when running.
 */
class qtype_preg_nfa_processing_state implements qtype_preg_matcher_state {
    public $automaton;           // A reference to the automaton, we need some methods from it
    public $state;               // A reference to the state which automaton is in.

    public $matches;             // 2-dimensional array; 1st dimension is subpattern number; 2nd is matches for subpattern repetitions. Each subpattern is initialized with (-1,-1) at start.

    public $full;                // Is this a full match?
    public $left;                // How many characters left for full match?
    public $extendedmatch;       // Match extension in case of partial match.

    public $str;                 // String being captured or generated.
    public $last_transition;     // The last transition matched.
    public $last_match_len;      // Length of the last match.
    public $captured_transitions;

    public function last_match($subpatt) {
        $matches = $this->matches[$subpatt];
        return end($matches);
    }

    public function set_last_match($subpatt, $index, $length) {
        $count = count($this->matches[$subpatt]);
        $this->matches[$subpatt][$count - 1] = array($index, $length);
    }

    public function increase_whole_match_length($delta) {
        $this->matches[1][0][1] += $delta; // The whole expression's id is 1; we need the only repetition; length is at index 1.
    }

    public function equals($to) {
        if ($this->state !== $to->state) {
            return false;
        }
        foreach ($this->matches as $key => $repetitions) {
            if ($this->last_match($key) != $to->last_match($key)) {
                return false;
            }
        }
        return true;
    }

    /**********************************************************************/

    public function index_first($subexpr = 0) {
        $subpatt = $this->automaton->subpatt_from_subexpr_number($subexpr);
        return $this->last_match($subpatt)[0];
    }

    public function length($subexpr = 0) {
        $subpatt = $this->automaton->subpatt_from_subexpr_number($subexpr);
        return $this->last_match($subpatt)[1];
    }

    public function is_subpattern_captured($subexpr) {
        return $this->length($subexpr) != qtype_preg_matching_results::NO_MATCH_FOUND;
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
        if (!($skipwholematch && $node->subpattern == 1)) {
            $this->matches[$node->subpattern][] = array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
        }
    }

    /**
     * Returns 1 if this beats other, -1 if other beats this, 0 otherwise.
     */
    public function leftmost_longest($other) {
        // Iterate over all subpatterns.
        for ($i = 1; $i <= $this->automaton->subpatt_count(); $i++) {
            if (/*$i == 1 ||*/ !array_key_exists($i, $this->matches)) {
                continue;
            }
            // Iterate over all repetitions.
            $count1 = count($this->matches[$i]);
            $count2 = count($other->matches[$i]);
            $mincount = min($count1, $count2);
            for ($j = 0; $j < $mincount; $j++) {
                $match1 = $this->matches[$i][$j];
                $match2 = $other->matches[$i][$j];
                $ind_this = $match1[0];
                $ind_that = $match2[0];
                $len_this = $match1[1];
                $len_that = $match2[1];
                if (($ind_this !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_that === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                    ($ind_this !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_this < $ind_that) ||
                    ($ind_this !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_this === $ind_that && $len_this > $len_that)) {
                    return 1;
                }
                if (($ind_that !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_this === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                    ($ind_that !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_that < $ind_this) ||
                    ($ind_that !== qtype_preg_matching_results::NO_MATCH_FOUND && $ind_that === $ind_this && $len_that > $len_this)) {
                    return -1;
                }
            }
        }
        return 0;
    }

    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {
        if ($this->full && !$other->full) {
            return false;
        } else if (!$this->full && $other->full) {
            return true;
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
    public function write_subpatt_info($transition, $startpos, $pos, $matchlen, $options) {
        if ($options !== null && !$options->capturesubpatterns) {
            return;
        }
        //echo "STATE " . $this->state->number . "; transition " . $transition->pregleaf->tohr() . "\n";
        //echo "before: " . $this->subpatterns_to_str() . "\n";

        // Begin a new iteration of a subpattern. In fact, we can call the method for
        // the subpattern with minimal number; all "bigger" subpatterns will be reset recursively.
        if ($transition->min_subpatt_node != null) {
            $this->begin_subpatt_iteration($transition->min_subpatt_node, $startpos, true, $options->mode);
            //echo "min: " . $transition->min_subpatt_node->subpattern . "\n";
        }

        // Set matches to (pos, -1) for the new iteration.
        foreach ($transition->subpatt_start as $node) {
            if ($node->subpattern != 1) {
                $this->set_last_match($node->subpattern, $pos, qtype_preg_matching_results::NO_MATCH_FOUND);
            }
        }

        // Set matches to (pos, length) for the ending iterations.
        foreach ($transition->subpatt_end as $node) {
            $last_match = $this->last_match($node->subpattern);
            $index = $last_match[0];
            if ($index != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $this->set_last_match($node->subpattern, $index, $pos - $index + $matchlen);
                //echo $node->subpattern . ': (' . $index . ', ' . ($pos - $index + $matchlen) . "); ";
            }
        }
        //echo "after: " . $this->subpatterns_to_str() . "\n";
    }

    public function concat_chr($char) {
        $this->str->concatenate($char);
    }

    public function last_transition_to_str() {
        $tr = $this->last_transition;
        if ($tr != null) {
            return $tr->from->number . '->' . $tr->pregleaf->tohr() . '->' . $tr->to->number;
        } else {
            return '';
        }
    }

    public function subpatterns_to_str() {
        $result = '';
        $min = min(array_keys($this->matches));
        $max = max(array_keys($this->matches));
        for ($i = $min; $i <= $max; $i++) {
            if (!array_key_exists($i, $this->matches)) {
                continue;
            }
            $match = $this->last_match($i);
            $result .= $i . ': (' . $match[0] . ', ' . $match[1] . ') ';
        }
        return $result;
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
            //case 'leaf_backref':
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
            //case qtype_preg_node::TYPE_LEAF_BACKREF:
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
        $result = new qtype_preg_nfa_processing_state();
        $result->automaton = $this->automaton;
        $result->state = $state;

        $result->matches = array();
        $result->begin_subpatt_iteration($root, $startpos, false/*, $mode*/);  // TODO: mode
        $result->set_last_match($root->subpattern, $startpos, 0);

        $result->full = false;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;
        $result->captured_transitions = array();

        return $result;
    }

    public function create_nomatch_result($str) {
        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subpattern(), $this->get_subpattern_map());
        $result->invalidate_match();
        return $result;
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

                $newstate->last_transition = $transition;
                $newstate->last_match_len = $length;
                $newstate->captured_transitions[$transition->number] = true;

                $newstate->increase_whole_match_length($length);
                $newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);

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
            //$newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);   // TODO: is it needed?

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
                        $newstate->write_subpatt_info($transition, $startpos, $startpos + $curstateobj->length[0], $length, $this->options);
                        $newstate->left = -1;   TODO: replace to this.
                        captured_transitions???
                        $newstate->extendedmatch = null;*/

                        $newstate = new qtype_preg_nfa_processing_state(false, $curstateobj->index, $curstateobj->length,
                                                                        $curstateobj->index_new, $curstateobj->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $transition, $length, $curstateobj->captured_transitions, $curstateobj);
                        $newstate->length[0] += $length;
                        $newstate->write_subpatt_info($transition, $startpos, $startpos + $curstateobj->length[0], $length, $this->options);

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
            $states[$endstateid]->index[0] = $startpos;
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
        $DEBUG = 0;

if ($DEBUG) {
    $styleprovider = new qtype_preg_dot_style_provider();
    $dotscript = $this->ast_root->dot_script($styleprovider);
    $this->automaton->draw('png', '/home/user/automaton.png');
    self::execute_dot($dotscript, 'png', '/home/user/ast.png');
}

        $states = array();           // Objects of qtype_preg_nfa_processing_state.
        $curstates = array();        // Numbers of states which the automaton is in at the current wave front.
        $partialmatches = array();   // Possible partial matches.
        $fullmatchfound = false;
        $areequal = false;

        $result = null;

        // Create an array of processing states for all nfa states (the only initial state, in fact, other states are null).
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
        foreach ($closure as $state) {
            $states[$state->state->number] = $state;
            $curstates[] = $state->state->number;
        }

        // Do search.
        while (count($curstates) != 0) {
            $reached = array();
            // We'll replace curstates with newstates by the end of this loop.
            while (count($curstates) != 0) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];

if ($DEBUG) {
    echo "\nstarting from state " . $curstate->state->number . "\n";
}

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

                        $newstate->last_transition = $transition;
                        $newstate->last_match_len = $length;
                        $newstate->captured_transitions[$transition->number] = true;

                        $newstate->increase_whole_match_length($length);
                        $newstate->write_subpatt_info($transition, $startpos, $curpos, $length, $this->options);

                        // Saving the current result.
                        $closure = $this->epsilon_closure($newstate, $str, $startpos);
                        foreach ($closure as $curclosure) {
                            if ($curclosure->full) {
                                $curclosure->left = 0;
                                $fullmatchfound = true;
                            }
                            $reached[] = $curclosure;
                        }

if ($DEBUG) {
    echo 'matched ' . $str[$curpos] . ' using ' . $transition->pregleaf->tohr() . ', gonna make state ' . $newstate->state->number . ': ' . $newstate->subpatterns_to_str() . "\n";
}

                    } else if (!$fullmatchfound) {    // Transition not matched, save the partial match.
                        // If a backreference matched partially - set corresponding fields.
                        /*$newres = clone $curstate;
                        $fulllastmatch = true;
                        if ($length > 0) {
                            $newres->length[0] += $length;
                            $newres->last_transition = $transition;
                            $newres->last_match_len = $length;
                            $fulllastmatch = false;
                        }
                        $newres->set_source_info($newres->str()->substring(0, $startpos + $newres->length[0]),
                                                 $this->get_max_subpattern(), $this->get_subpattern_map());

                        $path = null;
                        // TODO: if ($this->options === null || $this->options->extensionneeded).
                        $path = null;//$this->determine_characters_left($str, $startpos, $newres, $fulllastmatch);
                        if ($path !== null) {
                            $newres->left = $path->length[0] - $newres->length[0];
                            $newres->extendedmatch = new qtype_preg_matching_results($path->full, $path->index,
                                                                                     $path->length, $path->left);

                            $newres->extendedmatch->set_source_info($path->str(), $this->get_max_subpattern(), $this->get_subpattern_map());
                        }
                        // Finally, save the possible partial match.
                        $partialmatches[] = $newres;*/
                    }
                }
            }
            // Resolve ambiguities between the reached states.
            foreach ($reached as $key1 => $reached1) {
                foreach ($reached as $key2 => $reached2) {
                    if ($reached1 == null || $reached2 == null || $reached1->state !== $reached2->state) {
                        continue;
                    }
                    if ($reached1->worse_than($reached2, false, false, $areequal)) {
                        $reached[$key1] = null;
                    } else if ($reached2->worse_than($reached1, false, false, $areequal)) {
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
            $newstates = array_keys($newstates);

if ($DEBUG) {
    echo "\ntotally matched, without ambiguities:\n";
    foreach ($newstates as $newstate) {
        echo $states[$newstate]->last_transition_to_str() . "\n";
    }
    echo "\nall reached states:\n";
    foreach ($states as $state) {
        if ($state != null) {
            echo 'state ' . $state->state->number . ': ' . $state->subpatterns_to_str() . 'last transition is ' . $state->last_transition_to_str() . "\n";
        }
    }
    echo "------------------------------------------\n";
}

            $curstates = $newstates;
        }
        // Find the best result.
        foreach ($states as $curresult) {
            if ($curresult !== null && ($result === null || $result->worse_than($curresult, false, false, $areequal))) {
                $result = $curresult;
            }
        }
        if (!$fullmatchfound) {
            foreach ($partialmatches as $curresult) {
                if ($curresult !== null && ($result === null || $result->worse_than($curresult, false, false, $areequal))) {
                    $result = $curresult;
                }
            }
        }
        if ($result === null) {
            return $this->create_nomatch_result($str);
        } else {
            $index = array($startpos);
            $length = array($result->length());
            for ($i = 1; $i <= $this->get_max_subpattern(); $i++) {
                $index[$i] = $result->index_first($i);
                $length[$i] = $result->length($i);
            }

            return new qtype_preg_matching_results($result->full, $index, $length, $result->left, null/*$result->extendedmatch*/);
        }
    }

    /**
     * Constructs an NFA corresponding to the given node.
     * @param $node - object of nfa_preg_node child class.
     * @param $isassertion - will the result be a lookaround-assertion automaton.
     * @return - object of qtype_preg_nondeterministic_fa in case of success, false otherwise.
     */
    public function build_nfa($node, $isassertion = false) {
        $result = new qtype_preg_nondeterministic_fa($this->parser->get_subpatterns_count(), $this->get_subpattern_map());

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

        if ($options === null) {
            $options = new qtype_preg_matching_options();
        }
        $options->expandtree = true;

        parent::__construct($regex, $modifiers, $options);

        if (!isset($regex) || !empty($this->errors)) {
            return;
        }

        $nfa = self::build_nfa($this->dst_root);
        if ($nfa !== false) {
            $this->automaton = $nfa;
            $this->automaton->on_subexpr_added(0, $this->ast_root->subpattern); // Inform the automaton that 0-subexpr is represented by root.
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
