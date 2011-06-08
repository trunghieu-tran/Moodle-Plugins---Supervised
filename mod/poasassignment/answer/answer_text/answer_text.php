<?php
require_once(dirname(dirname(__FILE__)).'\answer.php');

// require_once($CFG->dirroot.'/course/moodleform_mod.php');
class answer_text extends poasassignment_answer {
    var $checked;
    function answer_text() {
        global $DB;
        $plugin = $DB->get_record('poasassignment_answers', 
                                  array('name' => 'answer_text'));
        if ($plugin) {
            $this->answerid = $plugin->id;  
        }        
    }
    
    /** Display plugin settings 
     *
     *  Display separate fieldset with plugin settings
     */
    function show_settings($mform,$poasassignmentid) {
        global $DB;
        $mform->addElement('header', 
                           'answertextheader', 
                           get_string('pluginname','poasassignmentanswertypes_answer_text'));
        $mform->addElement('checkbox', 
                           'answertext', 
                           get_string('answertext','poasassignmentanswertypes_answer_text'));
        $conditions = array('poasassignmentid' => $poasassignmentid, 
                            'answerid' => $this->answerid);
        if ($DB->record_exists('poasassignment_ans_stngs',$conditions))
            $mform->setDefault('answertext','true');
        $mform->addHelpButton('answertext', 
                              'answertext', 
                              'poasassignmentanswertypes_answer_text');
    }
    function show_answer_form($mform) {
        $mform->addElement('header',
                           'answertextheader',
                           get_string('answertext','poasassignmentanswertypes_answer_text'));
        //$mform->addElement('htmleditor', 
        $mform->addElement('textarea', 
                           'text_editor', 
                           get_string('answertexteditor','poasassignmentanswertypes_answer_text'));
        $mform->closeHeaderBefore('answertextheader');
    }
    function show_assignee_answer($assigneeid,$poasassignmentid,$needbox=1,$attemptid=null) {
        global $DB,$OUTPUT;
        $html='';
        if(!$assigneeid)
            return $html;
        if(!isset($attemptid)) {
            $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        }
        else
            $attempt=$DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
        
        if($attempt) {
            $submission=$DB->get_record('poasassignment_submissions',array('answerid'=>$this->answerid,'attemptid'=>$attempt->id));
            if($submission) {
                if($needbox)
                    $html.= $OUTPUT->box_start();
                
                $html.= $submission->value;
                
                
                if($needbox)
                    $html.= $OUTPUT->box_end();
            }
            return $html;
        }            
    }
    function save_settings($poasassignment,$id) {
        global $DB;
        if ($this->checked) {
            $settingsrecord->poasassignmentid=$id;
            $settingsrecord->answerid=$this->answerid;
            $DB->insert_record('poasassignment_ans_stngs',$settingsrecord);
        }
    }
    function update_settings($poasassignment) {
        global $DB;
        $conditions = array('poasassignmentid'=>$poasassignment->id,
                'answerid'=>$this->answerid);
        $recordexists = $DB->record_exists('poasassignment_ans_stngs',$conditions);
        if (!$recordexists)
            $this->save_settings($poasassignment,$poasassignment->id);
        if ($recordexists && !$this->checked)
            $this->delete_settings($poasassignment->id);
    }
    function delete_settings($poasassignmentid) {
        global $DB;
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                'answerid'=>$this->answerid);
        return $DB->delete_records('poasassignment_ans_stngs',$conditions);
    }
    function configure_flag($poasassignment) {
        if (isset($poasassignment->answertext)) {
            $this->checked=true;
            unset($poasassignment->answertext);
        }
        else
            $this->checked=false;
    }
    function save_answer($assigneeid,$data) {
        global $DB,$USER;

        $rec->attemptid=$this->bind_submission_to_attempt($assigneeid,isset($data->draft),isset($data->final));
        $rec->assigneeid=$assigneeid;
        $rec->answerid=$this->answerid;
        $rec->value=$data->text_editor;
        $DB->insert_record('poasassignment_submissions',$rec);
        return $rec->attemptid;
    }
    
    function get_answer_values() {
        global $DB;
        $poasmodel=poasassignment_model::get_instance();
        $data->text_editor='';
        //$poasmodel->assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$poasmodel->assignee->id,'poasassignmentid'=>$poasmodel->poasassignment->id));
        if($poasmodel->assignee) {
            $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id));
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount));
            if($attempt) {
                $submission=$DB->get_record('poasassignment_submissions',array('answerid'=>$this->answerid,'attemptid'=>$attempt->id));
                if($submission) 
                    $data->text_editor=$submission->value;
            }
        }
        return $data;
    }
}
