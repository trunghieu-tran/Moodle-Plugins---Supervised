<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Preg question type upgrade code.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_qtype_preg_upgrade($oldversion=0) {

    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2010072201) {

        // Define field exactmatch to be added to question_preg.
        $table = new xmldb_table('question_preg');
        $field = new xmldb_field('exactmatch', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'rightanswer');
        // Launch add field exactmatch.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2010072201, 'qtype', 'preg');
    }

    if ($oldversion < 2010080800) {
        $table = new xmldb_table('question_preg');
        // Define field usehint to be added to question_preg.
        $field = new xmldb_field('usehint', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'exactmatch');
        // Conditionally launch add field usehint.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field hintpenalty to be added to question_preg.
        $field = new xmldb_field('hintpenalty', XMLDB_TYPE_FLOAT, '4, 2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'usehint');
        // Launch add field hintpenalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2010080800, 'qtype', 'preg');
    }

    if ($oldversion < 2010081600) {
        $table = new xmldb_table('question_preg');
        // Define field hintgradeborder to be added to question_preg.
        $field = new xmldb_field('hintgradeborder', XMLDB_TYPE_FLOAT, '4, 2', XMLDB_UNSIGNED,
                                 XMLDB_NOTNULL, null, '1', 'hintpenalty');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field engine to be added to question_preg.
         $field = new xmldb_field('engine', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL,
                                  null, 'preg_php_matcher', 'hintgradeborder');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Rename field rightanswer on table question_preg to correctanswer.
        $table = new xmldb_table('question_preg');
        $field = new xmldb_field('rightanswer', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'usecase');
        // Launch rename field rightanswer.
        $dbman->rename_field($table, $field, 'correctanswer');

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2010081600, 'qtype', 'preg');
    }

    if ($oldversion < 2011111900) {

        // Define field notation to be added to question_preg.
        $table = new xmldb_table('question_preg');
        $field = new xmldb_field('notation', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'native', 'engine');

        // Conditionally launch add field notation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2011111900, 'qtype', 'preg');
    }

    if ($oldversion < 2011121200) {

        // Define field notation to be added to question_preg.
        $table = new xmldb_table('question_preg');

        // Launch rename table for quiz_reports.
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'qtype_preg');
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2011121200, 'qtype', 'preg');
    }

    if ($oldversion < 2012011300) {

        // Rename fields.
        $queries = array("UPDATE {qtype_preg} SET engine='dfa_matcher' WHERE engine='dfa_preg_matcher'",
                         "UPDATE {qtype_preg} SET engine='nfa_matcher' WHERE engine='nfa_preg_matcher'",
                         "UPDATE {qtype_preg} SET engine='php_preg_matcher' WHERE engine='preg_php_matcher'",
                         "UPDATE {config} SET value='dfa_matcher' WHERE value='dfa_preg_matcher'",
                         "UPDATE {config} SET value='nfa_matcher' WHERE value='nfa_preg_matcher'",
                         "UPDATE {config} SET value='php_preg_matcher' WHERE value='preg_php_matcher'");

        foreach ($queries as $query) {
            $DB->execute($query);
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2012011300, 'qtype', 'preg');
    }

    if ($oldversion < 2012072300) {
        // Define field uselexemhint to be added to qtype_preg.
        $table = new xmldb_table('qtype_preg');
        $field = new xmldb_field('uselexemhint', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'notation');

        // Conditionally launch add field uselexemhint.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field lexemhintpenalty to be added to qtype_preg.
        $field = new xmldb_field('lexemhintpenalty', XMLDB_TYPE_FLOAT, '4, 2', null, XMLDB_NOTNULL, null, '0', 'uselexemhint');

        // Conditionally launch add field lexemhintpenalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field langid to be added to qtype_preg.
        $field = new xmldb_field('langid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'lexemhintpenalty');

        // Conditionally launch add field langid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field lexemusername to be added to qtype_preg.
        $field = new xmldb_field('lexemusername', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, 'word', 'langid');

        // Conditionally launch add field lexemusername.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Rename field usehint on table qtype_preg to usecharhint.
        $field = new xmldb_field('usehint', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'exactmatch');
        // Launch rename field usehint.
        $dbman->rename_field($table, $field, 'usecharhint');

        // Rename field hintpenalty on table qtype_preg to charhintpenalty.
        $field = new xmldb_field('hintpenalty', XMLDB_TYPE_FLOAT, '4, 2', null, XMLDB_NOTNULL, null, '0', 'usecharhint');

        // Launch rename field hintpenalty.
        $dbman->rename_field($table, $field, 'charhintpenalty');

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2012072300, 'qtype', 'preg');
    }

    if ($oldversion < 2012090300) {
        // Remove temporarily added pcrestrict notation as redundant.
        $query = "UPDATE {qtype_preg} SET notation='native' WHERE notation='pcrestrict'";
        $DB->execute($query);

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2012090300, 'qtype', 'preg');
    }

    if ($oldversion < 2013062600) {
         // Rename field question on table qtype_preg to questionid.
        $table = new xmldb_table('qtype_preg');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field question.
        $dbman->rename_field($table, $field, 'questionid');

        // Define field answers to be dropped from qtype_preg.
        $table = new xmldb_table('qtype_preg');
        $field = new xmldb_field('answers');

        // Conditionally launch drop field answers.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define key questionid (foreign-unique) to be added to qtype_preg.
        $table = new xmldb_table('qtype_preg');
        $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid'), 'question', array('id'));

        // Launch add key questionid.
        $dbman->add_key($table, $key);

        // Define table qtype_preg to be renamed to qtype_preg_options.
        $table = new xmldb_table('qtype_preg');

        // Launch rename table for qtype_preg.
        $dbman->rename_table($table, 'qtype_preg_options');

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2013062600, 'qtype', 'preg');
    }

    if ($oldversion < 2013071400) {

        // Define table qtype_preg_regex_tests to be created.
        $table = new xmldb_table('qtype_preg_regex_tests');

        // Adding fields to table qtype_preg_regex_tests.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('tablename', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('tableid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('regextests', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table qtype_preg_regex_tests.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for qtype_preg_regex_tests.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2013071400, 'qtype', 'preg');
    }

    if ($oldversion < 2013100500) {

        // Rename field tableid on table qtype_preg_regex_tests to NEWNAMEGOESHERE.
        $table = new xmldb_table('qtype_preg_regex_tests');
        $field = new xmldb_field('tableid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'tablename');

        // Launch rename field tableid.
        $dbman->rename_field($table, $field, 'answerid');

        // Define field tablename to be dropped from qtype_preg_regex_tests.
        $table = new xmldb_table('qtype_preg_regex_tests');
        $field = new xmldb_field('tablename');

        // Conditionally launch drop field tablename.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2013100500, 'qtype', 'preg');
    }

    // There ends code for Preg 2.5 release upgrade.

    if ($oldversion < 2014042200) {

        // Change matching engine.
        $queries = array("UPDATE {qtype_preg_options} SET engine='fa_matcher' WHERE engine='dfa_matcher'",
                         "UPDATE {qtype_preg_options} SET engine='fa_matcher' WHERE engine='nfa_matcher'",
                         "UPDATE {config} SET value='fa_matcher' WHERE value='dfa_matcher'",
                         "UPDATE {config} SET value='fa_matcher' WHERE value='nfa_matcher'");

        foreach ($queries as $query) {
            $DB->execute($query);
        }

        // Preg savepoint reached.
        upgrade_plugin_savepoint(true, 2014042200, 'qtype', 'preg');
    }

    return true;
}
