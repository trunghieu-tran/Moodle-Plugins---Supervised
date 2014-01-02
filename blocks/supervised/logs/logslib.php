<?php

function supervisedblock_build_logs_array($sessionid, $timefrom, $timeto, $userid, $limitfrom, $limitnum) {
    global $DB;

    $session = $DB->get_record('block_supervised_session', array('id'=>$sessionid));
    //$classroom = $DB->get_record('block_supervised_classroom', array('id'=>$session->classroomid)); // todo remove with filtering

    // Prepare query
    $params = array();
    $selector = "(l.time BETWEEN :timefrom AND :timeto) AND l.course = :courseid";
    $params['timefrom'] = $timefrom;
    $params['timeto']   = $timeto;
    $params['courseid'] = $session->courseid;
    if($userid != 0) {
        $selector .= " AND l.userid = :userid";
        $params['userid'] = $userid;
    }
    // Get logs
    $logs = get_logs($selector, $params, 'l.time DESC', '', '', $totalcount);

    // Filter logs by classroom ip subnet
    $logs_filtered = $logs; // TODO Do we really need this filtering?
    /*$logs_filtered = array();
    foreach ($logs as $id=>$log) {
        echo($log->ip);
        if(address_in_subnet($log->ip, $classroom->iplist))
            $logs_filtered[$id] = $log;
    }*/

    $result['logs'] = array_slice($logs_filtered, $limitfrom, $limitnum);
    $result['totalcount'] = $totalcount;

    return $result;
}

function supervisedblock_print_logs($sessionid, $timefrom, $timeto, $userid=0, $page=0, $perpage=50, $url=""){
    global $OUTPUT;

    $logs = supervisedblock_build_logs_array($sessionid, $timefrom, $timeto, $userid, $page*$perpage, $perpage);
    $totalcount = $logs['totalcount'];

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    $table = new html_table();
    $table->classes = array('logtable','generaltable');
    $table->align = array('right', 'left', 'left');
    $table->head = array(
        get_string('time'),
        get_string('ip_address'),
        get_string('fullnameuser'),
        get_string('action'),
        get_string('info')
    );
    $table->data = array();


    $strftimedatetime = get_string("strftimerecent");
    foreach ($logs['logs'] as $log) {
        // If $log->url has been trimmed short by the db size restriction
        // code in add_to_log, keep a note so we don't add a link to a broken url
        $brokenurl=(textlib::strlen($log->url)==100 && textlib::substr($log->url,97)=='...');

        $row = array();

        $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);

        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 440, 'width' => 700)));

        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log));

        $displayaction="$log->module $log->action";
        if ($brokenurl) {
            $row[] = $displayaction;
        } else {
            $link = make_log_url($log->module,$log->url);
            $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'), array('height' => 440, 'width' => 700));
        }
        $row[] = $log->info;
        $table->data[] = $row;
    }


    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}



function print_session_info_form($sessionid){
    global $DB;
    // Prepare session info form.
    $mform = "viewsession_form.php";
    if (file_exists($mform)) {
        require_once($mform);
    } else {
        print_error('noformdesc');
    }
    $mform = new viewsession_form();
    // TODO use get_session from sessions/lib.php
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
}