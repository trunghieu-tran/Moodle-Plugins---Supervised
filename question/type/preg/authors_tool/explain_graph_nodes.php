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
                if (count($this->pregnode->flags) == 1)
                {
                    $result = qtype_preg_author_tool_leaf::process_flag_value($this->pregnode->flags[0]);
                    if ($result == ' ')
                        return get_string('description_char_space', 'qtype_preg');
                    elseif ($result == '	')
                        return get_string('explain_tab', 'qtype_preg');
                    elseif ($result == '"')
                        return get_string('explain_quote', 'qtype_preg');
                    elseif ($result == '\\')
                        return get_string('explain_slash', 'qtype_preg');
                    else
                        return $result;
                }
                else
                {
                    $result = array();
                    if ($this->pregnode->negative) $result[] = '^';
                    foreach ($this->pregnode->flags as $flag)
                    {
                        $result[] = qtype_preg_author_tool_leaf::process_flag_value($flag, TRUE);
                    }

                    return $result;
                }
            case qtype_preg_node::TYPE_LEAF_META:
                if ($this->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY)
                    return 'Void';
                else
                    return get_string('explain_unknow_meta', 'qtype_preg');
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
                return get_string('explain_backref', 'qtype_preg') . $this->pregnode->number;
            case qtype_preg_node::TYPE_LEAF_RECURSION:
                return get_string('explain_recursion', 'qtype_preg') . ($this->pregnode->number ? ' in #' . $this->pregnode->number : '');
            default:
                return get_string('explain_unknow_node', 'qtype_preg');
        }
    }
    
    /**
     * Returns color of node which will be in graph. 
     */
    public function get_color() {
        switch ($this->pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CHARSET:
                if (count($this->pregnode->userinscription) == 1) {
                    if ($this->pregnode->flags[0][0]->type == qtype_preg_charset_flag::FLAG || $this->pregnode->flags[0][0]->type == qtype_preg_charset_flag::UPROP || 
                    $this->pregnode->userinscription[0] == ' ' || $this->pregnode->userinscription[0] == '	' || $this->pregnode->userinscription[0] == '\\' || $this->pregnode->userinscription[0] == '"')
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
        if ($this->pregnode->type == qtype_preg_node::TYPE_LEAF_META)
            return 'ellipse';
        elseif (count($this->pregnode->userinscription) > 1)
            return 'record';
        else {
            if ($this->pregnode->userinscription[0] == '\d' || $this->pregnode->userinscription[0] == '.' || $this->pregnode->userinscription[0] == '\W' ||
                    $this->pregnode->userinscription[0] == '\D' || $this->pregnode->userinscription[0] == '\s' || $this->pregnode->userinscription[0] == '\S' || $this->pregnode->userinscription[0] == '\w')
                return 'ellipse';
            elseif (strlen($this->pregnode->userinscription[0]) > 1)
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
        
        if ($id == $this->pregnode->id)
        {
            $graph->style .= '; color=darkgreen';

            $marking = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid', 0.5 + $this->pregnode->id);
            $marking->subgraphs[] = $graph;

            $marking->entries[] = end($graph->nodes);
            $marking->exits[] = end($graph->nodes);

            return $marking;
        }
        else
        {
            $graph->entries[] = end($graph->nodes);
            $graph->exits[] = end($graph->nodes);
        }
        
        return $graph;
    }
    
    private static function process_flag_value($flag, $charclass = FALSE)
    {
        switch ($flag[0]->type)
        {
            case qtype_preg_charset_flag::SET:
                return $flag[0]->data->string();  //TODO: ranges !!!
            case qtype_preg_charset_flag::FLAG:
                $tmp = ($flag[0]->negative ? get_string('explain_not', 'qtype_preg') . get_string('description_charflag_' . $flag[0]->data, 'qtype_preg') : get_string('description_charflag_' . $flag[0]->data, 'qtype_preg'));
                return ($charclass ? chr(10) . $tmp : $tmp) ;
            case qtype_preg_charset_flag::UPROP:
                $tmp = ($flag[0]->negative ? get_string('explain_not', 'qtype_preg') . get_string('description_charflag_' . $flag[0]->data, 'qtype_preg') : get_string('description_charflag_' . $flag[0]->data, 'qtype_preg'));
                return ($charclass ? chr(10) . 'Unicode: ' . $tmp : 'Unicode: ' . $tmp) ;
            default:
                return get_string('explain_unknow_charset_flag', 'qtype_preg');
        }
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
