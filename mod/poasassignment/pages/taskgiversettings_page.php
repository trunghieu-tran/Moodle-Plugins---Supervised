<?php
global $CFG;
require_once('abstract_page.php');
//require_once('graderssettings_form.php');
require_once('model.php');
class taskgiversettings_page extends abstract_page {
    var $poasassignment;
    
    function __construct($cm, $poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
     function has_satisfying_parameters() {
        $flag = $this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS;
        if(!$flag)
            return false;
        return true;
    }
    
    function get_error_satisfying_parameters() {
        $flag=$this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        if(!$flag)
            return 'errorindtaskmodeisdisabled';
    } 
    
    function view() {
        global $DB;
        
        $id = $this->cm->id;
        $poasassignmentid = $this->poasassignment->id;
        $taskgiverrec = $DB->get_record('poasassignment_taskgivers', array('id' => $this->poasassignment->howtochoosetask));
        require_once($taskgiverrec->path);
        $taskgivername = $taskgiverrec->name;
        $taskgiver = new $taskgivername();
        if(!$taskgiver->hassettings) {
            print_string('taskgiverhasnosettings','poasassignment');
        }
        else {
            $mform = $taskgiver->get_settings_form($id, $poasassignmentid);
            $data = new stdClass();
            $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $poasassignmentid));
            foreach ($fields as $field) {
                $fieldname = 'field' . $field->id;
                if ($DB->record_exists('poasassignment_paramch', array('fieldid' => $field->id))) {
                    $data->$fieldname = true;
                }
            }
            $mform->set_data($data);
            if($mform->get_data()) {
                echo '11111';
                $taskgiver->save_settings($mform->get_data());
            }
            $mform->display(); 
        }
    }
    
}