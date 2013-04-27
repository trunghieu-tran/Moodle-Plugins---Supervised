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
     * Returns the name used for all graphs. The name usually follows the "digraph" keyword.
     */
    public static function get_graph_name() {
        return 'qtype_preg_graph';
    }

    /**
     * Returns heading of a dot script which is usually looks like "digraph {".
     * @param string $rankdirlr if true, also adds "rankdir = LR".
     * @return string the heading of a dot script.
     */
    public static function get_dot_head($rankdirlr = false) {
        // TODO protected
        $result = 'digraph ' . self::get_graph_name() . ' {';
        if ($rankdirlr) {
            $result .= 'rankdir = LR;';
        }
        return $result;
    }

    /**
     * Returns tail of a dot script which is usually looks like "}".
     * @return string the tail of a dot script.
     */
    public static function get_dot_tail() {
        // TODO protected
        return '}';
    }

    /**
     * Replaces non-printable and special characters in the given string.
     * Highlights them if needed.
     */
    protected static function replace_special_characters($string, $usecolor = false) {
        $special = array('&' => '&#38;',
                         '"' => '&#34;',
                         '\\\\'=> '&#92;',
                         '{' => '&#123;',
                         '}' => '&#125;',
                         '>' => '&#62;',
                         '<' => '&#60;',
                         '[' => '&#91;',
                         ']' => '&#93;',
                         ',' => '&#44;',
                         '|' => '&#124;',
                         );
        $nonprintable = array('\n' => 'description_charA',
                              '\t' => 'description_char9',
                              '\r' => 'description_charD',
                              qtype_preg_unicode::code2utf8(127) => 'description_char7F',
                              qtype_preg_unicode::code2utf8(160) => 'description_charA0',
                              qtype_preg_unicode::code2utf8(173) => 'description_charAD',
                              qtype_preg_unicode::code2utf8(8194) => 'description_char2002',
                              qtype_preg_unicode::code2utf8(8195) => 'description_char2003',
                              qtype_preg_unicode::code2utf8(8201) => 'description_char2009',
                              qtype_preg_unicode::code2utf8(8204) => 'description_char200C',
                              qtype_preg_unicode::code2utf8(8205) => 'description_char200D',
                              );

        $colors = array(true => '"blue"', false => '"green"');

        $result = $string;

        // Replace special characters without using color.
        foreach ($special as $key => $value) {
            if (qtype_preg_unicode::strpos($result, $key) !== false) {
                $result = str_replace($key, $value, $result);
            }
        }

        // TODO non-printable.

        /*$flag = true;
        for ($i = 1; $i < 33; $i++) { // until space character
            if (qtype_preg_unicode::strpos($result, qtype_preg_unicode::code2utf8($i)) !== false) {
                $color = '"blue"';
                if ($usecolor) {
                    $color = $colors[$flag];
                }
                $result = str_replace(qtype_preg_unicode::code2utf8($i), '<font color=' . $color . '>' . shorten_text(get_string('description_char' . dechex($i), 'qtype_preg'), $length) . '</font>,', $result);
                $flag = !$flag;
            }
        }

        $flag = true;
        foreach ($nonprintable as $key => $value) {
            if (qtype_preg_unicode::strpos($result, $key) !== false) {
                $color = '"blue"';
                if ($usecolor) {
                    $color = $colors[$flag];
                }
                $result = str_replace($key, '<font color=' . $color . '>' . shorten_text(get_string($value, 'qtype_preg'), $length) . '</font> ', $result);
                $flag = !$flag;
            }
        }
        $result = str_replace('\\', '', $result);*/

        return $result;
    }

    /**
     * Returns the dot script corresponding to this node.
     * @param context an instance of qtype_preg_dot_node_context.
     * @return mixed the dot script if this is the root, array(dot script, node styles) otherwise.
     */
    public abstract function dot_script($context);  // TODO: move from preg_nodes.php

    protected function get_style($context) {
        $label = $this->label();
        $tooltip = $this->tooltip();
        $shape = $this->shape();
        $color = $this->color();
        $result = "id = {$this->pregnode->id}, label = $label, tooltip = \"$tooltip\", shape = $shape, color = $color";
        if ($context->selectid == $this->pregnode->id) {
            $result .= ', style = dotted';
        }
        return '[' . $result . ']';
    }

    protected function label() {
        // Is userinscription an object?
        if (is_object($this->pregnode->userinscription)) {
            return shorten_text(self::replace_special_characters($this->pregnode->userinscription->data));
        }
        // Userinscription is an array, iterate over all objects.
        $label = '';
        foreach ($this->pregnode->userinscription as $tmp) {
            $label .= shorten_text(self::replace_special_characters($tmp->data));
        }
        return $label;
    }

    protected function tooltip() {
        return $this->label(); // TODO: this is placeholder, write real code
    }

    protected function shape() {
      return 'ellipse';
    }

    protected function color() {
        return count($this->pregnode->errors) > 0 ? 'red' : 'black';
    }
}

