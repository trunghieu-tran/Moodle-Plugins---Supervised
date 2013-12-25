<?php
require_once('../../../config.php');
 
global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$id         = required_param('id', PARAM_INT);
$site = get_site();

$course = $DB->get_record('course', array('id' => $courseid));

require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_supervised', $courseid);
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}

if (! $classroom = $DB->get_record("block_supervised_classroom", array("id"=>$id))) {
    print_error(get_string("invalidclassroomid", 'block_supervised'));
}

// Change active field.
$classroom->active = (int)!($classroom->active);
// Update DB.
if (!$DB->update_record('block_supervised_classroom', $classroom)) {
    print_error('insertclassroomerror', 'block_supervised');
}
// Redirect.
$url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
redirect($url);
?>