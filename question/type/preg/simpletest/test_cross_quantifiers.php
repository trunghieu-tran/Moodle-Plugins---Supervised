<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/simpletest/crosstester.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');

class test_cross_quantifiers extends preg_cross_tester {

    function setUp() {
    }

    function tearDown() {
    }

    function data_for_test_quant_1() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'index_last'=>array(0=>3,1=>2,2=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(a+)(a+)',
                     'tests'=>array($test1));
    }
}

?>