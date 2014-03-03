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

global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error('invalidcourseid');
}

require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);

$PAGE->set_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('classroomspagetitle', 'block_supervised'));
require('breadcrumbs.php');

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('classroomsheader', 'block_supervised'), 'classroomsdefinition', 'block_supervised');

// Prepare table data.
$classrooms = $DB->get_records('block_supervised_classroom', null, 'name');
$tabledata = array();
foreach ($classrooms as $id => $classroom) {
    // Prepare icons.
    $editurl = new moodle_url('/blocks/supervised/classrooms/addedit.php', array('id' => $id, 'courseid' => $courseid));
    $iconedit = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
    $deleteurl = new moodle_url('/blocks/supervised/classrooms/delete.php', array('courseid' => $courseid, 'id' => $id));
    $icondelete = '';
    if (can_delete_classroom($id)) {
        $icondelete = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
    }

    $iconshowhide = '';
    if (can_showhide_classroom($id)) {
        if ($classroom->active) {
            $showhide = 'hide';
        } else {
            $showhide = 'show';
        }
        $showhideurl = new moodle_url('/blocks/supervised/classrooms/showhide.php', array('courseid' => $courseid, 'id' => $id));
        $iconshowhide = $OUTPUT->action_icon($showhideurl, new pix_icon('t/'.$showhide, get_string($showhide)));
    }

    // Combine new row.
    $tabledata[] = array(
        $classroom->name,
        $classroom->iplist,
        $iconedit . $icondelete . $iconshowhide
    );
}

// Add button 'Add classroom'.
$params['courseid'] = $courseid;
$url = new moodle_url('/blocks/supervised/classrooms/addedit.php', $params);
$caption = get_string('addclassroom', 'block_supervised');
echo $OUTPUT->single_button($url, $caption, 'get');

// Build table.
$table = new html_table();
$headclassroom = get_string('classroom', 'block_supervised');
$headiplist = get_string('iplist', 'block_supervised');
$headedit = get_string('edit');
$table->head = array($headclassroom, $headiplist, $headedit);
$table->data = $tabledata;
echo html_writer::table($table);

// Display footer.
echo $OUTPUT->footer();