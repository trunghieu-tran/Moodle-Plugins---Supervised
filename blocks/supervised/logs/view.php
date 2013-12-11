<?php
require_once('../../../config.php');
require_once('../lib.php');

global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid',  PARAM_INT);
$sessionid  = required_param('sessionid', PARAM_INT);
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
require_capability('block/supervised:readlogs', $PAGE->context);
$PAGE->set_url('/blocks/supervised/logs/view.php', array('courseid' => $courseid, 'sessionid' => $sessionid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('logspagetitle', 'block_supervised'));
include("breadcrumbs.php");

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("logsview", 'block_supervised'), 3);



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




// TODO output select logs form
/*print_log($session->courseid, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
    $url="", $modname="", $modid=0, $modaction="", $groupid=0);*/


// Prepare logs form.
$mformlogs = "logs_form.php";
if (file_exists($mformlogs)) {
    require_once($mformlogs);
} else {
    print_error('noformdesc');
}


if ($fromform = $mform->get_data()) {

} else{
    $toformlogs['sessionid']    = $sessionid;
    $toformlogs['courseid']     = $courseid;
    $mformlogs = new logs_form(null, array('groupid' => $session->groupid, 'courseid' => $courseid));
    $mformlogs->set_data($toformlogs);
    $mformlogs->display();
}





// Display footer.
echo $OUTPUT->footer();