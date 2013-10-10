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
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_explaining_graph_node_abstract {

    public $pregnode; // A reference to the corresponding preg_node.
    public $handler;

    public function __construct($node, $handler) {
        $this->pregnode = $node;
        $this->handler = $handler;
    }

    /**
     * Creates and returns subgraph which explaining part of regular expression.
     * @param int $id Identifier of node which will be picked out in image.
     */
    abstract public function &create_graph($id = -1);

    /**
     * Checks admissibility of node by the engine.
     * @return bool True if this node is supported by the engine.
     */
    public function accept() {
        switch ($this->pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT:
            case qtype_preg_node::TYPE_LEAF_CONTROL:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return get_string($this->pregnode->type, 'qtype_preg');
            default:
                return true;
        }
    }
}

/**
 * Class for tree's leaf.
 */
abstract class qtype_preg_explaining_graph_leaf extends qtype_preg_explaining_graph_node_abstract {
    /**
     * Returns filling settings of node which will be in graph.
     * @return string Filling settings of node.
     */
    public function get_filled() {
        if ($this->pregnode->caseless || ($this->pregnode->type == qtype_preg_node::TYPE_LEAF_OPTIONS && $this->pregnode->posopt == 'i')) {
            return ', style=filled, fillcolor=grey';
        } else {
            return '';
        }
    }

    /**
     * Returns value of node which will be in graph.
     * @return string Value of node.
     */
    public abstract function get_value();

    /**
     * Returns color of node which will be in graph.
     * @return string Color of node.
     */
    public abstract function get_color();

    /**
     * Returns shape of node which will be in graph.
     * @return string Shape of node.
     */
    public abstract function get_shape();

    /**
     * Implementation of abstract create_graph for leaf.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node($this->get_value(), $this->get_shape(), $this->get_color(), $graph, $this->pregnode->id, $this->get_filled());
        if ($this->pregnode->negative) {
            $graph->nodes[0]->invert = true;
        }

        if ($id == $this->pregnode->id) {
            $graph->style .= '; color=darkgreen';

            $marking = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', 0.5 + $this->pregnode->id);
            $marking->subgraphs[] = $graph;

            $marking->entries[] = end($graph->nodes);
            $marking->exits[] = end($graph->nodes);

            return $marking;
        } else {
            $graph->entries[] = end($graph->nodes);
            $graph->exits[] = end($graph->nodes);
        }

        return $graph;
    }
}

/**
 * Class for tree's charset leaf.
 */
class qtype_preg_explaining_graph_leaf_charset extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        $info = $this->pregnode->userinscription;   // Refer a userinscription to new variable for convenience.
        if (count($this->pregnode->userinscription) > 1) {
            array_shift($info);
            array_pop($info);
        }
        $result = array('');                          // This will store the result, first element is empty.

        // Now, iterate over userinscription elements.
        foreach ($info as $userinscription) {
            $res = qtype_preg_authoring_tool::userinscription_to_string($userinscription);
            if ($res === $userinscription->data) {
                $result[0] .= $userinscription->data;
            } else {
                $result[] = $res;
            }
        }

        // If first element is empty then delete it.
        if ($result[0] == '') {
            array_shift($result);
        }

        return $result;
    }

    public function get_color() {
        $tmp = $this->get_value();
        if (count($tmp) == 1) {
            if ($tmp[0][0] == chr(10)) {
                return 'hotpink';
            } else {
                return 'black';
            }
        } else {
            return 'black';
        }
    }

    public function get_shape() {

        if (count($this->pregnode->flags) > 1 || $this->pregnode->negative) {
            return 'record';
        } else {
            if ($this->get_color() == 'hotpink') {
                return 'ellipse';
            } else if ($this->pregnode->flags[0][0]->data->length() > 1) {
                return 'record';
            } else {
                return 'ellipse';
            }
        }
    }
}

/**
 * Class for tree's meta leaf.
 */
