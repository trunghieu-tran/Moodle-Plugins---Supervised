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
 *    Remark: different matching engines may give different results, especially when matching quantifiers. For that situations it's possible to define different acceptable results.
 *    In this case the 'str' field remains the same, but the second field would be an array of possible match results and defined by the 'results' key:
 *    'results' => array(array('is_match'=>true, ...), array('is_match'=>false),...).
 *    This situation appears when a character may lead to continuing matching both quantifier and the rest of the regex, for example:
 *    the regex is '[a-z]*bacd' and the string is 'abacd'. The character is underlined.
 *                                                  ^
 *    
 *    A test is passed if engine returns a result which matches one element of this array.
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
 *    And here's an example test function for case when finite automata and backtracking engines give different results.
 *    function data_for_test_quant_greedy() {
 *        $test1 = array('str'=>'abacd',
 *                       'results'=>array(array('is_match'=>true,    // result for backtracking engine
 *                                              'full'=>false,
 *                                              'index_first'=>array(0=>0),
 *                                              'index_last'=>array(0=>2),
 *                                              'left'=>array(4),
 *                                              'next'=>'b'),
 *                                        array('is_match'=>true,    // result for fa engine
 *                                              'full'=>false,
 *                                              'index_first'=>array(0=>0),
 *                                              'index_last'=>array(0=>4),
 *                                              'left'=>array(4),
 *                                              'next'=>'b')
 *                                        ));
 *
 *       return array('regex'=>'ab+[a-z]*bacd',
 *                    'tests'=>array($test1));
 *   }
 *
 *    So when you are writing a test containing quantifiers, don't forget to use arrays to define all possible results!
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');

class preg_cross_tester extends UnitTestCase {

    var $question;            // an object of question_preg_qtype
    var $engines = array();   // an array of available engines

    public function __construct() {
        global $CFG;

        $question = new qtype_preg();
        $this->engines = $question->available_engines();
        unset($this->engines['preg_php_matcher']);
        $this->engines = array_keys($this->engines);
        foreach ($this->engines as $enginename) {
            require_once($CFG->dirroot . '/question/type/preg/'.$enginename.'/'.$enginename.'.php');
        }
    }

    /**
    * checks matcher for parsing and accepting errors
    * @param $matcher - a matcher to be checked
    */
    function check_for_errors(&$matcher) {
        if ($matcher->is_error_exists()) {
            $errors = $matcher->get_error_objects();
            foreach ($errors as $error) {
                if (is_a($error, 'preg_parsing_error')) {    // error messages are displayed for parsing errors only
                    echo 'Regex incorrect: '.$error->errormsg.'<br/>';
                    $this->assertTrue(false);
                }
            }
            return true;
        }
        return false;
    }

