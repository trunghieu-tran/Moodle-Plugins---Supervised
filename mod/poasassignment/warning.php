<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('model.php');
require_once(dirname(__FILE__).'/lib.php');
$id     = optional_param('id', 0, PARAM_INT); // course_module ID
$action = optional_param('action', null, PARAM_TEXT);

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
$poasmodel = poasassignment_model::get_instance();
$poasmodel->cash_instance($poasassignment->id);
require_login($course, true, $cm);
    
global $OUTPUT,$DB,$PAGE, $CFG;
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
$context = context_module::instance($cm->id);
switch ($action) {
    case 'disablepenalty':
        $attemptid=optional_param('attemptid', -1, PARAM_INT);
        if(!isset($attemptid) || $attemptid < 1)
            print_error('invalidattemptid', 'poasassignment');
        
        if(has_capability('mod/poasassignment:grade', $context)) {
            $attempt = $DB->get_record('poasassignment_attempts', array('id' => $attemptid));
            $attempt->disablepenalty = 1;
            $DB->update_record('poasassignment_attempts', $attempt);
            redirect(new moodle_url('view.php',array('id' => $cm->id,'page' => 'attempts', 'assigneeid' => $attempt->assigneeid)));
        }        
        break;
    case 'enablepenalty':
        $attemptid=optional_param('attemptid',-1,PARAM_INT);
        if(!isset($attemptid) ||$attemptid<1)
            print_error('invalidattemptid','poasassignment');
        if(has_capability('mod/poasassignment:grade',$context)) {
            $attempt=$DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
            $attempt->disablepenalty=0;
            $DB->update_record('poasassignment_attempts',$attempt);
            redirect(new moodle_url('view.php',array('id'=>$cm->id,'page' => 'attempts', 'assigneeid'=>$attempt->assigneeid)));
        }        
        break;
    case 'canceltask':
        $assigneeid = optional_param('assigneeid', -1, PARAM_INT);
        if(!isset($assigneeid) ||$assigneeid<1)
            print_error('invalidassigneeid','poasassignment');
        if($poasmodel->can_cancel_task($assigneeid, $context)) {
            $poasmodel->cancel_task($assigneeid);
            $attempts=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            foreach($attempts as $attempt) {
                $DB->delete_records('poasassignment_submissions',array('attemptid'=>$attempt->id));
                $DB->delete_records('poasassignment_rating_values',array('attemptid'=>$attempt->id));
            }
            $DB->delete_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            if (has_capability('mod/poasassignment:managetasks',$context)) {
                redirect(new moodle_url('view.php',array('id'=>$cm->id,'page'=>'submissions')),null,0);
            }
            else {
                redirect(new moodle_url('view.php',array('id'=>$cm->id,'page'=>'view')),null,0);
            }
        }
    case 'deletefield':
        require_capability('mod/poasassignment:managetasksfields',$context);
        $fieldid = required_param('fieldid', PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&fieldid='.$fieldid.'&action=deletefield');
        if ($fieldid < 1) {
            print_error('invalidfieldid','poasassignment');
        }
        $field = $DB->get_record('poasassignment_fields',array('id' => $fieldid));
        if(!$field)
            print_error('invalidfieldid','poasassignment');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        
        $model = poasassignment_model::get_instance();
        $owners = $model->get_instance_task_owners();        
        
        // Open form
        echo '<form action="warning.php?action=deletefieldconfirmed&id=' . $cm->id . '" method="post">';
        
        echo '<input type="hidden" name="ownerscount" value="'.count($owners).'"/>';
        if (count($owners) > 0) {
            // Show owners table
            $usersinfo = $model->get_users_info($owners);
            print_string('instanceowners', 'poasassignment');
            require_once ('poasassignment_view.php');
            $extcolumns = array(
                    'task',
                    'saveprogress',
                    'dropprogress'
            );
            $extheaders = array(
                    get_string('task', 'poasassignment'),

                    get_string('saveprogress', 'poasassignment').' '.
                    $OUTPUT->help_icon('saveprogress', 'poasassignment'),

                    get_string('dropprogress', 'poasassignment').' '.
                    $OUTPUT->help_icon('dropprogress', 'poasassignment')
            );
            $table = poasassignment_view::get_instance()->prepare_flexible_table_owners($extcolumns, $extheaders);
            foreach ($usersinfo as $userinfo) {
                $row = get_owner($userinfo);
                // Get link to student's task
                $taskurl = new moodle_url(
                            'view.php',
                            array(
                                    'page' => 'taskview',
                                    'taskid' => $userinfo->taskid,
                                    'id' => $id
                                    )
                            );
                $task = $model->get_task_info($userinfo->taskid);
                $row[] = html_writer::link($taskurl, $task->name.$model->help_icon($task->description));

                $row[] = '<input type="radio" name="action_'.$userinfo->id.'" value="saveprogress" checked="checked"></input>';
                $row[] = '<input type="radio" name="action_'.$userinfo->id.'" value="dropprogress"></input>';
                $table->add_data($row);
                echo '<input type="hidden" name="assigneids[]" value="'.$userinfo->id.'"/>';
            }
            $table->print_html();
        }
        else {
            print_string('nobodytooktask', 'poasassignment');
        }
        
        // Ask user to confirm delete
        echo '<br/>';
        print_string('deletefieldconfirmation', 'poasassignment');
        if (count($owners) > 0) {
            echo ' <span class="poasassignment-critical">(';
            print_string('deletingfieldwillchangestudentsdata', 'poasassignment');
            echo ')</span>';
        }
        
        $nobutton = '<input type="submit" name="confirm" value="'.get_string('no').'"/>';
        $yesbutton = '<input type="submit" name="confirm" value="'.get_string('yes').'"/>';
        echo '<input type="hidden" name="mode" value="deleteconfirmed"/>';
        echo '<input type="hidden" name="fieldid" value="'.$fieldid.'"/>';
        echo '<div class="poasassignment-confirmation-buttons">'.$yesbutton.$nobutton.'</div>';
        echo '</form>';
        echo $OUTPUT->footer();    
        break;
    case 'deletefieldconfirmed':
        require_capability('mod/poasassignment:managetasksfields',$context);
        $confirm = required_param('confirm', PARAM_TEXT);
        if ($confirm == get_string('no')) {
            redirect(
                    new moodle_url(
                            '/mod/poasassignment/view.php',
                            array(
                                    'id'=>$cm->id,
                                    'page'=>'tasksfields'
                                    )
                            )
                    );
        }
        else {
            $model = poasassignment_model::get_instance();
            $fieldid = required_param('fieldid', PARAM_INT);
            if($fieldid < 1)
                print_error('invalidfieldid','poasassignment');

            $model->delete_field($fieldid);

            if (required_param('ownerscount', PARAM_INT) > 0) {
                // $_POST['assigneids'] contains array of owners ids
                $assigneeids = $_POST['assigneids'];
                foreach ($assigneeids as $assigneeid) {
                    if (required_param('action_'.$assigneeid, PARAM_ALPHANUMEXT) == 'dropprogress') {
                        // Drop progress - attempts and grades
                        $model->drop_assignee_progress($assigneeid);
                    }
                }
            }
        }
        redirect(new moodle_url('view.php',array('id' => $cm->id, 'page'=>'tasksfields')));
        break;
    case 'taketask':
        $taskid = optional_param('taskid', -1, PARAM_INT);
        $userid = optional_param('userid', -1, PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&taskid='.$taskid.'&action=taketask&userid='.$userid);

        // Only task manager can provide task to another user
        if ($USER->id != $userid) {
            require_capability('mod/poasassignment:managetasks', $context);
        }

        if ($USER->id == $userid && $error = $poasmodel->check_dates()) {
            print_error($error, 'poasassignment');
        }

        if(!isset($taskid) || $taskid<1) {
            print_error('invalidtaskid','poasassignment');
        }

        if(!isset($userid) || $userid<1) {
            print_error('invaliduserid','poasassignment');
        }
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        echo $OUTPUT->box_start();
        global $USER;
        if ($USER->id == $userid) {
            echo get_string('taketaskconfirmation','poasassignment');
        }
        else {
            echo get_string('providetaskconfirmation','poasassignment');
        }
        echo $OUTPUT->box_end();
        echo '<div class="poasassignment-confirmation-buttons">';
        echo $OUTPUT->single_button(new moodle_url('warning.php',
                                array('id'=>$id,'taskid'=>$taskid,'userid'=>$userid,'action'=>'taketaskconfirmed')), 
                                get_string('yes'),
                                'post');
        echo $OUTPUT->single_button(new moodle_url('view.php',array('page'=> 'taskview', 'id'=>$id, 'taskid'=>$taskid)), get_string('no'),'get');
        echo '</div>';
        echo $OUTPUT->footer();    
        break;
    case 'taketaskconfirmed':
        if (isset($_POST['taskid']))
            $taskid = $_POST['taskid'];
        
        //$taskid = optional_param('taskid', -1, PARAM_INT);
        $userid = optional_param('userid', -1, PARAM_INT);
        $PAGE->set_url(new moodle_url('/mod/poasassignment/warning.php',array('id'=>$id,'taskid'=>$taskid,'userid'=>$userid,'action'=>'taketaskconfirmed')));

        if(!isset($taskid) || $taskid<1)
            print_error('invalidtaskid','poasassignment');
        if(!isset($userid) || $userid<1)
            print_error('invaliduserid','poasassignment');
        
        $assignee = $DB->get_record('poasassignment_assignee',array(
            'userid'=>$userid,
            'poasassignmentid'=>$poasassignment->id,
            'cancelled' => 0));
        if($assignee && $assignee->taskid > 0) {
        //if($DB->record_exists('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$poasassignment->id)))
            print_error('alreadyhavetask','poasassignment');
        }
        
        
        $poasmodel->bind_task_to_assignee($userid,$taskid);
        
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'page'=>'view')),null,0);
        break;
        
    case 'deletetask':
        require_capability('mod/poasassignment:managetasks', $context);
        // Get task id
        $taskid = required_param('taskid', PARAM_INT);
        // Breadcrumbs
        $PAGE->set_url(new moodle_url('/mod/poasassignment/warning.php',array('id'=>$id,'taskid'=>$taskid,'action'=>'deletetask')));
        $url = new moodle_url('view.php', array('id' => $id, 'page' => 'tasks'));
        $PAGE->navbar->add(get_string('tasks','poasassignment'), $url);

        // Headers
        echo $OUTPUT->header();
        $task = $DB->get_record('poasassignment_tasks', array('id' => $taskid), 'name');

        echo $OUTPUT->heading(
            $poasassignment->name.
            ' : '.
            get_string('deletingtask','poasassignment').
            ' "'.
            $task->name.
            '" (id = '.
            $taskid.
            ')'
        );



        // Get owners of the task
        if ($taskid > 0) {
            $owners = $poasmodel->get_task_owners($taskid);
        }
        else {
            print_error('invalidtaskid','poasassignment');
        }

        // If there are students, that own this task, show them
        if (count($owners) > 0) {
            require_once ('poasassignment_view.php');
            $table = poasassignment_view::get_instance()->prepare_flexible_table_owners();
            $usersinfo = $poasmodel->get_users_info($owners);
            print_string('ownersofthetask', 'poasassignment');
            foreach ($usersinfo as $userinfo) {
                $table->add_data(get_owner($userinfo));
            }
            $table->print_html();
        }
        else {
            print_string('nooneownsthetask', 'poasassignment');
            echo '<br/><br/>';
        }

        // Ask user to confirm delete
        print_string('deletetaskconfirmation', 'poasassignment');
        if (count($owners) > 0) {
            echo ' <span class="poasassignment-critical">(';
            print_string('deletingtaskwillchangestudentsdata', 'poasassignment');
            echo ')</span>';
        }
        $yesbutton =  $OUTPUT->single_button(
                            new moodle_url(
                                'warning.php',
                                array(    'id' => $id,
                                        'taskid' => $taskid,
                                        'action' => 'deletetaskconfirmed'
                                )
                            ),
                            get_string('yes'),
                            'post'
                        );
        $nobutton =  $OUTPUT->single_button(
                        new moodle_url(
                                'view.php',
                                array(
                                    'id' => $id,
                                    'page' => 'tasks')
                                ),
                        get_string('no'),
                        'get'
                    );
        echo '<div class="poasassignment-confirmation-buttons">'.$yesbutton.$nobutton.'</div>';
        echo $OUTPUT->footer();
        break;

    case 'deletetaskconfirmed':
        // User confirmed task delete action

        require_capability('mod/poasassignment:managetasks', $context);
        //Get task id
        if (isset($_POST['taskid'])) {
            $taskid = $_POST['taskid'];
        }
        $poasmodel->delete_task($taskid);
        redirect(new moodle_url('view.php',array('id'=>$cm->id,'page'=>'tasks')));
        break;
}


