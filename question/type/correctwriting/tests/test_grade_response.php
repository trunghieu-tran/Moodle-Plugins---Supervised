<?php
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
       $this->question->lexicalerrorthreshold = 3000;
       $this->question->lexicalerrorweight = 0.1;
       $this->question->usedlanguage = $language;
       $this->question->movedmistakeweight = 0.1;
       $this->question->absentmistakeweight = 0.11;
       $this->question->addedmistakeweight = 0.12;
       $this->question->hintgradeborder = 0.75;
       $this->question->maxmistakepercentage = 0.95;
    }
    // Tests perfect match
    public function test_perfect_match() {
       $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
       $this->question->answers = $answers;
       $state = $this->question->grade_response(array('answer' => 'a data template'));
       $this->assertEquals($state[0],1.0);
       $this->question->invalidate_cache();
    }
    // Test when exact match with lower mark is preferred over a bigger non-exact match
    public function test_exact_over_non_exact_match() {
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $this->question->answers = $answers;
        $state = $this->question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.4);
        $this->question->invalidate_cache();
    }
    //  Test fully incorrect response
    public function test_incorrect_response() {
       $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
       $this->question->answers = $answers;
       $state = $this->question->grade_response(array('answer' => 'test'));
       $this->assertEquals($state[0],0.0);
       $this->question->invalidate_cache();
    }
    // Test when non-exact match with higher mark is preferred over exact match
    public function test_non_exact_over_exact_match() {
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $this->question->answers = $answers;
        $state = $this->question->grade_response(array('answer' => 'a data test'));
        $this->assertEquals($state[0],0.77);
        $this->question->invalidate_cache();
    }
    // Test when one exact match is preferred over another
    public function test_two_exact_matches() {
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data', 'fraction' => 0.3);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data', 'fraction' => 0.4);
        $this->question->answers = $answers;
        $state = $this->question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.4);
        $this->question->invalidate_cache();
    }
    // Test when one non-exact match is preferred over another
    public function test_two_non_exact_matches() {
        $answers = array();
        $answers[] = (object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 0.78);
        $answers[] = (object)array('id' => 2, 'answer' => 'a data a a', 'fraction' => 1.0);
        $this->question->answers = $answers;
        $state = $this->question->grade_response(array('answer' => 'a data'));
        $this->assertEquals($state[0],0.78);
        $this->question->invalidate_cache();
    }
 }
 
 ?>