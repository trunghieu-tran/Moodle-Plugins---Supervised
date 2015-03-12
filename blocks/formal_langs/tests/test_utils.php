<?php
/**
 * Defines unit-tests utils
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');


/**
 * Tests exact matches for running simple english languages
 */
class block_formal_langs_language_test_utils {
    /**
     * A language, specified in constructor
     * @var block_formal_langs_predefined_language
     */
    protected $lang;
    /**
     * Test for asserting
     * @var PHPUnit_Framework_TestCase
     */
    protected $test;
    /**
     * Constructs a new utils for language
     * @param string $langname  name of language class
     * @param PHPUnit_Framework_TestCase $test test for working
     */
    public function __construct($langname, $test) {
        $this->lang = new $langname();
        $this->test = $test;
    }
    /**
     * Tests whether any token contains in expressions
     * @param array $expressions  string array of expressions
     */
    public function test_exact_matches($expressions) {
        $processedstring = $this->lang->create_from_string(implode(' ', $expressions));
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        foreach($result as $token) {
            $tokenvalues[] = $token->value();
        }
        $this->test->assertTrue(count($expressions) == count($result), 'There must be same amount of lexemes but ' . count($result) . ' given: ' . implode("\n",$tokenvalues));
        for($i = 0; $i < count($result); $i = $i + 1) {
            $needle = $expressions[$i];
            $this->test->assertTrue(in_array($needle,$tokenvalues), $needle . ' is not found');
        }
    }
    /**
     * Tests whether any token contains in expressions an all of them has specified type
     * @param array $tokens  string array of expressions
     * @param string $class checked class
     */
    public function test_object($tokens, $class) {
        $processedstring = $this->lang->create_from_string(implode(' ', $tokens));
        $result = $processedstring->stream->tokens;
        $this->test->assertTrue(count($result) == count($tokens), 'Incorrect amount of lexemes');
        for($i = 0; $i < count($tokens); $i++) {
            $token = $result[$i];
            $correct = is_a($token, $class);
            $this->test->assertTrue($correct, 'Invalid object');
            $this->test->assertTrue($token->value() == $tokens[$i], 'Incorrect token: ' . $token->value());
        }
    }

}