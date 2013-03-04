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
 * Represents a transition between two states.
 */
class qtype_preg_nfa_transition extends qtype_preg_fa_transition {

    /** @var array qtype_preg_node instances for starting subpatterns. */
    public $subpatt_start;
    /** @var array qtype_preg_node instances for ending subpatterns. */
    public $subpatt_end;

    public $is_null;

    public function __construct(&$from, &$pregleaf, &$to, $number, $consumechars = true) {
        parent::__construct($from, $pregleaf, $to, $number, $consumechars);
        $this->subpatt_start = array();
        $this->subpatt_end = array();
        $this->is_null = false;
    }

    // Overriden for subpatterns information
    public function get_label_for_dot() {
        $index1 = $this->from->number;
        $index2 = $this->to->number;
        $lab = $this->number . ':' . $this->pregleaf->tohr() . ',';

        // Information about subpatterns.
        if (count($this->subpatt_start) > 0) {
            $lab = $lab . 'starts';
            foreach ($this->subpatt_start as $node) {
                $lab = $lab . "{$node->id},";
            }
        }
        if (count($this->subpatt_end) > 0) {
            $lab = $lab . 'ends';
            foreach ($this->subpatt_end as $node) {
                $lab = $lab . "{$node->id},";
            }
        }
        $lab = substr($lab, 0, strlen($lab) - 1);
        $lab = '"' . str_replace('"', '\"', $lab) . '"';
        // Dummy transitions are displayed dotted.
        if ($this->consumechars) {
            return "$index1->$index2" . "[label = $lab];";
        } else {
            return "$index1->$index2" . "[label = $lab, style = dotted];";
        }
    }
}

/**
 * Represents a nondeterministic finite automaton.
 */
class qtype_preg_nondeterministic_fa extends qtype_preg_finite_automaton {

    protected $subpatt_count;

    protected $subexpr_map;
    protected $subexpr_to_subpatt_map;

    public function __construct($nodescount, $subexpr_map) {
        parent::__construct();
        $this->subpatt_count = $nodescount; // TODO - decrease to the actual value.
        $this->subexpr_map = $subexpr_map;
        $this->subexpr_to_subpatt_map = array();
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

    public function subpatt_count() {
        return $this->subpatt_count;
    }

    public function subpatt_from_subexpr_number($subexpr_number) {
        return $this->subexpr_to_subpatt_map[$subexpr_number];
    }

    public function on_subexpr_added($subexpr_number, $subpatt_number) {
        $this->subexpr_to_subpatt_map[$subexpr_number] = $subpatt_number;
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
        if (!$this->pregnode->is_subpattern()) {
            return;
        }

        $body = array_pop($stack);

        // Copy this node to the starting transitions.
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $transition->subpatt_start[] = $this->pregnode;
        }

        // Copy this node to the ending transitions.
        foreach ($automaton->get_states() as $state) {
            foreach ($state->outgoing_transitions() as $transition) {
                if ($transition->to === $body['end']) {
                    $transition->subpatt_end[] = $this->pregnode;
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
 * Class for infinite quantifiers * {0,} + and {1,}.
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
            if (!$transition->is_null) {
                $newtransition = clone $transition;
                $newtransition->number = ++$transitioncounter;
                $body['end']->add_transition($newtransition);    // "from" will be set here.
            }
        }

        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body, $transitioncounter);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end'], ++$transitioncounter);
        $transition->is_null = true;
        $body['start']->add_transition($transition);

        // Update automaton/stack properties.
        $automaton->set_start_state($body['start']);
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for + or {1,} quantifier.
     */
    private function create_plus(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        $body = array_pop($stack);

        // Now, clone all transitions from the start state to the end state.
        foreach ($body['start']->outgoing_transitions() as $transition) {
            if (!$transition->is_null) {
                $newtransition = clone $transition;
                $newtransition->number = ++$transitioncounter;
                $body['end']->add_transition($newtransition);    // "from" will be set here.
            }
        }

        // Update automaton/stack properties.
        $stack[] = $body;
    }

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        if ($this->pregnode->leftborder == 0) {
            $this->create_aster($matcher, $automaton, $stack, $transitioncounter);
        } else if ($this->pregnode->leftborder == 1) {
            $this->create_plus($matcher, $automaton, $stack, $transitioncounter);
        } else {
            // TODO: throw exception.
            echo "SOMETHING WENT WRONG DURING qtype_preg_nfa_node_infinite_quant NFA BUILDING\n";
            var_dump($this->pregnode->leftborder);
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
        $transition->is_null = true;
        $body['start']->add_transition($transition);

        // Update automaton/stack properties.
        $automaton->set_start_state($body['start']);
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        if ($this->pregnode->rightborder == 0) {
            // Repeating 0 times means eps-transition.
            $start = new qtype_preg_fa_state($automaton);
            $end = new qtype_preg_fa_state($automaton);
            $automaton->add_state($start);
            $automaton->add_state($end);

            // Add eps-transition.
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_nfa_transition($start, $epsleaf, $end, ++$transitioncounter);
            $transition->is_null = true;
            $start->add_transition($transition);

            // Update automaton/stack properties.
            $automaton->set_start_state($start);
            $automaton->set_end_state($end);
            $stack[] = array('start' => $start, 'end' => $end);
        } else if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            // This is ? or {0, 1} operator.
            $this->create_qu($matcher, $automaton, $stack, $transitioncounter);
        } else if ($this->pregnode->leftborder == 1 && $this->pregnode->rightborder == 1) {
            // This is {1} operator.
            $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        } else {
            // TODO: throw exception
            echo "SOMETHING WENT WRONG DURING qtype_preg_nfa_node_finite_quant NFA BUILDING\n";
            var_dump($this->pregnode->leftborder);
            var_dump($this->pregnode->rightborder);
        }
    }
}

/**
 * Class for subpatterns.
 */
class qtype_preg_nfa_node_subpatt extends qtype_preg_nfa_operator {

    public function create_automaton_inner(&$matcher, &$automaton, &$stack, &$transitioncounter) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack, $transitioncounter);
        if ($this->pregnode->subtype == qtype_preg_node_subpatt::SUBTYPE_GROUPING) {
            return;
        }

        $automaton->on_subexpr_added($this->pregnode->number, $this->pregnode->id);
    }
}
