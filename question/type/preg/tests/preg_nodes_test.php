<?php

/**
 * Unit tests for question/type/preg/preg_nodes.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

class qtype_preg_nodes_test extends PHPUnit_Framework_TestCase {

    function test_clone_preg_operator() {
        //Try copying tree for a|b*
        $anode = new qtype_preg_leaf_charset();
        $anode->set_user_info(new qtype_preg_position());
        $anode->charset = 'a';
        $bnode = new qtype_preg_leaf_charset();
        $bnode->set_user_info(new qtype_preg_position());
        $bnode->charset = 'b';
        $astnode = new qtype_preg_node_infinite_quant();
        $astnode->set_user_info(new qtype_preg_position());
        $astnode->leftborder = 0;
        $astnode->operands[] = $bnode;
        $altnode = new qtype_preg_node_alt();
        $altnode->set_user_info(new qtype_preg_position());
        $altnode->operands[] = $anode;
        $altnode->operands[] = $astnode;

        $copyroot = clone $altnode;

        $this->assertTrue($copyroot == $altnode, 'Root node contents copied wrong');
        $this->assertTrue($copyroot !== $altnode, 'Root node wasn\'t copied');
        $this->assertTrue($copyroot->operands[0] == $altnode->operands[0], 'A character node contents copied wrong');
        $this->assertTrue($copyroot->operands[0] !== $altnode->operands[0], 'A character node wasn\'t copied');
        $this->assertTrue($copyroot->operands[1] == $altnode->operands[1], 'Asterisk node contents copied wrong');
        $this->assertTrue($copyroot->operands[1] !== $altnode->operands[1], 'Asterisk node wasn\'t copied');
        $this->assertTrue($copyroot->operands[1]->operands[0] == $altnode->operands[1]->operands[0], 'B character node contents copied wrong');
        $this->assertTrue($copyroot->operands[1]->operands[0] !== $altnode->operands[1]->operands[0], 'B character node wasn\'t copied');
    }
    function test_backref_no_match() {
        $regex = '(abc)';
        $length = 0;
        $matchoptions = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $matcher = new qtype_preg_nfa_matcher($regex, $matchoptions);
        $matcher->match('abc');
        $backref = new qtype_preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Matching at the end of the string.
        $res = $backref->match(new qtype_poasquestion_string('abc'), 3, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, 'abc');
        // The string doesn't match with backref at all.
        $res = $backref->match(new qtype_poasquestion_string('abcdef'), 3, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abcdef'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, 'abc');
    }
    function test_backref_partial_match() {
        $regex = '(abc)';
        $length = 0;
        $matchoptions = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $matcher = new qtype_preg_nfa_matcher($regex, $matchoptions);
        $matcher->match('abc');
        $backref = new qtype_preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // Reaching the end of the string.
        $res = $backref->match(new qtype_poasquestion_string('abcab'), 3, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 2);
        $this->assertEquals($ch, 'c');
        // The string matches backref partially.
        $res = $backref->match(new qtype_poasquestion_string('abcacd'), 3, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abcdef'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 1);
        $this->assertEquals($ch, 'bc');
    }
    function test_backref_full_match() {
        $regex = '(abc)';
        $length = 0;
        $matchoptions = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $matcher = new qtype_preg_nfa_matcher($regex, $matchoptions);
        $matcher->match('abc');
        $backref = new qtype_preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match(new qtype_poasquestion_string('abcabc'), 3, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abc'), 3, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 3);
        $this->assertEquals($ch, '');
    }
    function test_backref_empty_match() {
        $regex = '(^$)';
        $length = 0;
        $matchoptions = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $matcher = new qtype_preg_nfa_matcher($regex, $matchoptions);
        $matcher->match('');
        $this->assertTrue($matcher->get_match_results()->full);
        $backref = new qtype_preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        $res = $backref->match(new qtype_poasquestion_string(''), 0, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string(''), -1, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, '');
    }
    function test_backref_alt_match() {
        $regex = '(ab|cd|)';
        $length = 0;
        $matchoptions = new qtype_preg_matching_options();  // Forced subexpression catupring.
        $matcher = new qtype_preg_nfa_matcher($regex, $matchoptions);
        $matcher->match('ab');
        $backref = new qtype_preg_leaf_backref();
        $backref->number = 1;
        $backref->matcher = $matcher;

        // 2 characters matched
        $res = $backref->match(new qtype_poasquestion_string('aba'), 2, $length, $matcher->get_match_results());
        $ch = $backref->next_character(new qtype_poasquestion_string('abc'), 2, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 1);
        $this->assertEquals($ch, 'b');
        // Emptiness matched.
        $matcher->match('xyz');
        $res = $backref->match(new qtype_poasquestion_string('xyz'), 0, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 0);
    }
    /*function test_anchoring() {
        $handler = new qtype_preg_nfa_matcher('^');
        $this->assertTrue($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^|^');
        $this->assertTrue($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.*x)|^');
        $this->assertTrue($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('(?:a.+$)|.*cd|(^a|.*x)|^');        // (?:a.+$) breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.+cd|(^a|.*x)|^');       // .+cd breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.cd|(^a|.*x)|^');        // .cd breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(a|.*x)|^');        // (a|.*x) breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|x)|^');         // (^a|x) breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.x)|^');        // (^a|.x) breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.?x)|^');       // (^a|.?x) breaks anchoring
        $this->assertFalse($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.*x)|^');
        $this->assertTrue($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.*x)||||');     // Emptiness makes anchoring
        $this->assertTrue($handler->is_regex_anchored());
        $handler = new qtype_preg_nfa_matcher('^(?:a.+$)|.*cd|(^a|.*x)|(|c)');    // (|c) makes anchoring
        $this->assertTrue($handler->is_regex_anchored());
    }*/
    function test_syntax_errors() {
        $handler = new qtype_preg_regex_handler('(*UTF9))((?(?=x)a|b|c)()({5,4})(?i-i)[[:hamster:]]\p{Squirrel}[abc');
        $errors = $handler->get_errors();
        $this->assertTrue(count($errors) == 11);
        /*$this->assertTrue($errors[0]->index_first == 31); // Setting and unsetting modifier.
        $this->assertTrue($errors[0]->index_last == 36);
        $this->assertTrue($errors[1]->index_first == 62); // Unclosed charset.
        $this->assertTrue($errors[1]->index_last == 65);
        $this->assertTrue($errors[2]->index_first == 0);  // Unknown control sequence.
        $this->assertTrue($errors[2]->index_last == 6);
        $this->assertTrue($errors[3]->index_first == 7);  // Wrong closing paren.
        $this->assertTrue($errors[3]->index_last == 7);
        $this->assertTrue($errors[4]->index_first == 9);  // Three alternations in the conditional subexpression.
        $this->assertTrue($errors[4]->index_last == 21);
        $this->assertTrue($errors[5]->index_first == 25); // Quantifier without operand.
        $this->assertTrue($errors[5]->index_last == 29);
        $this->assertTrue($errors[6]->index_first == 26); // Wrong quantifier ranges.
        $this->assertTrue($errors[6]->index_last == 28);
        $this->assertTrue($errors[7]->index_first == 38); // Unknown POSIX class.
        $this->assertTrue($errors[7]->index_last == 48);
        $this->assertTrue($errors[8]->index_first == 50); // Unknown Unicode property.
        $this->assertTrue($errors[8]->index_last == 61);
        $this->assertTrue($errors[9]->index_first == 22); // Empty parens.
        $this->assertTrue($errors[9]->index_last == 23);
        $this->assertTrue($errors[10]->index_first == 8); // Wrong opening paren.
        $this->assertTrue($errors[10]->index_last == 8);*/
        $handler = new qtype_preg_regex_handler('(?z)a(b)\1\2');
        $errors = $handler->get_errors();
        $this->assertTrue(count($errors) == 3);
        /*$this->assertTrue($errors[0]->index_first == 0);  // Wrong modifier.
        $this->assertTrue($errors[0]->index_last == 3);
        $this->assertTrue($errors[1]->index_first == 10); // Backreference to unexisting subexpression.
        $this->assertTrue($errors[1]->index_last == 11);*/
    }

    function test_expand_concat() {
        $handler = new qtype_preg_regex_handler("abcd");
        $idcounter = 1000;
        $node = $handler->get_ast_root();
        $node->expand(0, 2, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 3);
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 2);
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 1);
    }

    function test_node_by_regex_fragment_one_char() {
        $handler = new qtype_preg_regex_handler("a");
        $idcounter = 1000;

        $indexfirst = 0; $indexlast = 0;     // Exact selection.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node === $root);

        $indexfirst = 0; $indexlast = 11;    // Too wide selection.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node === null);
    }

    function test_node_by_regex_fragment_concat() {
        $handler = new qtype_preg_regex_handler("abcd");
        $idcounter = 1000;

        $indexfirst = 0; $indexlast = 1;
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[0]->flags[0][0]->data == 'a' && $node->operands[1]->flags[0][0]->data == 'b');

        $indexfirst = 0; $indexlast = 2;
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[1]->flags[0][0]->data == 'c');
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[0]->flags[0][0]->data == 'a' && $node->operands[1]->flags[0][0]->data == 'b');
    }

    function test_node_by_regex_fragment_concat_subpatt_quant() {
        $handler = new qtype_preg_regex_handler("(abcd)+");
        $idcounter = 1000;

        $indexfirst = 0; $indexlast = 5;     // Exact subpattern selection.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node === $root->operands[0]);

        $indexfirst = 0; $indexlast = 4;     // Selection to be expanded to the whole subexpression.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node === $root->operands[0]);

        $indexfirst = 2; $indexlast = 6;     // Selection to be expanded to the whole quantifier.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node === $root);

        $indexfirst = 1; $indexlast = 4;     // Exact concatenation selection.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[1]->flags[0][0]->data == 'd');
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[1]->flags[0][0]->data == 'c');
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'a' && $node->operands[1]->flags[0][0]->data == 'b');

        $indexfirst = 2; $indexlast = 3;     // Middle operands to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($node->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->operands[1]->flags[0][0]->data == 'c');
    }

    function test_node_by_regex_fragment_alt() {
        $handler = new qtype_preg_regex_handler("ab|cde");
        $idcounter = 1000;

        $indexfirst = 3; $indexlast = 3;     // Exact selection: 'c'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        $indexfirst = 3; $indexlast = 4;     // Exact selection: 'cd'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        $indexfirst = 0; $indexlast = 5;     // Exact selection: 'ab|cde'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && count($node->operands) == 2);
        $node = $node->operands[1];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[1]->flags[0][0]->data == 'e');
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'c' && $node->operands[1]->flags[0][0]->data == 'd');

        $indexfirst = 1; $indexlast = 3;     // Selection to be expanded: 'b|c'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && count($node->operands) == 2);
        $node = $node->operands[1];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[1]->flags[0][0]->data == 'e');
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'c' && $node->operands[1]->flags[0][0]->data == 'd');
    }

    function test_node_by_regex_fragment_multiline() {
        $handler = new qtype_preg_regex_handler("ab|d\n(abcd)+\nqwe(?#comment\n)|alt");
        $idcounter = 1000;

        $indexfirst = 0; $indexlast = 1;     // Exact selection 'b'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        $indexfirst = 7; $indexlast = 7;     // Exact selection 'b'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        $indexfirst = 30; $indexlast = 30;     // Exact selection 't'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        $indexfirst = 28; $indexlast = 30;     // Exact selection 'alt'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);

        $indexfirst = 13; $indexlast = 15;     // Selection 'qwe' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        $indexfirst = 19; $indexlast = 25;     // Comment selection, should be expanded to the whole alternation.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);

        $indexfirst = 11; $indexlast = 11;     // Selection '+' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);

        $indexfirst = 28; $indexlast = 28;     // Selection '|' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment($indexfirst, $indexlast, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);
    }
}
