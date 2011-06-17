<?php
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class grade_page {
    var $lasterror;
    private $assigneeid;
    function abstract_page() {
        $this->assigneeid = optional_param('assigneeid', -1, PARAM_INT);
    }

    /** Getter of page capability
     * @return capability 
     */
    function get_cap() {
        return 'mod/poasassignment:grade';
    }

    /** Checks module settings that prohibit viewing this page, used in has_ability_to_view
     * @return true if neither setting prohibits
     */
    function has_satisfying_parameters() {
        return true;
    }
    
    /** Requires settings and capabilities to view
     */
    function require_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            print_error($this->lasterror,'poasassignment');
        $this->require_cap();
    }

    /** Checks settings and capabilities to view
     * @return true if nothing prohibits
     */
    function has_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            return false;
        return $this->has_cap();
    }
    
    /** Checks capabilities to view, used in has_ability_to_view
     * @return true if has capability to view
     */
    function has_cap() {
        return has_capability($this->get_cap(),poasassignment_model::get_instance()->get_context());
    }

    /** Requires capabilities to view, used in has_ability_to_view
     */
    function require_cap() {
        return require_capability($this->get_cap(),poasassignment_model::get_instance()->get_context());
    }
    function view() {
        $poasmodel = poasassignment_model::get_instance();
        $cmid = $poasmodel->get_cm()->id;
        $poasassignmentid = $poasmodel->get_poasassignment()->id;
        $mform = new grade_form2(null,array('id'=>$cmid,'assigneeid'=>$this->assigneeid,'poasassignmentid'=>$poasassignmentid));
        $data = $poasmodel->get_rating_data($this->assigneeid);
        $mform->set_data($data);
        if ($mform->is_cancelled()) {
            redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$cm->id,'page'=>'submissions')),null,0);
        }
        else {
            if($data = $mform->get_data()) {
                $poasmodel->save_grade($this->assigneeid, $data);
                redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$cm->id,'page'=>'submissions')),null,0);
            }
        }
    }
    
    public static function display_in_navbar() {
        return false;
    }
    
}
class grade_form2 extends moodleform {

