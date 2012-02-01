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

class qtype_preg_cross_tests_from_nfa {

    //-----------------------------------------------------------------------tests for general cases----------------------------------------------------------//
    function data_for_test_concat() {
        $test1 = array( 'str'=>'the matcher works',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'_the matcher works',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>18),
                        'length'=>array(0=>0),
                        'left'=>array(17),
                        'correctending'=>'t');

        $test3 = array( 'str'=>'the matcher',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(6),
                        'correctending'=>' ');

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(17),
                        'correctending'=>'t');

        return array('regex'=>'^the matcher works',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_alt_1() {
        $test1 = array( 'str'=>'abcf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'deff',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^abc|def$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_alt_2() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'correctending'=>'aceg');

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_assertions_simple_1() {
        $test1 = array( 'str'=>' abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>' 9bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'  b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'correctending'=>'abcdefghijklmnopqrstuvwxyz');

        return array('regex'=>'^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_zero_length_loop() {
        $test1 = array( 'str'=>' a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'[prefix] a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^*[a-z 0-9](?:\b)+a${1,}',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_negative_charset() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'correctending'=>' acdefghijklmnopqrstuvwxyz0123456789!?.,');

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'aacde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_case_sensitive() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'correctending'=>'B');

        $test2 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'correctending'=>'c');

        return array('regex'=>'aBcD',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_case_insensitive() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'aBcD',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    //-----------------------------------------------tests for cases with ambiguity - subpatterns, quantifiers and backreferences-----------------------------//
    function data_for_test_empty_match() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(abcd|)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_nested() {
        $test1 = array( 'str'=>'abcbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3,3=>4),
                        'length'=>array(0=>6,1=>4,2=>2,3=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>-1,3=>-1),    // the quantifier is outside subpatterns 2 and 3 so they are not matched!
                        'length'=>array(0=>2,1=>0,2=>-1,3=>-1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^a((b(c)(?:\b|\B))*)d$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_with_quant_nested() {
        $test1 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>-1),
                        'length'=>array(0=>2,1=>2,2=>-1),
                        'left'=>array(2),
                        'correctending'=>'.');

        $test2 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>-1),
                        'length'=>array(0=>1,1=>1,2=>-1),
                        'left'=>array(2),
                        'correctending'=>'.');

        return array('regex'=>'[+\-]?([0-9]+)?\.([0-9]+)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_concatenated() {
        $test1 = array( 'str'=>'_abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>3,3=>5),
                        'length'=>array(0=>6,1=>2,2=>2,3=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'[prefix] abef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>9,1=>9,2=>-1,3=>11),
                        'length'=>array(0=>4,1=>2,2=>-1,3=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(ab)(cd)?(ef)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_alternated() {
        $test1 = array( 'str'=>'abcdefgh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>-1,4=>-1),
                        'length'=>array(0=>2,1=>2,2=>2,3=>-1,4=>-1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'((ab)|(cd)|(efgh))',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_quantifier_inside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(a*)',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_quantifier_outside() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(a)*',
                     'tests'=>array($test1));
    }

    function data_for_test_subpatt_tricky() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>2),
                        'length'=>array(0=>3,1=>3,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>3,3=>4,4=>5),
                        'length'=>array(0=>6,1=>3,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(([a*]|\b)([b*]|\b)([c*]|\b))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_nested_and_concatenated() {
        $test1 = array( 'str'=>'zw',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>-1,3=>1),
                        'length'=>array(0=>2,1=>1,2=>-1,3=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'*&^%&^',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>2,1=>-1,2=>-1,3=>-1),
                        'length'=>array(0=>0,1=>-1,2=>-1,3=>-1),
                        'left'=>array(2),
                        'correctending'=>'z');

        return array('regex'=>'(z|y(x))(w)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_duplicate_simple() {
        $test1 = array( 'str'=>'cat-cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'dog-dogs',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>3,2=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?|(cat)|(dog))-\1(s)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_duplicate() {
        $test1 = array( 'str'=>'abee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>-1,4=>2),
                        'length'=>array(0=>4,1=>1,2=>1,3=>-1,4=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'acdee',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                        'length'=>array(0=>5,1=>1,2=>1,3=>1,4=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(a)(?|(b)|(c)(d))(e)\4',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_subpatt_named() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?P<name>abc)\1\g{name}',
                     'tests'=>array($test1));
    }

    function data_for_test_quant_qu() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'correctending'=>'c');

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_1() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),    // 'left' takes priority
                        'correctending'=>'ab');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?:a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_aster_2() {
        $test1 = array( 'str'=>'abcabcabcabcabcabcabcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abcabcabcabcabcabcabcabcabcab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(0),
                        'correctending'=>'');

        $test4 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?:abc)*',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_quant_plus() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'correctending'=>'b');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>101),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_brace_finite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'correctending'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>36),
                        'left'=>array(1),
                        'correctending'=>'c');

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_brace_infinite() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'correctending'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>104),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^ab{15,}c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quant_greedy() {
        $test1 = array('str'=>'abacd',
                       'results'=>array(array('is_match'=>true,    // result for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>3),
                                              'left'=>array(4),
                                              'correctending'=>'b'),
                                        array('is_match'=>true,    // result for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>5),
                                              'left'=>array(4),
                                              'correctending'=>'b')
                                        ));
        $test2 = array('str'=>'ababac',
                       'results'=>array(array('is_match'=>true,    // result for backtracking engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>3),
                                              'left'=>array(4),
                                              'correctending'=>'b'),
                                        array('is_match'=>true,    // result for fa engine
                                              'full'=>false,
                                              'index_first'=>array(0=>0),
                                              'length'=>array(0=>6),
                                              'left'=>array(1),
                                              'correctending'=>'d')
                                        ));

        return array('regex'=>'ab+[a-z]*bacd',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_characters_left_simple() {
        $test1 = array( 'str'=>'ab cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(3),
                        'correctending'=>' ');

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'correctending'=>'b');

        $test4 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>-1),
                        'length'=>array(0=>-1),
                        'left'=>array(5),
                        'correctending'=>'a');

        return array('regex'=>'ab\b cd',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_characters_left() {
        $test1 = array( 'str'=>'abefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>2,1=>-1),
                        'left'=>array(1),
                        'correctending'=>'h');

        $test3 = array('str'=>'abe',    // different strategies
                       'results'=>array(array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>3,1=>-1),
                                              'left'=>array(2),
                                              'correctending'=>'f'),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>2,1=>-1),
                                              'left'=>array(1),
                                              'correctending'=>'h')
                                        ));

        return array('regex'=>'ab(cd|efg|h)',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_simple() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>12,1=>6,2=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>6,1=>6,2=>3),
                        'left'=>array(6),
                        'correctending'=>'a');    // backref #1 not captured at all

        $test3 = array( 'str'=>'abcabcab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>8,1=>6,2=>3),
                        'left'=>array(4),
                        'correctending'=>'c');    // backref #1 captured partially

        return array('regex'=>'((abc)\2)\1',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_relative() {
        $test1 = array( 'str'=>'abcdefghidef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>12,1=>9,2=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(abc(def)ghi)\g{-1}',
                     'tests'=>array($test1));
    }

    function data_for_test_backrefs_alternated() {
        $test1 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>-1,3=>2),
                        'length'=>array(0=>4,1=>2,2=>-1,3=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1,2=>0,3=>2),
                        'length'=>array(0=>4,1=>-1,2=>2,3=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>-1,3=>-1),
                        'length'=>array(0=>3,1=>2,2=>-1,3=>-1),
                        'left'=>array(1),
                        'correctending'=>'b');

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>-1,3=>-1),
                        'length'=>array(0=>2,1=>2,2=>-1,3=>-1),
                        'left'=>array(2),
                        'correctending'=>'a');

        return array('regex'=>'(?:(ab)|(cd))(\1|\2)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_backrefs_quantified() {
        $test1 = array( 'str'=>'ababcdababcdababcdababcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>12,2=>12),
                        'length'=>array(0=>24,1=>6,2=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'cdcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>-1,2=>-1),
                        'length'=>array(0=>0,1=>-1,2=>-1),
                        'left'=>array(10000000),                    // TODO: standardize this value
                        'correctending'=>'');

        return array('regex'=>'((ab)\2cd)*\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_full_and_partial() {
        $test1 = array( 'str'=>'abcdabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abcdab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>4),
                        'left'=>array(2),
                        'correctending'=>'c');

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(4),
                        'correctending'=>'a');

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>3,1=>-1),
                        'left'=>array(5),
                        'correctending'=>'d');

        return array('regex'=>'(abcd)\1',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_backrefs_noway() {
        $test1 = array( 'str'=>'abxyabab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'abxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array('str'=>'cdxyabab',
                       'results'=>array(array('is_match'=>true,        // different strategies
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>6,1=>-1),
                                              'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                                              'correctending'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>4,1=>-1),
                                              'left'=>array(2),
                                              'correctending'=>'c')
                                        ));

        $test4 = array( 'str'=>'cdxycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>6,1=>-1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?:(ab)|cd)xy(?:ab\1|cd)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_backrefs_alt_in_subpatt_1() {
        $test1 = array( 'str'=>'Do hats eat cats?',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>3,1=>-1),
                        'left'=>array(12),
                        'correctending'=>'cbr');

        $test2 = array('str'=>'Do cats',
                       'results'=>array(array('is_match'=>true,        // different strategies
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>6),
                                              'length'=>array(0=>7,1=>1),
                                              'left'=>array(10),
                                              'correctending'=>' '),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>6),
                                              'length'=>array(0=>6,1=>0),
                                              'left'=>array(9),
                                              'correctending'=>' ')
                                        ));

        $test3 = array( 'str'=>'bat eat fat?',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>12,1=>-1),
                        'length'=>array(0=>0,1=>-1),
                        'left'=>array(10),
                        'correctending'=>'D');

        return array('regex'=>'Do (?:[cbr]at(s|)) eat (?:[cbr]at\1)\?',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_alt_in_subpatt2() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(9),
                        'correctending'=>'a');

        $test2 = array( 'str'=>'0as',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>2,1=>-1),
                        'left'=>array(8),
                        'correctending'=>'b');

        $test3 = array('str'=>'0defab',        // different strategies
                       'results'=>array(array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>4,1=>-1),
                                              'left'=>array(12),
                                              'correctending'=>'g'),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>-1),
                                              'length'=>array(0=>1,1=>-1),
                                              'left'=>array(9),
                                              'correctending'=>'a')
                                        ));

        return array('regex'=>'0(abc|defghx)[0-9]{3}\1',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_quant_in_subpatt() {
        $test1 = array( 'str'=>'0x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>-1),
                        'length'=>array(0=>1,1=>-1),
                        'left'=>array(13),
                        'correctending'=>'a');

        $test2 = array('str'=>'0aaaaaaz',
                       'results'=>array(array('is_match'=>true,        // different strategies
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>1),
                                              'length'=>array(0=>7,1=>6),
                                              'left'=>array(9),
                                              'correctending'=>'0123456789'),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>1),
                                              'length'=>array(0=>6,1=>5),
                                              'left'=>array(8),
                                              'correctending'=>'0123456789')
                                        ));

        return array('regex'=>'0(a{5,10})[0-9]{3}\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_subpatt_modifying() {
        $test1 = array('str'=>'ababba',
                       'results'=>array(array('is_match'=>true,        // different strategies
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>3),
                                              'length'=>array(0=>6,1=>3),
                                              'left'=>array(4),
                                              'correctending'=>'x'),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>0),
                                              'length'=>array(0=>1,1=>1),
                                              'left'=>array(2),
                                              'correctending'=>'x')
                                        ));

        $test2 = array( 'str'=>'ababbaxbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>10,1=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array('str'=>'abab',
                       'results'=>array(array('is_match'=>true,        // different strategies
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>1),
                                              'length'=>array(0=>4,1=>2),
                                              'left'=>array(6),
                                              'correctending'=>'b'),
                                        array('is_match'=>true,
                                              'full'=>false,
                                              'index_first'=>array(0=>0,1=>0),
                                              'length'=>array(0=>1,1=>1),
                                              'left'=>array(2),
                                              'correctending'=>'x')
                                        ));

        return array('regex'=>'(a|b\1)+x\1',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_backrefs_tricky_1() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>0),
                        'length'=>array(0=>5,1=>3,2=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>3,2=>1),
                        'length'=>array(0=>5,1=>3,2=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(x\2|(ab))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_tricky_2() {
        $test1 = array( 'str'=>'abxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'xabxab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>1),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(x\58|(ab))+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_backrefs_tricky_3() {
        $test1 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(0),
                        'correctending'=>'');

        $test2 = array( 'str'=>'ababba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(0),
                        'correctending'=>'');

        $test3 = array( 'str'=>'ababbabbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'correctending'=>'');


        $test4 = array( 'str'=>'ababbabbbabbbba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>15,1=>5),
                        'left'=>array(0),
                        'correctending'=>'');

        $test5 = array( 'str'=>'ababbabbbabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'correctending'=>'');

        $test6 = array( 'str'=>'',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>-1,1=>-1),
                        'length'=>array(0=>-1,1=>-1),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(a|b\1)+',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    //-----------------------------------------------------------------tests for acceptance-------------------------------------------------------------------//
    function data_for_test_node_assert() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'ab(?=cd)',
                     'tests'=>array($test1));
    }

    function data_for_test_node_cond_subpatt() {
        $test1 = array( 'str'=>'11-aaa-11',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'(?(?=[^a-z]*[a-z])\d{2}-[a-z]{3}-\d{2}|\d{2}-\d{2}-\d{2})',
                     'tests'=>array($test1));
    }

    function data_for_test_shortanswer_notation() {
        $test1 = array( 'str'=>'/+fghjhj4587abc*',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>16),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'^(?:/\+.*abc\*)$',
                     'tests'=>array($test1));
    }

    /*function data_for_test_leaf_assert_G() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(0),
                        'correctending'=>'');

        return array('regex'=>'a\Gb',
                     'tests'=>array($test1));
    }*/
}
?>