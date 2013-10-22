<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
require_once('grade_form.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$assigneeid = optional_param('assigneeid', -1, PARAM_INT);

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:grade',get_context_instance(CONTEXT_MODULE,$cm->id));

add_to_log($course->id, 'poasassignment', 'grade', "grade.php?id=$cm->id&assigneeid=$assigneeid", $poasassignment->name, $cm->id);

$PAGE->set_url('/mod/poasassignment/grade.php?id='.$cm->id.'&assigneeid='.$assigneeid);
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB,$USER;
if(!$DB->record_exists('poasassignment_criterions',array('poasassignmentid'=>$poasassignment->id))) {
    print_error('errornocriterions','poasassignment');
}    
$poasmodel = poasassignment_model::get_instance($poasassignment);
$mform=new grade_form(null,array('id'=>$cm->id,'assigneeid'=>$assigneeid,'poasassignmentid'=>$poasassignment->id));
$data=$poasmodel->get_rating_data($assigneeid);
$mform->set_data($data);
if($mform->is_cancelled()) {
    redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'submissions')),null,0);
}
else {
    if($data=$mform->get_data()) {
        $poasmodel->save_grade($assigneeid,$data);
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'submissions')),null,0);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
$mform->display();
echo $OUTPUT->footer();