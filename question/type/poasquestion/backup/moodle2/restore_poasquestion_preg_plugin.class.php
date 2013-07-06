<?php
// This file is part of Poasquestion question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Poasquestion question type is free software: you can redistribute it and/or modify
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
 * POAS abstract question type restore code.
 *
 * @package    qtype_poasquestion
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/bank.php');

class restore_qtype_poasquestion_plugin extends restore_qtype_plugin {


    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {
        $qtypeobj = question_bank::get_qtype($this->pluginname);
        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elepath = $this->get_pathfor('/' . $qtypeobj->name());
        $paths[] = new restore_path_element($qtypeobj->name(), $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/... element.
     */
    public function process_poasquestion($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_... too.
        if ($questioncreated) {
            $qtypeobj = question_bank::get_qtype($this->pluginname);
            $extraquestionfields = $qtypeobj->extra_question_fields();
            $tablename = array_shift($extraquestionfields);

            // Adjust some columns.
            $qtfield = $qtypeobj->questionid_column_name();
            $data->$qtfield = $newquestionid;

            // Insert record.
            $newitemid = $DB->insert_record($tablename, $data);

            // Create mapping.
            $this->set_mapping($tablename, $oldid, $newitemid);
        }
    }
}
