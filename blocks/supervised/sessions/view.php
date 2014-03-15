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
require_once('sessionstate.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE, $USER;
$date = usergetdate(time());
$courseid   = required_param('courseid', PARAM_INT);
$page       = optional_param('page', '0', PARAM_INT);           // Which page to show.
$perpage    = optional_param('perpage', '50', PARAM_INT);       // How many per page.
$from       = optional_param('f', make_timestamp($date['year'], $date['mon'], $date['mday']-7, 0, 0, 0), PARAM_INT);
$to         = optional_param('t', make_timestamp($date['year'], $date['mon'], $date['mday'], 23, 55, 0), PARAM_INT);
$teacher    = optional_param('teacher', $USER->id, PARAM_INT);  // Sessions filtering: teacher id.
$coursefilter = optional_param('course', $courseid, PARAM_INT); // Sessions filtering: course id.
$lessontype = optional_param('lessontype', '-1', PARAM_INT);    // Sessions filtering: lessontype id.
$classroom  = optional_param('classroom', '0', PARAM_INT);      // Sessions filtering: classroom id.
$state      = optional_param('state', '0', PARAM_INT);          // Sessions filtering: state index.


$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error('invalidcourseid');
}

require_login($course);

$PAGE->set_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('sessionspagetitle', 'block_supervised'));
require('breadcrumbs.php');


// Check if user has at least one of capabilities for view smth.
if (!  (has_capability('block/supervised:viewownsessions', $PAGE->context)
    || has_capability('block/supervised:viewallsessions', $PAGE->context)
    || has_capability('block/supervised:manageownsessions', $PAGE->context)
    || has_capability('block/supervised:manageallsessions', $PAGE->context)
    || has_capability('block/supervised:managefinishedsessions', $PAGE->context))   ) {
    require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
}


// Print display options form.
$mform = 'displayoptions_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new displayoptions_sessions_form(null, array('course' => $coursefilter));
$toform['courseid'] = $courseid;
$toform['pagesize'] = $perpage;
$toform['from'] = $from;
$toform['to'] = $to;
$toform['teacher'] = $teacher;
$toform['course'] = $coursefilter;
$toform['classroom'] = $classroom;
$toform['lessontype'] = $lessontype;
$toform['state'] = $state;

if ($fromform = $mform->get_data()) {
    $url = new moodle_url('/blocks/supervised/sessions/view.php',
        array('courseid' => $courseid, 'perpage' => $fromform->pagesize, 'f' => $fromform->from, 't' => $fromform->to,
            'teacher' => $fromform->teacher, 'course' => $fromform->course,
            'classroom' => $fromform->classroom, 'lessontype' => $fromform->lessontype, 'state' => $fromform->state ));
    redirect($url); // Redirect must be done before $OUTPUT->header().
} else {
    // Form didn't validate or this is the first display.
    // Display header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading_with_help(get_string('sessionsheader', 'block_supervised'), 'sessionsdefinition', 'block_supervised');

    // Add 'Plan new session' button.
    if (  has_capability('block/supervised:manageownsessions', $PAGE->context)
        || has_capability('block/supervised:manageallsessions', $PAGE->context)  ) {
        $params['courseid'] = $courseid;
        $url = new moodle_url('/blocks/supervised/sessions/addedit.php', $params);
        $caption = get_string('plansession', 'block_supervised');
        echo $OUTPUT->single_button($url, $caption, 'get');
    }

    print_courses_selector($courseid, $coursefilter, $perpage, $from, $to, $classroom, $state);

    // Print display options form.
    $mform->set_data($toform);
    $mform->display();
}

// Print sessions table.
print_sessions($page, $perpage, "view.php?courseid=$courseid", $from, $to, $teacher,
    $coursefilter, $classroom, $lessontype, $state);

// Display footer.
echo $OUTPUT->footer();


/**
 * Outputs selector with courses which one reloads page when a value has been changed.
 * We do not put it in filtering form because it has its own form.
 *
 * @param $courseid
 * @param $course
 * @param $perpage
 * @param $from
 * @param $to
 * @param $classroom
 * @param $state
 */
function print_courses_selector($courseid, $course, $perpage, $from, $to, $classroom, $state) {
    global $OUTPUT, $SITE;

    $active = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=$course&classroom=$classroom&state=$state";

    // Without teacher and lesson type.
    $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=0&classroom=$classroom&state=$state";
    $urls[$url] = get_string('fulllistofcourses', '');

    if ($courses = get_courses()) {
        foreach ($courses as $course) {
            $coursecontext = context_course::instance($course->id);
            if ($course->id != $SITE->id && has_capability('block/supervised:supervise', $coursecontext)) {
                $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=$course->id&classroom=$classroom&state=$state";
                $urls[$url] = $course->fullname;
            }
        }
    }

    $select = new url_select($urls, $active, null, 'supervisedblock_selectcourseform');
    $select->set_label(get_string('course'), array('id' => 'supervisedblock_courselabel'));
    echo $OUTPUT->render($select);
}