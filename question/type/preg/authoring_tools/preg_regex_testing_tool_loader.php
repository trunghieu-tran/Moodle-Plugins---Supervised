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
    global $CFG;
    $json_array = array();
    $regextext = optional_param('regex', '', PARAM_RAW);
    $answers = optional_param('answer', '', PARAM_RAW);
    $matcher = optional_param('matcher', '', PARAM_RAW);
    $usecase = optional_param('usecase', '', PARAM_INT);
    $exactmatch = optional_param('exactmatch', '', PARAM_INT);
    $notation = optional_param('notation', '', PARAM_RAW);
    
    if($usecase == 1){
        $usecase = true;
    } else {
        $usecase = false;
    }
    
    if($exactmatch == 1){
        $exactmatch = true;
    } else {
        $exactmatch = false;
    }
    
    $regex_testing_tool = new qtype_preg_regex_testing_tool($regextext, $answers, $matcher, $usecase, $exactmatch);
    $regex_testing_tool->generate_json($json_array);

    return $json_array;
}

$json_array = qtype_preg_get_json_array();
echo json_encode($json_array);
