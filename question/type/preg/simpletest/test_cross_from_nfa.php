<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/simpletest/crosstester.php');

class test_cross_from_nfa extends preg_cross_tester {

    function data_for_test_concat() {
        $test1 = array( 'str'=>'the matcher works',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>16),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'_the matcher works',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>18),
                        'index_last'=>array(0=>17),
                        'left'=>array(17),
                        'next'=>'t');

        $test3 = array( 'str'=>'the matcher',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>10),
                        'left'=>array(6),
                        'next'=>' ');

        return array('regex'=>'^the matcher works',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_alt() {
        $test1 = array( 'str'=>'abcf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'deff',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^abc|def$',
                     'tests'=>array($test1, $test2, $test3));
    }
	
    function data_for_test_assertions_simple_1() {
        $test1 = array( 'str'=>' abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>' 9bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'  b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(2),
                        'next'=>'abcdefghijklmnopqrstuvwxyz');

        return array('regex'=>'^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9]',
                     'tests'=>array($test1, $test2, $test3));
    }
	
    function data_for_test_assertions_simple_2() {
        $test1 = array( 'str'=>'abc?z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abcaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'');    // can't generate a character

        return array('regex'=>'^abc[a-z.?!]\b[a-zA-Z]',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_zero_length_loop() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^[a-z 0-9]*(?:\b)+a${1,}',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatterns_nested() {
        $test1 = array( 'str'=>'abcbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1,3=>2),
                        'index_last'=>array(0=>5,1=>4,2=>2,3=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a((b(c))*)d$',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatterns_concatenated() {
        $test1 = array( 'str'=>'_abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>3,3=>5),
                        'index_last'=>array(0=>6,1=>2,2=>4,3=>6),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(ab)(cd)(ef)',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatterns_alternated() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>-1,4=>-1),
                        'index_last'=>array(0=>1,1=>1,2=>1,3=>-2,4=>-2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'((ab)|(cd)|(efgh))',
                     'tests'=>array($test1));
    }

    function data_for_test_questquant() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_negative_charset() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'acdefghijklmnopqrstuvwxyz0123456789!?., ');

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'aacde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_many_alternatives() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'index_last'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'aceg');

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_repeated_chars() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>1),
                        'left'=>array(1,3),
                        'next'=>'ab');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'index_last'=>array(0=>78),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_brace_finite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>5),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>26),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>35),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_brace_infinite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>5),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>26),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>103),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab{15,}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_plus() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(2),
                        'next'=>'b');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>100),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_cs() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'B');

        return array('regex'=>'aBcD',
                     'tests'=>array($test1));
    }

    function data_for_test_cins() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'index_last'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'aBcD',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_backref_simple() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>11,1=>5,2=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>5,1=>5,2=>2),
                        'left'=>array(6),
                        'next'=>'a');    // backref #1 not captured at all
                        
        $test3 = array( 'str'=>'abcabcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>7,1=>5,2=>2),
                        'left'=>array(4),
                        'next'=>'c');    // backref #1 captured partially

        return array('regex'=>'((abc)\2)\1',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_alternated_backrefs() {
        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>-1,3=>2),
                        'index_last'=>array(0=>3,1=>1,2=>-2,3=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1,2=>0,3=>2),
                        'index_last'=>array(0=>3,1=>-2,2=>1,3=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:(ab)|(cd))(\1|\2)',
                     'tests'=>array($test1, $test2));
    }
    
    function data_for_test_backref_quantified() {
        $test1 = array( 'str'=>'ababcdababcdababcdababcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'index_last'=>array(0=>23,1=>5,2=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0,3=>2),
                        'index_last'=>array(0=>3,2=>1,3=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'((ab)\2cd)*\1',
                     'tests'=>array($test1));
    }
}

?>