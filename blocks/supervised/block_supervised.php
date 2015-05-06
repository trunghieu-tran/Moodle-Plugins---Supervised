<?php

/** This is supervised block
 *
 * @author: Shekin Alex (Volgograd, VSTU)
 * @date: 2010 09 01
 */

class block_supervised extends block_base {

    function init() {
      $this->title = get_string('pluginname', 'block_supervised');
    } //init

    function get_content() {
        global $CFG, $DB, $COURSE, $USER;
        require_once($CFG->dirroot.'/blocks/supervised/lib.php');

        $url = new moodle_url(qualified_me());
        $idcourse = optional_param('id', SITEID, PARAM_INT);

        if ($this->content !== null) {
            return $this->content;
        }

        if ($USER->id == 0) {
            $this->content = new stdClass;
            $this->content->text = '';
            return $this->content;
        }


        if (has_capability('block/supervised:start', $this->context)) {
            // base for lecturer
            $currentclassroomarray = get_current_classroom();
            $classroomnumber = $currentclassroomarray['number'];
            $lesson = get_start_classperiod($COURSE->id, $classroomnumber);

            // if classroom is free then view form for start lessons
            if ($lesson == null) {
                // get basic form ( when lecturer was registered )
                // time and classroom isn't yet chose
                if (has_capability('block/supervised:chooseclassroom', $this->context)) {
                    $classrooms = array();
                    $return = $DB->get_records_sql('SELECT `number`, `id` FROM {block_supervised_classroom}');
                    foreach ($return as $record) {
                        $classrooms[$record->id] = $record->number;
                    }
                    $htmlclassroom = html_writer::select($classrooms, 'classroom_list', $currentclassroomarray['id']);
                } else {
                    $currentclassroomarray = get_current_classroom();
                    $currentclassroom = $currentclassroomarray['number'];
                    //$id = $DB->get_record('block_supervised_classroom', array('number'=>$currentclassroom), 'id');
                    $htmlclassroom = '<b>' . $currentclassroom . '</b>';
                    $htmlclassroom .= '<input type="hidden" id="classroom_list" name="classroom_list" value="' . $currentclassroomarray['id'] . '"/>';
                }

                $durationhours = '<select id="duration_hours" name="duration_hours">';
                $maxcounthours = 3;    // set in options

                for ($i=0; $i<$maxcounthours+1; $i++) {
                    $durationhours .= '<option>' . $i . '</option>';
                }
                $durationhours .= '</select>';

                $durationminutes = '<select id="duration_minutes" name="duration_minutes">';
                $minute = 12; // * 5
                for ($i=0; $i<$minute; $i++) {
                    $durationminutes .= '<option>' . ($i*5)  . '</option>';
                }
                $durationminutes .= '</select>';

                $groups = array();
                $return = $DB->get_records_sql('SELECT `name`, `id` FROM {groups} WHERE courseid=?', array($COURSE->id));
                foreach ($return as $record) {
                    $groups[$record->id] = $record->name;
                }
                $groups[0] = 'all';

                $maincontent = '<form id="main_supervised" method="post" action="' . $CFG->wwwroot . '/blocks/supervised/query.php">'
                    . '<div>'
                        . '<div>'
                            . '<div>'
                                . '<div><p>'
                                    . '<label for="duration_hours">'. get_string('labelduration', 'block_supervised') . ':&nbsp; </label>'
                                    . '&nbsp;' . $durationhours . '&nbsp;' . get_string('labelhours', 'block_supervised') 
                                    . '&nbsp;' . $durationminutes . '&nbsp;'. get_string('labelminutes', 'block_supervised')
                                . '</p></div>'
                            . '</div>'
                        . '<div>'
                            . '<div><p>'
                                . html_writer::label(get_string('labelroom', 'block_supervised').':&nbsp;', 'classroom_list')
                                . $htmlclassroom 
                            . '</p></div>'
                        . '</div>'
                        . '<div>'
                            . '<div><p>'
                                . html_writer::label(get_string('numbergroup', 'block_supervised').':&nbsp;', 'number_group')
                                . html_writer::select($groups, 'group_list', '0')
                            . '</p></div>'
                        . '</div>'
                        . '<div>'
                            . '<div>'
                            . '</div>'
                            . '<div class="block_start_button" align="center"><p>'
                                . '<input id="start_button" type="submit" value="' . get_string('startoccupation', 'block_supervised') . '" /> '
                            . '</p></div>'
                        . '</div>'
                    . '</div>'
                    . '<input type="hidden" id="links" name="links" value="' . $url . '"/>'
                    . '<input type="hidden" id="id" name="id" value="'. $idcourse .'"/>'
                    . '</div>'
                        . '<div> <p>'
                            . get_string('currentclassroom', 'block_supervised') . ':&nbsp;' . $classroomnumber
                        . '</p></div>'
                    .'</form>'
                    . '<div align="center">'
                    . '<a href="'. $CFG->wwwroot. '/blocks/supervised/listclassroom.php'. '"main">' . get_string('classroomlistlabel', 'block_supervised') . '</div>' . '</a>'
                    . '</div>'
                    . '<div align="center">'
                    . '<a href="'. $CFG->wwwroot. '/blocks/supervised/report.php' . '"main">' . get_string('reportslabel', 'block_supervised') . '</div>' . '</a>'
                    . '</div>';
            } else {
                // else view information about classroom
                // and conduct checking for ended lesson
                $this->try_end_classperiod($lesson);

                $namelecturer = $DB->get_record('user', array('id'=>$lesson->lecturerid), 'firstname, lastname', MUST_EXIST);
                if ($lesson->groupid != 0) {
                    $numgroup = $DB->get_record('groups', array('id'=>$lesson->groupid), 'name', MUST_EXIST);
                    $namegroup = $numgroup->name;
                } else {
                    $namegroup = 'all groups';
                }
                $linknamelecturer = '<a href ="'. $CFG->wwwroot .'/user/profile.php?id=' . $lesson->lecturerid .'">' . $namelecturer->firstname . ' ' . $namelecturer->lastname .'</a>';
                $params = 'course=' . $COURSE->id . '&number=' . $classroomnumber;
                $endclassperiod = '<a href="'. $CFG->wwwroot . '/blocks/supervised/endclassperiod.php?' . $params. '">' . get_string('completeclassperiod', 'block_supervised') . '</a>';
                $maincontent = '<div>' 
                    . '<div>'
                        . '<div><p>'
                            . $linknamelecturer
                        . '</p></div>'
                        . '<div>'
                            . '<div><p>' 
                                . get_string('labelroom', 'block_supervised') . ':&nbsp;' . $classroomnumber 
                            . '</p></div>'
                        . '</div>'
                        . '<div>'
                            . '<div><p>' 
                                . get_string('timestart', 'block_supervised') . ':&nbsp;' . date("H:i:s", $lesson->starttimework) 
                            . '</p></div>'
                        . '</div>'
                        . '<div>'
                            . '<div><p>' 
                                . get_string('endtime', 'block_supervised') . ':&nbsp;' . date("H:i:s", ($lesson->starttimework + (60 * (int)$lesson->timework ) ) ) 
                            . '</p></div>'
                    . '</div>'
                    . '<div>'
                        . '<div><p>' 
                            . get_string('groups', 'block_supervised') . ':&nbsp;' . $namegroup 
                        . '</p></div>'
                    . '</div>'
                    . '<div align="center"><p>'
                        . $endclassperiod
                    . '</p></div>'
                    . '<div>'
                        . '<div align="center">'
                            . '<a href="'. $CFG->wwwroot. '/blocks/supervised/listclassroom.php'. '"main">' . get_string('classroomlistlabel', 'block_supervised') . '</div>' . '</a>'
                        . '</div>'
                        . '<div align="center">'
                            . '<a href="'. $CFG->wwwroot. '/blocks/supervised/report.php'. '"main">' . get_string('reportslabel', 'block_supervised') . '</div>' . '</a>'
                        . '</div>'
                    . '</div>'
                        . '</div>'
                    . '</div>';
            }

            $this->content = new stdClass;
            $this->content->text = $maincontent;
        } else {
            $namelecturer = '';
            $currentclassroomarray = get_current_classroom();
            $classroomnumber = $currentclassroomarray['number'];
            $lesson = get_start_classperiod($COURSE->id, $classroomnumber);

            // if classroom isn't free
            if ($lesson != null) {
                $name = $DB->get_record('user', array('id'=>$lesson->lecturerid), 'firstname, lastname', MUST_EXIST);
                $namelecturer .= '<a href ="'. $CFG->wwwroot .'/user/profile.php?id=' . $lesson->lecturerid .'">' . $name->firstname . ' ' . $name->lastname .'</a>';

                $this->try_end_classperiod($lesson);
            }

            $groupid = get_id_group_for_user($USER->id);

            if (isset($lesson)) {
                if ($groupid == $lesson->groupid || $lesson->groupid == 0) {
                    $timeend = '<div>'
                        . '<div align="center">' 
                            . '<p>' .get_string('endtime', 'block_supervised') . ':&nbsp;' . date("H:i:s", ($lesson->starttimework + (60 * (int)$lesson->timework ) ) ) 
                            . '</p>'
                        . '</div>'
                    . '</div>';
                    $lecturer = '<div>'
                        . '<div  align="center">'
                            . '<p>' . $namelecturer . '</p>'
                        . '</div>'
                    . '</div>';
                } else {
                    $timeend = '';
                    $lecturer = '';
                }
            } else {
                $timeend = '';
                $lecturer = '';
            }

            $stdcontent = '<div>'
            . '<div>'
                . '<div>'
                    . '<div>'
                        . '<div align ="center" width="100%">'
                            . '<p>' . get_string('labelroom', 'block_supervised') . '&nbsp;' . ' <b>' . $classroomnumber . '</b></p> ' 
                        . '</div>'
                    . '</div>'
                . '</div>'
                . $timeend
                . $lecturer
            . '</div>'
            . '</div>';

            $this->content = new stdClass;
            $this->content->text = $stdcontent;
        }

        return $this->content;
    } //get_content