/**
 * Class for leafs.
 */
class qtype_preg_explaining_tree_leaf extends qtype_preg_explaining_tree_node {

    public function dot_script($context) {
        // Calculate the node name, style and the result.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ';';
        $dotscript = $nodename . ';';
        if ($context->isroot) {
            $dotscript = self::get_dot_head() . $style . $dotscript . self::get_dot_tail();
            return $dotscript;
        } else {
            return array($dotscript, $style);
        }
    }

    protected function shape() {
        return 'rectangle';
    }
    // TODO: тут может быть еще что-то полезное
}

/**
 * Class for operators.
 */
class qtype_preg_explaining_tree_operator extends qtype_preg_explaining_tree_node {

    public $operands = array(); // an array of operands

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $handler->from_preg_node($operand);
        }
    }

    public function dot_script($context) {
        // Calculate the node name and style.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ';';

        // Get child dot scripts and styles.
        $childscripts = array();

        foreach ($this->operands as $operand) {
            // Change the context to select the subtree.
            $newcontext = clone $context;
            $newcontext->isroot = false;
            if ($newcontext->selectid == $this->pregnode->id) {
                $newcontext->selectid = $operand->pregnode->id;
            }
            // Recursive call to subtree.
            $tmp = $operand->dot_script($newcontext);
            $childscripts[] = $tmp[0];
            $style .= $tmp[1];
        }

        // Form the result.
        $dotscript = '';
        foreach ($childscripts as $childscript) {
            $dotscript .= $nodename . '->' . $childscript;
        }
        if ($context->isroot) {
            $dotscript = self::get_dot_head() . $style . $dotscript . self::get_dot_tail();
            return $dotscript;
        } else {
            return array($dotscript, $style);
        }
    }
}

class qtype_preg_explaining_tree_leaf_charset extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        $result = parent::label();
        if ($this->pregnode->negative) {
            $result = '^' . $result;
        }
        if (is_array($this->pregnode->userinscription)) {
            $result = '&#91;' . $result . '&#93;';
        }
        return '<' . $result . '>';
    }

    protected function tooltip() {
        return parent::tooltip();
    }
}

class qtype_preg_explaining_tree_leaf_meta extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        return '"Emptiness"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_emptiness', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_leaf_assert extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        $label = parent::label();
        if ($label[0] === "\\") {
            $label = qtype_preg_unicode::substr($label, 1);
        }
        return '"Assertion ' . $label . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_assertion', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_leaf_backref extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        return '"Backreference to subexpression #' . $this->pregnode->number . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_backreference', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_leaf_option extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_option', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_leaf_recursion extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        return '"Recursion ' . $this->pregnode->number . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_recursion', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_leaf_control extends qtype_preg_explaining_tree_leaf {

    protected function label() {
        return '"Control sequence ' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_control_sequence', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_finite_quant extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_finite_quantifier', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_infinite_quant extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_infinite_quantifier', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_concat extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"&#8226;"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_concatenation', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_alt extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_alternative', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_assert extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"Assertion ' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_assertion', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_subexpr extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_subexpression', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_cond_subexpr extends qtype_preg_explaining_tree_operator {

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        // Add the condbranch as the first operand.
        // It simplifies the drawing process.
        if ($this->pregnode->condbranch !== null) {
            $condbranch = $handler->from_preg_node($this->pregnode->condbranch);
            $this->operands = array_merge(array($condbranch), $this->operands);
        }
    }

    protected function label() {
        return '"' . parent::label() . '"';
    }

    protected function tooltip() {
        return get_string('authoring_tool_tooltip_conditional_subexpression', 'qtype_preg');
    }
}

class qtype_preg_explaining_tree_node_error extends qtype_preg_explaining_tree_operator {

    protected function label() {
        return '"Error"';
    }

    protected function tooltip() {
        return $this->pregnode->error_string();
    }

    protected function color() {
        return 'red';
    }
}

?>
