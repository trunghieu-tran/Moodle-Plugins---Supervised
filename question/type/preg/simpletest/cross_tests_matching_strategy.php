<?php
/**
 * Unit tests for matchers
 *
 * @copyright &copy; 2012  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

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
                        'index_first'=>array(0=>0,1=>0,2=>-1),
                        'length'=>array(0=>4,1=>4,2=>-1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a*)(a*)',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_1() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(4),
                        'next'=>'a');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(3),
                        'next'=>'a');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[0],    // (a{2,5}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_2() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(4),
                        'next'=>'a');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(3),
                        'next'=>'a');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[1],    // (a{2,5}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_3() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[2],    // (a{2,5}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_4() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[0].')'.$this->quants[3],    // (a{2,5})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_5() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(4),
                        'next'=>'a');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(3),
                        'next'=>'a');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[0],    // (a{2,}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_6() {
        $test0 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(4),
                        'next'=>'a');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(3),
                        'next'=>'a');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'a');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + (aa)
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[1],    // (a{2,}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_7() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),    // aaaa + (aa)
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[2],    // (a{2,}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_8() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaaa)
                        'length'=>array(0=>6,1=>6),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[1].')'.$this->quants[3],    // (a{2,})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_9() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + (aa)
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + (a)
                        'length'=>array(0=>6,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[0],    // (a{,5}){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_10() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaa)
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // (aaaaa) + ''
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + (a)
                        'length'=>array(0=>6,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[1],    // (a{,5}){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_11() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaa)
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + (a)
                        'length'=>array(0=>6,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[2],    // (a{,5}){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_12() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaa)
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + (a)
                        'length'=>array(0=>6,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[2].')'.$this->quants[3],    // (a{,5})*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_13() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[0],    // (a*){2,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_14() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),    // a + ('')
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),    // aaa + ('')
                        'length'=>array(0=>3,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),    // aaaaa + ('')
                        'length'=>array(0=>5,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),    // aaaaaa + ('')
                        'length'=>array(0=>6,1=>0),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[1],    // (a*){2,}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_15() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (a)
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaa)
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaaa)
                        'length'=>array(0=>6,1=>6),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[2],    // (a*){,5}
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_16() {
        $test0 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (a)
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaa)
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaa)
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),    // (aaaaaa)
                        'length'=>array(0=>6,1=>6),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a'.$this->quants[3].')'.$this->quants[3],    // (a*)*
                     'tests'=>array($test0, $test1, $test2, $test3, $test4));
    }
}

?>