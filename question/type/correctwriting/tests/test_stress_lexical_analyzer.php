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

    public function test_2() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 20;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'float lengths [ 2 ] [ 3 ]  [  2  ]   =   {   {   { 0,1}, {2,3},{4,5}}, {{6,7}, {8,9},{10,11}}};',
            'float lengths [2][3][2]={{{0,1},{2,3},{4,5}},{{5,6},{6,7},{7,8}}}'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $mistakes = $pair->mistakes();
        $this->assertTrue(count($mistakes) == 0, 'Mistake count is non zero');
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.6, 'Time limit reached');
    }

    public function test_3() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 20;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'double modulos[3][6]={{0,2,4,8,10,12}, {14,16,18,20,22,24},{26,28,30,32,34,36}}',
            'float modulos[3][6]={{0,2,4,8,10,12},{14,18,20,22,24,28},{30,32,34,38,40,42}};'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $mistakes = $pair->mistakes();
        $this->assertTrue(count($mistakes) == 0, 'Mistake count is non zero');
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.6, 'Time limit reached');
    }
    public function test_4() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 20;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'int i_array[3][3] = { {-1,0,1}, {2,3,4},{5,6,7}};',
            'int i_array[3][3]={{-1,0,1},{2,3,4,},{5,6,7}};'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $mistakes = $pair->mistakes();
        $this->assertTrue(count($mistakes) == 0, 'Mistake count is non zero');
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.6, 'Time limit reached');
    }

    public function test_5() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 5;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'char matrix[2][4]={{-4,-3,-2,-1},{0,1,2,3}};',
            'char matrix[2][4]= -4,-matrix[0][0],-matrix[0][1],-matrix[0][2],-matrix[0][4],-matrix[1][0],-matrix[1][2] ;'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_6() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'compare = strstr(string1,string2) - string1 > strlen(string2);',
            'if( strlen(strstr(string2, string1)) > strlen(string2)){ compare = strlen(strstr(string2, string1)); }'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_7() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'float lengths[2][3][2]={{{0,1}, {2,3},{4,5}}, {{6,7}, {8,9},{10,11}}};',
            'float lengths[2][3][2]={(0,0),(0,0),(0,0),(0,0),(0,0),(0,0)};'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_8() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'scalars[9-i]=scalar_multiply(vectors, i-1, i);',
            'scalar_multiplications[9-i]=scalar_multiply(int vectors[10][2], int i[i][1], int i[i][2])*scalar_multiply(int vectors[10][2], int i[i+1][1], int i[i+1][2]);'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_9() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'double modulos[3][6]={{0,2,4,8,10,12}, {14,16,18,20,22,24},{26,28,30,32,34,36}};',
            'double modulos[3][6] = {{moduls[0][0]=0},{moduls[0][1]=2},{moduls[0][2]=4},{moduls[0][3]=8},{moduls[0][4]=10},{moduls[0][5]=12},{moduls[1][0]=14},{moduls[1][1]=16},{moduls[1][2]=18},{moduls[1][3]=20},{moduls[1][4]=22},{moduls[1][5]=24},{moduls[2][0]=26},{moduls[2][1]=28},{moduls[2][2]=30},{moduls[2][3]=32},{moduls[2][4]=34},{moduls[2][5]=36}};"'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_10() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'float lengths[2][3][2]={{{0,1}, {2,3},{4,5}}, {{6,7}, {8,9},{10,11}}};',
            'float lengths[2][3][2]={{{[0,][0][0]}{ [0][0][1],}}};'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }

    public function test_11() {
        global $CFG;
        $CFG->block_formal_langs_maximum_lexical_backracking_execution_time = 10;
        $CFG->block_formal_langs_maximum_variations_of_typo_correction = 10;
        $begin = time();
        $bestmatchpair = $this->make_pair(
            'strncat(strcat(strcpy(result, str1), " "), strchr(str2, \'x\'),6); ',
            'strncat(strcpy(result,strncat(str1,\'x\',6)));'
        );
        $analyzer1 = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->language, false);
        $pairs = $analyzer1->result_pairs(); // array of resultstringpairs
        $pair = $pairs[0];
        $this->assertTrue(time() - $begin < $CFG->block_formal_langs_maximum_lexical_backracking_execution_time * 1.1, 'Time limit reached');
    }
}
