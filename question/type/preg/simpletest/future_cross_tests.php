<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class qtype_preg_cross_tests_future {

    // from nfa
    function data_for_test_assertions_simple_2() {
        $test1 = array( 'str'=>'abc?z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abcaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'correctending'=>'');    // can't generate a character

        return array('regex'=>'^abc[a-z.?!]\b[a-zA-Z]',
                     'tests'=>array($test1, $test2));
    }
}
?>