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
 * Class displayoptions_logs_form
 *
 * Logs display options form (logs number per page)
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class displayoptions_logs_form extends moodleform {

    protected function definition() {
        global $DB;
        $mform =& $this->_form;

        $sessionid  = $this->_customdata['sessionid'];

        // Gets array of all groups in current course.
        $users[0] = get_string('allusers', 'block_supervised');
        $teacher = $DB->get_record('user', array('id' => $this->_customdata['teacherid']));
        $users[$teacher->id] = fullname($teacher);

        $usersinsession = $DB->get_records('block_supervised_user', array('sessionid' => $sessionid));
        foreach ($usersinsession as $curuser) {
            $userobj = $DB->get_record('user', array('id' => $curuser->userid));
            // TODO Add user groups as string's preffix.
            $users[$userobj->id] = fullname($userobj);
        }

        $mform->addElement('header', 'sessionsoptionsview', get_string('reportdisplayoptions', 'quiz'));
        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);
        $mform->addElement('select', 'userid', get_string('filterlogsbyuser', 'block_supervised'), $users);
        // ...hidden elements.
        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('submit', 'submitbutton', get_string('showlogsbutton', 'block_supervised'));
    }

    // Form validation.
    public function validation($data, $files) {
        $errors = array();

        // Page size must be greater than zero.
        if ($data['pagesize'] <= 0) {
            $errors['pagesize'] = get_string('pagesizevalidationerror', 'block_supervised');
        }

        return $errors;
    }
}