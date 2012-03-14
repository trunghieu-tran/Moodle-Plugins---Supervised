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
    }
    
    function has_satisfying_parameters() {
    	//TODO приватность
        global $DB;
        if(!$this->task = $DB->get_record('poasassignment_tasks', array('id' => $this->taskid))) {        
            $this->lasterror = 'errornonexistenttask';
            return false;
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


        $fields = $DB->get_records('poasassignment_fields',
            array('poasassignmentid' => $poasassignmentid));
        $owntask = $DB->record_exists('poasassignment_assignee',
            array('userid' => $USER->id,
                'taskid' => $this->taskid,
                'poasassignmentid' => $poasassignmentid));

        $html = '';
        $html .= $OUTPUT->box_start();
        if ($owntask) {
            echo $OUTPUT->heading(get_string('itsyourtask', 'poasassignment'));
        }
        else {
            echo $OUTPUT->heading(get_string('itsnotyourtask', 'poasassignment'));
        }
        $html .= '<table>';
        $html .= '<tr><td align="right"><b>'.get_string('taskname','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->name.'</td></tr>';

        $html .= '<tr><td align="right"><b>'.get_string('taskintro','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->description.'</td></tr>';

            
        foreach ($fields as $field) {
            if (!$field->secretfield || $owntask || has_capability('mod/poasassignment:managetasks', $model->get_context())) {
                // If it is random value and our task, load value from DB
                if ($field->random && ($owntask || has_capability('mod/poasassignment:managetasks',$model->get_context()))) {
                    $assigneeid = $model->assignee->id;
                    $taskvalue = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,
                                                                        'taskid'=>$this->taskid,
                                                                        'assigneeid'=>$assigneeid));
                }
                else {
                    $taskvalue=$DB->get_record('poasassignment_task_values', array('fieldid' => $field->id,
                                                                        'taskid' => $this->taskid));
                }
                
                $html .= '<tr><td align="right"><b>' . $field->name;
                if (has_capability('mod/poasassignment:seefielddescription', $model->get_context())) {
                    $html .= ' ' . $model->help_icon($field->description);
                }
                $html .= '</b>:</td>';
                if (!$taskvalue) {
                    $html .= '<td class="c1"><span class="poasassignment-critical">'.get_string('notdefined', 'poasassignment').'</span></td></tr>';
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
                    $html .= '<td class="c1">' . $str . '</td></tr>';
                }
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