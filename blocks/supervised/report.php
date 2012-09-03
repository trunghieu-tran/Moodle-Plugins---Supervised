<?php
require_once('../../config.php');
require_once('../../course/lib.php');
//require_once($CFG->libdir.'/formslib.php');
define('COUNTRECORDSLOG', 30);
define('COUNTRECORDS', 10);
$countreclogs = 30; // define
require_login();

$strviewfeed = get_string('report');
$PAGE->set_pagelayout('standard');
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
//$PAGE->set_context($context);
require_capability('block/supervised:changeclassroom', $context);
$PAGE->set_context($context);
$PAGE->set_title($strviewfeed);
$PAGE->set_heading($strviewfeed);
$PAGE->set_url('/blocks/supervised/report.php');

function print_log_supervised($idlogstart, $idlogend, $lecturer, $course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {

    global $CFG, $DB, $OUTPUT;
    $logs['logs'] = $DB->get_records_sql('SELECT * FROM {log} WHERE id >= :start and id <= :end', array('start'=>$idlogstart, 'end'=>$idlogend), $page*$perpage, $perpage);

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    //$totalcount = $logs['totalcount'];
    $total = $DB->get_record_sql('select count(id) as countlogs from {log} where id >= :logstart and id <= :logend', array('logstart'=>$idlogstart, 'logend'=>$idlogend));
    $totalcount = $total->countlogs;

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
    
    $table = new html_table();
    $table->align = array('right', 'left', 'left');
    $table->head = array(
        get_string('time', 'block_supervised'),
        get_string('ipaddress', 'block_supervised'),
        get_string('teacher', 'block_supervised'),
        get_string('action', 'block_supervised'),
        get_string('info', 'block_supervised'),
        get_string('supervised', 'block_supervised')
    );
    $table->width = '100%';
    $table->data = array();

    if ($course->id == SITEID) {
        array_unshift($table->align, 'left');
        array_unshift($table->head, get_string('course'));
    }

    // Make sure that the logs array is an array, even it is empty, to avoid warnings from the foreach.
    if (empty($logs['logs'])) {
        $logs['logs'] = array();
    }
    $lecturername = $DB->get_record('user', array('id'=>$lecturer), 'firstname, lastname');
    foreach ($logs['logs'] as $log) {
        if ($idlogend != -1) {
            if ($log->id >= $idlogstart && $log->id <= $idlogend ) {
                if (isset($ldcache[$log->module][$log->action])) {
                    $ld = $ldcache[$log->module][$log->action];
                } else {
                    $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
                    $ldcache[$log->module][$log->action] = $ld;
                }
                if ($ld && is_numeric($log->info)) {
                    // ugly hack to make sure fullname is shown correctly
                    if ($ld->mtable == 'user' && $ld->field == $DB->sql_concat('firstname', "' '" , 'lastname')) {
                        $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
                    } else {
                        $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
                    }
                }

                //Filter log->info
                $log->info = format_string($log->info);

                // If $log->url has been trimmed short by the db size restriction
                // code in add_to_log, keep a note so we don't add a link to a broken url
                $tl=textlib_get_instance();
                $brokenurl=($tl->strlen($log->url)==100 && $tl->substr($log->url,97)=='...');

                $row = array();
                if ($course->id == SITEID) {
                    if (empty($log->course)) {
                        $row[] = get_string('site');
                    } else {
                        $row[] = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">". format_string($courses[$log->course])."</a>";
                    }
                }

                $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);

                $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
                $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 440, 'width' => 700)));

                $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id))));

                $displayaction="$log->module $log->action";
                if ($brokenurl) {
                    $row[] = $displayaction;
                } else {
                    $link = make_log_url($log->module,$log->url);
                    $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'), array('height' => 440, 'width' => 700));
                }
                $row[] = $log->info;

                $row[] = html_writer::link(
                                new moodle_url("/user/view.php?id=$lecturer&course={$log->course}"), 
                                $lecturername->firstname . ' ' . $lecturername->lastname
                                );
                $table->data[] = $row;
            }
        } else {
            if ($log->id >= $idlogstart ) {
                if (isset($ldcache[$log->module][$log->action])) {
                    $ld = $ldcache[$log->module][$log->action];
                } else {
                    $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
                    $ldcache[$log->module][$log->action] = $ld;
                }
                if ($ld && is_numeric($log->info)) {
                    // ugly hack to make sure fullname is shown correctly
                    if ($ld->mtable == 'user' && $ld->field == $DB->sql_concat('firstname', "' '" , 'lastname')) {
                        $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
                    } else {
                        $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
                    }
                }

                //Filter log->info
                $log->info = format_string($log->info);

                // If $log->url has been trimmed short by the db size restriction
                // code in add_to_log, keep a note so we don't add a link to a broken url
                $tl=textlib_get_instance();
                $brokenurl=($tl->strlen($log->url)==100 && $tl->substr($log->url,97)=='...');

                $row = array();
                if ($course->id == SITEID) {
                    if (empty($log->course)) {
                        $row[] = get_string('site');
                    } else {
                        $row[] = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">". format_string($courses[$log->course])."</a>";
                    }
                }

                $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);

                $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
                $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 440, 'width' => 700)));

                $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id))));

                $displayaction="$log->module $log->action";
                if ($brokenurl) {
                    $row[] = $displayaction;
                } else {
                    $link = make_log_url($log->module,$log->url);
                    $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'), array('height' => 440, 'width' => 700));
                }
                $row[] = $log->info;

                $row[] = html_writer::link(
                                new moodle_url("/user/view.php?id=$lecturer&course={$log->course}"), 
                                $lecturername->firstname . ' ' . $lecturername->lastname
                                );
                $table->data[] = $row;
            }
        }
    }

    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}

