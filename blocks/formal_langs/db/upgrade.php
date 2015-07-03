<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');


function xmldb_block_formal_langs_upgrade($oldversion = 0) {
    global $CFG, $DB;

    if ($oldversion < 2013030700) {
        $lang = new stdClass();
        $lang->uiname = 'C++ programming language';
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
        $lang->uiname = 'C formatting string rules';
        $lang->description = 'C formatting string rules, as used in printf';
        $lang->name = 'printf_language';
        $lang->scanrules = null;
        $lang->parserules = null;
        $lang->version='1.0';
        $lang->visible = 1;

        $DB->insert_record('block_formal_langs',$lang);
    }

    if ($oldversion < 2013071900) {
        $dbman = $DB->get_manager();
        $bfl = new xmldb_table('block_formal_langs');
        $lexemenamefield = new xmldb_field('lexemname', XMLDB_TYPE_TEXT ,null,null,null, null, null, 'visible');
        $dbman->add_field($bfl, $lexemenamefield);
    }

    if ($oldversion < 2013091800) {
        $dbman = $DB->get_manager();
        $bfl = new xmldb_table('block_formal_langs');
        $uinamefield = new xmldb_field('ui_name', XMLDB_TYPE_TEXT ,null,null,null, null, null, 'id');
        $dbman->rename_field($bfl, $uinamefield, 'uiname');
    }

    if ($oldversion < 2013091817) {
        $dbman = $DB->get_manager();
        $perms = new xmldb_table('block_formal_langs_perms');

        $field = new xmldb_field('id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $perms->addField($field);

        $field = new xmldb_field('languageid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null);
        $perms->addField($field);

        $field = new xmldb_field('contextid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null);
        $perms->addField($field);

        $field = new xmldb_field('visible');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null);
        $perms->addField($field);

        $idpk = new xmldb_key('primary');
        $idpk->set_attributes(XMLDB_KEY_PRIMARY, array('id'), null, null);
        $perms->addKey($idpk);

        $dbman->create_table($perms);
    }

    if ($oldversion < 2013091818) {
        block_formal_langs::sync_contexts_with_config();
    }

    if ($oldversion < 2013111018)  {
        block_formal_langs::sync_contexts_with_config();
    }

    if ($oldversion < 2013120600) {
        $dbman = $DB->get_manager();
        $bfl = new xmldb_table('block_formal_langs');
        // Rename old buggy update, if somebody applied it
        if ($dbman->field_exists($bfl, 'lexemename')) {
            $lexemenamefield = new xmldb_field('lexemename', XMLDB_TYPE_TEXT ,null,null,null, null, null, 'visible');
            $dbman->rename_field($bfl, $lexemenamefield, 'lexemname');
        }
        $field = new xmldb_field('author');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'lexemname');
        $dbman->add_field($bfl, $field);
    }

    if ($oldversion < 2014060500) {
        /*
		$lang = new stdClass();
        $lang->uiname = 'C++ parseable programming language';
        $lang->description = 'C++ parseable language';
        $lang->name = 'cpp_parseable_language';
        $lang->scanrules = null;
        $lang->parserules = null;
        $lang->version='1.0';
        $lang->visible = 1;
        $lang->lexemname = '';
        $lang->version='1.0';
        $lang->visible = 1;

        $DB->insert_record('block_formal_langs',$lang);
		*/
    }

    if ($oldversion < 2015050600)  {
        $dbman = $DB->get_manager();
        $langname = 'cpp_parseable_language';
        $clause = $DB->sql_compare_text('name')  . ' =  ?';
        $statement = 'SELECT * FROM {block_formal_langs} WHERE ' . $clause;
        $parseablelang = $DB->get_record_sql($statement, array($langname));
        $langname = 'cpp_language';
        $cpplang = $DB->get_record_sql($statement, array($langname));
        if ($parseablelang !== false && $dbman->table_exists('qtype_correctwriting')) {
            $dependentquestions = $DB->get_records('qtype_correctwriting', array('langid' => $parseablelang->id));
            if (count($dependentquestions)) {
                foreach($dependentquestions as $id => $qobj) {
                    $qobj->langid = $cpplang->id;
                    $DB->update_record('qtype_correctwriting', $qobj);
                }
            }
        }
        if ($cpplang !== false) {
            $cpplang->name = 'cpp_parseable_language';
            $cpplang->description = 'C++ language with basic preprocessor support';
            $DB->update_record('block_formal_langs', $cpplang);
        }
    }

    return true;
}