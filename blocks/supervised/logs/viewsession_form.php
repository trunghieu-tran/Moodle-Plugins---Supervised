<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class viewsession_form
 *
 * Information about session
 */
class viewsession_form extends moodleform {
 
    function definition() {

        $mform =& $this->_form;

        $mform->addElement('static', 'coursename', get_string('course', 'block_supervised'));
        $mform->addElement('static', 'classroomname', get_string('classroom', 'block_supervised'));
        $mform->addElement('static', 'groupname', get_string('group', 'block_supervised'));
        $mform->addElement('static', 'teachername', get_string('teacher', 'block_supervised'));
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        $mform->addElement('static', 'duration', get_string('duration', 'block_supervised'));
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));
        $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));
    }
}