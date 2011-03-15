<?php
global $CFG;
require_once dirname(dirname(__FILE__)).'\taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class studentschoice extends taskgiver {
//    function print_before_tasks() {
//        echo "students choice";
//    }
    public $showtasks = true;
    
    function get_task_extra_string($taskid,$cmid) {
        global $USER,$OUTPUT;

        $takeurl = new moodle_url('warning.php?id='.$cmid.'&action=taketask&taskid='.$taskid.'&userid='.$USER->id);
        $takeicon= '<a href="'.$takeurl.'">'.'<img src="'.$OUTPUT->pix_url('taketask','poasassignment').
                    '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('taketask','poasassignment').'" /></a>';
        return $takeicon;
    }
//    function print_after_tasks() {
//        echo 'Good bye!';
//    }
}
?>
