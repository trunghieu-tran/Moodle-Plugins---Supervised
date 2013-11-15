<?php
// Add breadcrumbs and links into Administration block.
$settingsnode = $PAGE->settingsnav->add(get_string('supervisedsettings', 'block_supervised'));
$classroomsurl = new moodle_url('/blocks/supervised/classrooms.php', array('courseid' => $courseid, 'blockid' => $blockid));
$lessontypesurl = new moodle_url('/blocks/supervised/lessontypes.php', array('courseid' => $courseid, 'blockid' => $blockid));
$settingsnode->add(get_string('classroomsbreadcrumb', 'block_supervised'), $classroomsurl);
$settingsnode->add(get_string('lessontypesbreadcrumb', 'block_supervised'), $lessontypesurl);
?>