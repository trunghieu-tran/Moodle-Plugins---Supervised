<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir . '/tablelib.php');
class submissions_page extends abstract_page {
    var $poasassignment;
    function submissions_page($cm, $poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
    }
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
    
    private function prepare_flexible_table() {
        global $PAGE;
        $table = new flexible_table('mod-poasassignment-submissions');
        $table->baseurl = $PAGE->url;
        $columns = array('picture');
        $columns[] = 'fullname';
        $headers = array(' ',get_string('fullname', 'poasassignment'));
        if($this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS) {
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
        $table->collapsible(true);
        $table->initialbars(false);
        $table->set_attribute('class', 'poasassignment-table');
        $table->set_attribute('width', '100%');
        return $table;
    }
    function view() {
        global $DB, $CFG, $OUTPUT;
        $table = $this->prepare_flexible_table();
        
        $table->setup();
        $poasmodel=poasassignment_model::get_instance($this->poasassignment);
        //$assignees = $DB->get_records('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id));
        $plugins=$poasmodel->get_plugins();
        $groupmode = groups_get_activity_groupmode($this->cm);
        $currentgroup = groups_get_activity_group($this->cm, true);
        groups_print_activity_menu($this->cm, $CFG->wwwroot . '/mod/poasassignment/view.php?id='.$this->cm->id.'&page=submissions');

        $usersid = $poasmodel->get_users_with_active_tasks();
        $indtasks=$this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        foreach($usersid as $userid) {
            $row = $this->get_row($userid, $this->poasassignment->id, $indtasks, $plugins);
            $table->add_data($row);
        }
        $table->print_html();
    }
    private function get_row($userid, $poasassignmentid, $indtasks, $plugins) {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance($poasassignmentid);
        
        // Row that will be returned
        $row = array();
        
        // Add user photo to the row
        $user = $DB->get_record('user', array('id' => $userid));
        $row[] = $OUTPUT->user_picture($user);
        
        // Add user's name to the row
        $userurl = new moodle_url('/user/profile.php', array('id' => $user->id, 'course' => $poasmodel->get_course()->id));
        $row[]=html_writer::link($userurl,fullname($user, true));
        
        // Add task info to the row
        $assignee = $poasmodel->get_assignee($userid, $this->poasassignment->id);
        if($indtasks) {
            if($assignee && $assignee->taskid != 0) {
                $task = $DB->get_record('poasassignment_tasks',array('id'=>$assignee->taskid));
                $taskurl = new moodle_url('view.php',array('page' => 'taskview', 'taskid' => $assignee->taskid,'id' => $this->cm->id, 'assigneeid' => $assignee->id));
                $deleteurl = new moodle_url('warning.php',array('action'=>'canceltask','assigneeid'=>$assignee->id,'id'=>$this->cm->id),'d','post');
                $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('canceltask', 'poasassignment').'" title="'.get_string('canceltask', 'poasassignment').'" /></a>';

                $row[]=html_writer::link($taskurl,$task->name).' '.$deleteicon;
            }
            else {
                $providetask = new moodle_url('view.php',array('page' => 'tasks', 'userid' => $assignee->userid, 'id' => $this->cm->id));
                $row[] = html_writer::link($providetask, get_string('notask','poasassignment'), array('title' => get_string('providetask','poasassignment')));
                //$row[]=get_string('notask','poasassignment');
            }
        }
        // Add last submission to the row
        $submis = '';
        if($assignee) {
            foreach($plugins as $plugin) {
                require_once($plugin->path);
                $poasassignmentplugin = new $plugin->name();
                $submis .= $poasassignmentplugin->show_assignee_answer($assignee->id,$this->poasassignment->id,0);
            }
        }
        if (strlen($submis) == 0)
            $submis = get_string('nosubmission', 'poasassignment');
        //$submis = shorten_text($submis);
        $row[]=$submis;
        
        // Add task status to the row
        if($assignee) {
            $attempts = $DB->get_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
            if(($indtasks && isset($assignee->taskid) && $assignee->taskid>0 && $attempts)||(!$indtasks && $attempts))
                $row[]=get_string('taskcompleted','poasassignment');
            if(($indtasks && isset($assignee->taskid) && $assignee->taskid>0 && !$attempts)||(!$indtasks && !$attempts))
                $row[]=get_string('taskinwork','poasassignment');
            if($indtasks && (!isset($assignee->taskid) || $assignee->taskid == 0))
                $row[]=get_string('notask','poasassignment');
        }
        else {
            if(!$indtasks)
                $row[]=get_string('taskinwork','poasassignment');
            if($indtasks)
                $row[]=get_string('notask','poasassignment');
        }
        
        // Add attempt date to the row
        if($assignee) {
            $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
            if($attemptscount>0) {
                $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
                $row[]=userdate($attempt->attemptdate);
            }
            else    
                $row[] = get_string('nosubmission', 'poasassignment');
        }
        else    
            $row[] = get_string('nosubmission', 'poasassignment');
        
        // Add rating date to the row
        // Add rating to the row
        if($assignee) {

            $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
            if($attempt) {
                $gradeurl = new moodle_url('view.php',array('page' => 'grade', 'assigneeid'=>$assignee->id,'id'=>$this->cm->id));
                if (isset($attempt->ratingdate)) {
                    $row[] = userdate($attempt->ratingdate);
                    if (isset($attempt->rating)) {
                        $ratingwithpenalty = $attempt->rating - $poasmodel->get_penalty($attempt->id);
                        if($attempt->ratingdate < $attempt->attemptdate)
                            $row[] = $ratingwithpenalty 
                                     . ' ('
                                     . get_string('outdated','poasassignment')
                                     . ') '
                                     . html_writer::link($gradeurl, get_string('editgrade', 'poasassignment'));
                        else {
                            $row[] = $ratingwithpenalty
                                     . ' '
                                     . html_writer::link($gradeurl, get_string('editgrade', 'poasassignment'));
                        }
                    }
                    else {
                        // Если нет оценки но есть дата - это был черновик
                        $row[] = get_string('draft','poasassignment')
                                 . ' '
                                 . html_writer::link($gradeurl, get_string('leavecomment', 'poasassignment'));
                    }
                }
                else {
                    $lastgraded = $poasmodel->get_last_graded_attempt($assignee->id);
                    if ($lastgraded == null) {
                        $row[] = '-';
                    }
                    else {
                        $row[] = userdate($lastgraded->ratingdate);
                    }

                    if ($attempt->draft == 1) {
                        $row[] = get_string('draft','poasassignment')
                                 . ' '
                                 . html_writer::link($gradeurl, get_string('leavecomment', 'poasassignment'));
                    }
                    else {
                        if ($lastgraded == null) {
                            $row[] = $OUTPUT->action_link($gradeurl, get_string('addgrade','poasassignment'));
                        }
                        else {
                            $ratingwithpenalty = $lastgraded->rating - $poasmodel->get_penalty($lastgraded->id);
                            $row[] = $ratingwithpenalty
                                    .' ('
                                    .get_string('outdated','poasassignment')
                                    .') '
                                    .html_writer::link($gradeurl,get_string('editgrade','poasassignment'));
                        }
                    }

                }
                /*
                if(isset($attempt->rating)) {
                    if($attempt->draft == 0) {
                        $ratingwithpenalty = $attempt->rating-$poasmodel->get_penalty($attempt->id);
                        if($attempt->ratingdate < $attempt->attemptdate)
                            $row[]=$ratingwithpenalty.' ('.get_string('outdated','poasassignment').') '.html_writer::link($gradeurl,get_string('editgrade','poasassignment'));
                        else
                            $row[]=$ratingwithpenalty.' '.html_writer::link($gradeurl,get_string('editgrade','poasassignment'));
                    }
                    else {
                        //$row[]='-';
                        $row[] = $OUTPUT->action_link($gradeurl, get_string('addgrade','poasassignment'));
                        //$row[]=html_writer::link($gradeurl,get_string('addgrade','poasassignment'));
                    }
                }
                if(!isset($attempt->rating)) {
                    $row[] = $OUTPUT->action_link($gradeurl, get_string('addgrade','poasassignment'));
                }*/
            }
            else {
                $row[]=get_string('noattemptsshort', 'poasassignment');
                $row[]=get_string('noattemptsshort', 'poasassignment');
            }
        }
        else {
                $row[]=get_string('noattemptsshort', 'poasassignment');
                $row[]=get_string('noattemptsshort', 'poasassignment');
        }
        return $row;
    }
}