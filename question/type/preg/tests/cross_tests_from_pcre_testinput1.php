<?php

// this file initially was generated automatically using testinput1
// partial match data could be added manually
// note: this file should be encoded in UTF-8

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_pcre_testinput1 {

    function data_for_test_1() {
        $test1 = array('str'=>"the quick brown fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test2 = array('str'=>"The quick brown FOX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"What do you know about the quick brown fox?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>23),
                       'length'=>array(0=>19));

        $test4 = array('str'=>"What do you know about THE QUICK BROWN FOX?",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"the quick brown fox",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_2() {
        $test1 = array('str'=>"the quick brown fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test2 = array('str'=>"The quick brown FOX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test3 = array('str'=>"What do you know about the quick brown fox?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>23),
                       'length'=>array(0=>19));

        $test4 = array('str'=>"What do you know about THE QUICK BROWN FOX?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>23),
                       'length'=>array(0=>19));

        return array('regex'=>"The quick brown fox",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_3() {
        $test1 = array('str'=>"abcd	\n\r9;\$\\?caxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>20));

        return array('regex'=>"abcd\\t\\n\\r\\f\\a\\e\\071\\x3b\\\$\\\\\\?caxyz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_4() {
        $test1 = array('str'=>"abxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>23));

        $test2 = array('str'=>"abxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>23));

        $test3 = array('str'=>"aabxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>24));

        $test4 = array('str'=>"aaabxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test5 = array('str'=>"aaaabxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>26));

        $test6 = array('str'=>"abcxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>24));

        $test7 = array('str'=>"aabcxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test8 = array('str'=>"aaabcxyzpqrrrabbxyyyypAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test9 = array('str'=>"aaabcxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>26));

        $test10 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        $test11 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>28));

        $test12 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqqqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>29));

        $test13 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqqqqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>30));

        $test14 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqqqqqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>31));

        $test15 = array('str'=>"aaaabcxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        $test16 = array('str'=>"abxyzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>24));

        $test17 = array('str'=>"aabxyzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>26));

        $test18 = array('str'=>"aaabxyzzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>28));

        $test19 = array('str'=>"aaaabxyzzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>29));

        $test20 = array('str'=>"abcxyzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test21 = array('str'=>"aabcxyzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        $test22 = array('str'=>"aaabcxyzzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>29));

        $test23 = array('str'=>"aaaabcxyzzzzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>30));

        $test24 = array('str'=>"aaaabcxyzzzzpqrrrabbbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>31));

        $test25 = array('str'=>"aaaabcxyzzzzpqrrrabbbxyyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>32));

        $test26 = array('str'=>"aaabcxyzpqrrrabbxyyyypABzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>26));

        $test27 = array('str'=>"aaabcxyzpqrrrabbxyyyypABBzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        $test28 = array('str'=>">>>aaabxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>25));

        $test29 = array('str'=>">aaaabxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>26));

        $test30 = array('str'=>">>>>abcxyzpqrrrabbxyyyypqAzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>24));

        $test31 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test32 = array('str'=>"abxyzpqrrabbxyyyypqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test33 = array('str'=>"abxyzpqrrrrabbxyyyypqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test34 = array('str'=>"abxyzpqrrrabxyyyypqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test35 = array('str'=>"aaaabcxyzzzzpqrrrabbbxyyyyyypqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test36 = array('str'=>"aaaabcxyzzzzpqrrrabbbxyyypqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test37 = array('str'=>"aaabcxyzpqrrrabbxyyyypqqqqqqqAzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a*abc?xyz+pqr{3}ab{2,}xy{4,5}pq{0,6}AB{0,}zz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23, $test24, $test25, $test26, $test27, $test28, $test29, $test30, $test31, $test32, $test33, $test34, $test35, $test36, $test37));
    }

    function data_for_test_5() {
        $test1 = array('str'=>"abczz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"abcabczz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>8,1=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"zz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abcabcabczz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>">>abczz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(abc){1,2}zz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_6() {
        $test1 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"bbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test3 = array('str'=>"bbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        $test4 = array('str'=>"bac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test5 = array('str'=>"bbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        $test6 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test7 = array('str'=>"abbbbbbbbbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>13,1=>11));

        $test8 = array('str'=>"bbbbbbbbbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>13,1=>1));

        $test9 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"aaac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"abbbbbbbbbbbac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(b+?|a){1,2}?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_7() {
        $test1 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"bbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test3 = array('str'=>"bbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        $test4 = array('str'=>"bac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test5 = array('str'=>"bbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        $test6 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test7 = array('str'=>"abbbbbbbbbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>13,1=>11));

        $test8 = array('str'=>"bbbbbbbbbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>13,1=>1));

        $test9 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"aaac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"abbbbbbbbbbbac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(b+|a){1,2}c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_8() {
        $test1 = array('str'=>"bbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"^(b+|a){1,2}?bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_9() {
        $test1 = array('str'=>"babc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>"bbabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>2));

        $test3 = array('str'=>"bababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>6,1=>2));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"bababbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"babababc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(b*|ba){1,2}?bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_10() {
        $test1 = array('str'=>"babc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>"bbabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>2));

        $test3 = array('str'=>"bababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>6,1=>2));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"bababbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"babababc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(ba|b*){1,2}?bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_11() {
        $test1 = array('str'=>";z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"^\\ca\\cA\\c[;\\c:",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_12() {
        $test1 = array('str'=>"athing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"bthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"]thing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"cthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"dthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"ething",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"fthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"[thing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"\\thing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[ab\\]cde]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_13() {
        $test1 = array('str'=>"]thing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"cthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"dthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"ething",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"athing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"fthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[]cde]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_14() {
        $test1 = array('str'=>"fthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"[thing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"\\thing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"athing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"bthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"]thing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"cthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"dthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"ething",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^ab\\]cde]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_15() {
        $test1 = array('str'=>"athing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"fthing",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"]thing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"cthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"dthing",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"ething",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^]cde]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_16() {
        $test1 = array('str'=>"0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"1",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"2",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"3",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"5",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"6",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test8 = array('str'=>"7",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test9 = array('str'=>"8",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test10 = array('str'=>"9",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test11 = array('str'=>"10",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test12 = array('str'=>"100",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test13 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test14 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[0-9]+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14));
    }

    function data_for_test_17() {
        $test1 = array('str'=>"enter",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"inter",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"uponter",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"^.*nter",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_18() {
        $test1 = array('str'=>"xxx0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"xxx1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xxx",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^xxx[0-9]+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_19() {
        $test1 = array('str'=>"x123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"xx123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"123456",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"x1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"^.+[0-9][0-9][0-9]\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_20() {
        $test1 = array('str'=>"x123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"xx123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"123456",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"x1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"^.+?[0-9][0-9][0-9]\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_21() {
        $test1 = array('str'=>"abc!pqr=apquxz.ixr.zzz.ac.uk",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4),
                       'length'=>array(0=>28,1=>3,2=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"!pqr=apquxz.ixr.zzz.ac.uk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abc!=apquxz.ixr.zzz.ac.uk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abc!pqr=apquxz:ixr.zzz.ac.uk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"abc!pqr=apquxz.ixr.zzz.ac.ukk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^([^!]+)!(.+)=apquxz\\.ixr\\.zzz\\.ac\\.uk\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_22() {
        $test1 = array('str'=>"Well, we need a colon: somewhere",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>21),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Fail if we don't",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>":",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_23() {
        $test1 = array('str'=>"0abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test3 = array('str'=>"fed",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test4 = array('str'=>"E",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test5 = array('str'=>"::",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test6 = array('str'=>"5f03:12C0::932e",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>15,1=>15));

        $test7 = array('str'=>"fed def",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>4),
                       'length'=>array(0=>3,1=>3));

        $test8 = array('str'=>"Any old stuff",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>11,1=>11),
                       'length'=>array(0=>2,1=>2));

        $test9 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"0zzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"gzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test12 = array('str'=>"fed ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test13 = array('str'=>"Any old rubbish",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"([\\da-f:]+)\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13));
    }

    function data_for_test_24() {
        $test1 = array('str'=>".1.2.3",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3,3=>5),
                       'length'=>array(0=>6,1=>1,2=>1,3=>1));

        $test2 = array('str'=>"A.12.123.0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>5,3=>9),
                       'length'=>array(0=>10,1=>2,2=>3,3=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>".1.2.3333",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"1.2.3",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"1234.2.3",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*\\.(\\d{1,3})\\.(\\d{1,3})\\.(\\d{1,3})\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_25() {
        $test1 = array('str'=>"1 IN SOA non-sp1 non-sp2(",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>9,3=>17),
                       'length'=>array(0=>25,1=>1,2=>7,3=>7));

        $test2 = array('str'=>"1    IN    SOA    non-sp1    non-sp2   (",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>18,3=>29),
                       'length'=>array(0=>40,1=>1,2=>7,3=>7));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"1IN SOA non-sp1 non-sp2(",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\d+)\\s+IN\\s+SOA\\s+(\\S+)\\s+(\\S+)\\s*\\(\\s*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_26() {
        $test1 = array('str'=>"a.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"Z.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"2.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"ab-c.pq-r.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>10,1=>5));

        $test5 = array('str'=>"sxk.zzz.ac.uk.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>10),
                       'length'=>array(0=>14,1=>3));

        $test6 = array('str'=>"x-.y-.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>6,1=>3));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"-abc.peq.",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[a-zA-Z\\d][a-zA-Z\\d\\-]*(\\.[a-zA-Z\\d][a-zA-z\\d\\-]*)*\\.\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_27() {
        $test1 = array('str'=>"*.a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*.b0-a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"*.c3-b.c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>8,1=>3,2=>2));

        $test4 = array('str'=>"*.c-a.b-c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>5,3=>7),
                       'length'=>array(0=>9,1=>2,2=>4,3=>2));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"*.0",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"*.a-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"*.a-b.c-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"*.c-a.0-c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\*\\.[a-z]([a-z\\-\\d]*[a-z\\d]+)?(\\.[a-z]([a-z\\-\\d]*[a-z\\d]+)?)*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
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

    function data_for_test_29() {
        $test1 = array('str'=>"abdf",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0,3=>3),
                       'length'=>array(0=>4,2=>3,3=>1));

        return array('regex'=>"^(?!(ab)de|x)(abd)(f)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
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

    function data_for_test_31() {
        $test1 = array('str'=>"a.b.c.d",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>2));

        $test2 = array('str'=>"A.B.C.D",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>2));

        $test3 = array('str'=>"a.b.c.1.2.3.C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>13,1=>2));

        return array('regex'=>"^[\\da-f](\\.[\\da-f])*\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_32() {
        $test1 = array('str'=>"\"1234\"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test2 = array('str'=>"\"abcd\" ;",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        $test3 = array('str'=>"\"\" ; rhubarb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>12,1=>9));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"\"1234\" : things",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\\".*\\\"\\s*(;.*)?\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_33() {
        $test1 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_34() {
        $test1 = array('str'=>"ab c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ab cde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"   ^    a   (?# begins with a)  b\\sc (?# then b c) \$ (?# then end)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_35() {
        $test1 = array('str'=>"ab c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ab cde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?x)   ^    a   (?# begins with a)  b\\sc (?# then b c) \$ (?# then end)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_36() {
        $test1 = array('str'=>"a bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"a b d",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab d",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^   a\\ b[c ]d       \$",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_37() {
        $test1 = array('str'=>"abcdefhijklm",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                       'length'=>array(0=>12,1=>3,2=>2,3=>1,4=>3,5=>2,6=>1,7=>3,8=>2,9=>1,10=>3,11=>2,12=>1));

        return array('regex'=>"^(a(b(c)))(d(e(f)))(h(i(j)))(k(l(m)))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_38() {
        $test1 = array('str'=>"abcdefhijklm",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2,3=>4,4=>5,5=>7,6=>8,7=>10,8=>11),
                       'length'=>array(0=>12,1=>2,2=>1,3=>2,4=>1,5=>2,6=>1,7=>2,8=>1));

        return array('regex'=>"^(?:a(b(c)))(?:d(e(f)))(?:h(i(j)))(?:k(l(m)))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_39() {
        $test1 = array('str'=>"a+ Z0+\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"^[\\w][\\W][\\s][\\S][\\d][\\D][\\b][\\n][\\c]][\\022]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_40() {
        $test1 = array('str'=>".^\$(*+)|{?,?}",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>13));

        return array('regex'=>"^[.^\$|()*+?{,}]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_41() {
        $test1 = array('str'=>"z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"az",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"aaaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test4 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test6 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test7 = array('str'=>"a+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test8 = array('str'=>"aa+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^a*\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_42() {
        $test1 = array('str'=>"z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"az",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"aaaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"a+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test8 = array('str'=>"aa+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^a*?\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_43() {
        $test1 = array('str'=>"az",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"aaaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test5 = array('str'=>"aa+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^a+\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_44() {
        $test1 = array('str'=>"az",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"aaaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test5 = array('str'=>"aa+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^a+?\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_45() {
        $test1 = array('str'=>"1234567890",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test2 = array('str'=>"12345678ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test3 = array('str'=>"12345678__",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"1234567",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\d{8}\\w{2,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_46() {
        $test1 = array('str'=>"uoie",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"12345",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test4 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"123456",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[aeiou\\d]{4,5}\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_47() {
        $test1 = array('str'=>"uoie",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"12345",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test4 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test5 = array('str'=>"123456",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^[aeiou\\d]{4,5}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_48() {
        $test1 = array('str'=>"abc=abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7),
                       'length'=>array(0=>10,1=>3,2=>3));

        $test2 = array('str'=>"def=defdefdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>10),
                       'length'=>array(0=>13,1=>3,2=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abc=defdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\A(abc|def)=(\\1){2,3}\\Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_49() {
        $test1 = array('str'=>"abcdefghijkcda2",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                       'length'=>array(0=>15,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>2));

        $test2 = array('str'=>"abcdefghijkkkkcda2",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>14),
                       'length'=>array(0=>18,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>2));

        return array('regex'=>"^(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)\\11*(\\3\\4)\\1(?#)2\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_50() {
        $test1 = array('str'=>"cataract cataract23",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3,3=>4,4=>17,5=>18),
                       'length'=>array(0=>19,1=>8,2=>5,3=>4,4=>0,5=>1));

        $test2 = array('str'=>"catatonic catatonic23",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3,3=>4,4=>19,5=>20),
                       'length'=>array(0=>21,1=>9,2=>6,3=>5,4=>0,5=>1));

        $test3 = array('str'=>"caterpillar caterpillar23",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3,4=>23,5=>24),
                       'length'=>array(0=>25,1=>11,2=>8,4=>0,5=>1));

        return array('regex'=>"(cat(a(ract|tonic)|erpillar)) \\1()2(3)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_51() {
        $test1 = array('str'=>"From abcd  Mon Sep 01 12:33:02 1997",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>27,1=>4));

        return array('regex'=>"^From +([^ ]+) +[a-zA-Z][a-zA-Z][a-zA-Z] +[a-zA-Z][a-zA-Z][a-zA-Z] +[0-9]?[0-9] +[0-9][0-9]:[0-9][0-9]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_52() {
        $test1 = array('str'=>"From abcd  Mon Sep 01 12:33:02 1997",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>15),
                       'length'=>array(0=>27,1=>4));

        $test2 = array('str'=>"From abcd  Mon Sep  1 12:33:02 1997",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>15),
                       'length'=>array(0=>27,1=>5));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"From abcd  Sep 01 12:33:02 1997",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^From\\s+\\S+\\s+([a-zA-Z]{3}\\s+){2}\\d{1,2}\\s+\\d\\d:\\d\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_53() {
        $test1 = array('str'=>"12\n34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"12\r34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"^12.34",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_54() {
        $test1 = array('str'=>"the quick brown	 fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>10),
                       'length'=>array(0=>5));

        return array('regex'=>"\\w+(?=\\t)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_55() {
        $test1 = array('str'=>"foobar is foolish see?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>10,1=>13),
                       'length'=>array(0=>12,1=>9));

        return array('regex'=>"foo(?!bar)(.*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_56() {
        $test1 = array('str'=>"foobar crowbar etc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>8,1=>14),
                       'length'=>array(0=>10,1=>4));

        $test2 = array('str'=>"barrel",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"2barrel",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>7,1=>3));

        $test4 = array('str'=>"A barrel",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>8,1=>3));

        return array('regex'=>"(?:(?!foo)...|^.{0,2})bar(.*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_57() {
        $test1 = array('str'=>"abc456",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\D*)(?=\\d)(?!123)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_58() {
        $test1 = array('str'=>"1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^1234(?# test newlines\n  inside)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_59() {
        $test1 = array('str'=>"1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^1234 #comment in extended re\n  ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_60() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"#rhubarb\n  abcd",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_61() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^abcd#rhubarb",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_62() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>4,1=>1,2=>1));

        $test2 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

        $test3 = array('str'=>"aaaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

        $test4 = array('str'=>"aaaaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

        return array('regex'=>"^(a)\\1{2,3}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_63() {
        $test1 = array('str'=>"the abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?!^)abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

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

    function data_for_test_65() {
        $test1 = array('str'=>"aabbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"^[ab]{1,3}(ab*|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_66() {
        $test1 = array('str'=>"aabbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>6));

        return array('regex'=>"^[ab]{1,3}?(ab*|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_67() {
        $test1 = array('str'=>"aabbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"^[ab]{1,3}?(ab*?|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_68() {
        $test1 = array('str'=>"aabbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"^[ab]{1,3}(ab*?|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_69() {
        $test1 = array('str'=>"Alan Other <user@dom.ain>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test2 = array('str'=>"<user@dom.ain>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>12));

        $test3 = array('str'=>"user@dom.ain",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        $test4 = array('str'=>"\"A. Other\" <user.1234@dom.ain> (a comment)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>42));

        $test5 = array('str'=>"A. Other <user.1234@dom.ain> (a comment)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>38));

        $test6 = array('str'=>"\"/s=user/ou=host/o=place/prmd=uu.yy/admd= /c=gb/\"@x400-re.lay",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>61));

        $test7 = array('str'=>"A missing angle <user@some.where",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>17),
                       'length'=>array(0=>15));

        $test8 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"The quick brown fox",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*                          # optional leading comment\n(?:    (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n)                    # initial word\n(?:  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n)  )* # further okay, if led by a period\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  @  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*    (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                           # initial subdomain\n(?:                                  #\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.                        # if led by a period...\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                     #   ...further okay\n)*\n# address\n|                     #  or\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n)             # one word, optionally followed by....\n(?:\n[^()<>@,;:\".\\\\\\[\\]\\x80-\\xff\\000-\\010\\012-\\037]  |  # atom and space parts, or...\n\\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)       |  # comments, or...\n\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n# quoted strings\n)*\n<  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*                     # leading <\n(?:  @  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*    (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                           # initial subdomain\n(?:                                  #\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.                        # if led by a period...\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                     #   ...further okay\n)*\n\n(?:  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  ,  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  @  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*    (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                           # initial subdomain\n(?:                                  #\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.                        # if led by a period...\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                     #   ...further okay\n)*\n)* # further okay, if led by comma\n:                                # closing colon\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  )? #       optional route\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n)                    # initial word\n(?:  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\" (?:                      # opening quote...\n[^\\\\\\x80-\\xff\\n\\015\"]                #   Anything except backslash and quote\n|                     #    or\n\\\\ [^\\x80-\\xff]           #   Escaped something (something != CR)\n)* \"  # closing quote\n)  )* # further okay, if led by a period\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  @  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*    (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                           # initial subdomain\n(?:                                  #\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  \\.                        # if led by a period...\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*   (?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|   \\[                         # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*    #    stuff\n\\]                        #           ]\n)                     #   ...further okay\n)*\n#       address spec\n(?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*  > #                  trailing >\n# name and address\n)  (?: [\\040\\t] |  \\(\n(?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  |  \\( (?:  [^\\\\\\x80-\\xff\\n\\015()]  |  \\\\ [^\\x80-\\xff]  )* \\)  )*\n\\)  )*                       # optional trailing comment\n",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_70() {
        $test1 = array('str'=>"Alan Other <user@dom.ain>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>25));

        $test2 = array('str'=>"<user@dom.ain>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>12));

        $test3 = array('str'=>"user@dom.ain",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        $test4 = array('str'=>"\"A. Other\" <user.1234@dom.ain> (a comment)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>30));

        $test5 = array('str'=>"A. Other <user.1234@dom.ain> (a comment)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>26));

        $test6 = array('str'=>"\"/s=user/ou=host/o=place/prmd=uu.yy/admd= /c=gb/\"@x400-re.lay",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>61));

        $test7 = array('str'=>"A missing angle <user@some.where",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>17),
                       'length'=>array(0=>15));

        $test8 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"The quick brown fox",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional leading comment\n(?:\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n# Atom\n|                       #  or\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n# Quoted string\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n# Atom\n|                       #  or\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n# Quoted string\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# additional words\n)*\n@\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n)*\n# address\n|                             #  or\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n# Atom\n|                       #  or\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n# Quoted string\n)\n# leading word\n[^()<>@,;:\".\\\\\\[\\]\\x80-\\xff\\000-\\010\\012-\\037] *               # \"normal\" atoms and or spaces\n(?:\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n|\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n) # \"special\" comment or quoted string\n[^()<>@,;:\".\\\\\\[\\]\\x80-\\xff\\000-\\010\\012-\\037] *            #  more \"normal\"\n)*\n<\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# <\n(?:\n@\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n)*\n(?: ,\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n@\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n)*\n)*  # additional domains\n:\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n)?     #       optional route\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n# Atom\n|                       #  or\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n# Quoted string\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n# Atom\n|                       #  or\n\"                                     # \"\n[^\\\\\\x80-\\xff\\n\\015\"] *                            #   normal\n(?:  \\\\ [^\\x80-\\xff]  [^\\\\\\x80-\\xff\\n\\015\"] * )*        #   ( special normal* )*\n\"                                     #        \"\n# Quoted string\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# additional words\n)*\n@\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n(?:\n\\.\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n(?:\n[^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]+    # some number of atom characters...\n(?![^(\\040)<>@,;:\".\\\\\\[\\]\\000-\\037\\x80-\\xff]) # ..not followed by something that could be part of an atom\n|\n\\[                            # [\n(?: [^\\\\\\x80-\\xff\\n\\015\\[\\]] |  \\\\ [^\\x80-\\xff]  )*     #    stuff\n\\]                           #           ]\n)\n[\\040\\t]*                    # Nab whitespace.\n(?:\n\\(                              #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                             #     normal*\n(?:                                 #       (\n(?:  \\\\ [^\\x80-\\xff]  |\n\\(                            #  (\n[^\\\\\\x80-\\xff\\n\\015()] *                            #     normal*\n(?:  \\\\ [^\\x80-\\xff]   [^\\\\\\x80-\\xff\\n\\015()] * )*        #     (special normal*)*\n\\)                           #                       )\n)    #         special\n[^\\\\\\x80-\\xff\\n\\015()] *                         #         normal*\n)*                                  #            )*\n\\)                             #                )\n[\\040\\t]* )*    # If comment found, allow more spaces.\n# optional trailing comments\n)*\n#       address spec\n>                    #                 >\n# name and address\n)\n",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_71() {
        $test1 = array('str'=>"abc def pqr xyz 0AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test2 = array('str'=>"abc456 abc def pqr xyz 0ABCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>7),
                       'length'=>array(0=>19));

        return array('regex'=>"abc\\0def\\00pqr\\000xyz\\0000AB",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_72() {
        $test1 = array('str'=>"abc\ref pqr 0xyz 00AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>20));

        $test2 = array('str'=>"abc456 abc\ref pqr 0xyz 00ABCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>7),
                       'length'=>array(0=>20));

        return array('regex'=>"abc\\x0def\\x00pqr\\x000xyz\\x0000AB",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_73() {
        $test1 = array('str'=>" A",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^[\\000-\\037]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_74() {
        $test1 = array('str'=>"    ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"\\0*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_75() {
        $test1 = array('str'=>"The A  Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"An A   Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"A Z",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"A    Z",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A\\x0{2,3}Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_76() {
        $test1 = array('str'=>"cowcowbell",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>6),
                       'length'=>array(0=>10,1=>3,2=>4));

        $test2 = array('str'=>"bell",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>4,1=>0,2=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"cowbell",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(cow|)\\1(bell)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_77() {
        $test1 = array('str'=>" abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"\nabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"\rabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"	abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_78() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^a	b\n  c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_79() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"acb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a|)\\1*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_80() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a|)\\1+b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_81() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"acb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a|)\\1?b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_82() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        $test2 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"aaaab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a|)\\1{2}b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_83() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        $test2 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"aaaaab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a|)\\1{2,3}b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_84() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test2 = array('str'=>"abbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"abbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"abbbbbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab{1,3}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_85() {
        $test1 = array('str'=>"track1.title:TBlah blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                       'length'=>array(0=>28,1=>6,2=>5,3=>14));

        return array('regex'=>"([^.]*)\\.([^:]*):[T ]+(.*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_86() {
        $test1 = array('str'=>"track1.title:TBlah blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                       'length'=>array(0=>28,1=>6,2=>5,3=>14));

        return array('regex'=>"([^.]*)\\.([^:]*):[T ]+(.*)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_87() {
        $test1 = array('str'=>"track1.title:TBlah blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                       'length'=>array(0=>28,1=>6,2=>5,3=>14));

        return array('regex'=>"([^.]*)\\.([^:]*):[t ]+(.*)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_88() {
        $test1 = array('str'=>"WXY_^abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"wxy",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[W-c]+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_89() {
        $test1 = array('str'=>"WXY_^abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"wxy_^ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"^[W-c]+\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_90() {
        $test1 = array('str'=>"WXY_^abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"wxy_^ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"^[\\x3f-\\x5F]+\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_91() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"qqq\nabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"abc\nzzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"qqq\nabc\nzzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"^abc\$",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_92() {
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

        $test3 = array('str'=>"qqq\nabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"qqq\nabc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^abc\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_93() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"qqq\nabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"qqq\nabc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\Aabc\\Z",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_94() {
        $test1 = array('str'=>"abc\ndef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"\\A(.)*\\Z",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_95() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>10),
                       'length'=>array(0=>11,1=>1));

        $test2 = array('str'=>"abc\ndef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\A(.)*\\Z",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_96() {
        $test1 = array('str'=>"b::c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"c::b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:b)|(?::+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_97() {
        $test1 = array('str'=>"az-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[-az]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_98() {
        $test1 = array('str'=>"za-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[az-]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_99() {
        $test1 = array('str'=>"a-z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[a\\-z]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_100() {
        $test1 = array('str'=>"abcdxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"[a-z]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_101() {
        $test1 = array('str'=>"12-34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\d-]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_102() {
        $test1 = array('str'=>"12-34z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\d-z]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_103() {
        $test1 = array('str'=>"\\",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\x5c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_104() {
        $test1 = array('str'=>"the Zoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"Zulu",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\x20Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_105() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"ABCabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"abcABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"(abc)\\1",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_106() {
        $test1 = array('str'=>"ab{3cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{3cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_107() {
        $test1 = array('str'=>"ab{3,cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"ab{3,cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_108() {
        $test1 = array('str'=>"ab{3,4a}cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"ab{3,4a}cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_109() {
        $test1 = array('str'=>"{4,5a}bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"{4,5a}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_110() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abc\ndef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abc\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_111() {
        $test1 = array('str'=>"abcS",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(abc)\\123",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_112() {
Error -10 (bad UTF-8 string) offset=3 reason=20
str: abc�
        return array('regex'=>"(abc)\\223",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_113() {
Error -10 (bad UTF-8 string) offset=3 reason=1
str: abc�
        return array('regex'=>"(abc)\\323",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_114() {
        $test1 = array('str'=>"abc@",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        $test2 = array('str'=>"abc@",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(abc)\\100",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_115() {
        $test1 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test5 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        $test6 = array('str'=>"abc@0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>3));

        return array('regex'=>"(abc)\\1000",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_116() {
        $test1 = array('str'=>"A8B9C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"A 8B 9C",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^A\\8B\\9C\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_117() {
        $test1 = array('str'=>"ABCDEFGHIHI",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8),
                       'length'=>array(0=>11,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1));

        return array('regex'=>"^(A)(B)(C)(D)(E)(F)(G)(H)(I)\\8\\9\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_118() {
        $test1 = array('str'=>"A8B9C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"A8B9C ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[A\\8B\\9C]+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_119() {
        $test1 = array('str'=>"abcdefghijkllS",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                       'length'=>array(0=>14,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1));

        return array('regex'=>"(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)(l)\\12\\123",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_120() {
        $test1 = array('str'=>"abcdefghijk\nS",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10),
                       'length'=>array(0=>13,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1));

        return array('regex'=>"(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)\\12\\123",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_121() {
        $test1 = array('str'=>"abidef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab\\idef",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_122() {
        $test1 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a{0}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_123() {
        $test1 = array('str'=>"xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(a|(bc)){0,0}?xyz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_124() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"abc[\\10]de",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_125() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"abc[\\1]de",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_126() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"(abc)[\\1]de",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_127() {
        $test1 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?s)a.b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_128() {
        $test1 = array('str'=>"baNOTccccd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>5),
                       'length'=>array(0=>9,1=>1,2=>1,3=>3,4=>4));

        $test2 = array('str'=>"baNOTcccd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>5),
                       'length'=>array(0=>8,1=>1,2=>1,3=>3,4=>3));

        $test3 = array('str'=>"baNOTccd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>4),
                       'length'=>array(0=>7,1=>1,2=>1,3=>2,4=>3));

        $test4 = array('str'=>"bacccd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>2),
                       'length'=>array(0=>5,1=>1,2=>1,3=>0,4=>3));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>8),
                       'length'=>array(0=>11,1=>1,2=>1,3=>6,4=>3));

        $test6 = array('str'=>"anything",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"bc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"baccd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^([^a])([^\\b])([^c]*)([^d]{3,4})",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_129() {
        $test1 = array('str'=>"Abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_130() {
        $test1 = array('str'=>"Abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_131() {
        $test1 = array('str'=>"AAAaAbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"[^a]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_132() {
        $test1 = array('str'=>"AAAaAbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]+",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_133() {
        $test1 = array('str'=>"bbb\nccc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"[^a]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_134() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>10),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^k]\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_135() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"kbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"kabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>8),
                       'length'=>array(0=>3));

        $test5 = array('str'=>"abk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"akb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"akk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^k]{2,3}\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_136() {
        $test1 = array('str'=>"12345678@a.b.c.d",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>16));

        $test2 = array('str'=>"123456789@x.y.z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>15));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"12345678@x.y.uk",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"1234567@a.b.c.d",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\d{8,}\\@.+[^k]\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_137() {
        $test1 = array('str'=>"aaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>1));

        $test2 = array('str'=>"aaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>10,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a)\\1{8,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_138() {
        $test1 = array('str'=>"aaaabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aaAabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_139() {
        $test1 = array('str'=>"aaaabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aaAabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_140() {
        $test1 = array('str'=>"aaaabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aaAabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"[^az]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_141() {
        $test1 = array('str'=>"aaaabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aaAabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        return array('regex'=>"[^az]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_142() {
        $test1 = array('str'=>" 	\n\r !\"#\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~ ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>256));

        return array('regex'=>"\\000\\001\\002\\003\\004\\005\\006\\007\\010\\011\\012\\013\\014\\015\\016\\017\\020\\021\\022\\023\\024\\025\\026\\027\\030\\031\\032\\033\\034\\035\\036\\037\\040\\041\\042\\043\\044\\045\\046\\047\\050\\051\\052\\053\\054\\055\\056\\057\\060\\061\\062\\063\\064\\065\\066\\067\\070\\071\\072\\073\\074\\075\\076\\077\\100\\101\\102\\103\\104\\105\\106\\107\\110\\111\\112\\113\\114\\115\\116\\117\\120\\121\\122\\123\\124\\125\\126\\127\\130\\131\\132\\133\\134\\135\\136\\137\\140\\141\\142\\143\\144\\145\\146\\147\\150\\151\\152\\153\\154\\155\\156\\157\\160\\161\\162\\163\\164\\165\\166\\167\\170\\171\\172\\173\\174\\175\\176\\177\\200\\201\\202\\203\\204\\205\\206\\207\\210\\211\\212\\213\\214\\215\\216\\217\\220\\221\\222\\223\\224\\225\\226\\227\\230\\231\\232\\233\\234\\235\\236\\237\\240\\241\\242\\243\\244\\245\\246\\247\\250\\251\\252\\253\\254\\255\\256\\257\\260\\261\\262\\263\\264\\265\\266\\267\\270\\271\\272\\273\\274\\275\\276\\277\\300\\301\\302\\303\\304\\305\\306\\307\\310\\311\\312\\313\\314\\315\\316\\317\\320\\321\\322\\323\\324\\325\\326\\327\\330\\331\\332\\333\\334\\335\\336\\337\\340\\341\\342\\343\\344\\345\\346\\347\\350\\351\\352\\353\\354\\355\\356\\357\\360\\361\\362\\363\\364\\365\\366\\367\\370\\371\\372\\373\\374\\375\\376\\377",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_143() {
        $test1 = array('str'=>"xxxxxxxxxxxPSTAIREISLLxxxxxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>11),
                       'length'=>array(0=>11));

        return array('regex'=>"P[^*]TAIRE[^*]{1,6}?LL",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_144() {
        $test1 = array('str'=>"xxxxxxxxxxxPSTAIREISLLxxxxxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>11),
                       'length'=>array(0=>11));

        return array('regex'=>"P[^*]TAIRE[^*]{1,}?LL",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_145() {
        $test1 = array('str'=>"1.230003938",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>10,1=>3));

        $test2 = array('str'=>"1.875000282",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>10,1=>4));

        $test3 = array('str'=>"1.235",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(\\.\\d\\d[1-9]?)\\d+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
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

    function data_for_test_147() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_148() {
        $test1 = array('str'=>"Food is on the foo table",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>15,1=>15,2=>19),
                       'length'=>array(0=>9,1=>3,2=>5));

        return array('regex'=>"\\b(foo)\\s+(\\w+)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_149() {
        $test1 = array('str'=>"The food is under the bar in the barn.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>7),
                       'length'=>array(0=>32,1=>26));

        return array('regex'=>"foo(.*)bar",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_150() {
        $test1 = array('str'=>"The food is under the bar in the barn.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>7),
                       'length'=>array(0=>21,1=>15));

        return array('regex'=>"foo(.*?)bar",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_151() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>23),
                       'length'=>array(0=>23,1=>23,2=>0));

        return array('regex'=>"(.*)(\\d*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_152() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>22),
                       'length'=>array(0=>23,1=>22,2=>1));

        return array('regex'=>"(.*)(\\d+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_153() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>0,1=>0,2=>0));

        return array('regex'=>"(.*?)(\\d*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_154() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7),
                       'length'=>array(0=>8,1=>7,2=>1));

        return array('regex'=>"(.*?)(\\d+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_155() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>22),
                       'length'=>array(0=>23,1=>22,2=>1));

        return array('regex'=>"(.*)(\\d+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_156() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>18),
                       'length'=>array(0=>23,1=>18,2=>5));

        return array('regex'=>"(.*?)(\\d+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_157() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>18),
                       'length'=>array(0=>23,1=>18,2=>5));

        return array('regex'=>"(.*)\\b(\\d+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_158() {
        $test1 = array('str'=>"I have 2 numbers: 53147",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>18),
                       'length'=>array(0=>23,1=>18,2=>5));

        return array('regex'=>"(.*\\D)(\\d+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_159() {
        $test1 = array('str'=>"ABC123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^\\D*(?!123)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_160() {
        $test1 = array('str'=>"ABC445",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABC123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\D*)(?=\\d)(?!123)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_161() {
        $test1 = array('str'=>"W46]789",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"-46]789",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"Wall",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"Zebra",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"42",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"[abcd]",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"]abcd[",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[W-]46]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_162() {
        $test1 = array('str'=>"W46]789",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"Wall",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"Zebra",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"Xylophone",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"42",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"[abcd]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"]abcd[",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test8 = array('str'=>"\\backslash",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test9 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"-46]789",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"well",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[W-\\]46]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_163() {
        $test1 = array('str'=>"01/01/2000",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"\\d\\d\\/\\d\\d\\/\\d\\d\\d\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_164() {
        $test1 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark otherword",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>74));

        $test2 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"word (?:[a-zA-Z0-9]+ ){0,10}otherword",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_165() {
        $test1 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark the quick brown fox and the lazy dog and several other words getting close to thirty by now I hope",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"word (?:[a-zA-Z0-9]+ ){0,300}otherword",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_166() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^(a){0,0}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_167() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"^(a){0,1}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_168() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"^(a){0,2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_169() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"^(a){0,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_170() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        $test5 = array('str'=>"aaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"^(a){0,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_171() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"^(a){1,1}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_172() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"^(a){1,2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_173() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"^(a){1,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_174() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        $test5 = array('str'=>"aaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"^(a){1,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_175() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>7),
                       'length'=>array(0=>7));

        return array('regex'=>".*\\.gif",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_176() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>7),
                       'length'=>array(0=>7));

        return array('regex'=>".{0,}\\.gif",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_177() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>7),
                       'length'=>array(0=>7));

        return array('regex'=>".*\\.gif",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_178() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14));

        return array('regex'=>".*\\.gif",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_179() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14));

        return array('regex'=>".*\\.gif",
                     'modifiers'=>"ms",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_180() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>15),
                       'length'=>array(0=>2));

        return array('regex'=>".*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_181() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>".*\$",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_182() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        return array('regex'=>".*\$",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_183() {
        $test1 = array('str'=>"borfle\nbib.gif\nno",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        return array('regex'=>".*\$",
                     'modifiers'=>"ms",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_184() {
        $test1 = array('str'=>"borfle\nbib.gif\nno\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>15),
                       'length'=>array(0=>2));

        return array('regex'=>".*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_185() {
        $test1 = array('str'=>"borfle\nbib.gif\nno\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>".*\$",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_186() {
        $test1 = array('str'=>"borfle\nbib.gif\nno\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>18));

        return array('regex'=>".*\$",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_187() {
        $test1 = array('str'=>"borfle\nbib.gif\nno\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>18));

        return array('regex'=>".*\$",
                     'modifiers'=>"ms",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_188() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6,1=>6),
                       'length'=>array(0=>5,1=>5));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcde\nBar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(.*X|^B)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_189() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6,1=>6),
                       'length'=>array(0=>5,1=>5));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"abcde\nBar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6,1=>6),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.*X|^B)",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_190() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>11,1=>11));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcde\nBar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(.*X|^B)",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_191() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>11,1=>11));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"abcde\nBar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6,1=>6),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.*X|^B)",
                     'modifiers'=>"ms",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_192() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>11,1=>11));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcde\nBar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?s)(.*X|^B)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_193() {
        $test1 = array('str'=>"abcde\n1234Xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        $test2 = array('str'=>"BarFoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcde\nBar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?s:.*X|^B)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_194() {
        $test1 = array('str'=>"**** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abc\nB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_195() {
        $test1 = array('str'=>"abc\nB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"(?s)^.*B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_196() {
        $test1 = array('str'=>"abc\nB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        return array('regex'=>"(?m)^.*B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_197() {
        $test1 = array('str'=>"abc\nB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"(?ms)^.*B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_198() {
        $test1 = array('str'=>"abc\nB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        return array('regex'=>"(?ms)^B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_199() {
        $test1 = array('str'=>"B\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?s)B\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_200() {
        $test1 = array('str'=>"123456654321",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_201() {
        $test1 = array('str'=>"123456654321",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"^\\d\\d\\d\\d\\d\\d\\d\\d\\d\\d\\d\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_202() {
        $test1 = array('str'=>"123456654321",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"^[\\d][\\d][\\d][\\d][\\d][\\d][\\d][\\d][\\d][\\d][\\d][\\d]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_203() {
        $test1 = array('str'=>"abcabcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"^[abc]{12}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_204() {
        $test1 = array('str'=>"abcabcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"^[a-c]{12}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_205() {
        $test1 = array('str'=>"abcabcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>11),
                       'length'=>array(0=>12,1=>1));

        return array('regex'=>"^(a|b|c){12}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_206() {
        $test1 = array('str'=>"n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"z",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[abcdefghijklmnopqrstuvwxy0123456789]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_207() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abce",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abcde{0,0}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_208() {
        $test1 = array('str'=>"abe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab[cd]{0,0}e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_209() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab(c){0,0}d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_210() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test3 = array('str'=>"abbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>4));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5,1=>6),
                       'length'=>array(0=>1,1=>0));

        $test5 = array('str'=>"bbbbb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(b*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_211() {
        $test1 = array('str'=>"abe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ab1e",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab\\d{0}e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_212() {
        $test1 = array('str'=>"the \"quick\" brown fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>5),
                       'length'=>array(0=>7,1=>5));

        $test2 = array('str'=>"\"the \\\"quick\\\" brown fox\"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>14),
                       'length'=>array(0=>25,1=>10));

        return array('regex'=>"\"([^\\\\\"]+|\\\\.)*\"",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_213() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>".*?",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_214() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"\\b",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_215() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"\\b",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_216() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_217() {
        $test1 = array('str'=>"<TR BGCOLOR='#DBE9E9'><TD align=left valign=top>43.<a href='joblist.cfm?JobID=94 6735&Keyword='>Word Processor<BR>(N-1286)</a></TD><TD align=left valign=top>Lega lstaff.com</TD><TD align=left valign=top>CA - Statewide</TD></TR>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>25,3=>48,4=>51,5=>122,6=>122,8=>134,9=>157,10=>180,11=>203),
                       'length'=>array(0=>227,1=>18,2=>22,3=>3,4=>71,5=>0,6=>0,8=>22,9=>15,10=>22,11=>14));

        return array('regex'=>"<tr([\\w\\W\\s\\d][^<>]{0,})><TD([\\w\\W\\s\\d][^<>]{0,})>([\\d]{0,}\\.)(.*)((<BR>([\\w\\W\\s\\d][^<>]{0,})|[\\s]{0,}))<\\/a><\\/TD><TD([\\w\\W\\s\\d][^<>]{0,})>([\\w\\W\\s\\d][^<>]{0,})<\\/TD><TD([\\w\\W\\s\\d][^<>]{0,})>([\\w\\W\\s\\d][^<>]{0,})<\\/TD><\\/TR>",
                     'modifiers'=>"is",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_218() {
        $test1 = array('str'=>"acb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[^a]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_219() {
        $test1 = array('str'=>"acb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a\nb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a.b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_220() {
        $test1 = array('str'=>"acb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[^a]b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_221() {
        $test1 = array('str'=>"acb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_222() {
        $test1 = array('str'=>"bac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"bbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        $test3 = array('str'=>"bbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>5,1=>1));

        $test4 = array('str'=>"bbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        $test5 = array('str'=>"bbbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"^(b+?|a){1,2}?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_223() {
        $test1 = array('str'=>"bac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"bbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        $test3 = array('str'=>"bbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>5,1=>1));

        $test4 = array('str'=>"bbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        $test5 = array('str'=>"bbbbbac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"^(b+|a){1,2}?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_224() {
        $test1 = array('str'=>"x\nb\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ax\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?!\\A)x",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_225() {
        $test1 = array('str'=>" {ab}",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"\\x0{ab}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_226() {
        $test1 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(A|B)*?CD",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_227() {
        $test1 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(A|B)*CD",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_228() {
        $test1 = array('str'=>"ABABAB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"(AB)*?\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_229() {
        $test1 = array('str'=>"ABABAB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>6,1=>2));

        return array('regex'=>"(AB)*\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_230() {
        $test1 = array('str'=>"foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"catfood",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"arfootle",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"rfoosh",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"barfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"towbarfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<!bar)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_231() {
        $test1 = array('str'=>"catfood",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"foo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"barfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"towbarfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\w{3}(?<!bar)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

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

    function data_for_test_233() {
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

        $test3 = array('str'=>"abc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"qqq\nabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"qqq\nabc\nzzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\Aabc\\z",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_234() {
        $test1 = array('str'=>"/this/is/a/very/long/line/in/deed/with/very/many/slashes/in/it/you/see/",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>.*/)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_235() {
        $test1 = array('str'=>"/this/is/a/very/long/line/in/deed/with/very/many/slashes/in/and/foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>67));

        return array('regex'=>"(?>.*/)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_236() {
        $test1 = array('str'=>"1.230003938",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>10,1=>3));

        $test2 = array('str'=>"1.875000282",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>10,1=>4));

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

        return array('regex'=>"(?>(\\.\\d\\d[1-9]?))\\d+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_237() {
        $test1 = array('str'=>"now is the time for all good men to come to the aid of the party",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>59),
                       'length'=>array(0=>64,1=>5));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"this is not a line with only words and spaces!",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^((?>\\w+)|(?>\\s+))*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_238() {
        $test1 = array('str'=>"12345a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>6,1=>5,2=>1));

        $test2 = array('str'=>"12345+",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4),
                       'length'=>array(0=>5,1=>4,2=>1));

        return array('regex'=>"(\\d+)(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_239() {
        $test1 = array('str'=>"12345a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>6,1=>5,2=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"12345+",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((?>\\d+))(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_240() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"(?>a+)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_241() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        return array('regex'=>"((?>a+)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_242() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(?>(a+))b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_243() {
        $test1 = array('str'=>"aaabbbccc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        return array('regex'=>"(?>b)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_244() {
        $test1 = array('str'=>"aaabbbbccccd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"(?>a+|b+|c+)*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_245() {
        $test1 = array('str'=>"((abc(ade)ufh()()x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>17),
                       'length'=>array(0=>16,1=>1));

        return array('regex'=>"((?>[^()]+)|\\([^()]*\\))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_246() {
        $test1 = array('str'=>"(abc)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"(abc(def)xyz)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>13,1=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"((()aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\(((?>[^()]+)|\\([^()]+\\))+\\)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_247() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(?-i)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_248() {
        $test1 = array('str'=>"a bcd e",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a b cd e",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcd e",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"a bcde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a (?x)b c)d e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_249() {
        $test1 = array('str'=>"a bcde f",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>8,1=>8));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a b(?x)c d (?-x)e f)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_250() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test2 = array('str'=>"aBc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"Abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"ABc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"AbC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a(?i)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_251() {
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

        $test4 = array('str'=>"ABC",
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

        return array('regex'=>"a(?i:b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_252() {
        $test1 = array('str'=>"aBc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"aBBc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aBBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(?i:b)*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
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

    function data_for_test_254() {
        $test1 = array('str'=>"more than million",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        $test2 = array('str'=>"more than MILLION",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        $test3 = array('str'=>"more \n than Million",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"MORE THAN MILLION",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"more \n than \n million",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?s-i:more.*than).*million",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_255() {
        $test1 = array('str'=>"more than million",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        $test2 = array('str'=>"more than MILLION",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>17));

        $test3 = array('str'=>"more \n than Million",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>19));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"MORE THAN MILLION",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"more \n than \n million",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?s-i)more.*than).*million",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_256() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"aBbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"aBBc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"Abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"abAb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"abbC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>a(?i)b+)+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
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

    function data_for_test_259() {
        $test1 = array('str'=>"aA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"bB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"bA",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(a)|b)(?(1)A|B)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_260() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"bb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a)?(?(1)a|b)+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_261() {
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
    }

    function data_for_test_262() {
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

        return array('regex'=>"^(?(?!abc)\\d\\d|\\w{3}:)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_263() {
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
    }

    function data_for_test_264() {
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

        return array('regex'=>"(?(?<!foo)cat|bar)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_265() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"(abcd)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>1));

        $test3 = array('str'=>"the quick (abcd) fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test4 = array('str'=>"(abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>4));

        return array('regex'=>"( \\( )? [^()]+ (?(1) \\) |) ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_266() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"(abcd)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>1));

        $test3 = array('str'=>"the quick (abcd) fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test4 = array('str'=>"(abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>4));

        return array('regex'=>"( \\( )? [^()]+ (?(1) \\) ) ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_267() {
        $test1 = array('str'=>"12",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        $test2 = array('str'=>"12a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>3,1=>1,2=>1));

        $test3 = array('str'=>"12aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>4,1=>1,2=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"1234",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?(2)a|(1)(2))+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_268() {
        $test1 = array('str'=>"blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test2 = array('str'=>"BLAH BLAH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test3 = array('str'=>"Blah Blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test4 = array('str'=>"blaH blaH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"blah BLAH",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"Blah blah",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"blaH blah",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((?i)blah)\\s+\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_269() {
        $test1 = array('str'=>"blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test2 = array('str'=>"BLAH BLAH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test3 = array('str'=>"Blah Blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test4 = array('str'=>"blaH blaH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test5 = array('str'=>"blah BLAH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test6 = array('str'=>"Blah blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        $test7 = array('str'=>"blaH blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>4));

        return array('regex'=>"((?i)blah)\\s+(?i:\\1)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_270() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"(?>a*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_271() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>3,1=>0));

        $test2 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>6,1=>0));

        $test3 = array('str'=>"abcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>9,1=>0));

        $test4 = array('str'=>"xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(abc|)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_272() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>5,1=>0));

        return array('regex'=>"([a]*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_273() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        $test3 = array('str'=>"ababab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>6,1=>0));

        $test4 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>5,1=>0));

        $test5 = array('str'=>"bbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>4,1=>0));

        return array('regex'=>"([ab]*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_274() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"bbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>4,1=>0));

        $test3 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^a]*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_275() {
        $test1 = array('str'=>"cccc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>4,1=>0));

        $test2 = array('str'=>"abab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^ab]*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_276() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([a]*?)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_277() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test3 = array('str'=>"abab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test4 = array('str'=>"baba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([ab]*?)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_278() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"bbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test3 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^a]*?)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_279() {
        $test1 = array('str'=>"c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"cccc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test3 = array('str'=>"baba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^ab]*?)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_280() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"aaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?>a*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_281() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>5,1=>0));

        $test2 = array('str'=>"aabbaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"((?>a*))*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_282() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"aabbaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"((?>a*?))*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_283() {
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
    }

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

    function data_for_test_285() {
        $test1 = array('str'=>"saturday",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"sunday",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test3 = array('str'=>"Saturday",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test4 = array('str'=>"Sunday",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test5 = array('str'=>"SATURDAY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test6 = array('str'=>"SUNDAY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        $test7 = array('str'=>"SunDay",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"(?i:saturday|sunday)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_286() {
        $test1 = array('str'=>"abcx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        $test2 = array('str'=>"aBCx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        $test3 = array('str'=>"bbx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test4 = array('str'=>"BBx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"abcX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"aBCX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"bbX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"BBX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a(?i)bc|BB)x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_287() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"aC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test3 = array('str'=>"bD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test4 = array('str'=>"elephant",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test5 = array('str'=>"Europe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test6 = array('str'=>"frog",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test7 = array('str'=>"France",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test8 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"Africa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^([ab](?i)[cd]|[ef])",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_288() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"aBd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test3 = array('str'=>"xy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test4 = array('str'=>"xY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test5 = array('str'=>"zebra",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test6 = array('str'=>"Zambesi",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"aCD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"XY",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(ab|a(?i)[b-c](?m-i)d|x(?i)y|z)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
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

    function data_for_test_290() {
        $test1 = array('str'=>"barbaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"barbarbaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"koobarbaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"baz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"foobarbaz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(?<!foo)bar)baz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_291() {
        $test1 = array('str'=>"/differently. We know that odd, and maybe incorrect, things happen with/",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"/recursive references in Perl, as far as 5.11.3 - see some stuff in test #2./",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"The cases of aaaa and aaaaaa are missed out below because Perl does things",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_292() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        $test5 = array('str'=>"aaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1));

        $test6 = array('str'=>"aaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"aaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"aaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>10,1=>4));

        $test9 = array('str'=>"aaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"aaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"aaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test12 = array('str'=>"aaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test13 = array('str'=>"aaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test14 = array('str'=>"aaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a\\1?){4}\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14));
    }

    function data_for_test_293() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                       'length'=>array(0=>4,1=>1,2=>1,3=>1,4=>1));

        $test5 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>4),
                       'length'=>array(0=>5,1=>1,2=>2,3=>1,4=>1));

        $test6 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>4),
                       'length'=>array(0=>6,1=>1,2=>2,3=>1,4=>2));

        $test7 = array('str'=>"aaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                       'length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1));

        $test8 = array('str'=>"aaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"aaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"aaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                       'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4));

        $test11 = array('str'=>"aaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test12 = array('str'=>"aaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test13 = array('str'=>"aaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test14 = array('str'=>"aaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test15 = array('str'=>"aaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test16 = array('str'=>"aaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a\\1?)(a\\1?)(a\\2?)(a\\3?)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16));
    }

    function data_for_test_294() {
        $test1 = array('str'=>"/are compatible with 5.004, but I'd rather not have to sort them out./",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"The following tests are taken from the Perl 5.005 test suite; some of them",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_295() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"xabcy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"xbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"axc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"abx",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_296() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_297() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab*bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_298() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>".{1}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_299() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>".{3,4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_300() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{0,}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_301() {
        $test1 = array('str'=>"abbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab+bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_302() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab+bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_303() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{1,}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_304() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{1,3}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_305() {
        $test1 = array('str'=>"abbbbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{3,4}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_306() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abq",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abbbbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab{4,5}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_307() {
        $test1 = array('str'=>"abbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab?bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_308() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab{0,1}bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_309() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_310() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab{0,1}c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_311() {
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

        $test3 = array('str'=>"abbbbc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^abc\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_312() {
        $test1 = array('str'=>"abcc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_313() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"aabcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abc\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_314() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_315() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>0));

        return array('regex'=>"\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_316() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"axc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_317() {
        $test1 = array('str'=>"axyzc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"a.*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_318() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"axyzd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[bc]d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_319() {
        $test1 = array('str'=>"ace",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[b-d]e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_320() {
        $test1 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"a[b-d]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_321() {
        $test1 = array('str'=>"a-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[-b]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_322() {
        $test1 = array('str'=>"a-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[b-]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_323() {
        $test1 = array('str'=>"a]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_324() {
        $test1 = array('str'=>"a]b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[]]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_325() {
        $test1 = array('str'=>"aed",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[^bc]d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_326() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[^-b]c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_327() {
        $test1 = array('str'=>"adc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a-c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"a]c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[^]b]c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_328() {
        $test1 = array('str'=>"a-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"-a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-a-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\ba\\b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_329() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"xy",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"yz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\by\\b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_330() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"a-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"-a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"-a-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\Ba\\B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_331() {
        $test1 = array('str'=>"xy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\By\\b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_332() {
        $test1 = array('str'=>"yz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\by\\B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_333() {
        $test1 = array('str'=>"xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\By\\B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_334() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_335() {
        $test1 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\W",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_336() {
        $test1 = array('str'=>"a b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a\\sb",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_337() {
        $test1 = array('str'=>"a-b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a-b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"a b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a\\Sb",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_338() {
        $test1 = array('str'=>"1",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_339() {
        $test1 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"1",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\D",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_340() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\w]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_341() {
        $test1 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\W]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_342() {
        $test1 = array('str'=>"a b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[\\s]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_343() {
        $test1 = array('str'=>"a-b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a-b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"a b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[\\S]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_344() {
        $test1 = array('str'=>"1",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\d]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_345() {
        $test1 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"1",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\D]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_346() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"ab|cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_347() {
        $test1 = array('str'=>"def",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"()ef",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_348() {
        $test1 = array('str'=>"a(b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a\\(b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_349() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"a((b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"a\\(*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_350() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a\\\\b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_351() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>1,2=>1));

        return array('regex'=>"((a))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_352() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"(a)b(c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_353() {
        $test1 = array('str'=>"aabbabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"a+b+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_354() {
        $test1 = array('str'=>"aabbabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"a{1,}b{1,}c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_355() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.+?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_356() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_357() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b){0,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_358() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_359() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b){1,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_360() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+|b)?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_361() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+|b){0,1}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_362() {
        $test1 = array('str'=>"cde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"[^ab]*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_363() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_364() {
        $test1 = array('str'=>"abbbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        return array('regex'=>"([abc])*d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_365() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"([abc])*bcd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_366() {
        $test1 = array('str'=>"e",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a|b|c|d|e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_367() {
        $test1 = array('str'=>"ef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a|b|c|d|e)f",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_368() {
        $test1 = array('str'=>"abcdefg",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"abcd*efg",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_369() {
        $test1 = array('str'=>"xabyabbbz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"xayabbbz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"ab*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_370() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(ab|cd)e",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_371() {
        $test1 = array('str'=>"hij",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"[abhgefdc]ij",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_372() {
        $test1 = array('str'=>"abcdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>4),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"(abc|)ef",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_373() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a|b)c*d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_374() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(ab|ab*)bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_375() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"a([bc]*)c*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_376() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3),
                       'length'=>array(0=>4,1=>2,2=>1));

        return array('regex'=>"a([bc]*)(c*d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_377() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3),
                       'length'=>array(0=>4,1=>2,2=>1));

        return array('regex'=>"a([bc]+)(c*d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_378() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>4,1=>1,2=>2));

        return array('regex'=>"a([bc]*)(c+d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_379() {
        $test1 = array('str'=>"adcdcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"a[bcd]*dcdcde",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_380() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"abcde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"adcdcde",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[bcd]+dcdcde",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_381() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(ab|a)b*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_382() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>3),
                       'length'=>array(0=>4,1=>3,2=>1,3=>1,4=>1));

        return array('regex'=>"((a)(b)c)(d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_383() {
        $test1 = array('str'=>"alpha",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"[a-zA-Z_][a-zA-Z0-9_]*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_384() {
        $test1 = array('str'=>"abh",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"^a(bc+|b[eh])g|.h\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_385() {
        $test1 = array('str'=>"effgz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        $test2 = array('str'=>"ij",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        $test3 = array('str'=>"reffgz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>5,1=>5));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"effg",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"bcdd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(bc+d\$|ef*g.|h?i(j|k))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_386() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                       'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1));

        return array('regex'=>"((((((((((a))))))))))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_387() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                       'length'=>array(0=>2,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1));

        return array('regex'=>"((((((((((a))))))))))\\10",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_388() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0),
                       'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1));

        return array('regex'=>"(((((((((a)))))))))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_389() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"uh-uh",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"multiple words of text",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_390() {
        $test1 = array('str'=>"multiple words, yeah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14));

        return array('regex'=>"multiple words",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_391() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>5,1=>2,2=>2));

        return array('regex'=>"(.*)c(.*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_392() {
        $test1 = array('str'=>"(a, b)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>4),
                       'length'=>array(0=>6,1=>1,2=>1));

        return array('regex'=>"\\((.*), (.*)\\)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_393() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"abcd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_394() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"a(bc)d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_395() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[-]?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_396() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"(abc)\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_397() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"([a-c]*)\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_398() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5,1=>5),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test4 = array('str'=>"x",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a)|\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_399() {
        $test1 = array('str'=>"ababbbcbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>3),
                       'length'=>array(0=>5,1=>2,2=>1));

        return array('regex'=>"(([a-c])b*?\\2)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_400() {
        $test1 = array('str'=>"ababbbcbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6,2=>6),
                       'length'=>array(0=>9,1=>3,2=>1));

        return array('regex'=>"(([a-c])b*?\\2){3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_401() {
        $test1 = array('str'=>"aaaxabaxbaaxbbax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>12,1=>12,2=>12,3=>14),
                       'length'=>array(0=>4,1=>4,2=>1,3=>1));

        return array('regex'=>"((\\3|b)\\2(a)x)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_402() {
        $test1 = array('str'=>"bbaababbabaaaaabbaaaabba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>15,1=>21,2=>21,3=>23),
                       'length'=>array(0=>9,1=>3,2=>1,3=>1));

        return array('regex'=>"((\\3|b)\\2(a)){2,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_403() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"XABCY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"ABABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aaxabxbaxbbx",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"XBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"AXC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"ABX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"abc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_404() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab*c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_405() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ABBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"ab*bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_406() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab*?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_407() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{0,}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_408() {
        $test1 = array('str'=>"ABBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"ab+?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_409() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABQ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab+bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_410() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab+bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_411() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{1,}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_412() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{1,3}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_413() {
        $test1 = array('str'=>"ABBBBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"ab{3,4}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_414() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ABQ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABBBBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab{4,5}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_415() {
        $test1 = array('str'=>"ABBC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab??bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_416() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab{0,1}?bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_417() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab??c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_418() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"ab{0,1}?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_419() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABBBBC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ABCC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^abc\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_420() {
        $test1 = array('str'=>"ABCC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^abc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_421() {
        $test1 = array('str'=>"AABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        return array('regex'=>"abc\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_422() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_423() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>0));

        return array('regex'=>"\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_424() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"AXC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_425() {
        $test1 = array('str'=>"AXYZC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"a.*?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_426() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"AABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"AXYZD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a.*c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_427() {
        $test1 = array('str'=>"ABD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[bc]d",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_428() {
        $test1 = array('str'=>"ACE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ABD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[b-d]e",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_429() {
        $test1 = array('str'=>"AAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"a[b-d]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_430() {
        $test1 = array('str'=>"A-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[-b]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_431() {
        $test1 = array('str'=>"A-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[b-]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_432() {
        $test1 = array('str'=>"A]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_433() {
        $test1 = array('str'=>"A]B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[]]b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_434() {
        $test1 = array('str'=>"AED",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[^bc]d",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_435() {
        $test1 = array('str'=>"ADC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ABD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"A-C",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a[^-b]c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_436() {
        $test1 = array('str'=>"ADC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[^]b]c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_437() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"ab|cd",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_438() {
        $test1 = array('str'=>"DEF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"()ef",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_439() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"A]C",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"B",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\$b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_440() {
        $test1 = array('str'=>"A(B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a\\(b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_441() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"A((B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"a\\(*b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_442() {
        $test1 = array('str'=>"A",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a\\\\b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_443() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>1,2=>1));

        return array('regex'=>"((a))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_444() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"(a)b(c)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_445() {
        $test1 = array('str'=>"AABBABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"a+b+c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_446() {
        $test1 = array('str'=>"AABBABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"a{1,}b{1,}c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_447() {
        $test1 = array('str'=>"ABCABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.+?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_448() {
        $test1 = array('str'=>"ABCABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.*?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_449() {
        $test1 = array('str'=>"ABCABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a.{0,5}?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_450() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b)*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_451() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b){0,}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_452() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b)+",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_453() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a+|b){1,}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_454() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+|b)?",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_455() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+|b){0,1}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_456() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(a+|b){0,1}?",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_457() {
        $test1 = array('str'=>"CDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"[^ab]*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_458() {
        $test1 = array('str'=>"ABBBCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>1));

        return array('regex'=>"([abc])*d",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_459() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"([abc])*bcd",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_460() {
        $test1 = array('str'=>"E",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a|b|c|d|e",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_461() {
        $test1 = array('str'=>"EF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a|b|c|d|e)f",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_462() {
        $test1 = array('str'=>"ABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"abcd*efg",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_463() {
        $test1 = array('str'=>"XABYABBBZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"XAYABBBZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"ab*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_464() {
        $test1 = array('str'=>"ABCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(ab|cd)e",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_465() {
        $test1 = array('str'=>"HIJ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"[abhgefdc]ij",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_466() {
        $test1 = array('str'=>"ABCDE",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(ab|cd)e",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_467() {
        $test1 = array('str'=>"ABCDEF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>4),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"(abc|)ef",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_468() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a|b)c*d",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_469() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(ab|ab*)bc",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_470() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"a([bc]*)c*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_471() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3),
                       'length'=>array(0=>4,1=>2,2=>1));

        return array('regex'=>"a([bc]*)(c*d)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_472() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3),
                       'length'=>array(0=>4,1=>2,2=>1));

        return array('regex'=>"a([bc]+)(c*d)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_473() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>4,1=>1,2=>2));

        return array('regex'=>"a([bc]*)(c+d)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_474() {
        $test1 = array('str'=>"ADCDCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"a[bcd]*dcdcde",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_475() {
        $test1 = array('str'=>"ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(ab|a)b*c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_476() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>3),
                       'length'=>array(0=>4,1=>3,2=>1,3=>1,4=>1));

        return array('regex'=>"((a)(b)c)(d)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_477() {
        $test1 = array('str'=>"ALPHA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"[a-zA-Z_][a-zA-Z0-9_]*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_478() {
        $test1 = array('str'=>"ABH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"^a(bc+|b[eh])g|.h\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_479() {
        $test1 = array('str'=>"EFFGZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        $test2 = array('str'=>"IJ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        $test3 = array('str'=>"REFFGZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>5,1=>5));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ADCDCDE",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"EFFG",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"BCDD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(bc+d\$|ef*g.|h?i(j|k))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_480() {
        $test1 = array('str'=>"A",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                       'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1));

        return array('regex'=>"((((((((((a))))))))))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_481() {
        $test1 = array('str'=>"AA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                       'length'=>array(0=>2,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1));

        return array('regex'=>"((((((((((a))))))))))\\10",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_482() {
        $test1 = array('str'=>"A",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0),
                       'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1));

        return array('regex'=>"(((((((((a)))))))))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_483() {
        $test1 = array('str'=>"A",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?:(?:(?:(?:(?:(?:(?:(?:(?:(a))))))))))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_484() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?:(?:(?:(?:(?:(?:(?:(?:(?:(a|b|c))))))))))",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_485() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"AA",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"UH-UH",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"multiple words of text",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_486() {
        $test1 = array('str'=>"MULTIPLE WORDS, YEAH",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>14));

        return array('regex'=>"multiple words",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_487() {
        $test1 = array('str'=>"ABCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>5,1=>2,2=>2));

        return array('regex'=>"(.*)c(.*)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_488() {
        $test1 = array('str'=>"(A, B)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>4),
                       'length'=>array(0=>6,1=>1,2=>1));

        return array('regex'=>"\\((.*), (.*)\\)",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_489() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"abcd",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_490() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"a(bc)d",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_491() {
        $test1 = array('str'=>"AC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"a[-]?c",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_492() {
        $test1 = array('str'=>"ABCABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"(abc)\\1",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_493() {
        $test1 = array('str'=>"ABCABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"([a-c]*)\\1",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_494() {
        $test1 = array('str'=>"abad",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"a(?!b).",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

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

    function data_for_test_497() {
        $test1 = array('str'=>"ace",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"a(?:b|c|d)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_498() {
        $test1 = array('str'=>"ace",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"a(?:b|c|d)*(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_499() {
        $test1 = array('str'=>"ace",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"a(?:b|c|d)+?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_500() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"a(?:b|c|d)+(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_501() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"a(?:b|c|d){2}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_502() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"a(?:b|c|d){4,5}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_503() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>6,1=>1));

        return array('regex'=>"a(?:b|c|d){4,5}?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_504() {
        $test1 = array('str'=>"foobar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>0,3=>3),
                       'length'=>array(0=>6,1=>3,2=>3,3=>3));

        return array('regex'=>"((foo)|(bar))*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_505() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"a(?:b|c|d){6,7}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_506() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"a(?:b|c|d){6,7}?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_507() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"a(?:b|c|d){5,6}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_508() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"a(?:b|c|d){5,6}?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_509() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        return array('regex'=>"a(?:b|c|d){5,7}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_510() {
        $test1 = array('str'=>"acdbcdbe",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>7,1=>1));

        return array('regex'=>"a(?:b|c|d){5,7}?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_511() {
        $test1 = array('str'=>"ace",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"a(?:b|(c|e){1,2}?|d)+?(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_512() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"^(.+)?B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_513() {
        $test1 = array('str'=>".",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"^([^a-z])|(\\^)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_514() {
        $test1 = array('str'=>"<&OUT",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[<>]&",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_515() {
        $test1 = array('str'=>"aaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>10,1=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a\\1?){4}\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_516() {
        $test1 = array('str'=>"aaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>10,1=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a(?(1)\\1)){4}\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_517() {
        $test1 = array('str'=>"foobar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5),
                       'length'=>array(0=>6,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1));

        return array('regex'=>"(?:(f)(o)(o)|(b)(a)(r))*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
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

    function data_for_test_519() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<!c)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_520() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?:..)*a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_521() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?:..)*?a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
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

    function data_for_test_523() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"^(){3,5}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_524() {
        $test1 = array('str'=>"aax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"^(a+)*ax",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_525() {
        $test1 = array('str'=>"aax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"^((a|b)+)*ax",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_526() {
        $test1 = array('str'=>"aax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"^((a|bc)+)*ax",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_527() {
        $test1 = array('str'=>"cab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(a|x)*ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_528() {
        $test1 = array('str'=>"cab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(a)*ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_529() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(?i)a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_530() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?i)a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_531() {
        $test1 = array('str'=>"Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(?i)a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_532() {
        $test1 = array('str'=>"Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?i)a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_533() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"cb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?i)a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_534() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?i:a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_535() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?i:a))b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_536() {
        $test1 = array('str'=>"Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?i:a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_537() {
        $test1 = array('str'=>"Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?i:a))b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_538() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?i:a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_539() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_540() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_541() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_542() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_543() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"Ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_544() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_545() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_546() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"Ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?-i)a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_547() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?-i:a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_548() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i:a))b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_549() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?-i:a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_550() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i:a))b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_551() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"Ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?-i:a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_552() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?-i:a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_553() {
        $test1 = array('str'=>"aB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?-i:a))b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_554() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"Ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?-i:a)b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_555() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"AB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a\nB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((?-i:a.))b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_556() {
        $test1 = array('str'=>"a\nB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"((?s-i:a.))b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_557() {
        $test1 = array('str'=>"cabbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"(?:c|d)(?:)(?:a(?:)(?:b)(?:b(?:))(?:b(?:)(?:b)))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_558() {
        $test1 = array('str'=>"caaaaaaaabbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>41));

        return array('regex'=>"(?:c|d)(?:)(?:aaaaaaaa(?:)(?:bbbbbbbb)(?:bbbbbbbb(?:))(?:bbbbbbbb(?:)(?:bbbbbbbb)))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_559() {
        $test1 = array('str'=>"Ab4ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        $test2 = array('str'=>"ab4Ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"(ab)\\d\\1",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_560() {
        $test1 = array('str'=>"foobar1234baz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>13));

        return array('regex'=>"foo\\w*\\d{4}baz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_561() {
        $test1 = array('str'=>"x~~",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"x(~~)*(?:(?:F)?)?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_562() {
        $test1 = array('str'=>"aaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^a(?#xxx){3}c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_563() {
        $test1 = array('str'=>"aaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^a (?#xxx) (?#yyy) {3}c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_564() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"B\nB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"dbcb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<![cd])b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_565() {
        $test1 = array('str'=>"dbaacb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<![cd])[ab]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_566() {
        $test1 = array('str'=>"dbaacb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<!(c|d))[ab]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_567() {
        $test1 = array('str'=>"cdaccb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<!cd)[ab]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_568() {
        $test1 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"dbcb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"a--",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"aa--",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:a?b?)*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_569() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>2,2=>1,3=>1));

        return array('regex'=>"((?s)^a(.))((?m)^b\$)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_570() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"((?m)^b\$)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_571() {
        $test1 = array('str'=>"a\nb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?m)^b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_572() {
        $test1 = array('str'=>"a\nb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?m)^(b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_573() {
        $test1 = array('str'=>"a\nb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"((?m)^b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_574() {
        $test1 = array('str'=>"a\nb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>2),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"\\n((?m)^b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_575() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>3),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>3),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?s).)c(?!.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_576() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        $test2 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"((?s)b.)c(?!.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_577() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"a\nb\nc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"a\nb\nc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"()^b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_578() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>2),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"((?m)^b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_579() {
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

        return array('regex'=>"(x)?(?(1)a|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_580() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(x)?(?(1)b|a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_581() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"()?(?(1)b|a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_582() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        return array('regex'=>"()?(?(1)a|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_583() {
        $test1 = array('str'=>"(blah)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>6,1=>1,2=>1));

        $test2 = array('str'=>"blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"blah)",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"(blah",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\()?blah(?(1)(\\)))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_584() {
        $test1 = array('str'=>"(blah)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>6,1=>1,2=>1));

        $test2 = array('str'=>"blah",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"blah)",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"(blah",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\(+)?blah(?(1)(\\)))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_585() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?!a)b|a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_586() {
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
    }

    function data_for_test_587() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?(?=a)a|b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_588() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>1),
                       'length'=>array(0=>3,1=>1,2=>3));

        return array('regex'=>"(?=(a+?))(\\1ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_589() {
        $test1 = array('str'=>"one:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        return array('regex'=>"(\\w+:)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
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

    function data_for_test_591() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>1),
                       'length'=>array(0=>3,1=>1,2=>3));

        return array('regex'=>"(?=(a+?))(\\1ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_592() {
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
    }

    function data_for_test_593() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>4,2=>4));

        $test2 = array('str'=>"xy:z:::abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7),
                       'length'=>array(0=>11,1=>7,2=>4));

        return array('regex'=>"([\\w:]+::)?(\\w+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_594() {
        $test1 = array('str'=>"aexycd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        return array('regex'=>"^[^bcd]*(c+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_595() {
        $test1 = array('str'=>"caab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(a*)b+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_596() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>4,2=>4));

        $test2 = array('str'=>"xy:z:::abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7),
                       'length'=>array(0=>11,1=>7,2=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,2=>4),
                       'length'=>array(0=>7,2=>7));

        $test4 = array('str'=>"abcd:",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abcd:",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"([\\w:]+::)?(\\w+)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_597() {
        $test1 = array('str'=>"aexycd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        return array('regex'=>"^[^bcd]*(c+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_598() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"(?>a+)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_599() {
        $test1 = array('str'=>"a:[b]:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"([[:]+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_600() {
        $test1 = array('str'=>"a=[b]=",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"([[=]+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_601() {
        $test1 = array('str'=>"a.[b].",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"([[.]+)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_602() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        return array('regex'=>"((?>a+)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_603() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(?>(a+))b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_604() {
        $test1 = array('str'=>"((abc(ade)ufh()()x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>17),
                       'length'=>array(0=>16,1=>1));

        return array('regex'=>"((?>[^()]+)|\\([^()]*\\))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_605() {
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

        $test3 = array('str'=>"a\nb\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a\\Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_606() {
        $test1 = array('str'=>"a\nb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"b\\Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_607() {
        $test1 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"b\\Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_608() {
        $test1 = array('str'=>"a\nb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"b\\z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_609() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>0));

        $test3 = array('str'=>"a-b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>0));

        $test4 = array('str'=>"0-9",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>0));

        $test5 = array('str'=>"a.b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>0));

        $test6 = array('str'=>"5.6.7",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>0));

        $test7 = array('str'=>"the.quick.brown.fox",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>19,1=>0));

        $test8 = array('str'=>"a100.b200.300c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>14,1=>0));

        $test9 = array('str'=>"12-ab.1245",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>10,1=>0));

        $test10 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test12 = array('str'=>".a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test13 = array('str'=>"-a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test14 = array('str'=>"a-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test15 = array('str'=>"a.",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test16 = array('str'=>"a_b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test17 = array('str'=>"a.-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test18 = array('str'=>"a..",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test19 = array('str'=>"ab..bc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test20 = array('str'=>"the.quick.brown.fox-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test21 = array('str'=>"the.quick.brown.fox.",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test22 = array('str'=>"the.quick.brown.fox_",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test23 = array('str'=>"the.quick.brown.fox+",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?>(?(1)\\.|())[^\\W_](?>[a-z0-9-]*[^\\W_])?)+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23));
    }

    function data_for_test_610() {
        $test1 = array('str'=>"alphabetabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>12,1=>4));

        $test2 = array('str'=>"endingwxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>10,1=>4));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"a rather long string that doesn't end with one of them",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>.*)(?<=(abcd|wxyz))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_611() {
        $test1 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark otherword",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>74));

        $test2 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"word (?>(?:(?!otherword)[a-zA-Z0-9]+ ){0,30})otherword",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_612() {
        $test1 = array('str'=>"word cat dog elephant mussel cow horse canary baboon snake shark the quick brown fox and the lazy dog and several other words getting close to thirty by now I hope",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"word (?>[a-zA-Z0-9]+ ){0,30}otherword",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_613() {
        $test1 = array('str'=>"999foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"123999foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"123abcfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=\\d{3}(?!999))foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_614() {
        $test1 = array('str'=>"999foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"123999foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"123abcfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(?!...999)\\d{3})foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_615() {
        $test1 = array('str'=>"123abcfoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"123456foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"123999foo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=\\d{3}(?!999)...)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_616() {
        $test1 = array('str'=>"123abcfoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"123456foo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"123999foo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=\\d{3}...)(?<!999)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_617() {
        $test1 = array('str'=>"<a href=abcd xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>8),
                       'length'=>array(0=>12,3=>4));

        $test2 = array('str'=>"<a href=\"abcd xyz pqr\" cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8,2=>9),
                       'length'=>array(0=>22,1=>1,2=>12));

        $test3 = array('str'=>"<a href='abcd xyz pqr' cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8,2=>9),
                       'length'=>array(0=>22,1=>1,2=>12));

        return array('regex'=>"<a[\\s]+href[\\s]*=[\\s]*          # find <a href=\n ([\\\"\\'])?                       # find single or double quote\n (?(1) (.*?)\\1 | ([^\\s]+))       # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n",
                     'modifiers'=>"isx",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_618() {
        $test1 = array('str'=>"<a href=abcd xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>8),
                       'length'=>array(0=>12,3=>4));

        $test2 = array('str'=>"<a href=\"abcd xyz pqr\" cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8,2=>9),
                       'length'=>array(0=>22,1=>1,2=>12));

        $test3 = array('str'=>"<a href       =       'abcd xyz pqr' cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>22,2=>23),
                       'length'=>array(0=>36,1=>1,2=>12));

        return array('regex'=>"<a\\s+href\\s*=\\s*                # find <a href=\n ([\"'])?                         # find single or double quote\n (?(1) (.*?)\\1 | (\\S+))          # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n",
                     'modifiers'=>"isx",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_619() {
        $test1 = array('str'=>"<a href=abcd xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>8),
                       'length'=>array(0=>12,3=>4));

        $test2 = array('str'=>"<a href=\"abcd xyz pqr\" cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8,2=>9),
                       'length'=>array(0=>22,1=>1,2=>12));

        $test3 = array('str'=>"<a href       =       'abcd xyz pqr' cats",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>22,2=>23),
                       'length'=>array(0=>36,1=>1,2=>12));

        return array('regex'=>"<a\\s+href(?>\\s*)=(?>\\s*)        # find <a href=\n ([\"'])?                         # find single or double quote\n (?(1) (.*?)\\1 | (\\S+))          # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n",
                     'modifiers'=>"isx",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_620() {
        $test1 = array('str'=>"ZABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>0),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"((Z)+|A)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_621() {
        $test1 = array('str'=>"ZABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>1,2=>0));

        return array('regex'=>"(Z()|A)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_622() {
        $test1 = array('str'=>"ZABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1,3=>1),
                       'length'=>array(0=>2,1=>1,2=>0,3=>0));

        return array('regex'=>"(Z(())|A)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_623() {
        $test1 = array('str'=>"ZABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((?>Z)+|A)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_624() {
        $test1 = array('str'=>"ZABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"((?>)+|A)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_625() {
        $test1 = array('str'=>"abbab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_626() {
        $test1 = array('str'=>"abcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"-things",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"0digit",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"bcdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[\\d-a]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_627() {
        $test1 = array('str'=>"> 	\n\r<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        return array('regex'=>"[[:space:]]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_628() {
        $test1 = array('str'=>"> 	\n\r<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"[[:blank:]]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_629() {
        $test1 = array('str'=>"> 	\n\r<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        return array('regex'=>"[\\s]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_630() {
        $test1 = array('str'=>"> 	\n\r<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        return array('regex'=>"\\s+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_631() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"ab",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_632() {
        $test1 = array('str'=>"a\nxb\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?!\\A)x",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_633() {
        $test1 = array('str'=>"a\nxb\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?!^)x",
                     'modifiers'=>"m",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_634() {
        $test1 = array('str'=>"abcabcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        return array('regex'=>"abc\\Qabc\\Eabc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_635() {
        $test1 = array('str'=>"abc(*+|abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"abc\\Q(*+|\\Eabc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_636() {
        $test1 = array('str'=>"abc abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcabcabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"   abc\\Q abc\\Eabc",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_637() {
        $test1 = array('str'=>"abc#not comment\n    literal",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        return array('regex'=>"abc#comment\n    \\Q#not comment\n    literal\\E",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_638() {
        $test1 = array('str'=>"abc#not comment\n    literal",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        return array('regex'=>"abc#comment\n    \\Q#not comment\n    literal",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_639() {
        $test1 = array('str'=>"abc#not comment\n    literal",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        return array('regex'=>"abc#comment\n    \\Q#not comment\n    literal\\E #more comment\n    ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_640() {
        $test1 = array('str'=>"abc#not comment\n    literal",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>27));

        return array('regex'=>"abc#comment\n    \\Q#not comment\n    literal\\E #more comment",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_641() {
        $test1 = array('str'=>"abc\\\$xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"\\Qabc\\\$xyz\\E",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_642() {
        $test1 = array('str'=>"abc\$xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"\\Qabc\\E\\\$\\Qxyz\\E",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_643() {
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

        $test3 = array('str'=>"xyzabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\Gabc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_644() {
        $test1 = array('str'=>"abc1abc2xyzabc3",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"\\Gabc.",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_645() {
        $test1 = array('str'=>"abc1abc2xyzabc3",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"abc.",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_646() {
        $test1 = array('str'=>"XabcdY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"Xa b c d Y",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(?x: b c )d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_647() {
        $test1 = array('str'=>"XabcY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"AxyzB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"((?x)x y z | a b c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_648() {
        $test1 = array('str'=>"XabCY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"XabcY",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?i)AB(?-i)C",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_649() {
        $test1 = array('str'=>"abCE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        $test2 = array('str'=>"DE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcE",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abCe",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"dE",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"De",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((?i)AB(?-i)C|D)E",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_650() {
        $test1 = array('str'=>"abc123abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>3));

        $test2 = array('str'=>"abc123bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>7,1=>2));

        return array('regex'=>"(.*)\\d+\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_651() {
        $test1 = array('str'=>"abc123abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>3));

        $test2 = array('str'=>"abc123bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>7,1=>2));

        return array('regex'=>"(.*)\\d+\\1",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_652() {
        $test1 = array('str'=>"abc123abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>9,1=>3,2=>3));

        $test2 = array('str'=>"abc123bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1,2=>1),
                       'length'=>array(0=>7,1=>2,2=>2));

        return array('regex'=>"((.*))\\d+\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_653() {
        $test1 = array('str'=>"a123::a123",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>10,1=>0));

        $test2 = array('str'=>"a123:b342::abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>10),
                       'length'=>array(0=>15,1=>0));

        $test3 = array('str'=>"a123:b342::324e:abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>10),
                       'length'=>array(0=>20,1=>0));

        $test4 = array('str'=>"a123:ddde:b342::324e:abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>15),
                       'length'=>array(0=>25,1=>0));

        $test5 = array('str'=>"a123:ddde:b342::324e:dcba:abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>15),
                       'length'=>array(0=>30,1=>0));

        $test6 = array('str'=>"a123:ddde:9999:b342::324e:dcba:abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>20),
                       'length'=>array(0=>35,1=>0));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"1:2:3:4:5:6:7:8",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"a123:bce:ddde:9999:b342::324e:dcba:abcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"a123::9999:b342::324e:dcba:abcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test11 = array('str'=>"abcde:2:3:4:5:6:7:8",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test12 = array('str'=>"::1",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test13 = array('str'=>"abcd:fee0:123::",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test14 = array('str'=>":1",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test15 = array('str'=>"1:",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?!:)                       # colon disallowed at start\n  (?:                         # start of item\n    (?: [0-9a-f]{1,4} |       # 1-4 hex digits or\n    (?(1)0 | () ) )           # if null previously matched, fail; else null\n    :                         # followed by colon\n  ){1,7}                      # end item; 1-7 of them required               \n  [0-9a-f]{1,4} \$             # final hex number at end of string\n  (?(1)|.)                    # check that there was an empty component\n  ",
                     'modifiers'=>"xi",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15));
    }

    function data_for_test_654() {
        $test1 = array('str'=>"z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"d",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[z\\Qa-d]\\E]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_655() {
        $test1 = array('str'=>"z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\z\\C]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_656() {
        $test1 = array('str'=>"M",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\M",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_657() {
        $test1 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a+)*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_658() {
Error -10 (bad UTF-8 string) offset=0 reason=20
str: �XAZXB
        return array('regex'=>"(?<=Z)X.",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_659() {
        $test1 = array('str'=>"ab cd defg",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"ab cd (?x) de fg",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_660() {
        $test1 = array('str'=>"ab cddefg",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcddefg",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"ab cd(?x) de fg",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_661() {
        $test1 = array('str'=>"foobarX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>3),
                       'length'=>array(0=>3,1=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"boobarX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<![^f]oo)(bar)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_662() {
        $test1 = array('str'=>"offX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"onyX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<![^f])X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
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

    function data_for_test_664() {
        $test1 = array('str'=>"a\nb\nc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^",
                     'modifiers'=>"mg",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
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

    function data_for_test_666() {
        $test1 = array('str'=>"bXaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(?:(?(1)a|b)(X))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_667() {
        $test1 = array('str'=>"bXXaYYaY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        $test2 = array('str'=>"bXYaXXaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(?:(?(1)\\1a|b)(X|Y))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_668() {
        $test1 = array('str'=>"bXXaYYaY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>1),
                       'length'=>array(0=>2,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>1));

        return array('regex'=>"()()()()()()()()()(?:(?(10)\\10a|b)(X|Y))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_669() {
        $test1 = array('str'=>"abc]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"a,b]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"[a,b,c]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"[[,abc,]+]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_670() {
        $test1 = array('str'=>"A B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?-x: )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_671() {
        $test1 = array('str'=>"A # B",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"#",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?x)(?-x: \\s*#\\s*)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_672() {
        $test1 = array('str'=>"A #include",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>9));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"A#include",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"A #Include",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?x-is)(?:(?-ixs) \\s*#\\s*) include",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_673() {
        $test1 = array('str'=>"aaabbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test2 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b*\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_674() {
        $test1 = array('str'=>"aaabbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        $test2 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b?\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_675() {
        $test1 = array('str'=>"aaabbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test2 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b{0,4}\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_676() {
        $test1 = array('str'=>"aaabbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test2 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b{0,}\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_677() {
        $test1 = array('str'=>"0a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*\\d*\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_678() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b *\\w",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_679() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*b#comment\n  *\\w",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_680() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a* b *\\w",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_681() {
        $test1 = array('str'=>"abc=xyz\\\npqr",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"^\\w+=.*(\\\\\\n.*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_682() {
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
    }

    function data_for_test_684() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^\\Eabc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_685() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"E",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[\\Eabc]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_686() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"E",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[a-\\Ec]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_687() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"E",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[a\\E\\E-\\Ec]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_688() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"-",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[\\E\\Qa\\E-\\Qz\\E]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_689() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^[a\\Q]bc\\E]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_690() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"-",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^[a-\\Q\\E]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_691() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>4),
                       'length'=>array(0=>4,1=>1,2=>0));

        return array('regex'=>"^(a()*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_692() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^(?:a(?:(?:))*)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_693() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>4),
                       'length'=>array(0=>4,1=>1,2=>0));

        return array('regex'=>"^(a()+)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_694() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^(?:a(?:(?:))+)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_695() {
        $test1 = array('str'=>"abbD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        $test2 = array('str'=>"ccccD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>4),
                       'length'=>array(0=>5,2=>0));

        $test3 = array('str'=>"D",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>1,2=>0));

        return array('regex'=>"(a){0,3}(?(1)b|(c|))*D",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_696() {
        $test1 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>60),
                       'length'=>array(0=>61,1=>0));

        return array('regex'=>"(a|)*\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_697() {
        $test1 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>61));

        return array('regex'=>"(?>a|)*\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_698() {
        $test1 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>61));

        return array('regex'=>"(?:a|)*\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_699() {
        $test1 = array('str'=>"abc\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>0));

        return array('regex'=>"\\Z",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_700() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"abc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?s)(?>.*)(?<!\\n)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_701() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?![^\\n]*\\n\\z)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_702() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"abc\n",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\z(?<!\\n)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_703() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>4,1=>0));

        return array('regex'=>"(.*(.)?)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_704() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>0,1=>0,2=>0));

        return array('regex'=>"( (A | (?(1)0|) )*   )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_705() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>0,1=>0,2=>0));

        return array('regex'=>"( ( (?(1)0|) )*   )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_706() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(  (?(1)0|)*   )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_707() {
        $test1 = array('str'=>"a]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>":]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"[[:abcd:xyz]]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_708() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"[",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>":",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"p",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[abc[:x\\]pqr]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_709() {
        $test1 = array('str'=>"fooabcfoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>".*[op][xyz]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_710() {
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
    }

    function data_for_test_711() {
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
    }

    function data_for_test_717() {
        $test1 = array('str'=>"XabX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"XAbX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"CcC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"XABX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?i)a(?-i)b|c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_718() {
        $test1 = array('str'=>"\n\r",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"[\\x00-\\xff\\s]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_719() {
        $test1 = array('str'=>"?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^\\c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_720() {
        $test1 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(abc)\\1",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_721() {
        $test1 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(abc)\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_722() {
        $test1 = array('str'=>"12abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"12ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]*",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_723() {
        $test1 = array('str'=>"12abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"12ABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]*+",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_724() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"12abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"12ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^a]*?X",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_725() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"12abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"12ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^a]+?X",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_726() {
        $test1 = array('str'=>"12aXbcX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"12AXBCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"BCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]?X",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_727() {
        $test1 = array('str'=>"12aXbcX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"12AXBCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"BCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]??X",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_728() {
        $test1 = array('str'=>"12aXbcX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"12AXBCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"BCX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]?+X",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_729() {
        $test1 = array('str'=>"abcdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ABCDEF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        return array('regex'=>"[^a]{2,3}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_730() {
        $test1 = array('str'=>"abcdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ABCDEF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"[^a]{2,3}?",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_731() {
        $test1 = array('str'=>"abcdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ABCDEF",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        return array('regex'=>"[^a]{2,3}+",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_732() {
        $test1 = array('str'=>"Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>0,2=>0));

        return array('regex'=>"((a|)+)+Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_733() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"(a)b|(a)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_734() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"(?>(a))b|(a)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

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

    function data_for_test_736() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>2,1=>2,3=>1));

        return array('regex'=>"((?>(a))b|(a)c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_737() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>2,1=>2,3=>1));

        return array('regex'=>"((?>(a))b|(a)c)++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_738() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"(?:(?>(a))b|(a)c)++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_739() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0,3=>0),
                       'length'=>array(0=>2,2=>1,3=>2));

        return array('regex'=>"(?=(?>(a))b|(a)c)(..)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_740() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"(?>(?>(a))b|(a)c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_741() {
        $test1 = array('str'=>"=ba=",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(?:(?>([ab])))+a=",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_742() {
        $test1 = array('str'=>"=ba=",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(?>([ab]))+a=",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_743() {
        $test1 = array('str'=>"aaaabaaabaabab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5,3=>9),
                       'length'=>array(0=>14,1=>14,2=>3,3=>5));

        return array('regex'=>"((?>(a+)b)+(aabab))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_744() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>a+|ab)+?c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_745() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>a+|ab)+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_746() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"(?:a+|ab)+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_747() {
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
    }

    function data_for_test_749() {
        $test1 = array('str'=>"aaaabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:a|ab)++c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_750() {
        $test1 = array('str'=>"aaaabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?>a|ab)++c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_751() {
        $test1 = array('str'=>"aaaabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"^(?:a|ab)+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

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

    function data_for_test_754() {
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
    }

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

    function data_for_test_756() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"xyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=abc){1}xyz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
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

    function data_for_test_760() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"^(?!a){0}\\w+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
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

    function data_for_test_762() {
        $test1 = array('str'=>"ggg<<<aaa>>>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"\\ga",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[\\g<a>]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_763() {
        $test1 = array('str'=>"gggagagaxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        return array('regex'=>"^[\\ga]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_764() {
        $test1 = array('str'=>"aaaa444:::Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        return array('regex'=>"^[:a[:digit:]]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_765() {
        $test1 = array('str'=>"aaaa444:::bbbZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>13));

        return array('regex'=>"^[:a[:digit:]:b]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_766() {
        $test1 = array('str'=>":xxx:",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"[:a]xxx[b:]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
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

    function data_for_test_768() {
        $test1 = array('str'=>"xabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"xaabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<!a{2})b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
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

    function data_for_test_775() {
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
    }

    function data_for_test_777() {
        $test1 = array('str'=>"aaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>7,1=>3));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a{2,3}){2,}+a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_778() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a{2,3})++a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_779() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"aaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a{2,3})*+a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_780() {
        $test1 = array('str'=>"abXde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"ab\\Cde",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_781() {
        $test1 = array('str'=>"aCb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"aDb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[\\CD]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_782() {
        $test1 = array('str'=>"aJb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"a[\\C-X]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_783() {
        $test1 = array('str'=>"X X\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"X	X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

/*Error -10 (bad UTF-8 string) offset=0 reason=20
str: � X\n*/
        return array('regex'=>"\\H\\h\\V\\v",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3/*, $test4*/));
    }

    function data_for_test_784() {
/*Error -10 (bad UTF-8 string) offset=2 reason=20
str: 	 �X\n\r\n
Error -10 (bad UTF-8 string) offset=2 reason=20
str: 	 �\n\r\n
Error -10 (bad UTF-8 string) offset=2 reason=20
str: 	 �\n*/
        $test4 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

/*Error -10 (bad UTF-8 string) offset=2 reason=20
str: 	 �\n*/
        return array('regex'=>"\\H*\\h+\\V?\\v{3,4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array(/*$test1, $test2, $test3,*/ $test4/*, $test5*/));
    }

    function data_for_test_785() {
        $test1 = array('str'=>"XY  ABCDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"XY  PQR ST",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"\\H{3,4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_786() {
        $test1 = array('str'=>"XY  AB    PQRS",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>6));

        return array('regex'=>".\\h{3,4}.",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_787() {
        $test1 = array('str'=>">XNNNYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>6));

        $test2 = array('str'=>">  X NYQZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>8));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>">XYZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>">  X NY Z",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\h*X\\h?\\H+Y\\H?Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_788() {
        $test1 = array('str'=>">XY\nZ\nANN",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>10));

        $test2 = array('str'=>">\n\rX\nY\nZZZ\nAAANNN",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>19));

        return array('regex'=>"\\v*X\\v?Y\\v+Z\\V*\\x0a\\V+\\x0b\\V{2,3}\\x0c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_789() {
        $test1 = array('str'=>"foobar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"(foo)\\Kbar",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_790() {
        $test1 = array('str'=>"foobar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0,2=>3),
                       'length'=>array(0=>3,1=>3,2=>3));

        $test2 = array('str'=>"foobaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>6,1=>3,2=>3));

        return array('regex'=>"(foo)(\\Kbar|baz)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_791() {
        $test1 = array('str'=>"foobarbaz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(foo\\Kbar)baz",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_792() {
        $test1 = array('str'=>"Xabcdefghi",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>0));

        return array('regex'=>"abc\\K|def\\K",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_793() {
        $test1 = array('str'=>"Xabcdefghi",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"ab\\Kc|de\\Kf",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

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

    function data_for_test_795() {
        $test1 = array('str'=>"abcdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"defabcxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^abc\\K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_796() {
        $test1 = array('str'=>"ababababbbabZXXXX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>13,1=>2,2=>1));

        return array('regex'=>"^(a(b))\\1\\g1\\g{1}\\g-1\\g{-1}\\g{-02}Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_797() {
        $test1 = array('str'=>"tom-tom",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>3));

        $test2 = array('str'=>"bon-bon",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>3));

        return array('regex'=>"(?<A>tom|bon)-\\g{A}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_798() {
        $test1 = array('str'=>"bacxxx",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(^(a|b\\g{-1}))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_799() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"xyzxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"xyzabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?|(abc)|(xyz))\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_800() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"xyzabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xyzxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?|(abc)|(xyz))(?1)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_801() {
        $test1 = array('str'=>"XYabcdY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>3,3=>4,4=>5,5=>6),
                       'length'=>array(0=>7,1=>1,2=>1,3=>1,4=>1,5=>1));

        return array('regex'=>"^X(?5)(a)(?|(b)|(q))(c)(d)(Y)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_802() {
        $test1 = array('str'=>"XYabcdY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>3,5=>4,6=>5,7=>6),
                       'length'=>array(0=>7,1=>1,2=>1,5=>1,6=>1,7=>1));

        return array('regex'=>"^X(?7)(a)(?|(b|(r)(s))|(q))(c)(d)(Y)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_803() {
        $test1 = array('str'=>"XYabcdY",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>3,5=>4,6=>5,7=>6),
                       'length'=>array(0=>7,1=>1,2=>1,5=>1,6=>1,7=>1));

        return array('regex'=>"^X(?7)(a)(?|(b|(?|(r)|(t))(s))|(q))(c)(d)(Y)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_804() {
        $test1 = array('str'=>"a:aaxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        $test2 = array('str'=>"ab:ababxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>2));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"a:axyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab:abxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?'abc'\\w+):\\k<abc>{2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_805() {
        $test1 = array('str'=>"a:aaxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        $test2 = array('str'=>"ab:ababxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>2));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"a:axyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ab:abxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?'abc'\\w+):\\g{abc}{2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_806() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"ce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?<ab>a)? (?(<ab>)b|c) (?('ab')d|e)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_807() {
        $test1 = array('str'=>"aXaXZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"^(a.)\\g-1Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_808() {
        $test1 = array('str'=>"aXaXZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"^(a.)\\g{-1}Z",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_809() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?(DEFINE) (?<A> a) (?<B> b) )  (?&A) (?&B) ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_810() {
        $test1 = array('str'=>"metcalfe 33",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>9),
                       'length'=>array(0=>11,1=>8,2=>2));

        return array('regex'=>"(?<NAME>(?&NAME_PAT))\\s+(?<ADDR>(?&ADDRESS_PAT))\n  (?(DEFINE)\n  (?<NAME_PAT>[a-z]+)\n  (?<ADDRESS_PAT>\\d+)\n  )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_811() {
        $test1 = array('str'=>"1.2.3.4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>5),
                       'length'=>array(0=>7,2=>2));

        $test2 = array('str'=>"131.111.10.206",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>10),
                       'length'=>array(0=>14,2=>4));

        $test3 = array('str'=>"10.0.0.0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>6),
                       'length'=>array(0=>8,2=>2));

        $test4 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"10.6",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"455.3.4.5",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?(DEFINE)(?<byte>2[0-4]\\d|25[0-5]|1\\d\\d|[1-9]?\\d))\\b(?&byte)(\\.(?&byte)){3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_812() {
        $test1 = array('str'=>"1.2.3.4",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>2));

        $test2 = array('str'=>"131.111.10.206",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>10),
                       'length'=>array(0=>14,1=>4));

        $test3 = array('str'=>"10.0.0.0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>6),
                       'length'=>array(0=>8,1=>2));

        $test4 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"10.6",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"455.3.4.5",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\b(?&byte)(\\.(?&byte)){3}(?(DEFINE)(?<byte>2[0-4]\\d|25[0-5]|1\\d\\d|[1-9]?\\d))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_813() {
        $test1 = array('str'=>"now is the time for all good men to come to the aid of the party",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>59),
                       'length'=>array(0=>64,1=>5));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"this is not a line with only words and spaces!",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\w++|\\s++)*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_814() {
        $test1 = array('str'=>"12345a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>6,1=>5,2=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"12345+",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(\\d++)(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_815() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"a++b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_816() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        return array('regex'=>"(a++b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_817() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>3));

        return array('regex'=>"(a++)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_818() {
        $test1 = array('str'=>"((abc(ade)ufh()()x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2,1=>17),
                       'length'=>array(0=>16,1=>1));

        return array('regex'=>"([^()]++|\\([^()]*\\))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_819() {
        $test1 = array('str'=>"(abc)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"(abc(def)xyz)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>13,1=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"((()aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\(([^()]++|\\([^()]+\\))+\\)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_820() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"a(b)c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        $test3 = array('str'=>"a(b(c))d",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>8,1=>1));

        $test4 = array('str'=>"*** Failers)",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"a(b(c)d",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^([^()]|\\((?1)*\\))*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_821() {
        $test1 = array('str'=>">abc>123<xyz<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>13,1=>1));

        $test2 = array('str'=>">abc>1(2)3<xyz<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>9),
                       'length'=>array(0=>15,1=>1));

        $test3 = array('str'=>">abc>(1(2)3)<xyz<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>17,1=>7));

        return array('regex'=>"^>abc>([^()]|\\((?1)*\\))*<xyz<\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_822() {
        $test1 = array('str'=>"1221",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>4,1=>4,2=>1));

        $test2 = array('str'=>"Satanoscillatemymetallicsonatas",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>31,3=>31,4=>1));

        $test3 = array('str'=>"AmanaplanacanalPanama",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>21,3=>21,4=>1));

        $test4 = array('str'=>"AblewasIereIsawElba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>19,3=>19,4=>1));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"Thequickbrownfox",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:((.)(?1)\\2|)|((.)(?3)\\4|.))\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_823() {
        $test1 = array('str'=>"12",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"(((2+2)*-3)-7)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>11),
                       'length'=>array(0=>14,1=>14,2=>1));

        $test3 = array('str'=>"-12",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"((2+2)*-3)-7)",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(\\d+|\\((?1)([+*-])(?1)\\)|-(?1))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_824() {
        $test1 = array('str'=>"xyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>3,1=>3,2=>1));

        $test2 = array('str'=>"xxyzxyzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>8,1=>8,2=>6));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"xxyzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"xxyzxyzxyzz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(x(y|(?1){2})z)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_825() {
        $test1 = array('str'=>"<>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        $test2 = array('str'=>"<abcd>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>6,1=>6,2=>6));

        $test3 = array('str'=>"<abc <123> hij>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>15,1=>15,2=>15));

        $test4 = array('str'=>"<abc <def> hij>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5,1=>5,2=>5),
                       'length'=>array(0=>5,1=>5,2=>5));

        $test5 = array('str'=>"<abc<>def>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>10,1=>10,2=>10));

        $test6 = array('str'=>"<abc<>",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4,1=>4,2=>4),
                       'length'=>array(0=>2,1=>2,2=>2));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"<abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((< (?: (?(R) \\d++  | [^<>]*+) | (?2)) * >))",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_826() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^a+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_827() {
        $test1 = array('str'=>"aaabccc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a+b?c+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_828() {
        $test1 = array('str'=>"aaabccc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a+b?(*PRUNE)c+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_829() {
        $test1 = array('str'=>"aaabccc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a+b?(*COMMIT)c+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_830() {
        $test1 = array('str'=>"aaabcccaaabccc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a+b?(*SKIP)c+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_831() {
        $test1 = array('str'=>"aaaxxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>9));

        $test2 = array('str'=>"aaa++++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"bbbxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test4 = array('str'=>"bbb+++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test5 = array('str'=>"cccxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test6 = array('str'=>"ccc++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test7 = array('str'=>"dddddddd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^(?:aaa(*THEN)\\w{6}|bbb(*THEN)\\w{5}|ccc(*THEN)\\w{4}|\\w{3})",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_832() {
        $test1 = array('str'=>"aaaxxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>9,1=>9));

        $test2 = array('str'=>"aaa++++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test3 = array('str'=>"bbbxxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>8,1=>8));

        $test4 = array('str'=>"bbb+++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test5 = array('str'=>"cccxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>7));

        $test6 = array('str'=>"ccc++++",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        $test7 = array('str'=>"dddddddd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"^(aaa(*THEN)\\w{6}|bbb(*THEN)\\w{5}|ccc(*THEN)\\w{4}|\\w{3})",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_833() {
        $test1 = array('str'=>"aaabccc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a+b?(*THEN)c+(*FAIL)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_834() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        $test2 = array('str'=>"ABX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        $test3 = array('str'=>"AADE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                       'length'=>array(0=>4,1=>3,2=>1,3=>1));

        $test4 = array('str'=>"ACDE",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                       'length'=>array(0=>4,1=>3,2=>1,3=>1));

        $test5 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"AD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (A|B(*ACCEPT)|C) D)(E)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_835() {
        $test1 = array('str'=>"1221",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>4,1=>4,2=>1));

        $test2 = array('str'=>"Satan, oscillate my metallic sonatas!",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>37,3=>36,4=>1));

        $test3 = array('str'=>"A man, a plan, a canal: Panama!",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>31,3=>30,4=>1));

        $test4 = array('str'=>"Able was I ere I saw Elba.",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>0),
                       'length'=>array(0=>26,3=>25,4=>1));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"The quick brown fox",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\W*+(?:((.)\\W*+(?1)\\W*+\\2|)|((.)\\W*+(?3)\\W*+\\4|\\W*+.\\W*+))\\W*+\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_836() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>3,1=>3,2=>1));

        $test3 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>5,1=>5,2=>1));

        $test4 = array('str'=>"abcdcba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>7,1=>7,2=>1));

        $test5 = array('str'=>"pqaabaaqp",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>9,1=>9,2=>1));

        $test6 = array('str'=>"ablewasiereisawelba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>19,1=>19,2=>1));

        $test7 = array('str'=>"rhubarb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"the quick brown fox",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^((.)(?1)\\2|.)\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
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

    function data_for_test_840() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"defdef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abcdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"defabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?|(abc)|(def))\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_841() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"defabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"defdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"abcdef",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?|(abc)|(def))(?1)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_842() {
        $test1 = array('str'=>"a\"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,3=>1),
                       'length'=>array(0=>7,1=>1,3=>1));

        $test2 = array('str'=>"b\"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,4=>1,6=>1),
                       'length'=>array(0=>7,4=>1,6=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"b\"11111",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:a(?<quote> (?<apostrophe>')|(?<realquote>\")) |b(?<quote> (?<apostrophe>')|(?<realquote>\")) ) (?('quote')[a-z]+|[0-9]+)",
                     'modifiers'=>"xJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_843() {
        $test1 = array('str'=>"ABCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>2),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"CCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"CAD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?1)|B)(A(*F)|C)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_844() {
        $test1 = array('str'=>"CCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"BCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ABCD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"CAD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"BAD",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:(?1)|B)(A(*F)|C)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_845() {
        $test1 = array('str'=>"AAD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"ACD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test3 = array('str'=>"BAD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"BCD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>3,1=>1));

        $test5 = array('str'=>"BAX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test6 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"ACX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test8 = array('str'=>"ABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?1)|B)(A(*ACCEPT)XX|C)D",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_846() {
        $test1 = array('str'=>"BAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?(DEFINE)(A))B(?1)C",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_847() {
        $test1 = array('str'=>"BAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"(?(DEFINE)((A)\\2))B(?1)C",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_848() {
        $test1 = array('str'=>"(ab(cd)ef)",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>7),
                       'length'=>array(0=>10,1=>10,2=>2));

        return array('regex'=>"(?<pn> \\( ( [^()]++ | (?&pn) )* \\) )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_849() {
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
    }

    function data_for_test_850() {
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
    }

    function data_for_test_852() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?>a\\Kb)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_853() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
                       'length'=>array(0=>1,1=>2));

        return array('regex'=>"((?>a\\Kb))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_854() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
                       'length'=>array(0=>1,1=>2));

        return array('regex'=>"(a\\Kb)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_855() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^a\\Kcz|ac",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_856() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?>a\\Kbz|ab)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_857() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"^(?&t)(?(DEFINE)(?<t>a\\Kb))\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_858() {
        $test1 = array('str'=>"a(b)c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        $test2 = array('str'=>"a(b(c)d)e",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"^([^()]|\\((?1)*\\))*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_859() {
        $test1 = array('str'=>"0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"00",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>1));

        $test3 = array('str'=>"0000",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>4,1=>4,2=>1));

        return array('regex'=>"(?P<L1>(?P<L2>0)(?P>L1)|(?P>L2))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_860() {
        $test1 = array('str'=>"0",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>1,2=>1));

        $test2 = array('str'=>"00",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>1,2=>1));

        $test3 = array('str'=>"0000",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>1,2=>1));

        return array('regex'=>"(?P<L1>(?P<L2>0)|(?P>L2)(?P>L1))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_861() {
        $test1 = array('str'=>"ACABX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*COMMIT)(B|D)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_862() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"AC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(A(*PRUNE:A)B|C(*PRUNE:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_863() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*SKIP:B)(C|X)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_864() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(A(*THEN:A)B|C(*THEN:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_865() {
        $test1 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:A(*THEN:A)B|C(*THEN:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_866() {
        $test1 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?>A(*THEN:A)B|C(*THEN:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_867() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*MARK:A)A+(*SKIP:A)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_868() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*MARK:A)A+(*MARK:B)(*SKIP:A)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_869() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*:A)A+(*SKIP:A)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_870() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*:A)A+(*SKIP:A)(B|Z)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_871() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*MARK:A)A+(*SKIP:B)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_872() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*MARK:A)A+(*SKIP:B)(B|Z) | AC(*:B)",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_873() {
        $test1 = array('str'=>"ABCDEFG",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"DEFGABC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*COMMIT)(A|P)(B|P)(C|P)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_874() {
        $test1 = array('str'=>"abbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(\\w+)(?>b(*COMMIT))\\w{2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_875() {
        $test1 = array('str'=>"abbb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(\\w+)b(*COMMIT)\\w{2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_876() {
        $test1 = array('str'=>"bac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?&t)(?#()(?(DEFINE)(?<t>a))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_877() {
        $test1 = array('str'=>"yes",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>(*COMMIT)(?>yes|no)(*THEN)(*F))?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_878() {
        $test1 = array('str'=>"yes",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>(*COMMIT)(yes|no)(*THEN)(*F))?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_879() {
        $test1 = array('str'=>"bc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"b?(*SKIP)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_880() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*SKIP)bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_881() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*SKIP)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_882() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(?P<abn>(?P=abn)xxx|)+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_883() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"aA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test4 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"Ba",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"ba",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?i:([^b]))(?1)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_884() {
        $test1 = array('str'=>"aaaaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaaaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?&t)*+(?(DEFINE)(?<t>a))\\w\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_885() {
        $test1 = array('str'=>"aaaaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        $test2 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>6));

        return array('regex'=>"^(?&t)*(?(DEFINE)(?<t>a))\\w\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_886() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

        $test2 = array('str'=>"YZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>1,2=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a)*+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_887() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        $test2 = array('str'=>"YZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"aaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:a)*+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_888() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"YZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a)++(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_889() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aaaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"YZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:a)++(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_890() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        $test2 = array('str'=>"YZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>1,2=>1));

        return array('regex'=>"^(a)?+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_891() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"YZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"^(?:a)?+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_892() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>4),
                       'length'=>array(0=>5,1=>1,2=>1));

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

        $test4 = array('str'=>"YZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a){2,}+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_893() {
        $test1 = array('str'=>"aaaaX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>5,1=>1));

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

        $test4 = array('str'=>"YZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(?:a){2,}+(\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_894() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>0));

        $test2 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        $test3 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"(a|)*(?1)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_895() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a)++(?1)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_896() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"ab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a)*+(?1)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_897() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?1)(?:(b)){0}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_898() {
        $test1 = array('str'=>"foo(bar(baz)+baz(bop))",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3,3=>4),
                       'length'=>array(0=>22,1=>22,2=>19,3=>17));

        return array('regex'=>"(foo ( \\( ((?:(?> [^()]+ )|(?2))*) \\) ) )",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_899() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        return array('regex'=>"(A (A|B(*ACCEPT)|C) D)(E)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_900() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"\\A.*?(a|bc)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_901() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\A.*?(?:a|bc)++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_902() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"\\A.*?(a|bc)++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_903() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\A.*?(?:a|bc|d)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_904() {
        $test1 = array('str'=>"beetle",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?:(b))++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_905() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?(?=(a(*ACCEPT)z))a)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_906() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>1));

        return array('regex'=>"^(a)(?1)+ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_907() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(a)(?1)++ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_908() {
        $test1 = array('str'=>"aZbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?=a(*:M))aZ",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_909() {
        $test1 = array('str'=>"aZbc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?!(*:M)b)aZ",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_910() {
        $test1 = array('str'=>"backgammon",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(DEFINE)(a))?b(?1)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_911() {
        $test1 = array('str'=>"abc\ndef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^\\N+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_912() {
        $test1 = array('str'=>"abc\ndef",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^\\N{1,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_913() {
        $test1 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>"(?(R)a+|(?R)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_914() {
        $test1 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>4));

        return array('regex'=>"(?(R)a+|((?R))b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_915() {
        $test1 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        return array('regex'=>"((?(R)a+|(?1)b))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_916() {
        $test1 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        return array('regex'=>"((?(R1)a+|(?1)b))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_917() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"((?(R)a|(?1)))*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_918() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"((?(R)a|(?1)))+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_919() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a(*:any \nname)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_920() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"bba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?>(?&t)c|(?&t))(?(DEFINE)(?<t>a|b(*PRUNE)c))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_921() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (a(*THEN)b) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_922() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"^.*? (a(*THEN)b|(*F)) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_923() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"^.*? ( (a(*THEN)b) | (*F) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_924() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? ( (a(*THEN)b) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_925() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?:a(*THEN)b) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_926() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?:a(*THEN)b|(*F)) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_927() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?: (?:a(*THEN)b) | (*F) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_928() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?: (?:a(*THEN)b) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_929() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?>a(*THEN)b) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_930() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?>a(*THEN)b|(*F)) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_931() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?> (?>a(*THEN)b) | (*F) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_932() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?> (?>a(*THEN)b) ) c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_933() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (a(*THEN)b)++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_934() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"^.*? (a(*THEN)b|(*F))++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_935() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"^.*? ( (a(*THEN)b)++ | (*F) )++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_936() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? ( (a(*THEN)b)++ )++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_937() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?:a(*THEN)b)++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_938() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?:a(*THEN)b|(*F))++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_939() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^.*? (?: (?:a(*THEN)b)++ | (*F) )++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_940() {
        $test1 = array('str'=>"aabc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^.*? (?: (?:a(*THEN)b)++ )++ c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_941() {
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
    }

    function data_for_test_946() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(?>a(*:m))",
                     'modifiers'=>"imsxSK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_947() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(?>(a)(*:m))",
                     'modifiers'=>"imsxSK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_948() {
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
    }

    function data_for_test_951() {
        $test1 = array('str'=>"xcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"acd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<!a(*FAIL)b)c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_952() {
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
    }

    function data_for_test_956() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>4,1=>1,2=>1));

        return array('regex'=>"(a)(?2){2}(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_957() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*PRUNE:B)(C|X)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_958() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*PRUNE:B)(C|X)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_959() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*THEN:B)(C|X)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_960() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*THEN:B)(C|X)",
                     'modifiers'=>"KSY",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_961() {
        $test1 = array('str'=>"C",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"D",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(*MARK:A)(*THEN:B)(C|X)",
                     'modifiers'=>"KS",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_962() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*MARK:A)A+(*SKIP)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_963() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*MARK:A)A+(*MARK:B)(*SKIP:B)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_964() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*:A)A+(*SKIP)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_965() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*MARK:A)A+(*SKIP:)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_966() {
        $test1 = array('str'=>"AABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"XXYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"XAQQ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"XAQQXZZ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"AXQQQ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"AXXQQQ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*:A)B|XX(*:B)Y",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_967() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"AC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(A(*THEN:A)B|C(*THEN:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_968() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"AC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"CB",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^(A(*PRUNE:A)B|C(*PRUNE:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_969() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        $test2 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"^(A(*PRUNE:)B|C(*PRUNE:B)D)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_970() {
        $test1 = array('str'=>"ACAB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*PRUNE:A)B",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_971() {
        $test1 = array('str'=>"AABC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"XXYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*:A)B|X(*:A)Y",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_972() {
        $test1 = array('str'=>"aw",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"b(*:m)f|a(*:n)w",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_973() {
        $test1 = array('str'=>"abaw",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"abax",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"b(*:m)f|aw",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_974() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>3));

        return array('regex'=>"A(*MARK:A)A+(*SKIP:B)(B|Z) | AAC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_975() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"axy",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(*PRUNE:X)bc|qq",
                     'modifiers'=>"KY",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_976() {
        $test1 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"axy",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(*THEN:X)bc|qq",
                     'modifiers'=>"KY",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

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
    }

    function data_for_test_983() {
        $test1 = array('str'=>"hello world test",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>12,2=>12),
                       'length'=>array(0=>4,2=>0));

        return array('regex'=>"(another)?(\\1?)test",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_984() {
        $test1 = array('str'=>"hello world test",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(another)?(\\1+)test",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_985() {
        $test1 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(a(*COMMIT)b){0}a(?1)|aac",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_986() {
        $test1 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"((?:a?)*)*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_987() {
        $test1 = array('str'=>"aac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"((?>a?)*)*c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_988() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(?>.*?a)(?<=ba)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

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

    function data_for_test_990() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>".*?a(*PRUNE)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_991() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>".*?a(*PRUNE)b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_992() {
        $test1 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^a(*PRUNE)b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_993() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>".*?a(*SKIP)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_994() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(?>.*?a)b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_995() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"(?>.*?a)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_996() {
        $test1 = array('str'=>"aab",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>^a)b",
                     'modifiers'=>"s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_997() {
        $test1 = array('str'=>"alphabetabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>12,1=>8),
                       'length'=>array(0=>0,1=>4));

        $test2 = array('str'=>"endingwxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>10,2=>6),
                       'length'=>array(0=>0,2=>4));

        return array('regex'=>"(?>.*?)(?<=(abcd)|(wxyz))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_998() {
        $test1 = array('str'=>"alphabetabcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>12,1=>4));

        $test2 = array('str'=>"endingwxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>6),
                       'length'=>array(0=>10,2=>4));

        return array('regex'=>"(?>.*)(?<=(abcd)|(wxyz))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_999() {
        $test1 = array('str'=>"abcdfooxyz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>.*)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1000() {
        $test1 = array('str'=>"abcdfooxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"(?>.*?)foo",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1001() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(a(*PRUNE)b)){0}(?:(?1)|ac)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1002() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?:(a(*SKIP)b)){0}(?:(?1)|ac)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1003() {
        $test1 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<=(*SKIP)ac)a",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1004() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*MARK:A)A+(*SKIP:B)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1005() {
        $test1 = array('str'=>"acacd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        return array('regex'=>"a(*SKIP:m)x|ac(*:n)(*SKIP:n)d|ac",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1006() {
        $test1 = array('str'=>"AB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"A(*SKIP:m)x|A(*SKIP:n)x|AB",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1007() {
        $test1 = array('str'=>"acacd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"((*SKIP:r)d){0}a(*SKIP:m)x|ac(*:n)|ac",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1008() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        return array('regex'=>"aaaaa(*PRUNE)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1009() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        return array('regex'=>"aaaaa(*SKIP)(*PRUNE)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1010() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        return array('regex'=>"aaaaa(*SKIP:N)(*PRUNE)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1011() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        return array('regex'=>"aaaa(*:N)a(*SKIP:N)(*PRUNE)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1012() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        return array('regex'=>"aaaaa(*THEN)(*PRUNE)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1013() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"aaaaa(*SKIP)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1014() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"aaaaa(*PRUNE)(*SKIP)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1015() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"aaaaa(*THEN)(*SKIP)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1016() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"aaaaa(*COMMIT)(*SKIP)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1017() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"aaaaa(*COMMIT)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1018() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"aaaaa(*THEN)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1019() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"aaaaa(*SKIP)(*THEN)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1020() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"aaaaa(*PRUNE)(*THEN)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1021() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"aaaaa(*COMMIT)(*THEN)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1022() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        return array('regex'=>"aaaaa(*:m)(*PRUNE:m)(*SKIP:m)m|a+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1023() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        return array('regex'=>"aaaaa(*:m)(*MARK:m)(*PRUNE)(*SKIP:m)m|a+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1024() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>4));

        return array('regex'=>"aaaaa(*:n)(*PRUNE:m)(*SKIP:m)m|a+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1025() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>1));

        return array('regex'=>"aaaaa(*:n)(*MARK:m)(*PRUNE)(*SKIP:m)m|a+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1026() {
        $test1 = array('str'=>"aaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        return array('regex'=>"a(*MARK:A)aa(*PRUNE:A)a(*SKIP:A)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1027() {
        $test1 = array('str'=>"aaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"a(*MARK:A)aa(*MARK:A)a(*SKIP:A)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1028() {
        $test1 = array('str'=>"aaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        return array('regex'=>"aaa(*PRUNE:A)a(*SKIP:A)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1029() {
        $test1 = array('str'=>"aaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"aaa(*MARK:A)a(*SKIP:A)b|a+c",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1030() {
        $test1 = array('str'=>"aaaaaac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>5),
                       'length'=>array(0=>2));

        return array('regex'=>"a(*:m)a(*COMMIT)(*SKIP:m)b|a+c",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1031() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>".?(a|b(*THEN)c)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1032() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        $test2 = array('str'=>"abd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a(*COMMIT)b)c|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1033() {
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
    }

    function data_for_test_1034() {
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

        return array('regex'=>"(?>a(*COMMIT)b)c|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1035() {
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
    }

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

    function data_for_test_1037() {
        $test1 = array('str'=>"abceabd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(?>b(*COMMIT)c)d|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1038() {
        $test1 = array('str'=>"abceabd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"a(?>bc)d|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1039() {
        $test1 = array('str'=>"abd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?>a(*COMMIT)b)c|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1040() {
        $test1 = array('str'=>"abd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?>a(*COMMIT)c)d|abd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1041() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>1),
                       'length'=>array(0=>2,2=>1));

        return array('regex'=>"((?=a(*COMMIT)b)ab|ac){0}(?:(?1)|a(c))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1042() {
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

        return array('regex'=>"^(a)?(?(1)a|b)+\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1043() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(?=a\\Kb)ab",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1044() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!a\\Kb)ac",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1045() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>"^abc(?<=b\\Kc)d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1046() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^abc(?<!b\\Kq)d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1047() {
        $test1 = array('str'=>"AAAC",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"A(*PRUNE:A)A+(*SKIP:A)(B|Z) | AC",
                     'modifiers'=>"xK",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1048() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcxy",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^((abc|abcx)(*THEN)y|abcd)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_1049() {
        $test1 = array('str'=>"yes",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^((yes|no)(*THEN)(*F))?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1050() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C? (*THEN)  | A D) (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1051() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C? (*THEN)  | A D) z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1052() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C? (*THEN)  | A D) \\s* (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1053() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C? (*THEN)  | A D) \\s* z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1054() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (?:C|) (*THEN)  | A D) (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1055() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (?:C|) (*THEN)  | A D) z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1056() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C{0,6} (*THEN)  | A D) (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1057() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   C{0,6} (*THEN)  | A D) z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1058() {
        $test1 = array('str'=>"AbcdCEBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (CE){0,6} (*THEN)  | A D) (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1059() {
        $test1 = array('str'=>"AbcdCEBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (CE){0,6} (*THEN)  | A D) z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1060() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (CE*){0,6} (*THEN)  | A D) (*FAIL)",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1061() {
        $test1 = array('str'=>"AbcdCBefgBhiBqz",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(A (.*)   (CE*){0,6} (*THEN)  | A D) z",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1062() {
        $test1 = array('str'=>"ac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*COMMIT)b|ac)ac|ac",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1063() {
        $test1 = array('str'=>"ac",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=a(*COMMIT)b|(ac)) ac | (a)c",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1064() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(?!b(*THEN)a)bn|bnn)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1065() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!b(*SKIP)a)bn|bnn",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1066() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(?!b(*SKIP)a)bn|bnn)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1067() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!b(*PRUNE)a)bn|bnn",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1068() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(?!b(*PRUNE)a)bn|bnn)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1069() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!b(*COMMIT)a)bn|bnn",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1070() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?(?!b(*COMMIT)a)bn|bnn)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1071() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?=b(*SKIP)a)bn|bnn",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1072() {
        $test1 = array('str'=>"bnn",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"(?=b(*THEN)a)bn|bnn",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1073() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^(?!a(*SKIP)b)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1074() {
        $test1 = array('str'=>"acd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?!a(*SKIP)b)..",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1075() {
        $test1 = array('str'=>"acd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!a(*SKIP)b)..",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1076() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"^(?(?!a(*SKIP)b))",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1077() {
        $test1 = array('str'=>"acd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^(?!a(*PRUNE)b)..",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1078() {
        $test1 = array('str'=>"acd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!a(*PRUNE)b)..",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1079() {
        $test1 = array('str'=>"ac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"(?!a(*COMMIT)b)ac|cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1080() {
        $test1 = array('str'=>"ba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\A.*?(?:a|bc)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1081() {
        $test1 = array('str'=>"CD",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"^(A(*THEN)B|C(*THEN)D)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1082() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(*:m(m)(?&y)(?(DEFINE)(?<y>b))",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1083() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(*PRUNE:m(m)(?&y)(?(DEFINE)(?<y>b))",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1084() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(*SKIP:m(m)(?&y)(?(DEFINE)(?<y>b))",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1085() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"(*THEN:m(m)(?&y)(?(DEFINE)(?<y>b))",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1086() {
        $test1 = array('str'=>"1234",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"123",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\d*\\w{4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1087() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^b]*\\w{4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1088() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^b]*\\w{4}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1089() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^a*\\w{4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1090() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"aaa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^a*\\w{4}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1091() {
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
    }

    function data_for_test_1092() {
        $test1 = array('str'=>"foofoo",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>3));

        $test2 = array('str'=>"barbar",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>6,2=>3));

        return array('regex'=>"(?:(?<n>foo)|(?<n>bar))\\k<n>",
                     'modifiers'=>"J",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1093() {
        $test1 = array('str'=>"AfooA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>5,1=>1,2=>3));

        $test2 = array('str'=>"AbarA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>5,1=>1,3=>3));

        $test3 = array('str'=>"** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"Afoofoo",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"Abarbar",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?<n>A)(?:(?<n>foo)|(?<n>bar))\\k<n>",
                     'modifiers'=>"J",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_1094() {
        $test1 = array('str'=>"1 IN SOA non-sp1 non-sp2(",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>9,3=>17),
                       'length'=>array(0=>25,1=>1,2=>7,3=>7));

        return array('regex'=>"^(\\d+)\\s+IN\\s+SOA\\s+(\\S+)\\s+(\\S+)\\s*\\(\\s*\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1095() {
        $test1 = array('str'=>"Ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        $test2 = array('str'=>"BAxy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0,3=>1),
                       'length'=>array(0=>4,2=>1,3=>1));

        return array('regex'=>"^ (?:(?<A>A)|(?'B'B)(?<A>A)) (?('A')x) (?(<B>)y)\$",
                     'modifiers'=>"xJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_1096() {
        $test1 = array('str'=>"A Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^A\\xZ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1097() {
        $test1 = array('str'=>"ASB",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^A\\o{123}B",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1098() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>" ^ a + + b \$ ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1099() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>" ^ a + #comment\n  + b \$ ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1100() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>" ^ a + #comment\n  #comment\n  + b \$ ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1101() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>5));

        return array('regex'=>" ^ (?> a + ) b \$ ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1102() {
        $test1 = array('str'=>"aaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>4));

        return array('regex'=>" ^ ( a + ) + + \\w \$ ",
                     'modifiers'=>"x",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1103() {
        $test1 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?:a\\Kb)*+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1104() {
        $test1 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?>a\\Kb)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1105() {
        $test1 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>1));

        return array('regex'=>"(?:a\\Kb)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1106() {
        $test1 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>2),
                       'length'=>array(0=>1,1=>2));

        return array('regex'=>"(a\\Kb)*+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1107() {
        $test1 = array('str'=>"ababc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>2),
                       'length'=>array(0=>1,1=>2));

        return array('regex'=>"(a\\Kb)*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1108() {
        $test1 = array('str'=>"acb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:x|(?:(xx|yy)+|x|x|x|x|x)|a|a|a)bc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1109() {
        $test1 = array('str'=>"NON QUOTED \"QUOT\"\"ED\" AFTER \"NOT MATCHED",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>28));

        return array('regex'=>"\\A(?:[^\\\"]++|\\\"(?:[^\\\"]*+|\\\"\\\")*+\\\")++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1110() {
        $test1 = array('str'=>"NON QUOTED \"QUOT\"\"ED\" AFTER \"NOT MATCHED",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>28));

        return array('regex'=>"\\A(?:[^\\\"]++|\\\"(?:[^\\\"]++|\\\"\\\")*+\\\")++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1111() {
        $test1 = array('str'=>"NON QUOTED \"QUOT\"\"ED\" AFTER \"NOT MATCHED",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>28));

        return array('regex'=>"\\A(?:[^\\\"]++|\\\"(?:[^\\\"]++|\\\"\\\")++\\\")++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1112() {
        $test1 = array('str'=>"NON QUOTED \"QUOT\"\"ED\" AFTER \"NOT MATCHED",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>21,2=>40),
                       'length'=>array(0=>28,1=>7,2=>0));

        return array('regex'=>"\\A([^\\\"1]++|[\\\"2]([^\\\"3]*+|[\\\"4][\\\"5])*+[\\\"6])++",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1113() {
        $test1 = array('str'=>"test test",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^\\w+(?>\\s*)(?<=\\w)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1114() {
        $test1 = array('str'=>"abbaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(?P<same>a)(?P<same>b)",
                     'modifiers'=>"gJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1115() {
        $test1 = array('str'=>"abbaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3,1=>3,2=>4),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"(?P<same>a)(?P<same>b)(?P=same)",
                     'modifiers'=>"gJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1116() {
        $test1 = array('str'=>"abbaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(?P=same)?(?P<same>a)(?P<same>b)",
                     'modifiers'=>"gJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1117() {
        $test1 = array('str'=>"bbbaaabaabb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>8,1=>1,2=>1));

        return array('regex'=>"(?:(?P=same)?(?:(?P<same>a)|(?P<same>b))(?P=same))+",
                     'modifiers'=>"gJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1118() {
        $test1 = array('str'=>"bbbaaaccccaaabbbcc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:(?P=same)?(?:(?P=same)(?P<same>a)(?P=same)|(?P=same)?(?P<same>b)(?P=same)){2}(?P=same)(?P<same>c)(?P=same)){2}(?P<same>z)?",
                     'modifiers'=>"gJ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1119() {
        $test1 = array('str'=>"acl",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        $test2 = array('str'=>"bdl",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>3,2=>1));

        $test3 = array('str'=>"adl",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"bcl",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        return array('regex'=>"(?P<Name>a)?(?P<Name2>b)?(?(<Name>)c|d)*l",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_1120() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"\\sabc",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1121() {
        $test1 = array('str'=>"aa]]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"[\\Qa]\\E]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_1122() {
        $test1 = array('str'=>"aa]]",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"[\\Q]a\\E]+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

}
