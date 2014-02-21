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

    // A subpattern node with minimal number.
    public $min_subpatt_node;

    // Does this transition start a backreferenced subexpression(s)?
    public $starts_backrefed_subexprs;

    // Does this transition start a quantifier?
    public $starts_quantifier;

    // Does this transition end a quantifier?
    public $ends_quantifier;

    // Does this transition make a infinite quantifier loop?
    public $is_loop;

    public function __construct($from, $pregleaf, $to, $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, $consumeschars = true) {
        parent::__construct($from, $pregleaf, $to, $origin, $consumeschars);
        $this->min_subpatt_node = null;
        $this->starts_backrefed_subexprs = false;
        $this->starts_quantifier = false;
        $this->ends_quantifier = false;
        $this->is_loop = false;
    }

    public function causes_backtrack() {
        return $this->starts_backrefed_subexprs || $this->starts_quantifier;
    }
}

/**
 * Represents a nondeterministic finite automaton.
 */
class qtype_preg_nfa extends qtype_preg_finite_automaton {

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

    public function ast_root() {
        return $this->astroot;
    }

    public function max_subpatt() {
        return $this->maxsubpatt;
    }

    public function max_subexpr() {
        return $this->maxsubexpr;
    }

    public function on_subexpr_added($pregnode, $body) {
        // Copy the node to the starting transitions.
        $start = $body['start'];
        $outgoing = $this->get_adjacent_transitions($start, true);
        foreach ($outgoing as $transition) {
            if (in_array($pregnode->number, $this->subexpr_ref_numbers)) {
                $transition->starts_backrefed_subexprs = true;
            }
        }
    }

    public function after_build($body) {
        $this->remove_all_start_states();
        $this->remove_all_end_states();
        $this->add_start_state($body['start']);
        $this->add_end_state($body['end']);
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
     * @param automaton - a reference to the automaton being built.
     * @param stack - a stack of arrays in the form of array('start' => $ref1, 'end' => $ref2),
     *                start and end states of parts of the resulting automaton.
     */
    abstract protected function create_automaton_inner(&$automaton, &$stack);

    public function __construct($node, $matcher) {
        $this->pregnode = $node;
    }

    public function create_automaton(&$automaton, &$stack) {
        $this->create_automaton_inner($automaton, $stack);

        // Don't augment transition if the node is not a subpattern.
        if ($this->pregnode->subpattern == -1) {
            return;
        }

        $body = array_pop($stack);

        // Copy this node to the starting transitions.
        foreach ($automaton->get_adjacent_transitions($body['start'], true) as $transition) {
            $transition->subpatt_start[$this->pregnode->subpattern] = $this->pregnode;
            if ($this->pregnode->subpattern < 0) {
                continue;
            }
            if ($transition->min_subpatt_node == null || $transition->min_subpatt_node->subpattern > $this->pregnode->subpattern) {
                $transition->min_subpatt_node = $this->pregnode;
            }
        }

        // Copy this node to the ending transitions.
        foreach ($automaton->get_adjacent_transitions($body['end'], false) as $transition) {
            if ($transition->to === $body['end']) {
                $transition->subpatt_end[$this->pregnode->subpattern] = $this->pregnode;
            }
        }

        $stack[] = $body;
    }
}

/**
 * Class for leafs. They contruct trivial NFAs with two states and one transition between them.
 */
class qtype_preg_nfa_leaf extends qtype_preg_nfa_node {

    public function accept() {
        if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_G) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        // Create start and end states of the resulting automaton.
        $start = $automaton->add_state();
        $end = $automaton->add_state();

        // Add a corresponding transition between them.
        $automaton->add_transition(new qtype_preg_nfa_transition($start, $this->pregnode, $end));

        $stack[] = array('start' => $start, 'end' => $end);
    }
}

/**
 * Abstract class for nodes, they construct NFAs by combining existing NFAs.
 */
abstract class qtype_preg_nfa_operator extends qtype_preg_nfa_node {

