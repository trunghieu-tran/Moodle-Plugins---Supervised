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
 * Defines NFA node classes.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');

/**
 * Represents a transition between two nfa states.
 */
class qtype_preg_nfa_transition extends qtype_preg_fa_transition {

    // Array of nodes representing subpatterns starting at this transition.
    public $subpatt_start;

    // Array of nodes representing subpatterns ending at this transition.
    public $subpatt_end;

    // Array of nodes representing subexpressions starting at this transition.
    public $subexpr_start;

    // Array of nodes representing subexpressions ending at this transition.
    public $subexpr_end;

    // Is this transition a "go-back" transition?
    public $is_loop;

    // A subpattern node with minimal number.
    public $min_subpatt_node;

    public function __construct(&$from, &$pregleaf, &$to, $number, $consumeschars = true) {
        parent::__construct($from, $pregleaf, $to, $number, $consumeschars);
        $this->subpatt_start = array();
        $this->subpatt_end = array();
        $this->subexpr_start = array();
        $this->subexpr_end = array();
        $this->is_loop = false;
        $this->min_subpatt_node = null;
    }

    // Overriden for subpatterns information
    public function get_label_for_dot() {
        $lab = $this->number . ':' . $this->pregleaf->tohr() . ',';

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

        if ($this->consumeschars) {
            return $this->from->number . '->' . $this->to->number . "[label = $lab];";
        } else {
            return $this->from->number . '->' . $this->to->number . "[label = $lab, style = dotted];";  // Dummy transitions are displayed dotted.
        }
    }
}

/**
 * Represents a nondeterministic finite automaton.
 */
class qtype_preg_nfa extends qtype_preg_finite_automaton {

    // Number of subpatterns in the regular expression.
    protected $max_subpatt;

    // Number of subexpressions in the regular expression.
    protected $max_subexpr;

    // Map where keys are subexpression names, and values are their numbers.
    protected $subexpr_name_to_number_map;

    public function __construct($max_subpatt, $max_subexpr, $subexpr_name_to_number_map) {
        parent::__construct();
        $this->max_subpatt = $max_subpatt;
        $this->max_subexpr = $max_subexpr;
        $this->subexpr_name_to_number_map = $subexpr_name_to_number_map;
    }

    protected function set_limits() {
        global $CFG;
        $this->statelimit = 250;
        $this->transitionlimit = 250;
        if (isset($CFG->qtype_preg_nfa_transition_limit)) {
            $this->statelimit = $CFG->qtype_preg_nfa_transition_limit;
        }
        if (isset($CFG->qtype_preg_nfa_state_limit)) {
            $this->transitionlimit = $CFG->qtype_preg_nfa_state_limit;
        }
    }

    public function should_be_deterministic() {
        return false;
    }

    public function substract_fa($anotherfa) {
    }

    public function invert_fa() {
    }

    public function match($str, $pos) {
    }

    public function next_character() {
    }

    public function complete_match() {
    }

    public function max_subpatt() {
        return $this->max_subpatt;
    }

    public function max_subexpr() {
        return $this->max_subexpr;
    }


    public function on_subexpr_added($pregnode) {
        $subpatt_number = $pregnode->subpattern;
        $subexpr_number = $pregnode->number;

        // Copy the node to the starting transitions.
        foreach ($this->startstate->outgoing_transitions() as $transition) {
            $transition->subexpr_start[$subexpr_number] = $pregnode;
        }

        // Copy the node to the ending transitions.
        foreach ($this->states as $state) {
            foreach ($state->outgoing_transitions() as $transition) {
                if ($transition->to === $this->endstate) {
                    $transition->subexpr_end[$subexpr_number] = $pregnode;
                }
            }
        }
    }
}

/**
 * Abstract class for both nodes (operators) and leafs (operands).
 */
abstract class qtype_preg_nfa_node {

    public $pregnode;    // Reference to the corresponding qtype_preg_node.

    /**
     * Returns true if this node is supported by the engine, rejection string otherwise.
     */
    public function accept() {
        return true; // Accepting anything by default.
    }

