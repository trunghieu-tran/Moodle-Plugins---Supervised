<?php

function build_sessions_array($limitfrom, $limitnum){
    global $DB, $USER, $PAGE;

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

    // Filter sessions according to user capabilities.
    $sessionsfiltered = array();
    foreach ($sessions as $session) {
        // Trying to add created row into table.
        if($session->teacherid != $USER->id){
            // Check if user has capability to view other user's sessions.
            if(   has_capability('block/supervised:viewallsessions', $PAGE->context) || has_capability('block/supervised:manageallsessions', $PAGE->context)   ){
                $sessionsfiltered[] = $session;
            }
        }
        else{
            // User can view his own sessions (already checked).
            $sessionsfiltered[] = $session;
        }
    }

    $result["sessions"] = array_slice($sessionsfiltered, $limitfrom, $limitnum);
    $result["totalcount"] = count($sessionsfiltered);
    return $result;
}



function print_sessions($pagenum=0, $perpage=50, $url=""){
    global $OUTPUT, $USER, $PAGE;

    $sessions = build_sessions_array($pagenum*$perpage, $perpage);
    $totalcount = $sessions['totalcount'];

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $pagenum, $perpage, "$url&perpage=$perpage");


    // Fill table with sessions according to user capabilities.
    $strftimedatetime = get_string("strftimerecent");
    $tabledata = array();
    foreach ($sessions["sessions"] as $session) {
        $logsurl = new moodle_url('/blocks/supervised/logs/view.php', array('sessionid' => $session->id, 'courseid' => $session->courseid));
        $logslink = '<a href="'.$logsurl.'">' . get_string('showlogs', 'block_supervised') . '</a>';


        // Combine new row.
        $tablerow = array(   $session->coursename,
            $session->classroomname,
            $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname,

            html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), $session->firstname . " " . $session->lastname),

            $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename,
            userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime),
            $session->duration,
            userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime),
            StateSession::getStateName($session->state),
            ($session->state !=  StateSession::Planned) ? $logslink : ('')
        );

        // Build edit icon.
        $iconedit = '';
        if($session->state ==  StateSession::Planned){
            if(  ($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
                || has_capability('block/supervised:manageallsessions', $PAGE->context) ){
                $editurl        = new moodle_url('/blocks/supervised/sessions/addedit.php', array('id' => $session->id, 'courseid' => $session->courseid));
                $iconedit       = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
            }
        }
        // Build delete icon.
        $icondelete = '';
        if(
            ($session->state ==  StateSession::Planned && $session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
            || ($session->state ==  StateSession::Planned && has_capability('block/supervised:manageallsessions', $PAGE->context))
            || ($session->state ==  StateSession::Finished && has_capability('block/supervised:managefinishedsessions', $PAGE->context))
        ){
            $deleteurl      = new moodle_url('/blocks/supervised/sessions/delete.php', array('courseid' => $session->courseid, 'id' => $session->id));
            $icondelete     = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
        }

        $tablerow[] = $iconedit . $icondelete;
        $tabledata[] = $tablerow;
    }


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
    echo $OUTPUT->paging_bar($totalcount, $pagenum, $perpage, "$url&perpage=$perpage");
}



function get_session($id){
    global $DB;

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

    return $DB->get_record_sql($select, $params);
}