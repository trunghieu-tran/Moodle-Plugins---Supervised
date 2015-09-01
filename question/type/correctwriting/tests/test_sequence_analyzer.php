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
require_once($CFG->dirroot.'/question/type/correctwriting/lexical_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

 /**
  *  Creates a specified tokens. Used for testing.
  *  @param array $types    array of token types
  *  @param array $values   values of tokens
  *  @param bool $isanswer is it an answer or responses
  *  @return array array of tokens
  */
function create_tokens($types,$values,$isanswer) {
    $tokens = array();
    for($i = 0;$i < count($types);$i++) {
      $tokens[] = new block_formal_langs_token_base(null, $types[$i], $values[$i], $isanswer, null);
    }
    $result = new block_formal_langs_token_stream();
    $result->tokens = $tokens;
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
    $options = new block_formal_langs_comparing_options();
    $options->usecase = true;
    return qtype_correctwriting_sequence_analyzer::lcs($answer, $response, $options);
}

/**
 *  A class of test utils to test some stuff d
 */
class qtype_correctwriting_sa_test_utils {
    /**
     * Tests, whether LCS is in list
     * @static
     * @param PHPUnit_Framework_TestCase $test test case
     * @param array $lcss    all of lcss
     * @param array $testlcs current testing lcs
     */
    public static function has_lcs($test, $lcss, $testlcs) {
        $haslcs = false;
        for($i = 0;$i < count($lcss);$i++) {
            $haslcs = $haslcs || ($lcss[$i] == $testlcs);
        }
        $test->assertTrue($haslcs, var_export($testlcs,true) . ' was not found in ' . var_export($lcss, true));
    }
}

 /**
  * This class contains the test cases for the sequence analyzer.
  * Currently lcs() function is being tested
  */
class qtype_correctwriting_sequence_analyzer_test extends PHPUnit_Framework_TestCase {
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
       qtype_correctwriting_sa_test_utils::has_lcs($this, $lcs, array(
                                                   0 => 0,
                                                   1 => 1,
                                                   2 => 2,
                                                   3 => 3
                                                   ));
       // Check second LCS
       qtype_correctwriting_sa_test_utils::has_lcs($this, $lcs, array(
                                                   0 => 0,
                                                   1 => 1,
                                                   2 => 2,
                                                   3 => 4
                                                   ));
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

    /** Used question
     * @var qtype_correctwriting_question
     */
    private $question;

    /**
     * Language
     * @var block_formal_langs_language_c_language
     */
    private $language;

    /**
     * Inits environment for testing analyzer
     */
    protected function setUp() {
        $this->language = new block_formal_langs_language_c_language();
        $this->question = new qtype_correctwriting_question();
        $this->question->usecase = true;
        $this->question->lexicalerrorthreshold = 0.5;
        $this->question->lexicalerrorweight = 0.1;
        $this->question->usedlanguage = $this->language;
        $this->question->movedmistakeweight = 0.1;
        $this->question->absentmistakeweight = 0.11;
        $this->question->addedmistakeweight = 0.12;
        $this->question->hintgradeborder = 0.75;
        $this->question->maxmistakepercentage = 1.0;
        $this->question->qtype = new qtype_correctwriting();
    }

    /**
     * Tests main sequence analyzer
     */
    public function test_sequence_analyzer() {
        $answer = $this->language->create_from_string('int a = 23 + 54;');
        $answer->set_descriptions_from_array(array(
            'type', // int
            'identifier',
            'equality',
            'number',
            'sum',
            'number',
            'semicolon'
        ));

        $response = $this->language->create_from_string('int a = 54 23;;');
        $pair = new qtype_correctwriting_string_pair($answer,$response, array());
        $analyzer = new qtype_correctwriting_sequence_analyzer($this->question, $pair, $this->language,false);

        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) > 0);
        /**
         * @var qtype_correctwriting_string_pair $pair
         */
        $pair = $result[0];
        $mistakes = $pair->mistakes();

        $findmistakes = function($classname) use($mistakes) {
            $result = 0;
            $largeclassname = 'qtype_correctwriting_lexeme_' .$classname . '_mistake';
            if (count($mistakes)) {
                foreach($mistakes as $mistake) {
                    if (is_a($mistake, $largeclassname)) {
                        $result += 1;
                    }
                }
            }
            return $result;
        };

        $count = $findmistakes('moved');
        $this->assertTrue($count > 0);

        $count = $findmistakes('added');
        $this->assertTrue($count > 0);

        $count = $findmistakes('absent');
        $this->assertTrue($count > 0);
    }

    /**
     * Tests interaction between lexical and sequence analyzers
     */
    public function test_lexical_sequence_analyzers() {
        $answer = $this->language->create_from_string('int a = 23 + 54;');
        $answer->set_descriptions_from_array(array(
            'type', // int
            'identifier',
            'equality',
            'number',
            'sum',
            'number',
            'semicolon'
        ));

        $response = $this->language->create_from_string('int a = 54 23;;');
        $pair = new qtype_correctwriting_string_pair($answer,$response, array());

        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $pair, $this->language,false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) > 0);
        /**
         * @var qtype_correctwriting_string_pair $pair
         */
        $pair = $result[0];

        $analyzer = new qtype_correctwriting_sequence_analyzer($this->question, $pair, $this->language,false);

        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) > 0);
        /**
         * @var qtype_correctwriting_string_pair $pair
         */
        $pair = $result[0];
        $mistakes = $pair->mistakes();

        $findmistakes = function($classname) use($mistakes) {
            $result = 0;
            $largeclassname = 'qtype_correctwriting_lexeme_' .$classname . '_mistake';
            if (count($mistakes)) {
                foreach($mistakes as $mistake) {
                    if (is_a($mistake, $largeclassname)) {
                        $result += 1;
                    }
                }
            }
            return $result;
        };

        $count = $findmistakes('moved');
        $this->assertTrue($count > 0);

        $count = $findmistakes('added');
        $this->assertTrue($count > 0);

        $count = $findmistakes('absent');
        $this->assertTrue($count > 0);
    }
}
