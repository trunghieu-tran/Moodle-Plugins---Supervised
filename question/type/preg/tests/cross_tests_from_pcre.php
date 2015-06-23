<?php
/**
 * Unit tests for matchers
 *
 * @copyright &copy; 2012 Sychev Oleg, Volgograd State Technical University
 * @author Vitaliy Bondarenko, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once('cross_tester.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_pcre {

    function data_for_test_1() {
        $test2 = array( 'str'=>'The quick brown FOX',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(19),
                        'next'=>'t',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'What do you know about THE QUICK BROWN FOX?',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'left'=>array(18),
                        'next'=>'h',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'the quick brown fox',
                     'tests'=>array($test2, $test4));
    }

    function data_for_test_4() {
        $test30 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(21),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test31 = array( 'str'=>'abxyzpqrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'left'=>array(13),
                        'next'=>'r',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test32 = array( 'str'=>'abxyzpqrrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(12),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test33 = array( 'str'=>'abxyzpqrrrabxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'left'=>array(10),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test34 = array( 'str'=>'aaaabcxyzzzzpqrrrabbbxyyyyyypqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'left'=>array(4),
                        'next'=>'p',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test35 = array( 'str'=>'aaaabcxyzzzzpqrrrabbbxyyypqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'left'=>array(5),
                        'next'=>'y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test36 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqqqqqqAzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>28),
                        'left'=>array(3),
                        'next'=>'A',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*abc?xyz+pqr{3}ab{2,}xy{4,5}pq{0,6}AB{0,}zz',
                     'tests'=>array($test30, $test31, $test32, $test33, $test34, $test35, $test36));
    }

    function data_for_test_5() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'zz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abcabcabczz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(2),
                        'next'=>'z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'>>abczz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(abc){1,2}zz',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_6() {
        $test9 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[ab]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'abbbbbbbbbbbac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>12,1=>11),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+?|a){1,2}?c',
                     'tests'=>array($test9, $test10, $test11));
    }

    function data_for_test_7() {
        $test9 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[ab]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'abbbbbbbbbbbac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>12,1=>11),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+|a){1,2}c',
                     'tests'=>array($test9, $test10, $test11));
    }

    function data_for_test_9() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bababbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'babababc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b*|ba){1,2}?bc',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_10() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bababbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'babababc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(ba|b*){1,2}?bc',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_12() {
        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'fthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'[thing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'\\thing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[ab\]cde]',
                     'tests'=>array($test7, $test8, $test9, $test10));
    }

    function data_for_test_13() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'athing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'fthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[]cde]',
                     'tests'=>array($test5, $test6, $test7));
    }

    function data_for_test_14() {
        $test5 = array( 'str'=>'athing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'bthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>']thing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'cthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'dthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'ething',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^ab\]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[^ab\]cde]',
                     'tests'=>array($test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_15() {
        $test4 = array( 'str'=>']thing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'cthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'dthing',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ething',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[^]cde]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[^]cde]',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

    function data_for_test_18() {
        $test13 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[0-9]+$',
                     'tests'=>array($test13, $test14));
    }

    function data_for_test_20() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xxx',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^xxx[0-9]+$',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_21() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(3),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.+[0-9][0-9][0-9]$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_22() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(3),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'[0-9]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.+?[0-9][0-9][0-9]$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_23() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'left'=>array(23),
                        'next'=>'!',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'!pqr=apquxz.ixr.zzz.ac.uk',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(24),
                        'next'=>'[^!]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abc!=apquxz.ixr.zzz.ac.uk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>25,1=>3,2=>21),
                        'left'=>array(21),
                        'next'=>'=',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abc!pqr=apquxz:ixr.zzz.ac.uk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>28,1=>3,2=>24),
                        'left'=>array(14),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abc!pqr=apquxz.ixr.zzz.ac.ukk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>29,1=>3,2=>25),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^!]+)!(.+)=apquxz\.ixr\.zzz\.ac\.uk$',
                     'tests'=>array($test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_24() {
        $test2 = array( 'str'=>'*** Fail if we don\'t',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>':',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>':',
                     'tests'=>array($test2));
    }

    function data_for_test_25() {
        $test9 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>2,1=>2),
                        'ext_index_first'=>array(0=>4,1=>4),
                        'ext_length'=>array(0=>2,1=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'0zzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'gzzz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\da-f:]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>"fed\x20",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>3,1=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'Any old rubbish',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>10,1=>10),
                        'length'=>array(0=>2,1=>2),
                        'ext_index_first'=>array(0=>10,1=>10),
                        'ext_length'=>array(0=>2,1=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([\da-f:]+)$',
                     'modifiers'=>'i',
                     'tests'=>array($test9, $test10, $test11, $test12, $test13));
    }

    function data_for_test_26() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(6),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'.1.2.3333',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'ext_index_first'=>array(0=>0,1=>1,2=>3,3=>5),
                        'ext_length'=>array(0=>6,1=>1,2=>1,3=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'1.2.3',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'ext_index_first'=>array(0=>0,1=>2,2=>4,3=>6),
                        'ext_length'=>array(0=>7,1=>1,2=>1,3=>1),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'1234.2.3',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>5,2=>7),
                        'length'=>array(0=>8,1=>1,2=>1),
                        'ext_index_first'=>array(0=>0,1=>5,2=>7,3=>9),
                        'ext_length'=>array(0=>10,1=>1,2=>1,3=>1),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_27() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(13),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'1IN SOA non-sp1 non-sp2(',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(12),
                        'next'=>'\s',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\d+)\s+IN\s+SOA\s+(\S+)\s+(\S+)\s*\(\s*$',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_28() {
        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[a-zA-Z\d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'-abc.peq.',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[a-zA-Z\d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a-zA-Z\d][a-zA-Z\d\-]*(\.[a-zA-Z\d][a-zA-z\d\-]*)*\.$',
                     'tests'=>array($test7, $test8));
    }

    function data_for_test_29() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'*.0',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[a-z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'*.a-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'*.a-b.c-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'*.c-a.0-c',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\*\.[a-z]([a-z\-\d]*[a-z\d]+)?(\.[a-z]([a-z\-\d]*[a-z\d]+)?)*$',
                     'tests'=>array($test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_34() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'"',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'"1234" : things',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>15),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>6),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\".*\"\s*(;.*)?$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_35() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^$',
                     'tests'=>array($test2));
    }

    function data_for_test_36() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'\s',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ab cde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'   ^    a     b\sc  $ ',
                     'modifiers'=>'x',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_37() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'\s',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ab cde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?x)   ^    a     b\sc  $ ',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_38() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^   a\ b[c ]d       $',
                     'modifiers'=>'x',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_47() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(10),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'1234567',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(3),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\d{8}\w{2,}',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_48() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[aeiou\d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'123456',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[aeiou\d]{4,5}$',
                     'tests'=>array($test5, $test6));
    }

    function data_for_test_50() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(10),
                        'next'=>'[ad]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abc=defdef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A(abc|def)=(\1){2,3}\Z',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_54() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'F',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'From abcd  Sep 01 12:33:02 1997',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>11),
                        'length'=>array(0=>15,1=>4),
                        'left'=>array(11),
                        'next'=>'[a-zA-Z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^From\s+\S+\s+([a-zA-Z]{3}\s+){2}\d{1,2}\s+\d\d:\d\d',
                     'tests'=>array($test3, $test4));
    }


    function data_for_test_59() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'abc123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(\D*)(?=\d)(?!123)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_65() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!^)abc',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_66() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'the abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=^)abc',
                     'tests'=>array($test2, $test3));
    }

/*  function data_for_test_71() { //наркоманство какое-то и модификатор x не поддерживается
        $test8 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'The quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*                          # optional leading comment
(?:    (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
)                    # initial word
(?:  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
)  )* # further okay, if led by a period
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  @  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*    (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                           # initial subdomain
(?:                                  #
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.                        # if led by a period...
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                     #   ...further okay
)*
# address
|                     #  or
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
)             # one word, optionally followed by....
(?:
[^()<>@,;:".\\\[\]\x80-\xff\000-\010\012-\037]  |  # atom and space parts, or...
\(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)       |  # comments, or...

" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
# quoted strings
)*
<  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*                     # leading <
(?:  @  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*    (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                           # initial subdomain
(?:                                  #
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.                        # if led by a period...
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                     #   ...further okay
)*

(?:  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  ,  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  @  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*    (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                           # initial subdomain
(?:                                  #
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.                        # if led by a period...
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                     #   ...further okay
)*
)* # further okay, if led by comma
:                                # closing colon
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  )? #       optional route
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
)                    # initial word
(?:  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
" (?:                      # opening quote...
[^\\\x80-\xff\n\015"]                #   Anything except backslash and quote
|                     #    or
\\ [^\x80-\xff]           #   Escaped something (something != CR)
)* "  # closing quote
)  )* # further okay, if led by a period
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  @  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*    (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                           # initial subdomain
(?:                                  #
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  \.                        # if led by a period...
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*   (?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|   \[                         # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*    #    stuff
\]                        #           ]
)                     #   ...further okay
)*
#       address spec
(?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*  > #                  trailing >
# name and address
)  (?: [\040\t] |  \(
(?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  |  \( (?:  [^\\\x80-\xff\n\015()]  |  \\ [^\x80-\xff]  )* \)  )*
\)  )*                       # optional trailing comment
',
                     'modifiers'=>'x',
                     'tests'=>array($test8, $test9));
    }
*/
/*  function data_for_test_72() { //наркоманство какое-то v2.0 и модификатор x не поддерживается
        $test8 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'The quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional leading comment
(?:
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
# Atom
|                       #  or
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
# Quoted string
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
# Atom
|                       #  or
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
# Quoted string
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# additional words
)*
@
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
)*
# address
|                             #  or
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
# Atom
|                       #  or
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
# Quoted string
)
# leading word
[^()<>@,;:".\\\[\]\x80-\xff\000-\010\012-\037] *               # "normal" atoms and or spaces
(?:
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
|
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
) # "special" comment or quoted string
[^()<>@,;:".\\\[\]\x80-\xff\000-\010\012-\037] *            #  more "normal"
)*
<
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# <
(?:
@
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
)*
(?: ,
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
@
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
)*
)*  # additional domains
:
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
)?     #       optional route
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
# Atom
|                       #  or
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
# Quoted string
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
# Atom
|                       #  or
"                                     # "
[^\\\x80-\xff\n\015"] *                            #   normal
(?:  \\ [^\x80-\xff]  [^\\\x80-\xff\n\015"] * )*        #   ( special normal* )*
"                                     #        "
# Quoted string
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# additional words
)*
@
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
(?:
\.
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
(?:
[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+    # some number of atom characters...
(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]) # ..not followed by something that could be part of an atom
|
\[                            # [
(?: [^\\\x80-\xff\n\015\[\]] |  \\ [^\x80-\xff]  )*     #    stuff
\]                           #           ]
)
[\040\t]*                    # Nab whitespace.
(?:
\(                              #  (
[^\\\x80-\xff\n\015()] *                             #     normal*
(?:                                 #       (
(?:  \\ [^\x80-\xff]  |
\(                            #  (
[^\\\x80-\xff\n\015()] *                            #     normal*
(?:  \\ [^\x80-\xff]   [^\\\x80-\xff\n\015()] * )*        #     (special normal*)*
\)                           #                       )
)    #         special
[^\\\x80-\xff\n\015()] *                         #         normal*
)*                                  #            )*
\)                             #                )
[\040\t]* )*    # If comment found, allow more spaces.
# optional trailing comments
)*
#       address spec
>                    #                 >
# name and address
)
',
                     'modifiers'=>'x',
                     'tests'=>array($test8, $test9));
    }
*/
    function data_for_test_77() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'A',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"A\0Z",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'\x00',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"A\0\x0\0\x0Z",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'Z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A\x0{2,3}Z',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_78() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'cowbell',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'ext_index_first'=>array(0=>0,1=>0,2=>0),
                        'ext_length'=>array(0=>4,1=>0,2=>4),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(cow|)\1(bell)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_79() {
        $test6 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\s',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\s',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\s',
                     'tests'=>array($test6, $test7));
    }

    function data_for_test_81() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|)\1*b',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_82() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|)\1+b',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_83() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|)\1?b',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_84() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|)\1{2}b',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_85() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aaaaab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|)\1{2,3}b',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

    function data_for_test_86() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abbbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{1,3}bc',
                     'tests'=>array($test4, $test5, $test6));
    }


    function data_for_test_90() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[W-c]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'wxy',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[W-c]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[W-c]+$',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_94() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"qqq\nabc",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abc\nzzz",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"qqq\nabc\nzzz",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'tests'=>array($test2, $test3, $test4, $test5));
    }

    function data_for_test_95() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"qqq\nabc",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"abc\nzzz",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>"qqq\nabc\nzzz",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Aabc\Z',
                     'modifiers'=>'m',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_97() {
        $test2 = array( 'str'=>"abc\ndef",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A(.)*\Z',
                     'modifiers'=>'m',
                     'tests'=>array($test2));
    }

    function data_for_test_99() {
        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[-az]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[-az]+',
                     'tests'=>array($test3));
    }

    function data_for_test_100() {
        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[az-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[az-]+',
                     'tests'=>array($test3));
    }

    function data_for_test_101() {
        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a\-z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[a\-z]+',
                     'tests'=>array($test3));
    }

    function data_for_test_103() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\d-]+',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_104() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\d-z]+',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_106() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'Z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Zulu',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\x20',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\x20Z',
                     'tests'=>array($test2, $test3));
    }


    function data_for_test_112() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abc\ndef",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc$',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_129() {
        $test6 = array( 'str'=>'anything',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'[^a]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>"b" . chr(8) . "c",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(4),
                        'next'=>'[^\b]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'baccd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>4,1=>1,2=>1,3=>0),
                        'left'=>array(1),
                        'next'=>'[^d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^a])([^\b])([^c]*)([^d]{3,4})',
                     'tests'=>array($test6, $test7, $test8));
    }

    function data_for_test_134() {
        $test3 = array( 'str'=>'abk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^k]$',
                     'tests'=>array($test3));
    }

    function data_for_test_135() {
        $test5 = array( 'str'=>'abk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'akb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[^k]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'akk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[^k]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^k]{2,3}$',
                     'tests'=>array($test5, $test6, $test7));
    }

    function data_for_test_136() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(11),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'12345678@x.y.uk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>15),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>11),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'1234567@a.b.c.d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(4),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\d{8,}\@.+[^k]$',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_137() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(8),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>1),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)\1{8,}',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_144() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'1.235',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1,2=>4),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(\.\d\d((?=0)|\d(?=\d)))',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_158() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'ABC123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(\D*)(?=\d)(?!123)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_159() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[W-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Wall',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'4',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'Zebra',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[W-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'42',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[W-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'[abcd] ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[W-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>']abcd[',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[W-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[W-]46]',
                     'tests'=>array($test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_160() {
        $test9 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[W-\]46]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'-46]789',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[W-\]46]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'well',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[W-\]46]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[W-\]46]',
                     'tests'=>array($test9, $test10, $test11));
    }

    function data_for_test_162() {
        $test2 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>64),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>68),
                        'left'=>array(9),
                        'next'=>'o',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?:[a-zA-Z0-9]+ ){0,10}otherword',
                     'tests'=>array($test2));
    }

    function data_for_test_163() {
        $test1 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark the quick brown fox and the lazy dog and several other words getting close to thirty by now I hope',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>163),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>123),
                        'left'=>array(4),
                        'next'=>'w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?:[a-zA-Z0-9]+ ){0,300}otherword',
                     'tests'=>array($test1));
    }

    function data_for_test_169() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,1}',
                     'tests'=>array($test1));
    }

    function data_for_test_170() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,2}',
                     'tests'=>array($test1));
    }

    function data_for_test_171() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,3}',
                     'tests'=>array($test1));
    }

    function data_for_test_172() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,}',
                     'tests'=>array($test1));
    }

    function data_for_test_186() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abcde\nBar",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*X|^B)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_188() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abcde\nBar  ",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*X|^B)',
                     'modifiers'=>'s',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_190() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abcde\nBar  ",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s)(.*X|^B)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_191() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abcde\nBar  ",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s:.*X|^B)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_192() {
        $test1 = array( 'str'=>'**** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'left'=>array(1),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\nB",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*B',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_204() {
        $test2 = array( 'str'=>'*** Failers ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[abcdefghijklmnopqrstuvwxy0123456789]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'z ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[abcdefghijklmnopqrstuvwxy0123456789]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[abcdefghijklmnopqrstuvwxy0123456789]',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_205() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abce  ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcde{0,0}',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_206() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcde ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'e',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab[cd]{0,0}e',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_207() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab(c){0,0}d',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_208() {
        $test5 = array( 'str'=>'bbbbb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(b*)',
                     'tests'=>array($test5));
    }

    function data_for_test_209() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ab1e',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'e',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab\d{0}e',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_213() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.b',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_218() {
        $test1 = array( 'str'=>"x\nb\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!\A)x',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
    }

    function data_for_test_224() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'barfoo',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'towbarfoo',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!bar)foo',
                     'tests'=>array($test5, $test6, $test7));
    }

    function data_for_test_225() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'foo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'barfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(4),
                        'next'=>'[^r]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'towbarfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\w{3}(?<!bar)foo',
                     'tests'=>array($test2, $test3, $test4, $test5));
    }

    function data_for_test_226() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'foobbar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(foo)a)bar',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_227() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"abc\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"qqq\nabc",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"abc\nzzz",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>"qqq\nabc\nzzz",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Aabc\z',
                     'modifiers'=>'m',
                     'tests'=>array($test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_228() {
        $test1 = array( 'str'=>'/this/is/a/very/long/line/in/deed/with/very/many/slashes/in/it/you/see/',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>71),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*\/)foo',
                     'tests'=>array($test1));
    }

    function data_for_test_229() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'1.235',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>4,1=>4),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(\.\d\d[1-9]?))\d+',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_230() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'this is not a line with only words and spaces!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>45,1=>45),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((?>\w+)|(?>\s+))*$',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_232() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12345+ ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>\d+))(\w)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_239() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'\(',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'((()aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa   ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(2),
                        'next'=>'[^()]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\(((?>[^()]+)|\([^()]+\))+\)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_240() {
        $test3 = array( 'str'=>'*** Failers ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?-i)b',
                     'modifiers'=>'i',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_241() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(6),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a b cd e',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(4),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcd e   ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(6),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'a bcde ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'left'=>array(2),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a (?x)b c)d e',
                     'tests'=>array($test2, $test3, $test4, $test5));
    }

    function data_for_test_242() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(7),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcdef  ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(7),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a b(?x)c d (?-x)e f)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_243() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ABc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'ABC',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'AbC',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a(?i)b)c',
                     'tests'=>array($test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_244() {
        $test3 = array( 'str'=>'*** Failers ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABC',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?i:b)c',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_245() {
        $test3 = array( 'str'=>'*** Failers ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aBBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?i:b)*c',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_246() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'aBCd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'abcD     ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'a(?=b(?i)c)\w\wd',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_247() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(15),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'MORE THAN MILLION',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(15),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>"more \n than \n million",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>21),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>18),
                        'left'=>array(7),
                        'next'=>'[Mm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s-i:more.*than).*million',
                     'modifiers'=>'i',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_248() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(15),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'MORE THAN MILLION',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(15),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>"more \n than \n million",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>21),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>18),
                        'left'=>array(7),
                        'next'=>'[Mm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?s-i)more.*than).*million',
                     'modifiers'=>'i',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_249() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'Abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abAb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'abbC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a(?i)b+)+c',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

    function data_for_test_250() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'Ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'abC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=a(?i)b)\w\wc',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_251() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'Abxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'ABxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'abxxC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2, 1=>2),
                        'length'=>array(0=>2, 1=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a(?i)b)(\w\w)c',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_252() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'A',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'A',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bA',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(a)|b)(?(1)A|B)',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_253() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>2,1=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)?(?(1)a|b)+$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_254() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(?(?=abc)\w{3}:|\d\d)$',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_255() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(?!abc)\d\d|\w{3}:)$',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_256() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'foocat',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?<=foo)bar|cat)',
                     'tests'=>array($test5, $test6));
    }

    function data_for_test_257() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'foocat',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?<!foo)cat|bar)',
                     'tests'=>array($test5, $test6));
    }

    function data_for_test_260() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'1',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'1234',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(2)a|(1)(2))+$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_261() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'blah BLAH',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'Blah blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'left'=>array(4),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'blaH blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>4),
                        'left'=>array(1),
                        'next'=>'H',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?i)blah)\s+\1',
                     'tests'=>array($test5, $test6, $test7, $test8));
    }

    function data_for_test_276() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(8),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'sep-12-98',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>5),
                        'left'=>array(3),
                        'next'=>'-',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=[^a-z]+[a-z])  \d{2}-[a-z]{3}-\d{2}  |  \d{2}-\d{2}-\d{2} ) ',
                     'modifiers'=>'x',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_277() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'barfoo',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(foo))bar\1',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_279() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abcX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aBCX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'bbX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'BBX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a(?i)bc|BB)x',
                     'tests'=>array($test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_280() {
        $test8 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[efEF]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'Africa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[efEF]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([ab](?i)[cd]|[ef])',
                     'tests'=>array($test8, $test9));
    }

    function data_for_test_281() {
        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[Zz]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aCD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'XY',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[Zz]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(ab|a(?i)[b-c](?m-i)d|x(?i)y|z)',
                     'tests'=>array($test7, $test8, $test9));
    }

    function data_for_test_282() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>"baz\nbar",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=foo\n)^bar',
                     'modifiers'=>'m',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_283() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'baz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'r',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'foobarbaz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(?<!foo)bar)baz',
                     'tests'=>array($test4, $test5, $test6));
    }
