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

require_once($CFG->dirroot . '/question/type/preg/explain_graph/explain_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/explain_graph/explain_graph_misc.php');
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
    abstract public function &create_graph();

    public function accept() {
        return true;
    }
    
}

/**
 * Class for tree's leaf.
 */
class qtype_preg_author_tool_leaf extends qtype_preg_author_tool_node
{
    /**
     * Returns value of node which will be in graph. 
     */
    public function get_value() {
        switch ($this->pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CHARSET:
                if (count($this->pregnode->userinscription) > 1) {
                    $tmp = '';
                    if ($this->pregnode->negative)
                        $tmp = '^';

                    foreach ($this->pregnode->userinscription as $element)
                        $tmp .= $element;

                    return $tmp;
                }
                else {
                    if ($this->pregnode->userinscription[0] == '\d')
                        return 'Any digit';
                    elseif ($this->pregnode->userinscription[0] == '\D')
                        return 'Any character except of digit';
                    elseif ($this->pregnode->userinscription[0] == '\s')
                        return 'Any separator';
                    elseif ($this->pregnode->userinscription[0] == '\S')
                        return 'Any character except of separator';
                    elseif ($this->pregnode->userinscription[0] == '\\')
                        return '\\';
                    elseif ($this->pregnode->userinscription[0] == '\w')
                        return 'A word character';
                    elseif ($this->pregnode->userinscription[0] == '\W')
                        return 'Not a word character';
                    elseif ($this->pregnode->userinscription[0] == '\.')
                        return 'Any character';
                    else
                        return $this->pregnode->userinscription[0];
                }
            case qtype_preg_node::TYPE_LEAF_META:
                if ($this->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY)
                    return 'Void';
                else
                    return 'Unknow meta';
            case qtype_preg_node::TYPE_LEAF_ASSERT:
                if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX)
                    return 'Begining of line';
                else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR)
                    return 'End of line';
                else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B)
                    return ($this->pregnode->negative ? 'Not a word border' : 'A word border');
                else
                    return 'Unknow assert';
            case qtype_preg_node::TYPE_LEAF_BACKREF:
                return 'The result of submask #' . $this->pregnode->number;
            default:
                return 'Unknow node';
        }
    }
    
    /**
     * Returns color of node which will be in graph. 
     */
    public function get_color() {
        switch ($this->pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CHARSET:
                if (count($this->pregnode->userinscription) == 1) {
                    if ($this->pregnode->userinscription[0] == '\d' || $this->pregnode->userinscription[0] == '\.' || $this->pregnode->userinscription[0] == '\W' ||
                        $this->pregnode->userinscription[0] == '\D' || $this->pregnode->userinscription[0] == '\s' || $this->pregnode->userinscription[0] == '\S' || $this->pregnode->userinscription[0] == '\w')
                        return 'green';
                    else
                        return 'black';
                }
                else
                    return 'black';
            case qtype_preg_node::TYPE_LEAF_META:
                return 'green';
            case qtype_preg_node::TYPE_LEAF_ASSERT:
                return 'red';
            case qtype_preg_node::TYPE_LEAF_BACKREF:
                return 'blue';
            default:
                return 'pink';
        }
    }
    
    /**
     * Returns shape of node which will be in graph. 
     */
    public function get_shape() {
        if (count($this->pregnode->userinscription) > 1)
            return 'record';
        else {
            if ($this->pregnode->userinscription[0] == '\d' || $this->pregnode->userinscription[0] == '\.' || $this->pregnode->userinscription[0] == '\W' ||
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
    public function &create_graph() {
        $graph = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');

        $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node($this->get_value(), $this->get_shape(), $this->get_color(), $graph);
        $graph->entries[] = end($graph->nodes);
        $graph->exits[] = end($graph->nodes);
        
        return $graph;
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
    public function &create_graph() {
        $graph = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        
        if ($this->pregnode->type == 'node_concat') {
            $left = $this->operands[0]->create_graph();
            $right = $this->operands[1]->create_graph();

            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $left);
            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $right);

            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $left->exits[count($left->exits) - 1], $right->entries[count($right->entries) - 1]);

            $graph->entries[] = end($left->entries);
            $graph->exits[] = end($right->exits);
        }
        elseif ($this->pregnode->type == 'node_alt') {
            $left = $this->operands[0]->create_graph();
            $right = $this->operands[1]->create_graph();

            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $left);
            qtype_preg_author_tool_explain_graph::assume_subgraph($graph, $right);

            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 1], $right->entries[count($right->entries) - 1]);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 1], $left->entries[count($left->entries) - 1]);
            $graph->entries[] = end($graph->nodes);

            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $right->exits[count($left->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $left->exits[count($left->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->exits[] = end($graph->nodes);
        }
        elseif ($this->pregnode->type == 'node_finite_quant' || $this->pregnode->type == 'node_infinite_quant') {
            $operand = $this->operands[0]->create_graph();

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

            $quant = new qtype_preg_author_tool_explain_graph_subgraph($label, 'dotted');
            qtype_preg_author_tool_explain_graph::assume_subgraph($quant, $operand);

            $graph->subgraphs[] = $quant;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        }
        elseif ($this->pregnode->type == 'node_subpatt') {
            $operand = $this->operands[0]->create_graph();

            $label = 'submask #' . $this->pregnode->number;

            $subpatt = new qtype_preg_author_tool_explain_graph_subgraph($label, 'solid');
            qtype_preg_author_tool_explain_graph::assume_subgraph($subpatt, $operand);

            $graph->subgraphs[] = $subpatt;
            $graph->entries[] = end($operand->entries);
            $graph->exits[] = end($operand->exits);
        }
        
        return $graph;
    }
    
}

?>
