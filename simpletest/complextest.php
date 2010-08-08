<?php  // $Id: testquestiontype.php,put version put time dvkolesov Exp $
/**
 * Unit tests for (some of) question/type/preg/dfa_preg_matcher.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/reasc.php');

class dfa_preg_matcher_test extends UnitTestCase {
    var $qtype;

    function test_easy() {
        $matcher = new dfa_preg_matcher('abcd');
        $result = $matcher->get_result('fgh');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
        $result = $matcher->get_result('abce');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 'd');
        $result = $matcher->get_result('abcd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next == 0);
    }
    function test_alternative() {
        $matcher = new dfa_preg_matcher('ab|cd');
        $result = $matcher->get_result('ad');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
        $result = $matcher->get_result('ab');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->get_result('cd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
    }
    function test_iteration() {
        $matcher = new dfa_preg_matcher('ab*c');
        $result = $matcher->get_result('ac');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->get_result('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 46 && $result->next == 0);
    }
    function test_questquant() {
        $matcher = new dfa_preg_matcher('ab?c');
        $result = $matcher->get_result('ac');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->get_result('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('abbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 'c');
    }
    function test_metasymbol_dot() {
        $matcher = new dfa_preg_matcher('a.c');
        $result = $matcher->get_result('afc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
    }
    function test_negative_character_class() {//NEED DEBUG!!!!
        $matcher = new dfa_preg_matcher('a[^b]cd');
        $result = $matcher->get_result('abcd');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next != 'b');
        $result = $matcher->get_result('axcd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next == 0);
    }
    function test_many_alternatives() {
        $matcher = new dfa_preg_matcher('(?:ab|cd|ef|gh)i');
        $result = $matcher->get_result('abi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('cdi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('efi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('ghi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('yzi');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && ($result->next == 'a' || $result->next == 'c' || $result->next == 'e' || $result->next == 'g'));
    }
    function test_repeat_chars() {
        $matcher = new dfa_preg_matcher('(?:a|b)*abb');
        $result = $matcher->get_result('ab');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && ($result->next == 'a'|| $result->next == 'b'));
        $result = $matcher->get_result('abb');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 75 && $result->next == 0);
    }
    function test_quantificator() {
        $matcher = new dfa_preg_matcher('ab{15,35}c');
        $result = $matcher->get_result('abbbbbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 5 && $result->next == 'b');
        $result = $matcher->get_result('abbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 26 && $result->next == 0);
        $result = $matcher->get_result('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 35 && $result->next == 'c');
    }
    function test_plusquant() {
        $matcher = new dfa_preg_matcher('ab+c');
        $result = $matcher->get_result('ac');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
        $result = $matcher->get_result('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->get_result('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 100 && $result->next == 0);
    }
    function test_assert() {//NEED DEBUG!!!!!!!
        $matcher = new dfa_preg_matcher('a(?=.*b)[xcvbnm]*');
        $result = $matcher->get_result('ax');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 'b');
        $result = $matcher->get_result('abxcv');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 4 && $result->next == 0);
        $result = $matcher->get_result('avbv');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next == 0);
    }
}
?>