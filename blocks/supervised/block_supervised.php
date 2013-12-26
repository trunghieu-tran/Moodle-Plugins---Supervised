<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


class block_supervised extends block_base {

    private function get_teacher_active_session(){
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

        WHERE (:time BETWEEN {block_supervised_session}.timestart AND {block_supervised_session}.timeend)
            AND {block_supervised_session}.courseid     = :courseid
            AND {block_supervised_session}.teacherid    = :teacherid
            AND {block_supervised_session}.state        = :stateactive
        ";


        $teacherid  = $USER->id;
        $courseid   = $COURSE->id;
        $params['time']             = time();
        $params['courseid']         = $courseid;
        $params['teacherid']        = $teacherid;
        $params['stateactive']      = StateSession::Active;

        $activesession = $DB->get_record_sql($select, $params);

        return $activesession;
    }

    private function get_teacher_planned_session(){
        require_once('sessions/sessionstate.php');
        global $DB, $COURSE, $USER;

        // Find nearest Planned sessions.
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
        {course}.fullname                   AS coursename

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

        WHERE ({block_supervised_session}.timestart BETWEEN :time1 AND :time2)
            AND {block_supervised_session}.courseid     = :courseid
            AND {block_supervised_session}.teacherid    = :teacherid
            AND {block_supervised_session}.state        = :stateplanned
        ";

        $time1      = time() - 20*60;
        $time2      = time() + 20*60;
        $teacherid  = $USER->id;
        $courseid   = $COURSE->id;
        $params['time1']            = $time1;
        $params['time2']            = $time2;
        $params['courseid']         = $courseid;
        $params['teacherid']        = $teacherid;
        $params['stateplanned']     = StateSession::Planned;

        $plannedsession = $DB->get_record_sql($select, $params);

        return $plannedsession;
    }


