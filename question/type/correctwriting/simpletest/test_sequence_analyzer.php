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
 
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
 
 /**
  *  Creates a specified tokens. Used for testing.
  *  @param array $types    array of token types
  *  @param array $values   values of tokens
  *  @param bool $isanswer is it an answer or responses
  *  @return array array of tokens
  */
function create_tokens($types,$values,$isanswer) {
    $result = array();
    for($i = 0;$i < count($types);$i++) {
      $result[] = new block_formal_langs_token_base(null, $types[$i], $values[$i], $isanswer, null);
    }
    return $result;
}

/** Creates an array of answer lexemes . Used for testing.
 *  @param array $types   types of answer lexemes
 *  @param array $values  values of answer lexemes 
 *  @return array array of tokens
 */
function create_answer($types,$values) {
    return create_tokens($types, $values, true);
}

/** Creates an array of response lexemes. Used for testing.
 *  @param array $types   types of response lexemes
 *  @param array $values  values of response lexemes
 *  @return array array of tokens 
 */
function create_response($types,$values) {
    return create_tokens($types, $values, false);
}

/**
  *  Computes and returns LCS for test-cases
  *  @param array $answertypes  types of answer lexemes
  *  @param array $answervalues values of answer lexemes
  *  @param array $responsetypes types of response lexemes
  *  @param array $responsevalues values of response lexemes
  *  @return array LCS
  */
