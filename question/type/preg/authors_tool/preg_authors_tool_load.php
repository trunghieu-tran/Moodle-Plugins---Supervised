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

/**
 * Generate array of links on image of interactive tree, explain graph, text map for interactive tree and text of description
 * 
 * @param array $json_array contains author tool content
 */
function get_json_array() {
    
    $json_array = array();

    $regextext = optional_param('regex', '', PARAM_TEXT);
    $id = optional_param('id', '', PARAM_INT);
    
    /*$regextext = optional_param('regex', '', PARAM_TEXT);
    $json_array['regex'] = $regextext;*/

    $tree = new qtype_preg_author_tool_tree($regextext);
    $graph = new qtype_preg_author_tool_explain_graph($regextext);
    $description = new qtype_preg_author_tool_description($regextext);
    
    $tree->generate_json($json_array, $regextext, $id);
    $graph->generate_json($json_array, $regextext, $id);
    $description->generate_json($json_array, $regextext, $id);
    
    return $json_array;
    
}

$json_array = get_json_array();
echo json_encode($json_array);
