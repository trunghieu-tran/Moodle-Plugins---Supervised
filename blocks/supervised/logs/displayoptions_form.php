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
 * Class displayoptions_logs_form
 *
 * Logs display options form (logs number per page)
 */
class displayoptions_logs_form extends moodleform {

    protected function definition() {
        global $DB;
        $mform =& $this->_form;

        // Gets array of all groups in current course.
        $teacher = $DB->get_record('user', array('id' => $this->_customdata['teacherid']));
        $users[0] = get_string('allusers', 'block_supervised');
        $users[$teacher->id] = fullname($teacher);

        $groupid = $this->_customdata['groupid'];
        $courseid = $this->_customdata['courseid'];
        if ($groupid == 0) {
            // All groups in course.
            $groups = groups_get_all_groups($courseid);
            foreach ($groups as $group) {
                $cusers = groups_get_members($group->id);
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = "[" . $group->name . "]" . " " . fullname($cuser);
                }
            }
        } else {
            // One group in course.
            if ( $cusers = groups_get_members($groupid) ) {
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = fullname($cuser);
                }
            }
        }

        $mform->addElement('header', 'sessionsoptionsview', get_string('reportdisplayoptions', 'quiz'));
        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);
        $mform->addElement('select', 'userid', get_string('filterlogsbyuser', 'block_supervised'), $users);

        // hidden elements
        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('submit', 'submitbutton', get_string('showlogsbutton', "block_supervised"));
    }

    // Form validation
    public function validation($data, $files) {
        $errors = array();

        // Page size must be greater than zero.
        if ($data["pagesize"] <= 0) {
            $errors["pagesize"] = get_string("pagesizevalidationerror", "block_supervised");
        }

        return $errors;
    }
}