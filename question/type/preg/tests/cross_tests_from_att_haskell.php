<?php

class qtype_preg_cross_tests_from_att_haskell {

    function data_for_test_att_haskell_0() {
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

    function data_for_test_att_haskell_1() {
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

    function data_for_test_att_haskell_2() {
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

    function data_for_test_att_haskell_3() {
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

    function data_for_test_att_haskell_4() {
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

    function data_for_test_att_haskell_5() {
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

    function data_for_test_att_haskell_6() {
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

    function data_for_test_att_haskell_7() {
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

    function data_for_test_att_haskell_8() {
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

    function data_for_test_att_haskell_9() {
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

    function data_for_test_att_haskell_10() {
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

    function data_for_test_att_haskell_11() {
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

    function data_for_test_att_haskell_12() {
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

    function data_for_test_att_haskell_13() {
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

    function data_for_test_att_haskell_14() {
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

    function data_for_test_att_haskell_15() {
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

    function data_for_test_att_haskell_16() {
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

    function data_for_test_att_haskell_17() {
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

    function data_for_test_att_haskell_18() {
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

    function data_for_test_att_haskell_19() {
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

    function data_for_test_att_haskell_20() {
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

    function data_for_test_att_haskell_21() {
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

    function data_for_test_att_haskell_22() {
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

    function data_for_test_att_haskell_23() {
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

    function data_for_test_att_haskell_24() {
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

    function data_for_test_att_haskell_25() {
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

    function data_for_test_att_haskell_26() {
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

    function data_for_test_att_haskell_27() {
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

    function data_for_test_att_haskell_28() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(aba|a*b)(aba|a*b)(aba|a*b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_haskell_29() {
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

    function data_for_test_att_haskell_30() {
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

    function data_for_test_att_haskell_31() {
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

    function data_for_test_att_haskell_32() {
        $test1 = array('str'=>"ababa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>4),
                       'length'=>array(0=>5,1=>2,2=>2,3=>1));

        return array('regex'=>"(aba|ab|a)(aba|ab|a)(aba|ab|a)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_33() {
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

    function data_for_test_att_haskell_34() {
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

    function data_for_test_att_haskell_35() {
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

    function data_for_test_att_haskell_36() {
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

    function data_for_test_att_haskell_37() {
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

    function data_for_test_att_haskell_38() {
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

    function data_for_test_att_haskell_39() {
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

    function data_for_test_att_haskell_40() {
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

    function data_for_test_att_haskell_41() {
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

    function data_for_test_att_haskell_42() {
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

    function data_for_test_att_haskell_43() {
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

    function data_for_test_att_haskell_44() {
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

    function data_for_test_att_haskell_45() {
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

    function data_for_test_att_haskell_46() {
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

    function data_for_test_att_haskell_47() {
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

    function data_for_test_att_haskell_48() {
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

    function data_for_test_att_haskell_49() {
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

    function data_for_test_att_haskell_50() {
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

    function data_for_test_att_haskell_51() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(^){0,3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_52() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"(\$){0,3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_53() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(^){1,3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_54() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>1,1=>1),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"(\$){1,3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_55() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>1,1=>1,3=>1));

        return array('regex'=>"((s^)|(s)|(^)|(\$)|(^.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_56() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"s(()|^)e",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_57() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"s(^|())e",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_58() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>2,1=>0,2=>0));

        return array('regex'=>"s(^|())e",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_59() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>0));

        return array('regex'=>"s()?e",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_60() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>2));

        return array('regex'=>"s(^)?e",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_61() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,4=>2),
                       'length'=>array(0=>3,1=>1,4=>1));

        return array('regex'=>"((s)|(e)|(a))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_62() {
        $test1 = array('str'=>"searchme",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"((s)|(e)|())*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_63() {
        $test1 = array('str'=>"cbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,2=>1),
                       'length'=>array(0=>3,1=>2,2=>2));

        return array('regex'=>"((b*)|c(c*))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_64() {
        $test1 = array('str'=>"yyyyyy",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3),
                       'length'=>array(0=>6,1=>3));

        return array('regex'=>"(yyy|(x?)){2,4}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_65() {
        $test1 = array('str'=>"xxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>0,2=>0));

        return array('regex'=>"(\$)|()",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_66() {
        $test1 = array('str'=>"ac\\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>0,2=>0));

        return array('regex'=>"\$()|^()",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_67() {
        $test1 = array('str'=>"ac\\n",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>0,1=>0));

        return array('regex'=>"^()|\$()",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_68() {
        $test1 = array('str'=>"__",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>1,2=>1));

        return array('regex'=>"(\$)?(.)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_69() {
        $test1 = array('str'=>"c",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>1,1=>1));

        return array('regex'=>"(.|()|())*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_70() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"((a)|(b)){2,}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_71() {
        $test1 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,2=>0),
                       'length'=>array(0=>0,2=>0));

        return array('regex'=>".()|((.)?)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_72() {
        $test1 = array('str'=>"xx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(.|\$){2,}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_73() {
        $test1 = array('str'=>"xx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(.|\$){2,2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_74() {
        $test1 = array('str'=>"xx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(.){2,}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_75() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1,5=>2),
                       'length'=>array(0=>3,1=>1,3=>1,5=>1));

        return array('regex'=>"(a|())(b|())(c|())",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_76() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>0));

        return array('regex'=>"ab()c|ab()c()",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_77() {
        $test1 = array('str'=>"bcde",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,3=>3),
                       'length'=>array(0=>4,1=>2,3=>1));

        return array('regex'=>"(b(c)|d(e))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_78() {
        $test1 = array('str'=>"aba",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>3,1=>1));

        return array('regex'=>"(a(b)*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_79() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"(()|.)(b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_80() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"(()|[ab])(b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_81() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"(()|[ab])+b",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_82() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"(.|())(b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_83() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"([ab]|())(b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_84() {
        $test1 = array('str'=>"aaab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2),
                       'length'=>array(0=>4,1=>1));

        return array('regex'=>"([ab]|())+b",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_haskell_85() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>2,1=>1,2=>1));

        return array('regex'=>"(.?)(b)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }
}
