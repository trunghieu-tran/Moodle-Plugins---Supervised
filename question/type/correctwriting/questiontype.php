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
require_once($CFG->dirroot . '/question/type/correctwriting/lib.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');

/**
 * Represents a correctwriting question type.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting extends qtype_shortanswer implements qtype_correctwriting_can_preserving_serialize {


    /** Returns fields, that differ from standard Moodle question fields
        and table
        @return array extra fields
     */
    public function extra_question_fields() {
        // Retrieve parent extra fields from shortanswer, like case sensivity and other fields from shortanswer.
        //$result = array_diff(parent::extra_question_fields(), array('answers'));// We unset answers fields, because we do not need them
        $result = parent::extra_question_fields();
        // Replace shortanswer table with our table
        $result[0]= 'qtype_correctwriting';
        // Language, which will be used for analysis
        $result[] = 'langid';
        // Penalty for absent lexeme mistake
        foreach($this->analyzers() as $value) {
            $classname = 'qtype_correctwriting_' . $value;
            /** @var qtype_correctwriting_abstract_analyzer $analyzer */
            $analyzer = new $classname();
            $fields = $analyzer->extra_question_fields();
            if (count($fields)) {
                foreach($fields as $field) {
                    $result[] = $field;
                }
            }
        }
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
        //Penalty for "where" picture hint.
        $result[] = 'wherepichintpenalty';

        //Is enabled fields
        $result[] = 'islexicalanalyzerenabled';
        $result[] = 'isenumanalyzerenabled';
        $result[] = 'issequenceanalyzerenabled';
        $result[] = 'issyntaxanalyzerenabled';

        //Penalty for "how to fix pic" picture hint.
        $result[] = 'howtofixpichintpenalty';


        return $result;
    }
    /** Returns a name of foreign key columns for question type
        @return string name of foreign key, that points to question table
     */
    public function questionid_column_name() {
        return 'questionid';
    }

    /**
     * Returns an array of supported analyzers.
     * Keys are numerical values, defining the order of execution for analyzers.
     */
    public function analyzers() {
        global $CFG;
        $analyzers =  array(   0x100 => 'lexical_analyzer',
                        0x200 => 'enum_analyzer',
                        0x300 => 'sequence_analyzer',
                        0x400 => 'syntax_analyzer'
                    );
        foreach ($analyzers as $name) {
            require_once($CFG->dirroot . '/question/type/correctwriting/' . $name . '.php');
        }
        return $analyzers;
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

        //We need an old answers in order to delete some old records
        $oldanswerunused = $DB->get_fieldset_select('question_answers', 'id', " question = '{$question->id}' ");

        // Save main question data
        $result = parent::save_question_options($question);


        $newanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');


        $storage = new stdClass();
        $storage->descriptions = $question->lexemedescriptions;
        $storage->question = $question;
        $storage->currentdescription = 0;
        $storage->currentid = 0;
        $storage->lang = block_formal_langs::lang_object($question->langid);


        $serializator = new qtype_correctwriting_preserving_serializator(
                            $oldanswerunused, $newanswers, $this, $storage
                        );
        $serializator->save();
        return $result;
    }

    /**
     * Saves a descriptions associated with answers
     * @param int $key            key of anwer record
     * @param stdClass $answer    answer data
     * @param stdClass $storage  temporary storage, where can be stored some important between loops data
     *                           One key is required - usedids, which can be used to handling some unused
     * @param array    $oldvalues  Old values of serialized data
     */
    public function save_stored_data($key, $answer, &$storage, $oldvalues) {
        if ($answer->fraction >= $storage->question->hintgradeborder) {
            //Check was removed, because if answer was saved
            // it must have a descriptions and all checks are made by shortanswer
            $description = $storage->descriptions[$storage->currentdescription];
            $string = $storage->lang->create_from_db('question_answers', $answer->id);
            $string->save_descriptions(explode(PHP_EOL, $description));
            if (in_array($answer->id, $oldvalues)) {
                $oldids = $storage->usedids;
                $oldids[] = $answer->id;
                $storage->usedids = $oldids;
            }
        }
        $storage->currentdescription += 1;
    }

    /**
     * Removes unused descriptions
     * @param array $ids unused description ids
     * @param stdClass $storage storage data
     */
    public function handle_unused_records($ids, &$storage) {
        // print_r($ids);
        block_formal_langs_processed_string::delete_descriptions_by_id('question_answers', $ids);
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
            $lang .= '        <answer_description>' . PHP_EOL;
            if (array_key_exists($key, $descriptions)) {
                $answerdescriptions = $descriptions[$key];
                foreach($answerdescriptions as $description) {
                    $value = $format->xml_escape(str_replace(array("\n", "\r"),array('', ''), $description));
                    $lang .= '            <description>'. $value .'</description>' . PHP_EOL;
                }
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
        $format->import_hints($qo, $data, false, false);//TODO - change last one to "true" when interactivehints will be implemented
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
                if (is_array($tokendescriptions)) {
                    foreach($tokendescriptions as $description) {
                        $descrarray[] = $description['#'];
                    }
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

    /** Overload hints functions to be able to work with interactivehints*/
    protected function make_hint($hint) {
        return qtype_poasquestion\moodle_hint_adapter::load_from_record($hint);
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

    protected function save_hint_options($formdata, $number, $withparts) {
        $array = array();
        if (!empty($formdata->whatis_[$number])) {
            $array[] = 'whatis_';
        }
        if (!empty($formdata->wheretxt_[$number])) {
            $array[] = 'wheretxt_';
        }
        if (!empty($formdata->wherepic_[$number])) {
            $array[] = 'wherepic_';
        }
        if (!empty($formdata->howtofixpic_[$number])) {
            $array[] = 'howtofixpic_';
        }
        return implode("\n", $array);
    }


    /**
     * Returns list of special tokens for lexical analyzer
     * @return array of token values
     */
    public static function lexical_analyzer_special_tokens_list() {
        global $CFG;
        // TODO Biryukova: Fill this list with special tokens
        $own = "

        ";

        $resultfromsettings = $own . "\n\n" . $CFG->qtype_correctwriting_special_tokens_list;
        $result = array();
        $lines = explode("\n", $resultfromsettings);
        if (count($lines)) {
            foreach($lines as $line) {
                $trimmedline = trim($line);
                if (core_text::strlen($trimmedline) != 0) {
                    $result[] = $trimmedline;
                }
            }
        }
        return $result;
    }
}
