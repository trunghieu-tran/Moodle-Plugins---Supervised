<?php

function xmldb_poasassignment_answertypes_answer_text_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'answer_text';
    $rec->path = 'answer\answer_text\answer_text.php';
    $DB->insert_record('poasassignment_answers',$rec);
}