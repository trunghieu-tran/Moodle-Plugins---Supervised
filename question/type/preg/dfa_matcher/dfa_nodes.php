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
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
* Abstract class for dfa nodes.
* Declare any necessary for every node function as absract there, optional - with empty body
*/
abstract class qtype_preg_dfa_node {

    //Instance of qtype_preg_node child class
    public $pregnode;
    //Cashes for important data
    public $nullable;
    public $firstpos;
    public $lastpos;
    public $number;
    //data for debug print
    public $dotnumber;
    public $color;

    public function __construct($node, &$matcher) {
        $this->pregnode = $node;
        //Convert operands to dfa nodes
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key=>$operand) {
                if (is_a($node->operands[$key], 'qtype_preg_node')) {//Just to be sure this is not plain-data operand
                    $node->operands[$key] = $matcher->from_preg_node($operand);
                }
            }
        }
    }

    /**
    * returns true if engine support the node, rejection string otherwise
    */
    public function accept() {
        return true; //accepting anything by default, overload function in case of partial accepting or total rejection
    }


    /**
    *Function print indent before something
    *@param indent size of indent in count of 5 dot
    */
    public function print_indent($indent) {
        for ($i=0; $i<$indent; $i++) {
            echo '.....';
        }
    }
    /**
    *Function print the subtree with root in this node with indents
    *@param indent indent for printing info about this node
    */
    public function print_tree($indent) {
        $this->print_self($indent);
    }
    /**
    *Function print info about this node
    *@param indent indent for printing info about this node
    */
    abstract public function print_self($indent);


    /**
    *Function append dotcode for subtree with root in this node
    *@param $dotcode array for dotcode
    *@param $maxnum service param, starting of number for nodes and leafs
    */
    abstract public function generate_dot_code(&$dotcode, &$maxnum);

    /**
    *Function generate node description at language
    *@return string with node description
    */
    public function write_self_to_dotcode() {
        if (isset($this->nullable)) {
            if ($this->nullable) {
                $nullable = 'true';
            } else {
                $nullable = 'false';
            }
        } else {
            $nullable = 'NULL';
        }
        if (isset($this->firstpos)) {
            $firstpos = '';
            foreach ($this->firstpos as $pos) {
                $firstpos .= $pos.';';
            }
            $firstpos = substr($firstpos, 0, strlen($firstpos)-1);
        } else {
            $firstpos = 'NULL';
        }
        if (isset($this->lastpos)) {
            $lastpos = '';
            foreach ($this->lastpos as $pos) {
                $lastpos .= $pos.';';
            }
            $lastpos = substr($lastpos, 0, strlen($lastpos)-1);
        } else {
            $lastpos = 'NULL';
        }
        $str = $this->dotnumber.' [shape=record,style=filled,color='.$this->color.',fillcolor='.$this->color.',label="{nullable: '.$nullable.'|firstpos: '.$firstpos .
                '|lastpos: '.$lastpos;
        return $str;
    }


    //DFA functions
    /**
    *Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
    *Put pair of number=>linktoleaf to connection.
    *@param $connection table for saving connection numbers and leafs.
    *@param $maxnum maximum number of leaf, it's number of previous leaf
    */
    abstract public function number(&$connection, &$maxnum);//replacement of old 'numeration'
    /**
    *Function determine: subtree with root in this node can give empty word or not.
    *@return true if can give empty word, else false
    */
    abstract public function nullable();
    /**
    *function determine characters which can be on first position in word, which given subtree with root in this node
    *@return numbers of characters (array)
    */
    abstract public function firstpos();
    /**
    *function determine characters which can be on last position in word, which given subtree with root in this node
    *@return numbers of characters (array)
    */
    abstract public function lastpos();
    /**
    *function determine characters which can follow characters from this node
    *@param fpmap - map of following of characters
    */
    abstract public function followpos(&$fpmap);
    /**
    *function find asserts' nodes in tree and put link to root of it to $roots[<number of assert>]
    *@param node - current nod for recursive search
    */
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

