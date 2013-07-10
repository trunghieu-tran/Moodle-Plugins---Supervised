<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_future {

    // From NFA.
    function data_for_test_assertions_simple_2() {
        $test1 = array( 'str'=>'abc?z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);

        $test2 = array( 'str'=>'abcaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER);    // Can't generate a character.

        return array('regex'=>'^abc[a-z.?!]\b[a-zA-Z]',
                     'tests'=>array($test1, $test2));
    }
}
