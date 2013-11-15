<?php
 
require_once('../../../config.php');
 
global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_supervised', $courseid);
}

require_login($course);
$PAGE->set_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('classroomspagetitle', 'block_supervised'));
$PAGE->set_heading(get_string('classroomsheader', 'block_supervised'));

// Add links into Administration block.
include("../administrationlinks.php");

$site = get_site();
// Display header.
echo $OUTPUT->header();

// Display page content.
// Prepare table data
$classrooms = $DB->get_records('block_supervised_classroom');
$tabledata = array();
foreach ($classrooms as $classroom) {
    $tabledata[] = array($classroom->name, $classroom->iplist);
}
// Build table.
$table = new html_table();
$table->head = array(get_string('classroom', 'block_supervised'), get_string('iplist', 'block_supervised'));
$table->data = $tabledata;
echo html_writer::table($table);

// Display footer.
echo $OUTPUT->footer();

?>