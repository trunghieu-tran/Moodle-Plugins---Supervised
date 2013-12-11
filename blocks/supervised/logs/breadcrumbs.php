<?php
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_supervised'));

$sessionsurl = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('sessionsbreadcrumb', 'block_supervised'), $sessionsurl);

$logsurl = new moodle_url('/blocks/supervised/logs/view.php', array('courseid' => $courseid, 'sessionid' => $sessionid));
$PAGE->navbar->add(get_string('logsbreadcrumb', 'block_supervised'), $logsurl);

?>