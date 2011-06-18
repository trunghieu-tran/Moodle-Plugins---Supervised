<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class graderresults_page extends abstract_page {
    private $attemptid;
    function graderresults_page() {
        global $USER, $DB;
        $this->attemptid = optional_param('attemptid', 0, PARAM_INT);
        if ($this->attemptid == 0) {
            $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
            if($rec = $DB->get_record('poasassignment_assignee', array('userid' => $USER->id, 'poasassignmentid' => $poasassignmentid), 'lastattemptid')) {
                $this->attemptid = $rec->lastattemptid;
            }
        }
    }
    
    function has_satisfying_parameters() {
        global $DB;
        $poasmodel = poasassignment_model::get_instance();
        $attempt = $DB->get_record('poasassignment_attempts',array('id'=>$this->attemptid));
        return $poasmodel->have_test_results($attempt);
    }
    
    function view() {
        $this->view_testresult();
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
        echo $OUTPUT->heading(get_string('lasttestresults','poasassignment'));
        echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
        echo $poasmodel->show_test_results($attempt);
        echo $OUTPUT->box_end();
    }
}