<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../../config.php');
require_once ($CFG->dirroot . '/lib/questionlib.php');

/**
 * Provides the information to backup preg questions
 *
 * @author     Valeriy Streltsov, Volgograd State Technical University
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_preg_plugin extends backup_qtype_plugin {
/**
     * Attach to $element (usually questions) the needed backup structures
     * for question_answers for a given question
     * Used by various qtypes (calculated, essay, multianswer,
     * multichoice, numerical, shortanswer, truefalse)
     */
    protected function add_question_question_answers($element) {
        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_answers_bad_parent_element', $element);
        }

        // Define the elements
        $answers = new backup_nested_element('answers');
        $answer = new backup_nested_element('answer', array('id'), array(
            'answertext', 'answerformat', 'fraction', 'feedback',
            'feedbackformat'));

        // Build the tree
        $element->add_child($answers);
        $answers->add_child($answer);

        // Set the sources
        $answer->set_source_sql('
                SELECT *
                FROM {question_answers}
                WHERE question = :question
                ORDER BY id',
                array('question' => backup::VAR_PARENTID));

        // Aliases
        $answer->set_source_alias('answer', 'answertext');

        // don't need to annotate ids nor files
    }

    /**
     * Returns all the components and fileareas used by all the installed qtypes
     *
     * The method introspects each qtype, asking it about fileareas used. Then,
     * one 2-level array is returned. 1st level is the component name (qtype_xxxx)
     * and 2nd level is one array of filearea => mappings to look
     *
     * Note that this function is used both in backup and restore, so it is important
     * to use the same mapping names (usually, name of the table in singular) always
     *
     * TODO: Surely this can be promoted to backup_plugin easily and make it to
     * work for ANY plugin, not only qtypes (but we don't need it for now)
     */
    public static function get_components_and_fileareas($filter = null) {
        $components = array();
        // Get all the plugins of this type
        $qtypes = get_plugin_list('qtype');
        foreach ($qtypes as $name => $path) {
            // Apply filter if specified
            if (!is_null($filter) && $filter != $name) {
                continue;
            }
            // Calculate the componentname
            $componentname = 'qtype_' . $name;
            // Get the plugin fileareas (all them MUST belong to the same component)
            $classname = 'backup_qtype_' . $name . '_plugin';
            if (class_exists($classname)) {
                $elements = call_user_func(array($classname, 'get_qtype_fileareas'));
                if ($elements) {
                    // If there are elements, add them to $components
                    $components[$componentname] = $elements;
                }
            }
        }
        return $components;
    }

    /**
     * Returns one array with filearea => mappingname elements for the qtype
     *
     * Used by {@link get_components_and_fileareas} to know about all the qtype
     * files to be processed both in backup and restore.
     */
    public static function get_qtype_fileareas() {
        // By default, return empty array, only qtypes having own fileareas will override this
        return array();
    }

    /**
     * Returns the qtype information to attach to question element.
     * Based on extra_question_fields and extra_answer_fields.
     * You need to reimplement this if your question type uses additional
     * tables besides extra_question_fields table and extra_answer_fields table.
     */
    protected function define_question_plugin_structure() {
        $pluginname = $this->pluginname;
        $qtype = question_bank::get_qtype($pluginname);
        $questionidfield = 'question';    //$qtype->questionid_column_name();        TODO use questionid_column_name()
        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', $pluginname);
        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);
        // If this qtype uses standard question_answers, add them here
        // to the tree before any other information that will use them.
        //if ($qtype->use_answers()) {
            $this->add_question_question_answers($pluginwrapper);
        //}

        $extraquestionfields = $qtype->extra_question_fields();
        if (is_array($extraquestionfields)) {
            $questionextensiontable = array_shift($extraquestionfields);
            $question = new backup_nested_element($pluginname, array('id'), $extraquestionfields);
            $pluginwrapper->add_child($question);
            $question->set_source_table($questionextensiontable, array($questionidfield => backup::VAR_PARENTID));
        }
        /*$extraanswerfields = $qtype->extra_answer_fields();
        if (is_array($extraanswerfields)) {
            // TODO backup extra answer table.
        }*/

        return $plugin;
    }
}
