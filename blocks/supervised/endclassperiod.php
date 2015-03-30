<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/supervised/lib.php');

global $DB;

$number =   required_param('number', PARAM_INT);  
$course =   required_param('course', PARAM_INT);  
if (isset($number) && isset($course)) {
    if (have_classperiod($course, $number)) {
        $classperiod = get_start_classperiod($course, $number);
        if ($classperiod != null) {
            // get name lecturer
            $lastname = $DB->get_field('user', 'lastname', array('id'=>$classperiod->lecturerid));
            $firstname = $DB->get_field('user', 'firstname', array('id'=>$classperiod->lecturerid));
            $eventend = new object();
            $eventend->component            =    '/blocks/supervised/block_supervised.php';
            $eventend->name                 =    'end';
            $eventend->userfrom             =    'supervised';
            $eventend->userto               =    'allbloks';
            $eventend->subject              =    'lessons ended';
            //$eventend->fullmessage          =    'lessons ended by lecturer: ' . $name->firstname . ' ' . $name->lastname ;
            $eventend->fullmessageformat    =    FORMAT_PLAIN;
            //$eventend->fullmessagehtml      =    'lessons ended by lecturer <b>' . $name->firstname . ' ' . $name->lastname . '</b>';
            $eventend->endtime              =    time();        // time start classperiod
            $eventend->redirect             =    new moodle_url($CFG->wwwroot);
            $eventend->idlecturer           =    $classperiod->lecturerid;
            $eventend->idcourse             =    $course;
            $eventend->startid              =    $classperiod->id;

            events_trigger('end_lesson', $eventend);    // send event about end lesson
            redirect($CFG->wwwroot);
        }
    }
}