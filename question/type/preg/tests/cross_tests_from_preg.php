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
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg {

    // Tests for general cases.
    function data_for_test_concat_1() {
        $test1 = array( 'str'=>'the matcher works',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        );

        $test2 = array( 'str'=>'_the matcher works',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(17),
                        'next'=>'t');

        $test3 = array( 'str'=>'the matcher',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(6),
                        'next'=>' ');

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(17),
                        'next'=>'t');

        return array('regex'=>'^the matcher works',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_concat_2() {
        $test1 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        $test2 = array('str'=>'abcdefg',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7),
                       'left'=>array(2),
                       'next'=>'z');

        $test3 = array('str'=>'abcdeGDDDRER',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'f');

        return array('regex'=>'abcdefgza',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_concat_3() {
        $test1 = array('str'=>'abcdefgza',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>2,4=>5,5=>6,6=>7),
                       'length'=>array(0=>9,1=>9,2=>2,3=>3,4=>4,5=>2,6=>1));

        $test2 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>4,2=>2),
                       'left'=>array(5),
                       'next'=>'e');

        return array('regex'=>'((ab)(cde)(f(g(z))a))',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_concat_4() {
        $test1 = array('str'=>'abef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>'abcdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>4,1=>2));

        $test3 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab|cd)ef',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_concat_5() {
        $test1 = array( 'str'=>'fgh',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');

        $test2 = array( 'str'=>'abce',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d');

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'^abcd$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_concat_6() {
        $test1 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'OacO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'ab',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_concat_7() {
        $test1 = array( 'str'=>'sometextwithoutmatchingandsomeregexwithmatchig',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>26),
                        'length'=>array(0=>9));

        return array('regex'=>'someregex',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_alt_1() {
        $test1 = array( 'str'=>'abcf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'deff',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>'^abc|def$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_alt_2() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'[aceg]');

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_alt_3() {
        $test1 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'A|B|C|D|E|F',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_alt_4() {
        $test1 = array('str'=>'abcdefabc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0),
                       'length'=>array(0=>6,3=>6));

        $test2 = array('str'=>'cdef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>3,2=>3));

        return array('regex'=>'(abc)|(cde)|(abcdef)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_alt_5() {
        $test1 = array('str'=>'DEF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>3,2=>3));

        $test2 = array('str'=>'C',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>'B',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test4 = array('str'=>'A',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'A|(B|C|(DEF))',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_alt_6() {
        $test1 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2));

        $test3 = array( 'str'=>'cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2));

        return array('regex'=>'^(ab|cd)$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_alt_7() {
        $test1 = array( 'str'=>'abcef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3));

        $test2 = array( 'str'=>'abce',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>4, 1=>3),
                        'left'=>array(1),
                        'next'=>'f');

        $test3 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4, 1=>4),
                        'length'=>array(0=>2, 1=>0));

        return array('regex'=>'(abc|)ef',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_charset_1() {
        $test1 = array('str'=>'3',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>'F',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>'7a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'[A-Z0-9]',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_charset_2() {
        $test1 = array('str'=>'aGfQ',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>'0Tdb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>'9Af7',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(1),
                       'next'=>'[^0-9]');

        $test4 = array('str'=>'TTff',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1),
                       'left'=>array(3),
                       'next'=>'A');

        return array('regex'=>'[^A-Z][A-Z][dfg][^0-9]',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_charset_3() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[^b]');

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>'aacde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_metacharacter_dot() {
        $test1 = array( 'str'=>'afc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'^a.c$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_digit() {
        $test1 = array( 'str'=>'273x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>'ax',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d');

        return array('regex'=>'(\d)+x',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_wordchar() {
        $test1 = array( 'str'=>'a_a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'a{a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\w');

        return array('regex'=>'a\wa',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_anchors_1() {
        $test1 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0));

        $test2 = array( 'str'=>'faily days',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'$()^\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_anchors_2() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0));

        $test2 = array( 'str'=>'faily days',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>1,1=>2),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'a$()\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_1() {
        $test1 = array( 'str'=>' abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>' 9bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'  b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[a-z0-9]');

        return array('regex'=>'^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9]',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_2() {
        $test1 = array('str'=>'3=',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>'[A-Z0-5=]\b[0-5A-R=]',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_3() {
        $test1 = array('str'=>'=',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(1),
                       'next'=>'[0-5A-R]');

        return array('regex'=>'\b[0-5A-R]',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_4() {
        $test1 = array('str'=>'AF',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>'[A-Z0-5]\B[0-5A-R]',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_5() {
        $test1 = array('str'=>'ABDEDSGR 0357',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        $test2 = array('str'=>'ABDEDSGR',       // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2),
                       'left'=>array(1),
                       'next'=>' ');

        $test3 = array('str'=>'ABDEDSGR',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8),
                       'left'=>array(1),
                       'next'=>'[0-5 ]');

        return array('regex'=>'[A-Z0-5 ]+\b[0-5A-R ]+',
                     'tests'=>array($test1, /*$test2,*/ $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_6() {
        $test1 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(0),
                       'next'=>'');

        return array('regex'=>'abc$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_7() {
        $test1 = array('str'=>'bc',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'ext_index_first'=>array(0=>0),
                       'ext_length'=>array(0=>1),
                       'left'=>array(1),
                       'next'=>'a');

        $test2 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'a|(b$)c',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_7_1() {
        $test1 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'ext_index_first'=>array(0=>0),
                       'ext_length'=>array(0=>2),
                       'left'=>array(1),
                       'next'=>'\n');

        $test2 = array('str'=>"a\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>'a\Z[c\n]',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_8() {
        $test1 = array('str'=>'abca',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'$abca',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_9() {
        $test1 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                       'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'abc^',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_10() {
        $test1 = array('str'=>'c',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>'ac',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'a|(^)c',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_11() {
        $test1 = array('str'=>'abca',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>'^abca',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_12() {
        $test1 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'^ab',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_13() {
        $test1 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'ab$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_simple_14() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA, qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    /*function data_for_test_assertions_simple_15() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\Aab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_16() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_17() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\Aab\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_18() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|)ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_19() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab(\Z|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_20() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|)ab(\Z|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_21() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));
/*
        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);
*
        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);
/*
        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);
*
        return array('regex'=>'\A(a|)',
                     'tests'=>array($test1, /*$test2,* $test3/*, $test4*),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_22() {

        /*$test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);*

        return array('regex'=>'(a|)\A',
                     'tests'=>array(/*$test1, $test2, $test3, $test4*),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_23() {

        /*$test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);*

        return array('regex'=>'(a|)\Z',
                     'tests'=>array(/*$test1, $test2, $test3, $test4*),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_24() {

        /*$test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);*

        return array('regex'=>'\Z(a|)',
                     'tests'=>array(/*$test1, $test2, $test3, $test4*),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_25() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\A(a|)\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_26() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\Zab\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_27() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\Z|)ab(\A|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_28() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\Zab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_29() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_30() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\Z|)ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_31() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab(\A|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_32() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test5 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test6 = array( 'str'=>'Oabab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test7 = array( 'str'=>'ababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test8 = array( 'str'=>'OababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\Aab',
                     'tests'=>array($test1, $test2, $test3, $test4,
                                    $test5, $test6, $test7, $test8),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_33() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test5 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test6 = array( 'str'=>'Oabab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test7 = array( 'str'=>'ababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test8 = array( 'str'=>'OababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\Zab',
                     'tests'=>array($test1, $test2, $test3, $test4,
                                    $test5, $test6, $test7, $test8),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_34() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|a)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_35() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(a|\A)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_36() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\Z|a)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_37() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(a|\Z)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_38() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(ab)\Z*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_39() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(ab)\A*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_40() {


        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\Z*(ab)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_41() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\A*(ab)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_42() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|a)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_43() {

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'Oa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OaO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\Z|a)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_44() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'(\A|\Z)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_45() {

        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'Oabab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'ababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\bab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_46() {

        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>'Oabab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'ababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OababO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test5 = array( 'str'=>'ab ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test6 = array( 'str'=>'Oab ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test7 = array( 'str'=>'ab abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test8 = array( 'str'=>'Oab abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'ab\Bab',
                     'tests'=>array($test1, $test2, $test3, $test4,
                                    $test5, $test6, $test7, $test8),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_47() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|$)*ab(\Z|^)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_48() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|^)*ab(\Z|$)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_49() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(\A|\1)*ab(\Z|\2)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_50() {

        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test4 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(^|\1)*ab($|\2)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }*/
/*
    function data_for_test_assertions_simple_51() {

        return array('regex'=>'a^b$c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_52() {

        return array('regex'=>'a$b^c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_53() {

        return array('regex'=>'a^$c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_54() {

        return array('regex'=>'a$^c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_55() {

        return array('regex'=>'(\A|a|\Z|\1|^|$)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_56() {

        return array('regex'=>'(\A*|a*|\Z*|\1*|^*|$*)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_57() {

        return array('regex'=>'(\A(\A|)*|)*ab(\Z(\Z|)*|)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_58() {

        return array('regex'=>'(a|b|\5)(\A)(a|b|\5)(\Z)(\2|\4)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_59() {

        return array('regex'=>'\A\"\\n\"ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_60() {

        return array('regex'=>'\"\\n\"ab\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_61() {

        return array('regex'=>'\Z\"\\n\"ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_62() {

        return array('regex'=>'\"\\n\"ab\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_63() {

        return array('regex'=>'$\"\\n\"ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_64() {

        return array('regex'=>'\"\\n\"ab$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_65() {

        return array('regex'=>'^\"\\n\"ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_66() {

        return array('regex'=>'\"\\n\"ab^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_67() {

        return array('regex'=>'\Aab\"\\n\"',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_68() {

        return array('regex'=>'ab\"\\n\"\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_69() {

        return array('regex'=>'\Zab\"\\n\"',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_70() {

        return array('regex'=>'ab\"\\n\"\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_71() {

        return array('regex'=>'$ab\"\\n\"',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_72() {

        return array('regex'=>'ab\"\\n\"$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_73() {

        return array('regex'=>'^ab\"\\n\"',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_74() {

        return array('regex'=>'ab\"\\n\"^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_75() {

        return array('regex'=>'\Aab\"\\n\"\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_76() {

        return array('regex'=>'\Zab\"\\n\"\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_77() {

        return array('regex'=>'\Zab\"\\n\"\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_78() {

        return array('regex'=>'\Aab\"\\n\"\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_79() {

        return array('regex'=>'$ab\"\\n\"^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_80() {

        return array('regex'=>'^ab\"\\n\"$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_81() {

        return array('regex'=>'^ab\"\\n\"$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_82() {

        return array('regex'=>'$ab\"\\n\"^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_83() {

        return array('regex'=>'\A(ab\"\\n\")*\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_84() {

        return array('regex'=>'\Z(ab\"\\n\")*\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_85() {

        return array('regex'=>'\Z(ab\"\\n\")*\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_86() {

        return array('regex'=>'\A(ab\"\\n\")*\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_87() {

        return array('regex'=>'$(ab\"\\n\")*^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_88() {

        return array('regex'=>'^(ab\"\\n\")*$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_89() {

        return array('regex'=>'^(ab\"\\n\")*$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_90() {

        return array('regex'=>'\A((ab\"\\n\")*|)\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_91() {

        return array('regex'=>'\Z((ab\"\\n\")*|)\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_92() {

        return array('regex'=>'\Z((ab\"\\n\")*|)\A',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_93() {

        return array('regex'=>'\A((ab\"\\n\")*|)\Z',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_94() {

        return array('regex'=>'$((ab\"\\n\")*|)^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_95() {

        return array('regex'=>'^((ab\"\\n\")*|)$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_96() {

        return array('regex'=>'^((ab\"\\n\")*|)$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_97() {

        return array('regex'=>'$((ab\"\\n\")*|)^',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_98() {

        return array('regex'=>'((\Z(ab\"\\n\")*)|
                                (^cd\"\\n\"de)|
                                ($ef\"\\n\"fg)|
                                (\Agh\"\\n\"hi))+
                                bc',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_99() {

        return array('regex'=>'((\b(ab\"\\n\")*)|
                                (\Bcd\"\\n\"de))+
                                bc',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_assertions_simple_100() {

        return array('regex'=>'(a(\"\\n\")*b)\b\1\B\1',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }*/

    function data_for_test_case_sensitivity1() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'B');

        $test2 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'c');

        return array('regex'=>'aBcD',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity2() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'aBcD',
                     'modifiers'=>'i',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity3() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test3 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));


        $test4 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        return array('regex'=>'(?:a(?i)b|c)',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity4() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8));

        $test2 = array( 'str'=>'abcdEFgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8));

        $test3 = array( 'str'=>'abcdEFGH',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(2),
                        'next'=>'g');

        $test4 = array( 'str'=>'abCDEFgh',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(6),
                        'next'=>'c');

        return array('regex'=>'ab(?:cd(?i)ef)gh',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity5() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>8,1=>4));

        $test2 = array( 'str'=>'abcdEFgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>8,1=>4));

        $test3 = array( 'str'=>'abcdEFGH',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>6,1=>4),
                        'left'=>array(2),
                        'next'=>'g');

        $test4 = array( 'str'=>'abCDEFgh',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(6),
                        'next'=>'c');

        return array('regex'=>'ab(cd(?i)ef)gh',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity6() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8));

        $test2 = array( 'str'=>'abCDefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8));

        $test3 = array( 'str'=>'abCDEFgh',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(4),
                        'next'=>'e');

        $test4 = array( 'str'=>'ABcdEFgh',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(4),
                        'next'=>'e');

        return array('regex'=>'ab(?:cd(?-i)ef)gh',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity7() {
        $test1 = array( 'str'=>'lowerUPPERlowerMiXeDMiXeD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>10,3=>15),
                        'length'=>array(0=>25,1=>10,2=>5,3=>10));

        return array('regex'=>'(?i)([[:lower:]]{10})(?-i)([[:lower:]]{5})(?i)((?:[[:upper:]][[:lower:]]){5})',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity8() {
        $test1 = array( 'str'=>'lowerUPPER',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        return array('regex'=>'\p{Ll}+',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_case_sensitivity9() {
        $test1 = array( 'str'=>'lowerUPPER',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10));

        return array('regex'=>'(?i)\p{Ll}+',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_extended_local() {
        $test1 = array( 'str'=>'abcdefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7));

        $test2 = array( 'str'=>'ab: c   d  e fg',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(5),
                        'next'=>'c');

        return array('regex'=>'ab(?x: c   d  e )fg',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    // Tests for cases with ambiguity - subexpressions, quantifiers and backreferences.
    function data_for_test_empty_match() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4));

        $test2 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0));

        return array('regex'=>'(abcd|)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_nested() {
        $test1 = array( 'str'=>'abcbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3,3=>4),
                        'length'=>array(0=>6,1=>4,2=>2,3=>1));

        $test2 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // The quantifier is outside subexpressions 2 and 3 so they are not matched!
                        'length'=>array(0=>2,1=>0));

        return array('regex'=>'^a((b(c)(?:\b|\B))*)d$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_with_quant_nested() {
        $test1 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(2),
                        'next'=>'.');

        $test2 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(2),
                        'next'=>'.');

        return array('regex'=>'[+\-]?([0-9]+)?\.([0-9]+)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_concatenated() {
        $test1 = array( 'str'=>'_abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>3,3=>5),
                        'length'=>array(0=>6,1=>2,2=>2,3=>2));

        $test2 = array( 'str'=>'[prefix] abef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>9,1=>9,3=>11),
                        'length'=>array(0=>4,1=>2,3=>2));

        return array('regex'=>'(ab)(cd)?(ef)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_alternated() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>'((ab)|(cd)|(efgh))',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_quantifier_inside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5));

        return array('regex'=>'(a*)',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_quantifier_outside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1));

        return array('regex'=>'(a)*',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_tricky() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>3,3=>3,4=>3),
                        'length'=>array(0=>3,1=>0,2=>0,3=>0,4=>0));

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6,2=>6,3=>6,4=>6),
                        'length'=>array(0=>6,1=>0,2=>0,3=>0,4=>0));

        return array('regex'=>'(([a*]|\b)([b*]|\b)([c*]|\b))+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_nested_and_concatenated() {
        $test1 = array( 'str'=>'zw',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,3=>1),
                        'length'=>array(0=>2,1=>1,3=>1));

        $test2 = array( 'str'=>'*&^%&^',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'z');

        return array('regex'=>'(z|y(x))(w)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_duplicate_simple() {
        $test1 = array( 'str'=>'cat-cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1));

        $test2 = array( 'str'=>'dog-dogs',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1));

        return array('regex'=>'(?|(cat)|(dog))-\1(s)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_duplicate() {
        $test1 = array( 'str'=>'abee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,4=>2),
                        'length'=>array(0=>4,1=>1,2=>1,4=>1));

        $test2 = array( 'str'=>'acdee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                        'length'=>array(0=>5,1=>1,2=>1,3=>1,4=>1));

        return array('regex'=>'(a)(?|(b)|(c)(d))(e)\4',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_duplicate_with_modifier_J_1() {
        $test1 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>'(?|(?<name>a)(?<name>b))\2',
                     'tests'=>array($test1, $test2),
                     'modifiers'=>'J',
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_duplicate_with_modifier_J_2() {
        $test1 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1));

        $test2 = array( 'str'=>'abcc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>2),
                        'length'=>array(0=>4,1=>1,2=>1));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'z');

        return array('regex'=>'(?|(?<qwe>a)z|a(b)(?|(?<qwe>c)))\2?',
                     'tests'=>array($test1, $test2, $test3),
                     'modifiers'=>'J',
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_subexpr_named() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>3));

        return array('regex'=>'(?P<name>abc)\1\g{name}',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_qu() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_aster_1() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),    // Less characters left.
                        'next'=>'[ab]');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>76));

        return array('regex'=>'(?:a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_aster_2() {
        $test1 = array( 'str'=>'abcabcabcabcabcabcabcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30));

        $test2 = array( 'str'=>'abcabcabcabcabcabcabcabcabcab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27));

        $test3 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0));

        $test4 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0));

        return array('regex'=>'(?:abc)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_aster_3() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>47));

        return array('regex'=>'^ab*c$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_quant_aster_4() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>72),
                        'length'=>array(0=>76, 1=>1));

        return array('regex'=>'^(a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_quant_aster_5() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[ab]');

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a');

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76));

        return array('regex'=>'(?:a|b|c|d)*abb',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_quant_aster_6() {
        $test1 = array( 'str'=>'aabbbabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
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
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));

        return array('regex'=>'[ab]*[ac]bb',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_aster_8() {
        $test1 = array( 'str'=>'@W#G%9bb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8));

        return array('regex'=>'.*\wbb',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_quant_aster_9() {
        $test1 = array( 'str'=>'aaa_aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7));

        return array('regex'=>'(?:\w)*a',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_quant_plus_1() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>101));

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_plus_2() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        $test2 = array('str'=>'abcbccbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>11,1=>1));

        return array('regex'=>'a(b|c)+b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_plus_3() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1));

        $test2 = array('str'=>'abcdaaaabcdZz',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>13,1=>1),
                       'ext_index_first'=>array(0=>0,1=>12),
                       'ext_length'=>array(0=>15,1=>1),
                       'left'=>array(2),
                       'next'=>'A');

        return array('regex'=>'abcd([a-z]|[A-Z])+Az',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_plus_4() {
        $test1 = array('str'=>'abbbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>5));

        $test2 = array('str'=>'abbbbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        return array('regex'=>'a(b+|c)b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_plus_5() {
        $test1 = array('str'=>'aaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        return array('regex'=>'(?:a+a)+a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_plus_6() {
        $test1 = array('str'=>'aaaaaaaaaaa',    // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(1),
                       'next'=>'b');

        $test2 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(1),
                       'next'=>'b');

        $test3 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        return array('regex'=>'(?:a+a)+b',
                     'tests'=>array(/*$test1,*/ $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_1() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>36),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_brace_finite_2() {
        $test1 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        $test2 = array('str'=>'abacd',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b');

        $test3 = array('str'=>'abacd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'b');

        return array('regex'=>'ab+[a-z]{1,6}bacd',
                     'tests'=>array($test1, /*$test2,*/ $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_3() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        $test2 = array('str'=>'abcbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        return array('regex'=>'a(b|c){3,4}b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_4() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>14,1=>1));

        $test2 = array('str'=>'abcdAz',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>5,1=>1),
                       'left'=>array(3),
                       'next'=>'[a-zA-Z]');

        $test3 = array('str'=>'abcdAz',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>6,1=>1),
                       'left'=>array(3),
                       'next'=>'[a-zA-Z]');

        return array('regex'=>'abcd([a-z]|[A-Z]){3,10}Az',
                     'tests'=>array($test1, /*$test2,*/ $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_6() {
        $test1 = array('str'=>'abbbbb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        $test2 = array('str'=>'abbcccbcbcbbcb',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>14,1=>1));

        $test3 = array('str'=>'abcb',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1),
                       'left'=>array(1),
                       'next'=>'b');

        return array('regex'=>'a(b|c){3,20}b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_7() {
        $test1 = array('str'=>'abcdAADFEDAzAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14));

        $test2 = array('str'=>'abcdaAz',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>'abcd(?:[a-z]|[A-Z]){0,10}Az',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_8() {
        $test1 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>'(?:a{3,5}a)a',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_9() {
        $test1 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>'aaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test3 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        return array('regex'=>'(?:a{3,5}a)+a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_finite_10() {
        $test1 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,            // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(1),
                       'next'=>'b');

        $test2 = array('str'=>'aaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11),
                       'left'=>array(1),
                       'next'=>'b');

        $test3 = array('str'=>'aaaaaaaaaab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        return array('regex'=>'(?:a{3,5}a)+b',
                     'tests'=>array(/*$test1,*/ $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_quant_brace_infinite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27));

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>104));

        return array('regex'=>'^ab{15,}c$',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_1() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(1),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'a+?',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_2() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(1),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'a*?a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_3() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(2),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(1),
                       'next'=>'a');

        $test3 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>'a+?a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_4() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(3),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(2),
                       'next'=>'a');

        $test3 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>'a{2,}?a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_5() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(3),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(2),
                       'next'=>'a');

        $test3 = array('str'=>'aaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>'a{2,10}?a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_lazy_6() {
        $test1 = array('str'=>'',
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array(),
                       'left'=>array(1),
                       'next'=>'a');

        $test2 = array('str'=>'a',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>'aaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>'a??a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_quant_greedy() {
        $test1 = array('str'=>'abacd',
                       'is_match'=>true,    // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b');

        $test2 = array('str'=>'abacd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5),
                       'left'=>array(4),
                       'next'=>'b');

        $test3 = array('str'=>'ababac',
                       'is_match'=>true,    // TODO: TAGS - BACKTRACKING-SPECIFIC
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3),
                       'left'=>array(4),
                       'next'=>'b');

        $test4 = array('str'=>'ababac',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6),
                       'ext_index_first'=>array(0=>0),
                       'ext_length'=>array(0=>7),
                       'left'=>array(1),
                       'next'=>'d');

        $test5 = array('str'=>'abbbbbacd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        return array('regex'=>'ab+[a-z]*bacd',
                     'tests'=>array(/*$test1,*/ $test2, /*$test3,*/ $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_characters_left_simple() {
        $test1 = array( 'str'=>'ab cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>' ');

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'next'=>'b');

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a');

        return array('regex'=>'ab\b cd',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_characters_left() {
        $test1 = array( 'str'=>'abefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>3));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'h');

        $test3 = array( 'str'=>'abe',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'ext_index_first'=>array(0=>0,1=>2),
                        'ext_length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'h');

        return array('regex'=>'ab(cd|efg|h)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_1() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>12,1=>6,2=>3));

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>6,1=>6,2=>3),
                        'ext_index_first'=>array(0=>0,1=>0,2=>0),
                        'ext_length'=>array(0=>12,1=>6,2=>3),
                        'left'=>array(6),
                        'next'=>'a');    // Backref #1 not captured at all.

        $test3 = array( 'str'=>'abcabcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>8,1=>6,2=>3),
                        'ext_index_first'=>array(0=>0,1=>0,2=>0),
                        'ext_length'=>array(0=>12,1=>6,2=>3),
                        'left'=>array(4),
                        'next'=>'c');    // Backref #1 captured partially.

        return array('regex'=>'((abc)\2)\1',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_2() {
        $test1 = array( 'str'=>'abcdefghidef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>12,1=>9,2=>3));

        return array('regex'=>'(abc(def)ghi)\g{-1}',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_3() {
        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,3=>2),
                        'length'=>array(0=>4,1=>2,3=>2));

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0,3=>2),
                        'length'=>array(0=>4,2=>2,3=>2));

        $test3 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'b');

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'(?:(ab)|(cd))(\1|\2)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_4() {
        $test1 = array( 'str'=>'ababcdababcdababcdababcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>12,2=>12),
                        'length'=>array(0=>24,1=>6,2=>2));

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0,1=>0,2=>0),
                        'ext_length'=>array(0=>12,1=>6,2=>2),
                        'left'=>array(12),
                        'next'=>'a');

        return array('regex'=>'((ab)\2cd)*\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_5() {
        $test1 = array( 'str'=>'abcdabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>4));

        $test2 = array( 'str'=>'abcdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>4),
                        'left'=>array(2),
                        'next'=>'c');

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(4),
                        'next'=>'a');

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(5),
                        'next'=>'d');

        return array('regex'=>'(abcd)\1',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_6() {
        $test1 = array( 'str'=>'abxyabab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>2));

        $test2 = array( 'str'=>'abxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>2));

        $test3 = array( 'str'=>'cdxyabab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>6),
                        'left'=>array(2),
                        'next'=>'c');

        $test4 = array( 'str'=>'cdxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6));

        return array('regex'=>'(?:(ab)|cd)xy(?:ab\1|cd)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_7() {
        $test1 = array( 'str'=>'Do hats eat cats?',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(12),
                        'next'=>'[cbr]');

        $test2 = array( 'str'=>'Do cats',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'ext_index_first'=>array(0=>0,1=>6),
                        'ext_length'=>array(0=>15,1=>0),
                        'left'=>array(9),
                        'next'=>' ');

        $test3 = array( 'str'=>'bat eat fat?',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(15),
                        'next'=>'D');

        return array('regex'=>'Do (?:[cbr]at(s|)) eat (?:[cbr]at\1)\?',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_8() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(9),
                        'next'=>'a');

        $test2 = array( 'str'=>'0as',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(8),
                        'next'=>'b');

        $test3 = array( 'str'=>'0defab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'ext_index_first'=>array(0=>0,1=>1),
                        'ext_length'=>array(0=>10,1=>3),
                        'left'=>array(9),
                        'next'=>'a');

        return array('regex'=>'0(abc|defghx)[0-9]{3}\1',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_9() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(13),
                        'next'=>'a');

        $test2 = array( 'str'=>'0aaaaaaz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>7,1=>6),
                        'ext_index_first'=>array(0=>0,1=>1),
                        'ext_length'=>array(0=>14,1=>5),
                        'left'=>array(8),
                        'next'=>'[0-9]');

        return array('regex'=>'0(a{5,10})[0-9]{3}\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_10() {
        $test1 = array('str'=>'aaaaaaaaaaaaa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>12),
                       'length'=>array(0=>13,1=>1));

        return array('regex'=>'(a|\1)+',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_backrefs_11() {
        $test1 = array( 'str'=>'ababba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>3,1=>1),
                        'left'=>array(2),
                        'next'=>'x');

        $test2 = array( 'str'=>'ababbaxbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>10,1=>3));

        $test3 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>3,1=>1),
                        'left'=>array(2),
                        'next'=>'x');

        return array('regex'=>'(a|b\1)+x\1',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_12() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>0),
                        'length'=>array(0=>5,1=>3,2=>2));

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>3,2=>1),
                        'length'=>array(0=>5,1=>3,2=>2));

        return array('regex'=>'(x\2|(ab))+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_13() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>2));

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>1),
                        'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>'(x\58|(ab))+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_14() {
        $test1 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1));

        $test2 = array( 'str'=>'ababba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3));

        $test3 = array( 'str'=>'ababbabbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4));


        $test4 = array( 'str'=>'ababbabbbabbbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>15,1=>5));

        $test5 = array( 'str'=>'ababbabbbabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4));

        $test6 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'(a|b\1)+',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_backrefs_15() {
        $test1 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>'ababa',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab)\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_backrefs_16() {
        $test1 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>'abcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2),
                       'ext_index_first'=>array(0=>0,1=>0),
                       'ext_length'=>array(0=>4,1=>2),
                       'left'=>array(2),
                       'next'=>'a');

        return array('regex'=>'(ab|cd)\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_backrefs_17() {
        $test1 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>12,1=>2));

        $test2 = array('str'=>'abab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab)+\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_backrefs_18() {
        $test1 = array('str'=>'abababababab',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>12,1=>2));

        $test2 = array('str'=>'ababacdcdcdcd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab|cd)\1+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    function data_for_test_backrefs_19() {
        $test1 = array('str'=>'abefabababababef',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>16,1=>2,2=>2));

        $test2 = array('str'=>'cdghabghghef',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2),
                       'left'=>array(4),
                       'next'=>'c');

        return array('regex'=>'(ab|cd)(ef|gh)\1+\2',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_BACKTRACKING));
    }

    // Tests for miscellanious cases: acceptance, unicode properties, long strings, empty strings etc.
    function data_for_test_node_assert_1() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'ab(?=cd)',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_node_assert_2() {
        $test1 = array( 'str'=>'ax',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'abxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test3 = array( 'str'=>'avbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'a(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_node_assert_3() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a');

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76));

        return array('regex'=>'(?:a|b|c|d)*(?=abb)(?:a|c)(?:b|d)(?:b|d)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_node_assert_4() {
        $test1 = array( 'str'=>'x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'bxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>'vbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_node_assert_5() {
        $test1 = array( 'str'=>'axcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>'abxv',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xcvnm]');

        $test3 = array( 'str'=>'axacav',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xcvnm]');

        return array('regex'=>'a(?:(?=[^b])[xcvbnm])+',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_node_cond_subexpr() {
        $test1 = array( 'str'=>'11-aaa-11',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9));

        return array('regex'=>'(?(?=[^a-z]*[a-z])\d{2}-[a-z]{3}-\d{2}|\d{2}-\d{2}-\d{2})',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_shortanswer_notation() {
        $test1 = array( 'str'=>'/+fghjhj4587abc*',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>16));

        return array('regex'=>'^(?:/\+.*abc\*)$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_zero_length_loop_1() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'[prefix] a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8),
                        'length'=>array(0=>2));

        return array('regex'=>'^*[a-z 0-9](?:\b)+a${1,}',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_zero_length_loop_2() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        );

        return array('regex'=>'((b)?$)+',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_zero_length_loop_with_backref() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>0));

        $test2 = array( 'str'=>'[prefix] a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8,1=>9),
                        'length'=>array(0=>2,1=>0));

        return array('regex'=>'^*[a-z 0-9](\b)+\1a${1,}\1',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_empty_string() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');

        return array('regex'=>'abc',
                     'tests'=>array($test1));
    }

    function data_for_test_backref_to_uncaptured_subexpr() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'ext_index_first'=>array(0=>0,2=>0),
                        'ext_length'=>array(0=>4,2=>2),
                        'left'=>array(4),
                        'next'=>'c');

        return array('regex'=>'(?:(ab)|(cd))\2',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_circumflex_should_not_be_matched() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'^a',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_unobvious_backslash() {
        $test1 = array( 'str'=>chr(octdec(37)).'8',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'\378',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
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
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_escape_sequences() {
        $test1 = array( 'str'=>'8X'.qtype_preg_unicode::code2utf8(0x2000).qtype_preg_unicode::code2utf8(0x000D).
                               ' ё'.qtype_preg_unicode::code2utf8(0x000D).qtype_preg_unicode::code2utf8(0x2000).'_ ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10));

        return array('regex'=>'\d\D\h\H\s\S\v\V\w\W',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_q() {
        $test1 = array( 'str'=>'x?+x{3,10}',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10));

        return array('regex'=>'x\Q?+x{3,10}',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_qe_empty() {
        $test1 = array( 'str'=>'xxxxxxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11));

        return array('regex'=>'x\Q\Ex{3,10}',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_qe() {
        $test1 = array( 'str'=>'a + b = c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9));

        return array('regex'=>'a\Q + b\E = c',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_uprops_and_posix_classes() {
        $uch    = qtype_preg_unicode::code2utf8(0xE01F0);
        $pch    = qtype_preg_unicode::code2utf8(0x007A);
        $hspace = qtype_preg_unicode::code2utf8(0x3000);

        $str = '';
        $length = 80;
        for ($i = 0; $i < $length; $i++) {
            $str .= $uch . $pch . $hspace;
        }

        $test1 = array( 'str'=>$str,
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3 * $length));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(10),
                        'next'=>'[\p{C}[:alpha:]\h]');

        return array('regex'=>'^[\p{C}[:alpha:]\h]{10,}',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_uprops_and_posix_negative() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(10),
                        'next'=>'[^\p{C}[:alpha:]\h]');

        $test2 = array( 'str'=>'??????????',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10));

        return array('regex'=>'^[^\p{C}[:alpha:]\h]{10,}',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_uprops_and_posix_negative_negative_1() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^\P{L}[:^alpha:]\W]');

        $test2 = array( 'str'=>'!',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^\P{L}[:^alpha:]\W]');

        $test3 = array( 'str'=>'alnum characters here',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0));

        return array('regex'=>'[^\P{L}[:^alpha:]\W]+',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_uprops_and_posix_negative_negative_2() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'[^\P{C}[:^alpha:]\H]+',  // This is an empty charset
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_esc_inside_charset() {
        $test1 = array( 'str'=>'This string contains 4 digits 43 characters and 9 spaces',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>56));

        $test2 = array( 'str'=>'...',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\w\d\s]');

        return array('regex'=>'[\w\d\s]+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_big_quantifier() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0),
                        'length'=>array(500));
        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'a{1,500}',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_zero_quantifier() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0),
                        'length'=>array(1));
        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0),
                        'length'=>array(0));
        $test3 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0),
                        'length'=>array(0));

        return array('regex'=>'a|b{0}',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_many_quantifiers_1() {
        $test1 = array( 'str'=>'Circle.Radius = 25; Circle.Center.point2d.X = 5; Circle.Center.point2d.Y = 0;',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0),
                        'length'=>array(77));
        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(69),
                        'next'=>'C');

        return array('regex'=>'\s*Circle\s*\.\s*Radius\s*=\s*25\s*;\s*Circle\s*\.\s*Center\s*\.\s*point2d\s*\.\s*X\s*=\s*5;\s*Circle\s*\.\s*Center\s*\.\s*point2d\s*\.\s*Y\s*=\s*0;\s*',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_many_quantifiers_2() {
        $test1 = array( 'str'=>"char str[][3]={{'д','е','ё'},{'ж','з','и'},{'й','к','л'},{'м','н','о'}};",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>72,1=>0));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(72),
                        'next'=>'c');

        return array('regex'=>"char\s+str\s*\[\s*(4|)\s*\]\s*\[\s*3\s*\]\s*=\s*{\s*{\s*'д'\s*,\s*'е'\s*,\s*'ё'\s*}\s*,\s*{\s*'ж'\s*,\s*'з'\s*,\s*'и'\s*}\s*,\s*{\s*'й'\s*,\s*'к'\s*,\s*'л'\s*}\s*,\s*{\s*'м'\s*,\s*'н'\s*,\s*'о'\s*}\s*}\s*;+",
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_multiline_regex_in_simple_mode() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>"a\nb",
                     'tests'=>array($test1));
    }

    function data_for_test_brute_force_methods_perfomance_1() {
        $test1 = array( 'str'=>"aaaa",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>"a{1,200}(a{1,200})?\\1a{0,200}",
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_brute_force_methods_perfomance_2() {
        $test1 = array( 'str'=>"aaaa",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(160),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>"a{40,80}(a{40,80})?\\1a{40,80}",
                     'tests'=>array(/*$test1,*/ $test2));   // test1 runs sooooo slooooow
    }

    function data_for_test_fast_methods_perfomance_1() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(300),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>"a{100,200}(ab){100,200}",
                     'tests'=>array($test1));
    }

    function data_for_test_subexpr_call_1() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(2),
                        'next'=>'a');

        $test3 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab)(?1)',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_subexpr_call_2() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        return array('regex'=>'(a){0}(?1)',
                     'tests'=>array($test1));
    }

    function data_for_test_subexpr_call_case_sensitivity() {
        $test1 = array( 'str'=>'abAB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(2),
                        'next'=>'a');

        $test2 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2));

        return array('regex'=>'(ab)(?i:(?1))',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_recursion_1() {
        $test1 = array( 'str'=>'[]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'[[]]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2));

        $test3 = array( 'str'=>'[[[]]]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>6,1=>4));

        return array('regex'=>'\[((?R))?\]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_recursion_2() {
        $test1 = array( 'str'=>'[',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'[[',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'[a[',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'[a[a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'\[[a-z](?R)?\]',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_recursion_3() {
        $test1 = array( 'str'=>'[a[a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'\]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'[a[a]',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>6,1=>6),
                        'left'=>array(1),
                        'next'=>'\]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(\[[a-z](?1)?\])$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_recursion_4() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>1,1=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'bbbbbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>8),
                        'length'=>array(0=>8,1=>8,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(b(?1)?)(b*)$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_recursion_5() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>1,1=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'bbbbbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>8),
                        'length'=>array(0=>8,1=>8,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(b*)(b?(?2)?)$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_recursion_6() {
        $test1 = array( 'str'=>'((b))',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>5,1=>5,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'((((b))))',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'^(\((?:(?-1)|    (\((?:(?-1)|b)\))   )\))$',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_cond_subexpr_subexpr_generation() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'e');

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'e');

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>4,1=>3),
                        'left'=>array(1),
                        'next'=>'d');

        return array('regex'=>'(abc)?(?(1)d|e)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_cond_subexpr_recursion_generation() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'1');

        $test2 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1));

        $test3 = array( 'str'=>'1abcde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'1abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>7));

        return array('regex'=>'^((?(R1)abcdef|1)(?1)?)$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));
    }

    function data_for_test_leaf_assert_G() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'a\Gb',
                     'tests'=>array($test1));
    }
}
