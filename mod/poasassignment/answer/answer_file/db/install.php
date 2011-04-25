<?php

function xmldb_poasassignment_answertypes_answer_file_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'answer_file';
    $rec->path = 'answer\answer_file\answer_file.php';
    $DB->insert_record('poasassignment_plugins',$rec);
}