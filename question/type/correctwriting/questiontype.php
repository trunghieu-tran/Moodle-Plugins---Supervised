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
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
/**
 * Represents a correctwriting question type.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting extends qtype_shortanswer {
    /** Returns fields, that differ from standard Moodle question fields
        and table
        @return array extra fields
     */
    public function extra_question_fields() {
        $result = array('qtype_correctwriting', 'langid', 'absentmistakeweight', 'addedmistakeweight', 'movedmistakeweight');
        $result[] = 'lexicalerrorthreshold'; 
        $result[] = 'lexicalerrorweight';
        $result[] = 'usecase';        
        return $result;
    }
    /** Returns extra tables, needed for question
        @return array extra tables, needed for question
     */
    public function extra_question_tables() {
        return array('qtype_correctwriting_symbols'); 
    }
    /** Returns a name of foreign key columns for question type
        @return string name of foreign key, that points to question table
     */
    public function questionid_column_name() {
        return 'questionid';
    }
    
    /** Loads a question type specific options for  the question
        @return bool              Indicates success or failure
        @param  object $question  The  question object, which must be filled with appropriate data
     */
    public function get_question_options($question) {
        global $DB;
        
        // Extra question fields will do job, like loading some answers for question
        if (!parent::get_question_options($options)) {
            return false;
        }
        
        // Fetch all symbols, that belongs to question
        list($idsql, $params) = $DB->get_in_or_equal(array_keys($question->options->answers));
        $symsql = 'SELECT * FROM {qtype_correctwriting_symbols} WHERE answerid {$idsql}';
        $allsymbols = $DB->get_records_select('{qtype_correctwriting_symbols}', $symsql);
        // Our task is to load some symbols for each answer from lexeme tables
        // Option answers is loaded as an associative array of id => stdClass of answer
        // So we just need to load some answer symbols, which belongs to an array
        // They also are loaded as stdClass
        foreach($question->options->answers as $id => $answer) {
            //Fill answer symbols
            foreach($allsymbols as $symbol) {
                if ($symbol->answerid == $id) {
                    $answer->symbols[] = $symbol; 
                }
            }
        }
        
        return true;
    }
    
    /** Initializes instance of question
        @param  object $question     The question object instance
        @param  object $questiondata The user question data 
      */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        //Rearrange lexemes arrays, arrange it as number => symbol
        foreach($question->answers as $id => $answer) {
            $symbols = array();
            foreach ($answer->symbols as $symid => $symbol) {
                $symbols[$symbol->number] = $symbol;
                $symbol->id = $symid;
            }
            $answer->symbols = $symbols;
        }
    }
    
    /** Saves a question 
        @param object $question question data
      */
    public function save_question_options($question) {
        global $DB;
        
        // Result of saving
        $result = new stdClass();

        //Context, where question belongs to
        $context = $question->context;
        
        //Remove symbols from old answers
        $sqlwhereget = " answerid IN (SELECT id FROM {question_answers} WHERE question  = {$question->id})";
        $oldsymbols = $DB->get_records_select('qtype_correctwriting_symbols', $sqlwhereget);
        
        $answers = $question->answer;
        // Save main question data
        $result = parent::save_question_options($question);
        
        
        // Answers contains an array of answer ids
        $insertedanswerids = explode(',',$question->answers);
        // Used lexeme descriptions for symbols
        $descriptions = $question->lexemedescriptions;
        $currentid = 0;
        $currentdescription = 0;
        
        
        // Insert all the new answers
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
                $currentdescription = $currentdescription + 1;
                continue;
            }
            
            // Extract current lexeme descriptions
            $description = $descriptions[$currentdescription];
            $answerid = $insertedanswerids[$currentid];
            
            //Token array description data
            $tokdescrs = explode(PHP_EOL, $description);
            $tokenindex = 0; 
            foreach ($tokdescrs as $newdescription) {
                $olddescription = array_shift($oldsymbols);
                $mustinsert  = ($olddescription == null);
                if ($olddescription == null) {
                    $olddescription = new stdClass();
                    //$olddescription->id = null;
                }
                $olddescription->answerid = $answerid;
                $olddescription->number = $tokenindex;
                $olddescription->description = $newdescription;
                
                if ($mustinsert) {
                    $DB->insert_record('qtype_correctwriting_symbols',$olddescription);
                } else {
                    $DB->update_record('qtype_correctwriting_symbols',$olddescription);
                }
                $tokenindex = $tokenindex + 1;
            }
            
            
            $currentid = $currentid + 1;
            $currentdescription = $currentdescription + 1;
        }
        
        //If some old symbols left - delete it
        if ($oldsymbols !=null) {
            $oldsymboldids = array();
            foreach($oldsymbols as $oldsymbol) {
                $oldsymboldids[] = $oldsymbol->id;    
            }
            $oldsymbolin = implode(',',$oldsymboldids);
            $DB->delete_records_select('{qtype_correctwriting_symbols}', " id IN ($oldsymbolin) ");
        }
        return $result;
    }
    
    /** Removes a symbols from tables and everything about question. 
     * @param int $questionid the question being deleted.
     * @param int $contextid the context this question belongs to. 
     */
    public function delete_question($questionid, $contextid) {
        global $DB;
        
        $sqlwheredelete = " answerid IN (SELECT id FROM {question_answers} WHERE question  = {$questionid})";
        $DB->delete_records_select('qtype_correctwriting_symbols', $sqlwheredelete);
        
        parent::delete_question($questionid, $contextid);
    }
}
