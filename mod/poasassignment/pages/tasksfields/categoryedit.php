<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'\config.php');
require_once('categoryedit_form.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\lib.php');
$id         = optional_param('id', 0, PARAM_INT); // course_module ID
$categoryid    = optional_param('categoryid', 0, PARAM_INT);
$fieldid = optional_param('fieldid', 0, PARAM_INT);

$cm             = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:managetasksfields', get_context_instance(CONTEXT_MODULE, $cm->id));

$PAGE->set_url('/mod/poasassignment/categoryedit.php?id=' . $id);
$PAGE->set_title(get_string('modulename', 'poasassignment') . ':' . $poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, 
                                       $course->id, 
                                       get_string('modulename', 'poasassignment')));


                         
global $OUTPUT,$DB;
$poasmodel = poasassignment_model::get_instance($poasassignment);
$mform = new categoryedit_form(null, array('id' => $id,
                                           'categoryid' => $categoryid,
                                           'fieldid' => $fieldid,
                                           'poasassignmentid' => $poasassignment->id));
if ($mform->is_cancelled()) {
    // return to taskfields page
    redirect(new moodle_url('/mod/poasassignment/view.php',
                            array('id' => $cm->id,
                                  'page' => 'tasksfields')), 
             null, 
             0);
}
else {
    if ($mform->get_data()) {
        $data = $mform->get_data();
        if(isset($data->apply)) {
            redirect(new moodle_url('categoryedit.php', 
                     array('id' => $id, 'fieldid' => $data->basefield)), null, 0);
        }
        // apply
        //print_r($data);
//        if ($fieldid > 0) {
//           $poasmodel->update_task_field($fieldid, $data);
//        }
//        else {
//            $poasmodel->add_task_field($data);
//        }
//        redirect(new moodle_url('/mod/poasassignment/view.php',array('id' => $cm->id,'page' => 'tasksfields')), null, 0);
    }
}
//if ($fieldid > 0) {
//    $mform->set_data($DB->get_record('poasassignment_fields', array('id' => $fieldid)));
//    $data = new stdClass();
//    $data->variants = $poasmodel->get_field_variants($fieldid, 0);
//    $data->id = $id;
//    $mform->set_data($data);
//}
echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
$mform->display();
echo $OUTPUT->footer();