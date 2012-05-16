<?php
/**
 * Defines NFA node classes
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/question/type/preg/preg_regex_handler.php');
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

    public function __construct(&$from, &$pregleaf, &$to) {
        parent::__construct($from, $pregleaf, $to);
        $this->tags = array();
    }
};

/**
 * Represents a nondeterministic finite automaton.
 */
class qtype_preg_nondeterministic_fa extends qtype_preg_finite_automaton {

    function set_limits() {
        global $CFG;
        $this->statelimit = 250;
        $this->transitionlimit = 250;
        if (isset($CFG->qtype_preg_nfastatelimit)) {
            $this->statelimit = $CFG->qtype_preg_nfastatelimit;
        }
        if (isset($CFG->qtype_preg_nfatransitionlimit)) {
            $this->transitionlimit = $CFG->qtype_preg_nfatransitionlimit;
        }
    }

    function should_be_deterministic() {
        return false;
    }

    function substract_fa($anotherfa){}

    function invert_fa(){}

    function match($str, $pos){}
    function next_character(){}
    function complete_match(){}
}

/**
* abstract class for both operators and leafs
*/
abstract class nfa_preg_node {

    public $pregnode;    // a reference to the corresponding preg_node

    /**
    * returns true if engine support the node, rejection string otherwise
    */
    public function accept() {
        return true; // accepting anything by default
    }

    /**
     * Creates an automaton corresponding to this node
     * @param matcher - a reference to the matcher
     * @param automaton - a reference to the automaton being built
     * @param stack - a stack of arrays in the form of array('start'=>ref1, 'end'=>ref2) - start and end states of parts of the automaton
     */
    abstract public function create_automaton(&$matcher, &$automaton, &$stack);

    public function __construct(&$node, &$matcher) {
        $this->pregnode = $node;
    }

}

/**
* class for nfa transitions
*/
class nfa_preg_leaf extends nfa_preg_node {

    public function accept() {
        if ($this->pregnode->type == preg_node::TYPE_LEAF_ASSERT && $this->pregnode->subtype == preg_leaf_assert::SUBTYPE_ESC_G) {
            $leafdesc = get_string($this->pregnode->name(), 'qtype_preg');
            return $leafdesc . ' \G';
        }
        return true;

    }

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // create start and end states of the resulting automaton
        $start =& new qtype_preg_fa_state($automaton);
        $end =& new qtype_preg_fa_state($automaton);
        $start->add_transition(new qtype_preg_nfa_transition($start, $this->pregnode, $end));
        $automaton->add_state($start);
        $automaton->add_state($end);
        $automaton->set_start_state($start);
        $automaton->set_end_state($end);
        $stack[] = array('start'=>$start, 'end'=>$end);
    }

}

/**
* abstract class for nfa operators
*/
abstract class nfa_preg_operator extends nfa_preg_node {

    public $operands = array();    // an array of operands

    public function __construct($node, &$matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }

}

/**
* defines concatenation
*/
class nfa_preg_node_concat extends nfa_preg_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // first, operands create their automatons
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack);

        // take automata and concatenate them
        $second = array_pop($stack);
        $first = array_pop($stack);

        $automaton->update_state_references($second['start'], $first['end']);
        $first['end']->merge_transition_set($second['start']);
        $automaton->remove_state($second['start']);

        $automaton->set_start_state($first['start']);
        $automaton->set_end_state($second['end']);
        $stack[] = array('start'=>$first['start'], 'end'=>$second['end']);
    }

}

/**
* defines alternation
*/
class nfa_preg_node_alt extends nfa_preg_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        // first, operands create their automatons
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $this->operands[1]->create_automaton($matcher, $automaton, $stack);

        // take automata and alternate them
        $second = array_pop($stack);
        $first = array_pop($stack);

        // add eps-transitions to the end of each automaton if necessary
        if (count($first['end']->outgoing_transitions()) > 0) {
            $end =& new qtype_preg_fa_state;
            $automaton->add_state($end);
            $epsleaf =& new preg_leaf_meta;
            $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
            $first['end']->add_transition(new qtype_preg_nfa_transition($first['end'], $epsleaf, $end));
            $first['end'] =& $end;
        }
        if (count($second['end']->outgoing_transitions()) > 0) {
            $end =& new qtype_preg_fa_state;
            $automaton->add_state($end);
            $epsleaf =& new preg_leaf_meta;
            $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
            $second['end']->add_transition(new qtype_preg_nfa_transition($second['end'], $epsleaf, $end));
            $second['end'] =& $end;
        }

        // start and end states are merged
        $automaton->update_state_references($second['start'], $first['start']);
        $automaton->update_state_references($second['end'], $first['end']);
        $first['start']->merge_transition_set($second['start']);
        $automaton->remove_state($second['start']);
        $automaton->remove_state($second['end']);

        $automaton->set_start_state($first['start']);
        $automaton->set_end_state($first['end']);
        $stack[] = array('start'=>$first['start'], 'end'=>$first['end']);
    }

}