    /**
    * compares obtained results with expected and writes all flags
    */
    function compare_results(&$matcher, &$expected, &$obtained, &$ismatchpassed, &$fullpassed, &$indexfirstpassed, &$indexlastpassed, &$nextpassed, &$leftpassed) {
        $ismatchpassed = ($expected['is_match'] == $obtained->is_match);
        $fullpassed = ($expected['full'] == $obtained->full);
        $result = $ismatchpassed && $fullpassed;
        if ($obtained->is_match && $expected->is_match) {   // TODO - what if we need a character with no match?
            // checking indexes
            if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                $indexfirstpassed = ($expected['index_first'] == $obtained->index_first);
                $indexlastpassed = ($expected['index_last'] == $obtained->index_last);
            } else {
                $indexfirstpassed = ($expected['index_first'][0] == $obtained->index_first[0]);
                $indexlastpassed = ($expected['index_last'][0] == $obtained->index_last[0]);
            }
            // checking next possible character
            if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                $nextpassed = (($expected['next'] === '' && $obtained->next === '') ||                                                            // both results are empty
                               ($expected['next'] !== '' && $obtained->next !== '' && strpos($expected['next'], $obtained->next) !== false));    // expected 'next' contains obtained 'next'
            } else {
                $nextpassed = true;
            }
            // checking number of characters left
            if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                $leftpassed = in_array($obtained->left, $expected['left']);
            } else {
                $leftpassed = true;
            }
            $result = $result && $indexfirstpassed && $indexlastpassed && $nextpassed && $leftpassed;
        } else {
            $indexfirstpassed = true;
            $indexlastpassed = true;
            $nextpassed = true;
            $leftpassed = true;
        }
        return $result;
    }

    /**
    * does assertions for every field. if assertionstrue == true then error messages displayed only
    */
    function do_assertions($matchername, $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed, $assertionstrue = false) {
        $this->assertTrue($assertionstrue || $ismatchpassed, "$matchername failed 'is_match' check on regex '$regex' and string '$str'");
        if (!$ismatchpassed) {
            echo 'obtained result ' . $obtained->is_match . ' for \'is_match\' is incorrect<br/>';
        }
        $this->assertTrue($assertionstrue || $fullpassed, "$matchername failed 'full' check on regex '$regex' and string '$str'");
        if (!$fullpassed) {
            echo 'obtained result ' . $obtained->full . ' for \'full\' is incorrect<br/>';
        }
        $this->assertTrue($assertionstrue || $indexfirstpassed, "$matchername failed 'index_first' check on regex '$regex' and string '$str'");
        if (!$indexfirstpassed) {
            echo 'obtained result '; print_r($obtained->index_first); echo ' for \'index_first\' is incorrect<br/>';
        }

        $this->assertTrue($assertionstrue || $indexlastpassed, "$matchername failed 'index_last' check on regex '$regex' and string '$str'");
        if (!$indexlastpassed) {
            echo 'obtained result '; print_r($obtained->index_last); echo ' for \'index_last\' is incorrect<br/>';
        }

        $this->assertTrue($assertionstrue || $nextpassed, "$matchername failed 'next' check on regex '$regex' and string '$str'");
        if (!$nextpassed) {
            echo 'obtained result \'' . $obtained->next . '\' for \'next\' is incorrect<br/>';
        }

        $this->assertTrue($assertionstrue || $leftpassed, "$matchername failed 'left' check on regex '$regex' and string '$str'");
        if (!$leftpassed) {
            echo 'obtained result \'' . $obtained->left . '\' for \'left\' is incorrect<br/>';
        }

    }

    /**
    * the main function - runs all matchers on test-data sets
    */
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
                            if (array_key_exists('is_match', $expected)) {
                                // compare with single result
                                $ismatchpassed = false;
                                $fullpassed = false;
                                $indexfirstpassed = false;
                                $indexlastpassed = false;
                                $nextpassed = false;
                                $leftpassed = false;
                                $this->compare_results($matcher, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                                $this->do_assertions($matchername, $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                            } else {
                                // compare with multiple results
                                $ismatchpassed = array();
                                $fullpassed = array();
                                $indexfirstpassed = array();
                                $indexlastpassed = array();
                                $nextpassed = array();
                                $leftpassed = array();
                                $indexmatch = array(); // index of $expecter['results'] which match obtained $index_first and $index_last
                                $passed = false;
                                foreach ($expected['results'] as $key=>$curexpected) {
                                    $ismatchpassed[$key] = false;
                                    $fullpassed[$key] = false;
                                    $indexfirstpassed[$key] = false;
                                    $indexlastpassed[$key] = false;
                                    $nextpassed[$key] = false;
                                    $leftpassed[$key] = false;
                                    $passed = $passed || $this->compare_results($matcher, $curexpected, $obtained, $ismatchpassed[$key], $fullpassed[$key], $indexfirstpassed[$key], $indexlastpassed[$key], $nextpassed[$key], $leftpassed[$key]);
                                    if ($indexfirstpassed[$key] && $indexlastpassed[$key]) {
                                        $indexmatch[] = $key;
                                    }
                                }
                                $this->assertTrue($passed, "$matchername failed on regex '$regex' and string '$str'");
                                // if the test is not passed - display obtained results
                                if (!$passed) {
                                    // if some indexes were matched - display other fields not matched
                                    foreach ($indexmatch as $key) {
                                        $number = $key + 1;
                                        echo "Results of comparison for the $number possible result:<br/>";
                                        $this->do_assertions($matchername, $regex, $str, $obtained, $ismatchpassed[$key], $fullpassed[$key], $indexfirstpassed[$key], $indexlastpassed[$key], $nextpassed[$key], $leftpassed[$key], true);
                                        echo '<br/>';
                                    }
                                    // if indexes were not matched at all - just print the obtained result
                                    if (count($indexmatch) == 0) {
                                        echo "Indexes not matched at all. Obtained result is:<br/>";
                                        echo 'is_match = ' . $obtained->is_match; echo '<br/>';
                                        echo 'full = ' . $obtained->full; echo '<br/>';
                                        echo 'index_first = '; print_r($obtained->index_first); echo '<br/>';
                                        echo 'index_last = '; print_r($obtained->index_last); echo '<br/>';
                                        echo 'next = ' . $obtained->next . '<br/>';
                                        echo 'left = ' . $obtained->left . '<br/>';
                                    }
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