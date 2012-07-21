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
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php'); 

 /**
  * Tests a simple english language
  */
class block_formal_langs_c_language_test extends PHPUnit_Framework_TestCase {
    // Tests a lexer of simple english language
    public function test_lexer() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string('struct test ; ');
        $result = $processedstring->stream->tokens;
        
        $this->assertTrue(count($result) == 3, 'There must be three lexemes in  \'struct test ;\'');
        $this->assertTrue($result[0]->value() == 'struct');
        $this->assertTrue($result[1]->value() == 'test');
        $this->assertTrue($result[2]->value() == ';');
        
    }
    // Test a lexer in order to get no faules
    public function test_fault() {
        $lang = new block_formal_langs_language_c_language();
        // Due to some bugs in string, we can't do this (some stuff in poas_question::ord)
        // $processedstring = $lang->create_from_string('!><ûמûהגמûהג{}\'   @@@!!% "  ');
        // $result = $processedstring->stream->tokens;
        // $this->assertTrue( count($processedstring->stream->errors) != 0);
        // If no exception thrown, than everything is good. 
        // $this->assertTrue( true );
    }
    
    // Test a string and char analysis
    public function test_string_char() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string('"\"" \'\\\'\' ');
        $result = $processedstring->stream->tokens;
        $this->assertTrue( count($processedstring->stream->errors) == 0);
        $this->assertTrue(count($result) == 2, 'There must be two lexemes: string and char');
        $this->assertTrue($result[0]->value() == '"""',$result[0]->value());
        $this->assertTrue($result[1]->value() == '\'\\\'\'');
    }
    //Test numeric objects
    public function test_numeric() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string('22 .22 22.  22.22E+9  ');
        $result = $processedstring->stream->tokens;
        $this->assertTrue( count($processedstring->stream->errors) == 0);
        $this->assertTrue(count($result) == 4, 'There must be two lexemes: string and char');
        $this->assertTrue($result[0]->value() == '22');
        $this->assertTrue($result[1]->value() == '.22');
        $this->assertTrue($result[2]->value() == '22.');
        $this->assertTrue($result[3]->value() == '22.22E+9');
    }
    //Test comments. We wont use line comment, because in Moodle we can enter only one line
    public function test_comments() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string('/*  a comment */  ');
        $result = $processedstring->stream->tokens;
        $this->assertTrue( count($processedstring->stream->errors) == 0);
        $this->assertTrue(count($result) == 1, 'There must be one lexeme');
        $this->assertTrue($result[0]->value() == '/*  a comment */');    
    }
}
 ?>