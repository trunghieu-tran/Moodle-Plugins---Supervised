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
 * Defines graph's node classes.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Abstract class for both operators and leafs.
 */
abstract class qtype_preg_authoring_tool_node_abstract {

    public $pregnode; // A reference to the corresponding preg_node.
    public $handler;

    public function __construct($node, $handler) {
        $this->pregnode = $node;
        $this->handler = $handler;
    }

    /**
     * Creates and returns subgraph which explaining part of regular expression.
     * @param int $id Identifier of node which will be picked out in image.
     */
    abstract public function &create_graph($id = -1);

    /**
     * Checks admissibility of node by the engine.
     * @return bool True if this node is supported by the engine.
     */
    public function accept() {
        switch ($this->pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT:
            case qtype_preg_node::TYPE_LEAF_CONTROL:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return get_string($this->pregnode->type, 'qtype_preg');
            default:
                return true;
        }
    }
}

/**
 * Class for tree's leaf.
 */
abstract class qtype_preg_authoring_tool_leaf extends qtype_preg_authoring_tool_node_abstract {
    /**
     * Returns filling settings of node which will be in graph.
     * @return string Filling settings of node.
     */
    public function get_filled() {
        if ($this->pregnode->caseless || ($this->pregnode->type == qtype_preg_node::TYPE_LEAF_OPTIONS && $this->pregnode->posopt == 'i')) {
            return ', style=filled, fillcolor=grey';
        } else {
            return '';
        }
    }

    /**
     * Returns value of node which will be in graph.
     * @return string Value of node.
     */
    public abstract function get_value();

    /**
     * Returns color of node which will be in graph.
     * @return string Color of node.
     */
    public abstract function get_color();

    /**
     * Returns shape of node which will be in graph.
     * @return string Shape of node.
     */
    public abstract function get_shape();

