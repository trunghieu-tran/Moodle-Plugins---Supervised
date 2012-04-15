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

/**
 * Represents a correctwriting question type.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting extends question_type {
    /** Returns fields, that differ from standard Moodle question fields
        and table
        @return array extra fields
     */
    public function extra_question_fields() {
        $result = array('qtype_correctwriting', 'langid', 'absenterrorweight', 'addederrorweight', 'movederrorweight');
        $result[] = 'lexicalerrorthreshold'; 
        $result[] = 'lexicalerrorweight';
        $result[] = 'casesensivity';        
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
      
        // Our task is to load some symbols for each answer from lexeme tables
        // Option answers is loaded as an associative array of id => stdClass of answer
        // So we just need to load some answer symbols, which belongs to an array
        // They also are loaded as stdClass
        foreach($question->options->answers as $id => $answer) {
            $answer->symbols = $DB->get_records('{qtype_correctwriting_symbols}', array('answerid' => $id)); 
        }
        
        return true;
    }
}