    function try_end_classperiod($classperiod) {
        global $COURSE, $USER, $CFG, $DB;
        $name = $DB->get_record_select('user', 'id=:id', array('id'=>$classperiod->lecturerid), 'firstname, lastname');
        $currenttime = time();
        if ($currenttime - $classperiod->starttimework >= $classperiod->timework * 60) {
            $eventend = new object();
            $eventend->component            =    '/blocks/supervised/block_supervised.php';
            $eventend->name                 =    'endsession';
            $eventend->userfrom             =    'supervised';
            $eventend->userto               =    'allbloks';
            $eventend->subject              =    'lessons ended';
            $eventend->fullmessage          =    'lessons ended by lecturer: ' . $name->firstname . ' ' . $name->lastname ;
            $eventend->fullmessageformat    =    FORMAT_PLAIN;
            $eventend->fullmessagehtml      =    'lessons ended by lecturer <b>' . $name->firstname . ' ' . $name->lastname . '</b>';
            $eventend->endtime              =    time();        // time start classperiod
            $eventend->redirect             =    new moodle_url($CFG->wwwroot);
            $eventend->idlecturer           =    $classperiod->lecturerid;
            $eventend->idcourse             =    $COURSE->id;
            $eventend->startid              =    $classperiod->id;

            events_trigger('end_lesson', $eventend);    // send event about end lesson
        }
    }   // try_end_classperiod
    
    function applicable_formats() {
        return array('course-view' => true);
    }

    function cron() {
        global $CFG, $USER, $COURSE, $DB;
        require_once($CFG->dirroot.'/blocks/supervised/lib.php');
        // get all non-end lessons
        $courses = $DB->get_records('block_supervised', array('twinkeyid'=>'-1'), 'courseid');
        foreach ($courses as $course) {
            if (have_classperiod($course->courseid)) {
                $lesson = get_start_classperiod($course->courseid);
                $url = new moodle_url($CFG->wwwroot);
                echo "\nHave lesson for course with id:" . $course->courseid . " \n";
                // check at the end of lesson
                $this->try_end_classperiod($classperiod);
            } else {
                echo "\nHavn't lesson\n";
            }
        }
        return true;
    } // cron
    
    function before_delete() {
        global $DB;
        $quizs = $DB->get_records('quiz', array(), '', 'id, popup');
        foreach ($quizs as $quiz) {
            if ($quiz->popup > 127) {
                $DB->set_field('quiz', 'popup', $quiz->popup-128, array('id'=>$quiz->id));
            }
        }
    } // before_delete
    
} // block_supervised
 