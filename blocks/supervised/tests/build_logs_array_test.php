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
 * Unit tests for building logs array function.
 * @group supervised_logs
 */
class block_supervised_build_logs_array_testcase extends advanced_testcase {
    /**
     * Constructs a new record in user's table 
     *
     * @param $name string user's name
     * @param $email string user's e-mail address
     * @return constructed user
     */
    public function construct_user($name, $email) {
        global $DB;
        $student = new stdClass();
        $student->email = (string)$email;
        $student->username = (string)$name;
        $student->id = $DB->insert_record('user', $student);
        return $student;
    }

    /**
     * Constructs a new record in course's table 
     *
     * @param $timecreated int time of creation
     * @param $timemodified int time of modification
     * @return constructed course
     */
    public function construct_course($timecreated, $timemodified) {
        global $DB;
        $course = new stdClass();
        $course->fullname = 'test_course';
        $course->shortname = 'test';
        $course->timecreated = $timecreated;
        $course->timemodified = $timemodified;
        $course->id = $DB->insert_record('course', $course);
        return $course;
    }

    /**
     * Constructs a new record in log's table 
     *
     * @param $userid int id of user, who done an action
     * @param $courseid int id of course where the action was done
     * @param $timecreated int time of creation
     * @param $ipaddress int address from which the action was done
     * @return constructed log
     */
    public function construct_log($userid, $courseid, $timecreated, $ipaddress) {
        global $DB;
        $log = new stdClass();
        $log->eventname = '\core\event\course_viewed';
        $log->component = 'core';
        $log->action = 'viewed';
        $log->target = 'course';
        $log->crud = 'r';
        $log->objecttable = null;
        $log->objectid = null;
        $log->relateduserid = null;
        $log->edulevel = '2';
        $log->contextid = '2';
        $log->anonymous = '0';
        $log->other = null;
        $log->origin = null;
        $log->realuserid = null;
        $log->contextlevel = '50';
        $log->contextinstanceid = '1';
        $log->userid = $userid;
        $log->courseid = $courseid;
        $log->timecreated = $timecreated;
        $log->ip = $ipaddress;
        $log->id = $DB->insert_record('logstore_standard_log', $log);
        return $log;
    }

    /**
     * Constructs a new record in session's table 
     *
     * @param $courseid int id of course for session
     * @param $classroomid int id of classroom for session
     * @param $groupid int id of group for session
     * @param $teacherid int id of teacher for session
     * @param $lessontypeid int id of lessontype for session
     * @return constructed session
     */
    public function construct_session_for_group($courseid, $classroomid, $groupid, $teacherid, $lessontypeid) {
        $session = new stdClass();
        $session->courseid = $courseid;
        $session->classroomid = $classroomid;
        $session->groupid = $groupid;
        $session->teacherid = $teacherid;
        $session->lessontypeid = $lessontypeid;
        return $session;
    }

    /**
     * Constructs a new record in group's table 
     *
     * @param $courseid string id of course 
     * @param $timecreated string time of creation
     * @param $timemodified string time of modification
     * @return constructed course
     */
    public function construct_group($courseid, $timecreated, $timemodified) {
        global $DB;
        $group = new stdClass();
        $group->courseid = $courseid;
        $group->name = 'TestGroup1';
        $group->timecreated = $timecreated;
        $group->timemodified = $timemodified;
        $group->id = $DB->insert_record('groups', $group);
        return $group;
    }

    /**
     * Adds user in a certain group 
     *
     * @param $groupid int id of group to be added in
     * @param $userid int id of user to be added in group
     */
    public function add_user_to_group($groupid, $userid) {
        global $DB;
        $groupmember = new stdClass();
        $groupmember->groupid = $groupid;
        $groupmember->userid = $userid;
        $DB->insert_record('groups_members', $groupmember);
    }

    /**
     * Constructs a new record in class table 
     *
     * @param $ipaddress int ipaddress diapason of class
     * @return constructed class
     */
    public function construct_class($ipaddress) {
        global $DB;
        $class = new stdClass();
        $class->iplist = $ipaddress;
        $class->name = 'TestClass';
        $class->id = $DB->insert_record('block_supervised_classroom', $class);
        return $class;
    }

