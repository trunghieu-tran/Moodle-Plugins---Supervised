<?php
/**
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Terechov Grigory <grvlter@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_explaining_tree_node {

    // A reference to the corresponding preg_node.
    public $pregnode;

    public function __construct($node, $handler) {
        $this->pregnode = $node;
    }

    /**
     * Returns true if this node is supported by the engine, rejection string otherwise.
     */
    public function accept() {
        return true; // Accepting anything by default.
    }

    /**
     * Returns the dot script corresponding to this node.
     * @param context an instance of qtype_preg_dot_node_context.
     * @return mixed the dot script if this is the root, array(dot script, node styles) otherwise.
     */
    public abstract function dot_script($context);  // TODO: move from preg_nodes.php

    protected abstract function label();

    protected abstract function tooltip();

    protected abstract function shape();

    protected function color() {
        return 'black';
    }


}

/**
 * Class for leafs.
 */
abstract class qtype_preg_explaining_tree_leaf extends qtype_preg_explaining_tree_node {

    protected function shape() {
        return 'rectangle';
    }
    // TODO: тут может быть еще что-то полезное
}

/**
 * Class for operators.
 */
abstract class qtype_preg_explaining_tree_operator extends qtype_preg_explaining_tree_node {
    public $operands = array(); // an array of operands

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $handler->from_preg_node($operand);
        }
    }

    protected function shape() {
        return 'ellipse';
    }
}

class qtype_preg_explaining_tree_node_leaf_charset extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_meta extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_assert extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_backref extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_option extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_recursion extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_leaf_control extends qtype_preg_explaining_tree_leaf {

}

class qtype_preg_explaining_tree_node_node_finite_quant extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_node_infinite_quant extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_concat extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_node_alt extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_node_assert extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_node_subexpr extends qtype_preg_explaining_tree_operator {

}

class qtype_preg_explaining_tree_node_node_cond_subexpr extends qtype_preg_explaining_tree_operator {

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        $condbranch = $handler->from_preg_node($this->pregnode->condbranch);
        $this->operands = array_merge(array($condbranch), $this->operands);
    }
}

class qtype_preg_explaining_tree_node_node_error extends qtype_preg_explaining_tree_operator {

}

?>
