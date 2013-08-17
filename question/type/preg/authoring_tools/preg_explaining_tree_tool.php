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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_tree_nodes.php');

class qtype_preg_explaining_tree_tool extends qtype_preg_dotbased_authoring_tool {

    public $rankdir = false;

    public function __construct($regex = null, $options = null, $engine = null, $notation = null, $rankdirlr = false) {
        parent::__construct($regex, $options, $engine, $notation);
        $this->rankdir = $rankdirlr;
    }

    public function name() {
        return 'explaining_tree_tool';
    }

    protected function is_preg_node_acceptable($pregnode) {
        // Well, everything that was parsed can be displayed to user.
        return true;
    }

    protected function node_infix() {
        // Nodes should be named like qtype_preg_explaining_tree_node_concat.
        // This allows us to use the inherited get_engine_node_name() method.
        return 'explaining_tree';
    }

    protected function json_key() {
        return 'tree_src';
    }

    protected function generate_json_for_empty_regex(&$json_array, $id) {
        $json_array[$this->json_key()] = 'Nothing to draw for empty regex';
    }

    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
        $json_array[$this->json_key()] = 'Your regex contains errors, so I can\'t build the interactive tree!';
    }

    /**
     * Generate image and map for interative tree
     *
     * @param array $json_array contains link on image and text map of interactive tree
     */
    protected function generate_json_for_accepted_regex(&$json_array, $id) {
        $context = new qtype_preg_dot_node_context(true, $id);
        $dotscript = $this->get_dst_root()->dot_script($context, $this->rankdir);
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json_array[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);

        // Pass the map and its DOM id via json array.
        $json_array['map'] = qtype_preg_regex_handler::execute_dot($dotscript, 'cmapx');
        /*$json_array['map_id'] = '#' . qtype_preg_explaining_tree_node::get_graph_name();*/
    }
}