class qtype_preg_explaining_graph_leaf_meta extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        if ($this->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
            return array('Void');
        } else {
            return array(get_string('explain_unknow_meta', 'qtype_preg'));
        }
    }

    public function get_color() {
        return 'orange';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's assert leaf.
 */
class qtype_preg_explaining_graph_leaf_assert extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        $result = get_string($this->pregnode->lang_key(true), 'qtype_preg');
        return array($result);
    }

    public function get_color() {
        return 'red';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's backreference leaf.
 */
class qtype_preg_explaining_graph_leaf_backref extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        return array(get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode->number));
    }

    public function get_color() {
        return 'blue';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's recursion leaf.
 */
class qtype_preg_explaining_graph_leaf_recursion extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        return array(get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode->number));
    }

    public function get_color() {
        return 'blue';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's options leaf.
 */
class qtype_preg_explaining_graph_leaf_options extends qtype_preg_explaining_graph_leaf {

    public function get_value() {
        return array('');
    }

    public function get_color() {
        return 'orange';
    }

    public function get_shape() {
        return 'box';
    }
}

/**
 * Class for tree's operator.
 */
abstract class qtype_preg_explaining_graph_operator extends qtype_preg_explaining_graph_node_abstract {

    public $operands = array(); // An array of operands.
    protected $condid = -1;      // A number of conditional branch of conditional subexpression.

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $handler->from_preg_node($operand);
        }
    }

    /**
     * Processes a tree's operator for creating a part of explainning graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $graph Current explainning graph.
     * @param int $id Same as a create_graph parameter.
     */
    abstract protected function process_operator($graph, $id);

    /**
     * Implementation of abstract create_graph for concatenation.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $this->process_operator($graph, $id);

        if ($id == $this->pregnode->id || ($id == $this->condid && $id != -1) ) {
            $marking = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=darkgreen', 0.5 + $this->pregnode->id);
            $marking->assume_subgraph($graph);
            $graph->nodes = array();
            $graph->links = array();
            $graph->subgraphs = array();
            $graph->subgraphs[] = $marking;
        }

        return $graph;
    }
}

/**
 * Class for tree's concatenation operator.
 */
class qtype_preg_explaining_graph_node_concat extends qtype_preg_explaining_graph_operator {

    protected function process_operator($graph, $id) {
        $left = $this->operands[0]->create_graph($id);
        $graph->assume_subgraph($left);
        $graph->entries[] = end($left->entries);

        $n = count($this->operands);
        for ($i = 1; $i < $n; ++$i) {
            $right = $this->operands[$i]->create_graph($id);
            $graph->assume_subgraph($right);
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left->exits[0], $right->entries[0], $graph);

            if ($i != $n-1) {
                $left = $right;
            } else {
                $graph->exits[] = end($right->exits);
            }
        }
    }
}

/**
 * Class for tree's alternation operator.
 */
class qtype_preg_explaining_graph_node_alt extends qtype_preg_explaining_graph_operator {

    protected function process_operator($graph, $id) {
        $left = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);
        $right = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

        foreach ($this->operands as $operand) {
            $newoperand = $operand->create_graph($id);
            $graph->assume_subgraph($newoperand);

            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left, $newoperand->entries[count($newoperand->entries)-1], $graph);
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $newoperand->exits[count($newoperand->exits)-1], $right, $graph);
        }

        $graph->nodes[] = $left;
        $graph->entries[] = $left;
        $graph->nodes[] = $right;
        $graph->exits[] = $right;
    }
}

/**
 * Class for tree's quantifier operator.
 */
class qtype_preg_explaining_graph_node_quant extends qtype_preg_explaining_graph_operator {

    protected function process_operator($graph, $id) {
        $operand = $this->operands[0]->create_graph($id);

        $a = new stdClass;
        $a->leftborder = $this->pregnode->leftborder;
        if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            $a->rightborder = $this->pregnode->rightborder;
        }
        $a->greedy = get_string($this->pregnode->lang_key_for_greediness(), 'qtype_preg');
        $a->firstoperand = '';
        $label = get_string($this->pregnode->lang_key(true), 'qtype_preg', $a);

