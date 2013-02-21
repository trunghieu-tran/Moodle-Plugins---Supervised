<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class taskview_page extends abstract_page {
    private $taskid;
    
    function __construct() {
        global $DB;
        $this->taskid = optional_param('taskid', 0, PARAM_INT);
        $this->from = optional_param('from', 'tasks', PARAM_TEXT);
        $this->assigneeid = optional_param('assigneeid', false, PARAM_INT);
    }
    
    function has_satisfying_parameters() {
        global $DB, $USER;
        if(!$this->task = $DB->get_record('poasassignment_tasks', array('id' => $this->taskid))) {        
            $this->lasterror = 'errornonexistenttask';
            return false;
        }
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $owntask = $DB->record_exists('poasassignment_assignee',
            array(
                'userid' => $USER->id,
                'taskid' => $this->taskid,
                'poasassignmentid' => $poasassignmentid,
                'cancelled' => 0));
        // If you are student and this is not your task, and you can't see other's student tasks...
        if (has_capability('mod/poasassignment:havetask', $model->get_context())
            && !$owntask
            && !has_capability('mod/poasassignment:seeotherstasks', $model->get_context())) {

            // And you also has your own task
            $sql = 'SELECT COUNT(*) as cnt from {poasassignment_assignee}
                where
                userid=? and
                poasassignmentid=? and
                cancelled=0 and
                taskid>0';
            $taskscount = $DB->get_record_sql($sql, array($USER->id, $poasassignmentid));
            if ($taskscount->cnt > 0) {
                $this->lasterror = 'errornotyourtask';
                return false;
            }
        }
        return true;
    }
    function pre_view() {
        // add navigation nodes
        global $PAGE;
        $id = poasassignment_model::get_instance()->get_cm()->id;
        $tasks = new moodle_url('view.php', array('id' => $id,
                                                  'page' => 'tasks'));
        $PAGE->navbar->add(get_string('tasks','poasassignment'), $tasks);

        $taskview = new moodle_url('view.php', array('id' => $id,
                                                     'page' => 'taskview',
                                                     'taskid' => $this->taskid));
        $PAGE->navbar->add(get_string('taskview','poasassignment'). ' ' . $this->task->name, $taskview);
    }
    function view() {
        global $DB, $OUTPUT, $USER;
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;

        $canmanagetasks = has_capability('mod/poasassignment:managetasks', $model->get_context());

        $fields = $DB->get_records('poasassignment_fields',
            array('poasassignmentid' => $poasassignmentid));
        $owntask = $DB->record_exists('poasassignment_assignee',
            array('userid' => $USER->id,
                'taskid' => $this->taskid,
                'poasassignmentid' => $poasassignmentid,
                'cancelled' => 0));

        $html = '';
        $html .= $OUTPUT->box_start();
        if ($canmanagetasks && $this->assigneeid) {
            $userinfo = $model->get_user_by_assigneeid($this->assigneeid);
            if ($userinfo)
                echo $OUTPUT->heading(get_string('studentstask', 'poasassignment') . ' ' . $userinfo->firstname . ' ' . $userinfo->lastname);
        }
        if(has_capability('mod/poasassignment:havetask', $model->get_context())) {
            if ($owntask) {
                echo $OUTPUT->heading(get_string('itsyourtask', 'poasassignment'));
            }
            else {
                echo $OUTPUT->heading(get_string('itsnotyourtask', 'poasassignment'));
            }
        }

        $html .= '<table>';
        $html .= '<tr><td align="right"><b>'.get_string('taskname','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->name.'</td></tr>';

        $html .= '<tr><td align="right"><b>'.get_string('taskintro','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->description.'</td></tr>';

        foreach ($fields as $field) {
            if (!$field->secretfield || $owntask || $canmanagetasks) {
                // If it is random value and our task, load value from DB
                if ($field->random && ($owntask || ($canmanagetasks && $this->assigneeid))) {

                    if ($canmanagetasks && $this->assigneeid) {
                        $assigneeid = $this->assigneeid;
                    }
                    elseif ($owntask) {
                        $assigneeid = $model->assignee->id;
                    }

                    $taskvalue = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,
                                                                        'taskid'=>$this->taskid,
                                                                        'assigneeid'=>$assigneeid));
                }
                else {
                    $taskvalue = $DB->get_record('poasassignment_task_values', array('fieldid' => $field->id,
                                                                        'taskid' => $this->taskid), '*', IGNORE_MULTIPLE);
                }
                
                $html .= '<tr><td align="right"><b>' . $field->name;
                if (has_capability('mod/poasassignment:seefielddescription', $model->get_context())) {
                    $html .= ' ' . $model->help_icon($field->description);
                }
                $html .= '</b>:</td>';
                if (!$taskvalue) {
                    $str = '<span class="poasassignment-critical">'.get_string('notdefined', 'poasassignment').'</span>';
                }
                else {
                    $str = ' ';
                    if ($field->ftype == STR 
                        ||$field->ftype == TEXT 
                        || $field->ftype == FLOATING 
                        || $field->ftype == NUMBER ) {
                        
                        $str = $taskvalue->value;
                    }
                    if ($field->ftype == DATE ) {
                        $str = userdate($taskvalue->value, get_string('strftimedaydate', 'langconfig'));
                    }
                    if ($field->ftype == FILE ) {
                        $str = $model->view_files($model->get_context()->id,'poasassignmenttaskfiles', $taskvalue->id);
                    }
                    if ($field->ftype == LISTOFELEMENTS ) {
                        $variants = $model->get_variants($field->id);
                        if (isset($variants[$taskvalue->value]))
                            $variant = $variants[$taskvalue->value];
                        else
                            $variant = '<span class="poasassignment-critical">'.get_string('notdefined', 'poasassignment').'</span>';
                        $str = $variant;
                    }
                    if ($field->ftype == MULTILIST ) {
                        $tok = strtok($taskvalue->value,',');
                        $opts=array();
                        while(strlen($tok)>0) {
                            $opts[]=$tok;
                            $tok=strtok(',');
                        }
                        $taskvalue->value='';
                        $variants=$model->get_field_variants($field->id);
                        foreach($opts as $opt) {
                            $variant = $variants[$opt];
                            $taskvalue->value .= $variant.'<br>';
                        }
                        $str = $taskvalue->value;
                    }
                }
                if ($field->random) {
                    if (!$owntask) {
                        if (($canmanagetasks && !$this->assigneeid) || !$canmanagetasks) {
                            $str = '<span class="random-field">'.get_string('randomfield', 'poasassignment').'</span>';
                        }
                    }
                }
                $html .= '<td class="c1">' . $str . '</td></tr>';
            }
        }
        $html .= '</table>';
        $html .= $OUTPUT->box_end();
        // Add back button
        $id = poasassignment_model::get_instance()->get_cm()->id;
        if ($this->from ==='view' || $this->from === 'tasks') {
            $html .= $OUTPUT->single_button(new moodle_url('view.php',array('id'=>$id,'page'=>$this->from)),
                                            get_string('backto'.$this->from,'poasassignment'),'get');
        }
        echo $html;
    }
    public static function display_in_navbar() {
        return false;
    }
}