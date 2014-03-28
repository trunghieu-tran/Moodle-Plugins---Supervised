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
global $CFG;
require_once("{$CFG->dirroot}/blocks/supervised/lib.php");

/**
 * Returns the sessions array according to specified conditions and capabilities of current user
 *
 * @param int $limitfrom sessions from the specified index
 * @param int $limitnum specified number of sessions
 * @param int $from session starts after this time
 * @param int $to session ends before this time
 * @param int $teacher teacher id
 * @param int $course course id
 * @param int $classroom classroom id
 * @param int $lessontype lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state session state
 * @return mixed array of the sessions
 */
function build_sessions_array($limitfrom, $limitnum, $from, $to, $teacher=0, $course=0, $classroom=0, $lessontype=-1, $state=0) {
    global $USER, $PAGE;

    $sessions = get_sessions($course, $teacher, $classroom, $lessontype, $state, $from, 0, 0, $to);

    // Filter sessions according to user capabilities.
    $sessionsfiltered = array();
    foreach ($sessions as $session) {
        // If user can't supervise access to session's course -> miss current session.
        $coursecontext = context_course::instance($session->courseid);
        if (has_capability('block/supervised:supervise', $coursecontext)) {
            // Now check if user can view current session.
            if ($session->teacherid != $USER->id) {
                // Check if user has capability to view other user's sessions.
                if (   has_capability('block/supervised:viewallsessions', $PAGE->context) ||
                    has_capability('block/supervised:manageallsessions', $PAGE->context)   ) {
                    $sessionsfiltered[] = $session;
                }
            } else {
                // User can view his own sessions (already checked).
                $sessionsfiltered[] = $session;
            }
        }
    }

    $result['sessions'] = array_slice($sessionsfiltered, $limitfrom, $limitnum);
    $result['totalcount'] = count($sessionsfiltered);
    return $result;
}


/**
 * Output sessions with pagination according to specified conditions
 *
 * @param int $pagenum current page index
 * @param int $perpage number of sessions per page
 * @param string $url the url prefix for pages
 * @param int $from session starts after this time
 * @param int $to session ends before this time
 * @param int $teacher teacher id
 * @param int $course course id
 * @param int $classroom classroom id
 * @param int $lessontype lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state session state
 */
