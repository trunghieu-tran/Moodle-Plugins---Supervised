<?php 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
class poasassignment_answer {

    var $pluginid;
    
    function poasassignment_answer() {
    }
    
    // Displays subplugin's settings in mod_form.php
    function show_settings($mform) {
    }
    
    // Vaildates subplugin's settigns in mod_form.php
    static function validation($data, &$errors) {
    }
    
    // Displays form to input an answer
    function show_answer_form() {
    }
    
    // Saves subplugin settings in DB
    function save_settings($poasassignmentanswer) {
    }
    // Delete all subplugin settings from DB
    function delete_settings($poasassignmentid) {
        global $DB;
        return $DB->delete_records('poasassignment_type_settings',array('poasassignmentid'=>$poasassignmentid));
    }
    
    function return_settings_type($poasassignmentid,$type) {      
    }
    
    function delete_settings_type($poasassignmentid, $type) {
    }
    
    // Returns true, if plugin with $pluginid subplugin is used in poasassignment with $poasassignmentid
    static function used_in_poasassignment($pluginid,$poasassignmentid) {
        global $DB;
        return $DB->record_exists('poasassignment_type_settings',array('poasassignmentid'=>$poasassignmentid,
                                                                'pluginid'=>$pluginid));    
    }
    
    function bind_submission_to_attempt($assigneeid,$draft) {
        global $DB;
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        //echo $draft;
        if($attemptscount==0) {
            $attempt->attemptnumber=1;
            $attempt->assigneeid=$assigneeid;
            $attempt->attemptdate=time();
            $attempt->disablepenalty=0;
            $attempt->draft=$draft;
            if($draft)
                $attempt->disablepenalty=1;
            
            $attemptid=$DB->insert_record('poasassignment_attempts',$attempt);
        }
        if($attemptscount>0) {
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
            if(!$DB->record_exists('poasassignment_submissions',array('pluginid'=>$this->pluginid,'attemptid'=>$attempt->id)))
                $attemptid=$attempt->id;
            else {
                $newattempt->attemptnumber=$attemptscount+1;
                $newattempt->assigneeid=$assigneeid;
                $newattempt->attemptdate=time();
                $newattempt->ratingdate=$attempt->ratingdate;
                $newattempt->rating=$attempt->rating;
                $newattempt->disablepenalty=0;
                $newattempt->draft=$draft;
                if($draft)
                    $newattempt->disablepenalty=1;
                $attemptid=$DB->insert_record('poasassignment_attempts',$newattempt);
            }
        }
        return $attemptid;
    }
    
    
}

class answer_form extends moodleform {
    function definition() {        
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $plugins=$DB->get_records('poasassignment_plugins');
        foreach($plugins as $plugin) {
            if(poasassignment_answer::used_in_poasassignment($plugin->id,$instance['poasassignmentid'])) {
                require_once($plugin->path);
                $poasassignmentplugin = new $plugin->name();
                $poasassignmentplugin->show_answer_form($mform,$instance['poasassignmentid']);
            }
        }
        $mform->addElement('checkbox','draft',get_string('draft','poasassignment'));
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'userid', $instance['userid']);
        $mform->setType('userid', PARAM_INT);
        
        $this->add_action_buttons(true,get_string('sendsubmission', 'poasassignment'));
    }
}