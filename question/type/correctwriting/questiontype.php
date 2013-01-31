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
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');

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
        // Retrieve parent extra fields from shortanswer, like case sensivity and other fields from shrtanswer
        // We unset answers fields, because we do not need them
        $result = array_diff(parent::extra_question_fields(), array('answers'));
        // Replace shortanswer table with our table
        $result[0]= 'qtype_correctwriting';
        // Language, which will be used for analysis
        $result[] = 'langid';
        // Penalty for absent lexeme mistake
        $result[] = 'absentmistakeweight';
        // Penalty for odd lexeme mistake
        $result[] = 'addedmistakeweight';
        // Penalty for moved lexeme mistake
        $result[] = 'movedmistakeweight';
        // A threshold for lexical error as fraction to it's length
        //$result[] = 'lexicalerrorthreshold';
        // A penalty for error in symbol
        //$result[] = 'lexicalerrorweight';
        // Minimal grade for  answer to be approximately matched with student response
        $result[] = 'hintgradeborder';
        // Maximum fraction of mistakes to length of teacher answer in lexemes
        $result[] = 'maxmistakepercentage';
        //Penalty for "what is" hint.
        $result[] = 'whatishintpenalty';
        //Penalty for "where" text hint.
        $result[] = 'wheretxthintpenalty';
        //Absent token hints penalty factor
        $result[] = 'absenthintpenaltyfactor';

        return $result;
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
        if (!parent::get_question_options($question)) {
            return false;
        }
        return true;
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


        $answers = $question->answer;

        //We need an old answers in order to delete some old records
        $oldanswerunused = $DB->get_fieldset_select('question_answers', 'id', " question = '{$question->id}' ");

        // Save main question data
        $result = parent::save_question_options($question);

        $lang = block_formal_langs::lang_object($question->langid);


        // Answers contains an array of answer ids
        $insertedanswerids = explode(',',$question->answers);
        // Used lexeme descriptions for symbols
        $descriptions = $question->lexemedescriptions;
        $currentid = 0;
        $currentdescription = 0;

        $oldanswerused = array();
        // Insert all the new answers
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
                $currentdescription = $currentdescription + 1;
                continue;
            }
            $description = $descriptions[$currentdescription];
            $string = $lang->create_from_db('question_answers',$insertedanswerids[$currentid]);
            $string->save_descriptions(explode(PHP_EOL, $description));

            $oldanswerused[] = $insertedanswerids[$currentid];
            $currentid = $currentid + 1;
            $currentdescription = $currentdescription + 1;
        }
        // Remove old unused descriptions
        $oldanswerunused = array_diff($oldanswerunused, $oldanswerused);
        if ($oldanswerunused !=null) {
            block_formal_langs_processed_string::delete_descriptions_by_id('question_answers', $oldanswerunused);
        }
        return $result;
    }

    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        global $DB;

        $result = parent::export_to_xml($question, $format, $extra);
        $langrecord = $DB->get_record('block_formal_langs',array('id' => $question->options->langid));
        $langfields = array('ui_name', 'description', 'name', 'scanrules', 'parserules', 'version', 'visible');
        $lang = '    <language>' . PHP_EOL;
        foreach ($langfields as $field) {
            $exportedvalue = $format->xml_escape($langrecord->$field);
            $lang .= '        <'  . $field . '>' . $exportedvalue . '</' . $field . '>' . PHP_EOL;
        }
        $lang .= '    </language>' . PHP_EOL;

        $lang .= '    <descriptions>' . PHP_EOL;

        $langobj = block_formal_langs::lang_object($question->options->langid);
        $string = $langobj->create_from_db('question_answers', 0);
        $descriptions = $string->get_descriptions_as_array('question_answers', array_keys($question->options->answers));
        foreach ($question->options->answers as $key => $answerdata) {
                $answerdescriptions = $descriptions[$key];
                $lang .= '        <answer_description>' . PHP_EOL;
                foreach($answerdescriptions as $description) {
                    $value = $format->xml_escape(str_replace(array("\n", "\r"),array('', ''), $description));
                    $lang .= '            <description>'. $value .'</description>' . PHP_EOL;
                }
                $lang .= '        </answer_description>' . PHP_EOL;
        }
        $lang .= '    </descriptions>' . PHP_EOL;

        return $result . $lang;
    }
    /*
     * Imports question from the Moodle XML format
     * Updates langid according to found or inserted language,
     * also sets a descriptions, according to new data for descriptions
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $qo = parent::import_from_xml($data, $question, $format, $extra);
        if ($qo == false) {
            return $qo;
        }
        $question_type = $data['@']['type'];
        if ($question_type != $this->name()) {
            return false;
        }
        $language = $format->getpath($data, array('#', 'language', 0, '#'), '');

        $languagerecord = array();
        foreach($language as $key => $value) {
            if (is_numeric($key) == false) {
                $languagerecord[$key]  = $value[0]['#'];
            }
        }

        $answerdescriptions = $format->getpath($data, array('#', 'descriptions', 0, '#', 'answer_description'), '');
        $lexemedescriptions = array();
        foreach ($answerdescriptions as $answerdescription) {
            if (count($answerdescription)) {
                $descrarray = array();
                $tokendescriptions  = $format->getpath($answerdescription, array('#', 'description'), '');
                foreach($tokendescriptions as $description) {
                    $descrarray[] = $description['#'];
                }
                if (count($descrarray) != 0) {
                    $stringdescrs = implode(PHP_EOL, $descrarray);
                } else {
                    $stringdescrs = '';
                }
                $lexemedescriptions[] = $stringdescrs;
            }
        }


        // insert or update langid, due info in XML
        $qo->langid = block_formal_langs::find_or_insert_language($languagerecord);
        // set lexeme descriptions arrays as  array of array of string,
        // arranged in certified order
        $qo->lexemedescriptions = $lexemedescriptions;
        return $qo;
    }
    /** Removes a symbols from tables and everything about question.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context this question belongs to.
     */
    public function delete_question($questionid, $contextid) {
        global $DB;
        $answerids = $DB->get_fieldset_select('question_answers', 'id', " question = '$questionid' ");
        block_formal_langs_processed_string::delete_descriptions_by_id('question_answers',$answerids);

        parent::delete_question($questionid, $contextid);
    }
}
