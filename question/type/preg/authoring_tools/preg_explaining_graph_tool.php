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
 * Defines explain graph's handler class.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');

/**
 * Class "handler" for regular expression's graph.
 */
class qtype_preg_explaining_graph_tool extends qtype_preg_dotbased_authoring_tool {

    public function __construct ($regex = null, $options = null, $engine = null, $notation = null) {
        parent::__construct($regex, $options, $engine, $notation);
    }

    /**
     * Creates graph which explaining regular expression.
     * @param id - identifier of node which will be picked out in image.
     * @return explainning graph of regular expression.
     */
    public function create_graph($id = -1) {
        $graph = $this->dst_root->create_graph($id);

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $graph, -1);
        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $graph, -1);

        if (count($graph->nodes) == 2 && count($graph->subgraphs) == 0) {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[0], $graph->nodes[count($graph->nodes) - 1], $graph);
        } else {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 2], $graph->entries[count($graph->entries) - 1], $graph);

            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->exits[count($graph->exits) - 1], $graph->nodes[count($graph->nodes) - 1], $graph);
            $graph->entries = array();
            $graph->exits = array();

            $graph->optimize_graph($graph, $graph);
        }

        return $graph;
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    public function name() {
        return 'explaining_graph_tool';
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function node_infix() {
        // Nodes should be named like qtype_preg_authoring_tool_node_concat.
        // This allows us to use the inherited get_engine_node_name() method.
        return 'authoring_tool';
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function get_engine_node_name($nodetype) {

        if ($nodetype == qtype_preg_node::TYPE_NODE_FINITE_QUANT ||
            $nodetype == qtype_preg_node::TYPE_NODE_INFINITE_QUANT) {
            return 'qtype_preg_authoring_tool_node_quant';
        }

        return parent::get_engine_node_name($nodetype);
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT:
            case qtype_preg_node::TYPE_LEAF_CONTROL:
            case qtype_preg_node::TYPE_NODE_ERROR:
                return false;
            default:
                return true;
        }
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function json_key() {
        return 'graph_src';
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function generate_json_for_empty_regex(&$json_array, $id) {
        $json_array[$this->json_key()] = 'Nothing to draw for empty regex';
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
        $json_array[$this->json_key()] = 'Your regex contains errors, so I can\'t build the explaining graph!';
    }

    /**
     * Generate image for explain graph.
     *
     * @param array $json_array contains link on image of explain graph.
     */
    protected function generate_json_for_accepted_regex(&$json_array, $id) {
        $graph = $this->create_graph($id);
        $dotscript = $graph->create_dot();
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json_array[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);
    }
}
