<?php

class qtype_preg_cross_tests_from_att_leftassoc {

    function data_for_test_att_leftassoc_0() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(a|ab)(c|bcd)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_1() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(a|ab)(bcd|c)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_2() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(ab|a)(c|bcd)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_3() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(ab|a)(bcd|c)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_4() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>3),
                       'length'=>array(0=>3,1=>0,2=>3,3=>0));

        return array('regex'=>"(a*)(b|abc)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_5() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>3),
                       'length'=>array(0=>3,1=>0,2=>3,3=>0));

        return array('regex'=>"(a*)(abc|b)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_6() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>3),
                       'length'=>array(0=>3,1=>0,2=>3,3=>0));

        return array('regex'=>"(a*)(b|abc)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_7() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>3),
                       'length'=>array(0=>3,1=>0,2=>3,3=>0));

        return array('regex'=>"(a*)(abc|b)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_8() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(a|ab)(c|bcd)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_9() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(a|ab)(bcd|c)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_10() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(ab|a)(c|bcd)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }

    function data_for_test_att_leftassoc_11() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>4),
                       'length'=>array(0=>4,1=>1,2=>3,3=>0));

        return array('regex'=>"(ab|a)(bcd|c)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_LEFT));
    }
}
