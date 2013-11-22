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

    private function get_planned_session(){
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
        {block_supervised_classroom}.id   AS classroomid,
        {block_supervised_lessontype}.id  AS lessontypeid,
        {user}.firstname,
        {user}.lastname,
        {groups}.id                       AS groupid,
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
        //$params['stateactive']      = StateSession::Active;
        $params['stateplanned']     = StateSession::Planned;

        $plannedsession = $DB->get_record_sql($select, $params);

        return $plannedsession;
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
        global $PAGE, $COURSE, $USER, $CFG;
        require_once('sessions/sessionstate.php');
        //echo '<link href="block.css" rel="stylesheet">';
        if ($this->content !== null) {
            return $this->content;
        }




        // TODO teacher or student?

        // Planned sessions.
        $plannedsession = $this->get_planned_session();

        if( !empty($plannedsession) ){
            $plannedsessionstitle = get_string('plannedsessionsnum', 'block_supervised', count($plannedsession));
            // Prepare form.
            $mform = $CFG->dirroot."/blocks/supervised/plannedsession_block_form.php";
            if (file_exists($mform)) {
                require_once($mform);
            } else {
                print_error('noformdesc');
            }
            $mform = new plannedsession_block_form();

            if ($fromform = $mform->get_data()) {
                // TODO Start session.
                // TODO Logging
            } else {
                // Display form.
                $strftimedatetime = get_string("strftimerecent");
                $toform['classroomid']      = $plannedsession->classroomid;
                $toform['groupid']          = $plannedsession->groupid;
                $toform['lessontypeid']     = $plannedsession->lessontypeid;
                $toform['duration']         = $plannedsession->duration;
                $toform['timestart']        = userdate($plannedsession->timestart, $strftimedatetime);
                $toform['sessioncomment']   = $plannedsession->sessioncomment;

                $mform->set_data($toform);
                $plannedsessionform = $mform->render();
            }
        }
        else{
            $plannedsessionstitle = get_string('plannedsessionsnum', 'block_supervised', 0);
        }




        // Add block body.
        $this->content         = new stdClass;
        $this->content->text   = $plannedsessionstitle . $plannedsessionform;







        // Add footer.
        $classroomsurl = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $COURSE->id));
        $links[] = html_writer::link($classroomsurl, get_string('classroomsurl', 'block_supervised'));
        $lessontypesurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $COURSE->id));
        $links[] = html_writer::link($lessontypesurl, get_string('lessontypesurl', 'block_supervised'));
        $sessionsurl = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $COURSE->id));
        $links[] = html_writer::link($sessionsurl, get_string('sessionsurl', 'block_supervised'));

        $this->content->footer = join(' ', $links);

        return $this->content;
    }
}