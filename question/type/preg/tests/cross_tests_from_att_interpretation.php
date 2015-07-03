<?php

class qtype_preg_cross_tests_from_att_interpretation {

    function data_for_test_att_interpretation_0() {
        $test1 = array('str'=>"xaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1),
                       'length'=>array(0=>2));

        return array('regex'=>"a+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_1() {
        $test1 = array('str'=>"xaax",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>".(a*).",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_2() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0),
                       'length'=>array(0=>2,1=>0,2=>2,3=>2));

        return array('regex'=>"(a?)((ab)?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_3() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,4=>1),
                       'length'=>array(0=>2,1=>1,2=>0,4=>1));

        return array('regex'=>"(a?)((ab)?)(b?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_4() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>2),
                       'length'=>array(0=>2,1=>2,2=>0,3=>2,4=>2,5=>0));

        return array('regex'=>"((a?)((ab)?))(b?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_5() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>1,5=>1),
                       'length'=>array(0=>2,1=>1,2=>1,3=>0,5=>1));

        return array('regex'=>"(a?)(((ab)?)(b?))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_6() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_7() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.?){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_8() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>1,1=>1,2=>0));

        return array('regex'=>"(.?)(.?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_9() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>1,1=>0));

        return array('regex'=>"(.?){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_10() {
        $test1 = array('str'=>"x",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.?)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_11() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(.?.?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_12() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(.?.?){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_13() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>2,2=>1));

        return array('regex'=>"(.?.?)(.?.?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_14() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(.?.?){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_15() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>3,1=>2,2=>1,3=>0));

        return array('regex'=>"(.?.?)(.?.?)(.?.?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_16() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"(.?.?){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_17() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(.?.?)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_18() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,3=>1),
                       'length'=>array(0=>2,1=>0,3=>1));

        return array('regex'=>"a?((ab)?)(b?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_19() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>0));

        return array('regex'=>"(a?)((ab)?)b?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_20() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"a?((ab)?)b?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_21() {
        $test1 = array('str'=>"xxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(a*){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_22() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>2,2=>1));

        return array('regex'=>"(ab?)(b?a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_23() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>2,2=>1));

        return array('regex'=>"(a|ab)(ba|a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_24() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(a|ab|ba)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_25() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>2,2=>1));

        return array('regex'=>"(a|ab|ba)(a|ab|ba)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_26() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a|ab|ba)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_27() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"(aba|a*b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_28() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>5,1=>2,2=>3));

        return array('regex'=>"(aba|a*b)(aba|a*b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_29() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>5,1=>3));

        return array('regex'=>"(aba|a*b)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_30() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"(aba|ab|a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_31() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>5,1=>2,2=>3));

        return array('regex'=>"(aba|ab|a)(aba|ab|a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_32() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>5,1=>3));

        return array('regex'=>"(aba|ab|a)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_33() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        return array('regex'=>"(a(b)?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_34() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>2,2=>1,3=>1));

        return array('regex'=>"(a(b)?)(a(b)?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_35() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a(b)?)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_36() {
        $test1 = array('str'=>"xx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>2,1=>2,2=>0));

        return array('regex'=>"(.*)(.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_37() {
        $test1 = array('str'=>"xx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>".*(.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_38() {
        $test1 = array('str'=>"azbazby",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>5));

        return array('regex'=>"(a.*z|b.*y)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_39() {
        $test1 = array('str'=>"azbazby",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>5),
                       'length'=>array(0=>7,1=>5,2=>2));

        return array('regex'=>"(a.*z|b.*y)(a.*z|b.*y)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_40() {
        $test1 = array('str'=>"azbazby",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>7,1=>2));

        return array('regex'=>"(a.*z|b.*y)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_41() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>2,1=>2,2=>0));

        return array('regex'=>"(.|..)(.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_42() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>3,1=>3,3=>3));

        return array('regex'=>"((..)*(...)*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_43() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0,4=>3),
                       'length'=>array(0=>3,1=>3,3=>3,4=>0));

        return array('regex'=>"((..)*(...)*)((..)*(...)*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_44() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>3,1=>3,3=>3));

        return array('regex'=>"((..)*(...)*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_45() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_46() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_47() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_48() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_49() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"(a{0,1})*b(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_50() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"(a*)*b(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_51() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(a*)b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_52() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>2),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_53() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_54() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_55() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_56() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_57() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>3,1=>1,2=>1));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_58() {
        $test1 = array('str'=>"abaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_59() {
        $test1 = array('str'=>"abaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_60() {
        $test1 = array('str'=>"abaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_61() {
        $test1 = array('str'=>"abaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_62() {
        $test1 = array('str'=>"abaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>4,1=>1,2=>1));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_63() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_64() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_65() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_66() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_67() {
        $test1 = array('str'=>"aab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>3),
                       'length'=>array(0=>3,1=>0,2=>0));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_68() {
        $test1 = array('str'=>"aaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_69() {
        $test1 = array('str'=>"aaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_70() {
        $test1 = array('str'=>"aaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>2));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_71() {
        $test1 = array('str'=>"aaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_72() {
        $test1 = array('str'=>"aaba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>3),
                       'length'=>array(0=>4,1=>1,2=>1));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_73() {
        $test1 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(a{0,1})*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_74() {
        $test1 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"(a*)*b\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_75() {
        $test1 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"(a*)b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_76() {
        $test1 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>5,1=>2));

        return array('regex'=>"(a*)*b\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_77() {
        $test1 = array('str'=>"aabaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>5,1=>2,2=>2));

        return array('regex'=>"(a*)*b(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_78() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(x)*a\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_interpretation_79() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(x)*a\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_80() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(x)*a(\\1)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_interpretation_81() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1));

        return array('regex'=>"(x)*a(\\1)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_82() {
        $test1 = array('str'=>"aabbaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>2));

        return array('regex'=>"(aa(b(b))?)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_83() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a(b)?)+",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_84() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"([ab]+)([bc]+)([cd]*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_85() {
        $test1 = array('str'=>"abcdaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                       'length'=>array(0=>5,1=>1,2=>2,3=>1));

        return array('regex'=>"([ab]*)([bc]*)([cd]*)\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_86() {
        $test1 = array('str'=>"abcdab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>6,1=>2,2=>1,3=>1));

        return array('regex'=>"([ab]*)([bc]*)([cd]*)\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_87() {
        $test1 = array('str'=>"abcdaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                       'length'=>array(0=>6,1=>1,2=>2,3=>1));

        return array('regex'=>"([ab]*)([bc]*)([cd]*)\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_88() {
        $test1 = array('str'=>"abcdab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>6,1=>2,2=>1,3=>1));

        return array('regex'=>"([ab]*)([bc]*)([cd]*)\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_89() {
        $test1 = array('str'=>"Aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>2,2=>1));

        return array('regex'=>"^(A([^B]*))?(B(.*))?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_90() {
        $test1 = array('str'=>"Bb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,3=>0,4=>1),
                       'length'=>array(0=>2,3=>2,4=>1));

        return array('regex'=>"^(A([^B]*))?(B(.*))?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_91() {
        $test1 = array('str'=>"ABA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>".*([AB]).*\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_interpretation_92() {
        $test1 = array('str'=>"\nA",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"[^A]*A",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }
}
