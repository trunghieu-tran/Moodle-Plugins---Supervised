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
global $DB, $PAGE, $OUTPUT, $USER;

$id         = required_param('id', PARAM_INT);              // Session id.
$courseid   = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

$site = get_site();
require_login($course);
$PAGE->set_url('/blocks/supervised/sessions/delete.php', array('id' => $id, 'courseid' => $courseid));
$PAGE->set_pagelayout('standard');
require('breadcrumbs.php');

if (! $session = get_session($id)) {
    print_error(get_string('invalidsessionid', 'block_supervised'));
}

// Check capabilities.
if ($session->state == StateSession::FINISHED) {
    // Only user with managefinishedsessions capability can remove finished sessions.
    require_capability('block/supervised:managefinishedsessions', $PAGE->context);
} else {
    if ( ($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
            || has_capability('block/supervised:manageallsessions', $PAGE->context)  ) {
        require_capability('block/supervised:manageownsessions', $PAGE->context);   // Print error.
    } else {
        // User wants remove session of other user.
        require_capability('block/supervised:manageallsessions', $PAGE->context);
    }
}

if ($session->state == StateSession::ACTIVE) {
    print_error(get_string('sessiondeleteerror', 'block_supervised'));
}




// Prepare form.
$mform = 'delete_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new delete_session_form();



if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the sessions view page.
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Delete session.
    // TODO Logging.
    add_to_log($COURSE->id, 'role', 'delete session',
        'blocks/supervised/sessions/view.php?courseid='.$COURSE->id, '');
    $DB->delete_records('block_supervised_session', array('id' => $id));
    $DB->delete_records('block_supervised_user', array('sessionid' => $id));
    // Send e-mail to teacher.
    if ($fromform->notifyteacher) {
        $session->messageforteacher = $fromform->messageforteacher;
        mail_removedsession($session, $USER);
    }
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // The first display.
    $PAGE->navbar->add(get_string('deletesessionnavbar', 'block_supervised'));
    $PAGE->set_title(get_string('sessiondeletetitle', 'block_supervised'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('sessiondeleteheader', 'block_supervised'), 2);

    $strftimedatetime = get_string('strftimerecent');

    $toform['id']               = $id;
    $toform['courseid']         = $courseid;
    $toform['coursename']       = html_writer::link(new moodle_url("/course/view.php?id={$courseid}"),
        $session->coursename);
    $toform['classroomname']    = $session->classroomname;
    $toform['groupname']        = $session->groupname == '' ?
        get_string('allgroups', 'block_supervised') :
        $session->groupname;
    $toform['teachername']      = html_writer::link(
        new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"),
        fullname($session));
    $toform['lessontypename']   = $session->lessontypename == '' ?
        get_string('notspecified', 'block_supervised') :
        $session->lessontypename;
    $toform['timestart']        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $toform['duration']         = $session->duration;
    $toform['timeend']          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    $toform['sessioncomment']   = $session->sessioncomment;
    $toform['notifyteacher']    = ($session->state == StateSession::FINISHED) ? 0 : 1;

    $mform->set_data($toform);
    $mform->display();


    echo $OUTPUT->footer();
}