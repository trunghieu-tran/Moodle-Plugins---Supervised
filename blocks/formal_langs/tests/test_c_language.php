<?php
/**
 * Defines unit-tests for C language
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
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');



 /**
  * Tests a simple english language
  */
class block_formal_langs_c_language_test extends PHPUnit_Framework_TestCase {

    /**
     * Utilities for testing
     * @var block_formal_langs_language_test_utils
     */
    protected $utils;

    public function __construct() {
        $this->utils = new block_formal_langs_language_test_utils('block_formal_langs_language_c_language', $this);
    }
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
    // Tests keywords
    public function test_keywords() {
        // Keywords
        $kwds = array('auto', 'break', 'const', 'continue', 'case', 'default',
                      'do', 'else', 'enum', 'extern', 'for', 'goto', 'if',
                      'return', 'sizeof', 'static', 'struct', 'register',
                      'switch', 'typedef', 'union', 'volatile', 'while');
        $o = 'block_formal_langs_c_token_keyword';
        $this->utils->test_object($kwds, $o);
    }

    // Tests typename keywords
    public function test_typename_keywords() {
        // Keywords
        $kwds = array('char', 'double', 'float', 'int','long', 'signed', 'unsigned', 'void');
        $o = 'block_formal_langs_c_token_typename';
        $this->utils->test_object($kwds, $o);
    }

    // Tests identifiers
    public function test_identifiers() {
        $tests = array('a0_2', 'A__', '__AW1__A');
        $o = 'block_formal_langs_c_token_identifier';
        $this->utils->test_object($tests, $o);
    }

    // Tests integral numbers
    public function test_integral_numbers() {
        $tests = array('10','10u', '10U', '10L', '10l', '0777', '0777u', '0777U',
                       '0777l', '0777L', '0xabcdef','0xABCDEF','0xABCDEFu',
                       '0xABCDEFU', '0xABCDEFl', '0xABCDEFL','1e+1','1E+1u',
                       '1e+1U', '1e+1l', '1e+1L');
        $o = 'block_formal_langs_c_token_numeric';
        $this->utils->test_object($tests, $o);
    }
    // Tests unmatched elements
    public function test_unmatched_elements() {
        $tests = array('/* unmatched comment', '" unmatched quotes', '\'u');
        $o = 'block_formal_langs_c_token_unknown';
        for($i = 0;$i < count($tests);$i++) {
            $lang = new block_formal_langs_language_c_language();
            $processedstring = $lang->create_from_string($tests[$i]);
            $result = $processedstring->stream->tokens;
            $this->assertTrue(count($result) == 1, count($result) . ' tokens given in test ');
            $this->assertTrue(is_a($result[0], $o), get_class($result[0]) . ' class object is given at position '. $i);
            $value = $result[0]->value();
            $this->assertTrue($value == $tests[$i], $value. ' is parsed in test ' . $i );
            $errors = $processedstring->stream->errors;
            $this->assertTrue(count($errors) == 1, count($errors) . ' is found instead of 1 in test ' . $i);
        }
    }
    // Tests operators lexemes
    public function test_operator_lexemes() {
        $tests = array('>>=', '<<=', '=', '+=', '-=', '*=', '/=', '%=', '&=',
                       '^=', '|=', '>>', '<<', '++', '--', '->', '&&', '||',
                       '<=', '>=', '==', '!=', '.', '&', '|', '^', '!', '~',
                       '-', '+', '*', '/', '%', '<', '>', '~=');
        $o = 'block_formal_langs_c_token_operators';
        $this->utils->test_object($tests, $o);
    }

