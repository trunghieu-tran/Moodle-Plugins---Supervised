<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$taskid = optional_param('taskid', -1, PARAM_INT);

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:view',get_context_instance(CONTEXT_MODULE,$cm->id));

add_to_log($course->id, 'poasassignment', 'taskview', "taskview.php?id=$cm->id&taskid=$taskid", $poasassignment->name, $cm->id);

$PAGE->set_url('/mod/poasassignment/taskview.php?id='.$cm->id.'&taskid='.$taskid);
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB,$USER;

$poasmodel = poasassignment_model::get_instance($poasassignment);

//$mform = new taskview_form(null,array('id'=>$cm->id,'taskid'=>$taskid,'poasassignmentid'=>$poasassignment->id));

echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
echo $OUTPUT->box_start();
$task=$DB->get_record('poasassignment_tasks',array('id'=>$taskid));
echo '<table>';
echo '<tr><td align="right"><b>'.get_string('taskname','poasassignment').'</b>:</td>';
echo '<td class="c1">'.$task->name.'</td></tr>';

echo '<tr><td align="right"><b>'.get_string('taskintro','poasassignment').'</b>:</td>';
echo '<td class="c1">'.$task->description.'</td></tr>';

$fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$poasassignment->id));        
$owntask=$DB->record_exists('poasassignment_assignee',array('userid'=>$USER->id,'taskid'=>$taskid));
    
foreach($fields as $field) {
    if(!$field->secretfield || $owntask  || has_capability('mod/poasassignment:managetasks',get_context_instance(CONTEXT_MODULE,$cm->id))) {
        if($field->random && ($owntask ||has_capability('mod/poasassignment:managetasks',get_context_instance(CONTEXT_MODULE,$cm->id)))) {
            $poasmodel=poasassignment_model::get_instance();
            $assigneeid=$poasmodel->assignee->id;
            $taskvalue=$DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,
                                                                'taskid'=>$taskid,
                                                                'assigneeid'=>$assigneeid));
        }
        else
            $taskvalue=$DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,
                                                                'taskid'=>$taskid));
        
        echo '<tr><td align="right"><b>'.$field->name;
        if(has_capability('mod/poasassignment:seefielddescription',get_context_instance(CONTEXT_MODULE,$cm->id)))
            echo ' '.$poasmodel->help_icon($field->description);
        echo '</b>:</td>';
        if(!$taskvalue)
            echo '<td class="c1"></td></tr>';
        else {
            //if($field->random) {
            //}
            //else {
                if($field->ftype==STR ||$field->ftype==TEXT || $field->ftype==FLOATING || $field->ftype==NUMBER ) {
                    echo '<td class="c1">'.$taskvalue->value.'</td></tr>';
                }
                if($field->ftype==DATE ) {
                    echo '<td class="c1">'.userdate($taskvalue->value,get_string('strftimedaydate', 'langconfig')).'</td></tr>';
                }
                if($field->ftype==FILE ) {
                    $context= get_context_instance(CONTEXT_MODULE, $cm->id);
                    echo '<td class="c1">'.$poasmodel->view_files($context->id,'poasassignmenttaskfiles',$taskvalue->id).'</td></tr>';
                }
                if($field->ftype==LISTOFELEMENTS ) {
                    $variants=$poasmodel->get_field_variants($field->id);
                    $variant=$variants[$taskvalue->value];
                    //$variant = $poasmodel->get_variant($taskvalue->value,$field->variants);
                    echo '<td class="c1">'.$variant.'</td></tr>';
                }
                if($field->ftype==MULTILIST ) {
                    $tok = strtok($taskvalue->value,',');
                    $opts=array();
                    while(strlen($tok)>0) {
                        $opts[]=$tok;
                        $tok=strtok(',');
                    }
                    $taskvalue->value='';
                    $variants=$poasmodel->get_field_variants($field->id);
                    foreach($opts as $opt) {
                        $variant = $variants[$opt];
                        $taskvalue->value.=$variant.'<br>';
                    }
                    echo '<td class="c1">'.$taskvalue->value.'</td></tr>';
                }
            //}
        }
    }
}
echo '</table>';
//echo $task->name;
echo $OUTPUT->box_end();
if($DB->record_exists('poasassignment_assignee',array('userid'=>$USER->id,'taskid'=>$taskid)))
    echo $OUTPUT->single_button(new moodle_url('view.php?id='.$id.'&tab=view'), get_string('back'));
else
    echo $OUTPUT->single_button(new moodle_url('view.php?id='.$id.'&tab=tasks'), get_string('back'));
$assignee=$DB->get_record('poasassignment_assignee',array('id'=>$poasmodel->assignee->id));

if(!$assignee ||($assignee && ($assignee->taskid==0 || !isset($assignee->taskid))))
    if($poasassignment->howtochoosetask==STUDENTSCHOICE)
        echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$id.'&action=taketask&taskid='.$taskid.'&userid='.$USER->id), get_string('taketask','poasassignment'));
//$mform->display();
echo $OUTPUT->footer();