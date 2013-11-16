<?php
require_once('../../../config.php');
require_once('../lib.php');

$id         = required_param('id', PARAM_INT);              // lessontype id
$courseid   = required_param('courseid', PARAM_INT);
$blockid    = required_param('blockid', PARAM_INT);
$delete     = optional_param('delete', '', PARAM_ALPHANUM); // delete confirmation hash

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_supervised', $courseid);
}


$site = get_site();
require_login($course);
$PAGE->set_url('/blocks/supervised/lessontypes/delete.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
include("breadcrumbs.php");

if (! $lessontype = $DB->get_record("block_supervised_lessontype", array("id"=>$id, "courseid"=>$courseid))) {
    print_error(get_string("invalidlessontypeid", 'block_supervised'));
}

if (!can_delete_lessontype($id)) {
    print_error(get_string("cannotdeletelessontype", 'block_supervised'));
}

// Show form first time.
if (! $delete) {
    $strdeletecheck = get_string("deletecheck", "", $lessontype->name);
    $strdeletelessontypecheck = get_string("deletelessontypecheck", 'block_supervised');

    $PAGE->navbar->add($strdeletecheck);
    $PAGE->set_title("$course->shortname: $strdeletecheck");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $message = "$strdeletelessontypecheck<br /><br />" . $lessontype->name;

    echo $OUTPUT->confirm($message, "delete.php?id=$id&blockid=$blockid&courseid=$courseid&delete=".md5($lessontype->name), "view.php?blockid=$blockid&courseid=$courseid");

    echo $OUTPUT->footer();
    exit;
}

if ($delete != md5($lessontype->name)) {
    print_error("invalidmd5");
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

// OK checks done, delete the lessontype now.

// TODO Logging
//add_to_log(SITEID, "course", "delete", "view.php?id=$course->id", "$course->fullname (ID $course->id)");
$DB->delete_records('block_supervised_lessontype', array('id'=>$id));
// Redirect to lessontypes page
$url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('blockid' => $blockid, 'courseid' => $courseid));
redirect($url);
