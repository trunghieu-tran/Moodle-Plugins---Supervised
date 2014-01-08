<?php

require_once("{$CFG->libdir}/formslib.php");

/**
 * Class addedit_classroom_form
 *
 * The form for adding of editing classrooms
 */
class addedit_classroom_form extends moodleform {
 
    function definition() {
 
        $mform =& $this->_form;
        
        // add group
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // add name element
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        $mform->setType('name', PARAM_RAW);
        $mform->addRule('name', null, 'required', null, 'client');
        // add iplist element
        $mform->addElement('text', 'iplist', get_string("iplist", 'block_supervised'), array('size'=>'48'));
        $mform->setType('iplist', PARAM_RAW);
        $mform->addRule('iplist', null, 'required', null, 'client');
        $mform->addHelpButton('iplist', 'iplist', 'block_supervised');
        // add active checkbox
        $mform->addElement('advcheckbox', 'active', get_string("active", 'block_supervised'));
        $mform->addHelpButton('active', 'active', 'block_supervised');

        // hidden elements
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        $this->add_action_buttons();
    }
}