    /**
     * Implementation of abstract create_graph for leaf.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node($this->get_value(), $this->get_shape(), $this->get_color(), $graph, $this->pregnode->id, $this->get_filled());
        if ($this->pregnode->negative) {
            $graph->nodes[0]->invert = true;
        }

        if ($id == $this->pregnode->id) {
            $graph->style .= '; color=darkgreen';

            $marking = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', 0.5 + $this->pregnode->id);
            $marking->subgraphs[] = $graph;

            $marking->entries[] = end($graph->nodes);
            $marking->exits[] = end($graph->nodes);

            return $marking;
        } else {
            $graph->entries[] = end($graph->nodes);
            $graph->exits[] = end($graph->nodes);
        }

        return $graph;
    }
}

/**
 * Class for tree's charset leaf.
 */
class qtype_preg_authoring_tool_leaf_charset extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        return $this->process_charset();
    }

    public function get_color() {
        $tmp = $this->get_value();
        if (count($tmp) == 1) {
            if ($tmp[0][0] == chr(10)) {
                return 'hotpink';
            } else {
                return 'black';
            }
        } else {
            return 'black';
        }
    }

    public function get_shape() {

        if (count($this->pregnode->flags) > 1 || $this->pregnode->negative) {
            return 'record';
        } else {
            if ($this->get_color() == 'hotpink') {
                return 'ellipse';
            } else if ($this->pregnode->flags[0][0]->data->length() > 1) {
                return 'record';
            } else {
                return 'ellipse';
            }
        }
    }

    /**
     * Processes userinscription of charset to make an array of information which one will be in result graph.
     * @return array Charset values for node of graph.
     */
    public function process_charset() {
        $info = $this->pregnode->userinscription;   // Refer a userinscription to new variable for convenience.
        if (count($this->pregnode->userinscription) > 1) {
            array_shift($info);
            array_pop($info);
        }
        $result = array('');                          // This will store the result, first element is empty.

        // Now, iterate over userinscription elements.
        foreach ($info as $iter) {
            $data = new qtype_poasquestion_string($iter->data);
            // First, we need to define: is it range?
            // So, check this pattern: <something>-<something>.
            $mpos = textlib::strpos($data, '-');
            // If position is not in beginning and not in end...
            if ($mpos != 0 && $mpos != $data->length() - 1) {
                if ($mpos == 1) { // If <something>'s length is 1 then our range hasn't hex code.
                    $result[] = chr(10) . get_string('explain_from', 'qtype_preg') . $data->substring(0, $mpos) .
                                          get_string('explain_to', 'qtype_preg') . $data->substring($mpos + 1);
                } else {            // ...else we deal with hex code in <something>.
                    $a = new stdClass();
                    $a->code = $data->substring(2, $mpos - 2)->string();
                    $tmp = chr(10) . get_string('explain_from', 'qtype_preg') .
                                     get_string('description_char_16value', 'qtype_preg', $a) . get_string('explain_to', 'qtype_preg');
                    $a->code = $data->substring($mpos + 3)->string();
                    $result[] = $tmp . get_string('description_char_16value', 'qtype_preg', $a);
                }

                continue; // Because we found range we iterate to next unserinscription element.
            }

            // Now we know that this $iter hasn't a range.
            // So, iterate over all characters in $iter.
            for ($i = 0; $i < $data->length(); $i++) {

                // Check this pattern: [:<something>:] (it is POSIX class).
                // First char should be '['.
                if ($i == 0 && $data[$i] == '[') {
                    $i += 2;
                    $tmp = '';  // Third char should be ':'.
                    while ($data[$i] != ':') { // iterate to next ':'
                        $tmp .= $data[$i]; // Accumulate <something>'s characters.
                        $i++;
                    }
                    $i++; // Move to last ']'.

                    // Extract POSIX class from lang-file.
                    $result[] = chr(10) . get_string('description_charflag_' . $tmp, 'qtype_preg');

                } else if ($data[$i] == '\\') { // Here we check another pattern: \<something>.
                    $i++; // Move to next character.

                    // Now we're just checking all possible <something> variants.
                    if ($data[$i] == '\\') {    // Here is \-escaping.
                        $result[count($result) - 1] .= '\\';
                    }
                    if ($data[$i] == 'p') {  // Unicode property.
                        $i++;
                        if ($data[$i] == '{') { // It may be like this - \p{<something>}.
                            $tmp = '';
                            $i++;
                            while ($data[$i] != '}') {
                                $tmp .= $data[$i];
                                $i++;
                            }
                            $result[] = chr(10) . get_string('description_charflag_' . $tmp, 'qtype_preg');
                        } else { // Or just like this \p<something>.
                            $result[] = chr(10) . get_string('description_charflag_' . $data[$i], 'qtype_preg');
                        }
                    } else if ($data[$i] == 'x' || $data[$i] == 'X') { // It may be like this - \x<somthing> or \X<something>, where <something> is hex number.
                        $i++;
                        $tmp = '';
                        if (ctype_xdigit($data[$i])) {
                            $tmp = $data[$i];
                            $i++;
                            if (ctype_xdigit($data[$i])) {
                                $tmp .= $data[$i];
                            }
                        } else if ($data[$i] == '{') { // It also may looks like \x{<something>}.
                            $i++;
                            while ($data[$i] != '}') {
                                $tmp .= $data[$i];
                                $i++;
                            }
                        } else {
                            $tmp = 0;
                            $i--;
                        }

                        // Extract a value from lang-file.
                        $a = new stdClass();
                        $a->code = $tmp;
                        $result[] = chr(10) . get_string('description_char_16value', 'qtype_preg', $a);
                    } else if ($data[$i] == 'n') {
                        $result[] = chr(10) . get_string('description_charA', 'qtype_preg');
                    } else if ($data[$i] == 'r') {
                        $result[] = chr(10) . get_string('description_charD', 'qtype_preg');
                    } else if ($data[$i] == 'd') {
                        $result[] = chr(10) . get_string('description_charflag_digit', 'qtype_preg');
                    } else if ($data[$i] == 'D') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_digit', 'qtype_preg');
                    } else if ($data[$i] == 's') {
                        $result[] = chr(10) . get_string('description_charflag_space', 'qtype_preg');
                    } else if ($data[$i] == 'S') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_space', 'qtype_preg');
                    } else if ($data[$i] == 'w') {
                        $result[] = chr(10) . get_string('description_charflag_word', 'qtype_preg');
                    } else if ($data[$i] == 'W') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg') . get_string('description_charflag_word', 'qtype_preg');
                    } else if ($data[$i] == 't') {
                        $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                    } else if ($data[$i] == 'h') {
                        $result[] = chr(10) . get_string('description_charflag_hspace', 'qtype_preg');
                    } else if ($data[$i] == 'v') {
                        $result[] = chr(10) . get_string('description_charflag_vspace', 'qtype_preg');
                    } else if ($data[$i] == 'H') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg')  . get_string('description_charflag_hspace', 'qtype_preg');
                    } else if ($data[$i] == 'V') {
                        $result[] = chr(10) . get_string('explain_not', 'qtype_preg')  . get_string('description_charflag_vspace', 'qtype_preg');
                    } else if ($data[$i] == ' ') {
                        $result[] = chr(10) . get_string('description_char20', 'qtype_preg');
                    } else if ($data[$i] == "\t") {
                        $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                    } else {
                        $result[0] .= $data[$i];
                    }
                } else if ($data[$i] == ' ') {
                    $result[] = chr(10) . get_string('description_char20', 'qtype_preg');
                } else if ($data[$i] == "\t") {
                    $result[] = chr(10) . get_string('description_char9', 'qtype_preg');
                } else if ($data[$i] == '.') {    // Here is .-escaping.
                        $result[] = chr(10) . get_string('description_charflag_print', 'qtype_preg');
                } else {
                    $result[0] .= $data[$i]; // All another characters are not special.
                }
            }
        }

        // If first element is empty then delete it.
        if ($result[0] == '') {
            unset($result[0]);
            $result = array_values($result);
        }

        return $result;
    }
}

