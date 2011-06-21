<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');

class taskview_page extends abstract_page {
    private $taskid;
    
    function __construct() {
        global $DB;
        $this->taskid = optional_param('taskid', 0, PARAM_INT);
    }
    
    function has_satisfying_parameters() {
        global $DB;
        if(!$this->task = $DB->get_record('poasassignment_tasks', array('id' => $this->taskid))) {        
            $this->lasterror = 'errornonexistenttask';
            return false;
        }
        return true;
    }
    function view() {
        global $DB, $OUTPUT, $USER;
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $html = '';
        $html .= $OUTPUT->box_start();
        $html .= '<table>';
        $html .= '<tr><td align="right"><b>'.get_string('taskname','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->name.'</td></tr>';

        $html .= '<tr><td align="right"><b>'.get_string('taskintro','poasassignment').'</b>:</td>';
        $html .= '<td class="c1">'.$this->task->description.'</td></tr>';

        $fields = $DB->get_records('poasassignment_fields',
                                   array('poasassignmentid' => $poasassignmentid));        
        $owntask = $DB->record_exists('poasassignment_assignee',
                                      array('userid' => $USER->id,
                                            'taskid' => $this->taskid,
                                            'poasassignmentid' => $poasassignmentid));
            
        foreach ($fields as $field) {
            if (!$field->secretfield || $owntask || has_capability('mod/poasassignment:managetasks', $model->get_context())) {
                // If it is random value and our task, load value from DB
                if ($field->random && ($owntask || has_capability('mod/poasassignment:managetasks',$model->get_context()))) {
                    $assigneeid = $smodel->assignee->id;
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
                    $html .= '<td class="c1"></td></tr>';
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
                        $variants = $model->get_field_variants($field->id);
                        $variant = $variants[$taskvalue->value];
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
        echo $html;
    }
    public static function display_in_navbar() {
        return false;
    }
}