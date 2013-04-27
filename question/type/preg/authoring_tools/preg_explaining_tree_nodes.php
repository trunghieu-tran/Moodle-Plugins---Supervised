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
     * Replaces non-printable and service characters and shortens their description
     * and additional info (for example, is it a charset flag (\ w, \ d) - true or false, etc)
     * to the appropriate equivalents for the dot.
     * @param object $tmp string for label.
     * @param string $tooltip string for tooltip.
     * @param int $length string length.
     * @return modified label.
     */
    protected static function get_spec_symbol($userinscription, &$tooltip, $length, $usecolor = false) {
        if ($userinscription->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG) {
            $tooltip = $userinscription->data . '&#10;';
            $result = '<font color="blue">' . $tooltip . '</font>';
        } else {
            $result = $userinscription->data;

            // Replacement of service and non-printable characters.
            $service = array('&' => '&#38;',
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
            $nonprintable = array(qtype_preg_unicode::code2utf8(127) => 'description_char7F',
                                  qtype_preg_unicode::code2utf8(160) => 'description_charA0',
                                  qtype_preg_unicode::code2utf8(173) => 'description_charAD',
                                  qtype_preg_unicode::code2utf8(8194) => 'description_char2002',
                                  qtype_preg_unicode::code2utf8(8195) => 'description_char2003',
                                  qtype_preg_unicode::code2utf8(8201) => 'description_char2009',
                                  qtype_preg_unicode::code2utf8(8204) => 'description_char200C',
                                  qtype_preg_unicode::code2utf8(8205) => 'description_char200D',
                                  );

            $colorednonprintable = array('\n' => get_string('description_charA', 'qtype_preg'),
                                         '\t' => get_string('description_char9', 'qtype_preg'),
                                         '\r' => get_string('description_charD', 'qtype_preg')
                                         );

            $colors = array(true => '"blue"', false => '"green"');

            foreach ($service as $key => $value) {
                if (qtype_preg_unicode::strpos($result, $key) !== false) {
                    $result = str_replace($key, $value, $result);
                }
            }

            $tooltip = $result . '&#10;';

            $flag = true;
            for ($i = 1; $i < 33; $i++) {
                if (qtype_preg_unicode::strpos($result, qtype_preg_unicode::code2utf8($i)) !== false) {
                    $tooltip .= get_string('description_char' . dechex($i), 'qtype_preg') . '&#10;';
                    $color = '"blue"';
                    if ($usecolor) {
                        $color = $colors[$flag];
                    }
                    $result = str_replace(qtype_preg_unicode::code2utf8($i), '<font color=' . $color . '>' . shorten_text(get_string('description_char' . dechex($i), 'qtype_preg'), $length) . '</font>,', $result);
                    $flag = !$flag;
                }
            }

            foreach($colorednonprintable as $key => $value) {
                if (qtype_preg_unicode::strpos($result, $key) !== false) {
                    $tooltip = str_replace($key, $value, $tooltip);
                    $result = str_replace($key, '<font color="blue">' . shorten_text($value, $length) . '</font>,', $result);
                }
            }

            $flag = true;
            foreach ($nonprintable as $key => $value) {
                if (qtype_preg_unicode::strpos($result, $key) !== false) {
                    $tooltip .= get_string($value, 'qtype_preg') . '&#10;';
                    $color = '"blue"';
                    if ($usecolor) {
                        $color = $colors[$flag];
                    }
                    $result = str_replace($key, '<font color=' . $color . '>' . shorten_text(get_string($value, 'qtype_preg'), $length) . '</font> ', $result);
                    $flag = !$flag;
                }
            }
            //$tooltip = str_replace('\\\\', '\\', $tooltip);
            $tooltip = str_replace('\\', '', $tooltip);
            $result = str_replace('\\', '', $result);
        }
        return $result;
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

    public function dot_script($context) {

    }

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

    public function dot_script($context) {

    }

    protected function shape() {
        return 'ellipse';
    }
}

class qtype_preg_explaining_tree_node_leaf_charset extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_meta extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_assert extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_backref extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_option extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_recursion extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_leaf_control extends qtype_preg_explaining_tree_leaf {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_finite_quant extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_infinite_quant extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_concat extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_alt extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_assert extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_subexpr extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_cond_subexpr extends qtype_preg_explaining_tree_operator {

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        if ($this->pregnode->condbranch !== null) {
            $condbranch = $handler->from_preg_node($this->pregnode->condbranch);
            $this->operands = array_merge(array($condbranch), $this->operands);
        }
    }

    protected function label() {

    }

    protected function tooltip() {

    }
}

class qtype_preg_explaining_tree_node_node_error extends qtype_preg_explaining_tree_operator {

    protected function label() {

    }

    protected function tooltip() {

    }
}

?>