/**
 * Class for tree's meta leaf.
 */
class qtype_preg_authoring_tool_leaf_meta extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        if ($this->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
            return array('Void');
        } else {
            return array(get_string('explain_unknow_meta', 'qtype_preg'));
        }
    }

    public function get_color() {
        return 'orange';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's assert leaf.
 */
class qtype_preg_authoring_tool_leaf_assert extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX) {
            return array(get_string('description_circumflex', 'qtype_preg'));
        } else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR) {
            return array(get_string('description_dollar', 'qtype_preg'));
        } else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B) {
            return array(($this->pregnode->negative ? get_string('description_wordbreak_neg', 'qtype_preg') : get_string('description_wordbreak', 'qtype_preg')));
        } else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_A) {
            return array(get_string('description_esc_a', 'qtype_preg'));
        } else if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_Z) {
            return array(get_string('description_esc_z', 'qtype_preg'));
        } else {
            return array(get_string('explain_unknow_assert', 'qtype_preg'));
        }
    }

    public function get_color() {
        return 'red';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's backreference leaf.
 */
class qtype_preg_authoring_tool_leaf_backref extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        $tmp = $this->pregnode->number;
        if (is_integer($this->pregnode->number)) {
            $a = new stdClass();
            $a->number = $tmp;
            return array(get_string('description_backref', 'qtype_preg', $a));
        } else {
            $a = new stdClass();
            $a->name = $tmp;
            return array(get_string('description_backref_name', 'qtype_preg', $a));
        }
    }

    public function get_color() {
        return 'blue';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's recursion leaf.
 */
class qtype_preg_authoring_tool_leaf_recursion extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        $tmp = $this->pregnode->number;
        if ($tmp == 0) {
            return array(get_string('description_recursion_all', 'qtype_preg'));
        } else if (is_integer($this->pregnode->number)) {
            $a = new stdClass();
            $a->number = $tmp;
            return array(get_string('description_recursion', 'qtype_preg', $a));
        } else {
            $a = new stdClass();
            $a->name = $tmp;
            return array(get_string('description_recursion_name', 'qtype_preg', $a));
        }
    }

    public function get_color() {
        return 'blue';
    }

    public function get_shape() {
        return 'ellipse';
    }
}

