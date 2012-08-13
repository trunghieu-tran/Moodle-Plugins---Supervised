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
                $label .= qtype_preg_dot_style_provider::get_spec_symbol($tmp);
            }
        } else {
            //$tmp = $pregnode->userinscription;
            $label .= qtype_preg_dot_style_provider::get_spec_symbol($pregnode->userinscription);
        }

        $id = $pregnode->id;
        
        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT: {
                return "[label = \"abstract node\", style = dotted, color = \"blue\"]";  // это пример, замени его потом на пустую строку.
            }
            case qtype_preg_node::TYPE_LEAF_CHARSET: {
                
                    //TODO: implement prsing $label on error range
                    /*for($i=0; $i<strlen($label); $i++){
                        if($label[$i] == '-' && $i != 0 && $i != strlen($label)){
                            if(ord($label[$i-1]) > ord($label[$i+1]) ){
                                $label = str_replace(']', '&#93;', $label);
                                $label = str_replace('[', '&#91;', $label);
                                $label = str_replace('\\', '&#92;', $label);
                                return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"Incorrect range: left border is greater then the right one\", shape = record, id = $id, color = \"red\"]";
                                //return "[label = \"$label\", tooltip = \"Incorrect range: left border is greater then the right one\", id = $id, color = \"red\"]";
                            }
                        }
                    }*/
                    
                    if ($pregnode->negative) {
                        $label = '[^' . $label . ']';
                        $label = str_replace(']', '&#93;', $label);
                        $label = str_replace('[', '&#91;', $label);
                        //$label = str_replace('\\', '&#92;', $label);
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = record, id = $id]";
                    } else if (qtype_poasquestion_string::strlen($label) > 1) {
                        $label = '[' . $label . ']';
                    }
                    
                    $label = str_replace(']', '&#93;', $label);
                    $label = str_replace('[', '&#91;', $label);
                    //$label = str_replace('\\', '&#92;', $label);
                    //var_dump("[label = <<TABLE BORDER=\"0\" CELLBORDER=\"1\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = none, id = $id]");
                    if($pregnode->error === NULL){
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"character class\", shape = record, id = $id]";
                    } else {
                        return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD>$label</TD></TR></TABLE>>, tooltip = \"Incorrect range: left border is greater then the right one\", shape = record, id = $id, color = \"red\"]";
                    }
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
                return "[label = \"&#8226;\", tooltip = concatenation, id = $id]";
                //return "[label = <<TABLE BORDER=\"0\" CELLBORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\"><TR><TD><font>&#8226;</font></TD></TR></TABLE>>, tooltip = concatenation, shape = record, id = $id]";
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
    
    /*
     * 
     */
    protected static function get_spec_symbol($tmp) {
        if ($tmp->addinfo === true) {
            $tmp->data = '<font color="blue">' . $tmp->data . '</font>';
        } else {
            //Entities for characters with special meaning in HTML and XHTML
            if (strpos('"', $tmp->data) >= 0) {
                $tmp->data = str_replace('"', '&#34;', $tmp->data);
            }
            if (strpos('\\', $tmp->data) >= 0) {
                $tmp->data = str_replace('\\', '&#92;', $tmp->data);
            }
            if (strpos('&', $tmp->data) >= 0) {
                $tmp->data = str_replace('&', '&#38;', $tmp->data);
            }
            if (strpos('{', $tmp->data) >= 0) {
                $tmp->data = str_replace('{', '\\{', $tmp->data);
            }
            if (strpos('}', $tmp->data) >= 0) {
                $tmp->data = str_replace('}', '\\}', $tmp->data);
            }
            if (strpos('>', $tmp->data) >= 0) {
                $tmp->data = str_replace('>', '&#62;', $tmp->data);
            }
            if (strpos('<', $tmp->data) >= 0) {
                $tmp->data = str_replace('<', '&#60;', $tmp->data);
            }
            //Entities for accented characters, accents, and other diacritics from Western European Languages
            if (strpos('´', $tmp->data) >= 0) {
                $tmp->data = str_replace('´', '&#180;', $tmp->data);
            }
            if (strpos('¸', $tmp->data) >= 0) {
                $tmp->data = str_replace('¸', '&#184;', $tmp->data);
            }
            if (strpos('ˆ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ˆ', '&#710;', $tmp->data);
            }
            if (strpos('¯', $tmp->data) >= 0) {
                $tmp->data = str_replace('¯', '&#175;', $tmp->data);
            }
            if (strpos('·', $tmp->data) >= 0) {
                $tmp->data = str_replace('·', '&#183;', $tmp->data);
            }
            if (strpos('˜', $tmp->data) >= 0) {
                $tmp->data = str_replace('˜', '&#732;', $tmp->data);
            }
            
            if (strpos('Á', $tmp->data) >= 0) {
                $tmp->data = str_replace('Á', '&#193;', $tmp->data);
            }
            if (strpos('á', $tmp->data) >= 0) {
                $tmp->data = str_replace('á', '&#225;', $tmp->data);
            }
            if (strpos('Â', $tmp->data) >= 0) {
                $tmp->data = str_replace('Â', '&#194;', $tmp->data);
            }
            if (strpos('â', $tmp->data) >= 0) {
                $tmp->data = str_replace('â', '&#226;', $tmp->data);
            }
            if (strpos('Æ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Æ', '&#198;', $tmp->data);
            }
            if (strpos('æ', $tmp->data) >= 0) {
                $tmp->data = str_replace('æ', '&#230;', $tmp->data);
            }
            if (strpos('À', $tmp->data) >= 0) {
                $tmp->data = str_replace('À', '&#192;', $tmp->data);
            }
            if (strpos('à', $tmp->data) >= 0) {
                $tmp->data = str_replace('à', '&#224;', $tmp->data);
            }
            if (strpos('Å', $tmp->data) >= 0) {
                $tmp->data = str_replace('Å', '&#197;', $tmp->data);
            }
            if (strpos('å', $tmp->data) >= 0) {
                $tmp->data = str_replace('å', '&#229;', $tmp->data);
            }
            if (strpos('Ã', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ã', '&#195;', $tmp->data);
            }
            if (strpos('ã', $tmp->data) >= 0) {
                $tmp->data = str_replace('ã', '&#227;', $tmp->data);
            }
            if (strpos('Ä', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ä', '&#196;', $tmp->data);
            }
            if (strpos('ä', $tmp->data) >= 0) {
                $tmp->data = str_replace('ä', '&#228;', $tmp->data);
            }
            if (strpos('Ç', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ç', '&#199;', $tmp->data);
            }
            if (strpos('ç', $tmp->data) >= 0) {
                $tmp->data = str_replace('ç', '&#231;', $tmp->data);
            }
            if (strpos('É', $tmp->data) >= 0) {
                $tmp->data = str_replace('É', '&#201;', $tmp->data);
            }
            if (strpos('é', $tmp->data) >= 0) {
                $tmp->data = str_replace('é', '&#233;', $tmp->data);
            }
            if (strpos('Ê', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ê', '&#202;', $tmp->data);
            }
            if (strpos('ê', $tmp->data) >= 0) {
                $tmp->data = str_replace('ê', '&#234;', $tmp->data);
            }
            if (strpos('È', $tmp->data) >= 0) {
                $tmp->data = str_replace('È', '&#200;', $tmp->data);
            }
            if (strpos('è', $tmp->data) >= 0) {
                $tmp->data = str_replace('è', '&#232;', $tmp->data);
            }
            if (strpos('Ð', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ð', '&#208;', $tmp->data);
            }
            if (strpos('ð', $tmp->data) >= 0) {
                $tmp->data = str_replace('ð', '&#240;', $tmp->data);
            }
            if (strpos('Ë', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ë', '&#203;', $tmp->data);
            }
            if (strpos('ë', $tmp->data) >= 0) {
                $tmp->data = str_replace('ë', '&#235;', $tmp->data);
            }
            if (strpos('Í', $tmp->data) >= 0) {
                $tmp->data = str_replace('Í', '&#205;', $tmp->data);
            }
            if (strpos('í', $tmp->data) >= 0) {
                $tmp->data = str_replace('í', '&#237;', $tmp->data);
            }
            if (strpos('Î', $tmp->data) >= 0) {
                $tmp->data = str_replace('Î', '&#206;', $tmp->data);
            }
            if (strpos('î', $tmp->data) >= 0) {
                $tmp->data = str_replace('î', '&#238;', $tmp->data);
            }
            if (strpos('Ì', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ì', '&#204;', $tmp->data);
            }
            if (strpos('ì', $tmp->data) >= 0) {
                $tmp->data = str_replace('ì', '&#236;', $tmp->data);
            }
            if (strpos('Ï', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ï', '&#207;', $tmp->data);
            }
            if (strpos('ï', $tmp->data) >= 0) {
                $tmp->data = str_replace('ï', '&#239;', $tmp->data);
            }
            if (strpos('Ñ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ñ', '&#209;', $tmp->data);
            }
            if (strpos('ñ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ñ', '&#241;', $tmp->data);
            }
            if (strpos('Ó', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ó', '&#211;', $tmp->data);
            }
            if (strpos('ó', $tmp->data) >= 0) {
                $tmp->data = str_replace('ó', '&#243;', $tmp->data);
            }
            if (strpos('Ô', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ô', '&#212;', $tmp->data);
            }
            if (strpos('ô', $tmp->data) >= 0) {
                $tmp->data = str_replace('ô', '&#244;', $tmp->data);
            }
            if (strpos('Œ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Œ', '&#338;', $tmp->data);
            }
            if (strpos('œ', $tmp->data) >= 0) {
                $tmp->data = str_replace('œ', '&#339;', $tmp->data);
            }
            if (strpos('Ò', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ò', '&#210;', $tmp->data);
            }
            if (strpos('ò', $tmp->data) >= 0) {
                $tmp->data = str_replace('ò', '&#242;', $tmp->data);
            }
            if (strpos('Ø', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ø', '&#216;', $tmp->data);
            }
            if (strpos('ø', $tmp->data) >= 0) {
                $tmp->data = str_replace('ø', '&#248;', $tmp->data);
            }
            if (strpos('Õ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Õ', '&#213;', $tmp->data);
            }
            if (strpos('õ', $tmp->data) >= 0) {
                $tmp->data = str_replace('õ', '&#245;', $tmp->data);
            }
            if (strpos('Ö', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ö', '&#214;', $tmp->data);
            }
            if (strpos('ö', $tmp->data) >= 0) {
                $tmp->data = str_replace('ö', '&#246;', $tmp->data);
            }
            if (strpos('Š', $tmp->data) >= 0) {
                $tmp->data = str_replace('Š', '&#352;', $tmp->data);
            }
            if (strpos('š', $tmp->data) >= 0) {
                $tmp->data = str_replace('š', '&#353;', $tmp->data);
            }
            if (strpos('ß', $tmp->data) >= 0) {
                $tmp->data = str_replace('ß', '&#223;', $tmp->data);
            }
            if (strpos('Þ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Þ', '&#222;', $tmp->data);
            }
            if (strpos('þ', $tmp->data) >= 0) {
                $tmp->data = str_replace('þ', '&#254;', $tmp->data);
            }
            if (strpos('Ú', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ú', '&#218;', $tmp->data);
            }
            if (strpos('ú', $tmp->data) >= 0) {
                $tmp->data = str_replace('ú', '&#250;', $tmp->data);
            }
            if (strpos('Û', $tmp->data) >= 0) {
                $tmp->data = str_replace('Û', '&#219;', $tmp->data);
            }
            if (strpos('û', $tmp->data) >= 0) {
                $tmp->data = str_replace('û', '&#251;', $tmp->data);
            }
            if (strpos('Ù', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ù', '&#217;', $tmp->data);
            }
            if (strpos('ù', $tmp->data) >= 0) {
                $tmp->data = str_replace('ù', '&#249;', $tmp->data);
            }
            if (strpos('Ü', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ü', '&#220;', $tmp->data);
            }
            if (strpos('ü', $tmp->data) >= 0) {
                $tmp->data = str_replace('ü', '&#252;', $tmp->data);
            }
            if (strpos('Ý', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ý', '&#221;', $tmp->data);
            }
            if (strpos('ý', $tmp->data) >= 0) {
                $tmp->data = str_replace('ý', '&#253;', $tmp->data);
            }
            if (strpos('ÿ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ÿ', '&#255;', $tmp->data);
            }
            if (strpos('Ÿ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ÿ', '&#376;', $tmp->data);
            }
            //Entities for punctuation characters
            if (strpos('¢', $tmp->data) >= 0) {
                $tmp->data = str_replace('¢', '&#162;', $tmp->data);
            }
            if (strpos('¤', $tmp->data) >= 0) {
                $tmp->data = str_replace('¤', '&#164;', $tmp->data);
            }
            if (strpos('€', $tmp->data) >= 0) {
                $tmp->data = str_replace('€', '&#8364;', $tmp->data);
            }
            if (strpos('£', $tmp->data) >= 0) {
                $tmp->data = str_replace('£', '&#163;', $tmp->data);
            }
            if (strpos('¥', $tmp->data) >= 0) {
                $tmp->data = str_replace('¥', '&#165;', $tmp->data);
            }
            
            if (strpos('¦', $tmp->data) >= 0) {
                $tmp->data = str_replace('¦', '&#166;', $tmp->data);
            }            
            if (strpos('•', $tmp->data) >= 0) {
                $tmp->data = str_replace('•', '&#8226;', $tmp->data);
            }
            if (strpos('©', $tmp->data) >= 0) {
                $tmp->data = str_replace('©', '&#169;', $tmp->data);
            }
            if (strpos('†', $tmp->data) >= 0) {
                $tmp->data = str_replace('†', '&#8224;', $tmp->data);
            }
            if (strpos('‡', $tmp->data) >= 0) {
                $tmp->data = str_replace('‡', '&#8225;', $tmp->data);
            }
            if (strpos('⁄', $tmp->data) >= 0) {
                $tmp->data = str_replace('⁄', '&#8260;', $tmp->data);
            }
            if (strpos('…', $tmp->data) >= 0) {
                $tmp->data = str_replace('…', '&#8230;', $tmp->data);
            }
            if (strpos('¡', $tmp->data) >= 0) {
                $tmp->data = str_replace('¡', '&#161;', $tmp->data);
            }
            if (strpos('ℑ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ℑ', '&#8465;', $tmp->data);
            }
            if (strpos('¿', $tmp->data) >= 0) {
                $tmp->data = str_replace('¿', '&#191;', $tmp->data);
            }
            if (strpos(chr(8206), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8206), '&#8206;', $tmp->data);
            }
            if (strpos('—', $tmp->data) >= 0) {
                $tmp->data = str_replace('—', '&#8212;', $tmp->data);
            }
            if (strpos('–', $tmp->data) >= 0) {
                $tmp->data = str_replace('–', '&#8211;', $tmp->data);
            }
            if (strpos('¬', $tmp->data) >= 0) {
                $tmp->data = str_replace('¬', '&#172;', $tmp->data);
            }
            if (strpos('‾', $tmp->data) >= 0) {
                $tmp->data = str_replace('‾', '&#8254;', $tmp->data);
            }
            if (strpos('ª', $tmp->data) >= 0) {
                $tmp->data = str_replace('ª', '&#170;', $tmp->data);
            }
            if (strpos('º', $tmp->data) >= 0) {
                $tmp->data = str_replace('º', '&#186;', $tmp->data);
            }
            if (strpos('¶', $tmp->data) >= 0) {
                $tmp->data = str_replace('¶', '&#182;', $tmp->data);
            }
            if (strpos('‰', $tmp->data) >= 0) {
                $tmp->data = str_replace('‰', '&#8240;', $tmp->data);
            }
            if (strpos('′', $tmp->data) >= 0) {
                $tmp->data = str_replace('′', '&#8242;', $tmp->data);
            }
            if (strpos('″', $tmp->data) >= 0) {
                $tmp->data = str_replace('″', '&#8243;', $tmp->data);
            }
            if (strpos('ℜ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ℜ', '&#8476;', $tmp->data);
            }
            if (strpos('®', $tmp->data) >= 0) {
                $tmp->data = str_replace('®', '&#174;', $tmp->data);
            }
            if (strpos(chr(8207), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8207), '&#8207;', $tmp->data);
            }
            if (strpos('§', $tmp->data) >= 0) {
                $tmp->data = str_replace('§', '&#167;', $tmp->data);
            }
            if (strpos(chr(173), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(173), '&#173;', $tmp->data);
            }
            if (strpos('¹', $tmp->data) >= 0) {
                $tmp->data = str_replace('¹', '&#185;', $tmp->data);
            }
            if (strpos('™', $tmp->data) >= 0) {
                $tmp->data = str_replace('™', '&#8482;', $tmp->data);
            }
            if (strpos('℘', $tmp->data) >= 0) {
                $tmp->data = str_replace('℘', '&#8472;', $tmp->data);
            }
            
            if (strpos('„', $tmp->data) >= 0) {
                $tmp->data = str_replace('„', '&#8222;', $tmp->data);
            }
            if (strpos('«', $tmp->data) >= 0) {
                $tmp->data = str_replace('«', '&#171;', $tmp->data);
            }
            if (strpos('“', $tmp->data) >= 0) {
                $tmp->data = str_replace('“', '&#8220;', $tmp->data);
            }
            if (strpos('‹', $tmp->data) >= 0) {
                $tmp->data = str_replace('‹', '&#8249;', $tmp->data);
            }
            if (strpos('‘', $tmp->data) >= 0) {
                $tmp->data = str_replace('‘', '&#8216;', $tmp->data);
            }
            if (strpos('»', $tmp->data) >= 0) {
                $tmp->data = str_replace('»', '&#187;', $tmp->data);
            }
            if (strpos('”', $tmp->data) >= 0) {
                $tmp->data = str_replace('”', '&#8221;', $tmp->data);
            }
            if (strpos('›', $tmp->data) >= 0) {
                $tmp->data = str_replace('›', '&#8250;', $tmp->data);
            }
            if (strpos('’', $tmp->data) >= 0) {
                $tmp->data = str_replace('’', '&#8217;', $tmp->data);
            }
            if (strpos('‚', $tmp->data) >= 0) {
                $tmp->data = str_replace('‚', '&#8218;', $tmp->data);
            }
            
            if (strpos(chr(8195), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8195), '&#8195;', $tmp->data);
            }
            if (strpos(chr(8194), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8194), '&#8194;', $tmp->data);
            }
            if (strpos(chr(160), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(160), '&#160;', $tmp->data);
            }
            if (strpos(chr(8201), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8201), '&#8201;', $tmp->data);
            }
            if (strpos(chr(8205), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8205), '&#8205;', $tmp->data);
            }
            if (strpos(chr(8204), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(8204), '&#8204;', $tmp->data);
            }
            //Entities for mathematical and technical characters (including Greek)
            if (strpos('°', $tmp->data) >= 0) {
                $tmp->data = str_replace('°', '&#176;', $tmp->data);
            }
            if (strpos('÷', $tmp->data) >= 0) {
                $tmp->data = str_replace('÷', '&#247;', $tmp->data);
            }
            if (strpos('½', $tmp->data) >= 0) {
                $tmp->data = str_replace('½', '&#189;', $tmp->data);
            }
            if (strpos('¼', $tmp->data) >= 0) {
                $tmp->data = str_replace('¼', '&#188;', $tmp->data);
            }
            if (strpos('¾', $tmp->data) >= 0) {
                $tmp->data = str_replace('¾', '&#190;', $tmp->data);
            }
            if (strpos('≥', $tmp->data) >= 0) {
                $tmp->data = str_replace('≥', '&#8805;', $tmp->data);
            }
            if (strpos('≤', $tmp->data) >= 0) {
                $tmp->data = str_replace('≤', '&#8804;', $tmp->data);
            }
            if (strpos('−', $tmp->data) >= 0) {
                $tmp->data = str_replace('−', '&#8722;', $tmp->data);
            }
            if (strpos('²', $tmp->data) >= 0) {
                $tmp->data = str_replace('²', '&#178;', $tmp->data);
            }
            if (strpos('³', $tmp->data) >= 0) {
                $tmp->data = str_replace('³', '&#179;', $tmp->data);
            }
            if (strpos('×', $tmp->data) >= 0) {
                $tmp->data = str_replace('×', '&#215;', $tmp->data);
            }
            
            if (strpos('ℵ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ℵ', '&#8501;', $tmp->data);
            }
            if (strpos('∧', $tmp->data) >= 0) {
                $tmp->data = str_replace('∧', '&#8743;', $tmp->data);
            }
            if (strpos('∠', $tmp->data) >= 0) {
                $tmp->data = str_replace('∠', '&#8736;', $tmp->data);
            }
            if (strpos('≈', $tmp->data) >= 0) {
                $tmp->data = str_replace('≈', '&#8776;', $tmp->data);
            }
            if (strpos('∩', $tmp->data) >= 0) {
                $tmp->data = str_replace('∩', '&#8745;', $tmp->data);
            }
            if (strpos('≅', $tmp->data) >= 0) {
                $tmp->data = str_replace('≅', '&#8773;', $tmp->data);
            }
            if (strpos('∪', $tmp->data) >= 0) {
                $tmp->data = str_replace('∪', '&#8746;', $tmp->data);
            }
            if (strpos('∅', $tmp->data) >= 0) {
                $tmp->data = str_replace('∅', '&#8709;', $tmp->data);
            }
            if (strpos('≡', $tmp->data) >= 0) {
                $tmp->data = str_replace('≡', '&#8801;', $tmp->data);
            }
            if (strpos('∃', $tmp->data) >= 0) {
                $tmp->data = str_replace('∃', '&#8707;', $tmp->data);
            }
            if (strpos('ƒ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ƒ', '&#402;', $tmp->data);
            }
            if (strpos('∀', $tmp->data) >= 0) {
                $tmp->data = str_replace('∀', '&#8704;', $tmp->data);
            }
            if (strpos('∞', $tmp->data) >= 0) {
                $tmp->data = str_replace('∞', '&#8734;', $tmp->data);
            }
            if (strpos('∫', $tmp->data) >= 0) {
                $tmp->data = str_replace('∫', '&#8747;', $tmp->data);
            }
            if (strpos('∈', $tmp->data) >= 0) {
                $tmp->data = str_replace('∈', '&#8712;', $tmp->data);
            }
            if (strpos('⟨', $tmp->data) >= 0) {
                $tmp->data = str_replace('⟨', '&#9001;', $tmp->data);
            }
            if (strpos('⌈', $tmp->data) >= 0) {
                $tmp->data = str_replace('⌈', '&#8968;', $tmp->data);
            }
            if (strpos('⌊', $tmp->data) >= 0) {
                $tmp->data = str_replace('⌊', '&#8970;', $tmp->data);
            }
            if (strpos('∗', $tmp->data) >= 0) {
                $tmp->data = str_replace('∗', '&#8727;', $tmp->data);
            }
            if (strpos('µ', $tmp->data) >= 0) {
                $tmp->data = str_replace('µ', '&#181;', $tmp->data);
            }
            if (strpos('∇', $tmp->data) >= 0) {
                $tmp->data = str_replace('∇', '&#8711;', $tmp->data);
            }
            if (strpos('≠', $tmp->data) >= 0) {
                $tmp->data = str_replace('≠', '&#8800;', $tmp->data);
            }
            if (strpos('∋', $tmp->data) >= 0) {
                $tmp->data = str_replace('∋', '&#8715;', $tmp->data);
            }
            if (strpos('∉', $tmp->data) >= 0) {
                $tmp->data = str_replace('∉', '&#8713;', $tmp->data);
            }
            if (strpos('⊄', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊄', '&#8836;', $tmp->data);
            }
            if (strpos('⊕', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊕', '&#8853;', $tmp->data);
            }
            if (strpos('∨', $tmp->data) >= 0) {
                $tmp->data = str_replace('∨', '&#8744;', $tmp->data);
            }
            if (strpos('⊗', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊗', '&#8855;', $tmp->data);
            }            
            if (strpos('∂', $tmp->data) >= 0) {
                $tmp->data = str_replace('∂', '&#8706;', $tmp->data);
            }
            if (strpos('⊥', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊥', '&#8869;', $tmp->data);
            }
            if (strpos('±', $tmp->data) >= 0) {
                $tmp->data = str_replace('±', '&#177;', $tmp->data);
            }
            if (strpos('∏', $tmp->data) >= 0) {
                $tmp->data = str_replace('∏', '&#8719;', $tmp->data);
            }
            if (strpos('∝', $tmp->data) >= 0) {
                $tmp->data = str_replace('∝', '&#8733;', $tmp->data);
            }
            if (strpos('√', $tmp->data) >= 0) {
                $tmp->data = str_replace('√', '&#8730;', $tmp->data);
            }
            if (strpos('⟩', $tmp->data) >= 0) {
                $tmp->data = str_replace('⟩', '&#9002;', $tmp->data);
            }
            if (strpos('⌉', $tmp->data) >= 0) {
                $tmp->data = str_replace('⌉', '&#8969;', $tmp->data);
            }
            if (strpos('⌋', $tmp->data) >= 0) {
                $tmp->data = str_replace('⌋', '&#8971;', $tmp->data);
            }
            if (strpos('⋅', $tmp->data) >= 0) {
                $tmp->data = str_replace('⋅', '&#8901;', $tmp->data);
            }
            if (strpos('∼', $tmp->data) >= 0) {
                $tmp->data = str_replace('∼', '&#8764;', $tmp->data);
            }
            if (strpos('⊂', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊂', '&#8834;', $tmp->data);
            }
            if (strpos('⊆', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊆', '&#8838;', $tmp->data);
            }
            if (strpos('∑', $tmp->data) >= 0) {
                $tmp->data = str_replace('∑', '&#8721;', $tmp->data);
            }
            if (strpos('⊃', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊃', '&#8835;', $tmp->data);
            }
            if (strpos('⊇', $tmp->data) >= 0) {
                $tmp->data = str_replace('⊇', '&#8839;', $tmp->data);
            }
            if (strpos('∴', $tmp->data) >= 0) {
                $tmp->data = str_replace('∴', '&#8968;', $tmp->data);
            }
            
            if (strpos('Α', $tmp->data) >= 0) {
                $tmp->data = str_replace('Α', '&#913;', $tmp->data);
            }
            if (strpos('α', $tmp->data) >= 0) {
                $tmp->data = str_replace('α', '&#945;', $tmp->data);
            }
            if (strpos('Β', $tmp->data) >= 0) {
                $tmp->data = str_replace('Β', '&#914;', $tmp->data);
            }
            if (strpos('β', $tmp->data) >= 0) {
                $tmp->data = str_replace('β', '&#946;', $tmp->data);
            }
            if (strpos('Χ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Χ', '&#946;', $tmp->data);
            }
            if (strpos('χ', $tmp->data) >= 0) {
                $tmp->data = str_replace('χ', '&#967;', $tmp->data);
            }
            if (strpos('Δ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Δ', '&#916;', $tmp->data);
            }
            if (strpos('δ', $tmp->data) >= 0) {
                $tmp->data = str_replace('δ', '&#948;', $tmp->data);
            }
            if (strpos('Ε', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ε', '&#917;', $tmp->data);
            }
            if (strpos('ε', $tmp->data) >= 0) {
                $tmp->data = str_replace('ε', '&#949;', $tmp->data);
            }
            if (strpos('Η', $tmp->data) >= 0) {
                $tmp->data = str_replace('Η', '&#919;', $tmp->data);
            }            
            if (strpos('η', $tmp->data) >= 0) {
                $tmp->data = str_replace('η', '&#951;', $tmp->data);
            }
            if (strpos('Γ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Γ', '&#915;', $tmp->data);
            }
            if (strpos('γ', $tmp->data) >= 0) {
                $tmp->data = str_replace('γ', '&#947;', $tmp->data);
            }
            if (strpos('Ι', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ι', '&#921;', $tmp->data);
            }
            if (strpos('ι', $tmp->data) >= 0) {
                $tmp->data = str_replace('ι', '&#953;', $tmp->data);
            }
            if (strpos('Κ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Κ', '&#922;', $tmp->data);
            }
            if (strpos('κ', $tmp->data) >= 0) {
                $tmp->data = str_replace('κ', '&#954;', $tmp->data);
            }
            if (strpos('Λ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Λ', '&#923;', $tmp->data);
            }
            if (strpos('λ', $tmp->data) >= 0) {
                $tmp->data = str_replace('λ', '&#955;', $tmp->data);
            }
            if (strpos('Μ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Μ', '&#924;', $tmp->data);
            }
            if (strpos('μ', $tmp->data) >= 0) {
                $tmp->data = str_replace('μ', '&#956;', $tmp->data);
            }
            if (strpos('Ν', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ν', '&#925;', $tmp->data);
            }
            if (strpos('ν', $tmp->data) >= 0) {
                $tmp->data = str_replace('ν', '&#957;', $tmp->data);
            }
            if (strpos('Ω', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ω', '&#937;', $tmp->data);
            }
            if (strpos('ω', $tmp->data) >= 0) {
                $tmp->data = str_replace('ω', '&#969;', $tmp->data);
            }
            if (strpos('Ο', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ο', '&#927;', $tmp->data);
            }
            if (strpos('ο', $tmp->data) >= 0) {
                $tmp->data = str_replace('ο', '&#959;', $tmp->data);
            }
            if (strpos('Φ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Φ', '&#934;', $tmp->data);
            }
            if (strpos('φ', $tmp->data) >= 0) {
                $tmp->data = str_replace('φ', '&#966;', $tmp->data);
            }
            if (strpos('Π', $tmp->data) >= 0) {
                $tmp->data = str_replace('Π', '&#928;', $tmp->data);
            }
            if (strpos('π', $tmp->data) >= 0) {
                $tmp->data = str_replace('π', '&#960;', $tmp->data);
            }
            if (strpos('ϖ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ϖ', '&#982;', $tmp->data);
            }
            if (strpos('Ψ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ψ', '&#936;', $tmp->data);
            }
            if (strpos('ψ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ψ', '&#968;', $tmp->data);
            }
            if (strpos('Ρ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ρ', '&#929;', $tmp->data);
            }
            if (strpos('ρ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ρ', '&#961;', $tmp->data);
            }
            if (strpos('Σ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Σ', '&#931;', $tmp->data);
            }
            if (strpos('σ', $tmp->data) >= 0) {
                $tmp->data = str_replace('σ', '&#963;', $tmp->data);
            }            
            if (strpos('ς', $tmp->data) >= 0) {
                $tmp->data = str_replace('ς', '&#962;', $tmp->data);
            }
            if (strpos('Τ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Τ', '&#932;', $tmp->data);
            }
            if (strpos('τ', $tmp->data) >= 0) {
                $tmp->data = str_replace('τ', '&#964;', $tmp->data);
            }
            if (strpos('Θ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Θ', '&#920;', $tmp->data);
            }
            if (strpos('θ', $tmp->data) >= 0) {
                $tmp->data = str_replace('θ', '&#952;', $tmp->data);
            }
            if (strpos('ϑ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ϑ', '&#977;', $tmp->data);
            }
            if (strpos('ϒ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ϒ', '&#978;', $tmp->data);
            }
            if (strpos('Υ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Υ', '&#933;', $tmp->data);
            }
            if (strpos('υ', $tmp->data) >= 0) {
                $tmp->data = str_replace('υ', '&#965;', $tmp->data);
            }
            if (strpos('Ξ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ξ', '&#926;', $tmp->data);
            }
            if (strpos('ξ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ξ', '&#958;', $tmp->data);
            }
            if (strpos('Ζ', $tmp->data) >= 0) {
                $tmp->data = str_replace('Ζ', '&#918;', $tmp->data);
            }
            if (strpos('ζ', $tmp->data) >= 0) {
                $tmp->data = str_replace('ζ', '&#950;', $tmp->data);
            }
            //Entities for shapes and arrows
            if (strpos('↵', $tmp->data) >= 0) {
                $tmp->data = str_replace('↵', '&#8629;', $tmp->data);
            }
            if (strpos('↓', $tmp->data) >= 0) {
                $tmp->data = str_replace('↓', '&#8592;', $tmp->data);
            }
            if (strpos('⇓', $tmp->data) >= 0) {
                $tmp->data = str_replace('⇓', '&#8659;', $tmp->data);
            }
            if (strpos('↔', $tmp->data) >= 0) {
                $tmp->data = str_replace('↔', '&#8596;', $tmp->data);
            }
            if (strpos('⇔', $tmp->data) >= 0) {
                $tmp->data = str_replace('⇔', '&#8660;', $tmp->data);
            }
            if (strpos('←', $tmp->data) >= 0) {
                $tmp->data = str_replace('←', '&#8592;', $tmp->data);
            }
            if (strpos('⇐', $tmp->data) >= 0) {
                $tmp->data = str_replace('⇐', '&#8656;', $tmp->data);
            }
            if (strpos('→', $tmp->data) >= 0) {
                $tmp->data = str_replace('→', '&#8594;', $tmp->data);
            }
            if (strpos('∑', $tmp->data) >= 0) {
                $tmp->data = str_replace('∑', '&#8721;', $tmp->data);
            }
            if (strpos('⇒', $tmp->data) >= 0) {
                $tmp->data = str_replace('⇒', '&#8658;', $tmp->data);
            }
            if (strpos('↑', $tmp->data) >= 0) {
                $tmp->data = str_replace('↑', '&#8593;', $tmp->data);
            }
            if (strpos('⇑', $tmp->data) >= 0) {
                $tmp->data = str_replace('⇑', '&#8657;', $tmp->data);
            }
            
            if (strpos('♣', $tmp->data) >= 0) {
                $tmp->data = str_replace('♣', '&#9827;', $tmp->data);
            }
            if (strpos('♦', $tmp->data) >= 0) {
                $tmp->data = str_replace('♦', '&#9830;', $tmp->data);
            }
            if (strpos('♥', $tmp->data) >= 0) {
                $tmp->data = str_replace('♥', '&#9829;', $tmp->data);
            }
            if (strpos('♠', $tmp->data) >= 0) {
                $tmp->data = str_replace('♠', '&#9824;', $tmp->data);
            }
            
            if (strpos('◊', $tmp->data) >= 0) {
                $tmp->data = str_replace('◊', '&#9674;', $tmp->data);
            }
            
            if (strpos(chr(32), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(32), '<font color="blue">' . get_string('description_char_space', 'qtype_preg') . '</font>', $tmp->data);
            } 
            if (strpos(chr(9), $tmp->data) >= 0) {
                $tmp->data = str_replace(chr(9), '<font color="blue">' . get_string('description_char_t', 'qtype_preg') . '</font>', $tmp->data);
            } 
            if (strpos('\\r', $tmp->data) >= 0) {
                $tmp->data = str_replace('\\r', '<font color="blue">' . get_string('description_char_r', 'qtype_preg') . '</font>', $tmp->data);
            } 
            if (strpos('\\n', $tmp->data) >= 0) {
                $tmp->data = str_replace('\\n', '<font color="blue">' . get_string('description_char_n', 'qtype_preg') . '</font>', $tmp->data);
            } 
            if (strpos('\\t', $tmp->data) >= 0) {
                $tmp->data = str_replace('\\t', '<font color="blue">' . get_string('description_char_t', 'qtype_preg') . '</font>', $tmp->data);
            }
        }
        return $tmp->data;
    }
}