abstract class qtype_preg_dfa_leaf extends qtype_preg_dfa_node {
    public function __construct($node, &$matcher) {
        parent::__construct($node, $matcher);
        $this->color = 'greenyellow';
    }
    public function number(&$connection, &$maxnum) {
        $this->number = ++$maxnum;
        $connection[$maxnum] = $this;
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
        return $this->lastpos;
    }
    public function followpos(&$fpmap) {
        ;//do nothing, because not need for leaf
    }
    public function find_asserts(&$roots) {
        ;//do nothing, because not need for leaf
    }
    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'number: ', $this->number, '<br/>';
        $this->print_indent($indent);
        if ($this->nullable) {
            echo 'nullable: true<br>';
        } else {
            echo 'nullable: false<br>';
        }
        $this->print_indent($indent);
        if (is_array($this->firstpos)) {
            $this->print_indent($indent);
            echo 'firstpos: ';
            foreach ($this->firstpos as $val) {
                echo $val, ' ';
            }
            echo '<br>';
        }
        if (is_array($this->lastpos)) {
            $this->print_indent($indent);
            echo 'lastpos: ';
            foreach ($this->lastpos as $val) {
                echo $val, ' ';
            }
            echo '<br>';
        }
    }
    public function generate_dot_code(&$dotcode, &$maxnum) {
        $this->dotnumber = ++$maxnum;
        $dotcode[] = $this->write_self_to_dotcode();
    }
}
class qtype_preg_dfa_leaf_charset extends qtype_preg_dfa_leaf {

    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: leaf charset ';
        if ($this->pregnode->negative) {
            echo 'negative';
        } else {
            echo 'positive';
        }
        echo '<br/>';
        $this->print_indent($indent);
        echo 'charset: ', $this->pregnode->charset, '<br/>';
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        if ($this->pregnode->negative) {
            $direction = 'negative';
        } else {
            $direction = 'positive';
        }
        $str .= '|CHARSET|charset: '.$this->pregnode->charset.'|'.$direction.'}"];';
        return $str;
    }
}
class qtype_preg_dfa_leaf_meta extends qtype_preg_dfa_leaf {

    const ENDREG = 186759556;
    public function number(&$connection, &$maxnum) {
        if ($this->pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_ENDREG) {
            $this->number = qtype_preg_dfa_leaf_meta::ENDREG;
            $connection[qtype_preg_dfa_leaf_meta::ENDREG] = $this;
        } else {
            parent::number($connection, $maxnum);
        }
    }

    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: leaf meta ';
        if ($this->pregnode->negative) {
            echo 'negative';
        } else {
            echo 'positive';
        }
        echo '<br/>';
        switch ($this->pregnode->subtype) {
            case qtype_preg_leaf_meta::SUBTYPE_DOT:
                $subtype = 'dot';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_UNICODE_PROP:
                $subtype = 'unicode property';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_WORD_CHAR:
                $subtype = 'word char';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_EMPTY:
                $subtype = 'empty';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_ENDREG:
                $subtype = 'endreg';
                break;
        }
        $this->print_indent($indent);
        echo 'subtype: ', $subtype, '<br/>';
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        if ($this->pregnode->negative) {
            $direction = 'negative';
        } else {
            $direction = 'positive';
        }
        switch ($this->pregnode->subtype) {
            case qtype_preg_leaf_meta::SUBTYPE_DOT:
                $subtype = 'dot';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_UNICODE_PROP:
                $subtype = 'unicode property';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_WORD_CHAR:
                $subtype = 'word char';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_EMPTY:
                $subtype = 'empty';
                break;
            case qtype_preg_leaf_meta::SUBTYPE_ENDREG:
                $subtype = 'endreg';
                break;
        }
        $str .= '|METACHARACTER|subtype: '.$subtype.'|'.$direction.'}"];';
        return $str;
    }
}
class qtype_preg_dfa_leaf_assert extends qtype_preg_dfa_leaf {
    public function accept() {
        if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_G) {
            $leafdesc = get_string($this->pregnode->name(), 'qtype_preg');
            return $leafdesc . ' \G';
        }
        return true;

    }
    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: node assert ';
        if ($this->pregnode->negative) {
            echo 'negative';
        } else {
            echo 'positive';
        }
        echo '<br/>';
        switch ($this->pregnode->subtype) {
            case qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX:
                $subtype = 'circumflex';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_DOLLAR:
                $subtype = 'dollar';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_B:
                $subtype = 'word break';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_A:
                $subtype = '\\A';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_Z:
                $subtype = '\\Z';
                break;
        }
        $this->print_indent($indent);
        echo 'subtype: ', $subtype, '<br/>';
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        if ($this->pregnode->negative) {
            $direction = 'negative';
        } else {
            $direction = 'positive';
        }
        switch ($this->pregnode->subtype) {
            case qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX:
                $subtype = 'circumflex';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_DOLLAR:
                $subtype = 'dollar';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_B:
                $subtype = 'word break';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_A:
                $subtype = '\\A';
                break;
            case qtype_preg_leaf_assert::SUBTYPE_ESC_Z:
                $subtype = '\\Z';
                break;
        }
        $str .= '|LEAF ASSERT|subtype: '.$subtype.'|'.$direction.'}"];';
        return $str;
    }
}
abstract class qtype_preg_dfa_operator extends qtype_preg_dfa_node {
    public function __construct($node, &$matcher) {
        parent::__construct($node, $matcher);
        $this->color = 'saddlebrown';
    }
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
            $this->pregnode->operands[$key]->find_asserts($roots);
        }
    }
    public function print_tree($indent) {
        parent::print_tree($indent);
        foreach ($this->pregnode->operands as $operand) {
            echo '<br/>';
            $this->print_indent($indent+1);
            echo 'OPERAND:<br/>';
            $operand->print_tree($indent+1);
        }
    }
    public function print_self($indent) {
        $this->print_indent($indent);
        if ($this->nullable) {
            echo 'nullable: true<br>';
        } else {
            echo 'nullable: false<br>';
        }
        if (is_array($this->firstpos)) {
            $this->print_indent($indent);
            echo 'firstpos: ';
            foreach ($this->firstpos as $val) {
                echo $val, ' ';
            }
            echo '<br>';
        }
        if (is_array($this->lastpos)) {
            $this->print_indent($indent);
            echo 'lastpos: ';
            foreach ($this->lastpos as $val) {
                echo $val, ' ';
            }
            echo '<br>';
        }
    }
    public function generate_dot_code(&$dotcode, &$maxnum) {
        $this->dotnumber = ++$maxnum;
        foreach ($this->pregnode->operands as $key=>$value) {
            $this->pregnode->operands[$key]->generate_dot_code($dotcode, $maxnum);
        }
        $dotcode[] = $this->write_self_to_dotcode();
        foreach ($this->pregnode->operands as $key=>$value) {
            $dotcode[] = $this->dotnumber.'->'.$this->pregnode->operands[$key]->dotnumber.'[label="'.$key.'"];';
        }
    }
}
class qtype_preg_dfa_node_concat extends qtype_preg_dfa_operator {

