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
 * Class viewsession_form
 *
 * Information about session
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class viewsession_form extends moodleform {

    protected function definition() {

        $mform =& $this->_form;

        $mform->addElement('static', 'coursename', get_string('course'));
        $mform->addElement('static', 'classroomname', get_string('classroom', 'block_supervised'));
        $mform->addElement('static', 'groupname', get_string('group'));
        $mform->addElement('static', 'teachername', get_string('superviser', 'block_supervised'));
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        $mform->addElement('static', 'duration', get_string('duration', 'block_supervised'));
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));
        $mform->addElement('static', 'sessioncomment', get_string('comment', 'question'));
    }
}