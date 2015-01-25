<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once('../lib.php');

$id         = required_param('id', PARAM_INT);              // Lessontype id.
$courseid   = required_param('courseid', PARAM_INT);
$delete     = optional_param('delete', '', PARAM_ALPHANUM); // Delete confirmation hash.

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}


$site = get_site();
require_login($course);
require_capability('block/supervised:editlessontypes', $PAGE->context);
$PAGE->set_url('/blocks/supervised/lessontypes/delete.php', array('id' => $id, 'courseid' => $courseid));
require('breadcrumbs.php');

if (! $lessontype = $DB->get_record('block_supervised_lessontype', array('id' => $id, 'courseid' => $courseid))) {
    print_error(get_string('invalidlessontypeid', 'block_supervised'));
}

if (!can_delete_lessontype($id)) {
    print_error(get_string('cannotdeletelessontype', 'block_supervised'));
}

// Show form first time.
if (! $delete) {
    $strdeletecheck = get_string('deletecheck', '', $lessontype->name);
    $strdeletelessontypecheck = get_string('deletelessontypecheck', 'block_supervised');

    $PAGE->navbar->add($strdeletecheck);
    $PAGE->set_title("$course->shortname: $strdeletecheck");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $message = "$strdeletelessontypecheck<br /><br />" . $lessontype->name;

    echo $OUTPUT->confirm($message,
        "delete.php?id=$id&courseid=$courseid&delete=".md5($lessontype->name),
        "view.php?courseid=$courseid");

    echo $OUTPUT->footer();
    exit;
}

if ($delete != md5($lessontype->name)) {
    print_error('invalidmd5');
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

// OK checks done, delete the lessontype now.
$DB->delete_records('block_supervised_lessontype', array('id' => $id));

// TODO Logging.
add_to_log($COURSE->id, 'role', 'delete lesson type',
    "blocks/supervised/lessontypes/view.php?courseid={$COURSE->id}", $lessontype->name);

// Redirect to lessontypes page.
$url = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $courseid));
redirect($url);
