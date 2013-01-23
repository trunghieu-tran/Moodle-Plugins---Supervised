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
 * A restore provider for correctwriting question
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 require_once ($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/restore_poasquestion_preg_plugin.class.php');
 
 class restore_qtype_correctwriting_plugin extends restore_qtype_poasquestion_plugin {
     /**
      * Processes question data
      * @var
      */
     protected $question_data;

     /**
      * Returns the paths to be handled by the plugin at question level.
      */
     protected function define_question_plugin_structure() {
         $paths = parent::define_question_plugin_structure();

         $qtypeobj = question_bank::get_qtype($this->pluginname);

         // Add in-depth paths
         $elepath = $this->get_pathfor('/' . $qtypeobj->name() . '_language');
         $paths[] = new restore_path_element('question_language', $elepath);
         $elepath = $this->get_pathfor('/' . $qtypeobj->name() . '_descriptions');
         $paths[] = new restore_path_element('question_descriptions', $elepath);

         return $paths; // And we return the interesting paths.
     }

     /**
      * Processes question descriptions
      * This  method is called when restoring path question_descriptions
      * @param array $data
     */
    public function process_correctwriting($data) {
        $this->question_data = $data;
        $this->process_poasquestion($data);

    }

     /**
      * Processes question language
      * @param array $data
      */
     public function process_question_language($data) {
         global $DB;

         $data = (object)$data;
         $oldid = $data->id;

         // Detect if the question is created or mapped
         // TODO: Move it in common code
         $oldquestionid   = $this->get_old_parentid('question');
         $newquestionid   = $this->get_new_parentid('question');
         $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

         /**
          * Here we have data array as defined
          * [id] =>
          * [ui_name] =>
          * [description] =>
          * [name] =>
          * [scanrules] =>
          * [parserules] =>
          * [version] =>
          * [visible] =>
          */
     }

     /**
      * Processes question descriptions
      * This  method is called when restoring path question_descriptions
      * @param array $data
      */
     public function process_question_descriptions($data) {
         // Detect if the question is created or mapped
         // TODO: Move it in common code

         /**
          * Here we have data array as defined
          * [id]=>
          * [tableid] =>
          * [number] =>
          * [description] =>
          */

     }
 }
 
 