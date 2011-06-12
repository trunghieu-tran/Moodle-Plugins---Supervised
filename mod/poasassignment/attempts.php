<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID
$assigneeid = optional_param('assigneeid', 0, PARAM_INT);
$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:view',get_context_instance(CONTEXT_MODULE,$cm->id));

add_to_log($course->id, 'poasassignment', 'attempts', "attempts.php?id=$cm->id&assigneeid=$assigneeid", $poasassignment->name, $cm->id);

$PAGE->set_url('/mod/poasassignment/attempts.php?id='.$cm->id.'&assigneeid='.$assigneeid);
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
    
global $OUTPUT,$DB,$USER;
$context=get_context_instance(CONTEXT_MODULE,$cm->id);
$assignee=$DB->get_record('poasassignment_assignee',array('id'=>$assigneeid));
if(has_capability('mod/poasassignment:grade',$context) || $assignee->userid==$USER->id) {
    
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading($poasassignment->name." : ".get_string('attempts', 'poasassignment'));
    $poasmodel = poasassignment_model::get_instance($poasassignment);
    $attempts=array_reverse($DB->get_records('poasassignment_attempts',array('assigneeid'=>$assigneeid),'attemptnumber'));
    $plugins=$poasmodel->get_plugins();
    $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$poasassignment->id));
    $latestattempt=$DB->get_record('poasassignment_attempts',array('id'=>$assignee->lastattemptid));
    $attemptscount=count($attempts);  
    foreach($attempts as $attempt) {    
        echo $OUTPUT->box_start();
        echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
        
        // show attempt's submission
        foreach($plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            echo $poasassignmentplugin->show_assignee_answer($assigneeid,$poasassignment->id,1,$attempt->id);
        }
        // show disablepenalty/enablepenalty button
        if(has_capability('mod/poasassignment:grade',$context)) {
            if(isset($attempt->disablepenalty) && $attempt->disablepenalty==1) {
                echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$id.'&action=enablepenalty&attemptid='.$attempt->id), 
                                                        get_string('enablepenalty','poasassignment'));
            }
            else {
                echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$id.'&action=disablepenalty&attemptid='.$attempt->id), 
                                                        get_string('disablepenalty','poasassignment'));
            }
        }
        $poasmodel->show_feedback($attempt,$latestattempt,$criterions,$context);
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
}
else
    print_error('noaccess','poasassignment');