/*The cases of aaaa and aaaaaa are missed out below because Perl does things differently. We know that odd, and maybe incorrect, things happen with recursive references in Perl, as far as 5.11.3 - see some stuff in test #2.*/
    function data_for_test_284() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),    // 4th repetition is
                        'length'=>array(0=>8,1=>3),         // incomplete at backreference
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),    // 4th repetition is
                        'length'=>array(0=>9,1=>3),         // incomplete at backreference
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'aaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'aaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'aaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'aaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?){4}$',
                     'tests'=>array($test1, $test2, $test3, $test6, $test7, $test9, $test10, $test11, $test12, $test13, $test14));
    }

    function data_for_test_285() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>3,1=>1,2=>2),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>8,1=>1,2=>2,3=>3),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>9,1=>1,2=>2,3=>3),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'aaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'aaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'aaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'aaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test16 = array( 'str'=>'aaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'ext_length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?)(a\1?)(a\2?)(a\3?)$',
                     'tests'=>array($test1, $test2, $test3, $test8, $test9, $test11, $test12, $test13, $test14, $test15, $test16));
    }
/*The following tests are taken from the Perl 5.005 test suite; some of them are compatible with 5.004, but I'd rather not have to sort them out.*/
    function data_for_test_286() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xbc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'axc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'abx',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

    function data_for_test_292() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab+bc',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_296() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(5),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{4,5}bc',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_301() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_303() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aabcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc$',
                     'tests'=>array($test2, $test4));
    }

    function data_for_test_308() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bc]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'axyzd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bc]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[bc]d',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_315() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[^bc]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^bc]d',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_317() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a]c',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[^]b]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^]b]c',
                     'tests'=>array($test2, $test4));
    }

    function data_for_test_319() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xy',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'yz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\by\b',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_320() {
        $test2 = array( 'str'=>'a-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>1),
                        'ext_length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>2),
                        'ext_length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'-a-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>2),
                        'ext_length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Ba\B',
                     'tests'=>array($test2, $test3, $test4),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_325() {
        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\W',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\W',
                     'tests'=>array($test3));
    }

    function data_for_test_327() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\S',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\Sb',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_329() {
        $test3 = array( 'str'=>'1',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\D',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\D',
                     'tests'=>array($test3));
    }

    function data_for_test_331() {
        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\W]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\W]',
                     'tests'=>array($test3));
    }

    function data_for_test_333() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[\S]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[\S]b',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_335() {
        $test3 = array( 'str'=>'1',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\D]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\D]',
                     'tests'=>array($test3));
    }

    function data_for_test_340() {
        $test1 = array( 'str'=>'a' . chr(0x08), // Originally contained \b for chr(0x08) (backspace).
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\\\\',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\\\\b',
                     'tests'=>array($test1));
    }

    function data_for_test_353() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_371() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(7),
                        'next'=>'[bcd]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(5),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'adcdcde',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'left'=>array(3),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[bcd]+dcdcde',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_376() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[jk]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'effg',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'bcdd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(bc+d$|ef*g.|h?i(j|k))',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_380() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'uh-uh',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'multiple words of text',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_387() {
        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\1',
                     'tests'=>array($test2));
    }

    function data_for_test_389() {
        $test4 = array( 'str'=>'x',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)|\1',
                     'tests'=>array($test4));
    }

    function data_for_test_394() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaxabxbaxbbx',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'XBC',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'AXC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'ABX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc',
                     'modifiers'=>'i',
                     'tests'=>array($test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_400() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab+bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_404() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(6),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(5),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{4,5}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_409() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABCC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_416() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>6),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'AXYZD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.*c',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test3));
    }

    function data_for_test_418() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[b-dB-D]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[eE]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[eE]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-d]e',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_425() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[cC]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'[^-Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'A-C',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[^-Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^-b]c',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_429() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'A]C',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'B',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test5 = array( 'str'=>'A]C',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test6 = array( 'str'=>'B',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'$b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6),
                     'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));
    }

    function data_for_test_432() {
        $test1 = array( 'str'=>'A',  // Originally was 'A\B', but pcretest converts \B to an option, so ignore it.
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'\\\\',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\\\\b',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_456() {
        $test1 = array( 'str'=>'ABCDE',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'[eE]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(ab|cd)e',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_469() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[jkJK]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ADCDCDE',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[giGI]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'EFFG',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'BCDD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(bc+d$|ef*g.|h?i(j|k))',
                     'modifiers'=>'i',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

    function data_for_test_475() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'[Mm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AA',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'[Mm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'UH-UH',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'[Mm]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'multiple words of text',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_504() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'AB',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),    // 4th repetition is
                        'length'=>array(0=>9,1=>3),         // incomplete at backreference
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?){4}$',
                     'tests'=>array($test2, $test3, $test4, $test5));
    }

    function data_for_test_505() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0,1=>6),
                        'ext_length'=>array(0=>10,1=>4),
                        'left'=>array(10),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>9,1=>3),
                        'ext_index_first'=>array(0=>0,1=>6),
                        'ext_length'=>array(0=>10,1=>4),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a(?(1)\1)){4}$',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_507() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'cb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a)b',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_518() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'cb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?i)a)b',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_520() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i:a)b',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_522() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'AB',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?-i)a)b',
                     'modifiers'=>'i',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_524() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AB',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'Ab',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?-i:a)b',
                     'modifiers'=>'i',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_526() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AB',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"a\nB",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?-i:a.))b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_535() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"B\nB",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'dbcb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<![cd])b',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_539() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'dbcb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'a--',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aa--',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a?b?)*$',
                     'tests'=>array($test5, $test6, $test7, $test8));
    }

    function data_for_test_548() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'()^b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_550() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(x)?(?(1)a|b)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_554() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'blah)',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'(blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'left'=>array(1),
                        'next'=>'\)',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\()?blah(?(1)(\)))$',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }

    function data_for_test_555() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'blah)',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'(blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'left'=>array(1),
                        'next'=>'\)',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\(+)?blah(?(1)(\)))$',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_557() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=a)b|a)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_562() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'^(?=(a+?))\1ab',
                     'tests'=>array($test1));
    }

    function data_for_test_565() {
        $test4 = array( 'str'=>'abcd:',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'ext_index_first'=>array(0=>0,2=>0),
                        'ext_length'=>array(0=>1,2=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([\w:]+::)?(\w+)$',
                     'tests'=>array($test4));
    }

    function data_for_test_573() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>5),
                        'ext_length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\Z',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_575() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b\z',
                     'tests'=>array($test2));
    }

    function data_for_test_576() {
        $test10 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(1),
                        'next'=>'[^\W_]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(1),
                        'next'=>'[^\W_]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'.a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(1),
                        'next'=>'[^\W_]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'-a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(1),
                        'next'=>'[^\W_]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'a-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'a.',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test16 = array( 'str'=>'a_b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test17 = array( 'str'=>'a.-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test18 = array( 'str'=>'a..',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test19 = array( 'str'=>'ab..bc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test20 = array( 'str'=>'the.quick.brown.fox-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test21 = array( 'str'=>'the.quick.brown.fox.',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test22 = array( 'str'=>'the.quick.brown.fox_',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test23 = array( 'str'=>'the.quick.brown.fox+',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?>(?(1)\.|())[^\W_](?>[a-z0-9-]*[^\W_])?)+$',
                     'tests'=>array($test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23));
    }

    function data_for_test_577() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'a rather long string that doesn\'t end with one of them',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?>.*)(?<=(abcd|wxyz))',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_578() {
        $test2 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>64),
                        'left'=>array(10),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?>(?:(?!otherword)[a-zA-Z0-9]+ ){0,30})otherword',
                     'tests'=>array($test2));
    }

    function data_for_test_579() {
        $test1 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark the quick brown fox and the lazy dog and several other words getting close to thirty by now I hope',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>163),
                        'left'=>array(10),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?>[a-zA-Z0-9]+ ){0,30}otherword',
                     'tests'=>array($test1));
    }

    function data_for_test_580() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=\d{3}(?!999))foo',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_581() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=(?!...999)\d{3})foo',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_582() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=\d{3}(?!999)...)foo',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_583() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=\d{3}...)(?<!999)foo',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_592() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a-\d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bcdef',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a-\d]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a-\d]',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_593() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-a]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bcdef',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\d-a]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\d-a]',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_600() {
        $test1 = array( 'str'=>"a\nxb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!^)x',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
    }

    function data_for_test_603() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(9),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(7),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'   abc\Q abc\Eabc',
                     'modifiers'=>'x',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_610() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyzabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'ext_index_first'=>array(0=>0),
                        'ext_length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Gabc',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_611() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Xa b c d Y',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?x: b c )d',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_613() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'XabcY',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'C',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i)AB(?-i)C',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_614() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>5,1=>5),
                        'ext_length'=>array(0=>2,1=>1),
                        'left'=>array(2),
                        'next'=>'[Dd]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcE',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'C',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abCe',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'E',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'dE',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'D',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'De',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'E',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?i)AB(?-i)C|D)E',
                     'tests'=>array($test3, $test4, $test5, $test6, $test7));
    }

