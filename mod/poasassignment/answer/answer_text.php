<?php
require_once('answer.php');
// require_once($CFG->dirroot.'/course/moodleform_mod.php');
class poasassignment_answer_text extends poasassignment_answer {
    var $checked;
    function poasassignment_answer_text() {
        global $DB;
        //if ($DB->get_records('poasassignment_plugins',array('name'=>'poasassignment_answer_text'))>=1) {
        $plugin=$DB->get_record('poasassignment_plugins',array('name'=>'poasassignment_answer_text'));
        if ($plugin) {
            $this->pluginid=$plugin->id;  
        }        
    }
    function insert_plugin_in_db() {
        global $DB;
        $record->name='poasassignment_answer_text';
        $record->path='answer/answer_text.php';
        if (!$DB->record_exists('poasassignment_plugins',array('name'=>$record->name,'path'=>$record->path)))
            $DB->insert_record('poasassignment_plugins',$record);
    }
    function show_settings(&$mform,$poasassignmentid) {
        global $DB;
        $mform->addElement('header','answertextheader',get_string('answertext','poasassignment'));
        $mform->addElement('checkbox','answertext', get_string('answertext','poasassignment'));
        $conditions = array('poasassignmentid'=>$poasassignmentid,'pluginid'=>$this->pluginid);
        if ($DB->record_exists('poasassignment_type_settings',$conditions))
            $mform->setDefault('answertext','true');
        $mform->addHelpButton('answertext', 'answertext', 'poasassignment');
    }
    function show_answer_form($mform) {
        $mform->addElement('header','answertextheader',get_string('answertext','poasassignment'));
        $mform->addElement('htmleditor', 'text_editor', get_string('answertexteditor','poasassignment'));
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
            $submission=$DB->get_record('poasassignment_submissions',array('pluginid'=>$this->pluginid,'attemptid'=>$attempt->id));
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
            $settingsrecord->pluginid=$this->pluginid;
            $DB->insert_record('poasassignment_type_settings',$settingsrecord);
        }
    }
    function update_settings($poasassignment) {
        global $DB;
        $conditions = array('poasassignmentid'=>$poasassignment->id,
                'pluginid'=>$this->pluginid);
        $recordexists = $DB->record_exists('poasassignment_type_settings',$conditions);
        if (!$recordexists)
            $this->save_settings($poasassignment,$poasassignment->id);
        if ($recordexists && !$this->checked)
            $this->delete_settings($poasassignment->id);
    }
    function delete_settings($poasassignmentid) {
        global $DB;
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                'pluginid'=>$this->pluginid);
        return $DB->delete_records('poasassignment_type_settings',$conditions);
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

        $rec->attemptid=$this->bind_submission_to_attempt($assigneeid,isset($data->draft));
        $rec->assigneeid=$assigneeid;
        $rec->pluginid=$this->pluginid;
        $rec->value=$data->text_editor;
        $DB->insert_record('poasassignment_submissions',$rec);
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
                $submission=$DB->get_record('poasassignment_submissions',array('pluginid'=>$this->pluginid,'attemptid'=>$attempt->id));
                if($submission) 
                    $data->text_editor=$submission->value;
            }
        }
        return $data;
    }
}
