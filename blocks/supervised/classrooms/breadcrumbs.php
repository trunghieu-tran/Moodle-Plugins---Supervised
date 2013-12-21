<?php
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_supervised'));
$url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('classroomsbreadcrumb', 'block_supervised'), $url);

?>