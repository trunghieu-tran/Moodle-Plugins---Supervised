<?php
require_once('../../../config.php');

$courseid   = required_param('courseid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // lessontype id (only for edit mode)
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
//require_capability('block/supervised:writelessontypes', $PAGE->context);
$PAGE->set_url('/blocks/supervised/lessontypes/addedit.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
include("breadcrumbs.php");


// Initializing variables depending of mode.
if(!$id){   // Add mode.
    $title = get_string('addlessontypepagetitle', 'block_supervised');
    $heading = get_string("addingnewlessontype", 'block_supervised');
} else{     // Edit mode.
    if (! $lessontype = $DB->get_record("block_supervised_lessontype", array("id"=>$id, "courseid"=>$courseid))) {
        print_error(get_string("invalidlessontypeid", 'block_supervised'));
    }
    $title = get_string('editlessontypepagetitle', 'block_supervised');
    $heading = get_string("editinglessontype", 'block_supervised');
    
    $toform['id']   = $lessontype->id;
    $toform['name'] = $lessontype->name;
}

$PAGE->set_title($title);

// Prepare form.
$mform = "addedit_form.php";
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new addedit_lessontype_form();
$toform['courseid'] = $courseid;
$mform->set_data($toform);

if($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if(!$id){   // Add mode.
        // TODO Logging
        if (!$DB->insert_record('block_supervised_lessontype', $fromform)) {
            print_error('insertlessontypeerror', 'block_supervised');
        }
    } else{     // Edit mode.
        // TODO Logging
        if (!$DB->update_record('block_supervised_lessontype', $fromform)) {
            print_error('insertlessontypeerror', 'block_supervised');
        }

    }
    $url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // form didn't validate or this is the first display
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 3);
    $mform->display();
    echo $OUTPUT->footer();
}
