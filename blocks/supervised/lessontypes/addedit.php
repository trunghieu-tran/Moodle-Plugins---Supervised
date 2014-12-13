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

$courseid   = required_param('courseid', PARAM_INT);
$id         = optional_param('id', '', PARAM_INT);        // Lessontype id (only for edit mode).
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error('invalidcourseid');
}

require_login($course);
require_capability('block/supervised:editlessontypes', $PAGE->context);
$PAGE->set_url('/blocks/supervised/lessontypes/addedit.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
require('breadcrumbs.php');
$context = context_course::instance($courseid);

// Initializing variables depending of mode.
if (!$id) {   // Add mode.
    $PAGE->navbar->add(get_string('addlessontypenavbar', 'block_supervised'));
    $title = get_string('addlessontypepagetitle', 'block_supervised');
    $heading = get_string('addingnewlessontype', 'block_supervised');
} else {     // Edit mode.
    if (! $lessontype = $DB->get_record('block_supervised_lessontype', array('id' => $id, 'courseid' => $courseid))) {
        print_error(get_string('invalidlessontypeid', 'block_supervised'));
    }
    $PAGE->navbar->add(get_string('editlessontypenavbar', 'block_supervised'));
    $title = get_string('editlessontypepagetitle', 'block_supervised');
    $heading = get_string('editinglessontype', 'block_supervised');

    $toform['id']   = $lessontype->id;
    $toform['name'] = $lessontype->name;
}

$PAGE->set_title($title);

// Prepare form.
$mform = 'addedit_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new addedit_lessontype_form();
$toform['courseid'] = $courseid;
$mform->set_data($toform);

if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Store the submitted data.
    if (!$id) {   // Add mode.
        if (!$newid = $DB->insert_record('block_supervised_lessontype', $fromform)) {
            print_error('insertlessontypeerror', 'block_supervised');
        }
        $event = \block_supervised\event\add_lessontype::create(array('context' => $context,
            'userid' => $USER->id, 'other' => array('fromform_name' => ($fromform->name),
            'courseid' => $courseid, 'newlessontypeid' => $newid)));
        $event->trigger();
    } else {     // Edit mode.
        if (!$DB->update_record('block_supervised_lessontype', $fromform)) {
            print_error('insertlessontypeerror', 'block_supervised');
        }
        $event = \block_supervised\event\update_lessontype::create(array('context' => $context,
            'userid' => $USER->id, 'other' => array('fromform_name' => ($fromform->name),
            'courseid' => $courseid, 'fromform_id' => $fromform->id)));
        $event->trigger();
    }
    $url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // Form didn't validate or this is the first display.
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading, 2);
    $mform->display();
    echo $OUTPUT->footer();
}
