<?php

/**
 * Defines a class which provides dot styles for different AST node types\subtypes for drawing via graphviz.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->libdir . '/moodlelib.php');

/**
 * Provides styles for node drawing using dot.
 */
class qtype_preg_dot_style_provider {

    /**
     * Returns the appropriate string for the given node.
     * @param $pregnode an abstract syntax tree node.
     * @return string which can be used as a dot instruction to set style, label etc, for example '[style - dotted, label = \"some label here\"]'.
     */
    public function get_style($pregnode) {
        // Form a label from the user inscription which can be an array of strings.
        $tmptooltip = '';
        if (is_array($pregnode->userinscription)) {
            $label = '';
            $tooltip = '';
            foreach ($pregnode->userinscription as $tmp) {
                $label .= qtype_preg_dot_style_provider::get_spec_symbol($tmp, $tmptooltip, 10);
                $tooltip .= $tmptooltip . ' ';
            }
        } else {
            $label .= qtype_preg_dot_style_provider::get_spec_symbol($pregnode->userinscription, $tmptooltip, 10);
            $tooltip .= $tmptooltip . ' ';
        }
        
        $id = $pregnode->id;
        
        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT: {
                return "[label = \"abstract node\", style = dotted, color = \"blue\"]";  // это пример, замени его потом на пустую строку.
            }
            case qtype_preg_node::TYPE_LEAF_CHARSET: {

                    if ($pregnode->negative) {
                        $label = '[^' . $label . ']';
                        $label = str_replace(']', '&#93;', $label);
                        $label = str_replace('[', '&#91;', $label);
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset', 'qtype_preg') . ": " . $tooltip . "\", shape = record, id = $id]";
                    } else if (qtype_poasquestion_string::strlen($label) > 1) {
                        $label = '[' . $label . ']';
                        //$label = '&#91;' . $label . '&#93;';
                    }

                    $label = str_replace(']', '&#93;', $label);
                    $label = str_replace('[', '&#91;', $label);
                    
                    if($pregnode->error === NULL){
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset', 'qtype_preg') . ": " . $tooltip . "\", shape = record, id = $id]";
                    } else {
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset_error', 'qtype_preg') . ": " . $tooltip . "\", shape = record, id = $id, color = \"red\"]";
                    }
            }
            case qtype_preg_node::TYPE_LEAF_META: {
                //if($pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return "[label = \"emptiness\", tooltip = " . get_string('tooltip_emptiness', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_ASSERT: {
                return "[label = \"assertion $label\", tooltip = " . get_string('tooltip_assertion', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_BACKREF: {
                return "[label = \"backreference to subpattern #$pregnode->number\", tooltip = " . get_string('tooltip_backreference', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_RECURSION: {
                return "[label = \"recursion ' . $pregnode->number . '\", tooltip = " . get_string('tooltip_recursion', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_CONTROL: {
                return "[label = control sequence \"$label\", tooltip = \"" . get_string('tooltip_control_sequence', 'qtype_preg') . "\", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_OPTIONS: {
                return "[label = \"$label\", tooltip = " . get_string('tooltip_option', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT: {
                if($pregnode->leftborder > $pregnode->rightborder){
                    $a = new stdClass;
                    $a->indfirst = $pregnode->indfirst;
                    $a->indlast = $pregnode->indlast;
                    return "[label = \"$label\", tooltip = \"" . get_string('error_incorrectquantrange', 'qtype_preg', $a) . "\", id = $id, color = red]";
                }
                return "[label = \"$label\", tooltip = \"" . get_string('tooltip_finite_quantifier', 'qtype_preg') . "\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT: {
                return "[label = \"$label\", tooltip = \"" . get_string('tooltip_infinite_quantifier', 'qtype_preg') . "\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_CONCAT: {
                return "[label = \"&#8226;\", tooltip = " . get_string('tooltip_concatenation', 'qtype_preg') . ", id = $id]";
                //return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD><font>&#8226;</font></TD></TR></TABLE>>, tooltip = concatenation, shape = record, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ALT: {
                return "[label = \"$label\", tooltip = " . get_string('tooltip_alternative', 'qtype_preg') . ", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ASSERT: {
                return "[label = \"assertion $label\", tooltip = " . get_string('tooltip_assertion', 'qtype_preg') . ", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_SUBPATT: {
                //return "[label = \"$label\", tooltip = " . get_string('tooltipe_subpattern', 'qtype_preg') . ", id = $id]";
                return "[label = \"( ... )\", tooltip = " . get_string('tooltip_subpattern', 'qtype_preg') . ", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_COND_SUBPATT: {
                return "[label = \"$label\", tooltip = \"" . get_string('tooltip_conditional_subpattern', 'qtype_preg') . "\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ERROR: {

                //return "[label = \"ERROR $label\", tooltip = error, id = $id]";
                return "[label = \"ERROR\", tooltip = \"" . $pregnode->error_string() . "\", id = $id, color = \"red\"]";
            }
            default: {
                return "[label = \"Unknown node subtype\", style = dotted]";
            }
        }
    }

    /**
     * Returns heading of a dot script which is usually looks like "digraph {".
     * @param string $rankdirlr if true, also adds "rankdir = LR".
     * @return string the heading of a dot script.
     */
    public function get_dot_head($rankdirlr = true) {
        $result = 'digraph {';
        if ($rankdirlr) {
            $result .= 'rankdir = LR;';
        }
        return $result;
    }

    /**
     * Returns tail of a dot script which is usually looks like "}".
     * @return string the tail of a dot script.
     */
    public function get_dot_tail() {
        return '}';
    }

    /**
     * Makes the subtree of the given node selected.
     * @param dotscript script with no selection.
     * @param id id of the root of the subtree to select.
     * @return modified dot script.
     */
    public function select_subtree($dotscript, $id) {
        $selectstyle = ', style = dotted';
        $stylelength = qtype_poasquestion_string::strlen($selectstyle);
        // Our dot script has the format: "[digraph][node styles][node chains].
        // First, get the chains and find subtree node id's.
        $script = new qtype_poasquestion_string($dotscript);
        $index = $script->length() - 2;
        $chainlength = 1;
        while ($script[$index - 1] !== ']') {
            $index--;
            $chainlength++;
        }
        $index++;
        $chainlength--;
        $chains = $script->substring($index, $chainlength);
        $selected = array((int)$id);
        $tmp = '';
        $write = false;
        // Look through the chains of node id's.
        for ($i = 0; $i < $chains->length(); $i++) {
            $cur = $chains[$i];
            if (ctype_digit($cur)) {
                $tmp .= $cur;
            } else if ($cur === '-') {
                // Number ready.
                if (in_array((int)$tmp, $selected)) {
                    $write = true;
                }
                if ($write) {
                    if (!in_array((int)$tmp, $selected)) {
                        $selected[] = (int)$tmp;
                    }
                }
                $tmp = '';
            } else if ($cur === ';') {
                // End of a chain.
                if ($write) {
                    if (!in_array((int)$tmp, $selected)) {
                        $selected[] = (int)$tmp;
                    }
                }
                $write = false;
                $tmp = '';
            }
        }
        // Children nodes obtained, modify their styles.
        $index = 0;
        while ($index < $script->length() - $chains->length() - 1) {
            $cur = $script[$index];
            // Skip quotes.
            if ($cur === '"') {
                $index++;
                while ($script[$index] !== '"') {
                    $index++;
                }
                $index++;
                continue;
            }
            // Get the current id.
            if ($script[$index] === ']') {
                $tmpindex = $index - 1;
                $tmpid = '';
                do {
                    $cur = $script[$tmpindex];
                    $isdigit = ctype_digit($cur);
                    if ($isdigit) {
                        $tmpid = $cur . $tmpid;
                    }
                    $tmpindex--;
                } while ($isdigit);
                // If this node should be selected, modify its style.
                if (in_array((int)$tmpid, $selected)) {
                    $part1 = $script->substring(0, $index);
                    $part2 = $script->substring($index);
                    $script = $part1;
                    $script->concatenate($selectstyle . $part2->string());
                    $index += $stylelength;
                }

            }
            $index++;
        }
        return $script->string();
    }
    
    /**
     * Replace non-printable and service character and shorten their description
     * and additional info (for example, is it a charset flag (\ w, \ d) - true or false, etc)
     * to the appropriate equivalents for the dot.
     * @param object $tmp string for label.
     * @param string $tooltip string for tooltip.
     * @param int $length string length.
     * @return modified label.
     */
    protected static function get_spec_symbol($tmp, &$tooltip, $length = 30) {
        if ($tmp->addinfo === true) {
            $tooltip = $tmp->data;
            $tmp->data = '<font color="blue">' . $tooltip . '</font>';
        } else {
            //Replacement of service characters
            if (strpos($tmp->data, '"') >= 0) {
                $tmp->data = str_replace('"', '&#34;', $tmp->data);
            }
            /*if (strpos($tmp->data, '\\') >= 0) {
                $tmp->data = str_replace('\\', '&#92;', $tmp->data);
            }*/
            if (strpos($tmp->data, '&') >= 0) {
                $tmp->data = str_replace('&', '&#38;', $tmp->data);
            }
            if (strpos($tmp->data, '{') >= 0) {
                $tmp->data = str_replace('{', '\\{', $tmp->data);
            }
            if (strpos($tmp->data, '}') >= 0) {
                $tmp->data = str_replace('}', '\\}', $tmp->data);
            }
            if (strpos($tmp->data, '>') >= 0) {
                $tmp->data = str_replace('>', '&#62;', $tmp->data);
            }
            if (strpos($tmp->data, '<') >= 0) {
                $tmp->data = str_replace('<', '&#60;', $tmp->data);
            }
            
            $tooltip = $tmp->data;            
            //Replacement of nonprinting characters
            for($i = 1; $i < 33; ++$i){
                if (strpos($tmp->data, chr($i)) !== FALSE) {
                    $tooltip = get_string('description_char' . dechex($i), 'qtype_preg');
                    $tmp->data = str_replace(chr($i), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
                }
            }
            
            if (strpos($tmp->data, chr(127)) !== FALSE) {
                $tooltip = get_string('description_char7F', 'qtype_preg');
                $tmp->data = str_replace(chr(127), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(160)) !== FALSE) {
                $tooltip = get_string('description_charA0', 'qtype_preg');
                $tmp->data = str_replace(chr(160), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(173)) !== FALSE) {
                $tooltip = get_string('description_charAD', 'qtype_preg');
                $tmp->data = str_replace(chr(173), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(8194)) !== FALSE) {
                $tooltip = get_string('description_char2002', 'qtype_preg');
                $tmp->data = str_replace(chr(8194), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(8195)) !== FALSE) {
                $tooltip = get_string('description_char2003', 'qtype_preg');
                $tmp->data = str_replace(chr(8195), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(8201)) !== FALSE) {
                $tooltip = get_string('description_char2009', 'qtype_preg');
                $tmp->data = str_replace(chr(8201), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(8204)) !== FALSE) {
                $tooltip = get_string('description_char200C', 'qtype_preg');
                $tmp->data = str_replace(chr(8204), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            if (strpos($tmp->data, chr(8205)) !== FALSE) {
                $tooltip = get_string('description_char200D', 'qtype_preg');
                $tmp->data = str_replace(chr(8205), '<font color="blue">' . shorten_text($tooltip, $length) . '</font>', $tmp->data);
            }
            
        }
        return $tmp->data;
    }
}
