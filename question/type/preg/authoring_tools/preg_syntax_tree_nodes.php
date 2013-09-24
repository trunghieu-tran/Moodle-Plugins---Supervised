<?php
/**
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Context for nodes drawing.
 */
class qtype_preg_dot_node_context {

    // Reference to the handler.
    public $handler;

    // Whether the node is the root or not.
    public $isroot;

    // Selection coordinates, an instance of qtype_preg_position.
    public $selection;

    public function __construct($handler, $isroot, $selection = null) {
        $this->handler = $handler;
        $this->isroot = $isroot;
        $this->selection = $selection !== null
                         ? $selection
                         : new qtype_preg_position();
    }
}

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_syntax_tree_node {

    // A reference to the corresponding preg_node.
    public $pregnode;
    public static $clasterscript = '';

    public function __construct($node, $handler) {
        $this->pregnode = $node;
        //self::clasterscript = '';
    }

    /**
     * Returns true if this node is supported, rejection string otherwise.
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
    protected static function userinscription_to_string($userinscription, $fortooltip) {
        if ($userinscription->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG) {
            if (!$fortooltip) {
                return '<font color="blue">' .
                            ($userinscription->data != '.'
                            ? $userinscription->data
                            : get_string('description_charflag_dot', 'qtype_preg')) .
                       '</font>';
                //return '<font color="blue">' . $userinscription->data . '</font>';
            } else if ($userinscription->data != '.') {
                return get_string('description_charflag_slash' . $userinscription->data[1], 'qtype_preg');
            } else {
                return get_string('description_charflag_dot', 'qtype_preg');
            }
        }

        $special = array('"' => '&#34;',
                         '&' => '&#38;',
                         ',' => '&#44;',
                         '<' => '&#60;',
                         '>' => '&#62;',
                         '[' => '&#91;',
                         ']' => '&#93;',
                         '{' => '&#123;',
                         '|' => '&#124;',
                         '}' => '&#125;',
                         '\\\\'=> '&#92;'
                         );

        for ($code = 1; $code <= 0x20; $code++) {
            $replacement = get_string('description_char' . strtoupper(dechex($code)), 'qtype_preg');
            if (!$fortooltip) {
                $replacement = '<font color="blue">' . $replacement . '</font>';
            }
            $special[qtype_preg_unicode::code2utf8($code)] = $replacement;
        }
        foreach (array(0x7F, 0xA0, 0xAD, 0x2002, 0x2003, 0x2009, 0x200C, 0x200D) as $code) {
            $replacement = get_string('description_char' . strtoupper(dechex($code)), 'qtype_preg');
            if (!$fortooltip) {
                $replacement = '<font color="blue">' . $replacement . '</font>';
            }
            $special[qtype_preg_unicode::code2utf8($code)] = $replacement;
        }

        $result = $userinscription->data;

        foreach ($special as $key => $value) {
            $result = str_replace($key, $value, $result);
        }

        if (strpos($result, '\x') === false) {
            $result = str_replace('\\', '', $result);
        }
        return $result;
    }

    /**
     * Returns the dot script corresponding to this node.
     * @param context an instance of qtype_preg_dot_node_context.
     * @return mixed the dot script if this is the root, array(dot script, node styles) otherwise.
     */
    public abstract function dot_script($context);

    protected function get_style($context) {
        $label = $this->label();
        $tooltip = $this->tooltip();
        $shape = $this->shape();
        $color = $this->color();
        $style = $this->style();
        $id = $this->pregnode->id . ',' . $this->pregnode->position->indfirst . ',' . $this->pregnode->position->indlast;
        $result = "id = \"$id\", label = $label, tooltip = \"$tooltip\", shape = $shape, color = $color";
        if ($this->pregnode->position->indfirst >= $context->selection->indfirst &&
            $this->pregnode->position->indlast <= $context->selection->indlast) {
            self::$clasterscript .= $this->pregnode->id . ';';
        }
        if ($context->handler->is_node_generated($this->pregnode)) {
            $style .= ', filled';
            $result .= ', fillcolor = lightgrey';
        }
        $result .= ", style = \"$style\"";


        //var_dump($result);
        return '[' . $result . ']';
    }

    protected function label() {
        $label = '';
        foreach ($this->pregnode->userinscription as $userinscription) {
            $label .= shorten_text(self::userinscription_to_string($userinscription, false)) . '&#10;';
        }
        return $label;
    }

    protected function tooltip() {
        // Almost all nodes use its type as string key.
        return get_string($this->pregnode->type, 'qtype_preg');
    }

    protected function shape() {
      return 'ellipse';
    }

    protected function color() {
        return count($this->pregnode->errors) > 0 ? 'red' : 'black';
    }

    protected function style() {
        return 'solid';
    }

    protected function get_claster_head() {
        return ' subgraph cluster_1 { style=solid; color=darkgreen; ';
    }
}

/**
 * Class for leafs.
 */
class qtype_preg_syntax_tree_leaf extends qtype_preg_syntax_tree_node {

