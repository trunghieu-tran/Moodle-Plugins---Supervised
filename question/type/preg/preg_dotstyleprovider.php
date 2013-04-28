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
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');
require_once($CFG->libdir . '/moodlelib.php');

/**
 * Context for ast nodes drawing.
 */
class qtype_preg_dot_node_context {

    // Whether the node is the ast root or not.
    public $isroot;

    // Id of the node to be selected.
    public $selectid;

    public function __construct($isroot, $selectid = -1) {
        $this->isroot = $isroot;
        $this->selectid = $selectid;
    }
}

/**
 * Provides styles for node drawing using dot.
 */
class qtype_preg_dot_style_provider {

    /**
     * Returns the appropriate string for the given node.
     * @param $pregnode an instance of qtype_preg_node.
     * @param $context an instance of qtype_preg_dot_node_context.
     * @return string which can be used as a dot instruction to set style, label etc, for example '[style - dotted, label = \"some label here\"]'.
     */
    public static function get_style($pregnode, $context) {
        $label = '';
        $tooltip = '';
        $shape = is_a($pregnode, 'qtype_preg_operator') ? 'ellipse' : 'rectangle';
        $color = 'black';
        if (count($pregnode->errors) > 0 || $pregnode->type == qtype_preg_node::TYPE_NODE_ERROR) {
          $color = 'red';
        }

        // Form a label from the user inscription which can be an array of strings.
        if (is_array($pregnode->userinscription)) {
            foreach ($pregnode->userinscription as $tmp) {
                $tmptooltip = '';
                $label .= qtype_preg_dot_style_provider::get_spec_symbol($tmp, $tmptooltip, 7);
                $tooltip .= $tmptooltip;
            }
        } else {
            $label = qtype_preg_dot_style_provider::get_spec_symbol($pregnode->userinscription, $tooltip, 7);
        }

        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
        case qtype_preg_node::TYPE_LEAF_CHARSET:
            if ($pregnode->negative) {
                $label = '^' . $label;
            }

            if (is_array($pregnode->userinscription) && count($pregnode->userinscription) > 1) {
                $label = '&#91;' . $label . '&#93;';
            }

            $label = '<<TABLE BORDER="0" CELLBORDER="0" CELLSPACING="0" CELLPADDING="4"><TR><TD>' . $label . '</TD></TR></TABLE>>';

            if (count($pregnode->errors) > 0) {
                $tooltip = get_string('authoring_tool_tooltip_charset_error', 'qtype_preg') . ": " . $tooltip;
            } else {
                $tooltip = $pregnode->negative ?
                           get_string('authoring_tool_tooltip_negative_charset', 'qtype_preg') . ": " . $tooltip :
                           get_string('authoring_tool_tooltip_charset', 'qtype_preg') . ": " . $tooltip;
            }
            break;
        }

        $result = "id = {$pregnode->id}, label = $label, tooltip = \"$tooltip\", shape = $shape, color = $color";
        if ($context->selectid == $pregnode->id) {
            $result .= ', style = dotted';
        }
        return '[' . $result . ']';
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
}
