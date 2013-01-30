<?php

/**
 * Unit tests for question/type/preg/preg_lexer.lex.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>, Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

class qtype_preg_lexer_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        return new qtype_preg_lexer($pseudofile);
    }

    function test_lexer_quantifiers() {
        $lexer = $this->create_lexer('?*++{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}+');
        $token = $lexer->nextToken();// ?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// *+
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue($token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// *?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// ??
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {1,5}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
    }
    function test_lexer_tricky_brackets() {
        $lexer = $this->create_lexer('a{1,2}{}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 2);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == '{');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == '}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == ']');
    }
    function test_lexer_backslash() {
        $lexer = $this->create_lexer('\\\\\\*\\[\23\9\023\x\x23\x{7ff}\d\s\t\b\B\>\<\%((((((((((((\g15\12\g{15}\g{-2}\a\e\f\n\r\cz\c{\c;\u3f1\U\p{Greek}\P{Lt}\P{^M}\PL[ab\p{Xps}]\p{Xwd}');
        $token = $lexer->nextToken();// \\
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == '\\');
        $token = $lexer->nextToken();// \*
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '*');
        $token = $lexer->nextToken();// \[
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '[');
        $token = $lexer->nextToken();// \23
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);    // No subpatterns before this token.
        $token = $lexer->nextToken();// \9
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);    // Backref to the 9th subpattern.
        $this->assertTrue($token->value->number == 9);
        $token = $lexer->nextToken();// \023
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) == 023);
        $token = $lexer->nextToken();// \x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();// \x23
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) == 0x23);
        $token = $lexer->nextToken();// \x{7ff}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) == 0x7ff);
        $token = $lexer->nextToken();// \d
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data == qtype_preg_charset_flag::DIGIT);
        $token = $lexer->nextToken();// \s
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data == qtype_preg_charset_flag::SPACE);
        $token = $lexer->nextToken();// \t
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x09));
        $token = $lexer->nextToken();// \b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue(!$token->value->negative);
        $token = $lexer->nextToken();// \B
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue($token->value->negative);
        $token = $lexer->nextToken();// \>
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '>');
        $token = $lexer->nextToken();// \<
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '<');
        $token = $lexer->nextToken();// \%
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '%');
        for ($i = 0; $i < 12; $i++) {
            $lexer->nextToken();// skip 12 subpatterns
        }
        $token = $lexer->nextToken();// \g15
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \12
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 12);
        $token = $lexer->nextToken();// \g{15}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \g{-2}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $token = $lexer->nextToken();// \a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x07));
        $token = $lexer->nextToken();// \e
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x1B));
        $token = $lexer->nextToken();// \f
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0C));
        $token = $lexer->nextToken();// \n
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0A));
        $token = $lexer->nextToken();// \r
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0D));
        $token = $lexer->nextToken();// \cz
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x3A));
        $token = $lexer->nextToken();// \c{
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x3B));
        $token = $lexer->nextToken();// \c;
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x7B));
        $token = $lexer->nextToken();// \u
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();// 3
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3');
        $token = $lexer->nextToken();// f
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'f');
        $token = $lexer->nextToken();// 1
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '1');
        $token = $lexer->nextToken();// \U
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();// \p{Greek}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::GREEK);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{Lt}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPLT);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{^M}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPM);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \PL
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPL);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// [ab\p{Xps}]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == qtype_preg_charset_flag::UPROPXPS);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data == 'ab');
        $this->assertFalse($token->value->flags[1][0]->negative);
        $token = $lexer->nextToken();// \p{Xwd}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPXWD);
        $this->assertFalse($token->value->flags[0][0]->negative);
    }
    function test_lexer_named_backref() {
        $lexer = $this->create_lexer('\\k<name_1>\\k\'name_2\'\\k{name_3}\\g{name_4}(?P=name_5)');
        for ($i = 0; $i < 5; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
            $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
            $this->assertTrue($token->value->number == 'name_' . ($i + 1));
        }
    }
    function test_lexer_tricky_backref() {
        $lexer = $this->create_lexer('\\040\\40\\7\\11(((((((((((\\11\\0113\\81\\378');
        $token = $lexer->nextToken();// \040
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(40)));
        $token = $lexer->nextToken();// \40
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(40)));
        $token = $lexer->nextToken();// \7
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 7);
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(11)));
        for ($i = 0; $i < 11; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
            $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        }
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $token = $lexer->nextToken();// \0113
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(11)));
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3');
        $token = $lexer->nextToken();// \81 - binary zero followed by '8' and '1'
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data == chr(0x00));
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data == '8');
        $this->assertTrue($token[2]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[2]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[2]->value->flags[0][0]->data == '1');
        $token = $lexer->nextToken();// \378 - chr(octal(37)) followed by '8'
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data == chr(octdec(37)));
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data == '8');
    }
    function test_lexer_named_subpatterns_and_backreferences() {
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'))(?P<rty>)\k<qwe>\k'qwe'\g{qwe}\k{rty}(?P=rty)");
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'rty');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'rty');
        $map = $lexer->get_subpattern_map();
        $this->assertTrue(count($map) === 2);
        $this->assertTrue(array_key_exists('qwe', $map) && $map['qwe'] === 1);
        $this->assertTrue(array_key_exists('rty', $map) && $map['rty'] === 2);
    }
    function test_lexer_lexems() {
        $lexer = $this->create_lexer('(?#this should be skipped)');
        $token = $lexer->nextToken();    // (?#{{)
        $this->assertTrue($token === null);
    }
    function test_lexer_charclass() {
        $lexer = $this->create_lexer('[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d0-9][3-6][^\x61-\x{63}][^-\w\D][\Q][?\E][]a][^]a]');
        $token = $lexer->nextToken();// [a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $token = $lexer->nextToken();// [abc]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'abc');
        $token = $lexer->nextToken();// [ab{]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab{');
        $token = $lexer->nextToken();// [ab\\]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab\\');
        $token = $lexer->nextToken();// [ab\]]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab]');
        $token = $lexer->nextToken();// [a\db]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::DIGIT);
        $this->assertTrue($token->value->flags[1][0]->data == 'ab');
        $token = $lexer->nextToken();// [a-d]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'abcd0123456789');
        $token = $lexer->nextToken();// [3-6]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3456');
        $token = $lexer->nextToken();// [\x61-\x{63}]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == qtype_poasquestion_string::code2utf8(0x61).qtype_poasquestion_string::code2utf8(0x62).qtype_poasquestion_string::code2utf8(0x63));
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// [^-\w\D]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::WORD);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data === qtype_preg_charset_flag::DIGIT);
        $this->assertTrue($token->value->flags[1][0]->negative);
        $this->assertTrue($token->value->flags[2][0]->data == '-');
        $this->assertFalse($token->value->flags[2][0]->negative);
        $token = $lexer->nextToken();// [\Q][?\E]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '][?');
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// []a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == ']a');
        $this->assertFalse($token->value->negative);
        $token = $lexer->nextToken();// [^]a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == ']a');
        $this->assertTrue($token->value->negative);
    }
    function test_lexer_few_number_in_quant() {
        $lexer = $this->create_lexer('{135,12755139}{135,}{,12755139}{135}');
        $token = $lexer->nextToken();// {135,12755139}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135,}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {,12755139}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 135);
        $this->assertTrue($token->value->greed);
    }
    function test_lexer_anchors() {
        $lexer = $this->create_lexer('^a|b$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_lexer_asserts() {
        $lexer = $this->create_lexer('(?=(?!(?<=(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_NLB);
    }
    function test_lexer_metasymbol_dot() {
        $lexer = $this->create_lexer('.');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::PRIN);
    }
    function test_lexer_subpatterns() {
        $lexer = $this->create_lexer('((?:(?>(?(?=(?(?!(?(?<=(?(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB);
        $lexer = $this->create_lexer('((?(123)(?(+1)(?(-1)(?(<name_1>)(?(\'name_2\')(?(name_3)(?(R)(?(R4)(?(R&name_4)(?(DEFINE)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[0]->value->number === 123);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[0]->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[0]->value->number === 1);
        for ($i = 0; $i < 3; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
            $this->assertTrue($token[0]->value->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
            $this->assertTrue($token[0]->value->number == 'name_' . ($i + 1));
        }
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 0);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 'name_4');
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE);

    }
    function test_lexer_subpatterns_nested() {
        $lexer = $this->create_lexer('((?:(?>()(');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
    }
    function test_lexer_duplicate_subpattern_numbers_from_pcre() {
        $lexer = $this->create_lexer('(a)(?|x(y)z|(p(q)r)|(t)u(v))(z)');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'y');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'z');
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // p
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'p');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // q
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'q');
        $token = $lexer->nextToken();    //)
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // r
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'r');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 't');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // u
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'u');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // v
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'v');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'z');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_lexer_duplicate_subpattern_numbers_nested() {
        $lexer = $this->create_lexer('()(?|()|()(?|()|(()))|())()');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 5);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_lexer_recursion() {
        $lexer = $this->create_lexer('(?R)(?14)');
        $token = $lexer->nextToken();// (?R)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==0);
        $token = $lexer->nextToken();// (?14)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==14);
    }
    function test_lexer_options() {
        $lexer = $this->create_lexer('a(?i)b(c(?-i)d)e');
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_options2() {
        $lexer = $this->create_lexer('(?i:a(?-i:b)c)');
        $token = $lexer->nextToken();// (?i:
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (?-i:
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_global_options() {
        $lexer = $this->create_lexer('ab(?-i:cd)e');
        $lexer->mod_top_opt(new qtype_poasquestion_string('i'), new qtype_poasquestion_string(''));
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_index() {
        $lexer = $this->create_lexer('ab{12,57}[abc]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst == 0);
        $this->assertTrue($token->value->indlast == 0);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst == 1);
        $this->assertTrue($token->value->indlast == 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst == 2);
        $this->assertTrue($token->value->indlast == 8);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst == 9);
        $this->assertTrue($token->value->indlast == 13);
    }
    function test_lexer_unicode() {
        $lexer = $this->create_lexer('^айёàéه(者)$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'а');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'й');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ё');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'à');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'é');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ه');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '者');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_lexer_qe() {
        $lexer = $this->create_lexer('\\Q');
        $token = $lexer->nextToken();// \Q
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) === 0);
        $lexer = $this->create_lexer('\\Q\\Ex{3,10}');
        $token = $lexer->nextToken();// \Q\E
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) === 0);
        $token = $lexer->nextToken();// x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();// {3,10}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 3);
        $this->assertTrue($token->value->rightborder === 10);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $lexer = $this->create_lexer('\\Qt@$t\\Es+');
        $token = $lexer->nextToken();// \Qt@$t\E
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) === 4);
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data == 't');
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data == '@');
        $this->assertTrue($token[2]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[2]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[2]->value->flags[0][0]->data == '$');
        $this->assertTrue($token[3]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[3]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[3]->value->flags[0][0]->data == 't');
        $token = $lexer->nextToken();// s
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 's');
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
    }
    function test_lexer_control_sequences() {
        $lexer = $this->create_lexer('(*ACCEPT)(*FAIL)(*F)(*MARK:NAME0)(*:NAME1)(*COMMIT)(*PRUNE)(*PRUNE:NAME2)(*SKIP)(*SKIP:NAME3)(*THEN)(*THEN:NAME4)(*CR)(*LF)(*CRLF)(*ANYCRLF)(*ANY)(*BSR_ANYCRLF)(*BSR_UNICODE)(*NO_START_OPT)(*UTF8)(*UTF16)(*UCP)(*SQUIRREL)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CONTROL);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
        $this->assertTrue($token->value->name === 'NAME0');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
        $this->assertTrue($token->value->name === 'NAME1');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE);
        $token = $lexer->nextToken();
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
        $this->assertTrue($token[0]->value->name === 'NAME2');
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME);
        $this->assertTrue($token->value->name === 'NAME3');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_THEN);
        $token = $lexer->nextToken();
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
        $this->assertTrue($token[0]->value->name === 'NAME4');
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->subtype === qtype_preg_leaf_control::SUBTYPE_THEN);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_CR);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_LF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ANY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_BSR_ANYCRLF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_BSR_UNICODE);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UTF16);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UCP);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($token->value->indfirst === 210);
        $this->assertTrue($token->value->indlast === 220);
    }
    function test_lexer_errors() {
        $lexer = $this->create_lexer('\p{C}[a\p{Squirrel}b]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPC);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab');
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->error[0]->indfirst === 7);
        $this->assertTrue($token->value->error[0]->indlast === 18);
        $lexer = $this->create_lexer('[[:alpha:]][[:^cntrl:]][[:nut:]]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::ALPHA);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::CNTRL);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($token->value->error[0]->indfirst === 24);
        $this->assertTrue($token->value->error[0]->indlast === 30);
        $lexer = $this->create_lexer('[0-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[z-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[a-0]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE);
        $this->assertTrue($token->value->error[0]->indfirst === 1);
        $this->assertTrue($token->value->error[0]->indlast === 3);
        $lexer = $this->create_lexer('{2,2}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $lexer = $this->create_lexer('{127,11}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->error->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($token->value->error->indfirst === 1);
        $this->assertTrue($token->value->error->indlast === 6);
        $lexer = $this->create_lexer('\p{b}[\pB][[:c:]]{4,3}+[^az-yb]\pO[\p{4}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 5);
        $this->assertTrue($token->value->indlast === 9);
        $this->assertTrue($token->value->userinscription[0]->data === '\pB');
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->error[0]->indfirst === 6);
        $this->assertTrue($token->value->error[0]->indlast === 8);
        $this->assertTrue($token->value->error[0]->addinfo === 'B');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 10);
        $this->assertTrue($token->value->indlast === 16);
        $this->assertTrue($token->value->userinscription[0]->data === '[:c:]');
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($token->value->error[0]->indfirst === 11);
        $this->assertTrue($token->value->error[0]->indlast === 15);
        $this->assertTrue($token->value->error[0]->addinfo === '[:c:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->indfirst === 17);
        $this->assertTrue($token->value->indlast === 22);
        $this->assertTrue($token->value->userinscription->data === '{4,3}+');
        $this->assertTrue($token->value->error->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($token->value->error->indfirst === 18);
        $this->assertTrue($token->value->error->indlast === 20);
        $this->assertTrue($token->value->error->addinfo === '4,3');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 23);
        $this->assertTrue($token->value->indlast === 30);
        $this->assertTrue($token->value->userinscription[0]->data === 'ab');
        $this->assertTrue($token->value->userinscription[1]->data === 'z-y');
        $this->assertTrue($token->value->flags[0][0]->data == 'ab');
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE);
        $this->assertTrue($token->value->error[0]->indfirst === 26);
        $this->assertTrue($token->value->error[0]->indlast === 28);
        $this->assertTrue($token->value->error[0]->addinfo === 'z-y');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->indfirst === 31);
        $this->assertTrue($token->value->indlast === 33);
        $this->assertTrue($token->value->addinfo === 'O');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 34);
        $this->assertTrue($token->value->indlast === 40);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{4}');
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->error[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->error[0]->indfirst === 35);
        $this->assertTrue($token->value->error[0]->indlast === 39);
        $this->assertTrue($token->value->error[0]->addinfo === '4');
        $lexer = $this->create_lexer('(?i-i)(?z-z:[bc');
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type == qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_MODIFIER);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET);
        $this->assertTrue($errors[0]->indfirst === 12);
        $this->assertTrue($errors[0]->indlast === 14);
        $lexer = $this->create_lexer('a\\');
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN);
        $this->assertTrue($token->value->indfirst === 1);
        $this->assertTrue($token->value->indlast === 1);
        $lexer = $this->create_lexer('b\\ca[:^alpha:]\\c');
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET);
        $this->assertTrue($token->value->indfirst === 4);
        $this->assertTrue($token->value->indlast === 13);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN);
        $this->assertTrue($token->value->indfirst === 14);
        $this->assertTrue($token->value->indlast === 15);
        $lexer = $this->create_lexer('(?#comment here)\x{FFFFFFFF}(?#comment without closing paren');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG);
        $this->assertTrue($token->value->indfirst === 16);
        $this->assertTrue($token->value->indlast === 27);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING);
        $this->assertTrue($token->value->indfirst === 28);
        $this->assertTrue($token->value->indlast === 59);
        $lexer = $this->create_lexer('(?(0)(?C255(?Pn(?<name1(?\'name2(?P<name3\g0(?<>(?\'\'(?P<>\g{}\k<>\k\'\'\k{}(?P=)\cй');
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_CONSUBPATT_ZERO_CONDITION);
        $this->assertTrue($token[1]->value->indfirst === 0);
        $this->assertTrue($token[1]->value->indlast === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING);
        $this->assertTrue($token->value->indfirst === 5);
        $this->assertTrue($token->value->indlast === 10);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $this->assertTrue($token->value->indfirst === 11);
        $this->assertTrue($token->value->indlast === 14);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $this->assertTrue($token->value->indfirst === 15);
        $this->assertTrue($token->value->indlast === 22);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $this->assertTrue($token->value->indfirst === 23);
        $this->assertTrue($token->value->indlast === 30);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $this->assertTrue($token->value->indfirst === 31);
        $this->assertTrue($token->value->indlast === 39);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_BACKREF_TO_ZERO);
        $this->assertTrue($token->value->indfirst === 40);
        $this->assertTrue($token->value->indlast === 42);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 43);
        $this->assertTrue($token->value->indlast === 46);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 47);
        $this->assertTrue($token->value->indlast === 50);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 51);
        $this->assertTrue($token->value->indlast === 55);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 56);
        $this->assertTrue($token->value->indlast === 59);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 60);
        $this->assertTrue($token->value->indlast === 63);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 64);
        $this->assertTrue($token->value->indlast === 67);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 68);
        $this->assertTrue($token->value->indlast === 71);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 72);
        $this->assertTrue($token->value->indlast === 76);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII);
        $this->assertTrue($token->value->indfirst === 77);
        $this->assertTrue($token->value->indlast === 79);
        $lexer = $this->create_lexer('(*MARK:)(*:)(?(R&)(?(<>)(?(\'\')(?()');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype == qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBPATT);
        $this->assertTrue($token[0]->value->subtype == null);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_CONDSUBPATT_ASSERT_EXPECTED);
        $lexer = $this->create_lexer('(?(R&(?(<(?(\'(?((?(Rd)');
        $token = $lexer->nextToken();   // (?(R&
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING);
        $token = $lexer->nextToken();   // (?(<
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING);
        $token = $lexer->nextToken();   // (?('
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING);
        $token = $lexer->nextToken();   // (?(
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING);
        $token = $lexer->nextToken();   // (?(Rd)
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER);
        $lexer = $this->create_lexer('(?<namebody(?\'namebody(?P<namebody');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING);
        $lexer = $this->create_lexer('\gA}\kA}\kA\'\kA>(?PA)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING);
        $lexer = $this->create_lexer('\g{A\k{A\k\'A\k<A(?P=A');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING);
        $lexer = $this->create_lexer('\L\l\U\u\N{abracadabra}[\L\l\U\u\N{abracadabra}]\m');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->error[0]->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->error[1]->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->error[2]->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->error[3]->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->error[4]->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'm');
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'(?'rty'(?'abc')))|(?'uio')");      // (?P<rty>)\k<qwe>\k'qwe'\g{qwe}\k{rty}(?P=rty)
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?'qwe'
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // (?'rty'
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // (?'abc'
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?'uio')
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBPATT_NAMES);
        $lexer = $this->create_lexer('(?P<name>(?<name>(?\'name\'(?<(');
        $token = $lexer->nextToken();    // (?P<name>
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // (?<name>
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBPATT_NAMES);
        $token = $lexer->nextToken();    // (?'name'
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBPATT_NAMES);
        $token = $lexer->nextToken();    // (?<(
        $this->assertTrue($token->type == qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_LBA);
    }
    function test_lexer_userinscription() {
        $lexer = $this->create_lexer('a\p{L}\x{ab}[\pCab-de\x00-\xff[:alpha:]]\p{Squirrel}[\p{Squirrel}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{L}');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === '\x{ab}');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === 'ae');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[1]->data === '\pC');
        $this->assertTrue($token->value->userinscription[1]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $this->assertTrue($token->value->userinscription[2]->data === 'b-d');
        $this->assertTrue($token->value->userinscription[3]->data === '\x00-\xff');
        $this->assertTrue($token->value->userinscription[4]->data === '[:alpha:]');
        $this->assertTrue($token->value->userinscription[4]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_ERROR);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{Squirrel}');

        $lexer = $this->create_lexer('[\xff\x00-\x1fA-B\t\n]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->userinscription[0]->data === '\xff\t\n');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[1]->data === '\x00-\x1f');
        $this->assertTrue($token->value->userinscription[1]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[2]->data === 'A-B');
        $this->assertTrue($token->value->userinscription[2]->type === qtype_preg_userinscription::TYPE_GENERAL);
    }
}
