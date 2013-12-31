<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
 
class activesession_block_form extends moodleform {
 
    function definition() {
        global $DB, $COURSE;

        $mform =& $this->_form;

        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active'=>true))) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }

        // Gets array of all groups in current course.
        $groups[0] = get_string('allgroups', 'block_supervised');
        if ($cgroups = groups_get_all_groups($COURSE->id)) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }

        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // Add show logs link.
        $sessionid = $this->_customdata['sessionid'];
        $courseid = $this->_customdata['courseid'];
        $logsurl = new moodle_url('/blocks/supervised/logs/view.php', array('sessionid' => $sessionid, 'courseid' => $courseid));
        $mform->addElement('link', 'showlogslink', null, $logsurl->out(false), get_string('showlogs', 'block_supervised'));
        // add classroom
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        $mform->addRule('classroomid', null, 'required', null, 'client');
        // add group combobox
        $mform->addElement('select', 'groupid', get_string('group', 'block_supervised'), $groups);
        $mform->addRule('groupid', null, 'required', null, 'client');
        // add lessontype
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        // add time start
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // add timeend
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));
        // add comment
        if($this->_customdata['needcomment']){
            $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));
        }
        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'timestartraw');     // session timestart (in seconds)
        $mform->setType('timestartraw', PARAM_INT);

        // add submit and cancel buttons
        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'supervised_updatebtn', get_string('updatesession', "block_supervised"));
        $buttonarray[] =& $mform->createElement('cancel', 'supervised_finishbtn', get_string('finishsession', "block_supervised"));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }


    // Form validation
    function validation($data, $files) {
        $errors = array();
        $curtime = time();

        // Session time end must be greater than current time + 1 minute.
        if($data['timestartraw'] + $data['duration']*60 <= $curtime+60){
            $errors["duration"] = get_string("increaseduration", "block_supervised");
        }

        return $errors;
    }
}