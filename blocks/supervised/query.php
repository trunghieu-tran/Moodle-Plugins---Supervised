<?php
require_once('../../config.php');
require_once($CFG->libdir . '/eventslib.php');

$classroom          =   required_param('classroom_list', PARAM_INT);
$group              =   required_param('group_list', PARAM_INT); 
$link               =   required_param('links', PARAM_URL);
$id                 =   required_param('id', PARAM_INT);
$durationhours      =   required_param('duration_hours', PARAM_INT); 
$durationminutes    =   required_param('duration_minutes', PARAM_INT); 

if ($classroom == 0 || ($durationhours + $durationminutes * 60 == 0) ) {
    redirect($link, get_string('errorinputtime', 'block_supervised'));
}

$eventstart = new object();
$eventstart->component          =   '/blocks/supervised/query.php';    
$eventstart->name               =   'startsession';
$eventstart->userfrom           =   'supervised';
$eventstart->userto             =   'allbloks';
$eventstart->subject            =   'lessons started';
$eventstart->fullmessage        =   'lessons started by lecturer: ' . $USER->firstname . ' ' . $USER->lastname ;
$eventstart->fullmessageformat  =   FORMAT_PLAIN;
$eventstart->fullmessagehtml    =   'lessons started by lecturer <b>' . $USER->firstname . ' ' . $USER->lastname . '</b>';
$eventstart->starttime          =   time();     // time start lesson
$eventstart->duration           =   ($durationhours * 60) + $durationminutes;
$eventstart->classroom          =   required_param('classroom_list', PARAM_INT); 
$eventstart->numbergroup        =   required_param('group_list', PARAM_INT); 
$eventstart->redirect           =   required_param('links', PARAM_URL);
$eventstart->idlecturer         =   $USER->id;
$eventstart->idcourse           =   required_param('id', PARAM_INT);

events_trigger('start_lesson', $eventstart);    // send event about start lesson