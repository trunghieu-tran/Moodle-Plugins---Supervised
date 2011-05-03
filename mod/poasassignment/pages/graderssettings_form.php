<?php

require_once($CFG->libdir.'/formslib.php');

class graderssettings_form extends moodleform {
    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        
        // Show settings for all used graders
        $graders = $DB->get_records('poasassignment_used_graders', array('poasassignmentid' => $instance['poasassignmentid']));
        foreach($graders as $graderrecord) {
            $usedgraderrecord = $DB->get_record('poasassignment_graders', array('id' => $graderrecord->graderid));
            require_once($usedgraderrecord->path);
            $gradername = $usedgraderrecord->name;
            $grader = $gradername::show_settings($mform, $graderrecord->id);
            //$grader->show_settings($mform, $instance['poasassignmentid']);
        }
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'graders');
        $mform->setType('page', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}