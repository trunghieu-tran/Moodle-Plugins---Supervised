<?php

/**
 * Unit tests for matchers
 *
 * @copyright &copy; 2012  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

defined('NOMATCH') || define('NOMATCH', qtype_preg_matching_results::NO_MATCH_FOUND);

class qtype_preg_cross_tests_matching_strategy {

    public $quants = array('{2,5}', '{2,}', '{,5}', '*');

    function setUp() {
    }

    function tearDown() {
    }

    function data_for_test_quant_0() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>4,1=>4,2=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a*)(a*)',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_1() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[0],    // (a{2,5}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_2() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[1],    // (a{2,5}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_3() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[2],    // (a{2,5}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_4() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[3],    // (a{2,5})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_5() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[0],    // (a{2,}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_6() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH,1=>NOMATCH),
                        'length'=>array(0=>NOMATCH,1=>NOMATCH),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[1],    // (a{2,}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_7() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[2],    // (a{2,}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_8() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>0,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>NOMATCH),
                        'length'=>array(0=>1,1=>NOMATCH),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[3],    // (a{2,})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_9() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[0],    // (a{,5}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_10() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[1],    // (a{,5}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_11() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[2],    // (a{,5}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_12() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[3],    // (a{,5})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_13() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[0],    // (a*){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_14() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[1],    // (a*){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_15() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[2],    // (a*){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_16() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_NFA));

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[3],    // (a*)*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }
}
