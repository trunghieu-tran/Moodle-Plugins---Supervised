<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg_intersection1 {

    function data_for_test_66() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'the abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=^)abc',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_144() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'1.235',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1,2=>4),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(\.\d\d((?=0)|\d(?=\d)))',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_246() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'aBCd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'abcD     ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'a(?=b(?i)c)\w\wd',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_250() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'Ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'abC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=a(?i)b)\w\wc',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    /*function data_for_test_254() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(?(?=abc)\w{3}:|\d\d)$',
                     'tests'=>array($test3, $test4, $test5));
    }*/

    /*function data_for_test_276() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(8),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'sep-12-98',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>5),
                        'left'=>array(3),
                        'next'=>'-',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=[^a-z]+[a-z])  \d{2}-[a-z]{3}-\d{2}  |  \d{2}-\d{2}-\d{2} ) ',
                     'modifiers'=>'x',
                     'tests'=>array($test3, $test4));
    }*/

    /*function data_for_test_557() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=a)b|a)',
                     'tests'=>array($test1, $test2));
    }*/

    /*function data_for_test_562() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(?=(a+?))\1ab',
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_670() {
        $test2 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=^.*b)b|^)',
                     'tests'=>array($test2));
    }*/

    //!!!
    function data_for_test_709() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=abc){1}xyz',
                     'tests'=>array($test1, $test2));
    }

    // !!!
    function data_for_test_577() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'a rather long string that doesn\'t end with one of them',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?>.*)(?<=(abcd|wxyz))',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_630() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'offX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^f])X',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_720() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'xabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_723() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_724() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'aAAbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_787() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'caz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(a)(?<=b(?1))',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_788() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=b(?1))(a)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_226() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'foobbar',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>3),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(foo)a)bar',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_251() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'Abxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'ABxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'abxxC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a(?i)b)(\w\w)c',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    /*function data_for_test_256() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'foocat',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?<=foo)bar|cat)',
                     'tests'=>array($test5, $test6));
    }*/

    function data_for_test_277() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'barfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6,1=>3),
                        'length'=>array(0=>0,1=>3),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(foo))bar\1',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_282() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>"baz\nbar",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=foo\n)^bar',
                     'modifiers'=>'m',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_507() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'cb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a)b',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_28() {
        $test1 = array('str'=>"abde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>0,3=>3),
                       'length'=>array(0=>4,1=>2,2=>3,3=>1));

        return array('regex'=>"^(?=ab(de))(abd)(e)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_30() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>0),
                       'length'=>array(0=>2,1=>4,2=>2,3=>2));

        return array('regex'=>"^(?=(ab(cd)))(ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_54() {
        $test1 = array('str'=>"the quick brown   fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>10),
                       'length'=>array(0=>5));

        return array('regex'=>"\\w+(?=\\t)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_64() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"the abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=^)abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_146() {
        $test1 = array('str'=>"1.230003938",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>4),
                       'length'=>array(0=>4, 1=>4, 2=>1));

        $test2 = array('str'=>"1.875000282",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>4),
                       'length'=>array(0=>4,1=>4,2=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"1.235",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(\\.\\d\\d((?=0)|\\d(?=\\d)))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_253() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"abCd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aBCd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abcD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(?=b(?i)c)\\w\\wd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_257() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"aBc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"Ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"aBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(?i)b)\\w\\wc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    /*function data_for_test_261() {
        $test1 = array('str'=>"abc:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"12",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?(?=abc)\\w{3}:|\\d\\d)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }*/

    /*function data_for_test_283() {
        $test1 = array('str'=>"12-sep-98",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        $test2 = array('str'=>"12-09-98",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"sep-12-98",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?(?=[^a-z]+[a-z])  \\d{2}-[a-z]{3}-\\d{2}  |  \\d{2}-\\d{2}-\\d{2} ) ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }*/

    function data_for_test_495() {
        $test1 = array('str'=>"abad",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?=d).",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_496() {
        $test1 = array('str'=>"abad",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?=c|d).",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_522() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"^(?:b|a(?=(.)))*\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_586() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?(?=a)b|a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }*/

    /*function data_for_test_587() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?=a)a|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_588() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>1),
                       'length'=>array(0=>3,1=>1,2=>3));

        return array('regex'=>"(?=(a+?))(\\1ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_591() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>1),
                       'length'=>array(0=>3,1=>1,2=>3));

        return array('regex'=>"(?=(a+?))(\\1ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_592() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?=(a+?))\\1ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }*/

    /*function data_for_test_682() {
        $test1 = array('str'=>"abcd:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>4));

        return array('regex'=>"(?=(\\w+))\\1:",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_683() {
        $test1 = array('str'=>"abcd:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>4));

        return array('regex'=>"^(?=(\\w+))\\1:",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_710() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?=.*b)b|^)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }*/

    /*function data_for_test_711() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?(?=^.*b)b|^)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_712() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(?(?=.*b)b|^)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_713() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?=.*b)b|^)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_714() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?=b).*b|^d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_715() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(?=.*b).*b|^d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_716() {
        $test1 = array('str'=>"%ab%",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>0));

        return array('regex'=>"^%((?(?=[a])[^%])|b)*%\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_735() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"(?=(a))ab|(a)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_739() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0,3=>0),
                       'length'=>array(0=>2,2=>1,3=>2));

        return array('regex'=>"(?=(?>(a))b|(a)c)(..)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_747() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?(?=(a))a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_748() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(?(?=(a))a)(b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_752() {
        $test1 = array('str'=>"abcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=abc){3}abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_753() {
        $test1 = array('str'=>"abcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=abc)+abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    /*function data_for_test_754() {
        $test1 = array('str'=>"abcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=abc)++abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }*/

    function data_for_test_755() {
        $test1 = array('str'=>"xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?=abc){0}xyz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_757() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?=(a))?.",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_758() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0, 1=>0),
                       'length'=>array(0=>1, 1=>1));

        $test2 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?=(a))??.",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_759() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"zcdxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"^(?=(?1))?[az]([abc])d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    /*function data_for_test_775() {
        $test1 = array('str'=>"XcccddYX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        return array('regex'=>"(?(?=c)c|d)++Y",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_776() {
        $test1 = array('str'=>"XcccddYX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        return array('regex'=>"(?(?=c)c|d)*+Y",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_794() {
        $test1 = array('str'=>"ABCDECBA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>0));

        return array('regex'=>"(?=C)",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_849() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?=a(*SKIP)b|ac)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }*/

    /*function data_for_test_850() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?=a(*PRUNE)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_851() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^(?=a(*ACCEPT)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_905() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?(?=(a(*ACCEPT)z))a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_908() {
        $test1 = array('str'=>"aZbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?=a(*:M))aZ",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_941() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?(?=a(*THEN)b)ab|ac)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_942() {
        $test1 = array('str'=>"ba",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*?(?(?=a)a|b(*THEN)c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_943() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^.*?(?:(?(?=a)a|b(*THEN)c)|d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_944() {
        $test1 = array('str'=>"ac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*?(?(?=a)a(*THEN)b|c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_945() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^.*(?=a(*THEN)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*
    function data_for_test_977() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*MARK:A)b)..x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_978() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*MARK:A)b)..(*:Y)x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_979() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*PRUNE:A)b)..x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_980() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*PRUNE:A)b)..(*:Y)x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_981() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*THEN:A)b)..x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_982() {
        $test1 = array('str'=>"abxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abpq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*THEN:A)b)..(*:Y)x",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }*/

    /*function data_for_test_1033() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?=a(*COMMIT)b)abc|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }*/

    /*function data_for_test_1035() {
        $test1 = array('str'=>"abd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?=b(*COMMIT)c)[^d]|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }*/

    function data_for_test_1036() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?=bc).|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    /*function data_for_test_1043() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?=a\\Kb)ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    /*function data_for_test_1091() {
        $test1 = array('str'=>"ca",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(?(?=ab)ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }*/

    function data_for_test_232() {
        $test1 = array('str'=>"fooabar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"bar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"foobbar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(foo)a)bar",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_258() {
        $test1 = array('str'=>"abxxc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        $test2 = array('str'=>"aBxxc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"Abxxc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ABxxc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"abxxC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=a(?i)b)(\\w\\w)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    /*function data_for_test_263() {
        $test1 = array('str'=>"foobar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"cat",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"fcat",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"focat",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"foocat",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?(?<=foo)bar|cat)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }*/

    function data_for_test_284() {
        $test1 = array('str'=>"foobarfoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"foobarfootling",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"foobar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"barfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(foo))bar\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_289() {
        $test1 = array('str'=>"foo\nbar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"bar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"baz\nbar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=foo\\n)^bar",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_518() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"cb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_590() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
                       'length'=>array(0=>0,1=>1));

        return array('regex'=>"\$(?<=^(a))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_663() {
        $test1 = array('str'=>"onyX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"offX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[^f])X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_665() {
        $test1 = array('str'=>"A\nC\nC\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>0));

        return array('regex'=>"(?<=C\\n)^",
                     'modifiers'=>"mg",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_761() {
        $test1 = array('str'=>"abcxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"pqrxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        return array('regex'=>"(?<=(abc))?xyz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_767() {
        $test1 = array('str'=>"xaabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=a{2})b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_769() {
        $test1 = array('str'=>"xa c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\h)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_770() {
        $test1 = array('str'=>"axxbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aAAbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xaabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[^a]{2})b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_771() {
        $test1 = array('str'=>"axxbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aAAbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xaabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[^a]{2})b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_772() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\H)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_773() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\V)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_774() {
        $test1 = array('str'=>"a\nc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\v)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_837() {
        $test1 = array('str'=>"baz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"caz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a)(?<=b(?1))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_838() {
        $test1 = array('str'=>"zbaaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>3),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=b(?1))(a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_839() {
        $test1 = array('str'=>"baz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?<X>a)(?<=b(?&X))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*unction data_for_test_948() {
        $test1 = array('str'=>"xacd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a(*ACCEPT)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_949() {
        $test1 = array('str'=>"xacd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>1),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?<=(a(*ACCEPT)b))c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_950() {
        $test1 = array('str'=>"xabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>1),
                       'length'=>array(0=>1,1=>2));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xacd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(a(*COMMIT)b))c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }*/

    /*function data_for_test_952() {
        $test1 = array('str'=>"xabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a(*:N)b)c",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_953() {
        $test1 = array('str'=>"xabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a(*PRUNE)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_954() {
        $test1 = array('str'=>"xabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a(*SKIP)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_955() {
        $test1 = array('str'=>"xabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a(*THEN)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_989() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?:.*?a)(?<=ba)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_1045() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"^abc(?<=b\\Kc)d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_24() {
        $test1 = array('str'=>"WXYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"É–XYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>1),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"XYZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(.))X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_43() {
        $test1 = array('str'=>"XyyyaÄ€Ä€bXzzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>8),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\x{100}{2}b)X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_80() {
        $test1 = array('str'=>"abcÈ€X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abcÄ€X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"X",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[\\x{100}\\x{200}])X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_81() {
        $test1 = array('str'=>"abcÈ€X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abcÄ€X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abQX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"X",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[Q\\x{100}\\x{200}])X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_82() {
        $test1 = array('str'=>"abcÄ€È€Ä€X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcÈ€X",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"X",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=[\\x{100}\\x{200}]{3})X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_19() {
        $test1 = array('str'=>"aXbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"(?<=aXb)cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_20() {
        $test1 = array('str'=>"aÄ€bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"(?<=a\\x{100}b)cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_21() {
        $test1 = array('str'=>"aô€€€bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"(?<=a\\x{100000}b)cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }



}
