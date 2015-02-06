<?php

class qtype_preg_cross_tests_from_att_nullsubexpr {

    function data_for_test_att_nullsubexpr_0() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_1() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(a*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_2() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_3() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_4() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_5() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(a*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_6() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_7() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_8() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_9() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(a+)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_10() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a+)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_11() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a+)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_12() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a+)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_13() {
        $test1 = array('str'=>"x",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a+)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_nullsubexpr_14() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a+)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_15() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"(a+)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_16() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_17() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_18() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_19() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_20() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([a]*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_21() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([a]*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_22() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([a]*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_23() {
        $test1 = array('str'=>"aaaaaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([a]*)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_24() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([^b]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_25() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^b]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_26() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([^b]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_27() {
        $test1 = array('str'=>"aaaaaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([^b]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_28() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_29() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_30() {
        $test1 = array('str'=>"ababab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_31() {
        $test1 = array('str'=>"bababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_32() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_33() {
        $test1 = array('str'=>"bbbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_34() {
        $test1 = array('str'=>"aaaabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        return array('regex'=>"([ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_35() {
        $test1 = array('str'=>"b",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"([^a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_36() {
        $test1 = array('str'=>"bbbbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([^a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_37() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^a]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_38() {
        $test1 = array('str'=>"ccccxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>6,1=>6));

        return array('regex'=>"([^ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_39() {
        $test1 = array('str'=>"ababab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"([^ab]*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_40() {
        $test1 = array('str'=>"zabcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"((z)+|a)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_41() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"a+?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_42() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_43() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(a*?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_44() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(a)*?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_45() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(a*?)*?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_46() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>0,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_47() {
        $test1 = array('str'=>"ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_48() {
        $test1 = array('str'=>"axa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_49() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>1),
                       'length'=>array(0=>1,1=>0,2=>1,3=>0));

        return array('regex'=>"(a*)*(x)(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_50() {
        $test1 = array('str'=>"ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1,3=>2),
                       'length'=>array(0=>2,1=>0,2=>1,3=>0));

        return array('regex'=>"(a*)*(x)(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_51() {
        $test1 = array('str'=>"axa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        return array('regex'=>"(a*)*(x)(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_52() {
        $test1 = array('str'=>"axax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                       'length'=>array(0=>4,1=>1,2=>1,3=>1,4=>1));

        return array('regex'=>"(a*)*(x)(\\1)(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_53() {
        $test1 = array('str'=>"axxa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1,3=>2,4=>2),
                       'length'=>array(0=>3,1=>0,2=>1,3=>0,4=>1));

        return array('regex'=>"(a*)*(x)(\\1)(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_54() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>0,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_55() {
        $test1 = array('str'=>"ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_56() {
        $test1 = array('str'=>"axa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)*(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_57() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>0,2=>1));

        return array('regex'=>"(a*)+(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_58() {
        $test1 = array('str'=>"ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)+(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_59() {
        $test1 = array('str'=>"axa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(a*)+(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_60() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>1,1=>0,2=>1));

        return array('regex'=>"(a*){2}(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_61() {
        $test1 = array('str'=>"ax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>1));

        return array('regex'=>"(a*){2}(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_nullsubexpr_62() {
        $test1 = array('str'=>"axa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>1));

        return array('regex'=>"(a*){2}(x)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }
}
