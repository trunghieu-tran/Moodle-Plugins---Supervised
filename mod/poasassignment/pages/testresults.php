<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class testresults_page extends abstract_page {
    private $assigneeid;
    private $attemptid;
    private $groupid;
    private $id;

    function __construct() {
        $this->attemptid = optional_param('attemptid', 0, PARAM_INT);
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        $this->groupid = optional_param('grounpid', 0, PARAM_INT);
        $this->id = required_param('id', PARAM_INT);
    }

    function view() {
        global $DB;
        $poasmodel = poasassignment_model::get_instance();
        $datagroups = array();
        $dataassignees = array();
        $dataattempts = array();

        $assignees = $poasmodel->get_assignees_ext($poasmodel->get_poasassignment()->id);
        $datagroups = $this->get_groups($assignees);

        $mform = new attempt_choose_ext_form(null,
            array(
                'groups' => $datagroups,
                'assignees' => $dataassignees,
                'attempts' => $dataattempts,
                'id' => $this->id),
            'get');
        $mform->set_data(array('groupid' => $this->groupid));
        $mform->display();
    }

    function get_groups($assignees) {
        $poasmodel = poasassignment_model::get_instance();
        $userids = array();
        foreach ($assignees as $assignee) {
            $userids[] = $assignee->userid;
        }

        // Divide assignees by groups, create array of used groups
        $groups = $poasmodel->get_users_groups($userids);

        $wogroup = new stdClass();
        $wogroup->name = get_string('wogroup', 'poasassignment');
        $wogroup->id = -2;

        $nogroup = new stdClass();
        $nogroup->name = '-';
        $nogroup->id = 0;

        array_unshift($groups, $wogroup);
        array_unshift($groups, $nogroup);
        foreach ($groups as $group) {
            $datagroups[$group->id] = $group->name;
        }
        return $datagroups;
    }
}
class attempt_choose_ext_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $mform->addElement('select', 'groupid', get_string('group', 'poasassignment'), $instance['groups']);

        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page', 'testresults');
        $mform->setType('page', PARAM_ALPHA);

        $mform->addElement('submit', null, get_string('show'));
    }
}