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

global $DB, $OUTPUT, $PAGE, $COURSE;

$courseid = required_param('courseid', PARAM_INT);

$id = optional_param('id', 0, PARAM_INT);


if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_role_assign', $courseid);
}

require_login($course);
require_capability('block/role_assign:viewactiverules', $PAGE->context);

$PAGE->set_url('/blocks/role_assign/active_rules.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname.'. '.get_string('activerules', 'block_role_assign'));
$PAGE->set_title($course->fullname.'. '.get_string('activerules', 'block_role_assign'));

$url = new moodle_url('/blocks/role_assign/active_rules.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('activerules', 'block_role_assign'), $url);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('activerules', 'block_role_assign'), 'activerules', 'block_role_assign');
$headings = array(get_string('user', 'block_role_assign'), get_string('current_rule', 'block_role_assign'),
    get_string('currentrole', 'block_role_assign'), get_string('prevrole', 'block_role_assign'));
$align = array('left');
$table = new html_table();
$table->head = $headings;
$table->align = $align;

// Get table with users whose role changed.
$rules = $DB->get_records_sql(
'select inst.id as id,inst.userid as userid,mdl_user.firstname as fn, mdl_user.lastname as ln,inst.ruleid
as ruleid,rules.name as rulename,rules.newroleid
as curroleid,(select mdl_role.shortname from mdl_role where mdl_role.id=rules.newroleid)
as currolename,inst.previousroleid as prevroleid,mdl_role.shortname
as prevrolename from mdl_block_role_assign_instances as inst, mdl_block_role_assign_rules as rules, mdl_role,mdl_user
where inst.ruleid = rules.id && mdl_role.id = inst.previousroleid && mdl_user.id = inst.userid');
foreach ($rules as $rule) {
    $data = array();

    $data [] = ' <a href="'.$CFG->wwwroot.'/user/view.php?id='.$rule->userid.'&amp;course='.$course->id.'">'.$rule->ln.' '
                            .$rule->fn.'</a>';
    $data [] = $rule->rulename;
    $data [] = ' <a href="'.$CFG->wwwroot.'/user/index.php?contextid=15&amp;roleid='.$rule->curroleid.'">'
                            .$rule->currolename.'</a>';
    $data [] = ' <a href="'.$CFG->wwwroot.'/user/index.php?contextid=15&amp;roleid='.$rule->prevroleid.'">'
                            .$rule->prevrolename.'</a>';
    $table->data[] = $data;
}
echo html_writer::table($table);
echo $OUTPUT->footer();