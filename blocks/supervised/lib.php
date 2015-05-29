<?php
/**
 * Have a lesson in classromm with number 'number'
 * @param $courseid Id for current course
 * @param $groupid  Id group
 * @param $number   Number of classroom
 * @param $userid   User id
 */
function have_classperiod($courseid, $number = '', $userid='') {
    global $DB, $USER;

    // if not specified the number of classromm
    // get number of current ip
    if ($number == '') {
        $ip = getremoteaddr();

        $classroom = $DB->get_record_sql('SELECT `number`, `id` FROM {block_supervised_classroom} WHERE `initialvalueip` <= ? and `finishvalueip` >= ?', array($ip, $ip));
        $number = $classroom->number;
    }
    // if not specified id user
    // get id current user
    if ($userid == '') {
        $userid = $USER->id;
    }

    // get context for user with id $userid
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
    if (is_enrolled($coursecontext, $userid)) {
        //$classroomid = $DB->get_record('block_supervised_classroom', array('number'=>$number), 'id');
        $classroomid = $DB->get_record_select('block_supervised_classroom', 'number=:number', array('number'=>$number), 'id');
        if($classroomid != '') {
            //$lesson = $DB->get_record("block_supervised", array('groupid'=>$groupid, 'courseid'=>$courseid, 'typeaction'=>'startsession', 'classroomid'=>$classroomid->id, 'twinkeyid'=>'-1'), '*');
            //$lesson = $DB->get_record("block_supervised", array('courseid'=>$courseid, 'typeaction'=>'startsession', 'classroomid'=>$classroomid->id, 'twinkeyid'=>'-1'), '*');
            $lesson = $DB->get_record_select('block_supervised', 'courseid=:courseid and typeaction=:typeaction and classroomid=:classroomid and twinkeyid=:twinkeyid', array('courseid'=>$courseid, 'typeaction'=>'startsession', 'classroomid'=>$classroomid->id, 'twinkeyid'=>'-1'));
            if ($lesson != '') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * returns a record with a lesson in the classroom started with the number $number.
 * return null if havn't a lesson in the classromm started with the number $number
 *
 * @param $courseid - id of course 
 * @param $number - number of classroom
 */
function get_start_classperiod($courseid, $number='') {
    global $DB;

    if ($number == '') {
        $ip = getremoteaddr();

        $classroom = $DB->get_record_sql('SELECT `number`, `id` FROM {block_supervised_classroom} WHERE `initialvalueip` <= ? and `finishvalueip` >= ?', array($ip, $ip));
        $number = $classroom->number;
    }

    if (have_classperiod($courseid, $number)) {
        // have lesson
        //$classroomid = $DB->get_record('block_supervised_classroom', array('number'=>$number), 'id');
        $classroomid = $DB->get_record_select('block_supervised_classroom', 'number=:number', array('number'=>$number), 'id');
        //$lesson = $DB->get_record("block_supervised", array('courseid'=>$courseid, 'typeaction'=>'startsession', 'classroomid'=>$classroomid->id, 'twinkeyid'=>'-1'), '*');
        $lesson = $DB->get_record_select('block_supervised', 'courseid=:courseid and typeaction=:typeaction and classroomid=:classroomid and twinkeyid=:twinkeyid', array('courseid'=>$courseid, 'typeaction'=>'startsession', 'classroomid'=>$classroomid->id, 'twinkeyid'=>'-1'));
        return $lesson;
    } else {
        // havn't lesson
        return null;
    }
}

/**
 * fucntion return number of current classroom
 * return @param array array['number']  number of classroom; array['id'] id of classroom in table database
 */
function get_current_classroom() {
    global $DB;
    $ip = getremoteaddr();
    $classroom = $DB->get_record_sql("SELECT `number`, `id` FROM {block_supervised_classroom} WHERE `initialvalueip` <= '" . $ip . "' and `finishvalueip` >= '" . $ip ."'");
    return array('id'=>$classroom->id, 'number'=>$classroom->number);
}

/*
 * function return id group for user id
 * param @userid id for users
 * return @param object $groupid
 */
function get_id_group_for_user($userid) {
    global $DB;
    $groupid = $DB->get_record('groups_members', array('userid'=>$userid), 'groupid');
    return $groupid->groupid;
}

/*
 * function return until the end of classperiod
 * param @classperiod current classperiod
 * return @param object $endtime
 */
function get_time_end_classperiod($classperiod) {
    global $DB;
    $currenttime = time();
    if ($classperiod->starttimework < $currenttime) {
        return 0;
    } else {
        $endtime = $classperiod->starttimework + $classperiod->timework;
        return $endtime - $currenttime;
    }
}