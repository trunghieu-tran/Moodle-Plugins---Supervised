<?php
require_once('../../../config.php');
require_once('sessionstate.php');
 
global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$page       = optional_param('page', '0', PARAM_INT);      // which page to show
$perpage    = optional_param('perpage', '50', PARAM_INT);  // how many per page
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


// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("sessionsheader", 'block_supervised'), 2);

// Add "Plan new session" button.
if(  has_capability('block/supervised:manageownsessions', $PAGE->context)
        || has_capability('block/supervised:manageallsessions', $PAGE->context)  ){
    $params['courseid'] = $courseid;
    $url = new moodle_url('/blocks/supervised/sessions/addedit.php', $params);
    $caption = get_string('plansession', 'block_supervised');
    echo $OUTPUT->single_button($url, $caption, 'get');
}

// Print sessions table.
print_sessions($page, $perpage, "view.php?courseid=$courseid");

// Display footer.
echo $OUTPUT->footer();