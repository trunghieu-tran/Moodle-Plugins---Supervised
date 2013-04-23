<?php
/**
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_authoring_tool_node {

    public $pregnode; // a reference to the corresponding preg_node

    public function __construct(&$node, &$handler) {
        $this->pregnode = $node;
    }

    /**
     * Creates and returns subgraph which explaining part of regular expression.
     */
    abstract public function &create_graph($id = -1);

    public function accept() {
        switch ($this->pregnode->type) {
        case qtype_preg_node::TYPE_ABSTRACT:
        case qtype_preg_node::TYPE_LEAF_CONTROL:
        case qtype_preg_node::TYPE_LEAF_OPTIONS:
        case qtype_preg_node::TYPE_NODE_ERROR:
            return false;
        default:
            return true;
        }
    }
}

/**
 * Class for tree's leaf.
 */
class qtype_preg_authoring_tool_leaf extends qtype_preg_authoring_tool_node
{
    /**
     * Returns filling settings of node which will be in graph.
     */
    public function get_filled() {
        if ($this->pregnode->caseinsensitive) {
            return ', style=filled, fillcolor=grey';
        } else {
            return '';
        }
    }

    /**
     * Returns value of node which will be in graph.
     */
    public function get_value() {
        switch ($this->pregnode->type) {
        case qtype_preg_node::TYPE_LEAF_CHARSET:
            return self::process_charset($this->pregnode->userinscription);

        case qtype_preg_node::TYPE_LEAF_META:
            if ($this->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY)
                return array('Void');
            else
                return array(get_string('explain_unknow_meta', 'qtype_preg'));

        case qtype_preg_node::TYPE_LEAF_ASSERT:
            if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX)
                return array(get_string('description_circumflex', 'qtype_preg'));
            else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR)
                return array(get_string('description_dollar', 'qtype_preg'));
            else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B)
                return array(($this->pregnode->negative ? get_string('description_wordbreak_neg', 'qtype_preg') : get_string('description_wordbreak', 'qtype_preg')));
            else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_A)
                return array(get_string('description_esc_a', 'qtype_preg'));
            else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_Z)
                return array(get_string('description_esc_z', 'qtype_preg'));
            else
                return array(get_string('explain_unknow_assert', 'qtype_preg'));

        case qtype_preg_node::TYPE_LEAF_BACKREF:
            if (is_integer($this->pregnode->number))
                return array(str_replace('%number', $this->pregnode->number, get_string('description_backref', 'qtype_preg')));
            else
                return array(str_replace('%name', $this->pregnode->number, get_string('description_backref_name', 'qtype_preg')));

        case qtype_preg_node::TYPE_LEAF_RECURSION:
            if ($this->pregnode->number == 0)
                return array(get_string('description_recursion_all', 'qtype_preg'));
            else if (is_integer($this->pregnode->number))
                return array(str_replace('%number', $this->pregnode->number, get_string('description_recursion', 'qtype_preg')));
            else
                return array(str_replace('%name', $this->pregnode->number, get_string('description_recursion_name', 'qtype_preg')));
        default:
            return array(get_string('explain_unknow_node', 'qtype_preg'));
        }
    }

    /**
     * Returns color of node which will be in graph.
     */
    public function get_color() {
        switch ($this->pregnode->type) {
        case qtype_preg_node::TYPE_LEAF_CHARSET:
            $tmp = $this->get_value();
            if (count($tmp) == 1) {
                if ($tmp[0][0] == chr(10))
                    return 'green';
                else
                    return 'black';
            } else {
                return 'black';
            }

        case qtype_preg_node::TYPE_LEAF_META:
            return 'orange';

        case qtype_preg_node::TYPE_LEAF_ASSERT:
            return 'red';

        case qtype_preg_node::TYPE_LEAF_BACKREF:
        case qtype_preg_node::TYPE_LEAF_RECURSION:
            return 'blue';

        default:
            return 'pink';
        }
    }

    /**
     * Returns shape of node which will be in graph.
     */
    public function get_shape() {
        if ($this->pregnode->type == qtype_preg_node::TYPE_LEAF_META || $this->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT ||
             $this->pregnode->type == qtype_preg_node::TYPE_LEAF_BACKREF || $this->pregnode->type == qtype_preg_node::TYPE_LEAF_RECURSION)
            return 'ellipse';
        else if (count($this->pregnode->flags) > 1 || $this->pregnode->negative) {
            return 'record';
        } else {
            if ($this->get_color() == 'green')
                return 'ellipse';
            else if ($this->pregnode->flags[0][0]->data->length() > 1)
                return 'record';
            else
                return 'ellipse';
        }
    }

    /**
     * Implementation of abstract create_graph for leaf.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node($this->get_value(), $this->get_shape(), $this->get_color(), $graph, $this->pregnode->id, $this->get_filled());
        if ($this->pregnode->negative)
            $graph->nodes[0]->invert = true;

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

    /**
     * Processes userinscription of charset to make an array of information which one will be in result graph.
     */
    public static function process_charset($info) {
        $result = array();

        $result[] = '';
        foreach ($info as $iter) {
            $mpos = strpos($iter->data, '-');
            if ($mpos != 0 && $mpos != strlen($iter->data) - 1) {
                if ($mpos == 1)
                    $result[] = chr(10) . 'from ' . substr($iter->data, 0, $mpos) . ' to ' . substr($iter->data, $mpos + 1);
                else
                    $result[] = chr(10) . 'from ' . str_replace('%code', substr($iter->data, 2, $mpos-2), get_string('description_char_16value', 'qtype_preg')) . ' to ' . str_replace('%code', substr($iter->data, $mpos + 3), get_string('description_char_16value', 'qtype_preg'));

                continue;
            }

            for ($i = 0; $i < strlen($iter->data); $i++) {
                if ($i == 0 && $iter->data[$i] == '[') {
                    $i += 2;
                    $tmp = '';
                    while ($iter->data[$i] != ':') {
                        $tmp .= $iter->data[$i];
                        $i++;
                    }
                    $i++;

                    $result[] = chr(10) . get_string('description_charflag_' . $tmp, 'qtype_preg');
                } else if ($iter->data[$i] == '\\') {
                    $i++;
                    if ($iter->data[$i] == '\\')
                        $result[count($result) - 1] .= '\\';
                    if ($iter->data[$i] == 'p') {
                        $i++;
                        if ($iter->data[$i] == '{') {
                            $tmp = '';
                            $i++;
                            while ($iter->data[$i] != '}') {
                                $tmp .= $iter->data[$i];
                                $i++;
                            }
                            $result[] = chr(10) . get_string('description_charflag_' . $tmp, 'qtype_preg');
                        } else {
                            $result[] = chr(10) . get_string('description_charflag_' . $iter->data[$i], 'qtype_preg');
                        }
                    } else if ($iter->data[$i] == 'x' || $iter->data[$i] == 'X') {
                        $i++;
                        if (ctype_xdigit($iter->data[$i])) {
                            $tmp = $iter->data[$i];
                            $i++;
                            if (ctype_xdigit($iter->data[$i])) {
                                $tmp .= $iter->data[$i];
                            }
                        } else if ($iter->data[$i] == '{') {
                            $i++;
                            while ($iter->data[$i] != '}') {
                                $tmp .= $iter->data[$i];
                                $i++;
                            }
                        } else {
                            $tmp = 0;
                            $i--;
                        }

                        $result[] = chr(10) . str_replace('%code', $tmp, get_string('description_char_16value', 'qtype_preg'));
                    } else if ($iter->data[$i] == 'n') {
                        $result[] = chr(10) . get_string('description_charA', 'qtype_preg');
                    } else if ($iter->data[$i] == 'r') {
                        $result[] = chr(10) . get_string('description_charD', 'qtype_preg');
                    } else if ($iter->data[$i] == 'd') {
                        $result[] = chr(10) . get_string('description_charflag_digit', 'qtype_preg');
                    } else if ($iter->data[$i] == 'D') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_digit', 'qtype_preg');
                    } else if ($iter->data[$i] == 's') {
                        $result[] = chr(10) . get_string('description_charflag_space', 'qtype_preg');
                    } else if ($iter->data[$i] == 'S') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_space', 'qtype_preg');
                    } else if ($iter->data[$i] == 'w') {
                        $result[] = chr(10) . get_string('description_charflag_word', 'qtype_preg');
                    } else if ($iter->data[$i] == 'W') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_word', 'qtype_preg');
                    } else if ($iter->data[$i] == 't') {
                        $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                    } else if ($iter->data[$i] == 'h') {
                        $result[] = chr(10) . get_string('description_charflag_hspace', 'qtype_preg');
                    } else if ($iter->data[$i] == 'v') {
                        $result[] = chr(10) . get_string('description_charflag_vspace', 'qtype_preg');
                    } else if ($iter->data[$i] == 'H') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg')  . get_string('description_charflag_hspace', 'qtype_preg');
                    } else if ($iter->data[$i] == 'V') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg')  . get_string('description_charflag_vspace', 'qtype_preg');
                    } else if ($iter->data[$i] == ' ') {
                        $result[] = chr(10) . get_string('description_char20', 'qtype_preg');
                    } else if ($iter->data[$i] == '	') {
                        $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                    } else {
                        $result[0] .= $iter->data[$i];
                    }
                } else if ($iter->data[$i] == ' ') {
                    $result[] = chr(10) . get_string('description_char20', 'qtype_preg');
                } else if ($iter->data[$i] == '	') {
                    $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                } else if ($iter->data[$i] == '.') {
                    if ($iter->type == qtype_preg_userinscription::TYPE_GENERAL)
                        $result[] = $iter->data[$i];
                    else
                        $result[] = chr(10) . get_string('description_charflag_print', 'qtype_preg');
                } else {
                    $result[0] .= $iter->data[$i];
                }
            }
        }

        if ($result[0] == '') {
            unset($result[0]);
            $result = array_values($result);
        }

        return $result;
    }
}

