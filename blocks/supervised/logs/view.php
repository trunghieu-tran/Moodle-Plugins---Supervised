<?php
require_once('../../../config.php');
require_once('../lib.php');
require_once('logslib.php');
require_once('../sessions/sessionstate.php');

global $DB, $OUTPUT, $PAGE;

$courseid    = required_param('courseid',  PARAM_INT);
$sessionid   = required_param('sessionid', PARAM_INT);
$page        = optional_param('page', '0', PARAM_INT);      // which page to show
$perpage     = optional_param('perpage', '50', PARAM_INT);  // how many per page
$userid      = optional_param('userid', '0', PARAM_INT);    // current user id

$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}
if (! $session = $DB->get_record("block_supervised_session", array("id"=>$sessionid))) {
    print_error(get_string("invalidsessionid", 'block_supervised'));
}

require_login($course);

$PAGE->set_url('/blocks/supervised/logs/view.php', array('courseid' => $courseid, 'sessionid' => $sessionid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('logspagetitle', 'block_supervised'));
include("breadcrumbs.php");

// Check capabilities.
if ($session->teacherid != $USER->id) {
    // User wants view logs of other user's session.
    require_capability('block/supervised:viewallsessions', $PAGE->context);
}
else{
    // User wants view logs of own session.
    if ($session->state != StateSession::Active) {
        // Check capabilities fow own active session.
        if(!  (has_capability('block/supervised:supervise', $PAGE->context)
            || has_capability('block/supervised:viewownsessions', $PAGE->context)
            || has_capability('block/supervised:viewallsessions', $PAGE->context))  ){
            require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
        }
    }
    else{
        // Check capabilities fow own not active session.
        if(!  (has_capability('block/supervised:viewownsessions', $PAGE->context)
            || has_capability('block/supervised:viewallsessions', $PAGE->context))  ){
            require_capability('block/supervised:viewownsessions', $PAGE->context);   // Print error.
        }
    }
}
if ($session->state == StateSession::Planned) {
    print_error(get_string("sessionlogserror", 'block_supervised'));
}

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("logsview", 'block_supervised'), 2);



// Prepare session info form.
$mform = "viewsession_form.php";
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new viewsession_form();
$select = "SELECT
        {block_supervised_session}.id,
        {block_supervised_session}.timestart,
        {block_supervised_session}.duration,
        {block_supervised_session}.timeend,
        {block_supervised_session}.courseid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.state,
        {block_supervised_session}.sessioncomment,
        {block_supervised_classroom}.name   AS classroomname,
        {block_supervised_lessontype}.name  AS lessontypename,
        {user}.firstname,
        {user}.lastname,
        {groups}.name                       AS groupname,
        {groups}.id                         AS groupid,
        {course}.fullname                   AS coursename

        FROM {block_supervised_session}
            JOIN {block_supervised_classroom}
              ON {block_supervised_session}.classroomid       =   {block_supervised_classroom}.id
            LEFT JOIN {block_supervised_lessontype}
              ON {block_supervised_session}.lessontypeid =   {block_supervised_lessontype}.id
            JOIN {user}
              ON {block_supervised_session}.teacherid    =   {user}.id
            LEFT JOIN {groups}
              ON {block_supervised_session}.groupid      =   {groups}.id
            JOIN {course}
              ON {block_supervised_session}.courseid     =   {course}.id

        WHERE {block_supervised_session}.id      = :sessionid
        ";
$params['sessionid']      = $sessionid;
$session = $DB->get_record_sql($select, $params);

$strftimedatetime = get_string("strftimerecent");
$toform['coursename']       = $session->coursename;
$toform['classroomname']    = $session->classroomname;
$toform['groupname']        = $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname;
$toform['teachername']      = html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), $session->firstname . " " . $session->lastname);
$toform['lessontypename']   = $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename;
$toform['timestart']        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
$toform['duration']         = $session->duration;
$toform['timeend']          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
$toform['sessioncomment']   = $session->sessioncomment;

$mform->set_data($toform);
$mform->display();      // Display view session form.





// Prepare filter logs form.
$mformlogs = "logs_form.php";
if (file_exists($mformlogs)) {
    require_once($mformlogs);
} else {
    print_error('noformdesc');
}

$mformlogs = new logs_form(null, array('groupid' => $session->groupid, 'courseid' => $courseid, 'teacherid' => $session->teacherid));
$toformlogs['sessionid']    = $sessionid;
$toformlogs['courseid']     = $courseid;
$toformlogs['userid']       = $userid;
$mformlogs->set_data($toformlogs);
$mformlogs->display();

if ($fromform = $mformlogs->get_data()) {
    $userid = $fromform->userid;
}

// Print logs.
supervisedblock_print_logs($sessionid, $session->timestart, $session->timeend, $userid, $page, $perpage, "view.php?courseid=$courseid&amp;sessionid=$sessionid&amp;userid=$userid");


// Display footer.
echo $OUTPUT->footer();