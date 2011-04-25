<?php

function xmldb_poasassignment_taskgivers_randomchoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'randomchoice';
    $rec->path = 'taskgivers\randomchoice\randomchoice.php';
    $rec->langpath = 'taskgivers\randomchoice\lang';
    $DB->insert_record('poasassignment_taskgivers',$rec);
}