/**
 * Class for tree's operator.
 */
class qtype_preg_authoring_tool_operator extends qtype_preg_authoring_tool_node {
    public $operands = array(); // an array of operands

    public function __construct($node, &$handler) {
        parent::__construct($node, $handler);

        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $handler->from_preg_node($operand));
        }

        if (isset($this->pregnode->condbranch)) {
            array_push($this->operands, $handler->from_preg_node($this->pregnode->condbranch));
        }
    }

    /**
     * Implementation of abstract create_graph for concatenation.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_CONCAT) {

            $left = $this->operands[0]->create_graph($id);
            qtype_preg_explaining_graph_tool::assume_subgraph($graph, $left);
            $graph->entries[] = end($left->entries);

            $n = count($this->operands);
            for($i = 1; $i < $n; ++$i) {
                $right = $this->operands[$i]->create_graph($id);
                qtype_preg_explaining_graph_tool::assume_subgraph($graph, $right);
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left->exits[0], $right->entries[0]);

                if ($i != $n-1) {
                    $left = $right;
                } else {
                    $graph->exits[] = end($right->exits);
                }
            }

        } else if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $left = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);
            $right = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

            foreach($this->operands as $operand) {
                $new_operand = $operand->create_graph($id);
                qtype_preg_explaining_graph_tool::assume_subgraph($graph, $new_operand);

                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left, $new_operand->entries[count($new_operand->entries)-1]);
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $new_operand->exits[count($new_operand->exits)-1], $right);
            }

            $graph->nodes[] = $left;
            $graph->entries[] = $left;
            $graph->nodes[] = $right;
            $graph->exits[] = $right;
        } else if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT || $this->pregnode->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT) {
            $operand = $this->operands[0]->create_graph($id);

            if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
                $label = 'from ' . $this->pregnode->leftborder . ' to ';
                if ($this->pregnode->rightborder == 1)
                    $label .= $this->pregnode->rightborder . ' time';
                else
                    $label .= $this->pregnode->rightborder .' times';
            } else {
                $label = 'from ' . $this->pregnode->leftborder . ' to infinity times';
            }

            $quant = new qtype_preg_explaining_graph_tool_subgraph($label, 'dotted; color=black', $this->pregnode->id);
            qtype_preg_explaining_graph_tool::assume_subgraph($quant, $operand);

            $graph->subgraphs[] = $quant;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        } else if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
            if ($this->pregnode->operands[0]->type != qtype_preg_node::TYPE_LEAF_META) {
                $operand = $this->operands[0]->create_graph($id);
            } else {
                $operand = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
                $operand->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $operand, -1);
                $operand->entries[] = end($operand->nodes);
                $operand->exits[] = end($operand->nodes);
            }

            $label = get_string('explain_subexpression', 'qtype_preg') . $this->pregnode->number;

            $subexpr = new qtype_preg_explaining_graph_tool_subgraph($label, 'solid; color=black', $this->pregnode->id);
            qtype_preg_explaining_graph_tool::assume_subgraph($subexpr, $operand);

            $graph->subgraphs[] = $subexpr;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        } else if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR) {

            $cond_subexpr = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=black', $this->pregnode->id);
            $cond_subexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=purple', 0.1 + $this->pregnode->id);
            $isAssert = FALSE;

            if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR || $this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION ||
                $this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {

                switch ($this->pregnode->subtype) {
                case qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR:
                    $cond_subexpr->subgraphs[0]->nodes[] =
                        new qtype_preg_explaining_graph_tool_node(
                                array(is_integer($this->pregnode->number) ?
                                      str_replace('%number', $this->pregnode->number, get_string('description_backref', 'qtype_preg')) :
                                      str_replace('%name', $this->pregnode->number, get_string('description_backref_name', 'qtype_preg'))),
                                'ellipse', 'blue', $cond_subexpr->subgraphs[0], -1
                                );
                    break;
                case qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION:
                    $cond_subexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(get_string('description_recursion_all', 'qtype_preg')), 'ellipse', 'blue', $cond_subexpr->subgraphs[0], -1);
                    break;
                case qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE:
                    $cond_subexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(get_string('explain_define', 'qtype_preg')), 'ellipse', 'blue', $cond_subexpr->subgraphs[0], -1);
                    break;
                }
            } else {
                $isAssert = TRUE; $index = 1;
                if (count($this->operands) == 3) {$index = 2;}
                $tmp = $this->operands[$index]->create_graph($id);
                //print_r($tmp);
                qtype_preg_explaining_graph_tool::assume_subgraph($cond_subexpr->subgraphs[0], $tmp->subgraphs[0]);
            }

            $cond_subexpr->subgraphs[0]->entries[] = end($cond_subexpr->subgraphs[0]->nodes[0]);
            $cond_subexpr->subgraphs[0]->exits[] = end($cond_subexpr->subgraphs[0]->nodes[0]);

            $cond_subexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph($this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE ? ''/*'true'*/ : '', 'dashed; color=purple', 0.2 + $this->pregnode->id);
            $tmp = $this->operands[0]->create_graph($id);
            qtype_preg_explaining_graph_tool::assume_subgraph($cond_subexpr->subgraphs[1], $tmp);
            $cond_subexpr->subgraphs[1]->entries[] = end($tmp->entries);
            $cond_subexpr->subgraphs[1]->exits[] = end($tmp->exits);

            if (((count($this->operands) == 2 && !$isAssert) || (count($this->operands) == 3 && $isAssert)) && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
                $cond_subexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(''/*'false'*/, 'dashed; color=purple', 0.3 + $this->pregnode->id);
                $tmp = $this->operands[1]->create_graph($id);
                qtype_preg_explaining_graph_tool::assume_subgraph($cond_subexpr->subgraphs[2], $tmp);
                $cond_subexpr->subgraphs[2]->entries[] = end($tmp->entries);
                $cond_subexpr->subgraphs[2]->exits[] = end($tmp->exits);
            }

            $graph->subgraphs[] = $cond_subexpr;
            $graph->entries[] = $cond_subexpr->subgraphs[0]->nodes[0];

            if (((count($this->operands) == 2 && !$isAssert) || (count($this->operands) == 3 && $isAssert)) && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
                $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);
                $graph->exits[] = $graph->subgraphs[0]->nodes[0];
                $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);

                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $cond_subexpr->subgraphs[0]->nodes[0], $graph->subgraphs[0]->nodes[1]);
                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $graph->subgraphs[0]->nodes[1], $cond_subexpr->subgraphs[1]->entries[0]);
                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $cond_subexpr->subgraphs[1]->exits[0], $graph->subgraphs[0]->nodes[0]);

                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('false', $graph->subgraphs[0]->nodes[1], $cond_subexpr->subgraphs[2]->entries[0]);
                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $cond_subexpr->subgraphs[2]->exits[0], $graph->subgraphs[0]->nodes[0]);
            } else {
                $graph->exits[] = $cond_subexpr->subgraphs[1]->exits[0];
                $cond_subexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $cond_subexpr->subgraphs[0]->nodes[0], $cond_subexpr->subgraphs[1]->entries[0]);
            }
        } else if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_ASSERT) {
            $operand = $this->operands[0]->create_graph($id);

            $subexpr = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=grey', $this->pregnode->id);
            qtype_preg_explaining_graph_tool::assume_subgraph($subexpr, $operand);

            $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

            switch ($this->pregnode->subtype) {
            case qtype_preg_node_assert::SUBTYPE_PLA:
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $operand->entries[0], 'normal, color=green, lhead="cluster_' . $this->pregnode->id . '"');
                break;
            case qtype_preg_node_assert::SUBTYPE_NLA:
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $operand->entries[0], 'normal, color=red, lhead="cluster_' . $this->pregnode->id . '"');
                break;
            case qtype_preg_node_assert::SUBTYPE_PLB:
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $operand->entries[0], 'inv, color=green, lhead="cluster_' . $this->pregnode->id . '"');
                break;
            case qtype_preg_node_assert::SUBTYPE_NLB:
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $operand->entries[0], 'inv, color=red, lhead="cluster_' . $this->pregnode->id . '"');
                break;
            default:
                $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $operand->entries[0]);
            }

            $graph->subgraphs[] = $subexpr;
            $graph->entries[] = $graph->nodes[count($graph->nodes) - 1];
            $graph->exits[] = $graph->nodes[count($graph->nodes) - 1];
        }

        if ($id == $this->pregnode->id) {
            $marking = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=darkgreen', 0.5 + $this->pregnode->id);
            qtype_preg_explaining_graph_tool::assume_subgraph($marking, $graph);
            $graph->nodes = array();
            $graph->links = array();
            $graph->subgraphs = array();
            $graph->subgraphs[] = $marking;
        }

        return $graph;
    }
}

?>
