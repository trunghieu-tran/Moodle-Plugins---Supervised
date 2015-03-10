<?php

class qtype_preg_cross_tests_from_att_austin {

    function data_for_test_att_austin_0() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>3,4=>10),
                       'length'=>array(0=>10,1=>10,2=>3,3=>7,4=>0));

        return array('regex'=>"((week|wee)(night|knights))(s*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_1() {
        $test1 = array('str'=>"wxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1),
                       'length'=>array(0=>4,1=>1,2=>3));

        return array('regex'=>"(w|wx)(y|x.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_2() {
        $test1 = array('str'=>"wxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2),
                       'length'=>array(0=>4,1=>2,2=>1));

        return array('regex'=>"(w|wx)(y|x.*).*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_3() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"(a{2,3})\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_4() {
        $test1 = array('str'=>"aaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>3,1=>3));

        return array('regex'=>"(a{2,3})\\1*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_5() {
        $test1 = array('str'=>"aaaa",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>4,1=>2));

        return array('regex'=>"(a{2,3})\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_6() {
        $test1 = array('str'=>"ba",
                       'is_match'=>false,
                       'full'=>false,
                       'index_first'=>array(),
                       'length'=>array());

        return array('regex'=>"(a*(b)*){2}\\2",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL));
    }

    function data_for_test_att_austin_7() {
        $test1 = array('str'=>"xxxxx",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>5,1=>5,3=>5));

        return array('regex'=>"((..)*(.....)*)*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_8() {
        $test1 = array('str'=>"bbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(^b)\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_9() {
        $test1 = array('str'=>"bbbb",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>1));

        return array('regex'=>"(^b)\\1",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_10() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4,3=>9),
                       'length'=>array(0=>10,1=>4,2=>5,3=>1));

        return array('regex'=>"(week|wee)(night|knights)(s*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_11() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(.|..).*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_12() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(..)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_13() {
        $test1 = array('str'=>"ab",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0),
                       'length'=>array(0=>2,1=>2));

        return array('regex'=>"(.{1,2})",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_14() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4,3=>9),
                       'length'=>array(0=>10,1=>4,2=>5,3=>1));

        return array('regex'=>"(wee|week)(knights|night)(s*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_15() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>10,1=>3,2=>7));

        return array('regex'=>"(wee|week)(knights|night)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_16() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3),
                       'length'=>array(0=>10,1=>3,2=>7));

        return array('regex'=>"(week|wee)(night|knights)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_17() {
        $test1 = array('str'=>"abcabc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>0),
                       'length'=>array(0=>6,1=>3,3=>3));

        return array('regex'=>"((abc){0,2}(abc){0,2})\\3",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_18() {
        $test1 = array('str'=>"azbazbyc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>5),
                       'length'=>array(0=>8,1=>2));

        return array('regex'=>"(a.*z|b.*y)*.*",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_19() {
        $test1 = array('str'=>"xxxxyz",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>4),
                       'length'=>array(0=>6,1=>0));

        return array('regex'=>"(x?)*y\\1z?",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_20() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>3,3=>4,4=>9),
                       'length'=>array(0=>10,1=>3,2=>1,3=>5,4=>1));

        return array('regex'=>"(wee)(k*)(night|knights)(s*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }

    function data_for_test_att_austin_21() {
        $test1 = array('str'=>"weeknights",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>4,3=>9),
                       'length'=>array(0=>10,1=>3,2=>5,3=>1));

        return array('regex'=>"(wee)k*(night|knights)(s*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX));
    }
}
