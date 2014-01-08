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
 * A restore provider for correctwriting question
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @author Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/restore_poasquestion_plugin.class.php');
require_once ($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');

class restore_qtype_correctwriting_plugin extends restore_qtype_poasquestion_plugin {
     /**
      * Language id
      * @var int
      */
     protected $langid;
    /**
     * Abstract language data
     * @var block_formal_langs_abstract_language
     */
     protected $langobject;
     /**
      * Old question id
      * @var int
      */
     protected $oldquestion;
     /**
      * New question id
      * @var  int
      */
     protected $newquestion;
     /**
      * Question type of plugin
      * @var  question_type
      */
     protected $qtype;
     /**
      * Whether question is created
      * @var bool
      */
     protected $questioncreated;
     /**
      * Answer mapping data
      * @var array
      */
     protected $answermapping;
     /**
      * Mapping of answer to count of lexemes in it by oldid
      * @var array
      */
     protected $tokencountmapping;
    /**
     * Mapping of answers to array of token description by oldid
     * @var  array
     */
    protected $tokendescriptionmapping;
     /**
      * Returns the paths to be handled by the plugin at question level.
      */
     protected function define_question_plugin_structure() {
         $paths = parent::define_question_plugin_structure();


         $qtypeobj = question_bank::get_qtype($this->pluginname);
         $this->qtype = $qtypeobj;

         // Add in-depth paths
         $elepath = $this->get_pathfor('/' . $qtypeobj->name() . '_language');
         $paths[] = new restore_path_element('question_language', $elepath);
         $elepath = $this->get_pathfor('/' . $qtypeobj->name() . '_descriptions');
         $paths[] = new restore_path_element('question_descriptions', $elepath);

         return $paths; // And we return the interesting paths.
     }

    /**
     * Restarts mechanism of restoring.
     * Wonder why dispatcher for  event of beginning is not in restore mechanism
     */
    protected function refill_class_fields() {
        // Simple initialization
        $this->langid = null;
        $this->langobject = null;
        $this->oldquestion   = $this->get_old_parentid('question');
        $this->newquestion   = $this->get_new_parentid('question');
        $this->questioncreated = null;
        $this->answermapping = null;
        $this->tokencountmapping = null;
        $this->tokendescriptionmapping = null;
     }
    /**
     * Saves old answer ids, because we can't found those otherwise
     */
    public function process_question_answer($data) {
        // If started new question - invalidate values
        $oldparentid = $this->get_old_parentid('question');
        if ($oldparentid != $this->oldquestion) {
            $this->refill_class_fields();
        }
        if (is_array($this->answermapping) == false) {
            $this->answermapping = array();
        }
        // Populate mapping with old ids
        $this->answermapping[] = $data['id'];
        parent::process_question_answer($data);
    }

     /**
      * Processes question descriptions
      * This  method is called when restoring path question_descriptions
      * @param array $data
      */
    public function process_correctwriting($data) {
        $olddata = $data;
        $this->process_poasquestion($data);


        $this->questioncreated = $this->get_mappingid('question_created', $this->oldquestion) ? true : false;
        $this->langid = $olddata['langid'];

        /**  //Uncomment on bug cases
        echo '<pre>';
        echo "Before proccesssing correctwriting\n";
        print_r($this->answermapping);
        echo '</pre>';
        */

        $answersarr = $this->answermapping;
        $this->answermapping  = array();
        foreach($answersarr as $id => $answer) {
            $this->answermapping[$answer] = $this->get_mappingid('question_answer', $answer);
        }

        /** // Uncomment on bug cases
        echo '<pre>';
        echo "After proccesssing correctwriting\n";
        print_r($this->answermapping);
        echo '</pre>';
        */
    }

     /**
      * Processes question language
      * @param array $data
      */
     public function process_question_language($data) {
         global $DB;

         /**
          * Here we have data array defined as tuple
          * <id, ui_name, description, name, scanrules, parserules, version visible>
          */
         $newid = block_formal_langs::find_or_insert_language($data);
         if ($this->langid != $newid ) {
             $this->update_langid($newid);
         }


         // Scan answers to detect how many decriptions we must insert
         // Map count of answer lexemes to each answer
         $this->langobject = block_formal_langs::lang_object($this->langid);
         $this->tokencountmapping = array();
         $this->tokendescriptionmapping = array();
         /** Uncomment on bug case
         echo '<pre>';
         echo "At language\n";
         print_r($this->answermapping);
         echo '</pre>';
         */
         foreach($this->answermapping as $oldanswerid => $newid) {
             $answer = $DB->get_record('question_answers', array('id' => $newid));
             // Uncomment on bug
             //if (is_object($answer) == false) {
             //    echo '|| Import failed for answer ' . $newid . '||';
             //}
             $ps = $this->langobject->create_from_string($answer->answer);
             /**
              * @var block_formal_langs_token_stream $stream
              */
             $stream = $ps->stream;
             $tokens = $stream->tokens;
             $this->tokencountmapping[$oldanswerid] = count($tokens);
             $this->tokendescriptionmapping[$oldanswerid] = array();
         }

     }

     /**
      * Updates language identifier in qtype table,
      * @param int $langid setting it to a new id
      */
     private function update_langid($langid) {
         global $DB;
         //For a successfull update we must fetch id field for correctwriting
         $qtype_id = $DB->get_field('qtype_correctwriting','id', array($this->qtype->questionid_column_name() => $this->newquestion), MUST_EXIST);
         $qdata = new stdClass();
         $qdata->id = $qtype_id;
         $qdata->langid = $langid;

         $DB->update_record('qtype_correctwriting', $qdata);
         $this->langid = $langid;
     }

     /**
      * Processes question descriptions, called for each description.
      * This  method is called when restoring path question_descriptions
      * @param array $data
      */
     public function process_question_descriptions($data) {
         /**
          * Here we have data array defined as tup;e
          * <id, tableid, number, description>
          */
         $answer = $data['tableid'];
         $description = $data['description'];
         // Remove \r and \n from string, because they are stored like this in XML
         $description = str_replace(array("\r", "\n"), array('', ''), $description);
         $this->tokendescriptionmapping[$answer][intval($data['number'])] = $description;
         /* // Uncomment on bugs
         if (array_key_exists($answer, $this->tokencountmapping) == false) {
             echo '<pre> Can\'t found some key ' . $answer .
                  ' in tokencountmappin </pre>';
         }
         */
         if (count($this->tokendescriptionmapping[$answer]) == $this->tokencountmapping[$answer]) {
             $string = $this->langobject->create_from_db('question_answers', $this->answermapping[$answer]);
             ksort($this->tokendescriptionmapping[$answer]);
             $string->save_descriptions($this->tokendescriptionmapping[$answer]);
         }
     }
 }
