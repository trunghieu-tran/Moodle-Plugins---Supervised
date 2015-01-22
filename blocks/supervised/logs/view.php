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
require_once('../lib.php');
require_once('logslib.php');
require_once('../sessions/sessionstate.php');

global $DB, $OUTPUT, $PAGE;

$courseid    = required_param('courseid',  PARAM_INT);
$sessionid   = required_param('sessionid', PARAM_INT);
$page        = optional_param('page', '0', PARAM_INT);      // Which page to show.
$perpage     = optional_param('perpage', '50', PARAM_INT);  // How many per page.
$userid      = optional_param('userid', '0', PARAM_INT);    // Current user id.

$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error('invalidcourseid');
}
if (! $session = $DB->get_record('block_supervised_session', array('id' => $sessionid))) {
    print_error(get_string('invalidsessionid', 'block_supervised'));
}

require_login($course);

$PAGE->set_url('/blocks/supervised/logs/view.php', array('courseid' => $courseid, 'sessionid' => $sessionid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('logspagetitle', 'block_supervised'));
require('breadcrumbs.php');

// Check capabilities.
if ($session->teacherid != $USER->id) {
    // User wants view logs of other user's session.
    require_capability('block/supervised:viewallsessions', $PAGE->context);
} else {
    // User wants view logs of own session.
    if ($session->state != StateSession::ACTIVE) {
        // Check capabilities fow own active session.
        if (!  (has_capability('block/supervised:supervise', $PAGE->context)
            || has_capability('block/supervised:viewownsessions', $PAGE->context)
            || has_capability('block/supervised:viewallsessions', $PAGE->context))  ) {
            require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
        }
    } else {
        // Check capabilities fow own not active session.
        if (!  (has_capability('block/supervised:viewownsessions', $PAGE->context)
            || has_capability('block/supervised:viewallsessions', $PAGE->context))  ) {
            require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
        }
    }
}
if ($session->state == StateSession::PLANNED) {
    print_error(get_string('sessionlogserror', 'block_supervised'));
}


// Prepare filter logs form.
$mform = 'displayoptions_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new displayoptions_logs_form(null, array('sessionid' => $session->id,
    'courseid' => $courseid, 'teacherid' => $session->teacherid));
$toform['sessionid']    = $sessionid;
$toform['courseid']     = $courseid;
$toform['userid']       = $userid;
$toform['pagesize']     = $perpage;


if ($fromform = $mform->get_data()) {
    $url = new moodle_url('/blocks/supervised/logs/view.php', array('courseid' => $courseid,
        'sessionid' => $sessionid,
        'userid' => $fromform->userid,
        'perpage' => $fromform->pagesize));
    redirect($url); // Redirect must be done before $OUTPUT->header().
} else {
    // Form didn't validate or this is the first display.
    // Display header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('logsview', 'block_supervised'), 2);

    print_session_info_form($sessionid);

    // Print display options form.
    $mform->set_data($toform);
    $mform->display();
}

// Print logs.
supervisedblock_print_logs($sessionid, $session->timestart, $session->timeend, $userid, $page, $perpage,
    "view.php?courseid=$courseid&amp;sessionid=$sessionid&amp;userid=$userid");

// Display footer.
echo $OUTPUT->footer();