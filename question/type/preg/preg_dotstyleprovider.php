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
 * Defines a class which provides dot styles for different AST node types\subtypes for drawing via graphviz.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>, Terechov Grigory <grvlter@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');
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
        $label = '';
        $tooltip = '';
        $bracketflag = true;//true if need [...]
        if (is_array($pregnode->userinscription)) {
            //$flag = true;
            //$colors = array(true=>'blue', false=>'green');
            $countiters = 0;
            foreach ($pregnode->userinscription as $tmp) {
                $countiters++;
                if((strlen($tmp->data) === 1 || $tmp->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG || $tmp->data === '\\\\') && $countiters < 2){
                    $bracketflag = false;
                } else {
                    $bracketflag = true;
                }
            
                $label .= qtype_preg_dot_style_provider::get_spec_symbol($tmp, $tmptooltip, 7);                
                //$flag = !$flag;
                //$color = $colors[$flag];
                //var_dump($colors[$flag]);
                //$label .= qtype_preg_dot_style_provider::get_spec_symbol_with_color($tmp, $tmptooltip, 7, $colors[$flag]);
                $tooltip .= $tmptooltip;// . '&#10;';
                $tmptooltip = '';
            }
        } else {
            //var_dump('1');
            /*if(strlen($tmp->data) === 2){
                    $bracketflag = false;
            }*/
            $label = qtype_preg_dot_style_provider::get_spec_symbol($pregnode->userinscription, $tmptooltip, 7);
            //$label = qtype_preg_dot_style_provider::get_spec_symbol_with_color($pregnode->userinscription, $tmptooltip, 7);
            $tooltip = $tmptooltip;
        }

        $id = $pregnode->id;

        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT: {
                return "[label = \"abstract node\", style = dotted, color = \"blue\"]";  // это пример, замени его потом на пустую строку.
            }
            case qtype_preg_node::TYPE_LEAF_CHARSET: {
            
                    $tooltip = substr($tooltip, 0, strlen($tooltip)-5);
                    
                    if (strrpos($label, ',') === (strlen($label)-1) && strlen($label) > 0) {
                            $label[strlen($label)-1] = '\0';
                    }
                    
                    if ($pregnode->negative) {
                        $label = '&#91;^' . $label . '&#93;';
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset', 'qtype_preg') . ": " . $tooltip . " \", shape = record, id = $id]";
                    } else if (qtype_poasquestion_string::strlen($label) > 1 && $bracketflag) {
                        $label = '&#91;' . $label;
                        //delete last comma
                        /*if (strrpos($label, ',') === (strlen($label)-1) && strlen($label) > 0) {
                            $label[strlen($label)-1] = '&';
                            $label .= '#93;';
                        } else {*/
                            $label .= '&#93;';
                        //}
                    }
                    
                    
                        
                    //var_dump($label);
                    if($pregnode->error === NULL){
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset', 'qtype_preg') . ": " . $tooltip . " \", shape = record, id = $id]";
                    } else {
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"" . get_string('tooltip_charset_error', 'qtype_preg') . ": " . $tooltip . " \", shape = record, color = red, id = $id]";
                    }
            }
            case qtype_preg_node::TYPE_LEAF_META: {
                //if($pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return "[label = \"emptiness\", tooltip = " . get_string('tooltip_emptiness', 'qtype_preg') . ", shape = rectangle, id = $id]";
            }
            case qtype_preg_node::TYPE_LEAF_ASSERT: {
                if($label[0] === "\\"){
                    $label = substr($label, 1);
                }
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
                if ($pregnode->error !== null){
                    return "[label = \"$label\", tooltip = \"" . get_string('error_incorrectquantrange', 'qtype_preg', $pregnode->error) . "\", color = red, id = $id]";
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
                return "[label = \"$label\", tooltip = " . get_string('tooltip_subpattern', 'qtype_preg') . ", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_COND_SUBPATT: {
                return "[label = \"$label\", tooltip = \"" . get_string('tooltip_conditional_subpattern', 'qtype_preg') . "\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_ERROR: {
                return "[label = \"ERROR\", tooltip = \"" . $pregnode->error_string() . "\", color = red, id = $id]";
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
     * Replaces non-printable and service characters and shortens their description
     * and additional info (for example, is it a charset flag (\ w, \ d) - true or false, etc)
     * to the appropriate equivalents for the dot.
     * @param object $tmp string for label.
     * @param string $tooltip string for tooltip.
     * @param int $length string length.
     * @return modified label.
     */
    protected static function get_spec_symbol($userinscription, &$tooltip, $length = 30) {
        if ($userinscription->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG) {
            $tooltip = $userinscription->data . '&#10;';
            $result = '<font color="blue">' . $tooltip . '</font>';
        } else {
            // Replacement of service and non-printable characters.
            $service = array('"' => '&#34;',
                             //'\\'=> '&#92;',
                             '&' => '&#38;',
                             '{' => '&#123;',
                             '}' => '&#125;',
                             '>' => '&#62;',
                             '<' => '&#60;',
                             '[' => '&#91;',
                             ']' => '&#93;',
                             ',' => '&#44;',
                             '|' => '&#124;',
                             //'\\\\' => '\\'
                             );
            $nonprintable = array(qtype_poasquestion_string::code2utf8(127) => 'description_char7F',
                                  qtype_poasquestion_string::code2utf8(160) => 'description_charA0',
                                  qtype_poasquestion_string::code2utf8(173) => 'description_charAD',
                                  qtype_poasquestion_string::code2utf8(8194) => 'description_char2002',
                                  qtype_poasquestion_string::code2utf8(8195) => 'description_char2003',
                                  qtype_poasquestion_string::code2utf8(8201) => 'description_char2009',
                                  qtype_poasquestion_string::code2utf8(8204) => 'description_char200C',
                                  qtype_poasquestion_string::code2utf8(8205) => 'description_char200D'
                                  );
            $result = $userinscription->data;
            foreach ($service as $key => $value) {
                if (qtype_poasquestion_string::strpos($result, $key) !== false) {
                    $result = str_replace($key, $value, $result);
                }
            }
            $tooltip = $result . '&#10;';
            for ($i = 1; $i < 33; $i++) {
                if (qtype_poasquestion_string::strpos($result, chr($i)) !== false) {
                    $tooltip .= get_string('description_char' . dechex($i), 'qtype_preg') . '&#10;';
                    $result = str_replace(chr($i), '<font color="blue">' . shorten_text(get_string('description_char' . dechex($i), 'qtype_preg'), $length) . '</font>,', $result);
                }
            }
            foreach ($nonprintable as $key => $value) {
                if (qtype_poasquestion_string::strpos($result, $key) !== false) {
                    $tooltip .= get_string($value, 'qtype_preg') . '&#10;';
                    $result = str_replace($key, '<font color="blue">' . shorten_text(get_string($value, 'qtype_preg'), $length) . '</font>,', $result);
                }
            }
            $tooltip = str_replace('\\\\', '\\', $tooltip);
            $result = str_replace('\\\\', '&#92;', $result);
            //var_dump($result);
        }
        return $result;
    }
    
    protected static function get_spec_symbol_with_color($userinscription, &$tooltip, $length = 30, $color = 'blue') {
        if ($userinscription->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG) {
            $tooltip = $userinscription->data . '&#10;';
            $result = '<font color="blue">' . $tooltip . '</font>';
            //$result = '<font color="' . $color . '">' . $tooltip . '</font>';
        } else {
            // Replacement of service and non-printable characters.
            $service = array('"' => '&#34;',
                             //'\\' '&#92;',
                             '&' => '&#38;',
                             '{' => '\\{',
                             '}' => '\\}',
                             '>' => '&#62;',
                             '<' => '&#60;',
                             '[' => '&#91;',
                             ']' => '&#93;',
                             ',' => '&#44;'
                             );
            $nonprintable = array(qtype_poasquestion_string::code2utf8(127) => 'description_char7F',
                                  qtype_poasquestion_string::code2utf8(160) => 'description_charA0',
                                  qtype_poasquestion_string::code2utf8(173) => 'description_charAD',
                                  qtype_poasquestion_string::code2utf8(8194) => 'description_char2002',
                                  qtype_poasquestion_string::code2utf8(8195) => 'description_char2003',
                                  qtype_poasquestion_string::code2utf8(8201) => 'description_char2009',
                                  qtype_poasquestion_string::code2utf8(8204) => 'description_char200C',
                                  qtype_poasquestion_string::code2utf8(8205) => 'description_char200D'
                                  );
            $flag = true;
            $colors = array(true=>'blue', false=>'green');
            
            $result = $userinscription->data;
            foreach ($service as $key => $value) {
                if (qtype_poasquestion_string::strpos($result, $key) !== false) {
                    $result = str_replace($key, $value, $result);
                }
            }
            $tooltip = $result . '&#10;';
            for ($i = 1; $i < 33; $i++) {
                if (qtype_poasquestion_string::strpos($result, chr($i)) !== false) {
                    $tooltip .= get_string('description_char' . dechex($i), 'qtype_preg') . '&#10;';
                    $result = str_replace(chr($i), '<font color="' . $colors[$flag]. '">' . shorten_text(get_string('description_char' . dechex($i), 'qtype_preg'), $length) . '</font> ', $result);
                    $flag = !$flag;
                }
            }
            foreach ($nonprintable as $key => $value) {
                if (qtype_poasquestion_string::strpos($result, $key) !== false) {
                    $tooltip .= get_string($value, 'qtype_preg') . '&#10;';
                    $result = str_replace($key, '<font color="' . $colors[$flag] . '">' . shorten_text(get_string($value, 'qtype_preg'), $length) . '</font> ', $result);
                    $flag = !$flag;
                }
            }
        }
        //var_dump($color);
        //var_dump($result);
        return $result;
    }
}
