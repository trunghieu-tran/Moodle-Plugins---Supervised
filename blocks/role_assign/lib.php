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
 * action when quiz attempt start
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_attempt_start($eventdata) {
    $context = get_context_instance(CONTEXT_COURSE, $eventdata->courseid);
    $roles = get_user_roles($context, $eventdata->userid);

    add_instance_for_user($eventdata->courseid, $eventdata->userid, $roles[key($roles)]->roleid,
        'quiz', $eventdata->quizid);
    return true;
}
/**
 * action when quiz attempt submit
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_attempt_submit($eventdata) {
    del_instance_for_user($eventdata->courseid, $eventdata->userid, $eventdata->quizid);
    return true;
}

/**
 * changed role for single user (add rule instance)
 *
 * @param int $courseid
 * @param int $userid
 * @param int $curentrole
 * @param string $typetask
 * @param int $taskid
 * @param int $rule
 * @internal param object $eventdata event parameters
 * @return boolean
 */
function add_instance_for_user($courseid, $userid, $curentrole, $typetask, $taskid, $rule = 0) {
    global $DB, $CFG, $USER;
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    // If we know role.
    if (isset($rule)) {
        // Find rule for this situation.
        $rule = $DB->get_record_sql('
        SELECT rules.id, roles.roleid , rules.newroleid
        FROM
            '.$CFG->prefix.'block_role_assign_rules rules ,
            '.$CFG->prefix.'block_role_assign_roles roles ,
            '.$CFG->prefix.'block_role_assign_values vals
        WHERE
            roles.ruleid = rules.id AND
            roles.roleid = ? AND
            rules.courseid = ? AND
            vals.ruleid = rules.id AND
            vals.name= ? AND
            vals.value = ?', array($curentrole, $courseid, $typetask, $taskid));
    }
    // If we get role.
    if ($rule) {
        $record = new stdClass();
        $record->ruleid = $rule->id;
        $record->userid = $userid;
        $record->timestart = time();
        $record->previousroleid = $rule->roleid;
        $record->paramvalueid = $taskid;
        // Add new instance.
        $DB->insert_record('block_role_assign_instances', $record);

        $curroleassign = $DB->get_record('role_assignments', array('roleid' => $rule->roleid,
            'contextid' => $context->id, 'userid' => $userid));

        $record = new stdClass();
        $record->id = $curroleassign->id;
        $record->roleid = $rule->newroleid;
        $DB->update_record('role_assignments', $record);
        $ra = new stdClass();
        $ra->roleid = $rule->newroleid;
        $ra->contextid = $context->id;
        $ra->userid = $userid;
        $ra->component = "";
        $ra->itemid = "";
        $ra->timemodified = time();
        $ra->modifierid = empty($USER->id) ? 0 : $USER->id;
        // Reload capabilities.
        reload_all_capabilities();
        events_trigger('role_assigned', $ra);
        return true;
    }
    return false;
}

/**
 * return role single user in course (delete rule instance)
 *
 * @param int $courseid
 * @param $userid
 * @param int $taskid
 * @return boolean
 */
function del_instance_for_user($courseid, $userid, $taskid) {
    global $DB, $CFG, $USER;
    // Find instance.
    $rule = $DB->get_record_sql('SELECT * FROM '.$CFG->prefix.'block_role_assign_instances
        WHERE userid = ? and paramvalueid = ? ', array($userid, $taskid));
    if ($rule) {
        $otherrule = $DB->get_records_sql('SELECT * FROM '.$CFG->prefix.'block_role_assign_instances
            WHERE userid = ? and timestart>(select timestart from '.$CFG->prefix.'block_role_assign_instances where
            userid = ? and ruleid = ?)', array($rule->userid, $rule->userid, $rule->ruleid));
        // If not exist instance which add later.
        if (!$otherrule) {
            // Delete instance.
            $DB->delete_records('block_role_assign_instances', array('id' => $rule->id));
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
            $curroleassign = $DB->get_record('role_assignments', array('contextid' => $context->id,
                'userid' => $userid));

            $record = new stdClass();
            $record->id = $curroleassign->id;
            $record->roleid = $rule->previousroleid;
            $DB->update_record('role_assignments', $record);

            $ra = new stdClass();
            $ra->roleid = $rule->id;
            $ra->contextid = $context->id;
            $ra->userid = $userid;
            $ra->component = "";
            $ra->itemid = "";
            $ra->timemodified = time();
            $ra->modifierid   = empty($USER->id) ? 0 : $USER->id;
            // Reload capabilities.
            reload_all_capabilities();
            events_trigger('role_assigned', $ra);
        } else { // If exist instance which add later.
            // Change previous role for next instance.
            $first = array_shift($otherrule);

            $record = new stdClass();
            $record->id = $first->id;
            $record->previousroleid = $rule->previousroleid;
            $DB->update_record('block_role_assign_instances', $record);

            $DB->delete_records('block_role_assign_instances', array('id' => $rule->id));
        }
    }
}
/**
 * action when poasasssignment recieved
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_poasassignment_task_recieved($eventdata) {
    return true;
}

/**
 * get task id for rule
 *
 * @param $id rule id
 * @return int
 */
function get_typetask ($id) {
    global $DB;
    $rec = $tasks = $DB->get_record('block_role_assign_types', array('ruleid' => $id));
    return $rec->taskid;
}

/**
 * get type task parameters
 *
 * @param int $id rule id when flag=0 or type task id when flag=1
 * @param int $flag what is id: ruleid or taskid
 * @return mixed
 */
function get_typetask_param($id, $flag = 0) {
    $typetaskparam = array(
        '1' => array (
            'name' => 'typetest',
            'tablename' => 'quiz',
            'typetask' => 'quiz'
        ),
        '2' => array (
            'name' => 'typepoasassignment',
            'tablename' => 'poasassignment',
            'typetask' => 'poasassignment'
        ),
        '3' => array (
            'name' => 'typesupervised',
            'tablename' => 'block_supervised_lessontype',
            'typetask' => 'supervised'
        )
    );
    if ($flag == 1) {
        return $typetaskparam[$id];
    }
    return $typetaskparam[get_typetask($id)];
}
/**
 * action when lesson start
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_session_started($eventdata) {
    if ($eventdata->groupid != 0 ) {
        add_instances_for_group($eventdata->courseid, $eventdata->groupid, 'supervised', $eventdata->lessontypeid);
    } else {
        add_instances_for_course($eventdata->courseid, 'supervised', $eventdata->lessontypeid);
    }
    return true;
}
/**
 * action when lesson finished
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_session_finished($eventdata) {

    if ($eventdata->groupid != 0) {
        del_instances_for_group($eventdata->courseid, $eventdata->groupid, $eventdata->lessontypeid);
    } else {
        del_instances_for_course($eventdata->courseid, $eventdata->lessontypeid);
    }
    return true;
}
/**
 * action when lesson update
 *
 * @param object $eventdata event parameters
 * @return boolean
 */
function event_handler_session_updated($eventdata) {

    if ($eventdata->oldgroupid != 0) {
        del_instances_for_group($eventdata->courseid, $eventdata->oldgroupid, $eventdata->lessontypeid);
    } else {
        del_instances_for_course($eventdata->courseid, $eventdata->lessontypeid);
    }
    if ($eventdata->newgroupid != 0) {
        add_instances_for_group($eventdata->courseid, $eventdata->newgroupid, 'supervised', $eventdata->lessontypeid);
    } else {
        add_instances_for_course($eventdata->courseid, 'supervised', $eventdata->lessontypeid);
    }
    return true;
}

/**
 * changed role for user group in course (add rule instances)
 *
 * @param int $courseid
 * @param $groupid
 * @param string $typetask
 * @param int $taskid
 * @return boolean
 */
function add_instances_for_group($courseid, $groupid, $typetask, $taskid) {
    global $DB, $CFG;
    $users = $DB->get_records_sql(
        'SELECT gm.userid,  rules.id as rule, r.id as current_role, rules.newroleid as newroleid
            FROM
            '.$CFG->prefix.'user u,
            '.$CFG->prefix.'role_assignments ra,
            '.$CFG->prefix.'context con,
            '.$CFG->prefix.'role r,
            '.$CFG->prefix.'groups_members gm,
            '.$CFG->prefix.'block_role_assign_rules rules,
            '.$CFG->prefix.'block_role_assign_roles roles,
            '.$CFG->prefix.'block_role_assign_values vals
            WHERE
            gm.groupid = ? AND
            vals.name = ? AND
            vals.ruleid = rules.id AND
            vals.value = ? AND
            roles.ruleid = rules.id AND
            rules.courseid = ? AND
            u.id = gm.userid AND
            u.id = ra.userid AND
            ra.contextid = con.id AND
            con.contextlevel = 50 AND
            con.instanceid = ? AND
            ra.roleid = r.id AND
            roles.roleid = r.id
        ',
        array($groupid, $typetask, $taskid, $courseid, $courseid));

    foreach ($users as $user) {
        $rule = new stdClass();
        $rule->id = $user->rule;
        $rule->roleid = $user->current_role;
        $rule->newroleid = $user->newroleid;
        add_instance_for_user($courseid, $user->userid, $user->current_role, $typetask, $taskid, $rule);
    }
}

/**
 * return role user group in course (delete rule instances)
 *
 * @param int $courseid
 * @param $groupid
 * @param int $taskid
 * @return boolean
 */
function del_instances_for_group($courseid, $groupid, $taskid) {
    global $DB, $CFG;
    $users = $DB->get_records_sql(
        'SELECT gm.userid
            FROM
            '.$CFG->prefix.'user u,
            '.$CFG->prefix.'role_assignments ra,
            '.$CFG->prefix.'context con,
            '.$CFG->prefix.'role r,
            '.$CFG->prefix.'groups_members gm

            WHERE
            gm.groupid = ? AND
            u.id = gm.userid AND
            u.id = ra.userid AND
            ra.contextid = con.id AND
            con.contextlevel = 50 AND
            con.instanceid = ? AND
            ra.roleid = r.id

        ', array($groupid, $courseid));
    foreach ($users as $user) {
        del_instance_for_user($courseid, $user->userid, $taskid);
    }
}

/**
 * changed role all users in course (add rule instances)
 *
 * @param int $courseid
 * @param string $typetask
 * @param int $taskid
 * @return boolean
 */
function add_instances_for_course($courseid, $typetask, $taskid) {
    global $DB, $CFG;
    $users = $DB->get_records_sql(
        '
        SELECT u.id as userid,  rules.id as rule, r.id as current_role, rules.newroleid as newroleid
            FROM
            '.$CFG->prefix.'user u,
            '.$CFG->prefix.'role_assignments ra,
            '.$CFG->prefix.'context con,
            '.$CFG->prefix.'role r,
            '.$CFG->prefix.'block_role_assign_rules rules,
            '.$CFG->prefix.'block_role_assign_roles roles,
            '.$CFG->prefix.'block_role_assign_values vals
            WHERE
            vals.name = ? AND
            vals.ruleid = rules.id AND
            vals.value = ? AND
            roles.ruleid = rules.id AND
            rules.courseid = ? AND
            u.id = ra.userid AND
            ra.contextid = con.id AND
            con.contextlevel = 50 AND
            con.instanceid = ? AND
            ra.roleid = r.id AND
            roles.roleid = r.id

        ',
        array($typetask, $taskid, $courseid, $courseid));

    foreach ($users as $user) {
        $rule = new stdClass();
        $rule->id = $user->rule;
        $rule->roleid = $user->current_role;
        $rule->newroleid = $user->newroleid;
        add_instance_for_user($courseid, $user->userid, $user->current_role, $typetask, $taskid, $rule);
    }
}

/**
 * return role all users in course (delete rule instances)
 *
 * @param int $courseid
 * @param int $taskid
 * @return boolean
 */
function del_instances_for_course ($courseid, $taskid) {
    global $DB, $CFG;
    $users = $DB->get_records_sql(
        'SELECT u.id as userid
            FROM
            '.$CFG->prefix.'user u,
            '.$CFG->prefix.'role_assignments ra,
            '.$CFG->prefix.'context con,
            '.$CFG->prefix.'role r

            WHERE
            u.id = ra.userid AND
            ra.contextid = con.id AND
            con.contextlevel = 50 AND
            con.instanceid = ? AND
            ra.roleid = r.id

        ', array($courseid));
    foreach ($users as $user) {
        del_instance_for_user($courseid, $user->userid, $taskid);
    }
}