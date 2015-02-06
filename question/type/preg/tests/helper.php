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
 * Test helper code for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the multiple choice question type.
 */
class qtype_preg_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('five_regexes_two_tests', 'six_regexes_one_test', 'four_regexes_three_tests');
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_data_five_regexes_two_tests() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'preg';
        $qdata->name = 'Regular expression question';
        $qdata->questiontext = 'Enter three digits from 0 to 4';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = '000 or 111 is good example';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->usecase = false;
        $qdata->options->correctanswer = '';
        $qdata->options->exactmatch = true;
        $qdata->options->notation = 'native';
        $qdata->options->engine = 'fa_matcher';
        $qdata->options->usecharhint = true;
        $qdata->options->uselexemhint = true;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => '000',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => '111',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.9',
                'feedback' => '111',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '111'
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => '222',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.8',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => '333',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => '333',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '333'
            ),
            17 => (object) array(
                'id' => 17,
                'answer' => '444',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '1.0',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 0,
                'clearwrong' => 0,
                'options' => 'hintmatchingpart',
            ),
            2 => (object) array(
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 'hintnextchar',
            ),
        );

        return $qdata;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_form_data_five_regexes_two_tests() {
        $qdata = new stdClass();

        $qdata->name = 'Regular expression question';
        $qdata->questiontext = array('text' => 'Enter three digits from 0 to 4', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => '000 or 111 is good example', 'format' => FORMAT_HTML);
        $qdata->defaultmark = 1;
        $qdata->noanswers = 5;
        $qdata->numhints = 2;
        $qdata->penalty = 0.3333333;

        $qdata->usecase = 1;
        $qdata->correctanswer = '';
        $qdata->exactmatch = 1;
        $qdata->notation = 'native';
        $qdata->engine = 'fa_matcher';
        $qdata->usecharhint = true;
        $qdata->uselexemhint = true;

        $qdata->fraction = array( 0 => '0.5', 1 => '0.9', 2 => '0.8', 3 => '0.5', 4 => '1.0', 5 => '0.0');
        $qdata->answer = array(
            0 => '000',
            1 => '111',
            2 => '222',
            3 => '333',
            4 => '444',
            5 => ''
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => '111',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => '333',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            5 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->regextests = array(
            0 => '',
            1 => '111',
            2 => '',
            3 => '333',
            4 => '',
            5 => ''
        );

        $qdata->hint = array(
            0 => array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML
            )
        );
        $qdata->hintclearwrong = array(0, 0);
        $qdata->hintshownumcorrect = array(0, 0);
        $qdata->interactivehint = array('hintmatchingpart', 'hintnextchar');

        return $qdata;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_data_six_regexes_one_test() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'preg';
        $qdata->name = 'Regular expression question';
        $qdata->questiontext = 'Enter three digits from 0 to 5';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = '000 or 111 is good example';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->usecase = false;
        $qdata->options->correctanswer = '';
        $qdata->options->exactmatch = true;
        $qdata->options->notation = 'native';
        $qdata->options->engine = 'fa_matcher';
        $qdata->options->usecharhint = true;
        $qdata->options->uselexemhint = true;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => '000',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => '111',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.9',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => '222',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.8',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => '333',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            17 => (object) array(
                'id' => 17,
                'answer' => '444',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '1.0',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            18 => (object) array(
                'id' => 18,
                'answer' => '555',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'Test 555',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '555'
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 0,
                'clearwrong' => 0,
                'options' => 'hintmatchingpart',
            ),
            2 => (object) array(
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 'hintnextchar',
            ),
        );

        return $qdata;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_form_data_six_regexes_one_test() {
        $qdata = new stdClass();

        $qdata->name = 'Regular expression question';
        $qdata->questiontext = array('text' => 'Enter three digits from 0 to 5', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => '000 or 111 is good example', 'format' => FORMAT_HTML);
        $qdata->defaultmark = 1;
        $qdata->noanswers = 5;
        $qdata->numhints = 2;
        $qdata->penalty = 0.3333333;

        $qdata->usecase = 1;
        $qdata->correctanswer = '';
        $qdata->exactmatch = 1;
        $qdata->notation = 'native';
        $qdata->engine = 'fa_matcher';
        $qdata->usecharhint = true;
        $qdata->uselexemhint = true;

        $qdata->fraction = array( 0 => '0.5', 1 => '0.9', 2 => '0.8', 3 => '0.5', 4 => '1.0', 5 => '0.5');
        $qdata->answer = array(
            0 => '000',
            1 => '111',
            2 => '222',
            3 => '333',
            4 => '444',
            5 => '555'
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            5 => array(
                'text' => 'Test 555',
                'format' => FORMAT_HTML
            )
        );

        $qdata->regextests = array(
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '555'
        );

        $qdata->hint = array(
            0 => array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML
            )
        );
        $qdata->hintclearwrong = array(0, 0);
        $qdata->hintshownumcorrect = array(0, 0);
        $qdata->interactivehint = array('hintmatchingpart', 'hintnextchar');

        return $qdata;
    }

    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_data_four_regexes_three_tests() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'preg';
        $qdata->name = 'Regular expression question';
        $qdata->questiontext = 'Enter three digits from 0 to 4';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = '000 or 111 is good example';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->length = 1;
        $qdata->penalty = 0.3333333;
        $qdata->hidden = 0;

        $qdata->options = new stdClass();
        $qdata->options->usecase = false;
        $qdata->options->correctanswer = '';
        $qdata->options->exactmatch = true;
        $qdata->options->notation = 'native';
        $qdata->options->engine = 'fa_matcher';
        $qdata->options->usecharhint = true;
        $qdata->options->uselexemhint = true;

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => '000',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '000'
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => '222',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.8',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => null
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => '333',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '0.5',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '333+'
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => '444',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => '1.0',
                'feedback' => 'No tests.',
                'feedbackformat' => FORMAT_HTML,
                'regextests' => '444'
            ),
        );

        $qdata->hints = array(
            1 => (object) array(
                'hint' => 'Hint 1.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 0,
                'clearwrong' => 0,
                'options' => 'hintmatchingpart',
            ),
            2 => (object) array(
                'hint' => 'Hint 2.',
                'hintformat' => FORMAT_HTML,
                'shownumcorrect' => 1,
                'clearwrong' => 1,
                'options' => 'hintnextchar',
            ),
        );

        return $qdata;
    }
    /**
     * Get the question data, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_preg_question_form_data_four_regexes_three_tests() {
        $qdata = new stdClass();

        $qdata->name = 'Regular expression question';
        $qdata->questiontext = array('text' => 'Enter three digits from 0 to 4', 'format' => FORMAT_HTML);
        $qdata->generalfeedback = array('text' => '000 or 111 is good example', 'format' => FORMAT_HTML);
        $qdata->defaultmark = 1;
        $qdata->noanswers = 5;
        $qdata->numhints = 2;
        $qdata->penalty = 0.3333333;

        $qdata->usecase = 1;
        $qdata->correctanswer = '';
        $qdata->exactmatch = 1;
        $qdata->notation = 'native';
        $qdata->engine = 'fa_matcher';
        $qdata->usecharhint = true;
        $qdata->uselexemhint = true;

        $qdata->fraction = array( 0 => '0.5', 1 => '0.0', 2 => '0.8', 3 => '0.5', 4 => '1.0', 5 => '0.0');
        $qdata->answer = array(
            0 => '000',
            1 => '',
            2 => '222',
            3 => '333',
            4 => '444',
            5 => ''
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => '',
                'format' => FORMAT_HTML
            ),
            2 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            3 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            4 => array(
                'text' => 'No tests.',
                'format' => FORMAT_HTML
            ),
            5 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->regextests = array(
            0 => '000',
            1 => '111',
            2 => '',
            3 => '333+',
            4 => '444',
            5 => ''
        );

        $qdata->hint = array(
            0 => array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML
            ),
            1 => array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML
            )
        );
        $qdata->hintclearwrong = array(0, 0);
        $qdata->hintshownumcorrect = array(0, 0);
        $qdata->interactivehint = array('hintmatchingpart', 'hintnextchar');

        return $qdata;
    }
}