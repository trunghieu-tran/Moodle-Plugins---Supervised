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

if (!defined('NOMATCH')) {
    define('NOMATCH', qtype_preg_matching_results::NO_MATCH_FOUND);
}

class qtype_preg_cross_tests_special_cases {

    function data_for_test_empty_string() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>NOMATCH),
                        'length'=>array(0=>NOMATCH),
                        'left'=>array(3),
                        'correctending'=>'a');

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
                        'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

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
                        'correctending'=>'a');

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
                        'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        return array('regex'=>'\378',
                     'tests'=>array($test1));
    }
}
?>