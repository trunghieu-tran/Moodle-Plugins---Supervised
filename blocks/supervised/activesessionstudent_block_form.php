<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class activesessionstudent_block_form
 *
 * The form for active session (for besupervised capability)
 *
 * @package block_supervised
 * @copyright
 * @licence
 */
class activesessionstudent_block_form extends moodleform {

    protected function definition() {
        $mform =& $this->_form;

        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // add teacher
        $mform->addElement('static', 'teacher', get_string('teacher', 'block_supervised'));
        // add lessontype
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        // add classroom
        $mform->addElement('static', 'classroomname', get_string('classroom', 'block_supervised'));
        // add group
        $mform->addElement('static', 'groupname', get_string('group', 'block_supervised'));
        // add timestart
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('static', 'duration', get_string('duration', 'block_supervised'));
        // add timeend
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));

        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id
        $mform->setType('id', PARAM_INT);
    }
}