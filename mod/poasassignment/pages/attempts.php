<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');

class attempts_page extends abstract_page {
    private $assigneeid;

    function __construct() {
        global $DB, $USER;        
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
    }
    
    function has_satisfying_parameters() {
        global $DB,$USER;
        $context = poasassignment_model::get_instance()->get_context();
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        if ($this->assigneeid > 0) {
            if (! $this->assignee = $DB->get_record('poasassignment_assignee', array('id' => $this->assigneeid))) {
                $this->lasterror = 'errornonexistentassignee';
                return false;
            }
        }
        else {
            $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
            $this->assignee = $DB->get_record('poasassignment_assignee', 
                                              array('userid' => $USER->id,
                                                    'poasassignmentid' => $poasassignmentid));
        }
        // Page exists always for teachers
        if (has_capability('mod/poasassignment:grade', $context) || has_capability('mod/poasassignment:finalgrades', $context)) {
            return true;
        }
        // Page exists, if assignee has attempts
        if ($this->assignee && $this->assignee->lastattemptid > 0) {
            // Page content is available if assignee wants to see his own attempts
            // or teacher wants to see them
            if($this->assignee->userid == $USER->id) {
				if (has_capability('mod/poasassignment:viewownsubmission', $context)) {
					return true;
				}
				else {
					$this->lasterror = 'errorviewownsubmissioncap';
					return false;
				}
            }
            else {
                $this->lasterror = 'erroranothersattempts';
                return false;
            }
        }
        else {
            $this->lasterror = 'errorassigneenoattempts';
            return false;
        }
        return true;
    }
    function view_assignee_block() {
        $poasmodel = poasassignment_model::get_instance();
        if (has_capability('mod/poasassignment:grade', $poasmodel->get_context())) {
            $mform = new assignee_choose_form(null, array('id' => $poasmodel->get_cm()->id));
            $mform->display();
        }
    }
    function view() {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $poasassignmentid = $poasmodel->get_poasassignment()->id;
        //$html = '';
        $this->view_assignee_block();
        // teacher has access to the page even if he has no task or attempts
        if(isset($this->assignee->id)) {
            $attempts = array_reverse($DB->get_records('poasassignment_attempts',
                                                       array('assigneeid'=>$this->assignee->id), 
                                                       'attemptnumber'));
            $plugins = $poasmodel->get_plugins();
            $criterions = $DB->get_records('poasassignment_criterions', array('poasassignmentid'=>$poasassignmentid));
            $latestattempt = $DB->get_record('poasassignment_attempts', array('id'=>$this->assignee->lastattemptid));
            $attemptscount = count($attempts);  
            foreach($attempts as $attempt) {
				attempts_page::show_attempt($attempt);
                //echo $OUTPUT->box_start();
                //echo $OUTPUT->heading(get_string('attemptnumber','poasassignment').':'.$attempt->attemptnumber.' ('.userdate($attempt->attemptdate).')');
                
                /*// show attempt's submission
                foreach($plugins as $plugin) {
                    require_once($plugin->path);
                    $poasassignmentplugin = new $plugin->name();
                    echo $poasassignmentplugin->show_assignee_answer($this->assignee->id,$poasassignmentid,1,$attempt->id);
                }
				*/
                // show disablepenalty/enablepenalty button
                if(has_capability('mod/poasassignment:grade',$poasmodel->get_context())) {
                    $cmid = $poasmodel->get_cm()->id;
                    if(isset($attempt->disablepenalty) && $attempt->disablepenalty==1) {
                        echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$cmid.'&action=enablepenalty&attemptid='.$attempt->id), 
                                                                get_string('enablepenalty','poasassignment'));
                    }
                    else {
                        echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$cmid.'&action=disablepenalty&attemptid='.$attempt->id), 
                                                                get_string('disablepenalty','poasassignment'));
                    }
                }
                echo $poasmodel->get_feedback($attempt,$latestattempt,$criterions,$poasmodel->get_context());
                //echo $OUTPUT->box_end();
            }
        }
    }
    public static function use_echo() {
        return false;
    }
	public static function show_attempt($attempt, $showcontent = true) {
		echo '<table class="poasassignment-table" width="100%">';
			
		$values = array(
						get_string('attemptnumber','poasassignment') => $attempt->attemptnumber,
						get_string('attemptdate','poasassignment') => userdate($attempt->attemptdate),
						//get_string('attempt','poasassignment') => '',
						get_string('draft', 'poasassignment') => $attempt->draft == 1 ? get_string('yes') : get_string('no'));
		
		if ($showcontent) {
			$poasmodel = poasassignment_model::get_instance();
			$plugins = $poasmodel->get_plugins();
			$content = '';
			foreach($plugins as $plugin) {
				require_once($plugin->path);
				$poasassignmentplugin = new $plugin->name();
				$content .= $poasassignmentplugin->show_assignee_answer($attempt->assigneeid, $poasmodel->get_poasassignment()->id, 0);
			}
			$values[get_string('attempt','poasassignment')] = $content;
		}
		
		foreach($values as $header => $value) {
			echo '<tr>';
			echo '<td class="header">' . $header . '</td>';
			echo '<td>' . $value . '</td>';
			echo '</tr>';
		}
		
		echo '</table>';
	}
}