    public function nullable() {
        $secnull = $this->pregnode->operands[1]->nullable();
        $this->nullable = $this->pregnode->operands[0]->nullable() && $secnull;
        return $this->nullable;
    }
    public function firstpos() {
        if ($this->pregnode->operands[0]->nullable) {
            $this->firstpos = array_merge($this->pregnode->operands[0]->firstpos(), $this->pregnode->operands[1]->firstpos());
        } else {
            $this->firstpos = $this->pregnode->operands[0]->firstpos();
            $this->pregnode->operands[1]->firstpos();
        }
        return $this->firstpos;
    }
    public function lastpos() {
        if ($this->pregnode->operands[1]->nullable) {
            $this->lastpos = array_merge($this->pregnode->operands[0]->lastpos(), $this->pregnode->operands[1]->lastpos());
        } else {
            $this->lastpos = $this->pregnode->operands[1]->lastpos();
            $this->pregnode->operands[0]->lastpos();
        }
        return $this->lastpos;
    }
    public function followpos(&$fpmap) {
        parent::followpos($fpmap);
        foreach ($this->pregnode->operands[0]->lastpos as $key) {
            qtype_preg_dfa_node::push_unique($fpmap[$key], $this->pregnode->operands[1]->firstpos);
        }
    }

    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: node concatenation<br/>';
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        $str .= '|CONCATENATION}"];';
        return $str;
    }
}
class qtype_preg_dfa_node_alt extends qtype_preg_dfa_operator {

    public function nullable() {
        $firnull = $this->pregnode->operands[0]->nullable();
        $this->nullable = $firnull || $this->pregnode->operands[1]->nullable();
        return $this->nullable;
    }
    public function firstpos() {
        $this->firstpos = array_merge($this->pregnode->operands[0]->firstpos(), $this->pregnode->operands[1]->firstpos());
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = array_merge($this->pregnode->operands[0]->lastpos(), $this->pregnode->operands[1]->lastpos());
        return $this->lastpos;
    }

    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: node alternative<br/>';
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        $str .= '|ALTERNATIVE}"];';
        return $str;
    }
}
class qtype_preg_dfa_node_assert extends qtype_preg_dfa_operator {
    const ASSERT_MIN_NUM = 1073741824;//it's minimum number for node with assert, for different from leafs

