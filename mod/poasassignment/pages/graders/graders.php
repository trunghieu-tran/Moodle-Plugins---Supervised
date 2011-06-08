<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once('graderssettings_form.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class graders_page extends abstract_page {
    var $poasassignment;
    
    function __construct($cm, $poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
    
    function has_satisfying_parameters() {
        global $DB;
        if(!$DB->record_exists('poasassignment_used_graders', 
                               array('poasassignmentid' => $this->poasassignment->id))) {
            $this->lasterror = 'errornograderused';
            return false;
        }
        return true;
    }
    function view() {
        global $DB,$OUTPUT;
        $id = $this->cm->id;
        $poasassignmentid = $this->poasassignment->id;
        $mform = new graderssettings_form(null, array('id' => $id, 'poasassignmentid' => $poasassignmentid));
        if($mform->get_data()) {
            grader::save_settings($mform->get_data(), $poasassignmentid);            
        }
        $mform->display();
    }
    
}