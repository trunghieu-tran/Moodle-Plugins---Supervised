<?php
/**
 * Creates authoring tools form.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
global $PAGE;
global $DB;

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/edit_ast_preg_form.php');
require_once($CFG->libdir . '/questionlib.php');

$PAGE->set_context(context_system::instance());

// Get and validate question id.
//$id = required_param('id', PARAM_INT);
/*$id = 1;
$question = question_bank::load_question($id);

// Were we given a particular context to run the question in?
// This affects things like filter settings, or forced theme or language.
if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $context = context_module::instance($cmid);

} else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    require_login($courseid);
    $context = context_course::instance($courseid);

} else {
    require_login();
    $category = $DB->get_record('question_categories',
            array('id' => $question->category), '*', MUST_EXIST);
    $context = context::instance_by_id($category->contextid);
    $PAGE->set_context($context);
    // Note that in the other cases, require_login will set the correct page context.
}*/

//Instantiate simplehtml_form
$mform = new qtype_preg_authoring_tool_form();
$mform->display();

?>
