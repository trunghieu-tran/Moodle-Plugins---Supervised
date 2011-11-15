<?php
/**
 * Data-driven cross-tester of matchers. Test functions should be implemented in child classes.
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 *
 *     A test function should:
 *     -be named "data_for_test_..."
 *     -return an array of input and output data as in the following example:
 *       array(
 *             'regex'=>'^[-.\w]+[a-z]{2,6}$',    // a regular expression
 *             'modifiers'=>'i',                  // modifiers. it's not necessary to define this element
 *             'tests'=>array($test1,...,$testn)  // array containing tests in the format described below. count of these tests is unlimited
 *             );
 *
 *    An array of expected results ($testi) should look like:
 *       array(
 *             'str'=>'sample string',            // a string to match
 *             'is_match'=>true,                  // is there a match?
 *             'full'=>true,                      // is it full?
 *             'index_first'=>array(0=>0),        // indexes of first correct characters for subpatterns. subpattern numbers are defined by array keys
 *             'index_last'=>array(0=>2),         // indexes of last correct characters for subpatterns.
 *             'left'=>array(0),                  // number of characters left to complete match. different engines can return different results, that's why it is an array
 *             'next'=>'');                       // a string of possible next characters in case of not full match
 *
 *    Here's an example test function:
 *    function data_for_test_example() {
 *       $test1 = array( 'str'=>'match ME',
 *                       'is_match'=>true,
 *                       'full'=>true,
 *                       'index_first'=>array(0=>0),
 *                       'index_last'=>array(0=>7),
 *                       'left'=>array(0),
 *                       'next'=>'');
 *       return array('regex'=>'.* ME',
 *                    'modifiers'=>'i',
 *                    'tests'=>array($test1));
 *   }
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');

class preg_cross_tester extends UnitTestCase {

    var $question;            // an object of question_preg_qtype
    var $engines = array();   // an array of available engines

    public function __construct() {
        $question = new question_preg_qtype();
        $this->engines = $question->available_engines();
        unset($this->engines['preg_php_matcher']);
        $this->engines = array_keys($this->engines);
    }

    /**
    * checks a matcher for parsing and accepting errors
    * @param $matcher - a matcher to be checked
    */
    function check_for_errors(&$matcher) {
        if ($matcher->is_error_exists()) {
            $errors = $matcher->get_error_objects();
            foreach ($errors as $error) {
                if (is_a($error, 'preg_parsing_error') /*|| is_a($error, 'preg_modifier_error')*/) {    // error messages are displayed for parsing errors only
                    echo 'Regex incorrect: '.$error->errormsg.'<br/>';
                    $this->assertTrue(false);
                }
            }
            return true;
        }
        return false;
    }

    function test() {
        $testmethods = get_class_methods($this);
        foreach ($testmethods as $curtestmethod) {
            // filtering class methods by names. A test method name should start with 'data_for_test_'
            $pos = strstr($curtestmethod, 'data_for_test_');
            if ($pos != false && $pos == 0) {
                $data = $this->$curtestmethod();
                $regex = $data['regex'];
                $modifiers = null;
                if (array_key_exists('modifiers', $data)) {
                    $modifiers = $data['modifiers'];
                }
                // iterate over available engines
                foreach ($this->engines as $enginename) {
                    $matcher = new $enginename($regex, $modifiers);
                    if (!$this->check_for_errors($matcher)) {
                        // iterate over all tests
                        foreach ($data['tests'] as $expected) {
                            $str = $expected['str'];
                            $matcher->match($str);
                            $matchername = $matcher->name();
                            $obtained = $matcher->get_match_results();
                            $passed = $this->assertTrue($expected['is_match'] == $obtained['is_match'], "$matchername failed 'is_match' check on regex '$regex' and string '$str'");
                            $passed = $passed && $this->assertTrue($expected['full'] == $obtained['full'], "$matchername failed 'full' check on regex '$regex' and string '$str'");
                            if ($obtained['is_match'] && $expected['is_match']) {
                                if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                                    $passed = $passed && $this->assertTrue($expected['index_first'] == $obtained['index_first'], "$matchername failed 'index_first' check on regex '$regex' and string '$str'");
                                    $passed = $passed && $this->assertTrue($expected['index_last'] == $obtained['index_last'], "$matchername failed 'index_last' check on regex '$regex' and string '$str'");
                                } else {
                                    $passed = $passed && $this->assertTrue($expected['index_first'][0] == $obtained['index_first'][0], "$matchername failed 'index_first' check on regex '$regex' and string '$str'");
                                    $passed = $passed && $this->assertTrue($expected['index_last'][0] == $obtained['index_last'][0], "$matchername failed 'index_last' check on regex '$regex' and string '$str'");
                                }
                                if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                                    $passed = $passed && $this->assertTrue(($expected['next'] === '' && $obtained['next'] === '') || ($expected['next'] !== '' && $obtained['next'] !== '' && strstr($expected['next'], $obtained['next']) != false), "$matchername failed 'next' check on regex '$regex' and string '$str'");  // expected 'next' contains obtained 'next'
								}
                                if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                                    $passed = $passed && $this->assertTrue(in_array($obtained['left'], $expected['left']), "$matchername failed 'left' check on regex '$regex' and string '$str'");
								}
                            }
                        }
                    }
                }
            }
        }
    }
}

?>