/*
 * all classperiod for the selected date
 */
function get_classperiod($date) {
    global $DB;
    $returnrecords = array();
    $userdata = date("d m Y ", $date);
    $day = date("d", $date);
    $month = date("m", $date);
    $year = date("Y", $date);
    $userdatastart = mktime(0, 0, 0, $month, $day, $year);
    $userdataend = mktime(23, 59, 59, $month, $day, $year);

    $logsupervised = $DB->get_records_sql('SELECT `starttimework`, `logid`, `lecturerid`, `courseid`, `twinkeyid` FROM {block_supervised} WHERE starttimework<=? and starttimework>=? and typeaction=?', array($userdataend, $userdatastart, 'startsession'));
    foreach ($logsupervised as $log) {
        $idend = $DB->get_record('block_supervised', array('id'=>$log->twinkeyid), 'logid');
        //$line = array('logid'=>$log->logid, 'lecturerid'=>$log->lecturerid);
        if ($idend != null) {
            $line = array('startclassperiodlog'=>$log->logid, 'endclassperiodlog'=>$idend->logid, 'lecturerid'=>$log->lecturerid, 'time'=>$log->starttimework, 'courseid'=>$log->courseid);
        } else {
            $line = array('startclassperiodlog'=>$log->logid, 'endclassperiodlog'=>'-1', 'lecturerid'=>$log->lecturerid, 'time'=>$log->starttimework, 'courseid'=>$log->courseid);
        }
        $returnrecords[] = $line;
    }

    return $returnrecords;
}
function get_all_classperiod($page, $perpage) {
    global $DB;
    $returnrecords = array();

    $logsupervised = $DB->get_records_sql('SELECT `id`, `starttimework`, `logid`, `lecturerid`, `courseid`, `twinkeyid` FROM {block_supervised} WHERE typeaction=?', array('startsession'), $page*$perpage, $perpage);
    foreach ($logsupervised as $log) {
        $idend = $DB->get_record('block_supervised', array('id'=>$log->twinkeyid), 'logid');
        if ($idend != null) {
            $line = array('startclassperiodlog'=>$log->logid, 'endclassperiodlog'=>$idend->logid, 'lecturerid'=>$log->lecturerid, 'time'=>$log->starttimework, 'courseid'=>$log->courseid, 'id'=>$log->id);
        } else {
            $line = array('startclassperiodlog'=>$log->logid, 'endclassperiodlog'=>'-1', 'lecturerid'=>$log->lecturerid, 'time'=>$log->starttimework, 'courseid'=>$log->courseid, 'id'=>$log->id);
        }
        $returnrecords[] = $line;
    }

    return $returnrecords;
}

