<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
class graderresults_page extends abstract_page {
    private $attemptid;
    private $assigneeid;
    function graderresults_page() {
        global $USER, $DB;
        $model = poasassignment_model::get_instance();
        $this->attemptid = optional_param('attemptid', 0, PARAM_INT);
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        if ($this->attemptid == 0) {
            if ($assignee = $model->get_assignee($USER->id)) {
                $this->attemptid = $model->get_last_attempt_id($assignee->id);
            }
        }
        if ($this->assigneeid == 0) {
            $this->assigneeid = $model->get_assigneeid();
        }
    }
    
    function has_satisfying_parameters() {
        global $DB;
        $poasmodel = poasassignment_model::get_instance();
        if(!$DB->record_exists('poasassignment_used_graders', array('poasassignmentid' =>$poasmodel->get_poasassignment()->id))) {
            return false;
        }
        if($attempt = $DB->get_record('poasassignment_attempts',array('id'=>$this->attemptid))) {
            return $poasmodel->have_test_results($attempt);
        }
        else {
            return false;
        }
    }
    
    function view() {
        global $DB, $USER;
        $model = poasassignment_model::get_instance();
        $context = $model->get_context();
        $attempt = $DB->get_record('poasassignment_attempts',array('id'=>$this->attemptid));
        if(has_capability('mod/poasassignment:grade',$context)
            || $DB->record_exists('poasassignment_assignee', array('id' => $attempt->assigneeid, 'userid' => $USER->id))) {
            $this->view_testresult();
        }
        else {
            print_error('nopermission');
        }
    }
    function view_testresult() {
        global $DB, $OUTPUT, $USER;
        $poasmodel = poasassignment_model::get_instance();
        $attempt = $DB->get_record('poasassignment_attempts',array('id'=>$this->attemptid));
        
        if(!$poasmodel->have_test_results($attempt)) {
            echo $OUTPUT->heading(get_string('nograderresults','poasassignment'));
            return;
        }
        echo $OUTPUT->box_start();
        if (has_capability('mod/poasassignment:grade', $poasmodel->get_context())) {
            $mform = new assignee_choose_form(null, array('id' => $poasmodel->get_cm()->id, 'page' => 'graderresults'));
            $mform->display();
            $mform2 = new attempt_choose_form(null, array('id' => $poasmodel->get_cm()->id, 'assigneeid' => $this->assigneeid));
            $mform2->display();
        }
        
        echo $OUTPUT->heading(get_string('lasttestresults','poasassignment'));
        echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
        echo $poasmodel->show_test_results($attempt);
        echo $OUTPUT->box_end();
    }
}