    function definition(){
        global $DB,$OUTPUT;
        $mform =& $this->_form;
        $instance = $this->_customdata;
        $assignee=$DB->get_record('poasassignment_assignee',array('id'=>$instance['assigneeid']));
        $poasmodel= poasassignment_model::get_instance();
        $user=$DB->get_record('user',array('id'=>$assignee->userid));
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$instance['assigneeid']));
        $attempt=$DB->get_record('poasassignment_attempts',
                                    array('assigneeid'=>$instance['assigneeid'],'attemptnumber'=>$attemptscount));
        $lateness=format_time(time()-$attempt->attemptdate);
        $poasassignment = $DB->get_record('poasassignment',array('id'=>$instance['poasassignmentid']));
        $attemptsurl = new moodle_url('/mod/poasassignment/attempts.php',array('id'=>$instance['id'],'assigneeid'=>$instance['assigneeid']));
        $userurl = new moodle_url('/user/profile.php',array('id'=>$user->id));
        if($poasassignment->flags && ACTIVATE_INDIVIDUAL_TASKS) {
            $taskviewurl = new moodle_url('/mod/poasassignment/pages/tasks/taskview.php', array('id'=>$instance['id'], 'taskid' => $assignee->taskid));
        }
        else {
            $taskviewurl = '';
        }
        $mform->addElement('static', 'picture', $OUTPUT->user_picture($user),
                                                html_writer::link($userurl,fullname($user, true)) . '<br>'.
                                                userdate($attempt->attemptdate) . '<br/>' .
                                                $lateness.' '.get_string('ago','poasassignment').'<br>'.
                                                html_writer::link($attemptsurl,get_string('studentattempts','poasassignment') . '<br>'.
                                                html_writer::link($taskviewurl,get_string('stundetstask','poasassignment'))));
        
        $mform->addElement('header','studentsubmission',get_string('studentsubmission','poasassignment'));
        $plugins = $poasmodel->get_plugins();
        foreach($plugins as $plugin) {
            require_once(dirname(dirname(dirname(__FILE__))) . '\\'.$plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $mform->addElement('static',null,null,$poasassignmentplugin->show_assignee_answer($instance['assigneeid'],$instance['poasassignmentid']));
        }
        $mform->addElement('header','gradeeditheader',get_string('gradeeditheader','poasassignment'));
        $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$instance['poasassignmentid']));
        for($i=0;$i<101;$i++) 
            $opt[]=$i.'/100';
        $weightsum = 0;
        foreach($criterions as $criterion) 
            $weightsum += $criterion->weight;
        
        $context = get_context_instance(CONTEXT_MODULE, $instance['id']);
        
        $options->area    = 'poasassignment_comment';
        $options->pluginname = 'poasassignment';
        $options->component = 'mod_poasassignment';
        $options->context = $context;
        $options->showcount = true;
        
        foreach($criterions as $criterion) {
            $mform->addElement('html', $OUTPUT->box_start());
            // show grading element
            if($attempt->draft == 0 || 
               has_capability('mod/poasassignment:manageanything', $context)) {
                $mform->addElement('select',
                                   'criterion' . $criterion->id,
                                   $criterion->name . ' ' . $poasmodel->help_icon($criterion->description),
                                   $opt);
            }
            // show normalized criterion weight
            $mform->addElement('static',
                               'criterion' . $criterion->id . 'weight',
                               get_string('normalizedcriterionweight', 'poasassignment'),
                               round($criterion->weight / $weightsum, 2));
            
            // show feedback
            $ratingvalue = $DB->get_record('poasassignment_rating_values', array('criterionid' => $criterion->id,
                                                                                 'attemptid' => $attempt->id));
            if($ratingvalue) {
                $options->itemid = $ratingvalue->id;
                $comment= new comment($options);
                $mform->addElement('static', 
                                   'criterion' . $criterion->id . 'comment',
                                   get_string('comment', 'poasassignment'),
                                   $comment->output(true));
            }
            else
                $mform->addElement('htmleditor','criterion'.$criterion->id.'comment',get_string('comment','poasassignment'));   
            
            $mform->addElement('html',$OUTPUT->box_end());
        }
        if($attempt->draft == 0 || has_capability('mod/poasassignment:manageanything',$context)) {
            $mform->addElement('checkbox', 'final', get_string('finalgrade','poasassignment'));
        }
        
        
        
        $mform->addElement('static','penalty',get_string('penalty','poasassignment'),$poasmodel->get_penalty($attempt->id));
        $mform->addElement('filemanager', 'commentfiles_filemanager', get_string('commentfiles','poasassignment'));
               
        
        // hidden params
        $mform->addElement('hidden', 'weightsum', $weightsum);
        $mform->setType('weightsum', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'assigneeid', $instance['assigneeid']);
        $mform->setType('assigneeid', PARAM_INT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    // function validation($data, $files) {
        // $errors = parent::validation($data, $files);
        // global $DB;
        // $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid']));
        // foreach($fields as $field) {
            // if(!$field->random &&($field->ftype==FLOATING || $field->ftype==NUMBER)) {
                // if(!($field->valuemin==0 && $field->valuemax==0 )) {
                    // if($data['field'.$field->id]>$field->valuemax || $data['field'.$field->id]<$field->valuemin) {
                    // $errors['field'.$field->id]=get_string('valuemustbe','poasassignment').' '.
                                                // get_string('morethen','poasassignment').' '.
                                                // $field->valuemin.' '.
                                                // get_string('and','poasassignment').' '.
                                                // get_string('lessthen','poasassignment').' '.
                                                // $field->valuemax;
                    // return $errors;
                    // }
                // }
            // }
        // }
       
        // return true;
    // }
}