    /**
     * Creates an automaton corresponding to this node.
     * @param matcher - a reference to the matcher.
     * @param automaton - a reference to the automaton being built.
     * @param stack - a stack of arrays in the form of array('start' => $ref1, 'end' => $ref2),
     *                start and end states of parts of the resulting automaton.
     */
    abstract public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter);

    public function __construct($node, &$matcher) {
        $this->pregnode = $node;
    }

    public function create_automaton(&$matcher, &$automaton, &$stack, &$transitioncounter)
    {
        $this->create_automaton_inner($matcher, $automaton, $stack, $transitioncounter);

        // Don't augment transition if the node is not a subpattern.
        if ($this->pregnode->subpattern == -1) {
            return;
        }

        $body = array_pop($stack);

        // Copy this node to the starting transitions.
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $transition->subpatt_start[$this->pregnode->subpattern] = $this->pregnode;
            if ($transition->min_subpatt_node == null || $transition->min_subpatt_node->subpattern > $this->pregnode->subpattern) {
                $transition->min_subpatt_node = $this->pregnode;
            }
        }

        // Copy this node to the ending transitions.
        foreach ($automaton->get_states() as $state) {
            foreach ($state->outgoing_transitions() as $transition) {
                if ($transition->to === $body['end']) {
                    $transition->subpatt_end[$this->pregnode->subpattern] = $this->pregnode;
                }
            }
        }

        $stack[] = $body;
    }

    public static function move_transitions($from, $to) {
        foreach ($from->outgoing_transitions() as $transition) {
            $to->add_transition($transition);   // "from" is set automatically.
        }
        $from->remove_all_transitions();
    }
}

/**
 * Class for leafs. They contruct trivial NFAs with two states and one transition between them.
 */
class qtype_preg_nfa_leaf extends qtype_preg_nfa_node {

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Create start and end states of the resulting automaton.
        $start = new qtype_preg_fa_state($automaton);
        $end = new qtype_preg_fa_state($automaton);
        $automaton->add_state($start);
        $automaton->add_state($end);

        // Add a corresponding transition between them.
        $start->add_transition(new qtype_preg_nfa_transition($start, $this->pregnode, $end, ++$transitioncounter));

        // Update automaton/stack properties.
        $automaton->set_start_state($start);
        $automaton->set_end_state($end);
        $stack[] = array('start' => $start, 'end' => $end);
    }
}

/**
 * Abstract class for nodes, they construct NFAs by combining existing NFAs.
 */
abstract class qtype_preg_nfa_operator extends qtype_preg_nfa_node {

    public $operands = array();    // Array of operands.

    public function __construct($node, &$matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }

    public static function add_ending_eps_transition_if_needed(&$automaton, &$stack_item, &$transitioncounter) {
        if (count($stack_item['end']->outgoing_transitions()) > 0) {
            $end = new qtype_preg_fa_state();
            $automaton->add_state($end);

            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $stack_item['end']->add_transition(new qtype_preg_nfa_transition($stack_item['end'], $epsleaf, $end, ++$transitioncounter));
            $stack_item['end'] = $end;
        }
    }

    public static function reverse_stack_items(&$stack, $number) {
        $tmp = array();
        for ($i = 0; $i < $number; $i++) {
            $tmp[] = array_pop($stack);
        }
        foreach ($tmp as $item) {
            $stack[] = $item;
        }
    }
}

/**
 * Class for concatenation.
 */
class qtype_preg_nfa_node_concat extends qtype_preg_nfa_operator {

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operands create their automatons.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack, $transitioncounter);

        // Take automatons and concatenate them.
        $second = array_pop($stack);
        $first = array_pop($stack);

        $automaton->update_state_references($second['start'], $first['end']);
        self::move_transitions($second['start'], $first['end']);
        $automaton->remove_state($second['start']);

        // Update automaton/stack properties.
        $automaton->set_start_state($first['start']);
        $automaton->set_end_state($second['end']);
        $stack[] = array('start' => $first['start'], 'end' => $second['end']);
    }
}

/**
 * Class for alternation.
 */
class qtype_preg_nfa_node_alt extends qtype_preg_nfa_operator {

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operands create their automatons.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack, $transitioncounter);

        // Take automatons and alternate them.
        $second = array_pop($stack);
        $first = array_pop($stack);

        // It is necessary to add eps-transitions to the end of each automaton if they represent quantifiers.
        self::add_ending_eps_transition_if_needed($automaton, $first, $transitioncounter);
        self::add_ending_eps_transition_if_needed($automaton, $second, $transitioncounter);

        // Merge start and end states.
        $automaton->update_state_references($second['start'], $first['start']);
        $automaton->update_state_references($second['end'], $first['end']);
        self::move_transitions($second['start'], $first['start']);
        $automaton->remove_state($second['start']);
        $automaton->remove_state($second['end']);

        // Update automaton/stack properties.
        $automaton->set_start_state($first['start']);
        $automaton->set_end_state($first['end']);
        $stack[] = array('start' => $first['start'], 'end' => $first['end']);
    }
}

/**
 * Class for infinite quantifiers (*, +, {m,}).
 */
class qtype_preg_nfa_node_infinite_quant extends qtype_preg_nfa_operator {