function print_sessions($pagenum=0, $perpage=50, $url, $from, $to, $teacher=0, $course=0, $classroom=0, $lessontype=-1, $state=0) {
    global $OUTPUT, $USER, $PAGE, $DB;

    $sessions = build_sessions_array($pagenum * $perpage, $perpage, $from, $to, $teacher, $course, $classroom, $lessontype, $state);
    $totalcount = $sessions['totalcount'];
    // Check if any lesson type exists in course.
    if ($course) {
        $lessontypesexist = $DB->record_exists('block_supervised_lessontype', array('courseid' => $course));
    } else {
        $lessontypesexist = $DB->record_exists('block_supervised_lessontype', array());
    }

    echo "<div class=\"info\">\n";
    print_string('displayingrecords', '', $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $pagenum, $perpage, "$url&perpage=$perpage");

    // Fill table with sessions according to user capabilities.
    $strftimedatetime = get_string('strftimerecent');
    $tabledata = array();
    foreach ($sessions['sessions'] as $session) {
        $logsurl = new moodle_url('/blocks/supervised/logs/view.php',
            array('sessionid' => $session->id, 'courseid' => $session->courseid));
        $logslink = '<a href="'.$logsurl.'">' . get_string('showlogs', 'block_supervised') . '</a>';

        // Combine new row.
        $tablerow = array();
        $tablerow[] = html_writer::link(new moodle_url("/course/view.php?id={$session->courseid}"), $session->coursename);
        $tablerow[] = $session->classroomname;
        $tablerow[] = $session->groupname == '' ? get_string('allgroups', 'block_supervised') : $session->groupname;
        $tablerow[] = html_writer::link(
            new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"),
            fullname($session));
        if ($lessontypesexist) {
            $tablerow[] = $session->lessontypename == '' ?
                get_string('notspecified', 'block_supervised') :
                $session->lessontypename;
        }
        $tablerow[] = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
        $tablerow[] = $session->duration;
        $tablerow[] = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
        $tablerow[] = StateSession::get_state_name($session->state);
        $tablerow[] = ($session->state != StateSession::PLANNED) ? $logslink : ('');

        // Build edit icon.
        $iconedit = '';
        if ($session->state == StateSession::PLANNED) {
            if (  ($session->teacherid == $USER->id && has_capability('block/supervised:manageownsessions', $PAGE->context))
                || has_capability('block/supervised:manageallsessions', $PAGE->context) ) {
                $editurl    = new moodle_url('/blocks/supervised/sessions/addedit.php',
                    array('id' => $session->id, 'courseid' => $session->courseid));
                $iconedit   = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
            }
        }
        // Build delete icon.
        $icondelete = '';
        if (
            ($session->state == StateSession::PLANNED && $session->teacherid == $USER->id
                && has_capability('block/supervised:manageownsessions', $PAGE->context))
            || ($session->state == StateSession::PLANNED
                && has_capability('block/supervised:manageallsessions', $PAGE->context))
            || ($session->state == StateSession::FINISHED
                && has_capability('block/supervised:managefinishedsessions', $PAGE->context))
        ) {
            $deleteurl      = new moodle_url('/blocks/supervised/sessions/delete.php',
                array('courseid' => $session->courseid, 'id' => $session->id));
            $icondelete     = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
        }

        $tablerow[] = $iconedit . $icondelete;
        $tabledata[] = $tablerow;
    }

    // Build table.
    $table = new html_table();
    $table->attributes['class'] .= 'flexible generaltable';
    // Build headers array.
    $table->head = array();
    $table->head[] = get_string('course');
    $table->head[] = get_string('classroom', 'block_supervised');
    $table->head[] = get_string('group');
    $table->head[] = get_string('superviser', 'block_supervised');
    if ($lessontypesexist) {
        $table->head[] = get_string('lessontype', 'block_supervised');
    }
    $table->head[] = get_string('timestart', 'block_supervised');
    $table->head[] = get_string('duration', 'block_supervised');
    $table->head[] = get_string('timeend', 'block_supervised');
    $table->head[] = get_string('state', 'question');
    $table->head[] = get_string('logs');
    $table->head[] = get_string('edit');

    $table->data = $tabledata;
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_div();
    echo $OUTPUT->paging_bar($totalcount, $pagenum, $perpage, "$url&perpage=$perpage");
}


/**
 * Returns an array of sessions according to specified conditions
 *
 * @param int $courseid course id
 * @param int $teacherid teacher id
 * @param int $classroomid classroom id
 * @param int $lessontypeid lessontype id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state    session state
 * @param int $timestart1 session must starts after this time
 * @param int $timestart2 session must starts before this time
 * @param int $timeend1 session must ends after this time
 * @param int $timeend2 session must ends before this time
 * @param int $id session id
 * @return array sessions
 */
