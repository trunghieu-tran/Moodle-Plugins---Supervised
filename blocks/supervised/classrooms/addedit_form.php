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


require_once("{$CFG->libdir}/formslib.php");

/**
 * Class addedit_classroom_form
 *
 * The form for adding of editing classrooms
 */
class addedit_classroom_form extends moodleform {

    protected function definition() {

        $mform =& $this->_form;

        // ...add group.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // ...name element.
        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', null, 'required', null, 'client');
        // ...iplist element.
        $mform->addElement('text', 'iplist', get_string('iplist', 'block_supervised'), array('size' => '48'));
        $mform->setType('iplist', PARAM_RAW);
        $mform->addRule('iplist', null, 'required', null, 'client');
        $mform->addHelpButton('iplist', 'iplist', 'block_supervised');
        // ...active checkbox.
        $mform->addElement('advcheckbox', 'active', get_string('active', 'block_supervised'));
        $mform->addHelpButton('active', 'active', 'block_supervised');

        // ...hidden elements.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }
}