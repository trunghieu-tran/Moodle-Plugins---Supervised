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

    /** @var array Integer values used for subpattern capturing.
     *
     * A value of 2*k means the beginning of the k-subpattern, a value of 2*k+1 means the ending of the k-subpattern.
     */
    public $tags;

    public function __construct(&$from, &$pregleaf, &$to, $consumechars = true) {
        parent::__construct($from, $pregleaf, $to, $consumechars);
        $this->tags = array();
    }
}

/**
 * Represents a nondeterministic finite automaton.
 */
class qtype_preg_nondeterministic_fa extends qtype_preg_finite_automaton {

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
    abstract public function create_automaton(&$matcher, &$automaton, &$stack);

    public function __construct(&$node, &$matcher) {
        $this->pregnode = $node;
    }
}

/**
 * Class for leafs. They contruct trivial NFAs with two states and one transition between them.
 */
class qtype_preg_nfa_leaf extends qtype_preg_nfa_node {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // Create start and end states of the resulting automaton.
        $start = new qtype_preg_fa_state($automaton);
        $end = new qtype_preg_fa_state($automaton);
        // Add a corresponding transition between them.
        $start->add_transition(new qtype_preg_nfa_transition($start, $this->pregnode, $end));
        $automaton->add_state($start);
        $automaton->add_state($end);
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

    public static function add_ending_eps_transition_if_needed(&$automaton, &$stack_item) {
        if (count($stack_item['end']->outgoing_transitions()) > 0) {
            $end = new qtype_preg_fa_state();
            $automaton->add_state($end);
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $stack_item['end']->add_transition(new qtype_preg_nfa_transition($stack_item['end'], $epsleaf, $end));
            $stack_item['end'] = $end;
        }
    }
}

/**
 * Class for concatenation.
 */
class qtype_preg_nfa_node_concat extends qtype_preg_nfa_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // First, operands create their automatons.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack);

        // Then, we take automatons and concatenate them.
        $second = array_pop($stack);
        $first = array_pop($stack);

        $automaton->update_state_references($second['start'], $first['end']);
        $first['end']->merge_transition_set($second['start']);
        $automaton->remove_state($second['start']);

        $automaton->set_start_state($first['start']);
        $automaton->set_end_state($second['end']);
        $stack[] = array('start' => $first['start'], 'end' => $second['end']);
    }

}

/**
 * Class for alternation.
 */
class qtype_preg_nfa_node_alt extends qtype_preg_nfa_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // First, operands create their automatons.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack);

        // Then, we take automatons and alternate them.
        $second = array_pop($stack);
        $first = array_pop($stack);

        // It is necessary to add eps-transitions to the end of each automaton if they represent quantifiers.
        qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $first);
        qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $second);

        // Now, merge start and end states.
        $automaton->update_state_references($second['start'], $first['start']);
        $automaton->update_state_references($second['end'], $first['end']);
        $first['start']->merge_transition_set($second['start']);
        $automaton->remove_state($second['start']);
        $automaton->remove_state($second['end']);

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
    private function create_aster(&$matcher, &$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $body = array_pop($stack);

        // Now, clone all transitions from the start state to the end state.
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $body['end']->add_transition(clone $transition);
        }

        // The body automaton can be skipped by an eps-transition.
        qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $body['start']->add_transition(new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']));
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m,} quantifier
     */
    private function create_brace(&$matcher, &$automaton, &$stack) {
        // Operand creates its automaton m times.
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        }

        $res = null;    // The resulting pair of states.

        // Linking automatons to the resulting one.
        for ($i = 0; $i < $leftborder; $i++) {
            $cur = array_pop($stack);
            // The last block is repeated.
            if ($i === $leftborder - 1) {
                foreach ($cur['start']->outgoing_transitions() as $transition) {
                    $cur['end']->add_transition(clone $transition);
                }
            }
            if ($res === null) {
                // On the first iteration we just remember current automaton as the result.
                $res = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->update_state_references($res['end'], $cur['start']);
                $cur['start']->merge_transition_set($res['end']);
                $automaton->remove_state($res['end']);
                $res['end'] = $cur['end'];
            }
        }
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        if ($this->pregnode->leftborder === 0) {
            return $this->create_aster($matcher, $automaton, $stack);
        } else {
            return $this->create_brace($matcher, $automaton, $stack);
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
    private function create_qu(&$matcher, &$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $body = array_pop($stack);

        // The body automaton can be skipped by an eps-transition.
        qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $body['start']->add_transition(new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']));
        $automaton->set_end_state($body['end']);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m, n} quantifier.
     */
    private function create_brace(&$matcher, &$automaton, &$stack) {
        // Operand creates its automaton n times.
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;

        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        }
        $res = null;                // The resulting automaton.
        $borderstates = array();    // States to which separating eps-transitions will be added.

        // Linking automatons to the resulting one.
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($stack);
            if ($i >= $leftborder) {
                qtype_preg_nfa_operator::add_ending_eps_transition_if_needed($automaton, $cur);
                $borderstates[] = $cur['start'];
            }
            if ($res === null) {
                // On the first iteration we just remember current automaton as the result.
                $res = $cur;
            } else {
                // On subsequent iterations we concatenate current automaton to the result.
                $automaton->update_state_references($res['end'], $cur['start']);
                $cur['start']->merge_transition_set($res['end']);
                $automaton->remove_state($res['end']);
                $res['end'] = $cur['end'];
            }
        }

        // Adding eps-transitions after first m bodies.
        foreach ($borderstates as $state) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $state->add_transition(new qtype_preg_nfa_transition($state, $epsleaf, $res['end']));
        }
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        if ($this->pregnode->rightborder === 0) {
            // Repeating 0 times means epsilon-transition.
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $start = new qtype_preg_fa_state($automaton);
            $end = new qtype_preg_fa_state($automaton);
            $start->add_transition(new qtype_preg_nfa_transition($start, $epsleaf, $end));
            $automaton->add_state($start);
            $automaton->add_state($end);
            $automaton->set_start_state($start);
            $automaton->set_end_state($end);
            $stack[] = array('start' => $start, 'end' => $end);
        } else if ($this->pregnode->leftborder === 0 && $this->pregnode->rightborder === 1) {
            return $this->create_qu($matcher, $automaton, $stack);
        } else {
            return $this->create_brace($matcher, $automaton, $stack);
        }
    }
}

/**
 * Class for subpatterns.
 */
class qtype_preg_nfa_node_subpatt extends qtype_preg_nfa_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        if ($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING) {
            return;
        }
        $body = array_pop($stack);

        // Every transition outgoing from the start state we tag with the value of (subpattern number * 2).
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $transition->tags[] = $this->pregnode->number * 2;
        }

        // Every transition to the end state we tag with the value of (subpattern number * 2 + 1).
        foreach ($automaton->get_states() as $state) {
            foreach ($state->outgoing_transitions() as $transition) {
                if ($transition->to === $body['end']) {
                    $transition->tags[] = $this->pregnode->number * 2 + 1;
                }
            }
        }
        $stack[] = $body;
    }
}