function get_sessions($courseid=0, $teacherid=0, $classroomid=0, $lessontypeid=-1, $state=0,
                      $timestart1=0, $timestart2=0, $timeend1=0, $timeend2=0, $id=0) {
    global $DB;

    $select = "SELECT
        {block_supervised_session}.id,
        {block_supervised_session}.classroomid,
        {block_supervised_session}.lessontypeid,
        {block_supervised_session}.groupid,
        {block_supervised_session}.timestart,
        {block_supervised_session}.duration,
        {block_supervised_session}.timeend,
        {block_supervised_session}.courseid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.state,
        {block_supervised_session}.sessioncomment,
        {block_supervised_session}.sendemail,
        {block_supervised_session}.iplist,
        {block_supervised_classroom}.name   AS classroomname,
        {block_supervised_lessontype}.name  AS lessontypename,
        {user}.firstname,
        {user}.lastname,
        {user}.middlename,
        {user}.lastnamephonetic,
        {user}.firstnamephonetic,
        {user}.alternatename,
        {groups}.name                       AS groupname,
        {course}.fullname                   AS coursename

        FROM {block_supervised_session}
            JOIN {block_supervised_classroom}
              ON {block_supervised_session}.classroomid  =   {block_supervised_classroom}.id
            LEFT JOIN {block_supervised_lessontype}
              ON {block_supervised_session}.lessontypeid =   {block_supervised_lessontype}.id
            JOIN {user}
              ON {block_supervised_session}.teacherid    =   {user}.id
            LEFT JOIN {groups}
              ON {block_supervised_session}.groupid      =   {groups}.id
            JOIN {course}
              ON {block_supervised_session}.courseid     =   {course}.id
        ";

    $whereflag = false;
    if ($courseid) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.courseid = :courseid';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.courseid = :courseid';
        }
        $params['courseid']      = $courseid;
    }
    if ($teacherid) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.teacherid = :teacherid';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.teacherid = :teacherid';
        }
        $params['teacherid']      = $teacherid;
    }
    if ($classroomid) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.classroomid = :classroomid';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.classroomid = :classroomid';
        }
        $params['classroomid']      = $classroomid;
    }
    if ($lessontypeid != -1) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.lessontypeid = :lessontypeid';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.lessontypeid = :lessontypeid';
        }
        $params['lessontypeid']      = $lessontypeid;
    }
    if ($state) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.state = :state';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.state = :state';
        }
        $params['state']      = $state;
    }
    if ($timestart1) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.timestart >= :timestart1';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.timestart >= :timestart1';
        }
        $params['timestart1']      = $timestart1;
    }
    if ($timestart2) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.timestart <= :timestart2';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.timestart <= :timestart2';
        }
        $params['timestart2']      = $timestart2;
    }
    if ($timeend1) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.timeend >= :timeend1';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.timeend >= :timeend1';
        }
        $params['timeend1']      = $timeend1;
    }
    if ($timeend2) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.timeend <= :timeend2';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.timeend <= :timeend2';
        }
        $params['timeend2']      = $timeend2;
    }
    if ($id) {
        if (!$whereflag) {
            $select .= ' WHERE {block_supervised_session}.id = :id';
            $whereflag = true;
        } else {
            $select .= ' AND {block_supervised_session}.id = :id';
        }
        $params['id']      = $id;
    }

    $select .= ' ORDER BY timestart';
    return $DB->get_records_sql($select, $params);
}

/**
 * Returns one session according to specified conditions
 *
 * @param int $courseid course id
 * @param int $teacherid teacher id
 * @param int $classroomid classroom id
 * @param int $lessontypeid lesson type id (0 for 'not specified', -1 for 'all lesson types')
 * @param int $state    session state
 * @param int $timestart1 session must starts after this time
 * @param int $timestart2 session must starts before this time
 * @param int $timeend1 session must ends after this time
 * @param int $timeend2 session must ends before this time
 * @param int $id session id
 * @return stdClass session
 */
function get_session($id=0, $courseid=0, $teacherid=0, $classroomid=0, $lessontypeid=-1, $state=0,
                     $timestart1=0, $timestart2=0, $timeend1=0, $timeend2=0) {

    $records = get_sessions($courseid, $teacherid, $classroomid, $lessontypeid, $state,
        $timestart1, $timestart2, $timeend1, $timeend2, $id);

    return array_shift($records); // Return the first element.
}

/**
 * Send e-mail to teacher about creation of a new session
 *
 * @param $session stdClass created session
 * @param $creator stdClass user who created the session
 */
