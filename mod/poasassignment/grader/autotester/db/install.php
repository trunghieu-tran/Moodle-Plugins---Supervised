<?php

function xmldb_poasassignment_autotester_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'autotester';
    $rec->path = 'grader/autotester/autotester.php';
    if(!$DB->record_exists('poasassignment_graders', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_graders', $rec);
}