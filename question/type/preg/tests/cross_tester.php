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
 *    For example, a file named "cross_tests_example.php" should contain a class named "cross_tests_example".
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
 *             'next'=>'',                         // A string of possible next characters in case of not full match.
 *             'tags'=>array());                   // Various tags, constants of the class below.
 *
 *    Remark: different matching engines may give different results, especially when matching quantifiers. This situation appears when a character may
 *    lead to continuing matching both a quantifier and the rest of the regex, for example:
 *    the regex is '[a-z]*bacd' and the string is 'abacd'. The character is underlined.
 *                                                  ^
 *    For this kind of situations it's needed to use TAGS which define engine-specific things.
 *
 *    Here's an example test function:
 *
 *    function data_for_test_example() {
 *       $test1 = array( 'str'=>'match ME',
 *                       'is_match'=>true,
 *                       'full'=>true,
 *                       'index_first'=>array(0=>0),
 *                       'length'=>array(0=>8),
 *                       'left'=>0,
 *                       'next'=>'',
 *                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PREG));
 *
 *       return array('regex'=>'.* ME',
 *                    'modifiers'=>'i',
 *                    'tests'=>array($test1));
 *    }
 *
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');

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

class qtype_preg_cross_tester extends PHPUnit_Framework_TestCase {

    const TAG_FROM_NFA          = 0;
    const TAG_FROM_DFA          = 1;
    const TAG_FROM_BACKTRACKING = 2;
    const TAG_FROM_PCRE         = 3;
    const TAG_FROM_AT_AND_T     = 4;

    var $testdataobjects;    // Objects with test data.
    var $extracheckobjects;  // Objects for extra checks.

