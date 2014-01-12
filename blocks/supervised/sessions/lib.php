<?php

/**
 * Returns the sessions array according to specified conditions and capabilities of current user
 *
 * @param int $limitfrom sessions from the specified index
 * @param int $limitnum specified number of sessions
 * @param int $from session starts after this time
 * @param int $to session ends before this time
 * @param int $teacher teacher id
 * @param int $course course id
 * @param int $classroom classroom id
 * @param int $lessontype lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state session state
 * @return mixed array of the sessions
 */
function build_sessions_array($limitfrom, $limitnum, $from, $to, $teacher=0, $course=0, $classroom=0, $lessontype=-1, $state=0){
    global $USER, $PAGE;

    $sessions = get_sessions($course, $teacher, $classroom, $lessontype, $state, $from, 0, 0, $to);

    // Filter sessions according to user capabilities.
    $sessionsfiltered = array();
    foreach ($sessions as $session) {
        // If user can't supervise access to session's course -> miss current session.
        $coursecontext = context_course::instance($session->courseid);
        if(has_capability('block/supervised:supervise', $coursecontext)){
            // Now check if user can view current session
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
    }

    $result["sessions"] = array_slice($sessionsfiltered, $limitfrom, $limitnum);
    $result["totalcount"] = count($sessionsfiltered);
    return $result;
}


/**
 * Output sessions with pagination according to specified conditions
 *
 * @param int $pagenum current page index
 * @param int $perpage number of sessions per page
 * @param string $url the url prefix for pages
 * @param int $from session starts after this time
 * @param int $to session ends before this time
 * @param int $teacher teacher id
 * @param int $course course id
 * @param int $classroom classroom id
 * @param int $lessontype lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state session state
 */
function print_sessions($pagenum=0, $perpage=50, $url, $from, $to, $teacher=0, $course=0, $classroom=0, $lessontype=-1, $state=0){
    global $OUTPUT, $USER, $PAGE;

    $sessions = build_sessions_array($pagenum*$perpage, $perpage, $from, $to, $teacher, $course, $classroom, $lessontype, $state);
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
        $tablerow = array(
            html_writer::link(new moodle_url("/course/view.php?id={$session->courseid}"), $session->coursename),
            $session->classroomname,
            $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname,
            html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), fullname($session)),
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


/**
 * Returns an array of sessions according to specified conditions
 *
 * @param int $courseid course id
 * @param int $teacherid teacher id
 * @param int $classroomid classroom id
 * @param int $lessontypeid lessontype id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state    session state
 * @param int $timestart1 session must starts after this time
 * @param int $timestart2 session must starts before this time
 * @param int $timeend1 session must ends after this time
 * @param int $timeend2 session must ends before this time
 * @param int $id session id
 * @return array sessions
 */
function get_sessions($courseid=0, $teacherid=0, $classroomid=0, $lessontypeid=-1, $state=0, $timestart1=0, $timestart2=0, $timeend1=0, $timeend2=0, $id=0){
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
        ";

    $whereflag = false;
    if($courseid){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.courseid = :courseid"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.courseid = :courseid";}
        $params['courseid']      = $courseid;
    }
    if($teacherid){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.teacherid = :teacherid"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.teacherid = :teacherid";}
        $params['teacherid']      = $teacherid;
    }
    if($classroomid){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.classroomid = :classroomid"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.classroomid = :classroomid";}
        $params['classroomid']      = $classroomid;
    }
    if($lessontypeid != -1){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.lessontypeid = :lessontypeid"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.lessontypeid = :lessontypeid";}
        $params['lessontypeid']      = $lessontypeid;
    }
    if($state){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.state = :state"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.state = :state";}
        $params['state']      = $state;
    }
    if($timestart1){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.timestart >= :timestart1"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.timestart >= :timestart1";}
        $params['timestart1']      = $timestart1;
    }
    if($timestart2){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.timestart <= :timestart2"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.timestart <= :timestart2";}
        $params['timestart2']      = $timestart2;
    }
    if($timeend1){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.timeend >= :timeend1"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.timeend >= :timeend1";}
        $params['timeend1']      = $timeend1;
    }
    if($timeend2){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.timeend <= :timeend2"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.timeend <= :timeend2";}
        $params['timeend2']      = $timeend2;
    }
    if($id){
        if($whereflag)  {$select .= " WHERE {block_supervised_session}.id = :id"; $whereflag=true;}
        else            {$select .= " AND {block_supervised_session}.id = :id";}
        $params['id']      = $id;
    }

    return $DB->get_records_sql($select, $params);
}

/**
 * Returns one session according to specified conditions
 *
 * @param int $courseid course id
 * @param int $teacherid teacher id
 * @param int $classroomid classroom id
 * @param int $lessontypeid lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state    session state
 * @param int $timestart1 session must starts after this time
 * @param int $timestart2 session must starts before this time
 * @param int $timeend1 session must ends after this time
 * @param int $timeend2 session must ends before this time
 * @param int $id session id
 * @return stdClass session
 */
function get_session($id=0, $courseid=0, $teacherid=0, $classroomid=0, $lessontypeid=-1, $state=0, $timestart1=0, $timestart2=0, $timeend1=0, $timeend2=0){
    $records = get_sessions($courseid, $teacherid, $classroomid, $lessontypeid, $state, $timestart1, $timestart2, $timeend1, $timeend2, $id);
    return array_shift($records); // Return the first element.
}

/**
 * Send e-mail to teacher about creation of a new session
 *
 * @param $session stdClass created session
 * @param $creator stdClass user who created the session
 */
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

/**
 * Send e-mail to teacher about removing of the session
 * @param $session stdClass removed session
 * @param $remover stdClass user who removed this session
 */
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


/**
 * Send e-mail to teacher about editing of the session
 * @param $updsession stdClass edited session
 * @param $editor stdClass user who edited this session
 */
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