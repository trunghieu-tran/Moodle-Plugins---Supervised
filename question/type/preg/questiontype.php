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

    public function save_question_options($question) {
		global $DB;
	
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
	
        parent::save_question_options($question);
		
		// regextests[0..n]  "qtype_preg_regex_tests"
		// tablename = question_answers
		// tableid = это id ансвера
		
		// Get old versions of the objects.
        /*$oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');
		
		$oldid = array();
		foreach ($oldanswers as $oldanswer) {
			$oldid[] = $oldanswer->id;
		}								// SELECT * FROM qtype_preg_regex_tests WHERE tableid IN $oldid
		$oldregextests = $DB->get_records_list('qtype_preg_regex_tests',
				'tableid', $oldid, null, 'id ASC');

        foreach ($question->regextests as $key => $regextest) {
            // Check for, and ingore, completely blank answer from the form.
            if (trim($regextest) == '') {
                continue;
            }

            // Update an existing answer if possible.
            $oldregexttest = array_shift($oldregextests);
            if (!$oldregexttest) {
                $oldregexttest = new stdClass();
                $oldregexttest->tablename = 'question_answers';
                $oldregexttest->tableid = array_shift($oldid);
				$oldregexttest->regextests = $regextest;
                $DB->insert_record('qtype_preg_regex_tests', $oldregexttest);
            } else {
				$oldregexttest = new stdClass();
                $oldregexttest->tablename = 'question_answers';
                $oldregexttest->tableid = array_shift($oldid);
				$oldregexttest->regextests = $regextest;
				$oldregexttest->id = $oldregexttest->id;
				$DB->update_record('qtype_preg_regex_tests', $oldregexttest);
			}
        }*/
    }
	
	public function get_question_options($question) {
        global $DB;

		parent::get_question_options($question);
		
		/*$answersid = array();
		foreach ($question->answers as $answer) {
			$answersid[] = $answer->id;
		}
		
		$regextests = $DB->get_records_list('qtype_preg_regex_tests',
				'tableid', $answersid, null, 'id ASC');
				
		foreach ($regextests as $regextest) {
			$question->regextests[] = $regextest->regextests;
		}*/
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

    public function save_hints($formdata, $withparts = false) {// TODO - remove, when Tim will add make_hint_options.
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