function get_owner($userinfo) {
    $model = poasassignment_model::get_instance();
    $owner = array();

    // Get student username and profile link
    $userurl = new moodle_url('/user/profile.php', array('id' => $userinfo->userid));
    $owner[] = html_writer::link($userurl, fullname($userinfo->userinfo, true));

    // TODO Get student's groups
    $owner[] = '?';


    // Get information about assignee's attempts and grades
    if ($attempt = $model->get_last_attempt($userinfo->id)) {
        $owner[] = get_string('hasattempts', 'poasassignment');

        // If assignee has an attempt(s), show information about his grade
        if ($attempt->rating != null) {
            // Show actual grade with penalty
            $owner[] =
            get_string('hasgrade', 'poasassignment').
            ' ('.
            $model->show_rating_methematics($attempt->rating, $model->get_penalty($attempt->id)).
            ')';
        }
        else {
            // Looks like assignee has no grade or outdated grade
            if ($lastgraded = $model->get_last_graded_attempt($userinfo->id)) {
                $owner[] =
                get_string('hasoutdatedgrade', 'poasassignment').
                ' ('.
                $model->show_rating_methematics($lastgraded->rating, $model->get_penalty($lastgraded->id)).
                ')';
            }
            else {
                // There is no graded attempts, so show 'No grade'
                $owner[] = get_string('nograde', 'poasassignment');
            }
        }
    }
    else {
        // No attepts => no grade
        $owner[] = get_string('hasnoattempts', 'poasassignment');
        $owner[] = get_string('nograde', 'poasassignment');
    }

    return $owner;
}