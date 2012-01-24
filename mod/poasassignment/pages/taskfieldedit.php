<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class taskfieldedit_page extends abstract_page {
    private $fieldid;
    private $field;
    private $mform;
    private $mode;
    function __construct($cm, $poasassignment) {
        $this->fieldid = optional_param('fieldid', 0, PARAM_INT);
        $this->mode = optional_param('mode', '', PARAM_TEXT);        
        $this->cm = $cm;
        $this->poasassignment = $poasassignment;
    }
    function get_cap() {
        return 'mod/poasassignment:managetasksfields';
    }
    function has_satisfying_parameters() {
        // page is available if individual tasks mode is avtive
        $flag = poasassignment_model::get_instance()->has_flag(ACTIVATE_INDIVIDUAL_TASKS);
        if (!$flag) {
            $this->lasterror = 'errorindtaskmodeisdisabled';
            return false;
        }
        // field is available for edidting if exists
        global $DB;
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        $options = array('id' => $this->fieldid, 'poasassignmentid' => $poasassignmentid);
        $fieldexistsininstance = $this->field = $DB->get_record('poasassignment_fields', $options);
        if($this->fieldid != 0 && !$fieldexistsininstance ) {
            $this->lasterror = 'errornonexistentfield';
            return false;
        }
        return true;
    }
    public function pre_view() {
		
		global $PAGE;
		$id = poasassignment_model::get_instance()->get_cm()->id;
		// add navigation nodes
		$tasksfields = new moodle_url('view.php', array('id' => $id,
														'page' => 'tasksfields'));
		$PAGE->navbar->add(get_string('tasksfields','poasassignment'), $tasksfields);
		
		$taskfieldedit = new moodle_url('view.php', array('id' => $id,
														  'page' => 'taskfieldedit',
														  'fieldid' => $this->fieldid));
		$PAGE->navbar->add(get_string('taskfieldedit','poasassignment'), $taskfieldedit);
		
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new taskfieldedit_form(null, array('id' => $model->get_cm()->id,
                                                      'fieldid' => $this->fieldid,
                                                      'poasassignmentid' => $poasassignmentid));
        if ($this->mform->is_cancelled()) {
            // return to taskfields page
            redirect(new moodle_url('view.php',
                                    array('id' => $model->get_cm()->id,
                                          'page' => 'tasksfields')), 
                     null, 
                     0);
        }
        else {
            if ($this->mform->get_data()) {
                $data = $this->mform->get_data();    
                //if ($this->fieldid > 0) {                	
                    //$model->update_task_field($this->fieldid, $data);
                //}
                if ($this->fieldid <= 0) {
                	// Insert field
                    $data = $model->add_task_field($data);
                    // Generate random values for students, who already took the task
                    $model->generate_randoms($data);
                    // Redirect to fields page                    
                    redirect(new moodle_url('view.php',array('id' => $model->get_cm()->id,'page' => 'tasksfields')), null, 0);
                }
            }
        }
    }
    function view() {
        global $DB, $OUTPUT, $USER;
		
        $model = poasassignment_model::get_instance();
        
        if ($data = $this->mform->get_data()) {
	        if ($this->mode == 'confirmedit') {
	        	$this->confirm_update($data);
	        }
        }
        else {        
	        if ($this->fieldid > 0) {
	            $this->mform->set_data($DB->get_record('poasassignment_fields', array('id' => $this->fieldid)));
	            $data = new stdClass();
	            $data->variants = $model->get_field_variants($this->fieldid, 0);
	            $data->id = $model->get_cm()->id;
	            $this->mform->set_data($data);
	        }
	        $this->mform->display();
        }
    }
    public static function display_in_navbar() {
        return false;
    }
    
    /**
     * Show confirm screen with task owners list
     * 
     * @access private
     * @param object $data data from moodleform
     */
    private function confirm_update($data) {
    	global $OUTPUT, $CFG;
    	$model = poasassignment_model::get_instance();
    	$owners = $model->get_instance_task_owners();
    	
    	// Open form
    	echo '<form action="view.php?page=taskedit&id='.$this->cm->id.'" method="post">';
    	
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
    			$table->add_data($this->get_owner($userinfo));
    			echo '<input type="hidden" name="assigneids[]" value="'.$userinfo->id.'"/>';
    		}
    		$table->print_html();
    	}
    	else {
    		print_string('nobodytooktask', 'poasassignment');
    	}
    	
    	// Ask user to confirm delete
    	echo '<br/>';
    	print_string('changefieldconfirmation', 'poasassignment');
    	if (count($owners) > 0) {
    		echo ' <span class="poasassignment-critical">(';
    		print_string('changingfieldwillchangestudentsdata', 'poasassignment');
    		echo ')</span>';
    	}
    	
    	$nobutton = '<input type="submit" name="confirm" value="'.get_string('no').'"/>';
    	$yesbutton = '<input type="submit" name="confirm" value="'.get_string('yes').'"/>';
    	echo '<input type="hidden" name="mode" value="changeconfirmed"/>';
    	echo '<div class="poasassignment-confirmation-buttons">'.$yesbutton.$nobutton.'</div>';
    	echo '</form>';
    }
    
    private function get_owner($userinfo) {
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
    	
    	// Get link to student's task
    	$taskurl = new moodle_url(
	    			'view.php', 
	    			array(
	    					'page' => 'taskview', 
	    					'taskid' => $userinfo->taskid, 
	    					'id' => $model->get_cm()->id
	    					)
	    			);
    	$task = $model->get_task_info($userinfo->taskid);
    	$owner[] = html_writer::link($taskurl, $task->name.$model->help_icon($task->description));
    	
    	$owner[] = '<input type="radio" name="action_'.$userinfo->id.'" value="saveprogress" checked="checked"></input>';
    	$owner[] = '<input type="radio" name="action_'.$userinfo->id.'" value="dropprogress"></input>';
    	
    	return $owner;
    }
}
class taskfieldedit_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        if ($instance['fieldid'] > 0) {
            $mform->addElement('header','taskfieldeditheader',get_string('taskfieldeditheader','poasassignment'));
            $mform->addElement('hidden', 'mode', 'confirmedit');
        }
        else {
            $mform->addElement('header','taskfieldaddheader',get_string('taskfieldaddheader','poasassignment'));
            $mform->addElement('hidden', 'mode', 'confirmadd');
        }
        $mform->setType('mode', PARAM_TEXT);
        
        $mform->addElement('text','name',get_string('taskfieldname','poasassignment'),array('size'=>45));
        $mform->addElement('textarea','description',get_string('taskfielddescription','poasassignment'),'rows="5" cols="50"');
        $mform->addRule('name', null, 'required', null, 'client');        
        $ftypes = array(get_string('char','poasassignment'),
                        get_string('text','poasassignment'),
                        get_string('float','poasassignment'),
                        get_string('int','poasassignment'),
                        get_string('date','poasassignment'),
                        get_string('file','poasassignment'),
                        get_string('list','poasassignment'),
                        get_string('multilist','poasassignment'));
                        //get_string('category', 'poasassignment'));
        $mform->addElement('select','ftype',get_string('ftype','poasassignment'),$ftypes);
        $mform->addElement('checkbox','showintable',get_string('showintable','poasassignment'));       
        
        $mform->addElement('checkbox','secretfield',get_string('secretfield','poasassignment'));
        
        $mform->addElement('checkbox','random',get_string('random','poasassignment'));
        $types = array(STR, TEXT, DATE, FILE, MULTILIST, CATEGORY);
        foreach ($types as $type) {
            $mform->disabledIf('random', 'ftype', 'eq', $type);
        }
        //$mform->disabledIf('random','valuemin','eq','valuemax');
       
        
        $mform->addElement('text','valuemin',get_string('valuemin','poasassignment'),10);
        $mform->setDefault('valuemin', 0);
        
        $mform->addElement('text','valuemax',get_string('valuemax','poasassignment'),10);
        $mform->setDefault('valuemax', 100);
        
        $types = array(STR, TEXT, DATE, FILE, LISTOFELEMENTS, MULTILIST);
        foreach ($types as $type) {
            $mform->disabledIf('valuemin', 'ftype', 'eq', $type);
            $mform->disabledIf('valuemax', 'ftype', 'eq', $type);
        }
        
        $mform->addElement('textarea','variants',get_string('variants','poasassignment'),'rows="10" cols="50"');
        $mform->addHelpButton('variants', 'variants', 'poasassignment');
        $types = array(STR, TEXT, FLOATING, NUMBER, DATE, FILE);
        foreach ($types as $type) {
                $mform->disabledIf('variants', 'ftype', 'eq', $type);
        }
        
        // hidden params
        $mform->addElement('hidden', 'fieldid', $instance['fieldid']);
        $mform->setType('fieldid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'page', 'taskfieldedit');
        $mform->setType('id', PARAM_TEXT);
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['ftype'] == LISTOFELEMENTS || $data['ftype'] == MULTILIST) {
            $tok = strtok($data['variants'], "\n");
            $count = 0;
            while ($tok) {
                $count++;
                $tok = strtok("\n");
            }
            if ($count < 2) {
                $errors['variants'] = get_string('errorvariants', 'poasassignment');
                return $errors;
            }
        }
        if (isset($data['valuemax']) && isset($data['valuemin'])) {
            if ($data['valuemax'] < $data['valuemin']) {
                $errors['valuemax'] = get_string('errormaxislessthenmin', 'poasassignment');
                return $errors;
            }
        }
        return true;
    }
}
