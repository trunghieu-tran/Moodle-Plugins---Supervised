<?php
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_supervised'));
$lessontypesurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->navbar->add(get_string('lessontypesbreadcrumb', 'block_supervised'), $lessontypesurl);

?>