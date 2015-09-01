<?php

function xmldb_poasassignmentanswertypes_answer_text_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'answer_text';
    $rec->path = 'answer/answer_text/answer_text.php';
    if(!$DB->record_exists('poasassignment_answers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_answers', $rec);
}