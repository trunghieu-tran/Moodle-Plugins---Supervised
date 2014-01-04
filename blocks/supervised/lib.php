<?php
function can_delete_lessontype($lessontypeid) {
    global $DB;

    // Can not remove lessontype used in session(s)
    if($DB->record_exists('block_supervised_session', array('lessontypeid'=>$lessontypeid))){
        return false;
    }

    // Can not remove lessontype used in quiz_access_rules table
    if($DB->record_exists('quizaccess_supervisedcheck', array('lessontypeid'=>$lessontypeid))){
        return false;
    }

    return true;
}

function can_delete_classroom($classroomid) {
    global $DB;
    // Can not remove classroom used in session(s)
    return ! $DB->record_exists('block_supervised_session', array('classroomid'=>$classroomid));
}

function can_showhide_classroom($classroomid) {
    require_once('sessions/sessionstate.php');
    global $DB;
    // Can not showhide classroom used in active session(s)
    return ! $DB->record_exists('block_supervised_session', array('classroomid'=>$classroomid, 'state'=>StateSession::Active));
}


/**
 * Get the information about the supervised JavaScript module.
 * @return array a standard jsmodule structure.
 */
function supervised_get_js_module() {
    return array(
        'name' => 'block_quiz',
        'fullpath' => '/blocks/supervised/module.js',
        'requires' => array('base', 'dom', 'event-delegate', 'event-key',
            'core_question_engine', 'moodle-core-formchangechecker'),
    );
}


function teacher_session_exists($teacherid, $timestart, $timeend, $sessionid=NULL){
    require_once('sessions/sessionstate.php');
    global $DB;

    // Find Active session.
    $select = "SELECT * FROM {block_supervised_session}
        WHERE ((:timestart BETWEEN {block_supervised_session}.timestart AND {block_supervised_session}.timeend)
                || (:timeend BETWEEN {block_supervised_session}.timestart AND {block_supervised_session}.timeend)
                || ( ({block_supervised_session}.timestart BETWEEN :timestart1 AND :timeend1)
                    AND ({block_supervised_session}.timeend BETWEEN :timestart2 AND :timeend2)
                   ))
            AND {block_supervised_session}.teacherid    = :teacherid
            AND ({block_supervised_session}.state       = :stateactive || {block_supervised_session}.state  = :stateplanned)
            AND {block_supervised_session}.id           != :sessionid
        ";

    $params['timestart']        = $timestart;
    $params['timestart1']       = $timestart;
    $params['timestart2']       = $timestart;
    $params['timeend']          = $timeend;
    $params['timeend1']         = $timeend;
    $params['timeend2']         = $timeend;
    $params['teacherid']        = $teacherid;
    $params['stateactive']      = StateSession::Active;
    $params['stateplanned']     = StateSession::Planned;
    $params['sessionid']        = $sessionid;

    return $DB->record_exists_sql($select, $params);
}


function event_handler_course_deleted($course){
    global $DB;
    $DB->delete_records('block_supervised_lessontype', array('courseid'=>$course->id));
    $DB->delete_records('block_supervised_session', array('courseid'=>$course->id));
}

function event_handler_course_content_removed($course){
    global $DB;
    $DB->delete_records('block_supervised_lessontype', array('courseid'=>$course->id));
    $DB->delete_records('block_supervised_session', array('courseid'=>$course->id));
}