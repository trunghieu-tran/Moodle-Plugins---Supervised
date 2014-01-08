<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class activesessionstudent_block_form
 *
 * The form for active session (for besupervised capability)
 *
 * @package block_supervised
 * @copyright
 * @licence
 */
class activesessionstudent_block_form extends moodleform {
 
    function definition() {
        $mform =& $this->_form;

        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // add teacher
        $mform->addElement('static', 'teacher', get_string('teacher', 'block_supervised'));
        // add lessontype
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        // add classroom
        $mform->addElement('static', 'classroomname', get_string('classroom', 'block_supervised'));
        // add group
        $mform->addElement('static', 'groupname', get_string('group', 'block_supervised'));
        // add timestart
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('static', 'duration', get_string('duration', 'block_supervised'));
        // add timeend
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));

        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id
        $mform->setType('id', PARAM_INT);
    }
}