<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir.'/formslib.php');
class graders_page extends abstract_page {
    //var $poasassignment;
    
    function __construct(/* $cm, $poasassignment */) {
        //$this->poasassignment = $poasassignment;
        //$this->cm = $cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
    
    function has_satisfying_parameters() {
        global $DB;
        if(!$DB->record_exists('poasassignment_used_graders', 
                               array('poasassignmentid' => poasassignment_model::get_instance()->get_poasassignment()->id))) {
            $this->lasterror = 'errornograderused';
            return false;
        }
        return true;
    }
    function view() {
        global $DB,$OUTPUT;
        $model = poasassignment_model::get_instance();
        $id = $model->get_cm()->id;
        //$id = $this->cm->id;
        $poasassignmentid = $model->get_poasassignment()->id;
        //$poasassignmentid = $this->poasassignment->id;
        $mform = new graderssettings_form(null, array('id' => $id, 'poasassignmentid' => $poasassignmentid));
        $graders = $DB->get_records('poasassignment_used_graders', array('poasassignmentid' => $poasassignmentid));
            
        if($mform->get_data()) {
            foreach($graders as $graderrecord) {
                $usedgraderrecord = $DB->get_record('poasassignment_graders', array('id' => $graderrecord->graderid));
                require_once($usedgraderrecord->path);
                $gradername = $usedgraderrecord->name;
                $gradername::save_settings($mform->get_data(), $poasassignmentid);     
            }
        }
        foreach($graders as $graderrecord) {
            $usedgraderrecord = $DB->get_record('poasassignment_graders', array('id' => $graderrecord->graderid));
            require_once($usedgraderrecord->path);
            $gradername = $usedgraderrecord->name;
            $mform->set_data($gradername::get_settings($poasassignmentid));
        }
        $mform->display();
    }
}

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
            $grader = $gradername::show_settings($mform, $graderrecord->id, $instance['poasassignmentid']);
        }
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'graders');
        $mform->setType('page', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}