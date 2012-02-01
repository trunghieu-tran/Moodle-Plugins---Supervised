<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_nodes.php.
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

class qtype_preg_regex_handler_test extends UnitTestCase {

    function test_clone_preg_operator() {
        //Try copying tree for a|b*
        $anode = new preg_leaf_charset;
        $anode->charset = 'a';
        $bnode = new preg_leaf_charset;
        $bnode->charset = 'b';
        $astnode = new preg_node_infinite_quant;
        $astnode->leftborder = 0;
        $astnode->operands[] = $bnode;
        $altnode = new preg_node_alt;
        $altnode->operands[] = $anode;
        $altnode->operands[] = $astnode;

        $copyroot = clone $altnode;

        $this->assertTrue($copyroot == $altnode, 'Root node contents copied wrong');
        $this->assertTrue($copyroot !== $altnode, 'Root node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[0] == $altnode->operands[0], 'A character node contents copied wrong');
        $this->assertTrue($copyroot->operands[0] !== $altnode->operands[0], 'A character node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[1] == $altnode->operands[1], 'Asterisk node contents copied wrong');
        $this->assertTrue($copyroot->operands[1] !== $altnode->operands[1], 'Asterisk node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[1]->operands[0] == $altnode->operands[1]->operands[0], 'B character node contents copied wrong');
        $this->assertTrue($copyroot->operands[1]->operands[0] !== $altnode->operands[1]->operands[0], 'B character node wasn\'t copyied');
    }

}

/**
 * Unit tests for preg_leaf_backref.
 *
 * @author Valeriy Streltsov
 */
class qtype_preg_backreferences_test extends UnitTestCase {
    function test_no_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Matching at the end of the string.
        $res = $backref->match('abc', 3, &$length, false);
        $ch = $backref->next_character('abc', 2, $length);
        $this->assertFalse($res);
        $this->assertEqual($length, 0);
        $this->assertEqual($ch, 'a');
        // The string doesn't match with backref at all.
        $res = $backref->match('abcdef', 3, &$length, false);
        $ch = $backref->next_character('abcdef', 2, $length);
        $this->assertFalse($res);
        $this->assertEqual($length, 0);
        $this->assertEqual($ch, 'a');
    }

    function test_partial_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Reaching the end of the string.
        $res = $backref->match('abcab', 3, &$length, false);
        $ch = $backref->next_character('abc', 2, $length);
        $this->assertFalse($res);
        $this->assertEqual($length, 2);
        $this->assertEqual($ch, 'c');
        // The string matches backref partially.
        $res = $backref->match('abcacd', 3, &$length, false);
        $ch = $backref->next_character('abcdef', 2, $length);
        $this->assertFalse($res);
        $this->assertEqual($length, 1);
        $this->assertEqual($ch, 'b');
    }

    function test_full_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match('abcabc', 3, &$length, false);
        $ch = $backref->next_character('abc', 3, $length);
        $this->assertTrue($res);
        $this->assertEqual($length, 3);
        $this->assertEqual($ch, '');
    }

    function test_empty_match() {
        $regex = '(^$)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('');
        $this->assertTrue($matcher->get_match_results()->full);
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match('', 0, &$length, false);
        $ch = $backref->next_character('', -1, $length);
        $this->assertTrue($res);
        $this->assertEqual($length, 0);
        $this->assertEqual($ch, '');
    }

    function test_alt_match() {
        $regex = '(ab|cd|)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('ab');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // 2 characters matched
        $res = $backref->match('aba', 2, &$length, false);
        $ch = $backref->next_character('abc', 2, $length);
        $this->assertFalse($res);
        $this->assertEqual($length, 1);
        $this->assertEqual($ch, 'b');
        // Emptiness matched.
        $matcher->match('xyz');
        $res = $backref->match('xyz', 0, &$length, false);
        $this->assertTrue($res);
        $this->assertEqual($length, 0);
    }
}
?>