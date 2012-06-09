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
 *    The cross-tester searches (not recursively!) for files named "cross_tests_<suffix>.php". A class with test data should be named the same as the corresponding file.
 *    For example, a file named "cross_tests_example.php" should contain a class named "cross_test_example".
 *    Next, those classes represent test data as a set of test functions. Those functions should:
 *    -be named "data_for_test_..."
 *    -return an array of input and output data as in the following example:
 *       array(
 *             'regex'=>'^[-.\w]+[a-z]{2,6}$',     // A regular expression.
 *             'modifiers'=>'i',                   // Modifiers. It's not necessary to define this element.
 *             'tests'=>array($test1, ..., $testn) // Array containing tests in the format described below. Count of these tests is unlimited.
 *             );
 *
 *    Finally, an array of expected results ($testi) should look like:
 *       array(
 *             'str'=>'sample string',             // A string to match.
 *             'is_match'=>true,                   // Is there a match?
 *             'full'=>true,                       // Is it full?
 *             'index_first'=>array(0=>0),         // Indexes of first correct characters for subpatterns. Subpattern numbers are defined by array keys.
 *             'length'=>array(0=>2),              // Lengths of all subpatterns should be matched.
 *             'left'=>0,                          // Number of characters left to complete match.
 *             'next'=>'');                        // A string of possible next characters in case of not full match.
 *
 *    Remark: different matching engines may give different results, especially when matching quantifiers. This situation appears when a character may
 *    lead to continuing matching both a quantifier and the rest of the regex, for example:
 *    the regex is '[a-z]*bacd' and the string is 'abacd'. The character is underlined.
 *                                                  ^
 *    For this kind of situations it's possible to define different acceptable results.
 *    In this case the 'str' field remains the same, but the second field would be an array of possible match results and defined by the 'results' key:
 *    'results' => array(array('is_match'=>true, ...), array('is_match'=>false),...).
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
 *                       'results'=>array(array('is_match'=>true,    // Result for backtracking engine.
 *                                              'full'=>false,
 *                                              'index_first'=>array(0=>0),
 *                                              'length'=>array(0=>3),
 *                                              'left'=>array(4),
 *                                              'next'=>'b'),
 *                                        array('is_match'=>true,    // Result for FA engine.
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
 *    So when you are writing a test containing quantifiers, don't forget to use arrays to define all possible results if they depend on engines!
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Represents auxiliary class for extra checks. The extra checks are performed by cross-tester
 * on tests with partial matching: cross-tester concatenates correct heading and returned ending, then
 * checks this string for full match and some other equalities. The purpose of this class is only to
 * return the name of the matcher to do this check.
 */
abstract class qtype_preg_cross_tests_extra_checker {

    /**
     * Returns name of the engine, implement it in child classes for each engine.
     */
    abstract public function engine_name();

}

class qtype_preg_cross_tester extends UnitTestCase {

    var $testdataobjects;    // Objects with test data.
    var $extracheckobjects;  // Objects for extra checks.

    /**
     * Returns name of the engine to be tested (without qtype_preg_ prefix!). Should be re-implemented in child classes.
     */
    public function engine_name() {
        return '';
    }

    public function __construct() {
        global $CFG;
        $this->testdataobjects = array();
        $this->extracheckobjects = array();
        $enginename = $this->engine_name();
        if ($enginename === '') {
            return;
        }
        // Include file with matcher to test.
        require_once($CFG->dirroot . '/question/type/preg/' . $enginename . '/' . $enginename . '.php');
        // Find all available test files.
        if ($dh = opendir($CFG->dirroot . '/question/type/preg/simpletest')) {
            while (($file = readdir($dh)) !== false) {
                if (strpos($file, 'cross_tests_') === 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    require_once($CFG->dirroot . '/question/type/preg/simpletest/' . $file);
                    $classname = 'qtype_preg_' . pathinfo($file, PATHINFO_FILENAME);
                    if (strpos($file, 'cross_tests_extra_checker') === 0) {
                        // Extra checker found.
                        $obj = new $classname;
                        $enginename = $obj->engine_name();
                        require_once($CFG->dirroot . '/question/type/preg/' . $enginename . '/' . $enginename . '.php');
                        $this->extracheckobjects[] = new $obj;
                    } else {
                        // Test data object found.
                        $this->testdataobjects[] = new $classname;
                    }
                }
            }
            closedir($dh);
        }
    }

    /**
     * Checks matcher for parsing and accepting errors.
     * @param $matcher - a matcher to be checked.
     * @return true if there are errors, false otherwise.
     */
    function check_for_errors(&$matcher) {
        if ($matcher->is_error_exists()) {
            $errors = $matcher->get_error_objects();
            foreach ($errors as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {    // Error messages are displayed for parsing errors only.
                    echo 'Regex incorrect: ' . $error->errormsg . '<br/>';
                    $this->assertTrue(false);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Performs some extra checks on results which contain generated ending of a partial match.
     * @param $regex - regular expression.
     * @param $modifiers - modifiers.
     * @param $obtained - a result to check.
     */
    function do_extra_check($regex, $modifiers, $obtained) {
        $boolstr = array(false => 'FALSE', true => 'TRUE');
        if ($obtained->extendedmatch === null) {
            return;
        }
        $str = $obtained->matched_part() . $obtained->string_extension();
        $thisenginename = $this->engine_name();
        foreach ($this->extracheckobjects as $obj) {
            $enginename = 'qtype_preg_' . $obj->engine_name();
            $matcher = new $enginename($regex, $modifiers);
            if ($obtained->extendedmatch->full || $matcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
                $matcher->match($str);
                $newresults = $matcher->get_match_results();

                // Length + left should remain the same.
                $sum1 = $obtained->length() + $obtained->left;
                $sum2 = $obtained->extendedmatch->length() + $obtained->extendedmatch->left;
                if ($obtained->length() === qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $sum1++;
                }

                // Do assertions.
                $full = $this->assertTrue($newresults->full === $obtained->extendedmatch->full, "$thisenginename failed 'full' EXTRA check on regex '$regex' and string '$str'");
                $sum = $this->assertTrue($sum1 === $sum2, "$thisenginename failed 'full' EXTRA check on regex '$regex' and string '$str'");
                if (!$full) {
                    echo "extended match field 'full' has the value of " . $boolstr[$obtained->extendedmatch->full] . " which is incorrect (extra-tested by $enginename)<br/>";
                }
                if (!$sum) {
                    echo "extended match fields 'length' and 'left' didn't pass: the old values are " . $obtained->length() . " and " . $obtained->left . ", the new values are " . $obtained->extendedmatch->length() . " and " . $obtained->extendedmatch->left . " (extra-tested by $enginename)<br/>";
                }
            }
        }
    }

    /**
     * Compares obtained results with expected and writes all flags.
     */
    function compare_results($regex, $modifiers, &$matcher, &$expected, &$obtained, &$ismatchpassed, &$fullpassed, &$indexfirstpassed, &$indexlastpassed, &$nextpassed, &$leftpassed) {
        $ismatchpassed = ($expected['is_match'] === $obtained->is_match());
        $fullpassed = ($expected['full'] === $obtained->full);

        // If no match found, generate arrays of qtype_preg_matching_results::NO_MATCH_FOUND; use $expected otherwise.
        if ($obtained->is_match()) {
            $index_first_expected = $expected['index_first'];
            $length_expected = $expected['length'];
        } else {
            $index_first_expected = array_fill(0, count($expected['index_first']), qtype_preg_matching_results::NO_MATCH_FOUND);
            $length_expected = $index_first_expected;
        }

        // Checking indexes.
        if ($matcher->is_supporting(qtype_preg_matcher::SUBPATTERN_CAPTURING)) {
            $indexfirstpassed = ($index_first_expected === $obtained->index_first);
            $indexlastpassed = ($length_expected === $obtained->length);
        } else {
            $indexfirstpassed = ($index_first_expected[0] === $obtained->index_first[0]);
            $indexlastpassed = ($length_expected[0] === $obtained->length[0]);
        }

        // Checking next possible character.
        $nextpassed = true;
        if ($matcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            $str = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER;
            if ($obtained->extendedmatch !== null) {
                $str = $obtained->string_extension();
            }
            $nextpassed = (($expected['next'] === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $str === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) ||
                           ($expected['next'] !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $str !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER &&
                            qtype_preg_unicode::strpos($expected['next'], qtype_preg_unicode::substr($str, 0, 1)) !== false));    // Expected 'next' contains obtained 'next'.
        }

        // Checking number of characters left.
        $leftpassed = true;
        if ($matcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
            $leftpassed = in_array($obtained->left, $expected['left']);
        }
        $this->do_extra_check($regex, $modifiers, $obtained);
        return $ismatchpassed && $fullpassed && $indexfirstpassed && $indexlastpassed && $nextpassed && $leftpassed;
    }

    /**
     * Does assertions for every field. if assertionstrue === true then error messages displayed only.
     */
    function do_assertions($enginename, $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed, $testdataclassname, $assertionstrue = false) {
        $boolstr = array(false => 'FALSE', true => 'TRUE');
        $this->assertTrue($assertionstrue || $ismatchpassed, "$enginename failed 'is_match' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$ismatchpassed) {
            echo 'obtained result ' . $boolstr[$obtained->is_match()] . " for 'is_match' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $fullpassed, "$enginename failed 'full' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$fullpassed) {
            echo 'obtained result ' . $boolstr[$obtained->full] . " for 'full' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $indexfirstpassed, "$enginename failed 'index_first' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$indexfirstpassed) {
            echo 'obtained result '; print_r($obtained->index_first); echo " for 'index_first' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $indexlastpassed, "$enginename failed 'length' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$indexlastpassed) {
            echo 'obtained result '; print_r($obtained->length); echo " for 'length' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $nextpassed, "$enginename failed 'next' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$nextpassed) {
            echo 'obtained result \'' . $obtained->string_extension() . "' for 'next' is incorrect    (test from $testdataclassname)<br/>";
        }
        $this->assertTrue($assertionstrue || $leftpassed, "$enginename failed 'left' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$leftpassed) {
            echo 'obtained result \'' . $obtained->left . "' for 'left' is incorrect    (test from $testdataclassname)<br/>";
        }
    }

    /**
     * The main function - runs all matchers on test-data sets.
     */
    function test() {
        global $CFG;
        $enginename = 'qtype_preg_' . $this->engine_name();
        foreach ($this->testdataobjects as $testdataobj) {
            $testmethods = get_class_methods($testdataobj);
            $testdataclassname = get_class($testdataobj);
            foreach ($testmethods as $curtestmethod) {
                // Filtering class methods by names. A test method name should start with 'data_for_test_'.
                if (strpos($curtestmethod, 'data_for_test_') === 0) {
                    $data = $testdataobj->$curtestmethod();
                    $regex = $data['regex'];
                    $modifiers = null;
                    if (array_key_exists('modifiers', $data)) {
                        $modifiers = $data['modifiers'];
                    }
                    // Iterate over available engines.
                    $matcher = new $enginename($regex, $modifiers);
                    if (!$this->check_for_errors($matcher)) {
                        // Iterate over all tests.
                        foreach ($data['tests'] as $expected) {
                            $str = $expected['str'];
                            $matcher->match($str);
                            $obtained = $matcher->get_match_results();
                            // Now the results are obtained, let us check them!
                            if (array_key_exists('is_match', $expected)) {
                                // Compare with a single result.
                                $ismatchpassed = false;
                                $fullpassed = false;
                                $indexfirstpassed = false;
                                $indexlastpassed = false;
                                $nextpassed = false;
                                $leftpassed = false;
                                $this->compare_results($regex, $modifiers, $matcher, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed);
                                $this->do_assertions($this->engine_name(), $regex, $str, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $indexlastpassed, $nextpassed, $leftpassed, $testdataclassname);
                            } else {
                                // Compare with a set of possible results.
                                $ismatchpassed = array();
                                $fullpassed = array();
                                $indexfirstpassed = array();
                                $indexlastpassed = array();
                                $nextpassed = array();
                                $leftpassed = array();
                                $indexmatch = array(); // Index of $expected['results'] which match obtained $index_first and $length.
                                $passed = false;
                                foreach ($expected['results'] as $key => $curexpected) {
                                    $ismatchpassed[$key] = false;
                                    $fullpassed[$key] = false;
                                    $indexfirstpassed[$key] = false;
                                    $indexlastpassed[$key] = false;
                                    $nextpassed[$key] = false;
                                    $leftpassed[$key] = false;
                                    $passed = $passed || $this->compare_results($regex, $modifiers, $matcher, $curexpected, $obtained, $ismatchpassed[$key], $fullpassed[$key], $indexfirstpassed[$key], $indexlastpassed[$key], $nextpassed[$key], $leftpassed[$key]);
                                    if ($indexfirstpassed[$key] && $indexlastpassed[$key]) {
                                        $indexmatch[] = $key;
                                    }
                                }
                                $this->assertTrue($passed, "$enginename failed on regex '$regex' and string '$str'");
                                // If the test is not passed - display obtained results.
                                if (!$passed) {
                                    // If some indexes were matched - display other fields not matched.
                                    foreach ($indexmatch as $key) {
                                        $number = $key + 1;
                                        echo "Results of comparison for the $number possible result:<br/>";
                                        $this->do_assertions($this->engine_name(), $regex, $str, $obtained, $ismatchpassed[$key], $fullpassed[$key], $indexfirstpassed[$key], $indexlastpassed[$key], $nextpassed[$key], $leftpassed[$key], $testdataclassname, true);
                                        echo '<br/>';
                                    }
                                    // If indexes were not matched at all - just print the obtained result.
                                    if (count($indexmatch) === 0) {
                                        echo "Indexes not matched at all. Obtained result is:<br/>";
                                        echo 'is_match = ' . $obtained->is_match(); echo '<br/>';
                                        echo 'full = ' . $obtained->full; echo '<br/>';
                                        echo 'index_first = '; print_r($obtained->index_first); echo '<br/>';
                                        echo 'length = '; print_r($obtained->length); echo '<br/>';
                                        echo 'correctending = ' . $obtained->extendedmatch->string_extension() . '<br/>';
                                        echo 'left = ' . $obtained->left . '<br/>';
                                        echo "(test from $testdataclassname)<br/>";
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