$logstart = optional_param('startclassperiodlog', -1, PARAM_INT);
$logend = optional_param('endclassperiodlog', -1, PARAM_INT);
$lecturer = optional_param('lecturer', -1, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$classperiodpage = optional_param('classperiodpage', 0, PARAM_INT);
$classperiodperpage = optional_param('classperiodperpage', COUNTRECORDS, PARAM_INT);



$date = time();
//$array1 = get_classperiod($date);
$array = get_all_classperiod($classperiodpage, $classperiodperpage);

$table = new html_table();
$table->head = array(get_string('number', 'block_supervised'), get_string('reportstarttime', 'block_supervised'), get_string('reportendtime', 'block_supervised'), get_string('labelduration', 'block_supervised'), get_string('fullcoursename', 'block_supervised'), get_string('supervised', 'block_supervised'));
$table->align = array('center', 'center', 'center', 'center');
$table->width = '100%';
$i=1;

foreach ($array as $log) {
    $startclassperiodlog = $log['startclassperiodlog'];
    $endclassperiodlog = $log['endclassperiodlog'];
    $lecturerid = $log['lecturerid'];
    $starttime = $log['time'];
    $duration = $DB->get_field_select('block_supervised', 'timework', 'id=:id', array('id'=>$log['id']));
    $endtime = $log['time'] + $duration * 60;

    // format link for time
    if ($logstart == $startclassperiodlog && $logend == $endclassperiodlog && $lecturer == $lecturerid) {
        $starttime = userdate($starttime);
        $endtime = userdate($endtime);
    } else {
        $starttime = html_writer::link(
                                    new moodle_url("report.php?startclassperiodlog=$startclassperiodlog&endclassperiodlog=$endclassperiodlog&lecturer=$lecturerid&classperiodpage=$classperiodpage&classperiodperpage=$classperiodperpage"),
                                    userdate($starttime)
                                );
        $endtime = html_writer::link(
                                    new moodle_url("report.php?startclassperiodlog=$startclassperiodlog&endclassperiodlog=$endclassperiodlog&lecturer=$lecturerid&classperiodpage=$classperiodpage&classperiodperpage=$classperiodperpage"),
                                    userdate($endtime)
                                );
    }

    $namecourse = $DB->get_field('course', 'fullname', array('id'=>$log['courseid']));
    if ($log['courseid'] == SITEID) {
        $course = get_string('site');
    } else {
        $course = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$log['courseid']}\">". $namecourse ."</a>";
    }
    $lecturername = $DB->get_record('user', array('id'=>$lecturerid), 'firstname, lastname');

    $hours = 0;
    $minutes = 0;
    if ($duration > 60 ) {
        $minutes = $duration % 60;
        $hours =   ($duration - $minutes) / 60;
    } else {
        $minutes = $duration;
    }
    $time = '';
    if ($hours != 0) {
        $time = $hours . get_string('labelhours', 'block_supervised') . $minutes . ' ' . get_string('labelminutes', 'block_supervised');
    } else {
        $time = $minutes . ' ' . get_string('labelminutes', 'block_supervised');
    }
    $row = array(   $i, 
                    $starttime, 
                    $endtime,
                    $time,
                    $course, 
                    html_writer::link(
                                        new moodle_url("/user/view.php?id=$lecturer&course={$log['courseid']}"),
                                        $lecturername->firstname . ' ' . $lecturername->lastname
                                    )
                );
    $table->data[] = $row;
    $i++;
}
echo $OUTPUT->header();
echo '<H1 align="center">' . get_string('classperiodlist', 'block_supervised') . '</H1>';
echo $OUTPUT->paging_bar($i+1, $classperiodpage, $classperiodperpage, "report.php?startclassperiodlog=$logstart&endclassperiodlog=$logend&lecturer=$lecturer&classperiodperpage=$classperiodperpage", 'classperiodpage');
echo html_writer::table($table);
echo $OUTPUT->paging_bar($i+1, $classperiodpage, $classperiodperpage, "report.php?startclassperiodlog=$logstart&endclassperiodlog=$logend&lecturer=$lecturer&classperiodperpage=$classperiodperpage", 'classperiodpage');

echo '<H1 align="center">' . get_string('classperiodlistlog', 'block_supervised') . '</H1>';

if ($logstart != -1 || $logend != -1 || $lecturer != -1) { 
    print_log_supervised($logstart, $logend, $lecturer, $COURSE, 0, 0 /*$date*/, 'l.time DESC', $page, COUNTRECORDSLOG, 
                "report.php?startclassperiodlog=$logstart&endclassperiodlog=$logend&lecturer=$lecturer&classperiodpage=$classperiodpage&classperiodperpage=$classperiodperpage");

    echo '<div align="center"><a href="report.php">' . get_string('cleanall', 'block_supervised'). '</a></div>';
}

echo $OUTPUT->footer();