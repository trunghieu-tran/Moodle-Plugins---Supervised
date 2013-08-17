<?php
/**
 * Create interactive tree, explain graph and description.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_description_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_tree_tool.php');

/**
 * Generates json array which stores authoring tools' content.
 */
function qtype_preg_get_json_array() {
    global $CFG;
    $json = array();

    $regex = optional_param('regex', '', PARAM_RAW);
    $id = optional_param('id', '', PARAM_INT);
    $tree_orientation = optional_param('tree_orientation', '', PARAM_TEXT);
    $notation = optional_param('notation', '', PARAM_RAW);
    $engine = optional_param('engine', '', PARAM_RAW);

    $rankdirlr = $tree_orientation == 'horizontal' ? true : false;

    // Array with authoring tools
    $tools = array(
        'tree' => new qtype_preg_explaining_tree_tool($regex, null, $engine, $notation, $rankdirlr),
        'graph' => new qtype_preg_explaining_graph_tool($regex, null, $engine, $notation),
        'description' => new qtype_preg_description_tool($regex, null, $engine, $notation)
    );

    // Fill the json array.
    foreach($tools as $tool) {
        $tool->generate_json($json, $regex, $id);
    }

    return $json;
}

$json = qtype_preg_get_json_array();
echo json_encode($json);
