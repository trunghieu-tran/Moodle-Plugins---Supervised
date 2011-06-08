<?php
function xmldb_poasassignmenttaskgivers_categorychoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'categorychoice';
    $rec->path = 'taskgivers\categorychoice\categorychoice.php';
    //$rec->langpath = 'taskgivers\categorychoice\lang';
    if(!$DB->record_exists('poasassignment_taskgivers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_taskgivers', $rec);
}