<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_backtracking {

    function data_for_test_Q_INF_1() {
        $test0 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abacd',
                       'results'=>array(array('is_match'=>true,    // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>3),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,    // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>5),
                                              'left'=>array(4),
                                              'correctending'=>'b')
                                        ));

        return array('regex'=>'ab+[a-z]*bacd',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_INF_2() {
        $test0 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcbccbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>11,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'a(b|c)+b',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_INF_3() {
        $test0 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcdaaaabcdZz',
                       'results'=>array(array('is_match'=>true,        // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>6),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,        // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>12),
                                              'length'=>array(0=>13,1=>1),
                                              'left'=>array(2),
                                              'correctending'=>'A')
                                        ));

        return array('regex'=>'abcd([a-z]|[A-Z])+Az',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_INF_4() {
        $test0 = array('str'=>'abbbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>5),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abbbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'a(b+|c)b',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_INF_5() {
        $test0 = array('str'=>'aaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a+a)+a',
                     'tests'=>array($test0, $test1, $test2));
    }

    function data_for_test_Q_INF_6() {
        $test0 = array('str'=>'aaaaaaaaaaa',
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>3),
                                              'left'=>array(1),
                                              'correctending'=>'b'),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>11),
                                              'left'=>array(1),
                                              'correctending'=>'b')
                                        ));

        $test1 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a+a)+b',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_1() {
        $test0 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abacd',
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>3),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>5),
                                              'left'=>array(4),
                                              'correctending'=>'b')
                                        ));

        return array('regex'=>'ab+[a-z]{1,6}bacd',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_2() {
        $test0 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'a(b|c){3,4}b',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_3() {
        $test0 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcdAz',
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>11),
                                              'length'=>array(0=>5,1=>1),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>5),
                                              'length'=>array(0=>6,1=>1),
                                              'left'=>array(3),
                                              'correctending'=>'a')
                                        ));

        return array('regex'=>'abcd([a-z]|[A-Z]){3,10}Az',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_4() {
        $test0 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abbcccbcbcbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'abcb',
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>3),
                                              'length'=>array(0=>4,1=>1),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>3),
                                              'length'=>array(0=>4,1=>1),
                                              'left'=>array(1),
                                              'correctending'=>'b')
                                        ));

        return array('regex'=>'a(b|c){3,20}b',
                     'tests'=>array($test0, $test1, $test2));
    }

    function data_for_test_Q_FIN_5() {
        $test0 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcdaAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'abcd(?:[a-z]|[A-Z]){,10}Az',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_6() {
        $test0 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a{3,5}a)+a',
                     'tests'=>array($test0));
    }

    function data_for_test_Q_FIN_6_1() {
        $test0 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a{3,5}a)a',
                     'tests'=>array($test0));
    }

    function data_for_test_Q_FIN_6_2() {
        $test0 = array('str'=>'aaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a{3,5}a)+a',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_Q_FIN_7() {
        $test0 = array('str'=>'aaaaaaaaaaa',
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>5),
                                              'left'=>array(0),
                                              'correctending'=>'nextchar'),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>11),
                                              'left'=>array(1),
                                              'correctending'=>'b')
                                        ));

        $test1 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(?:a{3,5}a)+b',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_CAT_1() {
        $test0 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcdefg',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7),
                       'left'=>array(2),
                       'correctending'=>'z');

        return array('regex'=>'abcdefgza',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_CAT_2() {
        $test0 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>2,4=>5,5=>6,6=>7),
                       'length'=>array(0=>9,1=>9,2=>2,3=>3,4=>4,5=>2,6=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>-1,2=>0,3=>-1,4=>-1,5=>-1,6=>-1),
                       'length'=>array(0=>4,1=>-1,2=>2,3=>-1,4=>-1,5=>-1,6=>-1),
                       'left'=>array(5),
                       'correctending'=>'e');

        return array('regex'=>'((ab)(cde)(f(g(z))a))',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_CAT_3() {
        $test0 = array('str'=>'abcdeGDDDRER',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'correctending'=>'f');

        return array('regex'=>'abcdefgza',
                     'tests'=>array($test0));
    }

    function data_for_test_CAT_4() {
        $test0 = array('str'=>'abef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(ab|cd)ef',
                     'tests'=>array($test0, $test1, $test2));
    }

    function data_for_test_OR_1() {
        $test0 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'A|B|C|D|E|F',
                     'tests'=>array($test0, $test1, $test2));
    }

    function data_for_test_OR_2() {
        $test0 = array('str'=>'abcdefabc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>-1,2=>-1,3=>0),
                       'length'=>array(0=>6,1=>-1,2=>-1,3=>6),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>-1,2=>0,3=>-1),
                       'length'=>array(0=>3,1=>-1,2=>3,3=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(abc)|(cde)|(abcdef)',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_OR_3() {
        $test0 = array('str'=>'DEF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>3,2=>3),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>-1),
                       'length'=>array(0=>1,1=>1,2=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'B',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>-1),
                       'length'=>array(0=>1,1=>1,2=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test3 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>-1,2=>-1),
                       'length'=>array(0=>1,1=>-1,2=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'A|(B|C|(DEF))',
                     'tests'=>array($test0, $test1, $test2, $test3));
    }

    function data_for_test_OR_4() {
        $test0 = array('str'=>'deff',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>-1,2=>-1),
                       'length'=>array(0=>3,1=>-1,2=>-1),
                       'left'=>array(0),
                       'correctending'=>qtype_preg_matching_results::DELETE_TAIL);

        $test1 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>-1),
                       'length'=>array(0=>3,1=>3,2=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(^abc)|(def$)',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_CHARSET_1() {
        $test0 = array('str'=>'3',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'7a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'[A-Z0-9]',
                     'tests'=>array($test0, $test1, $test2));
    }

    function data_for_test_CHARSET_2_1() {
        $test0 = array('str'=>'aGfQ',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'0Tdb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'correctending'=>'');

        $test2 = array('str'=>'9Af7',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(1),
                       'correctending'=>' abcdefghijklmnopqrstuvwxyz');

        $test3 = array('str'=>'TTff',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1),
                       'left'=>array(3),
                       'correctending'=>'A');

        return array('regex'=>'[^A-Z][A-Z][dfg][^0-9]',
                     'tests'=>array($test0, $test1, $test2, $test3));
    }

    function data_for_test_ASSERT_b_1() {
        $test0 = array('str'=>'3=',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'[A-Z0-5=]\b[0-5A-R=]',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_b_2() {
        $test0 = array('str'=>'=',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>0),
                       'left'=>array(0),
                       'correctending'=>'nextchar');

        return array('regex'=>'\b[0-5A-R]',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_not_b_1() {
        $test0 = array('str'=>'AF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'[A-Z0-5]\B[0-5A-R]',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_b_3() {
        $test0 = array('str'=>'ABDEDSGR 0357',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'ABDEDSGR',                                  // test fails at the moment
                       'results'=>array(array('is_match'=>true,            // results for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>2),
                                              'left'=>array(1),
                                              'correctending'=>' '),
                                        array('is_match'=>true,            // results for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>8),
                                              'left'=>array(1),
                                              'correctending'=>' 012345')
                                        ));

        return array('regex'=>'[A-Z0-5 ]+\b[0-5A-R ]+',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_1() {
        $test0 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'ababa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(ab)\1',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_2() {
        $test0 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2),
                       'left'=>array(2),
                       'correctending'=>'a');

        return array('regex'=>'(ab|cd)\1',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_3() {
        $test0 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>12,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(ab)+\1',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_4() {
        $test0 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>12,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'ababacdcdcdcd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(ab|cd)\1+',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_5() {
        $test0 = array('str'=>'abefabababababef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>16,1=>2,2=>2),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'cdghabghghef',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2),
                       'left'=>array(4),
                       'correctending'=>'c');

        return array('regex'=>'(ab|cd)(ef|gh)\1+\2',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_BACKREF_6() {
        $test0 = array('str'=>'aaaaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>13,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(a|\1)+',
                     'tests'=>array($test0));
    }

    function data_for_test_BACKREF_7() {
        $test0 = array('str'=>'ababbaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'(a|b\1)+',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_ETEST_1() {
        $test0 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'correctending'=>qtype_preg_matching_results::DELETE_TAIL);

        return array('regex'=>'abc$',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_ASSERT_ETEST_3() {
        $test0 = array('str'=>'bc',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>-1),
                       'length'=>array(0=>1,1=>-1),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test1 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>-1),
                       'length'=>array(0=>1,1=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'a|(b$)c',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_ASSERT_ETEST_5() {
        $test0 = array('str'=>'abca',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>-1),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'$abca',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_STEST_1() {
        $test0 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'abc^',
                     'tests'=>array($test0));
    }

    function data_for_test_ASSERT_STEST_2() {
        $test0 = array('str'=>'c',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0),
                       'left'=>array(0),
                       'correctending'=>'');

        $test1 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>-1),
                       'length'=>array(0=>1,1=>-1),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'a|(^)c',
                     'tests'=>array($test0, $test1));
    }

    function data_for_test_ASSERT_STEST_4() {
        $test0 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'correctending'=>'');

        return array('regex'=>'^abca',
                     'tests'=>array($test0));
    }
}