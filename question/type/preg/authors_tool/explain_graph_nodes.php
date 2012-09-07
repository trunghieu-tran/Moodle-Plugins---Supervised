<?php
/**
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_author_tool_node {

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
            case qtype_preg_node::TYPE_NODE_COND_SUBPATT:
            case qtype_preg_node::TYPE_NODE_ERROR:
            case qtype_preg_node::TYPE_NODE_ASSERT:
                return FALSE;
            default:
                return TRUE;
        }
    }
    
}

/**
 * Class for tree's leaf.
 */
class qtype_preg_author_tool_leaf extends qtype_preg_author_tool_node
{
    /**
     * Returns filling settings of node which will be in graph. 
     */
    public function get_filled()
    {
        if ($this->pregnode->caseinsensitive)
        {
            return ', style=filled, fillcolor=grey';
        }
        else
            return '';
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
                if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX || $this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_A)
                    return get_string('description_circumflex', 'qtype_preg');
                elseif ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR || $this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_Z  || $this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_G)
                    return get_string('description_dollar', 'qtype_preg');
                elseif ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B)
                    return ($this->pregnode->negative ? get_string('description_wordbreak_neg', 'qtype_preg') : get_string('description_wordbreak', 'qtype_preg'));
                else
                    return get_string('explain_unknow_assert', 'qtype_preg');
            case qtype_preg_node::TYPE_LEAF_BACKREF:
                return array(get_string('explain_backref', 'qtype_preg') . $this->pregnode->number);
            case qtype_preg_node::TYPE_LEAF_RECURSION:
                return array(get_string('explain_recursion', 'qtype_preg') . ($this->pregnode->number ? ' in #' . $this->pregnode->number : ''));
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
                }
                else
                    return 'black';
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
        elseif (count($this->pregnode->flags) > 1 || $this->pregnode->negative) {
            return 'record';
        }
        else {
            if ($this->get_color() == 'green')
                return 'ellipse';
            elseif ($this->pregnode->flags[0][0]->data->length() > 1)
                return 'record';
            else
                return 'ellipse';
        }
    }
    
    /**
     * Implementation of abstract create_graph for leaf.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        
        $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node($this->get_value(), $this->get_shape(), $this->get_color(), $graph, $this->pregnode->id, $this->get_filled());
        if ($this->pregnode->negative) 
            $graph->nodes[0]->invert = TRUE;
        
        if ($id == $this->pregnode->id) {
            $graph->style .= '; color=darkgreen';

            $marking = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid', 0.5 + $this->pregnode->id);
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
                } elseif ($iter->data[$i] == '\\') {
                    $i++;
                    if ($iter->data[$i] == '\\')
                        $result[count($result) - 1] .= '\\\\';
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
                    }
                    elseif ($iter->data[$i] == 'x')
                    {
                        $i++;
                        $tmp = $iter->data[$i];
                        $i++;
                        $tmp .= $iter->data[$i];

                        $result[] = chr(10) . str_replace('%code', $tmp, get_string('description_char_16value', 'qtype_preg'));
                    }
                    elseif ($iter->data[$i] == 'n')
                    {
                        $result[] = chr(10) . get_string('description_char_n', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'r')
                    {
                        $result[] = chr(10) . get_string('description_char_r', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'd')
                    {
                        $result[] = chr(10) . get_string('description_charflag_digit', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'D')
                    {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_digit', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 's')
                    {
                        $result[] = chr(10) . get_string('description_charflag_space', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'S')
                    {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_space', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'w')
                    {
                        $result[] = chr(10) . get_string('description_charflag_word', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 'W')
                    {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_word', 'qtype_preg');
                    }
                    elseif ($iter->data[$i] == 't')
                    {
                        goto tab;
                    }
                    elseif ($iter->data[$i] == 'h')
                    {
                        goto horiz;
                    }
                    elseif ($iter->data[$i] == 'v')
                    {
                        goto vert;
                    }
                    elseif ($iter->data[$i] == ' ')
                    {
                        goto space;
                    }
                    elseif ($iter->data[$i] == '	')
                    {
                        goto tab;
                    }
                    else
                    {
                        $result[0] .= $iter->data[$i];
                    }
                }
                elseif ($iter->data[$i] == 'h')
                {
                    horiz:
                    $result[] = chr(10) . get_string('description_charflag_hspace', 'qtype_preg');
                }
                elseif ($iter->data[$i] == 'v')
                {
                    vert:
                    $result[] = chr(10) . get_string('description_charflag_vspace', 'qtype_preg');
                }
                elseif ($iter->data[$i] == ' ')
                {
                    space:
                    $result[] = chr(10) . get_string('description_char_space', 'qtype_preg');
                }
                elseif ($iter->data[$i] == '	')
                {
                    tab:
                    $result[] = chr(10) . get_string('description_char_t', 'qtype_preg');
                }
                elseif ($iter->data[$i] == '.')
                {
                    $result[] = chr(10) . get_string('description_charflag_print', 'qtype_preg');
                }
                else
                {
                    $result[0] .= $iter->data[$i];
                }
            }
        }

        if ($result[0] == '')
        {
            unset($result[0]);
            $result = array_values($result);
        }

        return $result;
    }
}

/**
 * Class for tree's operator.
 */
