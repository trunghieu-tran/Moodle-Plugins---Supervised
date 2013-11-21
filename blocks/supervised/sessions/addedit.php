<?php
require_once('../../../config.php');
require_once('sessionstate.php');
global $DB, $PAGE, $OUTPUT, $USER;

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
$toform['courseid'] = $courseid;
if(!$id){   // Add mode.
    $title = get_string('addsessionpagetitle', 'block_supervised');
    $heading = get_string("addingnewsession", 'block_supervised');

    // Setting default values
    $toform['teacherid'] = $USER->id;
    $toform['sendemail'] = 1;
    $toform['duration']  = 90;
} else{     // Edit mode.
    if (! $session = $DB->get_record("block_supervised_session", array("id"=>$id))) {
        print_error(get_string("invalidsessionid", 'block_supervised'));
    }
    if ($session->state != StateSession::Planned) {
        print_error(get_string("invalidsessionid", 'block_supervised'));
    }
    $title = get_string('editsessionpagetitle', 'block_supervised');
    $heading = get_string("editingsession", 'block_supervised');
    
    $toform['id']               = $session->id;
    $toform['courseid']         = $session->courseid;
    $toform['classroomid']      = $session->classroomid;
    $toform['groupid']          = $session->groupid;
    $toform['teacherid']        = $session->teacherid;
    $toform['lessontypeid']     = $session->lessontypeid;
    $toform['timestart']        = $session->timestart;
    $toform['duration']         = $session->duration;
    $toform['timeend']          = $session->timeend;
    $toform['sendemail']        = $session->sendemail;
    $toform['sessioncomment']   = $session->sessioncomment;
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
        $fromform->state    = StateSession::Planned;
        $fromform->timeend  = $fromform->timestart + ($fromform->duration)*60;

        if (!$DB->insert_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }
    } else{     // Edit mode.
        // TODO Logging
        $fromform->timeend  = $fromform->timestart + ($fromform->duration)*60;
        if (!$DB->update_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }

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