    public $operands = array();    // Array of operands.

    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $matcher->from_preg_node($operand);
        }
    }

    public static function add_ending_eps_transition_if_needed(&$automaton, &$stack_item) {
        $outgoing = $automaton->get_adjacent_transitions($stack_item['end'], true);
        if (!empty($outgoing)) {
            $end = $automaton->add_state();
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $automaton->add_transition(new qtype_preg_nfa_transition($stack_item['end'], $epsleaf, $end));
            $stack_item['end'] = $end;
        }
    }
}

/**
 * Class for concatenation.
 */
class qtype_preg_nfa_node_concat extends qtype_preg_nfa_operator {

    protected function create_automaton_inner(&$automaton, &$stack) {
        $count = count($this->operands);
        $result = null;

        // Create automata for operands and concatenate them.
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack);
            $cur = array_pop($stack);
            if ($result === null) {
                $result = $cur;
            } else {
                $automaton->redirect_transitions($cur['start'], $result['end']);
                $result = array('start' => $result['start'], 'end' => $cur['end']);
            }
        }

        $stack[] = $result;
    }
}

/**
 * Class for alternation.
 */
class qtype_preg_nfa_node_alt extends qtype_preg_nfa_operator {

    protected function create_automaton_inner(&$automaton, &$stack) {
        $count = count($this->operands);
        $result = null;

        // Create automata for operands and alternate them.
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack);
            $cur = array_pop($stack);
            self::add_ending_eps_transition_if_needed($automaton, $cur);  // Necessary if there's a quantifier in the end.
            if ($result === null) {
                $result = $cur;
            } else {
                // Merge start and end states.
                $automaton->redirect_transitions($cur['start'], $result['start']);
                $automaton->redirect_transitions($cur['end'], $result['end']);
            }
        }

        $stack[] = $result;
    }
}

/**
 * Class containing common methods for both finite and infinite quantifiers.
 */
abstract class qtype_preg_nfa_node_quant extends qtype_preg_nfa_operator {

    public function accept() {
        if ($this->pregnode->possessive) {
            return get_string('possessivequant', 'qtype_preg');
        }
        return true;
    }

    protected function mark_transitions($automaton, $startstate, $endstate) {
        $outgoing = $automaton->get_adjacent_transitions($startstate, true);
        $incoming = $automaton->get_adjacent_transitions($endstate, false);
        foreach ($outgoing as $transition) {
            $transition->starts_quantifier = true;
        }
        foreach ($incoming as $transition) {
            $transition->ends_quantifier = true;
        }
    }
}

/**
 * Class for infinite quantifiers (*, +, {m,}).
 */
class qtype_preg_nfa_node_infinite_quant extends qtype_preg_nfa_node_quant {

    /**
     * Creates an automaton for * or {0,} quantifier.
     */
    private function create_aster(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);
        $body = array_pop($stack);

        // Now, clone all transitions from the start state to the end state.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;        // Set this field for transitions, including original body.
            $newtransition = clone $transition;
            $newtransition->from = $body['end'];
            $newtransition->is_loop = true;
            $automaton->add_transition($newtransition);
        }

        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);

        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m,} quantifier
     */
    private function create_brace(&$automaton, &$stack) {
        // Operand creates its automaton m times.
        $leftborder = $this->pregnode->leftborder;
        $result = null;

        // Linking automatons to the resulting one.
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack);
            $cur = array_pop($stack);
            // The last block is repeated.
            if ($i == $leftborder - 1) {
                $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
                $outgoing = $automaton->get_adjacent_transitions($cur['start'], true);
                foreach ($outgoing as $transition) {
                    $newtransition = clone $transition;
                    $realgreediness = qtype_preg_fa_transition::min_greediness($newtransition->greediness, $greediness);
                    $newtransition->greediness = $realgreediness; // Set this field only for the last repetition.
                    $newtransition->from = $cur['end'];
                    $newtransition->is_loop = true;
                    $automaton->add_transition($newtransition);
                }
            }
            if ($result === null) {
                // On the first iteration we just remember current automaton as the result.
                $result = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->redirect_transitions($result['end'], $cur['start']);
                $result['end'] = $cur['end'];
            }
        }

        $stack[] = $result;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        if ($this->pregnode->leftborder == 0) {
            $this->create_aster($automaton, $stack);
        } else {
            $this->create_brace($automaton, $stack);
        }
        $body = array_pop($stack);
        $this->mark_transitions($automaton, $body['start'], $body['end']);

        $stack[] = $body;
    }
}

