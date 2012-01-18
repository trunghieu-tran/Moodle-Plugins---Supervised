<?php
/**
 * Data-driven cross-tester of matchers. Test functions should be implemented in child classes.
 *
 * @copyright &copy; 2012  Valeriy Streltsov
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
 *             'length'=>array(0=>2),             // length of the i-subpattern
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
 *                       'length'=>array(0=>8),
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
 *                                              'length'=>array(0=>3),
 *                                              'left'=>array(4),
 *                                              'next'=>'b'),
 *                                        array('is_match'=>true,    // result for fa engine
 *                                              'full'=>false,
 *                                              'index_first'=>array(0=>0),
 *                                              'length'=>array(0=>5),
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

class qtype_preg_cross_tester extends UnitTestCase {

    var $testdataobjects;    // objects with test data

    /**
    * Returns name of the engine to be tested (without qtype_preg_ prefix!). Should be re-implemented in child classes.
    */
    public function engine_name() {
        return '';
    }

    public function __construct() {
        global $CFG;
        $this->testdataobjects = array();
        if ($this->engine_name() === '') {
            return;
        }
        // find all available test files
        if ($dh = opendir($CFG->dirroot . '/question/type/preg/simpletest')) {
            while (($file = readdir($dh)) !== false) {
                if (strpos($file, 'cross_tests_') === 0 && pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                    require_once($CFG->dirroot . '/question/type/preg/simpletest/' . $file);
                    $classname = 'qtype_preg_' . pathinfo($file, PATHINFO_FILENAME);
                    $this->testdataobjects[] = new $classname;
                }
            }
            closedir($dh);
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
                if (is_a($error, 'qtype_preg_parsing_error')) {    // error messages are displayed for parsing errors only
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
        $ismatchpassed = ($expected['is_match'] == $obtained->is_match());
        $fullpassed = ($expected['full'] == $obtained->full);
        $result = $ismatchpassed && $fullpassed;
        if ($obtained->is_match() && $expected['is_match']) {   // TODO - what if we need a character with no match?
            // checking indexes
            if ($matcher->is_supporting(qtype_preg_matcher::SUBPATTERN_CAPTURING)) {
                $indexfirstpassed = ($expected['index_first'] == $obtained->index_first);
                $indexlastpassed = ($expected['length'] == $obtained->length);
            } else {
                $indexfirstpassed = ($expected['index_first'][0] == $obtained->index_first[0]);
                $indexlastpassed = ($expected['length'][0] == $obtained->length[0]);
            }
            // checking next possible character
            if ($matcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
                $nextpassed = (($expected['next'] === '' && $obtained->next === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) ||                                                           // both results are empty
                               ($expected['next'] !== '' && $obtained->next !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && strpos($expected['next'], $obtained->next) !== false));    // expected 'next' contains obtained 'next'
            } else {
                $nextpassed = true;
            }
            // checking number of characters left
            if ($matcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
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
    function do_assertions($matchername, $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed, $testdataclassname, $assertionstrue = false) {
        $boolstr = array(false=>'FALSE', true=>'TRUE');
        $this->assertTrue($assertionstrue || $ismatchpassed, "$matchername failed 'is_match' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$ismatchpassed) {
            echo 'obtained result ' . $boolstr[$obtained->is_match()] . " for 'is_match' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $fullpassed, "$matchername failed 'full' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$fullpassed) {
            echo 'obtained result ' . $boolstr[$obtained->full] . " for 'full' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $indexfirstpassed, "$matchername failed 'index_first' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$indexfirstpassed) {
            echo 'obtained result '; print_r($obtained->index_first); echo " for 'index_first' is incorrect    (test from $testdataclassname)<br/>";
        }

        $this->assertTrue($assertionstrue || $indexlastpassed, "$matchername failed 'length' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$indexlastpassed) {
            echo 'obtained result '; print_r($obtained->length); echo " for 'length' is incorrect    (test from $testdataclassname)<br/>";
        }

        $this->assertTrue($assertionstrue || $nextpassed, "$matchername failed 'next' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$nextpassed) {
            echo 'obtained result \'' . $obtained->next . "' for 'next' is incorrect    (test from $testdataclassname)<br/>";
        }

        $this->assertTrue($assertionstrue || $leftpassed, "$matchername failed 'left' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$leftpassed) {
            echo 'obtained result \'' . $obtained->left . "' for 'left' is incorrect    (test from $testdataclassname)<br/>";
        }

    }

    /**
    * the main function - runs all matchers on test-data sets
    */
    function test() {
        global $CFG;
        $enginename = $this->engine_name();
        if ($enginename === '') {
            return;
        }
        require_once($CFG->dirroot . '/question/type/preg/' . $enginename . '/' . $enginename . '.php');    // matching engine
        $enginename = 'qtype_preg_' . $enginename;
        foreach ($this->testdataobjects as $testdataobj) {
            $testmethods = get_class_methods($testdataobj);
            foreach ($testmethods as $curtestmethod) {
                // filtering class methods by names. A test method name should start with 'data_for_test_'
                if (strpos($curtestmethod, 'data_for_test_') === 0) {
                    $data = $testdataobj->$curtestmethod();
                    $regex = $data['regex'];
                    $modifiers = null;
                    if (array_key_exists('modifiers', $data)) {
                        $modifiers = $data['modifiers'];
                    }
                    // iterate over available engines
                    $matcher = new $enginename($regex, $modifiers);
                    if (!$this->check_for_errors($matcher)) {
                        // iterate over all tests
                        foreach ($data['tests'] as $expected) {
                            $str = $expected['str'];
                            $matcher->match($str);
                            $obtained = $matcher->get_match_results();
                            // now the results are obtained, let us check them!
                            if (array_key_exists('is_match', $expected)) {
                                // compare with single result
                                $ismatchpassed = false;
                                $fullpassed = false;
                                $indexfirstpassed = false;
                                $indexlastpassed = false;
                                $nextpassed = false;
                                $leftpassed = false;
                                $this->compare_results($matcher, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                                $this->do_assertions($enginename, $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed, get_class($testdataobj));
                            } else {
                                // compare with multiple results
                                $ismatchpassed = array();
                                $fullpassed = array();
                                $indexfirstpassed = array();
                                $indexlastpassed = array();
                                $nextpassed = array();
                                $leftpassed = array();
                                $indexmatch = array(); // index of $expected['results'] which match obtained $index_first and $length
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
                                $this->assertTrue($passed, "$enginename failed on regex '$regex' and string '$str'");
                                // if the test is not passed - display obtained results
                                if (!$passed) {
                                    // if some indexes were matched - display other fields not matched
                                    foreach ($indexmatch as $key) {
                                        $number = $key + 1;
                                        echo "Results of comparison for the $number possible result:<br/>";
                                        $this->do_assertions($enginename, $regex, $str, $obtained, $ismatchpassed[$key], $fullpassed[$key], $indexfirstpassed[$key], $indexlastpassed[$key], $nextpassed[$key], $leftpassed[$key], get_class($testdataobj), true);
                                        echo '<br/>';
                                    }
                                    // if indexes were not matched at all - just print the obtained result
                                    if (count($indexmatch) == 0) {
                                        echo "Indexes not matched at all. Obtained result is:<br/>";
                                        echo 'is_match = ' . $obtained->is_match(); echo '<br/>';
                                        echo 'full = ' . $obtained->full; echo '<br/>';
                                        echo 'index_first = '; print_r($obtained->index_first); echo '<br/>';
                                        echo 'length = '; print_r($obtained->length); echo '<br/>';
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