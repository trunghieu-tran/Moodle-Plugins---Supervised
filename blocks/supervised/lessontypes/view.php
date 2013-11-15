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
$PAGE->set_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('lessontypespagetitle', 'block_supervised'));
$PAGE->set_heading(get_string('lessontypesheader', 'block_supervised'));

// Add links into Administration block.
include("../administrationlinks.php");

$site = get_site();
// Display header.
echo $OUTPUT->header();

// Display page content.

// Prepare table data
$lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$courseid));
$tabledata = array();
foreach ($lessontypes as $id=>$lessontype) {
    $editurl = new moodle_url('/blocks/supervised/lessontypes/edit.php', array('id' => $id));
    $deleteurl = new moodle_url('/blocks/supervised/lessontypes/delete.php', array('id' => $id));
    $iconedit = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit', 'block_supervised')));
    $icondelete = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete', 'block_supervised')));
    $tabledata[] = array($lessontype->name . $iconedit . $icondelete);
}
// Build table.
$table = new html_table();
$table->head = array(get_string('lessontype', 'block_supervised'));
$table->data = $tabledata;
echo html_writer::table($table);


//$icon = new moodle_action_icon();
//$icon->image->src = $OUTPUT->old_icon_url('moodlelogo');
//$icon->image->alt = 'What is moodle?';
//$icon->link->url = new moodle_url('http://domain.com/index.php');
//$icon->add_confirm_action('Are you sure?'); // Optional. Equivalent to doing $icon->link->add_confirm_action('Are you sure?');
//echo $OUTPUT->action_icon($icon);



// Display footer.
echo $OUTPUT->footer();

?>