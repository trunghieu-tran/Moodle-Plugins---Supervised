<?php
global $CFG;
require_once dirname(dirname(__FILE__)).'\taskgiver.php';
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
        if(!$DB->record_exists('poasassignment_assignee',array('poasassignmentid'=>$poasassignment->id,'userid'=>$USER->id))) {
            $model = poasassignment_model::get_instance();
            $tasks = poasassignment_model::get_instance($poasassignment)->get_available_tasks($USER->id);
            $tasksarray = array();
            foreach($tasks as $task) 
                $tasksarray[] = $task->id;
            if(count($tasksarray) > 0) {
                $taskid = $tasksarray[rand(0, count($tasksarray) - 1)];
                $poasmodel = poasassignment_model::get_instance($poasassignment);
                $poasmodel->bind_task_to_assignee($USER->id,$taskid);
                redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'view')),null,0);
            }
            else {
                print_string('noavailabletask','poasassignmenttaskgivers_randomchoice');
            }
        }
    }
}
?>
