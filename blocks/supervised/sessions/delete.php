<?php
require_once('../../../config.php');
require_once('sessionstate.php');
global $DB, $PAGE, $OUTPUT, $USER;

$id         = required_param('id', PARAM_INT);              // session id
$courseid   = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}

$site = get_site();
require_login($course);
$PAGE->set_url('/blocks/supervised/sessions/delete.php', array('id' => $id, 'courseid' => $courseid));
$PAGE->set_pagelayout('standard');
include("breadcrumbs.php");

if (! $session = $DB->get_record("block_supervised_session", array("id"=>$id))) {
    print_error(get_string("invalidsessionid", 'block_supervised'));
}

// Check capabilities.
if ($session->state == StateSession::Finished) {
    // Only user with managefinishedsessions capability can remove finished sessions.
    require_capability('block/supervised:managefinishedsessions', $PAGE->context);
}
else{
    if ( ! (($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
            || has_capability('block/supervised:manageallsessions', $PAGE->context))   ){
        require_capability('block/supervised:manageownsessions', $PAGE->context);   // Print error.
    }
    else{
        // User wants remove session of other user.
        require_capability('block/supervised:manageallsessions', $PAGE->context);
    }
}

if ($session->state == StateSession::Active) {
    print_error(get_string("sessiondeleteerror", 'block_supervised'));
}




// Prepare form.
$mform = "delete_form.php";
if (file_exists($mform)) {
    require_once($mform);
} else {
    print_error('noformdesc');
}
$mform = new delete_session_form();



if($mform->is_cancelled()) {
    // Cancelled forms redirect to the sessions view page.
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else if ($fromform = $mform->get_data()) {
    // Delete session.
    // TODO Logging
    $DB->delete_records('block_supervised_session', array('id'=>$id));
    $url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
    redirect($url);
} else {
    // The first display.
    $PAGE->navbar->add(get_string("deletesessionnavbar", 'block_supervised'));
    $PAGE->set_title(get_string("sessiondeletetitle", 'block_supervised'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string("sessiondeleteheader", 'block_supervised'), 3);

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

        WHERE {block_supervised_session}.id      = :id
        ";
    $params['id']      = $id;
    $session = $DB->get_record_sql($select, $params);

    $strftimedatetime = get_string("strftimerecent");

    $toform['id']               = $id;
    $toform['courseid']         = $courseid;
    $toform['coursename']       = $session->coursename;
    $toform['classroomname']    = $session->classroomname;
    $toform['groupname']        = $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname;
    $toform['teachername']      = html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), $session->firstname . " " . $session->lastname);
    $toform['lessontypename']   = $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename;
    $toform['timestart']        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $toform['duration']         = $session->duration;
    $toform['timeend']          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    $toform['sessioncomment']   = $session->sessioncomment;
    $toform['notifyteacher']    = 1;

    $mform->set_data($toform);
    $mform->display();


    echo $OUTPUT->footer();
}