        $quant = new qtype_preg_explaining_graph_tool_subgraph($label, 'dotted; color=black', $this->pregnode->id);
        $quant->assume_subgraph($operand);

        $graph->subgraphs[] = $quant;
        $graph->entries[] = end($operand->entries);
        $graph->exits[] = end($operand->exits);
    }

}

/**
 * Class for tree's subexpression operator.
 */
class qtype_preg_explaining_graph_node_subexpr extends qtype_preg_explaining_graph_operator {

    protected function process_operator($graph, $id) {
        if ($this->pregnode->operands[0]->type != qtype_preg_node::TYPE_LEAF_META) {
            $operand = $this->operands[0]->create_graph($id);
        } else {
            $operand = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
            if ($this->operands[0]->pregnode->id == $id) {
                $operand->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=darkgreen');
                $operand->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $operand->subgraphs[0], -1);
                $operand->entries[] = end($operand->subgraphs[0]->nodes);
                $operand->exits[] = end($operand->subgraphs[0]->nodes);
            } else {
                $operand->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $operand, -1);
                $operand->entries[] = end($operand->nodes);
                $operand->exits[] = end($operand->nodes);
            }
        }

        $label = '';
        if ($this->pregnode->number != -1) {
            $label = get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode);
            $label = qtype_poasquestion_string::replace(': [ {$a->firstoperand} ]', '', $label);
            $label = qtype_poasquestion_string::replace('"', '\\"', $label);
        }

        $generated = $this->handler->is_node_generated($this->pregnode);

        $subexpr = new qtype_preg_explaining_graph_tool_subgraph(
                        $label,
                        ($this->pregnode->userinscription[0]->data != '(?i:...)')
                            ? ($generated ? 'solid; color=invis; bgcolor=white' : 'solid; color=black')
                            : 'filled; color=lightgrey',
                        $this->pregnode->id
                    );
        $subexpr->assume_subgraph($operand);

        $graph->subgraphs[] = $subexpr;
        $graph->entries[] = end($operand->entries);
        $graph->exits[] = end($operand->exits);
    }
}

/**
 * Class for tree's conditional subexpression operator.
 */
