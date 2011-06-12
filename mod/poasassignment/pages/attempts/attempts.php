<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class attempts_page extends abstract_page {
    var $poasassignment;
    var $assignee;
    var $context;
    function attempts_page($cm, $poasassignment) {
        global $DB, $USER;
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        
        $assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        if($assigneeid > 0) {
            $this->assignee = $DB->get_record('poasassignment_assignee', array('id' => $assigneeid,));
        }
        else {
            $this->assignee = $DB->get_record('poasassignment_assignee', 
                                              array('userid' => $USER->id, 'poasassignmentid' => $poasassignment->id));
        }
    }
    
    function has_satisfying_parameters() {
        global $DB,$USER;
        // TODO
        if ($this->assignee && $this->assignee->lastattemptid > 0) {
            if($this->assignee->userid == $USER->id || 
                    has_capability('mod/poasassignment:grade', $this->context)) {
                    
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
        
        /* if ($assignee = $DB->get_record('poasassignment_assignee',
                                        array('userid' => $USER->id, 
                                              'poasassignmentid' => $this->poasassignment->id))) {
            if(isset($assignee->lastattempt)) */
        return true;
    }
    
    function view() {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $attempts=array_reverse($DB->get_records('poasassignment_attempts',array('assigneeid'=>$this->assignee->id),'attemptnumber'));
        $plugins=$poasmodel->get_plugins();
        $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        $latestattempt=$DB->get_record('poasassignment_attempts',array('id'=>$this->assignee->lastattemptid));
        $attemptscount=count($attempts);  
        foreach($attempts as $attempt) {    
            echo $OUTPUT->box_start();
            echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
            
            // show attempt's submission
            foreach($plugins as $plugin) {
                require_once($plugin->path);
                $poasassignmentplugin = new $plugin->name();
                echo $poasassignmentplugin->show_assignee_answer($this->assignee->id,$this->poasassignment->id,1,$attempt->id);
            }
            // show disablepenalty/enablepenalty button
            if(has_capability('mod/poasassignment:grade',$this->context)) {
                if(isset($attempt->disablepenalty) && $attempt->disablepenalty==1) {
                    echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$this->cm->id.'&action=enablepenalty&attemptid='.$attempt->id), 
                                                            get_string('enablepenalty','poasassignment'));
                }
                else {
                    echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$this->cm->id.'&action=disablepenalty&attemptid='.$attempt->id), 
                                                            get_string('disablepenalty','poasassignment'));
                }
            }
            $poasmodel->show_feedback($attempt,$latestattempt,$criterions,$this->context);
            echo $OUTPUT->box_end();
        }
        }
}