<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns logs array according to specified conditions
 *
 * @param $sessionid int session id
 * @param $timefrom int logs must be created after this time
 * @param $timeto int logs must be created before this time
 * @param $userid int user id
 * @param $limitfrom int logs from the specified index
 * @param $limitnum int specified number of logs
 * @return mixed logs array
 */
function supervisedblock_build_logs_array($sessionid, $timefrom, $timeto, $userid, $limitfrom, $limitnum) {
    global $DB;

    $session = $DB->get_record('block_supervised_session', array('id' => $sessionid));

    $params['timeto'] = $timeto;
    $params['timefrom'] = $timefrom;
    $params['courseid'] = $session->courseid;
    $logs = $DB->get_records_sql('SELECT * FROM {logstore_standard_log} WHERE
                                timecreated BETWEEN :timefrom AND :timeto AND courseid = :courseid',
                                $params);
    // Filter logs by classroom's ip subnet, student's group and user's id.
    $logsfiltered = array();
    foreach ($logs as $id => $log) {
        $teacher = $DB->record_exists_sql('SELECT * FROM {block_supervised_session} WHERE id = ? AND teacherid = ?',
                                            array($sessionid, $log->userid));
        $student = $DB->record_exists_sql('SELECT * FROM {block_supervised_user} WHERE sessionid = ? AND userid = ?',
                                            array($sessionid, $log->userid));
        if (address_in_subnet($log->ip, $session->iplist) && ($teacher || $student )
            && (($log->userid == $userid) || $userid == 0)) {
            $logsfiltered[$id] = $log;
        }
    }
    $result['logs'] = array_slice($logsfiltered, $limitfrom, $limitnum);
    $result['totalcount'] = count($logsfiltered);

    return $result;
}


/**
 * Output logs with pagination according to specified conditions
 *
 * @param $sessionid int session id
 * @param $timefrom int logs must be created after this time
 * @param $timeto int logs must be created before this time
 * @param int $userid int user id
 * @param int $page int current page
 * @param int $perpage int logs number per page
 * @param string $url the url prefix for pages
 */
function supervisedblock_print_logs($sessionid, $timefrom, $timeto, $userid=0, $page=0, $perpage=50, $url='') {
    global $OUTPUT, $DB;

    $logs = supervisedblock_build_logs_array($sessionid, $timefrom, $timeto, $userid, $page * $perpage, $perpage);
    $totalcount = $logs['totalcount'];

    echo "<div class=\"info\">\n";
    print_string('displayingrecords', '', $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    $table = new html_table();
    $table->classes = array('logtable', 'generaltable');
    $table->align = array('right', 'left', 'left');
    $table->head = array(
        get_string('time'),
        get_string('fullnameuser'),
        get_string('eventcontext', 'report_log'),
        get_string('eventname'),
        get_string('action'),
        get_string('info'),
        get_string('ip_address'),
    );
    $table->data = array();

    $strftimedatetime = get_string('strftimerecent');
    foreach ($logs['logs'] as $log) {
        $manager = get_log_manager();
        $logreaders = $manager->get_readers();
        // Getting the event description.
        $select = "id=:logid"; // Log id is the same with event id.
        $params = array();
        $params['logid'] = $log->id;
        // Creating a new table row.
        $row = array();
        // Getting the time of triggered event.
        $row[] = userdate($log->timecreated, '%a').' '.userdate($log->timecreated, $strftimedatetime);
        // Getting the full username and connecting it with user's info page.
        $user = $DB->get_record_sql('SELECT * FROM {user} WHERE id=?', array($log->userid), IGNORE_MISSING);
        $fullname = fullname($user);
        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->courseid}"), $fullname);
        // Getting event context.
        foreach ($logreaders as $reader) {
            if ($reader instanceof \core\log\sql_select_reader) {
                $events = $reader->get_events_select($select, $params, 'timecreated', 0, $perpage);
                foreach ($events as $event) {
                    $context = context::instance_by_id($event->contextid, IGNORE_MISSING);
                    if ($context) {
                        $contextname = $context->get_context_name(true);
                        if ($url = $context->get_url()) {
                            $contextname = html_writer::link($url, $contextname);
                        }
                    } else {
                        $contextname = get_string('other');
                    }
                    $row[] = $contextname;
                }
            }
        }
        // Getting event name.
        foreach ($logreaders as $reader) {
            if ($reader instanceof \core\log\sql_select_reader) {
                $events = $reader->get_events_select($select, $params, 'timecreated', 0, $perpage);
                foreach ($events as $event) {
                    $eventname = $event->get_name();
                    if ($url = $event->get_url()) {
                        $eventname = html_writer::link($url, $eventname);
                    }
                    $row[] = $eventname;
                }
            }
        }
        // Getting the type of event (read, update, create or delete).
        switch ($log->crud ) {
            case ('r'):
                $row[] = get_string('read','report_eventlist');
            break;
            case ('u'):
                $row[] = get_string('update','report_eventlist');
            break;
            case ('d'):
                $row[] = get_string('delete','report_eventlist');
            break;
            case ('c'):
                $row[] = get_string('create','report_eventlist');
            break;
        }
        // Getting the event description.
        foreach ($logreaders as $reader) {
            if ($reader instanceof \core\log\sql_select_reader) {
                $events = $reader->get_events_select($select, $params, 'timecreated', 0, $perpage);
                foreach ($events as $event) {
                    $row[] = $event->get_description();
                }
            }
        }
        // Connecting the ip of user with iplookup.
        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link,
                'iplookup', array('height' => 440, 'width' => 700)));
        $table->data[] = $row;
    }

    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}


/**
 * Outputs the form with information about apecified session
 *
 * @param $sessionid int session id
 */
function print_session_info_form($sessionid) {
    require_once('../sessions/lib.php');

    // Prepare session info form.
    $mform = 'viewsession_form.php';
    if (file_exists($mform)) {
        require_once($mform);
    } else {
        print_error('noformdesc');
    }
    $mform = new viewsession_form();
    $session = get_session($sessionid);

    $strftimedatetime = get_string('strftimerecent');
    $toform['coursename']       = html_writer::link(
        new moodle_url("/course/view.php?id={$session->courseid}"),
        $session->coursename);
    $toform['classroomname']    = $session->classroomname;
    $toform['groupname']        =
        $session->groupname == '' ? get_string('allgroups', 'block_supervised') : $session->groupname;
    $toform['teachername']      =
        html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"),
        fullname($session));
    $toform['lessontypename']   =
        $session->lessontypename == '' ? get_string('notspecified', 'block_supervised') : $session->lessontypename;
    $toform['timestart']        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $toform['duration']         = $session->duration;
    $toform['timeend']          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    $toform['sessioncomment']   = $session->sessioncomment;

    $mform->set_data($toform);
    $mform->display();      // Display view session form.
}