    protected $eol           = "\n";                                    // End-of-line character.
    protected $boolstr       = array(false => 'FALSE', true => 'TRUE'); // For printing boolean values.
    protected $doextrachecks = false;                                   // TODO - control this field from outside.

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
        if ($dh = opendir($CFG->dirroot . '/question/type/preg/tests')) {
            while (($file = readdir($dh)) !== false) {
                if (strpos($file, 'cross_tests_') === 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    require_once($CFG->dirroot . '/question/type/preg/tests/' . $file);
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
                }
            }
            return true;
        }
        return false;
    }

    function check_next_character($regex, $char) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $leaf = $lexer->nextToken()->value;
        $res = $leaf->match(new qtype_poasquestion_string($char), 0, $length, false);
        fclose($pseudofile);
        return $res;
    }

    /**
     * Performs some extra checks on results which contain generated ending of a partial match.
     * @param $regex - regular expression.
     * @param $modifiers - modifiers.
     * @param $obtained - a result to check.
     */
    function do_extra_check($regex, $modifiers, $obtained) {
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
                    echo "extended match field 'full' has the value of " . $this->boolstr[$obtained->extendedmatch->full] . " which is incorrect (extra-tested by $enginename)<br/>";
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
    function compare_results($regex, $modifiers, &$matcher, &$expected, &$obtained, &$ismatchpassed, &$fullpassed, &$indexfirstpassed, &$lengthpassed, &$nextpassed, &$leftpassed) {
        $fullpassed = ($expected['full'] === $obtained->full);
        if ($matcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
            $ismatchpassed = ($expected['is_match'] === $obtained->is_match());
        } else {
            $ismatchpassed = $fullpassed;
        }

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
            $indexfirstpassed = true;
            foreach ($obtained->index_first as $key => $index) {
                $indexfirstpassed = $indexfirstpassed && ((!array_key_exists($key, $index_first_expected) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                          (array_key_exists($key, $index_first_expected) && $index_first_expected[$key] === $obtained->index_first[$key]));
                if (!$indexfirstpassed) {
                    break;
                }
            }

            $lengthpassed = true;
            foreach ($obtained->length as $key => $index) {
                $lengthpassed = $lengthpassed && ((!array_key_exists($key, $length_expected) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                  (array_key_exists($key, $length_expected) && $length_expected[$key] === $obtained->length[$key]));
                if (!$lengthpassed) {
                    break;
                }
            }
        } else {
            $indexfirstpassed = (!array_key_exists(0, $index_first_expected) && $obtained->index_first[0] === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                (array_key_exists(0, $index_first_expected) && $index_first_expected[0] === $obtained->index_first[0]);
            $lengthpassed = (!array_key_exists(0, $length_expected) && $obtained->length[0] === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                            (array_key_exists(0, $length_expected) && $length_expected[0] === $obtained->length[0]);
        }

        // Checking next possible character.
        $nextpassed = true;
        if ($matcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            $str = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER;
            if ($obtained->extendedmatch !== null) {
                $str = $obtained->string_extension();
            }
            $pattern = $expected['next'];
            $char = qtype_poasquestion_string::substr($str, 0, 1);
            $nextpassed = (($expected['next'] === $str && $str === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) ||
                           ($expected['next'] !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->check_next_character($pattern, $char)));
        }

        // Checking number of characters left.
        $leftpassed = true;
        if ($matcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
            $leftpassed = in_array($obtained->left, $expected['left']);
        }
        if ($this->doextrachecks) {
            $this->do_extra_check($regex, $modifiers, $obtained);
        }
        return $ismatchpassed && $fullpassed && $indexfirstpassed && $lengthpassed && $nextpassed && $leftpassed;
    }

    /**
     * Does assertions for every field. if assertionstrue === true then error messages displayed only.
     */
    function do_assertions($enginename, $regex, $str, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $lengthpassed, $nextpassed, $leftpassed, $testdataclassname, $assertionstrue = false) {

        // is_match
        $this->assertTrue($assertionstrue || $ismatchpassed);
        if (!$ismatchpassed) {
            echo "$enginename failed 'IS_MATCH' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected is_match: ' . $this->boolstr[$expected['is_match']] . $this->eol .
                 'obtained is_match: ' . $this->boolstr[$obtained->is_match()] . $this->eol .
                 'source class:      ' . $testdataclassname . $this->eol . $this->eol;
        }

        // full
        $this->assertTrue($assertionstrue || $fullpassed);
        if (!$fullpassed) {
            echo "$enginename failed 'FULL' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected full: ' . $this->boolstr[$expected['full']]     . $this->eol .
                 'obtained full: ' . $this->boolstr[$obtained->full]       . $this->eol .
                 'source class:  ' . $testdataclassname . $this->eol . $this->eol;
        }

        // index_first
        $this->assertTrue($assertionstrue || $indexfirstpassed);
        if (!$indexfirstpassed) {
            echo "$enginename failed 'INDEX_FIRST' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected index_first: '; print_r($expected['index_first']); echo $this->eol .
                 'obtained index_first: '; print_r($obtained->index_first);   echo $this->eol .
                 'source class:         ' . $testdataclassname . $this->eol      . $this->eol;
        }

        // length
        $this->assertTrue($assertionstrue || $lengthpassed);
        if (!$lengthpassed) {
            echo "$enginename failed 'LENGTH' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected length: '; print_r($expected['length']); echo $this->eol .
                 'obtained length: '; print_r($obtained->length);   echo $this->eol .
                 'source class:    ' . $testdataclassname . $this->eol . $this->eol;
        }

        // next
        $this->assertTrue($assertionstrue || $nextpassed);
        if (!$nextpassed) {
            echo "$enginename failed 'NEXT' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected next: ' . $expected['next']               . $this->eol .
                 'obtained next: ' . $obtained->string_extension()   . $this->eol .
                 'source class:  ' . $testdataclassname . $this->eol . $this->eol;
        }

        // left
        $this->assertTrue($assertionstrue || $leftpassed, "$enginename failed 'left' check on regex '$regex' and string '$str'    (test from $testdataclassname)");
        if (!$leftpassed) {
            echo "$enginename failed 'LEFT' check on regex '$regex' and string '$str'" . $this->eol .
                 'expected left: ' . $expected['left'][0]            . $this->eol .
                 'obtained left: ' . $obtained->left                 . $this->eol .
                 'source class:  ' . $testdataclassname . $this->eol . $this->eol;
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
                    $exception = false;
                    try {
                        $matcher = new $enginename($regex, $modifiers);
                    } catch (Exception $e) {
                        $exception = true;
                        echo "EXCEPTION CATCHED WHILE BUILDING MATCHER, test name is " . $curtestmethod . $this->eol;
                    }
                    if (!$exception && !$this->check_for_errors($matcher)) {
                        try {
                            // Iterate over all tests.
                            foreach ($data['tests'] as $expected) {
                                $str = $expected['str'];
                                $matcher->match($str);
                                $obtained = $matcher->get_match_results();
                                // Now the results are obtained, let us check them!
                                $ismatchpassed = false;
                                $fullpassed = false;
                                $indexfirstpassed = false;
                                $lengthpassed = false;
                                $nextpassed = false;
                                $leftpassed = false;
                                $this->compare_results($regex, $modifiers, $matcher, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $lengthpassed, $nextpassed, $leftpassed);
                                $this->do_assertions($this->engine_name(), $regex, $str, $expected, $obtained, $ismatchpassed, $fullpassed, $indexfirstpassed, $lengthpassed, $nextpassed, $leftpassed, $testdataclassname, true);
                            }
                        } catch (Exception $e) {
                            $exception = true;
                            echo "EXCEPTION CATCHED WHILE CHECKING RESULTS, test name is " . $curtestmethod .  $this->eol . $e->getMessage() . $this->eol;
                        }
                    }
                }
            }
        }
    }
}
