<?php

class qtype_preg_cross_tests_from_att_rightassoc {

    function data_for_test_att_rightassoc_0() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(a|ab)(c|bcd)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_1() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(a|ab)(bcd|c)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_2() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(ab|a)(c|bcd)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_3() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(ab|a)(bcd|c)(d*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_4() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        return array('regex'=>"(a*)(b|abc)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_5() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        return array('regex'=>"(a*)(abc|b)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_6() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        return array('regex'=>"(a*)(b|abc)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_7() {
        $test1 = array('str'=>"abc",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                       'length'=>array(0=>3,1=>1,2=>1,3=>1));

        return array('regex'=>"(a*)(abc|b)(c*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_8() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(a|ab)(c|bcd)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_9() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(a|ab)(bcd|c)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_10() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(ab|a)(c|bcd)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }

    function data_for_test_att_rightassoc_11() {
        $test1 = array('str'=>"abcd",
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>2,3=>3),
                       'length'=>array(0=>4,1=>2,2=>1,3=>1));

        return array('regex'=>"(ab|a)(bcd|c)(d|.*)",
                     'modifiers'=>'Ds',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX, qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
    }
}
