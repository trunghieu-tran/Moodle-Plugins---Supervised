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
require_once('../../config.php');
require_once('role_assign_form.php');

global $DB, $OUTPUT, $PAGE;


$courseid = required_param('courseid', PARAM_INT);


$id = optional_param('id', 0, PARAM_INT);
$rulename = optional_param('rulename', 0, PARAM_TEXT);
$typetask = optional_param('typetask', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_role_assign', $courseid);
}

require_login($course);
require_capability('block/role_assign:editrule', $PAGE->context);

// Set type page: add or edit.
$typepage = "editrule";
if (!empty($rulename) && !empty($typetask)) {
    $PAGE->set_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid, 'id' => $id,
        'rulename' => $rulename, 'typetask' => $typetask));
    $typepage = "addrule";
} else {
    $PAGE->set_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid, 'id' => $id));
}
// Set page param.
$PAGE->set_title(get_string($typepage, 'block_role_assign'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string($typepage, 'block_role_assign'));

if (!empty($rulename) && !empty($typetask)) {
    $url = new moodle_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid, 'id' => $id,
        'rulename' => $rulename, 'typetask' => $typetask));
} else {
    $url = new moodle_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid, 'id' => $id));
}
$PAGE->navbar->add(get_string($typepage, 'block_role_assign'), $url);

$settingsnode = $PAGE->settingsnav->add(get_string('role_assignsettings', 'block_role_assign'));
$editurl = new moodle_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid, 'id' => $id));


// Set type task.
$form = new add_edit_rule_form();
if ($id) {
    $typetask = get_typetask($id);
    $typetaskparam = get_typetask_param($id);
} else if ($typetask) {
    $typetaskparam = get_typetask_param($typetask, 1);
}
// Display field in form with elements depending on type task.
if ($typetask) {
    $form->set_tasks_param($typetaskparam['tablename'], $typetaskparam['name']);
}


if ($form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $form->get_data()) {

    $record = new stdClass();
    $record->name = $fromform->rulename;
    $record->courseid = $fromform->courseid;
    $record->newroleid = $fromform->newrole;

    // Set type task id.
    if ($fromform->typetaskid == 1) {
        $typetasktext = 'quiz';
    } else if ($fromform->typetaskid == 2) {
        $typetasktext = 'poasassignment';
    } else if ($fromform->typetaskid == 3) {
        $typetasktext = 'supervised';
    }
    // Check whether there already exists rule.
    if (!$id) {
        $typetaskparam = get_typetask_param($fromform->typetaskid, 1);
        $form->set_tasks_param($typetaskparam['tablename'], $typetaskparam['name']);
        $fromform = $form->get_data();
    }
    $checkrules = $DB->get_records_sql('select rules.id,rules.name, roles.roleid as allow_role,vals.value
        from '.$CFG->prefix.'block_role_assign_rules as rules,'.$CFG->prefix.'block_role_assign_roles as roles,'
            .$CFG->prefix.'block_role_assign_values as vals
        where roles.ruleid=rules.id && vals.ruleid = rules.id && vals.name=\''.$typetasktext.'\' &&
        roles.roleid in ('.implode(',', $fromform->allowroles).') && vals.value in ('.implode(',', $fromform->tasks).')
        &&rules.id<>'.$id.';'
        , array());
    if ($checkrules) {
        print_error('current rule already exixst');
    } else {
        // Add new rule (or update).
        if (!$id) {
            $lastinsertid = $DB->insert_record('block_role_assign_rules', $record);
        } else {
            $record->id = $id;
            $DB->update_record('block_role_assign_rules', $record);
            $lastinsertid = $id;
        }
        // Delete old rule params.
        if ($id) {
            $DB->delete_records('block_role_assign_roles', array('ruleid' => $id));
            $DB->delete_records('block_role_assign_values', array('ruleid' => $id, 'name' => $typetaskparam['typetask']));
        } else {
            $record = new stdClass();
            $record->ruleid = $lastinsertid;
            $record->taskid = $fromform->typetaskid;
            $DB->insert_record('block_role_assign_types', $record);
        }
        // Add new rule params.
        foreach ($fromform->allowroles as $role) {
            $record = new stdClass();
            $record->ruleid = $lastinsertid;
            $record->roleid = $role;
            $DB->insert_record('block_role_assign_roles', $record);
        }
        foreach ($fromform->tasks as $task) {
            $record = new stdClass();
            $record->name = $typetaskparam['typetask'];
            $record->ruleid = $lastinsertid;
            $record->value = $task;
            $DB->insert_record('block_role_assign_values', $record);
        }
        $curruleurl = new moodle_url('/blocks/role_assign/current_rules.php', array('courseid' => $courseid));
        redirect($curruleurl);
    }
} else {
    // Display form when page open.
    if ($id) {
            $rule = $DB->get_record_sql('
            select rules.id, rules.name,tasks.id as type ,
        rules.newroleid,
        (select group_concat('.$CFG->prefix.$typetaskparam['tablename'].'.id)
            from '.$CFG->prefix.'block_role_assign_values as vals,'.$CFG->prefix.$typetaskparam['tablename'].'
            where vals.name in (select tasks.name from '.$CFG->prefix.'block_role_assign_tasks)&& rules.id=vals.ruleid
            && '.$CFG->prefix.$typetaskparam['tablename'].'.id=vals.value ) as tasks,
        (select group_concat('.$CFG->prefix.'role.id)
            from '.$CFG->prefix.'role,'.$CFG->prefix.'block_role_assign_roles as roles
            where '.$CFG->prefix.'role.id=roles.roleid && rules.id = roles.ruleid) as allow_roles
        from '.$CFG->prefix.'block_role_assign_rules as rules, '.$CFG->prefix.'block_role_assign_types as types,'.
                $CFG->prefix.'block_role_assign_tasks as tasks
        where rules.id = ? && rules.id=types.ruleid && types.taskid = tasks.id
        ', array($id));
        $rulename = $rule->name;
        $typetask = $rule->type;
        $toform['newrole'] = $rule->newroleid;
        $toform['allowroles'] = explode(',', $rule->allow_roles);
        $toform['tasks'] = explode(',', $rule->tasks);
    }
    $toform['id'] = $id;
    $toform['courseid'] = $courseid;
    $toform['rulename'] = $rulename;
    $toform['typetaskid'] = $typetask;
    if ($typetask == 1) {
        $toform['typetask'] = get_string('typetest', 'block_role_assign');
    } else if ($typetask == 2) {
        $toform['typetask'] = get_string('typepoasassignment', 'block_role_assign');
    } else if ($typetask == 3) {
        $toform['typetask'] = get_string('typesupervised', 'block_role_assign');
    }
    $form->set_data($toform);
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string($typepage, 'block_role_assign'), 3);
    $form->display();
    echo $OUTPUT->footer();
}