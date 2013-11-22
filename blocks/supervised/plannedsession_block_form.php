<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
 
class plannedsession_block_form extends moodleform {
 
    function definition() {
        global $DB;

        $mform =& $this->_form;


        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active'=>true))) {
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
        $lessontypes[0] = get_string('notspecified', 'block_supervised');
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$this->_customdata['courseid']))) {
            foreach ($clessontypes as $clessontype) {
                $lessontypes[$clessontype->id] = $clessontype->name;
            }
        }


        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // add classroom combobox
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        $mform->addRule('classroomid', null, 'required', null, 'client');
        // add group combobox
        $mform->addElement('select', 'groupid', get_string('group', 'block_supervised'), $groups);
        $mform->addRule('groupid', null, 'required', null, 'client');
        // add lessontype combobox
        $mform->addElement('select', 'lessontypeid', get_string('lessontype', 'block_supervised'), $lessontypes);
        $mform->addRule('lessontypeid', null, 'required', null, 'client');
        // add time start
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // add comment
        $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));

        
        $this->add_action_buttons(false, get_string('startsession', "block_supervised"));
    }
}