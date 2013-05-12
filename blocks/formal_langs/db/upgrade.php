<?php


function xmldb_block_formal_langs_upgrade($oldversion = 0) {
    global $CFG, $DB;

    if ($oldversion < 2013030700) {
        $lang = new stdClass();
        $lang->ui_name = 'C++ programming language';
        $lang->description = 'C++ language, with only lexer';
        $lang->name = 'cpp_language';
        $lang->scanrules = null;
        $lang->parserules = null;
        $lang->version='1.0';
        $lang->visible = 1;

        $DB->insert_record('block_formal_langs',$lang);
    }

    if ($oldversion < 2013041400) {
        $lang = new stdClass();
        $lang->ui_name = 'C formatting string rules';
        $lang->description = 'C formatting string rules, as used in printf';
        $lang->name = 'printf_language';
        $lang->scanrules = null;
        $lang->parserules = null;
        $lang->version='1.0';
        $lang->visible = 1;

        $DB->insert_record('block_formal_langs',$lang);
    }

    return true;
}