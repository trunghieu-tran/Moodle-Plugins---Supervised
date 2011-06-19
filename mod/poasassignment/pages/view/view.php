<?php
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
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
        //$poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $poasmodel = poasassignment_model::get_instance();
        // Show submission statistics if user has capability
        if (has_capability('mod/poasassignment:grade', $this->context))
            echo '<div align="right">'.$poasmodel->get_statistics().'</div>';
        
        // Show poasassignment intro
        $this->view_intro();

        // Show task files
        echo $poasmodel->view_files($this->context->id, 'poasassignmentfiles',0);
        $poasmodel->get_files('poasassignmentfiles', 0);
        echo $OUTPUT->box_end();
        $this->view_status();
        $this->view_dates();
        $this->view_feedback();
        $this->view_testresult();
        $this->view_answer_block();
    }
    
    /** Show task status
     *
     *  Draws box with information about student task only if individual tasks
     *  mode is activate.
     */
    function view_status() {
        global $DB,$USER,$OUTPUT;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        // If individual tasks mode is active
        if ($this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS) {
            echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
            echo $OUTPUT->heading(get_string('status','poasassignment'));
            // If user have task
            //if ($DB->record_exists('poasassignment_assignee',
            //        array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id))) {
                $assignee=$DB->get_record('poasassignment_assignee', array('userid'=>$USER->id,
                                                                            'poasassignmentid'=>$this->poasassignment->id));
                if ($assignee && $assignee->taskid > 0) {
                    echo get_string('youhavetask', 'poasassignment');
                    echo ' ';
                    // Show link to the task
                    $taskurl = new moodle_url('/mod/poasassignment/pages/tasks/taskview.php', array('taskid'=>$assignee->taskid, 'id'=>$this->cm->id), 'v', 'get');
                    $task=$DB->get_record('poasassignment_tasks', array('id'=>$assignee->taskid));
                    echo html_writer::link($taskurl, $task->name);

                    // If user can cancel task - show cancel button
                    if($poasmodel->can_cancel_task($assignee->id, $this->context)) {
                    //if (has_capability('mod/poasassignment:managetasks', $this->context)) {
                        $deleteurl = new moodle_url('warning.php', array('action'=>'canceltask',
                                                                        'assigneeid'=>$assignee->id,
                                                                        'id'=>$this->cm->id), 'd', 'post');
                        $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                                '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                        echo ' '.$deleteicon;
                    }
                    if(!empty($this->poasassignment->deadline)) {
                        echo '<br><br>';
                        echo '<b>' . 
                        get_string('timetocompletetask', 'poasassignment') . 
                        ': ' .
                        format_time(time() - $this->poasassignment->deadline) . 
                        '</b>';
                    }
                }
            //}
            else {
                // If user have no task - show link to task page
                echo get_string('youhavenotask', 'poasassignment');
                $taskspageurl = new moodle_url('view.php', array('id'=>$this->cm->id, 'page'=>'tasks'));
                echo ' '.html_writer::link($taskspageurl, get_string('gototasskpage', 'poasassignment'));
                if(!empty($this->poasassignment->choicedate)) {
                    echo '<br><br>';
                    echo '<b>' . 
                        get_string('timetochoosetask', 'poasassignment') . 
                        ': ' .
                        format_time(time() - $this->poasassignment->choicedate) . 
                        '</b>';
                }
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
            if(!$attempt->ratingdate) {
            //if(!$DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id)))
                continue;
            }
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
    }
    
    function view_testresult() {
        global $DB, $OUTPUT, $USER;
        $poasmodel = poasassignment_model::get_instance();
        if(!$DB->record_exists('poasassignment_used_graders', array('poasassignmentid' => $this->poasassignment->id)))
            return;
        if(!$assignee=$DB->get_record('poasassignment_assignee', array('poasassignmentid'=>$this->poasassignment->id,
                                                                            'userid'=>$USER->id)))
            return;
        
        $attempts=array_reverse($DB->get_records('poasassignment_attempts',array('assigneeid'=>$assignee->id),'attemptnumber'));
        
        foreach ($attempts as $attempt) {
            // ask grader if student have test results
            if(!$poasmodel->have_test_results($attempt)) {
                continue;
            }
            else {
                echo $OUTPUT->box_start();
                echo $OUTPUT->heading(get_string('lasttestresults','poasassignment'));
                echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
                echo $poasmodel->show_test_results($attempt);
                echo $OUTPUT->box_end();
                break;
            }
        }
        
    }
    function view_answer_block() {
        global $OUTPUT,$DB,$USER;
        //$plugins=$DB->get_records('poasassignment_answers');
        //$poasmodel=poasassignment_model::get_instance($this->poasassignment);
        $poasmodel=poasassignment_model::get_instance();
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
                    // $attemptsurl = new moodle_url('attempts.php',array('id'=>$this->cm->id,'assigneeid'=>$attempt->assigneeid)); 
                    // echo '<br>'.html_writer::link($attemptsurl,get_string('myattempts','poasassignment'));
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
                echo '<div align="center">';
                echo $OUTPUT->single_button(new moodle_url('submission.php',array('id'=>$this->cm->id)),get_string('addsubmission','poasassignment'));
                echo '</div>';
            }
            
        }
    }
}