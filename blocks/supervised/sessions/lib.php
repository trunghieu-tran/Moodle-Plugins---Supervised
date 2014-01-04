<?php

function build_sessions_array($limitfrom, $limitnum, $from, $to, $teacher=0, $course=0, $classroom=0, $state=0){
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

        WHERE {block_supervised_session}.timestart >= :from
            AND {block_supervised_session}.timeend <= :to
    ";

    $params['from']         = $from;
    $params['to']           = $to;
    if($teacher){
        $select .= "AND {block_supervised_session}.teacherid = :teacher";
        $params['teacher']      = $teacher;
    }
    if($course){
        $select .= "AND {block_supervised_session}.courseid = :course";
        $params['course']       = $course;
    }
    if($classroom){
        $select .= "AND {block_supervised_session}.classroomid = :classroom";
        $params['classroom']    = $classroom;
    }
    if($state){
        $select .= "AND {block_supervised_session}.state = :state";
        $params['state']        = $state;
    }

    $sessions = $DB->get_records_sql($select, $params);

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



function print_sessions($pagenum=0, $perpage=50, $url, $from, $to, $teacher=0, $course=0, $classroom=0, $state=0){
    global $OUTPUT, $USER, $PAGE;

    $sessions = build_sessions_array($pagenum*$perpage, $perpage, $from, $to, $teacher, $course, $classroom, $state);
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
        {block_supervised_session}.classroomid,
        {block_supervised_session}.lessontypeid,
        {block_supervised_session}.groupid,
        {block_supervised_session}.timestart,
        {block_supervised_session}.duration,
        {block_supervised_session}.timeend,
        {block_supervised_session}.courseid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.state,
        {block_supervised_session}.sessioncomment,
        {block_supervised_session}.sendemail,
        {block_supervised_classroom}.name   AS classroomname,
        {block_supervised_lessontype}.name  AS lessontypename,
        {user}.firstname,
        {user}.lastname,
        {groups}.name                       AS groupname,
        {course}.fullname                   AS coursename

        FROM {block_supervised_session}
            JOIN {block_supervised_classroom}
              ON {block_supervised_session}.classroomid  =   {block_supervised_classroom}.id
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

function mail_newsession($session, $creator){
    global $DB, $CFG;
    $strftimedatetime = get_string("strftimerecent");

    $site        = get_site();
    $supportuser = generate_email_supportuser();
    $user        = $DB->get_record('user', array('id'=>$session->teacherid));

    $data = new stdClass();
    $data->teachername      = fullname($user);
    $data->sitename         = format_string($site->fullname);
    $data->creatorname      = fullname($creator);
    $data->course           = $session->coursename;
    $data->classroom        = $session->classroomname;
    $data->group            = $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname;
    $data->lessontype       = $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename;
    $data->timestart        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $data->duration         = $session->duration;
    $data->timeend          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    $data->editurl          = $CFG->wwwroot ."/blocks/supervised/sessions/addedit.php?courseid=$session->courseid&id=$session->id";
    $data->deleteurl        = $CFG->wwwroot ."/blocks/supervised/sessions/delete.php?courseid=$session->courseid&id=$session->id";
    if($session->sessioncomment){
        $data->comment          = get_string('emailsessioncomment', 'block_supervised', $session->sessioncomment);
    }
    else{
        $data->comment          = '';
    }


    $message    = get_string('emailnewsession', 'block_supervised', $data);
    $subject    = get_string('emailnewsessionsubject', 'block_supervised', $data);

    email_to_user($user, $supportuser, $subject, $message);
}

function mail_removedsession($session, $remover){
    global $DB, $CFG;
    $strftimedatetime = get_string("strftimerecent");

    $site        = get_site();
    $supportuser = generate_email_supportuser();
    $user        = $DB->get_record('user', array('id'=>$session->teacherid));

    $data = new stdClass();
    $data->teachername      = fullname($user);
    $data->sitename         = format_string($site->fullname);
    $data->removername      = fullname($remover);
    $data->state            = StateSession::getStateName($session->state);
    $data->course           = $session->coursename;
    $data->classroom        = $session->classroomname;
    $data->group            = $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname;
    $data->lessontype       = $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename;
    $data->timestart        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $data->duration         = $session->duration;
    $data->timeend          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    $data->comment          = $session->sessioncomment;
    if($session->sessioncomment){
        $data->comment          = get_string('emailsessioncomment', 'block_supervised', $session->sessioncomment);
    }
    else{
        $data->comment          = '';
    }
    if($session->messageforteacher){
        $data->custommessage    = get_string('emailremovedsessionmsg', 'block_supervised', $session->messageforteacher);
    }
    else{
        $data->custommessage    = '';
    }

    $message    = get_string('emailremovedsession', 'block_supervised', $data);
    $subject    = get_string('emailremovedsessionsubject', 'block_supervised', $data);

    email_to_user($user, $supportuser, $subject, $message);
}



function mail_editedsession($updsession, $editor){
    global $DB, $CFG;
    $strftimedatetime = get_string("strftimerecent");

    $site        = get_site();
    $supportuser = generate_email_supportuser();
    $user        = $DB->get_record('user', array('id'=>$updsession->teacherid));

    $data = new stdClass();
    $data->teachername      = fullname($user);
    $data->sitename         = format_string($site->fullname);
    $data->editorname       = fullname($editor);
    $data->course           = $updsession->coursename;
    $data->classroom        = $updsession->classroomname;
    $data->group            = $updsession->groupname == '' ? get_string('allgroups', 'block_supervised'): $updsession->groupname;
    $data->lessontype       = $updsession->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $updsession->lessontypename;
    $data->timestart        = userdate($updsession->timestart, '%a').' '.userdate($updsession->timestart, $strftimedatetime);
    $data->duration         = $updsession->duration;
    $data->timeend          = userdate($updsession->timeend, '%a').' '.userdate($updsession->timeend, $strftimedatetime);
    $data->editurl          = $CFG->wwwroot ."/blocks/supervised/sessions/addedit.php?courseid=$updsession->courseid&id=$updsession->id";
    $data->deleteurl        = $CFG->wwwroot ."/blocks/supervised/sessions/delete.php?courseid=$updsession->courseid&id=$updsession->id";
    if($updsession->sessioncomment){
        $data->comment          = get_string('emailsessioncomment', 'block_supervised', $updsession->sessioncomment);
    }
    else{
        $data->comment          = '';
    }


    $message    = get_string('emaileditedsession', 'block_supervised', $data);
    $subject    = get_string('emaileditedsessionsubject', 'block_supervised', $data);

    email_to_user($user, $supportuser, $subject, $message);
}