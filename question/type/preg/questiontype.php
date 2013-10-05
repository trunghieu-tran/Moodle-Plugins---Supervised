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
 * Defines the Preg question type class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

class qtype_preg extends qtype_shortanswer {

    /**
     * Returns an array of available engines.
     * key = engine indentifier, value = interface string with engine name.
     */
    public function available_engines() {
        return array(   'php_preg_matcher' => get_string('php_preg_matcher', 'qtype_preg'),
                        'dfa_matcher' => get_string('dfa_matcher', 'qtype_preg'),
                        'nfa_matcher' => get_string('nfa_matcher', 'qtype_preg')/*,
                        'backtracking_matcher' => 'backtracking_matcher'*/
                    );
    }

    /**
     * Returns an array of supported notations.
     * key = notation indentifier, value = interface string with notation name.
     */
    public function available_notations() {
        return array(   'native' => get_string('notation_native', 'qtype_preg'),
                        'pcreextended' => get_string('notation_pcreextended', 'qtype_preg'),
                        'mdlshortanswer' => get_string('notation_mdlshortanswer', 'qtype_preg')
                    );
    }

    public function name() {
        return 'preg';
    }

    public function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'qtype_preg_options');
        array_push($extraquestionfields, 'correctanswer', 'exactmatch', 'usecharhint', 'charhintpenalty', 'hintgradeborder',
                    'engine', 'notation', 'uselexemhint', 'lexemhintpenalty', 'langid', 'lexemusername');
        return $extraquestionfields;
    }

    public function extra_answer_fields() {
        return array ('qtype_preg_regex_tests', 'regextests');
    }

    // TODO - clean up when this will be in the core (hopefully 2.6).
    public function save_question_options($question) {

        global $DB;
        $result = new stdClass();

        // Fill in some data that could be absent due to disabling form controls.
        if (!isset($question->usecharhint)) {
            $question->usecharhint = false;
        }
        if (!isset($question->charhintpenalty)) {
            $question->charhintpenalty = 0;
        }
        if (!isset($question->uselexemhint)) {
            $question->uselexemhint = false;
        }
        if (!isset($question->lexemhintpenalty)) {
            $question->lexemhintpenalty = 0;
        }
        if (!isset($question->lexemusername)) {
            $question->lexemusername = '';
        }
        if (!isset($question->langid)) {
            $question->langid = 0;
        }
        if (!isset($question->hintgradeborder)) {
            $question->hintgradeborder = 1;
        }

        // Sanity check for engine capabilities - disabling form controls works really strange...
        $questionobj = new qtype_preg_question;
        $querymatcher = $questionobj->get_query_matcher($question->engine);
        if (!$querymatcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            $question->usecharhint = false;
            $question->uselexemhint = false;
        }

        $context = $question->context;

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // We need separate arrays for answers and extra answer data, so no JOINS there.
        $extraansfields = $this->extra_answer_fields();
        $isextraansfields = is_array($extraansfields);
        $extraanstable = '';
        $oldextras = array();
        if ($isextraansfields) {
            $extraanstable = array_shift($extraansfields);
            if (!empty($oldanswers)) {
                $oldextras = $DB->get_records_sql("SELECT * FROM {{$extraanstable}} WHERE " .
                    'answerid IN (SELECT id FROM {question_answers} WHERE question = ' . $question->id . ')' );
            }
        }

        $maxfraction = -1;

        // Insert all the new answers.
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if ($this->is_answer_empty($question, $key)) {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            $this->fill_answer_fields($answer, $question, $key, $context);
            $DB->update_record('question_answers', $answer);

            if ($question->fraction[$key] > $maxfraction) {
                $maxfraction = $question->fraction[$key];
            }

            if ($isextraansfields) {
                // Now check, if this answer contains some tests.
                if ($this->is_extra_answer_fields_empty($question, $key)) {
                    continue;
                }

                $extra = array_shift($oldextras);
                if (!$extra) {
                    $extra = new stdClass();
                    $extra->answerid = $answer->id;
                    foreach ($extraansfields as $field) {
                        $extra->$field = ''; // TODO find a good default for any extra field.
                    }
                    $extra->id = $DB->insert_record($extraanstable, $extra);
                }

                foreach ($extraansfields as $field) {
                    $fieldarray = $question->$field;
                    $extra->$field = $fieldarray[$key];
                }
                $DB->update_record($extraanstable, $extra);
            }

        }

        // We don't want to call shortanswer question function, since it will repeat the steps to save answers.
        $parentresult = question_type::save_question_options($question);
        
        if ($parentresult !== null) {
            // Parent function returns null if all is OK.
            return $parentresult;
        }

        // Delete any left over extra answer fields records.
        $oldextraids = array();
        foreach ($oldextras as $oldextra) {
            $oldextraids[] = $oldextra->id;
        }
        $DB->delete_records_list($extraanstable, 'id', $oldextraids);


        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $this->save_hints($question);

        // Perform sanity checks on fractional grades.
        if ($maxfraction != 1) {
            $result->noticeyesno = get_string('fractionsnomax', 'question', $maxfraction * 100);
            return $result;
        }

    }

    /**
     * Returns true is answer with the $key is empty in the question data and should not be saved in DB.
     *
     * The questions with non-standard uses for question_answers table will want to overload this.
     * @param object $questionform This holds the information from the question editing form.
     * @param int $key A key of the answer in question.
     * @return bool True if answer shouldn't be saved in DB.
     */
    protected function is_answer_empty($questionform, $key) {
        return trim($questionform->answer[$key]) == '' && $questionform->fraction[$key] == 0 &&
                    html_is_blank($questionform->feedback[$key]['text']);
    }

    
    /**
     * Change $answer, filling necessary fields for the question_answers table.
     *
     * The questions with non-standard uses for question_answers table will want to overload this.
     * @param stdClass $answer Object to save data.
     * @param object $questionform This holds the information from the question editing form.
     * @param int $key A key of the answer in question.
     * @param $context needed for working with files.
     */
    protected function fill_answer_fields($answer, $questionform, $key, $context) {
        $answer->answer   = $questionform->answer[$key];
        $answer->fraction = $questionform->fraction[$key];
        $answer->feedback = $this->import_or_save_files($questionform->feedback[$key],
                $context, 'question', 'answerfeedback', $answer->id);
        $answer->feedbackformat = $questionform->feedback[$key]['format'];
    }

    /**
     * Returns true if extra answer fields for answerwith the $key is empty 
     * in the question data and should not be saved in DB.
     *
     * Questions where extra answer fields are optional will want to overload this.
     * @param object $questionform This holds the information from the question editing form.
     * @param int $key A key of the answer in question.
     * @return bool True if answer shouldn't be saved in DB.
     */
    protected function is_extra_answer_fields_empty($questionform, $key) {
        return !isset($questionform->regextests) || trim($questionform->regextests[$key]) == '';
    }

    // TODO - delete when this will be in the core (hopefully 2.6).
    public function get_question_options($question) {
        global $CFG, $DB, $OUTPUT;

        if (!isset($question->options)) {
            $question->options = new stdClass();
        }

        $extraquestionfields = $this->extra_question_fields();
        if (is_array($extraquestionfields)) {
            $question_extension_table = array_shift($extraquestionfields);
            $extra_data = $DB->get_record($question_extension_table,
                    array($this->questionid_column_name() => $question->id),
                    implode(', ', $extraquestionfields));
            if ($extra_data) {
                foreach ($extraquestionfields as $field) {
                    $question->options->$field = $extra_data->$field;
                }
            } else {
                echo $OUTPUT->notification('Failed to load question options from the table ' .
                        $question_extension_table . ' for questionid ' . $question->id);
                return false;
            }
        }

        $extraanswerfields = $this->extra_answer_fields();
        if (is_array($extraanswerfields)) {
            $answer_extension_table = array_shift($extraanswerfields);
            // Use LEFT JOIN in case not every answer has extra data.
            $question->options->answers = $DB->get_records_sql("
                    SELECT qa.*, qax." . implode(', qax.', $extraanswerfields) . '
                    FROM {question_answers} qa ' . "
                    LEFT JOIN {{$answer_extension_table}} qax ON qa.id = qax.answerid
                    WHERE qa.question = ?
                    ORDER BY qa.id", array($question->id));
            if (!$question->options->answers) {
                echo $OUTPUT->notification('Failed to load question answers from the table ' .
                        $answer_extension_table . 'for questionid ' . $question->id);
                return false;
            }
        } else {
            // Don't check for success or failure because some question types do
            // not use the answers table.
            $question->options->answers = $DB->get_records('question_answers',
                    array('question' => $question->id), 'id ASC');
        }

        $question->hints = $DB->get_records('question_hints',
                array('questionid' => $question->id), 'id ASC');

        return true;
    }

    /** Overload import from Moodle XML format to import hints */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $qo = parent::import_from_xml($data, $question, $format, $extra);
        $format->import_hints($qo, $data, false, true);
        return $qo;
    }

    /*public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = parent::export_to_xml($question, $format, $extra);
        //$expout .= $format->write_hints($question);
        return $expout;
    }*/

    /** Overload hints functions to be able to work with interactivehints*/
    protected function make_hint($hint) {
        return qtype_poasquestion_moodlehint_adapter::load_from_record($hint);
    }

    public function save_hints($formdata, $withparts = false) {// TODO - remove in 2.6
        global $DB;
        $context = $formdata->context;

        $oldhints = $DB->get_records('question_hints',
                array('questionid' => $formdata->id), 'id ASC');

        if (!empty($formdata->hint)) {
            $numhints = max(array_keys($formdata->hint)) + 1;
        } else {
            $numhints = 0;
        }

        if ($withparts) {
            if (!empty($formdata->hintclearwrong)) {
                $numclears = max(array_keys($formdata->hintclearwrong)) + 1;
            } else {
                $numclears = 0;
            }
            if (!empty($formdata->hintshownumcorrect)) {
                $numshows = max(array_keys($formdata->hintshownumcorrect)) + 1;
            } else {
                $numshows = 0;
            }
            $numhints = max($numhints, $numclears, $numshows);
        }

        for ($i = 0; $i < $numhints; $i += 1) {
            if (html_is_blank($formdata->hint[$i]['text'])) {
                $formdata->hint[$i]['text'] = '';
            }

            if ($withparts) {
                $clearwrong = !empty($formdata->hintclearwrong[$i]);
                $shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
            }

            if (empty($formdata->hint[$i]['text']) && empty($clearwrong) &&
                    empty($shownumcorrect)) {
                continue;
            }

            // Update an existing hint if possible.
            $hint = array_shift($oldhints);
            if (!$hint) {
                $hint = new stdClass();
                $hint->questionid = $formdata->id;
                $hint->hint = '';
                $hint->id = $DB->insert_record('question_hints', $hint);
            }

            $hint->hint = $this->import_or_save_files($formdata->hint[$i],
                    $context, 'question', 'hint', $hint->id);
            $hint->hintformat = $formdata->hint[$i]['format'];
            if ($withparts) {
                $hint->clearwrong = $clearwrong;
                $hint->shownumcorrect = $shownumcorrect;
            }
            $hint->options = $this->save_hint_options($formdata, $i, $withparts);
            $DB->update_record('question_hints', $hint);
        }

        // Delete any remaining old hints.
        $fs = get_file_storage();
        foreach ($oldhints as $oldhint) {
            $fs->delete_area_files($context->id, 'question', 'hint', $oldhint->id);
            $DB->delete_records('question_hints', array('id' => $oldhint->id));
        }
    }

    protected function save_hint_options($formdata, $number, $withparts) {
        $options = $formdata->interactivehint[$number];
        return $options;
    }

}
