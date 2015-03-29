<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$action = optional_param('action', null, PARAM_TEXT);

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
    
global $OUTPUT,$DB,$PAGE;
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
switch ($action) {
    case 'disablepenalty':
        $attemptid=optional_param('attemptid',-1,PARAM_INT);
        if(!isset($attemptid) ||$attemptid<1)
            print_error('invalidattemptid','poasassignment');
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        if(has_capability('mod/poasassignment:grade',$context)) {
            $attempt=$DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
            $attempt->disablepenalty=1;
            $DB->update_record('poasassignment_attempts',$attempt);
            redirect(new moodle_url('attempts.php',array('id'=>$cm->id,'assigneeid'=>$attempt->assigneeid)));
        }        
        break;
    case 'enablepenalty':
        $attemptid=optional_param('attemptid',-1,PARAM_INT);
        if(!isset($attemptid) ||$attemptid<1)
            print_error('invalidattemptid','poasassignment');
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        if(has_capability('mod/poasassignment:grade',$context)) {
            $attempt=$DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
            $attempt->disablepenalty=0;
            $DB->update_record('poasassignment_attempts',$attempt);
            redirect(new moodle_url('attempts.php',array('id'=>$cm->id,'assigneeid'=>$attempt->assigneeid)));
        }        
        break;
    case 'canceltask':
        $assigneeid = optional_param('assigneeid', -1, PARAM_INT);
        if(!isset($assigneeid) ||$assigneeid<1)
            print_error('invalidassigneeid','poasassignment');
        
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        if(has_capability('mod/poasassignment:managetasks',$context)) {
            $DB->delete_records('poasassignment_assignee',array('id'=>$assigneeid));
            $attempts=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            foreach($attempts as $attempt) {
                $DB->delete_records('poasassignment_submissions',array('attemptid'=>$attempt->id));
                $DB->delete_records('poasassignment_rating_values',array('attemptid'=>$attempt->id));
            }
            $DB->delete_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'view')),null,0);
        }
    case 'deletefield':
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        require_capability('mod/poasassignment:managetasksfields',$context);
        $fieldid=optional_param('fieldid',-1,PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&fieldid='.$fieldid.'&action=deletefield');
        if(!isset($fieldid) ||$fieldid<1)
            print_error('invalidfieldid','poasassignment');
        $field=$DB->get_record('poasassignment_fields',array('id'=>$fieldid));
        if(!$field)
            print_error('invalidfieldid','poasassignment');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        echo $OUTPUT->box_start();
        echo get_string('deletefieldconfirmation','poasassignment');
        echo ' -'.$field->name;
        echo $OUTPUT->box_end();
        echo $OUTPUT->single_button(new moodle_url('warning.php',
                                array('id'=>$id,'fieldid'=>$fieldid,'action'=>'deletefieldconfirmed')), 
                                get_string('yes'),
                                'post');
        echo $OUTPUT->single_button(new moodle_url('view.php',array('id'=>$id,'tab'=>'tasksfields')), get_string('no'),'get');
        echo $OUTPUT->footer();    
        break;
    case 'deletefieldconfirmed':
        if (isset($_POST['fieldid']))
            $fieldid = $_POST['fieldid'];
        if(!isset($fieldid) ||$fieldid<1)
            print_error('invalidfieldid','poasassignment');
        $field=$DB->get_record('poasassignment_fields',array('id'=>$fieldid));
        if(!$field)
            print_error('invalidfieldid','poasassignment');
        $poasmodel=poasassignment_model::get_instance($poasassignment);
        $poasmodel->delete_field($fieldid);
        
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'tasksfields')),null,0);
        break;
    case 'taketask':
        $taskid = optional_param('taskid', -1, PARAM_INT);
        $userid = optional_param('userid', -1, PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&taskid='.$taskid.'&action=taketask&userid='.$userid);

        if(!isset($taskid) || $taskid<1)
            print_error('invalidtaskid','poasassignment');
        if(!isset($userid) || $userid<1)
            print_error('invaliduserid','poasassignment');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        echo $OUTPUT->box_start();
        echo get_string('taketaskconfirmation','poasassignment');
        echo $OUTPUT->box_end();
        echo $OUTPUT->single_button(new moodle_url('warning.php',
                                array('id'=>$id,'taskid'=>$taskid,'userid'=>$userid,'action'=>'taketaskconfirmed')), 
                                get_string('yes'),
                                'post');
        echo $OUTPUT->single_button(new moodle_url('taskview.php',array('id'=>$id,'taskid'=>$taskid)), get_string('no'),'get');
        echo $OUTPUT->footer();    
        break;
    case 'taketaskconfirmed':
        if (isset($_POST['taskid']))
            $taskid = $_POST['taskid'];
        
        //$taskid = optional_param('taskid', -1, PARAM_INT);
        $userid = optional_param('userid', -1, PARAM_INT);
        $PAGE->set_url(new moodle_url('/mod/poasassignment/warning.php',array('id'=>$id,'taskid'=>$taskid,'userid'=>$userid,'action'=>'taketaskconfirmed')));

        if(!isset($taskid) || $taskid<1)
            print_error('invalidtaskid','poasassignment');
        if(!isset($userid) || $userid<1)
            print_error('invaliduserid','poasassignment');
        if($DB->record_exists('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$poasassignment->id)))
            print_error('alreadyhavetask','poasassignment');
        $poasmodel=poasassignment_model::get_instance($poasassignment);
        
        $poasmodel->bind_task_to_assignee($userid,$taskid);
        
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'view')),null,0);
        break;
}
//require_capability('mod/poasassignment:managetasks',get_context_instance(CONTEXT_MODULE,$cm->id));

//add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&tab=$tab", $poasassignment->name, $cm->id);

