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
        if (is_array($pregnode->userinscription)) {
            $label = '';
            foreach ($pregnode->userinscription as $tmp) {
                $label .= $tmp;
            }
        } else {
            $label = $pregnode->userinscription;
        }
        $id = $pregnode->id;

        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT: {
                return "[label = \"abstract node\", style = dotted, color = \"blue\"]";  // это пример, замени его потом на пустую строку.
            }
            case qtype_preg_node::TYPE_LEAF_CHARSET: {
                
                //if($pregnode->flags != NULL){
                    //$flag = false;
                    //$quote = true;
                    $flag =TRUE;
                    $label = str_replace('"', '&#34;', $label);
                    $label = str_replace(' ', '<font color="blue">' . get_string('description_char_space', 'qtype_preg') . '</font>', $label);
                    $label = str_replace('	', '<font color="blue">' . get_string('description_char_t', 'qtype_preg') . '</font>', $label);
                    $label = str_replace('\\r', '<font color="blue">' . get_string('description_char_r', 'qtype_preg') . '</font>', $label);
                    $label = str_replace('\\n', '<font color="blue">' . get_string('description_char_n', 'qtype_preg') . '</font>', $label);
                    $label = str_replace('\\t', '<font color="blue">' . get_string('description_char_t', 'qtype_preg') . '</font>', $label);
                    $label = str_replace('\\d', '<font color="blue">\\d</font>', $label);
                    $label = str_replace('\\D', '<font color="blue">\\D</font>', $label);
                    $label = str_replace('\\s', '<font color="blue">\\s</font>', $label);
                    $label = str_replace('\\S', '<font color="blue">\\S</font>', $label);
                    $label = str_replace('\\w', '<font color="blue">\\w</font>', $label);
                    $label = str_replace('\\W', '<font color="blue">\\W</font>', $label);
                    $label = str_replace('\\v', '<font color="blue">\\v</font>', $label);
                    $label = str_replace('\\V', '<font color="blue">\\V</font>', $label);
                    $label = str_replace('\\h', '<font color="blue">\\h</font>', $label);
                    $label = str_replace('\\H', '<font color="blue">\\H</font>', $label);
                    
                    //TODO: implement prsing $label on error range
                    for($i=0; $i<strlen($label); $i++){
                        if($label[$i] == '-' && $i != 0 && $i != strlen($label)){
                            if(ord($label[$i-1]) > ord($label[$i+1]) ){
                                return "[label = \"$label\", tooltip = \"Incorrect range: left border is greater then the right one\", id = $id, color = \"red\"]";
                            }
                        }
                    }
                    
                    if ($pregnode->negative) {
                        $label = '[^' . $label . ']';
                        //if($flag == true){
                            $label = str_replace(']', '&#93;', $label);
                            $label = str_replace('[', '&#91;', $label);
                            $label = str_replace('\\', '&#92;', $label);
                            //$quote = false;
                        //}
                        //return "[label = \"$label\", tooltip = \"negative character class\", shape = rectangle, id = $id]";
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = record, id = $id]";
                    } else if (qtype_poasquestion_string::strlen($label) > 1 && $label != 'd' && $label != '\\D' && $label != '\\s' && $label != '\\S' && $label != '\\h' && $label != '\\H' && $label != '\\v' && $label != '\\V' && $label != '\\w' && $label != '\\W' && $label != 'space') {
                        $label = '[' . $label . ']';
                    }
                    
                    $label = str_replace(']', '&#93;', $label);
                    $label = str_replace('[', '&#91;', $label);
                    $label = str_replace('\\', '&#92;', $label);
                    //var_dump("[label = <<TABLE BORDER=\"0\" CELLBORDER=\"1\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = none, id = $id]");
                    return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = record, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_META: {
                //if($pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return "[label = \"emptiness\", tooltip = emptiness, shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_ASSERT: {
                return "[label = \"assertion $label\", tooltip = assertion, shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_BACKREF: {
                return "[label = \"backreference to subpattern #$pregnode->number\", tooltip = backreference, shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_RECURSION: {
                return "[label = \"recursion ' . $pregnode->number . '\", tooltip = recursion, shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_CONTROL: {
                return "[label = control sequence \"$label\", tooltip = \"control sequence\", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_OPTIONS: {
                return "[label = \"$label\", tooltip = option, shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT: {
                if($pregnode->leftborder > $pregnode->rightborder){
                    $a = new stdClass;
                    $a->indfirst = $pregnode->indfirst;
                    $a->indlast = $pregnode->indlast;
                    return "[label = \"$label\", tooltip = \"" . get_string('error_incorrectquantrange', 'qtype_preg', $a) . "\", id = $id, color = red]";
                }
                return "[label = \"$label\", tooltip = \"finite quantifier\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT: {
                return "[label = \"$label\", tooltip = \"infinite quantifier\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_CONCAT: {
                return "[label = \"concat\", tooltip = concatenation, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ALT: {
                return "[label = \"$label\", tooltip = alternative, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ASSERT: {
                return "[label = \"assertion $label\", tooltip = assertion, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_SUBPATT: {
                return "[label = \"$label\", tooltip = subpattern, id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_COND_SUBPATT: {
                return "[label = \"$label\", tooltip = \"conditional subpattern\", id = $id]";
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
        //$selectstyle = ', color = "blue"';
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
}
