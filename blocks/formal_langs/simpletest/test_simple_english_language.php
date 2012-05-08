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
 
require_once($CFG->dirroot.'/blocks/formal_langs/simple_english_language.php'); 

 /**
  * Tests a simple english language
  */
class block_formal_langs_simple_english_language_test extends UnitTestCase {
    // Tests a lexer of simple english language
    public function test_lexer() {
        $lang = new block_formal_langs_simple_english_language();
        $processedstring = new block_formal_langs_processed_string();
        $processedstring->string = 'dog  bites fly';
        $stream = $lang->scan($processedstring);
        $result = $processedstring->tokenstream->tokens;
        $this->assertTrue(count($result) == 3, 'There must be three lexemes in  \'dog bites fly\'');
        $this->assertTrue($result[0]->value() == 'dog');
        $this->assertTrue($result[1]->value() == 'bites');
        $this->assertTrue($result[2]->value() == 'fly');
    }
}
 ?>