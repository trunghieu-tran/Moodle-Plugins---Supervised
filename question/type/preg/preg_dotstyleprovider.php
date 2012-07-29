<?php

defined('MOODLE_INTERNAL') || die();

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
        $label = $label;
        $id = $pregnode->id;

        // Now the label is ready, just return the appropriate style for node type and subtype.
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT: {
                return "[label = \"abstract node\", style = dotted]";  // это пример, замени его потом на пустую строку.
            }
            case qtype_preg_node::TYPE_LEAF_CHARSET: {
                if ($pregnode->negative) {
                    $label = '[^' . $label . ']';
                } else {
                    $label = '[' . $label . ']';
                }
                return "[label = \"$label\", tooltip = \"character class\", id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_META: {
                //if($pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return "[label = \"emptiness\", tooltip = emptiness, id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_ASSERT: {
                return "[label = \"assertion $label\", tooltip = assertion, id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_BACKREF: {
                return "[label = \"backreference to ' . $pregnode->number . ' subpattern\", tooltip = backreference, id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_RECURSION: {
                return "[label = \"recursion ' . $pregnode->number . '\", shape = square, tooltip = recursion, id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_CONTROL: {
                return "[label = control sequence \"$label\", tooltip = \"control sequence\", id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_LEAF_OPTIONS: {
                return "[label = \"$label\", tooltip = option, id = $id, shape = rectangle]";
            }
            case qtype_preg_node::TYPE_NODE_FINITE_QUANT: {
                return "[label = \"$label\", tooltip = \"finite quantificator\", id = $id]";
            }
            case qtype_preg_node::TYPE_NODE_INFINITE_QUANT: {
                return "[label = \"$label\", tooltip = \"infinite quantificator\", id = $id]";
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

                return "[label = \"ERROR $label\", tooltip = error, id = $id]";
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
}
