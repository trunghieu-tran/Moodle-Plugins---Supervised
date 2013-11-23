<?php

/**
 * Data-driven cross-tester of matchers. Interit this class and
 * implement the engine_name() function for testing a concrete matcher.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*******************************************************************************************************************************************
*
*  The cross-tester searches for files named "cross_tests_<suffix>.php". The search isn't recursive, so put your tests
*  in the "tests" folder. A class with test data should be named the same as the corresponding file is named.
*  For example, a file named "cross_tests_example.php" should contain a class named "cross_tests_example".
*
*  Those classes represent test data as a set of test functions. A test function should:
*    -be named "data_for_test_..."
*    -return an array of input and output data as in the following example:
*
*    array(
*          'regex'=>'.*(.*)',                                     // The regular expression to test the matcher on.
*          'tests'=>array($test1, ... , $testn),                  // Array containing tests in the format described below.
*          'modifiers'=>'i',                                      // (Optional) modifiers, default value is null.
*          'tags'=>array($tag1, ..., $tagn),                      // (Optional) tags for the regex, default value is array().
*          'notation'=>qtype_preg_cross_tester::NOTATION_NATIVE)  // (Optional) regex notation, default value is 'native'.
*          );
*
*  An array of expected results ($testi) should look like:
*
*    array(
*          'str'=>'aaa',                        // A string to match.
*          'is_match'=>true,                    // Is there a match?
*          'full'=>false,                       // Is the match full?
*          'index_first'=>array(0=>0,1=>3),     // Start indexes of subexpressions; not necessary to define unmatched subexpressions.
*          'length'=>array(0=>3,1=>0),          // Lengths of subexpressions; not necessary to define unmatched subexpressions.
*          'ext_index_first'=>array(0=>0,1=>3), // (Optional) the same indexes for generated extension.
*          'ext_length'=>array(0=>4,1=>0),      // (Optional) the same lengths for generated extension.
*          'left'=>0,                           // (Defined for partial matches) number of characters left to complete the partial match.
*          'next'=>'',                          // (Defined for partial matches) a regex matching possible next character.
*          'tags'=>array());                    // (Optional) tags for the string, default value is array().
*
*  Here's an example test function:
*
*  function data_for_test_att_nullsubexpr_2() {
*          $test1 = array('str'=>'aaaaaa',
*                         'is_match'=>true,
*                         'full'=>true,
*                         'index_first'=>array(0=>0,1=>0),
*                         'length'=>array(0=>6,1=>6));
*
*          return array('regex'=>'(a*)*',
*                       'tests'=>array($test1),
*                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT));
*  }
*
*******************************************************************************************************************************************/

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

abstract class qtype_preg_cross_tester extends PHPUnit_Framework_TestCase {

    // Different sources of test data.
    const TAG_FROM_NFA           = 1;
    const TAG_FROM_DFA           = 2;
    const TAG_FROM_BACKTRACKING  = 4;
    const TAG_FROM_PCRE          = 8;
    const TAG_FROM_ATT           = 16;

    const TAG_CATEGORIZE         = 32;   // The test determines the matcher's associativity.
    const TAG_ASSOC_LEFT         = 64;   // The test should be used for left-associative matchers.
    const TAG_ASSOC_RIGHT        = 128;  // The test should be used for right-associative matchers.

    const TAG_MODE_PCRE          = 256;  // PCRE compatibility mode which is default.
    const TAG_MODE_POSIX         = 512;  // POSIX compatibility mode.

    const TAG_DONT_CHECK_PARTIAL = 1024; // Indicates that if there's no full match, the cross-tester skips partial match checking.
    const TAG_DEBUG_MODE         = 2048; // Informs matchers that it's debug mode.

    // Different notations.
    const NOTATION_NATIVE             = 'native';
    const NOTATION_MDLSHORTANSWER     = 'mdlshortanswer';
    const NOTATION_PCRESTRICT         = 'pcrestrict';

    // TODO: tags for different capabilities for matchers.

