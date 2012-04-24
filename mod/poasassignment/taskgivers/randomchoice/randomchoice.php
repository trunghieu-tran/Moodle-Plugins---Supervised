<?php
global $CFG;
require_once dirname(dirname(__FILE__)).'/taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class randomchoice extends taskgiver {

    public static function has_settings() {
        return false;
    }
    public static function show_tasks() {
        return false;
    }

    function process_before_tasks($cmid, $poasassignment) {
        global $USER,$DB;
        //if(has_capability('mod/poasassignment:managetasks',poasassignment_model::get_instance()->get_context())) {
        //    return;
        //}
        if (has_capability('mod/poasassignment:havetask', poasassignment_model::get_instance()->get_context())) {
            if (!poasassignment_model::user_have_active_task($USER->id, $poasassignment->id)) {
	        //if(!$DB->record_exists('poasassignment_assignee',array('poasassignmentid'=>$poasassignment->id, 'userid'=>$USER->id, 'taskid' => 0))) {
	            $model = poasassignment_model::get_instance();
	            $tasks = $model->get_available_tasks($USER->id);
				$taskid = poasassignment_model::get_random_task_id($tasks);
	            //$tasksarray = array();
	            //foreach($tasks as $task) 
	            //    $tasksarray[] = $task->id;
	            if($taskid > -1) {
	                //$taskid = $tasksarray[rand(0, count($tasksarray) - 1)];
	                $poasmodel = poasassignment_model::get_instance($poasassignment);
	                $poasmodel->bind_task_to_assignee($USER->id, $taskid);
	                redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'view')),null,0);
	            }
	            else {
	                print_string('noavailabletask','poasassignmenttaskgivers_randomchoice');
	            }
	        }
        }
    }
}
?>