/**
* defines infinite quantifiers * + {m,}
*/
class nfa_preg_node_infinite_quant extends nfa_preg_operator {

    public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
        return true;
    }

    /**
     * creates an automaton for * or {0,} quantifier
     */
    private function create_aster(&$matcher, &$automaton, &$stack) {
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $body = array_pop($stack);
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $body['end']->add_transition(clone $transition);
        }
        $epsleaf =& new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body['start']->add_transition(new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']));
        $stack[] = $body;
    }

    /**
     * creates an automaton for {m,} quantifier
     */
    private function create_brace(&$matcher, &$automaton, &$stack) {
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        }
        $invstack = array();
        for ($i = 0; $i < $leftborder; $i++) {
            $invstack[] = array_pop($stack);
        }
        $res = null;    // the resulting pair of states
        // linking automata to the resulting one
        for ($i = 0; $i < $leftborder; $i++) {
            $cur = array_pop($invstack);
            // the last block is repeated
            if ($i == $leftborder - 1) {
                foreach ($cur['start']->outgoing_transitions() as $transition) {
                    $cur['end']->add_transition(clone $transition);
                }
            }
            if ($res === null) {
                $res = $cur;
            } else {
                $automaton->update_state_references($res['end'], $cur['start']);
                $cur['start']->merge_transition_set($res['end']);
                $automaton->remove_state($res['end']);
                $res['end'] =& $cur['end'];
            }
        }
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        if ($this->pregnode->leftborder == 0) {
            return $this->create_aster($matcher, $automaton, $stack);
        } else {
            return $this->create_brace($matcher, $automaton, $stack);
        }
    }

}

/**
* defines finite quantifiers {m, n}
*/
class nfa_preg_node_finite_quant extends nfa_preg_operator {

    /**
     * creates an automaton for ? quantifier
     */
    private function create_qu(&$matcher, &$automaton, &$stack) {
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $body = array_pop($stack);
        $epsleaf =& new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body['start']->add_transition(new qtype_preg_nfa_transition($body['start'], $epsleaf, $body['end']));
        $stack[] = $body;
    }

    /**
     * creates an automaton for {m, n} quantifier
     */
    private function create_brace(&$matcher, &$automaton, &$stack) {
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;
        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        }
        $invstack = array();
        for ($i = 0; $i < $rightborder; $i++) {
            $invstack[] = array_pop($stack);
        }
        $res = null;        // the resulting automaton
        $borderstates = array();
        // linking automatons to the resulting one
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($invstack);
            if ($i >= $leftborder) {
                $borderstates[] =& $cur['start'];
            }
            if ($res === null) {
                $res = $cur;
            } else {
                // Concatenate 2 automatons.
                $automaton->update_state_references($res['end'], $cur['start']);
                $cur['start']->merge_transition_set($res['end']);
                $automaton->remove_state($res['end']);
                $res['end'] =& $cur['end'];
            }
        }
        foreach ($borderstates as $state) {
            $state->add_transition(new qtype_preg_nfa_transition($state, $epsleaf, $res['end']));
        }
        $automaton->set_start_state($res['start']);
        $automaton->set_end_state($res['end']);
        $stack[] = $res;
    }

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            return $this->create_qu($matcher, $automaton, $stack);
        } else {
            return $this->create_brace($matcher, $automaton, $stack);
        }
    }

}

/**
* defines subpatterns
*/
class nfa_preg_node_subpatt extends nfa_preg_operator {

    public function create_automaton(&$matcher, &$automaton, &$stack) {
        $this->operands[0]->create_automaton($matcher, $automaton, $stack);
        $body = array_pop($stack);
        foreach ($body['start']->outgoing_transitions() as $transition) {
            $transition->tags[] = $this->pregnode->number * 2;
        }
        foreach ($automaton->get_states() as $state) {
            foreach ($state->outgoing_transitions() as $transition) {
                //$next->belongs_to_subpatt[$this->pregnode->number] = true;
                if ($transition->to === $body['end']) {
                    $transition->tags[] = $this->pregnode->number * 2 + 1;
                }
            }
        }
        $stack[] = $body;
    }

}

?>