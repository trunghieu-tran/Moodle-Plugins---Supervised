<?php
function xmldb_poasassignmenttaskgivers_parameterchoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'parameterchoice';
    $rec->path = 'taskgivers/parameterchoice/parameterchoice.php';
    //$rec->langpath = 'taskgivers\parameterchoice\lang';
    if(!$DB->record_exists('poasassignment_taskgivers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_taskgivers', $rec);
}