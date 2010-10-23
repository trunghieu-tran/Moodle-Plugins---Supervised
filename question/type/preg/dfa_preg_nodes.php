<?php
/**
 * Defines DFA matcher node classes with code needed to do DFA stuff
 *
 * @copyright &copy; 2010 Sychev Oleg, Kolesov Dmitriy
 * @author Sychev Oleg, Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
* Abstract class for dfa nodes. 
* Declare any necessary for every node function as absract there, optional - with empty body
*/
abstract class dfa_preg_node {

    //Instance of preg_node child class
    protected $pregnode;
    //Cashes for important data
    protected $nullable;
    protected $firstpos;
    protected $lastpos;
    protected $number;

    //TODO decide, if it could also do convert_tree job...
    public function __construct($node) {
        $this->pregnode = $node;
        //Convert operands to dfa nodes
        if (is_a($node, 'preg_operator')) {
            foreach ($node->operands as &$operand) {
                if (is_a($operand, 'preg_node')) {//Just to be sure this is not plain-data operand
                    $operand =& self::from_preg_node($operand);
                }
            }
        }
    }

    /**
    * DFA node factory
    * @param pregnode preg_node child class instance
    * @return corresponding dfa_preg_node child class instance
    */
    static public function &from_preg_node($pregnode) {
        $dfanodename = 'dfa_preg_'.$pregnode->name();
        if (class_exists($dfanodename)) {
            $dfanode = new $dfanodename($pregnode);
        } else {
            $dfanode = $pregnode;
        }
        return $dfanode;
    }


    /**
    * Return false if the node is supported by engine, interface string name to report as unsupported if not
    */
    abstract public function not_supported();

    //DFA functions
    abstract public function number(&$connection, &$maxnum);//replacement of old 'numeration'
    abstract public function nullable();
    abstract public function firstpos();
    abstract public function lastpos();
    abstract public function followpos(&$fpmap);
    abstract public function find_asserts(&$roots);
    
    //Service DFA function
    /**
    *function append array2 to array1, non unique values not add
    *@param arr1 - first array
    *@param arr2 - second array, which will appended to arr1
    */
    static public function push_unique(&$arr1, $arr2) {
        if (!is_array($arr1)) {
            $arr1 = array();
        }
        foreach ($arr2 as $value) {
            if (!in_array($value, $arr1)) {
                $arr1[] = $value;
            }
        }
    }
}

