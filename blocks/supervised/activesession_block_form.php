<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
 
class activesession_block_form extends moodleform {
 
    function definition() {
        global $DB, $COURSE;

        $mform =& $this->_form;

        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active'=>true))) {
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

        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // add classroom
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        $mform->addRule('classroomid', null, 'required', null, 'client');
        // add group combobox
        $mform->addElement('select', 'groupid', get_string('group', 'block_supervised'), $groups);
        $mform->addRule('groupid', null, 'required', null, 'client');
        // add lessontype
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        // add time start
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // add comment
        $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));

        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id

        $this->add_action_buttons(false, get_string('finishsession', "block_supervised"));
    }
}