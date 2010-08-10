<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $
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

require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');

class dfa_preg_matcher_complex_test extends UnitTestCase {
    
    function test_easy() {
        $matcher = new dfa_preg_matcher('abcd');
        $result = $matcher->match('fgh');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
        $result = $matcher->match('abce');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 'd');
        $result = $matcher->match('abcd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next == 0);
    }
    function test_alternative() {
        $matcher = new dfa_preg_matcher('ab|cd');
        $result = $matcher->match('ad');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
        $result = $matcher->match('ab');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->match('cd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
    }
    function test_iteration() {
        $matcher = new dfa_preg_matcher('ab*c');
        $result = $matcher->match('ac');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->match('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 46 && $result->next == 0);
    }
    function test_questquant() {
        $matcher = new dfa_preg_matcher('ab?c');
        $result = $matcher->match('ac');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
        $result = $matcher->match('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('abbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 'c');
    }
    function test_metasymbol_dot() {
        $matcher = new dfa_preg_matcher('a.c');
        $result = $matcher->match('afc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
    }
    function test_negative_character_class() {
        $matcher = new dfa_preg_matcher('a[^b]cd');
        $result = $matcher->match('abcd');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next != 'b');
        $result = $matcher->match('axcd');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next == 0);
    }
    function test_many_alternatives() {
        $matcher = new dfa_preg_matcher('(?:ab|cd|ef|gh)i');
        $result = $matcher->match('abi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('cdi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('efi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('ghi');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('yzi');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && ($result->next == 'a' || $result->next == 'c' || $result->next == 'e' || $result->next == 'g'));
    }
    function test_repeat_chars() {
        $matcher = new dfa_preg_matcher('(?:a|b)*abb');
        $result = $matcher->match('ab');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && ($result->next == 'a'|| $result->next == 'b'));
        $result = $matcher->match('abb');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 75 && $result->next == 0);
    }
    function test_quantificator() {
        $matcher = new dfa_preg_matcher('ab{15,35}c');
        $result = $matcher->match('abbbbbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 5 && $result->next == 'b');
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 26 && $result->next == 0);
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 35 && $result->next == 'c');
    }
    function test_plusquant() {
        $matcher = new dfa_preg_matcher('ab+c');
        $result = $matcher->match('ac');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
        $result = $matcher->match('abc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next == 0);
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 100 && $result->next == 0);
    }
    function test_assert() {
        $matcher = new dfa_preg_matcher('a(?=.*b)[xcvbnm]*');
        $result = $matcher->match('ax');
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && $result->next === 'b');
        $result = $matcher->match('abxcv');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 4 && $result->next === 0);
        $result = $matcher->match('avbv');
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 3 && $result->next === 0);
    }
}
?>