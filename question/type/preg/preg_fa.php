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
 * Defines finite automata states and transitions classes for regular expression matching.
 * The class is used by FA-based matching engines (DFA and NFA), provides standartisation to them and enchances testability.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');

/**
 * Represents finite automaton transitions (without subpatterns information).
 *
 * As NFA and DFA have different ways to store subpatterns information, they both should inherit this class to add necessary fields.
 */
class qtype_preg_fa_transition {

    /** Empty transition. */
    const TYPE_TRANSITION_EPS = 'eps_transition';
    /** Transition with unmerged simple assert. */
    const TYPE_TRANSITION_ASSERT = 'assert';
    /** Empty transition or transition with unmerged simple assert. */
    const TYPE_TRANSITION_BOTH = 'both';
    /** Capturing transition. */
    const TYPE_TRANSITION_CAPTURE = 'capturing';

    /** Transition from first automata. */
    const ORIGIN_TRANSITION_FIRST = 'first';
    /** Transition from second automata. */
    const ORIGIN_TRANSITION_SECOND = 'second';
    /** Transition from intersection part. */
    const ORIGIN_TRANSITION_INTER = 'intersection';

    /** @var object of qtype_preg_fa_state class - a state which transition starts from. */
    public $from;
    /** @var object of qtype_preg_leaf class - condition for this transition. */
    public $pregleaf;
    /** @var object of qtype_preg_fa_state class - state which transition leads to. */
    public $to;
    /** @var type of the transition - should be equal to a constant defined in this class. */
    public $type;
    /** @var origin of the transition - should be equal to a constant defined in this class. */
    public $origin;
    public $consumeschars;
    /** @var array of qtype_preg_nodes representing subpatterns starting at this transition. */
    public $subpatt_start;
    /** @var array of qtype_preg_nodes representing subpatterns ending at this transition. */
    public $subpatt_end;
    /** @var array of qtype_preg_nodes representing subexpressions starting at this transition. */
    public $subexpr_start;
    /** @var array of qtype_preg_nodes representing subexpressions ending at this transition. */
    public $subexpr_end;

    public function __clone() {
        $this->pregleaf = clone $this->pregleaf;    // When clonning a transition we also want a clone of its pregleaf.
    }

    public function __construct($from, &$pregleaf, $to, $origin = self::ORIGIN_TRANSITION_FIRST, $consumeschars = true) {
        $this->from = $from;
        $this->pregleaf = clone $pregleaf;
        $this->to = $to;
        $this->origin = $origin;
        $this->consumeschars = $consumeschars;
        $this->subpatt_start = array();
        $this->subpatt_end = array();
        $this->subexpr_start = array();
        $this->subexpr_end = array();
    }

    public function get_label_for_dot($index1, $index2) {
        $addedcharacters = '/(), ';
        if (strpbrk($index1, $addedcharacters) !== false) {
            $index1 = '"' . $index1 . '"';
        }
        if (strpbrk($index2, $addedcharacters) !== false) {
            $index2 = '"' . $index2 . '"';
        }
        if ($this->origin == self::ORIGIN_TRANSITION_FIRST) {
            $color = 'violet';
        } else if ($this->origin == self::ORIGIN_TRANSITION_SECOND) {
            $color = 'blue';
        } else if ($this->origin == self::ORIGIN_TRANSITION_INTER) {
            $color = 'red';
        }
        $lab = $this->open_tags_tohr();
        $lab .= $this->pregleaf->leaf_tohr();
        $lab .= $this->close_tags_tohr();
        $lab = '"[' . str_replace('"', '\"', $lab) . ']"';

        // Dummy transitions are displayed dotted.
        if ($this->consumeschars) {
            return "$index1->$index2" . "[label = $lab, color = $color];";
        } else {
            return "$index1->$index2" . "[label = $lab, color = $color, style = dotted];";
        }
    }

    /**
     * Remove same elements from automata.
     *
     * @param array array for removing.
     */
    public function remove_same_elements(&$array) {
        for ($i = 0; $i < count($array); $i++) {
            for ($j = ($i+1); $j < count($array); $j++) {
                if ($array[$i] == $array[$j]) {
                    unset($array[$j]);
                    $array = array_values($array);
                    $j--;
                }
            }
        }
    }

    /**
     * Returns intersection of transitions.
     *
     * @param other another transition for intersection.
     */
    public function intersect($other) {
        $thishastags = $this->has_tags();
        $otherhastags = $other->has_tags();
        $resulttran = null;
        $resultleaf = $this->pregleaf->intersect_leafs($other->pregleaf, $thishastags, $otherhastags);
        if ($resultleaf != null) {
            if (($this->is_eps() || $this->is_unmerged_assert()) && (!$other->is_eps() && !$other->is_unmerged_assert())) {
                $resulttran = new qtype_preg_nfa_transition(0, $resultleaf, 1, $other->origin, $other->consumeschars);
            } else if (($other->is_eps() || $other->is_unmerged_assert()) && (!$this->is_eps() && !$this->is_unmerged_assert())) {
                $resulttran = new qtype_preg_nfa_transition(0, $resultleaf, 1, $this->origin, $this->consumeschars);
            } else {
                $resulttran = new qtype_preg_nfa_transition(0, $resultleaf, 1, self::ORIGIN_TRANSITION_INTER);
            }
        }
        if ($resulttran !== null) {
            $resulttran->subpatt_start = array_merge($this->subpatt_start, $other->subpatt_start);
            $resulttran->subpatt_end = array_merge($this->subpatt_end, $other->subpatt_end);
            $resulttran->subexpr_start = array_merge($this->subexpr_start, $other->subexpr_start);
            $resulttran->subexpr_end = array_merge($this->subexpr_end, $other->subexpr_end);
            $resulttran->remove_same_elements($resulttran->subpatt_start);
            $resulttran->remove_same_elements($resulttran->subpatt_end);
            $resulttran->remove_same_elements($resulttran->subexpr_start);
            $resulttran->remove_same_elements($resulttran->subexpr_end);
        }
        return $resulttran;
    }

    /**
     * Returns true if transition has any tag.
     */
    public function has_tags() {
        return (count($this->subpatt_start) || count($this->subpatt_end) || count($this->subexpr_start) || count($this->subexpr_end));
    }

    /**
     * Returns true if transition is eps.
     *
     * @param other another transition for intersection.
     */
    public function is_eps() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_META && ($this->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY));
    }

    /**
     * Returns true if transition is with unmerged assert.
     *
     * @param other another transition for intersection.
     */
    public function is_unmerged_assert() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT);
    }

    /**
     * Set this transition right type.
     */
    public function set_transition_type() {
        if ($this->is_eps()) {
            $this->type = self::TYPE_TRANSITION_EPS;
        } else if ($this->is_unmerged_assert()) {
            $this->type = self::TYPE_TRANSITION_ASSERT;
        } else {
            $this->type = self::TYPE_TRANSITION_CAPTURE;
        }
    }

    /**
     * Save tags from other transition in this transition.
     *
     * @param other another transition for saving tags.
     * @return result transition.
     */
    public function save_tags($other) {
        $this->subpatt_start = array_merge($this->subpatt_start, $other->subpatt_start);
        $this->subpatt_end = array_merge($this->subpatt_end, $other->subpatt_end);
        $this->subexpr_start = array_merge($this->subexpr_start, $other->subexpr_start);
        $this->subexpr_end = array_merge($this->subexpr_end, $other->subexpr_end);
        return $this;
    }

    public function open_tags_tohr() {
        $result ='';
        if (count($this->subpatt_start) != 0 || count($this->subexpr_start) != 0) {
            foreach ($this->subpatt_start as $subpatt) {
                $result .= '(';
            }
            $result .= '/';
            foreach ($this->subexpr_start as $subpatt) {
                $result .= '(';
            }
            return $result;
        }
    }

    public function close_tags_tohr() {
        $result ='';
        if (count($this->subpatt_end) != 0 || count($this->subexpr_end) != 0) {
            foreach ($this->subexpr_end as $subpatt) {
                $result .= ')';
            }
            $result .= '/';
            foreach ($this->subpatt_end as $subpatt) {
                $result .= ')';
            }
            return $result;
        }
    }
}

/**
 * Class for finite automaton state.
 */
class qtype_preg_fa_state {

    /** @var object reference to the qtype_preg_finite_automaton object this state belongs to.
     *
     * We are violating principle "a child shouldn't know the parent" there, but the state need to signal important information back to
     * automaton during its construction: becoming non-deterministic, having eps or pure-assert transitions etc.
     */
    protected $fa;
    /** @var array of qtype_preg_fa_transition child objects, indexed. */
    protected $outtransitions;
    /** @var array of qtype_preg_fa_transition child objects, indexed. */
    protected $intotransitions;
    /** @var boolean whether state is from intersection part or not. */
    public $hasintersection;
    /** @var boolean whether state was copied or not. */
    public $wascopied;
    /** @var boolean whether state is deterministic, i.e. whether it has no characters with two or more possible outgoing transitions. */
    protected $deterministic;
    /** @var array of int - first numbers of the state. */
    public $firstnumbers;
    /** @var array of int - second numbers of the state, if state is from intersection part. */
    public $secondnumbers;

    public function __construct(&$fa = null) {
        $this->fa = $fa;
        $this->firstnumbers = array(-1);    // States should be numerated from 0 by calling qtype_preg_finite_automaton::numerate_states().
        $this->secondnumbers = array();
        $this->outtransitions = array();
        $this->intotransitions = array();
        $this->hasintersection = false;
        $this->wascopied = false;
        $this->deterministic = true;
    }

    public function set_fa(&$fa) {
        $this->fa = $fa;
    }

    /**
     * Adds a transtition to the given state.
     *
     * @param transtion a reference to an object of child class of qtype_preg_fa_transition.
     */
    public function add_transition(&$transition) {
        $transition->from = $this;
        $this->outtransitions[] = $transition;
        // TODO - check whether it makes a node non-deterministic.
        // TODO - signal automaton if a node become non-deterministic, see make_nondeterministic function in automaton class.

        if ($transition->pregleaf->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
            $this->fa->epsilon_transtion_added();
        }

        if ($transition->pregleaf->type === qtype_preg_node::TYPE_LEAF_ASSERT) {
            $this->fa->assertion_transition_added();
        }

        $this->fa->transition_added();
    }

    /**
     * Removes all transitions from this state.
     */
    public function remove_all_transitions() {
        $this->outtransitions = array();
    }

    /**
     * Replaces oldref with newref in each transition.
     *
     * @param oldref - a reference to the old state.
     * @param newref - a reference to the new state.
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach ($this->outtransitions as $transition) {
            if ($transition->to === $oldref) {
                $transition->to = $newref;
            }
        }
    }

    public function outgoing_transitions() {
        return $this->outtransitions;
    }

    /**
     * Returns an array of transitions possible with current string and position.
     */
    public function possible_transitions($str, $pos) {
        // TODO - use pregnode->match from transitions.
    }

    /**
     * Returns true if this is accepting end state.
     *
     * End state doesn't have outgoing transitions.
     */
    /*public function is_end_state() {
        return empty($this->outtransitions);
    }*/
}

