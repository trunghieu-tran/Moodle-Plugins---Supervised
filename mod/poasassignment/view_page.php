<?php

require_once('abstract_page.php');
require_once('model.php');
class view_page extends abstract_page {
    var $poasassignment;
    var $context;

    /** Constructor, initializes variables $poasassignment, $cm, $context
     */
    function view_page($cm,$poasassignment) {
        
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
        $this->context=get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    function view() {
        global $OUTPUT,$USER;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        
        // Show submission statistics if user has capability
        if (has_capability('mod/poasassignment:grade', $this->context))
            echo '<div align="right">'.$poasmodel->get_statistics().'</div>';
        
        // Show poasassignment intro
        $this->view_intro();

        // Show task files
        echo $poasmodel->view_files($this->context->id, 'poasassignmentfiles',0);
        echo $OUTPUT->box_end();
        $this->view_status();
        $this->view_dates();
        $this->view_feedback();
        $this->view_answer_block();
    }
    
    /** Show task status
     *
     *  Draws box with information about student task only if individual tasks
     *  mode is activate.
     */
    function view_status() {
        global $DB,$USER,$OUTPUT;
        // If individual tasks mode is active
        if ($this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS) {
            echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
            echo $OUTPUT->heading(get_string('status','poasassignment'));
            // If user have task
            if ($DB->record_exists('poasassignment_assignee',
                    array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id))) {
                $assignee=$DB->get_record('poasassignment_assignee', array('userid'=>$USER->id,
                                                                            'poasassignmentid'=>$this->poasassignment->id));
                if ($assignee && $assignee->taskid>0) {
                    echo get_string('youhavetask', 'poasassignment');
                    echo ' ';
                    // Show link to the task
                    $taskurl = new moodle_url('taskview.php', array('taskid'=>$assignee->taskid, 'id'=>$this->cm->id), 'v', 'get');
                    $task=$DB->get_record('poasassignment_tasks', array('id'=>$assignee->taskid));
                    echo html_writer::link($taskurl, $task->name);

                    // TODO: second choice option must be checked too
                    // If user have capability to cancel task - show cancel button
                    if (has_capability('mod/poasassignment:managetasks', $this->context)) {
                        $deleteurl = new moodle_url('warning.php', array('action'=>'canceltask',
                                                                        'assigneeid'=>$assignee->id,
                                                                        'id'=>$this->cm->id), 'd', 'post');
                        $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                                '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                        echo ' '.$deleteicon;
                    }
                }
            }
            else {
                // If user have no task - show link to task page
                echo get_string('youhavenotask', 'poasassignment');
                $taskspageurl = new moodle_url('view.php', array('id'=>$this->cm->id, 'page'=>'tasks'));
                echo ' '.html_writer::link($taskspageurl, get_string('gototasskpage', 'poasassignment'));
            }
            echo $OUTPUT->box_end();
        }
    }

    /** Show module intro
     */
    function view_intro() {
        global $OUTPUT;
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        echo $OUTPUT->heading(get_string('taskdescription','poasassignment'));
        echo format_module_intro('poassignment', $this->poasassignment, $this->cm->id);
    }

    /** Show dates (available date, choice date, deadline) if they exist
     */
    function view_dates() {
        global $OUTPUT;
        if (!empty($this->poasassignment->availabledate) && !empty($this->poasassignment->choicedate) && !empty($this->poasassignment->deadline)) {
            echo $OUTPUT->box_start();
            echo '<table>';
            if (!empty($this->poasassignment->availabledate)) {
                echo '<tr><td align="right"><b>'.get_string('availablefrom','poasassignment').'</b>:</td>';
                echo '    <td class="c1">'.userdate($this->poasassignment->availabledate).'</td></tr>';
            }
            if (!empty($this->poasassignment->choicedate)) {
                echo '<tr><td align="right"><b>'.get_string('selectbefore','poasassignment').'</b>:</td>';
                echo '    <td class="c1">'.userdate($this->poasassignment->choicedate).'</td></tr>';
            }
            if (!empty($this->poasassignment->deadline)) {
                echo '<tr><td align="right"><b>'.get_string('deadline','poasassignment').'</b>:</td>';
                echo '    <td class="c1">'.userdate($this->poasassignment->deadline).'</td></tr>';        
            }
            echo '</table>';
            echo $OUTPUT->box_end();
        }
    }

