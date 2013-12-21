<?php
require_once('../../../config.php');
require_once('../lib.php');

global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}

require_login($course);
// TODO Capabilities
//require_capability('block/supervised:readlessontypes', $PAGE->context);
$PAGE->set_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('lessontypespagetitle', 'block_supervised'));
include("breadcrumbs.php");

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("lessontypesview", 'block_supervised'), 3);

// Prepare table data
$lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$courseid), 'name');
$tabledata = array();
foreach ($lessontypes as $id=>$lessontype) {
    // Prepare icons.
    $editurl = new moodle_url('/blocks/supervised/lessontypes/addedit.php', array('id' => $id, 'courseid' => $courseid));
    $deleteurl = new moodle_url('/blocks/supervised/lessontypes/delete.php', array('courseid' => $courseid, 'id' => $id));
    $iconedit = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
    $icondelete = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
    // Combine new row.
    $tabledata[] = array(
        $lessontype->name,
        can_delete_lessontype($id) ? $iconedit . $icondelete : $iconedit
    );
}

$addurl = new moodle_url('/blocks/supervised/lessontypes/addedit.php', array('courseid' => $courseid));
echo ('<a href="'.$addurl.'">' . get_string('addlessontype', 'block_supervised') . '</a>');

// Build table.
$table = new html_table();
$headname = get_string('lessontype', 'block_supervised');
$headedit = get_string('edit');
$table->head = array($headname, $headedit);
$table->data = $tabledata;
echo html_writer::table($table);

// Display footer.
echo $OUTPUT->footer();

?>