<?php
require_once('answer.php');

class poasassignment_answer_file extends poasassignment_answer {
    var $checked;
    var $fieldnames = array ( 'fileamount','maxfilesize','fileextensions');
    function poasassignment_answer_file() {
        global $DB;
        //if ($DB->get_records('poasassignment_plugins',array('name'=>'poasassignment_answer_file'))>=1) {
        $plugin=$DB->get_record('poasassignment_plugins',array('name'=>'poasassignment_answer_file'));
        if ($plugin) {
            $this->pluginid=$plugin->id;
        }
    }
    function insert_plugin_in_db() {
        global $DB;
        $record->name='poasassignment_answer_file';
        $record->path='answer/answer_file.php';
        if (!$DB->record_exists('poasassignment_plugins',array('name'=>$record->name,'path'=>$record->path)))
            $DB->insert_record('poasassignment_plugins',$record);
    }
    function show_settings(&$mform,$poasassignmentid) {
        global $CFG,$COURSE,$DB;
        $mform->addElement('header','answerfileheader',get_string('answerfile','poasassignment'));
        $mform->addElement('checkbox','answerfile', get_string('answerfile','poasassignment'));
        
        $conditions = array('poasassignmentid'=>$poasassignmentid,'pluginid'=>$this->pluginid);
        if ($DB->record_exists('poasassignment_type_settings',$conditions))
            $mform->setDefault('answerfile','true');
        $mform->addHelpButton('answerfile', 'answerfile', 'poasassignment');
        
        $mform->addElement('select','fileamount',get_string('submissionfilesamount','poasassignment'),array(
            1,2,3,4,5,6,7,8,9,10));
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                'pluginid'=>$this->pluginid,
                'name'=>'fileamount');
        if ($DB->record_exists('poasassignment_type_settings',$conditions)) {
            $rec=$DB->get_record('poasassignment_type_settings',$conditions);
            $mform->setDefault('fileamount',$rec->value);
            }
        $mform->disabledIf('fileamount','answerfile');
        
        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        $choices[0] = get_string('courseuploadlimit') . ' ('.display_size($COURSE->maxbytes).')';
        $mform->addElement('select', 'maxfilesize', get_string('maximumsize', 'poasassignment'), $choices);
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                'pluginid'=>$this->pluginid,
                'name'=>'maxfilesize');
        if ($DB->record_exists('poasassignment_type_settings',$conditions)) {
            $rec=$DB->get_record('poasassignment_type_settings',$conditions);
            $mform->setDefault('maxfilesize',$rec->value);
            }
        $mform->disabledIf('maxfilesize','answerfile');
        
        $mform->addElement('text', 'fileextensions', get_string('fileextensions', 'poasassignment'), array('size'=>'64'));
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                'pluginid'=>$this->pluginid,
                'name'=>'fileextensions');
        if ($DB->record_exists('poasassignment_type_settings',$conditions)) {
            $rec=$DB->get_record('poasassignment_type_settings',$conditions);
            $mform->setDefault('fileextensions',$rec->value);
            }
        $mform->addHelpButton('fileextensions', 'fileextensions', 'poasassignment');
        $mform->disabledIf('fileextensions','answerfile');
    }
    function save_settings($poasassignment, $id) {
        global $DB;
        if ($this->checked) {
            //$plugin=$DB->get_record('poasassignment_plugins',array('name'=>'poasassignment_answer_file'));
            $settingsrecord->poasassignmentid=$id;
            //$settingsrecord->pluginid=$plugin->id;
            $settingsrecord->pluginid=$this->pluginid;
            
            $settingsrecord->name='fileamount';
            $settingsrecord->value=$poasassignment->fileamount;
            $DB->insert_record('poasassignment_type_settings',$settingsrecord);
            
            $settingsrecord->name='maxfilesize';
            $settingsrecord->value=$poasassignment->maxfilesize;
            $DB->insert_record('poasassignment_type_settings',$settingsrecord);
            
            $settingsrecord->name='fileextensions';
            $settingsrecord->value=$poasassignment->fileextensions;
            $DB->insert_record('poasassignment_type_settings',$settingsrecord);
        }
    }
    function update_settings($poasassignment) {
        global $DB;
        //$plugin=$DB->get_record('poasassignment_plugins',array('name'=>'poasassignment_answer_file'));
        $conditions = array('poasassignmentid'=>$poasassignment->id,
                //'pluginid'=>$plugin->id);
                'pluginid'=>$this->pluginid);
        $recordexists = $DB->record_exists('poasassignment_type_settings',$conditions);
        if (!$recordexists)
            $this->save_settings($poasassignment,$poasassignment->id);
        //$temp=$poasassignment->flags&64;
        if ($recordexists && !$this->checked)
            $this->delete_settings($poasassignment->id);
        if ($recordexists && $this->checked) {
            $settingsrecord->poasassignmentid=$poasassignment->id;
            //$settingsrecord->pluginid=$plugin->id;
            $settingsrecord->pluginid=$this->pluginid;
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'fileamount');
            $currentsetting=$DB->get_record('poasassignment_type_settings',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='fileamount';
            $settingsrecord->value=$poasassignment->fileamount;
            $DB->update_record('poasassignment_type_settings',$settingsrecord);
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'maxfilesize');
            $currentsetting=$DB->get_record('poasassignment_type_settings',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='maxfilesize';
            $settingsrecord->value=$poasassignment->maxfilesize;
            $DB->update_record('poasassignment_type_settings',$settingsrecord);
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'fileextensions');
            $currentsetting=$DB->get_record('poasassignment_type_settings',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='fileextensions';
            $settingsrecord->value=$poasassignment->fileextensions;
            $DB->update_record('poasassignment_type_settings',$settingsrecord);
        }
    }
    function delete_settings($poasassignmentid) {
        global $DB;
        //$plugin=$DB->get_record('poasassignment_plugins',array('name'=>'poasassignment_answer_file'));
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                //'pluginid'=>$plugin->id);
                'pluginid'=>$this->pluginid);
        return $DB->delete_records('poasassignment_type_settings',$conditions);
    }
    function show_answer_form($mform,$poasassignmentid) {
        global $DB;
        /* $plugin_settings = $DB->get_records('poasassignment_type_settings',array('poasassignmentid'=>$poasassignmentid,
                                                            'pluginid'=>$this->pluginid)); */
        /* $mform = new answer_form_file();
        $mform->display(); */
        
        //answer options
        $mform->addElement('header','answerfileheader',get_string('answerfile','poasassignment'));
                
        $options = array();
        $options['subdirs']=0;
        $plugin_settings_size = $DB->get_record('poasassignment_type_settings',array('poasassignmentid'=>$poasassignmentid,
                                                            'pluginid'=>$this->pluginid,'name'=>'maxfilesize'));
        $plugin_settings_amount = $DB->get_record('poasassignment_type_settings',array('poasassignmentid'=>$poasassignmentid,
                                                            'pluginid'=>$this->pluginid,'name'=>'fileamount'));  
                                                                    
        $options['maxbytes'] = $plugin_settings_size->value;
        $options['maxfiles'] = ($plugin_settings_amount->value)+1;
        $mform->addElement('filemanager', 'answerfiles_filemanager', get_string('answerfiles','poasassignment'),$options);
    }
    function configure_flag($poasassignment) {
        if (isset($poasassignment->answerfile)) {
            $this->checked=true;
            unset($poasassignment->answerfile);
            }
        else
            $this->checked=false;
    }
    function save_answer($assigneeid,$data) {
        $poasmodel = poasassignment_model::get_instance();
        global $DB;
        
        $rec->attemptid=$this->bind_submission_to_attempt($assigneeid,isset($data->draft));
        $rec->assigneeid=$assigneeid;
        $rec->pluginid=$this->pluginid;
        $name='answerfiles_filemanager';
        $rec->value=$data->$name;
        $submissionid=$DB->insert_record('poasassignment_submissions',$rec);
        $poasmodel->save_files($data->$name,'submissionfiles',$submissionid);
    }
    
    function show_assignee_answer($assigneeid,$poasassignmentid,$needbox=1) {
        global $DB,$OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $html='';
        if(!$assigneeid)
            return $html;
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        if($attempt) {
            $submission=$DB->get_record('poasassignment_submissions',array('pluginid'=>$this->pluginid,'attemptid'=>$attempt->id));
            if($submission) {
                
                if($needbox)
                    $html.=$OUTPUT->box_start();
                $cm = get_coursemodule_from_instance('poasassignment',$poasassignmentid);
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                $html.= $poasmodel->view_files($context->id,'submissionfiles',$submission->id);
                if($needbox) 
                    $html.= $OUTPUT->box_end();                
            }
            return $html;
                //echo $submission->value;
        }        
    }
    
    function get_answer_values($poasassignmentid) {
        global $DB;
        $cm = get_coursemodule_from_instance('poasassignment',$poasassignmentid);
        $context=get_context_instance(CONTEXT_MODULE, $cm->id);
        $data = new stdclass();
        $poasmodel=poasassignment_model::get_instance();
        $filemanager_options = array('subdirs'=>0);
        if($poasmodel->assignee) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id));
                $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount));
                if($attempt) {
                    $submission=$DB->get_record('poasassignment_submissions',array('pluginid'=>$this->pluginid,'attemptid'=>$attempt->id));
                    if($submission) {
                        $data = file_prepare_standard_filemanager($data, 'answerfiles', $filemanager_options, $context, 'mod_poasassignment', 'submissionfiles', $submission->id);    
                        }
                }
            }
        return $data;
    // set file manager itemid, so it will find the files in draft area
    }
}
/* class answer_form_file extends moodleform
{
    function definition() {        
        $mform = $this->_form;
        $mform->addElement('filemanager', 'answerfiles', 'answerfiles');
    }
} */