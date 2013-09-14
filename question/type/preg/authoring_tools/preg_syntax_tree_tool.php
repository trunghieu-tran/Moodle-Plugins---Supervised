<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_syntax_tree_nodes.php');

class qtype_preg_syntax_tree_tool extends qtype_preg_dotbased_authoring_tool {

    public $rankdir = false;

    public function __construct($regex = null, $options = null, $engine = null, $notation = null, $rankdirlr = false, $selection = null) {
        parent::__construct($regex, $options, $engine, $notation);
        if ($selection !== null) {
            $idcounter = $this->parser->get_max_id() + 1;
            $this->ast_root = $this->ast_root->node_by_regex_fragment($selection->indfirst, $selection->indlast, $idcounter);
            $this->build_dst();
        }
        $this->rankdir = $rankdirlr;
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    public function name() {
        return 'syntax_tree_tool';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function node_infix() {
        return 'syntax_tree';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return true;
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function json_key() {
        return 'tree_src';
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function generate_json_for_accepted_regex(&$json, $id = -1) {
        $context = new qtype_preg_dot_node_context(true, $id);
        $dotscript = $this->get_dst_root()->dot_script($context, $this->rankdir);
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);

        // Pass the map and its DOM id via json array.
        $json['map'] = qtype_preg_regex_handler::execute_dot($dotscript, 'cmapx');
        /*$json['map_id'] = '#' . qtype_preg_syntax_tree_node::get_graph_name();*/
    }
}
