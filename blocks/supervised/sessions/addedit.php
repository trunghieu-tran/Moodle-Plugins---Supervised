<?php
require_once('../../../config.php');
require_once('sessionstate.php');
require_once('lib.php');
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

// Check capabilities.
if(!  (has_capability('block/supervised:manageownsessions', $PAGE->context)
       || has_capability('block/supervised:manageallsessions', $PAGE->context))  ){
    require_capability('block/supervised:manageownsessions', $PAGE->context);
}


// Initializing variables depending of mode.
$toform['courseid'] = $courseid;
if(!$id){   // Add mode.
    $PAGE->navbar->add(get_string("plansessionnavbar", 'block_supervised'));
    $title = get_string('addsessionpagetitle', 'block_supervised');
    $heading = get_string("addingnewsession", 'block_supervised');

    // Setting default values
    $toform['teacherid']    = $USER->id;
    $toform['sendemail']    = 1;
    $toform['duration']     = 90;
    $toform['coursename']   = $course->fullname;
} else{     // Edit mode.
    $PAGE->navbar->add(get_string("editsessionnavbar", 'block_supervised'));
    if (! $session = get_session($id)) {
        print_error(get_string("invalidsessionid", 'block_supervised'));
    }
    // Check capabilities for edit mode.
    if ( ! (($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
        || has_capability('block/supervised:manageallsessions', $PAGE->context))   ){
        require_capability('block/supervised:manageownsessions', $PAGE->context);   // Print error.
    }
    else{
        // User wants edit session of other user.
        require_capability('block/supervised:manageallsessions', $PAGE->context);
    }

    // Check session state.
    if ($session->state != StateSession::Planned) {
        print_error(get_string("sessionediterror", 'block_supervised'));
    }

    $title = get_string('editsessionpagetitle', 'block_supervised');
    $heading = get_string("editingsession", 'block_supervised');
    
    $toform['id']               = $session->id;
    $toform['coursename']       = $course->fullname;
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



if($mform->is_cancelled()) {
    // Cancelled forms redirect to the sessions view page.
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if(!$id){   // Add mode.
        $PAGE->navbar->add(get_string("plansessionnavbar", 'block_supervised'));
        $fromform->state    = StateSession::Planned;
        $fromform->timeend  = $fromform->timestart + ($fromform->duration)*60;

        if (!$newid = $DB->insert_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }
        // TODO Logging
        // Send e-mail to teacher.
        if($fromform->sendemail){
            mail_newsession(get_session($newid), $USER);
        }
    } else{     // Edit mode.
        $fromform->timeend  = $fromform->timestart + ($fromform->duration)*60;
        if (!$DB->update_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }
        // TODO Logging
        // Send e-mail to teacher(s).
        if($fromform->sendemail){
            $oldteacherid = $session->teacherid;
            $newteacherid = $fromform->teacherid;
            if($oldteacherid != $newteacherid){
                // Send e-mail to both teachers if teacher has been changed.
                mail_newsession(get_session($fromform->id), $USER); // new session for new teacher
                $session->messageforteacher = '';
                mail_removedsession($session, $USER);               // removed session for old teacher
            }
            else{
                mail_editedsession(get_session($fromform->id), $USER);
            }
        }
    }
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // form didn't validate or this is the first display
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 2);

    $mform->set_data($toform);
    $mform->display();
    echo $OUTPUT->footer();
}
