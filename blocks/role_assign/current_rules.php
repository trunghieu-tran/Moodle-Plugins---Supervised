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
require_once($CFG->libdir . '/pluginlib.php');

global $DB, $OUTPUT, $PAGE, $COURSE;


$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_role_assign', $courseid);
}

require_login($course);
require_capability('block/role_assign:viewcurrentrules', $PAGE->context);
// Set page param.
$PAGE->set_url('/blocks/role_assign/current_rules.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname.'. '.get_string('currentrules', 'block_role_assign'));

$PAGE->set_title($course->fullname.'. '.get_string('currentrules', 'block_role_assign'));
$url = new moodle_url('/blocks/role_assign/current_rules.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('currentrules', 'block_role_assign'), $url);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($course->fullname.'. '.get_string('currentrules', 'block_role_assign'), 'currentrules',
    'block_role_assign');

if (has_capability('block/role_assign:addrule', $PAGE->context)) {
    $newrule = new moodle_url('/blocks/role_assign/new_rule.php', array('courseid' => $COURSE->id));
    echo $OUTPUT->single_button($newrule, get_string('newrule', 'block_role_assign'), 'get');
}

$headings = array(get_string('rule', 'block_role_assign'), get_string('type', 'block_role_assign'),
    get_string('newrole', 'block_role_assign'), get_string('task', 'block_role_assign'),
    get_string('role', 'block_role_assign'), get_string('edit', 'block_role_assign'));
$align = array('left');
$table = new html_table();

$table->head = $headings;
$table->align = $align;
// Get exist rules. !!! for lesson type when type not specified wrong output data (this value not displayed in table).

$query = "";
$pluginman = plugin_manager::instance();
if ($pluginman->get_plugin_info('quiz')) {
    $query .= '(select rules.id, rules.name,tasks.name as type ,
(select '.$CFG->prefix.'role.shortname from '.$CFG->prefix.'role where id=rules.newroleid )as newrole ,
    (select group_concat('.$CFG->prefix.'quiz.name)
        from '.$CFG->prefix.'block_role_assign_values as vals,'.$CFG->prefix.'quiz
where vals.name in (select tasks.name from '.$CFG->prefix.'block_role_assign_tasks)&&             rules.id=vals.ruleid
&& '.$CFG->prefix.'quiz.id=vals.value ) as tasks,
    (select group_concat(concat_WS(\',\','.$CFG->prefix.'role.shortname,'.$CFG->prefix.'role.id))
        from '.$CFG->prefix.'role,'.$CFG->prefix.'block_role_assign_roles as roles
        where '.$CFG->prefix.'role.id=roles.roleid && rules.id = roles.ruleid) as allow_roles
    from '.$CFG->prefix.'block_role_assign_rules as rules, '.$CFG->prefix.'block_role_assign_types as types,'.
        $CFG->prefix.'block_role_assign_tasks as tasks
    where rules.id=types.ruleid && types.taskid = tasks.id && types.taskid=1)';
}
if ($pluginman->get_plugin_info('poasassignment')) {
    $query .= 'union
(select rules.id, rules.name,tasks.name as type ,
(select '.$CFG->prefix.'role.shortname from '.$CFG->prefix.'role where id=rules.newroleid )as newrole ,
    (select group_concat('.$CFG->prefix.'poasassignment.name)
        from '.$CFG->prefix.'block_role_assign_values as vals,'.$CFG->prefix.'poasassignment
where vals.name in (select tasks.name from '.$CFG->prefix.'block_role_assign_tasks)&&             rules.id=vals.ruleid
&& '.$CFG->prefix.'poasassignment.id=vals.value ) as tasks,
    (select group_concat(concat_WS(\',\','.$CFG->prefix.'role.shortname,'.$CFG->prefix.'role.id))
        from '.$CFG->prefix.'role,'.$CFG->prefix.'block_role_assign_roles as roles
        where '.$CFG->prefix.'role.id=roles.roleid && rules.id = roles.ruleid) as allow_roles
    from '.$CFG->prefix.'block_role_assign_rules as rules, '.$CFG->prefix.'block_role_assign_types as types,'.
        $CFG->prefix.'block_role_assign_tasks as tasks
    where rules.id=types.ruleid && types.taskid = tasks.id && types.taskid=2)';
}
if ($pluginman->get_plugin_info('block_supervised')) {
    $query .= 'union
(select rules.id, rules.name,tasks.name as type ,
(select '.$CFG->prefix.'role.shortname from '.$CFG->prefix.'role where id=rules.newroleid )as newrole ,
    (select group_concat('.$CFG->prefix.'block_supervised_lessontype.name)
        from '.$CFG->prefix.'block_role_assign_values as vals,'.$CFG->prefix.'block_supervised_lessontype
where vals.name in (select tasks.name from '.$CFG->prefix.'block_role_assign_tasks)&&             rules.id=vals.ruleid
&& '.$CFG->prefix.'block_supervised_lessontype.id=vals.value ) as tasks,
    (select group_concat(concat_WS(\',\','.$CFG->prefix.'role.shortname,'.$CFG->prefix.'role.id))
        from '.$CFG->prefix.'role,'.$CFG->prefix.'block_role_assign_roles as roles
        where '.$CFG->prefix.'role.id=roles.roleid && rules.id = roles.ruleid) as allow_roles
    from '.$CFG->prefix.'block_role_assign_rules as rules, '.$CFG->prefix.'block_role_assign_types as types,'.
        $CFG->prefix.'block_role_assign_tasks as tasks
    where rules.id=types.ruleid && types.taskid = tasks.id && types.taskid=3)';
}
$rules = $DB->get_records_sql($query);
$i = 0;
// Display table.
foreach ($rules as $rule) {
    $row = new html_table_row();
    $row->id = "cur_table".$i;
    $ids = array();
    $row->cells[0] = $rule->name;
    $row->cells [1] = $rule->type;
    $row->cells [2] = $rule->newrole;
    $row->cells [3] = $rule->tasks;
    $ids = explode(',', $rule->allow_roles);
    $row->cells [4] = "";
    for ($j = 0; $j < count($ids); $j += 2) {
        $row->cells [4] .= ' <a href="'.$CFG->wwwroot.'/user/index.php?contextid=15&amp;roleid='.$ids[$j + 1].'">'.
            $ids[$j].'</a>';
    }
    if (has_capability('block/role_assign:editrule', $PAGE->context)) {
        $editurl = new moodle_url('/blocks/role_assign/add_edit_rule_param.php', array('id' => $rule->id,
            'courseid' => $courseid));
        $row->cells[5] = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')) );
    }
    if (has_capability('block/role_assign:deleterule', $PAGE->context)) {
        $row->cells[5] .= "<span id='yesscript' style='display:none'>";
        $row->cells[5] .= $OUTPUT->action_icon("javascript:void(0)", new pix_icon('t/delete', get_string('delete'),
            'moodle', array('id' => "cur_cell$i")));
        $row->cells[5] .= "</span><span id='noscript'>";
        $deleteurl = new moodle_url('/blocks/role_assign/delete_rule.php', array('id' => $rule->id, 'courseid' =>
        $courseid, 'jsdisabled' => 1));
        $row->cells[5] .= $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')) );
        $row->cells[5] .= "</span>";
        $PAGE->requires->js_init_call('M.block_role_assign.del_rule', array($i, $rule->id, $courseid,
            md5($courseid.$rule->id), get_string('deleterule', 'block_role_assign')));
    }
    $i ++;
    $table->data[] = $row;
}
echo html_writer::table($table);

$PAGE->requires->js('/blocks/role_assign/module.js');
echo html_writer::script("Y.all('#yesscript').show();Y.all('#noscript').hide();");

echo $OUTPUT->footer();