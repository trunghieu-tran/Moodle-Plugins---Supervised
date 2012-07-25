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
class restore_role_reassign_block_structure_step extends restore_structure_step {

    protected function define_structure() {
        
        echo 'mystructure';
        // To know if we are including userinfo
        //$userinfo = $this->get_setting_value('userinfo');
 
        $paths = array();
        
        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('role_reassign', '/block/role_reassign');
        $paths[] = new restore_path_element('rule', '/block/role_reassign/rules/rule');
        $paths[] = new restore_path_element('sourcerole', '/block/role_reassign/sourceroles/sourcerole');
        $paths[] = new restore_path_element('group', '/block/role_reassign/groups/group');
        $paths[] = new restore_path_element('instance', '/block/role_reassign/instances/instance');
        $paths[] = new restore_path_element('affected_user', '/block/role_reassign/affected_users/affected_user');
 
        return $paths;
    }
    
    public function process_block($data) {
        echo 'processing';
        global $DB;
        $data = (object)$data;
        $rulesarr = array();
        $sourcerolesarr = array();
        $groupsarr = array();
        $instancesarr = array();
        $affected_usersarr = array();
        
        if (isset($data->role_reassign['rules']['rule'])) {
            foreach ($data->role_reassign['rules']['rule'] as $rule) {
                $rule = (object)$rule;
                $params = array('destroleid' => $rule->destroleid,
                                'restorable' => $rule->restorable,
                                'eventname'  => $rule->eventname,
                                'restoreeventname' => $rule->restoreeventname,
                                'name' => $rule->name);
                $ruleid = $DB->get_field_select('role_reassign_rules', 'id', 'destroleid=:destroleid and restorable=:restorable and eventname=:eventname and restoreeventname=:restoreeventname and name=:name', $params);
                if ($ruleid != '') {
                    $rulesarr[] = $ruleid;
                } else {
                    $ruleid = $DB->insert_record('role_reassign_rules', $rule);
                    $rulesarr[] = $ruleid;
                }
            }
        }
        
        if (isset($data->role_reassign['sourceroles']['sourcerole'])) {
            foreach ($data->role_reassign['sourceroles']['sourcerole'] as $sourcerole) {
                $sourcerole = (object)$sourcerole;
                $params = array('ruleid' => $sourcerole->ruleid,
                                'roleid' => $sourcerole->roleid);
                $sourceroleid = $DB->get_field_select('role_reassign_source_roles', 'id', 'ruleid=:ruleid and roleid=:roleid', $params);
                if ($sourceroleid != '') {
                    $sourcerolesarr[] = $sourceroleid;
                } else {
                    $sourceroleid = $DB->insert_record('role_reassign_source_roles', $sourcerole);
                    $sourcerolesarr[] = $sourceroleid;
                }
            }
        }
        
        if (isset($data->role_reassign['groups']['group'])) {
            foreach ($data->role_reassign['groups']['group'] as $group) {
                $group = (object)$group;
                $params = array('ruleid' => $group->ruleid,
                                'groupid' => $group->groupid);
                $groupid = $DB->get_field_select('role_reassign_groups', 'id', 'ruleid=:ruleid and groupid=:groupid', $params);
                if ($groupid != '') {
                    $groupsarr[] = $groupid;
                } else {
                    $groupid = $DB->insert_record('role_reassign_groups', $group);
                    $groupsarr[] = $groupid;
                }
            }
        }
        
        if (isset($data->role_reassign['instances']['instance'])) {
            foreach ($data->role_reassign['instances']['instance'] as $instance) {
                $instance = (object)$instance;
                $params = array('ruleid' => $instance->ruleid,
                                'instanceid' => $instance->instanceid);
                $instanceid = $DB->get_field_select('role_reassign_instances', 'id', 'ruleid=:ruleid and instanceid=:instanceid', $params);
                if ($instanceid != '') {
                    $instancesarr[] = $instanceid;
                } else {
                    $instanceid = $DB->insert_record('role_reassign_instances', $instance);
                    $instancesarr[] = $instanceid;
                }
            }
        }
        
        if (isset($data->role_reassign['affected_users']['affected_user'])) {
            foreach ($data->role_reassign['affected_users']['affected_user'] as $affected_user) {
                $affected_user = (object)$affected_user;
                $params = array('instanceid' => $affected_user->instanceid,
                                'attemptid' => $affected_user->attemptid,
                                'userid' => $affected_user->userid,
                                'roleid' => $affected_user->roleid,
                                'eventname' => $affected_user->eventname);
                                
                $affected_userid = $DB->get_field_select('role_reassign_user', 'id', 'instanceid=:instanceid and attemptid=:attemptid and userid=:userid and roleid=:roleid and eventname=:eventname', $params);
                if ($affected_userid != '') {
                    $affected_usersarr[] = $affected_userid;
                } else {
                    $affected_userid = $DB->insert_record('role_reassign_user', $affected_user);
                    $affected_usersarr[] = $affected_userid;
                }
            }
        }
        
              
        
    }
        
}