<?php
require_once('../../../config.php');

$courseid   = required_param('courseid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // session id (only for edit mode)
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}

require_login($course);
$PAGE->set_url('/blocks/supervised/sessions/addedit.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
include("breadcrumbs.php");


// Initializing variables depending of mode.
if(!$id){   // Add mode.
    $title = get_string('addsessionpagetitle', 'block_supervised');
    $heading = get_string("addingnewsession", 'block_supervised');

    // Setting default values
    $toform['courseid'] = $courseid;
} else{     // Edit mode.
    if (! $session = $DB->get_record("block_supervised_session", array("id"=>$id))) {
        print_error(get_string("invalidsessionid", 'block_supervised'));
    }
    $title = get_string('editsessionpagetitle', 'block_supervised');
    $heading = get_string("editingsession", 'block_supervised');
    
    $toform['id']       = $session->id;
    $toform['courseid'] = $session->courseid;
    //$toform['name']     = $session->name;
    //$toform['iplist']   = $session->iplist;
    //$toform['active']   = $session->active;
}

$PAGE->set_title($title);

// Prepare form.
$mform = "addedit_form.php";
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new addedit_session_form(null, array('courseid' => $courseid));

$mform->set_data($toform);

if($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if(!$id){   // Add mode.
        // TODO Logging
        /*if (!$DB->insert_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }*/
    } else{     // Edit mode.
        // TODO Logging
        /*if (!$DB->update_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }*/

    }
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // form didn't validate or this is the first display
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 3);
    $mform->display();
    echo $OUTPUT->footer();
}
