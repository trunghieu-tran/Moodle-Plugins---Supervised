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

/**
 * Represents finite automaton transitions (without subpatterns information).
 *
 * As NFA and DFA have different ways to store subpatterns information, they both should inherit this class to add necessary fields.
 */
class qtype_preg_fa_transition {

    /** @var object of qtype_preg_fa_state class - a state which transition starts from. */
    public $from;
    /** @var object of qtype_preg_leaf class - condition for this transition. */
    public $pregleaf;
    /** @var object of qtype_preg_fa_state class - state which transition leads to. */
    public $to;
    /** @var boolean - true if a transition consumes characters. A non-assertion automaton could have such transitions only at start and at end of the automaton. */
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

    public function __construct(&$from, &$pregleaf, &$to, $consumeschars = true) {
        $this->from = $from;
        $this->pregleaf = clone $pregleaf;
        $this->to = $to;
        $this->consumeschars = $consumeschars;
        $this->subpatt_start = array();
        $this->subpatt_end = array();
        $this->subexpr_start = array();
        $this->subexpr_end = array();
    }

    public function get_label_for_dot() {
        $lab = $this->pregleaf->tohr() . ',';

        if (count($this->subpatt_start) > 0) {
            $lab = $lab . 'starts';
            foreach ($this->subpatt_start as $node) {
                $lab = $lab . "{$node->subpattern},";
            }
        }
        if (count($this->subpatt_end) > 0) {
            $lab = $lab . 'ends';
            foreach ($this->subpatt_end as $node) {
                $lab = $lab . "{$node->subpattern},";
            }
        }

        $lab = substr($lab, 0, strlen($lab) - 1);
        $lab = '"' . str_replace('"', '\"', $lab) . '"';

        // Dummy transitions are displayed dotted.
        if ($this->consumeschars) {
            return $this->from->number . '->' . $this->to->number . "[label = $lab];";
        } else {
            return $this->from->number . '->' . $this->to->number . "[label = $lab, style = dotted];";  // Dummy transitions are displayed dotted.
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
    /** @var boolean whether state is deterministic, i.e. whether it has no characters with two or more possible outgoing transitions. */
    protected $deterministic;
    /** @var int number of the state. */
    public $number;

    public function __construct(&$fa = null) {
        $this->fa = $fa;
        $this->number = -1;    // States should be numerated from 0 by calling qtype_preg_finite_automaton::numerate_states().
        $this->outtransitions = array();
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
 * Represents an abstract finite automaton. Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa.
 */
abstract class qtype_preg_finite_automaton {

    /** @var array of qtype_preg_fa_state, indexed by state numbers. */
    protected $states;
    /** @var object of qtype_preg_fa_state - start state. */
    protected $startstate;
    /** @var object of qtype_preg_fa_state - end state. */
    protected $endstate;

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
        $this->states = array();
        $this->startstate = null;
        $this->endstate = null;
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
    public function start_state() {
        return $this->startstate;
    }

    /**
     * Return the end state of the automaton.
     *
     * TODO - determine, whether we could get automaton with several end states - then return array.
     */
    public function end_state() {
        return $this->endstate;
    }

    public function get_states() {
        return $this->states;
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
     * Set the start state of the automaton to given state.
     */
    public function set_start_state(&$state) {
        if ($this->state_exists($state)) {
            $this->startstate = $state;
        } else {
            throw new qtype_preg_exception('set_start_state error: No state ' . $state->number . ' in automaton');
        }
    }

    /**
     * Set the end state of the automaton to given state.
     */
    public function set_end_state(&$state) {
        if ($this->state_exists($state)) {
            $this->endstate = $state;
        } else {
            throw new qtype_preg_exception('set_end_state error: No state ' . $state->number . ' in automaton');
        }
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
     */
    public function add_state(&$state) {
        $this->states[] = $state;
        $state->set_fa($this);
        $this->statecount++;
        if ($this->statecount > $this->statelimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
     * Removes a state from the automaton.
     *
     * @param state a reference to the state to be removed.
     */
    public function remove_state(&$state) {
        foreach ($this->states as $key => $curstate) {
            if ($curstate === $state) {
                $this->transitioncount -= count($curstate->outgoing_transitions());
                $this->statecount--;
                unset($this->states[$key]);
                break;
            }
        }
    }

    /**
     * Read and create a FA from dot-like language. Mainly used for unit-testing.
     */
    public function read_fa($dotstring) {
        // TODO - kolesov.
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
    public function compare_fa($another) {
        // TODO - streltsov.
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
     * Changes automaton to not contain wordbreak  simple assertions (\b and \B).
     */
    public function avoid_wordbreaks() {
        // TODO - delete \b and \B.
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex integer index of state of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     */
    public function instersect_fa($anotherfa, $stateidnex, $isstart) {
        // TODO.
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
