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
 * Class activesessionstudent_block_form
 *
 * The form for active session (for besupervised capability)
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activesessionstudent_block_form extends moodleform {

    protected function definition() {
        $mform =& $this->_form;

        // Add group.
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // ...superviser.
        $mform->addElement('static', 'lessontypelabel', '', '<b>'.get_string('superviser', 'block_supervised').'</b>');
        $mform->addElement('static', 'teacher', '');
        // ...lessontype.
        $mform->addElement('static', 'lessontypelabel', '', '<b>'.get_string('lessontype', 'block_supervised').'</b>');
        $mform->addElement('static', 'lessontypename', '');
        // ...classroom.
        $mform->addElement('static', 'classroomlabel', '<b>'.get_string('classroom', 'block_supervised').'</b>');
        $mform->addElement('static', 'classroomname', '');
        // ...group.
        $mform->addElement('static', 'grouplabel', '<b>'.get_string('group').'</b>');
        $mform->addElement('static', 'groupname', '');
        // ...timestart.
        $mform->addElement('static', 'timestartlabel', '<b>'.get_string('timestart', 'block_supervised').'</b>');
        $mform->addElement('static', 'timestart', '');
        // ...duration.
        $mform->addElement('static', 'durationlabel', '<b>'.get_string('duration', 'block_supervised').'</b>');
        $mform->addElement('static', 'duration', '');
        // ...timeend.
        $mform->addElement('static', 'timeendlabel', '<b>'.get_string('timeend', 'block_supervised').'</b>');
        $mform->addElement('static', 'timeend', '');

        // ...hidden elements.
        $mform->addElement('hidden', 'id');     // course id.
        $mform->setType('id', PARAM_INT);
    }
}