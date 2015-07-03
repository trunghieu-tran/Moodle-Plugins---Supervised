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

    // Prepare query.
    $params = array();
    $selector = '(l.time BETWEEN :timefrom AND :timeto) AND l.course = :courseid';
    $params['timefrom'] = $timefrom;
    $params['timeto']   = $timeto;
    $params['courseid'] = $session->courseid;
    if ($userid != 0) {
        $selector .= ' AND l.userid = :userid';
        $params['userid'] = $userid;
    }
    // Get logs.
    $logs = get_logs($selector, $params, 'l.time DESC', '', '', $totalcount);

    // Filter logs by classroom's ip subnet.
    $logsfiltered = array();
    foreach ($logs as $id => $log) {
        if (address_in_subnet($log->ip, $session->iplist)) {
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
        get_string('ip_address'),
        get_string('fullnameuser'),
        get_string('action'),
        get_string('info')
    );
    $table->data = array();

    $strftimedatetime = get_string('strftimerecent');
    foreach ($logs['logs'] as $log) {

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module' => $log->module, 'action' => $log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // Ugly hack to make sure fullname is shown correctly.
            if ($ld->mtable == 'user' && $ld->field == $DB->sql_concat('firstname', "' '" , 'lastname')) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id' => $log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id' => $log->info));
            }
        }

        // Filter log->info.
        $log->info = format_string($log->info);

        // If $log->url has been trimmed short by the db size restriction
        // code in add_to_log, keep a note so we don't add a link to a broken url.
        $brokenurl = (core_text::strlen($log->url) == 100 && core_text::substr($log->url, 97) == '...');

        $row = array();

        $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);

        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup',
            array('height' => 440, 'width' => 700)));

        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"),
            fullname($log));

        $displayaction = "$log->module $log->action";
        if ($brokenurl) {
            $row[] = $displayaction;
        } else {
            $link = make_log_url($log->module, $log->url);
            $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'),
                array('height' => 440, 'width' => 700));
        }
        $row[] = $log->info;
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