/* This tests for an IPv6 address in the form where it can have up to eight components, one and only one of which is empty. This must be an internal component. */
    function data_for_test_618() {
        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'1:2:3:4:5:6:7:8',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'a123:bce:ddde:9999:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'a123::9999:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'abcde:2:3:4:5:6:7:8',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'::1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'abcd:fee0:123::   ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>':1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'1:  ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!:)                       # colon disallowed at start\n(?:                         # start of item\n(?: [0-9a-f]{1,4} |       # 1-4 hex digits or\n(?(1)0 | () ) )           # if null previously matched, fail; else null\n:                         # followed by colon\n){1,7}                      # end item; 1-7 of them required\n[0-9a-f]{1,4} $             # final hex number at end of string\n(?(1)|.)                    # check that there was an empty component\n',
                     'modifiers'=>'xi',
                     'tests'=>array($test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15));
    }

    function data_for_test_619() {
        $test7 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[za\-d\]]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[z\Qa-d]\E]',
                     'tests'=>array($test7));
    }

    function data_for_test_622() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>70,1=>70),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+)*b',
                     'tests'=>array($test1));
    }

    function data_for_test_627() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'left'=>array(8),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcddefg',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(7),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab cd(?x) de fg',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_628() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'boobarX',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<![^f]oo)(bar)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_629() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'onyX',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<![^f])X',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_630() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'offX',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^f])X',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_631() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'#',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'#',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?x)(?-x: \s*#\s*)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_632() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(8),
                        'next'=>'#',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'A#include',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'A #Include',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(7),
                        'next'=>'i',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?x-is)(?:(?-ixs) \s*#\s*) include',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_645() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\Eabc]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'E',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\Eabc]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\Eabc]',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_646() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'E',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a-\Ec]',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_647() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a\E\E-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a\E\E-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'E',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a\E\E-\Ec]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a\E\E-\Ec]',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_648() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\E\Qa\E-\Qz\E]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\E\Qa\E-\Qz\E]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\E\Qa\E-\Qz\E]+',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_656() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>60),
                        'length'=>array(0=>60,1=>0),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|)*\d',
                     'tests'=>array($test1));
    }

    function data_for_test_657() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>60),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a|)*\d',
                     'tests'=>array($test1));
    }

    function data_for_test_658() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>60),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:a|)*\d',
                     'tests'=>array($test1));
    }

    function data_for_test_659() {
        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?s)(?>.*)(?<!\n)',
                     'tests'=>array($test2));
    }

    function data_for_test_660() {
        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?![^\n]*\n\z)',
                     'tests'=>array($test2));
    }

    function data_for_test_661() {
        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\z(?<!\n)',
                     'tests'=>array($test2));
    }

    function data_for_test_668() {
        $test1 = array( 'str'=>'fooabcfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'left'=>array(1),
                        'next'=>'[xyz]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*[op][xyz]',
                     'tests'=>array($test1));
    }

    function data_for_test_670() {
        $test2 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?(?=^.*b)b|^)',
                     'tests'=>array($test2));
    }

    function data_for_test_676() {
        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'XABX   ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i)a(?-i)b|c',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_679() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\1',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_682() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]*?X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_683() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]+?X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_700() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a+|ab)+?c',
                     'tests'=>array($test1));
    }

    function data_for_test_701() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a+|ab)+c',
                     'tests'=>array($test1));
    }

    function data_for_test_705() {
        $test1 = array( 'str'=>'aaaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a|ab)++c',
                     'tests'=>array($test1));
    }

    function data_for_test_706() {
        $test1 = array( 'str'=>'aaaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?>a|ab)++c',
                     'tests'=>array($test1));
    }

    function data_for_test_709() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test2 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?=abc){1}xyz',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_715() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\g<a>]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'\\ga',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\g<a>]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\g<a>]+',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_720() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'xabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=a{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_721() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xaabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!a{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_723() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_724() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'aAAbc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_730() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>9,1=>3),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a{2,3}){2,}+a',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_731() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a{2,3})++a',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_732() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a{2,3})*+a',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_737() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\v',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>qtype_preg_unicode::code2utf8(0xa0) . ' X' . qtype_preg_unicode::code2utf8(0x0a),
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'\h',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\H\h\V\v',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_738() {
        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(3),
                        'next'=>'\v',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>qtype_preg_unicode::code2utf8(0x09) . qtype_preg_unicode::code2utf8(0x20) . qtype_preg_unicode::code2utf8(0xa0) . qtype_preg_unicode::code2utf8(0x0a) . qtype_preg_unicode::code2utf8(0x0b),
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'\v',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\H*\h+\V?\v{3,4}',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_741() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(4),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'>XYZ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'left'=>array(2),
                        'next'=>'Y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'>  X NY Z',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>6),
                        'left'=>array(1),
                        'next'=>'Z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\h*X\h?\H+Y\H?Z',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_748() {
        $test1 = array( 'str'=>'bacxxx',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'ext_index_first'=>array(0=>0,1=>0,2=>0),
                        'ext_length'=>array(0=>1,1=>1,2=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(^(a|b\g{-1}))',
                     'tests'=>array($test1));
    }

    function data_for_test_749() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'left'=>array(5),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xyzabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?|(abc)|(xyz))\1',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_750() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'left'=>array(5),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xyzxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?|(abc)|(xyz))(?1)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_754() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>7,1=>7),
                        'ext_index_first'=>array(0=>3,1=>3),
                        'ext_length'=>array(0=>4,1=>1),
                        'left'=>array(3),
                        'next'=>':',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a:axyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab:abxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?\'abc\'\w+):\k<abc>{2}',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_755() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>7,1=>7),
                        'ext_index_first'=>array(0=>3,1=>3),
                        'ext_length'=>array(0=>4,1=>1),
                        'left'=>array(3),
                        'next'=>':',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a:axyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ab:abxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?\'abc\'\w+):\g{abc}{2}',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_761() {
        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'10.6',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(4),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'455.3.4.5',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>5),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(DEFINE)(?<byte>2[0-4]\d|25[0-5]|1\d\d|[1-9]?\d))\b(?&byte)(\.(?&byte)){3}',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_762() {
        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'10.6',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(4),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'455.3.4.5',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>5),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\b(?&byte)(\.(?&byte)){3}(?(DEFINE)(?<byte>2[0-4]\d|25[0-5]|1\d\d|[1-9]?\d))',
                     'tests'=>array($test4, $test5, $test6));
    }

    function data_for_test_763() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'this is not a line with only words and spaces!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>45),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\w++|\s++)*$',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_764() {
        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12345+',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\d++)(\w)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_769() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'\(',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'((()aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[^()]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\(([^()]++|\([^()]+\))+\)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_770() {
        $test4 = array( 'str'=>'*** Failers)',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>11,1=>1),
                        'ext_index_first'=>array(0=>0,1=>10),
                        'ext_length'=>array(0=>11,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'a(b(c)d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^()]|\((?1)*\))*$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_772() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>11,2=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Thequickbrownfox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>16,2=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:((.)(?1)\2|)|((.)(?3)\4|.))$',
                     'modifiers'=>'i',
                     'tests'=>array($test5, $test6));
    }

    function data_for_test_773() {
        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'((2+2)*-3)-7)',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>6),
                        'length'=>array(0=>10,1=>10,2=>1),
                        'ext_index_first'=>array(0=>0,1=>0,2=>6),
                        'ext_length'=>array(0=>10,1=>10,2=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\d+|\((?1)([+*-])(?1)\)|-(?1))$',
                     'tests'=>array($test4, $test5));
    }

    function data_for_test_774() {
        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xxyzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1),
                        'ext_length'=>array(0=>3,1=>3,2=>1),
                        'left'=>array(2),
                        'next'=>'y',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xxyzxyzxyzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>1),
                        'length'=>array(0=>7,2=>6),
                        'ext_index_first'=>array(0=>0,1=>0,2=>1),
                        'ext_length'=>array(0=>8,1=>8,2=>6),
                        'left'=>array(1),
                        'next'=>'z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(x(y|(?1){2})z)',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_775() {
        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'<',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'<abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>4,1=>4,2=>4),
                        'left'=>array(1),
                        'next'=>'>',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((< (?: (?(R) \d++  | [^<>]*+) | (?2)) * >))',
                     'modifiers'=>'x',
                     'tests'=>array($test7, $test8));
    }