/**
 * Class for tree's options leaf.
 */
class qtype_preg_authoring_tool_leaf_options extends qtype_preg_authoring_tool_leaf {

    public function get_value() {
        return array('');
    }

    public function get_color() {
        return 'orange';
    }

    public function get_shape() {
        return 'box';
    }
}

/**
 * Class for tree's operator.
 */
abstract class qtype_preg_authoring_tool_operator extends qtype_preg_authoring_tool_node_abstract {

    public $operands = array(); // An array of operands.
    protected $condid = -1;      // A number of conditional branch of conditional subexpression.

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $handler->from_preg_node($operand);
        }
    }

    /**
     * Processes a tree's operator for creating a part of explainning graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $graph Current explainning graph.
     * @param int $id Same as a create_graph parameter.
     */
    abstract protected function process_operator($graph, $id);

    /**
     * Implementation of abstract create_graph for concatenation.
     */
    public function &create_graph($id = -1) {
        $graph = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $this->process_operator($graph, $id);

        if ($id == $this->pregnode->id || ($id == $this->condid && $id != -1) ) {
            $marking = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=darkgreen', 0.5 + $this->pregnode->id);
            $marking->assume_subgraph($graph);
            $graph->nodes = array();
            $graph->links = array();
            $graph->subgraphs = array();
            $graph->subgraphs[] = $marking;
        }

        return $graph;
    }
}

/**
 * Class for tree's concatenation operator.
 */
class qtype_preg_authoring_tool_node_concat extends qtype_preg_authoring_tool_operator {

    protected function process_operator($graph, $id) {
        $left = $this->operands[0]->create_graph($id);
        $graph->assume_subgraph($left);
        $graph->entries[] = end($left->entries);

        $n = count($this->operands);
        for ($i = 1; $i < $n; ++$i) {
            $right = $this->operands[$i]->create_graph($id);
            $graph->assume_subgraph($right);
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left->exits[0], $right->entries[0], $graph);

            if ($i != $n-1) {
                $left = $right;
            } else {
                $graph->exits[] = end($right->exits);
            }
        }
    }
}

/**
 * Class for tree's alternation operator.
 */
class qtype_preg_authoring_tool_node_alt extends qtype_preg_authoring_tool_operator {

    protected function process_operator($graph, $id) {
        $left = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);
        $right = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

        foreach ($this->operands as $operand) {
            $newoperand = $operand->create_graph($id);
            $graph->assume_subgraph($newoperand);

            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $left, $newoperand->entries[count($newoperand->entries)-1], $graph);
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $newoperand->exits[count($newoperand->exits)-1], $right, $graph);
        }

        $graph->nodes[] = $left;
        $graph->entries[] = $left;
        $graph->nodes[] = $right;
        $graph->exits[] = $right;
    }
}

/**
 * Class for tree's quantifier operator.
 */
class qtype_preg_authoring_tool_node_quant extends qtype_preg_authoring_tool_operator {

    protected function process_operator($graph, $id) {
        $operand = $this->operands[0]->create_graph($id);

        if ($this->pregnode->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            if ($this->pregnode->rightborder != $this->pregnode->leftborder) {
                $label = get_string('explain_from', 'qtype_preg') . $this->pregnode->leftborder . get_string('explain_to', 'qtype_preg');

                $label .= $this->pregnode->rightborder . qtype_preg_authoring_tool_node_quant::getEnding($this->pregnode->rightborder);
            } else {
                $label = $this->pregnode->leftborder . qtype_preg_authoring_tool_node_quant::getEnding($this->pregnode->rightborder);
            }
        } else {
            $label = get_string('explain_from', 'qtype_preg') . $this->pregnode->leftborder . get_string('explain_to', 'qtype_preg')
                    . get_string('explain_any', 'qtype_preg') . get_string('explain_time', 'qtype_preg');
        }

        $quant = new qtype_preg_explaining_graph_tool_subgraph($label, 'dotted; color=black', $this->pregnode->id);
        $quant->assume_subgraph($operand);

        $graph->subgraphs[] = $quant;
        $graph->entries[] = end($operand->entries);
        $graph->exits[] = end($operand->exits);
    }

