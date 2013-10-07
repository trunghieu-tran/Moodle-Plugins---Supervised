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
 * Preg question type backup code.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/backup_poasquestion_preg_plugin.class.php');

class backup_qtype_preg_plugin extends backup_qtype_poasquestion_plugin {

    protected function define_question_plugin_structure() {
        $plugin = parent::define_question_plugin_structure();
        $pluginwrapper = $plugin->get_child($this->get_recommended_name());
        $answers = $pluginwrapper->get_child('answers');
        $answer = $answers->get_child('answer');
        $qtypeobj = question_bank::get_qtype($this->pluginname);

        // Extra answer fields.
        $extraanswerfields = $qtypeobj->extra_answer_fields();
        $tablename = array_shift($extraanswerfields);
        $child = new backup_nested_element('regextests', array('id'), $extraanswerfields);
        $answer->add_child($child);
        $child->set_source_table($tablename, array('answerid' => backup::VAR_PARENTID));

        return $plugin;
    }
}
