<?php
// This file is part of Correct Writing question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Correct Writing question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Correct Writing is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Correct Writing question type upgrade code.
 *
 * @package    qtype_correctwriting
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');

function xmldb_qtype_correctwriting_upgrade($oldversion=0) {

    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2013011500) {

        // Define field whatishintpenalty to be added to qtype_correctwriting
        $table = new xmldb_table('qtype_correctwriting');
        $field = new xmldb_field('whatishintpenalty', XMLDB_TYPE_NUMBER, '4, 2', null, XMLDB_NOTNULL, null, '1.1', 'maxmistakepercentage');

        // Conditionally launch add field whatishintpenalty
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2013011500, 'qtype', 'correctwriting');
    }

    if ($oldversion < 2013011800) {

        // Define field wheretxthintpenalty to be added to qtype_correctwriting
        $table = new xmldb_table('qtype_correctwriting');
        $field = new xmldb_field('wheretxthintpenalty', XMLDB_TYPE_NUMBER, '4, 2', null, XMLDB_NOTNULL, null, '1.1', 'whatishintpenalty');

        // Conditionally launch add field wheretxthintpenalty
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2013011800, 'qtype', 'correctwriting');
    }

    if ($oldversion < 2013012300) {

        // Define field absenthintpenaltyfactor to be added to qtype_correctwriting
        $table = new xmldb_table('qtype_correctwriting');
        $field = new xmldb_field('absenthintpenaltyfactor', XMLDB_TYPE_NUMBER, '4, 1', null, XMLDB_NOTNULL, null, '1', 'wheretxthintpenalty');

        // Conditionally launch add field absenthintpenaltyfactor
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2013012300, 'qtype', 'correctwriting');
    }
    if ($oldversion < 2013012900) {
        // Define field wherepichintpenalty to be added to qtype_correctwriting
        $table = new xmldb_table('qtype_correctwriting');
        $field = new xmldb_field('wherepichintpenalty', XMLDB_TYPE_NUMBER, '4, 2', null, XMLDB_NOTNULL, null, '1.1', 'absenthintpenaltyfactor');

        // Conditionally launch add field wherepichintpenalty
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2013012900, 'qtype', 'correctwriting');
    }
    
    $updateanalyzersenables = function() {
        global $DB;
        $record = new stdClass();
        $record->islexicalanalyzerenabled = 0;
        $record->isenumanalyzerenabled = 0;
        $record->issequenceanalyzerenabled = 1;
        $record->issyntaxanalyzerenabled = 0;
        $result = $DB->get_records('qtype_correctwriting', null, 'id', 'id');
        if (count($result)) {
            foreach($result as $id => $object) {
                $record->id = $id;
                $DB->update_record('qtype_correctwriting', $record, true);
            }
        }
    };

    if ($oldversion < 2013092400) {
        $table = new xmldb_table('qtype_correctwriting');
        $fieldnames = array(
            'islexicalanalyzerenabled' => 'wherepichintpenalty',
            'isenumanalyzerenabled' => 'islexicalanalyzerenabled',
            'issequenceanalyzerenabled' => 'isenumanalyzerenabled',
            'issyntaxanalyzerenabled' =>  'issequenceanalyzerenabled'
        );

        foreach($fieldnames as $name => $previous) {
            $defaultvalue = ($name == 'issequenceanalyzerenabled') ? '1' : '0';
            $field = new xmldb_field($name, XMLDB_TYPE_INTEGER, '4', null ,XMLDB_NOTNULL, null, $defaultvalue, $previous);
            $dbman->add_field($table, $field);
        }

        $updateanalyzersenables();

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2013092400, 'qtype', 'correctwriting');
    }

    if ($oldversion < 2015033100) {
        // Repeat action from last part to make sure 2.6.1 release works normally
        $updateanalyzersenables();
        
        // Define field whatishintpenalty to be added to qtype_correctwriting
        $table = new xmldb_table('qtype_correctwriting');
        $field = new xmldb_field('howtofixpichintpenalty', XMLDB_TYPE_NUMBER, '4, 2', null, XMLDB_NOTNULL, null, '1.1', 'issyntaxanalyzerenabled');

        // Conditionally launch add field whatishintpenalty
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2015033100, 'qtype', 'correctwriting');
    }

    if ($oldversion < 2015071000) {
        // Fix bugs, linked to syntax analyzer, being enabled to all question
        // Also, if enum analyzer is enabled in incorrect cases - disable it too
        $record = new stdClass();
        $record->islexicalanalyzerenabled = 0;
        $record->isenumanalyzerenabled = 0;
        $record->issequenceanalyzerenabled = 1;
        $record->issyntaxanalyzerenabled = 0;
        $result = $DB->get_records(
            'qtype_correctwriting',
            array('issyntaxanalyzerenabled' => 1),
            'id',
            implode(',', array(
                'id',
                'langid',
                'questionid',
                'islexicalanalyzerenabled',
                'isenumanalyzerenabled',
                'issequenceanalyzerenabled',
                'issyntaxanalyzerenabled'
            ))
        );
        if (count($result)) {
            $langidtolangs = array();
            $questionidsforfullyupdated = array();
            foreach($result as $id => $object) {
                $islexicalanalyzerenabled = intval($object->islexicalanalyzerenabled);
                $isenumanalyzerenabled = intval($object->isenumanalyzerenabled);
                $issequenceanalyzerenabled = intval($object->issequenceanalyzerenabled);
                if ($islexicalanalyzerenabled == 1
                    && $isenumanalyzerenabled == 1
                    && $issequenceanalyzerenabled == 1) {
                    $questionidsforfullyupdated []= $object->questionid;
                }
            }
            list($insql, $params) = $DB->get_in_or_equal($questionidsforfullyupdated);
            $sql = 'SELECT id, timemodified
            FROM {question}
            WHERE id ' . $insql;
            $questions = $DB->get_records_sql($sql, $params);
            foreach($result as $id => $object) {
                $islexicalanalyzerenabled = intval($object->islexicalanalyzerenabled);
                $isenumanalyzerenabled = intval($object->isenumanalyzerenabled);
                $issequenceanalyzerenabled = intval($object->issequenceanalyzerenabled);
                $object->issyntaxanalyzerenabled = 0;
                if ($isenumanalyzerenabled == 1) {
                    // Fetch lang
                    $lang = null;
                    if (array_key_exists($object->langid, $langidtolangs)) {
                        $lang = $langidtolangs[$object->langid];
                    } else {
                        $lang = block_formal_langs::lang_object($object->langid);
                        $langidtolangs[$object->langid] = $lang;
                    }

                    if ($lang == null) {
                        $object->isenumanalyzerenabled = 0;
                    } else {
                        if ($lang->name() != 'cpp_parseable') {
                            $object->isenumanalyzerenabled = 0;
                        }
                    }
                }
                // Timestamp for 4 Jun 2015 5:42 - to make sure all timezones will be covered
                $release28 = 1435988520;
                if ($islexicalanalyzerenabled == 1
                    && $isenumanalyzerenabled == 1
                    && $issequenceanalyzerenabled == 1
                    && $islexicalanalyzerenabled == 1) {
                    $shouldmodify = true;
                    if (array_key_exists($object->questionid, $questions)) {
                        // Do not modify already changed questions
                        $timemodified = intval($questions[$object->questionid]->timemodified);
                        $shouldmodify = $timemodified  < $release28;
                    }
                    if ($shouldmodify) {
                        $object->islexicalanalyzerenabled = 0;
                        $object->isenumanalyzerenabled = 0;
                        $object->issequenceanalyzerenabled = 1;
                        $object->islexicalanalyzerenabled = 0;
                    }
                }

                $DB->update_record('qtype_correctwriting', $object, true);
            }
        }

        // correctwriting savepoint reached
        upgrade_plugin_savepoint(true, 2015071000, 'qtype', 'correctwriting');
    }


    return true;
}