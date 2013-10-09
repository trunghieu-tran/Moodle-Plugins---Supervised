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

    // Direction of the tree.
    public $rankdirlr;

    // Selection coordinates, an instance of qtype_preg_position.
    public $selection;

    public $insideusercluster = false;
    public $insideselectioncluster = false;

    public function __construct($handler, $isroot, $rankdirlr = false, $selection = null) {
        $this->handler = $handler;
        $this->isroot = $isroot;
        $this->rankdirlr = $rankdirlr;
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

    public function __construct($node, $handler) {
        $this->pregnode = $node;
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
     */
    protected static function get_dot_head($context) {
        $result = 'digraph ' . self::get_graph_name() . " {\n";
        if ($context->handler->is_node_generated($context->handler->get_ast_root())) {
            $result .= "bgcolor=lightgrey;\n";
        }
        if ($context->rankdirlr) {
            $result .= 'rankdir = LR;';
        }
        return $result;
    }

    protected static function get_user_cluster_head() {
        return "subgraph cluster_user { color=invis; bgcolor=white;\n";
    }

    protected static function get_sel_cluster_head() {
        return "subgraph cluster_sel { style=solid; color=darkgreen;\n";
    }

    /**
     * Returns tail of a dot script which is usually looks like "}".
     * @return string the tail of a dot script.
     */
    protected static function get_dot_tail() {
        return '}';
    }

    /**
     * Replaces non-printable and special characters in the given string.
     * Highlights them if needed.
     */
    protected static function userinscription_to_string($userinscription, $negative, $fortooltip) {
        if ($userinscription->type === qtype_preg_userinscription::TYPE_FLAG) {
            if (!$fortooltip) {
                return $userinscription->data != '.'
                           ? $userinscription->data
                           : get_string('description_charflag_dot', 'qtype_preg');
            } else if ($userinscription->data != '.') {
                return get_string('description_charflag_slash' . textlib::strtolower($userinscription->data[1]), 'qtype_preg');
            } else {
                return get_string('description_charflag_dot', 'qtype_preg');
            }
        }

        $special = array('&' => '&#38;',
                         '"' => '&#34;',
                         '[' => '&#91;',
                         ']' => '&#93;',
                         ',' => '&#44;',
                         '<' => '&#60;',
                         '>' => '&#62;',
                         //'[' => '&#91;',
                         //']' => '&#93;',
                         '{' => '&#123;',
                         '|' => '&#124;',
                         '}' => '&#125;',
                         '\\\\'=> '&#92;'
                         );

        for ($code = 1; $code <= 0x20; $code++) {
            $replacement = get_string('description_char' . textlib::strtoupper(dechex($code)), 'qtype_preg');
            if (!$fortooltip) {
                $replacement = '<font color="blue">' . $replacement . '</font>';
            }
            $special[qtype_preg_unicode::code2utf8($code)] = $replacement;
        }
        foreach (array(0x7F, 0xA0, 0xAD, 0x2002, 0x2003, 0x2009, 0x200C, 0x200D) as $code) {
            $replacement = get_string('description_char' . textlib::strtoupper(dechex($code)), 'qtype_preg');
            if (!$fortooltip) {
                $replacement = '<font color="blue">' . $replacement . '</font>';
            }
            $special[qtype_preg_unicode::code2utf8($code)] = $replacement;
        }

        $result = $userinscription->data;

        foreach ($special as $key => $value) {
            $result = str_replace($key, $value, $result);
        }

        if (textlib::strpos($result, '\x') === false) {
            $result = qtype_poasquestion_string::replace('\\', '', $result);
        }
        return $result;
    }

    /**
     * Returns the dot script corresponding to this node.
     * @param context an instance of qtype_preg_dot_node_context.
     * @return mixed the dot script if this is the root, array(dot script, node styles) otherwise.
     */
    public function dot_script($context) {
        $nodename = $this->pregnode->id;
        $dotscript = $nodename . ";\n";

        $startusercluster = !$context->insideusercluster && !$context->handler->is_node_generated($this->pregnode);
        $startselectioncluster = !$context->insideselectioncluster && $this->is_selected($context);
        $context->insideusercluster = $context->insideusercluster || $startusercluster;
        $context->insideselectioncluster = $context->insideselectioncluster || $startselectioncluster;
        if ($startusercluster) {
            $dotscript .= self::get_user_cluster_head();
        }
        if ($startselectioncluster) {
            $dotscript .= self::get_sel_cluster_head();
        }

        $innerresult = $this->dot_script_inner($context);
        $dotscript .= $innerresult[0];
        $style = $innerresult[1];

        if ($startusercluster) {
            $dotscript .= self::get_dot_tail();
        }
        if ($startselectioncluster) {
            $dotscript .= self::get_dot_tail();
        }

        if ($context->isroot) {
            return self::get_dot_head($context) . $style . $dotscript . self::get_dot_tail();
        } else {
            return array($dotscript, $style);
        }
    }

    public abstract function dot_script_inner($context);

    protected function is_selected($context) {
        return $this->pregnode->position->indfirst >= $context->selection->indfirst &&
               $this->pregnode->position->indlast <= $context->selection->indlast;
    }

    public function get_style($context) {
        $label = $this->label();
        $tooltip = $this->tooltip();
        $shape = $this->shape();
        $color = $this->color();
        $style = $this->style();
        $id = $this->pregnode->id . ',' . $this->pregnode->position->indfirst . ',' . $this->pregnode->position->indlast;
        $result = "id = \"$id\", label = <$label>, tooltip = \"$tooltip\", shape = $shape, color = $color";
        if ($context->handler->is_node_generated($this->pregnode)) {
            $style .= ', filled';
            $result .= ', fillcolor = lightgrey';
        }
        $result .= ", style = \"$style\"";

        return '[' . $result . ']';
    }

    public function label() {
        $label = '';
        $count = count($this->pregnode->userinscription);
        foreach ($this->pregnode->userinscription as $i => $userinscription) {
            $label .= shorten_text(self::userinscription_to_string($userinscription, false, false));
            if ($i != $count - 1) {
                $label .= '&#10;';
            }
        }
        return $label;
    }

    public function tooltip() {
        $result = '';
        if ($this->pregnode->subtype != '') {
            $result = get_string($this->pregnode->subtype, 'qtype_preg');
        }
        if ($result == '') {
            $result = get_string($this->pregnode->type, 'qtype_preg');
        }
        return $result;
    }

    public function shape() {
      return 'ellipse';
    }

    public function color() {
        return count($this->pregnode->errors) > 0 ? 'red' : 'black';
    }

    public function style() {
        return 'solid';
    }
}

/**
 * Class for leafs.
 */
class qtype_preg_syntax_tree_leaf extends qtype_preg_syntax_tree_node {

    public function dot_script_inner($context) {
        // Calculate the node name, style and the result.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ";\n";
        $dotscript = $nodename . ";\n";
        return array($dotscript, $style);
    }

    public function shape() {
        return 'rectangle';
    }
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

    public function dot_script_inner($context) {
        // Calculate the node name and style.
        $nodename = $this->pregnode->id;
        $style = $nodename . self::get_style($context) . ";\n";
        $dotscript = $nodename . ";\n";
        foreach ($this->operands as $operand) {
            $newcontext = clone $context;
            $newcontext->isroot = false;
            $tmp = $operand->dot_script($newcontext);
            $dotscript .= $nodename . '->' . $tmp[0];
            $style .= $tmp[1];
        }
        return array($dotscript, $style);
    }
}

class qtype_preg_syntax_tree_leaf_charset extends qtype_preg_syntax_tree_leaf {

    public function tooltip() {
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
        if (count($this->pregnode->userinscription) > 1) {
            $start++;
            $end--;
        } else if ($end == 1 && $this->pregnode->userinscription[0]->type == qtype_preg_userinscription::TYPE_FLAG) {
            $tooltip = '';
        }
        for ($i = $start; $i < $end; ++$i) {
            if ($tooltip != '') {
                $tooltip .= ($start > 0) ? '&#10;' : ' ';
            }
            $tooltip .= self::userinscription_to_string($this->pregnode->userinscription[$i], false, true);
        }
        return $tooltip;
    }
}

class qtype_preg_syntax_tree_leaf_meta extends qtype_preg_syntax_tree_leaf {

    public function label() {
        return get_string($this->pregnode->subtype, 'qtype_preg');
    }
}

class qtype_preg_syntax_tree_leaf_assert extends qtype_preg_syntax_tree_leaf {

    public function style() {
        return 'dashed';
    }
}

class qtype_preg_syntax_tree_leaf_backref extends qtype_preg_syntax_tree_leaf {

    public function style() {
        return 'rounded';
    }

    public function label() {
        return '&#92;&#92; ' . $this->pregnode->number;
    }

    public function tooltip() {
        return get_string('description_' . $this->pregnode->subtype, 'qtype_preg', $this->pregnode->number);
    }
}

class qtype_preg_syntax_tree_leaf_recursion extends qtype_preg_syntax_tree_leaf {

    public function label() {
        return get_string('description_' . $this->pregnode->subtype, 'qtype_preg', $this->pregnode->number);
    }
}

class qtype_preg_syntax_tree_leaf_control extends qtype_preg_syntax_tree_leaf {

}

class qtype_preg_syntax_tree_leaf_options extends qtype_preg_syntax_tree_leaf {

    public function style() {
        return 'diagonals';
    }

    public function tooltip() {
        return parent::tooltip() . ' \\"' . get_string("description_option_" . $this->pregnode->posopt, 'qtype_preg') . '\\"';
    }
}

class qtype_preg_syntax_tree_node_finite_quant extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_infinite_quant extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_concat extends qtype_preg_syntax_tree_operator {

    public function label() {
        return '&#8226;';
    }
}

class qtype_preg_syntax_tree_node_alt extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_assert extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_subexpr extends qtype_preg_syntax_tree_operator {

    public function label() {
        if ($this->pregnode->number > 0) {
            return parent::label() . " #" . $this->pregnode->number;
        }
        return parent::label();
    }

    public function tooltip() {
        if ($this->pregnode->number > 0) {
            return parent::tooltip() . " #" . $this->pregnode->number;
        }
        return parent::tooltip();
    }
}

class qtype_preg_syntax_tree_node_cond_subexpr extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_error extends qtype_preg_syntax_tree_operator {

    public function label() {
        return get_string($this->pregnode->type, 'qtype_preg');
    }

    public function tooltip() {
        return $this->pregnode->error_string();
    }

    public function color() {
        return 'red';
    }
}

?>
