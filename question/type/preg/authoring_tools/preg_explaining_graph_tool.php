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
 * Defines explaining graph's handler class.
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

    public function __construct ($regex = null, $options = null) {
        parent::__construct($regex, $options);
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    public function name() {
        return 'explaining_graph_tool';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function node_infix() {
        return 'explaining_graph';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    public function get_errors() {
        return qtype_preg_regex_handler::get_errors();
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function get_engine_node_name($nodetype, $nodesubtype) {
        if ($nodetype == qtype_preg_node::TYPE_NODE_FINITE_QUANT || $nodetype == qtype_preg_node::TYPE_NODE_INFINITE_QUANT) {
            return 'qtype_preg_explaining_graph_node_quant';
        }
        return parent::get_engine_node_name($nodetype, $nodesubtype);
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_LEAF_CONTROL:
                return get_string($pregnode->type, 'qtype_preg');
            default:
                return true;
        }
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function json_key() {
        return 'graph';
    }

    public function generate_html() {
        if ($this->regex->string() == '') {
            return $this->data_for_empty_regex();
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            return $this->data_for_unaccepted_regex();
        }
        $data = $this->data_for_accepted_regex();
        return '<img src="' . $data['img'] . '">';
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function data_for_accepted_regex() {
        $graph = $this->create_graph();
        $dotscript = $graph->create_dot();
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        return array(
            'img' => $rawdata
        );
    }

    /**
     * Creates graph which explaining regular expression.
     * @return explainning graph of regular expression.
     */
    public function create_graph() {
        $graph = $this->dstroot->create_graph();

        if ($this->options->exactmatch) {
            $graph->isexact = true;
        }

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(get_string('explain_begin', 'qtype_preg')), 'box', 'purple', $graph, -1, 'filled', 'purple');
        $graph->nodes[count($graph->nodes) - 1]->type = qtype_preg_explaining_graph_tool_node::TYPE_BOUNDARY;
        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(get_string('explain_end', 'qtype_preg')), 'box', 'purple', $graph, -1, 'filled', 'purple');
        $graph->nodes[count($graph->nodes) - 1]->type = qtype_preg_explaining_graph_tool_node::TYPE_BOUNDARY;

        if (count($graph->nodes) == 2 && count($graph->subgraphs) == 0) {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[0], $graph->nodes[count($graph->nodes) - 1], $graph);
        } else {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 2], $graph->entries[count($graph->entries) - 1], $graph);
            if ($graph->entries[count($graph->entries) - 1]->borderoftemplate !== null)
                $graph->links[count($graph->links)-1]->lhead = $graph->entries[count($graph->entries) - 1]->borderoftemplate;

            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->exits[count($graph->exits) - 1], $graph->nodes[count($graph->nodes) - 1], $graph);
            if ($graph->exits[count($graph->exits) - 1]->borderoftemplate !== null)
                $graph->links[count($graph->links)-1]->ltail = $graph->exits[count($graph->exits) - 1]->borderoftemplate;

            $graph->entries = array();
            $graph->exits = array();

            $graph->optimize_graph($graph);
        }

        return $graph;
    }
}
