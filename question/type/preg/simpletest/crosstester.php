<?php
/**
 * Data-driven cross-tester of matchers. Test functions should be implemented in child classes.
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
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
 *             'index_last'=>array(0=>2),         // indexes of last correct characters for subpatterns
 *             'left'=>0,                         // number of characters left to complete match
 *             'next'=>'');                       // a string of possible next characters in case of not full match
 *
 *    Remark: different matching engines may give different results, especially when matching quantifiers. That's why there's another way to define
 *    'index_first', 'index_last', 'left' and 'next' fields - they can be arrays of possible results. A test is passed if engine returns a result which matches one of them.
 *    Note that 'is_match' and 'full' should NOT be stored in arrays - they are equal for all engines.
 *
 *    Here's an example test function for the only possible match result:
 *    function data_for_test_example() {
 *       $test1 = array( 'str'=>'match ME',
 *                       'is_match'=>true,
 *                       'full'=>true,
 *                       'index_first'=>array(0=>0),
 *                       'index_last'=>array(0=>7),
 *                       'left'=>0,
 *                       'next'=>'');
 *
 *       return array('regex'=>'.* ME',
 *                    'modifiers'=>'i',
 *                    'tests'=>array($test1));
 *    }
 *
 *    And here's an example test function for case when finite automata and backtracking engines give different results. Results in different arrays should be in the same sequence!
 *    function data_for_test_example() {
 *       $test1 = array( 'str'=>'abacd',
 *                       'is_match'=>true,
 *                       'full'=>false,
 *                       'index_first'=>array(array(0=>0),  // first index returned by backtracking engine
 *                                            array(0=>0)), // first index returned by finite automata engine
 *                       'index_last'=>array(array(0=>2),   // last index returned by backtracking engine
 *                                           array(0=>4)),  // last index returned by finite automata engine
 *                       'left'=>array(4, 4),               // results for backtracking and fa respectively
 *                       'next'=>array('b', 'b'));          // results for backtracking and fa respectively
 *
 *       return array('regex'=>'ab+[a-z]*bacd',
 *                    'tests'=>array($test1));
 *    }
 *
 *    So when you are writing a test containing quantifiers, don't forget to use arrays for the specified fields!
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
    * checks matcher for parsing and accepting errors
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

    function compare_with_single_result(&$matcher, &$expected, &$obtained, &$indexfirstpassed, &$indexlastpassed, &$nextpassed, &$leftpassed) {
        // checking indexes
        if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
            $indexfirstpassed = ($expected['index_first'] == $obtained['index_first']);
            $indexlastpassed = ($expected['index_last'] == $obtained['index_last']);
        } else {
            $indexfirstpassed = ($expected['index_first'][0] == $obtained['index_first'][0]);
            $indexlastpassed = ($expected['index_last'][0] == $obtained['index_last'][0]);
        }
        // checking next possible character
        if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
            $nextpassed = (($expected['next'] === '' && $obtained['next'] === '') ||                                                            // both results are empty
                           ($expected['next'] !== '' && $obtained['next'] !== '' && strstr($expected['next'], $obtained['next']) != false));    // expected 'next' contains obtained 'next'
        } else {
            $nextpassed = true;
        }
        // checking number of characters left
        if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
            $leftpassed = ($obtained['left'] == $expected['left']);
        } else {
            $leftpassed = true;
        }
    }

    function compare_with_multiple_results(&$matcher, &$expected, &$obtained, &$indexfirstpassed, &$indexlastpassed, &$nextpassed, &$leftpassed) {
        // checking indexes
        $passindex = -1; // results should have the same index in arrays
        foreach ($expected['index_first'] as $index=>$curexpected) {
            if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                $indexfirstpassed = ($curexpected == $obtained['index_first']);
                if ($indexfirstpassed) {
                    $indexlastpassed = ($expected['index_last'][$index] == $obtained['index_last']);
                }
                // save index for checking fields 'left' and 'next'
                if ($indexlastpassed) {
                    $passindex = $index;
                    break;
                }
            } else {
                $indexfirstpassed = ($curexpected[0] == $obtained['index_first'][0]);
                if ($indexfirstpassed) {
                    $indexlastpassed = ($expected['index_last'][$index][0] == $obtained['index_last'][0]);
                }
                // save index for checking next fields - 'left' and 'next'
                if ($indexlastpassed) {
                    $passindex = $index;
                    break;
                }
            }
        }
        // checking next possible character and number of characters left
        if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER) && $passindex >= 0) {
            $nextpassed = (($expected['next'][$passindex] === '' && $obtained['next'] === '') ||                                                         // both results are empty
            ($expected['next'][$passindex] !== '' && $obtained['next'] !== '' && strstr($expected['next'][$passindex], $obtained['next']) != false));    // expected 'next' contains obtained 'next'
        } else {
            $nextpassed = true;
        }
        if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT) && $passindex >= 0) {
            $leftpassed = ($obtained['left'] == $expected['left'][$passindex]);
        } else {
            $leftpassed = true;
        }
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
                            // not the results are obtained, let us check them!
                            $ismatchpassed = ($expected['is_match'] == $obtained['is_match']);
                            $fullpassed = ($expected['full'] == $obtained['full']);

                            // if there's a match - check the other fields
                            if ($obtained['is_match'] && $expected['is_match']) {
                                $indexfirstpassed = false;
                                $indexlastpassed = false;
                                $nextpassed = false;
                                $leftpassed = false;
                                // how many possible results?
                                if (is_array($expected['index_first'][0])) {
                                    $this->compare_with_multiple_results($matcher, $expected, $obtained, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                                } else {
                                    $this->compare_with_single_result($matcher, $expected, $obtained, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                                }
                            }
                            // it's time to pass of to fail the test
                            $this->assertTrue($ismatchpassed, "$matchername failed 'is_match' check on regex '$regex' and string '$str'");
                            if (!$ismatchpassed) {
                                echo 'obtained result ' . $obtained['is_match'] . ' for \'is_match\' is incorrect<br/>';
                            }
                            $this->assertTrue($fullpassed, "$matchername failed 'full' check on regex '$regex' and string '$str'");
                            if (!$fullpassed) {
                                echo 'obtained result ' . $obtained['full'] . ' for \'full\' is incorrect<br/>';
                            }
                            $this->assertTrue($indexfirstpassed, "$matchername failed 'index_first' check on regex '$regex' and string '$str'");
                            if (!$indexfirstpassed) {
                                echo 'obtained result '; print_r($obtained['index_first']); echo ' for \'index_first\' is incorrect<br/>';
                            }
                            $this->assertTrue($indexlastpassed, "$matchername failed 'index_last' check on regex '$regex' and string '$str'");
                            if (!$indexlastpassed) {
                                echo 'obtained result '; print_r($obtained['index_last']); echo ' for \'index_last\' is incorrect<br/>';
                            }
                            $this->assertTrue($nextpassed, "$matchername failed 'next' check on regex '$regex' and string '$str'");
                            if (!$nextpassed) {
                                echo 'obtained result \'' . $obtained['next'] . '\' for \'next\' is incorrect<br/>';
                            }
                            $this->assertTrue($leftpassed, "$matchername failed 'left' check on regex '$regex' and string '$str'");
                            if (!$leftpassed) {
                                echo 'obtained result \'' . $obtained['left'] . '\' for \'left\' is incorrect<br/>';
                            }

                        }
                    }
                }
            }
        }
    }
}

?>