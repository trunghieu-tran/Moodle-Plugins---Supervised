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
 * Defines unit-tests for sequence analyzer
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
 

 /**
  * This class contains the test cases for grade response function from question.
  */
 class qtype_correctwriting_grade_response_test extends PHPUnit_Framework_TestCase {
 
    /** Used question
        @var qtype_correctwriting_question
     */
    private $question;
    
    protected function setUp() {
       $language = new block_formal_langs_language_simple_english();
       $this->question = new qtype_correctwriting_question();
       $this->question->usecase = true;
       $this->question->lexicalerrorthreshold = 0.1;
       $this->question->lexicalerrorweight = 0.1;
       $this->question->usedlanguage = $language;
       $this->question->movedmistakeweight = 0.1;
       $this->question->absentmistakeweight = 0.11;
       $this->question->addedmistakeweight = 0.12;
       $this->question->hintgradeborder = 0.75;
       $this->question->maxmistakepercentage = 0.95;
       $this->question->qtype = new qtype_correctwriting();
    }
    // Tests perfect match
    public function test_perfect_match() {
       $question = clone $this->question;
       $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
       $question->answers = $answers;
       $state = $question->grade_response(array('answer' => 'a data template'));
       $this->assertEquals($state[0],1.0);
    }
    // Test when exact match with lower mark is preferred over a bigger non-exact match
    public function test_exact_over_non_exact_match() {
        $question = clone $this->question;
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.4);
    }
    //  Test fully incorrect response
    public function test_incorrect_response() {
       $question = clone $this->question;
       $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
       $question->answers = $answers;
       $state = $question->grade_response(array('answer' => 'test'));
       $this->assertEquals($state[0],0.0);
    }
    // Test when non-exact match with higher mark is preferred over exact match
    public function test_non_exact_over_exact_match() {
        $question = clone $this->question;
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'a data test'));
        $this->assertEquals($state[0],0.77);
    }
    // Test when one exact match is preferred over another
    public function test_two_exact_matches() {
        $question = clone $this->question;
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data', 'fraction' => 0.3);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.4);
    }
    // Test when one non-exact match is preferred over another
    public function test_two_non_exact_matches() {
        $question = clone $this->question;
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 0.78);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data a a', 'fraction' => 1.0);
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.78);
    }
 }
 
 ?>