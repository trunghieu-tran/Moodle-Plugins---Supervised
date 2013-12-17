<?php
require_once('../../../config.php');
require_once('../lib.php');

$id         = required_param('id', PARAM_INT);              // classroom id
$courseid   = required_param('courseid', PARAM_INT);
$delete     = optional_param('delete', '', PARAM_ALPHANUM); // delete confirmation hash

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}


$site = get_site();
require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);
$PAGE->set_url('/blocks/supervised/classrooms/delete.php', array('id' => $id, 'courseid' => $courseid));
include("breadcrumbs.php");

if (! $classroom = $DB->get_record("block_supervised_classroom", array("id"=>$id))) {
    print_error(get_string("invalidclassroomid", 'block_supervised'));
}

if (!can_delete_classroom($id)) {
    print_error(get_string("cannotdeleteclassroom", 'block_supervised'));
}

// Show form first time.
if (! $delete) {
    $strdeletecheck = get_string("deletecheck", "", $classroom->name);
    $strdeleteclassroomcheck = get_string("deleteclassroomcheck", 'block_supervised');

    $PAGE->navbar->add($strdeletecheck);
    $PAGE->set_title("$course->shortname: $strdeletecheck");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $message = "$strdeleteclassroomcheck<br /><br />" . $classroom->name;

    echo $OUTPUT->confirm($message, "delete.php?id=$id&courseid=$courseid&delete=".md5($classroom->name), "view.php?courseid=$courseid");

    echo $OUTPUT->footer();
    exit;
}

if ($delete != md5($classroom->name)) {
    print_error("invalidmd5");
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

// OK checks done, delete the classroom now.

// TODO Logging

$DB->delete_records('block_supervised_classroom', array('id'=>$id));
// Redirect to classrooms page
$url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
redirect($url);
