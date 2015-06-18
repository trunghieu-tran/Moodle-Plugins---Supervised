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

// Set sessions filter parameters.
$pref = get_sessions_filter_user_preferences();
$pref['block_supervised_page']      = optional_param('page',        $pref['block_supervised_page'],      PARAM_INT);
$pref['block_supervised_perpage']   = optional_param('perpage',     $pref['block_supervised_perpage'],   PARAM_INT);
$pref['block_supervised_from']      = optional_param('f',           $pref['block_supervised_from'],      PARAM_INT);
$pref['block_supervised_to']        = optional_param('t',           $pref['block_supervised_to'],        PARAM_INT);
$pref['block_supervised_teacher']   = optional_param('teacher',     $pref['block_supervised_teacher'],   PARAM_INT);
$pref['block_supervised_course']    = optional_param('course',      $pref['block_supervised_course'],    PARAM_INT);
$pref['block_supervised_lessontype'] = optional_param('lessontype',  $pref['block_supervised_lessontype'], PARAM_INT);
$pref['block_supervised_classroom'] = optional_param('classroom',   $pref['block_supervised_classroom'], PARAM_INT);
$pref['block_supervised_state']     = optional_param('state',       $pref['block_supervised_state'],     PARAM_INT);
check_sessions_filter_user_preferences($pref);
save_sessions_filter_user_preferences($pref);


// Output sessions filter.
$mform = 'displayoptions_form.php';
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new displayoptions_sessions_form(null, array('course' => $pref['block_supervised_course']));
$toform['courseid']     = $courseid;
$toform['pagesize']     = $pref['block_supervised_perpage'];
$toform['from']         = $pref['block_supervised_from'];
$toform['to']           = $pref['block_supervised_to'];
$toform['teacher']      = $pref['block_supervised_teacher'];
$toform['course']       = $pref['block_supervised_course'];
$toform['classroom']    = $pref['block_supervised_classroom'];
$toform['lessontype']   = $pref['block_supervised_lessontype'];
$toform['state']        = $pref['block_supervised_state'];

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

    print_courses_selector($courseid, $pref['block_supervised_course'],
        $pref['block_supervised_perpage'], $pref['block_supervised_from'],
        $pref['block_supervised_to'], $pref['block_supervised_classroom'],
        $pref['block_supervised_state']);

    // Print display options form.
    $mform->set_data($toform);
    $mform->display();
}

print_sessions($pref['block_supervised_page'], $pref['block_supervised_perpage'],
    "view.php?courseid=$courseid", $pref['block_supervised_from'],
    $pref['block_supervised_to'], $pref['block_supervised_teacher'],
    $pref['block_supervised_course'], $pref['block_supervised_classroom'],
    $pref['block_supervised_lessontype'], $pref['block_supervised_state']);

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
    global $OUTPUT, $SITE, $USER;

    $active = "/blocks/supervised/sessions/view.php?courseid=$courseid&page=0&perpage=$perpage&f=$from&t=$to&course=$course&classroom=$classroom&state=$state&lessontype=-1&teacher=$USER->id";

    // All courses url.
    $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&page=0&perpage=$perpage&f=$from&t=$to&course=0&classroom=$classroom&state=$state&lessontype=-1&teacher=$USER->id";
    $urls[$url] = get_string('fulllistofcourses', '');

    if ($courses = get_courses()) {
        foreach ($courses as $course) {
            $coursecontext = context_course::instance($course->id);
            if ($course->id != $SITE->id && has_capability('block/supervised:supervise', $coursecontext)) {
                $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&page=0&perpage=$perpage&f=$from&t=$to&course=$course->id&classroom=$classroom&state=$state&lessontype=-1&teacher=$USER->id";
                $urls[$url] = $course->fullname;
            }
        }
    }

    $select = new url_select($urls, $active, null, 'supervisedblock_selectcourseform');
    $select->set_label(get_string('course'), array('id' => 'supervisedblock_courselabel'));
    echo $OUTPUT->render($select);
}