abstract class dfa_preg_leaf extends dfa_preg_node {
    public function number(&$connection, &$maxnum) {
        $this->number = ++$maxnum;
        $connection[$maxnum] = &$this;
    }
    public function nullable() {
        $this->nullable = false;
        return false;
    }
    public function firstpos() {
        $this->firstpos = array($this->number);
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = array($this->number);
    }
    public function followpos(&$fpmap) {
        ;//do nothing, because not need for leaf
    }
    public function find_asserts(&$roots) {
        ;//do nothing, because not need for leaf
    }
}
class dfa_preg_leaf_charset extends dfa_preg_leaf {
    public function not_supported() {
        return false;
    }
}
class dfa_preg_leaf_meta extends dfa_preg_leaf {
    public function not_supported() {
        return false;
    }
}
class dfa_preg_leaf_assert extends dfa_preg_leaf {
    public function not_supported() {
        return $this->pregnode->subtype != preg_leaf_assert::SUBTYPE_ESC_G;
    }
}
abstract class dfa_preg_operator extends dfa_preg_node {
    public function number(&$connection, &$maxnum) {
        foreach ($this->pregnode->operands as $key => $operand) {
            $this->pregnode->operands[$key]->number($connection, $maxnum);
        }
    }
    public function followpos(&$fpmap) {
        foreach ($this->pregnode->operands as $key=>$operand) {
            $this->pregnode->operands[$key]->followpos($fpmap);
        }
    }
    public function find_asserts(&$roots) {
        foreach ($this->pregnode->operands as $key=>$operand) {
            $this->pregnode->operands[$key]->find_asserts(&$roots);
        }
    }
}
class dfa_preg_node_concat extends dfa_preg_operator {
    public function nullable() {
        $result = true;
        foreach ($this->pregnode->operands as $operand) {
            if(!$this->pregnode->operands[$key]->nullable()) {
                $result = false;
            }
        }
        $this->nullable = $result;
        return $result;
    }
    public function firstpos() {
        $this->firstpos = $this->pregnode->operands[1]->firstpos();
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = $this->pregnode->operands[2]->lastpos();
        return $this->lastpos;
    }
    public function followpos(&$fpmap) {
        foreach ($this->pregnode->operands as $key=>$operand) {
            $this->pregnode->operands[$key]->followpos($fpmap);
        }
        foreach ($this->pregnode->operands[1]->lastpos as $key) {
            dfa_preg_node::push_unique($fpmap[$key], $this->pregnode->operands[2]->firstpos);
        }        
    }
    public function not_supported() {
        return false;
    }
}
class dfa_preg_node_alt extends dfa_preg_operator {
    public function nullable() {
        $result = false;
        foreach ($this->pregnode->operands as $operand) {
            if($this->pregnode->operands[$key]->nullable()) {
                $result = true;
            }
        }
        $this->nullable = $result;
        return $result;
    }
    public function firstpos() {
        $this->firstpos = array();
        foreach ($this->pregnode->operands as $operand) {
            $this->firstpos = array_push($this->firstpos, $this->pregnode->operands[$key]->firstpos());
        }
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = array();
        foreach ($this->pregnode->operands as $operand) {
            $this->lastpos = array_push($this->lastpos, $this->pregnode->operands[$key]->lastpos());
        }
        return $this->lastpos;
    }
    public function not_supported() {
        return false;
    }
}
class dfa_preg_node_assert extends dfa_preg_operator {
    const ASSERT_MIN_NUM = 1073741824;//it's minimum number for node with assert, for different from leafs
    
    public function not_supported() {
        if ($this->pregnode->subtype != preg_node_assert::SUBTYPE_PLA) {
            $res = false;
        } else {
            $res = true;
        }
        return $res;
    }
    public function number(&$connection, &$maxnum) {
        $this->number = ++$maxnum + dfa_preg_node_assert::ASSERT_MIN_NUM;
        $connection[$maxnum] = &$this;
    }
    public function nullable() {
        $this->nullable = true;
        return true;
    }
    public function firstpos() {
        $this->firstpos = array($this->number);
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = array($this->number);
    }
    public function followpos(&$fpmap) {
        ;//do nothing, because not need for assert
    }
    public function find_asserts(&$roots) {
        $roots[$this->number] = &$this;
    }
}
abstract class dfa_preg_node_finite_quant extends dfa_preg_operator {
    public function nullable() {
        //{} quantificators will be converted to ? and * combination
        if ($this->pregnode->leftborder == 0) {//? or *
            $result = true;
            $this->pregnode->operands[1]->nullable();
        } else {//+
            $reulst = $this->pregnode->operands[1]->nullable();
        }
        $this->nullable = $result;
        return $result;
    }
    public function firstpos() {
        $this->firstpos = $this->pregnode->operands[1]->firstpos();
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = $this->pregnode->operands[1]->lastpos();
        return $this->lastpos;
    }
    public function not_supported() {
        return false;
    }
}
class dfa_preg_node_infinite_quant extends dfa_preg_node_finite_quant {
    public function followpos(&$fpmap) {
        $this->pregnode->operands[1]->followpos($fpmap);
        foreach ($this->pregnode->operands[1]->lastpos as $lpkey) {
            dfa_preg_node::push_unique($fpmap[$lpkey], $this->pregnode->operands[1]->firstpos);
        }
    }
}
?>