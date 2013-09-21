<?php
function start_lesson_input($eventdata)
{
    require_once('../../config.php');
    global $DB, $CFG;

    // add to mdl_block_supervised
    $url = "view.php?id=" . $eventdata->idcourse;
    if ($eventdata->idcourse != SITEID) {
        $urlsupervised = $url;
    } else {
        $urlsupervised = '';
    }

    // insert record to the mdl_log
    $blockparams = array(
                            'time'=>time(),
                            'userid'=>$eventdata->idlecturer,
                            'ip'=>getremoteaddr(),
                            'course'=>$eventdata->idcourse,
                            'module'=>'supervised',
                            'cmid'=>0,
                            'action'=>'startsession',
                            'url'=>$url,
                            'info'=>$eventdata->idcourse,
                        );

    $idrec = $DB->insert_record('log', $blockparams);

    // add to mdl_supervised
    $recordsupervised = array("logid"=>$idrec, 
                                "classroomid"=>$eventdata->classroom, 
                                "courseid"=>$eventdata->idcourse,
                                "groupid"=>$eventdata->numbergroup,
                                "starttimework"=>$eventdata->starttime,
                                "timework"=>$eventdata->duration,
                                "lecturerid"=>$eventdata->idlecturer,
                                "typeaction"=>"startsession",
                                "twinkeyid"=>-1);
    
    $DB->insert_record('block_supervised', $recordsupervised);
    redirect($eventdata->redirect);
}

// end lessons 
function end_lesson_input($eventdata)
{
    global $DB, $CFG;

    $url = "view.php?id=" . $eventdata->idcourse;
    if ($eventdata->idcourse != SITEID) {
        $urlsupervised = $url;
    } else {
        $urlsupervised = '';
    }
    
    add_to_log($eventdata->idcourse, 'course',         'view',     $url,              $eventdata->idlecturer, 0, $eventdata->idlecturer);
    // insert record to the mdl_log
    $blockparams = array(
                            'time'=>time(),
                            'userid'=>$eventdata->idlecturer,
                            'ip'=>getremoteaddr(),
                            'course'=>$eventdata->idcourse,
                            'module'=>'supervised',
                            'cmid'=>0,
                            'action'=>'endsession',
                            'url'=>$url,
                            'info'=>$eventdata->idcourse,
                        );
    
    $idrec = $DB->insert_record('log', $blockparams);
    
    // add to mdl_supervised
    $startrecord = $DB->get_record('block_supervised', array('id'=>$eventdata->startid), '*', MUST_EXIST);
    $recordsupervised = array("logid"=>$idrec, 
                                "classroomid"=>$startrecord->classroomid, 
                                "courseid"=>$startrecord->courseid, 
                                "groupid"=>$startrecord->groupid, 
                                "starttimework"=>$startrecord->starttimework,
                                "timework"=>$startrecord->timework,
                                "lecturerid"=>$startrecord->lecturerid,
                                "typeaction"=>"endsession",
                                "twinkeyid"=>$startrecord->id);
    $endid = $DB->insert_record('block_supervised', $recordsupervised);
    $updatelesson = array(
                        'id'=>$startrecord->id,
                        'logid'=>$startrecord->logid,
                        'classroomid'=>$startrecord->classroomid,
                        'courseid'=>$startrecord->courseid,
                        'groupid'=>$startrecord->groupid,
                        'starttimework'=>$startrecord->starttimework,
                        'timework'=>$startrecord->timework,
                        'lecturerid'=>$startrecord->lecturerid,
                        'typeaction'=>$startrecord->typeaction,
                        'twinkeyid'=>$endid
                        );
    $DB->update_record('block_supervised', $updatelesson);
}

// start quiz
function quiz_start_lesson($eventdata)
{
    global $DB;

    // get log id 
    $logid = $DB->get_record('log', array('action'=>'attempt', 'url'=>'review.php?attempt='.$eventdata->attempt), 'id', MUST_EXIST);
    $lesson = get_start_classperiod($eventdata->course);
    if ($lesson != null) {
        // write to block_supevision    
        $attempstart = array(
                            'logid'=>$logid->id,
                            'classroomid'=>$lesson->classroomid,
                            'courseid'=>$lesson->courseid,
                            'groupid'=>$lesson->groupid,
                            'starttimework'=>$lesson->starttimework,
                            'timework'=>$lesson->timework,
                            'lecturerid'=>$lesson->lecturerid,
                            'typeaction'=>'attempt',
                            'twinkeyid'=>$lesson->id
                            );
    
        $DB->insert_record('block_supervised', $attempstart); 
    }
}
// end quiz
function quiz_processed_lesson($eventdata)
{
    global $DB, $CFG;
    require_once($CFG->dirroot.  '/blocks/supervised/lib.php');
    
    // get log id 
    $logid = $DB->get_record('log', array('action'=>'close attempt', 'url'=>'review.php?attempt='.$eventdata->attempt), 'id', MUST_EXIST);
    $lesson = get_start_classperiod($eventdata->course);
    if ($lesson != null) {
        // write to block_supevision    
        $attemptend = array(
                            'logid'=>$logid->id,
                            'classroomid'=>$lesson->classroomid,
                            'courseid'=>$lesson->courseid,
                            'groupid'=>$lesson->groupid,
                            'starttimework'=>$lesson->starttimework,
                            'timework'=>$lesson->timework,
                            'lecturerid'=>$lesson->lecturerid,
                            'typeaction'=>'close attempt',
                            'twinkeyid'=>$lesson->id
                            );

        $DB->insert_record('block_supervised', $attemptend); 
    }
}