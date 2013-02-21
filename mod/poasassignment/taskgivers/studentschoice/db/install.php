<?php

function xmldb_poasassignmenttaskgivers_studentschoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'studentschoice';
    $rec->path = 'taskgivers/studentschoice/studentschoice.php';
    //$rec->langpath = 'taskgivers\studentschoice\lang';
    if(!$DB->record_exists('poasassignment_taskgivers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_taskgivers', $rec);
}