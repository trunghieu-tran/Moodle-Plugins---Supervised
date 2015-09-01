<?php
require_once(dirname(dirname(__FILE__)) . '/model.php');
class abstract_page {
    //var $cm;
    var $lasterror;
    //function abstract_page($cm) {
    //    $this->cm=$cm;
    //}
    
    function abstract_page() {
    }

    /** Getting page view capability
     * @return capability 
     */
    function get_cap() {
        return 'mod/poasassignment:view';
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
            print_error(
                $this->lasterror,
                'poasassignment',
                new moodle_url('/mod/poasassignment/view.php',
                    array(
                        'id'=>poasassignment_model::get_instance()->get_cm()->id,
                        'page' => 'view')));
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
        return has_capability($this->get_cap(), poasassignment_model::get_instance()->get_context());
    }

    /** Requires capabilities to view, used in has_ability_to_view
     */
    function require_cap() {
        return require_capability($this->get_cap(), poasassignment_model::get_instance()->get_context());
    }
    public function pre_view() {
    }
    function view() {
    }
    
    public static function display_in_navbar() {
        return true;
    }
    
    /** This function is temporary. All pages instaed of using echo 
     *  must use variable to add elements and return this variable
     *  This is connected with redirecting.
     */
    public static function use_echo() {
        return true;
    }
}

class assignee_choose_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        $recs = $DB->get_records('poasassignment_assignee',array('poasassignmentid' => $poasassignmentid));
        foreach ($recs as $rec) {
            $lastattemptid = poasassignment_model::get_instance()->get_last_attempt_id($rec->id);
            if ($lastattemptid == null || $lastattemptid == 0) {
                unset($recs[$rec->id]);
                continue;
            }
            $user = $DB->get_record('user', array('id' => $rec->userid));
            $recs[$rec->id] = fullname($user, true);
        }
        $mform->addElement('select', 'assigneeid', get_string('assignee', 'poasassignment'),$recs);
        $mform->addElement('submit', 'submit', 'go');
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $page = 'attempts';
        if(isset($instance['page'])) {
            $page = $instance['page'];
        }
        $mform->addElement('hidden', 'page', $page);        
        $mform->setType('page', PARAM_TEXT);
    }
}
class attempt_choose_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        $attempts = $DB->get_records('poasassignment_attempts', array('assigneeid' => $instance['assigneeid']),'id DESC');
        foreach ($attempts as $attempt) {
            $attempts[$attempt->id] = get_string('attempt', 'poasassignment') . $attempt->attemptnumber . ':' . userdate($attempt->attemptdate);
        }
        $mform->addElement('select', 'attemptid', get_string('attempt', 'poasassignment'),$attempts);
        $mform->addElement('submit', 'submit', 'go');
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'page', 'graderresults');
        $mform->setType('page', PARAM_TEXT);
    }
}