    /**
     * Constructs a new record in lesson's table 
     *
     * @param $courseid int id of course for lesson
     * @return constructed lesson
     */
    public function construct_lesson($courseid) {
        global $DB;
        $lesson = new stdClass();
        $lesson->name = 'Lection';
        $lesson->courseid = $courseid;
        $lesson->id = $DB->insert_record('block_supervised_lessontype', $lesson);
        return $lesson;
    }

    /** Test for function of filtering logs when
     * classroom is correct,
     * time of session is correct,
     * student's group is correct.
     * Result: log is not deleted.
     */
    public function test_in_class_in_time_in_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.248");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.248";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id, $course->id, '1411044800', "192.168.173.248");

        $logsfilteredexpected = array();
        $logsfilteredexpected[$log->id] = $log;
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 1;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is correct,
     * time of session is not correct,
     * student's group is correct.
     * Result: log is deleted.
     */
    public function test_in_class_not_time_in_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.248");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.248";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id, $course->id, '1411044900', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is not correct,
     * time of session is correct,
     * student's group is correct.
     * Result: log is deleted.
     */
    public function test_not_class_in_time_in_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.153.1");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.153.1";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id, $course->id, '1411044800', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;
        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is not correct,
     * time of session is correct,
     * student's group is correct.
     * Result: log is deleted.
     */
    public function test_not_class_not_time_in_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.153.1");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.153.1";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id, $course->id, '1411044900', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;
        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is correct,
     * time of session is correct,
     * student's group is not correct.
     * Result: log is deleted.
     */
    public function test_in_class_in_time_not_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.248");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.248";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id + 1;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id + 1, $course->id, '1411044800', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is correct,
     * time of session is not correct,
     * student's group is not correct.
     * Result: log is deleted.
     */
    public function test_in_class_not_time_not_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.248");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.248";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id + 1;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id + 1, $course->id, '1411044721', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is not correct,
     * time of session is correct,
     * student's group is not correct.
     * Result: log is deleted.
     */
    public function test_not_class_in_time_not_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.100");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.100";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id + 1;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id + 1, $course->id, '1411044800', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }

    /** Test for function of filtering logs when
     * classroom is not correct,
     * time of session is not correct,
     * student's group is not correct.
     * Result: log is deleted.
     */
    public function test_not_class_not_time_not_group() {
        require_once('C:\wamp\www\moodle\blocks\supervised\logs\logslib.php');
        global $DB;
        $this->resetAfterTest(true);
        // Add new student into user's table.
        $student = $this->construct_user('student', 'student1@example.com');
        // Add new teacher into user's table.
        $teacher = $this->construct_user('teacher', 'teacher1@example.com');
        // Add new course into course's table.
        $course = $this->construct_course(1418301283, 1418301683);
        // Add new group into group's table.
        $group = $this->construct_group($course->id, 1418301283, 1418301683);
        // Add student into group.
        $this->add_user_to_group($group->id, $student->id);
        // Add new classroom into classroom's table.
        $class = $this->construct_class("192.168.173.100");
        // Add new lessontype into lessontype's table.
        $lesson = $this->construct_lesson($course->id);
        // Add new session into session's table.
        $session = $this->construct_session_for_group($course->id, $class->id, $group->id, $teacher->id, $lesson->id);
        $session->timestart = 1411044722;
        $session->duration = '2';
        $session->timeend = 1411044813;
        $session->state = '2';
        $session->iplist = "192.168.173.100";
        $session->id = $DB->insert_record('block_supervised_session', $session);
        // Add new user-session pair into supervised_user table.
        $studentsession = new stdClass();
        $studentsession->sessionid = $session->id;
        $studentsession->userid = $student->id + 1;
        $DB->insert_record('block_supervised_user', $studentsession);
        // Write record in logstore table about event.
        $log = $this->construct_log($student->id + 1, $course->id, '1411044900', "192.168.173.248");

        $logsfilteredexpected = array();
        $expectedresult['logs'] = array_slice($logsfilteredexpected, 0, 10);
        $expectedresult['totalcount'] = 0;

        $timefrom = 1411044722;
        $timeto = 1411044813;
        $result = supervisedblock_build_logs_array($session->id, $timefrom, $timeto, $student->id, 0, 10);
        $this->assertEquals($result, $expectedresult);
    }
}