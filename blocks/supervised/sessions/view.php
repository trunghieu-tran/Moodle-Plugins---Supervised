<?php
require_once('../../../config.php');
require_once('sessionstate.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$page       = optional_param('page', '0', PARAM_INT);       // which page to show
$perpage    = optional_param('perpage', '50', PARAM_INT);   // how many per page
$from       = optional_param('f', mktime(0, 0, 0, date('n'), date('j')), PARAM_INT);     // sessions filtering: timestamp from
$to         = optional_param('t', mktime(23, 55, 0, date('n'), date('j')), PARAM_INT);   // sessions filtering: timestamp to
$teacher    = optional_param('teacher', '0', PARAM_INT);    // sessions filtering: teacher id
$coursefilter = optional_param('course', '0', PARAM_INT);   // sessions filtering: course id
$lessontype = optional_param('lessontype', '-1', PARAM_INT); // sessions filtering: lessontype id
$classroom  = optional_param('classroom', '0', PARAM_INT);  // sessions filtering: classroom id
$state      = optional_param('state', '0', PARAM_INT);      // sessions filtering: state index




$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}

require_login($course);

$PAGE->set_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('sessionspagetitle', 'block_supervised'));
include("breadcrumbs.php");


// Check if user has at least one of capabilities for view smth.
if(!  (has_capability('block/supervised:viewownsessions', $PAGE->context)
    || has_capability('block/supervised:viewallsessions', $PAGE->context)
    || has_capability('block/supervised:manageownsessions', $PAGE->context)
    || has_capability('block/supervised:manageallsessions', $PAGE->context)
    || has_capability('block/supervised:managefinishedsessions', $PAGE->context))   ) {
    require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
}


// Print display options form.
$mform = "displayoptions_form.php";
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
        array('courseid'=>$courseid, 'perpage'=>$fromform->pagesize, 'f'=>$fromform->from, 't'=>$fromform->to,
            'teacher'=>$fromform->teacher, 'course'=>$fromform->course, 'classroom'=>$fromform->classroom, 'lessontype'=>$fromform->lessontype, 'state'=>$fromform->state ));
    redirect($url); // Redirect must be done before $OUTPUT->header().
} else {
    // Form didn't validate or this is the first display.
    // Display header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading_with_help(get_string("sessionsheader", 'block_supervised'), 'sessionsdefinition', 'block_supervised');

    // Add "Plan new session" button.
    if(  has_capability('block/supervised:manageownsessions', $PAGE->context)
        || has_capability('block/supervised:manageallsessions', $PAGE->context)  ){
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
print_sessions($page, $perpage, "view.php?courseid=$courseid", $from, $to, $teacher, $coursefilter, $classroom, $lessontype, $state);

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
function print_courses_selector($courseid, $course, $perpage, $from, $to, $classroom, $state){
    global $OUTPUT, $SITE;

    $active = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=$course&classroom=$classroom&state=$state";

    // Without teacher and lesson type.
    $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=0&classroom=$classroom&state=$state";
    $urls[$url] = get_string('fulllistofcourses', '');

    if ($courses = get_courses()) {
        foreach ($courses as $course) {
            if($course->id != $SITE->id){
                $url = "/blocks/supervised/sessions/view.php?courseid=$courseid&perpage=$perpage&f=$from&t=$to&course=$course->id&classroom=$classroom&state=$state";
                $urls[$url] = $course->fullname;
            }
        }
    }

    $select = new url_select($urls, $active, null, 'supervisedblock_selectcourseform');
    $select->set_label(get_string('course', 'block_supervised'), array("id"=>"supervisedblock_courselabel"));
    echo $OUTPUT->render($select);
}