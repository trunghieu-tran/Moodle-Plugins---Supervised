<?php
require_once('../../../config.php');

$courseid   = required_param('courseid', PARAM_INT);
$blockid    = required_param('blockid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // lessontype id (only for edit mode)

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_supervised', $courseid);
}

$site = get_site();
require_login($course);
$PAGE->set_url('/blocks/supervised/lessontypes/mod.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('addlessontypepagetitle', 'block_supervised'));
include("breadcrumbs.php");

// Add mode
if($id == "") {
    $modmoodleform = "mod_form.php";
    if (file_exists($modmoodleform)) {
        require_once($modmoodleform);
    } else {
        print_error('noformdesc');
    }

    $mform = new mod_lessontype_form();

    $toform['blockid'] = $blockid;
    $toform['courseid'] = $courseid;
    $mform->set_data($toform);
    
    if($mform->is_cancelled()) {
        // Cancelled forms redirect to the course main page.
         $courseurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('blockid' => $blockid, 'courseid' => $courseid));
        redirect($courseurl);
    } else if ($fromform = $mform->get_data()) {
        // Store the submitted data.
        // TODO Logging
        //add_to_log(SITEID, "course", "delete", "view.php?id=$course->id", "$course->fullname (ID $course->id)");
        $courseurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('blockid' => $blockid, 'courseid' => $courseid));
        $fromform->courseid = $courseid;
        if (!$DB->insert_record('block_supervised_lessontype', $fromform)) {
            print_error('insertlessontypeerror', 'block_supervised');
        }
        redirect($courseurl);
    } else {
        // form didn't validate or this is the first display
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string("addingnewlessontype", 'block_supervised'), 3);
        $mform->display();
        echo $OUTPUT->footer();
    }
}