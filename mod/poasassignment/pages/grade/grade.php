<?php
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class grade_page extends abstract_page{
    private $assigneeid;
    private $assignee;
    function __construct() {
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
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
        global $DB;
        if (!$this->assignee = $DB->get_record('poasassignment_assignee', array('id' => $this->assigneeid))) {
            $this->lasterror = 'errornonexistentassignee';
            return false;
        }
        return true;
    }
    function view() {
        $poasmodel = poasassignment_model::get_instance();
        $cmid = $poasmodel->get_cm()->id;
        $poasassignmentid = $poasmodel->get_poasassignment()->id;
        $mform = new grade_form(null,array('id' => $cmid, 'assigneeid' => $this->assigneeid, 'poasassignmentid' => $poasassignmentid));
        $data = $poasmodel->get_rating_data($this->assigneeid);
        $mform->set_data($data);
        if ($mform->is_cancelled()) {
            redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'submissions')),null,0);
        }
        else {
            if($data = $mform->get_data()) {
                $poasmodel->save_grade($this->assigneeid, $data);
                redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'submissions')),null,0);
            }
        }
        $mform->display();
    }
    
    public static function display_in_navbar() {
        return false;
    }
    
}
class grade_form extends moodleform {

    function definition(){
        global $DB,$OUTPUT;
        $mform =& $this->_form;
        $instance = $this->_customdata;
        $assignee = $DB->get_record('poasassignment_assignee',array('id'=>$instance['assigneeid']));
        $poasmodel = poasassignment_model::get_instance();
        $user = $DB->get_record('user',array('id'=>$assignee->userid));
        $attemptscount = $DB->count_records('poasassignment_attempts',array('assigneeid'=>$instance['assigneeid']));
        $attempt = $DB->get_record('poasassignment_attempts',
                                    array('assigneeid' => $instance['assigneeid'],'attemptnumber' => $attemptscount));
        $lateness = format_time(time() - $attempt->attemptdate);
        $poasassignment = $DB->get_record('poasassignment',array('id'=>$instance['poasassignmentid']));
        $attemptsurl = new moodle_url('view.php',array('page' => 'attempts',
                                                       'id' => $instance['id'],
                                                       'assigneeid' => $instance['assigneeid']));
        $userurl = new moodle_url('/user/profile.php',array('id'=>$user->id));
        if ($poasmodel->has_flag(ACTIVATE_INDIVIDUAL_TASKS)) {
            $taskviewurl = new moodle_url('view.php', array('page' => 'taskview', 
                                                            'id' => $instance['id'], 
                                                            'taskid' => $assignee->taskid));
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
        
        $mform->addElement('hidden', 'page', 'grade');
        $mform->setType('assigneeid', PARAM_TEXT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}
