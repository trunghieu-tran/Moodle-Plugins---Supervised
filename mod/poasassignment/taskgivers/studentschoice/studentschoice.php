<?php
global $CFG;
require_once dirname(dirname(__FILE__)).'/taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class studentschoice extends taskgiver {
    public static function has_settings() {
        return false;
    }
    public static function show_tasks() {
        return true;
    }

    /**
     * Get html to add after task name in table cell
     *
     * @param $taskid poas assignment task id
     * @param $cmid course module id
     * @return mixed html code to add after task name
     */
    function get_task_extra_string($taskid, $cmid) {
        global $USER, $OUTPUT;
        $model = poasassignment_model::get_instance();
        $takeicon = '';
        $hascaptohavetask = has_capability('mod/poasassignment:havetask', poasassignment_model::get_instance()->get_context());
        if ($hascaptohavetask && !$model->check_dates()) {
            // Require mod/poasassignment:havetask to show 'take task' link
            $takeurl = new moodle_url('warning.php?id='.$cmid.'&action=taketask&taskid='.$taskid.'&userid='.$USER->id);
            $takeicon = '<a href="'.$takeurl.'">'.'<img src="'.$OUTPUT->pix_url('taketask','poasassignment').
                '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('taketask','poasassignment').'" /></a>';
        }
        return $takeicon;
    }
}
?>
