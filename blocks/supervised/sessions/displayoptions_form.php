<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

class displayoptions_sessions_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'sessionsoptionsview', get_string('reportdisplayoptions', 'quiz'));
        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);

        $mform->addElement('submit', 'submitbutton', get_string('showreport', 'quiz'));

        // hidden elements
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
    }

    // Form validation
    function validation($data, $files) {
        $errors = array();

        // Page size must be greater than zero.
        if($data["pagesize"] <= 0){
            $errors["pagesize"] = get_string("pagesizevalidationerror", "block_supervised");
        }

        return $errors;
    }
}
