<?php
function xmldb_poasassignmenttaskgivers_analogy_choice_install() {
    global $DB;
    $rec = new stdClass();
    $rec->name = 'analogy_choice';
    $rec->path = 'taskgivers/analogy_choice/analogy_choice.php';
    if(!$DB->record_exists('poasassignment_taskgivers', array('name' => $rec->name)))
        $DB->insert_record('poasassignment_taskgivers', $rec);
}