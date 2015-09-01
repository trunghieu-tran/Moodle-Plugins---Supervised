<?php

function xmldb_poasassignment_remote_autotester_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'remote_autotester';
    $rec->path = 'grader/remote_autotester/remote_autotester.php';
    if(!$DB->record_exists('poasassignment_graders', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_graders', $rec);

}