/*  function data_for_test_776() {
        $test1 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_777() {
        $test1 = array( 'str'=>'aaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_778() {
        $test1 = array( 'str'=>'aaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*PRUNE)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_779() {
        $test1 = array( 'str'=>'aaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*COMMIT)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_780() {
        $test1 = array( 'str'=>'aaabcccaaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*SKIP)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_783() {
        $test1 = array( 'str'=>'aaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*THEN)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_784() {
        $test5 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'AD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(A (A|B(*ACCEPT)|C) D)(E)',
                     'modifiers'=>'x',
                     'tests'=>array($test5, $test6));
    }
*/
    function data_for_test_785() {
        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,3=>0,4=>4),
                        'length'=>array(0=>11,3=>11,4=>1),
                        'left'=>array(1),
                        'next'=>'[fF]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'The quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>19,3=>19,4=>1),
                        'left'=>array(1),
                        'next'=>'[tT]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\W*+(?:((.)\W*+(?1)\W*+\2|)|((.)\W*+(?3)\W*+\4|\W*+.\W*+))\W*+$',
                     'modifiers'=>'i',
                     'tests'=>array($test5, $test6));
    }

    function data_for_test_786() {
        $test7 = array( 'str'=>'rhubarb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>7,2=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'the quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>19,2=>1),
                        'ext_index_first'=>array(0=>0,1=>0),
                        'ext_length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((.)(?1)\2|.)$',
                     'tests'=>array($test7, $test8));
    }

    function data_for_test_787() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'caz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(a)(?<=b(?1))',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_788() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FAIL_MODE_MERGE));

        return array('regex'=>'(?<=b(?1))(a)',
                     'tests'=>array($test2, $test3));
    }

    function data_for_test_790() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'[ad]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'defabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?|(abc)|(def))\1',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_791() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'[ad]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'defdef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?|(abc)|(def))(?1)',
                     'tests'=>array($test3, $test4, $test5));
    }

    function data_for_test_792() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[\'"]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"b\"11111",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,4=>1,6=>1),
                        'length'=>array(0=>2,4=>1,6=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:a(?<quote> (?<apostrophe>\')|(?<realquote>")) |b(?<quote> (?<apostrophe>\')|(?<realquote>")) ) (?(\'quote\')[a-z]+|[0-9]+)',
                     'modifiers'=>'xJ',
                     'tests'=>array($test3, $test4));
    }

/*  function data_for_test_793() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'CAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?1)|B)(A(*F)|C)',
                     'tests'=>array($test3, $test4));
    }
*/
/*  function data_for_test_794() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'BAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:(?1)|B)(A(*F)|C)',
                     'tests'=>array($test3, $test4, $test5, $test6));
    }
*/
/*  function data_for_test_795() {
        $test6 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ACX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?1)|B)(A(*ACCEPT)XX|C)D',
                     'tests'=>array($test6, $test7, $test8));
    }
*/
/*  function data_for_test_800() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*SKIP)b|ac)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_802() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*PRUNE)b)',
                     'tests'=>array($test2, $test3));
    }
*/
/* This one does fail, as expected, in Perl. It needs the complex item at the
     end of the pattern. A single letter instead of (B|D) makes it not fail,
     which I think is a Perl bug. */
/*  function data_for_test_814() {
        $test1 = array( 'str'=>'ACABX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*COMMIT)(B|D)',
                     'tests'=>array($test1));
    }
*//* Check the use of names for failure */
/*  function data_for_test_815() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*PRUNE:A)B|C(*PRUNE:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_817() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2));
    }
*/
/* COMMIT at the start of a pattern should act like an anchor. Again,
however, we need the complication for Perl. */
/*  function data_for_test_826() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>1,2=>1,3=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'DEFGABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>1,2=>1,3=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*COMMIT)(A|P)(B|P)(C|P)',
                     'tests'=>array($test1, $test2, $test3));
    }
*//* COMMIT inside an atomic group can't stop backtracking over the group. */
/*  function data_for_test_828() {
        $test1 = array( 'str'=>'abbb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\w+)b(*COMMIT)\w{2}',
                     'tests'=>array($test1));
    }
*/
/* COMMIT should override THEN */
/*  function data_for_test_830() {
        $test1 = array( 'str'=>'yes',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(*COMMIT)(?>yes|no)(*THEN)(*F))?',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_831() {
        $test1 = array( 'str'=>'yes',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(*COMMIT)(yes|no)(*THEN)(*F))?',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_833() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*SKIP)bc',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_834() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*SKIP)b',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_836() {
        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i:([^b]))(?1)',
                     'tests'=>array($test4, $test5, $test6, $test7));
    }

/*  function data_for_test_837() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>'[^Bb]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?&t)*+(?(DEFINE)(?<t>a))\w$',
                     'tests'=>array($test2, $test3));
    }
*/
    function data_for_test_839() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)*+(\w)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_840() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a)*+(\w)',
                     'tests'=>array($test3, $test4));
    }

    function data_for_test_841() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'YZ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)++(\w)',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_842() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'YZ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a)++(\w)',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_845() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'YZ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){2,}+(\w)',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_846() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'YZ',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a){2,}+(\w)',
                     'tests'=>array($test2, $test3, $test4));
    }

    function data_for_test_848() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)++(?1)b',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_849() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)*+(?1)b',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_866() {
        $test1 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)(?1)++ab',
                     'tests'=>array($test1));
    }

