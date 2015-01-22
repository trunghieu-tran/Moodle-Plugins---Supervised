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
 * Class block_supervised
 *
 * The main idea of the supervised block is to have an add additional control over your students,
 * so they will be able to do something only under teacher supervision.
 * Installed with supervisedcheck (quiz access rules plugin, included out of the box)
 * allows you to add restrictions to your quizzes.
 *
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_supervised extends block_base {

    /**
     * Returns an active session for current user ($USER->id) in current course ($COURSE->id).
     * The current time must be between session's time start and time end. The session's state must be 'Active'.
     *
     * @return stdClass
     */
    private function get_teacher_active_session() {
        require_once('sessions/sessionstate.php');
        global $DB, $COURSE, $USER;

        // Find Active session.
        $select = "SELECT
        {block_supervised_session}.id,
        {block_supervised_session}.timestart,
        {block_supervised_session}.duration,
        {block_supervised_session}.timeend,
        {block_supervised_session}.courseid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.state,
        {block_supervised_session}.sessioncomment,
        {block_supervised_session}.classroomid,
        {block_supervised_session}.lessontypeid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.sendemail,
        {block_supervised_session}.groupid,
        {user}.firstname,
        {user}.lastname,
        {course}.fullname                   AS coursename,
        {block_supervised_lessontype}.name  AS lessontypename

        FROM {block_supervised_session}
            JOIN {block_supervised_classroom}
              ON {block_supervised_session}.classroomid       =   {block_supervised_classroom}.id
            LEFT JOIN {block_supervised_lessontype}
              ON {block_supervised_session}.lessontypeid =   {block_supervised_lessontype}.id
            JOIN {user}
              ON {block_supervised_session}.teacherid    =   {user}.id
            LEFT JOIN {groups}
              ON {block_supervised_session}.groupid      =   {groups}.id
            JOIN {course}
              ON {block_supervised_session}.courseid     =   {course}.id

        WHERE (:time BETWEEN {block_supervised_session}.timestart AND {block_supervised_session}.timeend+10)
            AND {block_supervised_session}.courseid     = :courseid
            AND {block_supervised_session}.teacherid    = :teacherid
            AND {block_supervised_session}.state        = :stateactive
        ";

        $teacherid  = $USER->id;
        $courseid   = $COURSE->id;
        $params['time']             = time();
        $params['courseid']         = $courseid;
        $params['teacherid']        = $teacherid;
        $params['stateactive']      = StateSession::ACTIVE;

        $activesession = $DB->get_record_sql($select, $params);

        return $activesession;
    }


    /**
     * Returns a planned session for current user ($USER->id) in current course ($COURSE->id).
     * The session's time start must be around current time (+- 1 minute). The session's state must be 'Planned'.
     *
     * @return stdClass
     */
    private function get_teacher_planned_session() {
        require_once('sessions/sessionstate.php');
        require_once('sessions/lib.php');
        global $COURSE, $USER;

        $time1          = time() - 20 * 60;
        $time2          = time() + 20 * 60;
        $teacherid      = $USER->id;
        $courseid       = $COURSE->id;
        $stateplanned   = StateSession::PLANNED;
        $plannedsession = get_session(0, $courseid, $teacherid, 0, -1, $stateplanned, $time1, $time2);

        return $plannedsession;
    }


    /**
     * Generates block's body for planned session.
     *
     * @param $title the text on the top of the block (output parameter)
     * @param $formbody the planned session's form (output parameter)
     * @return bool true if the planned session exists
     */
    private function render_plannedsession_form(&$title, &$formbody) {
        global $CFG, $COURSE, $DB, $USER;
        $context = context_course::instance($COURSE->id);
        $plannedsession = $this->get_teacher_planned_session();

        if ( !empty($plannedsession) ) {
            // Prepare form.
            $mform = $CFG->dirroot.'/blocks/supervised/plannedsession_block_form.php';
            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $mform = new plannedsession_block_form(null, array('lessontype' => $plannedsession->lessontypeid,
                'needcomment' => $plannedsession->sessioncomment != '' ));
            if ($fromform = $mform->get_data()) {
                // Start session and update fields that user could edit.
                $curtime = time();
                $plannedsession->state          = StateSession::ACTIVE;
                $plannedsession->classroomid    = $fromform->classroomid;
                $plannedsession->groupid        = $fromform->groupid;
                $plannedsession->lessontypeid   = $fromform->lessontypeid;
                $plannedsession->timestart      = $curtime;
                $plannedsession->duration       = $fromform->duration;
                $plannedsession->timeend        = $curtime + $fromform->duration * 60;
                $classroom = $DB->get_record('block_supervised_classroom', array('id' => $plannedsession->classroomid));
                $plannedsession->iplist  = $classroom->iplist;
                if (!$DB->update_record('block_supervised_session', $plannedsession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }
                update_users_in_session($plannedsession->groupid, $plannedsession->courseid, $plannedsession->id);
                $event = \block_supervised\event\start_planned_session::create(array('context' => $context,
                    'userid' => $USER->id, 'other' => array('courseid' => $COURSE->id,
                    'groupid' => $plannedsession->groupid, 'lessontypeid' => $plannedsession->lessontypeid)));
                $event->trigger();
                unset($plannedsession);
            } else {
                $title = get_string('plannedsessiontitle', 'block_supervised');
                // Display form.
                $toform['id']               = $COURSE->id;
                $strftimedatetime = get_string('strftimerecent');
                $toform['classroomid']      = $plannedsession->classroomid;
                $toform['groupid']          = $plannedsession->groupid;
                $toform['lessontypeid']     = $plannedsession->lessontypeid;
                $toform['duration']         = $plannedsession->duration;
                $toform['timestart']        = userdate($plannedsession->timestart, $strftimedatetime);
                $toform['timeend']          = userdate($plannedsession->timeend, $strftimedatetime);
                $toform['sessioncomment']   = $plannedsession->sessioncomment;
                $mform->set_data($toform);
                $formbody = $mform->render();
            }
        }

        return empty($plannedsession);
    }


    /**
     * Generates block's body for an active session.
     *
     * @param $title the text on the top of the block (output parameter)
     * @param $formbody the active session's form (output parameter)
     * @return bool true if the active session exists
     */
    private function render_activesession_form(&$title, &$formbody) {
        global $CFG, $COURSE, $DB, $USER;
        $context = context_course::instance($COURSE->id);
        $activesession  = $this->get_teacher_active_session();

        if ( !empty($activesession) ) {
            // Prepare form.
            $mform = $CFG->dirroot.'/blocks/supervised/activesession_block_form.php';

            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $mform = new activesession_block_form(null, array('sessionid' => $activesession->id,
                'courseid' => $activesession->courseid, 'needcomment' => $activesession->sessioncomment != '',
                'needlessontype' => $activesession->lessontypeid != 0 ));

            if ($mform->is_cancelled()) {
                // Finish session and update timeend and duration fields.
                $curtime = time();
                $activesession->state           = StateSession::FINISHED;
                $activesession->timeend         = $curtime;
                $activesession->duration        = ($curtime - $activesession->timestart) / 60;

                if (!$DB->update_record('block_supervised_session', $activesession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }
                $event = \block_supervised\event\finish_session::create(array('context' => $context,
                'userid' => $USER->id, 'other' => array('courseid' => $activesession->courseid,
                'groupid' => $activesession->groupid, 'lessontypeid' => $activesession->lessontypeid)));
                $event->trigger();

                unset($activesession);
            } else if ($fromform = $mform->get_data()) {
                // Update session.
                $event = \block_supervised\event\update_active_session::create(array('context' => $context,
                'userid' => $USER->id, 'other' => array('courseid' => $COURSE->id)));
                $event->trigger();
                $title = get_string('activesessiontitle', 'block_supervised');
                $oldgroupid = $activesession->groupid;
                $newgroupid = $fromform->groupid;

                $activesession->classroomid     = $fromform->classroomid;
                $activesession->groupid         = $newgroupid;
                $activesession->duration        = $fromform->duration;
                $activesession->timeend         = $activesession->timestart + $fromform->duration * 60;
                $classroom = $DB->get_record('block_supervised_classroom', array('id' => $activesession->classroomid));
                $activesession->iplist  = $classroom->iplist;

                if (!$DB->update_record('block_supervised_session', $activesession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }
                update_users_in_session($activesession->groupid, $activesession->courseid, $activesession->id);
                // Trigger event (session updated) if group was updated.
                if ($oldgroupid != $newgroupid) {
                    $event = \block_supervised\event\update_session::create(array('context' => $context,
                    'userid' => $USER->id, 'other' => array('courseid' => $activesession->courseid, 'oldgroupid' => $oldgroupid,
                    'newgroupid' => $newgroupid, 'lessontypeid' => $activesession->lessontypeid)));
                    $event->trigger();
                }

                // Refresh block: render active session form.
                $strftimedatetime = get_string('strftimerecent');
                $toform['lessontypename']   = $activesession->lessontypeid != 0 ? $activesession->lessontypename : '';
                $toform['timestart']        = userdate($activesession->timestart, $strftimedatetime);
                $toform['timeend']          = userdate($activesession->timeend, $strftimedatetime);
                $mform->set_data($toform);
                $formbody = $mform->render();
                $this->add_countdown_timer($activesession->timeend - time());
            } else {
                $title = get_string('activesessiontitle', 'block_supervised');
                // Display form.
                $toform['id']               = $COURSE->id;
                $toform['timestartraw']     = $activesession->timestart;

                $strftimedatetime = get_string('strftimerecent');
                $toform['classroomid']      = $activesession->classroomid;
                $toform['groupid']          = $activesession->groupid;
                $toform['lessontypename']   = $activesession->lessontypeid != 0 ? $activesession->lessontypename : '';
                $toform['duration']         = $activesession->duration;
                $toform['timestart']        = userdate($activesession->timestart, $strftimedatetime);
                $toform['timeend']          = userdate($activesession->timeend, $strftimedatetime);
                $toform['sessioncomment']   = $activesession->sessioncomment;

                $mform->set_data($toform);
                $formbody = $mform->render();
                $this->add_countdown_timer($activesession->timeend - time());
            }
        }
        return empty($activesession);
    }

    /**
     * Adds hidden countdown timer on the page for finish an active session.
     *
     * @param $duration time in seconds before current active session will be finished
     */
    private function add_countdown_timer($duration) {
        global $CFG, $PAGE;
        require_once("{$CFG->dirroot}/blocks/supervised/lib.php");

        $PAGE->requires->js('/blocks/supervised/module.js');
        $options = array($duration);
        $PAGE->requires->js_init_call('M.block_supervised.timer.init', $options, false, supervised_get_js_module());
    }


    /**
     * Generates block's body with start new session form.
     *
     * @param $title the text on the top of the block (output parameter)
     * @param $formbody the start new session's form (output parameter)
     */
    private function render_startsession_form(&$title, &$formbody) {
        global $CFG, $COURSE, $DB, $USER;
        $context = context_course::instance($COURSE->id);
        $title = get_string('nosessionstitle', 'block_supervised');
        // Prepare form.
        $mform = $CFG->dirroot."/blocks/supervised/startsession_block_form.php";
        if (file_exists($mform)) {
            require_once($mform);
        } else {
            print_error('noformdesc');
        }
        $mform = new startsession_block_form();

        if ($fromform = $mform->get_data()) {
            $event = \block_supervised\event\start_session::create(array('context' => $context,
            'userid' => $USER->id, 'other' => array('courseid' => $COURSE->id, 'groupid' => $fromform->groupid,
            'lessontypeid' => $fromform->lessontypeid)));
            $event->trigger();
            // Start session.
            $curtime = time();
            $fromform->state          = StateSession::ACTIVE;
            $fromform->courseid       = $COURSE->id;
            $fromform->teacherid      = $USER->id;
            $fromform->timestart      = $curtime;
            $fromform->timeend        = $curtime + $fromform->duration * 60;
            $classroom = $DB->get_record('block_supervised_classroom', array('id' => $fromform->classroomid));
            $fromform->iplist  = $classroom->iplist;
            if (!$newid = $DB->insert_record('block_supervised_session', $fromform)) {
                print_error('insertsessionerror', 'block_supervised');
            }
            update_users_in_session($fromform->groupid, $fromform->courseid, $newid);
            // Refresh block: render active session form.
            $title = '';
            $formbody = '';
            $this->render_activesession_form($title, $formbody);

        } else {
            // Display form.
            $toform['id']               = $COURSE->id;
            $toform['duration']         = $this->config->duration;
            $toform['lessontypeid']     = 0;

            $mform->set_data($toform);
            $formbody = $mform->render();
        }
    }


    /**
     * Renders block's body for user with supervise capability.
     */
    private function render_supervise_body() {
        global $DB;
        $formbody = '';
        // Planned session: render planned session form.
        $isemptyplanned = $this->render_plannedsession_form($sessionstitle, $formbody);
        // Active session: render active session form.
        $isemptyactive = $this->render_activesession_form($sessionstitle, $formbody);
        // No sessions: render start session form.
        if ($isemptyplanned && $isemptyactive) {
            $classroomsexist = $DB->record_exists('block_supervised_classroom', array('active' => 1));
            if ($classroomsexist) {
                $this->render_startsession_form($sessionstitle, $formbody);
            } else {
                $formbody .= get_string('createclassroom', 'block_supervised');
            }
        }

        // Add block body.
        $this->content         = new stdClass;
        $this->content->text   = $sessionstitle . $formbody;
    }


    /**
     * Renders block's body for user with besupervised capability.
     */
    private function render_besupervised_body() {
        global $COURSE, $CFG;
        require_once("{$CFG->dirroot}/blocks/supervised/lib.php");

        $activesessions = user_active_sessions();

        if (!empty($activesessions)) {
            $sessionstitle = get_string('activesessionsstudenttitle', 'block_supervised', count($activesessions));
            $blockbody = '';
            // Prepare form.
            $mform = $CFG->dirroot.'/blocks/supervised/activesessionstudent_block_form.php';
            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $strftimedatetime = get_string('strftimerecent');
            foreach ($activesessions as $session) {
                $mform = new activesessionstudent_block_form();
                $toform['id']               = $COURSE->id;
                $toform['teacher']          = html_writer::link(
                    new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"),
                    fullname($session));
                $toform['lessontypename']   = $session->lessontypename == '' ?
                    get_string('notspecified', 'block_supervised') :
                    $session->lessontypename;
                $toform['classroomname']    = $session->classroomname;
                $toform['groupname']        = $session->groupname == '' ?
                    get_string('allgroups', 'block_supervised') :
                    $session->groupname;
                $toform['timestart']        = userdate($session->timestart, $strftimedatetime);
                $toform['duration']         = $session->duration;
                $toform['timeend']          = userdate($session->timeend, $strftimedatetime);
                $mform->set_data($toform);
                $blockbody .= $mform->render();
            }
        } else {
            $sessionstitle = get_string('nosessionsstudenttitle', 'block_supervised');
            $blockbody = '';
        }

        // Add block body.
        $this->content->text   = $sessionstitle . $blockbody;
    }



    public function init() {
        $this->title = get_string('blocktitle', 'block_supervised');
    }

    public function applicable_formats() {
        return array(
            'all' => false,
            'course-view' => true);
    }

    /**
     * Function sets up duration value for current course if it wasn't saved before.
     */
    public function specialization() {
        global $CFG;
        if (empty($this->config)) {
            $this->config = new stdClass();
        }
        if (empty($this->config->duration)) {
            $this->config->duration = $CFG->block_supervised_session_duration;
        }
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content  = new stdClass;

        // Render body.
        if (has_capability('block/supervised:supervise', $this->context)) {
            // Supervise mode.
            $this->render_supervise_body();
        } else if (has_capability('block/supervised:besupervised', $this->context)) {
            // Be supervised mode.
            $this->render_besupervised_body();
        }
        // Render footer.
        $this->render_footer();

        return $this->content;
    }

    /**
     * Renders block's footer according to user capabilities.
     */
    public function render_footer() {
        global $COURSE;
        // Add links to a footer according to user capabilities.
        if (has_capability('block/supervised:editclassrooms', $this->context)) {
            $classroomsurl = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($classroomsurl, get_string('classroomsurl', 'block_supervised'));
        }
        if (has_capability('block/supervised:editlessontypes', $this->context)) {
            $lessontypesurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($lessontypesurl, get_string('lessontypesurl', 'block_supervised'));
        }
        if (has_capability('block/supervised:viewownsessions', $this->context)
            || has_capability('block/supervised:viewallsessions', $this->context)
            || has_capability('block/supervised:manageownsessions', $this->context)
            || has_capability('block/supervised:manageallsessions', $this->context)
            || has_capability('block/supervised:managefinishedsessions', $this->context)) {
            $sessionsurl = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($sessionsurl, get_string('sessionsurl', 'block_supervised'));
        }

        if (isset($links)) {
            $this->content->footer = join(' ', $links);
        }
    }

    /**
     * Cron function that changes out-of-date session statuses from Planned and Active to Finish
     * @return bool true
     */
    public function cron() {
        global $CFG, $DB;
        require_once("{$CFG->dirroot}/blocks/supervised/sessions/sessionstate.php");

        mtrace( 'Cron script for supervised block is running' );

        // Find all out of date sessions.
        $select = "SELECT * FROM {block_supervised_session}
                    WHERE timeend < :curtime
                    AND   (state   = :stateactive OR state   = :stateplanned)
                    ";
        $params['curtime']      = time();
        $params['stateactive']  = StateSession::ACTIVE;
        $params['stateplanned'] = StateSession::PLANNED;
        $sessions = $DB->get_records_sql($select, $params);
        $sessionscount = count($sessions);
        foreach ($sessions as $session) {
            $session->state = StateSession::FINISHED;
            $DB->update_record('block_supervised_session', $session);
        }

        mtrace( 'Updated ' . $sessionscount . ' records');

        return true;
    }

    /**
     * Cleanup all data associated with the block on deletion.
     */
    public function instance_delete() {
        global $CFG, $COURSE;
        require_once("{$CFG->dirroot}/blocks/supervised/lib.php");
        cleanup($COURSE->id);
    }

    public function has_config() {
        return true;
    }
}