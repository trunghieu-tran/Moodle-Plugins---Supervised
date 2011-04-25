<?php
function xmldb_poasassignment_taskgivers_parameterchoice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'parameterchoice';
    $rec->path = 'taskgivers\parameterchoice\parameterchoice.php';
    $rec->langpath = 'taskgivers\parameterchoice\lang';
    $DB->insert_record('poasassignment_taskgivers',$rec);
}