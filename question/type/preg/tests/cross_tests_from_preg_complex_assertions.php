<?php

/**
 * Unit tests for matchers
 *
 * @copyright 2015  Valeriy Streltsov, Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg_complex_assertions {

    function data_for_test_1() {
        $test1 = array('str'=>'gccbadcdcd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test2 = array('str'=>'gccabdcdcd',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>10));

        $test3 = array('str'=>'gccaddcdcd',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(2),
                       'next'=>'b');

        return array('regex'=>'g(?=[bcd]*(?=[cd]*b)a)[abcd]*',
                     'tests'=>array($test1, $test2, $test3),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }

    function data_for_test_2() {
        $test1 = array('str'=>'aa',
                       'is_match'=>true,
                       'full'=>false,
                       'index_first'=>array(0=>0),
                       'length'=>array(0=>1),
                       'left'=>array(1),
                       'next'=>'%');

        return array('regex'=>'a(?=[%asd])\W',
                     'tests'=>array($test1),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FROM_DFA));
    }
}
