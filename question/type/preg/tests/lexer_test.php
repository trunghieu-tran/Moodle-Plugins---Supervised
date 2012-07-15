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
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');

class qtype_preg_lexer_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        return new qtype_preg_lexer($pseudofile);
    }

    function test_lexer_quantifiers() {
        $lexer = $this->create_lexer('?*++{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}+');
        $token = $lexer->nextToken();// ?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// *+
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue($token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// *?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// ??
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
    }
    function test_lexer_backslash() {
        $lexer = $this->create_lexer('\\\\\\*\\[\23\9\023\x\x23\x{7ff}\d\s\t\b\B\>\<\%((((((((((((\g15\12\g{15}\g{-2}\a\e\f\n\r\cz\c{\c;\u3f1\U\uffff\p{Greek}\P{Lt}\P{^M}\PL[ab\p{Xps}]\p{Xwd}');
        $token = $lexer->nextToken();// \\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data == '\\');
        $token = $lexer->nextToken();// \*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '*');
        $token = $lexer->nextToken();// \[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '[');
        $token = $lexer->nextToken();// \23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);    // No subpatterns before this token.
        $token = $lexer->nextToken();// \9
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);    // Backref to the 9th subpattern.
        $this->assertTrue($token->value->number == 9);
        $token = $lexer->nextToken();// \023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_preg_unicode::ord($token->value->flags[0][0]->data->string()) == 023);
        $token = $lexer->nextToken();// \x
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();// \x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_preg_unicode::ord($token->value->flags[0][0]->data->string()) == 0x23);
        $token = $lexer->nextToken();// \x{7ff}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_preg_unicode::ord($token->value->flags[0][0]->data->string()) == 0x7ff);
        $token = $lexer->nextToken();// \d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data == qtype_preg_charset_flag::DIGIT);
        $token = $lexer->nextToken();// \s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data == qtype_preg_charset_flag::SPACE);
        $token = $lexer->nextToken();// \t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x09));
        $token = $lexer->nextToken();// \b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue(!$token->value->negative);
        $token = $lexer->nextToken();// \B
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue($token->value->negative);
        $token = $lexer->nextToken();// \>
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '>');
        $token = $lexer->nextToken();// \<
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '<');
        $token = $lexer->nextToken();// \%
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == '%');
        for ($i = 0; $i < 12; $i++) {
            $lexer->nextToken();// skip 12 subpatterns
        }
        $token = $lexer->nextToken();// \g15
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \12
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 12);
        $token = $lexer->nextToken();// \g{15}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \g{-2}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $token = $lexer->nextToken();// \a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x07));
        $token = $lexer->nextToken();// \e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x1B));
        $token = $lexer->nextToken();// \f
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0C));
        $token = $lexer->nextToken();// \n
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0A));
        $token = $lexer->nextToken();// \r
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x0D));
        $token = $lexer->nextToken();// \cz
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x3A));
        $token = $lexer->nextToken();// \c{
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x3B));
        $token = $lexer->nextToken();// \c;
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(0x7B));
        $token = $lexer->nextToken();// \u
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'u');
        $token = $lexer->nextToken();// 3
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3');
        $token = $lexer->nextToken();// f
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'f');
        $token = $lexer->nextToken();// 1
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '1');
        $token = $lexer->nextToken();// \U
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'U');
        $token = $lexer->nextToken();// \uffff
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == qtype_preg_unicode::code2utf8(0xffff));
        $token = $lexer->nextToken();// \p{Greek}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::GREEK);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{Lt}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPLT);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{^M}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPM);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \PL
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPL);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// [ab\p{Xps}]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPZ);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data == 'ab'.qtype_preg_unicode::code2utf8(0x09).qtype_preg_unicode::code2utf8(0x0A).qtype_preg_unicode::code2utf8(0x0B).qtype_preg_unicode::code2utf8(0x0C).qtype_preg_unicode::code2utf8(0x0D));
        $this->assertFalse($token->value->flags[1][0]->negative);
        $token = $lexer->nextToken();// \p{Xwd}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPL);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data === qtype_preg_charset_flag::UPROPN);
        $this->assertFalse($token->value->flags[1][0]->negative);
        $this->assertTrue($token->value->flags[2][0]->data == '_');
        $this->assertFalse($token->value->flags[2][0]->negative);
    }
    function test_lexer_named_backref() {
        $lexer = $this->create_lexer('\\k<name_1>\\k\'name_2\'\\k{name_3}\\g{name_4}(?P=name_5)');
        for ($i = 0; $i < 5; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
            $this->assertTrue($token->value->number == 'name_'.($i + 1));
        }
    }
    function test_lexer_tricky_backref() {
        $lexer = $this->create_lexer('\\040\\40\\7\\11(((((((((((\\11\\0113\\81\\378');
        $token = $lexer->nextToken();// \040
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(40)));
        $token = $lexer->nextToken();// \40
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(40)));
        $token = $lexer->nextToken();// \7
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 7);
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(11)));
        for ($i = 0; $i < 11; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
            $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        }
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $token = $lexer->nextToken();// \0113
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == chr(octdec(11)));
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3');
        $token = $lexer->nextToken();// \81 - binary zero followed by '8' and '1'
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[0]->value->flags[0][0]->data == chr(0x00));
            $this->assertTrue($token[1]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[1]->value->flags[0][0]->data == '8');
            $this->assertTrue($token[2]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[2]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[2]->value->flags[0][0]->data == '1');
        }
        $token = $lexer->nextToken();// \378 - chr(octal(37)) followed by '8'
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[0]->value->flags[0][0]->data == chr(octdec(37)));
            $this->assertTrue($token[1]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[1]->value->flags[0][0]->data == '8');
        }
    }
    function test_lexer_named_subpatterns_and_backreferences() {
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'))(?P<rty>)\k<qwe>\k'qwe'\g{qwe}\k{rty}(?P=rty)");
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'rty');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
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
        $lexer = $this->create_lexer('[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d0-9][3-6][^\x61-\x{63}][^-\w\D][\Q][?\E]');
        $token = $lexer->nextToken();// [a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $token = $lexer->nextToken();// [abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'abc');
        $token = $lexer->nextToken();// [ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab{');
        $token = $lexer->nextToken();// [ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab\\');
        $token = $lexer->nextToken();// [ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ab]');
        $token = $lexer->nextToken();// [a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::DIGIT);
        $this->assertTrue($token->value->flags[1][0]->data == 'ab');
        $token = $lexer->nextToken();// [a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'abcd0123456789');
        $token = $lexer->nextToken();// [3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '3456');
        $token = $lexer->nextToken();// [\x80-\x{82}]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data == qtype_preg_unicode::code2utf8(0x61).qtype_preg_unicode::code2utf8(0x62).qtype_preg_unicode::code2utf8(0x63));
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// [^-\w\D]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::WORDCHAR);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data === qtype_preg_charset_flag::DIGIT);
        $this->assertTrue($token->value->flags[1][0]->negative);
        $this->assertTrue($token->value->flags[2][0]->data == '-');
        $this->assertFalse($token->value->flags[2][0]->negative);
        $token = $lexer->nextToken();// [\Q][?\E]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '][?');
        $this->assertFalse($token->value->flags[0][0]->negative);
    }
    function test_lexer_few_number_in_quant() {
        $lexer = $this->create_lexer('{135,12755139}{135,}{,12755139}{135}');
        $token = $lexer->nextToken();// {135,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 135);
        $this->assertTrue($token->value->greed);
    }
    function test_lexer_anchors() {
        $lexer = $this->create_lexer('^a|b$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_lexer_asserts() {
        $lexer = $this->create_lexer('(?=(?!(?<=(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_assert::SUBTYPE_NLB);
    }
    function test_lexer_metasymbol_dot() {
        $lexer = $this->create_lexer('.');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::PRIN);
    }
    function test_lexer_subpatterns() {
        $lexer = $this->create_lexer('((?:(?>(?(?=(?(?!(?(?<=(?(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB);
    }
    function test_lexer_subpatterns_nested() {
        $lexer = $this->create_lexer('((?:(?>()(');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
    }
    function test_lexer_duplicate_subpattern_numbers_from_pcre() {
        $lexer = $this->create_lexer('(a)(?|x(y)z|(p(q)r)|(t)u(v))(z)');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // x
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'y');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'z');
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // p
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'p');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // q
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'q');
        $token = $lexer->nextToken();    //)
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // r
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'r');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 't');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // u
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'u');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // v
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'v');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'z');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
    }
    function test_lexer_duplicate_subpattern_numbers_nested() {
        $lexer = $this->create_lexer('()(?|()|()(?|()|(()))|())()');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 5);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
    }
    function test_lexer_recursion() {
        $lexer = $this->create_lexer('(?R)(?14)');
        $token = $lexer->nextToken();// (?R)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==0);
        $token = $lexer->nextToken();// (?14)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==14);
    }
    function test_lexer_options() {
        $lexer = $this->create_lexer('a(?i)b(c(?-i)d)e');
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_options2() {
        $lexer = $this->create_lexer('(?im-Js:a(?s-i:b)c)');
        $token = $lexer->nextToken();// (?i:
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (?-i:
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_global_options() {
        $lexer = $this->create_lexer('ab(?-i:cd)e');
        $lexer->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'c');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
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
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'а');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'й');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ё');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'à');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'é');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'ه');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == '者');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_lexer_qe() {
        $lexer = $this->create_lexer('\\Q');
        $token = $lexer->nextToken();// \Q
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue(count($token) === 0);
        }
        $lexer = $this->create_lexer('\\Q\\Ex{3,10}');
        $token = $lexer->nextToken();// \Q\E
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue(count($token) === 0);
        }
        $token = $lexer->nextToken();// x
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 'x');
        $token = $lexer->nextToken();// {3,10}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 3);
        $this->assertTrue($token->value->rightborder === 10);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
        $lexer = $this->create_lexer('\\Qt@$t\\Es+');
        $token = $lexer->nextToken();// \Qt@$t\E
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue(count($token) === 4);
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[0]->value->flags[0][0]->data == 't');
            $this->assertTrue($token[1]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[1]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[1]->value->flags[0][0]->data == '@');
            $this->assertTrue($token[2]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[2]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[2]->value->flags[0][0]->data == '$');
            $this->assertTrue($token[3]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[3]->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[3]->value->flags[0][0]->data == 't');
        }
        $token = $lexer->nextToken();// s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data == 's');
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greed);
        $this->assertTrue(!$token->value->possessive);
    }
    function test_lexer_pcre_compatibility() {
        global $CFG;
        $file = fopen($CFG->dirroot . '/question/type/preg/tests/pcre_lexer_testinput1.txt', 'r');
        $counter = 0;
        while (!feof($file)) {
            $str = fgets($file);
            //echo $counter++.'<br/>';
            if ($str !== '') {
                $lexer = $this->create_lexer('\\Q');
                while ($token = $lexer->nextToken())
                    ;
            }
        }
        fclose($file);
    }
    function test_lexer_control_sequences() {
        $lexer = $this->create_lexer('(*ACCEPT)(*FAIL)(*MARK:NAME)(*COMMIT)(*PRUNE)(*PRUNE:NAME)(*SKIP)(*SKIP:NAME)(*THEN)(*THEN:NAME)(*CR)(*LF)(*CRLF)(*ANYCRLF)(*ANY)(*BSR_ANYCRLF)(*BSR_UNICODE)(*NO_START_OPT)(*UTF8)(*UTF16)(*UCP)(*SQUIRREL)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE);

        $token = $lexer->nextToken();
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE);
        }


        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP);

        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME);

        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_THEN);

        $token = $lexer->nextToken();
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME);
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE);
        }


        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_CR);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_LF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF);

        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_ANY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_BSR_ANYCRLF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_BSR_UNICODE);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UTF16);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_control::SUBTYPE_UCP);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errors[0]->indfirst === 193);
        $this->assertTrue($errors[0]->indlast === 203);
    }
    function test_lexer_errors() {
        $lexer = $this->create_lexer('\p{C}\p{Squirrel}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPC);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($errors[0]->indfirst === 5);
        $this->assertTrue($errors[0]->indlast === 16);
        $lexer = $this->create_lexer('[[:alpha:]][[:nut:]]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::ALPHA);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($errors[0]->indfirst === 12);
        $this->assertTrue($errors[0]->indlast === 18);
        $lexer = $this->create_lexer('[0-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[z-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[a-0]');
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_RANGE);
        $this->assertTrue($errors[0]->indfirst === 1);
        $this->assertTrue($errors[0]->indlast === 3);
        $lexer = $this->create_lexer('{2,2}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $lexer = $this->create_lexer('{127,11}');
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_errors();
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_RANGE);
        $this->assertTrue($errors[0]->indfirst === 1);
        $this->assertTrue($errors[0]->indlast === 6);
    }
}
