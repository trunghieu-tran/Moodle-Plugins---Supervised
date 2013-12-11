<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
 
class logs_form extends moodleform {
 
    function definition() {
        $mform =& $this->_form;

        // Gets array of all groups in current course.
        $users[0] = get_string('allusers', 'block_supervised');
        $groupid = $this->_customdata['groupid'];
        $courseid = $this->_customdata['courseid'];
        if($groupid == 0){
            // All groups in course.
            $groups = groups_get_all_groups($courseid);
            foreach ($groups as $group) {
                $cusers = groups_get_members($group->id);
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = "[" . $group->name . "]" . " " . $cuser->lastname . " " . $cuser->firstname;
                }
            }
        }
        else{
            // One group in course.
            if ( $cusers = groups_get_members($groupid) ) {
                foreach ($cusers as $cuser) {
                    $users[$cuser->id] = $cuser->lastname . " " . $cuser->firstname;
                }
            }
        }




        // add group
        $mform->addElement('header', 'general', get_string('filterlogsgroup', 'block_supervised'));
        // add users combobox
        $mform->addElement('select', 'userid', get_string('filterlogsbyuser', 'block_supervised'), $users);

        // hidden elements
        $mform->addElement('hidden', 'sessionid');
        $mform->addElement('hidden', 'courseid');

        $mform->addElement('submit', 'submitbutton', get_string('showlogsbutton', "block_supervised"));
    }
}