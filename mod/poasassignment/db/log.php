<?php

defined('MOODLE_INTERNAL') || die();

// Install default common logging actions
$logs = array(
    array('module'=>'poasassignment', 'action'=>'add', 'mtable'=>'poasassignment', 'field'=>'name'),
    array('module'=>'poasassignment', 'action'=>'update', 'mtable'=>'poasassignment', 'field'=>'name'),
    array('module'=>'poasassignment', 'action'=>'view', 'mtable'=>'poasassignment', 'field'=>'name'),
    array('module'=>'poasassignment', 'action'=>'view all', 'mtable'=>'poasassignment', 'field'=>'name'),
);