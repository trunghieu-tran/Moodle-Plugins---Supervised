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
        $matcher = new dfa_preg_matcher('^abcd$');
        $matcher->match('fgh');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == strlen('fgh')-1 && $matcher->next_char() === 'a');
        $result = $matcher->match('abce');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === 'd');
        $result = $matcher->match('abcd');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === '');
    }
    function test_alternative() {
        $matcher = new dfa_preg_matcher('^(ab|cd)$');
        $result = $matcher->match('ad');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() === 'b');
        $result = $matcher->match('ab');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '');
        $result = $matcher->match('cd');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '');
    }
    function test_iteration() {
        $matcher = new dfa_preg_matcher('^ab*c$');
        $result = $matcher->match('ac');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '');
        $result = $matcher->match('abc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 46 && $matcher->next_char() === '');
    }
    function test_questquant() {
        $matcher = new dfa_preg_matcher('^ab?c$');
        $result = $matcher->match('ac');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '');
        $result = $matcher->match('abc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('abbc');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === 'c');
    }
    function test_metasymbol_dot() {
        $matcher = new dfa_preg_matcher('^a.c$');
        $result = $matcher->match('afc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
    }
    function test_negative_character_class() {
        $matcher = new dfa_preg_matcher('^a[^b]cd$');
        $result = $matcher->match('abcd');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() !== 'b');
        $result = $matcher->match('axcd');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === '');
    }
    function test_many_alternatives() {
        $matcher = new dfa_preg_matcher('^(?:ab|cd|ef|gh)i$');
        $result = $matcher->match('abi');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('cdi');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('efi');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('ghi');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('yzi');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == strlen('yzi')-1 && ($matcher->next_char() === 'a' || $matcher->next_char() === 'c' || $matcher->next_char() === 'e' || $matcher->next_char() === 'g'));
    }
    function test_repeat_chars() {
        $matcher = new dfa_preg_matcher('^(?:a|b)*abb$');
        $result = $matcher->match('ab');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && ($matcher->next_char() === 'a'|| $matcher->next_char() === 'b'));
        $result = $matcher->match('abb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $result = $matcher->match('ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 75 && $matcher->next_char() === '');
    }
    function test_quantificator() {
        $matcher = new dfa_preg_matcher('^ab{15,35}c$');
        $result = $matcher->match('abbbbbc');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 5 && $matcher->next_char() === 'b');
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 26 && $matcher->next_char() === '');
        $result = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 35 && $matcher->next_char() === 'c');
    }
    function test_plusquant() {
        $matcher = new dfa_preg_matcher('^ab+c$');
        $matcher->match('ac');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() === 'b');
        $matcher->match('abc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 100 && $matcher->next_char() === '');
    }
    function test_assert() {
        $matcher = new dfa_preg_matcher('a(?=[xcvnm]*b)[xcvbnm]*');
        $matcher->match('ax');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === 'b');
        $matcher->match('abxcv');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 4 && $matcher->next_char() === '');
        $matcher->match('avbv');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === '');
    }
    function test_repeat_chars_with_assert() {
        $matcher = new dfa_preg_matcher('(?:a|b|c|d)*(?=abb)(a|c)(b|d)(b|d)');
        $matcher->match('ab');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && ($matcher->next_char() === 'a'|| $matcher->next_char() === 'b'));
        $matcher->match('cdd');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && ($matcher->next_char() === 'a'|| $matcher->next_char() === 'b'));
        $matcher->match('abb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $matcher->match('adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 75 && $matcher->next_char() === '');
    }
    function test_repeat_chars_without_assert() {
        $matcher = new dfa_preg_matcher('(?:a|b|c|d)*abb');
        $matcher->match('ab');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && ($matcher->next_char() === 'a'|| $matcher->next_char() === 'b'|| $matcher->next_char() === 'c'|| $matcher->next_char() === 'd'));
        $matcher->match('cdd');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && ($matcher->next_char() === 'a'|| $matcher->next_char() === 'b'|| $matcher->next_char() === 'c'|| $matcher->next_char() === 'd'));
        $matcher->match('abb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
        $matcher->match('adcdcbabadcbababcdcbbabababaabcccccbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 75 && $matcher->next_char() === '');
    }
    function test_start_on_assert() {
        $matcher = new dfa_preg_matcher('(?=[xcvnm]*b)[xcvbnm]*');
        $matcher->match('x');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() === 'b');
        $matcher->match('bxcv');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === '');
        $matcher->match('vbv');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '');
    }
    function test_loop_assert() {
        $matcher = new dfa_preg_matcher('a((?=[^b])[xcvbnm])+');//$matcher->draw(0, 'fp');$matcher->draw(0, 'dfa');
        $matcher->match('axcv');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('abxv');
        $this->assertFalse($matcher->is_matching_complete());
        $matcher = new dfa_preg_matcher('(a(?=[^b])[xcvbnm])+');$matcher->draw(0, 'fp');$matcher->draw(0, 'dfa');
        $matcher->match('axacav');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('ab');
        echo $matcher->last_correct_character_index();
        $this->assertFalse($matcher->is_matching_complete());
    }
    function test_no_anchor() {
        $matcher = new dfa_preg_matcher('ab');
        $matcher->match('OabO');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 1);
        $matcher->match('OacO');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === 'b' && $matcher->first_correct_character_index() == 1);
    }
    function test_left_anchor() {
        $matcher = new dfa_preg_matcher('^ab');
        $matcher->match('abO');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 0);
        $matcher->match('OabO');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == strlen('OabO')-1 && $matcher->next_char() === 'a' && $matcher->first_correct_character_index() == strlen('OabO'));
    }
    function test_right_anchor() {
        $matcher = new dfa_preg_matcher('ab$');
        $matcher->match('Oab');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 1);
        $matcher->match('OabO');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 1);
    }
    function test_full_anchor() {
        $matcher = new dfa_preg_matcher('^ab$');
        $matcher->match('ab');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 0);
        $matcher->match('Oab');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == strlen('Oab')-1 && $matcher->next_char() === 'a' && $matcher->first_correct_character_index() == strlen('Oab'));
        $matcher->match('abO');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === '' && $matcher->first_correct_character_index() == 0);
    }
    function _test_subpattern() {
        $matcher = new dfa_preg_matcher('(a|b)');
        $matcher->match('a');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('b');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('Incorrect!!!');
        $this->assertFalse($matcher->is_matching_complete());
    }
    function test_digit() {
        $matcher = new dfa_preg_matcher('(\d)+x');
        $matcher->match('273x');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('ax');
        $char = $matcher->next_char();
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue(ctype_digit($char));
    }
    function test_word_char() {
        $matcher = new dfa_preg_matcher('a\wa');
        $matcher->match('a_a');
        $this->assertTrue($matcher->is_matching_complete());
        $matcher->match('a{a');
        $char = $matcher->next_char();
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue(ctype_alnum($char) || $char === '_');
    }
    function test_case_insensitive() {
        $matcher = new dfa_preg_matcher('aBcD');
        $matcher->match('Abcd');
        $this->assertFalse($matcher->is_matching_complete());
        $matcher = new dfa_preg_matcher('aBcD', 'i');
        $matcher->match('Abcd');
        $this->assertTrue($matcher->is_matching_complete());
    }
    function test_match_not_from_string_start() {
        $matcher = new dfa_preg_matcher('someregex');
        $matcher->match('sometextwithoutmatchingandsomeregexwithmatchig');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->first_correct_character_index()==26);
        $this->assertTrue($matcher->last_correct_character_index()==34);
    }
}
?>