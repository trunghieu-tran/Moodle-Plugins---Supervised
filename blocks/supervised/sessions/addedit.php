<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once('sessionstate.php');
require_once('lib.php');
global $DB, $PAGE, $OUTPUT, $USER, $CFG;

$courseid   = required_param('courseid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // Session id (only for edit mode).
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error('invalidcourseid');
}

require_login($course);
$PAGE->set_url('/blocks/supervised/sessions/addedit.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
require('breadcrumbs.php');

// Check capabilities.
if (!  (has_capability('block/supervised:manageownsessions', $PAGE->context)
       || has_capability('block/supervised:manageallsessions', $PAGE->context))  ) {
    require_capability('block/supervised:manageownsessions', $PAGE->context);
}


// Initializing variables depending of mode.
$addnotspecified = 0;
$toform['courseid'] = $courseid;
if (!$id) {   // Add mode.
    $PAGE->navbar->add(get_string('plansessionnavbar', 'block_supervised'));
    $title = get_string('addsessionpagetitle', 'block_supervised');
    $heading = get_string('addingnewsession', 'block_supervised');

    // Find block instance.
    $coursecontext = context_course::instance($course->id);
    $blockrecord = $DB->get_record('block_instances', array('blockname' => 'supervised',
        'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
    $supervisedinstance = block_instance('supervised', $blockrecord);

    // Setting default values.
    $toform['teacherid']    = $USER->id;
    $toform['sendemail']    = 1;
    $toform['duration']     = $supervisedinstance->config->duration;
    $toform['lessontypeid'] = 0;
    $toform['coursename']   = html_writer::link(new moodle_url("/course/view.php?id={$course->id}"), $course->fullname);
} else {     // Edit mode.
    $PAGE->navbar->add(get_string('editsessionnavbar', 'block_supervised'));
    if (! $session = get_session($id)) {
        print_error(get_string('invalidsessionid', 'block_supervised'));
    }
    // Check capabilities for edit mode.
    if ( ($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
        || has_capability('block/supervised:manageallsessions', $PAGE->context)   ) {
        require_capability('block/supervised:manageownsessions', $PAGE->context);   // Print error.
    } else {
        // User wants edit session of other user.
        require_capability('block/supervised:manageallsessions', $PAGE->context);
    }

    // Check session state.
    if ($session->state != StateSession::PLANNED) {
        print_error(get_string('sessionediterror', 'block_supervised'));
    }

    $title = get_string('editsessionpagetitle', 'block_supervised');
    $heading = get_string('editingsession', 'block_supervised');

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

    // We should add 'Not specified' lesson type in editing mode if the session was created with this option.
    if ($session->lessontypeid == 0) {
        $addnotspecified = 1;
    }
}

$PAGE->set_title($title);

// Check if teachers and classrooms exist.
$teachersexist = (boolean)(get_users_by_capability($PAGE->context, array('block/supervised:supervise')));
$classroomsexist = $DB->record_exists('block_supervised_classroom', array('active' => 1));
if (!$teachersexist || !$classroomsexist) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 2);
    if (!$teachersexist) {
        echo get_string('enrollteacher', 'block_supervised');
        echo ' ' . html_writer::link(new moodle_url("/enrol/users.php?id={$course->id}"),
                get_string('gotoenrollment', 'block_supervised'));
        echo '<br/>';
    }
    if (!$classroomsexist) {
        echo get_string('createclassroom', 'block_supervised');
        echo ' ' . html_writer::link(new moodle_url("/blocks/supervised/classrooms/view.php?courseid={$course->id}"),
                get_string('gotoclassrooms', 'block_supervised'));
        echo '<br/>';
    }
    echo $OUTPUT->footer();
    exit;
}

// Prepare form.
$mform = 'addedit_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new addedit_session_form(null, array('courseid' => $courseid, 'addnotspecified' => $addnotspecified));



if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the sessions view page.
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if (!$id) {   // Add mode.
        $PAGE->navbar->add(get_string('plansessionnavbar', 'block_supervised'));
        $fromform->state    = StateSession::PLANNED;
        $fromform->timeend  = $fromform->timestart + ($fromform->duration) * 60;
        $classroom = $DB->get_record('block_supervised_classroom', array('id' => $fromform->classroomid));
        $fromform->iplist  = $classroom->iplist;

        if (!$newid = $DB->insert_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }
        update_users_in_session($fromform->groupid, $fromform->courseid, $newid);
        // TODO Logging.
        add_to_log($COURSE->id, 'role', 'plane session',
            'blocks/supervised/sessions/view.php?courseid='.$COURSE->id, '');
        // Send e-mail to teacher.
        if ($fromform->sendemail) {
            mail_newsession(get_session($newid), $USER);
        }
    } else {     // Edit mode.
        $fromform->timeend  = $fromform->timestart + ($fromform->duration) * 60;
        $classroom = $DB->get_record('block_supervised_classroom', array('id' => $fromform->classroomid));
        $fromform->iplist  = $classroom->iplist;
        if (!$DB->update_record('block_supervised_session', $fromform)) {
            print_error('insertsessionerror', 'block_supervised');
        }
        update_users_in_session($fromform->groupid, $fromform->courseid, $fromform->id);
        // TODO Logging.
        add_to_log($COURSE->id, 'role', 'edit session',
            'blocks/supervised/sessions/view.php?courseid='.$COURSE->id, '');
        // Send e-mail to teacher(s).
        if ($fromform->sendemail) {
            $oldteacherid = $session->teacherid;
            $newteacherid = $fromform->teacherid;
            if ($oldteacherid != $newteacherid) {
                // Send e-mail to both teachers if teacher has been changed.
                mail_newsession(get_session($fromform->id), $USER); // New session for new teacher.
                $session->messageforteacher = '';
                mail_removedsession($session, $USER);               // Removed session for old teacher.
            } else {
                mail_editedsession(get_session($fromform->id), $USER);
            }
        }
    }
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // Form didn't validate or this is the first display.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 2);

    $mform->set_data($toform);
    $mform->display();
    echo $OUTPUT->footer();
}
