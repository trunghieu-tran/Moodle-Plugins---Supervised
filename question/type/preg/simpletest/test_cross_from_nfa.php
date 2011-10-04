<?php

/**
* Data-driven cross-testing of matchers.
*
*     A test function should:
*     -be named "test_match_..."
*     -return an array of input and output data as in the following example:
*       array(
*             'regex'=>'^[-.\w]+[a-z]{2,6}$',    // a regular expression
*             'modifiers'=>'i',                  // modifiers. it's not necessary to define this element
*             'tests'=>array($test1,...,$testn)  // array containing tests in the format described below. count of these tests is unlimited
*             );
*
*    An array of expected results ($test-i) should look like:
*       array(
*             'str'=>'sample string',            // a string to match
*             'is_match'=>true,                  // is there a match?
*             'full'=>true,                      // is it full?
*             'index_first'=>array(0=>0),        // indexes of first correct characters for subpatterns. subpattern numbers are defined by array keys
*             'index_last'=>array(0=>2),         // indexes of last correct characters for subpatterns.
*             'left'=>array(0),                  // number of characters left to complete match. different engines can return different results, that's why it is an array
*             'next'=>'');                       // a string of possible next characters in case of not full match
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');
require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');

class test_cross_from_nfa extends UnitTestCase {

    var $question;            // an object of question_preg_qtype
    var $engines = array();   // an array of available engines

    public function __construct() {
        $question = new question_preg_qtype();
        $en = $question->available_engines();
        foreach ($en as $key=>$value) {
            if ($key != 'preg_php_matcher') {
                $this->engines[] = $key;
            }
        }
    }

    function check_for_errors($matcher) {
        if ($matcher->is_error_exists()) {
            $errors = $matcher->get_errors();
            foreach ($errors as $error) {
                echo "$error<br />";
            }
            return true;
        }
        return false;
    }

    function test() {
        $testmethods = get_class_methods($this);
        foreach ($testmethods as $curtestmethod) {
            // filtering class methods by names. A test method name should start with 'test_match_'
            $pos = strstr($curtestmethod, 'test_match_');
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
                            $matcher->match($expected['str']);
                            $obtained = $matcher->get_match_results();
                            $passed = $this->assertTrue($expected['is_match'] == $obtained['is_match']);
                            $passed = $passed && $this->assertTrue($expected['full'] == $obtained['full']);
                            if ($obtained['is_match'] && $expected['is_match']) {
                                if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                                    $passed = $passed && $this->assertTrue($expected['index_first'] == $obtained['index_first']);
                                    $passed = $passed && $this->assertTrue($expected['index_last'] == $obtained['index_last']);
                                } else {
                                    $passed = $passed && $this->assertTrue($expected['index_first'][0] == $obtained['index_first'][0]);
                                    $passed = $passed && $this->assertTrue($expected['index_last'][0] == $obtained['index_last'][0]);
                                }
                                if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                                    $passed = $passed && $this->assertTrue(($expected['next'] === '' && $obtained['next'] === '') || strstr($expected['next'], $obtained['next']) != false);        // expected 'next' contains obtained 'next'
                                }
                                if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                                    $passed = $passed && $this->assertTrue(in_array($obtained['left'], $expected['left']));
                                }
                            }
                            if (!$passed) {
                                $msg = $matcher->name() . " works seriously wrong<br />";
                                echo $msg;
                            }
                        }
                    }
                }
            }
        }
    }

    function test_match_concat() {
        $test1 = array( 'str'=>'the matcher works',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>16),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'_the matcher works',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>-1),
                        'index_last'=>array(0=>-1),
                        'left'=>array(17),
                        'next'=>'t');

        $test3 = array( 'str'=>'the matcher',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>10),
                        'left'=>array(6),
                        'next'=>' ');

        return array('regex'=>'^the matcher works',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_alt() {
        $test1 = array( 'str'=>'abcf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'t');

        $test3 = array( 'str'=>'deff',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^abc|def$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_assertions_simple_1() {
        $test1 = array( 'str'=>' abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>' 9bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'  b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(2),
                        'next'=>'abcdefghijklmnopqrstuvwxyz');

		return array('regex'=>'^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_assertions_simple_2() {
        $test1 = array( 'str'=>'abc?z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abcaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'');    // can't generate a character

	    return array('regex'=>'^abc[a-z.?!]\b[a-zA-Z]',
                     'tests'=>array($test1, $test2));
    }

    function test_match_zero_length_loop() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^*[a-z 0-9](?:\b)+a${1,}',
                     'tests'=>array($test1));
    }

    function test_match_subpatterns_nested() {
        $test1 = array( 'str'=>'abcbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1,3=>2),
                        'index_last'=>array(0=>5,1=>4,2=>2,3=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a((b(c))*)d$',
                     'tests'=>array($test1));
    }

    function test_match_subpatterns_concatenated() {
        $test1 = array( 'str'=>'_abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>3,3=>5),
                        'index_last'=>array(0=>6,1=>2,2=>4,3=>6),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(ab)(cd)(ef)',
                     'tests'=>array($test1));
    }

    function test_match_subpatterns_alternated() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>1,1=>1,2=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'((ab)|(cd)|(efgh))',
                     'tests'=>array($test1));
    }

    function test_match_questquant() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_negative_charset() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'acdefghijklmnopqrstuvwxyz0123456789!?.,');

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aacde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_many_alternatives() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'aceg');

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function test_match_repeated_chars() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(1,3),
                        'next'=>'ab');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'index_last'=>array(0=>78),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_brace_finite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>5),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>26),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>35),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_brace_infinite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>5),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>26),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>103),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab{15,}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_plus() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(2),
                        'next'=>'b');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>100),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function test_match_cs() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'B');

        return array('regex'=>'aBcD',
                     'tests'=>array($test1));
    }

    function test_match_cins() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'aBcD',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function test_match_backref_simple() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>11,1=>5,2=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>5,1=>5,2=>2),
                        'left'=>array(6),
                        'next'=>'a');

        return array('regex'=>'((abc)\2)\1',
                     'tests'=>array($test1/*, $test2*/));
    }
}

?>