    public function dot_script($context, $rankdirlr = false) {
        // Calculate the node name, style and the result.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ';';
        $dotscript = $nodename . ';';
        if ($context->isroot) {
            $dotscript1 = self::get_dot_head($rankdirlr) . $style;
            if (parent::$clasterscript !== '') {
                $dotscript1 .= parent::get_claster_head() .  parent::$clasterscript . self::get_dot_tail();
            }
            $dotscript1 .= $dotscript . self::get_dot_tail();
/*
            var_dump($dotscript1);
            var_dump(parent::$clasterscript);
*/
            return $dotscript1;
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
class qtype_preg_syntax_tree_operator extends qtype_preg_syntax_tree_node {

    public $operands = array(); // an array of operands

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $handler->from_preg_node($operand);
        }
    }

    public function dot_script($context, $rankdirlr = false) {
        // Calculate the node name and style.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ';';

        // Get child dot scripts and styles.
        $childscripts = array();

        foreach ($this->operands as $operand) {
            // Change the context to select the subtree.
            $newcontext = clone $context;
            $newcontext->isroot = false;
            // Recursive call to subtree.
            $tmp = $operand->dot_script($newcontext, $rankdirlr);
            $childscripts[] = $tmp[0];
            $style .= $tmp[1];
        }

        // Form the result.
        $dotscript = $nodename . ';';
        foreach ($childscripts as $childscript) {
            $dotscript .= $nodename . '->' . $childscript;
        }
        if ($context->isroot) {
            $dotscript1 = self::get_dot_head($rankdirlr) . $style;
            if (parent::$clasterscript !== '') {
                $dotscript1 .= parent::get_claster_head() .  parent::$clasterscript . self::get_dot_tail();
            }
            $dotscript1 .= $dotscript . self::get_dot_tail();
/*
            var_dump($dotscript1);
            var_dump(parent::$clasterscript);
*/
            return $dotscript1;
        } else {
            return array($dotscript, $style);
        }
    }
}

class qtype_preg_syntax_tree_leaf_charset extends qtype_preg_syntax_tree_leaf {

    protected function label() {
        $label = '';
        foreach ($this->pregnode->userinscription as $userinscription) {
            $label .= shorten_text(self::userinscription_to_string($userinscription, false)) . '&#10;';
        }
        if ($this->pregnode->negative) {
            $label = '<font color="blue">' . get_string('leaf_charset_except', 'qtype_preg') . '</font> ' . $label;
        }
        return '<' . $label . '>';
    }

    protected function tooltip() {
        $start = 0;
        $end = count($this->pregnode->userinscription);
        if (count($this->pregnode->errors) > 0) {
            $tooltip = get_string($this->pregnode->type . '_error', 'qtype_preg');
        } else if ($this->pregnode->negative) {
            $tooltip = get_string($this->pregnode->type . '_negative', 'qtype_preg');
        } else if ($end == 1) {
            $tooltip = get_string($this->pregnode->type . '_one', 'qtype_preg');
        } else {
            $tooltip = get_string($this->pregnode->type, 'qtype_preg');
        }
        if ($end > 2 && $this->pregnode->userinscription[$end - 1] == ']' ) {
            $start++;
            $end--;
        } else if ($end == 1 && $this->pregnode->userinscription[0]->type == qtype_preg_userinscription::TYPE_CHARSET_FLAG) {
            $tooltip = '';
        }
        for ($i = $start; $i < $end; ++$i) {
            if ($tooltip != '') {
                $tooltip .= ($start > 0) ? '&#10;' : ' ';
            }
            $tooltip .= self::userinscription_to_string($this->pregnode->userinscription[$i], true);
        }
        return $tooltip;
    }
}

class qtype_preg_syntax_tree_leaf_meta extends qtype_preg_syntax_tree_leaf {

    protected function label() {
        return '"' . get_string($this->pregnode->subtype, 'qtype_preg') . '"';
    }

    protected function tooltip() {
        return get_string($this->pregnode->subtype, 'qtype_preg');
    }
}

class qtype_preg_syntax_tree_leaf_assert extends qtype_preg_syntax_tree_leaf {

    protected function style() {
        return 'dashed';
    }

    protected function label() {
        return '"' . get_string($this->pregnode->subtype, 'qtype_preg') . '"';
    }

    protected function tooltip() {
        return get_string($this->pregnode->subtype, 'qtype_preg');
    }
}

class qtype_preg_syntax_tree_leaf_backref extends qtype_preg_syntax_tree_leaf {

    protected function style() {
        return 'rounded';
    }

    protected function label() {
        $a = new stdClass;
        $a->number = $this->pregnode->number;
        return '"' . get_string('description_backref', 'qtype_preg', $a) . '"';
    }
}

class qtype_preg_syntax_tree_leaf_options extends qtype_preg_syntax_tree_leaf {

    protected function style() {
        return 'diagonals';
    }

    protected function label() {
        return '"' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_leaf_recursion extends qtype_preg_syntax_tree_leaf {

    protected function label() {
        return '"' . get_string('leaf_recursion', 'qtype_preg') . ' ' . $this->pregnode->number . '"';
    }
}

class qtype_preg_syntax_tree_leaf_control extends qtype_preg_syntax_tree_leaf {

    protected function label() {
        return '"' . get_string('leaf_control', 'qtype_preg') . ' ' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_finite_quant extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_infinite_quant extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_concat extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"&#8226;"';
    }
}

class qtype_preg_syntax_tree_node_alt extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_assert extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . get_string($this->pregnode->subtype, 'qtype_preg') . ' ' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_subexpr extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . parent::label() . '"';
    }
}

class qtype_preg_syntax_tree_node_cond_subexpr extends qtype_preg_syntax_tree_operator {

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
}

class qtype_preg_syntax_tree_node_error extends qtype_preg_syntax_tree_operator {

    protected function label() {
        return '"' . get_string('node_error', 'qtype_preg') . '"';
    }

    protected function tooltip() {
        return $this->pregnode->error_string();
    }

    protected function color() {
        return 'red';
    }
}

?>
