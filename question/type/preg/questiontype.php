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

    /*public function questionid_column_name() {
        return 'questionid';
    }*/

    /**
     * Returns an array of available engines.
     * key = engine indentifier, value = interface string with engine name.
     */
    public function available_engines() {
        return array(   'php_preg_matcher' => get_string('php_preg_matcher','qtype_preg'),
                        'dfa_matcher' => get_string('dfa_matcher','qtype_preg'),
                        'nfa_matcher' => get_string('nfa_matcher','qtype_preg')/*,
                        'backtracking_matcher' => 'backtracking_matcher'*/
                    );
    }

    /**
     * Returns an array of supported notations.
     * key = notation indentifier, value = interface string with notation name.
     */
    public function available_notations() {
        return array(   'native' => get_string('notation_native', 'qtype_preg'),
                        'mdlshortanswer' => get_string('notation_mdlshortanswer', 'qtype_preg')
                    );
    }

    function name() {
        return 'preg';
    }

    public function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'qtype_preg');
        array_push($extraquestionfields, 'correctanswer', 'exactmatch', 'usecharhint', 'charhintpenalty', 'hintgradeborder', 'engine', 'notation', 'uselexemhint', 'lexemhintpenalty', 'langid', 'lexemusername');
        return $extraquestionfields;
    }

    function save_question_options($question) {
        //Fill in some data that could be absent due to disabling form controls
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

        //Sanity check for engine capabilities - disabling form controls works really strange...
        $questionobj = new qtype_preg_question;
        $querymatcher = $questionobj->get_query_matcher($question->engine);
        if (!$querymatcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            $question->usecharhint = false;
            $question->uselexemhint = false;
        }

        parent::save_question_options($question);
    }

    /*function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        $hintneeded = ($question->usecharhint || $question->uselexemhint) && $answer->fraction >= $question->hintgradeborder;
        $matcher = $question->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id, $question->notation);
        return $matcher->match($state->responses['']);
    }*/

}
