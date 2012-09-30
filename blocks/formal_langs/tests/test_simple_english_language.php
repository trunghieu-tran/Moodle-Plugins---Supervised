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
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');

/**
 * Tests exact matches for running simple english languages
 */
class block_formal_langs_simple_english_language_test_utils {
    /**
     * Tests whether any token contains in expressions
     * @param block_formal_langs_simple_english_language_test $test Test object
     * @param array $expressions  string array of expressions
     * @param block_formal_langs_language_simple_english $lang language object
     */
    public static function test_exact_matches($test,$expressions, $lang) {
        $processedstring = $lang->create_from_string(implode(' ', $expressions));
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        foreach($result as $token) {
            $tokenvalues[] = $token->value();
        }
        $test->assertTrue(count($expressions) == count($result), 'There must be same amount of lexemes but ' . count($result) . ' given: ' . implode("\n",$tokenvalues));
        for($i = 0; $i < count($result); $i = $i + 1) {
            $needle = $expressions[$i];
            $test->assertTrue(in_array($needle,$tokenvalues), $needle . " is not found");
        }
    }
    /**
     * Tests whether any token contains in expressions an all of them has specified type
     * @param block_formal_langs_simple_english_language_test $test Test object
     * @param array $tokens  string array of expressions
     * @param block_formal_langs_language_simple_english $lang language object
     * @param string $class checked class
     */
    public static function test_object($test, $tokens, $lang, $class) {
        $processedstring = $lang->create_from_string(implode(' ', $tokens));
        $result = $processedstring->stream->tokens;
        $test->assertTrue(count($result) == count($tokens), "Incorrect amount of lexemes");
        for($i = 0; $i < count($tokens); $i++) {
            $token = $result[$i];
            $correct = is_a($token, $class);
            $test->assertTrue($correct,"Invalid object");
            $test->assertTrue($token->value() == $tokens[$i], 'Incorrect token: ' . $token->value());
        }
    }
}
 /**
  * Tests a simple english language
  */
class block_formal_langs_simple_english_language_test extends PHPUnit_Framework_TestCase {
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
        $lang = new block_formal_langs_language_simple_english();
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
                                    '\'twere', '\'twill', '\'twould', '\'um', '\'ve' );
        foreach($contractions as $c) {
            block_formal_langs_simple_english_language_test_utils::test_exact_matches($this,
                                                                                      $c,
                                                                                      $lang);
        }
    }
    //  Tests common regexp for common contractions
    public function test_common_contractions() {
        $lang = new block_formal_langs_language_simple_english();
        $tests = array('test', 'tests\'','test-data','shan\'t','y\'all\'re','fo\'c\'s\'le');
        block_formal_langs_simple_english_language_test_utils::test_exact_matches($this, $tests, $lang);
    }

    // Tests numeric lexemes
    public function test_numeric() {
        $lang = new block_formal_langs_language_simple_english();
        $tests = array( '0', '123', '34567777777' );
        $type = 'block_formal_langs_token_simple_english_numeric';
        block_formal_langs_simple_english_language_test_utils::test_object($this,$tests,$lang,$type);
    }

    // Tests punctuation marks
    public function test_punctuation() {
        $lang = new block_formal_langs_language_simple_english();
        $tests = array ('.',',',';',':','!','?','?!', '!!','!!!','\'','"','(',')','...') ;
        $type = 'block_formal_langs_token_simple_english_punctuation';
        block_formal_langs_simple_english_language_test_utils::test_object($this,$tests,$lang,$type);
    }
    // Tests typograph marks
    public function test_typographmark() {
        $lang = new block_formal_langs_language_simple_english();
        $tests = array ('+','-','=','<','>','@','#','%','^','&','*','$') ;
        $type = 'block_formal_langs_token_simple_english_typographic_mark';
        block_formal_langs_simple_english_language_test_utils::test_object($this,$tests,$lang,$type);
    }
    // Tests other symbols
    public function test_other() {
        $lang = new block_formal_langs_language_simple_english();
        $tests = array ("\30") ;
        $type = 'block_formal_langs_token_simple_english_other';
        block_formal_langs_simple_english_language_test_utils::test_object($this,$tests,$lang,$type);
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
        $this->assertTrue(count($result) == 5, count($result) . ":" . implode("\n", $tokenvalues));
        $this->assertTrue($result[0]->value() == '\'');
        $this->assertTrue($result[1]->value() == 'Just');
        $this->assertTrue($result[2]->value() == 'a');
        $this->assertTrue($result[3]->value() == 'text');
        $this->assertTrue($result[4]->value() == '\'');
    }
}
 ?>