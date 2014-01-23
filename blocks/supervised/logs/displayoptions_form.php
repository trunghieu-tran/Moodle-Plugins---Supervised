<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class displayoptions_logs_form
 *
 * Logs display options form (logs number per page)
 */
class displayoptions_logs_form extends moodleform {
 
    function definition() {
        global $DB;
        $mform =& $this->_form;

        // Gets array of all groups in current course.
        $teacher = $DB->get_record('user', array('id'=>$this->_customdata['teacherid']));
        $users[0] = get_string('allusers', 'block_supervised');
        $users[$teacher->id] = fullname($teacher);

        $groupid = $this->_customdata['groupid'];
        $courseid = $this->_customdata['courseid'];
        if($groupid == 0){
            // All groups in course.
            $groups = groups_get_all_groups($courseid);
            foreach ($groups as $group) {
                $cusers = groups_get_members($group->id);
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = "[" . $group->name . "]" . " " . fullname($cuser);
                }
            }
        }
        else{
            // One group in course.
            if ( $cusers = groups_get_members($groupid) ) {
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = fullname($cuser);
                }
            }
        }


        $mform->addElement('header', 'sessionsoptionsview', get_string('reportdisplayoptions', 'quiz'));
        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);
        $mform->addElement('select', 'userid', get_string('filterlogsbyuser', 'block_supervised'), $users);

        // hidden elements
        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('submit', 'submitbutton', get_string('showlogsbutton', "block_supervised"));
    }

    // Form validation
    function validation($data, $files) {
        $errors = array();

        // Page size must be greater than zero.
        if($data["pagesize"] <= 0){
            $errors["pagesize"] = get_string("pagesizevalidationerror", "block_supervised");
        }

        return $errors;
    }
}