    // Tests various tokens
    public function test_various_tokens() {
        $tests = array('...' => array('...', 'ellipsis'),
                       ';'   => array(';', 'semicolon'),
                       ','   => array(',', 'comma'),
                       ':'   => array(':', 'colon'),
                       '('   => array('(', 'bracket'),
                       ')'   => array(')', 'bracket'),
                       '{'   => array('{', 'bracket'),
                       '}'   => array('}', 'bracket'),
                       '<%'  => array('{', 'bracket'),
                       '%>'  => array('}', 'bracket'),
                       '['   => array('[', 'bracket'),
                       ']'   => array(']', 'bracket'),
                       '<:'  => array('[', 'bracket'),
                       ':>'  => array(']', 'bracket'),
                       '?'   => array('?', 'question_mark') );
        $keys = array_keys($tests);
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string(implode(' ', $keys));
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == count($tests), count($result) . ' tokens given in test ');
        for($i = 0; $i < count($result); $i++) {
            $object =  'block_formal_langs_c_token_' . $tests[$keys[$i]][1];
            $cvalue =   $tests[$keys[$i]][0];
            $this->assertTrue(is_a($result[$i], $object), get_class($result[$i]) . ' class object is given at position '. $i);
            $value = $result[$i]->value();
            $this->assertTrue($value == $cvalue, $value. ' is given at position '. $i);
        }
    }

    // Tests floating point numbers
    public function test_fp_numbers() {
        $tests = array('22.22', '.22', '22.', '22.22E+1', '22.22E-1',
                       '22.22f', '22.22F', '22.22l', '22.22L', '.22e-1f',
                       '.22e-1F', '.22e-1l', '.22e-1L');
        $o = 'block_formal_langs_c_token_numeric';
        $this->utils->test_object($tests, $o);
    }
    // Tests preprocessor directives
    public function test_preprocessor() {
        $tests = array('#include <stdio.h>', '#include "stdio.h"', '#define',
                       '#if', '#ifdef', '#elif', '#else', '#endif', '#', '##');
        $o = 'block_formal_langs_c_token_preprocessor';
        $this->utils->test_object($tests, $o);
    }

    // Tests singleline comments
    public function test_singleline_comment() {
        $lang = new block_formal_langs_language_c_language();
        $unixstring = "// I love Linux!\n";
        $winstring  = "// I also use Windows! \n\r";
        $macstring  = "// For MAC users \r";
        $endstring  = "// This is not error";
        $strings = array($unixstring, $winstring, $macstring, $endstring);
        for ($i = 0; $i < count($strings); $i++) {
            $processedstring = $lang->create_from_string($strings[$i]);
            $result = $processedstring->stream->tokens;
            $this->assertTrue(count($result) == 1, count($result) . ' tokens given in test '. $i);
            $this->assertTrue($result[0]->value() == $strings[$i], $result[0]->value() . $strings[$i] . ' is parsed ');
        }
    }

    // Tests full character analysis
    public function test_character_analysis() {
        $lang = new block_formal_langs_language_c_language();
        $string = "L'' 'a' 'A' '\\'' '\\a' '\\b' '\\f' '\\n' '\\r' '\\t' '\\v' '\\\"' '\\\\' '\\?' '\\1' '\\x1'";
        $processedstring = $lang->create_from_string($string);
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        for($i = 0; $i < count($result);$i++) {
            $tokenvalues[] = $result[$i]->value() . ' ' . get_class($result[$i]);
        }
        $this->assertTrue(count($result) == 16, count($result) . ' tokens given instead of 16: ' . implode("\n", $tokenvalues));
        $chars = array("L''", "'a'", "'A'", "'''", "'\a'", "'\b'",
                       "'\f'", "'\n'", "'\r'", "'\t'", "'\v'", "'\"'",
                      "'\\'", "'?'", "'\x1'", "'\x1'");
        for($i = 0; $i < count($result);$i++) {
            $token = $result[$i]->value();
            $char = $chars[$i];
            $this->assertTrue($token == $char, 'Incorrect parsed char at ' . $i . ': ' . $char );
        }
    }

    // Tests full string analysis
    public function test_string_analysis() {
        $lang = new block_formal_langs_language_c_language();
        $string = "L\"\" \"aB\" \"A\" \"\\'\" \"\\a\" \"\\b\" \"\\f\" \"\\n\" \"\\r\" \"\\t\" \"\\v\" \"\\\"\" \"\\\\\" \"\\?\" \"\\1\" \"\\x1\"";
        $processedstring = $lang->create_from_string($string);
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        for($i = 0; $i < count($result);$i++) {
            $tokenvalues[] = $result[$i]->value() . ' ' . get_class($result[$i]);
        }
        $this->assertTrue(count($result) == 16, count($result) . ' tokens given instead of 16: ' . implode("\n", $tokenvalues));
        $chars = array("L\"\"", "\"aB\"", "\"A\"", "\"'\"", "\"\a\"", "\"\b\"", "\"\f\"",
                       "\"\n\"", "\"\r\"", "\"\t\"", "\"\v\"", "\"\"\"",
                       "\"\\\"", "\"?\"", "\"\x1\"", "\"\x1\"");

        for($i = 0; $i < count($result);$i++) {
            $token = $result[$i]->value();
            $char = $chars[$i];
            $this->assertTrue($token == $char, 'Incorrect parsed string at ' . $i . ': ' . $char );
        }
    }
    
    // Test a string and character analysis, when they happen in same string
    public function test_string_and_character() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string('"\"" \'\\\'\' ');
        $result = $processedstring->stream->tokens;
        $this->assertTrue( count($processedstring->stream->errors) == 0);
        $this->assertTrue(count($result) == 2, 'There must be two lexemes: string and char');
        $this->assertTrue($result[0]->value() == '"""',$result[0]->value());
        $this->assertTrue($result[1]->value() == '\'\'\'',$result[1]->value() );
    }
    //Test numeric objects
    public function test_numeric() {
        $lang = new block_formal_langs_language_c_language();
        $processedstring = $lang->create_from_string(' .22 22.  22.22E+9  ');
        $result = $processedstring->stream->tokens;
        $this->assertTrue( count($processedstring->stream->errors) == 0);
        $this->assertTrue(count($result) == 3, 'There must be three lexemes');
        $this->assertTrue($result[0]->value() == '.22');
        $this->assertTrue($result[1]->value() == '22.');
        $this->assertTrue($result[2]->value() == '22.22E+9');
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