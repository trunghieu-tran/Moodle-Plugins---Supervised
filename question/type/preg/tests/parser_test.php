<?php

/**
 * Unit tests for (some of) question/type/preg/preg_parser.php.
 *
 * @copyright 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');

class qtype_preg_parser_test extends PHPUnit_Framework_TestCase {

    function test_parser_id_dummy_1() {
        $parser = $this->run_parser('a', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->id === 0);
    }

    function test_parser_id_dummy_2() {
        $parser = $this->run_parser('$', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->id === 0);
    }

    function test_parser_id_alt() {
        $parser = $this->run_parser('a|', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->id === 2);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->id == 0);
    }

    function test_parser_id_grouping() {
        $parser = $this->run_parser('(?:ab)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->userinscription === '');
        $this->assertTrue($root->id == 2);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->id == 0);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->id == 1);
    }

    function test_parser_id_subpatt() {
        $parser = $this->run_parser('(ab)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->userinscription === '( ... )');
        $this->assertTrue($root->id == 3);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->userinscription == '');
        $this->assertTrue($root->operands[0]->id == 2);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->operands[0]->id == 0);
        $this->assertTrue($root->operands[0]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[0]->operands[1]->id == 1);
    }

    function test_parser_id_qu() {
        $parser = $this->run_parser('(?:ab)??', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->userinscription === '??');
        $this->assertTrue($root->id == 3);
        $this->assertTrue($root->lazy);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->userinscription === '');
        $this->assertTrue($root->operands[0]->id == 2);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->operands[0]->id == 0);
        $this->assertTrue($root->operands[0]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[0]->operands[1]->id == 1);
    }

    function test_parser_id_aster() {
        $parser = $this->run_parser('(?:[a-z\w]b)*', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription === '*');
        $this->assertTrue($root->id == 3);
        $this->assertTrue($root->greed);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->userinscription === '');
        $this->assertTrue($root->operands[0]->id == 2);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription === array('a-z', '\w'));
        $this->assertTrue($root->operands[0]->operands[0]->id == 0);
        $this->assertTrue($root->operands[0]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[0]->operands[1]->id == 1);
    }

    function test_parser_id_plus() {
        $parser = $this->run_parser('(?:[\wab-yz\d])++', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription === '++');
        $this->assertTrue($root->indfirst === 0);
        $this->assertTrue($root->indlast === 16);
        $this->assertTrue($root->id == 1);
        $this->assertTrue($root->possessive);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription === array('az', '\w', 'b-y', '\d'));
        $this->assertTrue($root->operands[0]->indfirst == 0);
        $this->assertTrue($root->operands[0]->indlast == 14);
        $this->assertTrue($root->operands[0]->id == 0);
    }

    function test_parser_id_brace() {
        $parser = $this->run_parser('[^\p{Egyptian_Hieroglyphs}]{8,}', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription === '{8,}');
        $this->assertTrue($root->id == 1);
        $this->assertTrue($root->greed);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription == array('\p{Egyptian_Hieroglyphs}'));
        $this->assertTrue($root->operands[0]->id == 0);
    }

    function test_parser_id_cond_subpatt() {
        $parser = $this->run_parser('(?(?=a)b|cd)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->userinscription === '(?(?= ... ) ... | .... )');
        $this->assertTrue($root->id == 7);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription == array('b'));
        $this->assertTrue($root->operands[0]->id == 1);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->id === 4);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->flags[0][0]->data == 'c');
        $this->assertTrue($root->operands[1]->operands[0]->id == 2);
        $this->assertTrue($root->operands[1]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[1]->flags[0][0]->data == 'd');
        $this->assertTrue($root->operands[1]->operands[1]->id == 3);
        $this->assertTrue($root->operands[2]->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[2]->userinscription === '(?= ... )');
        // id 5 consumed by alternation node.
        $this->assertTrue($root->operands[2]->id === 6);
        $this->assertTrue($root->operands[2]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->operands[0]->userinscription === array('a'));
        $this->assertTrue($root->operands[2]->operands[0]->id === 0);
        $parser = $this->run_parser('(?(DEFINE)a|b)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->userinscription === '(?(DEFINE) ... | .... )');
        $this->assertTrue($root->id == 4);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription == array('a'));
        $this->assertTrue($root->operands[0]->id == 1);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->userinscription == array('b'));
        $this->assertTrue($root->operands[1]->id == 2);
    }

    function test_parser_easy_regex() {//a|b
        $parser = $this->run_parser('a|b', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->userinscription === '|');
        $this->assertTrue($root->id === 2);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->userinscription === array('a'));
        $this->assertTrue($root->operands[0]->id === 0);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->userinscription === array('b'));
        $this->assertTrue($root->operands[1]->id === 1);
    }
    function test_parser_quantifier() {//ab+
        $parser = $this->run_parser('ab+', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->userinscription === '');
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->userinscription === array('a'));
        $this->assertTrue($root->operands[0]->id === 0);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[1]->leftborder == 1);
        $this->assertTrue($root->operands[1]->userinscription === '+');
        $this->assertTrue($root->operands[1]->id === 2);
        $this->assertTrue($root->operands[1]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->operands[0]->userinscription === array('b'));
        $this->assertTrue($root->operands[1]->operands[0]->id === 1);
    }
    function test_parser_alt_and_quantifier() {//a*|b
        $parser = $this->run_parser('a*|b', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->id === 3);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[0]->userinscription === '*');
        $this->assertTrue($root->operands[0]->id === 1);
        $this->assertTrue($root->operands[0]->leftborder == 0);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->operands[0]->id == 0);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->id === 2);
    }
    function test_parser_concatenation() {//ab
        $parser = $this->run_parser('ab', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->id === 2);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->id == 0);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->id == 1);
    }
    function test_parser_alt_and_concatenation() {//ab|cd
        $parser = $this->run_parser('ab|cd', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->id === 6);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->id === 2);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->operands[0]->id == 0);
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[0]->operands[1]->id === 1);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->id == 5);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->flags[0][0]->data == 'c');
        $this->assertTrue($root->operands[1]->operands[0]->id === 3);
        $this->assertTrue($root->operands[1]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[1]->flags[0][0]->data == 'd');
        $this->assertTrue($root->operands[1]->operands[1]->id === 4);
    }
    function test_parser_cond_subpatt() {// (?(name)a|b)
        $parser = $this->run_parser('(?(name)a|b)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->number == 'name');
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'b');
    }
    function _test_parser_long_regex() {//(?:a|b)*abb
        $parser = $this->run_parser('(?:a|b)*abb', $errornodes);
        $matcher = new qtype_preg_dfa_preg_matcher;
        $matcher->roots[0] = $parser->get_root();
        $matcher->append_end(0);
        $matcher->buildfa(0);
        $res = $matcher->compare('ab', 0);
        $this->assertTrue(!$res->full);
        $this->assertTrue($res->index == 1);
        $this->assertTrue(($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abb', 0);
        $this->assertTrue($res->full);
        $this->assertTrue($res->index == 2);
        $this->assertTrue($res->next == 0);
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbab', 0);
        $this->assertTrue(!$res->full);
        $this->assertTrue($res->index == 57);
        $this->assertTrue(($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbabb', 0);
        $this->assertTrue($res->full);
        $this->assertTrue($res->index == 58);
        $this->assertTrue($res->next == 0);
    }
    function test_parser_two_anchors() {
        $parser = $this->run_parser('^a$', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->operands[0]->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription === '^');
        $this->assertTrue($root->operands[0]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_parser_start_anchor() {
        $parser = $this->run_parser('^a', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'a');
    }
    function test_parser_end_anchor() {
        $parser = $this->run_parser('a$', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_parser_error() {
        $parser = $this->run_parser('^((ab|cd)ef$', $errornodes);
        $this->assertTrue($parser->get_error());
    }
    function test_parser_no_error() {
        $parser = $this->run_parser('((ab|cd)ef)', $errornodes);
        $this->assertFalse($parser->get_error());
    }
    function test_parser_asserts() {
        $parser = $this->run_parser('(?<=\w)(?<!_)a*(?=\w)(?!_)', $errornodes);
        $root = $parser->get_root();
        $tb = $root->operands[0]->operands[0];
        $fb = $root->operands[0]->operands[1];
        $tf = $root->operands[1]->operands[1]->operands[0];
        $ff = $root->operands[1]->operands[1]->operands[1];
        $this->assertTrue($tf->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tf->subtype == qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($tf->userinscription === '(?= ... )');
        $this->assertTrue($ff->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($ff->subtype == qtype_preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($ff->userinscription === '(?! ... )');
        $this->assertTrue($fb->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($fb->subtype == qtype_preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($fb->userinscription === '(?<! ... )');
        $this->assertTrue($tb->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tb->subtype == qtype_preg_node_assert::SUBTYPE_PLB);
        $this->assertTrue($tb->userinscription === '(?<= ... )');
    }
    function test_parser_metasymbol_dot() {
        $parser = $this->run_parser('.', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->flags[0][0]->data == qtype_preg_charset_flag::PRIN);
    }
    function test_parser_word_break() {
        $parser = $this->run_parser('a\b', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue(!$root->operands[1]->negative);
    }
    function test_parser_word_not_break() {
        $parser = $this->run_parser('a\B', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue($root->operands[1]->negative);
    }
    function test_parser_subpatterns() {
        $parser = $this->run_parser('((?:(?(?=a)(?>b)|a)))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->operands[0]->operands[0]->subtype == qtype_preg_node_subpatt::SUBTYPE_ONCEONLY);
    }
    function test_parser_duplicate_subpattern_numbers() {
        $parser = $this->run_parser('(?|a|b|c)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == 'b');
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == 'c');
    }
    function test_parser_index() {
        $parser = $this->run_parser('abcdefgh|(abcd)*', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->indfirst == 0);
        $this->assertTrue($root->indlast == 15);
        $this->assertTrue($root->operands[0]->indfirst == 0);
        $this->assertTrue($root->operands[0]->indlast == 7);
        $this->assertTrue($root->operands[1]->indfirst == 9);
        $this->assertTrue($root->operands[1]->indlast == 15);
    }
    function test_parser_array_of_tokens() {//\88
        $parser = $this->run_parser('\89', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data == chr(0));
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data == '8');
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data == '9');
    }
    function test_syntax_errors() { // Test error reporting.
        // Unclosed square brackets.
        $parser = $this->run_parser('ab(c|d)[fg\\]', $errornodes);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET);
        $this->assertTrue($errornodes[0]->indfirst == 7);
        // Unclosed parenthesis.
        $parser = $this->run_parser('a(b(?:c(?=d(?!e(?<=f(?<!g(?>h', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 7);
        $this->assertFalse(empty($errornodes[0]->operands));
        $this->assertFalse(empty($errornodes[1]->operands));
        $this->assertFalse(empty($errornodes[2]->operands));
        $this->assertFalse(empty($errornodes[3]->operands));
        $this->assertFalse(empty($errornodes[4]->operands));
        $this->assertFalse(empty($errornodes[5]->operands));
        $this->assertFalse(empty($errornodes[6]->operands));
        $root = $parser->get_root();
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->operands[1]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        // Unopened parenthesis.
        $parser = $this->run_parser(')ab(c|d)eg)', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) === 2);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[1]->indfirst == 10);
        $root = $parser->get_root();
        $this->assertTrue($errornodes[1]->operands[0] === $root->operands[0]);
        //var_dump($root);
        // Several unopened and unclosed parenthesis.
        $parser = $this->run_parser(')a)b)e(((g(', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) === 7);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertFalse(empty($errornodes[1]->operands));
        $this->assertFalse(empty($errornodes[2]->operands));
        $this->assertTrue(empty($errornodes[3]->operands));
        $this->assertFalse(empty($errornodes[4]->operands));
        $this->assertFalse(empty($errornodes[5]->operands));
        $this->assertFalse(empty($errornodes[6]->operands));
        // Empty parenthesis
        $parser = $this->run_parser(')abeg(?!)f', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) === 2);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[2]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[2]->indfirst == 5);
        $this->assertTrue($errornodes[2]->indlast == 8);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertTrue(empty($errornodes[2]->operands));
        // Several empty parenthesis.
        $parser = $this->run_parser(')ab()eg(?!)f', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) === 3);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[3]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[3]->indfirst == 7);
        $this->assertTrue($errornodes[3]->indlast == 10);
        $this->assertTrue($errornodes[4]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[4]->indfirst == 3);
        $this->assertTrue($errornodes[4]->indlast == 4);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertTrue(empty($errornodes[3]->operands));
        $this->assertTrue(empty($errornodes[4]->operands));
        // Quantifiers without argument inside parentheses.
        $parser = $this->run_parser('?a({2,3})c(+)e(+)(*s)f', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) === 5);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[0]->indlast == 0);
        $this->assertTrue($errornodes[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[1]->indfirst == 3);
        $this->assertTrue($errornodes[1]->indlast == 7);
        $this->assertTrue($errornodes[2]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype == qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[2]->indfirst == 11);
        $this->assertTrue($errornodes[2]->indlast == 11);
        $this->assertTrue($errornodes[3]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype == qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[3]->indfirst == 15);
        $this->assertTrue($errornodes[3]->indlast == 15);
        $this->assertTrue($errornodes[4]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype == qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errornodes[4]->indfirst == 17);
        $this->assertTrue($errornodes[4]->indlast == 20);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertTrue(empty($errornodes[1]->operands));
        $this->assertTrue(empty($errornodes[2]->operands));
        $this->assertTrue(empty($errornodes[3]->operands));
        $this->assertTrue(empty($errornodes[4]->operands));
        // Test error reporting for conditional subpatterns, which are particulary tricky.
        // Three or more alternatives in conditional subpattern.
        $parser = $this->run_parser('(?(?=bc)dd|e*f|hhh)', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[0]->indlast == 18);
        $this->assertTrue(is_a($errornodes[0]->operands[0], 'qtype_preg_node_alt'));//There should be two operands for such error: alternative and expression inside assertion
        $this->assertTrue(is_a($errornodes[0]->operands[1], 'qtype_preg_node_concat'));
        // Correct situation: alternatives are nested within two alternatives for conditional subpattern.
        $parser = $this->run_parser('(?(?=bc)(dd|e*f)|(hhh|ff))', $errornodes);
        $this->assertFalse($parser->get_error());
        // Unclosed second parenthesis.
        $parser = $this->run_parser('a(?(?=bc)dd|e*f|hhh', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 1);
        $this->assertTrue($errornodes[0]->indlast == 5);
        $this->assertTrue(is_a($errornodes[0]->operands[0], 'qtype_preg_node_alt'));//There should be two operands for such error: alternative and expression inside assertion
        $this->assertTrue(is_a($errornodes[0]->operands[1], 'qtype_preg_node_concat'));
        // Two parenthesis unclosed.
        $parser = $this->run_parser('(?(?=bce*f|hhh', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[0]->indlast == 4);
        $this->assertTrue(is_a($errornodes[0]->operands[0], 'qtype_preg_node_alt'));
        // Empty assert in conditional subpattern.
        $parser = $this->run_parser('a(?(?=)', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[1]->indfirst == 1);
        $this->assertTrue($errornodes[1]->indlast == 6);
        $this->assertTrue(empty($errornodes[1]->operands));
        // Empty yes-expr in conditional subpattern
        $parser = $this->run_parser('(?(?=ab))', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[1]->indfirst == 0);
        $this->assertTrue($errornodes[1]->indlast == 8);
        $this->assertTrue(is_a($errornodes[1]->operands[0], 'qtype_preg_node_concat'));
        // Conditional subpattern starts at the end of expression.
        $parser = $this->run_parser('ab(?(?=', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->indfirst == 2);
        $this->assertTrue($errornodes[0]->indlast == 6);
        $this->assertTrue(empty($errornodes[1]->operands));
        // Everything possible.
        $parser = $this->run_parser('(*UTF9))((?(?=x)a|b|c)()({5,4})(?i-i)[[:hamster:]]\p{Squirrel}', $errornodes);
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 10);
        $this->assertTrue($errornodes[0]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errornodes[0]->indfirst == 0);
        $this->assertTrue($errornodes[0]->indlast == 6);
        $this->assertTrue($errornodes[0]->addinfo == '(*UTF9)');
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertTrue($errornodes[1]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[1]->indfirst == 7);
        $this->assertTrue($errornodes[1]->indlast == 7);
        $this->assertTrue(is_a($errornodes[1]->operands[0], 'qtype_preg_leaf_control'));
        $this->assertTrue($errornodes[2]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype == qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER);
        $this->assertTrue($errornodes[2]->indfirst == 9);
        $this->assertTrue($errornodes[2]->indlast == 21);
        $this->assertTrue(is_a($errornodes[2]->operands[0], 'qtype_preg_node_alt'));
        $this->assertTrue(is_a($errornodes[2]->operands[1], 'qtype_preg_leaf_charset'));
        $this->assertTrue($errornodes[4]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype == qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[4]->indfirst == 25);
        $this->assertTrue($errornodes[4]->indlast == 29);
        $this->assertTrue(empty($errornodes[4]->operands));
        $this->assertTrue($errornodes[5]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[5]->subtype == qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($errornodes[5]->indfirst == 26);
        $this->assertTrue($errornodes[5]->indlast == 28);
        $this->assertTrue(empty($errornodes[5]->operands));
        $this->assertTrue($errornodes[6]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[6]->subtype == qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER);
        $this->assertTrue($errornodes[6]->indfirst == 31);
        $this->assertTrue($errornodes[6]->indlast == 36);
        $this->assertTrue(empty($errornodes[6]->operands));
        $this->assertTrue($errornodes[7]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[7]->subtype == qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($errornodes[7]->indfirst == 38);
        $this->assertTrue($errornodes[7]->indlast == 48);
        $this->assertTrue(empty($errornodes[7]->operands));
        $this->assertTrue($errornodes[7]->addinfo == '[:hamster:]');
        $this->assertTrue($errornodes[8]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[8]->subtype == qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($errornodes[8]->indfirst == 50);
        $this->assertTrue($errornodes[8]->indlast == 61);
        $this->assertTrue($errornodes[8]->addinfo == 'Squirrel');
        $this->assertTrue(empty($errornodes[8]->operands));
        $this->assertTrue($errornodes[9]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[9]->subtype == qtype_preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[9]->indfirst == 22);
        $this->assertTrue($errornodes[9]->indlast == 23);
        $this->assertTrue(empty($errornodes[8]->operands));
        $this->assertTrue($errornodes[10]->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[10]->subtype == qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[10]->indfirst == 8);
        $this->assertTrue($errornodes[10]->indlast == 8);
        $this->assertTrue(is_a($errornodes[10]->operands[0], 'qtype_preg_node_concat'));
    }
    function test_preserve_all_nodes() {//Tests for preserve all nodes option.
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser('(?:a)', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->subtype == qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET);
    }
    function test_pcre_strict() {//Tests for PCRE strict option.
        $options = new qtype_preg_handling_options;
        $options->pcrestrict = true;
        //Empty parenthesis should be empty subpattern.
        $parser = $this->run_parser('()', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->subtype == qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        //Empty parenthesis with concatenation.
        $parser = $this->run_parser('a()b', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        //Empty assertion
        $parser = $this->run_parser('(?=)', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->subtype == qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($root->operands[0]->type == qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    }
    /**
     * Service function to run parser on regex.
     * @param regex Regular expression to parse.
     * @param options qtype_preg_handling_options
     * @return parser object.
     */
    protected function run_parser($regex, &$errors, $options = null) {
        $parser = new preg_parser_yyParser;
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        if ($options === null) {
            $options = new qtype_preg_handling_options;
        }
        $lexer->handlingoptions = $options;
        $parser->handlingoptions = $options;
        while ($token = $lexer->nextToken()) {
            if (!is_array($token)) {
                $parser->doParse($token->type, $token->value);
            } else {
                 foreach ($token as $curtoken) {
                    $parser->doParse($curtoken->type, $curtoken->value);
                }
            }
        }
        $parser->doParse(0, 0);
        $errors = array();
        $lexerrors = $lexer->get_errors();
        foreach ($lexerrors as $node) {
            $errors[] = $node;
        }
        $parseerrors = $parser->get_error_nodes();
        foreach($parseerrors as $node) {
            $errors = $node;
        }
        fclose($pseudofile);
        return $parser;
    }
}
