<?php
require_once('../../../config.php');
require_once('sessionstate.php');
 
global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
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

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("sessionsheader", 'block_supervised'), 3);


// Prepare table data 
$select = "SELECT
{block_supervised_session}.id,
{block_supervised_session}.timestart,
{block_supervised_session}.duration,
{block_supervised_session}.timeend,
{block_supervised_session}.courseid,
{block_supervised_session}.teacherid,
{block_supervised_session}.state,
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
";
/*TODO Add WHERE for filtering
WHERE ({block_supervised_session}.timestart BETWEEN :time1 AND :time2)
    AND {block_supervised_session}.courseid     = :courseid
    AND {block_supervised_session}.teacherid    = :teacherid
    AND {block_supervised_session}.groupid      = :groupid


// TODO initialize from filter
$time1      = 1378024800;
$time2      = 1378024890;
$teacherid  = 3;
$groupid    = 1;
$params['time1']        = $time1;
$params['time2']        = $time2;
$params['courseid']     = $courseid;
$params['teacherid']    = $teacherid;
$params['groupid']      = $groupid;
*/
$sessions = $DB->get_records_sql($select/*, $params*/);

$strftimedatetime = get_string("strftimerecent");
$tabledata = array();
foreach ($sessions as $id=>$session) {
    // Prepare icons and urls.
    $editurl        = new moodle_url('/blocks/supervised/sessions/addedit.php', array('id' => $id, 'courseid' => $courseid));
    $iconedit       = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
    $deleteurl      = new moodle_url('/blocks/supervised/sessions/delete.php', array('courseid' => $courseid, 'id' => $id));
    $icondelete     = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
    $logsurl = new moodle_url('/blocks/supervised/logs/view.php', array('sessionid' => $id, 'courseid' => $courseid));
    

    // Combine new row.
    $tabledata[] = array(   $session->coursename,
                            $session->classroomname,
                            $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname,
                            
                            html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), $session->firstname . " " . $session->lastname),
                            
                            $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename,
                            userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime),
                            $session->duration,
                            userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime),
                            StateSession::getStateName($session->state),
                            '<a href="'.$logsurl.'">' . get_string('showlogs', 'block_supervised') . '</a>',
                            ($session->state ==  StateSession::Planned) ? ($iconedit . $icondelete) : ('')
                        );
}
$addurl = new moodle_url('/blocks/supervised/sessions/addedit.php', array('courseid' => $courseid));
echo ('<a href="'.$addurl.'">' . get_string('plansession', 'block_supervised') . '</a>');

// Build table.
$table = new html_table();
// Prepare headers.
$headcourse         = get_string('course', 'block_supervised');
$headclassroom      = get_string('classroom', 'block_supervised');
$headgroup          = get_string('group', 'block_supervised');
$headteacher        = get_string('teacher', 'block_supervised');
$headlessontype     = get_string('lessontype', 'block_supervised');
$headtimestart      = get_string('timestart', 'block_supervised');
$headduration       = get_string('duration', 'block_supervised');
$headtimeend        = get_string('timeend', 'block_supervised');
$headstate          = get_string('state', 'block_supervised');
$headlogs           = get_string('logs', 'block_supervised');
$headedit           = get_string('edit');


$table->head = array($headcourse, $headclassroom, $headgroup, $headteacher, $headlessontype, $headtimestart, $headduration, $headtimeend, $headstate, $headlogs, $headedit);
$table->data = $tabledata;
echo html_writer::table($table);

// Display footer.
echo $OUTPUT->footer();

?>