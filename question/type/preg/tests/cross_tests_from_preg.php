<?php

/**
 * Unit tests for matchers
 *
 * @copyright 2012  Valeriy Streltsov, Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

defined('NOMATCH') || define('NOMATCH', qtype_preg_matching_results::NO_MATCH_FOUND);

class qtype_preg_cross_tests_from_preg {

    // Tests for general cases.
    function data_for_test_concat_1() {
        $test1 = array( 'str'=>'the matcher works',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'_the matcher works',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(17),
                        'next'=>'t',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'the matcher',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(6),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(17),
                        'next'=>'t',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^the matcher works',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_concat_2() {
        $test1 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcdefg',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7),
                       'left'=>array(2),
                       'next'=>'z',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'abcdeGDDDRER',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'f',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abcdefgza',
                     'tests'=>array($test1, $test2, $test3));
    }

        function data_for_test_concat_3() {
        $test1 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>2,4=>5,5=>6,6=>7),
                       'length'=>array(0=>9,1=>9,2=>2,3=>3,4=>4,5=>2,6=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>NOMATCH,2=>0,3=>NOMATCH,4=>NOMATCH,5=>NOMATCH,6=>NOMATCH),
                       'length'=>array(0=>4,1=>NOMATCH,2=>2,3=>NOMATCH,4=>NOMATCH,5=>NOMATCH,6=>NOMATCH),
                       'left'=>array(5),
                       'next'=>'e',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'((ab)(cde)(f(g(z))a))',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_concat_4() {
        $test1 = array('str'=>'abef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab|cd)ef',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_concat_5() {
        $test1 = array( 'str'=>'fgh',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'abce',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^abcd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_concat_6() {
        $test1 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'OacO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'ab',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_concat_7() {
        $test1 = array( 'str'=>'sometextwithoutmatchingandsomeregexwithmatchig',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>26),
                        'length'=>array(0=>9),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'someregex',
                     'tests'=>array($test1));
    }

    function data_for_test_alt_1() {
        $test1 = array( 'str'=>'abcf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'deff',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'^abc|def$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_alt_2() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'[aceg]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_alt_3() {
        $test1 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'A|B|C|D|E|F',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_alt_4() {
        $test1 = array('str'=>'abcdefabc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>NOMATCH,2=>NOMATCH,3=>0),
                       'length'=>array(0=>6,1=>NOMATCH,2=>NOMATCH,3=>6),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>NOMATCH,2=>0,3=>NOMATCH),
                       'length'=>array(0=>3,1=>NOMATCH,2=>3,3=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(abc)|(cde)|(abcdef)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_alt_5() {
        $test1 = array('str'=>'DEF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>3,2=>3),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                       'length'=>array(0=>1,1=>1,2=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'B',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                       'length'=>array(0=>1,1=>1,2=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test4 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>NOMATCH,2=>NOMATCH),
                       'length'=>array(0=>1,1=>NOMATCH,2=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'A|(B|C|(DEF))',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_alt_6() {
        $test1 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>NOMATCH),
                        'length'=>array(0=>1, 1=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^(ab|cd)$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_charset_1() {
        $test1 = array('str'=>'3',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'7a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'[A-Z0-9]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_charset_2() {
        $test1 = array('str'=>'aGfQ',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'0Tdb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'9Af7',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(1),
                       'next'=>'[^0-9]',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test4 = array('str'=>'TTff',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1),
                       'left'=>array(3),
                       'next'=>'A',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'[^A-Z][A-Z][dfg][^0-9]',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_charset_3() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[^b]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aacde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_metacharacter_dot() {
        $test1 = array( 'str'=>'afc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^a.c$',
                     'tests'=>array($test1));
    }

    function data_for_test_digit() {
        $test1 = array( 'str'=>'273x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'ax',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH, 1=>NOMATCH),
                        'length'=>array(0=>NOMATCH, 1=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'(\d)+x',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_wordchar() {
        $test1 = array( 'str'=>'a_a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'a{a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'a\wa',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_1() {
        $test1 = array( 'str'=>' abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>' 9bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'  b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[a-z 0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_assertions_simple_2() {
        $test1 = array('str'=>'3=',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'[A-Z0-5=]\b[0-5A-R=]',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_3() {
        $test1 = array('str'=>'=',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(0=>NOMATCH),
                       'length'=>array(0=>NOMATCH),
                       'left'=>array(1),
                       'next'=>'[0-5A-R]',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'\b[0-5A-R]',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_4() {
        $test1 = array('str'=>'AF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'[A-Z0-5]\B[0-5A-R]',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_5() {
        $test1 = array('str'=>'ABDEDSGR 0357',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'ABDEDSGR',       // TODO: TAGS - BACKTRACKING
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(1),
                       'next'=>' ',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'ABDEDSGR',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8),
                       'left'=>array(1),
                       'next'=>'[0-5 ]',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'[A-Z0-5 ]+\b[0-5A-R ]+',
                     'tests'=>array($test1, /*$test2,*/ $test3));
    }

    function data_for_test_assertions_simple_6() {
        $test1 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abc$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_7() {
        $test1 = array('str'=>'bc',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>NOMATCH),
                       'length'=>array(0=>1,1=>NOMATCH),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>NOMATCH),
                       'length'=>array(0=>1,1=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a|(b$)c',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_8() {
        $test1 = array('str'=>'abca',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(0=>NOMATCH),
                       'length'=>array(0=>NOMATCH),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'$abca',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_9() {
        $test1 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abc^',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_10() {
        $test1 = array('str'=>'c',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>NOMATCH),
                       'length'=>array(0=>1,1=>NOMATCH),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a|(^)c',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_11() {
        $test1 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'^abca',
                     'tests'=>array($test1));
    }

    function data_for_test_assertions_simple_12() {
        $test1 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^ab',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_13() {
        $test1 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'ab$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_simple_14() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^ab$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_case_sensitivity1() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'aBcD',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_case_sensitivity2() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'aBcD',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_case_sensitivity3() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));


        $test4 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?:a(?i)b|c)',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    // Tests for cases with ambiguity - subpatterns, quantifiers and backreferences.
    function data_for_test_empty_match() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(abcd|)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_nested() {
        $test1 = array( 'str'=>'abcbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3,3=>4),
                        'length'=>array(0=>6,1=>4,2=>2,3=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>NOMATCH,3=>NOMATCH),    // The quantifier is outside subpatterns 2 and 3 so they are not matched!
                        'length'=>array(0=>2,1=>0,2=>NOMATCH,3=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^a((b(c)(?:\b|\B))*)d$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_with_quant_nested() {
        $test1 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                        'length'=>array(0=>2,1=>2,2=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                        'length'=>array(0=>1,1=>1,2=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'[+\-]?([0-9]+)?\.([0-9]+)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_concatenated() {
        $test1 = array( 'str'=>'_abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>3,3=>5),
                        'length'=>array(0=>6,1=>2,2=>2,3=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'[prefix] abef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>9,1=>9,2=>NOMATCH,3=>11),
                        'length'=>array(0=>4,1=>2,2=>NOMATCH,3=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(ab)(cd)?(ef)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_alternated() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>NOMATCH,4=>NOMATCH),
                        'length'=>array(0=>2,1=>2,2=>2,3=>NOMATCH,4=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'((ab)|(cd)|(efgh))',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_quantifier_inside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a*)',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_quantifier_outside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a)*',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_tricky() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>2),
                        'length'=>array(0=>3,1=>3,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>3,3=>4,4=>5),
                        'length'=>array(0=>6,1=>3,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(([a*]|\b)([b*]|\b)([c*]|\b))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_nested_and_concatenated() {
        $test1 = array( 'str'=>'zw',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH,3=>1),
                        'length'=>array(0=>2,1=>1,2=>NOMATCH,3=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'*&^%&^',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH,2=>NOMATCH,3=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH,2=>NOMATCH,3=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(z|y(x))(w)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_duplicate_simple() {
        $test1 = array( 'str'=>'cat-cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'dog-dogs',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?|(cat)|(dog))-\1(s)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_duplicate() {
        $test1 = array( 'str'=>'abee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>NOMATCH,4=>2),
                        'length'=>array(0=>4,1=>1,2=>1,3=>NOMATCH,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'acdee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                        'length'=>array(0=>5,1=>1,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a)(?|(b)|(c)(d))(e)\4',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_named() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?P<name>abc)\1\g{name}',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_qu() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_1() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),    // Less characters left.
                        'next'=>'[ab]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?:a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_2() {
        $test1 = array( 'str'=>'abcabcabcabcabcabcabcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abcabcabcabcabcabcabcabcabcab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?:abc)*',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_aster_3() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>47),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^ab*c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_4() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>NOMATCH),
                        'length'=>array(0=>2, 1=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>NOMATCH),
                        'length'=>array(0=>3, 1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>72),
                        'length'=>array(0=>76, 1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'^(a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_5() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[ab]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'(?:a|b|c|d)*abb',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_aster_6() {
        $test1 = array( 'str'=>'aabbbabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'[ab]*abb',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_aster_7() {
        $test1 = array( 'str'=>'aabbbabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'[ab]*[ac]bb',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_aster_8() {
        $test1 = array( 'str'=>'@W#G%9bb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'.*\wbb',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_aster_9() {
        $test1 = array( 'str'=>'aaa_aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'(?:\w)*a',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_plus_1() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>101),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_plus_2() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcbccbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>11,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a(b|c)+b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_quant_plus_3() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcdaaaabcdZz',      // TODO: TAGS - BACKTRACKING
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6),
                       'left'=>array(2),
                       'next'=>'A',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'abcdaaaabcdZz',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>13,1=>1),
                       'left'=>array(2),
                       'next'=>'A',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abcd([a-z]|[A-Z])+Az',
                     'tests'=>array($test1, /*$test2,*/ $test3));
    }

    function data_for_test_quant_plus_4() {
        $test1 = array('str'=>'abbbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>5),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abbbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a(b+|c)b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_quant_plus_5() {
        $test1 = array('str'=>'aaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(?:a+a)+a',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_plus_6() {
        $test1 = array('str'=>'aaaaaaaaaaa',    // TODO: TAGS - BACKTRACKING
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(1),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(1),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(?:a+a)+b',
                     'tests'=>array(/*$test1,*/ $test2, $test3));
    }

    function data_for_test_quant_brace_finite_1() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>36),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_brace_finite_2() {
        $test1 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abacd',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'abacd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'ab+[a-z]{1,6}bacd',
                     'tests'=>array($test1, /*$test2,*/ $test3));
    }

    function data_for_test_quant_brace_finite_3() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a(b|c){3,4}b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_quant_brace_finite_4() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcdAz',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>5,1=>1),
                       'left'=>array(3),
                       'next'=>'[a-zA-Z]',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'abcdAz',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(3),
                       'next'=>'[a-zA-Z]',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abcd([a-z]|[A-Z]){3,10}Az',
                     'tests'=>array($test1, /*$test2,*/ $test3));
    }

    function data_for_test_quant_brace_finite_6() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abbcccbcbcbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>14,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'abcb',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1),
                       'left'=>array(1),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'a(b|c){3,20}b',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_brace_finite_7() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcdaAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'abcd(?:[a-z]|[A-Z]){,10}Az',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_quant_brace_finite_8() {
        $test1 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(?:a{3,5}a)a',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_brace_finite_9() {
        $test1 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'aaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(?:a{3,5}a)+a',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_brace_finite_10() {
        $test1 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(1),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(1),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test3 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(?:a{3,5}a)+b',
                     'tests'=>array(/*$test1,*/ $test2, $test3));
    }

    function data_for_test_quant_brace_infinite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>104),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^ab{15,}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_greedy() {
        $test1 = array('str'=>'abacd',
                       'is_match'=>true,    // TODO: TAGS - BACKTRACKING
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abacd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array('str'=>'ababac',
                       'is_match'=>true,    // TODO: TAGS - BACKTRACKING
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test4 = array('str'=>'ababac',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6),
                       'left'=>array(1),
                       'next'=>'d',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test5 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'ab+[a-z]*bacd',
                     'tests'=>array(/*$test1,*/ $test2, /*$test3,*/ $test4, $test5,));
    }

    function data_for_test_characters_left_simple() {
        $test1 = array( 'str'=>'ab cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'ab\b cd',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_characters_left() {
        $test1 = array( 'str'=>'abefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>2,1=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'h',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abe',
                        'is_match'=>true,         // TODO - LONGEST MATCH
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>3,1=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'abe',
                        'is_match'=>true,        // TODO - LESS CHARACTERS LEFT
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>2,1=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'h',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'ab(cd|efg|h)',
                     'tests'=>array($test1, $test2, /*$test3,*/ $test4));
    }

    function data_for_test_backrefs_1() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>12,1=>6,2=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>6,1=>6,2=>3),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));    // Backref #1 not captured at all.

        $test3 = array( 'str'=>'abcabcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>8,1=>6,2=>3),
                        'left'=>array(4),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));    // Backref #1 captured partially.

        return array('regex'=>'((abc)\2)\1',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_2() {
        $test1 = array( 'str'=>'abcdefghidef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>12,1=>9,2=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(abc(def)ghi)\g{-1}',
                     'tests'=>array($test1));
    }

    function data_for_test_backrefs_3() {
        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH,3=>2),
                        'length'=>array(0=>4,1=>2,2=>NOMATCH,3=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH,2=>0,3=>2),
                        'length'=>array(0=>4,1=>NOMATCH,2=>2,3=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH,3=>NOMATCH),
                        'length'=>array(0=>3,1=>2,2=>NOMATCH,3=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH,3=>NOMATCH),
                        'length'=>array(0=>2,1=>2,2=>NOMATCH,3=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?:(ab)|(cd))(\1|\2)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_backrefs_4() {
        $test1 = array( 'str'=>'ababcdababcdababcdababcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>12,2=>12),
                        'length'=>array(0=>24,1=>6,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH,2=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH,2=>NOMATCH),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'((ab)\2cd)*\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_5() {
        $test1 = array( 'str'=>'abcdabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abcdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>4),
                        'left'=>array(2),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>3,1=>NOMATCH),
                        'left'=>array(5),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(abcd)\1',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_backrefs_6() {
        $test1 = array( 'str'=>'abxyabab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'abxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'cdxyabab',
                        'is_match'=>true,        // TODO - Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>6,1=>NOMATCH),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'cdxyabab',
                        'is_match'=>true,        // TODO - Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>4,1=>NOMATCH),
                        'left'=>array(2),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test5 = array( 'str'=>'cdxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>6,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?:(ab)|cd)xy(?:ab\1|cd)',
                     'tests'=>array($test1, $test2, /*$test3,*/ $test4, $test5));
    }

    function data_for_test_backrefs_7() {
        $test1 = array( 'str'=>'Do hats eat cats?',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>3,1=>NOMATCH),
                        'left'=>array(12),
                        'next'=>'[cbr]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'Do cats',
                        'is_match'=>true,        // TODO - Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'left'=>array(10),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'Do cats',
                        'is_match'=>true,        // TODO - Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(9),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'bat eat fat?',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(15),
                        'next'=>'D',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'Do (?:[cbr]at(s|)) eat (?:[cbr]at\1)\?',
                     'tests'=>array($test1, /*$test2,*/ $test3, $test4));
    }

    function data_for_test_backrefs_8() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(9),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'0as',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>2,1=>NOMATCH),
                        'left'=>array(8),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'0defab',
                        'is_match'=>true,        // TODO - Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>4,1=>NOMATCH),
                        'left'=>array(12),
                        'next'=>'g',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'0defab',
                        'is_match'=>true,        // TODO - Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(9),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'0(abc|defghx)[0-9]{3}\1',
                     'tests'=>array($test1, $test2, /*$test3,*/ $test4));
    }

    function data_for_test_backrefs_9() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(13),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'0aaaaaaz',
                        'is_match'=>true,        // TODO - Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>7,1=>6),
                        'left'=>array(9),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'0aaaaaaz',
                        'is_match'=>true,        // TODO - Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>6,1=>5),
                        'left'=>array(8),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));


        return array('regex'=>'0(a{5,10})[0-9]{3}\1',
                     'tests'=>array($test1, /*$test2,*/ $test3));
    }

    function data_for_test_backrefs_10() {
        $test1 = array('str'=>'aaaaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>13,1=>1),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(a|\1)+',
                     'tests'=>array($test1));
    }

    function data_for_test_backrefs_11() {

        $test1 = array( 'str'=>'ababba',
                        'is_match'=>true,        // TODO Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(4),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'ababba',
                        'is_match'=>true,        // TODO Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(2),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'ababbaxbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>10,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'abab',
                        'is_match'=>true,        // TODO Longest match.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test5 = array( 'str'=>'abab',
                        'is_match'=>true,        // TODO Less characters left.
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(2),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a|b\1)+x\1',
                     'tests'=>array(/*$test1,*/ $test2, $test3, /*$test4,*/ $test5));
    }

    function data_for_test_backrefs_12() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>0),
                        'length'=>array(0=>5,1=>3,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>3,2=>1),
                        'length'=>array(0=>5,1=>3,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(x\2|(ab))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_13() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>1),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(x\58|(ab))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_14() {
        $test1 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'ababba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'ababbabbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));


        $test4 = array( 'str'=>'ababbabbbabbbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>15,1=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test5 = array( 'str'=>'ababbabbbabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test6 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a|b\1)+',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

        function data_for_test_backrefs_15() {
        $test1 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'ababa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab)\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_16() {
        $test1 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2),
                       'left'=>array(2),
                       'next'=>'a',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab|cd)\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_17() {
        $test1 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>12,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab)+\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_18() {
        $test1 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>12,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'ababacdcdcdcd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab|cd)\1+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_19() {
        $test1 = array('str'=>'abefabababababef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>16,1=>2,2=>2),
                       'left'=>array(0),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        $test2 = array('str'=>'cdghabghghef',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2),
                       'left'=>array(4),
                       'next'=>'c',
                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));

        return array('regex'=>'(ab|cd)(ef|gh)\1+\2',
                     'tests'=>array($test1, $test2));
    }

    // Tests for miscellanious cases: acceptance, unicode properties, long strings, empty strings etc.
    function data_for_test_node_assert_1() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'ab(?=cd)',
                     'tests'=>array($test1));
    }

    function data_for_test_node_assert_2() {
        $test1 = array( 'str'=>'ax',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'abxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'avbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'a(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_node_assert_3() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'(?:a|b|c|d)*(?=abb)(?:a|c)(?:b|d)(?:b|d)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_node_assert_4() {
        $test1 = array( 'str'=>'x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'bxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'vbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_node_assert_5() {
        $test1 = array( 'str'=>'axcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test2 = array( 'str'=>'abxv',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xcvnm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test3 = array( 'str'=>'axacav',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xcvnm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'a(?:(?=[^b])[xcvbnm])+',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_node_cond_subpatt() {
        $test1 = array( 'str'=>'11-aaa-11',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(?(?=[^a-z]*[a-z])\d{2}-[a-z]{3}-\d{2}|\d{2}-\d{2}-\d{2})',
                     'tests'=>array($test1));
    }

    function data_for_test_shortanswer_notation() {
        $test1 = array( 'str'=>'/+fghjhj4587abc*',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>16),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(?:/\+.*abc\*)$',
                     'tests'=>array($test1));
    }

    function data_for_test_zero_length_loop() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'[prefix] a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^*[a-z 0-9](?:\b)+a${1,}',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_empty_string() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a');

        return array('regex'=>'abc',
                     'tests'=>array($test1));
    }

    function data_for_test_backref_to_uncaptured_subpatt() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                        'length'=>array(0=>2,1=>2,2=>NOMATCH),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?:(ab)|(cd))\2',
                     'tests'=>array($test1));
    }

    function data_for_test_circumflex_should_not_be_matched() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'^a',
                     'tests'=>array($test1));
    }

    function data_for_test_unobvious_backslash() {
        $test1 = array( 'str'=>chr(octdec(37)).'8',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\378',
                     'tests'=>array($test1));
    }

    function data_for_test_unicode() {
        $test1 = array( 'str'=>'абв',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'é');

        return array('regex'=>'абвé',
                     'tests'=>array($test1));
    }

    function data_for_test_escape_sequences() {
        $test1 = array( 'str'=>'8X'.qtype_poasquestion_string::code2utf8(0x2000).qtype_poasquestion_string::code2utf8(0x000D).
                               ' ё'.qtype_poasquestion_string::code2utf8(0x000D).qtype_poasquestion_string::code2utf8(0x2000).'_ ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\d\D\h\H\s\S\v\V\w\W',
                     'tests'=>array($test1));
    }

    function data_for_test_q() {
        $test1 = array( 'str'=>'x?+x{3,10}',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'x\Q?+x{3,10}',
                     'tests'=>array($test1));
    }

    function data_for_test_qe_empty() {
        $test1 = array( 'str'=>'xxxxxxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'x\Q\Ex{3,10}',
                     'tests'=>array($test1));
    }

    function data_for_test_qe() {
        $test1 = array( 'str'=>'a + b = c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'a\Q + b\E = c',
                     'tests'=>array($test1));
    }

    function data_for_test_uprops_and_posix_classes() {
        $uch    = qtype_poasquestion_string::code2utf8(0xE01F0);
        $pch    = qtype_poasquestion_string::code2utf8(0x007A);
        $hspace = qtype_poasquestion_string::code2utf8(0x3000);

        $str = '';
        $length = 80;
        for ($i = 0; $i < $length; $i++) {
            $str .= $uch . $pch . $hspace;
        }

        $test1 = array( 'str'=>$str,
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3 * $length),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(10),
                        'next'=>'[\p{C}[:alpha:]\h]');

        return array('regex'=>'^[\p{C}[:alpha:]\h]{10,}',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_uprops_and_posix_negative() {

        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(10),
                        'next'=>'[^\p{C}[:alpha:]\h]');

        $test2 = array( 'str'=>'??????????',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'^[^\p{C}[:alpha:]\h]{10,}',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_uprops_and_posix_negative_negative() {

        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'[^\P{C}[:^alpha:]\H]');

        $test2 = array( 'str'=>'!',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'[^\P{C}[:^alpha:]\H]+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_esc_inside_charset() {

        $test1 = array( 'str'=>'This string contains 4 digits 43 characters and 9 spaces',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>56),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'...',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'[\w\d\s]');

        return array('regex'=>'[\w\d\s]+',
                     'tests'=>array($test1, $test2));
    }

    /*function data_for_test_leaf_assert_G() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'a\Gb',
                     'tests'=>array($test1));
    }*/
}
