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
    
    // For asserts with modifiers.
    function data_for_test_assertions_modifier_1() {
        $test1 = array( 'str'=>"abc\nab",	
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));
        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a');
        $test3 = array( 'str'=>'abcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0=>2),
                        'next'=>'\n');
            
        return array('regex'=>'(?m)\Aabc\n^a',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_2() {
        $test1 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a');

        $test3 = array( 'str'=>"kl\nab\nab\nab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>5));

        $test4 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0=>3),
                        'next'=>'\n');    
        return array('regex'=>'(?m)^ab\n^ab',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_3() {
        $test1 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5));

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'next'=>'\n');


        $test3 = array( 'str'=>"kl\nab\nab\nab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>5));


        $test4 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0=>3),
                        'next'=>'\n');
  
        return array('regex'=>'(?m)^ab$\n^ab',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_4() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        );

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');
  
        return array('regex'=>'(?m)\n^',
                     'tests'=>array($test1, $test2));
    }
    
    function data_for_test_assertions_modifier_5() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1));
  
        return array('regex'=>'(?m)$\n',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_6() {
        $test1 = array( 'str'=>"\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test2 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\n');

        $test3 = array( 'str'=>"ab\nab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        );
  
        return array('regex'=>'(?m)$\n^',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_7() {
        $test1 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=> '\n');
  
        return array('regex'=>'(?m)$a^',
                     'tests'=>array($test1));
    }
    
    function data_for_test_assertions_modifier_8() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a');
        
        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\n');
                        
        $test4 = array( 'str'=>"a\nb\n\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(),
                        'next'=>'NEXT_CHAR_END_HERE');

        return array('regex'=>'(?m)a\nb\Z\n',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_9() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));


        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');
        
        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a\nb\Z',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_10() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'NEXT_CHAR_END_HERE');

        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');
        
        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a\nb\z',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_10() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'NEXT_CHAR_END_HERE');

        $test2 = array( 'str'=>"kl\nkl",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a');
        
        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        return array('regex'=>'a\nb\z',
                     'tests'=>array($test1, $test2, $test3));
    }
    
    function data_for_test_assertions_modifier_11() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=> 'NEXT_CHAR_CANNOT_GENERATE ');

        return array('regex'=>'(?D)a$\n',
                     'tests'=>array($test1));
    }
    
    function data_for_test_assertions_modifier_12() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));


        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a[a-z0-9\n]^b',
                     'tests'=>array($test1, $test2));
    }
    
    function data_for_test_assertions_modifier_13() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$[ab0-9\n]b',
                     'tests'=>array($test1, $test2));
    }
    
    function data_for_test_assertions_modifier_14() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\n');

        return array('regex'=>'(?m)a$[ab0-9\n]^b',
                     'tests'=>array($test1, $test2));
    }
    
    function data_for_test_assertions_modifier_15() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>' NEXT_CHAR_CANNOT_GENERATE ');

        return array('regex'=>'(?m)a$[ab0-9]^b',
                     'tests'=>array($test1));
    }
    
    function data_for_test_assertions_modifier_16() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>1));

        $test2 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>0));

        $test3 = array( 'str'=>"b\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2), 
                        'next'=>'a');
                        
        $test4 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>0));
        
        $test5 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1),
                        'left'=>array(1),
                        'next'=>'\n');

        return array('regex'=>'(?m)a(b|$)\n',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
    
    function data_for_test_assertions_modifier_17() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test2 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test3 = array( 'str'=>"b\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),  
                        'next'=>'a');
                        
        $test4 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        $test5 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>2, 1=>1));

        return array('regex'=>'(?m)a(b|$\n)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
    
    function data_for_test_assertions_modifier_18() {
        $test1 = array( 'str'=>"a\nbc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1));

        $test2 = array( 'str'=>"a\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>0));

        $test3 = array( 'str'=>"a\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),  
                        'next'=>'c');
                        
        $test4 = array( 'str'=>"a\nb\nс",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>3, 1=>1),
                        'left'=>array(1),  
                        'next'=>'c');

        return array('regex'=>'(?m)a\n(b|^)c',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_19() {
        $test1 = array( 'str'=>"aab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'a');

        $test4 = array( 'str'=>"aab\nab\nab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>10, 1=>9));

        return array('regex'=>'(?m)a(ab\n)?',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_20() {
        $test1 = array( 'str'=>"ab\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>4, 1=>3));

        $test2 = array( 'str'=>"ab",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),  
                        'next'=>'\n');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'c');

        $test4 = array( 'str'=>"ab\nab\nab\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>10, 1=>9));

        return array( 'regex'=>'(?m)(ab$\n)*c',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_21() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),  
                        'next'=>'\n');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),  
                        'next'=>'a');

        $test4 = array( 'str'=>"ab\nab\nab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9));

        return array('regex'=>'(?m)(^ab$\n)+',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_22() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>3, 1=>2));

        $test2 = array( 'str'=>"\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'\n');

        $test4 = array( 'str'=>"\na",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),  
                        'next'=>'\n');

        return array('regex'=>'(?m)[a-z\n](^a$\n |)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_23() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>1),
                        'length'=>array(0=>1, 1=>0));

        $test2 = array( 'str'=>"\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>0));


        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'\n');

        $test4 = array( 'str'=>"\na",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>1, 1=>0));

        return array('regex'=>'(?m)\n(^|)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_24() {
        $test1 = array( 'str'=>"\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2));

        $test2 = array( 'str'=>"a\na\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4));

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),  
                        'next'=>'a');

        $test4 = array( 'str'=>"\na\na",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2));

        return array('regex'=>'(?m)(^a$\n)*',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
    
    function data_for_test_assertions_modifier_25() {
        $test1 = array( 'str'=>"ab\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2),
                        'length'=>array(0=>3, 1=>1, 2=>1));

        $test2 = array( 'str'=>"\nab\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'b');

        $test3 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),  
                        'next'=>'b');

        $test4 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1));
                        
        $test5 = array( 'str'=>"ab\n\n\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0, 2=>2),
                        'length'=>array(0=>5, 1=>1, 2=>3));

        return array('regex'=>'(?m)\A(^a|)b($\n)*\z',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
    
    function data_for_test_assertions_modifier_26() {
        $test1 = array( 'str'=>"abc\nab",	
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),  
                        'next'=>'a');

        $test2 = array( 'str'=>"klabc\nab",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a');

        $test3 = array( 'str'=>'abcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0=>2),
                        'next'=>'\n');

        return array('regex'=>'(?D)\Aabc\n$a',
                     'tests'=>array($test1, $test2, $test3));
    }
}
