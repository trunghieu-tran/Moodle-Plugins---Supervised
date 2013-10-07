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
 * Preg question type restore code.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/restore_poasquestion_preg_plugin.class.php');

class restore_qtype_preg_plugin extends restore_qtype_poasquestion_plugin {

    // Current answer being processed.
    protected $currentanswer;

    /**
      * Returns the paths to be handled by the plugin at question level.
      */
    protected function define_question_plugin_structure() {
        $paths = parent::define_question_plugin_structure();

        $qtypeobj = question_bank::get_qtype($this->pluginname);
        $this->qtype = $qtypeobj;

        // Add in-depth paths
        $elepath = $this->get_pathfor('/answers/answer/regextests');
        $paths[] = new restore_path_element('regextests', $elepath);

        return $paths;
    }

    public function process_regextests($data) {
        global $DB;

        $data['answerid'] = $this->currentanswer['newid'];
        /*$newtestid =*/ $DB->insert_record('qtype_preg_regex_tests', $data);
    }

    public function process_question_answer($data) {
        parent::process_question_answer($data);
        $this->currentanswer = $data;
        $this->currentanswer['newid'] = $this->get_mappingid('question_answer', $data['id']);
    }

    public function process_preg($data) {
        $this->process_poasquestion($data);
    }
}
