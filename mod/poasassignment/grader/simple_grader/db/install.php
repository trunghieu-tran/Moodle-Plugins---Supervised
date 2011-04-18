<?php

function xmldb_poasassignment_simple_grader_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'simple_grader';
    $rec->path = 'grader\simple_grader\simple_grader.php';
    $DB->insert_record('poasassignment_graders',$rec);
}