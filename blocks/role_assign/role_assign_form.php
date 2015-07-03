<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
require_once("{$CFG->libdir}/formslib.php");
require_once($CFG->libdir . '/pluginlib.php');

/**
 * class, form for add new rule.
 */
class add_rule_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('text', 'rulename', get_string('rulename', 'block_role_assign'));
        $mform->setType('rulename', PARAM_RAW);
        $mform->addRule('rulename', null, 'required', null, 'client');
        $mform->addHelpButton('rulename', 'rulename', 'block_role_assign');

        // Add field type rule, check exist modules.
        $pluginman = plugin_manager::instance();
        $options = array();
        if ($pluginman->get_plugin_info('quiz')) {
            $options['1'] = get_string('typetest', 'block_role_assign');
        }
        if ($pluginman->get_plugin_info('poasassignment')) {
            $options['2'] = get_string('typepoasassignment', 'block_role_assign');
        }
        if ($pluginman->get_plugin_info('block_supervised')) {
            $options['3'] = get_string('typesupervised', 'block_role_assign');
        }
        $select = $mform->addElement('select', 'typetask', get_string('typetask', 'block_role_assign'), $options);
        $select->setSelected('1');
        $mform->addHelpButton('typetask', 'typetask', 'block_role_assign');
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_RAW);
        $this->add_action_buttons(true, get_string("next", 'block_role_assign'));
    }
}
/**
 * class, form for edit new rule.
 */
class add_edit_rule_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('text', 'rulename', get_string('rulename', 'block_role_assign'));
        $mform->setType('rulename', PARAM_RAW);
        $mform->addRule('rulename', null, 'required', null, 'client');
        $mform->addHelpButton('rulename', 'rulename', 'block_role_assign');
        $mform->addElement('static', 'typetask', get_string('typetask', 'block_role_assign'));
        $mform->addHelpButton('typetask', 'typetask', 'block_role_assign');
        global $DB, $COURSE;
        $roles = $DB->get_records('role');
        foreach ($roles as $role) {
            $options[$role->id] = $role->shortname;
        }
        $selectnewrole = $mform->addElement('select', 'newrole', get_string('newrole', 'block_role_assign'), $options);
        $mform->addRule('newrole', null, 'required', null, 'client');
        $mform->addHelpButton('newrole', 'newrole', 'block_role_assign');
        $selectallowrole = $mform->addElement('select', 'allowroles', get_string('allowroles', 'block_role_assign'),
            $options);
        $selectallowrole->setMultiple(true);
        $mform->addRule('allowroles', null, 'required', null, 'client');
        $mform->addHelpButton('allowroles', 'allowroles', 'block_role_assign');
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_RAW);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_RAW);
        $mform->addElement('hidden', 'typetaskid');
        $mform->setType('typetaskid', PARAM_RAW);

    }

    /**
     * add field which contain course elements specified type task
     *
     * @param string $tablename
     * @param string $name
     */
    public function set_tasks_param($tablename, $name) {
        global $DB, $COURSE;
        $mform =& $this->_form;
        if ($name == 'typesupervised') {
            $tasks = $DB->get_records($tablename, array('courseid' => $COURSE->id));
            $optiontasks[0] = get_string('lesson_not_specified', 'block_role_assign');
        } else {
            $tasks = $DB->get_records($tablename, array('course' => $COURSE->id));
        }

        foreach ($tasks as $task) {
            $optiontasks[$task->id] = $task->name;
        }
        $selecttasks = $mform->addElement('select', 'tasks', get_string($name, 'block_role_assign'), $optiontasks);
        $selecttasks->setMultiple(true);
        $mform->addRule('tasks', null, 'required', null, 'client');
        $mform->addHelpButton('tasks', 'tasks', 'block_role_assign');
        $this->add_action_buttons();
    }
}