/**
 * Class for finite quantifiers {m,n}.
 */
class qtype_preg_nfa_node_finite_quant extends qtype_preg_nfa_node_quant {

    /**
     * Creates an automaton for ? quantifier.
     */
    private function create_qu(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);
        $body = array_pop($stack);

        // Set the greediness.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;
        }

        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);

        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m, n} quantifier.
     */
    private function create_brace(&$automaton, &$stack) {
        // Operand creates its automaton n times.
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;
        $result = null;
        $borderstates = array();    // States to which separating eps-transitions will be added.

        // Linking automatons to the resulting one.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack);
            $cur = array_pop($stack);
            if ($i >= $leftborder) {
                self::add_ending_eps_transition_if_needed($automaton, $cur);
                $outgoing = $automaton->get_adjacent_transitions($cur['start'], true);
                foreach ($outgoing as $transition) {
                    $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
                    $transition->greediness = $realgreediness;
                }
                $borderstates[] = $cur['start'];
            }
            if ($result === null) {
                // On the first iteration we just remember current automaton as the result.
                $result = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->redirect_transitions($result['end'], $cur['start']);
                $result['end'] = $cur['end'];
            }
        }

        // Adding eps-transitions after first m bodies.
        foreach ($borderstates as $state) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_nfa_transition($state, $epsleaf, $result['end']);
            $automaton->add_transition($transition);
        }

        $stack[] = $result;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        if ($this->pregnode->rightborder == 0) {
            $this->operands[0]->create_automaton($automaton, $stack);
            $body = array_pop($stack);

            $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
            foreach ($outgoing as $transition) {
                $transition->greediness = qtype_preg_fa_transition::GREED_ZERO;
            }

            // The body automaton can be skipped by a greedy eps-transition.
            self::add_ending_eps_transition_if_needed($automaton, $body);
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']);
            $automaton->add_transition($transition);

            $stack[] = $body;
        } else if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            $this->create_qu($automaton, $stack);
        } else {
            $this->create_brace($automaton, $stack);
        }
        $body = array_pop($stack);
        $this->mark_transitions($automaton, $body['start'], $body['end']);

        $stack[] = $body;
    }
}

/**
 * Class for subexpressions.
 */
class qtype_preg_nfa_node_subexpr extends qtype_preg_nfa_operator {

    public function accept() {
        if ($this->pregnode->subtype == qtype_preg_node_subexpr::SUBTYPE_ONCEONLY) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);

        if ($this->pregnode->subpattern != -1) {
            $automaton->on_subexpr_added($this->pregnode, end($stack));
        }
    }
}

/**
 * Class for conditional subexpressions.
 */
class qtype_preg_nfa_node_cond_subexpr extends qtype_preg_nfa_operator {

    public function __construct($node, $matcher) {
        $this->pregnode = $node;

        $shift = (int)$node->is_condition_assertion();

        // Add an eps leaf if there's only positive branch.
        if (count($node->operands) - $shift == 1) {
            $node->operands[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        }

        $concatpos = new qtype_preg_node_concat();
        $concatpos->operands[] = new qtype_preg_leaf_assert_subexpr_captured(false, $node->number);
        $concatpos->operands[] = $node->operands[0 + $shift];

        $concatneg = new qtype_preg_node_concat();
        $concatneg->operands[] = new qtype_preg_leaf_assert_subexpr_captured(true, $node->number);
        $concatneg->operands[] = $node->operands[1 + $shift];

        $alt = new qtype_preg_node_alt();
        $alt->operands[] = $concatpos;
        $alt->operands[] = $concatneg;

        $grouping = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $grouping->subpattern = $node->subpattern;
        $grouping->operands[] = $alt;

        $this->operands = array($matcher->from_preg_node($grouping));
    }

    public function accept() {
        if ($this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        $this->operands[0]->create_automaton($automaton, $stack);
    }
}
