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

    public $index;               // Indexes captured for each subtree.
    public $length;              // Lengths captured for each subtree.

    public $index_new;           // new
    public $length_new;          // story

    public $full;                // Is this a full match?
    public $left;                // How many characters left for full match?
    public $extendedmatch;       // Match extension in case of partial match.

    public $str;                 // String being captured or generated.
    public $last_transition;     // The last transition matched.
    public $last_match_len;      // Length of the last match.
    public $captured_transitions;

    public function index_first($subpattern = 0) {
        $subtree = $this->automaton->subtree_from_subpatt_number($subpattern);
        return $this->index[$subtree];
    }

    public function length($subpattern = 0) {
        $subtree = $this->automaton->subtree_from_subpatt_number($subpattern);
        return $this->length[$subtree];
    }

    public function is_subpattern_captured($subpattern) {
        $subtree = $this->automaton->subtree_from_subpatt_number($subpattern);
        return $this->length[$subtree] != qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    public function set_whole_match($index, $length) {
        $this->index[1] = $index;
        $this->length[1] = $length; // The whole expression's id is 1.
    }

    public function increase_whole_match_length($delta) {
        $this->length[1] += $delta; // The whole expression's id is 1.
    }

    public function equals($to) {
        return ($this->state === $to->state &&
                $this->index == $to->index &&
                $this->length == $to->length);
    }

    /**
     * Resets the given subpattern to no match. In PCRE mode also resets all inner subpatterns.
     */
    public function reset_subtree($node, $old, $new, $mode = qtype_preg_handling_options::MODE_PCRE) {
        if ($node->id == 1) {
            return;
        }
        if (/*$mode == qtype_preg_handling_options::MODE_POSIX &&*/ is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->reset_subtree($operand, $old, $new, $mode);
            }
        }
        if ($old) {
            $this->index[$node->id] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $this->length[$node->id] = qtype_preg_matching_results::NO_MATCH_FOUND;
        }
        if ($new) {
            $this->index_new[$node->id] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $this->length_new[$node->id] = qtype_preg_matching_results::NO_MATCH_FOUND;
        }
    }

    /**
     * Returns 1 if this beats other, -1 if other beats this, 0 otherwise.
     */
    public function leftmost_longest($other) {
        for ($i = 1; $i <= $this->automaton->subptree_count(); $i++) {
            if (/*$i == 0 ||*/ !array_key_exists($i, $this->index)) {
                continue;
            }
            $ind_this = $this->index[$i];
            $ind_that = $other->index[$i];
            $len_this = $this->length[$i];
            $len_that = $other->length[$i];

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
     * Writes subtrees start\end information to this state.
     */
    public function write_subtree_info($transition, $pos, $matchlen, $options) {
        if ($options !== null && !$options->capturesubpatterns) {
            return;
        }

        // Reset all NEW subtrees to no match - they are being matched again.
        foreach ($transition->subtree_start as $node) {
            $this->reset_subtree($node, false, true, $options->mode);
        }
        // Reset all OLD subtrees to no match - they are matched and replaced by new ones.
        foreach ($transition->subtree_end as $node) {
            if ($this->index_new[$node->id] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $this->reset_subtree($node, true, false, $options->mode);
            }
        }

        // Set start indexes of subtrees.
        foreach ($transition->subtree_start as $node) {
            $this->index_new[$node->id] = $pos;
        }

        // Set length of subtrees.
        foreach ($transition->subtree_end as $node) {
            if ($this->index_new[$node->id] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $this->length_new[$node->id] = $pos - $this->index_new[$node->id] + $matchlen;
                // Replace old results with new results.
                $this->index[$node->id] = $this->index_new[$node->id];
                $this->length[$node->id] = $this->length_new[$node->id];
            }
        }
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
        foreach ($this->index as $key => $index) {
            $length = $this->length[$key];
            $result .= $key . ': (' . $index . ', ' . $length . ') ';
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
    public function create_nomatch_state($state, $str) {
        $result = new qtype_preg_nfa_processing_state();
        $result->automaton = $this->automaton;
        $result->state = $state;

        for ($i = 1; $i <= $this->parser->get_number_of_nodes(); $i++) {     // Include the whole expression with id = 1.
            $result->index[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $result->length[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $result->index_new = $result->index;
            $result->length_new = $result->length;
        }

        $result->full = false;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;

        $result->str = clone $str;
        $result->last_transition = null;
        $result->last_match_len = 0;
        $result->captured_transitions = array();

        return $result;
    }

    public function create_nomatch_result() {
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
                $newstate->write_subtree_info($transition, $curpos, $length, $this->options);

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
            //$newstate->write_subtree_info($transition, $curpos, $length, $this->options);   // TODO: is it needed?

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
                        $newstate->write_subtree_info($transition, $startpos + $curstateobj->length[0], $length, $this->options);
                        $newstate->left = -1;   TODO: replace to this.
                        captured_transitions???
                        $newstate->extendedmatch = null;*/

                        $newstate = new qtype_preg_nfa_processing_state(false, $curstateobj->index, $curstateobj->length,
                                                                        $curstateobj->index_new, $curstateobj->length_new,
                                                                        qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null,
                                                                        $transition->to, $transition, $length, $curstateobj->captured_transitions, $curstateobj);
                        $newstate->length[0] += $length;
                        $newstate->write_subtree_info($transition, $startpos + $curstateobj->length[0], $length, $this->options);

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
        $DEBUG = 1;

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
                $initial = $this->create_nomatch_state($curstate, $str);
                $initial->set_whole_match($startpos, 0);
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
                        //continue;
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
                        $newstate->write_subtree_info($transition, $curpos, $length, $this->options);

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
            // Replace curstates with newstates.
            $newstates = array();
            foreach ($reached as $curstate) {
                if ($states[$curstate->state->number] === null ||
                    $states[$curstate->state->number]->worse_than($curstate, false, false, $areequal)) {
                    // Currently stored state needs replacement.
                    $states[$curstate->state->number] = $curstate;
                    $newstates[$curstate->state->number] = true;
                    //echo 'REPLACING state ' . $curstate->state->number . ': ' . $curstate->subpatterns_to_str() . "\n";
                } else {
                    //echo 'THROWN state ' . $curstate->state->number . ': ' . $curstate->subpatterns_to_str() . "\n";
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
            return $this->create_nomatch_result();
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
        $result = new qtype_preg_nondeterministic_fa($this->parser->get_number_of_nodes(), $this->get_subpattern_map());

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
            $this->automaton->on_subpatt_added(0, $this->ast_root->id);
        } else {
            $this->automaton = null;
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
        }
    }
}
