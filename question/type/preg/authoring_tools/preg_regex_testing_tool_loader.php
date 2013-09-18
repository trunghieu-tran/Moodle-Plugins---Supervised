<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
global $PAGE;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool.php');
$PAGE->set_context(context_system::instance());

/**
 * Generates json array which stores regex testing content.
 */
function qtype_preg_get_json_array() {
    $regex = optional_param('regex', '', PARAM_RAW);
    $engine = optional_param('engine', '', PARAM_RAW);
    $notation = optional_param('notation', '', PARAM_RAW);
    $exactmatch = (bool)optional_param('exactmatch', '', PARAM_INT);
    $usecase = (bool)optional_param('usecase', '', PARAM_INT);
    $strings = optional_param('strings', '', PARAM_RAW);

    $json = array();
    $regex_testing_tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
    $regex_testing_tool->generate_json($json);

    return $json;
}

$json = qtype_preg_get_json_array();
echo json_encode($json);