    /**
     * Returns "time" or "times" within sending number.
     * @param int $end just number
     * @return string right form of "time(s)"
     */
    private function getEnding($end) {
        return ($this->pregnode->rightborder == 1 || $this->pregnode->rightborder == 0)
            ? get_string('explain_time', 'qtype_preg') : get_string('explain_times', 'qtype_preg');
    }
}

/**
 * Class for tree's subexpression operator.
 */
class qtype_preg_authoring_tool_node_subexpr extends qtype_preg_authoring_tool_operator {

    protected function process_operator($graph, $id) {
        if ($this->pregnode->operands[0]->type != qtype_preg_node::TYPE_LEAF_META) {
            $operand = $this->operands[0]->create_graph($id);
        } else {
            $operand = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
            if ($this->operands[0]->pregnode->id == $id) {
                $operand->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=darkgreen');
                $operand->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $operand->subgraphs[0], -1);
                $operand->entries[] = end($operand->subgraphs[0]->nodes);
                $operand->exits[] = end($operand->subgraphs[0]->nodes);
            } else {
                $operand->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $operand, -1);
                $operand->entries[] = end($operand->nodes);
                $operand->exits[] = end($operand->nodes);
            }
        }

        $label = ($this->pregnode->number != -1 ? get_string('explain_subexpression', 'qtype_preg') . $this->pregnode->number : '');

        $generated = $this->handler->is_node_generated($this->pregnode);

        $subexpr = new qtype_preg_explaining_graph_tool_subgraph(
                        $label,
                        ($this->pregnode->userinscription[0]->data != '(?i:...)')
                            ? ($generated ? 'solid; color=invis; bgcolor=white' : 'solid; color=black')
                            : 'filled; color=lightgrey',
                        $this->pregnode->id
                    );
        $subexpr->assume_subgraph($operand);

        $graph->subgraphs[] = $subexpr;
        $graph->entries[] = end($operand->entries);
        $graph->exits[] = end($operand->exits);
    }
}

/**
 * Class for tree's conditional subexpression operator.
 */
class qtype_preg_authoring_tool_node_cond_subexpr extends qtype_preg_authoring_tool_operator {

    public function __construct($node, $handler) {
        parent::__construct($node, $handler);
        if (count($this->operands) == 3) {
            $this->condid = $this->operands[0]->pregnode->id;
        }
    }

