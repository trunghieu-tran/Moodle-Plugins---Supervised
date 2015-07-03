<?php

class qtype_preg_cross_tests_from_att_repetition {

    function data_for_test_att_repetition_0() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_1() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_2() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_3() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_4() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_5() {
        $test1 = array('str'=>"",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_6() {
        $test1 = array('str'=>"",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>0));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_7() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>1,1=>1,3=>1));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_8() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_9() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_10() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>1,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_11() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_12() {
        $test1 = array('str'=>"a",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_13() {
        $test1 = array('str'=>"a",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>1,1=>1,3=>1));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_14() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_15() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0,4=>1,6=>1),
                       'length'=>array(0=>2,1=>1,3=>1,4=>1,6=>1));

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_16() {
        $test1 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_17() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_18() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>1,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_19() {
        $test1 = array('str'=>"aa",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_20() {
        $test1 = array('str'=>"aa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_21() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_22() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,6=>2),
                       'length'=>array(0=>3,1=>2,2=>2,4=>1,6=>1));

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_23() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0,4=>1,6=>1,7=>2,9=>2),
                       'length'=>array(0=>3,1=>1,3=>1,4=>1,6=>1,7=>1,9=>1));

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_24() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_25() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,3=>2),
                       'length'=>array(0=>3,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_26() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,3=>2),
                       'length'=>array(0=>3,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_27() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,3=>2),
                       'length'=>array(0=>3,1=>1,3=>1));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_28() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_29() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,5=>2),
                       'length'=>array(0=>4,1=>2,2=>2,4=>2,5=>2));

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_30() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,6=>2,7=>3,9=>3),
                       'length'=>array(0=>4,1=>2,2=>2,4=>1,6=>1,7=>1,9=>1));

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_31() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_32() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_33() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,3=>3),
                       'length'=>array(0=>4,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_34() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_35() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_36() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,5=>2),
                       'length'=>array(0=>4,1=>2,2=>2,4=>2,5=>2));

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_37() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,5=>2,7=>4,9=>4),
                       'length'=>array(0=>5,1=>2,2=>2,4=>2,5=>2,7=>1,9=>1));

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_38() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_39() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_40() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4,3=>4),
                       'length'=>array(0=>5,1=>1,3=>1));

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_41() {
        $test1 = array('str'=>"aaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4,3=>4),
                       'length'=>array(0=>5,1=>1,3=>1));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_42() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_43() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,5=>2),
                       'length'=>array(0=>4,1=>2,2=>2,4=>2,5=>2));

        return array('regex'=>"((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_44() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,4=>2,5=>2,7=>4,8=>4),
                       'length'=>array(0=>6,1=>2,2=>2,4=>2,5=>2,7=>2,8=>2));

        return array('regex'=>"((..)|(.))((..)|(.))((..)|(.))",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_45() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0),
                       'length'=>array(0=>2,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){1}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_46() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>2,2=>2),
                       'length'=>array(0=>4,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){2}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_47() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4,2=>4),
                       'length'=>array(0=>6,1=>2,2=>2));

        return array('regex'=>"((..)|(.)){3}",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_48() {
        $test1 = array('str'=>"aaaaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4,2=>4),
                       'length'=>array(0=>6,1=>2,2=>2));

        return array('regex'=>"((..)|(.))*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_49() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){0,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_50() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){1,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_51() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){2,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_52() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){3,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_53() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){4,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_54() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){5,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_55() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){6,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_56() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){7,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_57() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>9,1=>0));

        return array('regex'=>"X(.?){8,}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_58() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){0,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_59() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){1,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_60() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){2,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_61() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){3,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_62() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){4,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_63() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){5,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_64() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){6,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_65() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>7),
                       'length'=>array(0=>9,1=>1));

        return array('regex'=>"X(.?){7,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_66() {
        $test1 = array('str'=>"X1234567Y",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>8),
                       'length'=>array(0=>9,1=>0));

        return array('regex'=>"X(.?){8,8}Y",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_67() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){0,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_68() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){1,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_69() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){2,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_70() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){3,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_71() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a|ab|c|bcd){4,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_72() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){0,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_73() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){1,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_74() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){2,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_75() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd){3,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_76() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a|ab|c|bcd){4,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_77() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd)*(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_78() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(a|ab|c|bcd)+(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_79() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){0,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_80() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){1,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_81() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){2,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_82() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){3,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_83() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(ab|a|c|bcd){4,}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_84() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){0,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_85() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){1,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_86() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){2,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_87() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd){3,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_88() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(ab|a|c|bcd){4,10}(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_repetition_89() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd)*(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_repetition_90() {
        $test1 = array('str'=>"ababcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>3,2=>6),
                       'length'=>array(0=>6,1=>3,2=>0));

        return array('regex'=>"(ab|a|c|bcd)+(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }
}