/**
 * Class for finite automaton group of states.
 */
 class qtype_preg_fa_group {
    /** @var reference to qtype_preg_finite_automaton object this group of states belongs to. */
    protected $fa;
    /** @var array of int ids of states, which include in this group. */
    protected $states;
    /** @var first character on which it made transition to this group. */
    protected $char;
    /** @var array of qtype_preg_fa_group through which are in this group. */
    public $prev_groups;
    
    public function __construct(&$fa = null) {
        $this->fa = $fa;
        $this->states = array();
        $this->char = 0;
        $this->prev_groups = array();
    }
    
    /**
     * Change reference to qtype_preg_finite_automaton object this group of states belongs to. 
     *
     * @param fa - a reference to new automata.
     */
    public function set_fa(&$fa) {
        $this->fa = $fa;
    }
    
    /**
     * Return character on which it made transition to this group.
     */
    public function get_char() {
        return $this->char;
    }
    
    /**
     * Change character on which it made transition to this group.
     *
     * @param char - new character on which it made transition to this group.
     */
    public function set_char($char) {
        $this->char = $char;
    }
    
    /**
     * Return array of int ids of states, which include in this group.
     */
    public function get_states() {
        return $this->states;
    }
    
    /**
     * Append new state in group.
     *
     * @param state - new state, which include in this group.
     */
    public function add_state($state) {
        $this->state[] = $state;
    }
    
    /**
     * Return array of group through which are in this group.
     */
    public function get_prev_groups() {
        return $this->prev_groups;
    }
    
    /**
     * Fill array of group through which are in this group.
     *
     * @param prev_groups - new array of group through which are in this group.
     */
    public function fill_prev_groups($prev_groups) {
        $this->prev_groups = $prev_groups;
    }
    
    /**
     * Compare two groups.
     *
     * @param another - group of states for compare.
     */
    public function cmpgroup(&$another) {
        if (count($this->states) != count($another->states)) {
            return false;
        }
        foreach ($this->states as $thisstate) {
            $find = false;
            foreach ($another->states as $anotherstate) {
                if ($thisstate == anotherstate) {
                    $find = true;
                }
            }
            if ($find != true) {
                return false;
            }
        }
        return true;
    }
    
    public function is_empty() {
        return (count($this->states) == 0);
    }
    
    public function has_end_states() {
        $endstates = $this->fa->end_states();
        foreach ($this->states as $thisstate) {
            foreach ($endstates as $endstate) {
                if ($thisstate == $endstate) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Create string with way.
     *
     * @param another - group of states from another automata.
     */
    public function way_to_string(&$another) {
        $string = '';
        for ($i = 0; $i < count($this->prev_groups); $i++) {
            if($i != 0) {
                $string .= '-['.$this->prev_groups[$i]->char.']->';
            }
            $string .= '[(';
            for ($j = 0; $j < count($this->prev_groups[$i]->states); $j++) {
                $string .= $this->fa->statenumbers[$this->prev_groups[$i]->states[$j]];
                if ($j != count($this->prev_groups[$i]->states) - 1) {
                    $string .= ',';
                }
            }
            $string .= '),(';
            for ($j = 0; $j < count($another->prev_groups[$i]->states); $j++) {
                $string .= $another->fa->statenumbers[$another->prev_groups[$i]->states[$j]];
                if ($j != count($another->prev_groups[$i]->states) - 1) {
                    $string .= ',';
                }
            }
            $string .= ')]';
        }
        $string .= '-['.$this->char.']->';
        if (count($this->states) == 0) {
            $string .= 'no';
        }
        else {
            for ($j = 0; $j < count($this->states); $j++) {
                $string .= $this->fa->statenumbers[$this->states[$j]];
                if ($j != count($this->states) - 1) {
                    $string .= ',';
                }
            }
        }
        $string .= '),(';
        if (count($another->states) == 0) {
            $string .= 'no';
        }
        else {
            for ($j = 0; $j < count($another->prev_groups[$i]->states); $j++) {
                $string .= $another->fa->statenumbers[$another->prev_groups[$i]->states[$j]];
                if ($j != count($another->prev_groups[$i]->states) - 1) {
                    $string .= ',';
                }
            }
        }
        $string .= ')]';
        return $string;
    }
 }

/**
 * Represents an abstract finite automaton. Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa.
 */
abstract class qtype_preg_finite_automaton {

    /** @var array of qtype_preg_fa_state, indexed by state numbers(will be deleted, do not use). */
    public $states;
    /** @var array with strings with numbers of states, indexed by their ids from adjacencymatrix. */
    public $statenumbers;
    /** @var array of int ids of states - start states. */
    public $startstates;
    /** @var array of of int ids of states - end states. */
    public $endstates;

    /** @var two-dimensional array of qtype_preg_fa_transition objects: first index is "from", second index is "to"*/
    protected $adjacencymatrix;

    /** @var boolean is automaton really deterministic - it can be even if it shoudn't.
     *
     * May be used for optimisation when an NFA object actually stores a DFA.
     */
    protected $deterministic;

    /** @var boolean whether automaton has epsilon-transtions. */
    protected $haseps;
    /** @var boolean whether automaton has simple assertion transtions. */
    protected $hasassertiontransitions;

    protected $statelimit;
    protected $statecount;

    protected $transitionlimit;
    protected $transitioncount;


    public function __construct() {
        $this->adjacencymatrix = array();
        $this->startstates = array();
        $this->endstates = array();
        $this->deterministic = true;
        $this->haseps = false;
        $this->hasassertiontransitions = false;
        $this->statecount = 0;
        $this->transitioncount = 0;
        $this->set_limits();
    }

    /**
     * The function should set $this->statelimit and $this->transitionlimit properties using $CFG.
     *
     * DFA and NFA have different size limits in $CFG, so let them have separate implementation of this function.
     */
    abstract protected function set_limits();

    /**
     * Returns whether automaton is really deterministic.
     */
    public function is_deterministic() {
        return $this->deterministic;
    }

    /**
     * Used from qype_preg_fa_state class to signal that automaton become non-deterministic.
     *
     * Note that only methods of the automaton can make it deterministic and set this property to true.
     */
    public function make_nondeterministic() {
        $this->deterministic = false;
    }

    /**
     * Returns whether this implementation support DFA or NFA.
     */
    abstract public function should_be_deterministic();

    /**
     * Returns the start state for automaton.
     */
    public function start_states() {
        return $this->startstates;
    }

    /**
     * Return the end state of the automaton.
     *
     * TODO - determine, whether we could get automaton with several end states - then return array.
     */
    public function end_states() {
        return $this->endstates;
    }

    /**
     * Return array of all states' ids of automata.
     *
     */
    public function get_states() {
        if (count($this->adjacencymatrix) == 0) {
            $states = array();
        } else {
            $states = array_keys($this->adjacencymatrix);
        }
        return $states;
    }

    /**
     * Return column with key from matrix $array.
     *
     * @param array - matrix.
     * @param key - key of column.
     */
    public function get_column($array, $key) {
        $result = array();
        foreach ($array as $element) {
            $reskey = array_search($element, $array);
            if (array_key_exists($key, $element)) {
                $result[$reskey] = $element[$key];
            }
        }
        return $result;
    }

    /**
     * Return outtransitions of state with id $state.
     *
     * @param state - id of state which outtransitions are intresting.
     * @param isoutcoming - boolean flag which type of transitions to get (true - outtransitions, false - intotransitions).
     */
    public function get_adjacent_transitions($state, $isoutcoming) {
        if ($isoutcoming) {
            return $this->adjacencymatrix[$state];
        } else {
            return $this->get_column($this->adjacencymatrix, $state);
        }
    }

    /**
     * Get array with reak numbers of states of this automata.
     *
     */
    public function get_state_numbers() {
        return $this->statenumbers;
    }

    public function state_exists(&$state) {
        foreach ($this->states as $curstate) {
            if ($curstate === $state) {
                return true;
            }
        }
        return false;
    }

    /**
     * Passing automata in given direction.
     *
     * @param direction - direction of passing(0-forward; 1-back).
     * @return array with ids of passed states.
     */
    public function pass_automata($direction) {
        // Initialization wavefront.
        if ($direction == 0) {
            // Going forward in automata.
            $oldfront = array_values($this->startstates);
        } else {
            // Going back in automata.
            $oldfront = array_values($this->endstates);
        }

        $aregone = array();
        $newfront = array();
        // Working with states from currendt front of wave.
        while (count($oldfront)!=0) {
            // Searching ways from current state.
            foreach ($oldfront as $curstate) {
                $isendstate = false;
                // State has not been already gone.
                if (array_search($curstate, $aregone) === false) {
                    // Comparing with end states or start states.
                    if ($direction == 0) {
                        if (array_search($curstate, $this->endstates) !== false) {
                            $isendstate = true;
                        }
                    } else {
                        if (array_search($curstate, $this->startstates) !== false) {
                            $isendstate = true;
                        }
                    }
                    // Analysis outtransitions if go forward and intotransitions if go back.
                    $transitions = $this->get_adjacent_transitions($curstate, !$direction);
                    
                    // No any interconnecting states.
                    if (count($transitions) != 0 || $isendstate) {
                        $aregone[] = $curstate;
                    }

                    // Has interconnecting states.
                    if (count($transitions) != 0) {
                        // Adding interconnecting states to new wave front.
                        foreach ($transitions as $curtran) {
                            if ($direction == 0) {
                                $newfront[] = $curtran->to;
                            } else {
                                $newfront[] = $curtran->from;
                            }
                        }
                    }
                }
            }
            // Coping new wavefront to old.
            $oldfront = $newfront;

            $newfront = array();
        }

        return $aregone;
    }

    /**
     * Delete all blind states in automata.
     *
     */
    public function del_blind_states() {
        // Pass automata forward.
        $aregoneforward = $this->pass_automata(0);
        // Pass automata forward.
        $aregoneback = $this->pass_automata(1);
        // Check for each state of atomata was it gone or not.
        $states = $this->get_states();
        foreach ($states as $curstate) {
            // Current state wasn't passed.
            if (array_search($curstate, $aregoneforward) === false || array_search($curstate, $aregoneback) === false) {
                $this->remove_state($curstate);
            }
        }
    }

    /**
     * Find index of state by its numbers.
     *
     * @param number1 - the first number of state.
     * @param number2 - the second number of state.
     * @return index of state if it was found and -1 if it wasn't found.
     */
    public function find_state_index($number1, $number2 = -1) {
        $index = -1;
        // Searching only by first numbers.
        if ($number2 == -1) {
            for ($i = 0; $i < count($this->states) && $index < 0; $i++) {
                $num1 = $this->states[$i]->firstnumbers[0];
                if ($num1 == $number1) {
                    $index = $i;
                }
            }
        } else if ($number1==-1) {
            // Searching only by second numbers.
            for ($i = 0; $i < count($this->states) && $index < 0; $i++) {
                $num2 = $this->states[$i]->secondnumbers[0];
                if ($num2 == $number2) {
                    $index = $i;
                }
            }
        } else {
            // Searching by both numbers.
            for ($i = 0; $i < count($this->states) && $index < 0; $i++) {
                $num1 = $this->states[$i]->firstnumbers[0];
                $num2 = $this->states[$i]->secondnumbers[0];
                if ($num1 == $number1 && $num2 == $number2) {
                    $index = $i;
                }
            }
        }
        return $index;
    }

    /**
     * Write automata as a dot-style string.
     * @param type type of the resulting image, should be 'svg', png' or something.
     * @param filename the absolute path to the resulting image file.

     * @return dot_style string with the description of automata.
     */
    public function fa_to_dot($type = null, $filename = null) {
        $addedcharacters = '/(), ';
        $result = "digraph res {\n    ";
        if ($this->statecount != 0) {
            // Add start states.
            foreach ($this->startstates as $start) {
                $realnumber = $this->statenumbers[$start];
                if (strpbrk($realnumber, $addedcharacters) !== false) {
                    $result .= '"' . $realnumber . '"';
                } else {
                    $result .= $realnumber;
                }
                $result .= ';';
            }
            $result .= "\n    ";
            // Add end states.
            foreach ($this->endstates as $end) {
                $realnumber = $this->statenumbers[$end];
                if (strpbrk($realnumber, $addedcharacters) !== false) {
                    $result .= '"' . $realnumber . '"';
                } else {
                    $result .= $realnumber;
                }
                $result .= ';';
            }
            // Add connected states.
            $states = $this->get_states();
            foreach ($states as $curstate) {
                $outtransitions = $this->get_adjacent_transitions($curstate, true);
                foreach ($outtransitions as $tran) {
                    $result .= "\n    ";
                    $fromindex = $this->statenumbers[$tran->from];
                    $toindex = $this->statenumbers[$tran->to];
                    $result .= $tran->get_label_for_dot($fromindex, $toindex);
                }
            }
        }
        $result .= "\n}";
        if ($type != null) {
            qtype_preg_regex_handler::execute_dot($result, $type, $filename);
        }
        return $result;
    }

    /**
     * Add the start state of the automaton to given state.
     */
    public function add_start_state($state) {
        if (array_key_exists($state, $this->adjacencymatrix)) {
            if (array_search($state, $this->startstates) === false) {
                $this->startstates[] = $state;
            }
        } else {
            throw new qtype_preg_exception('set_start_state error: No state ' . $state . ' in automaton');
        }
    }

    /**
     * Add the end state of the automaton to given state.
     */
    public function add_end_state($state) {
        if (array_key_exists($state, $this->adjacencymatrix)) {
            if (array_search($state, $this->endstates) === false) {
                $this->endstates[] = $state;
            }
        } else {
            throw new qtype_preg_exception('set_end_state error: No state ' . $state . ' in automaton');
        }
    }

    /**
     * Remove the end state of the automaton.
     */
    public function remove_end_state($state) {
        unset($this->endstates[array_search($state, $this->endstates)]);
        $this->endstates = array_values($this->endstates);
    }

    /**
     * Remove the start state of the automaton.
     */
    public function remove_start_state($state) {
        unset($this->startstates[array_search($state, $this->startstates)]);
        $this->startstates = array_values($this->startstates);
    }

    /**
     * Remove all end states of the automaton.
     */
    public function clean_end_states() {
        // Cleaning end states.
        $endstates = $this->end_states();
        foreach ($endstates as $endstate) {
            $this->remove_end_state($endstate);
        }
    }

    /**
     * Remove all start states of the automaton.
     */
    public function clean_start_states() {
        // Cleaning end states.
        $startstates = $this->start_states();
        foreach ($startstates as $startstate) {
            $this->remove_start_state($startstate);
        }
    }

    /**
     * Set state as copied.
     *
     * @param state - state to be copied.
     */
    public function set_copied_state($state) {
        $number = $this->statenumbers[$state];
        $number = '(' . $number;
        $number .= ')';
        $this->statenumbers[$state] = $number;
    }

    /**
     * Change real number of state.
     *
     * @param state - state to change.
     * @param realnumber - new real number.
     */
    public function change_real_number($state, $realnumber) {
        $this->statenumbers[$state] = $realnumber;
    }

    /**
     * Replaces oldref with newref in every transition of the automaton.
     *
     * @param oldref - a reference to the old state.
     * @param newref - a reference to the new state.
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach ($this->states as $curstate) {
            $curstate->update_state_references($oldref, $newref);
        }
    }

    public function has_epsilons() {
        return $this->haseps;
    }

    /**
     * Used from qype_preg_fa_state class to signal that a transition was added to the automaton.
     */
    public function transition_added() {
        $this->transitioncount++;
        if ($this->transitioncount > $this->transitionlimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
     * Used from qype_preg_fa_state class to signal that an epsilon-transition was added to the automaton.
     * Note that only methods of the automaton can delete all epsilon-transitions and make property false.
     */
    public function epsilon_transtion_added() {
        $this->haseps = true;
    }

    public function has_assertion_transitions() {
        return $this->hasassertiontransitions;
    }

    /**
     * Delete transition.
     *
     * @param del transition for deleting.
     */
    public function del_transition ($del) {
        unset($this->adjacencymatrix[$del->from][$del->to]);
    }

    /**
     * Used from qype_preg_fa_state class to signal that an assert-transition was added to the automaton.
     * Note that only methods of the automaton the merge all assert-transitions and make property false.
     */
    public function assertion_transition_added() {
        $this->hasassertiontransitions = false;
    }

    /**
     * Adds a state to the automaton.
     *
     * @param state a reference to an object of qtype_preg_fa_state class.
     * @return state id of added state.
     */
    public function add_state($statenumber) {
        if ((count($this->statenumbers) != 0 && array_search($statenumber, $this->statenumbers) === false) || count($this->statenumbers) == 0) {
            $this->adjacencymatrix[] = array();
            $this->statenumbers[] = $statenumber;
            $this->statecount++;
            if ($this->statecount > $this->statelimit) {
                throw new qtype_preg_toolargefa_exception('');
            }
        }
        return array_search($statenumber, $this->statenumbers);
    }

    /**
     * Add transition.
     *
     * @param transition transition for adding.
     */
    public function add_transition(&$transition) {
        $outtransitions = $this->get_adjacent_transitions($transition->from, true);
        // Automata  has already such ttransition.
        if (array_key_exists($transition->to, $outtransitions)) {
            // Get transition which it had before.
            $tran = &$this->adjacencymatrix[$transition->from][$transition->to];
            // Transitions are not equal.
            if ($tran != $transition) {
                // Find tags.
                $thishastags = $tran->has_tags();
                $otherhastags = $transition->has_tags();
                // Get union of leafs.
                $newleaf = $tran->pregleaf->unite_leafs($transition->pregleaf, $thishastags, $otherhastags);
                // Union isn't possible.
                if ($newleaf === null) {
                    $clones = array();  // array of clones of coping transitions.
                    // Get coping transitions.
                    $outtransitions = $this->get_adjacent_transitions($tran->to, true);
                    $intotransitions = $this->get_adjacent_transitions($tran->to, false);
                    // Get new number of cloned state.
                    $newnumber = '/' . $this->statenumbers[$transition->to];
                    $newto = $this->add_state($newnumber);
                    // Add clone state in start states if it's possible.
                    if (array_search($tran->to, $this->startstates) !== false) {
                        $this->add_start_state($newto);
                    }
                    // Add clone state in end states if it's possible
                    if (array_search($tran->to, $this->endstates) !== false) {
                        $this->add_end_state($newto);
                    }
                    $states = $this->get_state_numbers();
                    // Change transition.
                    $transition->to = $newto;
                    // Copy outtransitions for clone state.
                    foreach ($outtransitions as $outtran) {
                        $clone = clone($outtran);
                        $clone->from = $newto;
                        $clones[] = $clone;
                    }
                    // Copy intotransitions for clone state.
                    foreach ($intotransitions as $intotran) {
                        if ($tran->from != $intotran->from) {
                            $clone = clone($intotran);
                            $clone->to = $newto;
                            $clones[] = $clone;
                        }
                    }
                    // Add transitions of clone to automata.
                    foreach ($clones as $clone) {
                        $this->add_transition($clone);
                    }
                    // Add transition.
                    $this->adjacencymatrix[$transition->from][$newto] = $transition;
                } else {
                    // Add union of transitions.
                    $transition->pregleaf = $newleaf;
                    $this->adjacencymatrix[$transition->from][$transition->to] = $transition;
                }
            }
        } else {
            // No such transition.
            $this->adjacencymatrix[$transition->from][$transition->to] = $transition;
        }
    }

    /**
     * Removes a state from the automaton.
     *
     * @param state an id of the state to be removed.
     */
    public function remove_state($state) {
        // Removing row.
        unset($this->adjacencymatrix[$state]);
        // Removing column.
        foreach ($this->adjacencymatrix as &$curcolumn) {
            if (array_key_exists($state, $curcolumn)) {
                unset($curcolumn[$state]);
            }
        }
        // Removing real numbers.
        unset($this->statenumbers[$state]);
        $this->statecount--;
    }

    /**
     * Removes this fa. Return enpty fa.
     */
    public function remove_fa() {
        $result = new qtype_preg_nfa(0, 0, 0, array());
        return $result;
    }

    /**
     * Check if this state is from intersection part of autmata.
     */
    public function is_intersectionstate($state) {
        if (strpos($this->statenumbers[$state], ',') === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if this state was copied.
     */
    public function is_copied_state($state) {
        return (strpos($this->statenumbers[$state], ')'));
    }

    /**
     * Check if this state is full intersect state, it means it has two numbers from both automata.
     */
    public function is_full_intersect_state($state) {
        $numbers = $this->statenumbers[$state];
        $number = explode(',', $numbers, 2);
        if (count($number) == 2 && $number[0] != '' && $number[1] != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if such state is in array of start states.
     */
    public function has_startstate($state) {
        if (array_search($state, $this->startstates) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if such state is in array of end states.
     */
    public function has_endstate($state) {
        if (array_search($state, $this->endstates) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Read and create a FA from dot-like language. Mainly used for unit-testing.
     */
    public function read_fa($dotstring, $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST) {
        //  Dotstring split into an array of strings.
        $dotstring = explode("\n",$dotstring);
        // String of start states split into an array of start states.
        $startstates = explode(";",$dotstring[1]);
        // Append start states in automata.
        for ($i = 0; $i < count($startstates) - 1; $i++) {
            $startstates[$i] = trim($startstates[$i]);
            $startstates[$i] = trim($startstates[$i],'"');
            $this->add_state($startstates[$i]);
            $this->add_start_state(($this->statecount) - 1);
        }
        // String of end states split into an array of end states.
        $endstates = explode(";",$dotstring[2]);
        // Append end states in automata.
        for ($i = 0; $i < count($endstates) - 1; $i++) {
            $endstates[$i] = trim($endstates[$i]);
            $endstates[$i] = trim($endstates[$i],'"');
            $this->add_state($endstates[$i]);
            $this->add_end_state(($this->statecount) - 1);
        }
        // Append transition in automata.
        for ($i = 3; $i < (count($dotstring) - 1); $i++) {
            $arraystrings = preg_split('/(->|\[label="\[|\]"|color=|\];$)/u',$dotstring[$i]);
            // Delete the spaces at the beginning and end of line.
            $arraystrings[0] = trim($arraystrings[0]);
            $arraystrings[0] = trim($arraystrings[0],'"');
            if(array_search($arraystrings[0], $this->statenumbers) === false) {
                $this->add_state($arraystrings[0]);
            }
            $statefrom = array_search($arraystrings[0], $this->statenumbers);
            // Delete the spaces at the beginning and end of line.
            $arraystrings[1] = trim($arraystrings[1]);
            $arraystrings[1] = trim($arraystrings[1],'"');
            if(array_search($arraystrings[1], $this->statenumbers) === false) {
                $this->add_state($arraystrings[1]);
            }
            $stateto = array_search($arraystrings[1], $this->statenumbers);
            // Create transition.
            $chars = '';
            $asserts = array();
            $subpatt_start = array();
            $subpatt_end = array();
            $subexpr_start = array();
            $subexpr_end = array();
            $currentindex = 0;
            $point = false;
            // Parse a string into components.
            while($currentindex < strlen($arraystrings[2])) {
                // If subpatt_start.
                if($arraystrings[2][$currentindex] == '(') {
                    if($currentindex == 0 || $arraystrings[2][$currentindex - 1] != '\\') {
                        while($arraystrings[2][$currentindex] != '/') {
                            $subpatt_start[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                            $currentindex++;
                        }
                    }
                    $currentindex++;
                    // If subexpr_start.
                    if($arraystrings[2][$currentindex] == '(') {
                        while($arraystrings[2][$currentindex] == '(') {
                            $subexpr_start[] = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
                            $currentindex++;
                        }
                    }
                }
                // If subexpr_start without subpatt_start.
                else if($arraystrings[2][$currentindex] == '/' && $arraystrings[2][$currentindex + 1] == '(') {
                    $currentindex++;
                    while($arraystrings[2][$currentindex] == '(') {
                        $subexpr_start[] = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
                        $currentindex++;
                    }
                }
                // If current symbol is back_slash.
                else if($arraystrings[2][$currentindex] == '\\') {
                    switch($arraystrings[2][$currentindex+1]) {
                        case 'b': $asserts[] = '\\b'; break;
                        case 'B': $asserts[] = '\\B'; break;
                        case 'A': $asserts[] = '\\A'; break;
                        case 'z': $asserts[] = '\\z'; break;
                        case 'Z': $asserts[] = '\\Z'; break;
                        case 'G': $asserts[] = '\\G'; break;
                        default : $chars = $chars.'\\'.$arraystrings[2][$currentindex+1];
                    }
                    $currentindex = $currentindex + 2;
                }
                // If current symbol is assert.
                else if($arraystrings[2][$currentindex] == '^' || $arraystrings[2][$currentindex] == '$') {
                    $asserts[] = $arraystrings[2][$currentindex];
                    $currentindex++;
                }
                // If subexpr_end.
                else if($arraystrings[2][$currentindex] == ')') {
                    while($arraystrings[2][$currentindex] != '/') {
                        $subexpr_end[] = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
                        $currentindex++;
                    }
                    $currentindex++;
                    // If subpatt_end.
                    while($currentindex < strlen($arraystrings[2]) && $arraystrings[2][$currentindex] == ')') {
                        $subpatt_end[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                        $currentindex++;
                    }
                }
                // If subpatt_end without subexpr_end
                else if($arraystrings[2][$currentindex] == '/' && $arraystrings[2][$currentindex + 1] == ')') {
                    $currentindex++;
                    while($currentindex < strlen($arraystrings[2])) {
                        $subpatt_end[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                        $currentindex++;
                    }
                }
                // Current symbol just symbol.
                else {
                    if ($arraystrings[2][$currentindex] == '.') {
                        $point = true;
                    }
                    $chars = $chars.$arraystrings[2][$currentindex];
                    $currentindex++;
                }
            }
            // Fill transition.
            if(strlen($arraystrings[2]) > 0) {
                if(strlen($chars) != 0) {
                    $pregleaf = new qtype_preg_leaf_charset();
                    if ($point) {
                        $chars = '.';
                    }
                    else {
                        $chars = '['.$chars.']';
                    }
                    $options = new qtype_preg_handling_options();
                    $options->preserveallnodes = true;
                    StringStreamController::createRef('regex', $chars);
                    $pseudofile = fopen('string://regex', 'r');
                    $lexer = new qtype_preg_lexer($pseudofile);
                    $lexer->set_options($options);
                    $pregleaf = $lexer->nextToken()->value;
                    for($j = 0; $j < count($asserts); $j++) {
                        switch($asserts[0]) {
                            case '\\b': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_b; break;
                            case '\\B': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_b(true); break;
                            case '\\A': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_a; break;
                            case '\\z': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_z; break;
                            case '\\Z': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_z(true); break;
                            case '\\G': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_g; break;
                            case '^': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_circumflex; break;
                            case '$': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_dollar; break;
                        }
                    }
                }
                else if(count($asserts) != 0) {
                    $type = '';
                    switch($asserts[0]) {
                        case '\\b': $pregleaf = new qtype_preg_leaf_assert_esc_b; break;
                        case '\\B': $pregleaf = new qtype_preg_leaf_assert_esc_b(true); break;
                        case '\\A': $pregleaf = new qtype_preg_leaf_assert_esc_a; break;
                        case '\\z': $pregleaf = new qtype_preg_leaf_assert_esc_z; break;
                        case '\\Z': $pregleaf = new qtype_preg_leaf_assert_esc_z(true); break;
                        case '\\G': $pregleaf = new qtype_preg_leaf_assert_esc_g; break;
                        case '^': $pregleaf = new qtype_preg_leaf_assert_circumflex; break;
                        case '$': $pregleaf = new qtype_preg_leaf_assert_dollar; break;
                    }

                    for($j = 1; $j < count($asserts); $j++) {
                        switch($asserts[0]) {
                            case '\\b': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_b; break;
                            case '\\B': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_b(true); break;
                            case '\\A': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_a; break;
                            case '\\z': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_z; break;
                            case '\\Z': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_z(true); break;
                            case '\\G': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_esc_g; break;
                            case '^': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_circumflex; break;
                            case '$': $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert_dollar; break;
                        }
                    }
                }
                else {
                    $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                }
                $transition = new qtype_preg_nfa_transition($statefrom,$pregleaf, $stateto);
                $transition->subpatt_start = $subpatt_start;
                $transition->subpatt_end = $subpatt_end;
                $transition->subexpr_start = $subexpr_start;
                $transition->subexpr_end = $subexpr_end;

            }
            else {
                $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                $transition = new qtype_preg_nfa_transition($statefrom, $pregleaf, $stateto);
            }
            // Search color of current transition.
            if ($arraystrings[3] == ',') {
                // Append color in transition.
                switch($arraystrings[4]) {
                case 'violet' : $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST; break;
                case 'blue' : $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND; break;
                case 'red' : $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER; break;
                }
            }
            else {
                $transition->origin = $origin;
            }
            $transition->consumeschars = ($transition->origin != qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND);
            // Append transition in automata.
            $transition->set_transition_type();
            $this->add_transition($transition);
        }
    }

    /**
     * Numerates FA states starting from 0 and trying to go from left to right (in a wawe).
     * Useful mainly for outputting and cloning FA.
     *
     * @return array where states are values and states number - keys.
     */
    public function numerate_states() {
        $result = array();
        $idcounter = 0;
        foreach ($this->states as $state) {
            $state->number = $idcounter++;
        }
        return $result;
    }

    /**
     * Creates a dot-file for the given FA. Mainly used for debugging.
     */
    public function write_fa_to_dot($file) {
        // TODO - kolesov.
    }

    /**
     * Compares to FA and returns whether they are equal. Mainly used for unit-testing.
     *
     * @param another qtype_preg_finite_automaton object - FA to compare.
     * @return boolean true if this FA equal to $another.
     */
    public function compare_fa(&$another, &$differences) {
        // TODO - streltsov.
        return false;
        if ($P->has_end_states() != $Q->has_end_states()) {
            $P->way_to_string($Q);
            if ($P->has_end_states()) {
                $error = $error.' Only first automata has endstate.';
            }
            else {
                $error = $error.' Only second automata has endstate.';
            }
            $differences[] = $error;
            
        }
        else {
            // Append pair of groups in fifo and stack of groups
        }
            unset($fifo[count($fifo)-1]);
            $P = $fifo[count($fifo)-1];
            unset($fifo[count($fifo)-1]);
            // Convert transition.
            $firsttransitionto = array();
            $secondtransitionto = array();
            $states = $P->get_states();
            foreach ($states as $state) {
                foreach ($this->adjacencymatrix[$state] as $transit) {
                    $firsttransitionto[] = $transit->to;
                    // TODO - convert ranges.
                }
            }
            $states = $Q->get_states();
            foreach ($states as $state) {
                foreach ($another->adjacencymatrix[$state] as $transit) {
                    $firsttransitionto[] = $transit->to;
                    // TODO - convert ranges.
                }
            }
            // Creates pairs of groups.
            $allend = true;
            while($allend == false) {
                // TODO - Search next pair.
                $p = new qtype_preg_fa_group($this);
                $q = new qtype_preg_fa_group($another); 
                // Check pair.
                $ismet = false;
                /* TODO
                for ($i = 0; $i < count($stack) - 1; $i++) {
                    if ($p->cmpgroup($stack[$i]) && $q->cmpgroup($stack[$i + 1])) {
                        $ismet = true;
                    }
                }*/
                if ($ismet == true) {
                    if ($p->is_empty() != $q->is_empty()) {
                        $error = $p->way_to_string($q);
                        if ($p->is_empty()) {
                            $error .= ' Only first automata has transition.';
                        }
                        else {
                            $error .= ' Only second automata has transition.';
                        }
                        $differences[] = $error;
                        $isequiv = false;
                    }
                    else if ($p->has_end_states() != $q->has_end_states()) {
                        $error = $p->way_to_string($q);
                        if ($p->has_end_states()) {
                            $error .= ' Only first automata has endstates.';
                        }
                        else {
                            $error .= ' Only second automata has endstates.';
                        }
                        $differences[] = $error;
                        $isequiv = false;
                    }
                    if ((count($differences) == 0) && $isequiv == true) {
                        // Append pair of groups in fifo and stack of groups
                        $fifo[] = $P;
                        $fifo[] = $Q;
                        $stack[0][] = $P;
                        $stack[1][] = $Q;
                    }
                }
            }
    }

    /**
     * Decide if the intersection was successful or not.
     *
     * @param fa qtype_preg_finite_automaton object - first automata taking part in intersection.
     * @param anotherfa qtype_preg_finite_automaton object - second automata taking part in intersection.
     * @return boolean true if intersection was successful.
     */
    public function has_successful_intersection($fa, $anotherfa, $direction) {
        $issuccessful = false;
        // Analysis of result intersection.
        if ($direction == 0) {
            // Analysis if the end state of intersection includes one of end states of given automata.
            $fastates = $fa->end_states();
            $anotherfastates = $anotherfa->end_states();
            $states = $this->endstates;
        } else {
            // Analysis if the start state of intersection includes one of start states of given automata.
            $fastates = $fa->start_states();
            $anotherfastates = $anotherfa->start_states();
            $states = $this->startstates;
        }
        // Get real numbers.
        $numbers = $fa->get_state_numbers();
        $realfanumbers = array();
        $realanotherfanumbers = array();
        foreach ($fastates as $state) {
            $realfanumbers[] = $numbers[$state];
        }
        $numbers = $anotherfa->get_state_numbers();
        foreach ($anotherfastates as $state) {
            $realanotherfanumbers[] = $numbers[$state];
        }
        $result = array();
        foreach ($states as $state) {
            $result[] = $this->statenumbers[$state];
        }
        // Compare real numbers
        foreach ($realfanumbers as $num1) {
            foreach ($result as $num2) {
                $resnumbers = explode(',', $num2, 2);
                if ($num1 == $resnumbers[0]) {
                    $issuccessful = true;
                }
            }
        }

        foreach ($realanotherfanumbers as $num1) {
            foreach ($result as $num2) {
                $resnumbers = explode(',', $num2, 2);
                if (strpos($resnumbers[1], $num1) === 0) {
                    $issuccessful = true;
                }
            }
        }
        return $issuccessful;
    }

    /**
     * Merging transitions without merging states.
     *
     * @param del - uncapturing transition for deleting.
     */
    public function go_round_transitions($del) {
        $clonetransitions = array();
        $transitions = $this->get_adjacent_transitions($del->to, true);
        // Changing leafs in case of merging.
        foreach ($transitions as $transition) {
            $tran = clone($transition);
            $tran = $tran->save_tags($del);
            $newleaf = $tran->pregleaf->intersect_asserts($del->pregleaf);
            $tran->pregleaf = $newleaf;
            $clonetransitions[] = $tran;
        }
        // Has deleting or changing transitions.
        if (count($transitions) !=0) {
            foreach ($clonetransitions as &$tran) {
                $tran->from = $del->from;
                $this->add_transition($tran);
            }
            $this->del_transition($del);
        }
    }

    /**
     * Merging states connected by uncapturing transition.
     *
     * @param del - uncapturing transition for deleting.
     */
    public function merge_states($del) {
        // Getting real numbers of new merged state.
        $numbers = array();
        // Merging intersection states.
        if ($this->is_intersectionstate($del->from)) {
            $fromnumbers = explode(',', $this->statenumbers[$del->from], 2);
            $tonumbers = explode(',', $this->statenumbers[$del->to], 2);
            for ($i = 0; $i < 2; $i++) {
                $numbers[] = $fromnumbers[$i] . '   ' . $tonumbers[$i];
            }
            $number = $numbers[0] . ',' . $numbers[1];
        } else {
            // Merging simple state.
            $number = $this->statenumbers[$del->from] . '   ' . $this->statenumbers[$del->to];
        }

        $this->statenumbers[$del->from] = $number;
    }

    /**
     * Merging transitions with merging states.
     *
     * @param del - uncapturing transition for deleting.
     */
    public function merge_transitions($del) {
        // Cycle with empty transition
        if ($del->to == $del->from && $del->is_eps()) {
            $this->del_transition($del);
        }

        // Transition for merging isn't cycle.
        if ($del->to != $del->from) {
            $needredacting = false;
            $transitions = $this->get_adjacent_transitions($del->to, true);
            $intotransitions = $this->get_adjacent_transitions($del->from, false);
            // Possibility of merging with outtransitions.
            if (count($transitions) != 0) {
                $needredacting = true;
            } else if (count($intotransitions) !=0 && $del->pregleaf->type != qtype_preg_node::TYPE_LEAF_ASSERT
                       && count($del->pregleaf->mergedassertions) == 0 && !$del->has_tags()) {
                // Possibility of merging with intotransitions.
                $transitions = $intotransitions;
            } else if ($this->statecount == 2 && $del->is_eps()) {
                // Possibility to get automata with one state.
                $this->merge_states($del);
                // Checking if start state was merged.
                if ($this->has_endstate($del->to)) {
                    $this->endstates[array_search($del->to, $this->endstates)] = $del->from;
                }
                $this->remove_state($del->to);
            }

            // Changing leafs in case of merging.
            foreach ($transitions as &$tran) {
                $tran = $tran->save_tags($del);
                $newleaf = $tran->pregleaf->intersect_asserts($del->pregleaf);
                $tran->pregleaf = $newleaf;
            }
            // Has deleting or changing transitions.
            if (count($transitions) !=0) {
                $this->merge_states($del);
                // Adding intotransitions from merged state.
                $intotransitions = $this->get_adjacent_transitions($del->to, false);
                foreach ($intotransitions as &$tran) {
                    if ($tran != $del) {
                        $tran->to = $del->from;
                        $this->add_transition($tran);
                    }
                }

                // Adding outtransitions from merged state.
                if ($needredacting) {
                    foreach ($transitions as &$tran) {
                        if ($tran->to == $del->from) {
                            $tran->to = $del->from;
                        }
                        $tran->from = $del->from;
                        $this->add_transition($tran);
                    }
                }
                // Checking if start state was merged.
                if ($this->has_endstate($del->to)) {
                    $this->endstates[array_search($del->to, $this->endstates)] = $del->from;
                }
                $this->remove_state($del->to);
            }
        }
    }

    /**
     * Merging all possible uncaptaring transitions in automata.
     *
     * @param transitiontype - type of uncapturing transitions for deleting(eps or simple assertions).
     * @param stateindex integer index of state of $this automaton with which to start intersection if it is nessessary.
     */
    public function merge_uncapturing_transitions($transitiontype, &$stateindex) {
        $newfront = array();
        $stateschecked = array();
        // Getting types of uncaptyring transitions.
        if ($transitiontype == qtype_preg_fa_transition::TYPE_TRANSITION_BOTH) {
            $trantype1 = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;
            $trantype2 = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        } else {
            $trantype1 = $transitiontype;
            $trantype2 = $transitiontype;
        }
        $oldfront = $this->startstates;
        while (count($oldfront) != 0) {
            $waschanged = false;
            // Analysis transitions of each state.
            foreach ($oldfront as $state) {
                if (!$waschanged && array_search($state, $stateschecked) === false) {
                    $transitions = $this->get_adjacent_transitions($state, true);
                    // Searching transition of given type.
                    foreach ($transitions as &$tran) {
                        $tran->set_transition_type();
                        if ($tran->type == $trantype1 || $tran->type == $trantype2) {
                            // Choice of merging way.
                            $intotransitions = $this->get_adjacent_transitions($tran->to, false);
                            if ($stateindex !== null && $tran->from == $stateindex && count($intotransitions) > 1) {
                                $this->go_round_transitions($tran);
                                $waschanged = true;
                            } else {
                                if ($tran->to == $stateindex) {
                                    $stateindex = $tran->from;
                                }
                                $this->merge_transitions($tran);
                                $waschanged = true;
                            }
                            // Adding changed state to new wavefront.
                            $newfront[] = $state;
                            $addedstate = array_search($state, $stateschecked);
                            if ($addedstate !== false) {
                                unset($stateschecked[$addedstate]);
                            }
                            $outtransitions = $this->get_adjacent_transitions($state, true);
                            // Delete cycle of uncapturing transition.
                            $wasdel = false;
                            foreach ($outtransitions as $outtran) {
                                if (!$wasdel) {
                                    if ($outtran->to == $outtran->from && $outtran->is_unmerged_assert()) {
                                        $this->del_transition($outtran);
                                        unset($newfront[count($newfront)-1]);
                                        $wasdel = true;
                                    }
                                }
                            }
                        } else {
                            $newfront[] = $tran->to;
                            if (($waschanged && array_search($state, $newfront) === false) || !$waschanged) {
                                $stateschecked[] = $state;
                            }
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }
    }

    /**
     * Get connected with given states in given direction.
     *
     * @param state - state for searching connexted.
     * @param direction - direction of searching.
     */
    public function get_connected_states($state, $direction) {
        $result = array();
        $transitions = $this->get_adjacent_transitions($state, !$direction);
        foreach ($transitions as $tran) {
            if ($direction == 0) {
                $result[] = $tran->to;
            } else {
                $result[] = $tran->from;
            }
        }
        return $result;
    }

    /**
     * Modify state for adding to automata which is intersection of two others.
     *
     * @param changedstate - state for modifying.
     * @param origin - origin of automata with this state.
     */
    public function modify_state($changedstate, $origin) {
        if ($origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST) {
            $resultstate = $changedstate . ',';
        } else {
            $resultstate = ',' . $changedstate;
        }
        return $resultstate;
    }

    /**
     * Copy transitions to workstate from automata source in given direction.
     *
     * @param stateswere - states which were in automata.
     * @param statefromsource - state from source automata which transitions are coped.
     * @param memoryfront - states added to automata in last state.
     * @param source - automata-source.
     * @param direction - direction of coping (0 - forward; 1 - back).
     */
    public function copy_transitions($stateswere, $statefromsource, $workstate, $memoryfront, $source, $direction) {
        // Get origin of source automata.
        $states = $source->get_states();
        if (count($states) != 0) {
            $keys = array_keys($states);
            $transitions = $source->get_adjacent_transitions($states[$keys[0]], true);
            $keys = array_keys($transitions);
            $origin = $transitions[$keys[0]]->origin;
        }
        // Get transition for analysis.
        if ($direction == 0) {
            $transitions = $source->get_adjacent_transitions($statefromsource, false);
        } else {
            $transitions = $source->get_adjacent_transitions($statefromsource, true);
        }
        $numbers = $source->get_state_numbers();

        // Search transition among states were.
        foreach ($stateswere as $state) {
            // Get real number of source state.
            if ($origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST) {
                $number = rtrim($state, ',');
            } else {
                $number = ltrim($state, ',');
            }
            $sourceindex = array_search($number, $numbers);
            if ($sourceindex !== false) {
                foreach ($transitions as $tran) {
                    if ($direction == 0) {
                        $sourcenum = trim($numbers[$tran->from], '()');
                    } else {
                        $sourcenum = trim($numbers[$tran->to], '()');
                    }
                    if ($sourcenum == $number) {
                        // Add transition.
                        $memstate = array_search($state, $this->statenumbers);
                        if ($direction == 0) {
                            $transition = new qtype_preg_nfa_transition($memstate, $tran->pregleaf, $workstate, $tran->origin, $tran->consumeschars);
                        } else {
                            $transition = new qtype_preg_nfa_transition($workstate, $tran->pregleaf, $memstate, $tran->origin, $tran->consumeschars);
                        }
                        $transition->set_transition_type();
                        $this->add_transition($transition);
                    }
                }
            }
        }

        // Search transition among states added on last step.
        foreach ($memoryfront as $state) {
            $number = $this->statenumbers[$state];
            $number = trim($number, ',');
            foreach ($transitions as $tran) {
                if ($direction == 0) {
                    $sourcenum = trim($numbers[$tran->from], '()');
                } else {
                    $sourcenum = trim($numbers[$tran->to], '()');
                }
                if ($sourcenum == $number) {
                    // Add transition.
                    if ($direction == 0) {
                        $transition = new qtype_preg_nfa_transition($state, $tran->pregleaf, $workstate, $tran->origin, $tran->consumeschars);
                    } else {
                        $transition = new qtype_preg_nfa_transition($workstate, $tran->pregleaf, $state, $tran->origin, $tran->consumeschars);
                    }
                    $transition->set_transition_type();
                    $this->add_transition($transition);
                }
            }
        }
    }

    /**
     * Copy and modify automata to stopcoping state or to the end of automata, if stopcoping == NULL.
     *
     * @param source - automata-source for coping.
     * @param oldfront - states from which coping starts.
     * @param stopcoping - state to which automata will be copied.
     * @param direction - direction of coping (0 - forward; 1 - back).
     * @return automata after coping.
     */
    public function copy_modify_branches(&$source, &$oldfront, $stopcoping, $direction) {
        $resultstop = null;
        $memoryfront = array();
        $newfront = array();
        $newmemoryfront = array();
        // Getting origin of automata.
        $states = $source->get_states();
        if (count($states) != 0) {
            $keys = array_keys($states);
            $transitions = $source->get_adjacent_transitions($states[$keys[0]], true);
            $keys = array_keys($transitions);
            $origin = $transitions[$keys[0]]->origin;
        }
        // Getting all states which are in automata for coping.
        $stateswere = $this->get_state_numbers();
        // Cleaning end states.
        $this->clean_end_states();

        // Coping.
        while (count ($oldfront) != 0) {
            foreach ($oldfront as $curstate) {
                if (count($stateswere) == 0) {
                            $stateswere = array();
                }
                if (!$source->is_copied_state($curstate)) {
                    // Modify states.
                    $changedstate = $source->statenumbers[$curstate];
                    $changedstate = $this->modify_state($changedstate, $origin);
                    // Mark state as copied state.
                    $source->set_copied_state($curstate);
                    $isfind = false;
                    // Search among states which were in automata.
                    if (count($stateswere) != 0) {
                        if (array_search($changedstate, $stateswere) !== false) {
                            $isfind = true;
                            $workstate = array_search($changedstate, $stateswere);
                        }
                    }
                    // Hasn't such state.
                    if (!$isfind) {
                        $this->add_state($changedstate);
                        $workstate = array_search($changedstate, $this->statenumbers);
                        $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);

                        // Check end of coping.
                        if ($stopcoping !== null && $curstate == $stopcoping) {
                            if ($direction == 0) {
                                $this->add_end_state($workstate);
                            }
                            $resultstop = $workstate;
                        } else {
                            $newmemoryfront[] = $workstate;
                            // Adding connected states.
                            $connectedstates = $source->get_connected_states($curstate, $direction);
                            $newfront = array_merge($newfront, $connectedstates);
                        }
                    } else {
                        $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);
                        $newmemoryfront[] = $workstate;
                        // Adding connected states.
                        $connectedstates = $source->get_connected_states($curstate, $direction);
                        $newfront = array_merge($newfront, $connectedstates);
                    }
                } else {
                    $changedstate = $source->statenumbers[$curstate];
                    $changedstate = trim($changedstate, '()');
                    $changedstate = $this->modify_state($changedstate, $origin);
                    $workstate = array_search($changedstate, $this->statenumbers);
                    $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);
                }
            }
            $oldfront = $newfront;
            $memoryfront = $newmemoryfront;
            $newfront = array();
            $newmemoryfront = array();
        }
        $sourcenumbers = $source->get_state_numbers();
        // Add start states if fa has no one.
        if (count($this->startstates) == 0) {
            $sourcestart = $source->start_states();
            foreach ($sourcestart as $start) {
                $realnumber = $sourcenumbers[$start];
                $realnumber = trim($realnumber, '()');
                $newstart = array_search($this->modify_state($realnumber, $origin), $this->statenumbers);
                if ($newstart !== false) {
                    $this->add_start_state($newstart);
                }
            }
        }

        $sourceend = $source->end_states();
        foreach ($sourceend as $end) {
            $realnumber = $sourcenumbers[$end];
            $realnumber = trim($realnumber, '()');
            $newend = array_search($this->modify_state($realnumber, $origin), $this->statenumbers);
            if ($newend !== false) {
                // Get last copied state.
                if ($resultstop === null) {
                    $resultstop = $newend;
                }
                $this->add_end_state($newend);
            }
        }
        // Remove flag of coping from states of source automata.
        $source->remove_flags_of_coping();
        return $resultstop;
    }

    /**
     * Merges simple assertion transitions into other transtions.
     */
    public function merge_simple_assertions() {
        if (!$this->hasassertiontransitions) {    // Nothing to merge.
            return;
        }
        // TODO - merge.
        $this->hasassertiontransitions = false;
    }

    /**
     * Deletes epsilon-transitions from the automaton.
     */
    public function aviod_eps() {
        if (!$this->haseps) {    // Nothing to delete.
            return;
        }
        // TODO - delete eps.
        $this->haseps = false;
    }

    /**
     * Check if there is such state in intersection part and add modified version of it.
     *
     * @param anotherfa - second automata, which toke part in intersection.
     * @param transition - transition for checking.
     * @param laststate - last added state.
     * @param realnumber - real number of serching state.
     * @param direction - direction of checking (0 - forward; 1 - back).
     * @return flag if it was possible to add another version of state.
     */
    public function has_same_state($anotherfa, &$transition, $laststate, &$clones, &$realnumber, $direction) {
        $oldfront = array();
        $isfind = false;
        $hasintersection = false;
        $aregone = array();
        $newfront = array();
        // Get right clones in case of divarication.
        $clones = array();
        $clones[] = $transition;
        $numbers = explode(',', $realnumber, 2);
        $numbertofind = $numbers[0];
        $addnum = $numbers[1];
        $oldfront[] = $laststate;
        $secnumbers = $anotherfa->get_state_numbers();

        // While there are states for analysis.
        while (count($oldfront) != 0 && !$isfind) {
            foreach ($oldfront as $state) {
                $aregone[] = $state;
                $numbers = explode(',', $this->statenumbers[$state], 2);
                // State with same number is found.
                if ($numbers[0] == $numbertofind && $numbers[1] !== '') {
                    // State with same number was found and there is one more.
                    if ($isfind) {
                        $clones[] = $clones[count($clones) - 1];
                        // Get added numbers
                        $tran = &$clones[count($clones) - 2];
                    } else {
                        // State wasn't found earlier but this state is a searched state.
                        $isfind = true;
                        $tran = &$transition;
                    }
                    if ($direction == 0) {
                        $clone = $tran->to;
                    } else {
                        $clone = $tran->from;
                    }
                    $addnumber = $numbertofind . ',' . $addnum . '   ' . $numbers[1];
                    foreach ($secnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0) {
                            $statefromsecond = array_search($num, $secnumbers);
                        }
                    }

                    $transitions = $anotherfa->get_adjacent_transitions($statefromsecond, $direction);
                    $transitions = array_values($transitions);

                    // There are transitions for analysis.
                    if (count($transitions) != 0) {
                        $intertran = $tran->intersect($transitions[0]);
                        if ($intertran !== null) {
                            $hasintersection = true;
                            // Form new transition.
                            $addstate = $this->add_state($addnumber);
                            $realnumber = $addnumber;
                            if ($direction == 0) {
                                $tran->to = $addstate;
                            } else {
                                $tran->from = $addstate;
                            }
                        }
                    } else {
                        // Form new transition.
                        $hasintersection = true;
                        $addstate = $this->add_state($addnumber);
                        $realnumber = $addnumber;
                        if ($direction == 0) {
                            $tran->to = $addstate;
                        } else {
                            $tran->from = $addstate;
                        }
                    }
                } else {
                    // Add connected states to new wave front.
                    if ($direction == 0) {
                        $conectstates = $this->get_connected_states($state, 1);
                    } else {
                        $conectstates = $this->get_connected_states($state, 0);
                    }
                    foreach ($conectstates as $conectstate) {
                        if (array_search($conectstate, $newfront) === false && array_search($conectstate, $aregone) === false) {
                            $newfront[] = $conectstate;
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }
        if (!$isfind) {
            $hasintersection = true;
        }
        return $hasintersection;
    }

    /**
     * Get transitions from automata for intersection.
     *
     * @param workstate state for getting transitions.
     * @param direction direction of intersection.
     * @return array of transitions for intersection.
     */
    public function get_transitions_for_intersection($workstate, $direction) {
        $transitions = $this->get_adjacent_transitions($workstate, !$direction);
        return $transitions;
    }

    /**
     * Changes automaton to not contain wordbreak  simple assertions (\b and \B).
     */
    public function avoid_wordbreaks() {
        // TODO - delete \b and \B.
    }

    /**
     * Generate real number of state from intersection part.
     *
     * @param firststate real number of state from first automata.
     * @param secondstate real number of state from second automata.
     * @return real number of state from intersection part.
     */
    public function get_inter_state($firststate, $secondstate) {
        $first = trim($firststate, '(,)');
        $second = trim($secondstate, '()');
        $state = $first . ',' . $second;
        return $state;
    }

    /**
     * Find state which should be added in way of passing cycle.
     *
     * @param anotherfa object automaton to find.
     * @param resulttransitions array of intersected transitions.
     * @param curstate last added state.
     * @param clones transitions appeared in case of several ways.
     * @param realnumber real number of $curstate.
     * @param index index of transition in $resulttransitions for analysis.
     * @return boolean flag if automata has state which should be added in way of passing cycle.
     */
    public function have_add_state_in_cycle($anotherfa, &$resulttransitions, $curstate, &$clones, &$realnumber, $index, $direction) {
        $resnumbers = $this->get_state_numbers();
        $hasalready = false;
        $wasdel = false;
        // No transitions from last state.
        if (count($clones) <= 1) {
            $ispossible = $this->has_same_state($anotherfa, $resulttransitions[$index], $curstate, $clones, $realnumber, $direction);
            // It's possible to add state in case of having state.
            if ($ispossible) {
                // Search same state in result automata.
                $searchnumbers = explode(',', $realnumber, 2);
                $searchnumber = $searchnumbers[0];
                foreach ($resnumbers as $resnum) {
                    $pos = strpos($resnum, $searchnumber);
                    if ($pos !== false && $pos < strpos($resnum, ',') && $searchnumbers[1] == '') {
                        $hasalready = true;
                    }
                }
            } else {
                // It's impossible to add state.
                unset($resulttransitions[$index]);
                $wasdel = true;
            }
        } else {
            // Has transitions from previous states.
            if (array_search($realnumber, $resnumbers) !== false) {
                $hasalredy = true;
            }
            unset($clones[count($clones) - 2]);
        }
        if ($hasalready || $wasdel) {
            return true;
        } else {
            // Coping transition copies.
            if (count($clones) > 1) {
                for ($i = count($clones) - 2; $i >= 0; $i--) {
                    // TODO - add after index in array.
                    $resulttransitions[] = $clones[$i];
                }
            }
            return false;
        }
    }

    /**
     * Find cycle in the automata.
     *
     * @return flag if automata has cycle or not.
     */
    public function has_cycle() {
        $newfront = array();
        $aregone = array();
        $hascycle = false;
        $states = $this->get_state_numbers();
        // Add start states to wave front.
        $oldfront = $this->start_states();

        // Analysis sattes from wave front.
        while (count($oldfront) != 0) {
            foreach ($oldfront as $curstate) {
                // State hasn't been  already gone.
                if (array_search($curstate, $aregone) === false) {
                    // Mark as gone.
                    $aregone[] = $curstate;
                    // Get connected states if they are.
                    $connectedstates = $this->get_connected_states($curstate, 0);
                    $newfront = array_merge($newfront, $connectedstates);
                } else {
                    // Analysis intotransitions.
                    $transitions = $this->get_adjacent_transitions($curstate, false);
                    foreach ($transitions as $tran) {
                        // Transition has come from state which is far in automata.
                        if ($states[$tran->from] > $states[$curstate]) {
                            $hascycle = true;
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }
        return $hascycle;
    }

    /**
     * Set right start and end states after before completing branches.
     *
     * @param fa object automaton taken part in intersection.
     * @param anotherfa object automaton second automaton taken part in intersection.
     */
    public function set_start_end_states_before_coping($fa, $anotherfa) {
        // Get nessesary data.
        $faends = $fa->end_states();
        $anotherfaends = $anotherfa->end_states();
        $fastarts = $fa->start_states();
        $anotherfastarts = $anotherfa->start_states();
        $fastates = $fa->get_state_numbers();
        $anotherfastates = $anotherfa->get_state_numbers();
        $states = $this->get_state_numbers();
        // Set right start and end states.
        foreach ($states as $statenum) {
            // Get states from first and second automata.
            $numbers = explode(',', $statenum, 2);
            if ($numbers[0] !== '') {
                $workstate1 = array_search($numbers[0], $fastates);
            }
            if ($numbers[1] != '') {
                foreach ($anotherfastates as $num) {
                    if (strpos($numbers[1], $num) === 0) {
                        $workstate2 = array_search($num, $anotherfastates);
                    }
                }
            }
            $state = array_search($statenum, $this->statenumbers);
            // Set start states.
            $isfirststart = $numbers[0] !== '' && array_search($workstate1, $fastarts) !== false;
            $issecstart = $numbers[1] !== '' && array_search($workstate2, $anotherfastarts) !== false;
            if (($isfirststart || $issecstart) && count($this->get_adjacent_transitions($state, false)) == 0) {
                $this->add_start_state(array_search($statenum, $this->statenumbers));
            }
            // Set end states.
            $isfirstend = $numbers[0] !== '' && array_search($workstate1, $faends) !== false;
            $issecend = $numbers[1] !== '' && array_search($workstate2, $anotherfaends) !== false;
            if (($isfirstend || $issecend) && count($this->get_adjacent_transitions($state, true)) == 0) {
                $this->add_end_state(array_search($statenum, $this->statenumbers));
            }
        }
    }

    /**
     * Set right start and end states after inetrsection two automata.
     *
     * @param fa object automaton taken part in intersection.
     * @param anotherfa object automaton second automaton taken part in intersection.
     */
    public function set_start_end_states_after_intersect($fa, $anotherfa) {
        // Get nessesary data.
        $faends = $fa->end_states();
        $anotherfaends = $anotherfa->end_states();
        $fastarts = $fa->start_states();
        $anotherfastarts = $anotherfa->start_states();
        $fastates = $fa->get_state_numbers();
        $anotherfastates = $anotherfa->get_state_numbers();
        $states = $this->get_state_numbers();
        // Set right start and end states.
        foreach ($states as $statenum) {
            // Get states from first and second automata.
            $numbers = explode(',', $statenum, 2);
            if ($numbers[0] != '') {
                $workstate1 = array_search($numbers[0], $fastates);
            }

            if ($numbers[1] != '') {
                foreach ($anotherfastates as $num) {
                    if (strpos($numbers[1], $num) === 0) {
                        $workstate2 = array_search($num, $anotherfastates);
                    }
                }
            }
            // Set start states.
            $isfirststart = ($numbers[0] !== '' && array_search($workstate1, $fastarts) !== false) || $numbers[0] == '';
            $issecstart = ($numbers[1] !== '' && array_search($workstate2, $anotherfastarts) !== false) || $numbers[1] == '';
            if ($isfirststart && $issecstart) {
                $this->add_start_state(array_search($statenum, $this->statenumbers));
            }
            // Set end states.
            $isfirstend = ($numbers[0] !== '' && array_search($workstate1, $faends) !== false) || $numbers[0] == '';
            $issecend = ($numbers[1] !== '' && array_search($workstate2, $anotherfaends) !== false) || $numbers[1] == '';
            if ($isfirstend && $issecend) {
                $this->add_end_state(array_search($statenum, $this->statenumbers));
            }
        }
    }

    /**
     * Return count of states from second automata which includes state from intersection.
     *
     * @param anotherfa object automaton second automaton taken part in intersection.
     * @param state id of state from intersection for counting.
     */
    public function get_second_numbers_count($anotherfa, $state) {
        $count = 0;
        $numbers = $this->get_state_numbers();
        $anotherfanumbers = $anotherfa->get_state_numbers();
        $realnum = $numbers[$state];
        $realsecond = explode(',', $realnum, 2)[1];
        foreach ($anotherfanumbers as $curnum) {
            if (strpos($realsecond, $curnum) !== false) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Find intersection part of automaton in case of intersection it with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param result object automaton to write intersection part.
     * @param start state of $this automaton with which to start intersection.
     * @param direction boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @param withcycle boolean intersect in case of forming right cycle.
     * @return result automata.
     */
    public function get_intersection_part ($anotherfa, &$result, $start, $direction, $withcycle) {
        $oldfront = array();
        $newfront = array();
        $clones = array();
        $oldfront[] = $start;
        // Work with each state.
        while (count($oldfront) != 0) {
            foreach ($oldfront as $curstate) {
                // Get states from first and second automata.
                $secondnumbers = $anotherfa->get_state_numbers();
                $resnumbers = $result->get_state_numbers();
                $resultnumber = $resnumbers[$curstate];
                $numbers = explode(',', $resultnumber, 2);
                $workstate1 = array_search($numbers[0], $this->statenumbers);
                foreach ($secondnumbers as $num) {
                    if (strpos($numbers[1], $num) === 0) {
                        $workstate2 = array_search($num, $secondnumbers);
                    }
                }
                // Get transitions for ntersection.
                $intertransitions1 = $this->get_transitions_for_intersection($workstate1, $direction);
                $intertransitions2 = $anotherfa->get_transitions_for_intersection($workstate2, $direction);
                // Intersect all possible transitions.
                $resulttransitions = array();
                $resultnumbers = array();
                foreach ($intertransitions1 as $intertran1) {
                    foreach ($intertransitions2 as $intertran2) {
                        $resulttran = $intertran1->intersect($intertran2);
                        if ($resulttran !== null) {
                            $resulttransitions[] = $resulttran;
                            if ($direction == 0) {
                                $resultnumbers[] = $result->get_inter_state($this->statenumbers[$intertran1->to], $secondnumbers[$intertran2->to]);
                            } else {
                                $resultnumbers[] = $result->get_inter_state($this->statenumbers[$intertran1->from], $secondnumbers[$intertran2->from]);
                            }
                        }
                    }
                }
                // Analysis result transitions.
                for ($i = 0; $i < count($resulttransitions); $i++) {
                    // Search state with the same number in result automata.
                    if ($withcycle) {
                        $searchstate = $result->have_add_state_in_cycle($anotherfa, $resulttransitions, $curstate, $clones, $resultnumbers[$i], $i, $direction);
                    } else {
                        $searchstate = array_search($resultnumbers[$i], $resnumbers);
                    }
                    // State was found.
                    if ($searchstate !== false) {
                        $resnumbers = $result->get_state_numbers();
                        $newstate = array_search($resultnumbers[$i], $resnumbers);
                    } else {
                        // State wasn't found.
                        $newstate = $result->add_state($resultnumbers[$i]);
                        $newfront[] = $newstate;
                    }
                    $resnumbers = $result->get_state_numbers();
                    // Change transitions.
                    if ($direction == 0) {
                        $resulttransitions[$i]->from = $curstate;
                        $resulttransitions[$i]->to = $newstate;
                    } else {
                        $resulttransitions[$i]->from = $newstate;
                        $resulttransitions[$i]->to = $curstate;
                    }
                    $result->add_transition($resulttransitions[$i]);
                }
                // Removing arrays.
                $intertransitions1 = array();
                $intertransitions2 = array();
                $resulttransitions = array();
                $resultnumbers = array();
            }
            $possibleend = $oldfront;
            $oldfront = $newfront;
            $newfront = array();
        }
        // Set right start and end states.
        if ($direction == 0) {
            // Cleaning end states.
            $result->clean_end_states();
            foreach ($possibleend as $end) {
                $result->add_end_state($end);
            }
        } else {
            // Cleaning start states.
            $startstates = $result->start_states();
            foreach ($startstates as $startstate) {
                if ($result->is_full_intersect_state($startstate)) {
                    $result->remove_start_state($startstate);
                }
            }
            // Add new start states.
            $state = $result->get_inter_state(0, 0);
            $state = array_search($state, $resnumbers);
            if ($state !== false) {
                $result->add_start_state($state);
            } else {
                foreach ($possibleend as $start) {
                    $result->add_start_state($start);
                }
            }
        }
        // Get cycle if it's nessessary.
        $newfront = array();
        $resultnumbers = $result->get_state_numbers();
        if ($withcycle == true) {
            foreach ($possibleend as $state) {
                $aregone = array();
                $isfind = false;
                $divfind = false;
                $searchnumbers = explode(',', $resultnumbers[$state], 2);
                $numbertofind = $searchnumbers[0];
                $oldfront = $result->get_connected_states($state, !$direction);
                $secondnumberscount = $result->get_second_numbers_count($anotherfa, $state);
                // Analysis states of automata serching interecsting state.
                while (count($oldfront) != 0 && !$isfind) {
                    foreach ($oldfront as $curstate) {
                        $aregone[] = $curstate;
                        $curnumberscount = $result->get_second_numbers_count($anotherfa, $curstate);
                        if (!$divfind && $secondnumberscount != $curnumberscount) {
                            $divfind = true;
                            $divstate = $curstate;
                        }
                        $numbers = explode(',', $resultnumbers[$curstate], 2);
                        // State with same number is found.
                        if ($numbers[0] == $numbertofind && $numbers[1] !== '' && strpos($searchnumbers[1], $numbers[1]) !== false) {
                            if ($direction == 0) {
                                $transitions = $result->get_adjacent_transitions($curstate, true);
                                foreach ($transitions as $tran) {
                                    $clonetran = clone($tran);
                                    $clonetran->from = $state;
                                    $result->add_transition($clonetran);
                                }
                            } else {
                                $realdiv = explode(',', $resultnumbers[$divstate], 2);
                                if ($realdiv[0] == $numbertofind) {
                                    $newpregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                                    $addtran = new qtype_preg_nfa_transition ($divstate, $newpregleaf, $state, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
                                    $result->add_transition($addtran);
                                } else {
                                    $lastcopied = false;
                                    $frontstate = $curstate;
                                    $clonestate = null;
                                    // Coping states to the state which is last in cycle.
                                    while (!$lastcopied) {
                                        $transitions = $result->get_adjacent_transitions($frontstate, false);
                                        // Analasis transitions.
                                        foreach ($transitions as $tran) {
                                            // Check should we copy this state or not.
                                            if ($tran->from == $divstate) {
                                                // No nessesary of coping.
                                                $fromtran = clone($tran);
                                                $fromtran->to = $clonestate;
                                                $result->add_transition($fromtran);
                                                $lastcopied = true;
                                            } else {
                                                // We should copy.
                                                $newnumber = $resultnumbers[$tran->from];
                                                $newnumber = '(' . $newnumber . ')';
                                                $fromtran = clone($tran);
                                                if ($clonestate === null) {
                                                    $fromtran->to = $state;
                                                } else {
                                                    $fromtran->to = $clonestate;
                                                }
                                                $clonestate = $result->add_state($newnumber);
                                                $fromtran->from = $clonestate;
                                                $result->add_transition($fromtran);
                                                $frontstate = $tran->from;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            // Add connected states to new wave front.
                            if ($direction == 0) {
                                $conectstates = $result->get_connected_states($curstate, 1);
                            } else {
                                $conectstates = $result->get_connected_states($curstate, 0);
                            }
                            foreach ($conectstates as $conectstate) {
                                if (array_search($conectstate, $newfront) === false && array_search($conectstate, $aregone) === false) {
                                    $newfront[] = $conectstate;
                                }
                            }
                        }
                    }
                    $oldfront = $newfront;
                    $newfront = array();
                }
            }
        }
        return $result;
    }

    /**
     * Lead all end states to one with epsilon-transitions.
     */
    public function lead_to_one_end() {
        $newleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $i = count($this->endstates) - 1;
        if ($i > 0) {
            $to = $this->endstates[0];
        }
        // Connect end states with first while automata has only one end state.
        while ($i > 0) {
            $exendstate = $this->endstates[$i];
            $transitions = $this->get_adjacent_transitions($exendstate, false);
            $epstran = new qtype_preg_nfa_transition ($exendstate, $newleaf, $to, current($transitions)->origin, current($transitions)->consumeschars);
            $this->add_transition($epstran);
            $i--;
            $this->remove_end_state($exendstate);
        }
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex string with real number of state of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @return result automata.
     */
    public function intersect ($anotherfa, $stateindex, $isstart) {
        // Check right direction.
        if ($isstart != 0 && $isstart !=1) {
            throw new qtype_preg_exception('intersect error: Wrong direction');
        }
        $number = array_search($stateindex, $this->statenumbers);
        if ($number === false) {
            throw new qtype_preg_exception('intersect error: No state with number' . $stateindex . '.');
        }
        // Prepare automata for intersection.
        $this->del_blind_states();
        $this->merge_uncapturing_transitions(qtype_preg_fa_transition::TYPE_TRANSITION_BOTH, $number);
        if ($isstart == 0) {
            $number2 = $anotherfa->start_states();
        } else {
            $number2 = $anotherfa->end_states();
        }
        $secnumber = $number2[0];
        $anotherfa->del_blind_states();
        $anotherfa->merge_uncapturing_transitions(qtype_preg_fa_transition::TYPE_TRANSITION_BOTH, $secnumber);
        $result = $this->intersect_fa($anotherfa, $number, $isstart);
        $result->del_blind_states();
        $result->lead_to_one_end();
        return $result;
    }

    /**
     * Complete branches ends with state, one number of which isn't start or end state depending on direction.
     *
     * @param fa object automaton to check start/end states.
     * @param anotherfa object automaton check start/end states.
     * @param durection direction of coping.
     */
    public function complete_non_intersection_branches($fa, $anotherfa, $direction) {
        $front = array();
        $secondnumbers = $anotherfa->get_state_numbers();
        $firstnumbers = $fa->get_state_numbers();
        // Find uncompleted branches.
        if ($direction == 0) {
            $states = $this->endstates;
            foreach ($states as $state) {
                if ($this->is_full_intersect_state($state)) {
                    $front[] = $state;
                }
            }
            foreach ($front as $state) {
                $isend = false;
                // Get states from first and second automata.
                $numbers = explode(',', $this->statenumbers[$state], 2);
                $workstate1 = array_search($numbers[0], $firstnumbers);
                if ($numbers[1] != '') {
                    foreach ($secondnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0) {
                            $workstate2 = array_search($num, $secondnumbers);
                        }
                    }
                }
                if ($fa->has_endstate($workstate1)) {
                    $isend = true;
                }
                if (!$isend) {
                    $transitions = $fa->get_adjacent_transitions($workstate1, true);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->to;
                    }
                    $this->copy_modify_branches($fa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $firstnumbers[$tran->to];
                        $number = trim($number, '()');
                        $number = $number . ',';
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        $addtran = new qtype_preg_fa_transition($state, $tran->pregleaf, $copiedstate, $tran->origin, $tran->consumeschars);
                        $this->add_transition($addtran);
                    }
                }
                $isend = false;
                if ($anotherfa->has_endstate($workstate2)) {
                    $isend = true;
                }
                if (!$isend) {
                    $transitions = $anotherfa->get_adjacent_transitions($workstate2, true);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->to;
                    }
                    $this->copy_modify_branches($anotherfa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $secondnumbers[$tran->to];
                        $number = trim($number, '()');
                        $number = ',' . $number;
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        $addtran = new qtype_preg_fa_transition($state, $tran->pregleaf, $copiedstate, $tran->origin, $tran->consumeschars);
                        $this->add_transition($addtran);
                    }
                }
            }
        } else {
            $states = $this->startstates;
            foreach ($states as $state) {
                if ($this->is_full_intersect_state($state)) {
                    $front[] = $state;
                }
            }
            foreach ($front as $state) {
                $isstart = false;
                // Get states from first and second automata.
                $numbers = explode(',', $this->statenumbers[$state], 2);
                $workstate1 = array_search($numbers[0], $firstnumbers);
                if ($numbers[1] != '') {
                    foreach ($secondnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0) {
                            $workstate2 = array_search($num, $secondnumbers);
                        }
                    }
                }
                if ($fa->has_startstate($workstate1)) {
                    $isstart = true;
                }
                if (!$isstart) {
                    $transitions = $fa->get_adjacent_transitions($workstate1, false);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->from;
                    }
                    $this->copy_modify_branches($fa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $firstnumbers[$tran->from];
                        $number = trim($number, '()');
                        $number = $number . ',';
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        $addtran = new qtype_preg_nfa_transition($copiedstate, $tran->pregleaf, $state);
                        $this->add_transition($addtran);
                    }
                }
                $isstart = false;
                if ($anotherfa->has_startstate($workstate2)) {
                    $isstart = true;
                }
                if (!$isstart) {
                    $transitions = $anotherfa->get_adjacent_transitions($workstate2, false);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->from;
                    }
                    $this->copy_modify_branches($anotherfa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $secondnumbers[$tran->from];
                        $number = trim($number, '()');
                        $number = ',' . $number;
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        $addtran = new qtype_preg_nfa_transition($copiedstate, $tran->pregleaf, $state, $tran->origin, $tran->consumeschars);
                        $this->add_transition($addtran);
                    }
                }
            }
        }
    }

    /**
     * Remove flags that state was copied from all states of the automaton.
     */
    public function remove_flags_of_coping() {
        // Remove flag of coping from states of automata.
        $states = $this->get_states();
        $numbers = $this->get_state_numbers();
        foreach ($states as $statenum) {
            $backnumber = trim($numbers[$statenum], '()');
            $this->change_real_number($statenum, $backnumber);
        }
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex integer index of state of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @return result automata without blind states with one end state and with merged asserts.
     */
    public function intersect_fa($anotherfa, $stateindex, $isstart) {
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $stopcoping = $stateindex;
        // Get states for starting coping.
        if ($isstart == 0) {
            $oldfront = $this->start_states();
        } else {
            $oldfront = $this->end_states();
        }
        // Copy branches.
        $stop = $result->copy_modify_branches($this, $oldfront, $stopcoping, $isstart);
        // Change state first from intersection.
        $numbers = $this->get_state_numbers();
        $secondnumbers = $anotherfa->get_state_numbers();
        if ($isstart == 0) {
            $states = $anotherfa->start_states();
        } else {
            $states = $anotherfa->end_states();
        }
        $secforinter = $secondnumbers[$states[0]];
        $resnumbers = $result->get_state_numbers();
        $state = $result->get_inter_state($resnumbers[$stop], $secforinter);
        $result->change_real_number($stop, $state);
        // Find intersection part.
        if (!$anotherfa->has_cycle() && $this->has_cycle()) {
            $this->get_intersection_part($anotherfa, $result, $stop, $isstart, true);
        } else {
            $this->get_intersection_part($anotherfa, $result, $stop, $isstart, false);
        }
        // Set right start and end states for completing branches.
        $result->set_start_end_states_before_coping($this, $anotherfa);
        if ($result->has_successful_intersection($this, $anotherfa, $isstart)) {
            // Cleaning end states.
            $result->clean_end_states();
            // Cleaning start states.
            $result->clean_start_states();
            // Set right start and end states for completing branches.
            $result->set_start_end_states_before_coping($this, $anotherfa);
            $result->complete_non_intersection_branches($this, $anotherfa, $isstart);
            // Cleaning end states.
            $result->clean_end_states();
            // Cleaning start states.
            $result->clean_start_states();
            $result->set_start_end_states_after_intersect($this, $anotherfa);
        } else {
            $result = $result->remove_fa();
        }
        return $result;
    }

    /**
     * Return set substraction: $this - $anotherfa. Used to get negation.
     */
    abstract public function substract_fa($anotherfa);// TODO - functions that could be implemented only for DFA should be moved to DFA class.

    /**
     * Return inversion of fa.
     */
    abstract public function invert_fa();

    abstract public function match($str, $pos);
    abstract public function next_character();// TODO - define arguments.

    /**
     * Finds shortest possible string, completing partial given match.
     */
    abstract public function complete_match();// TODO - define arguments.

    public function __clone() {
        // TODO - clone automaton.
    }

    /**
     * Generates dot code for drawing FA.
     * @param type image type.
     * @param filename - name of the resulting image file.
     * @deprecated since 2.5
     */
    public function draw($type, $filename) {
        $result = 'digraph {rankdir = LR;';
        foreach ($this->states as $curstate) {
            $index1 = $curstate->number;

            if (count($curstate->outgoing_transitions()) == 0) {
                // Draw a single state.
                $result .= $index1 . ';';
            } else {
                // Draw a state with transitions.
                foreach ($curstate->outgoing_transitions() as $curtransition) {
                    $result .= $curtransition->get_label_for_dot();
                }
            }
        }
        // Make start and end states more fancy.
        $result .= $this->start_state()->number . '[shape=rarrow];';
        $result .= $this->end_state()->number . '[shape=doublecircle];';
        $result .= '};';
        qtype_preg_regex_handler::execute_dot($result, $type, $filename);
    }


    /**
     * Reads fa from a special code and modifies current object.
     * code format: i->abc->j;k->charset->l; e.t.c.
     * maximum count of subexpressions when reading fa is 9 in current implementation.
     * @param facode string with the code of the finite automaton.
     */
    public function input_fa($facode) {
        $this->read_code_member($facode);
        $this->set_start_state($this->states[0]);
        $this->set_end_state($this->states[$this->statecount - 1]);
    }

    /**
     * Reads one code member.
     * @param facode string with the code of the finite automaton.
     * @param start index of the first character of current member in facode.
     */
    protected function read_code_member($facode, $start = 0) {
        if ($start >= strlen($facode)) {
            return;
        }
        $end = $start;
        $tmpstr = '';
        while ($facode[$end] != '-') {
            $tmpstr .= $facode[$end];
            $end++;
        }
        $end += 2;
        $fir = (int)$tmpstr;
        $tmpstr = '';
        $transition = self::read_transition($facode, $end);
        $end++;
        while ($facode[$end - 2] != '-' || $facode[$end - 1] != '>') {
            $end++;
        }
        while ($facode[$end] != ';') {
            $tmpstr .= $facode[$end];
            $end++;
        }
        $lst = (int)$tmpstr;
        if (!isset($this->states[$fir])) {
            $this->states[$fir] = new qtype_preg_fa_state();
            $this->states[$fir]->set_fa($this);
            $this->statecount++;
            if ($this->statecount > $this->statelimit) {
                throw new qtype_preg_toolargefa_exception('');
            }
        }
        if (!isset($this->states[$lst])) {
            $this->states[$lst] = new qtype_preg_fa_state();
            $this->states[$lst]->set_fa($this);
            $this->statecount++;
            if ($this->statecount > $this->statelimit) {
                throw new qtype_preg_toolargefa_exception('');
            }
        }
        $transition->to = $this->states[$lst];
        $end++;
        $this->states[$fir]->add_transition($transition);
        $this->read_code_member($facode, $end);
    }

    /**
     * Read one leaf of regex from the code of finite automaton.
     * @param facode string with the code of finite automaton
     * @param start index of first character of current leaf in facode.
     */
    static protected function read_transition($facode, $start) {
        $i = $start;
        $subexprstarts = array();
        $subexprends = array();
        $charset = '';
        $error = false;
        // Input subexpressions.
        if ($facode[$start] == '#') {
            $i = $start + 1;
            do {
                if ($i >= strlen($facode)) {
                    $error = true;
                    echo "<BR><BR><BR>Incorrect fa code!<BR><BR><BR>";
                    // TODO: correct error message.
                } else if ($facode[$i] == 's') {
                    $subexprstarts[] = (int)$facode[$i + 1];
                } else if ($facode[$i] == 'e') {
                    $subexprends[] = (int)$facode[$i + 1];
                } else {
                    $error = true;
                    echo "<BR><BR><BR>Incorrect fa code!<BR><BR><BR>";
                    // TODO: correct error message.
                }
                $i += 2;
            } while (!$error && $i < strlen($facode) && $facode[$i] != '#');
            $i++;
        }
        if ($error || $i >= strlen($facode)) {
            return;
        }
        // Input transition leaf.
        while ($facode[$i] != '-' || $facode[$i + 1] != '>') {
            if ($facode[$i] == '\\') {
                $charset .= $facode[$i + 1];
                $i += 2;
            } else {
                $charset .= $facode[$i];
                $i++;
            }
        }
        $leaf = new qtype_preg_leaf_charset();
        $leaf->charset = $charset;
        // TODO: input for dfa.
        $trash =  new qtype_preg_fa_state();
        $transition = new qtype_preg_nfa_transition($trash, $leaf, $trash);
        $transition->tags = array();
        foreach ($subexprstarts as $val) {
            $transition->tags[] = $val * 2;
        }
        foreach ($subexprends as $val) {
            $transition->tags[] = $val * 2 + 1;
        }
        return $transition;
    }
}
