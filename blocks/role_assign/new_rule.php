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

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_role_assign', $courseid);
}

require_login($course);
require_capability('block/role_assign:addrule', $PAGE->context);
// Set page param.
$PAGE->set_url('/blocks/role_assign/new_rule.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('addrule', 'block_role_assign'));

$PAGE->set_title(get_string('addrule', 'block_role_assign'));

$form = new add_rule_form();

$url = new moodle_url('/blocks/role_assign/new_rule.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('addrule', 'block_role_assign'), $url);

$toform['courseid'] = $courseid;
$form->set_data($toform);

if ($form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $form->get_data()) {
    $url = new moodle_url('/blocks/role_assign/add_edit_rule_param.php', array('courseid' => $courseid,
        'typetask' => $fromform->typetask, 'rulename' => $fromform->rulename));
    redirect($url);
} else {
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('addrule', 'block_role_assign'), 3);
    $form->display();
    echo $OUTPUT->footer();
}