function mail_newsession($session, $creator) {
    global $DB, $CFG;
    $strftimedatetime = get_string('strftimerecent');

    $site        = get_site();
    $supportuser = core_user::get_support_user();
    $user        = $DB->get_record('user', array('id' => $session->teacherid));

    $sessioninfo = new stdClass();
    $sessioninfo->course           = $session->coursename;
    $sessioninfo->classroom        = $session->classroomname;
    $sessioninfo->group            = $session->groupname == '' ?
        get_string('allgroups', 'block_supervised') :
        $session->groupname;
    $sessioninfo->lessontype       = $session->lessontypename == '' ?
        get_string('notspecified', 'block_supervised') :
        $session->lessontypename;
    $sessioninfo->timestart        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $sessioninfo->duration         = $session->duration;
    $sessioninfo->timeend          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    if ($session->sessioncomment) {
        $sessioninfo->comment          = get_string('emailsessioncomment', 'block_supervised', $session->sessioncomment);
    } else {
        $sessioninfo->comment          = '';
    }

    $data = new stdClass();
    $data->sitename         = format_string($site->fullname);
    $data->teachername      = fullname($user);
    $data->creatorname      = fullname($creator);
    $data->sessioninfo      = get_string('emailsessioninfo', 'block_supervised', $sessioninfo);
    $data->haveaniceday     = get_string('haveaniceday', 'block_supervised');
    $coursecontext = context_course::instance($session->courseid);
    if (has_capability('block/supervised:manageownsessions', $coursecontext, $user) ||
        has_capability('block/supervised:manageallsessions', $coursecontext, $user) ) {
        $editurl             = $CFG->wwwroot ."/blocks/supervised/sessions/addedit.php?courseid=$session->courseid&id=$session->id";
        $deleteurl           = $CFG->wwwroot ."/blocks/supervised/sessions/delete.php?courseid=$session->courseid&id=$session->id";
        $data->editsession   = get_string('emaileditsessionurl', 'block_supervised', $editurl);
        $data->deletesession = get_string('emaildeletesessionurl', 'block_supervised', $deleteurl);
    }

    $subjectfields = new stdClass();
    $subjectfields->sitename  = $data->sitename;
    $subjectfields->timestart = $sessioninfo->timestart;

    $message    = get_string('emailnewsession', 'block_supervised', $data);
    $subject    = get_string('emailnewsessionsubject', 'block_supervised', $subjectfields);

    email_to_user($user, $supportuser, $subject, $message);
}

/**
 * Send e-mail to teacher about removing of the session
 * @param $session stdClass removed session
 * @param $remover stdClass user who removed this session
 */
function mail_removedsession($session, $remover) {
    global $DB, $CFG;
    $strftimedatetime = get_string('strftimerecent');

    $site        = get_site();
    $supportuser = core_user::get_support_user();
    $user        = $DB->get_record('user', array('id' => $session->teacherid));

    $sessioninfo = new stdClass();
    $sessioninfo->course           = $session->coursename;
    $sessioninfo->classroom        = $session->classroomname;
    $sessioninfo->group            = $session->groupname == '' ?
        get_string('allgroups', 'block_supervised') :
        $session->groupname;
    $sessioninfo->lessontype       = $session->lessontypename == '' ?
        get_string('notspecified', 'block_supervised') :
        $session->lessontypename;
    $sessioninfo->timestart        = userdate($session->timestart, '%a').' '.userdate($session->timestart, $strftimedatetime);
    $sessioninfo->duration         = $session->duration;
    $sessioninfo->timeend          = userdate($session->timeend, '%a').' '.userdate($session->timeend, $strftimedatetime);
    if ($session->sessioncomment) {
        $sessioninfo->comment          = get_string('emailsessioncomment', 'block_supervised', $session->sessioncomment);
    } else {
        $sessioninfo->comment          = '';
    }

    $data = new stdClass();
    $data->sitename         = format_string($site->fullname);
    $data->teachername      = fullname($user);
    $data->sitename         = format_string($site->fullname);
    $data->removername      = fullname($remover);
    $data->state            = StateSession::get_state_name($session->state);
    $data->sessioninfo      = get_string('emailsessioninfo', 'block_supervised', $sessioninfo);
    $data->haveaniceday     = get_string('haveaniceday', 'block_supervised');
    if ($session->messageforteacher) {
        $data->custommessage    = get_string('emailremovedsessionmsg', 'block_supervised', $session->messageforteacher);
    } else {
        $data->custommessage    = '';
    }

    $subject = new stdClass();
    $subject->sitename  = $data->sitename;
    $subject->timestart = $sessioninfo->timestart;

    $subjectfields = new stdClass();
    $subjectfields->sitename  = $data->sitename;
    $subjectfields->timestart = $sessioninfo->timestart;

    $message    = get_string('emailremovedsession', 'block_supervised', $data);
    $subject    = get_string('emailremovedsessionsubject', 'block_supervised', $subjectfields);

    email_to_user($user, $supportuser, $subject, $message);
}


/**
 * Send e-mail to teacher about editing of the session
 * @param $updsession stdClass edited session
 * @param $editor stdClass user who edited this session
 */
