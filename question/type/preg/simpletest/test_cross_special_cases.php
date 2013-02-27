<?php
/**
 * Unit tests for matchers
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/simpletest/crosstester.php');

class test_cross_special_cases extends preg_cross_tester {

    function data_for_test_empty_string() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>-1),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'abc',
                     'tests'=>array($test1));
    }

    function data_for_test_backref_to_uncaptured_subpatt() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>-1),
                        'index_last'=>array(0=>1,1=>1,2=>-2),
                        'left'=>array(10000000),
                        'next'=>'');

        return array('regex'=>'(?:(ab)|(cd))\2',
                     'tests'=>array($test1));
    }

    function data_for_test_circumflex_should_not_be_matched() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'index_last'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'^a',
                     'tests'=>array($test1));
    }
}
?>