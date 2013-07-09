<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('criterionsedit_form.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$mode = optional_param('mode', ADD_MODE, PARAM_INT);
$criterionid = optional_param('criterionid', -1, PARAM_INT);

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:managecriterions',get_context_instance(CONTEXT_MODULE,$cm->id));

add_to_log($course->id, 'poasassignment', 'criterionsedit', 
            "criterionsedit.php?id=$cm->id", $poasassignment->name, $cm->id);

$PAGE->set_url('/mod/poasassignment/criterionedit.php?id=$cm->id');
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB;
$poasmodel = poasassignment_model::get_instance($poasassignment);
if($mode==DELETE_MODE) {
    if($criterionid>0) {
        $poasmodel->delete_field('poasassignment_criterions',$criterionid,'poasassignment_rating_values','criterionid');
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'criterions')),null,0);
    } else
        error('Incorrect criterion id');
}

$mform = new criterionsedit_form(null,array('id'=>$cm->id,'criterionid'=>$criterionid,'mode'=>$mode,'poasassignmentid'=>$poasassignment->id));
if($mform->is_cancelled()) {
    redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'criterions')),null,0);
}
else {
    if($mform->get_data()) {
        $data=$mform->get_data();    
        
        // if($mode==EDIT_MODE) {
            // if($criterionid>0) {
                // $criterion=$mform->get_data();
                // $poasmodel->update_criterion($criterionid,$criterion);
                // redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'criterions')),null,0);
            // } else
                // error('Incorrect criterion id');
        // }
        
        $poasmodel->save_criterion($data);
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'criterions')),null,0);
    }
    
}
//if($mode==EDIT_MODE) {
    //$mform->set_data($DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$poasassignment->id)));
    $mform->set_data($poasmodel->get_criterions_data());
    $mform->set_data(array('id'=>$id));
//}
echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
$mform->display();
echo $OUTPUT->footer();