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


global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class plannedsession_block_form
 *
 * The form for planned session (for supervise capability)
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plannedsession_block_form extends moodleform {

    protected function definition() {
        global $DB, $COURSE;

        $mform =& $this->_form;

        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active' => true))) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }

        // Gets array of all groups in current course.
        $groups[0] = get_string('allgroups', 'block_supervised');
        if ($cgroups = groups_get_all_groups($COURSE->id)) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }

        // Find lessontypes in current course.
        if ($this->_customdata['lessontype'] == 0) {
            // If user planned session for 'Not specified' lesson type,
            // then added some lesson types - we should show 'Not specified' in select.
            $lessontypes[0] = get_string('notspecified', 'block_supervised');
        }
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id))) {
            foreach ($clessontypes as $clessontype) {
                $lessontypes[$clessontype->id] = $clessontype->name;
            }
        }

        // Add group.
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // ...classroom combobox.
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        // ...group combobox.
        $mform->addElement('select', 'groupid', get_string('group'), $groups);
        // ...lessontype combobox.
        if ($clessontypes) {
            $mform->addElement('select', 'lessontypeid', get_string('lessontype', 'block_supervised'), $lessontypes);
        } else {
            $mform->addElement('hidden', 'lessontypeid');
            $mform->setType('lessontypeid', PARAM_INT);
        }
        // ...time start.
        $mform->addElement('static', 'timestartlabel', '', '<b>'.get_string('timestart', 'block_supervised').'</b>');
        $mform->addElement('static', 'timestart', '');
        // ...duration.
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // ...timeend.
        $mform->addElement('static', 'timeendlabel', '', '<b>'.get_string('timeend', 'block_supervised').'</b>');
        $mform->addElement('static', 'timeend', '');
        // ...comment.
        if ($this->_customdata['needcomment']) {
            $mform->addElement('static', 'sessioncommentlabel', '', '<b>'.get_string('comment', 'question').'</b>');
            $mform->addElement('static', 'sessioncomment', '');
        }
        // ...hidden elements.
        $mform->addElement('hidden', 'id');     // Course id.
        $mform->setType('id', PARAM_INT);

        // ...submit button.
        $mform->addElement('submit', 'submitbutton', get_string('startsession', 'block_supervised'));
    }

    // Form validation.
    public function validation($data, $files) {
        $errors = array();

        // Duration must be greater than zero.
        if ($data['duration'] <= 0) {
            $errors['duration'] = get_string('durationvalidationerror', 'block_supervised');
        }

        return $errors;
    }
}