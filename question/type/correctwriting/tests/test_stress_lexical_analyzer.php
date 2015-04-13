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
 * Defines unit-tests for lexical analyzer for stressing it when we go up to timelimit.
 * Here should be placed all bad tests
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;

require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_cpp_parseable_language.php');

/**
 * @class qtype_correctwriting_lexical_analyzer_stress_test
 * A stress test for lexical analyzer
 */
class qtype_correctwriting_lexical_analyzer_stress_test extends PHPUnit_Framework_TestCase {
    /**
     * Used language
     * @var block_formal_langs_language_cpp_parseable_language
     */
    protected $language;
    /**
     * A lexical analyzer
     * @var qtype_correctwriting_question
     */
    protected $question;

    /**
     * Sets up the environment
     */
    public function setUp() {
        $this->language = new block_formal_langs_language_cpp_parseable_language();

        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 0.4;
        $question->lexicalerrorweight = 0.05;
        $question->usedlanguage = $this->language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();

        $this->question = $question;
    }

    /**
     * Makes pair from correct string and compared string
     * @param $correctstring
     * @param $comparedstring
     * @return qtype_correctwriting_string_pair
     */
    private function make_pair($correctstring, $comparedstring) {
        return new qtype_correctwriting_string_pair(
            $this->language->create_from_string($correctstring),
            $this->language->create_from_string($comparedstring),
            null
        );
    }

    public function test_1() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 20;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'long clear_row(unsigned char console[20][81], int index);',
            'long int clear_row(unsigned char console[20][81], int index);'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $mistakes = $pair->mistakes();
        $this->assertTrue(count($mistakes) == 0, 'Mistake count is non zero');
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.6, 'Time limit reached');
    }
}