    private function render_plannedsession_form(&$sessionstitle, &$formbody){
        global $CFG, $COURSE, $DB;
        $plannedsession = $this->get_teacher_planned_session();

        if( !empty($plannedsession) ){
            // Prepare form.
            $mform = $CFG->dirroot."/blocks/supervised/plannedsession_block_form.php";
            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $mform = new plannedsession_block_form(null, array('needcomment'=>$plannedsession->sessioncomment!='' ));
            if ($fromform = $mform->get_data()) {
                // TODO Logging
                // Start session and update fields that user could edit.

                $curtime = time();
                $plannedsession->state          = StateSession::Active;
                $plannedsession->classroomid    = $fromform->classroomid;
                $plannedsession->groupid        = $fromform->groupid;
                $plannedsession->lessontypeid   = $fromform->lessontypeid;
                $plannedsession->timestart      = $curtime;
                $plannedsession->duration       = $fromform->duration;
                $plannedsession->timeend        = $curtime + $fromform->duration*60;
                if (!$DB->update_record('block_supervised_session', $plannedsession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }

                // Trigger event (session started).
                $sessioninfo = new stdClass();
                $sessioninfo->courseid      = $plannedsession->courseid;
                $sessioninfo->groupid       = $plannedsession->groupid;
                $sessioninfo->lessontypeid  = $plannedsession->lessontypeid;
                events_trigger('session_started', $sessioninfo);

                unset($plannedsession);
            } else {
                $sessionstitle = get_string('plannedsessiontitle', 'block_supervised');
                // Display form.
                $toform['id']               = $COURSE->id;

                $strftimedatetime = get_string("strftimerecent");
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


    private function render_activesession_form(&$sessionstitle, &$formbody){
        global $CFG, $COURSE, $DB;
        $activesession  = $this->get_teacher_active_session();

        if( !empty($activesession) ){
            // Prepare form.
            $mform = $CFG->dirroot."/blocks/supervised/activesession_block_form.php";

            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $mform = new activesession_block_form(null, array('sessionid'=>$activesession->id, 'courseid'=>$activesession->courseid, 'needcomment'=>$activesession->sessioncomment!='' ));

            if($mform->is_cancelled()) {
                // Finish session and update timeend and duration fields
                // TODO Logging
                $curtime = time();
                $activesession->state           = StateSession::Finished;
                $activesession->timeend         = $curtime;
                $activesession->duration        = ($curtime - $activesession->timestart) / 60;

                if (!$DB->update_record('block_supervised_session', $activesession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }

                // Trigger event (session finished).
                $sessioninfo = new stdClass();
                $sessioninfo->courseid      = $activesession->courseid;
                $sessioninfo->groupid       = $activesession->groupid;
                $sessioninfo->lessontypeid  = $activesession->lessontypeid;
                events_trigger('session_finished', $sessioninfo);

                unset($activesession);
            } else if ($fromform = $mform->get_data()) {
                // Update session
                // TODO Logging
                $sessionstitle = get_string('activesessiontitle', 'block_supervised');
                $oldgroupid = $activesession->groupid;
                $newgroupid = $fromform->groupid;

                $activesession->classroomid     = $fromform->classroomid;
                $activesession->groupid         = $newgroupid;
                $activesession->duration        = $fromform->duration;
                $activesession->timeend         = $activesession->timestart  + $fromform->duration*60;

                if (!$DB->update_record('block_supervised_session', $activesession)) {
                    print_error('insertsessionerror', 'block_supervised');
                }
                // Trigger event (session updated) if group was updated.
                if($oldgroupid != $newgroupid){
                    $sessioninfo = new stdClass();
                    $sessioninfo->courseid      = $activesession->courseid;
                    $sessioninfo->oldgroupid    = $oldgroupid;
                    $sessioninfo->newgroupid    = $newgroupid;
                    $sessioninfo->lessontypeid  = $activesession->lessontypeid;
                    events_trigger('session_updated', $sessioninfo);
                }

                // Refresh block: render active session form.
                $strftimedatetime = get_string("strftimerecent");
                $toform['lessontypename']   = $activesession->lessontypeid == 0 ? get_string('notspecified', 'block_supervised'): $activesession->lessontypename;
                $toform['timestart']        = userdate($activesession->timestart, $strftimedatetime);
                $toform['timeend']          = userdate($activesession->timeend, $strftimedatetime);
                $mform->set_data($toform);
                $formbody = $mform->render();
            }
            else {
                $sessionstitle = get_string('activesessiontitle', 'block_supervised');
                // Display form.
                $toform['id']               = $COURSE->id;

                $strftimedatetime = get_string("strftimerecent");
                $toform['classroomid']      = $activesession->classroomid;
                $toform['groupid']          = $activesession->groupid;
                $toform['lessontypename']   = $activesession->lessontypeid == 0 ? get_string('notspecified', 'block_supervised'): $activesession->lessontypename;
                $toform['duration']         = $activesession->duration;
                $toform['timestart']        = userdate($activesession->timestart, $strftimedatetime);
                $toform['timeend']          = userdate($activesession->timeend, $strftimedatetime);
                $toform['sessioncomment']   = $activesession->sessioncomment;

                $mform->set_data($toform);
                $formbody = $mform->render();
            }
        }
        return empty($activesession);
    }

    private function render_startsession_form(&$sessionstitle, &$formbody){
        global $CFG, $COURSE, $DB, $USER;

        $sessionstitle = get_string('nosessionstitle', 'block_supervised');
        // Prepare form.
        $mform = $CFG->dirroot."/blocks/supervised/startsession_block_form.php";
        if (file_exists($mform)) {
            require_once($mform);
        } else {
            print_error('noformdesc');
        }
        $mform = new startsession_block_form();

        if ($fromform = $mform->get_data()) {
            // TODO Logging
            // Trigger event (session started).
            $sessioninfo = new stdClass();
            $sessioninfo->courseid      = $COURSE->id;
            $sessioninfo->groupid       = $fromform->groupid;
            $sessioninfo->lessontypeid  = $fromform->lessontypeid;
            events_trigger('session_started', $sessioninfo);

            // Start session
            $curtime = time();
            $fromform->state          = StateSession::Active;
            $fromform->courseid       = $COURSE->id;
            $fromform->teacherid      = $USER->id;
            $fromform->timestart      = $curtime;
            $fromform->timeend        = $curtime + $fromform->duration*60;
            if (!$DB->insert_record('block_supervised_session', $fromform)) {
                print_error('insertsessionerror', 'block_supervised');
            }
            // Refresh block: render active session form.
            $sessionstitle = '';
            $formbody = '';
            $this->render_activesession_form($sessionstitle, $formbody);

        } else {
            // Display form.
            $toform['id']               = $COURSE->id;
            $toform['duration']         = 90;

            $mform->set_data($toform);
            $formbody = $mform->render();
        }
    }

    private function render_supervise_body(){
        global $COURSE;

        $formbody = '';
        // Planned session: render planned session form.
        $isemptyplanned = $this->render_plannedsession_form($sessionstitle, $formbody);
        // Active session: render active session form.
        $isemptyactive = $this->render_activesession_form($sessionstitle, $formbody);
        // No sessions: render start session form.
        if($isemptyplanned && $isemptyactive){
            $this->render_startsession_form($sessionstitle, $formbody);
        }


        // Add block body.
        $this->content         = new stdClass;
        $this->content->text   = $sessionstitle . $formbody;
    }


    private function get_student_active_sessions(){
        require_once('sessions/sessionstate.php');
        global $DB, $COURSE, $USER;

        // Find Active sessions.
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
        {block_supervised_classroom}.name   AS classroomname,
        {groups}.name                       AS groupname,
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

        WHERE (:time BETWEEN {block_supervised_session}.timestart AND {block_supervised_session}.timeend)
            AND {block_supervised_session}.courseid     = :courseid
            AND {block_supervised_session}.state        = :stateactive
        ";

        $courseid   = $COURSE->id;
        $params['time']             = time();
        $params['courseid']         = $courseid;
        $params['stateactive']      = StateSession::Active;

        $activesessions = $DB->get_records_sql($select, $params);

        // Filter sessions by user groups
        $groupinggroups = groups_get_user_groups($COURSE->id, $USER->id);
        $groups = $groupinggroups[0];
        foreach($activesessions as $id=>$session){
            if(!in_array($session->groupid, $groups) AND $session->groupid != 0){
                // If user isn't in session->groupid - delete this session
                unset($activesessions[$id]);
            }
        }

        return $activesessions;
    }



    private function render_besupervised_body(){
        global $COURSE, $CFG;

        $activesessions = $this->get_student_active_sessions();

        if(!empty($activesessions)){
            $sessionstitle = get_string('activesessionsstudenttitle', 'block_supervised', count($activesessions));
            $blockbody = '';
            // Prepare form.
            $mform = $CFG->dirroot."/blocks/supervised/activesessionstudent_block_form.php";
            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $strftimedatetime = get_string("strftimerecent");
            foreach($activesessions as $session){
                $mform = new activesessionstudent_block_form();
                $toform['id']               = $COURSE->id;
                $toform['teacher']          = html_writer::link(new moodle_url("/user/view.php?id={$session->teacherid}&course={$session->courseid}"), $session->firstname . " " . $session->lastname);
                $toform['lessontypename']   = $session->lessontypename == '' ? get_string('notspecified', 'block_supervised'): $session->lessontypename;
                $toform['classroomname']    = $session->classroomname;
                $toform['groupname']        = $session->groupname == '' ? get_string('allgroups', 'block_supervised'): $session->groupname;
                $toform['timestart']        = userdate($session->timestart, $strftimedatetime);
                $toform['duration']         = $session->duration;
                $toform['timeend']          = userdate($session->timeend, $strftimedatetime);
                $mform->set_data($toform);
                $blockbody .= $mform->render();
            }
        }
        else{
            $sessionstitle = get_string('nosessionsstudenttitle', 'block_supervised');
            $blockbody = '';
        }

        // Add block body.
        $this->content->text   = $sessionstitle . $blockbody;
    }



    public function init() {
        $this->title = get_string('blocktitle', 'block_supervised');
    }

    /**
     */
    public function applicable_formats() {
        return array(
            'all' => false,
            'course-view' => true);
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content  = new stdClass;

        // Render body.
        if(has_capability('block/supervised:supervise', $this->context)){
            // Supervise mode.
            $this->render_supervise_body();
        }
        else if(has_capability('block/supervised:besupervised', $this->context)){
            // Be supervised mode.
            $this->render_besupervised_body();
        }
        // Render footer.
        $this->render_footer();

        return $this->content;
    }

    public function render_footer(){
        global $COURSE;
        // Add links to a footer according to user capabilities.
        if(has_capability('block/supervised:editclassrooms', $this->context)){
            $classroomsurl = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($classroomsurl, get_string('classroomsurl', 'block_supervised'));
        }
        if(has_capability('block/supervised:editlessontypes', $this->context)){
            $lessontypesurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($lessontypesurl, get_string('lessontypesurl', 'block_supervised'));
        }
        if(has_capability('block/supervised:viewownsessions', $this->context)
            || has_capability('block/supervised:viewallsessions', $this->context)
            || has_capability('block/supervised:manageownsessions', $this->context)
            || has_capability('block/supervised:manageallsessions', $this->context)
            || has_capability('block/supervised:managefinishedsessions', $this->context))
        {
            $sessionsurl = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $COURSE->id));
            $links[] = html_writer::link($sessionsurl, get_string('sessionsurl', 'block_supervised'));
        }

        $this->content->footer = join(' ', $links);
    }

    public function cron() {
        require_once('sessions/sessionstate.php');
        global $DB;

        mtrace( "Cron script for supervised block is running" );

        // Find all out of date sessions.
        $select = "SELECT * FROM {block_supervised_session}
                    WHERE timeend < :curtime
                    AND   (state   = :stateactive OR state   = :stateplanned)
                    ";
        $params['curtime']      = time();
        $params['stateactive']  = StateSession::Active;
        $params['stateplanned'] = StateSession::Planned;
        $sessions = $DB->get_records_sql($select, $params);
        $sessionscount = count($sessions);
        print_object($sessions);
        foreach($sessions as $session){
            $session->state = StateSession::Finished;
            $DB->update_record('block_supervised_session', $session);
        }

        mtrace( "Updated " . $sessionscount . " records");

        return true;
    }
}