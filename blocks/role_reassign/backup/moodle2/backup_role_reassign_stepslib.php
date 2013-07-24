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

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that wll be used by the backup_supervised_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
echo 'infile<br>';
class backup_role_reassign_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;
        echo 'mystructure';
        // To know if we are including userinfo
        //$userinfo = $this->get_setting_value('userinfo');
 
        $block = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));
        $role_reassign = new backup_nested_element('role_reassign', array('id'), null);
        
        $rules = new backup_nested_element('rules');
        $rule = new backup_nested_element('rule', array('id'), array(
            'destroleid', 'restorable', 'eventname', 'restoreeventname', 'name'));
            
        $sourceroles = new backup_nested_element('sourceroles');
        $sourcerole = new backup_nested_element('sourcerole', array('id'), array(
            'ruleid', 'roleid'));
        
        $groups = new backup_nested_element('groups');
        $group = new backup_nested_element('group', array('id'), array(
            'ruleid', 'groupid'));
            
        $instances = new backup_nested_element('instances');
        $instance = new backup_nested_element('instance', array('id'), array(
            'ruleid', 'instanceid'));
        
        $reassignees = new backup_nested_element('affected_users');
        $reassignee = new backup_nested_element('affected_user', array('id'), array(
            'instanceid', 'attemptid', 'userid', 'roleid', 'eventname'));
            
        // Build the tree
        $role_reassign->add_child($rules);
        $rules->add_child($rule);
        
        $role_reassign->add_child($sourceroles);
        $sourceroles->add_child($sourcerole);
        
        $role_reassign->add_child($groups);
        $groups->add_child($group);
        
        $role_reassign->add_child($instances);
        $instances->add_child($instance);
        
        $role_reassign->add_child($reassignees);
        $reassignees->add_child($reassignee);
        
        
        
        // Define sources
        $role_reassign->set_source_array(array((object)array('id' => $this->task->get_blockid())));
        $rule->set_source_table('role_reassign_rules', array());
        
       // $rule->annotate_ids('role', 'sourceroleid');
        //$rule->annotate_ids('
        $instance->set_source_table('role_reassign_instances',array());
        $group->set_source_table('role_reassign_groups',array());
        $sourcerole->set_source_table('role_reassign_source_roles',array()); 
        // Define id annotations
        // Define file annotations (none)
 
        // Return the root element (choice), wrapped into standard activity structure
        return $this->prepare_block_structure($role_reassign);
    }
}