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

    function process_before_output($cmid, $poasassignment) {
        global $USER;
        $model = poasassignment_model::get_instance();
        if (has_capability('mod/poasassignment:havetask', $model->get_context()) && !$model->check_dates()) {
            if (!poasassignment_model::user_have_active_task($USER->id, $poasassignment->id)) {
                $tasks = $model->get_available_tasks($USER->id);
                $taskid = poasassignment_model::get_random_task_id($tasks);

                if($taskid > -1) {
                    $poasmodel = poasassignment_model::get_instance($poasassignment);
                    $poasmodel->bind_task_to_assignee($USER->id, $taskid);
                    redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'view')),null,0);
                }
                else {
                    print_error('noavailabletask', 'poasassignmenttaskgivers_randomchoice', new moodle_url('/mod/poasassignment/view.php',
                            array('id'=>$model->get_cm()->id, 'page' => 'view')));
                }
            }
        }
    }
}
?>
