<?php

function supervisedblock_build_logs_array($sessionid, $timefrom, $timeto, $userid, $limitfrom, $limitnum) {
    global $DB;

    $session = $DB->get_record('block_supervised_session', array('id'=>$sessionid));
    $classroom = $DB->get_record('block_supervised_classroom', array('id'=>$session->classroomid));

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
    $logs = get_logs($selector, $params);

    // Filter logs by classroom ip subnet
    $logs_filtered = array();
    foreach ($logs as $id=>$log) {
        if(address_in_subnet($log->ip, $classroom->iplist))
            $logs_filtered[$id] = $log;
    }

    $result['logs'] = array_slice($logs_filtered, $limitfrom, $limitnum);
    $result['totalcount'] = count($logs_filtered);

    return $result;
}

function supervisedblock_print_logs($sessionid, $timefrom, $timeto, $userid=0, $page=0, $perpage=3, $url=""){
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