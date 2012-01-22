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
    
global $OUTPUT,$DB,$PAGE;
$PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
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
        //if(has_capability('mod/poasassignment:managetasks',$context)) {
            $poasmodel->cancel_task($assigneeid);
            //$DB->delete_records('poasassignment_assignee',array('id'=>$assigneeid));
            $attempts=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            foreach($attempts as $attempt) {
                $DB->delete_records('poasassignment_submissions',array('attemptid'=>$attempt->id));
                $DB->delete_records('poasassignment_rating_values',array('attemptid'=>$attempt->id));
            }
            $DB->delete_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
            redirect(new moodle_url('view.php',array('id'=>$cm->id,'page'=>'view')),null,0);
        }
    case 'deletefield':
        require_capability('mod/poasassignment:managetasksfields',$context);
        $fieldid=optional_param('fieldid',-1,PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&fieldid='.$fieldid.'&action=deletefield');
        if(!isset($fieldid) ||$fieldid<1)
            print_error('invalidfieldid','poasassignment');
        $field=$DB->get_record('poasassignment_fields',array('id'=>$fieldid));
        if(!$field)
            print_error('invalidfieldid','poasassignment');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        echo $OUTPUT->box_start();
        echo get_string('deletefieldconfirmation','poasassignment');
        echo '<br>- ' . $field->name;
        echo $OUTPUT->box_end();
        echo '<div align="center" class="buttons">';
        $yesbutton =  $OUTPUT->single_button(new moodle_url('warning.php',
                                    array('id'=>$id,'fieldid'=>$fieldid,'action'=>'deletefieldconfirmed')), 
                                    get_string('yes'),
                                    'post');                
        $nobutton =  $OUTPUT->single_button(new moodle_url('view.php',array('id'=>$id,'page'=>'tasksfields')), get_string('no'),'get');
        echo '</div>';
		echo '<table align="center"><tr><td>'.$yesbutton.'</td><td>'.$nobutton.'</td></tr></table>';
        echo $OUTPUT->footer();    
        break;
    case 'deletefieldconfirmed':
    	require_capability('mod/poasassignment:managetasksfields',$context);
        if (isset($_POST['fieldid']))
            $fieldid = $_POST['fieldid'];
        if(!isset($fieldid) ||$fieldid<1)
            print_error('invalidfieldid','poasassignment');
        $field=$DB->get_record('poasassignment_fields',array('id'=>$fieldid));
        if(!$field)
            print_error('invalidfieldid','poasassignment');
        $poasmodel=poasassignment_model::get_instance($poasassignment);
        $poasmodel->delete_field($fieldid);
        
        redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$cm->id,'page'=>'tasksfields')),null,0);
        break;
    case 'taketask':
        $taskid = optional_param('taskid', -1, PARAM_INT);
        $userid = optional_param('userid', -1, PARAM_INT);
        $PAGE->set_url('/mod/poasassignment/warning.php?id='.$cm->id.'&taskid='.$taskid.'&action=taketask&userid='.$userid);

        if(!isset($taskid) || $taskid<1)
            print_error('invalidtaskid','poasassignment');
        if(!isset($userid) || $userid<1)
            print_error('invaliduserid','poasassignment');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($poasassignment->name);
        echo $OUTPUT->box_start();
        echo get_string('taketaskconfirmation','poasassignment');
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
        
        $assignee = $DB->get_record('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$poasassignment->id));
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
    	if (isset($_GET['taskid'])) {
    		$taskid = $_GET['taskid'];
    	}
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
				    			array(	'id' => $id,
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
			$this->show_rating_methematics($attempt->rating, $model->get_penalty($attempt->id)).
			')';
		}
		else {
			// Looks like assignee has no grade or outdated grade
			if ($lastgraded = $model->get_last_graded_attempt($userinfo->id)) {
				$owner[] =
				get_string('hasoutdatedgrade', 'poasassignment').
				' ('.
				$this->show_rating_methematics($lastgraded->rating, $model->get_penalty($lastgraded->id)).
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