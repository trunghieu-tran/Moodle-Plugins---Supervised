<?php
/**
 * Defines syntax tree tool's nodes.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

defined('MOODLE_INTERNAL') || die();

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

    public $foldcoords;
    public $isfold;

    public function __construct($handler, $isroot, $rankdirlr = false, $selection = null, $foldcoords = null, $isfold = false) {
        $this->handler = $handler;
        $this->isroot = $isroot;
        $this->rankdirlr = $rankdirlr;
        $this->selection = $selection !== null
                         ? $selection
                         : new qtype_preg_position();
        $this->foldcoords = $foldcoords;
        $this->isfold = $isfold;
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
    public function accept($options) {
        return true; // Accepting anything by default.
    }

    /**
     * Returns the name used for all graphs. The name usually follows the "digraph" keyword.
     */
    public static function get_graph_name() {
        return 'qtype_preg_tree';
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
     * Returns the dot script corresponding to this node.
     * @param context an instance of qtype_preg_dot_node_context.
     * @return mixed the dot script if this is the root, array(dot script, node styles) otherwise.
     */
    public function dot_script($context) {
        $nodename = $this->pregnode->id;
        $dotscript = $nodename . ";\n";
        $startselectioncluster = false;

        $startusercluster = !$context->insideusercluster && !$context->handler->is_node_generated($this->pregnode);
        //if($context->isfold === false) {
            $startselectioncluster = !$context->insideselectioncluster && $this->is_selected($context);
        //}

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

    protected function is_selected($context) {
        return $this->pregnode->position->indfirst >= $context->selection->indfirst &&
               $this->pregnode->position->indlast <= $context->selection->indlast;
    }

    public abstract function dot_script_inner($context);

    public abstract function label();

    public abstract function tooltip();

    public function shape() {
      return 'ellipse';
    }

    public function shape_color() {
        return count($this->pregnode->errors) > 0 ? 'red' : 'black';
    }

    public function font_color() {
        return 'black';
    }

    public function style() {
        return 'solid';
    }

    public function get_style($context) {
        $label = qtype_preg_authoring_tool::escape_characters($this->label(), array('\\', '"')) . ' ';        // Extra space to make dot happy
        $tooltip = qtype_preg_authoring_tool::escape_characters($this->tooltip(), array('\\', '"')) . ' ';    // Same thing
        $shape = $this->shape();
        $color = $this->shape_color();
        $fontcolor = $this->font_color();
        $style = $this->style();
        $id = 'treeid_' . $this->pregnode->id . '_' . $this->pregnode->position->indfirst . '_' . $this->pregnode->position->indlast;
        $result = "id = \"$id\", label = \"$label\", tooltip = \"$tooltip\", shape = \"$shape\", color = \"$color\", fontcolor = \"$fontcolor\"";
        if ($context->handler->is_node_generated($this->pregnode)) {
            $style .= ', filled';
            $result .= ', fillcolor = lightgrey';
        } else {
            $style .= ', filled';
            $result .= ', fillcolor = white';
        }
        $result .= ", style = \"$style\"";
        return '[' . $result . ']';
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

    public function label() {
        // Just concatenate userinscriptions.
        $result = '';
        foreach ($this->pregnode->userinscription as $userinscription) {
            $result .= $userinscription->data;
        }
        return $result;
    }

    public function tooltip() {
        // Leaves use description_ strings by default.
        return get_string($this->pregnode->lang_key(true), 'qtype_preg');
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

            //$currcoord = $this->pregnode->position->indfirst . ',' . $this->pregnode->position->indlast;
            if(strpos($context->foldcoords, $this->pregnode->position->indfirst . ',' . $this->pregnode->position->indlast) === false) {
                foreach ($this->operands as $operand) {
                    $newcontext = clone $context;
                    $newcontext->isroot = false;
                    $tmp = $operand->dot_script($newcontext);
                    $edgelabel = $this->label_for_edge($operand);
                    if ($edgelabel != '') {
                        $othernodename = $operand->pregnode->id;
                        $dotscript .= $nodename . '->' . $othernodename . "[label=\"$edgelabel\"];\n";
                        $dotscript .= $tmp[0];
                    } else {
                        $dotscript .= $nodename . '->' . $tmp[0];
                    }
                    $style .= $tmp[1];
                }
            } else {
                $indfirst = $this->pregnode->position->indfirst;
                $length =  $this->pregnode->position->indlast - $this->pregnode->position->indfirst + 1;
                if ($indfirst < 0) {
                    $indfirst = 0;
                }
                if ($this->pregnode->position->indlast < 0) {
                    $length = 0;
                }
                $tooltip = substr($context->handler->get_regex(),
                                    $indfirst,
                                    $length);
                $tmpcoord = "treeid_" . $this->pregnode->id . '_' . $this->pregnode->position->indfirst . '_' . $this->pregnode->position->indlast;
                $dotscript .= $nodename . "[id=\"" . $tmpcoord . "\"" . "label=\"...\", tooltip=\"" . $tooltip . "\", style=\"dotted\"];\n";
            }

            return array($dotscript, $style);
    }

    public function label() {
        return $this->pregnode->userinscription[0]->data;
    }

    public function tooltip() {
        // Operators use subtype strings instead of description_ by default.
        return get_string($this->pregnode->lang_key(false), 'qtype_preg');
    }

    /**
     * Returns a label for the edge to the given operand.
     */
    public function label_for_edge($operand) {
        return '';
    }
}

class qtype_preg_syntax_tree_leaf_charset extends qtype_preg_syntax_tree_leaf {

    public function needs_highlighting() {
        if (count($this->pregnode->userinscription) > 1) {
            return false;
        }
        $ui = $this->pregnode->userinscription[0];
        if ($ui->is_valid_escape_sequence()) {
            return false;
        }
        return $this->pregnode->is_single_dot() || $this->pregnode->is_single_non_printable_character();
    }

    public function label() {
        $ui = $this->pregnode->userinscription[0];
        if ($this->pregnode->is_single_escape_sequence_character() ||    // \a \b \n \r \t
            $this->pregnode->is_single_flag() ||                         // \w \d
            count($this->pregnode->userinscription) > 1)                 // [complex charset]
        {
            // Flag or complex charset - return "as is".
            return parent::label();
        }

        if ($this->needs_highlighting()) {
            // Something that needs to be highlighted and replaced with a lang string.
            return qtype_preg_authoring_tool::userinscription_to_string($ui);
        }
        // A single character - return the actual value.
        if (!$ui->is_valid_escape_sequence() && $ui->data != '\\' && $ui->data[0] == '\\') {
            return core_text::substr($ui->data, 1);
        }
        return $ui->data;
    }

    public function tooltip() {
        $start = 0;
        $end = count($this->pregnode->userinscription);
        $key = $this->pregnode->type;
        $delimiter = '&#10;';
        if (count($this->pregnode->errors) > 0) {
            $key .= '_error';
        } else if ($this->pregnode->negative) {
            $key .= '_neg';
        } else if ($end == 1) {
            $key .= '_one';
            $delimiter = ' ';
        }
        $tooltip = get_string($key, 'qtype_preg') . $delimiter;
        if (count($this->pregnode->userinscription) > 1) {
            $start++;
            $end--;
        }
        if ($end == 1) {
            $ui = $this->pregnode->userinscription[0];
            if ($ui->isflag !== null || $ui->is_valid_escape_sequence()) {
                $tooltip = '';
            }
        }
        // Concatenate userinscriptions.
        $delimiter = $start > 0 ? '&#10;' : ' ';
        for ($i = $start; $i < $end; $i++) {
            $ui = $this->pregnode->userinscription[$i];
            $tooltip .= qtype_preg_authoring_tool::userinscription_to_string($ui);
            if ($i != $end - 1) {
                $tooltip .= $delimiter;
            }
        }
        return $tooltip;
    }

    public function font_color() {
        if ($this->needs_highlighting()) {
            return 'blue';
        }
        return parent::font_color();
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

    public function tooltip() {
        //return get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode->number);
        $postfix = $this->pregnode->isrecursive ? '_recursive' : '';
        return get_string($this->pregnode->lang_key(true) . $postfix, 'qtype_preg', $this->pregnode->number);
    }
}

class qtype_preg_syntax_tree_leaf_subexpr_call extends qtype_preg_syntax_tree_leaf {

    /*public function label() {
        //return get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode->number);
        return $this->pregnode->userinscription;
    }*/

    public function tooltip() {
        $postfix = $this->pregnode->isrecursive ? '_recursive' : '';
        return get_string($this->pregnode->lang_key(true) . $postfix, 'qtype_preg', $this->pregnode->number);
    }
}

class qtype_preg_syntax_tree_leaf_control extends qtype_preg_syntax_tree_leaf {

}

class qtype_preg_syntax_tree_leaf_options extends qtype_preg_syntax_tree_leaf {

    public function style() {
        return 'diagonals';
    }

    public function tooltip() {
        $options = array();
        for ($i = 0; $i < $this->pregnode->posopt->length(); $i++) {
            $options[] = get_string('description_option_' . $this->pregnode->posopt[$i], 'qtype_preg');
        }
        for ($i = 0; $i < $this->pregnode->negopt->length(); $i++) {
            $options[] = get_string('description_unsetoption_' . $this->pregnode->negopt[$i], 'qtype_preg');
        }
        return implode(', ', $options);
    }
}

class qtype_preg_syntax_tree_leaf_template extends qtype_preg_syntax_tree_leaf {

    public function tooltip() {
        $available = qtype_preg\template::available_templates();
        if ($this->pregnode->name != '' && array_key_exists($this->pregnode->name, $available)) {
            return get_string('leaf_template', 'qtype_preg') . '&#10;' . get_string('description_template_' . $this->pregnode->name, 'qtype_preg');
        }

        return 'unknown template';
    }

    public function shape_color() {
        $available = qtype_preg\template::available_templates();
        if ($this->pregnode->name != '' && array_key_exists($this->pregnode->name, $available)) {
            return 'blue';
        }
        return 'red';
    }
}

class qtype_preg_syntax_tree_node_quant extends qtype_preg_syntax_tree_operator {

    public function tooltip() {
        $a = new stdClass;
        $a->leftborder = $this->pregnode->leftborder;
        if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            $a->rightborder = $this->pregnode->rightborder;
        }
        $a->greedy = get_string($this->pregnode->lang_key_for_greediness(), 'qtype_preg');
        $a->firstoperand = get_string('description_operand', 'qtype_preg');
        $result = get_string($this->pregnode->lang_key(true), 'qtype_preg', $a);
        return $result;
    }
}

class qtype_preg_syntax_tree_node_concat extends qtype_preg_syntax_tree_operator {

    public function label() {
        return '&#8226;';
    }
}

class qtype_preg_syntax_tree_node_alt extends qtype_preg_syntax_tree_operator {

}

class qtype_preg_syntax_tree_node_assert extends qtype_preg_syntax_tree_operator {

    public function style() {
        return 'dashed';
    }
}

class qtype_preg_syntax_tree_node_subexpr extends qtype_preg_syntax_tree_operator {

    public function label() {
        if ($this->pregnode->number > 0) {
            return parent::label() . " #" . $this->pregnode->number;
        }
        return parent::label();
    }

    public function tooltip() {
        $result = get_string($this->pregnode->lang_key(true), 'qtype_preg', $this->pregnode);
        $result = qtype_poasquestion\string::replace(': [ {$a->firstoperand} ]', '', $result);
        return $result;
    }
}

class qtype_preg_syntax_tree_node_cond_subexpr extends qtype_preg_syntax_tree_operator {

    public function label_for_edge($operand) {
        $count = count($this->operands);
        $shift = $this->pregnode->is_condition_assertion() ? 1 : 0;
        if ($operand === $this->operands[$shift]) {
            return core_text::strtolower(get_string('yes', 'moodle'));
        } else if ($shift + 1 < $count && $operand === $this->operands[$shift + 1]) {
            return core_text::strtolower(get_string('no', 'moodle'));
        }
        return '';
    }
}

class qtype_preg_syntax_tree_node_error extends qtype_preg_syntax_tree_operator {

    public function tooltip() {
        return $this->pregnode->error_string();
    }

    public function shape_color() {
        return 'red';
    }
}

class qtype_preg_syntax_tree_node_template extends qtype_preg_syntax_tree_operator {

    public function label() {
        return parent::label() . '...(?:###>)';
    }

    public function tooltip() {
        $available = qtype_preg\template::available_templates();
        if ($this->pregnode->name != '' && array_key_exists($this->pregnode->name, $available)) {
            return get_string('node_template', 'qtype_preg') . '&#10;' . get_string('description_template_' . $this->pregnode->name, 'qtype_preg');
        }

        return 'unknown template';
    }

    public function shape_color() {
        $available = qtype_preg\template::available_templates();
        if ($this->pregnode->name != '' && array_key_exists($this->pregnode->name, $available)) {
            return 'blue';
        }
        return 'red';
    }

    public function label_for_edge($operand) {
        if (count($this->operands) < 2) {
            return '';
        }
        $available = qtype_preg\template::available_templates();
        $parametersdescription = null;
        if ($this->pregnode->name != '' && array_key_exists($this->pregnode->name, $available)) {
            $parametersdescription = $available[$this->pregnode->name]->get_parametersdescription();
        }
        $j = array_search($operand, $this->operands);
        return $parametersdescription === null ? get_string('explain_parameter', 'qtype_preg') :  $parametersdescription[$j];
    }
}
