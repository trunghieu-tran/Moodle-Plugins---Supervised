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
        $dfanodename = 'dfa_'.$pregnode->name();
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
    abstract public function number();//replacement of old 'numeration'
    abstract public function nullable();
    abstract public function firstpos();
    abstract public function lastpos();
    abstract public function followpos(&$fpmap);
    abstract public function find_asserts();    
}

//TODO -  - implement child nodes
?>