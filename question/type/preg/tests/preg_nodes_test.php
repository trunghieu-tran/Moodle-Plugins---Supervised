<?php

/**
 * Unit tests for (some of) question/type/preg/preg_nodes.php.
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

class qtype_preg_nodes_test extends PHPUnit_Framework_TestCase {

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

    function test_backref_no_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Matching at the end of the string.
        $res = $backref->match(new qtype_preg_string('abc'), 3, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, 'abc');
        // The string doesn't match with backref at all.
        $res = $backref->match(new qtype_preg_string('abcdef'), 3, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abcdef'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, 'abc');
    }

    function test_backref_partial_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Reaching the end of the string.
        $res = $backref->match(new qtype_preg_string('abcab'), 3, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 2);
        $this->assertEquals($ch, 'c');
        // The string matches backref partially.
        $res = $backref->match(new qtype_preg_string('abcacd'), 3, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abcdef'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 1);
        $this->assertEquals($ch, 'bc');
    }

    function test_backref_full_match() {
        $regex = '(abc)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('abc');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match(new qtype_preg_string('abcabc'), 3, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abc'), 3, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 3);
        $this->assertEquals($ch, '');
    }

    function test_backref_empty_match() {
        $regex = '(^$)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('');
        $this->assertTrue($matcher->get_match_results()->full);
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match(new qtype_preg_string(''), 0, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string(''), -1, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, '');
    }

    function test_backref_alt_match() {
        $regex = '(ab|cd|)';
        $length = 0;
        $matcher = new qtype_preg_nfa_matcher($regex);
        $matcher->match('ab');
        $backref = new preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // 2 characters matched
        $res = $backref->match(new qtype_preg_string('aba'), 2, $length, false, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_preg_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 1);
        $this->assertEquals($ch, 'b');
        // Emptiness matched.
        $matcher->match('xyz');
        $res = $backref->match(new qtype_preg_string('xyz'), 0, $length, false, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 0);
    }
}