    protected function process_operator($graph, $id) {
        $condsubexpr = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=black', $this->pregnode->id);
        $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=purple', 0.1 + $this->pregnode->id);
        $isassert = false;
        $tmp = null;

        if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {

            $a = new stdClass();
            $a->number = $this->pregnode->number;
            $a->name = $this->pregnode->number;

            $condsubexpr->subgraphs[0]->nodes[] =
                new qtype_preg_explaining_graph_tool_node(
                        array(is_integer($this->pregnode->number) ?
                                get_string('description_backref', 'qtype_preg', $a) :
                                get_string('description_backref_name', 'qtype_preg', $a)),
                        'ellipse', 'blue', $condsubexpr->subgraphs[0], -1
                        );

        } else if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION) {

            $condsubexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
                                                        array(get_string('description_recursion_all', 'qtype_preg')),
                                                        'ellipse',
                                                        'blue',
                                                        $condsubexpr->subgraphs[0],
                                                        -1
                                                    );

        } else if ($this->pregnode->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {

            $condsubexpr->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
                                                        array(get_string('explain_define', 'qtype_preg')),
                                                        'ellipse',
                                                        'blue',
                                                        $condsubexpr->subgraphs[0],
                                                        -1
                                                    );

        } else {
            $isassert = true; $index = 1;
            if (count($this->operands) == 3) {
                $index = 2;
            }
            $tmp = $this->operands[$index]->create_graph($id);

            $condsubexpr->subgraphs[0]->assume_subgraph($tmp);
        }

        $point = count($condsubexpr->subgraphs[0]->nodes) ? $condsubexpr->subgraphs[0]->nodes[0] : $condsubexpr->subgraphs[0]->subgraphs[0]->nodes[0];
        $condsubexpr->subgraphs[0]->entries[] = $point;
        $condsubexpr->subgraphs[0]->exits[] = $point;

        $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(
                                            $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE ? '' : '',
                                            'dashed; color=purple',
                                            0.2 + $this->pregnode->id
                                        );
        $tmp = $this->operands[0]->create_graph($id);
        $condsubexpr->subgraphs[1]->assume_subgraph($tmp);
        $condsubexpr->subgraphs[1]->entries[] = end($tmp->entries);
        $condsubexpr->subgraphs[1]->exits[] = end($tmp->exits);

        if (((count($this->operands) == 2 && !$isassert) || (count($this->operands) == 3 && $isassert))
            && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            $condsubexpr->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'dashed; color=purple', 0.3 + $this->pregnode->id);
            $tmp = $this->operands[1]->create_graph($id);
            $condsubexpr->subgraphs[2]->assume_subgraph($tmp);
            $condsubexpr->subgraphs[2]->entries[] = end($tmp->entries);
            $condsubexpr->subgraphs[2]->exits[] = end($tmp->exits);
        }

        $graph->subgraphs[] = $condsubexpr;
        $graph->entries[] = $point;

        if (((count($this->operands) == 2 && !$isassert) || (count($this->operands) == 3 && $isassert))
            && $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);
            $graph->exits[] = $graph->subgraphs[0]->nodes[0];
            $graph->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph->subgraphs[0], -1);

            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $point, $graph->subgraphs[0]->nodes[1], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $graph->subgraphs[0]->nodes[1], $condsubexpr->subgraphs[1]->entries[0], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $condsubexpr->subgraphs[1]->exits[0], $graph->exits[0], $condsubexpr);

            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('false', $graph->subgraphs[0]->nodes[1], $condsubexpr->subgraphs[2]->entries[0], $condsubexpr);
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('', $condsubexpr->subgraphs[2]->exits[0], $graph->exits[0], $condsubexpr);
        } else {
            $graph->exits[] = $condsubexpr->subgraphs[1]->exits[0];
            $condsubexpr->links[] = new qtype_preg_explaining_graph_tool_link('true', $point, $condsubexpr->subgraphs[1]->entries[0], $condsubexpr);
        }
    }
}

/**
 * Class for tree's assert operator.
 */
class qtype_preg_authoring_tool_node_assert extends qtype_preg_authoring_tool_operator {

    private static $linkoptions = array(
                                        qtype_preg_node_assert::SUBTYPE_PLA => 'normal, color="green"',
                                        qtype_preg_node_assert::SUBTYPE_NLA => 'normal, color="red"',
                                        qtype_preg_node_assert::SUBTYPE_PLB => 'inv, color="green"',
                                        qtype_preg_node_assert::SUBTYPE_NLB => 'inv, color="red"'
                                    );

    protected function process_operator($graph, $id) {
        $operand = $this->operands[0]->create_graph($id);

        $color = (($this->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLA || $this->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLB) ? 'green' : 'red');

        $sub = new qtype_preg_explaining_graph_tool_subgraph(
                    '',
                    'solid; edge[style=dotted, color=' . $color . ']; node[style=dashed, color=' . $color . ']; color=grey',
                    $this->pregnode->id
                );
        $sub->assume_subgraph($operand);

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

        $graph->links[] = new qtype_preg_explaining_graph_tool_link(
                                '',
                                $graph->nodes[count($graph->nodes) - 1],
                                $operand->entries[0], $graph,
                                self::$linkoptions[$this->pregnode->subtype]
                            );

        $graph->subgraphs[] = $sub;
        $graph->entries[] = $graph->nodes[count($graph->nodes) - 1];
        $graph->exits[] = $graph->nodes[count($graph->nodes) - 1];
    }
}
