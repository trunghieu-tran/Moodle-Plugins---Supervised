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

    // Should we call bruteforce method to find a match?
    protected $bruteforcematch = false;

    // Should we call bruteforce method to generate a partial match extension?
    protected $bruteforcegeneration = false;

    // Max number of states during simulation
    protected $maxstatescount = 1000;

    public function name() {
        return 'fa_matcher';
    }

    protected function get_engine_node_name($nodetype, $nodesubtype) {
        switch($nodetype) {
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT:
            case qtype_preg_node::TYPE_NODE_CONCAT:
            case qtype_preg_node::TYPE_NODE_ALT:
            case qtype_preg_node::TYPE_NODE_ASSERT:
            case qtype_preg_node::TYPE_NODE_SUBEXPR:
            case qtype_preg_node::TYPE_NODE_COND_SUBEXPR:
                return 'qtype_preg_fa_' . $nodetype;
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
            case qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL:
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

    /**
     * These subtypes do not have directly corresponding DST nodes.
     */
    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CHARSET:
            case qtype_preg_node::TYPE_LEAF_META:
            case qtype_preg_node::TYPE_LEAF_ASSERT:
            case qtype_preg_node::TYPE_LEAF_BACKREF:
            case qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL:
            case qtype_preg_node::TYPE_LEAF_TEMPLATE:
            case qtype_preg_node::TYPE_NODE_TEMPLATE:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return true;
            default:
                return get_string($pregnode->type, 'qtype_preg');
        }
    }

    protected function create_fa_exec_stack_item($subexpr, $state, $startpos) {
        $stackitem = new qtype_preg_fa_stack_item();
        $stackitem->subexpr = $subexpr;
        $stackitem->recursionstartpos = $startpos;
        $stackitem->state = $state;
        $stackitem->full = false;
        $stackitem->next_char_flags = 0x00;
        $stackitem->matches = array();
        $stackitem->subexpr_to_subpatt = array(0 => $this->astroot);   // Remember this explicitly
        $stackitem->last_transition = null;
        $stackitem->last_match_len = 0;
        return $stackitem;
    }

    /**
     * Creates a processing state object for the given state filled with "nomatch" values.
     */
    protected function create_initial_state($state, $str, $startpos) {
        $result = new qtype_preg_fa_exec_state();
        $result->matcher = $this;
        $result->startpos = $startpos;
        $result->length = 0;
        $result->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $result->extendedmatch = null;
        $result->str = clone $str;
        $result->stack = array($this->create_fa_exec_stack_item(0, $state, $startpos));
        $result->backtrack_states = array();
        if (in_array($state, $this->backtrackstates)) {
            $result->backtrack_states[] = $result;
        }
        return $result;
    }

    protected function set_last_transition($state, $transition, $length) {
        $state->set_last_transition($transition);
        $state->set_last_match_len($length);
    }

    /**
     * Generates character(s) by the transition and returns new state.
     */
    protected function generate_char_by_transition($curstate, $transition, $str, $curpos) {
        $newstate = clone $curstate;
        $transitions = array_merge($transition->mergedbefore, array($transition), $transition->mergedafter);

        foreach ($transitions as $tr) {

            //echo "lookin at $tr\n";

            // One more crutch. Are there merged transitions?
            if (count($transitions) > 1 && $tr !== $transition) {
                //echo "inside if\n";
                $is_assert = $tr->pregleaf->type === qtype_preg_node::TYPE_LEAF_ASSERT;
                $affects_generation = $is_assert && ($tr->pregleaf->is_start_anchor() || $tr->pregleaf->is_end_anchor());

                // If this is is an anchor, everything is handled in the main transition.
                // Otherwise, we should call next_character here.
                if ($affects_generation) {
                    $this->after_transition_passed($newstate, $tr, $curpos, 0, false);
                    //echo "continue\n";
                    continue;
                }
            }

            list($flag, $newchr) = $tr->next_character($str, $newstate->str, $curpos, 0, $newstate);
            if ($flag === qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE) {
                //echo "return null\n";
                return null;
            }
            //echo "generated: $flag '$newchr'\n";

            $length = 0;

            // TODO: ^ and \A

            if (is_object($newchr) && $newchr->length() > 0) {
                // If we had to end the generation, but generated something, cut out this path
                if ($newstate->is_flag_set(qtype_preg_leaf::NEXT_CHAR_END_HERE)) {
                    return null;
                }
                $newstate->str->concatenate($newchr);
                $length = $newchr->length();
                //var_dump($newstate->str->string());
                //var_dump($newchr->string());
                //echo "\n";
            }

            $this->after_transition_passed($newstate, $tr, $curpos, $length, false);
            $newstate->set_flag($flag);

            $curpos += $length;

            /*$recursionlevel = $newstate->recursion_level();
            echo "level $recursionlevel: generated char '$newchr' by $tr. length changed {$newstate->length} : {$newstate->length}\n";
            echo "new string is {$newstate->str}\n\n";*/
        }
        foreach ($transition->mergedafter as $tr) {
            $this->after_transition_passed($newstate, $tr, $curpos, 0, false);
        }
        $this->set_last_transition($newstate, $transition, $newstate->length - $curstate->length);
        return $newstate;
    }

    /**
     * Matches an array of transitions. If all transitions are matched, that means a full match. Partial match otherwise.
     */
    protected function match_transitions($curstate, $transitions, $str, $curpos, &$length, &$full, $addbacktracks) {
        $newstate = clone $curstate;
        $length = 0;
        $full = true;

        foreach ($transitions as $tr) {
            $tmplength = 0;
            $result = $tr->pregleaf->match($str, $curpos, $tmplength, $newstate);
            if ($result) {
                $this->after_transition_passed($newstate, $tr, $curpos, $tmplength, $addbacktracks);
                //echo "passed $tr\n";
            } else {
                $newstate->length += $tmplength;
            }

            // Increase curpos and length anyways, even if the match is partial (backrefs)
            $curpos += $tmplength;
            $length += $tmplength;

            if (!$result) {
                $full = false;
                break;
            }

            // Unbelievable crutch: we should stop matching merged transitions that
            // could be placed outside this subexpression in the original automaton
            if ($newstate->recursion_level() > 0 && $newstate->is_subexpr_captured_top($newstate->subexpr())) {
                break;
            }
        }

        if (!$full) {
            $newstate->set_state($curstate->state());
            $newstate->set_full(false);
            $newstate->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        }

        return $newstate;
    }

    protected function match_recursive_transition_begin($curstate, $transition, $str, $curpos, &$length, &$full, $addbacktracks) {
        $result = $this->match_transitions($curstate, $transition->mergedbefore, $str, $curpos, $length, $full, $addbacktracks);
        if ($full) {
            $this->set_last_transition($result, $transition, $length);
        }
        return $result;
    }

    protected function match_recursive_transition_end($newstate, $recursionstartpos, $recursionlength, $str, $curpos, &$length, &$full, $addbacktracks) {
        $this->after_transition_passed($newstate, $newstate->last_transition(), $recursionstartpos, $recursionlength, $addbacktracks);
        $newstate->length -= $recursionlength;
        return $this->match_transitions($newstate, $newstate->last_transition()->mergedafter, $str, $curpos, $length, $full, $addbacktracks);
    }

    /**
     * Checks if this transition (with all merged to it) matches a character. Returns a new state.
     */
    protected function match_regular_transition($curstate, $transition, $str, $curpos, &$length, &$full, $addbacktracks) {
        $transitions = array_merge($transition->mergedbefore, array($transition), $transition->mergedafter);
        $newstate = $this->match_transitions($curstate, $transitions, $str, $curpos, $length, $full, $addbacktracks);
        $this->set_last_transition($newstate, $transition, $newstate->length - $curstate->length);
        return $newstate;
    }

    /**
     * Updates all fields in the newstate after a transition match.
     */
    protected function after_transition_passed($newstate, $transition, $curpos, $length, $addbacktracks = true) {
        $endstates = $this->automaton->get_end_states($newstate->subexpr());

        $newstate->set_state($transition->to);
        $newstate->set_full(in_array($newstate->state(), $endstates));
        $newstate->left = $newstate->is_full() ? 0 : qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $newstate->length += $length;
        $newstate->write_tag_values($transition, $curpos, $length);

        if ($addbacktracks && in_array($transition->to, $this->backtrackstates)) {
            $newstate->backtrack_states[] = $newstate;
        }
    }

    /**
     * Returns an array of states which can be reached without consuming characters.
     * @param qtype_preg_fa_exec_state startstates states to go from.
     * @return an array of states (including the start state) which can be reached without consuming characters.
     */
    protected function epsilon_closure($startstates, $str, $addbacktracks) {
        $curstates = $startstates;
        $result = array(qtype_preg_fa_transition::GREED_LAZY => array(),
                        qtype_preg_fa_transition::GREED_GREEDY => $startstates
                        );
        while (!empty($curstates)) {
            // Get the current state and iterate over all transitions.
            $curstate = array_pop($curstates);
            $curpos = $curstate->startpos + $curstate->length;
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state(), true);
            foreach ($transitions as $transition) {
                $length = 0;
                $full = true;

                if ($transition->pregleaf->subtype != qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                    continue;
                }

                $newstate = $this->match_regular_transition($curstate, $transition, $str, $curpos, $length, $full, $addbacktracks);
                if (!$full) {
                    continue;
                }

                // This could be the end of a recursive call.
                while ($full && $newstate->recursion_level() > 0 && $newstate->is_full()) {
                    $topitem = array_pop($newstate->stack);
                    $recursionmatch = $topitem->last_subexpr_match($this->get_options()->mode, $topitem->subexpr);
                    $newstate = $this->match_recursive_transition_end($newstate, $topitem->recursionstartpos, $recursionmatch[1], $str, $curpos, $length, $full, $addbacktracks);
                }

                if (!$full) {
                    continue;
                }

                // Resolve ambiguities if any.
                $number = $newstate->state();
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

    protected function get_resume_state($str, $laststate) {

        if ($laststate->last_match_len() > 0 && $laststate->last_transition()->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            // The last transition was a partially matched backreference; we can only continue from this transition.
            $backref_length = $laststate->length($laststate->last_transition()->pregleaf->number);
            $prevpos = $laststate->startpos + $laststate->length - $laststate->last_match_len();

            $resumestate = clone $laststate;
            $this->after_transition_passed($resumestate, $laststate->last_transition(), $prevpos, $backref_length, false);
            $resumestate->length -= $laststate->last_match_len(); // Backreference was partially matched

            // Re-write the string with correct characters.
            list($flag, $newchr) = $laststate->last_transition()->next_character($str, $resumestate->str, $prevpos, $laststate->last_match_len(), $laststate);
            if ($flag !== qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE) {
                $resumestate->str->concatenate($newchr);
            }

            return $resumestate;
        }

        $endstates = $this->automaton->get_end_states($laststate->subexpr());

        // There was no match at all, or the last transition was fully-matched.
        $curpos = $laststate->startpos + $laststate->length;

        // Check for a \Z \z or $ assertion before the eps-closure of the end state. Then it's possible to remove few characters.
        $transitions = $this->automaton->get_adjacent_transitions($laststate->state(), true);
        foreach ($transitions as $transition) {
            if ($transition->loopsback || !($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $transition->pregleaf->is_end_anchor())) {
                continue;
            }
            $closure = $this->epsilon_closure(array($laststate->state() => $laststate), $str, false);
            $closure = array_merge($closure[qtype_preg_fa_transition::GREED_LAZY], $closure[qtype_preg_fa_transition::GREED_GREEDY]);
            foreach ($closure as $curclosure) {
                if (in_array($curclosure->state(), $endstates)) {
                    // The end state is reachable; return it immediately.
                    $result = clone $laststate;
                    $this->after_transition_passed($result, $transition, $curpos, 0, false);
                    return $result;
                }
            }
        }

        // Just return the state as is.
        return $laststate;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion\string str - original string that was matched.
     * @param qtype_preg_fa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_fa_exec_state.
     */
    protected function generate_extension_brute_force($str, $laststate) {
        $endstates = $this->automaton->get_end_states($laststate->subexpr());
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state(), $endstates)) {
            return $resumestate;
        }

        $curstates = array($resumestate);
        $result = null;

        $statescount = count($curstates);

        while (!empty($curstates)) {
            $curstate = array_pop($curstates);
            --$statescount;
            $curpos = $curstate->startpos + $curstate->length;
            if ($curstate->is_full() && ($result === null || $curstate->leftmost_shortest($result))) {
                $result = $curstate;
            }
            $transitions = $this->automaton->get_adjacent_transitions($curstate->state(), true);
            foreach ($transitions as $transition) {
                if ($transition->pregleaf->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
                    continue;
                }
                // Skip loops.
                if ($transition->loopsback) {
                    continue;
                }

                // Create a new state.
                $newstate = $this->generate_char_by_transition($curstate, $transition, $str, $curpos);
                if ($newstate === null) {
                    continue;
                }
                // Is it longer than existing one?
                if ($result !== null && $newstate->length > $result->length) {
                    continue;
                }

                // This could be the end of a recursive call.
                $length = $transition->pregleaf->consumes($curstate);
                $full = true;
                while ($full && $newstate->recursion_level() > 0 && $newstate->is_full()) {
                    $topitem = array_pop($newstate->stack);
                    $recursionmatch = $topitem->last_subexpr_match($this->get_options()->mode, $topitem->subexpr);
                    $newstate = $this->match_recursive_transition_end($newstate, $topitem->recursionstartpos, $recursionmatch[1], $str, $curpos, $length, $full, false);
                }

                if (!$full) {
                    continue;
                }

                // Save the new state.
                if ($statescount >= $this->maxstatescount) {
                    break;
                }
                $curstates[] = $newstate;
                ++$statescount;
            }
        }
        return $result;
    }

    /**
     * Returns the minimal path to complete a partial match.
     * @param qtype_poasquestion\string str - original string that was matched.
     * @param qtype_preg_fa_exec_state laststate - the last state matched.
     * @return object of qtype_preg_fa_exec_state.
     */
    protected function generate_extension_fast($str, $laststate) {
        $endstates = $this->automaton->get_end_states($laststate->subexpr());
        $resumestate = $this->get_resume_state($str, $laststate);
        if (in_array($resumestate->state(), $endstates)) {
            return $resumestate;
        }

        $states = array();
        $curstates = array();

        // Create an array of processing states for all fa states (the only resumestate, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states[$curstate] = $curstate === $resumestate->state()
                               ? $resumestate
                               : null;
        }

        // Get an epsilon-closure of the resume state.
        $closure = $this->epsilon_closure(array($resumestate->state() => $resumestate), $str, false);
        $closure = array_merge($closure[qtype_preg_fa_transition::GREED_LAZY], $closure[qtype_preg_fa_transition::GREED_GREEDY]);
        foreach ($closure as $curclosure) {
            $states[$curclosure->state()] = $curclosure;
            $curstates[] = $curclosure->state();
        }

        $result = null;

        $statescount = count($curstates);

        // Do search.
        while (!empty($curstates)) {
            $reached = array();
            // We'll replace curstates with reached by the end of this loop.
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $curstate = $states[array_pop($curstates)];
                --$statescount;
                $curpos = $curstate->startpos + $curstate->length;
                if ($curstate->is_full() && ($result === null || $curstate->leftmost_shortest($result))) {
                    $result = $curstate;
                }
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state(), true);
                foreach ($transitions as $transition) {
                    if ($transition->pregleaf->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }
                    if ($transition->pregleaf->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
                        continue;
                    }
                    // Skip loops.
                    if ($transition->loopsback) {
                        continue;
                    }

                    // Create a new state.
                    $newstate = $this->generate_char_by_transition($curstate, $transition, $str, $curpos);
                    if ($newstate === null) {
                        continue;
                    }
                    // Is it longer than existing one?
                    if ($result !== null && $newstate->length > $result->length) {
                        continue;
                    }

                    // This could be the end of a recursive call.
                    $length = $transition->pregleaf->consumes($curstate);
                    $full = true;
                    while ($full && $newstate->recursion_level() > 0 && $newstate->is_full()) {
                        $topitem = array_pop($newstate->stack);
                        $recursionmatch = $topitem->last_subexpr_match($this->get_options()->mode, $topitem->subexpr);
                        $newstate = $this->match_recursive_transition_end($newstate, $topitem->recursionstartpos, $recursionmatch[1], $str, $curpos, $length, $full, false);
                    }

                    if (!$full) {
                        continue;
                    }

                    // Save the current result.
                    $number = $newstate->state();
                    if (!isset($reached[$number]) || $newstate->leftmost_shortest($reached[$number])) {
                        $reached[$number] = $newstate;
                    }
                }
            }

            $reached = $this->epsilon_closure($reached, $str, false);
            $reached = array_merge($reached[qtype_preg_fa_transition::GREED_LAZY], $reached[qtype_preg_fa_transition::GREED_GREEDY]);

            // Replace curstates with reached.
            foreach ($reached as $curstate) {
                // Currently stored state needs replacement if it's null, or if it's worse than the new state.
                if ($states[$curstate->state()] === null || $curstate->leftmost_shortest($states[$curstate->state()])) {
                    if ($statescount >= $this->maxstatescount) {
                        break;
                    }
                    $states[$curstate->state()] = $curstate;
                    $curstates[] = $curstate->state();
                    ++$statescount;
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
        $maxrecursionlevel = $str->length() + 1;

        $fullmatches = array();       // Possible full matches.
        $partialmatches = array();    // Possible partial matches.

        $curstates = array();    // States which the automaton is in at the current wave front.
        $lazystates = array();   // States reached lazily.

        foreach ($this->automaton->get_start_states(0) as $state) {
            $curstates[] = $this->create_initial_state($state, $str, $startpos);
        }

        $statescount = count($curstates);

        // Do search.
        while (!empty($curstates)) {
            $reached = array();
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $curstate = array_pop($curstates);
                --$statescount;
                $curpos = $startpos + $curstate->length;
                $cursubexpr = $curstate->subexpr();
                $recursionlevel = $curstate->recursion_level();
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state(), true);

                //echo "\n";

                foreach ($transitions as $transition) {
                    $length = 0;
                    $full = true;

                    //$char = core_text::substr($str, $curpos, 1);
                    //echo "level $recursionlevel: trying $transition at pos $curpos (char '$char')\n";

                    if ($transition->pregleaf->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && $recursionlevel < $maxrecursionlevel) {
                        // Handle a recursive transition
                        $newstate = $this->match_recursive_transition_begin($curstate, $transition, $str, $curpos, $length, $full, true);
                        if ($full) {
                            $startstates = $this->automaton->get_start_states($transition->pregleaf->number);
                            foreach ($startstates as $state) {
                                $newnewstate = clone $newstate;
                                $newnewstate->stack[] = $this->create_fa_exec_stack_item($transition->pregleaf->number, $state, $curpos);
                                //echo "add recursive state {$newnewstate->state()}\n";
                                $reached[] = $newnewstate;
                            }
                        }
                    } else if ($transition->pregleaf->type !== qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
                        // Handle a non-recursive transition transition
                        $newstate = $this->match_regular_transition($curstate, $transition, $str, $curpos, $length, $full, true);

                        if ($full) {

                            //echo "level $recursionlevel: MATCHED $transition at pos $curpos (char '$char'). length changed {$curstate->length} : {$newstate->length}\n";
                            //echo $newstate->subpatts_to_string();

                            // Filter out states that did not start the actual subexpression call.
                            $skip = !$newstate->is_subexpr_match_started($cursubexpr);

                            // Filter out states that have senseless zero-length loops.
                            $skip = $skip || ($transition->loopsback && $newstate->has_null_iterations());

                            // This could be the end of a recursive call.
                            while (!$skip && $full && $newstate->recursion_level() > 0 && $newstate->is_full()) {
                                $topitem = array_pop($newstate->stack);
                                $recursionmatch = $topitem->last_subexpr_match($this->get_options()->mode, $topitem->subexpr);
                                $newstate = $this->match_recursive_transition_end($newstate, $topitem->recursionstartpos, $recursionmatch[1], $str, $curpos, $length, $full, true);
                            }

                            $skip = $skip || !$full;

                            // Save the current match.
                            if (!$skip) {
                                if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                                    $lazystates[] = $newstate;
                                } else {
                                    //echo "add state {$newstate->state()}\n";
                                    $reached[] = $newstate;
                                }
                            }

                            if (!$skip && $newstate->recursion_level() === 0 && $newstate->is_full()) {
                                $fullmatches[] = $newstate;
                            }
                        }
                    }

                    if (!$full && empty($fullmatches)) {
                        // Handle a partial match.
                        //echo "level $recursionlevel: not matched, partial match length is $length\n";
                        $partialmatches[] = $newstate;
                    }
                }

                // If there's no full match yet and no states reached, try the lazy ones.
                if (empty($fullmatches) && empty($reached) && !empty($lazystates)) {
                    $reached[] = array_pop($lazystates);
                }
            }
            foreach ($reached as $newstate) {
                if ($statescount >= $this->maxstatescount) {
                    break;
                }
                $curstates[] = $newstate;
                ++$statescount;
            }
        }

        // Return array of all possible matches.
        $result = $fullmatches;
        if (empty($result)) {
            $result = $partialmatches;
        }
        return $result;
    }

    /**
     * Creates an index object for fast matching. In classical TNFS we would use one dimension (state number),
     * but to support recursion we have to add one more dimension. In fact, $recursionlevel is a string
     * sequence containing numbers of subsequently called subexpressions.
     */
    protected static function create_index($recursionlevel, $statenumber) {
        $result = new stdClass();
        $result->recursionlevel = $recursionlevel;
        $result->state = $statenumber;
        return $result;
    }

    protected static function ensure_index_exists(&$array, $key0, $key1, $defaultvalue) {
        if (!isset($array[$key0])) {
            $array[$key0] = array();
        }
        if (!isset($array[$key0][$key1])) {
            $array[$key0][$key1] = $defaultvalue;
        }
    }

    /**
     * This method should be used if there are no backreferences in the regex.
     * Returns array of all possible matches.
     */
    protected function match_fast($str, $startpos) {
        $maxrecursionlevel = $str->length() + 1;

        $states = array('0' => array()); // Objects of qtype_preg_fa_exec_state. First dimension is recursion level, second is state number.
        $curstates = array();          // Indexes of states which the automaton is in at the current wave front. Use stdClass with "recursionlevel" and "state" fields.
        $lazystates = array();         // States (objects!) reached lazily.
        $partialmatches = array();     // Possible partial matches.

        $startstates = $this->automaton->get_start_states(0);

        $endstatereached = false;

        // Create an array of processing states for all fa states (the only initial state, other states are null yet).
        foreach ($this->automaton->get_states() as $curstate) {
            $states['0'][$curstate] = in_array($curstate, $startstates)
                                  ? $this->create_initial_state($curstate, $str, $startpos)
                                  : null;
        }

        $reached = array();

        // Get an epsilon-closure of the initial state.
        foreach ($states['0'] as $state) {
            if ($state !== null) {
                $reached[] = $state;
            }
        }

        $closure = $this->epsilon_closure($reached, $str, true);
        $lazystates = $closure[qtype_preg_fa_transition::GREED_LAZY];
        $closure = $closure[qtype_preg_fa_transition::GREED_GREEDY];

        foreach ($closure as $state) {
            $states['0'][$state->state()] = $state;
            $curstates[] = self::create_index('0', $state->state());
            $endstatereached = $endstatereached || $state->is_full();
        }

        $statescount = count($curstates);

        // Do search.
        while (!empty($curstates)) {
            $reached = array(); // $reached uses stdClass with "recursionlevel" and "state" fields as well
            // We'll replace curstates with reached by the end of this loop.
            while (!empty($curstates)) {
                // Get the current state and iterate over all transitions.
                $index = array_pop($curstates);
                $curstate = $states[$index->recursionlevel][$index->state];
                --$statescount;
                $curpos = $startpos + $curstate->length;
                $cursubexpr = $curstate->subexpr();
                $recursionlevel = $curstate->recursion_level();
                $transitions = $this->automaton->get_adjacent_transitions($curstate->state(), true);

                //echo "\n";

                foreach ($transitions as $transition) {
                    if ($transition->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                        continue;
                    }

                    $length = 0;
                    $full = true;

                    //$char = core_text::substr($str, $curpos, 1);
                    //echo "level $recursionlevel: trying $transition at pos $curpos (char '$char')\n";

                    if ($transition->pregleaf->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && $recursionlevel < $maxrecursionlevel) {
                        // Handle a recursive transition
                        $newstate = $this->match_recursive_transition_begin($curstate, $transition, $str, $curpos, $length, $full, true);
                        if ($full) {
                            $startstates = $this->automaton->get_start_states($transition->pregleaf->number);
                            foreach ($startstates as $state) {
                                $newnewstate = clone $newstate;
                                $newnewstate->stack[] = $this->create_fa_exec_stack_item($transition->pregleaf->number, $state, $curpos);
                                $index = self::create_index($newnewstate->recursive_calls_sequence(), $newnewstate->state());
                                self::ensure_index_exists($reached, $index->recursionlevel, $index->state, null);
                                if ($reached[$index->recursionlevel][$index->state] === null || $newnewstate->leftmost_longest($reached[$index->recursionlevel][$index->state])) {
                                    //echo "add recursive state {$newnewstate->state()} for subexpr {$transition->pregleaf->number}\n";
                                    $reached[$index->recursionlevel][$index->state] = $newnewstate;
                                }
                            }
                        }
                    } else if ($transition->pregleaf->type !== qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
                        // Handle a non-recursive transition transition
                        $newstate = $this->match_regular_transition($curstate, $transition, $str, $curpos, $length, $full, true);

                        if ($full) {

                            //echo "level $recursionlevel: MATCHED $transition at pos $curpos (char '$char'). length changed {$curstate->length} : {$newstate->length}\n";
                            //echo $newstate->subpatts_to_string();

                            // Filter out states that did not start the actual subexpression call.
                            $skip = !$newstate->is_subexpr_match_started($cursubexpr);

                            $endstatereached = $endstatereached || (!$skip && $newstate->recursion_level() === 0 && $newstate->is_full());

                            // This could be the end of a recursive call.
                            while (!$skip && $full && $newstate->recursion_level() > 0 && $newstate->is_full()) {
                                $topitem = array_pop($newstate->stack);
                                $recursionmatch = $topitem->last_subexpr_match($this->get_options()->mode, $topitem->subexpr);
                                $newstate = $this->match_recursive_transition_end($newstate, $topitem->recursionstartpos, $recursionmatch[1], $str, $curpos, $length, $full, true);
                                //$newtopitem = end($newstate->stack);
                                //echo "ended matching of {$topitem->subexpr}, now matching {$newtopitem->subexpr} from state {$newstate->state()}\n";
                            }

                            $skip = $skip || !$full;

                            // Save the current result.
                            if (!$skip) {
                                if ($transition->greediness == qtype_preg_fa_transition::GREED_LAZY) {
                                    $lazystates[] = $newstate;
                                } else {
                                    $index = self::create_index($newstate->recursive_calls_sequence(), $newstate->state());
                                    self::ensure_index_exists($reached, $index->recursionlevel, $index->state, null);
                                    if ($reached[$index->recursionlevel][$index->state] === null || $newstate->leftmost_longest($reached[$index->recursionlevel][$index->state])) {
                                        //echo "add state {$newstate->state()}\n";
                                        $reached[$index->recursionlevel][$index->state] = $newstate;
                                    }
                                }
                            }
                        }
                    }

                    if (!$full && !$endstatereached) {
                        // Handle a partial match.
                        //echo "level $recursionlevel: not matched, partial match length is $length\n";
                        $partialmatches[] = $newstate;
                    }
                }
            }

            // If there's no full match yet and no states reached, try the lazy ones.
            if (!$endstatereached && empty($reached) && !empty($lazystates)) {
                $lazy = array_pop($lazystates);
                $index = self::create_index($lazy->recursive_calls_sequence(), $lazy->state());
                self::ensure_index_exists($reached, $index->recursionlevel, $index->state, null);
                $reached[$index->recursionlevel][$index->state] = $lazy;
            }

            foreach ($reached as $recursionlevel => $reachedforlevel) {
                $reached[$recursionlevel] = $this->epsilon_closure($reachedforlevel, $str, true);
                $lazystates = array_merge($lazystates, $reached[$recursionlevel][qtype_preg_fa_transition::GREED_LAZY]);
                $reached[$recursionlevel] = $reached[$recursionlevel][qtype_preg_fa_transition::GREED_GREEDY];
            }


            // Iterate over reached states. Get epsilon-closure for each recursion level.
            foreach ($reached as $newstates) {
                foreach ($newstates as $newstate) {
                    $index = self::create_index($newstate->recursive_calls_sequence(), $newstate->state());
                    self::ensure_index_exists($states, $index->recursionlevel, $index->state, null);
                    if ($states[$index->recursionlevel][$index->state] === null || $newstate->leftmost_longest($states[$index->recursionlevel][$index->state])) {
                        if ($statescount >= $this->maxstatescount) {
                            break;
                        }
                        $states[$index->recursionlevel][$index->state] = $newstate;
                        $curstates[] = $index;
                        ++$statescount;
                        $endstatereached = $endstatereached || ($newstate->recursion_level() === 0 && $newstate->is_full());
                    }
                }
            }
        }

        // Return array of all possible matches.
        $result = array();
        foreach ($this->automaton->get_end_states(0) as $endstate) {
            if ($states['0'][$endstate] !== null) {
                $result[] = $states['0'][$endstate];
            }
        }
        if (empty($result)) {
            $result = $partialmatches;
        }
        return $result;
    }

    protected function generate_extensions($matches, $str, $startpos) {

        //echo "\nSTART generate_extensions\n";
        //echo $this->bruteforcegeneration ? "brute force mode\n\n" : "fast mode\n\n";

        $cache = array();

        foreach ($matches as $match) {

            //echo "\nGENERATING FOR MATCH IN STATE ({$match->state()},{$match->recursive_calls_sequence()}) : {$match->str->substring(0, $startpos + $match->length)}\n";

            $match->extendedmatch = null;
            // Try each backtrack state and choose the shortest one.
            $match->backtrack_states = array_merge(array($match), $match->backtrack_states);
            foreach ($match->backtrack_states as $backtrack) {
                $backtrack->str = $backtrack->str->substring(0, $startpos + $backtrack->length);

                //echo "\nbacktrack to state ({$backtrack->state()},{$backtrack->recursive_calls_sequence()}) : {$backtrack->str}\n";

                // Look in the cache.
                $cacheindex0 = $backtrack->recursive_calls_sequence();
                $cacheindex1 = $backtrack->state();
                self::ensure_index_exists($cache, $cacheindex0, $cacheindex1, array());

                $fromcache = false;
                $tmp = null;
                foreach ($cache[$cacheindex0][$cacheindex1] as $cached) {
                    if ($cached->backtrack->equals($backtrack)){
                        //echo "TAKE FROM CACHE\n";
                        $tmp = $cached->extendedmatch;
                        $fromcache = true;
                    }
                }

                // No cached version, generate new one.
                if ($tmp === null) {
                    $tmp = $this->bruteforcegeneration
                         ? $this->generate_extension_brute_force($str, $backtrack)
                         : $this->generate_extension_fast($str, $backtrack);
                }

                // Ain't no nothing at all.
                if ($tmp === null) {
                    continue;
                }

                // Cache the generated match.
                if (!$fromcache) {
                    $tocache = new stdClass();
                    $tocache->backtrack = $backtrack;
                    $tocache->extendedmatch = $tmp;
                    $cache[$cacheindex0][$cacheindex1][] = $tocache;
                }

                //echo "result str: {$tmp->str}\n";

                // Calculate 'left'.
                $prefixlen = $startpos;
                while ($prefixlen < $match->str->length() && $prefixlen < $tmp->str->length() &&
                       $match->str[$prefixlen] == $tmp->str[$prefixlen]) {
                    $prefixlen++;
                }
                $left = $tmp->str->length() - $prefixlen;
                // Choose the best one by:
                // 1) minimizing 'left' of the generated extension
                // 2) maximizing length of the generated extension so it's as much to the original length as possible
                if (($match->extendedmatch === null) ||
                    ($match->left > $left) ||
                    ($match->left === $left && $match->extendedmatch->length < $tmp->length)) {
                    $match->extendedmatch = $tmp;
                    $match->left = $left;
                }
            }
        }
    }

    public function match_from_pos($str, $startpos) {

        if ($this->automaton->is_empty()) {
            $result = $this->create_initial_state(null, $str, $startpos);
            return $result->to_matching_results();
        }

        // Find all possible matches. Using the fast match method if there are no backreferences.
        $possiblematches = $this->bruteforcematch
                         ? $this->match_brute_force($str, $startpos)
                         : $this->match_fast($str, $startpos);

        //$matchescount = count($possiblematches);
        //echo "\n FOUND $matchescount matches\n\n";

        if (empty($possiblematches)) {
            $result = $this->create_initial_state(null, $str, $startpos);
            if ($this->options->extensionneeded) {
                $this->generate_extensions(array($result), $str, $startpos);
            }
        } else {
            // Check if a full match was found.
            $fullmatchexists = false;
            foreach ($possiblematches as $match) {
                if ($match->is_full()) {
                    $fullmatchexists = true;
                    break;
                }
            }

            // If there was no full match, generate extensions for each partial match.
            if (!$fullmatchexists && $this->options->extensionneeded) {
                $this->generate_extensions($possiblematches, $str, $startpos);
            }

            // Choose the best match.
            $result = array_pop($possiblematches);
            foreach ($possiblematches as $match) {
                if ($match->leftmost_longest($result, false)) {
                    $result = $match;
                }
            }

            // Choose the best extension. A better extension could be generated from another match.
            // But use the extention from the choosen match as the default value.
            if (!$fullmatchexists && $this->options->extensionneeded) {
                foreach ($possiblematches as $match) {
                    $ext = $match->extendedmatch;
                    if ($ext === null) {
                        continue;
                    }
                    if ($result->extendedmatch === null || $match->left < $result->left) {
                        $result->extendedmatch = $ext;
                        $result->left = $match->left;
                    }
                }
            }
        }

        // Because a partial match could be found in a recursive call, remove all
        // recursive stack items to get back to the 0-level
        $result->stack = array($result->stack[0]);

        if ($result->extendedmatch !== null) {
            $result->extendedmatch = $result->extendedmatch->to_matching_results();
            $result->extendedmatch->extendedmatch = null;   // Holy cow, this is ugly
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
    public function build_fa($dstnode, $mergeassertions = false) {
        $result = new qtype_preg_fa($this, $this->get_nodes_with_subexpr_refs());

        $mergeassertions = true;

        $stack = array();
        $dstnode->create_automaton($result, $stack, $mergeassertions);
        $body = array_pop($stack);
        $result->fastartstates[0] = array($body['start']);
        $result->faendstates[0] = array($body['end']);
        $result->calculate_subexpr_start_and_end_states();

        if ($mergeassertions) {
            $result->remove_unreachable_states();
        }

        if ($body['breakpos'] !== null || empty($result->adjacencymatrix)) {
            throw new qtype_preg_empty_fa_exception('', $body['breakpos']);
        }
        $result->merge_end_transitions();
        global $CFG;
        $CFG->pathtodot = '/usr/bin/dot';
        $intersected = array();
        if ($mergeassertions)
        {
            // Intersect complex assertions automata.
            foreach ($result->innerautomata as $state => $inner) {
                foreach ($inner as $automaton) {
                    if (!in_array($automaton, $intersected)) {
                        $states = array();
                        if (array_key_exists($state, $result->innerautomata)) {
                            $states[] = $state;
                        }
                        foreach ($result->innerautomata as $anotherstate => &$anotherinner) {
                            foreach ($anotherinner as &$anotherautomaton) {
                                if ($automaton == $anotherautomaton && $state !== $anotherstate) {
                                    $states[] = $anotherstate;
                                    $intersected[] = $automaton;
                                }
                            }
                        }
                        //$result->fa_to_dot('svg', "/home/elena/fa_2.svg");
                        //$automaton[0]->fa_to_dot('svg', "/home/elena/fa_3.svg");
                        $result = $result->intersect($automaton[0], $states, $automaton[1]);
                        // $result->fa_to_dot('svg', "/home/elena/fa_1.svg", true);
                    }
                }
            }
            //$result->calculate_subexpr_start_and_end_states();
        }

        /*global $CFG;
        $CFG->pathtodot = '/usr/bin/dot';
        $namesuffix = $mergeassertions ? "merged" : "original";
        $result->fa_to_dot('svg', "/home/user/fa_$namesuffix.svg");*/

        return $result;
    }



    protected function check_for_infinite_recursion() {
        $end = $this->automaton->get_end_states();
        $front = $this->automaton->get_start_states();
        $endstatereached = false;
        while (!$endstatereached && !empty($front)) {
            $curstate = array_pop($front);
            if (in_array($curstate, $end)) {
                $endstatereached = true;
                break;
            }
            $transitions = $this->automaton->get_adjacent_transitions($curstate, true);
            foreach ($transitions as $transition) {
                if ($transition->loopsback) {
                    continue;
                }
                // Skip recursive transitions
                if ($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && $transition->pregleaf->isrecursive) {
                    continue;
                }
                $front[] = $transition->to;
            }
        }
        if (!$endstatereached) {
            $this->errors[] = new qtype_preg_error(get_string('error_infiniterecursion', 'qtype_preg'), $this->regex->string(), $this->astroot->position);
        }
    }

    public function __construct($regex = null, $options = null) {
        global $CFG;

        if ($options === null) {
            $options = new qtype_preg_matching_options();
        }
        $options->replacesubexprcalls = true;

        parent::__construct($regex, $options);

        if (isset($CFG->qtype_preg_fa_simulation_state_limit)) {
            $this->maxstatescount = $CFG->qtype_preg_fa_simulation_state_limit;
        }
        $this->maxstatescount = max($this->maxstatescount, 100);

        if (!isset($regex) || !empty($this->errors)) {
            return;
        }

        try {
            $this->automaton = $this->build_fa($this->dstroot, $this->options->mergeassertions);
        } catch (qtype_preg_toolargefa_exception $e) {
            $this->errors[] = new qtype_preg_too_complex_error($regex, $this);
            return;
        } catch (qtype_preg_empty_fa_exception $e) {
            $this->errors[] = new qtype_preg_empty_fa_error($regex, $e->a);
            return;
        }

        $this->check_for_infinite_recursion();
        if (!empty($this->errors)) {
            return;
        }

        $this->calculate_nesting_map($this->astroot, array($this->astroot->subpattern));
        $this->calculate_backtrackstates();
        $this->calculate_bruteforce();

        //echo "backtrack states:\n";
        //var_dump($this->backtrackstates);

        // Here we need to inform the automaton that 0-subexpr is represented by the AST root.
        // But for now it's implemented in other way, using the subexpr_to_subpatt array of the exec state.
        // $this->automaton->on_subexpr_added($this->astroot);
    }
}
