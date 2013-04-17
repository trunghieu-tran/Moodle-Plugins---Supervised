<?php
/**
 * Create interactive tree, explain graph and description.
 *
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_description.php');
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_authors_tool_tree.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

/**
 * Generate array of links on image of interactive tree, explain graph, text map for interactive tree and text of description
 *
 * @param array $json_array contains author tool content
 */
abstract class author_json {

    /**
     * Generate json array, who store author tools content
     */
    public static function get_json_array() {

        global $CFG;

        $json_array = array();

        $regextext = optional_param('regex', '', PARAM_TEXT);

        //if(!empty($regextext)) {//regex not empty (owervise can't build tree)
            $id = optional_param('id', '', PARAM_INT);

            //array with author tools
            $tools = array(
                "tree" => new qtype_preg_author_tool_tree($regextext),
                "graph" => new qtype_preg_author_tool_explain_graph($regextext),
                "description" => new qtype_preg_author_tool_description($regextext),
            );

            //fill json array
            foreach($tools as $tool){
                $tool->generate_json($json_array, $regextext, $id);
            }
        /*} else {
            author_json::get_json_error($json_array);
        }*/

        return $json_array;
    }

    /**
     * Fill json_array information, when regex is empty
     *
     * @param array $json_array whith containt for author tool
     */
    private static function get_json_error(&$json_array) {
            $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot('digraph { "This place is for interactive tree" [color=white]; }', 'png'));
            $json_array['graph_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot('digraph { "This place is for explain graph" [color=white]; }', 'png'));
            $json_array['description'] = 'This place is for description!';
    }
}

$json_array = author_json::get_json_array();
echo json_encode($json_array);