function mail_editedsession($updsession, $editor) {
    global $DB, $CFG;
    $strftimedatetime = get_string('strftimerecent');

    $site        = get_site();
    $supportuser = core_user::get_support_user();
    $user        = $DB->get_record('user', array('id' => $updsession->teacherid));

    $sessioninfo = new stdClass();
    $sessioninfo->course           = $updsession->coursename;
    $sessioninfo->classroom        = $updsession->classroomname;
    $sessioninfo->group            = $updsession->groupname == '' ?
        get_string('allgroups', 'block_supervised') :
        $updsession->groupname;
    $sessioninfo->lessontype       = $updsession->lessontypename == '' ?
        get_string('notspecified', 'block_supervised') :
        $updsession->lessontypename;
    $sessioninfo->timestart        = userdate($updsession->timestart, '%a').' '.userdate($updsession->timestart, $strftimedatetime);
    $sessioninfo->duration         = $updsession->duration;
    $sessioninfo->timeend          = userdate($updsession->timeend, '%a').' '.userdate($updsession->timeend, $strftimedatetime);
    if ($updsession->sessioncomment) {
        $sessioninfo->comment          = get_string('emailsessioncomment', 'block_supervised', $updsession->sessioncomment);
    } else {
        $sessioninfo->comment          = '';
    }

    $data = new stdClass();
    $data->sitename         = format_string($site->fullname);
    $data->teachername      = fullname($user);
    $data->editorname       = fullname($editor);
    $data->sessioninfo      = get_string('emailsessioninfo', 'block_supervised', $sessioninfo);
    $data->haveaniceday     = get_string('haveaniceday', 'block_supervised');
    $coursecontext = context_course::instance($updsession->courseid);
    if (has_capability('block/supervised:manageownsessions', $coursecontext, $user) ||
        has_capability('block/supervised:manageallsessions', $coursecontext, $user) ) {
        $editurl    = $CFG->wwwroot ."/blocks/supervised/sessions/addedit.php?courseid=$updsession->courseid&id=$updsession->id";
        $deleteurl  = $CFG->wwwroot ."/blocks/supervised/sessions/delete.php?courseid=$updsession->courseid&id=$updsession->id";
        $data->editsession      = get_string('emaileditsessionurl', 'block_supervised', $editurl);
        $data->deletesession    = get_string('emaildeletesessionurl', 'block_supervised', $deleteurl);
    }

    $subjectfields = new stdClass();
    $subjectfields->sitename  = $data->sitename;
    $subjectfields->timestart = $sessioninfo->timestart;

    $message    = get_string('emaileditedsession', 'block_supervised', $data);
    $subject    = get_string('emaileditedsessionsubject', 'block_supervised', $subjectfields);

    email_to_user($user, $supportuser, $subject, $message);
}


/**
 * Add users from session into database table
 * @param $groupid
 * @param $courseid
 * @param $sessionid
 */
function update_users_in_session($groupid, $courseid, $sessionid) {
    global $DB;
    // Prepare users array.
    $users = array();
    if ($groupid == ALL_GROUPS) {
        $groups = groups_get_all_groups($courseid);
        foreach ($groups as $group) {
            $users = $users + groups_get_members($group->id);
        }
    } else {
        $users = groups_get_members($groupid);
    }

    // Update existing records if possible.
    $oldusers = $DB->get_records('block_supervised_user', array('sessionid' => $sessionid));
    foreach ($users as $user) {
        $curuser = array_shift($oldusers);
        if (!$curuser) {
            $curuser                = new stdClass();
            $curuser->sessionid     = $sessionid;
            $curuser->userid        = $user->id;
            $curuser->id            = $DB->insert_record('block_supervised_user', $curuser);
        }
        $curuser->userid        = $user->id;
        $DB->update_record('block_supervised_user', $curuser);
    }
    // Delete any remaining old rules.
    foreach ($oldusers as $olduser) {
        $DB->delete_records('block_supervised_user', array('id' => $olduser->id));
    }
}


