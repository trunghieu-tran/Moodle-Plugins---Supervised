<?php

require_once('abstract_tab.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('model.php');
class submissions_tab extends abstract_tab {
    var $poasassignment;
    function submissions_tab($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
    
    function view() {
        global $DB,$CFG,$OUTPUT;
        $table = new flexible_table('mod-poasassignment-submissions');
        
        $columns=array('picture');
        $columns[]='fullname';
        $headers=array(' ',get_string('fullname','poasassignment'));
        if($this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS) {
            $columns[]='task';
            $headers[]=get_string('task','poasassignment');
        }
        $columns[]='submission';
        $columns[]='status';
        $columns[]='submissiondate';
        $columns[]='gradedate';
        $columns[]='grade';
        $headers[]=get_string('submission','poasassignment');
        $headers[]=get_string('status','poasassignment');
        $headers[]=get_string('submissiondate','poasassignment');
        $headers[]=get_string('gradedate','poasassignment');
        $headers[]=get_string('grade','poasassignment');
        $table->define_columns($columns);
        $table->define_headers($headers);
        //$table->sortable(true, 'name');
        $table->collapsible(true);
        $table->initialbars(true);
        /* $table->column_suppress('fullname'); */
        $table->set_attribute('border', '1');
        $table->set_attribute('width', '100%');
        
        $table->setup();
        $poasmodel=poasassignment_model::get_instance($this->poasassignment);
        $assignees = $DB->get_records('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id));
        $plugins=$DB->get_records('poasassignment_plugins');
        
        $groupmode = groups_get_activity_groupmode($this->cm);
        $currentgroup = groups_get_activity_group($this->cm, true);
        groups_print_activity_menu($this->cm, $CFG->wwwroot . '/mod/poasassignment/view.php?id='.$this->cm->id.'&tab=submissions');
        $context=get_context_instance(CONTEXT_MODULE,$this->cm->id);
        /// Get all ppl that are allowed to submit assignments
        if ($usersid = get_enrolled_users($context, 'mod/poasassignment:submit', $currentgroup, 'u.id')) {
            $usersid = array_keys($usersid);
        }
        
        $indtasks=$this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        foreach($usersid as $userid) {
            $row=array();
            $user=$DB->get_record('user',array('id'=>$userid));
            $user=$DB->get_record('user',array('id'=>$userid));
            $row[]=$OUTPUT->user_picture($user);
            $row[]=$user->firstname.' '.$user->lastname;
            $assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$this->poasassignment->id));
            
            if($indtasks) {
                if($assignee) {
                    $task=$DB->get_record('poasassignment_tasks',array('id'=>$assignee->taskid));
                    $taskurl = new moodle_url('taskview.php',array('taskid'=>$assignee->taskid,'id'=>$this->cm->id),'v','get'); 
                    $deleteurl = new moodle_url('warning.php',array('action'=>'canceltask','assigneeid'=>$assignee->id,'id'=>$this->cm->id),'d','post');
                    $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                                '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                    
                    $row[]=html_writer::link($taskurl,$task->name).' '.$deleteicon;
                }
                else
                    $row[]=get_string('notask','poasassignment');
            }
            $submis='';
            if($assignee)
                foreach($plugins as $plugin) {
                    require_once($plugin->path);
                    $poasassignmentplugin = new $plugin->name();
                    $submis.=$poasassignmentplugin->show_assignee_answer($assignee->id,$this->poasassignment->id,0).'<br>';                
                }
            $row[]=$submis;
            if($assignee) {
                $attempts = $DB->get_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                if(($indtasks && isset($assignee->taskid) && $assignee->taskid>0 && $attempts)||(!$indtasks && $attempts))
                    $row[]=get_string('taskcompleted','poasassignment');
                if(($indtasks && isset($assignee->taskid) && $assignee->taskid>0 && !$attempts)||(!$indtasks && !$attempts))
                    $row[]=get_string('taskinwork','poasassignment');
                if($indtasks &&!isset($assignee->taskid))
                    $row[]=get_string('notask','poasassignment');
            }
            else {
                if(!$indtasks)
                    $row[]=get_string('taskinwork','poasassignment');
                if($indtasks)
                    $row[]=get_string('notask','poasassignment');
            }
            if($assignee) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                if($attemptscount>0) {
                    $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
                    $row[]=userdate($attempt->attemptdate);
                }
                else    
                    $row[]='-';
            }
            else    
                $row[]='-';
            //$row[]='submission date';
            //$row[]='grade date';
            if($assignee) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
                if($attempt) {
                    $gradeurl = new moodle_url('grade.php',array('assigneeid'=>$assignee->id,'id'=>$this->cm->id)); 
                    if(isset($attempt->rating)) {
                        if($attempt->draft==0) {
                            $row[]=userdate($attempt->ratingdate);
                            $ratingwithpenalty=$attempt->rating-$poasmodel->get_penalty($attempt->id);
                            if($attempt->ratingdate<$attempt->attemptdate)
                                $row[]=$ratingwithpenalty.' ('.get_string('outdated','poasassignment').') '.html_writer::link($gradeurl,get_string('editgrade','poasassignment'));
                            else
                                $row[]=$ratingwithpenalty.' '.html_writer::link($gradeurl,get_string('editgrade','poasassignment'));
                        }
                        else {
                            $row[]='-';
                            $row[]=html_writer::link($gradeurl,get_string('addgrade','poasassignment'));
                        }
                    }
                    if(!isset($attempt->rating)) {
                        $row[]='-';
                        $row[]=html_writer::link($gradeurl,get_string('addgrade','poasassignment'));
                    }
                }
                else {
                    $row[]='-';
                    $row[]='-';
                }
            }
            else {
                    $row[]='-';
                    $row[]='-';
            }
            $table->add_data($row);
        }
        

        $table->print_html();
    }
}