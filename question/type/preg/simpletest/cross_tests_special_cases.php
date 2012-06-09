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

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

defined('NOMATCH') || define('NOMATCH', qtype_preg_matching_results::NO_MATCH_FOUND);

class qtype_preg_cross_tests_special_cases {

    function data_for_test_empty_string() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(3),
                        'next'=>'a');

        return array('regex'=>'abc',
                     'tests'=>array($test1));
    }

    function data_for_test_backref_to_uncaptured_subpatt() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>NOMATCH),
                        'length'=>array(0=>2,1=>2,2=>NOMATCH),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'(?:(ab)|(cd))\2',
                     'tests'=>array($test1));
    }

    function data_for_test_circumflex_should_not_be_matched() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(1),
                        'next'=>'a');

        return array('regex'=>'^a',
                     'tests'=>array($test1));
    }

    function data_for_test_unobvious_backslash() {
        $test1 = array( 'str'=>chr(octdec(37)).'8',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\378',
                     'tests'=>array($test1));
    }

    function data_for_test_unicode() {
        $test1 = array( 'str'=>'абв',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'é');

        return array('regex'=>'абвé',
                     'tests'=>array($test1));
    }
}