function get_test_lcs($answertypes,$answervalues,$responsetypes,$responsevalues) {
    $answer = create_answer($answertypes, $answervalues);
    $response = create_response($responsetypes, $responsevalues);
    return qtype_correctwriting_sequence_analyzer::lcs($answer, $response);
}

 /**
  * This class contains the test cases for the sequence analyzer.
  * Currently lcs() function is being tested
  */
 class qtype_correctwriting_sequence_analyzer_simpletest extends UnitTestCase {
    // Tests lcs() function with case, when answer is equal to response
    public function test_equal_correctedresponse() {
       $types = array('noun', 'verb', 'verb', 'exclamation_mark');
       $values = array('I', 'am',' testing', '!');
       $lcs = get_test_lcs($types, $values, $types, $values);
       // Check LCS props
       $this->assertTrue($lcs != null, 'LCS does not exists!');
       $this->assertTrue(count($lcs) == 1, 'Incorrect amount of LCS found!');
       // Check LCS
       $this->assertTrue(count($lcs[0]) == 4, 'Incorrect amount of lexemes in first LCS!');
       $this->assertTrue($lcs[0][0] == 0, 'LCS must contain 0->0  index pair!');
       $this->assertTrue($lcs[0][1] == 1, 'LCS must contain 1->1  index pair!');
       $this->assertTrue($lcs[0][2] == 2, 'LCS must contain 2->2  index pair!');
       $this->assertTrue($lcs[0][3] == 3, 'LCS must contain 3->3  index pair!');
    }
    // Tests lcs() function with case, when one lexeme in response is replaced 
    public function test_replaced_lexemes() {
       $types = array('noun', 'verb', 'verb', 'exclamation_mark');
       $values = array('I', 'am', 'testing', '!');
       $responsevalues = array('She', 'is', 'testing', '!');
       $lcs = get_test_lcs($types, $values, $types, $responsevalues);
       // Check LCS props
       $this->assertTrue($lcs != null, 'LCS does not exists!');
       $this->assertTrue(count($lcs) == 1, 'Incorrect amount of LCS found!');
       // Check LCS
       $this->assertTrue(count($lcs[0]) == 2, 'Incorrect amount of lexemes in first LCS!');
       $this->assertTrue($lcs[0][2] == 2, 'LCS must contain 2->2  index pair!');
       $this->assertTrue($lcs[0][3] == 3, 'LCS must contain 3->3  index pair!');
    }
    // Tests lcs() function with case, when one lexeme in response is removed 
    public function test_removed_lexemes() {
       $answertypes = array('noun', 'verb', 'verb', 'exclamation_mark');
       $answervalues = array('I', 'am', 'testing', '!');
       $responsetypes = array('noun', 'verb', 'verb');
       $responsevalues = array('I', 'am', 'testing');
       $lcs = get_test_lcs($answertypes, $answervalues, $responsetypes, $responsevalues);
       // Check LCS props
       $this->assertTrue($lcs != null,'LCS does not exists!');
       $this->assertTrue(count($lcs) == 1, 'Incorrect amount of LCS found!');
       // Check LCS
       $this->assertTrue(count($lcs[0]) == 3, 'Incorrect amount of lexemes in first LCS!');        
       $this->assertTrue($lcs[0][0] == 0, 'LCS must contain 0->0  index pair!');
       $this->assertTrue($lcs[0][1] == 1, 'LCS must contain 1->1  index pair!');
       $this->assertTrue($lcs[0][2] == 2, 'LCS must contain 2->2  index pair!');
    }
    
    // Tests lcs() function with case, when one lexeme in response is added 
    public function test_added_lexemes() {
       $answertypes = array('noun', 'verb', 'verb', 'exclamation_mark');
       $answervalues = array('I', 'am', 'testing', '!');
       $responsetypes = array('noun', 'verb', 'verb', 'exclamation_mark', 'exclamation_mark');
       $responsevalues = array('I', 'am', 'testing', '!', '!');
       $lcs = get_test_lcs($answertypes, $answervalues, $responsetypes, $responsevalues);
       // Check LCS props
       $this->assertTrue($lcs != null, 'LCS does not exists!');
       $this->assertTrue(count($lcs) == 2, 'Incorrect amount of LCS found!');
       // Check first LCS   
       $this->assertTrue(count($lcs[0]) == 4, 'Incorrect amount of lexemes in first LCS!');
       $this->assertTrue($lcs[0][0] == 0, 'First LCS must contain 0->0  index pair!');
       $this->assertTrue($lcs[0][1] == 1, 'First LCS must contain 1->1  index pair!');
       $this->assertTrue($lcs[0][2] == 2, 'First LCS must contain 2->2  index pair!');
       $this->assertTrue($lcs[0][3] == 3, 'First LCS must contain 3->3  index pair!');
       // Check second LCS   
       $this->assertTrue(count($lcs[1]) == 4, 'Incorrect amount of lexemes in second LCS!');
       $this->assertTrue($lcs[1][3] == 4, 'Second LCS must contain 3->4  index pair!');
       $this->assertTrue($lcs[1][2] == 2, 'Second LCS must contain 2->2  index pair!');
       $this->assertTrue($lcs[1][1] == 1, 'Second LCS must contain 1->1  index pair!');
       $this->assertTrue($lcs[1][0] == 0, 'Second LCS must contain 0->0  index pair!');
    }
    
    // Tests lcs() function with case, when no LCS can be found
    public function test_empty_lcs() {
       $types = array('noun');
       $answervalues = array('I');
       $responsevalues = array('She');
       $lcs = get_test_lcs($types, $answervalues, $types, $responsevalues);       
       $this->assertTrue(count($lcs) == 0, 'LCS exists!');
    }
    
    //Tests lcs() function with common case
    public function test_common() {
       $answertypes = array('data', 'data', 'data', 'data', 'data');
       $answervalues = array('This', 'is', 'correct', 'answer', '.');
       $responsetypes = array( 'data', 'data', 'data', 'data', 'data', 'data', 'data');
       $responsevalues = array('This', 'not', 'correct', 'answer', 'This', 'definitely', 'not');
       $lcs = get_test_lcs($answertypes, $answervalues, $responsetypes, $responsevalues);
       // Check LCS props
       $this->assertTrue($lcs != null, 'LCS does not exists!');
       $this->assertTrue(count($lcs) == 1, 'Incorrect amount of LCS found!');
       // Check LCS
       $this->assertTrue(count($lcs[0]) == 3, 'Incorrect amount of lexemes in LCS!');
       $this->assertTrue($lcs[0][3] == 3, 'LCS must contain 3->3  index pair!');
       $this->assertTrue($lcs[0][2] == 2, 'LCS must contain 2->2  index pair!');
       $this->assertTrue($lcs[0][0] == 0, 'LCS must contain 0->0  index pair!');       
    }
 }
 
 ?>