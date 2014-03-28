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

class block_supervised_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG;
        // Section header title.
        $mform->addElement('header', 'configheader', get_string('coursesettings', 'block_supervised'));
        // Course specific session duration.
        $mform->addElement('text', 'config_duration', get_string('sessiondurationcourse', 'block_supervised'));
        $mform->setDefault('config_duration', $CFG->block_supervised_session_duration);
        $mform->setType('config_duration', PARAM_INT);
        $mform->addRule('config_duration', null, 'required', null, 'client');
        $mform->addRule('config_duration', null, 'numeric', null, 'client');
    }

    public function validation($data, $files) {
        $errors = array();
        // Duration must be greater than zero.
        if ($data['config_duration'] <= 0) {
            $errors['config_duration'] = get_string('durationvalidationerror', 'block_supervised');
        }
        return $errors;
    }
}