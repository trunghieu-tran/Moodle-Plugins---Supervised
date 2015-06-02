<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg_merging {

    // From NFA.
    function data_for_test_assertions_simple_2() {
        $test1 = array( 'str'=>'abc?z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'abcaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'!');

        return array('regex'=>'^abc[a-z.?!]\b[a-zA-Z]',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // For asserts with modifiers.
    function data_for_test_assertions_modifier_1() {
        $test1 = array( 'str'=>"abc\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a');

        $test3 = array( 'str'=>'abcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)\Aabc\n^a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_2() {
        $test1 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        $test3 = array( 'str'=>"kl\nab\nab\nab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>5));

        $test4 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');
        return array('regex'=>'(?m)^ab\n^ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_3() {
        $test1 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');


        $test3 = array( 'str'=>"kl\nab\nab\nab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>5));


        $test4 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');

        return array('regex'=>'(?m)^ab$\n^ab',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_4() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        );

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        return array('regex'=>'(?m)\n^',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_5() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1));

        return array('regex'=>'(?m)$\n',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_6() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        );

        return array('regex'=>'(?m)$\n^',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_7() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?m)$a^',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_8() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');

        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\n');

        $test4 = array( 'str'=>"a\nb\n\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'');

        return array('regex'=>'(?m)a\nb\Z\n',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_9() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));


        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');

        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a\nb\Z',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_10() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');

        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a\nb\z',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_11() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=> qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?D)a$\n',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_12() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));


        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);


        return array('regex'=>'(?m)a[a-z0-9\n]^b',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_assertions_modifier_13() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$[ab0-9\n]b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_14() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$[ab0-9\n]^b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_15() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?m)a$[ab0-9]^b',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_16() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>0));

        $test3 = array( 'str'=>"b\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        $test4 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>0));

        $test5 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(1),
                        'next'=>'\n');

        return array('regex'=>'(?m)a(b|$)\n',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_17() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>"b\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        $test4 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test5 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        return array('regex'=>'(?m)a(b|$\n)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_18() {
        $test1 = array( 'str'=>"a\nbc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>"a\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>0));

        $test3 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>0),
                        'left'=>array(1),
                        'next'=>'c');

        $test4 = array( 'str'=>"a\nb\nс",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'(?m)a\n(b|^)c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_19() {
        $test1 = array( 'str'=>"aab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test4 = array( 'str'=>"aab\nab\nab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        return array('regex'=>'(?m)a(ab\n)?',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_20() {
        $test1 = array( 'str'=>"ab\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'c');

        $test4 = array( 'str'=>"ab\nab\nab\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>6),
                        'length'=>array(0=>10, 1=>3));

        return array( 'regex'=>'(?m)(ab$\n)*c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_21() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>3));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');

        $test4 = array( 'str'=>"ab\nab\nab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>6),
                        'length'=>array(0=>9, 1=>3));

        return array('regex'=>'(?m)(^ab$\n)+',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_22() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test2 = array( 'str'=>"\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a');

        $test4 = array( 'str'=>"\na",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        return array('regex'=>'(?m)[a-z\n](^a$\n |)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_23() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test2 = array( 'str'=>"\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));


        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        $test4 = array( 'str'=>"\na",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        return array('regex'=>'(?m)\n(^|)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_24() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>0, 1=>-1));

        $test2 = array( 'str'=>"a\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>2));

        $test3 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>0, 1=>-1));

        $test4 = array( 'str'=>"\na\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>0, 1=>-1));

        return array('regex'=>'(?m)(^a$\n)*',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_25() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1));

        $test2 = array( 'str'=>"\nab\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b');

        $test4 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>-1),
                        'length'=>array(0=>1, 1=>0, 2=>-1));

        $test5 = array( 'str'=>"ab\n\n\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>4),
                        'length'=>array(0=>5, 1=>1, 2=>1));

        return array('regex'=>'(?m)\A(^a|)b($\n)*\z',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_modifier_26() {
        $test1 = array( 'str'=>"abc\nab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test3 = array( 'str'=>'abcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?D)\Aabc\n$a',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_1() {
        $test1 = array( 'str'=>"\tc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'\t\bc',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_2() {
        $test1 = array( 'str'=>"c\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'c\b\t',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_3() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'a\bc',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_4() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"dog\ncat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        return array('regex'=>'(?m)\bcat',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_5() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"cat\ndog",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>"cat dog",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'cat\b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_6() {
        $test1 = array( 'str'=>"b\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4));

        return array('regex'=>'\b\tcat',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_7() {
        $test1 = array( 'str'=>"cat\tb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'cat\t\b',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_8() {
        $test1 = array( 'str'=>"\t",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\t\Bc',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_9() {
        $test1 = array( 'str'=>'c',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'c\B\t',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_10() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'a b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'a\Bc',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_11() {
        $test1 = array( 'str'=>'ccat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>' ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'\w');

        return array('regex'=>'\Bcat',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_12() {
        $test1 = array( 'str'=>'catt',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'c b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'a');

        return array('regex'=>'cat\B',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_13() {
        $test1 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>" \tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>"\n\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4));

        return array('regex'=>'(?m)\B\tcat',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_14() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>"cat\t ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>"cat\t\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'(?m)cat\t\B',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_15() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'^\bcat\b$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_16() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'^c\Ba\Bt$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_17() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'^\bc\Ba\Bt\b$',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_18() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'^\Bc\Ba\bt\b$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_19() {
        $test1 = array( 'str'=>'a+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[+]');

        $test3 = array( 'str'=>'?c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test4 = array( 'str'=>'?+',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'[a!?]\b[c+]',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_20() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1, 2=>2),
                        'length'=>array(0=>1, 1=>1, 2=>0));

        $test2 = array( 'str'=>' c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1, 2=>1),
                        'length'=>array(0=>1, 1=>0, 2=>1));

        $test3 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0),
                        'length'=>array(0=>1, 1=>0, 2=>1));

        $test4 = array( 'str'=>'b+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0),
                        'length'=>array(0=>0, 1=>0, 2=>0));

        $test5 = array( 'str'=>'b ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>0),
                        'length'=>array(0=>0, 1=>0, 2=>0));

        $test6 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>0));

        $test7 = array( 'str'=>'a ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>0));

        $test8 = array( 'str'=>'?b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>1, 1=>1, 2=>0));

        $test9 = array( 'str'=>'!c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>2, 1=>1, 2=>1));

        $test10 = array( 'str'=>'a+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>2, 1=>1, 2=>1));

        return array('regex'=>'([a!?]|)\b([c+]|)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_21() {
        $test1 = array( 'str'=>'a?c&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>'!b*d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));

        return array('regex'=>'[a!&]\b[b?+]\b[c*/]\b(d|&)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_22() {
        $test1 = array( 'str'=>'a?c+',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'!');

        $test2 = array( 'str'=>'!b*d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        return array('regex'=>'[a!&]\b[b?+]\b[c*/]\bd',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_23() {
        $test1 = array( 'str'=>'a?c&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));


        $test2 = array( 'str'=>'!b*d',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');

        return array('regex'=>'[a!&]\b[b?+]\b[c*/]\b&',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_24() {
        $test1 = array( 'str'=>'a?c&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));


        $test2 = array( 'str'=>'e!b*d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>4),
                        'length'=>array(0=>4, 1=>1));

        $test3 = array( 'str'=>' a?c&',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>4),
                        'length'=>array(0=>4, 1=>1));

        return array('regex'=>'\b[a!&]\b[b?+]\b[c*/]\b(d|&)',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_25() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));


        $test2 = array( 'str'=>"c\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        return array('regex'=>'c(at$|\b\t$)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_26() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));


        $test2 = array( 'str'=>"c\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>'!at',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));


        $test4 = array( 'str'=>'!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        return array('regex'=>'[c!](at$|\b[a\t]$)',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_27() {
        $test1 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        return array('regex'=>'c(at$|\Bat$)',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_28() {
        $test1 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));


        $test2 = array( 'str'=>"\tdog",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        return array('regex'=>'\t\b(cat|dog)$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_29() {
        $test1 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"\tdog",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test3 = array( 'str'=>'a?og',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test4 = array( 'str'=>'a!at',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        return array('regex'=>'[a\t]\b([c!]at|[d?]og)$',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_30() {
        $test1 = array( 'str'=>"\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"\tdog",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'c');


        return array('regex'=>'\t\b(cat|\tog)$',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_31() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test2 = array( 'str'=>"cow\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        return array('regex'=>'c(at\b|ow)\t',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_32() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test2 = array( 'str'=>"cow\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test3 = array( 'str'=>'ca!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test4 = array( 'str'=>'cowa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        return array('regex'=>'c(a[!t]\b|ow)[a\t]',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_33() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test2 = array( 'str'=>'cow',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test3 = array( 'str'=>'ca!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test4 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test5 = array( 'str'=>'ca!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2),
                        'left'=>array(1),
                        'next'=>'\w');

        return array('regex'=>'c(a[!t]\b|ow)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_34() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test2 = array( 'str'=>"cow\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        return array('regex'=>'c(at|ow)\b\t',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_35() {
        $test1 = array( 'str'=>"cat\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test2 = array( 'str'=>"cow\t",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test3 = array( 'str'=>'ca!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>2));

        $test4 = array( 'str'=>'cowa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2),
                        'left'=>array(1),
                        'next'=>'\t');

        return array('regex'=>'c(a[!t]|ow)\b[a\t]',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_36() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>0));

        $test3 = array( 'str'=>' ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test4 = array( 'str'=>' b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        return array('regex'=>'\b(a|)b',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_37() {
        $test1 = array( 'str'=>"d\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4));

        $test2 = array( 'str'=>"d\tcat\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5),
                        'length'=>array(0=>9, 1=>4));

        return array('regex'=>'d(\tcat\b)+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_38() {
        $test1 = array( 'str'=>"d\tcat",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4));

        $test2 = array( 'str'=>'dbca!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4),
                        'left'=>array(1),
                        'next'=>'\w');

        $test3 = array( 'str'=>"dbcat\tcat\tca!",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5),
                        'length'=>array(0=>9, 1=>4));

        $test4 = array( 'str'=>"dbca!cat\tca!",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4));

        return array('regex'=>'d([b\t]ca[t!]\b)+',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_39() {
        $test1 = array( 'str'=>"\ta\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>5, 1=>2));

        $test2 = array( 'str'=>"\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>"\ta\ta\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>7, 1=>2));

        return array('regex'=>'\ta(\b\ta)*c',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_40() {
        $test1 = array( 'str'=>"\ta\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>5, 1=>2));

        $test2 = array( 'str'=>"\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test3 = array( 'str'=>"\ta\ta\tac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>7, 1=>2));

        $test4 = array( 'str'=>"\t!b!bac",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>4),
                        'length'=>array(0=>7, 1=>2));

        return array('regex'=>'[b\t][a!](\b[b\t][a!])*c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_41() {
        $test1 = array( 'str'=>"\ta",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2));

        $test2 = array( 'str'=>"\ta\ta",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>2));

        return array('regex'=>'(\ta\b)+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_42() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1));

        $test2 = array( 'str'=>'a!a!',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        $test3 = array( 'str'=>'!a!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>4, 1=>1));

        $test4 = array( 'str'=>'a!a!!',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        $test5 = array( 'str'=>'!a!aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        return array('regex'=>'([a!]\b)+',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_43() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>1),
                        'left'=>array(1),
                        'next'=>'\w');

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        return array('regex'=>'(a\B)+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_44() {
        $test1 = array( 'str'=>"d\tcatt",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4));

        $test2 = array( 'str'=>"d\tcat\tcatt",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4),
                        'left'=>array(1),
                        'next'=>'\w');

        return array('regex'=>'d(\tcat\B)+',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_45() {
        $test1 = array( 'str'=>"d\tcatt",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>5, 1=>4));

        $test2 = array( 'str'=>"d\tcat\tcatt",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        return array('regex'=>'d(\tcat\B)*',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_46() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>2));

        $test2 = array( 'str'=>' ababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>3),
                        'length'=>array(0=>5, 1=>2));

        return array('regex'=>'\b(ab)+c',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_47() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>2));

        $test2 = array( 'str'=>' ababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>3),
                        'length'=>array(0=>5, 1=>2));

        $test3 = array( 'str'=>'  ababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2, 1=>4),
                        'length'=>array(0=>5, 1=>2));

        $test4 = array( 'str'=>'s!bab!bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>5),
                        'length'=>array(0=>7, 1=>2));

        return array('regex'=>'\b([a!]b)+c',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_48() {
        $test1 = array( 'str'=>'abdegf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>1),
                        'length'=>array(0=>6, 1=>4, 2=>3));

        $test2 = array( 'str'=>'abdegbdegf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5, 2=>5),
                        'length'=>array(0=>10, 1=>4, 2=>3));

        $test3 = array( 'str'=>"a\tgf",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1,2=>1),
                        'length'=>array(0=>4, 1=>2,2=>1));

        $test4 = array( 'str'=>"a\tg\tgf",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3,2=>3),
                        'length'=>array(0=>6, 1=>2,2=>1));

        return array('regex'=>'a((\b\t|bde)g)+f',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_49() {
        $test1 = array( 'str'=>'at',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'\ba*t',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_50() {
        $test1 = array( 'str'=>'kt',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'aat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a*\Bt',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_51() {
        $test1 = array( 'str'=>'at',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        return array('regex'=>'a\B*t',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_52() {
        $test1 = array( 'str'=>'cata!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>6, 1=>2));

        $test2 = array( 'str'=>'cata!at?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5),
                        'length'=>array(0=>8, 1=>2));

        return array('regex'=>'c(a[t!])+\b[d?]',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_53() {
        $test1 = array( 'str'=>'cata!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>3),
                        'length'=>array(0=>5, 1=>2));

        $test2 = array( 'str'=>'cata!at?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5),
                        'length'=>array(0=>7, 1=>2));

        $test3 = array( 'str'=>'cata!at',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>5),
                        'length'=>array(0=>7, 1=>2));

        return array('regex'=>'c(a[t!])+\b',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_54() {
        $test1 = array( 'str'=>'acd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>'a!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>1));

        $test3 = array( 'str'=>'!!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>3, 1=>1));

        $test4 = array( 'str'=>'b!d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1, 1=>1),
                        'length'=>array(0=>2, 1=>0));

        return array('regex'=>'(\b|[a!])[!c]d',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_wordboundary_55() {
        $test1 = array( 'str'=>'dca',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>'d!a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        $test3 = array( 'str'=>'d!!',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1));

        $test4 = array( 'str'=>'d!b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>2, 1=>0));

        return array('regex'=>'d[!c](\b|[a!])',
                     'tests'=>array($test1, $test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Asserts with tags.
    function data_for_test_assertions_tags_1() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$(c|\nb)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_2() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?m)a(\n|c)(^b)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_assertions_tags_3() {
        $test1 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>2, 1=>1, 2=>1));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'\n');

        return array('regex'=>'(?m)(a$)(\n|c)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_4() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1),
                        'length'=>array(0=>3, 1=>1, 2=>1));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)(a$)(\n|c)^b',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_5() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>1, 3=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1, 3=>1));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)(a$)(\n|c)(^b)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_6() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1, 2=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1));

        $test2 = array( 'str'=>"ac",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$(\n|c)(^b)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_7() {
        $test1 = array( 'str'=>"a\n\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2, 3=>2),
                        'length'=>array(0=>4, 1=>2, 2=>2, 3=>1));

        $test2 = array( 'str'=>"a\nc",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)(a\n$)(^(\n|c)b)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_8() {
        $test1 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2));

        $test2 = array( 'str'=>"\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'(?m)\A(^ab)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_9() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2),
                        'length'=>array(0=>3, 1=>2, 2=>1));

        $test2 = array( 'str'=>"abc",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'\n');

        return array('regex'=>'(?m)(ab$)\Z(\n|c)',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_assertions_tags_10() {
        $test1 = array( 'str'=>"\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>"ab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\\n',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?m)(\n|a)^b',
                     'tests'=>array($test1, $test2));
    }


}
