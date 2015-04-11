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
                        'length'=>array(0=>3, 1=>3));

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
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>3));

        return array('regex'=>'a(?=b)([a-z])*c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_lookahead_4() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>2, 1=>2));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>0, 1=>0));

        $test3 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>0, 1=>0));

        return array('regex'=>'(?=bc?)(bc|)',
                     'tests'=>array($test1, $test2, $test3),
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
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>1),
                        'length'=>array(0=>2, 1=>2, 2=>2, 3=>1));

        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'(?=(bc)?)(b(c|))',
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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
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
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>4, 1=>0),
                        'left'=>array(1),
                        'next'=>'b');

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
                        'full'=>false,
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
                        'next'=>'\W');

        return array('regex'=>'a\B(?=\W).',
                     'tests'=>array($test1, $test2),
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
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>3, 1=>2, 1=>2));
                        
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
                        'length'=>array(0=>6, 1=>2, 2=>2));
                        
        $test7 = array( 'str'=>'abcbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>3, 1=>2, 2=>2));
                        
        return array('regex'=>'a(?=(b[cd])|)(b[c-z])*(?<=(ab[cl])|)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }
    
    function data_for_test_both_assertions_5() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));
                        
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
                        'index_first'=>array(0=>0, 1=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1, 2=>2));
                        
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
                        'length'=>array(0=>0, 1=>0
                        
        $test6 = array( 'str'=>'abcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>3, 1=>1, 2=>2));

        return array('regex'=>'(a|)(?=(b[cd])|)(?<=(ab[cl])|)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }
    
    function data_for_test_both_assertions_7() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 1=>1),
                        'length'=>array(0=>3, 1=>0, 2=>3));
                        
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
                        'index_first'=>array(0=>0, 1=>0, 2=>0, 3=>3),
                        'length'=>array(0=>5, 1=>0, 2=>3, 3=>2));
                        
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
                        'left'=>array(2),
                        'next'=>'\n');

        $test2 = array( 'str'=>'a\n',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\n');
                        
        $test4 = array( 'str'=>'a\n\n',
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

        $test2 = array( 'str'=>'a\t',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\w');
                        
        $test3 = array( 'str'=>'a\nc',
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
                        'left'=>array(1),
                        'next'=>'\W');

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

        return array('regex'=>'(?=\b)cat(?=\b)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }
    
    function data_for_test_both_assertions_11() {
        $test1 = array( 'str'=>" cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>' cat',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'\t');

        return array('regex'=>'(?=c| )\bcat\b(?=t|\t)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }
    
    function data_for_test_both_assertions_12() {
        $test1 = array( 'str'=>" cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>' cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test4 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test5 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test6 = array( 'str'=>"\tcat ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

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
}
