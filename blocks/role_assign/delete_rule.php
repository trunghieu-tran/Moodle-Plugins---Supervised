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


$id = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$del = optional_param('del', '', PARAM_ALPHANUM);
$jsdisabled = optional_param('jsdisabled', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_role_assign', $courseid);
}

require_login($course);
require_capability('block/role_assign:deleterule', $PAGE->context);
$site = get_site();
$PAGE->set_url('/blocks/role_assign/delete_rule.php', array('id' => $id, 'courseid' => $courseid));


if (!$del) {
    $PAGE->set_title(get_string("delrule", 'block_role_assign'));
    echo $OUTPUT->header();

    $message = get_string("deleterule", 'block_role_assign');

    echo $OUTPUT->confirm($message, "delete_rule.php?id=$id&courseid=$courseid&jsdisabled=$jsdisabled&del=".
        md5($courseid.$id), "view.php?courseid=$courseid");
    echo $OUTPUT->footer();
    exit;
}

if ($del != md5($courseid.$id)) {
    print_error("invalidmd5");
}
// Delete rule params.
$DB->delete_records('block_role_assign_roles', array('ruleid' => $id));
$DB->delete_records('block_role_assign_types', array('ruleid' => $id));
$DB->delete_records('block_role_assign_values', array('ruleid' => $id));
// Delete rule.
$DB->delete_records('block_role_assign_rules', array('id' => $id));

if ($jsdisabled) {
    $url = new moodle_url('/blocks/role_assign/current_rules.php', array('courseid' => $courseid));
    redirect($url);
}
