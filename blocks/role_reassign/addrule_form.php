<?php
require_once("../../config.php");
require_once($CFG->libdir.'/formslib.php');
class role_reassign_addrule_form extends moodleform {
    private $rolesstart;
    private $rolesend;
    private $instancesstart;
    private $instancesend;
    private $groupsstart;
    private $groupsend;
    private $roles;
    private $instances;
    private $groups;
    function definition() {
    
        global $CFG;
        global $DB;
        $courseid = optional_param('courseid', 0, PARAM_INT);
        $mform  = & $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'rulename', get_string('tablename', 'block_role_reassign')); // Rule name input field
        // for checkbox labels
        $roles = $DB->get_records('role', null, 'id');
        foreach ($roles as $role) {
            $rolenames[] = $role->name;
            $roleshortnames[] = $role->shortname;
        }
        $roleids = array_keys($roles);
        $this->roles = array_combine($roleshortnames, $roleids);
        $rolelongnames = array_combine($roleshortnames, $rolenames);
        $selectreadyroles = array_combine($roleids, $rolenames);
        $mform->addElement('hidden', 'editnumber', -1);
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->addElement('select', 'destrole', 'Role to switch to:', $selectreadyroles);
        $mform->addElement('select', 'event', get_string('tablestartevent', 'block_role_reassign'), array( ($DB->get_field_select('events_handlers', 'eventname', 'eventname = \'quiz_attempt_started\' AND component = \'block_role_reassign\'')) =>'quiz_attempt_started', 
                                                                           ($DB->get_field_select('events_handlers', 'eventname', 'eventname = \'quiz_attempt_processed\' AND component = \'block_role_reassign\'')) =>'quiz_attempt_processed'));
        $mform->addElement('selectyesno', 'reversable', get_string('tablerestorable', 'block_role_reassign'));
        $mform->addElement('select', 'reverseevent', get_string('tablerestoreevent', 'block_role_reassign'), array( ($DB->get_field_select('events_handlers', 'eventname', 'eventname = \'quiz_attempt_started\' AND component = \'block_role_reassign\'')) =>'quiz_attempt_started', 
                                                                           ($DB->get_field_select('events_handlers', 'eventname', 'eventname = \'quiz_attempt_processed\' AND component = \'block_role_reassign\'')) =>'quiz_attempt_processed'));
        $mform->addElement('static', 'roles_label', get_string('tableaffectedroles', 'block_role_reassign'), '');
        $this->rolesstart = 7;
        $this->rolesend = $this->rolesstart + count($rolenames);
        $index = 0;
        foreach ($rolelongnames as $short  => $long) {
            $mform->addElement('checkbox', $short, null, ' '.$long);
            $index++;
        }
        $mform->addElement('static', 'quizzes_label', get_string('affectedinstances', 'block_role_reassign'), '');
        // list of affected quizzes' chekcboxes
        $instancerecords = $DB->get_records('quiz', null, 'id');
        $this->instancesstart = $this->rolesend + 1;
        $this->instancesend = $this->instancesstart + count($instancerecords);
        $this->instances = array();
        $index = 0;
        foreach ($instancerecords as $record) {
            $instancesstring = '<a href='.$CFG->wwwroot.'/mod/quiz/view.php?q='.$record->id.'/>'.$record->name.'</a>';
            $mform->addElement('checkbox', 'instance'.$record->id, null, ' '.$instancesstring);
            $this->instances['instance'.$record->id] = $record->id;
            $index++;
        }
       
        // affected groups
        $mform->addElement('static', 'groups_label', get_string('tableaffectedgroups', 'block_role_reassign'), '');
        $this->groups = array();
        $this->groupsstart = $this->instancesend + 1;
        $index = 0;
        $grouprecords = groups_get_all_groups($courseid);
        $this->groupsend = count($grouprecords);
        foreach ($grouprecords as $grouprecord) {
            $mform->addElement('checkbox', 'group'.$grouprecord->id, null, ' '.$grouprecord->name);
            $this->groups['group'.$grouprecord->id] = $grouprecord->id;
        }
        $this->add_action_buttons();
    }
    function get_rule_name() {
        return $this->_form->_submitValues['rulename'];
    }
    function get_destrole() {
        return $this->_form->_submitValues['destrole'];
    }
    function get_affected_roles() {
        $roles = array();
        $index = $this->rolesstart;
        foreach ($this->roles as $shortname  => $id) {  // iterate through form checkboxes and roles array simultaneously.
            if (array_key_exists($shortname, $this->_form->_submitValues)) {
                $roles[] = $id;
            }
            $index++;
        }
        return $roles;
    }
    function get_instances() {
        $instances = array();
        foreach ($this->instances as $name  => $id) {
            if (array_key_exists($name, $this->_form->_submitValues)) {
                $instances[] = $id;
            }
        }
        return $instances;
    }
    function get_groups() {
        $groupids = array();
        foreach ($this->groups as $name  => $id) {
            if (array_key_exists($name, $this->_form->_submitValues)) {
                $groupids[] = $id;
            }
        }
        return $groupids;
    }
    function get_event() {
        return $this->_form->_submitValues['event'];
    }
    function get_restorable() {
        return $this->_form->_elements[3]->_values[0];
    }
    function get_restorevent() {
        return $this->_form->_submitValues['reverseevent'];
    }
} 