    function view_feedback_optimized() {
        global $OUTPUT,$DB,$USER;
        if($assignee=$DB->get_record('poasassignment_assignee', array('poasassignmentid'=>$this->poasassignment->id,
                                                                        'userid'=>$USER->id))) {
            if($lastattempt = $DB->get_record('poasassignment_attempts',array('id'=>$assignee->lastattemptid))) {
                
            }
        }
    }
    /** Show teacher comments and submissions for last graded attempt
     */
    function view_feedback() {
        //$this->view_feedback_optimized();
        //return;
        
        global $OUTPUT,$DB,$USER;
        
        if(!$assignee=$DB->get_record('poasassignment_assignee', array('poasassignmentid'=>$this->poasassignment->id,
                                                                            'userid'=>$USER->id)))
            return;        
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        
        
        $attempts=array_reverse($DB->get_records('poasassignment_attempts',array('assigneeid'=>$assignee->id),'attemptnumber'));
        $plugins=$poasmodel->get_plugins();
        $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        $latestattempt=$DB->get_record('poasassignment_attempts',array('id'=>$assignee->lastattemptid));
        $attemptscount=count($attempts);
        // show latest graded feedback
        foreach($attempts as $attempt) {
            if(!$DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id)))
                continue;
            echo $OUTPUT->box_start();
            echo $OUTPUT->heading(get_string('lastgraded','poasassignment'));
            echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
            // show attempt's submission
            foreach($plugins as $plugin) {
                require_once($plugin->path);
                $poasassignmentplugin = new $plugin->name();
                echo $poasassignmentplugin->show_assignee_answer($assignee->id,$this->poasassignment->id,1,$attempt->id);
            }
            $poasmodel->show_feedback($attempt,$latestattempt,$criterions,$this->context);
            echo $OUTPUT->box_end();
            break;
        }   
    
        /* // If user registred in poasassignment database
        if ($DB->record_exists('poasassignment_assignee', array('poasassignmentid'=>$this->poasassignment->id,
                                                                'userid'=>$USER->id))) {
            $assignee=$DB->get_record('poasassignment_assignee', array('poasassignmentid'=>$this->poasassignment->id,
                                                                        'userid'=>$USER->id));
            if ($assignee) {
            
                // Receive attempts count
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                // Recieve latest attempt
                $latestattempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
                // Recieve last attempt with rating
                $attempt=false;
                for($i=$attemptscount;$i>0;$i--) {
                    $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$i));
                    if($attempt && isset($attempt->rating) && 
                            $DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id)))
                            break;
                }
                if($attempt && isset($attempt->rating) && 
                           $DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
                            
                    echo $OUTPUT->heading(get_string('feedback','poasassignment'));
                    if($attempt->ratingdate<$latestattempt->attemptdate)
                        echo $OUTPUT->heading(get_string('oldfeedback','poasassignment'));
                    echo $OUTPUT->box_start();
                    $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
                    foreach($criterions as $criterion) {
                        $ratingvalue=$DB->get_record('poasassignment_rating_values',
                                array('criterionid'=>$criterion->id,
                                        'attemptid'=>$attempt->id));
                        if($ratingvalue) {                
                            
                            echo $OUTPUT->box_start();
                            echo $criterion->name.'<br>';
                            if($attempt->draft==0) {
                                if(has_capability('mod/poasassignment:seecriteriondescription',$this->context))
                                    echo $criterion->description.'<br>';
                                echo $ratingvalue->value.'/100<br>';
                            }
                            
                            $options->area    = 'poasassignment_comment';
                            $options->pluginname = 'poasassignment';
                            $options->context = $this->context;
                            $options->showcount = true;
                            $options->itemid  = $ratingvalue->id;
                            $comment = new comment($options);
                            $comment->output(false);
                            echo $OUTPUT->box_end();
                        }
                    }
                    $poasmodel = poasassignment_model:: get_instance();
                    echo $poasmodel->view_files($this->context->id,'commentfiles',$attempt->id);
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
                            $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
                            foreach($criterions as $criterion) {
                                $ratingvalue=$DB->get_record('poasassignment_rating_values',
                                    array('criterionid'=>$criterion->id,
                                            'attemptid'=>$attempt->id));
                                if($ratingvalue) {                
                                    
                                    echo $OUTPUT->box_start();
                                    echo $criterion->name.'<br>';
                                    if($attempt->draft==0) {
                                        if(has_capability('mod/poasassignment:seecriteriondescription',$this->context))
                                            echo $criterion->description.'<br>';
                                        echo $ratingvalue->value.'/100<br>';
                                    }
                                    
                                    $options->area    = 'poasassignment_comment';
                                    $options->pluginname = 'poasassignment';
                                    $options->context = $this->context;
                                    $options->showcount = true;
                                    $options->itemid  = $ratingvalue->id;
                                    $comment = new comment($options);
                                    $comment->output(false);
                                    echo $OUTPUT->box_end();
                                }
                            }
                            echo $OUTPUT->box_end();
                            $poasmodel = poasassignment_model:: get_instance();
                            echo $poasmodel->view_files($this->context->id,'commentfiles',$attempt->id);
                        }
                    }
                }
            }
        } */                            
    }
    
    function view_answer_block() {
        global $OUTPUT,$DB,$USER;
        //$plugins=$DB->get_records('poasassignment_plugins');
        $poasmodel=poasassignment_model::get_instance($this->poasassignment);
        $plugins=$poasmodel->get_plugins();
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id));
        // if individual tasks mode is active
        if($this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS) {

            if($DB->record_exists('poasassignment_assignee',
                            array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id))) {
                if($attempt=$DB->get_record('poasassignment_attempts',
                            array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount))) {
                    echo $OUTPUT->heading(get_string('lastattempt','poasassignment'));
                    echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attemptscount.' ('.userdate($attempt->attemptdate).')');
                    $attemptsurl = new moodle_url('attempts.php',array('id'=>$this->cm->id,'assigneeid'=>$attempt->assigneeid)); 
                    echo '<br>'.html_writer::link($attemptsurl,get_string('myattempts','poasassignment'));
                    foreach($plugins as $plugin) {
                        require_once($plugin->path);
                        $poasassignmentplugin = new $plugin->name();
                        echo $poasassignmentplugin->show_assignee_answer($poasmodel->assignee->id,$this->poasassignment->id);
                    }
                    
                    /* If student has several attempts and hasn't final grade */
                    if($this->poasassignment->flags&SEVERAL_ATTEMPTS && $poasmodel->assignee->finalized!=1) {
                        if(!$attempt->final || ($attempt->final  && $attempt->rating!=null ))
                            if($submission=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id))) {
                                echo $OUTPUT->single_button(new moodle_url('submission.php',
                                                array('id'=>$this->cm->id,'assigneeid'=>$poasmodel->assignee->id)),get_string('editsubmission','poasassignment'));
                            }
                    }
                }
                if(!$DB->get_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id))) {
                            echo $OUTPUT->single_button(new moodle_url('submission.php',array('id'=>$this->cm->id)),get_string('addsubmission','poasassignment'));
                }
            }
        }
        else {
            if($DB->record_exists('poasassignment_assignee',
                            array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id))) {
                $attempt=$DB->get_record('poasassignment_attempts',
                            array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount));
                echo $OUTPUT->heading(get_string('yoursubmissions','poasassignment'));
                foreach($plugins as $plugin) {
                    require_once($plugin->path);
                    $poasassignmentplugin = new $plugin->name();
                    echo $poasassignmentplugin->show_assignee_answer($poasmodel->assignee->id,$this->poasassignment->id);
                }
                if($this->poasassignment->flags&SEVERAL_ATTEMPTS) {
                    if($attempt && (!$attempt->final || ($attempt->final  && $attempt->rating>0 )))
                        if($submission=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id))) {
                            echo $OUTPUT->single_button(new moodle_url('submission.php',
                                            array('id'=>$this->cm->id,'assigneeid'=>$poasmodel->assignee->id)),get_string('editsubmission','poasassignment'));
                }
            }
            }
            else {
                echo $OUTPUT->single_button(new moodle_url('submission.php',array('id'=>$this->cm->id)),get_string('addsubmission','poasassignment'));
            }
            
        }
    }
}