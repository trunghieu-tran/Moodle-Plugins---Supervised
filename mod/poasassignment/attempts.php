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
    echo $OUTPUT->heading($poasassignment->name);
    $poasmodel = poasassignment_model::get_instance($poasassignment);
    $attempts=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$assigneeid),'attemptnumber');
    $plugins=$DB->get_records('poasassignment_plugins');
    foreach($attempts as $attempt) {
        echo $OUTPUT->box_start();
        echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
        foreach($plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            echo $poasassignmentplugin->show_assignee_answer($assigneeid,$poasassignment->id,1,$attempt->id);
        }
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
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
        $latestattempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
        if(isset($attempt->rating) && 
                            $DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
            echo $OUTPUT->heading(get_string('feedback','poasassignment'));
            echo $OUTPUT->box_start();
            $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$poasassignment->id));
            foreach($criterions as $criterion) {
                $ratingvalue=$DB->get_record('poasassignment_rating_values',
                        array('criterionid'=>$criterion->id,
                                'attemptid'=>$attempt->id));
                if($ratingvalue) {                
                    
                    echo $OUTPUT->box_start();
                    echo $criterion->name.'<br>';
                    if($attempt->draft==0) {
                        if(has_capability('mod/poasassignment:seecriteriondescription',$context))
                            echo $criterion->description.'<br>';
                        echo $ratingvalue->value.'/100<br>';
                    }
                    
                    $options->area    = 'poasassignment_comment';
                    $options->pluginname = 'poasassignment';
                    $options->context = $context;
                    $options->showcount = true;
                    $options->itemid  = $ratingvalue->id;
                    $comment = new comment($options);
                    $comment->output(false);
                    echo $OUTPUT->box_end();
                }
            }
            $poasmodel = poasassignment_model:: get_instance();
            echo $poasmodel->view_files($context->id,'commentfiles',$attempt->id);
            if($attempt->draft==0) {
                echo get_string('penalty','poasassignment').'='.$poasmodel->get_penalty($attempt->id);
                $ratingwithpenalty=$attempt->rating-$poasmodel->get_penalty($attempt->id);
                echo '<br>'.get_string('totalratingis','poasassignment').' '.$ratingwithpenalty;
            }
            echo $OUTPUT->box_end();
        }
        else {
            if($attempt && $attempt->draft==1) {
                if($DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
                    echo $OUTPUT->heading(get_string('feedback','poasassignment'));
                    if($attempt->ratingdate<$latestattempt->attemptdate)
                        echo $OUTPUT->heading(get_string('oldfeedback','poasassignment'));
                    echo $OUTPUT->box_start();
                    $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$poasassignment->id));
                    foreach($criterions as $criterion) {
                    $ratingvalue=$DB->get_record('poasassignment_rating_values',
                            array('criterionid'=>$criterion->id,
                                    'attemptid'=>$attempt->id));
                        if($ratingvalue) {                
                            
                            echo $OUTPUT->box_start();
                            echo $criterion->name.'<br>';
                            if($attempt->draft==0) {
                                if(has_capability('mod/poasassignment:seecriteriondescription',$context))
                                    echo $criterion->description.'<br>';
                                echo $ratingvalue->value.'/100<br>';
                            }
                            
                            $options->area    = 'poasassignment_comment';
                            $options->pluginname = 'poasassignment';
                            $options->context = $context;
                            $options->showcount = true;
                            $options->itemid  = $ratingvalue->id;
                            $comment = new comment($options);
                            $comment->output(false);
                            echo $OUTPUT->box_end();
                        }
                    }
                    echo $OUTPUT->box_end();
                    $poasmodel = poasassignment_model:: get_instance();
                    echo $poasmodel->view_files($context->id,'commentfiles',$attempt->id);
                }
            }
        }
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
}
else
    print_error('noaccess','poasassignment');