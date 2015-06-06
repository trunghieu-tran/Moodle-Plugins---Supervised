<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg_intersection {

    // With epsilons.
    function data_for_test_assertions_lookahead_1() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test3 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');


        return array('regex'=>'a(?=b)(\w|)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_2() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test3 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');


        return array('regex'=>'a(?=b)(b?)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_3() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test3 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test4 = array( 'str'=>'abbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>5, 1=>1));

        return array('regex'=>'a(?=b)([a-z])*c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_4() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1, 2=>0),
                        'length'=>array(0=>0, 1=>-1, 2=>0));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1, 2=>0),
                        'length'=>array(0=>0, 1=>-1, 2=>0));

        $test3 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1, 2=>0),
                        'length'=>array(0=>0, 1=>-1, 2=>0));

        $test4 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0),
                        'length'=>array(0=>2, 1=>2, 2=>2));

        return array('regex'=>'(?=(bc)?)(bc|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_5() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>0));

        $test2 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>2, 1=>2, 2=>1));

        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'(?=bc?)(b(c|))',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_6() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>0));

        $test2 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0),
                        'left'=>array(1),
                        'next'=>'b');

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'a(?=b)()\w',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_7() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>0));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>4, 1=>0));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test4 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'a(?=b)(\w?)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_8() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>0));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>4, 1=>0));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test4 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>0));

        return array('regex'=>'a(?=b*)(\w?)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_9() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test4 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        return array('regex'=>'a(?=b?)()',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // With asserts ^/$.
    function data_for_test_assertions_lookahead_10() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        return array('regex'=>'(?m)ab\n(?=cd)^cd',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_11() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab(?=\ncd)\n^cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_12() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab(?=\ncd)[a-z\n]^cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_13() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?m)ab(?=cd)[a-z\n]^cd',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_14() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        return array('regex'=>'(?m)ab$(?=\ncd)\ncd',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_15() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab$(?=\ncd)\n^cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_16() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab$(?=\ncd)[a-z\n]cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_17() {
        $test1 = array( 'str'=>"ab\ncd",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?m)ab$(?=cd)[a-z\n]cd',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // With wordbreak assertions.
    function data_for_test_assertions_lookahead_18() {
        $test1 = array( 'str'=>'ab d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'ab\b(?= d) d',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_19() {
        $test1 = array( 'str'=>'abc d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>' ');

        return array('regex'=>'ab(?=c d)c\b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_20() {
        $test1 = array( 'str'=>'a&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'&');

        $test4 = array( 'str'=>'!&',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'d');

        return array('regex'=>'[a!](?=d|&)\b[&d]',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_21() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'a ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'\w');

        return array('regex'=>'a\B(?=\w).',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Intersection capturing and uncapturing transitions.
    function data_for_test_assertions_lookahead_22() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'cab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'(?=ab)(a|^)b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_23() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'cb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'dab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'(?=^[ac]b)^(a|c)b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_24() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'cb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'dab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'(?=^[ac]b)(^a|c)b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_25() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        $test2 = array( 'str'=>'cb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        $test3 = array( 'str'=>'dab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>0, 1=>0));

        return array('regex'=>'(?=[ac]b)()',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_26() {
        $test1 = array( 'str'=>'dab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'d(?=ab)$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_27() {
        $test1 = array( 'str'=>"d\na",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'da',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)d(?=\na)$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Problem test!!!
    function data_for_test_assertions_lookahead_28() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'(?m)\n(?=^a)ab',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_29() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        return array('regex'=>'a(?=b)',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_30() {
        $test1 = array( 'str'=>'dx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'(?=[a-d][b-x])[d-y][x-z]',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_31() {
        $test1 = array( 'str'=>'dx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'jy',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'d');

        $test3 = array( 'str'=>'dz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'x');

        return array('regex'=>'(?=[a-k][a-z])(?=[a-d][c-x])[d-y][x-z]',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_32() {
        $test1 = array( 'str'=>'ab!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'=');

        $test2 = array( 'str'=>'a=cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'=');

        $test4 = array( 'str'=>'a=!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'a(b|=)(?=\b[!c]d)cd',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_33() {
        $test1 = array( 'str'=>'abe=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>'ab!t',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'e');

        $test3 = array( 'str'=>'abet',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'=');

        return array('regex'=>'a(?=b[!e]\b)be(=|t)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_34() {
        $test1 = array( 'str'=>'abe=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3, 2=>3),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        $test2 = array( 'str'=>'ab!t',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'e');

        $test3 = array( 'str'=>'abet',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3, 2=>3),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        return array('regex'=>'a(?=b[!e](\b|.))be(=|t)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_35() {
        $test1 = array( 'str'=>'ab!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(3),
                        'next'=>'c');

        $test2 = array( 'str'=>'a=cdd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>5, 1=>1, 2=>0));

        $test3 = array( 'str'=>'abccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        $test4 = array( 'str'=>'a=!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(3),
                        'next'=>'c');

        return array('regex'=>'a(b|=)(?=(\b|.)[!c]d)c[cd]d',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // \b in regex.
    function data_for_test_assertions_lookahead_36() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)(?=ab)\bab',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_37() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)\b(?=ab)ab',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_38() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)\b(?=ab)\bab',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_39() {
        $test1 = array( 'str'=>'cdabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>4, 1=>1, 2=>0));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>2, 1=>1, 2=>0),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)(?=ab)(\b|[a-z])[a-k]b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_40() {
        $test1 = array( 'str'=>'cdaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>4, 1=>1, 2=>0));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)(\b|[a-z])(?=ab)[a-k]b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_41() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)\b(?=\bab)ab',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_42() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'c ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'c bb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'c( |d)(?=\bab)\bab',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_1() {
        $test1 = array( 'str'=>'abef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        $test2 = array( 'str'=>'acef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b');

        return array('regex'=>'a(b|c)(?<=a(b|d))ef',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_2() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>1),
                        'length'=>array(0=>3, 1=>1, 2=>2));

        $test2 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>3),
                        'length'=>array(0=>3, 1=>1, 2=>0));

        return array('regex'=>'ab(c|d)(?<=(bc|))',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_3() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'cd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');

        return array('regex'=>'(?<=ab)cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }


    function data_for_test_assertions_lookbehind_4() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>'aabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4));

        return array('regex'=>'ab+(?<=a*b)cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_5() {
        $test1 = array( 'str'=>'ax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'ay',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'a[x-z](?<=[a-c][a-z])',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_6() {
        $test1 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>5, 1=>2));

        $test2 = array( 'str'=>'cce',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'cde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'(ab)?cd*(?<=c[a-d])e',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_7() {
        $test1 = array( 'str'=>'bcad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>0),
                        'length'=>array(0=>4, 1=>2, 2=>4));

        $test2 = array( 'str'=>'adbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>0),
                        'length'=>array(0=>4, 1=>2, 2=>4));

        $test3 = array( 'str'=>'adbcad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4, 2=>2),
                        'length'=>array(0=>6, 1=>2, 2=>4));

        $test4 = array( 'str'=>'adadbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4, 2=>2),
                        'length'=>array(0=>6, 1=>2, 2=>4));

        $test5 = array( 'str'=>'bcbcad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4, 2=>2),
                        'length'=>array(0=>6, 1=>2, 2=>4));

        $test6 = array( 'str'=>'bcadbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4, 2=>2),
                        'length'=>array(0=>6, 1=>2, 2=>4));

        return array('regex'=>'(ad|bc)+(?<=(bcad|adbc))',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_8() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>4, 1=>2));

        $test2 = array( 'str'=>'ababcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>4, 1=>2));

        return array('regex'=>'ab(?<=(ab)+)cd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_9() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'^ab$(?<=ab)',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_10() {
        $test1 = array( 'str'=>"ab\nef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab$[cd\n](?<=.[a-z]\n)ef',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_11() {
        $test1 = array( 'str'=>"ab\nef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abcef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)ab[cd\n](?<=.[a-z]\n)^ef',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_12() {
        $test1 = array( 'str'=>"ab\nef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>5, 1=>3));

        $test2 = array( 'str'=>"abcab\nef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>8, 1=>3));

        $test3 = array( 'str'=>'ef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'(?m)(ab[cd\n](?<=.[a-z]\n))*^ef',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_13() {
        $test1 = array( 'str'=>'!c&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'[a!]\b[c=]\b(?<=[b!]\b[c&])&',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_14() {
        $test1 = array( 'str'=>'cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'^|a(?<=\b)cd',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Intersection capturing and uncapturing transitions.
    function data_for_test_assertions_lookbehind_15() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        return array('regex'=>'b(a|$)(?<=ba)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_16() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>2),
                        'length'=>array(0=>2, 1=>1));

        $test4 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'b(a|c)$(?<=b[ac])',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_17() {
        $test1 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'bca',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'b(a|c$)(?<=b[ac]$)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_18() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>0, 1=>0));

        $test2 = array( 'str'=>'cb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>0, 1=>0));

        $test3 = array( 'str'=>'dab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3, 1=>3),
                        'length'=>array(0=>0, 1=>0));

        return array('regex'=>'()(?<=[ac]b)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_19() {
        $test1 = array( 'str'=>'abd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'^(?<=ab)d',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_20() {
        $test1 = array( 'str'=>"a\nd",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)^(?<=a\n)d',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Problem test!!!
    function data_for_test_assertions_lookbehind_21() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'(?m)ab(?<=b$)\n',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_22() {
        $test1 = array( 'str'=>'cs',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'ck',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'s');

        $test3 = array( 'str'=>'ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'[a-z]{2}(?<=[a-c][a-s])(?<=[c-z][s-z])',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_23() {
        $test1 = array( 'str'=>'ab!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'=');

        $test2 = array( 'str'=>'a=cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'=');

        $test4 = array( 'str'=>'a=!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'a(b|=)cd(?<=\b[!c]d)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_24() {
        $test1 = array( 'str'=>'abe=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>'ab!t',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'e');

        $test3 = array( 'str'=>'abet',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'=');

        return array('regex'=>'abe(?<=b[!e]\b)(=|t)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_25() {
        $test1 = array( 'str'=>'abe=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3, 2=>3),
                        'length'=>array(0=>4, 1=>0, 2=>1));

        $test2 = array( 'str'=>'ab!t',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'e');

        $test3 = array( 'str'=>'abet',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>3),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        return array('regex'=>'abe(?<=[ab][!be](\b|.))(=|t)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_26() {
        $test1 = array( 'str'=>'ab!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>2, 1=>1, 2=>1),
                        'left'=>array(2),
                        'next'=>'c');

        $test2 = array( 'str'=>'a=cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>4, 1=>1, 2=>1));

        $test4 = array( 'str'=>'a=!d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>2, 1=>1, 2=>1),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'a(b|=)cd(?<=(.|\b)[!c]d)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_27() {
        $test1 = array( 'str'=>'abdc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>' ');

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'ab(?<=ab)\b( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_28() {
        $test1 = array( 'str'=>'abdc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>' ');

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'ab\b(?<=ab)( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    /*function data_for_test_assertions_lookbehind_29() {
        $test1 = array( 'str'=>'abdc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>' ');

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'ab\b(?<=ab)\b( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }*/

    function data_for_test_assertions_lookbehind_30() {
        $test1 = array( 'str'=>'aabdc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>3),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>2),
                        'length'=>array(0=>4, 1=>0, 2=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2, 2=>2),
                        'length'=>array(0=>3, 1=>0, 2=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'a[ab](\b|[a-k])(?<=ab)( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_31() {
        $test1 = array( 'str'=>'abkdc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>3),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2, 2=>2),
                        'length'=>array(0=>4, 1=>0, 2=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2, 2=>2),
                        'length'=>array(0=>3, 1=>0, 2=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'ab(?<=ab)(\b|[a-k])( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_32() {
        $test1 = array( 'str'=>'abdc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>' ');

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'ab(?<=ab\b)\b( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_33() {
        $test1 = array( 'str'=>'abdc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>' ');

        $test2 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>'ab b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'ab\b(?<=ab\b)( |d)c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_1() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'a(?=[b-d])\w(?<=[a-z])',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_2() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test2 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test4 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'a(?=[b-d])(|\s)(?<=[a-z])',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_3() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'a(?=b[cd]|)b[c-z](?<=ab[cl])',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_4() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1, 3=>0),
                        'length'=>array(0=>3, 1=>2, 2=>2, 3=>3));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test5 = array( 'str'=>'abcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>3),
                        'length'=>array(0=>5, 1=>2, 2=>2));

        $test6 = array( 'str'=>'abcbcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>5),
                        'length'=>array(0=>7, 1=>2, 2=>2));

        $test7 = array( 'str'=>'abcbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1, 3=>0),
                        'length'=>array(0=>3, 1=>2, 2=>2,3=>3));

        return array('regex'=>'a(?=(b[cd])|)(b[c-z])*(?<=(ab[cl])|)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_5() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>2));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        return array('regex'=>'a(?=(b[cd])|)(?<=(ab[cl])|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_6() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>2));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1));

        $test5 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        $test6 = array( 'str'=>'abcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>2));

        return array('regex'=>'(a|)(?=(b[cd])|)(?<=(ab[cl])|)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_7() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3, 1=>3, 2=>0),
                        'length'=>array(0=>0, 1=>0, 2=>3));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1));

        $test3 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>0, 1=>0));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>0, 1=>0));

        $test5 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        $test6 = array( 'str'=>'abcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3, 1=>3, 2=>3, 3=>0),
                        'length'=>array(0=>0, 1=>0, 2=>2, 3=>3));

        return array('regex'=>'(a|)(?=(b[cd])|)(?<=(ab[cl])|$)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_8() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'\n');

        $test2 = array( 'str'=>'a\n',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>'a\n\n',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'(?m)a(?=$)[bc\n](?<=^)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_9() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\t');

        $test2 = array( 'str'=>"a\t",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\w');

        $test3 = array( 'str'=>"a\tc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'a(?=\b\t)\W(?<=\s\b)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_10() {
        $test1 = array( 'str'=>'cata',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'cat ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>' cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test5 = array( 'str'=>' cat ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        return array('regex'=>'(?<=\b)cat(?=\b)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_11() {
        $test1 = array( 'str'=>" cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>' cata',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?=c| )\bcat\b(?<=t|\t)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_12() {
        $test1 = array( 'str'=>" cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>' cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test5 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test6 = array( 'str'=>"\tcat ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        return array('regex'=>'\b(?=c| )cat(?<=t|\t)\b',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_both_assertions_13() {
        $test1 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1, 3=>0),
                        'length'=>array(0=>3, 1=>1, 2=>2, 3=>3));

        $test3 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2, 3=>0),
                        'length'=>array(0=>4, 1=>2, 2=>2, 3=>4));

        $test4 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>0),
                        'length'=>array(0=>2, 1=>0, 2=>2, 3=>2));

        $test5 = array( 'str'=>'bcc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>0),
                        'length'=>array(0=>3, 1=>0, 2=>3, 3=>2));

        $test6 = array( 'str'=>'abcc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1, 3=>0),
                        'length'=>array(0=>4, 1=>1, 2=>3, 3=>3));

        $test7 = array( 'str'=>'aaaabcc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>4, 3=>0),
                        'length'=>array(0=>7, 1=>4, 2=>3, 3=>6));

        $test8 = array( 'str'=>'bl',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>0),
                        'length'=>array(0=>2, 1=>0, 2=>0, 3=>2));

        $test9 = array( 'str'=>'bdd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>0),
                        'length'=>array(0=>3, 1=>0, 2=>3, 3=>0));

        return array('regex'=>'(a|)*(?=(b[cd]+)|)(?<=(a*b[cl])|$)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_both_start() {
        $test1 = array( 'str'=>'ddab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'acab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'bdak',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');


        return array('regex'=>'(?=ab)(?<=[a-d][b-d])[a-z][a-k]',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_both_end() {
        $test1 = array( 'str'=>'cdab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'acab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'bbak',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b');


        return array('regex'=>'[a-z][a-k](?=ab)(?<=[a-d][b-d])',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_end() {
        $test1 = array( 'str'=>'yzax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'dzdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'yxaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c');


        return array('regex'=>'[d-y][x-z](?=[a-k][a-z])(?=[a-d][c-x])',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookbehind_start() {
        $test1 = array( 'str'=>'acxz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'dxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'aayy',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'c');


        return array('regex'=>'(?<=[a-k][a-z])(?<=[a-d][c-x])[d-y][x-z]',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }
}
