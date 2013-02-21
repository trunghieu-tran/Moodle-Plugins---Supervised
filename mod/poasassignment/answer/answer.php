<?php 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
class poasassignment_answer {

    var $answerid;
    
    function __construct() {
    }

    /**
     * Displays subplugin's settings in mod_form.php
     *
     * @param $mform - POAS assignment mod_form.php moodle form
     * @param $poasassignmentid - POAS assignment instance id
     */
    function show_settings($mform, $poasassignmentid) {
    }
    
    // Vaildates subplugin's settigns in mod_form.php
    static function validation($data, &$errors) {
    }

    /**
     * Displays form to input an answer
     *
     * @param $mform - POAS assignment mod_form.php moodle form
     * @param $poasassignmentid - POAS assignment instance id
     */
    function show_answer_form($mform, $poasassignmentid) {
    }
    
    // Saves subplugin settings in DB
    function save_settings($poasassignment, $id) {
    }
    // Delete all subplugin settings from DB
    function delete_settings($poasassignmentid) {
        global $DB;
        return $DB->delete_records('poasassignment_ans_stngs',array('poasassignmentid'=>$poasassignmentid));
    }
    
    function return_settings_type($poasassignmentid,$type) {      
    }
    
    function delete_settings_type($poasassignmentid, $type) {
    }
    
    // Returns true, if plugin with $answerid subplugin is used in poasassignment with $poasassignmentid
    static function used_in_poasassignment($answerid,$poasassignmentid) {
        global $DB;
        return $DB->record_exists('poasassignment_ans_stngs',array('poasassignmentid'=>$poasassignmentid,
                                                                'answerid'=>$answerid));    
    }
    
    public function save_submission($attemptid, $data) {
    }

    /**
     * Deleting submission
     *
     * @param $attemptid
     * @param $cmid
     * @internal param $data
     */
    public function delete_submission($attemptid, $cmid) {
    }
    
    function bind_submission_to_attempt($assigneeid,$draft,$final=0) {
        global $DB;
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        
        //echo $draft;
        $newattempt=new stdClass();
        $newattempt->assigneeid=$assigneeid;
        $newattempt->attemptdate=time();
        $newattempt->disablepenalty=0;
        $newattempt->draft=$draft;
        $newattempt->final=$final;
        if($draft)
            $newattempt->disablepenalty=1;
        
        if($attemptscount==0) {
            $newattempt->attemptnumber=1;
            $attemptid=$DB->insert_record('poasassignment_attempts',$newattempt);
        }
        if($attemptscount>0) {
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
            if(!$DB->record_exists('poasassignment_submissions',array('answerid'=>$this->answerid,'attemptid'=>$attempt->id)))
                $attemptid=$attempt->id;
            else {
                $newattempt->attemptnumber=$attemptscount+1;
                $newattempt->ratingdate=$attempt->ratingdate;
                $newattempt->rating=$attempt->rating;
                $attemptid=$DB->insert_record('poasassignment_attempts',$newattempt);
            }
        }
        return $attemptid;
    }
    public function show_attempt_submission($attemptid) {
    }

    /**
     * Validate user's submission.
     *
     * @param array $data data
     * @param array $files files
     * @return true | array of errors
     */
    public static function validate_submission($data, $files) {
        return array();
    }
    
    
}

class answer_form extends moodleform {
    function definition() {        
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $model = poasassignment_model::get_instance();
        $model->cash_instance($instance['poasassignmentid']);
        $plugins=$DB->get_records('poasassignment_answers');
        foreach($plugins as $plugin) {
            if(poasassignment_answer::used_in_poasassignment($plugin->id, $instance['poasassignmentid'])) {
                require_once($plugin->path);
                $poasassignmentplugin = new $plugin->name();
                $poasassignmentplugin->show_answer_form($mform, $instance['poasassignmentid']);
            }
        }
        
        $mform->addElement('header', 'submissionoptions', get_string('submissionoptions', 'poasassignment'));
        
        $mform->addElement('checkbox', 'draft', get_string('draft', 'poasassignment'));
        
        //$poasassignment  = $DB->get_record('poasassignment', array('id' => $instance['poasassignmentid']), '*', MUST_EXIST);


        //$model = poasassignment_model::get_instance($poasassignment);
        
        if($model->get_poasassignment()->flags & MATCH_ATTEMPT_AS_FINAL) {
            $mform->addElement('checkbox','final',get_string('final','poasassignment'));
        }        
        
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT); 
        
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'userid', $instance['userid']);
        $mform->setType('userid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'submission');
        $mform->setType('userid', PARAM_TEXT);
        
        $this->add_action_buttons(true,get_string('sendsubmission', 'poasassignment'));
    }

    function validation($data, $files) {
        $errors = array();
        $plugins = poasassignment_model::get_instance()->get_plugins();
        foreach($plugins as $plugin) {
            if(poasassignment_answer::used_in_poasassignment($plugin->id, $data['poasassignmentid'])) {
                require_once($plugin->path);
                $pluginname = $plugin->name;
                $errors = array_merge($errors, $pluginname::validate_submission($data, $files));
            }
        }
        return $errors;
    }
}