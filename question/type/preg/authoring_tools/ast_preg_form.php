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

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/edit_ast_preg_form.php');
require_once($CFG->libdir . '/questionlib.php');

$PAGE->set_context(context_system::instance());

//Instantiate simplehtml_form
$mform = new qtype_preg_authoring_tool_form();
$mform->display();