class qtype_preg_author_tool_operator extends qtype_preg_author_tool_node {
    public $operands = array(); // an array of operands
    
    public function __construct($node, &$handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $handler->from_preg_node($operand));
        }
    }
    
    /**
     * Implementation of abstract create_graph for concatenation.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        
        if ($this->pregnode->type == 'node_concat') {
            $left = $this->operands[0]->create_graph($id);
            $right = $this->operands[1]->create_graph($id);

            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $left);
            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $right);

            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $left->exits[0], $right->entries[0]);

            $graph->entries[] = end($left->entries);
            $graph->exits[] = end($right->exits);
        }
        elseif ($this->pregnode->type == 'node_alt') {
            $left = $this->operands[0]->create_graph($id);
            $right = $this->operands[1]->create_graph($id);

            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $left);
            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $right);

            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $this->pregnode->id - 0.1);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 1], $right->entries[count($right->entries) - 1]);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 1], $left->entries[count($left->entries) - 1]);
            $graph->entries[] = end($graph->nodes);

            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $this->pregnode->id - 0.2);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $right->exits[count($left->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $left->exits[count($left->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->exits[] = end($graph->nodes);
        }
        elseif ($this->pregnode->type == 'node_finite_quant' || $this->pregnode->type == 'node_infinite_quant') {
            $operand = $this->operands[0]->create_graph($id);

            if ($this->pregnode->type == 'node_finite_quant') {
                $label = 'from ' . $this->pregnode->leftborder . ' to ';
                if ($this->pregnode->rightborder == 1)
                    $label .= $this->pregnode->rightborder . ' time';
                else
                    $label .= $this->pregnode->rightborder .' times';
            }
            else {
                $label = 'from ' . $this->pregnode->leftborder . ' to infinity times';
            }

            $quant = new qtype_preg_author_tool_explain_graph_subgraph($label, 'dotted; color=black', $this->pregnode->id);
            qtype_preg_author_tool_explain_graph::assume_subgraph($quant, $operand);

            $graph->subgraphs[] = $quant;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        }
        elseif ($this->pregnode->type == 'node_subpatt') {
            $operand = $this->operands[0]->create_graph($id);

            $label = get_string('explain_subpattern', 'qtype_preg') . $this->pregnode->number;

            $subpatt = new qtype_preg_author_tool_explain_graph_subgraph($label, 'solid; color=black', $this->pregnode->id);
            qtype_preg_author_tool_explain_graph::assume_subgraph($subpatt, $operand);

            $graph->subgraphs[] = $subpatt;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        }

        if ($id == $this->pregnode->id)
        {
            $marking = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid; color=darkgreen', 0.5 + $this->pregnode->id);
            qtype_preg_author_tool_explain_graph::assume_subgraph($marking, $graph);
            $graph->nodes = array();
            $graph->links = array();
            $graph->subgraphs = array();
            $graph->subgraphs[] = $marking;
        }
        
        return $graph;
    }
    
}

?>