class qtype_preg_explaining_graph_node_cond_subexpr extends qtype_preg_explaining_graph_operator {

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        if (count($this->operands) == 3) {
            $this->condid = $this->operands[0]->pregnode->id;
        }
    }

    protected function process_operator($graph, $id) {
        $condsubexpr = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=black', $this->pregnode->id);
        $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=purple', 0.1 + $this->pregnode->id);
        $isassert = false;
        $tmp = null;

        if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            // TODO: refactor this and below
            $key = 'description_leaf_backref';
            if (is_string($this->pregnode->number)) {
                $key .= '_name';
            }
            $condsubexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
                                                        array(get_string($key, 'qtype_preg', $this->pregnode->number)),
                                                        'ellipse', 'blue', $condsubexpr->subgraphs[0], -1);
        } else if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION) {
            $key = 'description_leaf_recursion';
            if ($this->pregnode->number === 0) {
                $key .= '_all';
            } else if (is_string($this->pregnode->number)) {
                $key .= '_name';
            }
            return array(get_string($key, 'qtype_preg', $this->pregnode->number));
            $condsubexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
                                                        array(get_string($key, 'qtype_preg', $this->pregnode->number)),
                                                        'ellipse', 'blue', $condsubexpr->subgraphs[0], -1);

        } else if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {

            $condsubexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
                                                        array(get_string('explain_define', 'qtype_preg')),
                                                        'ellipse',
                                                        'blue',
                                                        $condsubexpr->subgraphs[0],
                                                        -1
                                                    );

        } else {
            $isassert = true; $index = 1;
            if (count($this->operands) == 3) {
                $index = 2;
            }
            $tmp = $this->operands[$index]->create_graph($id);

            $condsubexpr->subgraphs[0]->assume_subgraph($tmp);
        }

        $point = count($condsubexpr->subgraphs[0]->nodes) ? $condsubexpr->subgraphs[0]->nodes[0] : $condsubexpr->subgraphs[0]->subgraphs[0]->nodes[0];
        $condsubexpr->subgraphs[0]->entries[] = $point;
        $condsubexpr->subgraphs[0]->exits[] = $point;

        $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(
                                            $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE ? '' : '',
                                            'dashed; color=purple',
                                            0.2 + $this->pregnode->id
                                        );
        $tmp = $this->operands[0]->create_graph($id);
        $condsubexpr->subgraphs[1]->assume_subgraph($tmp);
        $condsubexpr->subgraphs[1]->entries[] = end($tmp->entries);
        $condsubexpr->subgraphs[1]->exits[] = end($tmp->exits);

        if (((count($this->operands) == 2 && !$isassert) || (count($this->operands) == 3 && $isassert))
            && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'dashed; color=purple', 0.3 + $this->pregnode->id);
            $tmp = $this->operands[1]->create_graph($id);
            $condsubexpr->subgraphs[2]->assume_subgraph($tmp);
            $condsubexpr->subgraphs[2]->entries[] = end($tmp->entries);
            $condsubexpr->subgraphs[2]->exits[] = end($tmp->exits);
        }

        $graph->subgraphs[] = $condsubexpr;
        $graph->entries[] = $point;

        if (((count($this->operands) == 2 && !$isassert) || (count($this->operands) == 3 && $isassert))
            && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);
            $graph->exits[] = $graph->subgraphs[0]->nodes[0];
            $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);

            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $point, $graph->subgraphs[0]->nodes[1], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $graph->subgraphs[0]->nodes[1], $condsubexpr->subgraphs[1]->entries[0], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $condsubexpr->subgraphs[1]->exits[0], $graph->exits[0], $condsubexpr);

            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('false', $graph->subgraphs[0]->nodes[1], $condsubexpr->subgraphs[2]->entries[0], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $condsubexpr->subgraphs[2]->exits[0], $graph->exits[0], $condsubexpr);
        } else {
            $graph->exits[] = $condsubexpr->subgraphs[1]->exits[0];
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $point, $condsubexpr->subgraphs[1]->entries[0], $condsubexpr);
        }
    }
}

/**
 * Class for tree's assert operator.
 */
class qtype_preg_explaining_graph_node_assert extends qtype_preg_explaining_graph_operator {

    private static $linkoptions = array(
                                        qtype_preg_node_assert::SUBTYPE_PLA => 'normal, color="green"',
                                        qtype_preg_node_assert::SUBTYPE_NLA => 'normal, color="red"',
                                        qtype_preg_node_assert::SUBTYPE_PLB => 'inv, color="green"',
                                        qtype_preg_node_assert::SUBTYPE_NLB => 'inv, color="red"'
                                    );

    protected function process_operator($graph, $id) {
        $operand = $this->operands[0]->create_graph($id);

        $color = (($this->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLA || $this->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLB) ? 'green' : 'red');

        $sub = new qtype_preg_explaining_graph_tool_subgraph(
                    '',
                    'solid; edge[style=dotted, color=' . $color . ']; node[style=dashed, color=' . $color . ']; color=grey',
                    $this->pregnode->id
                );
        $sub->assume_subgraph($operand);

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

        $graph->links[] = new qtype_preg_explaining_graph_tool_link(
                                '',
                                $graph->nodes[count($graph->nodes) - 1],
                                $operand->entries[0], $graph,
                                self::$linkoptions[$this->pregnode->subtype]
                            );

        $graph->subgraphs[] = $sub;
        $graph->entries[] = $graph->nodes[count($graph->nodes) - 1];
        $graph->exits[] = $graph->nodes[count($graph->nodes) - 1];
    }
}