    protected $passcount;              // Number of passes.
    protected $failcount;              // Number of fails.
    protected $skipcount;              // Number of skipped tests.
    protected $exceptionscount;        // Number of exceptions during testing.
    protected $testdataobjects;        // Objects with test data.
    protected $doextrachecks;          // Is it needed to do extra checks.
    protected $question;               // Question object for getting matchers.

    protected $blacklist;              // Blacklist of tags in different modes.

    /**
     * Returns name of the engine to be tested (without qtype_preg_ prefix!). Should be implemented in child classes.
     */
    abstract protected function engine_name();

    /**
     * Returns engine-specific tags, tests with wich will be skipped.
     */
    protected function blacklist_tags() {
        return array();
    }

    /**
     * Determines the matcher's associativity.
     */
    function categorize_assoc($enginename) {
        $test1 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1),
                       'tags'=>array(qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
        $test2 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>2),
                       'length'=>array(0=>2,1=>0,2=>2,3=>0),
                       'tags'=>array(qtype_preg_cross_tester::TAG_ASSOC_LEFT));

        $regex = '(a*)(ab)*(b*)';

        // Create a matcher in POSIX mode for categorization tests.
        $options = new qtype_preg_matching_options();
        $options->mode = qtype_preg_handling_options::MODE_POSIX;
        $matcher = $this->question->get_matcher($enginename, $regex);
        $matcher->set_options($options);

        // Match the first test.
        $matcher->match($test1['str']);
        $obtained1 = $matcher->get_match_results();
        $right = $this->compare_results($regex, self::NOTATION_NATIVE, $test1['str'], 0, '', $matcher, $test1, $obtained1, 'categorize', 'associativity', false, false);

        // Match the second test.
        $matcher->match($test2['str']);
        $obtained2 = $matcher->get_match_results();
        $left = $this->compare_results($regex, self::NOTATION_NATIVE, $test2['str'], 0, '', $matcher, $test2, $obtained2, 'categorize', 'associativity', false, false);

        if ($left && !$right) {
            return self::TAG_ASSOC_LEFT;
        } else if (!$left && $right) {
            return self::TAG_ASSOC_RIGHT;
        }
        return false;
    }

    public function __construct() {
        $this->passcount = 0;
        $this->failcount = 0;
        $this->skipcount = 0;
        $this->exceptionscount = 0;
        $this->testdataobjects = array();
        $this->doextrachecks = false;       // TODO: control this field from outside.
        $this->question = new qtype_preg_question();

        $testdir = dirname(__FILE__) . '/';
        $pregdir = dirname($testdir) . '/';

        // Find all available test files.
        $dh = opendir($testdir);
        if (!$dh) {
            return;
        }

        $enginename = $this->engine_name();

        // Include the file with class of the matcher to test.
        require_once($pregdir . $enginename . '/' . $enginename . '.php');

        // Include files with test data.
        while ($file = readdir($dh)) {
            if (strpos($file, 'cross_tests_') !== 0 || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }
            // Test data file found.
            require_once($testdir . $file);
            $classname = 'qtype_preg_' . pathinfo($file, PATHINFO_FILENAME);
            $this->testdataobjects[] = new $classname;
        }
        closedir($dh);

        // Depending on associativity, blacklist some tests.
        $assoc = $this->categorize_assoc($enginename);
        if ($assoc === self::TAG_ASSOC_LEFT) {
            echo "\n$enginename has LEFT ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_RIGHT);
        } else if ($assoc === self::TAG_ASSOC_RIGHT) {
            echo "\n$enginename has RIGHT ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_LEFT);
        } else {
            echo "\n$enginename has UNDEFINED ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_LEFT, self::TAG_ASSOC_RIGHT);
        }
    }

    /**
     * Checks matcher for parsing and accepting errors.
     */
    function check_for_errors($matcher) {
        if ($matcher->errors_exist()) {
            $errors = $matcher->get_errors();
            foreach ($errors as $error) {
                // Error messages are displayed for parsing errors only.
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    echo 'Regex incorrect: ' . $error->errormsg . "\n";
                }
            }
            return true;
        }
        return false;
    }

    function dump_boolean($label, $value) {
        return $label . ($value ? 'TRUE' : 'FALSE') . "\n";
    }

    function dump_scalar($label, $value) {
        return $label . $value . "\n";
    }

    function dump_scalar_array($label, $values) {
        $result = $label;
        foreach ($values as $key => $value) {
            $result .= $key . '=>' . $value . ', ';
        }
        return $result . "\n";
    }

    function check_next_character($regex, $char) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $token = $lexer->nextToken();
        $leaf = is_array($token) ? $token[0]->value : $token->value;
        $res = $leaf->match(new qtype_poasquestion_string($char), 0, $length, false);
        fclose($pseudofile);
        return $res;
    }

    /**
     * Performs some extra checks on results which contain generated ending of a partial match.
     * @param $regex - regular expression.
     * @param $modifiers - modifiers.
     * @param $obtained - a result to check.
     * @return true if everything is correct, false otherwise.
     */
    function do_extra_check($regex, $notation, $modifiers, $obtained) {
        if ($obtained->extendedmatch === null || !$obtained->extendedmatch->full) {
            return;
        }

        $str = $obtained->matched_part() . $obtained->string_extension();
        $boolstr = array(false => 'FALSE', true => 'TRUE');
        $result = true;
        $matcher = $this->question->get_matcher('php_preg_matcher', $regex, false, $modifiers, null, $notation);

        $matcher->match($str);
        $newresults = $matcher->get_match_results();

        // Length + left should remain the same.
        $sum1 = $obtained->length() + $obtained->left;
        $sum2 = $obtained->extendedmatch->length() + $obtained->extendedmatch->left;
        if ($obtained->length() === qtype_preg_matching_results::NO_MATCH_FOUND) {
            $sum1++;
        }

        if ($newresults->full != $obtained->extendedmatch->full) {
            $result = false;
            echo "extended match field 'full' has the value " . $boolstr[$obtained->extendedmatch->full] . " which is incorrect\n";
        }

        return $result;
    }

    /**
     * Compares obtained results with expected and writes all flags.
     */
    function compare_results($regex, $notation, $str, $mods, $modstr, $matcher, $expected, $obtained, $classname, $methodname, $skippartialcheck, $dumpfails) {
        // Do some initialization.
        $fullpassed = ($expected['full'] === $obtained->full);
        $ismatchpassed = true;
        $indexfirstpassed = true;
        $lengthpassed = true;
        $extindexfirstpassed = true;
        $extlengthpassed = true;
        $nextpassed = true;
        $leftpassed = true;

        if (!$skippartialcheck) {
            // Check match existance.
            if ($matcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
                $ismatchpassed = ($expected['is_match'] === $obtained->is_match());
            } else {
                $ismatchpassed = $fullpassed;
            }

            // Check indexes and lengths.
            $subexprsupported = $matcher->is_supporting(qtype_preg_matcher::SUBEXPRESSION_CAPTURING);
            foreach ($obtained->indexfirst as $key => $index) {
                if (!$subexprsupported && $key != 0) {
                    continue;
                }
                $indexfirstpassed = $indexfirstpassed && ((!array_key_exists($key, $expected['index_first']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                          (array_key_exists($key, $expected['index_first']) && $expected['index_first'][$key] === $obtained->indexfirst[$key]));
            }
            foreach ($obtained->length as $key => $index) {
                if (!$subexprsupported && $key != 0) {
                    continue;
                }
                $lengthpassed = $lengthpassed && ((!array_key_exists($key, $expected['length']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                  (array_key_exists($key, $expected['length']) && $expected['length'][$key] === $obtained->length[$key]));
            }

            if ($obtained->extendedmatch !== null && array_key_exists('ext_index_first', $expected)) {
                foreach ($obtained->extendedmatch->indexfirst as $key => $index) {
                    if (!$subexprsupported && $key != 0) {
                        continue;
                    }
                    $extindexfirstpassed = $extindexfirstpassed && ((!array_key_exists($key, $expected['ext_index_first']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                              (array_key_exists($key, $expected['ext_index_first']) && $expected['ext_index_first'][$key] === $obtained->extendedmatch->indexfirst[$key]));
                }
                foreach ($obtained->extendedmatch->length as $key => $index) {
                    if (!$subexprsupported && $key != 0) {
                        continue;
                    }
                    $extlengthpassed = $extlengthpassed && ((!array_key_exists($key, $expected['ext_length']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                      (array_key_exists($key, $expected['ext_length']) && $expected['ext_length'][$key] === $obtained->extendedmatch->length[$key]));
                }
            }

            // Check the next possible character.
            $obtainednext = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER;
            if (!$expected['full'] && $matcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
                if ($obtained->extendedmatch !== null) {
                    $obtainednext = $obtained->string_extension();
                }
                $pattern = $expected['next'];
                $char = qtype_poasquestion_string::substr($obtainednext, 0, 1);
                $nextpassed = (($expected['next'] === $obtainednext && $obtainednext === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) ||
                               ($expected['next'] !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->check_next_character($pattern, $char)));
            }

            // Check the number of characters left.
            if (!$expected['full'] && $matcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
                $leftpassed = in_array($obtained->left, $expected['left']);
            }
            if ($this->doextrachecks) {
                $this->do_extra_check($regex, $notation, $mods, $obtained);
            }
        }

        $passed = $ismatchpassed && $fullpassed && $indexfirstpassed && $lengthpassed && $extindexfirstpassed && $extlengthpassed && $nextpassed && $leftpassed;

        if (!$passed && $dumpfails) {
            $obtainedstr = '';
            $expectedstr = '';

            // is_match
            if (!$ismatchpassed) {
                $obtainedstr .= $this->dump_boolean('IS_MATCH:        ', $obtained->is_match());
                $expectedstr .= $this->dump_boolean('IS_MATCH:        ', $expected['is_match']);
            }

            // full
            if (!$fullpassed) {
                $obtainedstr .= $this->dump_boolean('FULL:            ', $obtained->full);
                $expectedstr .= $this->dump_boolean('FULL:            ', $expected['full']);
            }

            // index_first
            if (!$indexfirstpassed) {
                $obtainedstr .= $this->dump_scalar_array('INDEX_FIRST:     ', $obtained->indexfirst);
                $expectedstr .= $this->dump_scalar_array('INDEX_FIRST:     ', $expected['index_first']);
            }

            // length
            if (!$lengthpassed) {
                $obtainedstr .= $this->dump_scalar_array('LENGTH:          ', $obtained->length);
                $expectedstr .= $this->dump_scalar_array('LENGTH:          ', $expected['length']);
            }

            // ext_index_first
            if (!$extindexfirstpassed) {
                $obtainedstr .= $this->dump_scalar_array('EXT_INDEX_FIRST: ', $obtained->extendedmatch->indexfirst);
                $expectedstr .= $this->dump_scalar_array('EXT_INDEX_FIRST: ', $expected['ext_index_first']);
            }

            // ext_length
            if (!$extlengthpassed) {
                $obtainedstr .= $this->dump_scalar_array('EXT_LENGTH:      ', $obtained->extendedmatch->length);
                $expectedstr .= $this->dump_scalar_array('EXT_LENGTH:      ', $expected['ext_length']);
            }

            // next
            if (!$nextpassed) {
                $obtainedstr .= $this->dump_scalar('NEXT:            ', $obtainednext);
                $expectedstr .= $this->dump_scalar('NEXT:            ', $expected['next']);
            }

            // left
            if (!$leftpassed) {
                $obtainedstr .= $this->dump_scalar('LEFT:            ', $obtained->left);
                $expectedstr .= $this->dump_scalar('LEFT:            ', $expected['left'][0]);
            }

            $enginename = $matcher->name();
            echo $mods == '' ?
                 "$enginename failed on regex '$regex' and string '$str' ($classname, $methodname):\n" :
                 "$enginename failed on regex '$regex' string '$str' and modifiers '$modstr' ($classname, $methodname):\n";
            echo $obtainedstr;
            echo "expected:\n";
            echo $expectedstr;
            echo "\n";
        }

        // Return true if everything is correct, false otherwise.
        return $passed;
    }

    /**
     * The main function - runs all matchers on test-data sets.
     */
    function test() {
        $options = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $enginename = $this->engine_name();
        $blacklist = array_merge($this->blacklist_tags(), $this->blacklist);

        foreach ($this->testdataobjects as $testdataobj) {
            $testmethods = get_class_methods($testdataobj);
            $classname = get_class($testdataobj);
            foreach ($testmethods as $methodname) {
                // Filtering class methods by names. A test method name should start with 'data_for_test_'.
                if (strpos($methodname, 'data_for_test_') !== 0) {
                    continue;
                }

                // Get current test data.
                $data = $testdataobj->$methodname();
                $regex = $data['regex'];
                $modifiersstr = '';
                $modifiers = 0;
                $regextags = array();
                $notation = self::NOTATION_NATIVE;
                if (array_key_exists('modifiers', $data)) {
                    $modifiersstr = $data['modifiers'];
                    $modifiers = qtype_preg_handling_options::string_to_modifiers($modifiersstr);
                }
                if (array_key_exists('tags', $data)) {
                    $regextags = $data['tags'];
                }
                if (array_key_exists('notation', $data)) {
                    $notation = $data['notation'];
                }

                // Skip regexes with blacklisted tags.
                if (count(array_intersect($blacklist, $regextags)) > 0) {
                    continue;
                }

                // Try to get matcher for the regex.

                    $options->mode = in_array(self::TAG_MODE_POSIX, $regextags) ? qtype_preg_handling_options::MODE_POSIX : qtype_preg_handling_options::MODE_PCRE;
                    $options->debugmode = in_array(self::TAG_DEBUG_MODE, $regextags);
                    $matcher = $this->question->get_matcher($enginename, $regex, false, $modifiers, null, $notation);
                    $matcher->set_options($options);


                // Skip to the next regex if there's something wrong.
                if ($this->check_for_errors($matcher)) {
                    $this->skipcount += count($data['tests']);
                    continue;
                }

                // Iterate over all tests.
                foreach ($data['tests'] as $expected) {
                    $str = $expected['str'];
                    $strtags = array();
                    if (array_key_exists('tags', $expected)) {
                        $strtags = $expected['tags'];
                    }

                    $tags = array_merge($regextags, $strtags);

                    // Skip tests with blacklisted tags.
                    if (count(array_intersect($blacklist, $tags)) > 0) {
                        continue;
                    }

                    // There can be exceptions during matching.
                    try {
                        $matcher->match($str);
                        $obtained = $matcher->get_match_results();
                    } catch (Exception $e) {
                        echo "EXCEPTION CATCHED DURING MATCHING, test name is " . $methodname .  "\n" . $e->getMessage() . "\n";
                        $this->exceptionscount++;
                        continue;
                    }

                    // Results obtained, check them.
                    $skippartialcheck = in_array(self::TAG_DONT_CHECK_PARTIAL, $tags);
                    if ($this->compare_results($regex, $notation, $str, $modifiers, $modifiersstr, $matcher, $expected, $obtained, $classname, $methodname, $skippartialcheck, true)) {
                        $this->passcount++;
                    } else {
                        $this->failcount++;
                    }
                }
            }
        }
        if ($this->failcount == 0 && $this->exceptionscount == 0 && $this->passcount > 0) {
            echo "\n\nWow! All tests passed!\n\n";
        }
        echo "======================\n";
        echo 'PASSED:     ' . $this->passcount . "\n";
        echo 'FAILED:     ' . $this->failcount . "\n";
        echo 'SKIPPED:    ' . $this->skipcount . "\n";
        echo 'EXCEPTIONS: ' . $this->exceptionscount . "\n";
        echo "======================";
    }
}
