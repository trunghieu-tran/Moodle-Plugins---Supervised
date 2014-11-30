<?php

require_once('abstract_tab.php');
require_once('model.php');
class view_tab extends abstract_tab {
    var $poasassignment;
    var $context;
    function view_tab($cm,$poasassignment) {
        
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
        $this->context=get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    function view() {
        global $OUTPUT;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        
        if (has_capability('mod/poasassignment:grade', $this->context))
            echo $poasmodel->get_statistics();
        
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        $this->view_intro();
        echo $poasmodel->view_files($this->context->id,'poasassignmentfiles',0);
        
        echo $OUTPUT->box_end();
        $this->view_status();
        $this->view_dates();
        $this->view_feedback();
        $this->view_answer_block();
    }
    
    function view_status() {
        global $DB,$USER,$OUTPUT;
        if($this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS) {
            echo $OUTPUT->box_start('generalbox boxaligncenter','intro');
            if($DB->record_exists('poasassignment_assignee',array('userid'=>$USER->id))) {
                $assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id));
                if($assignee && $assignee->taskid>0) {
                    echo get_string('youhavetask','poasassignment');
                    echo ' ';
                    $taskurl = new moodle_url('taskview.php',array('taskid'=>$assignee->taskid,'id'=>$this->cm->id),'v','get'); 
                    $task=$DB->get_record('poasassignment_tasks',array('id'=>$assignee->taskid));
                    echo html_writer::link($taskurl,$task->name);
                    
                    if(has_capability('mod/poasassignment:managetasks',$this->context)) {
                        $deleteurl = new moodle_url('warning.php',array('action'=>'canceltask','assigneeid'=>$assignee->id,'id'=>$this->cm->id),'d','post');
                        $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                                '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                        echo ' '.$deleteicon;
                    }
                }
            }
            else {
                echo get_string('youhavenotask','poasassignment');
                $taskstaburl = new moodle_url('view.php',array('id'=>$this->cm->id,'tab'=>'tasks')); 
                echo ' '.html_writer::link($taskstaburl,get_string('gototassktab','poasassignment'));
            }
            echo $OUTPUT->box_end();
        }
    }
    function view_intro() {
        echo format_module_intro('poassignment', $this->poasassignment, $this->cm->id);
    }
    
    
    function view_dates() {
        global $OUTPUT;
        if(!empty($this->poasassignment->availabledate) && !empty($this->poasassignment->choicedate) && !empty($this->poasassignment->deadline)) {
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
    
    function view_feedback() {
        global $OUTPUT,$DB,$USER;
         if($DB->record_exists('poasassignment_assignee',
                            array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id))) {
            $assignee=$DB->get_record('poasassignment_assignee',
                            array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id));
            if($assignee /* && isset($assignee->rating) */) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                $latestattempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
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
        }                            
    }
    
    function view_answer_block() {
        global $OUTPUT,$DB,$USER;
        $plugins=$DB->get_records('poasassignment_plugins');
        $poasmodel=poasassignment_model::get_instance($this->poasassignment);
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id));
        if($this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS) {
            if($DB->record_exists('poasassignment_assignee',
                            array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id))) {
                if($attempt=$DB->get_record('poasassignment_attempts',
                            array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount))) {
                    echo $OUTPUT->heading(get_string('yoursubmissions','poasassignment'));
                    echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attemptscount.' ('.userdate($attempt->attemptdate).')');
                    $attemptsurl = new moodle_url('attempts.php',array('id'=>$this->cm->id,'assigneeid'=>$attempt->assigneeid)); 
                    echo '<br>'.html_writer::link($attemptsurl,get_string('myattempts','poasassignment'));
                    foreach($plugins as $plugin) {
                        require_once($plugin->path);
                        $poasassignmentplugin = new $plugin->name();
                        echo $poasassignmentplugin->show_assignee_answer($poasmodel->assignee->id,$this->poasassignment->id);
                    }
                    
                    if($this->poasassignment->flags&SEVERAL_ATTEMPTS && $poasmodel->assignee->finalized!=1) {
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
                echo $OUTPUT->heading(get_string('yoursubmissions','poasassignment'));
                foreach($plugins as $plugin) {
                    require_once($plugin->path);
                    $poasassignmentplugin = new $plugin->name();
                    echo $poasassignmentplugin->show_assignee_answer($poasmodel->assignee->id,$this->poasassignment->id);
                }
                if($this->poasassignment->flags&SEVERAL_ATTEMPTS) {
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