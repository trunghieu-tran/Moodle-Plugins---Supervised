<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A backup provider for correctwriting question
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @author Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once ($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/backup_poasquestion_plugin.class.php');

class backup_qtype_correctwriting_plugin extends backup_qtype_poasquestion_plugin {


    /**
     * Includes another dependent data into plugin
     * @return backup_plugin_element
     */
    protected function define_question_plugin_structure() {
        $plugin = parent::define_question_plugin_structure();
        /**
         * @var backup_plugin_element $pluginwrapper
         */
        $pluginwrapper = $plugin->get_child($this->get_recommended_name());

        $qtypeobj = question_bank::get_qtype($this->pluginname);

        // Why we add those into plugin wrapper? Because there
        // are no way we could reach a question id otherwise
        // It will cause a errors in backup otherwise, since id structure
        // will be broken

        $langfields = array('ui_name', 'description', 'name', 'scanrules', 'parserules', 'version', 'visible');
        $child = new backup_nested_element($qtypeobj->name() . '_language', array('id'), $langfields);
        $pluginwrapper->add_child($child);
        $child->set_source_sql('
            SELECT * FROM {block_formal_langs}
             WHERE id IN (SELECT langid FROM {qtype_correctwriting}
             WHERE questionid = :question);
        ',
        array('question' => backup::VAR_PARENTID)
        );

        // Because we can't include descriptions in answer, we
        // include them as one table part

        $dscrfields = array('tableid', 'number', 'description');
        $child = new backup_nested_element($qtypeobj->name() . '_descriptions', array('id'), $dscrfields);
        $pluginwrapper->add_child($child);
        $child->set_source_sql('
            SELECT * FROM {block_formal_langs_node_dscr}
            WHERE tablename = \'question_answers\' AND tableid IN (
              SELECT id FROM {question_answers} WHERE question = :question
            );
        ',
        array('question' => backup::VAR_PARENTID)
        );


        return $plugin;
    }

}
