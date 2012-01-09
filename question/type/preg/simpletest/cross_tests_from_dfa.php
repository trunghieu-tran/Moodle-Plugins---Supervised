<?php
/**
 * Unit tests for matchers
 *
 * @copyright &copy; 2012  Dmitriy Kolesov
 * @author Dmitriy Kolesov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class qtype_preg_cross_tests_from_dfa {

    function data_for_test_easy() {
        $test1 = array( 'str'=>'fgh',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'left'=>array(4),
                        'next'=>'a');

        $test2 = array( 'str'=>'abce',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d');

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^abcd$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_alternative() {
        $test1 = array( 'str'=>'ad',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>1, 1=>-1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>0),
                        'length'=>array(0=>2, 1=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^(ab|cd)$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_iteration() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>47),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab*c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_questquant() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab?c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_metacharacter_dot() {
        $test1 = array( 'str'=>'afc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a.c$',
                     'tests'=>array($test1));
    }

    function data_for_test_negative_character_class() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'acdefghijklomnopqrstuvwxyzACDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()<>,./?~\| ');

        $test2 = array( 'str'=>'axcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^a[^b]cd$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_many_alternatives() {
        $test1 = array( 'str'=>'abi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'cdi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'efi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'ghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test5 = array( 'str'=>'yzi',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'aceg');

        return array('regex'=>'^(?:ab|cd|ef|gh)i$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_repeat_chars() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>2, 1=>-1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>3, 1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>72),
                        'length'=>array(0=>76, 1=>1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^(a|b)*abb$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_quantificator() {
        $test1 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(11),
                        'next'=>'b');

        $test2 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>36),
                        'left'=>array(1),
                        'next'=>'c');

        return array('regex'=>'^ab{15,35}c$',
                     'tests'=>array($test1, $test2, $test3));
    }
    function data_for_test_plusquant() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b');

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>101),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab+c$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_assert() {
        $test1 = array( 'str'=>'ax',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'abxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'avbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'a(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_repeat_chars_with_assert() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a');

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:a|b|c|d)*(?=abb)(?:a|c)(?:b|d)(?:b|d)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_repeat_chars_without_assert() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'ab');

        $test2 = array( 'str'=>'cdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'ab');

        $test3 = array( 'str'=>'abb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>76),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:a|b|c|d)*abb',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_start_on_assert() {
        $test1 = array( 'str'=>'x',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        $test2 = array( 'str'=>'bxcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        $test3 = array( 'str'=>'vbv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?=[xcvnm]*b)[xcvbnm]*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_loop_assert() {
        $test1 = array( 'str'=>'axcv',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'abxv',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'xcvnm');

        $test3 = array( 'str'=>'axacav',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(0),
                        'next'=>'');

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'xcvnm');

        return array('regex'=>'a(?:(?=[^b])[xcvbnm])+',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_no_anchor() {
        $test1 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'OacO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b');

        return array('regex'=>'ab',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_left_anchor() {
        $test1 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>4),
                        'left'=>array(2),
                        'next'=>'a');

        return array('regex'=>'^ab',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_right_anchor() {
        $test1 = array( 'str'=>'Oab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'OabO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'ab$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_full_anchor() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'Oab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>-1),
                        'left'=>array(2),
                        'next'=>'a');

        $test3 = array( 'str'=>'abO',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'^ab$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_digit() {
        $test1 = array( 'str'=>'273x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0, 1=>2),
                        'length'=>array(0=>4, 1=>1),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'ax',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(0=>0, 1=>-1),
                        'length'=>array(0=>-1, 1=>-1),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(\d)+x',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_wordchar() {
        $test1 = array( 'str'=>'a_a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'');

        $test2 = array( 'str'=>'a{a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'abcdefghijklomnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_');

        return array('regex'=>'a\wa',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_match_not_from_string_start() {
        $test1 = array( 'str'=>'sometextwithoutmatchingandsomeregexwithmatchig',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>26),
                        'length'=>array(0=>9),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'someregex',
                     'tests'=>array($test1));
    }

    function data_for_test_partial_match_charsets_1() {
        $test1 = array( 'str'=>'aabbbabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'[ab]*abb',
                     'tests'=>array($test1));
    }

    function data_for_test_partial_match_charsets_2() {
        $test1 = array( 'str'=>'aabbbabb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'[ab]*[ac]bb',
                     'tests'=>array($test1));
    }

    function data_for_test_partial_match_meta_meta() {
        $test1 = array( 'str'=>'@W#G%9bb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'.*\wbb',
                     'tests'=>array($test1));
    }

    function data_for_test_partial_match_charset_meta() {
        $test1 = array( 'str'=>'aaa_aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(0),
                        'next'=>'');

        return array('regex'=>'(?:\w)*a',
                     'tests'=>array($test1));
    }
}