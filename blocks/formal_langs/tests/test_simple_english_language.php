<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines unit-tests for Simple English language
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG; 
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');

 /**
  * Tests a simple english language
  */
class block_formal_langs_simple_english_language_test extends PHPUnit_Framework_TestCase {

    /**
     * Utilities for testing
     * @var block_formal_langs_language_test_utils
     */
    protected $utils;


    public function __construct() {
        $this->utils = new block_formal_langs_language_test_utils('block_formal_langs_language_simple_english', $this);
    }

    public function test_other_unicode() {
        $lang = new block_formal_langs_language_simple_english();
        $processedstring = $lang->create_from_string('а');
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 1, 'There must be one lexeme');
        $this->assertTrue($result[0]->value() == 'а');
    }

    // Tests a lexer of simple english language
    public function test_lexer() {
        $lang = new block_formal_langs_language_simple_english();
        $processedstring = $lang->create_from_string('dog  bites fly');
        $result = $processedstring->stream->tokens;
        
        $this->assertTrue(count($result) == 3, 'There must be three lexemes in  \'dog bites fly\'');
        $this->assertTrue($result[0]->value() == 'dog');
        $this->assertTrue($result[1]->value() == 'bites');
        $this->assertTrue($result[2]->value() == 'fly');

    }
    // Tests english contractions, because it's very hard stuff
    public function test_contractions() {
        $contractions = array();
        $i = 0;
        $contractions[$i++] = array('\'twou\'dn\'t', '\'e\'ll', '\'e\'s', '\'tisn\'t',
                                    '\'twasn\'t', '\'twon\'t', '\'twou\'d',
                                    '\'twouldn\'t', '\'n\'', '\'kay', '\'sfoot','\'taint',
                                    '\'tweren\'t', '\'tshall', '\'twixt', '\'twon\'t',
                                    '\'twou\'dn\'t', '\'zat');
        $contractions[$i++] = array('\'cause', '\'d', '\'fraid', '\'hood', 'i\'', 'a\'',
                                    '-in\'', '\'m', 'mo\'', '\'neath', 'o\'', 'o\'th\'',
                                    'po\'', '\'pon', '\'re', '\'round', '\'s', '\'sblood',
                                    '\'scuse', '\'sup');
        $contractions[$i++] = array('\'t', 't\'', 'th\'', '\'tis', '\'twas', '\'tween',
                                    '\'twere', '\'twill', '\'twould', '\'um', '\'ve', '\'em' );
        foreach($contractions as $c) {
            $this->utils->test_exact_matches($c);
        }
    }
    //  Tests common regexp for common contractions
    public function test_common_contractions() {
        $tests = array('test', 'tests\'','test-data','shan\'t','y\'all\'re','fo\'c\'s\'le');
        $this->utils->test_exact_matches($tests);
    }

    // Tests numeric lexemes
    public function test_numeric() {
        $tests = array( '0', '123', '34567777777' );
        $type = 'block_formal_langs_token_simple_english_numeric';
        $this->utils->test_object($tests,$type);
    }

    // Tests punctuation marks
    public function test_punctuation() {
        $tests = array ('.',',',';',':','!','?','?!', '!!','!!!','\'','"','(',')','...') ;
        $type = 'block_formal_langs_token_simple_english_punctuation';
        $this->utils->test_object($tests,$type);
    }
    // Tests typograph marks
    public function test_typographmark() {
        $tests = array ('+','-','=','<','>','@','#','%','^','&','*','$') ;
        $type = 'block_formal_langs_token_simple_english_typographic_mark';
        $this->utils->test_object($tests,$type);
    }
    // Tests other symbols
    public function test_other() {
        $tests = array ("\30") ;
        $type = 'block_formal_langs_token_simple_english_other';
        $this->utils->test_object($tests,$type);
    }
    // Tests direct speech in text
    public function test_direct_speech() {
        $lang = new block_formal_langs_language_simple_english();
        $speech = '\'Just a text\'';
        $processedstring = $lang->create_from_string($speech);
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        foreach($result as $token) {
            $tokenvalues[] = $token->value();
        }
        $this->assertTrue(count($result) == 5, count($result) . ':' . implode("\n", $tokenvalues));
        $this->assertTrue($result[0]->value() == '\'');
        $this->assertTrue($result[1]->value() == 'Just');
        $this->assertTrue($result[2]->value() == 'a');
        $this->assertTrue($result[3]->value() == 'text');
        $this->assertTrue($result[4]->value() == '\'');
    }

    public function test_unicode_apostrophe()  {
        $lang = new block_formal_langs_language_simple_english();
        $test = 'madman’s';
        $processedstring = $lang->create_from_string($test);
        $result = $processedstring->stream->tokens;
        $this->assertTrue($result[0]->value() == $test);
    }

    public function test_multiline() {
        $lang = new block_formal_langs_language_simple_english();
        $test = 'mad
                 man
                ';
        $processedstring = $lang->create_from_string($test);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 2);
        $this->assertTrue($result[0]->value() == 'mad');
        $this->assertTrue($result[1]->value() == 'man');
    }

    public function test_stringpos() {
        $lang = new block_formal_langs_language_simple_english();
        $test = 'mad';
        $processedstring = $lang->create_from_string($test);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 1);
        $this->assertTrue($result[0]->position()->stringstart() == 0);
        $this->assertTrue($result[0]->position()->stringend() == 2);
    }

}
 ?>