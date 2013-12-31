<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
 
class startsession_block_form extends moodleform {
 
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

        // Find lessontypes in current course.
        $lessontypes[0] = get_string('notspecified', 'block_supervised');
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$COURSE->id))) {
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
        // add duration
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id
        $mform->setType('id', PARAM_INT);
        // add submit button
        $mform->addElement('submit', 'submitbutton', get_string('startsession', "block_supervised"));
    }


    // Form validation
    function validation($data, $files) {
        $errors = array();

        // Duration must be greater than zero.
        if($data["duration"] <= 0){
            $errors["duration"] = get_string("durationvalidationerror", "block_supervised");
        }

        return $errors;
    }
}