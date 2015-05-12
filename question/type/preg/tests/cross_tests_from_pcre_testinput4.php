<?php

// this file initially was generated automatically using testinput4
// partial match data could be added manually
// note: this file should be encoded in UTF-8

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_pcre_testinput4 {

    function data_for_test_1() {
        $test1 = array('str'=>"acb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"aĀb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"a\nb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a.b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_2() {
        $test1 = array('str'=>"a䀀xyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"a䀀yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"a䀀Āyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"a䀀b",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"ac\ncb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3})b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    /*function data_for_test_3() {
Error -10 (bad UTF-8 string) offset=1 reason=15
str: a��b
        return array('regex'=>"a(.*?)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_4() {
        $test1 = array('str'=>"aĀb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>1));

        return array('regex'=>"a(.*?)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_5() {
Error -10 (bad UTF-8 string) offset=1 reason=15
str: a��b
        return array('regex'=>"a(.*)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_6() {
        $test1 = array('str'=>"aĀb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"a(.*)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_7() {
Error -10 (bad UTF-8 string) offset=1 reason=15
str: a��bcd
        return array('regex'=>"a(.)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_8() {
        $test1 = array('str'=>"aɀbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"a(.)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_9() {
Error -10 (bad UTF-8 string) offset=1 reason=15
str: a��bcd
        return array('regex'=>"a(.?)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_10() {
        $test1 = array('str'=>"aɀbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"a(.?)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    /*function data_for_test_11() {
Error -10 (bad UTF-8 string) offset=1 reason=15
str: a��bcd
        return array('regex'=>"a(.??)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_12() {
        $test1 = array('str'=>"aɀbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>1));

        return array('regex'=>"a(.??)(.)",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_13() {
        $test1 = array('str'=>"aሴxyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"aሴ䌡yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"aሴ䌡㐒b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"aሴb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"ac\ncb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3})b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_14() {
        $test1 = array('str'=>"aሴxyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"aሴ䌡yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"aሴ䌡㐒b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"axxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>15,1=>13));

        $test5 = array('str'=>"aሴ䌡㐒㐡b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test6 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"aሴb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3,})b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_15() {
        $test1 = array('str'=>"aሴxyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"aሴ䌡yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"aሴ䌡㐒b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"axxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test5 = array('str'=>"aሴ䌡㐒㐡b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test6 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"aሴb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3,}?)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_16() {
        $test1 = array('str'=>"aሴxyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"aሴ䌡yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"aሴ䌡㐒b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"axxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test5 = array('str'=>"aሴ䌡㐒㐡b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test6 = array('str'=>"axbxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test7 = array('str'=>"axxxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>5));

        $test8 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"aሴb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"axxxxxxbcdefghijb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3,5})b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_17() {
        $test1 = array('str'=>"aሴxyb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test2 = array('str'=>"aሴ䌡yb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test3 = array('str'=>"aሴ䌡㐒b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>5,1=>3));

        $test4 = array('str'=>"axxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test5 = array('str'=>"aሴ䌡㐒㐡b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test6 = array('str'=>"axbxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>6,1=>4));

        $test7 = array('str'=>"axxxxxbcdefghijb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>7,1=>5));

        $test8 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test9 = array('str'=>"aሴb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test10 = array('str'=>"axxxxxxbcdefghijb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"a(.{3,5}?)b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_18() {
        $test1 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test2 = array('str'=>"Ā",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[a\\x{c0}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
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
        $test1 = array('str'=>"aĀbcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"(?<=a\\x{100}b)cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_21() {
        $test1 = array('str'=>"a􀀀bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>3),
                       'length'=>array(0=>2));

        return array('regex'=>"(?<=a\\x{100000}b)cd",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_22() {
        $test1 = array('str'=>"ĀĀĀb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"ĀĀb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(?:\\x{100}){3}b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_23() {
        $test1 = array('str'=>"«",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"«",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>" {ab}",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\x{ab}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_24() {
        $test1 = array('str'=>"WXYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
                       'length'=>array(0=>1,1=>1));

        $test2 = array('str'=>"ɖXYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>0),
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

    function data_for_test_25() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ĀaYɖZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]+",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_26() {
        $test1 = array('str'=>"Ābc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[^a]{2}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_27() {
        $test1 = array('str'=>"ĀbcAa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"^[^a]{2,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_28() {
        $test1 = array('str'=>"Ābca",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[^a]{2,}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_29() {
        $test1 = array('str'=>"bcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ĀaYɖZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[^a]+",
                     'modifiers'=>"ig",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_30() {
        $test1 = array('str'=>"Ābc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[^a]{2}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_31() {
        $test1 = array('str'=>"ĀbcAa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^[^a]{2,}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_32() {
        $test1 = array('str'=>"Ābca",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[^a]{2,}?",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_33() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"\\x{100}{0,0}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_34() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"ĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\x{100}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_35() {
        $test1 = array('str'=>"ĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"\\x{100}{0,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_36() {
        $test1 = array('str'=>"abce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        $test2 = array('str'=>"ĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        return array('regex'=>"\\x{100}*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_37() {
        $test1 = array('str'=>"abcdĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        return array('regex'=>"\\x{100}{1,1}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_38() {
        $test1 = array('str'=>"abcdĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"\\x{100}{1,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_39() {
        $test1 = array('str'=>"abcdĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>4));

        return array('regex'=>"\\x{100}+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_40() {
        $test1 = array('str'=>"abcdĀĀĀXX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        return array('regex'=>"\\x{100}{3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_41() {
        $test1 = array('str'=>"abcdĀĀĀĀĀĀĀXX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>5));

        return array('regex'=>"\\x{100}{3,5}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_42() {
        $test1 = array('str'=>"abcdĀĀĀĀĀĀĀXX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>7));

        return array('regex'=>"\\x{100}{3,}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_43() {
        $test1 = array('str'=>"XyyyaĀĀbXzzz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>8),
                       'length'=>array(0=>1));

        return array('regex'=>"(?<=a\\x{100}{2}b)X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_44() {
        $test1 = array('str'=>"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>222));

        return array('regex'=>"\\D*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_45() {
        $test1 = array('str'=>"ĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>222));

        return array('regex'=>"\\D*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_46() {
        $test1 = array('str'=>"1X2",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"1Ā2",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\D",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_47() {
        $test1 = array('str'=>"> >X Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"> >Ā Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        return array('regex'=>">\\S",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_48() {
        $test1 = array('str'=>"Ā3",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\d",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_49() {
        $test1 = array('str'=>"Ā X",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"\\s",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_50() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>11));

        $test3 = array('str'=>"1234",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\D+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_51() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"12ab34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"1234",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"12a34",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\D{2,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_52() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"12ab34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"1234",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"12a34",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\D{2,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_53() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\d+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_54() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"1234abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"1.4",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\d{2,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_55() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"1234abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"1.4",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\d{2,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_56() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"    ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\S+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_57() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"1234abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"     ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\S{2,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_58() {
        $test1 = array('str'=>"12abcd34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"1234abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"     ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\S{2,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_59() {
        $test1 = array('str'=>"12>      <34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>8));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>">\\s+<",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_60() {
        $test1 = array('str'=>"ab>  <cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"ab>   <ce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ab>    <cd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>">\\s{2,3}<",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_61() {
        $test1 = array('str'=>"ab>  <cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"ab>   <ce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>5));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ab>    <cd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>">\\s{2,3}?<",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_62() {
        $test1 = array('str'=>"12      34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>7));

        $test3 = array('str'=>"+++=*!",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\w+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_63() {
        $test1 = array('str'=>"ab  cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"abcd ce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"a.b.c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\w{2,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_64() {
        $test1 = array('str'=>"ab  cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"abcd ce",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"a.b.c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\w{2,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_65() {
        $test1 = array('str'=>"12====34",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test3 = array('str'=>"abcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\W+",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_66() {
        $test1 = array('str'=>"ab====cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test2 = array('str'=>"ab==cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"a.b.c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\W{2,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_67() {
        $test1 = array('str'=>"ab====cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ab==cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"a.b.c",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"\\W{2,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_68() {
        $test1 = array('str'=>"Ā",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"ZĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"ĀZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\x{100}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_69() {
        $test1 = array('str'=>"ZĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"Ā",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"ĀZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Z\\x{100}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_70() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\x{100}\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_71() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abđcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\x{100}-\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_72() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abđcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"abzcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"ab|cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[z-\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_73() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"Q?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Q\\x{100}\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_74() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abđcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"Q?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Q\\x{100}-\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_75() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abđcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"abzcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"ab|cd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"Q?",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Qz-\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_76() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abȀĀȀĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\x{100}\\x{200}]{1,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_77() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abȀĀȀĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[\\x{100}\\x{200}]{1,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_78() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abȀĀȀĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Q\\x{100}\\x{200}]{1,3}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_79() {
        $test1 = array('str'=>"abĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abȀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"abȀĀȀĀcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[Q\\x{100}\\x{200}]{1,3}?",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_80() {
        $test1 = array('str'=>"abcȀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abcĀX",
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
        $test1 = array('str'=>"abcȀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>4),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"abcĀX",
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
        $test1 = array('str'=>"abcĀȀĀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>6),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test3 = array('str'=>"abcȀX",
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

    function data_for_test_83() {
        $test1 = array('str'=>"AX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ŐX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"ԀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ĀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"ȀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^\\x{100}\\x{200}]X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_84() {
        $test1 = array('str'=>"AX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ŐX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"ԀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ĀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"ȀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test7 = array('str'=>"QX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^Q\\x{100}\\x{200}]X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_85() {
        $test1 = array('str'=>"AX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test2 = array('str'=>"ԀX",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        $test3 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test4 = array('str'=>"ĀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"ŐX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"ȀX",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[^\\x{100}-\\x{200}]X",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_86() {
        $test1 = array('str'=>"z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"Z",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"Ā",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"*** Failers",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test5 = array('str'=>"Ă",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        $test6 = array('str'=>"y",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"[z-\\x{100}]",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    /*function data_for_test_87() {
Error -10 (bad UTF-8 string) offset=1 reason=21
str: >�<
        return array('regex'=>"[\\xFF]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }*/

    function data_for_test_88() {
        $test1 = array('str'=>">ÿ<",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\xff]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_89() {
        $test1 = array('str'=>"XYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[^\\xFF]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_90() {
        $test1 = array('str'=>"XYZ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"ģ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[^\\xff]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_91() {
        $test1 = array('str'=>"xb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[ac]*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_92() {
        $test1 = array('str'=>"xb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[ac\\x{100}]*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_93() {
        $test1 = array('str'=>"xb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^x]*b",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_94() {
        $test1 = array('str'=>"xb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^[^x]*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_95() {
        $test1 = array('str'=>"xb",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\d*b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_96() {
        $test1 = array('str'=>"catac",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        $test2 = array('str'=>"aɖa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(|a)",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_97() {
        $test1 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^\\x{85}\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_98() {
        $test1 = array('str'=>"ሴ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^ሴ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_99() {
        $test1 = array('str'=>"ሴ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"^\\ሴ",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_100() {
        $test1 = array('str'=>"abcdefg",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        $test2 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(?s)(.{1,5})",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_101() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a*\\x{100}*\\w",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_102() {
        $test1 = array('str'=>"A£BC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\S\\S",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_103() {
        $test1 = array('str'=>"A£BC",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\S{2}",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_104() {
        $test1 = array('str'=>"+£==",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\W\\W",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_105() {
        $test1 = array('str'=>"+£==",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"\\W{2}",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_106() {
        $test1 = array('str'=>"тест",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\S",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_107() {
        $test1 = array('str'=>"тест",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\S]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_108() {
        $test1 = array('str'=>"тест",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\D",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_109() {
        $test1 = array('str'=>"тест",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\D]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_110() {
        $test1 = array('str'=>"⑂␵⑁⑂",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"\\W",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_111() {
        $test1 = array('str'=>"⑂␵⑁⑂",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\W]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_112() {
        $test1 = array('str'=>"abc\n\rтестxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>12));

        return array('regex'=>"[\\S\\s]*",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_113() {
        $test1 = array('str'=>"тест",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[\\x{41f}\\S]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_114() {
        $test1 = array('str'=>"abc defтуxyz\npqr",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        return array('regex'=>".[^\\S].",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_115() {
        $test1 = array('str'=>"abc defтуxyz\npqr",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>2),
                       'length'=>array(0=>3));

        return array('regex'=>".[^\\S\\n].",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_116() {
        $test1 = array('str'=>"+⑂",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^alnum:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_117() {
        $test1 = array('str'=>"+⑂",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^alpha:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_118() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^ascii:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_119() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^blank:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_120() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^cntrl:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_121() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^digit:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_122() {
        $test1 = array('str'=>"󠇿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^graph:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_123() {
        $test1 = array('str'=>"AТ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^lower:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_124() {
        $test1 = array('str'=>"󠇿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^print:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_125() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^punct:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_126() {
        $test1 = array('str'=>"Aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^space:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_127() {
        $test1 = array('str'=>"aт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^upper:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_128() {
        $test1 = array('str'=>"+⑂",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^word:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_129() {
        $test1 = array('str'=>"Mт",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"[[:^xdigit:]]",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_130() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^[^d]*?\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_131() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^[^d]*?\$",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_132() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^[^d]*?\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_133() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^[^d]*?\$",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_134() {
        $test1 = array('str'=>"Àb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^[a\\x{c0}]b",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_135() {
        $test1 = array('str'=>"aÀaaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"^([a\\x{c0}]*?)aa",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_136() {
        $test1 = array('str'=>"aÀaaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        $test2 = array('str'=>"aÀaÀaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>4));

        return array('regex'=>"^([a\\x{c0}]*?)aa",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_137() {
        $test1 = array('str'=>"aÀaaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>4));

        $test2 = array('str'=>"aÀaÀaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>7,1=>5));

        return array('regex'=>"^([a\\x{c0}]*)aa",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_138() {
        $test1 = array('str'=>"aÀaaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>0));

        $test2 = array('str'=>"aÀaÀaaa/",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"^([a\\x{c0}]*)a\\x{c0}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_139() {
        $test1 = array('str'=>"AABģBAA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"A*",
                     'modifiers'=>"g",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_140() {
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

    function data_for_test_141() {
        $test1 = array('str'=>"abc",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(abc)\\1",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_142() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a(*:a\\x{1234}b)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_143() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a(*:a£b)",
                     'modifiers'=>"K",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_144() {
        $test1 = array('str'=>"￾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test2 = array('str'=>"￿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test3 = array('str'=>"🿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test4 = array('str'=>"🿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test5 = array('str'=>"𯿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test6 = array('str'=>"𯿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test7 = array('str'=>"𿿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test8 = array('str'=>"𿿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test9 = array('str'=>"񏿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test10 = array('str'=>"񏿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test11 = array('str'=>"񟿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test12 = array('str'=>"񟿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test13 = array('str'=>"񯿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test14 = array('str'=>"񯿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test15 = array('str'=>"񿿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test16 = array('str'=>"񿿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test17 = array('str'=>"򏿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test18 = array('str'=>"򏿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test19 = array('str'=>"򟿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test20 = array('str'=>"򟿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test21 = array('str'=>"򯿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test22 = array('str'=>"򯿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test23 = array('str'=>"򿿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test24 = array('str'=>"򿿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test25 = array('str'=>"󏿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test26 = array('str'=>"󏿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test27 = array('str'=>"󟿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test28 = array('str'=>"󟿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test29 = array('str'=>"󯿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test30 = array('str'=>"󯿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test31 = array('str'=>"󿿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test32 = array('str'=>"󿿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test33 = array('str'=>"􏿾",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test34 = array('str'=>"􏿿",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test35 = array('str'=>"﷐",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test36 = array('str'=>"﷑",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test37 = array('str'=>"﷒",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test38 = array('str'=>"﷓",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test39 = array('str'=>"﷔",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test40 = array('str'=>"﷕",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test41 = array('str'=>"﷖",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test42 = array('str'=>"﷗",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test43 = array('str'=>"﷘",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test44 = array('str'=>"﷙",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test45 = array('str'=>"﷚",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test46 = array('str'=>"﷛",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test47 = array('str'=>"﷜",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test48 = array('str'=>"﷝",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test49 = array('str'=>"﷞",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test50 = array('str'=>"﷟",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test51 = array('str'=>"﷠",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test52 = array('str'=>"﷡",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test53 = array('str'=>"﷢",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test54 = array('str'=>"﷣",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test55 = array('str'=>"﷤",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test56 = array('str'=>"﷥",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test57 = array('str'=>"﷦",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test58 = array('str'=>"﷧",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test59 = array('str'=>"﷨",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test60 = array('str'=>"﷩",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test61 = array('str'=>"﷪",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test62 = array('str'=>"﷫",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test63 = array('str'=>"﷬",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test64 = array('str'=>"﷭",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test65 = array('str'=>"﷮",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        $test66 = array('str'=>"﷯",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>".",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23, $test24, $test25, $test26, $test27, $test28, $test29, $test30, $test31, $test32, $test33, $test34, $test35, $test36, $test37, $test38, $test39, $test40, $test41, $test42, $test43, $test44, $test45, $test46, $test47, $test48, $test49, $test50, $test51, $test52, $test53, $test54, $test55, $test56, $test57, $test58, $test59, $test60, $test61, $test62, $test63, $test64, $test65, $test66));
    }

    function data_for_test_145() {
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

    function data_for_test_146() {
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

    function data_for_test_147() {
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

    function data_for_test_148() {
        $test1 = array('str'=>"ĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"ĀĀĀ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\x{100}*.{4}",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_149() {
        $test1 = array('str'=>"ĀĀĀĀ",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>4));

        $test2 = array('str'=>"ĀĀĀ",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"^\\x{100}*.{4}",
                     'modifiers'=>"i",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_150() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"^a+[a\\x{200}]",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_151() {
        $test1 = array('str'=>"𐄣𐄤𐄥",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>3));

        return array('regex'=>"^.\\B.\\B.",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

    function data_for_test_152() {
        $test1 = array('str'=>"#𐀀#Ā#􏿿#",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>7));

        return array('regex'=>"^#[^\\x{ffff}]#[^\\x{ffff}]#[^\\x{ffff}]#",
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL),
                     'tests'=>array($test1));
    }

}
