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
require_once('../sessions/sessionstate.php');

$courseid   = required_param('courseid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // Classroom id (only for edit mode).
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage)..
    print_error('invalidcourseid');
}

require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);
$PAGE->set_url('/blocks/supervised/classrooms/addedit.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
require('breadcrumbs.php');


// Initializing variables depending of mode.
if (!$id) {   // Add mode.
    $PAGE->navbar->add(get_string('addclassroomnavbar', 'block_supervised'));
    $title = get_string('addclassroompagetitle', 'block_supervised');
    $heading = get_string('addingnewclassroom', 'block_supervised');

    $toform['active']   = 1;    // Default value.
} else {     // Edit mode.
    if (! $classroom = $DB->get_record('block_supervised_classroom', array('id' => $id))) {
        print_error(get_string('invalidclassroomid', 'block_supervised'));
    }
    $PAGE->navbar->add(get_string('editclassroomnavbar', 'block_supervised'));
    $title = get_string('editclassroompagetitle', 'block_supervised');
    $heading = get_string('editingclassroom', 'block_supervised');

    $toform['id']       = $classroom->id;
    $toform['name']     = $classroom->name;
    $toform['iplist']   = $classroom->iplist;
    $toform['active']   = $classroom->active;
}

$PAGE->set_title($title);

// Prepare form.
$mform = 'addedit_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new addedit_classroom_form();
$toform['courseid'] = $courseid;
$mform->set_data($toform);

if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if (!$id) {   // Add mode.
        if (!$newid = $DB->insert_record('block_supervised_classroom', $fromform)) {
            print_error('insertclassroomerror', 'block_supervised');
        }
        // TODO Logging.
        add_to_log($COURSE->id, 'role', 'add classroom',
            "blocks/supervised/classrooms/addedit.php?id={$newid}&courseid={$COURSE->id}", $fromform->name);
    } else {     // Edit mode.

        if (!$DB->update_record('block_supervised_classroom', $fromform)) {
            print_error('insertclassroomerror', 'block_supervised');
        }
        // TODO Logging.
        add_to_log($COURSE->id, 'role', 'edit classroom',
            "blocks/supervised/classrooms/addedit.php?id={$fromform->id}&courseid={$COURSE->id}", $fromform->name);

        // Find all Active and Planned sessions and update their ip lists.
        $select = "SELECT * FROM {block_supervised_session}
                    WHERE classroomid = :classroomid
                        AND (state = :plannedstate OR state = :activestate)";
        $params['activestate']  = StateSession::ACTIVE;
        $params['plannedstate'] = StateSession::PLANNED;
        $params['classroomid']  = $fromform->id;
        $sessions = $DB->get_records_sql($select, $params);
        foreach ($sessions as $session) {
            $session->iplist = $fromform->iplist;
            if (!$DB->update_record('block_supervised_session', $session)) {
                print_error('insertsessionerror', 'block_supervised');
            }
        }
    }
    $url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // Form didn't validate or this is the first display.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 2);
    $mform->display();
    echo $OUTPUT->footer();
}