    public function accept() {
        return 'Asserts temporary unsupported!';
        if ($this->pregnode->subtype!=qtype_preg_node_assert::SUBTYPE_PLA) {
            switch ($this->pregnode->subtype) {
                case qtype_preg_node_assert::SUBTYPE_NLA:
                    $res = 'nla_node_assert';
                    break;
                case qtype_preg_node_assert::SUBTYPE_PLB:
                    $res = 'plb_node_assert';
                    break;
                case qtype_preg_node_assert::SUBTYPE_NLB:
                    $res = 'nlb_node_assert';
                    break;
            }
            return get_string($res, 'qtype_preg');
        }
        return true;
    }
    public function number(&$connection, &$maxnum) {
        $this->number = ++$maxnum + dfa_preg_node_assert::ASSERT_MIN_NUM;
        $connection[$this->number] = $this;
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
        return $this->lastpos;
    }
    public function followpos(&$fpmap) {
        ;//do nothing, because not need for assert
    }
    public function find_asserts(&$roots) {
        $roots[$this->number] = $this;
    }
    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: node assert<br/>';
        switch ($this->pregnode->subtype) {
            case qtype_preg_node_assert::SUBTYPE_PLA:
                $subtype = 'PLA';
                break;
            case qtype_preg_node_assert::SUBTYPE_PLB:
                $subtype = 'PLB';
                break;
            case qtype_preg_node_assert::SUBTYPE_NLA:
                $subtype = 'NLA';
                break;
            case qtype_preg_node_assert::SUBTYPE_NLB:
                $subtype = 'NLB';
                break;
        }
        $this->print_indent($indent);
        echo 'subtype: ', $subtype, '<br/>';
        $this->print_indent($indent);
        echo 'number: ', $this->number, '<br/>';
        parent::print_self();
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        switch ($this->pregnode->subtype) {
            case qtype_preg_node_assert::SUBTYPE_PLA:
                $subtype = 'PLA';
                break;
            case qtype_preg_node_assert::SUBTYPE_PLB:
                $subtype = 'PLB';
                break;
            case qtype_preg_node_assert::SUBTYPE_NLA:
                $subtype = 'NLA';
                break;
            case qtype_preg_node_assert::SUBTYPE_NLB:
                $subtype = 'NLB';
                break;
        }
        $str .= '|ASSERT|subtype: '.$subtype.'}"];';
        return $str;
    }
}
class qtype_preg_dfa_node_infinite_quant extends qtype_preg_dfa_operator {

    public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
        return true;
    }
    public function nullable() {
        //{}quantificators will be converted to ? and * combination
        if ($this->pregnode->leftborder == 0) {//? or *
            $result = true;
            $this->pregnode->operands[0]->nullable();
        } else {//+
            $result = $this->pregnode->operands[0]->nullable();
        }
        $this->nullable = $result;
        return $result;
    }
    public function firstpos() {
        $this->firstpos = $this->pregnode->operands[0]->firstpos();
        return $this->firstpos;
    }
    public function lastpos() {
        $this->lastpos = $this->pregnode->operands[0]->lastpos();
        return $this->lastpos;
    }

    public function followpos(&$fpmap) {
        parent::followpos($fpmap);
        foreach ($this->pregnode->operands[0]->lastpos as $lpkey) {
            qtype_preg_dfa_node::push_unique($fpmap[$lpkey], $this->pregnode->operands[0]->firstpos);
        }
    }
    public function print_self($indent) {
        $this->print_indent($indent);
        if (!is_a($this, 'dfa_preg_node_finite_quant')) {
            echo 'type: node infinite quant<br/>';
        }
        $this->print_indent($indent);
        echo 'left border: ', $this->pregnode->leftborder, '<br/>';
        if (is_a($this, 'dfa_preg_node_finite_quant')) {
            $this->print_indent($indent);
            echo 'right border: ', $this->pregnode->rightborder, '<br/>';
        }
        parent::print_self($indent);
    }
    public function write_self_to_dotcode() {
        $str = dfa_preg_node::write_self_to_dotcode();
        if ($this->pregnode->greed) {
            $greedness = 'greed';
        } else {
            $greedness = 'lazy';
        }
        if (isset($this->pregnode->rightborder)) {
            $rightbordertext = '|rightborder: '.$this->pregnode->rightborder;
            $name = 'FIN QUANT';
        } else {
            $rightbordertext = '';
            $name = 'INF QUANT';
        }
        $str .= '|'.$name.'|'.$greedness.'|leftborder: '.$this->pregnode->leftborder.$rightbordertext.'}"];';
        return $str;
    }
}
class qtype_preg_dfa_node_finite_quant extends qtype_preg_dfa_node_infinite_quant {
	
	const MAX_SIZE=50;
	
    public function followpos(&$fpmap) {
        qtype_preg_dfa_operator::followpos($fpmap);
    }

    public function print_self($indent) {
        $this->print_indent($indent);
        echo 'type: node finite quant<br/>';
        parent::print_self($indent);
    }
	public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
		if ($this->pregnode->rightborder-$this->pregnode->leftborder > self::MAX_SIZE) {
			return get_string('toolargequant', 'qtype_preg');
		}
        return true;
    }
}
