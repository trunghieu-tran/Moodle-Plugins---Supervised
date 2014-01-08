<?php

/**
 * Returns true if the user can safely delete passed lesson type
 *
 * @param $lessontypeid integer id of the lesson type
 * @return bool true if the user can delete lesson type
 */
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

/**
 * Returns true if the user can safely delete passed classroom
 *
 * @param $classroomid integer id of the classroom
 * @return bool true if the user can delete classroom
 */
function can_delete_classroom($classroomid) {
    global $DB;
    // Can not remove classroom used in session(s)
    return ! $DB->record_exists('block_supervised_session', array('classroomid'=>$classroomid));
}

/**
 * Returns true if the user can show or hide passed classroom
 * @param $classroomid integer id of the classroom
 * @return bool true if the user can show or hide passed classroom
 */
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


/**
 * Returns true if the session(s) exist
 *
 * @param $teacherid integer id of the teacher
 * @param $timestart integer the session must be started after this time
 * @param $timeend integer the session must be ended before this time
 * @param null $sessionid integer the session id
 * @return bool true if the session(s) exist
 */
function session_exists($teacherid, $timestart, $timeend, $sessionid=NULL){
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


/**
 * Delete lesson types and session from the course if the course has been deleted
 *
 * @param $course integer course id
 */
function event_handler_course_deleted($course){
    global $DB;
    $DB->delete_records('block_supervised_lessontype', array('courseid'=>$course->id));
    $DB->delete_records('block_supervised_session', array('courseid'=>$course->id));
}

/**
 * Delete lesson types and session from the course if the course content has been deleted
 *
 * @param $course integer course id
 */
function event_handler_course_content_removed($course){
    global $DB;
    $DB->delete_records('block_supervised_lessontype', array('courseid'=>$course->id));
    $DB->delete_records('block_supervised_session', array('courseid'=>$course->id));
}