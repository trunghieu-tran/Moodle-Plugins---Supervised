<?php

function xmldb_poasassignment_taskgivers_studentschoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'studentschoice';
    $rec->path = 'taskgivers\studentschoice\studentschoice.php';
    $rec->langpath = 'taskgivers\studentschoice\lang';
    $DB->insert_record('poasassignment_taskgivers',$rec);
}