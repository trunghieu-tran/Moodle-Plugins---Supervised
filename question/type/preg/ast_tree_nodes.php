<?php
/**
 * Defines tree's node classes.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_authors_tool.php');

/**
 * Class for both operators and leafs.
 */
abstract class qtype_preg_author_tool_tree_node {

    public $pregnode; // a reference to the corresponding preg_node
    
    public function __construct(&$node, &$handler){
        $this->pregnode = $node;
    }

    public function accept(){
        return true;
    }
    
    /**
     * Return dot instructions
     */
    abstract public function node_info_tree();
    
}

class qtype_preg_author_tool_node_leaf extends qtype_preg_author_tool_tree_node {
    
    public function node_info_tree(){
        
        $type_node;
        $subtype_node;

        switch($this->pregnode->type){
            
        case qtype_preg_node::TYPE_LEAF_CHARSET:
            //TODO: implement in the future
            if (count($this->pregnode->userinscription) > 1) {
                $tmp = '[';
                if ($this->pregnode->negative) {
                    $tmp .= '^';
                }

                foreach ($this->pregnode->userinscription as $element) {
                    $tmp .= $element;
                }

                $tmp .= ']';
                return '[label="'.$tmp.'", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
            } else {
                return '[label="'.$this->pregnode->userinscription[0].'", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            
            return '[label="Unknown node subtype"]';
            
        case qtype_preg_node::TYPE_LEAF_META:
    
            if($this->pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY){
                return'[label="EMPTY", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            /*elseIf($this->pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_ENDREG){
                return'[label=\"Unknown node subtype\"]';
            }*/
        
            return'[label="Unknown node subtype"]';
            
        case qtype_preg_node::TYPE_LEAF_ASSERT:
        
            if($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX){
                return'[label="^", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR){
                return'[label="$", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_WORDBREAK){
                return'[label="\\b", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_A){
                return'[label="\\A", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_Z){
                return'[label="\\Z", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_G){
                return'[label="\\G", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            return'[label="Unknown node subtype"]';
            
        case qtype_preg_node::TYPE_LEAF_BACKREF:
        
            //$subtype_node = $this->pregnode->number;
            return'[label="Backref(submask is '.$this->pregnode->number.')", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
        case qtype_preg_node::TYPE_LEAF_RECURSION:
        
            $subtype_node = $this->pregnode->number;//TODO: use this variable
            return'[label="RECURSION", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
        }
        
        return'[label="Unknown node type"]';
    }
}

class qtype_preg_author_tool_node_operator extends qtype_preg_author_tool_tree_node {
    
    public $operands=array();
    
    public function __construct(&$node, &$handler){
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $handler->from_preg_node($operand));
        }

    }
    public function node_info_tree(){

        switch($this->pregnode->type){
            
        case qtype_preg_node::TYPE_NODE_FINITE_QUANT:
        
            /*$left_bord=$this->pregnode->leftborder;
            $right_bord=$this->pregnode->rightborder;*/
            
            return'[label="Finite quantificator:\nleft border is '.$this->pregnode->leftborder.';\nright border is '.$this->pregnode->rightborder.'", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
        case qtype_preg_node::TYPE_NODE_INFINITE_QUANT:
        
            //$left_bord=$this->pregnode->leftborder;
            
            return'[label="Infinite quantificator:\nleft border is '.$this->pregnode->leftborder.'", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
        case qtype_preg_node::TYPE_NODE_CONCAT:
        
            return'[label="СONCAT", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
        case qtype_preg_node::TYPE_NODE_ALT:
        
            return'[label="ALTERNATIVE", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            
        case qtype_preg_node::TYPE_NODE_SUBPATT:
        
            if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT){
                return'[label="SUBPATTERN", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            elseIf($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY){
                return'[label="ONCE-ONLY SUBPATTERN", shape="square", tooltip="'.$this->pregnode->id.'", id="'.$this->pregnode->id.'"]';
            }
            return'[label="Unknown node subtype"]';
        }
        
        return'[label="Unknown node type"]';
    }
}

class qtype_preg_author_tool_explain_tree extends qtype_preg_author_tool{
    
    public function __construct ($regex = null, $modifiers = null){
        parent::__construct($regex, $modifiers);
        if ($regex === null){
            return;
        }
    }
    
    protected function get_engine_node_name($pregname){
        switch($pregname){
        case 'node_finite_quant':
        case 'node_infinite_quant':
        case 'node_concat':
        case 'node_alt':
        case 'node_subpatt':
            return'qtype_preg_author_tool_node_operator';
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return 'qtype_preg_author_tool_node_leaf';
        }

        return parent::get_engine_node_name($pregname);
    }
    
    /**
     * Create dot instruction 
     */
    public function create_dot(){        

        $dot_instructions='';//String with the instructions on the dot language
        
        //Filling array $dot_instructions
        $dot_instructions .= 'digraph "id_tree"{'.chr(10);
        $dot_instructions .= 'rankdir = LR'.chr(10);
        
        $dot_instructions .= chr(34).'node'.$this->dst_root->pregnode->id.chr(34).$this->dst_root->node_info_tree().chr(10);
        
        //if this is operator
        if($this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_CONCAT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_ALT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_ASSERT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_SUBPATT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_COND_SUBPATT
        || $this->dst_root->pregnode->type === qtype_preg_node::TYPE_NODE_ERROR) {
            
            $this->process_operator($this->dst_root, $dot_instructions);
        }
        /* else { //else if this operand
            $dot_instructions[] = '\"node'.$this->dst_root->id.'\"'.$this->dst_root->node_info_tree();
        }*/
        
        $dot_instructions .= '}';
        
        //var_dump($dot_instructions);
        echo "\n";
        return $dot_instructions;
        
    }
    
    private function process_operator(&$operator, &$dot_instructions){

        //if this is operator            
        foreach($operator->operands as $operand){
            if($operand->pregnode->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_CONCAT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_ALT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_ASSERT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_SUBPATT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_COND_SUBPATT
            || $operand->pregnode->type === qtype_preg_node::TYPE_NODE_ERROR) {

                $this->process_operator($operand, $dot_instructions);
            }
            $dot_instructions .= chr(34).'node'.$operand->pregnode->id.chr(34).$operand->node_info_tree().chr(10);
            $dot_instructions .= chr(34).'node'.$operator->pregnode->id.chr(34).'->'.chr(34).'node'.$operand->pregnode->id.chr(34).chr(10);
        }
        
    }
    
    public function get_html(){
        return true;
    }
    
    public function name(){
        return 'author_tool_explaine_tree';
    }
}
?>
