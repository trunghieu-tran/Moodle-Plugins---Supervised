<?php

/**
 * Unit tests for question/type/preg/preg_nodes.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>, Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');

class qtype_preg_nodes_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_handling_options();
            $options->preserveallnodes = true;
        }
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        return $lexer;
    }

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

    function test_anchoring() {
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
    }

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
        $node->expand(0, 2, $idcounter, true);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 3);
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 2);
        $node = $node->operands[0];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && $node->position->indfirst == 0 && $node->position->indlast == 1);
    }

    function test_expand_emptiness() {
        $handler = new qtype_preg_regex_handler("a|b|");
        $idcounter = 1000;

        $root = $handler->get_ast_root();
        $root->expand(1, 2, $idcounter);
        $this->assertTrue($root->operands[1]->operands[0]->position->indfirst == 2 && $root->operands[1]->operands[0]->position->indlast == 2);
        $this->assertTrue($root->operands[1]->operands[1]->position->indfirst == 4 && $root->operands[1]->operands[1]->position->indlast == 3);
        $this->assertTrue($root->operands[1]->position->indfirst == 2 && $root->operands[1]->position->indlast == 3);
    }

    function test_node_by_regex_fragment_one_char() {
        $handler = new qtype_preg_regex_handler("a");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 0, $idcounter);
        $this->assertTrue($node === $root);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 11, $idcounter);
        $this->assertTrue($node === null);
    }

    function test_node_by_regex_fragment_concat() {
        $handler = new qtype_preg_regex_handler("abcd");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 1, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2 && $node->operands[0]->flags[0][0]->data == 'a' && $node->operands[1]->flags[0][0]->data == 'b');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 2, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 3 &&
                          $node->operands[0]->flags[0][0]->data == 'a' && $node->operands[2]->flags[0][0]->data == 'c');
    }

    function test_node_by_regex_fragment_concat_subpatt_quant() {
        $handler = new qtype_preg_regex_handler("(abcd)+");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 5, $idcounter);
        $this->assertTrue($node === $root->operands[0]);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 4, $idcounter);
        $this->assertTrue($node === $root->operands[0]);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(2, 6, $idcounter);
        $this->assertTrue($node === $root);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(1, 4, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 4 &&
                          $node->operands[0]->flags[0][0]->data == 'a' && $node->operands[3]->flags[0][0]->data == 'd');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(2, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($node->operands[1]->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->operands[1]->flags[0][0]->data == 'c');
    }

    function test_node_by_regex_fragment_alt() {
        $handler = new qtype_preg_regex_handler("ab|cde");
        $idcounter = 1000;

        // Exact selection: 'c'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(3, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        // Exact selection: 'cd'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(3, 4, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        // Exact selection: 'ab|cde'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 5, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && count($node->operands) == 2);
        $this->assertTrue($node->operands[0]->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands[0]->operands) == 2);
        $this->assertTrue($node->operands[1]->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands[1]->operands) == 3);

        // Selection to be expanded: 'b|c'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(1, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && count($node->operands) == 2);
        $node = $node->operands[1];
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT && count($node->operands) == 3 && $node->operands[2]->flags[0][0]->data == 'e');
    }

    function test_node_by_regex_fragment_multiline() {
        $handler = new qtype_preg_regex_handler("ab|d\n(abcd)+\nqwe(?#comment\n)|alt");
        $idcounter = 1000;

        // Exact selection 'b'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 1, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        // Exact selection 'b'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(7, 7, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        // Exact selection 't'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(30, 30, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET);

        // Exact selection 'alt'.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(28, 30, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);

        // Selection 'qwe' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(13, 15, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_CONCAT);

        // Comment selection, should be expanded to the whole alternation.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(19, 25, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);

        // Selection '+' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(11, 11, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);

        // Selection '|' to be expanded.
        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(28, 28, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT);
    }

    function test_node_by_regex_fragment_emptiness() {
        $handler = new qtype_preg_regex_handler("a|b|");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(3, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && $node->position->indfirst == 2 && $node->position->indlast == 3);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($node->operands[1]->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(2, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && $node->position->indfirst == 2 && $node->position->indlast == 3);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($node->operands[1]->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY);

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(1, 1, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && $node->position->indfirst == 0 && $node->position->indlast == 2);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'a');
        $this->assertTrue($node->operands[1]->flags[0][0]->data == 'b');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(4, 3, $idcounter);
        $this->assertTrue($node->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY && $node->position->indfirst == 4 && $node->position->indlast == 3);

        $handler = new qtype_preg_regex_handler("|a|b");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, -1, $idcounter);
        $this->assertTrue($node->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY && $node->position->indfirst == 0 && $node->position->indlast == -1);
    }

    function test_node_by_regex_fragment_whitespaces() {
        $handler = new qtype_preg_regex_handler("a  \t");
        $idcounter = 1000;

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(0, 0, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->position->indfirst == 0 && $node->position->indlast == 0);
        $this->assertTrue($node->flags[0][0]->data == 'a');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(1, 1, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->position->indfirst == 1 && $node->position->indlast == 1);
        $this->assertTrue($node->flags[0][0]->data == ' ');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(2, 2, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->position->indfirst == 2 && $node->position->indlast == 2);
        $this->assertTrue($node->flags[0][0]->data == ' ');

        $root = clone $handler->get_ast_root();
        $node = $root->node_by_regex_fragment(3, 3, $idcounter);
        $this->assertTrue($node->type == qtype_preg_node::TYPE_LEAF_CHARSET && $node->position->indfirst == 3 && $node->position->indlast == 3);
        $this->assertTrue($node->flags[0][0]->data == "\t");
    }

    function test_selection_as_option() {
        $options = new qtype_preg_handling_options();
        $options->selection = new qtype_preg_position(3, 3);
        $handler = new qtype_preg_regex_handler("a|b|", $options);
        $node = $handler->get_selected_node();
        $this->assertTrue($node->type == qtype_preg_node::TYPE_NODE_ALT && $node->position->indfirst == 2 && $node->position->indlast == 3);
        $this->assertTrue($node->operands[0]->flags[0][0]->data == 'b');
        $this->assertTrue($node->operands[1]->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    }

/***************************************** Tests for matching and next character generation *****************************************/

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
        $str = new qtype_poasquestion_string('abc');
        $res = $backref->match($str, 3, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 3, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 0);
        $this->assertEquals($ch, 'abc');
        // The string doesn't match backref at all.
        $str = new qtype_poasquestion_string('abcdef');
        $res = $backref->match($str, 3, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 3, $length, $matcher->get_match_results());
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
        $str = new qtype_poasquestion_string('abcab');
        $res = $backref->match($str, 3, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 3, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 2);
        $this->assertEquals($ch, 'c');
        // The string matches backref partially.
        $str = new qtype_poasquestion_string('abcacd');
        $res = $backref->match($str, 3, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 4, $length, $matcher->get_match_results());
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

        $str = new qtype_poasquestion_string('abcabc');
        $res = $backref->match($str, 3, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 3, $length, $matcher->get_match_results());
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

        $str = new qtype_poasquestion_string('');
        $res = $backref->match($str, 0, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 0, $length, $matcher->get_match_results());
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
        $str = new qtype_poasquestion_string('aba');
        $res = $backref->match($str, 2, $length, $matcher->get_match_results());
        list($flag, $ch) = $backref->next_character($str, $str, 3, $length, $matcher->get_match_results());
        $this->assertFalse($res);
        $this->assertEquals($length, 1);
        $this->assertEquals($ch, 'b');
        // Emptiness matched.
        $matcher->match('');
        $str = new qtype_poasquestion_string('');
        $res = $backref->match($str, 0, $length, $matcher->get_match_results());
        $this->assertTrue($res);
        $this->assertEquals($length, 0);
    }

    function test_match_string_ends() {
        $str = new qtype_poasquestion_string("a\n");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        $a = $leaf->match($str, $pos, $length);
        $this->assertTrue($a, 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 1, 'Return length is not equal to expected');
    }

    function test_match_character_with_circumflex() {
        $str = new qtype_poasquestion_string("ab\n");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 0;
        $this->assertFalse($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_string_ends_dollar_assert() {
        $str = new qtype_poasquestion_string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 2;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 1, 'Return length is not equal to expected');
    }

    function test_match_character_with_dollar() {
        $str = new qtype_poasquestion_string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 2;
        $this->assertFalse($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_one_string() {
        $str = new qtype_poasquestion_string("ab");
        $length = 0;
        $lexer = $this->create_lexer("[a]");
        $leaf = $lexer->nextToken()->value;
        $pos = 0;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 1, 'Return length is not equal to expected');
    }

    function test_match_single_assert() {
        $str = new qtype_poasquestion_string("ab\na\nas");
        $length = 0;
        $leaf= new qtype_preg_leaf_assert_circumflex;
        $pos = 0;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_before_and_after_asserts_true() {
        $str = new qtype_poasquestion_string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 2;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 1, 'Return length is not equal to expected');
    }

    function test_match_before_and_after_asserts_false() {
        $str = new qtype_poasquestion_string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 2;
        $this->assertFalse($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_empty_string_true() {
        $str = new qtype_poasquestion_string("ab\n\nas");
        $length = 0;
        $lexer = $this->create_lexer("[a-z\n]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 3;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 1, 'Return length is not equal to expected');
    }

    function test_match_empty_string_false() {
        $str = new qtype_poasquestion_string("ab\n\nas");
        $length = 0;
        $lexer = $this->create_lexer("[a-z]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 3;
        $this->assertFalse($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_single_dollar_in_the_end() {
        $str = new qtype_poasquestion_string("ab\n\nas");
        $length = 0;
        $leaf = new qtype_preg_leaf_assert_dollar;
        $pos = 6;
        $this->assertTrue($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_match_middle_of_the_string() {
        $str = new qtype_poasquestion_string("bcd");
        $length = 0;
        $lexer = $this->create_lexer("[a-c\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        $this->assertFalse($leaf->match($str, $pos, $length), 'Return boolean flag is not equal to expected');
        $this->assertEquals($length, 0, 'Return length is not equal to expected');
    }

    function test_generation_empty_string() {
        $str = new qtype_poasquestion_string("ax");
        $length = 1;
        $lexer = $this->create_lexer("[ab\n\\x1]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n", 'Return character is not equal to expected');
    }

    function test_generation_string_ends_false() {
        $str = new qtype_poasquestion_string("b\n");
        $length = 1;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, 'Return character is not equal to expected');
    }

    function test_generation_string_ends_dollar_assert() {
        $str = new qtype_poasquestion_string("bx\na\nas");
        $length = 2;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 2;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n", 'Return character is not equal to expected');
    }

    function test_generation_character_with_dollar() {
        $str = new qtype_poasquestion_string("b\na\nas");
        $length = 1;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, 'Return character is not equal to expected');
    }

    function test_generation_one_string() {
        $str = new qtype_poasquestion_string("ab");
        $length = 1;
        $lexer = $this->create_lexer("[x-z]");
        $leaf = $lexer->nextToken()->value;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, 'x', 'Return character is not equal to expected');
    }

    function test_generation_single_assert() {
        $str = new qtype_poasquestion_string("\n\nas");
        $length = 0;
        $leaf = new qtype_preg_leaf_assert_circumflex;
        $pos = 0;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, '', 'Return character is not equal to expected');
    }

    function test_generation_before_and_after_asserts_false() {
        $str = new qtype_poasquestion_string("a\na\nas");
        $length = 1;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, 'Return character is not equal to expected');
    }

    function test_generation_before_and_after_asserts_true() {
        $str = new qtype_poasquestion_string("abcd\nas");
        $length = 1;
        $lexer = $this->create_lexer("[a-z\n]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n", 'Return character is not equal to expected');
    }

    function test_generation_single_dollar_in_the_end() {
        $str = new qtype_poasquestion_string("as");
        $length = 2;
        $leaf = new qtype_preg_leaf_assert_dollar;
        $pos = 2;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_END_HERE, 'Return character is not equal to expected');
    }

    function test_generation_middle_of_the_string() {
        $str = new qtype_poasquestion_string("bcd");
        $length = 1;
        $lexer = $this->create_lexer("[c\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n", 'Return character is not equal to expected');
    }

    function test_generation_last_character() {
        $str = new qtype_poasquestion_string("a\n");
        $length = 1;
        $lexer = $this->create_lexer("[\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_capital_esc_z;
        $leaf->assertionsbefore[] = $assert;
        $pos = 1;
        list($flag, $ch) = $leaf->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n", 'Return character is not equal to expected');
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_END_HERE, 'Return flag is not equal to expected');
    }

/***************************************** Tests for charset *****************************************/

    function test_charflag_set_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('asdf0123'));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 4, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 5, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 4, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 5, true));
    }

    function test_charflag_flag_d_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_DIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
    }

    function test_charflag_flag_xdigit_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_XDIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
    }

    function test_charflag_flag_s_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_SPACE);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 4, true));
    }

    function test_charflag_flag_w_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }

    function test_charflag_flag_alnum_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALNUM);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }

    function test_charflag_flag_alpha_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALPHA);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }

    function test_charflag_flag_ascii_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ASCII);
        $str = new qtype_poasquestion_string(qtype_preg_unicode::code2utf8(17).qtype_preg_unicode::code2utf8(78).qtype_preg_unicode::code2utf8(130).qtype_preg_unicode::code2utf8(131).qtype_preg_unicode::code2utf8(200));
        $this->assertTrue($flag->match($str, 0, true));
        $this->assertTrue($flag->match($str, 1, true));
        $this->assertFalse($flag->match($str, 2, true));
        $this->assertFalse($flag->match($str, 3, true));
        $this->assertFalse($flag->match($str, 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match($str, 0, true));
        $this->assertFalse($flag->match($str, 1, true));
        $this->assertTrue($flag->match($str, 2, true));
        $this->assertTrue($flag->match($str, 3, true));
        $this->assertTrue($flag->match($str, 4, true));
    }

    function test_charflag_flag_graph_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_GRAPH);
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 4, true));
    }

    function test_charflag_flag_lower_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_LOWER);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1, false));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3, false));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1, false));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3, false));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
    }

    function test_charflag_flag_upper_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_UPPER);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0, false));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4, false));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0, false));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4, false));
    }

    function test_charflag_flag_print_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PRINT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 4, true));
    }

    function test_charflag_flag_punct_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PUNCT);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 4, true));
    }

    function test_charflag_flag_cntrl_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_CNTRL);
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 4, true));
    }

    function test_charset_match() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        //form charsets
        $charset = new qtype_preg_leaf_charset;
        $charset->flags[0][0] = $a;
        $charset->flags[1][0] = $b;
        $charset->flags[1][1] = $c;
        $this->assertTrue($charset->match(new qtype_poasquestion_string('bs@'), 0, $l));
        $this->assertFalse($charset->match(new qtype_poasquestion_string('bs@'), 1, $l));
        $this->assertTrue($charset->match(new qtype_poasquestion_string('bs@'), 2, $l));
    }

    function test_charset_next() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        //form charsets
        $charset = new qtype_preg_leaf_charset;
        $charset->flags[0][0] = $a;
        $charset->flags[1][0] = $b;
        $charset->flags[1][1] = $c;
        $str = new qtype_poasquestion_string('');
        list($flag, $ch) = $charset->next_character($str, $str, 0);
        $this->assertTrue(strlen($ch)==1, 'Not one character got by next_character()!');
        $this->assertTrue($charset->match($ch, 0, $l), 'Next character is unmatched!');
    }

    /*function test_charset_intersect() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $d = new qtype_preg_charset_flag;
        $e = new qtype_preg_charset_flag;
        $f = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%)'));
        //form charsets
        $charset1 = new qtype_preg_leaf_charset;
        $charset1->flags[0][0] = $a;
        $charset1->flags[1][0] = $b;
        $charset1->flags[1][1] = $c;
        $charset2 = new qtype_preg_leaf_charset;
        $charset2->flags[0][0] = $d;
        $charset2->flags[0][1] = $e;
        $charset2->flags[1][0] = $f;
        //intersect them
        $result = $charset1->intersect($charset2);
        //verify result
        $this->assertTrue(count($result->flags)==3, 'Incorrect count of disjunct in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[0])==1, 'Incorrect count of flags in first disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[1])==1, 'Incorrect count of flags in second disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[2])==1, 'Incorrect count of flags in third disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead first set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[1][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[2][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[0][0]->negative, 'First set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[1][0]->negative, 'Second set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[2][0]->negative, 'Second set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='(' || $result->flags[1][0]->set=='(' || $result->flags[2][0]->set=='(', '\'(\' not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='b%' || $result->flags[1][0]->set=='b%' || $result->flags[2][0]->set=='b%%', '\"b%\" not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='b' || $result->flags[1][0]->set=='b' || $result->flags[2][0]->set=='b', '\"b\" not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 0, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 1, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(b@%)'), 2, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 3, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(b@%)'), 4, $l, true), 'Incorrect matching');
    }

    function test_charset_substract() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $d = new qtype_preg_charset_flag;
        $e = new qtype_preg_charset_flag;
        $f = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%)'));
        //form charsets
        $charset1 = new qtype_preg_leaf_charset;
        $charset1->flags[0][0] = $a;
        $charset1->flags[1][0] = $b;
        $charset1->flags[1][1] = $c;
        $charset1->negative = true;
        $charset2 = new qtype_preg_leaf_charset;
        $charset2->flags[0][0] = $d;
        $charset2->flags[0][1] = $e;
        $charset2->flags[1][0] = $f;
        $charset2->negative = false;
        //intersect them
        $result = $charset1->substract($charset2);
        //verify result
        $this->assertTrue(count($result->flags)==1, 'Incorrect count of disjunct in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[0])==1, 'Incorrect count of flags in first disjunct of  substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead first set in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[0][0]->negative, 'First set is negative  in substraction of ^[b%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='s', '\"s\" not exist in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 0, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 1, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(bs%)'), 2, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 3, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 4, $l, true), 'Incorrect matching');
    }*/
}
