<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('tasksfieldsedit_form.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$mode = optional_param('mode', ADD_MODE, PARAM_INT);
$fieldid = optional_param('fieldid', -1, PARAM_INT);
$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:managetasksfields',get_context_instance(CONTEXT_MODULE,$cm->id));

//add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&tab=$tab", $poasassignment->name, $cm->id);
$PAGE->set_url('/mod/poasassignment/tasksfieldsedit.php?id='.$id);
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB;
$poasmodel = poasassignment_model::get_instance($poasassignment);
$mform = new tasksfieldsedit_form(null,array('id'=>$id,'fieldid'=>$fieldid,'mode'=>$mode,'poasassignmentid'=>$poasassignment->id));
if($mform->is_cancelled()) {
    redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'tasksfields')),null,0);
}
else {
    if($mform->get_data()) {
        $data=$mform->get_data();    
        
        if($mode==EDIT_MODE) {
            if($fieldid>0) {
                $field=$mform->get_data();
                $poasmodel->update_task_field($fieldid,$field);
                redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'tasksfields')),null,0);
            } else
                error('Incorrect field id');
        }
        
        $poasmodel->add_task_field($data);
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'tasksfields')),null,0);
    }
    
}
if($mode==EDIT_MODE) {
    $mform->set_data($DB->get_record('poasassignment_fields',array('id'=>$fieldid)));
    $mform->set_data(array('id'=>$id));
    $preloadeddata->variants=$poasmodel->get_field_variants($fieldid,0,"\n");
    $mform->set_data($preloadeddata);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
$mform->display();
echo $OUTPUT->footer();