function get_sessions_filter_user_preferences() {
    global $USER, $COURSE, $CFG;

    $date = usergetdate(time());
    $pref = get_user_preferences();

    if ( !isset($pref['block_supervised_perpage']) ) {
        $pref['block_supervised_perpage'] = 50;
    }
    if ( !isset($pref['block_supervised_page']) ) {
        $pref['block_supervised_page'] = 0;
    }
    if ( !isset($pref['block_supervised_timestamp']) ) {    // Last saved preferences time.
        $pref['block_supervised_timestamp'] = make_timestamp($date['year'], $date['mon'],
            $date['mday'], $date['hours'], $date['minutes'], $date['seconds']);
    }
    if ( !isset($pref['block_supervised_from']) ) {
        $pref['block_supervised_from'] =
            make_timestamp($date['year'], $date['mon'], $date['mday'] - $CFG->block_supervised_sessions_days_past, 0, 0, 0);
    }
    if ( !isset($pref['block_supervised_to']) ) {
        $pref['block_supervised_to'] = make_timestamp($date['year'], $date['mon'], $date['mday'], 23, 55, 0);
    }
    if ( !isset($pref['block_supervised_teacher']) ) {
        $pref['block_supervised_teacher'] = $USER->id;
    }
    if ( !isset($pref['block_supervised_course']) ) {
        $pref['block_supervised_course'] = $COURSE->id;
    }
    if ( !isset($pref['block_supervised_lessontype']) ) {
        $pref['block_supervised_lessontype'] = -1;
    }
    if ( !isset($pref['block_supervised_classroom']) ) {
        $pref['block_supervised_classroom'] = 0;
    }
    if ( !isset($pref['block_supervised_state']) ) {
        $pref['block_supervised_state'] = 0;
    }

    return $pref;
}

/**
 * Function checks user preferences and may do some changes:
 * If the current page saved in block_supervised_page is empty (no sessions) => decrease current page to last valid page
 * If last preferences saved earlier than 24h => update preferences to defaults.
 * @param $pref array user preferences
 */
function check_sessions_filter_user_preferences(&$pref) {
    global $USER, $COURSE, $CFG;
    // Check if there are any session on the current page.
    $sessions = build_sessions_array(
        $pref['block_supervised_page'] * $pref['block_supervised_perpage'],
        $pref['block_supervised_perpage'],      $pref['block_supervised_from'],
        $pref['block_supervised_to'],           $pref['block_supervised_teacher'],
        $pref['block_supervised_course'],       $pref['block_supervised_classroom'],
        $pref['block_supervised_lessontype'],   $pref['block_supervised_state']);
    if ( $sessions['totalcount'] > 0 && count($sessions['sessions']) == 0 ) {
        // Current page is empty but we have some sessions - decrease current page number to last valid page.
        $totalpages = floor($sessions['totalcount'] / $pref['block_supervised_perpage']);
        if ($sessions['totalcount'] % $pref['block_supervised_perpage'] > 0) {
            $totalpages++;
        }
        $pref['block_supervised_page'] = $totalpages - 1;
    }

    // Check timestamp.
    $date = usergetdate(time());
    $curtimestamp = make_timestamp($date['year'], $date['mon'],
        $date['mday'], $date['hours'], $date['minutes'], $date['seconds']);
    if ( $curtimestamp - $pref['block_supervised_timestamp'] > 86400 ) {
        // Last preferences saved earlier 24h => update preferences to defaults.
        $pref['block_supervised_from'] = make_timestamp($date['year'], $date['mon'],
            $date['mday'] - $CFG->block_supervised_sessions_days_past, 0, 0, 0);
        $pref['block_supervised_to'] = make_timestamp($date['year'], $date['mon'], $date['mday'], 23, 55, 0);
        $pref['block_supervised_page'] = 0;
        $pref['block_supervised_teacher'] = $USER->id;
        $pref['block_supervised_course'] = $COURSE->id;
        $pref['block_supervised_lessontype'] = -1;
        $pref['block_supervised_classroom'] = 0;
        $pref['block_supervised_state'] = 0;
    }
}


function save_sessions_filter_user_preferences($pref) {
    // Remove _lastloaded field.
    if ( isset($pref['_lastloaded']) ) {
        unset($pref['_lastloaded']);
    }
    // Update timestamp.
    $date = usergetdate(time());
    $pref['block_supervised_timestamp'] = make_timestamp($date['year'], $date['mon'],
        $date['mday'], $date['hours'], $date['minutes'], $date['seconds']);

    set_user_preferences($pref);
}