/* Checking revised (*THEN) handling *//* Capture */
/*  function data_for_test_878() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_881() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1,2=>1),
                        'length'=>array(0=>4,1=>2,2=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? ( (a(*THEN)b) ) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*//* Non-capture */
/*  function data_for_test_882() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_885() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?: (?:a(*THEN)b) ) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*//* Atomic */
/*  function data_for_test_886() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?>a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_889() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?> (?>a(*THEN)b) ) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*//* Possessive capture */
/*  function data_for_test_890() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b)++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_893() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1,2=>1),
                        'length'=>array(0=>4,1=>2,2=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? ( (a(*THEN)b)++ )++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*//* Possessive non-capture */
/*  function data_for_test_894() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b)++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_897() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?: (?:a(*THEN)b)++ )++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/* Condition */
/*  function data_for_test_899() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*?(?(?=a)a|b(*THEN)c)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_901() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*?(?(?=a)a(*THEN)b|c)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_907() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>1),
                        'length'=>array(0=>1,1=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xacd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>1),
                        'length'=>array(0=>1,1=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(a(*COMMIT)b))c',
                     'tests'=>array($test2, $test3));
    }
*/
/* A check on what happens after hitting a mark and them bumping along to
something that does not even start. Perl reports tags after the failures here,
though it does not when the individual letters are made into something
more complicated. */
/*  function data_for_test_923() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'XAQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'XAQQXZZ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'AXQQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'AXXQQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*:A)B|XX(*:B)Y',
                     'modifiers'=>'K',
                     'tests'=>array($test3, $test4, $test5, $test6, $test7));
    }
*/
/*  function data_for_test_924() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test3, $test4, $test5));
    }
*/
/*  function data_for_test_925() {
        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*PRUNE:A)B|C(*PRUNE:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test3, $test4, $test5));
    }
*/
/*  function data_for_test_930() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abax',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b(*:m)f|aw',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3, $test4));
    }
*/
/*  function data_for_test_934() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*MARK:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_935() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*MARK:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_936() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*PRUNE:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_937() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*PRUNE:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_938() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*THEN:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_939() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*THEN:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test2, $test3));
    }
*/
    function data_for_test_941() {
        $test1 = array( 'str'=>'hello world test',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(18),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(another)?(\1+)test',
                     'tests'=>array($test1));
    }

/*  function data_for_test_950() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a(*PRUNE)b',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_954() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>^a)b',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_957() {
        $test1 = array( 'str'=>'abcdfooxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*)foo',
                     'tests'=>array($test1));
    }

/* следующие тесты со строки 6860 в testoutput1 */
/*  function data_for_test_971() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc){3}abc',
                     'modifiers'=>'+',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_972() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc)+abc',
                     'modifiers'=>'+',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_973() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc)++abc',
                     'modifiers'=>'+',
                     'tests'=>array($test2, $test3));
    }
*/
/*  function data_for_test_977() {
        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'defabcxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc\K',
                     'modifiers'=>'+',
                     'tests'=>array($test2, $test3));
    }
*/}
