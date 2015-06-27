<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_preg_merging {

    // From NFA.
    
    function data_for_test_assertions_wordboundary_62() {
        $test1 = array( 'str'=>' c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>'  ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'\w');

        return array('regex'=>'[\w\W]\b\w',
                     'tests'=>array($test1, $test2),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    // Asserts with tags.
    
}
