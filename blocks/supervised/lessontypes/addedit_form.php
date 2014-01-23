<?php

require_once("{$CFG->libdir}/formslib.php");

/**
 * Class addedit_lessontype_form
 *
 * The form for adding of editing lesson types
 */
class addedit_lessontype_form extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;
        
        // add group
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // add name element
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', null, 'required', null, 'client');

        // hidden elements
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        $this->add_action_buttons();
    }
}