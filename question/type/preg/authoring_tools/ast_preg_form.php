<?php
/**
 * Creates authoring tools form.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/edit_ast_preg_form.php');

$PAGE->set_url('/question/type/preg/authoring_tools/ast_preg_form.php');
$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();

$mform = new qtype_preg_authoring_tool_form();
$mform->display();

echo $OUTPUT->footer();
