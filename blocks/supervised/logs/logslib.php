<?php

function supervisedblock_get_logs($sessionid, $timefrom, $timeto, $userid=0) {
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

    return $logs_filtered;
}