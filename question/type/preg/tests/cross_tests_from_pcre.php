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
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_cross_tests_from_pcre {

    function data_for_test_1() {
        $test1 = array( 'str'=>'the quick brown fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'The quick brown FOX',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(19),
                        'next'=>'t',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'What do you know about the quick brown fox?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>23),
                        'length'=>array(0=>19),
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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_2() {
        $test1 = array( 'str'=>'the quick brown fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'The quick brown FOX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'What do you know about the quick brown fox?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>23),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'What do you know about THE QUICK BROWN FOX?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>23),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'The quick brown fox',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_3() {
        $test1 = array( 'str'=>"abcd\t\n\r" . chr(0x0C) . chr(0x07) . chr(0x1B) . "9;\$\\?caxyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>20),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcd\t\n\r\f\a\e\071\x3b\$\\\\\?caxyz',
                     'tests'=>array($test1));
    }

    function data_for_test_4() {
        $test1 = array( 'str'=>'abxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>23),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aabxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>24),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaabxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaabxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abcxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>24),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aabcxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>28),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqqqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>29),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqqqqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypqqqqqqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>31),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'aaaabcxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'abxyzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>24),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test16 = array( 'str'=>'aabxyzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test17 = array( 'str'=>'aaabxyzzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>28),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test18 = array( 'str'=>'aaaabxyzzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>29),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test19 = array( 'str'=>'abcxyzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test20 = array( 'str'=>'aabcxyzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test21 = array( 'str'=>'aaabcxyzzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>29),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test22 = array( 'str'=>'aaaabcxyzzzzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test23 = array( 'str'=>'aaaabcxyzzzzpqrrrabbbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>31),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test24 = array( 'str'=>'aaaabcxyzzzzpqrrrabbbxyyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>32),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test25 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypABzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test26 = array( 'str'=>'aaabcxyzpqrrrabbxyyyypABBzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test27 = array( 'str'=>'>>>aaabxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test28 = array( 'str'=>'>aaaabxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test29 = array( 'str'=>'>>>>abcxyzpqrrrabbxyyyypqAzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>24),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23, $test24, $test25, $test26, $test27, $test28, $test29, $test30, $test31, $test32, $test33, $test34, $test35, $test36));
    }

    function data_for_test_5() {
        $test1 = array( 'str'=>'abczz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abcabczz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>8,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_6() {
        $test1 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'abbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>13,1=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'bbbbbbbbbbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>11),
                        'length'=>array(0=>13,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'abbbbbbbbbbbac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+?|a){1,2}?c',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_7() {
        $test1 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'abbbbbbbbbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>13,1=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'bbbbbbbbbbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>11),
                        'length'=>array(0=>13,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_8() {
        $test1 = array( 'str'=>'bbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+|a){1,2}?bc',
                     'tests'=>array($test1));
    }

    function data_for_test_9() {
        $test1 = array( 'str'=>'babc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>6,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_10() {
        $test1 = array( 'str'=>'babc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>6,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_11() {
        $test1 = array( 'str'=>"\x01\x01" . chr(0x1B) . ";z",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\ca\cA\c[\c{\c:',
                     'tests'=>array($test1));
    }

    function data_for_test_12() {
        $test1 = array( 'str'=>'athing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>']thing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'cthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'dthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'ething',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_13() {
        $test1 = array( 'str'=>']thing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'cthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'dthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ething',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_14() {
        $test1 = array( 'str'=>'fthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'[thing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'\\thing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10));
    }

    function data_for_test_15() {
        $test1 = array( 'str'=>'athing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'fthing',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_16() {
        $test1 = array( 'str'=>'Ѓ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\Ѓ',
                     'tests'=>array($test1));
    }

    function data_for_test_17() {
        $test1 = array( 'str'=>'я',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^я',
                     'tests'=>array($test1));
    }

    function data_for_test_18() {
        $test1 = array( 'str'=>'0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'2',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'3',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'5',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'6',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'7',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'8',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'9',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'10',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'100',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14));
    }

    function data_for_test_19() {
        $test1 = array( 'str'=>'enter',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'inter',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'uponter',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*nter',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_20() {
        $test1 = array( 'str'=>'xxx0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xxx1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_21() {
        $test1 = array( 'str'=>'x123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xx123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'123456',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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

        $test6 = array( 'str'=>'x1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.+[0-9][0-9][0-9]$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_22() {
        $test1 = array( 'str'=>'x123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xx123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'123456',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
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

        $test6 = array( 'str'=>'x1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.+?[0-9][0-9][0-9]$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_23() {
        $test1 = array( 'str'=>'abc!pqr=apquxz.ixr.zzz.ac.uk',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>28,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>14,1=>3,2=>3),
                        'left'=>array(14),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abc!pqr=apquxz.ixr.zzz.ac.ukk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>28,1=>3,2=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^!]+)!(.+)=apquxz\.ixr\.zzz\.ac\.uk$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_24() {
        $test1 = array( 'str'=>'Well, we need a colon: somewhere',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>21),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Fail if we don\'t',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>':',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>':',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_25() {
        $test1 = array( 'str'=>'0abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'fed',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'E',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'::',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'5f03:12C0::932e',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>15,1=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'fed def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'Any old stuff',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>11,1=>11),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'0zzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'Any old rubbish',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>10,1=>10),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([\da-f:]+)$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13));
    }

    function data_for_test_26() {
        $test1 = array( 'str'=>'.1.2.3',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3,3=>5),
                        'length'=>array(0=>6,1=>1,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'A.12.123.0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>5,3=>9),
                        'length'=>array(0=>10,1=>2,2=>3,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0,1=>1,2=>3,3=>5),
                        'length'=>array(0=>8,1=>1,2=>1,3=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'1.2.3',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'1234.2.3',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>5,2=>7),
                        'length'=>array(0=>8,1=>1,2=>1),
                        'left'=>array(2),
                        'next'=>'\.',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_27() {
        $test1 = array( 'str'=>'1 IN SOA non-sp1 non-sp2(',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>9,3=>17),
                        'length'=>array(0=>25,1=>1,2=>7,3=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1    IN    SOA    non-sp1    non-sp2   (',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>18,3=>29),
                        'length'=>array(0=>40,1=>1,2=>7,3=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_28() {
        $test1 = array( 'str'=>'a.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Z.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'2.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ab-c.pq-r.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>10,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'sxk.zzz.ac.uk.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>14,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'x-.y-.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_29() {
        $test1 = array( 'str'=>'*.a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*.b0-a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*.c3-b.c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>6),
                        'length'=>array(0=>8,1=>3,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*.c-a.b-c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>5,3=>7),
                        'length'=>array(0=>9,1=>2,2=>4,3=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'*.a-b.c-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3,2=>5),
                        'length'=>array(0=>7,1=>2,2=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'*.c-a.0-c',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>5,1=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\*\.[a-z]([a-z\-\d]*[a-z\d]+)?(\.[a-z]([a-z\-\d]*[a-z\d]+)?)*$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_30() {
        $test1 = array( 'str'=>'abde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>0,3=>3),
                        'length'=>array(0=>4,1=>2,2=>3,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=ab(de))(abd)(e)',
                     'tests'=>array($test1));
    }

    function data_for_test_31() {
        $test1 = array( 'str'=>'abdf',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0,3=>3),
                        'length'=>array(0=>4,2=>3,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!(ab)de|x)(abd)(f)',
                     'tests'=>array($test1));
    }

    function data_for_test_32() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>2,3=>0),
                        'length'=>array(0=>2,1=>4,2=>2,3=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=(ab(cd)))(ab)',
                     'tests'=>array($test1));
    }

    function data_for_test_33() {
        $test1 = array( 'str'=>'a.b.c.d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'A.B.C.D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a.b.c.1.2.3.C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>11),
                        'length'=>array(0=>13,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\da-f](\.[\da-f])*$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_34() {
        $test1 = array( 'str'=>'"1234"',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'"abcd" ;',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'"" ; rhubarb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>12,1=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>7),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\".*\"\s*(;.*)?$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_35() {
        $test1 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_36() {
        $test1 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'   ^    a     b\sc  $ ',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_37() {
        $test1 = array( 'str'=>'ab c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?x)   ^    a     b\sc  $ ',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_38() {
        $test1 = array( 'str'=>'a bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a b d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_39() {
        $test1 = array( 'str'=>'abcdefhijklm',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                        'length'=>array(0=>12,1=>3,2=>2,3=>1,4=>3,5=>2,6=>1,7=>3,8=>2,9=>1,10=>3,11=>2,12=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a(b(c)))(d(e(f)))(h(i(j)))(k(l(m)))$',
                     'tests'=>array($test1));
    }

    function data_for_test_40() {
        $test1 = array( 'str'=>'abcdefhijklm',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>2,3=>4,4=>5,5=>7,6=>8,7=>10,8=>11),
                        'length'=>array(0=>12,1=>2,2=>1,3=>2,4=>1,5=>2,6=>1,7=>2,8=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a(b(c)))(?:d(e(f)))(?:h(i(j)))(?:k(l(m)))$',
                     'tests'=>array($test1));
    }

    function data_for_test_41() {
        $test1 = array( 'str'=>"a+ Z0+\x08\n\x1d\x12",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\w][\W][\s][\S][\d][\D][\b][\n][\c]][\022]',
                     'tests'=>array($test1));
    }

    function data_for_test_42() {
        $test1 = array( 'str'=>".^\$(*+)|{?,?}",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>13),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[.^$|()*+?{,}]+',
                     'tests'=>array($test1));
    }

    function data_for_test_43() {
        $test1 = array( 'str'=>'z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'a+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aa+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a*\w',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_44() {
        $test1 = array( 'str'=>'z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'a+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aa+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a*?\w',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_45() {
        $test1 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aa+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a+\w',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_46() {
        $test1 = array( 'str'=>'az',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aa+',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a+?\w',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_47() {
        $test1 = array( 'str'=>'1234567890',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12345678ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12345678__',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_48() {
        $test1 = array( 'str'=>'uoie',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12345',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[aeiou\d]{4,5}$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_49() {
        $test1 = array( 'str'=>'uoie',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12345',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'123456',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[aeiou\d]{4,5}?',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_50() {
        $test1 = array( 'str'=>'abc=abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>10,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'def=defdefdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>10),
                        'length'=>array(0=>13,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_51() {
        $test1 = array( 'str'=>'abcdefghijkcda2',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                        'length'=>array(0=>15,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abcdefghijkkkkcda2',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>14),
                        'length'=>array(0=>18,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)\11*(\3\4)\1(?#)2$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_52() {
        $test1 = array( 'str'=>'cataract cataract23',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3,3=>4,4=>17,5=>18),
                        'length'=>array(0=>19,1=>8,2=>5,3=>4,4=>0,5=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'catatonic catatonic23',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3,3=>4,4=>19,5=>20),
                        'length'=>array(0=>21,1=>9,2=>6,3=>5,4=>0,5=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'caterpillar caterpillar23',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3,4=>23,5=>24),
                        'length'=>array(0=>25,1=>11,2=>8,4=>0,5=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(cat(a(ract|tonic)|erpillar)) \1()2(3)',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_53() {
        $test1 = array( 'str'=>'From abcd  Mon Sep 01 12:33:02 1997',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>27,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^From +([^ ]+) +[a-zA-Z][a-zA-Z][a-zA-Z] +[a-zA-Z][a-zA-Z][a-zA-Z] +[0-9]?[0-9] +[0-9][0-9]:[0-9][0-9]',
                     'tests'=>array($test1));
    }

    function data_for_test_54() {
        $test1 = array( 'str'=>'From abcd  Mon Sep 01 12:33:02 1997',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>15),
                        'length'=>array(0=>27,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'From abcd  Mon Sep  1 12:33:02 1997',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>15),
                        'length'=>array(0=>27,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_55() {
        $test1 = array( 'str'=>"12\n34",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"12\r34",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^12.34',
                     'modifiers'=>'s',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_56() {
        $test1 = array( 'str'=>"the quick brown\t fox",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>10),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\w+(?=\t)',
                     'tests'=>array($test1));
    }

    function data_for_test_57() {
        $test1 = array( 'str'=>'foobar is foolish see?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>10,1=>13),
                        'length'=>array(0=>12,1=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'foo(?!bar)(.*)',
                     'tests'=>array($test1));
    }

    function data_for_test_58() {
        $test1 = array( 'str'=>'foobar crowbar etc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8,1=>14),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'barrel',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'2barrel',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>7,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'A barrel',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>8,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?!foo)...|^.{0,2})bar(.*)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_59() {
        $test1 = array( 'str'=>'abc456',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\D*)(?=\d)(?!123)',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_60() {
        $test1 = array( 'str'=>'1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"^1234(?# test newlines\ninside)",
                     'tests'=>array($test1));
    }

    function data_for_test_61() {
        $test1 = array( 'str'=>'1234',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"^1234 #comment in extended re\n",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_62() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"#rhubarb\nabcd",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_63() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abcd#rhubarb',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_64() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>4,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)\1{2,3}(.)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_65() {
        $test1 = array( 'str'=>'the abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_66() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'the abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=^)abc',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_67() {
        $test1 = array( 'str'=>'aabbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>7,1=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[ab]{1,3}(ab*|b)',
                     'tests'=>array($test1));
    }

    function data_for_test_68() {
        $test1 = array( 'str'=>'aabbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>7,1=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[ab]{1,3}?(ab*|b)',
                     'tests'=>array($test1));
    }

    function data_for_test_69() {
        $test1 = array( 'str'=>'aabbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[ab]{1,3}?(ab*?|b)',
                     'tests'=>array($test1));
    }

    function data_for_test_70() {
        $test1 = array( 'str'=>'aabbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[ab]{1,3}(ab*?|b)',
                     'tests'=>array($test1));
    }

/*  function data_for_test_71() { //наркоманство какое-то и модификатор x не поддерживается
        $test1 = array( 'str'=>'Alan Other <user@dom.ain>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'<user@dom.ain>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'user@dom.ain',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'"A. Other" <user.1234@dom.ain> (a comment)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>42),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'A. Other <user.1234@dom.ain> (a comment)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>38),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'"/s=user/ou=host/o=place/prmd=uu.yy/admd= /c=gb/"@x400-re.lay',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>61),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'A missing angle <user@some.where',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'The quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(),
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }
*/
/*  function data_for_test_72() { //наркоманство какое-то v2.0 и модификатор x не поддерживается
        $test1 = array( 'str'=>'Alan Other <user@dom.ain>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>25),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'<user@dom.ain>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'user@dom.ain',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'"A. Other" <user.1234@dom.ain> (a comment)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>30),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'A. Other <user.1234@dom.ain> (a comment)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'"/s=user/ou=host/o=place/prmd=uu.yy/admd= /c=gb/"@x400-re.lay',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>61),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'A missing angle <user@some.where',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'The quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>17),
                        'length'=>array(0=>15),
                        'left'=>array(),
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }
*/
    function data_for_test_73() {
        $test1 = array( 'str'=>"abc\0def\00pqr\000xyz\0000AB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc456 abc\0def\00pqr\000xyz\0000ABCDE",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\0def\00pqr\000xyz\0000AB',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_74() {
        $test1 = array( 'str'=>"abc\x0def\x00pqr\x000xyz\x0000AB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>20),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc456 abc\x0def\x00pqr\x000xyz\x0000ABCDE",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>20),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\x0def\x00pqr\x000xyz\x0000AB',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_75() {
        $test1 = array( 'str'=>"\0A",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"\01B",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"\037C",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\000-\037]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_76() {
        $test1 = array( 'str'=>"\0\0\0\0",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\0*',
                     'tests'=>array($test1));
    }

    function data_for_test_77() {
        $test1 = array( 'str'=>"The A\x0\x0Z",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"An A\0\x0\0Z",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_78() {
        $test1 = array( 'str'=>'cowcowbell',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>6),
                        'length'=>array(0=>10,1=>3,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bell',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>4,1=>0,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_79() {
        $test1 = array( 'str'=>"\040abc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"\x0cabc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"\nabc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"\rabc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"\tabc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_80() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"^a   b\n  c",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_81() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_82() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_83() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_84() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_85() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_86() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_87() {
        $test1 = array( 'str'=>'track1.title:TBlah blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                        'length'=>array(0=>28,1=>6,2=>5,3=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^.]*)\.([^:]*):[T ]+(.*)',
                     'tests'=>array($test1));
    }

    function data_for_test_88() {
        $test1 = array( 'str'=>'track1.title:TBlah blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                        'length'=>array(0=>28,1=>6,2=>5,3=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^.]*)\.([^:]*):[T ]+(.*)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_89() {
        $test1 = array( 'str'=>'track1.title:TBlah blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7,3=>14),
                        'length'=>array(0=>28,1=>6,2=>5,3=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^.]*)\.([^:]*):[t ]+(.*)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_90() {
        $test1 = array( 'str'=>'WXY_^abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_91() {
        $test1 = array( 'str'=>'WXY_^abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'wxy_^ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[W-c]+$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_92() {
        $test1 = array( 'str'=>'WXY_^abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'wxy_^ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\x3f-\x5F]+$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_93() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"qqq\nabc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"abc\nzzz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"qqq\nabc\nzzz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'modifiers'=>'m',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_94() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_95() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_96() {
        $test1 = array( 'str'=>"abc\ndef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A(.)*\Z',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_97() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>11,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\ndef",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A(.)*\Z',
                     'modifiers'=>'m',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_98() {
        $test1 = array( 'str'=>'b::c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'c::b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:b)|(?::+)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_99() {
        $test1 = array( 'str'=>'az-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[-az]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[-az]+',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_100() {
        $test1 = array( 'str'=>'za-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[az-]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[az-]+',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_101() {
        $test1 = array( 'str'=>'a-z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[a\-z]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[a\-z]+',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_102() {
        $test1 = array( 'str'=>'abcdxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[a-z]+',
                     'tests'=>array($test1));
    }

    function data_for_test_103() {
        $test1 = array( 'str'=>'12-34',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_104() {
        $test1 = array( 'str'=>'12-34z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_105() {
        $test1 = array( 'str'=>'\\',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\x5c',
                     'tests'=>array($test1));
    }

    function data_for_test_106() {
        $test1 = array( 'str'=>'the Zoo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_107() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABCabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABCABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\1',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_108() {
        $test1 = array( 'str'=>'ab{3cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{3cd',
                     'tests'=>array($test1));
    }

    function data_for_test_109() {
        $test1 = array( 'str'=>'ab{3,cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{3,cd',
                     'tests'=>array($test1));
    }

    function data_for_test_110() {
        $test1 = array( 'str'=>'ab{3,4a}cd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{3,4a}cd',
                     'tests'=>array($test1));
    }

    function data_for_test_111() {
        $test1 = array( 'str'=>'{4,5a}bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'{4,5a}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_112() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_113() {
        $test1 = array( 'str'=>"abc\x53",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\123',
                     'tests'=>array($test1));
    }

    function data_for_test_114() {
        $test1 = array( 'str'=>'abc' . qtype_preg_unicode::code2utf8(0x93),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\223',
                     'tests'=>array($test1));
    }

    function data_for_test_115() {
        $test1 = array( 'str'=>'abc' . qtype_preg_unicode::code2utf8(0xd3),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\323',
                     'tests'=>array($test1));
    }

    function data_for_test_116() {
        $test1 = array( 'str'=>"abc\x40",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\100",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\100',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_117() {
        $test1 = array( 'str'=>"abc\x400",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\x40\x30",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"abc\1000",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"abc\100\x30",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>"abc\100\060",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>"abc\100\60",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\1000',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_118() {
        $test1 = array( 'str'=>"abc\081",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\0\x38\x31",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\81',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_119() {
        $test1 = array( 'str'=>"abc\091",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\0\x39\x31",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\91',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_120() {
        $test1 = array( 'str'=>'abcdefghijkllS',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10,12=>11),
                        'length'=>array(0=>14,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)(l)\12\123',
                     'tests'=>array($test1));
    }

    function data_for_test_121() {
        $test1 = array( 'str'=>"abcdefghijk\12S",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5,7=>6,8=>7,9=>8,10=>9,11=>10),
                        'length'=>array(0=>13,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)(b)(c)(d)(e)(f)(g)(h)(i)(j)(k)\12\123',
                     'tests'=>array($test1));
    }

    function data_for_test_122() {
        $test1 = array( 'str'=>'abidef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab\idef',
                     'tests'=>array($test1));
    }

    function data_for_test_123() {
        $test1 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a{0}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_124() {
        $test1 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|(bc)){0,0}?xyz',
                     'tests'=>array($test1));
    }

    function data_for_test_125() {
        $test1 = array( 'str'=>"abc\010de",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc[\10]de',
                     'tests'=>array($test1));
    }

    function data_for_test_126() {
        $test1 = array( 'str'=>"abc\1de",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc[\1]de',
                     'tests'=>array($test1));
    }

    function data_for_test_127() {
        $test1 = array( 'str'=>"abc\1de",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)[\1]de',
                     'tests'=>array($test1));
    }

    function data_for_test_128() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s)a.b',
                     'tests'=>array($test1));
    }

    function data_for_test_129() {
        $test1 = array( 'str'=>'baNOTccccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>5),
                        'length'=>array(0=>9,1=>1,2=>1,3=>3,4=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'baNOTcccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>5),
                        'length'=>array(0=>8,1=>1,2=>1,3=>3,4=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'baNOTccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>4),
                        'length'=>array(0=>7,1=>1,2=>1,3=>2,4=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bacccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>2),
                        'length'=>array(0=>5,1=>1,2=>1,3=>0,4=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>8),
                        'length'=>array(0=>11,1=>1,2=>1,3=>6,4=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'anything',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'[^a]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>"b\bc",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>4,1=>1,2=>1,3=>0),
                        'left'=>array(1),
                        'next'=>'[^\d]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_130() {
        $test1 = array( 'str'=>'Abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaAabcd ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_131() {
        $test1 = array( 'str'=>'Abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaAabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_132() {
        $test1 = array( 'str'=>'AAAaAbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"bbb\nccc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]+',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_133() {
        $test1 = array( 'str'=>'AAAaAbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]+',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_134() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>10),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^k]$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_135() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'kbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'kabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abk',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_136() {
        $test1 = array( 'str'=>'12345678@a.b.c.d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>16),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'123456789@x.y.z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>14),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_137() {
        $test1 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>10,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_138() {
        $test1 = array( 'str'=>'aaaabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaAabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^az]',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_139() {
        $test1 = array( 'str'=>'aaaabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaAabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^az]',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_140() {
        $original = '\000\001\002\003\004\005\006\007\010\011\012\013\014\015\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037\040\041\042\043\044' .
                    '\045\046\047\050\051\052\053\054\055\056\057\060\061\062\063\064\065\066\067\070\071\072\073\074\075\076\077\100\101\102\103\104\105\106\107\110\111' .
                    '\112\113\114\115\116\117\120\121\122\123\124\125\126\127\130\131\132\133\134\135\136\137\140\141\142\143\144\145\146\147\150\151\152\153\154\155\156' .
                    '\157\160\161\162\163\164\165\166\167\170\171\172\173\174\175\176\177\200\201\202\203\204\205\206\207\210\211\212\213\214\215\216\217\220\221\222\223' .
                    '\224\225\226\227\230\231\232\233\234\235\236\237\240\241\242\243\244\245\246\247\250\251\252\253\254\255\256\257\260\261\262\263\264\265\266\267\270' .
                    '\271\272\273\274\275\276\277\300\301\302\303\304\305\306\307\310\311\312\313\314\315\316\317\320\321\322\323\324\325\326\327\330\331\332\333\334\335' .
                    '\336\337\340\341\342\343\344\345\346\347\350\351\352\353\354\355\356\357\360\361\362\363\364\365\366\367\370\371\372\373\374\375\376\377';
        $codes = explode('\\', $original);
        array_shift($codes);
        $str = '';
        foreach ($codes as $code) {
            $str .= core_text::code2utf8(octdec($code));
        }
        $test1 = array( 'str'=>$str,
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>256),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>$original,
                     'tests'=>array($test1));
    }

    function data_for_test_141() {
        $test1 = array( 'str'=>'xxxxxxxxxxxPSTAIREISLLxxxxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>11),
                        'length'=>array(0=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'P[^*]TAIRE[^*]{1,6}?LL',
                     'tests'=>array($test1));
    }

    function data_for_test_142() {
        $test1 = array( 'str'=>'xxxxxxxxxxxPSTAIREISLLxxxxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>11),
                        'length'=>array(0=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'P[^*]TAIRE[^*]{1,}?LL',
                     'tests'=>array($test1));
    }

    function data_for_test_143() {
        $test1 = array( 'str'=>'1.230003938',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>10,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1.875000282',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'1.235',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\.\d\d[1-9]?)\d+',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_144() {
        $test1 = array( 'str'=>'1.230003938',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>4),
                        'length'=>array(0=>3,1=>3,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1.875000282',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>4),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>1,1=>1,2=>4),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\.\d\d((?=0)|\d(?=\d)))',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_145() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?)b',
                     'tests'=>array($test1));
    }

    function data_for_test_146() {
        $test1 = array( 'str'=>'Food is on the foo table',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>15,1=>15,2=>19),
                        'length'=>array(0=>9,1=>3,2=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\b(foo)\s+(\w+)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_147() {
        $test1 = array( 'str'=>'The food is under the bar in the barn.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>7),
                        'length'=>array(0=>32,1=>26),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'foo(.*)bar',
                     'tests'=>array($test1));
    }

    function data_for_test_148() {
        $test1 = array( 'str'=>'The food is under the bar in the barn.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>7),
                        'length'=>array(0=>21,1=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'foo(.*?)bar',
                     'tests'=>array($test1));
    }

    function data_for_test_149() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>23),
                        'length'=>array(0=>23,1=>23,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)(\d*)',
                     'tests'=>array($test1));
    }

    function data_for_test_150() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>22),
                        'length'=>array(0=>23,1=>22,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)(\d+)',
                     'tests'=>array($test1));
    }

    function data_for_test_151() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>0,1=>0,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*?)(\d*)',
                     'tests'=>array($test1));
    }

    function data_for_test_152() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>8,1=>7,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*?)(\d+)',
                     'tests'=>array($test1));
    }

    function data_for_test_153() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>22),
                        'length'=>array(0=>23,1=>22,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)(\d+)$',
                     'tests'=>array($test1));
    }

    function data_for_test_154() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>18),
                        'length'=>array(0=>23,1=>18,2=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*?)(\d+)$',
                     'tests'=>array($test1));
    }

    function data_for_test_155() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>18),
                        'length'=>array(0=>23,1=>18,2=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)\b(\d+)$',
                     'tests'=>array($test1));
    }

    function data_for_test_156() {
        $test1 = array( 'str'=>'I have 2 numbers: 53147',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>18),
                        'length'=>array(0=>23,1=>18,2=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*\D)(\d+)$',
                     'tests'=>array($test1));
    }

    function data_for_test_157() {
        $test1 = array( 'str'=>'ABC123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\D*(?!123)',
                     'tests'=>array($test1));
    }

    function data_for_test_158() {
        $test1 = array( 'str'=>'ABC445',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABC123',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\D*)(?=\d)(?!123)',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_159() {
        $test1 = array( 'str'=>'W46]789 ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'-46]789',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_160() {
        $test1 = array( 'str'=>'W46]789 ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Wall',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Zebra',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Xylophone  ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'42',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'[abcd] ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>']abcd[',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'\\backslash ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11));
    }

    function data_for_test_161() {
        $test1 = array( 'str'=>'01/01/2000',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\d\d\/\d\d\/\d\d\d\d',
                     'tests'=>array($test1));
    }

    function data_for_test_162() {
        $test1 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark otherword',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>74),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>59),
                        'left'=>array(9),
                        'next'=>'o',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?:[a-zA-Z0-9]+ ){0,10}otherword',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_163() {
        $test1 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark the quick brown fox and the lazy dog and several other words getting close to thirty by now I hope',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>159),
                        'left'=>array(9),
                        'next'=>'o',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?:[a-zA-Z0-9]+ ){0,300}otherword',
                     'tests'=>array($test1));
    }

    function data_for_test_164() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){0,0}',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_165() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){0,1}',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_166() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){0,2}',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_167() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){0,3}',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_168() {
        $test1 = array( 'str'=>'bcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){0,}',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
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

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab  ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,1}',
                     'tests'=>array($test1, $test2, $test3));
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

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,2}',
                     'tests'=>array($test1, $test2, $test3));
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

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,3}',
                     'tests'=>array($test1, $test2, $test3, $test4));
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

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a){1,}',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_173() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*\.gif',
                     'tests'=>array($test1));
    }

    function data_for_test_174() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.{0,}\.gif',
                     'tests'=>array($test1));
    }

    function data_for_test_175() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*\.gif',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
    }

    function data_for_test_176() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*\.gif',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_177() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*\.gif',
                     'modifiers'=>'ms',
                     'tests'=>array($test1));
    }

    function data_for_test_178() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>15),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'tests'=>array($test1));
    }

    function data_for_test_179() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
    }

    function data_for_test_180() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_181() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'ms',
                     'tests'=>array($test1));
    }

    function data_for_test_182() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>15),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'tests'=>array($test1));
    }

    function data_for_test_183() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
    }

    function data_for_test_184() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>18),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_185() {
        $test1 = array( 'str'=>"borfle\nbib.gif\nno\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>18),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*$',
                     'modifiers'=>'ms',
                     'tests'=>array($test1));
    }

    function data_for_test_186() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6,1=>6),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_187() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6,1=>6),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"abcde\nBar  ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6,1=>6),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*X|^B)',
                     'modifiers'=>'m',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_188() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_189() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"abcde\nBar  ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6,1=>6),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*X|^B)',
                     'modifiers'=>'ms',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_190() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>11,1=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_191() {
        $test1 = array( 'str'=>"abcde\n1234Xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BarFoo ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
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

    function data_for_test_193() {
        $test1 = array( 'str'=>"abc\nB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s)^.*B',
                     'tests'=>array($test1));
    }

    function data_for_test_194() {
        $test1 = array( 'str'=>"abc\nB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?m)^.*B',
                     'tests'=>array($test1));
    }

    function data_for_test_195() {
        $test1 = array( 'str'=>"abc\nB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?ms)^.*B',
                     'tests'=>array($test1));
    }

    function data_for_test_196() {
        $test1 = array( 'str'=>"abc\nB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?ms)^B',
                     'tests'=>array($test1));
    }

    function data_for_test_197() {
        $test1 = array( 'str'=>"B\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s)B$',
                     'tests'=>array($test1));
    }

    function data_for_test_198() {
        $test1 = array( 'str'=>'123456654321',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]',
                     'tests'=>array($test1));
    }

    function data_for_test_199() {
        $test1 = array( 'str'=>'123456654321 ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\d\d\d\d\d\d\d\d\d\d\d\d',
                     'tests'=>array($test1));
    }

    function data_for_test_200() {
        $test1 = array( 'str'=>'123456654321',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\d][\d][\d][\d][\d][\d][\d][\d][\d][\d][\d][\d]',
                     'tests'=>array($test1));
    }

    function data_for_test_201() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[abc]{12}',
                     'tests'=>array($test1));
    }

    function data_for_test_202() {
        $test1 = array( 'str'=>'abcabcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a-c]{12}',
                     'tests'=>array($test1));
    }

    function data_for_test_203() {
        $test1 = array( 'str'=>'abcabcabcabc ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>11),
                        'length'=>array(0=>12,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a|b|c){12}',
                     'tests'=>array($test1));
    }

    function data_for_test_204() {
        $test1 = array( 'str'=>'n',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_205() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_206() {
        $test1 = array( 'str'=>'abe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_207() {
        $test1 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_208() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>5,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5,1=>6),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbbbb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(b*)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_209() {
        $test1 = array( 'str'=>'abe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_210() {
        $test1 = array( 'str'=>'the "quick" brown fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>5),
                        'length'=>array(0=>7,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'"the \\"quick\\" brown fox"',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>14),
                        'length'=>array(0=>25,1=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'"([^\\\\"]+|\\\\.)*"',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_211() {
        $test1 = array( 'str'=>'<TR BGCOLOR=\'#DBE9E9\'><TD align=left valign=top>43.<a href=\'joblist.cfm?JobID=94 6735&Keyword=\'>Word Processor<BR>(N-1286)</a></TD><TD align=left valign=top>Lega lstaff.com</TD><TD align=left valign=top>CA - Statewide</TD></TR>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>25,3=>48,4=>51,5=>122,6=>122,8=>134,9=>157,10=>180,11=>203),
                        'length'=>array(0=>227,1=>18,2=>22,3=>3,4=>71,5=>0,6=>0,8=>22,9=>15,10=>22,11=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'<tr([\w\W\s\d][^<>]{0,})><TD([\w\W\s\d][^<>]{0,})>([\d]{0,}\.)(.*)((<BR>([\w\W\s\d][^<>]{0,})|[\s]{0,}))<\/a><\/TD><TD([\w\W\s\d][^<>]{0,})>([\w\W\s\d][^<>]{0,})<\/TD><TD([\w\W\s\d][^<>]{0,})>([\w\W\s\d][^<>]{0,})<\/TD><\/TR>',
                     'modifiers'=>'is',
                     'tests'=>array($test1));
    }

    function data_for_test_212() {
        $test1 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^a]b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_213() {
        $test1 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_214() {
        $test1 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^a]b',
                     'modifiers'=>'s',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_215() {
        $test1 = array( 'str'=>'acb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.b',
                     'modifiers'=>'s',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_216() {
        $test1 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bbbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbbbbac ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+?|a){1,2}?c',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_217() {
        $test1 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bbbbac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbbbbac ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(b+|a){1,2}?c',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
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

        $test2 = array( 'str'=>"a\bx\n  ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!\A)x',
                     'modifiers'=>'m',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_219() {
        $test1 = array( 'str'=>"\0{ab}",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\x0{ab}',
                     'tests'=>array($test1));
    }

    function data_for_test_220() {
        $test1 = array( 'str'=>'CD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(A|B)*?CD',
                     'tests'=>array($test1));
    }

    function data_for_test_221() {
        $test1 = array( 'str'=>'CD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(A|B)*CD',
                     'tests'=>array($test1));
    }

    function data_for_test_222() {
        $test1 = array( 'str'=>'ABABAB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(AB)*?\1',
                     'tests'=>array($test1));
    }

    function data_for_test_223() {
        $test1 = array( 'str'=>'ABABAB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>6,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(AB)*\1',
                     'tests'=>array($test1));
    }

    function data_for_test_224() {
        $test1 = array( 'str'=>'foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'catfood',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'arfootle',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'rfoosh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_225() {
        $test1 = array( 'str'=>'catfood',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_226() {
        $test1 = array( 'str'=>'fooabar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'foobbar',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>3),
                        'left'=>array(4),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(foo)a)bar',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_227() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
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

        $test2 = array( 'str'=>'/this/is/a/very/long/line/in/deed/with/very/many/slashes/in/and/foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>67),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*\/)foo',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_229() {
        $test1 = array( 'str'=>'1.230003938',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>10,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'1.875000282',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_230() {
        $test1 = array( 'str'=>'now is the time for all good men to come to the aid of the party',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>59),
                        'length'=>array(0=>64,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'this is not a line with only words and spaces!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>45,1=>45),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((?>\w+)|(?>\s+))*$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_231() {
        $test1 = array( 'str'=>'12345a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5),
                        'length'=>array(0=>6,1=>5,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12345+ ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>4),
                        'length'=>array(0=>5,1=>4,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\d+)(\w)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_232() {
        $test1 = array( 'str'=>'12345a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5),
                        'length'=>array(0=>6,1=>5,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_233() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a+)b',
                     'tests'=>array($test1));
    }

    function data_for_test_234() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a+)b)',
                     'tests'=>array($test1));
    }

    function data_for_test_235() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(a+))b',
                     'tests'=>array($test1));
    }

    function data_for_test_236() {
        $test1 = array( 'str'=>'aaabbbccc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>b)+',
                     'tests'=>array($test1));
    }

    function data_for_test_237() {
        $test1 = array( 'str'=>'aaabbbbccccd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a+|b+|c+)*c',
                     'tests'=>array($test1));
    }

    function data_for_test_238() {
        $test1 = array( 'str'=>'((abc(ade)ufh()()x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>17),
                        'length'=>array(0=>16,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>[^()]+)|\([^()]*\))+',
                     'tests'=>array($test1));
    }

    function data_for_test_239() {
        $test1 = array( 'str'=>'(abc)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'(abc(def)xyz)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>13,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_240() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_241() {
        $test1 = array( 'str'=>'a bcd e',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_242() {
        $test1 = array( 'str'=>'a bcde f',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_243() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_244() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_245() {
        $test1 = array( 'str'=>'aBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_246() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abCd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aBCd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abcD     ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(1),
                        'next'=>'d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?=b(?i)c)\w\wd',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_247() {
        $test1 = array( 'str'=>'more than million',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'more than MILLION',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"more \n than Million",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>12),
                        'left'=>array(7),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?s-i:more.*than).*million',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_248() {
        $test1 = array( 'str'=>'more than million',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'more than MILLION',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"more \n than Million",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'length'=>array(0=>12),
                        'left'=>array(7),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?s-i)more.*than).*million',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_249() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aBBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_250() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Ab',
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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(?i)b)\w\wc',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_251() {
        $test1 = array( 'str'=>'abxxc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBxxc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Abxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'ABxxc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(5),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'abxxC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(?i)b)(\w\w)c',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

/*  function data_for_test_252() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aA',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'B',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(a)|b)(?(1)A|B)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_253() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)?(?(1)a|b)+$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_254() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'abc:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(?=abc)\w{3}:|\d\d)$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_255() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'abc:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_256() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'fcat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'focat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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

        return array('regex'=>'(?(?<=foo)bar|cat)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
/*  function data_for_test_257() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'cat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'fcat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'focat',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
    function data_for_test_258() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'(abcd)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'the quick (abcd) fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'(abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'( \( )? [^()]+ (?(1) \) |) ',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_259() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'(abcd)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'the quick (abcd) fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'(abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'( \( )? [^()]+ (?(1) \) ) ',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

/*  function data_for_test_260() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>4,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(2)a|(1)(2))+$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
    function data_for_test_261() {
        $test1 = array( 'str'=>'blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BLAH BLAH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Blah Blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'blaH blaH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_262() {
        $test1 = array( 'str'=>'blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BLAH BLAH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Blah Blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'blaH blaH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'blah BLAH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Blah blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'blaH blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?i)blah)\s+(?i:\1)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_263() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a*)*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_264() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>6,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>9,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc|)+',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_265() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>5,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([a]*)*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_266() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ababab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>6,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>5,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'bbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>4,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([ab]*)*',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_267() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>4,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^a]*)*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_268() {
        $test1 = array( 'str'=>'cccc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>4,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^ab]*)*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_269() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([a]*?)*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_270() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'baba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([ab]*?)*',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_271() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^a]*?)*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_272() {
        $test1 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'cccc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'baba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^ab]*?)*',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_273() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a*)*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_274() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>5,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aabbaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>6),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a*))*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_275() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aabbaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a*?))*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_276() {
        $test1 = array( 'str'=>'12-sep-98',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12-09-98',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(8),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'sep-12-98',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>5),
                        'left'=>array(3),
                        'next'=>'-',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=[^a-z]+[a-z])  \d{2}-[a-z]{3}-\d{2}  |  \d{2}-\d{2}-\d{2} ) ',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_277() {
        $test1 = array( 'str'=>'foobarfoo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'foobarfootling',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'barfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6,1=>3),
                        'length'=>array(0=>0,1=>3),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(foo))bar\1',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_278() {
        $test1 = array( 'str'=>'saturday',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'sunday',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Saturday',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Sunday',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'SATURDAY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'SUNDAY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'SunDay',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i:saturday|sunday)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_279() {
        $test1 = array( 'str'=>'abcx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBCx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'BBx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_280() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'elephant',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'Europe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'frog',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'France',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_281() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aBd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'zebra',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Zambesi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'z',
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
                        'next'=>'z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(ab|a(?i)[b-c](?m-i)d|x(?i)y|z)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9));
    }

    function data_for_test_282() {
        $test1 = array( 'str'=>"foo\nbar",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bar',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"baz\nbar",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(7),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=foo\n)^bar',
                     'modifiers'=>'m',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_283() {
        $test1 = array( 'str'=>'barbaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'barbarbaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'koobarbaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'baz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(4),
                        'next'=>'r',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'foobarbaz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(?<!foo)bar)baz',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
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
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'aaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'aaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'aaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'aaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?){4}$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14));
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
                        'length'=>array(0=>1,1=>1,2=>1),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>1,1=>1,2=>1,3=>1),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3),
                        'length'=>array(0=>4,1=>1,2=>1,3=>1,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>4),
                        'length'=>array(0=>5,1=>1,2=>2,3=>1,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>4),
                        'length'=>array(0=>6,1=>1,2=>2,3=>1,4=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'aaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>7,1=>1,2=>2,3=>3,4=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'aaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'aaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'aaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>'aaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'aaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test16 = array( 'str'=>'aaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3,4=>6),
                        'length'=>array(0=>10,1=>1,2=>2,3=>3,4=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?)(a\1?)(a\2?)(a\3?)$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16));
    }
/*The following tests are taken from the Perl 5.005 test suite; some of them are compatible with 5.004, but I'd rather not have to sort them out.*/
    function data_for_test_286() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xabcy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ababc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_287() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*c',
                     'tests'=>array($test1));
    }

    function data_for_test_288() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*bc',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_289() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.{1}',
                     'tests'=>array($test1));
    }

    function data_for_test_290() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.{3,4}',
                     'tests'=>array($test1));
    }

    function data_for_test_291() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_292() {
        $test1 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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

        $test5 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab+bc',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_293() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{1,}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_294() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{1,3}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_295() {
        $test1 = array( 'str'=>'abbbbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{3,4}bc',
                     'tests'=>array($test1));
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

    function data_for_test_297() {
        $test1 = array( 'str'=>'abbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab?bc',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_298() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,1}bc',
                     'tests'=>array($test1));
    }

    function data_for_test_299() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab?c',
                     'tests'=>array($test1));
    }

    function data_for_test_300() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,1}c',
                     'tests'=>array($test1));
    }

    function data_for_test_301() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_302() {
        $test1 = array( 'str'=>'abcc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc',
                     'tests'=>array($test1));
    }

    function data_for_test_303() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aabcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_304() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^',
                     'tests'=>array($test1));
    }

    function data_for_test_305() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'$',
                     'tests'=>array($test1));
    }

    function data_for_test_306() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'axc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.c',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_307() {
        $test1 = array( 'str'=>'axyzc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.*c',
                     'tests'=>array($test1));
    }

    function data_for_test_308() {
        $test1 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_309() {
        $test1 = array( 'str'=>'ace',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-d]e',
                     'tests'=>array($test1));
    }

    function data_for_test_310() {
        $test1 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-d]',
                     'tests'=>array($test1));
    }

    function data_for_test_311() {
        $test1 = array( 'str'=>'a-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[-b]',
                     'tests'=>array($test1));
    }

    function data_for_test_312() {
        $test1 = array( 'str'=>'a-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-]',
                     'tests'=>array($test1));
    }

    function data_for_test_313() {
        $test1 = array( 'str'=>'a]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a]',
                     'tests'=>array($test1));
    }

    function data_for_test_314() {
        $test1 = array( 'str'=>'a]b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[]]b',
                     'tests'=>array($test1));
    }

    function data_for_test_315() {
        $test1 = array( 'str'=>'aed',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_316() {
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^-b]c',
                     'tests'=>array($test1));
    }

    function data_for_test_317() {
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'left'=>array(1),
                        'next'=>'c',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a-c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_318() {
        $test1 = array( 'str'=>'a-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'-a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-a-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\ba\b',
                     'tests'=>array($test1, $test2, $test3));
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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
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
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'-a-',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Ba\B',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_321() {
        $test1 = array( 'str'=>'xy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\By\b',
                     'tests'=>array($test1));
    }

    function data_for_test_322() {
        $test1 = array( 'str'=>'yz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\by\B',
                     'tests'=>array($test1));
    }

    function data_for_test_323() {
        $test1 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\By\B',
                     'tests'=>array($test1));
    }

    function data_for_test_324() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\w',
                     'tests'=>array($test1));
    }

    function data_for_test_325() {
        $test1 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\W',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\W',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_326() {
        $test1 = array( 'str'=>'a b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\sb',
                     'tests'=>array($test1));
    }

    function data_for_test_327() {
        $test1 = array( 'str'=>'a-b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_328() {
        $test1 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\d',
                     'tests'=>array($test1));
    }

    function data_for_test_329() {
        $test1 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'1',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'\D',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\D',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_330() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\w]',
                     'tests'=>array($test1));
    }

    function data_for_test_331() {
        $test1 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\W]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\W]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_332() {
        $test1 = array( 'str'=>'a b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[\s]b',
                     'tests'=>array($test1));
    }

    function data_for_test_333() {
        $test1 = array( 'str'=>'a-b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_334() {
        $test1 = array( 'str'=>'1',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\d]',
                     'tests'=>array($test1));
    }

    function data_for_test_335() {
        $test1 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'1',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[\D]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\D]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_336() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab|cd',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_337() {
        $test1 = array( 'str'=>'def',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'()ef',
                     'tests'=>array($test1));
    }

    function data_for_test_338() {
        $test1 = array( 'str'=>'a(b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\(b',
                     'tests'=>array($test1));
    }

    function data_for_test_339() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a((b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\(*b',
                     'tests'=>array($test1, $test2));
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

    function data_for_test_341() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((a))',
                     'tests'=>array($test1));
    }

    function data_for_test_342() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>2),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)b(c)',
                     'tests'=>array($test1));
    }

    function data_for_test_343() {
        $test1 = array( 'str'=>'aabbabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b+c',
                     'tests'=>array($test1));
    }

    function data_for_test_344() {
        $test1 = array( 'str'=>'aabbabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a{1,}b{1,}c',
                     'tests'=>array($test1));
    }

    function data_for_test_345() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.+?c',
                     'tests'=>array($test1));
    }

    function data_for_test_346() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)*',
                     'tests'=>array($test1));
    }

    function data_for_test_347() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){0,}',
                     'tests'=>array($test1));
    }

    function data_for_test_348() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)+',
                     'tests'=>array($test1));
    }

    function data_for_test_349() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){1,}',
                     'tests'=>array($test1));
    }

    function data_for_test_350() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)?',
                     'tests'=>array($test1));
    }

    function data_for_test_351() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){0,1}',
                     'tests'=>array($test1));
    }

    function data_for_test_352() {
        $test1 = array( 'str'=>'cde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^ab]*',
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

    function data_for_test_354() {
        $test1 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*',
                     'tests'=>array($test1));
    }

    function data_for_test_355() {
        $test1 = array( 'str'=>'abbbcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([abc])*d',
                     'tests'=>array($test1));
    }

    function data_for_test_356() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([abc])*bcd',
                     'tests'=>array($test1));
    }

    function data_for_test_357() {
        $test1 = array( 'str'=>'e',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a|b|c|d|e',
                     'tests'=>array($test1));
    }

    function data_for_test_358() {
        $test1 = array( 'str'=>'ef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|b|c|d|e)f',
                     'tests'=>array($test1));
    }

    function data_for_test_359() {
        $test1 = array( 'str'=>'abcdefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcd*efg',
                     'tests'=>array($test1));
    }

    function data_for_test_360() {
        $test1 = array( 'str'=>'xabyabbbz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xayabbbz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_361() {
        $test1 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|cd)e',
                     'tests'=>array($test1));
    }

    function data_for_test_362() {
        $test1 = array( 'str'=>'hij',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[abhgefdc]ij',
                     'tests'=>array($test1));
    }

    function data_for_test_363() {
        $test1 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc|)ef',
                     'tests'=>array($test1));
    }

    function data_for_test_364() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|b)c*d',
                     'tests'=>array($test1));
    }

    function data_for_test_365() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|ab*)bc',
                     'tests'=>array($test1));
    }

    function data_for_test_366() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)c*',
                     'tests'=>array($test1));
    }

    function data_for_test_367() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3),
                        'length'=>array(0=>4,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)(c*d)',
                     'tests'=>array($test1));
    }

    function data_for_test_368() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3),
                        'length'=>array(0=>4,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]+)(c*d)',
                     'tests'=>array($test1));
    }

    function data_for_test_369() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>2),
                        'length'=>array(0=>4,1=>1,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)(c+d)',
                     'tests'=>array($test1));
    }

    function data_for_test_370() {
        $test1 = array( 'str'=>'adcdcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[bcd]*dcdcde',
                     'tests'=>array($test1));
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

    function data_for_test_372() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|a)b*c',
                     'tests'=>array($test1));
    }

    function data_for_test_373() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((a)(b)c)(d)',
                     'tests'=>array($test1));
    }

    function data_for_test_374() {
        $test1 = array( 'str'=>'alpha',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[a-zA-Z_][a-zA-Z0-9_]*',
                     'tests'=>array($test1));
    }

    function data_for_test_375() {
        $test1 = array( 'str'=>'abh',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a(bc+|b[eh])g|.h$',
                     'tests'=>array($test1));
    }

    function data_for_test_376() {
        $test1 = array( 'str'=>'effgz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ij',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'reffgz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(bc+d$|ef*g.|h?i(j|k))',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_377() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                        'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((((((((((a))))))))))',
                     'tests'=>array($test1));
    }

    function data_for_test_378() {
        $test1 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                        'length'=>array(0=>2,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((((((((((a))))))))))\10',
                     'tests'=>array($test1));
    }

    function data_for_test_379() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0),
                        'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(((((((((a)))))))))',
                     'tests'=>array($test1));
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

    function data_for_test_381() {
        $test1 = array( 'str'=>'multiple words, yeah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'multiple words',
                     'tests'=>array($test1));
    }

    function data_for_test_382() {
        $test1 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>5,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)c(.*)',
                     'tests'=>array($test1));
    }

    function data_for_test_383() {
        $test1 = array( 'str'=>'(a, b)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>4),
                        'length'=>array(0=>6,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\((.*), (.*)\)',
                     'tests'=>array($test1));
    }

    function data_for_test_384() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcd',
                     'tests'=>array($test1));
    }

    function data_for_test_385() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(bc)d',
                     'tests'=>array($test1));
    }

    function data_for_test_386() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[-]?c',
                     'tests'=>array($test1));
    }

    function data_for_test_387() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc)\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_388() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([a-c]*)\1',
                     'tests'=>array($test1));
    }

    function data_for_test_389() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'x',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)|\1',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_390() {
        $test1 = array( 'str'=>'ababbbcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>3),
                        'length'=>array(0=>5,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(([a-c])b*?\2)*',
                     'tests'=>array($test1));
    }

    function data_for_test_391() {
        $test1 = array( 'str'=>'ababbbcbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6,2=>6),
                        'length'=>array(0=>9,1=>3,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(([a-c])b*?\2){3}',
                     'tests'=>array($test1));
    }

    function data_for_test_392() {
        $test1 = array( 'str'=>'aaaxabaxbaaxbbax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>12,1=>12,2=>12,3=>14),
                        'length'=>array(0=>4,1=>4,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((\3|b)\2(a)x)+',
                     'tests'=>array($test1));
    }

    function data_for_test_393() {
        $test1 = array( 'str'=>'bbaababbabaaaaabbaaaabba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>15,1=>21,2=>21,3=>23),
                        'length'=>array(0=>9,1=>3,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((\3|b)\2(a)){2,}',
                     'tests'=>array($test1));
    }

    function data_for_test_394() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XABCY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
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
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_395() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_396() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_397() {
        $test1 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_398() {
        $test1 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_399() {
        $test1 = array( 'str'=>'ABBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab+?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_400() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(2),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab+bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_401() {
        $test1 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{1,}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_402() {
        $test1 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{1,3}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_403() {
        $test1 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{3,4}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_404() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(6),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(5),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ABBBBC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(2),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{4,5}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_405() {
        $test1 = array( 'str'=>'ABBC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab??bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_406() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,1}?bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_407() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab??c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_408() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab{0,1}?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_409() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_410() {
        $test1 = array( 'str'=>'ABCC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_411() {
        $test1 = array( 'str'=>'AABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc$',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_412() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_413() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'$',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_414() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AXC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.c',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_415() {
        $test1 = array( 'str'=>'AXYZC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.*?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
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

        $test2 = array( 'str'=>'AABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_417() {
        $test1 = array( 'str'=>'ABD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[bc]d',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_418() {
        $test1 = array( 'str'=>'ACE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_419() {
        $test1 = array( 'str'=>'AAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-d]',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_420() {
        $test1 = array( 'str'=>'A-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[-b]',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_421() {
        $test1 = array( 'str'=>'A-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[b-]',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_422() {
        $test1 = array( 'str'=>'A]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a]',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_423() {
        $test1 = array( 'str'=>'A]B',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[]]b',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_424() {
        $test1 = array( 'str'=>'AED',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^bc]d',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_425() {
        $test1 = array( 'str'=>'ADC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'left'=>array(2),
                        'next'=>'[^-bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'A-C',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[^-bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^-b]c',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_426() {
        $test1 = array( 'str'=>'ADC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[^]b]c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_427() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab|cd',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_428() {
        $test1 = array( 'str'=>'DEF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'()ef',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
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

        return array('regex'=>'$b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_430() {
        $test1 = array( 'str'=>'A(B',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\(b',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_431() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'A((B',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\(*b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
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

    function data_for_test_433() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((a))',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_434() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>2),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)b(c)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_435() {
        $test1 = array( 'str'=>'AABBABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b+c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_436() {
        $test1 = array( 'str'=>'AABBABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a{1,}b{1,}c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_437() {
        $test1 = array( 'str'=>'ABCABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.+?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_438() {
        $test1 = array( 'str'=>'ABCABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.*?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_439() {
        $test1 = array( 'str'=>'ABCABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a.{0,5}?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_440() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)*',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_441() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){0,}',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_442() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)+',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_443() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){1,}',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_444() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b)?',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_445() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){0,1}',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_446() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a+|b){0,1}?',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_447() {
        $test1 = array( 'str'=>'CDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^ab]*',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_448() {
        $test1 = array( 'str'=>'ABBBCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([abc])*d',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_449() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([abc])*bcd',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_450() {
        $test1 = array( 'str'=>'E',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a|b|c|d|e',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_451() {
        $test1 = array( 'str'=>'EF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|b|c|d|e)f',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_452() {
        $test1 = array( 'str'=>'ABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcd*efg',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_453() {
        $test1 = array( 'str'=>'XABYABBBZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XAYABBBZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab*',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_454() {
        $test1 = array( 'str'=>'ABCDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|cd)e',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_455() {
        $test1 = array( 'str'=>'HIJ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[abhgefdc]ij',
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

    function data_for_test_457() {
        $test1 = array( 'str'=>'ABCDEF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(abc|)ef',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_458() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|b)c*d',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_459() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|ab*)bc',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_460() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)c*',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_461() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3),
                        'length'=>array(0=>4,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)(c*d)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_462() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>3),
                        'length'=>array(0=>4,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]+)(c*d)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_463() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>2),
                        'length'=>array(0=>4,1=>1,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a([bc]*)(c+d)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_464() {
        $test1 = array( 'str'=>'ADCDCDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[bcd]*dcdcde',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_465() {
        $test1 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab|a)b*c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_466() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>1,4=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((a)(b)c)(d)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_467() {
        $test1 = array( 'str'=>'ALPHA',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[a-zA-Z_][a-zA-Z0-9_]*',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_468() {
        $test1 = array( 'str'=>'ABH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a(bc+|b[eh])g|.h$',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_469() {
        $test1 = array( 'str'=>'EFFGZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'IJ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'REFFGZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>'[gG]',
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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(bc+d$|ef*g.|h?i(j|k))',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_470() {
        $test1 = array( 'str'=>'A',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                        'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((((((((((a))))))))))',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_471() {
        $test1 = array( 'str'=>'AA',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0),
                        'length'=>array(0=>2,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((((((((((a))))))))))\10',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_472() {
        $test1 = array( 'str'=>'A',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0),
                        'length'=>array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(((((((((a)))))))))',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_473() {
        $test1 = array( 'str'=>'A',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?:(?:(?:(?:(?:(?:(?:(?:(a))))))))))',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_474() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?:(?:(?:(?:(?:(?:(?:(?:(a|b|c))))))))))',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_475() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AA',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'UH-UH',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(22),
                        'next'=>'m',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'multiple words of text',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_476() {
        $test1 = array( 'str'=>'MULTIPLE WORDS, YEAH',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>14),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'multiple words',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_477() {
        $test1 = array( 'str'=>'ABCDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>5,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)c(.*)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_478() {
        $test1 = array( 'str'=>'(A, B)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>4),
                        'length'=>array(0=>6,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\((.*), (.*)\)',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_479() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abcd',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_480() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(bc)d',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_481() {
        $test1 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[-]?c',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_482() {
        $test1 = array( 'str'=>'ABCABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([a-c]*)\1',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_483() {
        $test1 = array( 'str'=>'abad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?!b).',
                     'tests'=>array($test1));
    }

    function data_for_test_484() {
        $test1 = array( 'str'=>'abad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?=d).',
                     'tests'=>array($test1));
    }

    function data_for_test_485() {
        $test1 = array( 'str'=>'abad',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?=c|d).',
                     'tests'=>array($test1));
    }

    function data_for_test_486() {
        $test1 = array( 'str'=>'ace',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d)(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_487() {
        $test1 = array( 'str'=>'ace',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d)*(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_488() {
        $test1 = array( 'str'=>'ace',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d)+?(.)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_489() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d)+(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_490() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){2}(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_491() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){4,5}(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_492() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>6,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){4,5}?(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_493() {
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>0,3=>3),
                        'length'=>array(0=>6,1=>3,2=>3,3=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((foo)|(bar))*',
                     'tests'=>array($test1));
    }

    function data_for_test_494() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){6,7}(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_495() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){6,7}?(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_496() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){5,6}(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_497() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){5,6}?(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_498() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){5,7}(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_499() {
        $test1 = array( 'str'=>'acdbcdbe',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|c|d){5,7}?(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_500() {
        $test1 = array( 'str'=>'ace',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>2),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(?:b|(c|e){1,2}?|d)+?(.)',
                     'tests'=>array($test1));
    }

    function data_for_test_501() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(.+)?B',
                     'tests'=>array($test1));
    }

    function data_for_test_502() {
        $test1 = array( 'str'=>'.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^a-z])|(\^)$',
                     'tests'=>array($test1));
    }

    function data_for_test_503() {
        $test1 = array( 'str'=>'<&OUT',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[<>]&',
                     'tests'=>array($test1));
    }

    function data_for_test_504() {
        $test1 = array( 'str'=>'aaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>7,1=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a\1?){4}$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

/*  function data_for_test_505() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(9),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>9,1=>3),
                        'left'=>array(1),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a(?(1)\1)){4}$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
*/
    function data_for_test_506() {
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2,4=>3,5=>4,6=>5),
                        'length'=>array(0=>6,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(f)(o)(o)|(b)(a)(r))*',
                     'tests'=>array($test1));
    }

    function data_for_test_507() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'cb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a)b',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_508() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!c)b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_509() {
        $test1 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:..)*a',
                     'tests'=>array($test1));
    }

    function data_for_test_510() {
        $test1 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:..)*?a',
                     'tests'=>array($test1));
    }

    function data_for_test_511() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:b|a(?=(.)))*\1',
                     'tests'=>array($test1));
    }

    function data_for_test_512() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(){3,5}',
                     'tests'=>array($test1));
    }

    function data_for_test_513() {
        $test1 = array( 'str'=>'aax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a+)*ax',
                     'tests'=>array($test1));
    }

    function data_for_test_514() {
        $test1 = array( 'str'=>'aax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((a|b)+)*ax',
                     'tests'=>array($test1));
    }

    function data_for_test_515() {
        $test1 = array( 'str'=>'aax',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>3,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((a|bc)+)*ax',
                     'tests'=>array($test1));
    }

    function data_for_test_516() {
        $test1 = array( 'str'=>'cab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|x)*ab',
                     'tests'=>array($test1));
    }

    function data_for_test_517() {
        $test1 = array( 'str'=>'cab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)*ab',
                     'tests'=>array($test1));
    }

    function data_for_test_518() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_519() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?i)a)b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_520() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_521() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?i:a))b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_522() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_523() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?-i)a)b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_524() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_525() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?-i:a))b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_526() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>2,1=>2),
                        'left'=>array(1),
                        'next'=>'[bB]',
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

    function data_for_test_527() {
        $test1 = array( 'str'=>"a\nB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?s-i:a.))b',
                     'modifiers'=>'i',
                     'tests'=>array($test1));
    }

    function data_for_test_528() {
        $test1 = array( 'str'=>'cabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:c|d)(?:)(?:a(?:)(?:b)(?:b(?:))(?:b(?:)(?:b)))',
                     'tests'=>array($test1));
    }

    function data_for_test_529() {
        $test1 = array( 'str'=>'caaaaaaaabbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>41),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:c|d)(?:)(?:aaaaaaaa(?:)(?:bbbbbbbb)(?:bbbbbbbb(?:))(?:bbbbbbbb(?:)(?:bbbbbbbb)))',
                     'tests'=>array($test1));
    }

    function data_for_test_530() {
        $test1 = array( 'str'=>'Ab4ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab4Ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(ab)\d\1',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_531() {
        $test1 = array( 'str'=>'foobar1234baz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>13),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'foo\w*\d{4}baz',
                     'tests'=>array($test1));
    }

    function data_for_test_532() {
        $test1 = array( 'str'=>'x~~',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'x(~~)*(?:(?:F)?)?',
                     'tests'=>array($test1));
    }

    function data_for_test_533() {
        $test1 = array( 'str'=>'aaac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a(?#xxx){3}c',
                     'tests'=>array($test1));
    }

    function data_for_test_534() {
        $test1 = array( 'str'=>'aaac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a (?#xxx) (?#yyy) {3}c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
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

    function data_for_test_536() {
        $test1 = array( 'str'=>'dbaacb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<![cd])[ab]',
                     'tests'=>array($test1));
    }

    function data_for_test_537() {
        $test1 = array( 'str'=>'dbaacb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!(c|d))[ab]',
                     'tests'=>array($test1));
    }

    function data_for_test_538() {
        $test1 = array( 'str'=>'cdaccb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!cd)[ab]',
                     'tests'=>array($test1));
    }

    function data_for_test_539() {
        $test1 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'aaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'dbcb',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'a--',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'aa--',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a?b?)*$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_540() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>2,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?s)^a(.))((?m)^b$)',
                     'tests'=>array($test1));
    }

    function data_for_test_541() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?m)^b$)',
                     'tests'=>array($test1));
    }

    function data_for_test_542() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?m)^b',
                     'tests'=>array($test1));
    }

    function data_for_test_543() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?m)^(b)',
                     'tests'=>array($test1));
    }

    function data_for_test_544() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?m)^b)',
                     'tests'=>array($test1));
    }

    function data_for_test_545() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>2),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\n((?m)^b)',
                     'tests'=>array($test1));
    }

    function data_for_test_546() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?s).)c(?!.)',
                     'tests'=>array($test1));
    }

    function data_for_test_547() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?s)b.)c(?!.)',
                     'tests'=>array($test1));
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

    function data_for_test_549() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>2),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?m)^b)',
                     'tests'=>array($test1));
    }

/*  function data_for_test_550() { //условные подмаски не поддерживаются
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
*/
/*  function data_for_test_551() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(x)?(?(1)b|a)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_552() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'()?(?(1)b|a)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_553() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'()?(?(1)a|b)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_554() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'(blah)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5),
                        'length'=>array(0=>6,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'(blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>')',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\()?blah(?(1)(\)))$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
/*  function data_for_test_555() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'(blah)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5),
                        'length'=>array(0=>6,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'blah',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'(blah',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'left'=>array(1),
                        'next'=>')',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\(+)?blah(?(1)(\)))$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_556() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?!a)b|a)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_557() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=a)b|a)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_558() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=a)a|b)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_559() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>1),
                        'length'=>array(0=>3,1=>1,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(a+?))(\1ab)',
                     'tests'=>array($test1));
    }

    function data_for_test_560() {
        $test1 = array( 'str'=>'one:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\w+:)+',
                     'tests'=>array($test1));
    }

    function data_for_test_561() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'$(?<=^(a))',
                     'tests'=>array($test1));
    }

    function data_for_test_562() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=(a+?))\1ab',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_563() {
        $test1 = array( 'str'=>'aexycd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[^bcd]*(c+)',
                     'tests'=>array($test1));
    }

    function data_for_test_564() {
        $test1 = array( 'str'=>'caab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>3,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a*)b+',
                     'tests'=>array($test1));
    }

    function data_for_test_565() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>4,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xy:z:::abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>11,1=>7,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,2=>4),
                        'length'=>array(0=>7,2=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcd:',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'ext_index_first'=>array(0=>0,2=>0),
                        'ext_length'=>array(0=>1,2=>1),
                        'left'=>array(1),
                        'next'=>'\w',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([\w:]+::)?(\w+)$',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_566() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a+)b',
                     'tests'=>array($test1));
    }

    function data_for_test_567() {
        $test1 = array( 'str'=>'a:[b]:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([[:]+)',
                     'tests'=>array($test1));
    }

    function data_for_test_568() {
        $test1 = array( 'str'=>'a=[b]=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([[=]+)',
                     'tests'=>array($test1));
    }

    function data_for_test_569() {
        $test1 = array( 'str'=>'a.[b].',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([[.]+)',
                     'tests'=>array($test1));
    }

    function data_for_test_570() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a+)b)',
                     'tests'=>array($test1));
    }

    function data_for_test_571() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(a+))b',
                     'tests'=>array($test1));
    }

    function data_for_test_572() {
        $test1 = array( 'str'=>'((abc(ade)ufh()()x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>17),
                        'length'=>array(0=>16,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>[^()]+)|\([^()]*\))+',
                     'tests'=>array($test1));
    }

    function data_for_test_573() {
        $test1 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a\Z',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_574() {
        $test1 = array( 'str'=>"a\nb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b\Z',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_575() {
        $test1 = array( 'str'=>"a\nb",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b\z',
                     'tests'=>array($test1, $test2));
    }

/*  function data_for_test_576() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a-b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'0-9',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'a.b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'5.6.7',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'the.quick.brown.fox',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'a100.b200.300c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>14,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'12-ab.1245',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>10,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'a.',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test16 = array( 'str'=>'a_b',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test17 = array( 'str'=>'a.-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test18 = array( 'str'=>'a..',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test19 = array( 'str'=>'ab..bc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test20 = array( 'str'=>'the.quick.brown.fox-',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test21 = array( 'str'=>'the.quick.brown.fox.',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test22 = array( 'str'=>'the.quick.brown.fox_',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test23 = array( 'str'=>'the.quick.brown.fox+',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>19,1=>0),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?>(?(1)\.|())[^\W_](?>[a-z0-9-]*[^\W_])?)+$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15, $test16, $test17, $test18, $test19, $test20, $test21, $test22, $test23));
    }
*/
/*  function data_for_test_577() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'alphabetabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>8),
                        'length'=>array(0=>12,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'endingwxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>10,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a rather long string that doesn\'t end with one of them',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(11),
                        'next'=>'l',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*)(?<=(abcd|wxyz))',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
*/
/*  function data_for_test_578() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark otherword',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>74),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'word cat dog elephant mussel cow horse canary baboon snake shark',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>64),
                        'left'=>array(10),
                        'next'=>' ',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'word (?>(?:(?!otherword)[a-zA-Z0-9]+ ){0,30})otherword',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_579() { //однократные подмаски не поддерживаются
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
*/
    function data_for_test_580() {
        $test1 = array( 'str'=>'999foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=\d{3}(?!999))foo',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_581() {
        $test1 = array( 'str'=>'999foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(?!...999)\d{3})foo',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_582() {
        $test1 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'123456foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=\d{3}(?!999)...)foo',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_583() {
        $test1 = array( 'str'=>'123abcfoo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'123456foo',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(9),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'123999foo',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>7),
                        'length'=>array(0=>0),
                        'left'=>array(3),
                        'next'=>'f',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=\d{3}...)(?<!999)foo',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_584() {
        $test1 = array( 'str'=>'<a href=abcd xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>8),
                        'length'=>array(0=>12,3=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"<a href=\"abcd xyz pqr\" cats",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>21,2=>9),
                        'length'=>array(0=>22,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'<a href=\'abcd xyz pqr\' cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>21,2=>9),
                        'length'=>array(0=>22,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"<a[\s]+href[\s]*=[\s]*          # find <a href=\n([\\\"\'])?                       # find single or double quote\n(?(1) (.*?)\\1 | ([^\s]+))       # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n",
                     'modifiers'=>'isx',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_585() {
        $test1 = array( 'str'=>'<a href=abcd xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>8),
                        'length'=>array(0=>12,3=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"<a href=\"abcd xyz pqr\" cats",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>21,2=>9),
                        'length'=>array(0=>22,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'<a href       =       \'abcd xyz pqr\' cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>35,2=>23),
                        'length'=>array(0=>36,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"<a\s+href\s*=\s*                # find <a href=\n ([\"'])?                         # find single or double quote\n (?(1) (.*?)\\1 | (\S+))          # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n",
                     'modifiers'=>'isx',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_586() {
        $test1 = array( 'str'=>'<a href=abcd xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>8),
                        'length'=>array(0=>12,3=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"<a href=\"abcd xyz pqr\" cats",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>21,2=>9),
                        'length'=>array(0=>22,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'<a href       =       \'abcd xyz pqr\' cats',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>35,2=>23),
                        'length'=>array(0=>36,1=>1,2=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'<a\s+href(?>\s*)=(?>\s*)        # find <a href=\n(["\'])?                         # find single or double quote\n(?(1) (.*?)\1 | (\S+))          # if quote found, match up to next matching\n                                 # quote, otherwise match up to next space\n',
                     'modifiers'=>'isx',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_587() {
        $test1 = array( 'str'=>'ZABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>0),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((Z)+|A)*',
                     'tests'=>array($test1));
    }

    function data_for_test_588() {
        $test1 = array( 'str'=>'ZABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1),
                        'length'=>array(0=>2,1=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(Z()|A)*',
                     'tests'=>array($test1));
    }

    function data_for_test_589() {
        $test1 = array( 'str'=>'ZABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1,3=>1),
                        'length'=>array(0=>2,1=>1,2=>0,3=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(Z(())|A)*',
                     'tests'=>array($test1));
    }

    function data_for_test_590() {
        $test1 = array( 'str'=>'ZABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>Z)+|A)*',
                     'tests'=>array($test1));
    }

    function data_for_test_591() {
        $test1 = array( 'str'=>'ZABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>)+|A)*',
                     'tests'=>array($test1));
    }

    function data_for_test_592() {
        $test1 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'-things',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'0digit',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_593() {
        $test1 = array( 'str'=>'abcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'-things',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'0digit',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_594() {
        $test1 = array( 'str'=>"> \x09\x0a\x0c\x0d\x0b<",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[[:space:]]+',
                     'tests'=>array($test1));
    }

    function data_for_test_595() {
        $test1 = array( 'str'=>"> \x09\x0a\x0c\x0d\x0b<",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[[:blank:]]+',
                     'tests'=>array($test1));
    }

    function data_for_test_596() {
        $test1 = array( 'str'=>"> \x09\x0a\x0c\x0d\x0b<",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\s]+',
                     'tests'=>array($test1));
    }

    function data_for_test_597() {
        $test1 = array( 'str'=>"> \x09\x0a\x0c\x0d\x0b<",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\s+',
                     'tests'=>array($test1));
    }

    function data_for_test_598() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_599() {
        $test1 = array( 'str'=>"a\nxb\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!\A)x',
                     'modifiers'=>'m',
                     'tests'=>array($test1));
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

    function data_for_test_601() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\Qabc\Eabc',
                     'tests'=>array($test1));
    }

    function data_for_test_602() {
        $test1 = array( 'str'=>'abc(*+|abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\Q(*+|\Eabc',
                     'tests'=>array($test1));
    }

    function data_for_test_603() {
        $test1 = array( 'str'=>'abc abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_604() {
        $test1 = array( 'str'=>"abc#not comment\n    literal",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"abc#comment\n    \Q#not comment\n    literal\E",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_605() {
        $test1 = array( 'str'=>"abc#not comment\n    literal",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"abc#comment\n    \Q#not comment\n    literal",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_606() {
        $test1 = array( 'str'=>"abc#not comment\n    literal",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"abc#comment\n    \Q#not comment\n    literal\E #more comment\n    ",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_607() {
        $test1 = array( 'str'=>"abc#not comment\n    literal",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>27),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"abc#comment\n    \Q#not comment\n    literal\E #more comment",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_608() {
        $test1 = array( 'str'=>"abc\\\$xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Qabc\$xyz\E',
                     'tests'=>array($test1));
    }

    function data_for_test_609() {
        $test1 = array( 'str'=>"abc\$xyz",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Qabc\E\$\Qxyz\E',
                     'tests'=>array($test1));
    }

    function data_for_test_610() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyzabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Gabc',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_611() {
        $test1 = array( 'str'=>'XabcdY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_612() {
        $test1 = array( 'str'=>'XabcY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AxyzB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?x)x y z | a b c)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_613() {
        $test1 = array( 'str'=>'XabCY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(2),
                        'next'=>'[bB]',
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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_614() {
        $test1 = array( 'str'=>'abCE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'DE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'left'=>array(3),
                        'next'=>'[Bb]',
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_615() {
        $test1 = array( 'str'=>'abc123abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc123bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)\d+\1',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_616() {
        $test1 = array( 'str'=>'abc123abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc123bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*)\d+\1',
                     'modifiers'=>'s',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_617() {
        $test1 = array( 'str'=>'abc123abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>9,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc123bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1,2=>1),
                        'length'=>array(0=>7,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((.*))\d+\1',
                     'tests'=>array($test1, $test2));
    }
/* This tests for an IPv6 address in the form where it can have up to eight components, one and only one of which is empty. This must be an internal component. */
/*  function data_for_test_618() {
        $test1 = array( 'str'=>'a123::a123',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>10,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a123:b342::abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>14),
                        'length'=>array(0=>15,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a123:b342::324e:abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>19),
                        'length'=>array(0=>20,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'a123:ddde:b342::324e:abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>24),
                        'length'=>array(0=>25,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'a123:ddde:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>29),
                        'length'=>array(0=>30,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'a123:ddde:9999:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'1:2:3:4:5:6:7:8',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test9 = array( 'str'=>'a123:bce:ddde:9999:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test10 = array( 'str'=>'a123::9999:b342::324e:dcba:abcd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test11 = array( 'str'=>'abcde:2:3:4:5:6:7:8',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test12 = array( 'str'=>'::1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test13 = array( 'str'=>'abcd:fee0:123::   ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test14 = array( 'str'=>':1',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test15 = array( 'str'=>'1:  ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>34),
                        'length'=>array(0=>35,1=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!:)                       # colon disallowed at start\n(?:                         # start of item\n(?: [0-9a-f]{1,4} |       # 1-4 hex digits or\n(?(1)0 | () ) )           # if null previously matched, fail; else null\n:                         # followed by colon\n){1,7}                      # end item; 1-7 of them required\n[0-9a-f]{1,4} $             # final hex number at end of string\n(?(1)|.)                    # check that there was an empty component\n',
                     'modifiers'=>'xi',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10, $test11, $test12, $test13, $test14, $test15));
    }
*/
    function data_for_test_619() {
        $test1 = array( 'str'=>'z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>']',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'b',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[za\-d\]]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[z\Qa-d]\E]',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

    function data_for_test_620() {
        $test1 = array( 'str'=>'z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\z\C]',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_621() {
        $test1 = array( 'str'=>'M',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\M',
                     'tests'=>array($test1));
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

    function data_for_test_623() {
        $test1 = array( 'str'=>'REGular',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'regulaer',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'Regex',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'regulдr',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i)reg(?:ul(?:[aд]|ae)r|ex)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_624() {
        $test1 = array( 'str'=>'Ежеда',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Ежедя',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ЕжедА',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ЕжедЯ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'Ежед[а-яА-Я]+',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_625() {
        $test1 = array( 'str'=>"\x84XAZXB",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=Z)X.',
                     'tests'=>array($test1));
    }

    function data_for_test_626() {
        $test1 = array( 'str'=>'ab cd defg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab cd (?x) de fg',
                     'tests'=>array($test1));
    }

    function data_for_test_627() {
        $test1 = array( 'str'=>'ab cddefg',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_628() {
        $test1 = array( 'str'=>'foobarX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_629() {
        $test1 = array( 'str'=>'offX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_630() {
        $test1 = array( 'str'=>'onyX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'offX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'X',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=[^f])X',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_631() {
        $test1 = array( 'str'=>'A # B',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_632() {
        $test1 = array( 'str'=>'A #include',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_633() {
        $test1 = array( 'str'=>'aaabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*b*\w',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_634() {
        $test1 = array( 'str'=>'aaabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*b?\w',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_635() {
        $test1 = array( 'str'=>'aaabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*b{0,4}\w',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_636() {
        $test1 = array( 'str'=>'aaabbbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*b{0,}\w',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_637() {
        $test1 = array( 'str'=>'0a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*\d*\w',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_638() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*b *\w',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_639() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>"a*b#comment\n*\w",
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_640() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a* b *\w',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_641() {
        $test1 = array( 'str'=>"abc=xyz\\\npqr",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\w+=.*(\\\n.*)*',
                     'tests'=>array($test1));
    }

    function data_for_test_642() {
        $test1 = array( 'str'=>'abcd:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(\w+))\1:',
                     'tests'=>array($test1));
    }

    function data_for_test_643() {
        $test1 = array( 'str'=>'abcd:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=(\w+))\1:',
                     'tests'=>array($test1));
    }

    function data_for_test_644() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\Eabc',
                     'tests'=>array($test1));
    }

    function data_for_test_645() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_646() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_647() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_648() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_649() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>']',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a\Q]bc\E]',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_650() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'-',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[a-\Q\E]',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_651() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>4),
                        'length'=>array(0=>4,1=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a()*)*',
                     'tests'=>array($test1));
    }

    function data_for_test_652() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a(?:(?:))*)*',
                     'tests'=>array($test1));
    }

    function data_for_test_653() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>4),
                        'length'=>array(0=>4,1=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a()+)+',
                     'tests'=>array($test1));
    }

    function data_for_test_654() {
        $test1 = array( 'str'=>'aaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a(?:(?:))+)+',
                     'tests'=>array($test1));
    }

/*  function data_for_test_655() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'abbD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ccccD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>4),
                        'length'=>array(0=>5,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>1,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a){0,3}(?(1)b|(c|))*D',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
    function data_for_test_656() {
        $test1 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>60),
                        'length'=>array(0=>60,1=>0),
                        'left'=>array(1),
                        'next'=>'\d',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>60),
                        'length'=>array(0=>61,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|)*\d',
                     'tests'=>array($test1, $test2));
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

        $test2 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>61),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a|)*\d',
                     'tests'=>array($test1, $test2));
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

        $test2 = array( 'str'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>61),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:a|)*\d',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_659() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?s)(?>.*)(?<!\n)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_660() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?![^\n]*\n\z)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_661() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"abc\n",
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\z(?<!\n)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_662() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>4,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(.*(.)?)*',
                     'tests'=>array($test1));
    }

    function data_for_test_663() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>0,1=>0,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'( (A | (?(1)0|) )*   )',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_664() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>0,1=>0,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'( ( (?(1)0|) )*   )',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_665() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>0,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(  (?(1)0|)*   )',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

    function data_for_test_666() {
        $test1 = array( 'str'=>'a]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>':]',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[[:abcd:xyz]]',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_667() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'[',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>':',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>']',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'p',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[abc[:x\]pqr]',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
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

/*  function data_for_test_669() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=.*b)b|^)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_670() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=^.*b)b|^)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_671() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=.*b)b|^)*',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_672() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'adc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=.*b)b|^)+',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_673() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=b).*b|^d)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_674() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=.*b).*b|^d)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_675() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'%ab%',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>4,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^%((?(?=[a])[^%])|b)*%$',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_676() {
        $test1 = array( 'str'=>'XabX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XAbX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'CcC ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_677() {
        $test1 = array( 'str'=>"\x0a\x0b\x0c\x0d",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[\x00-\xff\s]+',
                     'tests'=>array($test1));
    }

    function data_for_test_678() {
        $test1 = array( 'str'=>'?',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\c',
                     'tests'=>array($test1));
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

    function data_for_test_680() {
        $test1 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]*',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_681() {
        $test1 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]*+',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_682() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'12ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'[xX]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]+?X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_684() {
        $test1 = array( 'str'=>'12aXbcX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12AXBCX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'BCX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]?X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_685() {
        $test1 = array( 'str'=>'12aXbcX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12AXBCX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'BCX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]??X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_686() {
        $test1 = array( 'str'=>'12aXbcX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'12AXBCX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'BCX ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]?+X',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_687() {
        $test1 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABCDEF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]{2,3}',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_688() {
        $test1 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABCDEF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]{2,3}?',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_689() {
        $test1 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABCDEF',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[^a]{2,3}+',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_690() {
        $test1 = array( 'str'=>'Z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>0,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((a|)+)+Z',
                     'tests'=>array($test1));
    }

    function data_for_test_691() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)b|(a)c',
                     'tests'=>array($test1));
    }

    function data_for_test_692() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(a))b|(a)c',
                     'tests'=>array($test1));
    }

    function data_for_test_693() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(a))ab|(a)c',
                     'tests'=>array($test1));
    }

    function data_for_test_694() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,3=>0),
                        'length'=>array(0=>2,1=>2,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>(a))b|(a)c)',
                     'tests'=>array($test1));
    }

    function data_for_test_695() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,3=>0),
                        'length'=>array(0=>2,1=>2,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>(a))b|(a)c)++',
                     'tests'=>array($test1));
    }

    function data_for_test_696() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?>(a))b|(a)c)++',
                     'tests'=>array($test1));
    }

/*  function data_for_test_697() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0,3=>0),
                        'length'=>array(0=>2,2=>1,3=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(?>(a))b|(a)c)(..)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_698() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(?>(a))b|(a)c)',
                     'tests'=>array($test1));
    }

    function data_for_test_699() {
        $test1 = array( 'str'=>'aaaabaaabaabab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5,3=>9),
                        'length'=>array(0=>14,1=>14,2=>3,3=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>(a+)b)+(aabab))',
                     'tests'=>array($test1));
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

    function data_for_test_702() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:a+|ab)+c',
                     'tests'=>array($test1));
    }

/*  function data_for_test_703() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=(a))a)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_704() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=(a))a)(b)',
                     'tests'=>array($test1));
    }
*/
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

    function data_for_test_707() {
        $test1 = array( 'str'=>'aaaabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a|ab)+c',
                     'tests'=>array($test1));
    }

    function data_for_test_708() {
        $test1 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc){0}xyz',
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
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xyz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(6),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc){1}xyz',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_710() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(a))?.',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_711() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=(a))??.',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_712() {
        $test1 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'zcdxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=(?1))?[az]([abc])d',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_713() {
        $test1 = array( 'str'=>'aaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!a){0}\w+',
                     'tests'=>array($test1));
    }

    function data_for_test_714() {
        $test1 = array( 'str'=>'abcxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'pqrxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(abc))?xyz',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_715() {
        $test1 = array( 'str'=>'ggg<<<aaa>>>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>12),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_716() {
        $test1 = array( 'str'=>'gggagagaxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[\ga]+',
                     'tests'=>array($test1));
    }

    function data_for_test_717() {
        $test1 = array( 'str'=>'aaaa444:::Z',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[:a[:digit:]]+',
                     'tests'=>array($test1));
    }

    function data_for_test_718() {
        $test1 = array( 'str'=>'aaaa444:::bbbZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>13),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^[:a[:digit:]:b]+',
                     'tests'=>array($test1));
    }

    function data_for_test_719() {
        $test1 = array( 'str'=>' :xxx:',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'[:a]xxx[b:]',
                     'tests'=>array($test1));
    }

    function data_for_test_720() {
        $test1 = array( 'str'=>'xaabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(3),
                        'next'=>'[aA]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_721() {
        $test1 = array( 'str'=>'xabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xaabc',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(1),
                        'next'=>'[bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!a{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_722() {
        $test1 = array( 'str'=>'xa c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a\h)c',
                     'tests'=>array($test1));
    }

    function data_for_test_723() {
        $test1 = array( 'str'=>'axxbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aAAbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_724() {
        $test1 = array( 'str'=>'axxbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aAAbc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'xaabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(1),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=[^a]{2})b',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_725() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a\H)c',
                     'tests'=>array($test1));
    }

    function data_for_test_726() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a\V)c',
                     'tests'=>array($test1));
    }

    function data_for_test_727() {
        $test1 = array( 'str'=>"a\nc",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a\v)c',
                     'tests'=>array($test1));
    }

/*  function data_for_test_728() { //условные подмаски и захватывающие квантификаторы не поддерживаются
        $test1 = array( 'str'=>'XcccddYX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=c)c|d)++Y',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_729() { //условные подмаски и захватывающие квантификаторы не поддерживаются
        $test1 = array( 'str'=>'XcccddYX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=c)c|d)*+Y',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_730() {
        $test1 = array( 'str'=>'aaaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3),
                        'length'=>array(0=>7,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
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

    function data_for_test_733() {
        $test1 = array( 'str'=>'abXde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab\Cde',
                     'tests'=>array($test1));
    }

    function data_for_test_734() {
        $test1 = array( 'str'=>'abZdeX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=ab\Cde)X',
                     'tests'=>array($test1));
    }

    function data_for_test_735() {
        $test1 = array( 'str'=>'aCb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aDb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[\CD]b',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_736() {
        $test1 = array( 'str'=>'aJb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a[\C-X]b',
                     'tests'=>array($test1));
    }

    function data_for_test_737() {
        $test1 = array( 'str'=>'X X' . qtype_preg_unicode::code2utf8(0x0a),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'X' . qtype_preg_unicode::code2utf8(0x09) . 'X' . qtype_preg_unicode::code2utf8(0x0b),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_738() {
        $test1 = array( 'str'=>qtype_preg_unicode::code2utf8(0x09) . qtype_preg_unicode::code2utf8(0x20) . qtype_preg_unicode::code2utf8(0xa0) . 'X' . qtype_preg_unicode::code2utf8(0x0a) . qtype_preg_unicode::code2utf8(0x0b) . qtype_preg_unicode::code2utf8(0x0c) . qtype_preg_unicode::code2utf8(0x0d) . qtype_preg_unicode::code2utf8(0x0a),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>qtype_preg_unicode::code2utf8(0x09) . qtype_preg_unicode::code2utf8(0x20) . qtype_preg_unicode::code2utf8(0xa0) . qtype_preg_unicode::code2utf8(0x0a) . qtype_preg_unicode::code2utf8(0x0b) . qtype_preg_unicode::code2utf8(0x0c) . qtype_preg_unicode::code2utf8(0x0d) . qtype_preg_unicode::code2utf8(0x0a),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>qtype_preg_unicode::code2utf8(0x09) . qtype_preg_unicode::code2utf8(0x20) . qtype_preg_unicode::code2utf8(0xa0) . qtype_preg_unicode::code2utf8(0x0a) . qtype_preg_unicode::code2utf8(0x0b) . qtype_preg_unicode::code2utf8(0x0c),
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_739() {
        $test1 = array( 'str'=>'XY  ABCDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XY  PQR ST',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\H{3,4}',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_740() {
        $test1 = array( 'str'=>'XY  AB    PQRS',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.\h{3,4}.',
                     'tests'=>array($test1));
    }

    function data_for_test_741() {
        $test1 = array( 'str'=>'>XNNNYZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'>  X NYQZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_742() {
        $test1 = array( 'str'=>">XY\x0aZ\x0aA\x0bNN\x0c",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>">\x0a\x0dX\x0aY\x0a\x0bZZZ\x0aAAA\x0bNNN\x0c",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>19),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\v*X\v?Y\v+Z\V*\x0a\V+\x0b\V{2,3}\x0c',
                     'tests'=>array($test1, $test2));
    }

/*  function data_for_test_743() { //\K не поддерживается
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(foo)\Kbar',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_744() { //\K не поддерживается
        $test1 = array( 'str'=>'foobar',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0,2=>3),
                        'length'=>array(0=>3,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'foobaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>6,1=>3,2=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(foo)(\Kbar|baz)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_745() { //\K не поддерживается
        $test1 = array( 'str'=>'foobarbaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>0),
                        'length'=>array(0=>6,1=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(foo\Kbar)baz',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_746() {
        $test1 = array( 'str'=>'ababababbbabZXXXX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>13,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a(b))\1\g1\g{1}\g-1\g{-1}\g{-02}Z',
                     'tests'=>array($test1));
    }

    function data_for_test_747() {
        $test1 = array( 'str'=>'tom-tom',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'bon-bon',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<A>tom|bon)-\g{A}',
                     'tests'=>array($test1, $test2));
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
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xyzxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_750() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xyzabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>4,1=>4),
                        'length'=>array(0=>1,1=>1),
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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_751() {
        $test1 = array( 'str'=>'XYabcdY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>3,3=>4,4=>5,5=>6),
                        'length'=>array(0=>7,1=>1,2=>1,3=>1,4=>1,5=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^X(?5)(a)(?|(b)|(q))(c)(d)(Y)',
                     'tests'=>array($test1));
    }

    function data_for_test_752() {
        $test1 = array( 'str'=>'XYabcdY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>3,5=>4,6=>5,7=>6),
                        'length'=>array(0=>7,1=>1,2=>1,5=>1,6=>1,7=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^X(?7)(a)(?|(b|(r)(s))|(q))(c)(d)(Y)',
                     'tests'=>array($test1));
    }

    function data_for_test_753() {
        $test1 = array( 'str'=>'XYabcdY',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2,2=>3,5=>4,6=>5,7=>6),
                        'length'=>array(0=>7,1=>1,2=>1,5=>1,6=>1,7=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^X(?7)(a)(?|(b|(?|(r)|(t))(s))|(q))(c)(d)(Y)',
                     'tests'=>array($test1));
    }

    function data_for_test_754() {
        $test1 = array( 'str'=>'a:aaxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab:ababxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>1,1=>1),
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_755() {
        $test1 = array( 'str'=>'a:aaxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab:ababxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>1,1=>1),
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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_756() {
        $test1 = array( 'str'=>'abd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ce',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?<ab>a)? (?(<ab>)b|c) (?(\'ab\')d|e)',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_757() {
        $test1 = array( 'str'=>'aXaXZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a.)\g-1Z',
                     'tests'=>array($test1));
    }

    function data_for_test_758() {
        $test1 = array( 'str'=>'aXaXZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a.)\g{-1}Z',
                     'tests'=>array($test1));
    }

/*  function data_for_test_759() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(DEFINE) (?<A> a) (?<B> b) )  (?&A) (?&B) ',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_760() {
        $test1 = array( 'str'=>'metcalfe 33',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>9),
                        'length'=>array(0=>11,1=>8,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<NAME>(?&NAME_PAT))\s+(?<ADDR>(?&ADDRESS_PAT))\n(?(DEFINE)\n(?<NAME_PAT>[a-z]+)\n(?<ADDRESS_PAT>\d+)\n)',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_761() {
        $test1 = array( 'str'=>'1.2.3.4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>5),
                        'length'=>array(0=>7,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'131.111.10.206',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>10),
                        'length'=>array(0=>14,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'10.0.0.0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>6),
                        'length'=>array(0=>8,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>6),
                        'length'=>array(0=>8,2=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'10.6',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>6),
                        'length'=>array(0=>8,2=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'455.3.4.5',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,2=>6),
                        'length'=>array(0=>8,2=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(DEFINE)(?<byte>2[0-4]\d|25[0-5]|1\d\d|[1-9]?\d))\b(?&byte)(\.(?&byte)){3}',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
/*  function data_for_test_762() {
        $test1 = array( 'str'=>'1.2.3.4',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>7,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'131.111.10.206',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>10),
                        'length'=>array(0=>14,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'10.0.0.0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>8,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'10.6',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'455.3.4.5',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>6),
                        'length'=>array(0=>8,1=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\b(?&byte)(\.(?&byte)){3}(?(DEFINE)(?<byte>2[0-4]\d|25[0-5]|1\d\d|[1-9]?\d))',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
    function data_for_test_763() {
        $test1 = array( 'str'=>'now is the time for all good men to come to the aid of the party',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>59),
                        'length'=>array(0=>64,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'*** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'this is not a line with only words and spaces!',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>45),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\w++|\s++)*$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_764() {
        $test1 = array( 'str'=>'12345a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>5),
                        'length'=>array(0=>6,1=>5,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_765() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a++b',
                     'tests'=>array($test1));
    }

    function data_for_test_766() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a++b)',
                     'tests'=>array($test1));
    }

    function data_for_test_767() {
        $test1 = array( 'str'=>'aaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a++)b',
                     'tests'=>array($test1));
    }

    function data_for_test_768() {
        $test1 = array( 'str'=>'((abc(ade)ufh()()x',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>17),
                        'length'=>array(0=>16,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'([^()]++|\([^()]*\))+',
                     'tests'=>array($test1));
    }

    function data_for_test_769() {
        $test1 = array( 'str'=>'(abc)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>5,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'(abc(def)xyz)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>13,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_770() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a(b)c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'a(b(c))d',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>8,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'*** Failers)',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>11),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'a(b(c)d',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>')',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^()]|\((?1)*\))*$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_771() {
        $test1 = array( 'str'=>'>abc>123<xyz<',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>7),
                        'length'=>array(0=>13,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'>abc>1(2)3<xyz<',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>9),
                        'length'=>array(0=>15,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'>abc>(1(2)3)<xyz<',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>5),
                        'length'=>array(0=>17,1=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^>abc>([^()]|\((?1)*\))*<xyz<$',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_772() {
        $test1 = array( 'str'=>'1221',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Satanoscillatemymetallicsonatas',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>31,3=>31,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'AmanaplanacanalPanama',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>21,3=>21,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AblewasIereIsawElba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>19,3=>19,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'*** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Thequickbrownfox',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:((.)(?1)\2|)|((.)(?3)\4|.))$',
                     'modifiers'=>'i',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_773() {
        $test1 = array( 'str'=>'12',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'(((2+2)*-3)-7)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>11),
                        'length'=>array(0=>14,1=>14,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'-12',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>10),
                        'left'=>array(0),
                        'next'=>qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(\d+|\((?1)([+*-])(?1)\)|-(?1))$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_774() {
        $test1 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>3,1=>3,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'xxyzxyzz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>8,1=>8,2=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'left'=>array(4),
                        'next'=>'x',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'xxyzxyzxyzz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>'z',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(x(y|(?1){2})z)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_775() {
        $test1 = array( 'str'=>'<>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'<abcd>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>6,1=>6,2=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'<abc <123> hij>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>15,1=>15,2=>15),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'<abc <def> hij>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5,1=>5,2=>5),
                        'length'=>array(0=>5,1=>5,2=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'<abc<>def>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>10,1=>10,2=>10),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'<abc<>',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4,1=>4,2=>4),
                        'length'=>array(0=>2,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

/*  function data_for_test_776() {
        $test1 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
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
                        'left'=>array(),
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
                        'left'=>array(),
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
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*SKIP)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_781() {
        $test1 = array( 'str'=>'aaaxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaa++++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bbb+++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'cccxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'ccc++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'dddddddd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:aaa(*THEN)\w{6}|bbb(*THEN)\w{5}|ccc(*THEN)\w{4}|\w{3})',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }
*/
/*  function data_for_test_782() {
        $test1 = array( 'str'=>'aaaxxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>9,1=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaa++++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bbbxxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>8,1=>8),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'bbb+++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'cccxxxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>7,1=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'ccc++++',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'dddddddd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(aaa(*THEN)\w{6}|bbb(*THEN)\w{5}|ccc(*THEN)\w{4}|\w{3})',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }
*/
/*  function data_for_test_783() {
        $test1 = array( 'str'=>'aaabccc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5,1=>5),
                        'length'=>array(0=>3,1=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a+b?(*THEN)c+(*FAIL)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_784() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ABX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'AADE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ACDE',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'AD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>3),
                        'length'=>array(0=>4,1=>3,2=>1,3=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(A (A|B(*ACCEPT)|C) D)(E)',
                     'modifiers'=>'x',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
    function data_for_test_785() {
        $test1 = array( 'str'=>'1221',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'Satan, oscillate my metallic sonatas!',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>37,3=>36,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'A man, a plan, a canal: Panama!',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>31,3=>30,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'Able was I ere I saw Elba.',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,3=>0,4=>0),
                        'length'=>array(0=>26,3=>25,4=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }

    function data_for_test_786() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>3,1=>3,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aabaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>5,1=>5,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abcdcba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>7,1=>7,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'pqaabaaqp',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>9,1=>9,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'ablewasiereisawelba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>19,1=>19,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'rhubarb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'left'=>array(1),
                        'next'=>'r',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'the quick brown fox',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'left'=>array(1),
                        'next'=>'t',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^((.)(?1)\2|.)$',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }

    function data_for_test_787() {
        $test1 = array( 'str'=>'baz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'caz',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)(?<=b(?1))',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_788() {
        $test1 = array( 'str'=>'zbaaz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>3),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaa',
                        'is_match'=>false,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(2),
                        'next'=>'b',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=b(?1))(a)',
                     'tests'=>array($test1, $test2, $test3));
    }

/*  function data_for_test_789() { //(?&) не поддерживается
        $test1 = array( 'str'=>'baz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<X>a)(?<=b(?&X))',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_790() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'defdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_791() {
        $test1 = array( 'str'=>'abcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'defabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>6,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(3),
                        'next'=>'a',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?|(abc)|(def))(?1)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }

    function data_for_test_792() {
        $test1 = array( 'str'=>"a\"aaaaa",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>-1,1=>2,3=>2),
                        'length'=>array(0=>7,1=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>"b\"aaaaa",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>-1,4=>2,6=>2),
                        'length'=>array(0=>7,4=>1,6=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>-1,4=>2,6=>2),
                        'length'=>array(0=>7,4=>1,6=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>"b\"11111",
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>-1,4=>2,6=>2),
                        'length'=>array(0=>7,4=>1,6=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:a(?<quote> (?<apostrophe>\')|(?<realquote>")) |b(?<quote> (?<apostrophe>\')|(?<realquote>")) ) (?(\'quote\')[a-z]+|[0-9]+)',
                     'modifiers'=>'xJ',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

/*  function data_for_test_793() {
        $test1 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>2),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'CAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?1)|B)(A(*F)|C)',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
*/
/*  function data_for_test_794() {
        $test1 = array( 'str'=>'CCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'BCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ABCD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'BAD',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:(?1)|B)(A(*F)|C)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6));
    }
*/
/*  function data_for_test_795() {
        $test1 = array( 'str'=>'AAD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ACD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'BAD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'BCD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'BAX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ACX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test8 = array( 'str'=>'ABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?1)|B)(A(*ACCEPT)XX|C)D',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8));
    }
*/
/*  function data_for_test_796() {
        $test1 = array( 'str'=>'BAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(DEFINE)(A))B(?1)C',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_797() {
        $test1 = array( 'str'=>'BAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(DEFINE)((A)\2))B(?1)C',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_798() {
        $test1 = array( 'str'=>'(ab(cd)ef)',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>7),
                        'length'=>array(0=>10,1=>10,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<pn> \( ( [^()]++ | (?&pn) )* \) )',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

/*  function data_for_test_799() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!a(*SKIP)b)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_800() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*SKIP)b|ac)',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_801() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*THEN)b|ac)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_802() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*PRUNE)b)',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_803() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*ACCEPT)b)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_804() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(?!a(*SKIP)b))',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_805() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a\Kb)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_806() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>0),
                        'length'=>array(0=>1,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a\Kb))',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_807() { //\K не поддерживается
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,1=>0),
                        'length'=>array(0=>1,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a\Kb)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_808() { //\K не поддерживается
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a\Kcz|ac',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_809() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a\Kbz|ab)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_810() {
        $test1 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?&t)(?(DEFINE)(?<t>a\Kb))$',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_811() {
        $test1 = array( 'str'=>'a(b)c',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'a(b(c)d)e',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>8),
                        'length'=>array(0=>9,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^([^()]|\((?1)*\))*$',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_812() {
        $test1 = array( 'str'=>'0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'00',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'0000',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>4,1=>4,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?P<L1>(?P<L2>0)(?P>L1)|(?P>L2))',
                     'tests'=>array($test1, $test2, $test3));
    }

    function data_for_test_813() {
        $test1 = array( 'str'=>'0',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'00',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'0000',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>0),
                        'length'=>array(0=>1,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?P<L1>(?P<L2>0)|(?P>L2)(?P>L1))',
                     'tests'=>array($test1, $test2, $test3));
    }
/* This one does fail, as expected, in Perl. It needs the complex item at the
     end of the pattern. A single letter instead of (B|D) makes it not fail,
     which I think is a Perl bug. */
/*  function data_for_test_814() {
        $test1 = array( 'str'=>'ACABX',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*PRUNE:A)B|C(*PRUNE:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*//* Force no study, otherwise mark is not seen. The studied version is in
     test 2 because it isn't Perl-compatible. */
/*  function data_for_test_816() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*SKIP:B)(C|X)',
                     'modifiers'=>'KSS',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_817() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_818() {
        $test1 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_819() {
        $test1 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?>A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*//* This should succeed, as the skip causes bump to offset 1 (the mark). Note
that we have to have something complicated such as (B|Z) at the end because,
for Perl, a simple character somehow causes an unwanted optimization to mess
with the handling of backtracking verbs. */
/*  function data_for_test_820() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP:A)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* Test skipping over a non-matching mark. */
/*  function data_for_test_821() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*MARK:B)(*SKIP:A)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* Check shorthand for MARK */
/*  function data_for_test_822() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*:A)A+(*SKIP:A)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* Don't loop! Force no study, otherwise mark is not seen. */
/*  function data_for_test_823() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(),
                        'length'=>array(),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*:A)A+(*SKIP:A)(B|Z)',
                     'modifiers'=>'KSS',
                     'tests'=>array($test1));
    }
*//* This should succeed, as a non-existent skip name disables the skip */
/*  function data_for_test_824() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP:B)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_825() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP:B)(B|Z) | AC(*:B)',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* COMMIT at the start of a pattern should act like an anchor. Again,
however, we need the complication for Perl. */
/*  function data_for_test_826() {
        $test1 = array( 'str'=>'ABCDEFG',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>1,2=>1,3=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>1,2=>1,3=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'DEFGABC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,2=>1,3=>2),
                        'length'=>array(0=>3,1=>1,2=>1,3=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*COMMIT)(A|P)(B|P)(C|P)',
                     'tests'=>array($test1, $test2, $test3));
    }
*//* COMMIT inside an atomic group can't stop backtracking over the group. */
/*  function data_for_test_827() {
        $test1 = array( 'str'=>'abbb',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\w+)(?>b(*COMMIT))\w{2}',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_828() {
        $test1 = array( 'str'=>'abbb',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>4,1=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(\w+)b(*COMMIT)\w{2}',
                     'tests'=>array($test1));
    }
*//* Check opening parens in comment when seeking forward reference. */
/*  function data_for_test_829() {
        $test1 = array( 'str'=>'bac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?&t)(?#()(?(DEFINE)(?<t>a))',
                     'tests'=>array($test1));
    }
*//* COMMIT should override THEN */
/*  function data_for_test_830() {
        $test1 = array( 'str'=>'yes',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(*COMMIT)(yes|no)(*THEN)(*F))?',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_832() {
        $test1 = array( 'str'=>'bc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b?(*SKIP)c',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_833() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*SKIP)b',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_835() {
        $test1 = array( 'str'=>'xxx',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>3,1=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?P<abn>(?P=abn)xxx|)+',
                     'tests'=>array($test1));
    }

    function data_for_test_836() {
        $test1 = array( 'str'=>'aa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aA',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'aB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'Ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?i:([^b]))(?1)',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }

/*  function data_for_test_837() {
        $test1 = array( 'str'=>'aaaaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'left'=>array(1),
                        'next'=>'[^bB]',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?&t)*+(?(DEFINE)(?<t>a))\w$',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_838() {
        $test1 = array( 'str'=>'aaaaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>7),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'aaaaaa',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>6),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?&t)*(?(DEFINE)(?<t>a))\w$',
                     'tests'=>array($test1, $test2));
    }
*/
    function data_for_test_839() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'YZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_840() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'YZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_841() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_842() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_843() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'YZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>0),
                        'length'=>array(0=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)?+(\w)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_844() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'YZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?:a)?+(\w)',
                     'tests'=>array($test1, $test2));
    }

    function data_for_test_845() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>3,2=>4),
                        'length'=>array(0=>5,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_846() {
        $test1 = array( 'str'=>'aaaaX',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>4),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

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
                     'tests'=>array($test1, $test2, $test3, $test4));
    }

    function data_for_test_847() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a|)*(?1)b',
                     'tests'=>array($test1, $test2, $test3));
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

    function data_for_test_850() {
        $test1 = array( 'str'=>'b',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?1)(?:(b)){0}',
                     'tests'=>array($test1));
    }

    function data_for_test_851() {
        $test1 = array( 'str'=>'foo(bar(baz)+baz(bop))',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3,3=>4),
                        'length'=>array(0=>22,1=>22,2=>19,3=>17),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(foo ( \( ((?:(?> [^()]+ )|(?2))*) \) ) )',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }

/*  function data_for_test_852() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>1),
                        'length'=>array(0=>2,1=>2,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(A (A|B(*ACCEPT)|C) D)(E)',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_853() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|b(*THEN)c)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_854() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|bc)',
                     'tests'=>array($test1));
    }

    function data_for_test_855() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(a|b(*THEN)c)',
                     'tests'=>array($test1));
    }

    function data_for_test_856() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(a|bc)',
                     'tests'=>array($test1));
    }

    /*function data_for_test_857() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|b(*THEN)c)++',
                     'tests'=>array($test1));
    }*/

    function data_for_test_858() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|bc)++',
                     'tests'=>array($test1));
    }

    /*function data_for_test_859() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(a|b(*THEN)c)++',
                     'tests'=>array($test1));
    }*/

    function data_for_test_860() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>2,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(a|bc)++',
                     'tests'=>array($test1));
    }

/*  function data_for_test_861() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|b(*THEN)c|d)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_862() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\A.*?(?:a|bc|d)',
                     'tests'=>array($test1));
    }

    function data_for_test_863() {
        $test1 = array( 'str'=>'beetle',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(b))++',
                     'tests'=>array($test1));
    }

/*  function data_for_test_864() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(?=(a(*ACCEPT)z))a)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_865() {
        $test1 = array( 'str'=>'aaaab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(a)(?1)+ab',
                     'tests'=>array($test1));
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

/*  function data_for_test_867() {
        $test1 = array( 'str'=>'aZbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?=a(*:M))aZ',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_868() {
        $test1 = array( 'str'=>'aZbc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?!(*:M)b)aZ',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_869() {
        $test1 = array( 'str'=>'backgammon',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(DEFINE)(a))?b(?1)',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_870() {
        $test1 = array( 'str'=>"abc\ndef",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\N+',
                     'tests'=>array($test1));
    }

    function data_for_test_871() {
        $test1 = array( 'str'=>"abc\ndef ",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^\N{1,}',
                     'tests'=>array($test1));
    }

/*  function data_for_test_872() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aaaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(R)a+|(?R)b)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_873() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aaaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?(R)a+|((?R))b)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_874() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aaaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?(R)a+|(?1)b))',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_875() { //условные подмаски не поддерживаются
        $test1 = array( 'str'=>'aaaabcde',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>5,1=>5),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?(R1)a+|(?1)b))',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_876() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>1,MK=>9),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(*:any \nname)',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_877() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'bba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(?&t)c|(?&t))(?(DEFINE)(?<t>a|b(*PRUNE)c))',
                     'tests'=>array($test1, $test2, $test3));
    }
*//* Checking revised (*THEN) handling *//* Capture */
/*  function data_for_test_878() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_879() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b|(*F)) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_880() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1),
                        'length'=>array(0=>4,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? ( (a(*THEN)b) | (*F) ) c',
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
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_883() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b|(*F)) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_884() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?: (?:a(*THEN)b) | (*F) ) c',
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
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?>a(*THEN)b) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_887() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?>a(*THEN)b|(*F)) c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_888() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?> (?>a(*THEN)b) | (*F) ) c',
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
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b)++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_891() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1),
                        'length'=>array(0=>4,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (a(*THEN)b|(*F))++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_892() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>1,2=>1),
                        'length'=>array(0=>4,1=>2,2=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? ( (a(*THEN)b)++ | (*F) )++ c',
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
                        'left'=>array(),
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b)++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_895() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?:a(*THEN)b|(*F))++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_896() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?: (?:a(*THEN)b)++ | (*F) )++ c',
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
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*? (?: (?:a(*THEN)b)++ )++ c',
                     'modifiers'=>'x',
                     'tests'=>array($test1));
    }
*//* Condition assertion */
/*  function data_for_test_898() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(?(?=a(*THEN)b)ab|ac)',
                     'tests'=>array($test1));
    }
*//* Condition */
/*  function data_for_test_899() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(),
                        'length'=>array(),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*?(?(?=a)a|b(*THEN)c)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_900() {
        $test1 = array( 'str'=>'ba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*?(?:(?(?=a)a|b(*THEN)c)|d)',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_901() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*?(?(?=a)a(*THEN)b|c)',
                     'tests'=>array($test1));
    }
*//* Assertion */
/*  function data_for_test_902() {
        $test1 = array( 'str'=>'aabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^.*(?=a(*THEN)b)',
                     'modifiers'=>' ',
                     'tests'=>array($test1));
    }
*//*------------------------*/
    /*function data_for_test_903() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>a(*:m))',
                     'modifiers'=>'imsxSK ',
                     'tests'=>array($test1));
    }

    function data_for_test_904() {
        $test1 = array( 'str'=>'a',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>(a)(*:m))',
                     'modifiers'=>'imsxSK ',
                     'tests'=>array($test1));
    }*/

/*  function data_for_test_905() {
        $test1 = array( 'str'=>'xacd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(*ACCEPT)b)c',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_906() {
        $test1 = array( 'str'=>'xacd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,1=>1),
                        'length'=>array(0=>1,1=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(a(*ACCEPT)b))c',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_907() {
        $test1 = array( 'str'=>'xabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,1=>1),
                        'length'=>array(0=>1,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>1),
                        'length'=>array(0=>1,1=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xacd',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>3,1=>1),
                        'length'=>array(0=>1,1=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=(a(*COMMIT)b))c',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_908() {
        $test1 = array( 'str'=>'xcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'acd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<!a(*FAIL)b)c',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_909() {
        $test1 = array( 'str'=>'xabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3,MK=>-1),
                        'length'=>array(0=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(*:N)b)c',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_910() {
        $test1 = array( 'str'=>'xabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(*PRUNE)b)c',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_911() {
        $test1 = array( 'str'=>'xabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(*SKIP)b)c',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_912() {
        $test1 = array( 'str'=>'xabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=a(*THEN)b)c',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_913() {
        $test1 = array( 'str'=>'abcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,2=>3),
                        'length'=>array(0=>4,1=>1,2=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a)(?2){2}(.)',
                     'tests'=>array($test1));
    }

/*  function data_for_test_914() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*PRUNE:B)(C|X)',
                     'modifiers'=>'KS',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_915() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*PRUNE:B)(C|X)',
                     'modifiers'=>'KSS',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_916() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*THEN:B)(C|X)',
                     'modifiers'=>'KS',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_917() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*THEN:B)(C|X)',
                     'modifiers'=>'KSY',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_918() {
        $test1 = array( 'str'=>'C',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'D',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>1,1=>1,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(*MARK:A)(*THEN:B)(C|X)',
                     'modifiers'=>'KSS',
                     'tests'=>array($test1, $test2));
    }
*//* This should fail, as the skip causes a bump to offset 3 (the skip) */
/*  function data_for_test_919() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(),
                        'length'=>array(),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* Same */
/*  function data_for_test_920() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(),
                        'length'=>array(),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*MARK:B)(*SKIP:B)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_921() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(),
                        'length'=>array(),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*:A)A+(*SKIP)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* This should fail, as a null name is the same as no name */
/*  function data_for_test_922() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(),
                        'length'=>array(),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP:)(B|Z) | AC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*//* A check on what happens after hitting a mark and them bumping along to
something that does not even start. Perl reports tags after the failures here,
though it does not when the individual letters are made into something
more complicated. */
/*  function data_for_test_923() {
        $test1 = array( 'str'=>'AABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,MK=>1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XXYZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'XAQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'XAQQXZZ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test6 = array( 'str'=>'AXQQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test7 = array( 'str'=>'AXXQQQ',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*:A)B|XX(*:B)Y',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5, $test6, $test7));
    }
*/
/*  function data_for_test_924() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>0),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*THEN:A)B|C(*THEN:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*/
/*  function data_for_test_925() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>0),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'AC',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test5 = array( 'str'=>'CB',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*PRUNE:A)B|C(*PRUNE:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3, $test4, $test5));
    }
*//* An empty name does not pass back an empty string. It is the same as if no
name were given. */
/*  function data_for_test_926() {
        $test1 = array( 'str'=>'AB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0),
                        'length'=>array(0=>2,1=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'CD',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>0,MK=>-1),
                        'length'=>array(0=>2,1=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^(A(*PRUNE:)B|C(*PRUNE:B)D)',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2));
    }
*//* PRUNE goes to next bumpalong; COMMIT does not. */
/*  function data_for_test_927() {
        $test1 = array( 'str'=>'ACAB',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2,MK=>2),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*PRUNE:A)B',
                     'modifiers'=>'K',
                     'tests'=>array($test1));
    }
*//* Mark names can be duplicated */
/*  function data_for_test_928() {
        $test1 = array( 'str'=>'AABC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,MK=>1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'XXYZ',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*:A)B|X(*:A)Y',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_929() {
        $test1 = array( 'str'=>'aw',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>2,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b(*:m)f|a(*:n)w',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_930() {
        $test1 = array( 'str'=>'abaw',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test4 = array( 'str'=>'abax',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'b(*:m)f|aw',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3, $test4));
    }
*/
/*  function data_for_test_931() {
        $test1 = array( 'str'=>'AAAC',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'A(*MARK:A)A+(*SKIP:B)(B|Z) | AAC',
                     'modifiers'=>'xK',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_932() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'axy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(*PRUNE:X)bc|qq',
                     'modifiers'=>'KY',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_933() {
        $test1 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'axy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a(*THEN:X)bc|qq',
                     'modifiers'=>'KY',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_934() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*MARK:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_935() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*MARK:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_936() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*PRUNE:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_937() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*PRUNE:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_938() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*THEN:A)b)..x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_939() {
        $test1 = array( 'str'=>'abxy',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'abpq',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0,MK=>-1),
                        'length'=>array(0=>3,MK=>1),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=a(*THEN:A)b)..(*:Y)x',
                     'modifiers'=>'K',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
    function data_for_test_940() {
        $test1 = array( 'str'=>'hello world test',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>12,2=>12),
                        'length'=>array(0=>4,2=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(another)?(\1?)test',
                     'tests'=>array($test1));
    }

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

/*  function data_for_test_942() {
        $test1 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(a(*COMMIT)b){0}a(?1)|aac',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_943() {
        $test1 = array( 'str'=>'ac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?!a(*COMMIT)b)ac|cd',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_944() {
        $test1 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?:a?)*)*c',
                     'tests'=>array($test1));
    }

    function data_for_test_945() {
        $test1 = array( 'str'=>'aac',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>2),
                        'length'=>array(0=>3,1=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'((?>a?)*)*c',
                     'tests'=>array($test1));
    }

    function data_for_test_946() {
        $test1 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*?a)(?<=ba)',
                     'tests'=>array($test1));
    }

    function data_for_test_947() {
        $test1 = array( 'str'=>'aba',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:.*?a)(?<=ba)',
                     'tests'=>array($test1));
    }

/*  function data_for_test_948() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*?a(*PRUNE)b',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_949() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*?a(*PRUNE)b',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_950() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^a(*PRUNE)b',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_951() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*?a(*SKIP)b',
                     'tests'=>array($test1));
    }
*/
    function data_for_test_952() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*?a)b',
                     'modifiers'=>'s',
                     'tests'=>array($test1));
    }

    function data_for_test_953() {
        $test1 = array( 'str'=>'aab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>2),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*?a)b',
                     'tests'=>array($test1));
    }

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

    function data_for_test_955() {
        $test1 = array( 'str'=>'alphabetabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>8,1=>8),
                        'length'=>array(0=>0,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'endingwxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6,2=>6),
                        'length'=>array(0=>0,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*?)(?<=(abcd)|(wxyz))',
                     'tests'=>array($test1, $test2));
    }

/*  function data_for_test_956() { //однократные подмаски не поддерживаются
        $test1 = array( 'str'=>'alphabetabcd',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,1=>8),
                        'length'=>array(0=>12,1=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'endingwxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0,2=>6),
                        'length'=>array(0=>10,2=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*)(?<=(abcd)|(wxyz))',
                     'tests'=>array($test1, $test2));
    }
*/
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

    function data_for_test_958() {
        $test1 = array( 'str'=>'abcdfooxyz',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>.*?)foo',
                     'tests'=>array($test1));
    }
/* следующие тесты со строки 2870 в testoutput1 */
/*  function data_for_test_959() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'.*?',
                     'modifiers'=>'g+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_960() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\b',
                     'modifiers'=>'g+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_961() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\b',
                     'modifiers'=>'+g',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_962() {
        $test1 = array( 'str'=>'abc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'',
                     'modifiers'=>'g',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 5966 в testoutput1 */
/*  function data_for_test_963() {
        $test1 = array( 'str'=>'abbab',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'a*',
                     'modifiers'=>'g',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 6085 в testoutput1 */
/*  function data_for_test_964() {
        $test1 = array( 'str'=>'abc1abc2xyzabc3',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Gabc.',
                     'modifiers'=>'g',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_965() {
        $test1 = array( 'str'=>'abc1abc2xyzabc3',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>4),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc.',
                     'modifiers'=>'g',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 6307 в testoutput1 */
/*  function data_for_test_966() {
        $test1 = array( 'str'=>"a\nb\nc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^',
                     'modifiers'=>'mg',
                     'tests'=>array($test1, $test2));
    }
*/
/*  function data_for_test_967() {
        $test1 = array( 'str'=>"A\nC\nC\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>9),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?<=C\n)^',
                     'modifiers'=>'mg',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 6547 в testoutput1 */
/*  function data_for_test_968() {
        $test1 = array( 'str'=>"abc\n",
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>4),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'\Z',
                     'modifiers'=>'g',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 6806 в testoutput1 */
/*  function data_for_test_969() {
        $test1 = array( 'str'=>'=ba=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?:(?>([ab])))+a=',
                     'modifiers'=>'+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_970() {
        $test1 = array( 'str'=>'=ba=',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>1),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?>([ab]))+a=',
                     'modifiers'=>'+',
                     'tests'=>array($test1));
    }
*//* следующие тесты со строки 6860 в testoutput1 */
/*  function data_for_test_971() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc){3}abc',
                     'modifiers'=>'+',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_972() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>0),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc)+abc',
                     'modifiers'=>'+',
                     'tests'=>array($test1, $test2, $test3));
    }
*/
/*  function data_for_test_973() {
        $test1 = array( 'str'=>'abcabcabc',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'xyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>6),
                        'length'=>array(0=>3),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=abc)++abc',
                     'modifiers'=>'+',
                     'tests'=>array($test1, $test2, $test3));
    }
*//* следующие тесты со строки 7128 в testoutput1 */
/*  function data_for_test_974() {
        $test1 = array( 'str'=>'Xabcdefghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>9),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'abc\K|def\K',
                     'modifiers'=>'g+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_975() {
        $test1 = array( 'str'=>'Xabcdefghi',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>3),
                        'length'=>array(0=>1),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'ab\Kc|de\Kf',
                     'modifiers'=>'g+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_976() {
        $test1 = array( 'str'=>'ABCDECBA',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>2),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'(?=C)',
                     'modifiers'=>'g+',
                     'tests'=>array($test1));
    }
*/
/*  function data_for_test_977() {
        $test1 = array( 'str'=>'abcdef',
                        'is_match'=>true,
                        'full'=>true,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test2 = array( 'str'=>'** Failers',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        $test3 = array( 'str'=>'defabcxyz',
                        'is_match'=>true,
                        'full'=>false,
                        'index_first'=>array(0=>5),
                        'length'=>array(0=>0),
                        'left'=>array(),
                        'next'=>'',
                        'tags'=>array(qtype_preg_cross_tester::TAG_FROM_PCRE));

        return array('regex'=>'^abc\K',
                     'modifiers'=>'+',
                     'tests'=>array($test1, $test2, $test3));
    }
*/}
