<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


require_once('../../../config.php');
require_once('../lib.php');

$id         = required_param('id', PARAM_INT);              // Classroom id.
$courseid   = required_param('courseid', PARAM_INT);
$delete     = optional_param('delete', '', PARAM_ALPHANUM); // Delete confirmation hash.

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}


$site = get_site();
require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);
$PAGE->set_url('/blocks/supervised/classrooms/delete.php', array('id' => $id, 'courseid' => $courseid));
require("breadcrumbs.php");

if (! $classroom = $DB->get_record("block_supervised_classroom", array("id" => $id))) {
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

    echo $OUTPUT->confirm($message,
        "delete.php?id=$id&courseid=$courseid&delete=".md5($classroom->name),
        "view.php?courseid=$courseid");

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

// TODO Logging.

$DB->delete_records('block_supervised_classroom', array('id' => $id));
// Redirect to classrooms page.
$url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
redirect($url);
