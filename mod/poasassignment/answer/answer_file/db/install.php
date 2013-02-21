<?php

function xmldb_poasassignmentanswertypes_answer_file_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'answer_file';
    $rec->path = 'answer/answer_file/answer_file.php';
    if(!$DB->record_exists('poasassignment_answers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_answers', $rec);
}