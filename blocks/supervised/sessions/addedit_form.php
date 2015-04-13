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
 * Class addedit_session_form
 *
 * The form for adding of editing sessions
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addedit_session_form extends moodleform {

    protected function definition() {
        global $DB, $PAGE, $USER;

        $mform =& $this->_form;

        $teachers = array();
        if ($cteachers = get_users_by_capability($PAGE->context, array('block/supervised:supervise'))) {
            if ( has_capability('block/supervised:manageownsessions', $PAGE->context) AND
                !has_capability('block/supervised:manageallsessions', $PAGE->context) ) {
                // If current user has only manageownsessions capability he can plane session only for himself.
                $teachers[$USER->id] = fullname($cteachers[$USER->id]);
            } else {
                // User can add/edit session for other users. So ...all teachers.
                foreach ($cteachers as $cteacher) {
                    $teachers[$cteacher->id] = fullname($cteacher);
                }
            }
        }

        // Find all classrooms.
        $classrooms = array();
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active' => true))) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }

        // Gets array of all groups in current course.
        $groups[0] = get_string('allgroups', 'block_supervised');
        if ($cgroups = groups_get_all_groups($this->_customdata['courseid'])) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }

        // Find lessontypes in current course.
        if ($this->_customdata['addnotspecified']) {
            // For editing mode: if user created session for 'Not specified' lesson type,
            // then added some lesson types and now wants edit session - we should show 'Not specified' in select.
            $lessontypes[0] = get_string('notspecified', 'block_supervised');
        }
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid' => $this->_customdata['courseid']))) {
            foreach ($clessontypes as $clessontype) {
                $lessontypes[$clessontype->id] = $clessontype->name;
            }
        }

        // ...group.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // ...teacher combobox.
        $mform->addElement('select', 'teacherid', get_string('superviser', 'block_supervised'), $teachers);
        $mform->addRule('teacherid', null, 'required', null, 'client');
        // ...send e-mail checkbox.
        $mform->addElement('advcheckbox', 'sendemail', get_string('notifyteacher', 'block_supervised'));
        $mform->addHelpButton('sendemail', 'notifyteacher', 'block_supervised');
        // ...course label.
        $mform->addElement('static', 'coursename', get_string('course'));
        // ...classroom combobox.
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        $mform->addRule('classroomid', null, 'required', null, 'client');
        // ...group combobox.
        $mform->addElement('select', 'groupid', get_string('group'), $groups);
        $mform->addRule('groupid', null, 'required', null, 'client');
        // ...lessontype combobox.
        if ($clessontypes) {
            $mform->addElement('select', 'lessontypeid', get_string('lessontype', 'block_supervised'), $lessontypes);
            $mform->addRule('lessontypeid', null, 'required', null, 'client');
        } else {
            $mform->addElement('hidden', 'lessontypeid');
            $mform->setType('lessontypeid', PARAM_INT);
        }
        // ...time start.
        $mform->addElement('date_time_selector', 'timestart', get_string('timestart', 'block_supervised'));
        $mform->addRule('timestart', null, 'required', null, 'client');
        // ...duration.
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // ...comment.
        $mform->addElement('textarea', 'sessioncomment', get_string('comment', 'question'), 'rows="4" cols="30"');

        // ...hidden elements.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }

    // Form validation.
    public function validation($data, $files) {
        global $PAGE, $USER, $CFG;
        require_once("{$CFG->dirroot}/blocks/supervised/lib.php");
        $errors = array();

        // Session must be active at least after 10 minutes from current time.
        $sessiontimeend = $data['timestart'] + $data['duration'] * 60;
        $minimumtimeend = time() + 10 * 60;
        if ($sessiontimeend <= $minimumtimeend) {
            $strftimedatetime = get_string('strftimerecent');
            $timeformatted = userdate($minimumtimeend, '%a').' '.userdate($minimumtimeend, $strftimedatetime);
            $errors['duration'] = get_string('timeendvalidationerror', 'block_supervised', $timeformatted);
        }
        // Duration must be greater than zero.
        if ($data['duration'] <= 0) {
            $errors['duration'] = get_string('durationvalidationerror', 'block_supervised');
        }

        // Session can not intersect with sessions of this teacher.
        if (session_exists($data['teacherid'], $data['timestart'], $sessiontimeend, $data['id'])) {
            $errors['timestart'] = get_string('teacherhassession', 'block_supervised');
        }

        // If current user has only manageownsessions capability he can add/edit session only for himself.
        if ( has_capability('block/supervised:manageownsessions', $PAGE->context) AND
            !has_capability('block/supervised:manageallsessions', $PAGE->context) ) {
            if ($data['teacherid'] != $USER->id) {
                $errors['teacherid'] = get_string('teachervalidationerror', 'block_supervised');
            }
        }

        return $errors;
    }
}