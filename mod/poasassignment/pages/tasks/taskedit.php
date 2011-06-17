<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'\config.php');
require_once('taskedit_form.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\lib.php');
$id     = optional_param('id', 0, PARAM_INT); // course_module ID
$mode   = optional_param('mode', null, PARAM_INT);
$taskid = optional_param('taskid', -1, PARAM_INT);
$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:managetasks',get_context_instance(CONTEXT_MODULE,$cm->id));

//add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&page=$page", $poasassignment->name, $cm->id);

$PAGE->set_url('/mod/poasassignment/taskedit.php?id=$cm->id');
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB;
// edit visibility of task
if ($mode == SHOW_MODE || $mode == HIDE_MODE) {
    if (isset($taskid) && $taskid > 0) {
        $task=$DB->get_record('poasassignment_tasks', array('id'=>$taskid));
        if ($mode == SHOW_MODE) {
            $task->hidden = 0;
        }
        else {
            $task->hidden = 1;
        }
        $DB->update_record('poasassignment_tasks', $task);
        redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$id,'page'=>'tasks')), null, 0);
    }
    else
        print_error('invalidtaskid','poasassignment');
}   
//$poasmodel = poasassignment_model::get_instance($poasassignment);
$poasmodel = poasassignment_model::get_instance();
$poasmodel->cash_instance($poasassignment->id);
if ($mode == DELETE_MODE) {
    if ($taskid > 0) {
        //TODO delete task and task values & references from student's pagele
        $poasmodel->delete_task($taskid);
        redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$id,'page'=>'tasks')), null, 0);
    } 
    else
        print_error('invalidtaskid','poasassignment');
}

$mform = new taskedit_form(null, array('id' => $id, 
                                       'taskid' => $taskid,
                                       'poasassignmentid' => $poasassignment->id));
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/poasassignment/view.php', array('id' => $id, 
                                                                  'page' => 'tasks')), 
                                                            null, 
                                                            0);
}
else {
    if ($mform->get_data()) {
        $data = $mform->get_data();
        if ($taskid > 0) {
            $poasmodel->update_task($taskid,$data);            
        }
        else {
            $poasmodel->add_task($data);
        }
        redirect(new moodle_url('/mod/poasassignment/view.php', array('id' => $cm->id, 'page' => 'tasks')), null, 0);
    }
    
}
if ($taskid > 0) {
    $data = $poasmodel->get_task_values($taskid);
    $data->id = $id;
    $mform->set_data($data);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
$mform->display();
echo $OUTPUT->footer();