    public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
        return true;
    }

    /**
     * Creates an automaton for * or {0,} quantifier.
     */
    private function create_aster(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        $body = array_pop($stack);

        // Now, clone all transitions from the start state to the end state.
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $newtransition = clone $transition;
            $newtransition->is_loop = true;
            $newtransition->number = ++$transitioncounter;
            $body['end']->add_transition($newtransition);    // "from" will be set here.
        }

        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body, $transitioncounter);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end'], ++$transitioncounter);
        $body['start']->add_transition($transition);

        // Update automaton/stack properties.
        $automaton->set_start_state($body['start']);
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m,} quantifier
     */
    private function create_brace(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton m times.
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        }
        self::reverse_stack_items($stack, $leftborder);

        $res = null;    // The resulting pair of states.

        // Linking automatons to the resulting one.
        for ($i = 0; $i < $leftborder; $i++) {
            $cur = array_pop($stack);
            // The last block is repeated.
            if ($i === $leftborder - 1) {
                foreach ($cur['start']->outgoing_transitions() as $transition) {
                    $newtransition = clone $transition;
                    $newtransition->is_loop = true;
                    $newtransition->number = ++$transitioncounter;
                    $cur['end']->add_transition($newtransition);    // "from" will be set here.
                }
            }
            if ($res === null) {
                // On the first iteration we just remember current automaton as the result.
                $res = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->update_state_references($res['end'], $cur['start']);
                self::move_transitions($res['end'], $cur['start']);
                $automaton->remove_state($res['end']);
                $res['end'] = $cur['end'];
            }
        }

        // Update automaton/stack properties.
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        if ($this->pregnode->leftborder === 0) {
            return $this->create_aster($matcher, $automaton, $stack, $transitioncounter);
        } else {
            return $this->create_brace($matcher, $automaton, $stack, $transitioncounter);
        }
    }
}

/**
 * Class for finite quantifiers {m, n}.
 */
class qtype_preg_nfa_node_finite_quant extends qtype_preg_nfa_operator {

    public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
        return true;
    }

    /**
     * Creates an automaton for ? quantifier.
     */
    private function create_qu(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        $body = array_pop($stack);

        // The body automaton can be skipped by an eps-transition.
        qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $body, $transitioncounter);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end'], ++$transitioncounter);
        $body['start']->add_transition($transition);

        // Update automaton/stack properties.
        $automaton->set_start_state($body['start']);
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m, n} quantifier.
     */
    private function create_brace(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton n times.
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;

        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        }
        self::reverse_stack_items($stack, $rightborder);

        $res = null;                // The resulting automaton.
        $borderstates = array();    // States to which separating eps-transitions will be added.

        // Linking automatons to the resulting one.
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($stack);
            if ($i >= $leftborder) {
                self::add_ending_eps_transition_if_needed($automaton, $cur, $transitioncounter);
                $borderstates[] = $cur['start'];
            }
            if ($res === null) {
                // On the first iteration we just remember current automaton as the result.
                $res = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->update_state_references($res['end'], $cur['start']);
                self::move_transitions($res['end'], $cur['start']);
                $automaton->remove_state($res['end']);
                $res['end'] = $cur['end'];
            }
        }

        // Adding eps-transitions after first m bodies.
        foreach ($borderstates as $state) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_nfa_transition($state, $epsleaf, $res['end'], ++$transitioncounter);
            $state->add_transition($transition);
        }

        // Update automaton/stack properties.
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        if ($this->pregnode->rightborder === 0) {
            // Repeating 0 times means eps-transition.
            $start = new qtype_preg_fa_state($automaton);
            $end = new qtype_preg_fa_state($automaton);
            $automaton->add_state($start);
            $automaton->add_state($end);

            // Add eps-transition.
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_nfa_transition($start, $epsleaf, $end, ++$transitioncounter);
            $start->add_transition($transition);

            // Update automaton/stack properties.
            $automaton->set_start_state($start);
            $automaton->set_end_state($end);
            $stack[] = array('start' => $start, 'end' => $end);
        } else if ($this->pregnode->leftborder === 0 && $this->pregnode->rightborder === 1) {
            return $this->create_qu($matcher, $automaton, $stack, $transitioncounter);
        } else {
            return $this->create_brace($matcher, $automaton, $stack, $transitioncounter);
        }
    }
}

/**
 * Class for subexpressions.
 */
class qtype_preg_nfa_node_subexpr extends qtype_preg_nfa_operator {

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        if ($this->pregnode->subtype == qtype_preg_node_subexpr::SUBTYPE_GROUPING) {
            return;
        }

        $automaton->on_subexpr_added($this->pregnode);
    }
}
