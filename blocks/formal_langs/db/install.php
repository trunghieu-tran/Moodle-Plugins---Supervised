<?php

function xmldb_block_formal_langs_install() {
    global $DB;

    $lang = new stdClass();
    $lang->ui_name = 'Simple english';
    $lang->description = 'Simple english language definition';
    $lang->name ='simpeng';
    $lang->version='1.0';
    $lang->visible = 1;
    
    $DB->insert_record('block_formal_langs',$object);


}

