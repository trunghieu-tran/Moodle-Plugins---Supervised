<?php
/**
 * Defines unit-tests for token_base is same
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');

 /**
  * This class contains the test cases for the is_same() function of token_base.
  * TODO Move it to formallangs if needed
  */
class block_formal_langs_token_base_is_same extends PHPUnit_Framework_TestCase {
    // Case, when a tokens are totally equal
    public function test_equal_tokens() {
        $answer = new block_formal_langs_token_base(null, 'type', 'value', true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'value', false, null);
        $this->assertTrue($answer->is_same($response), 'Tokens with equal types and values are detected as non-equal!');
    }
    // Case, when tokens are totally equal and both values is null
    public function test_equal_tokens_is_null() {
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', null, false, null);
        $this->assertTrue($answer->is_same($response), 'Tokens with equal types and null values are detected as non-equal!');
    }
    // Case, when tokens are not equal, because values are different
    public function test_inequal_values() {
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'test', false, null);
        $this->assertFalse($answer->is_same($response), 'Tokens with inequal values are detected as equal!');
    }
    // Case, when tokens are not equal, because types are different
    public function test_inequal_types() {
        $answer=new block_formal_langs_token_base(null, 'type', 'test', true, null);
        $response=new block_formal_langs_token_base(null, 'type2', 'test', false, null);
        $this->assertFalse($answer->is_same($response), 'Tokens with inequal types are detected as equal');
    }
}
 ?>