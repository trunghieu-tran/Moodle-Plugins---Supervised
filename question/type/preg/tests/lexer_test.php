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
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');

class qtype_preg_lexer_test extends PHPUnit_Framework_TestCase {

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
    function test_charset_simple() {
        $lexer = $this->create_lexer('[a][abc][ab{][ab\\\\][ab\\]][a\\db]');
        $token = $lexer->nextToken();// [a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();// [abc]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'abc');
        $token = $lexer->nextToken();// [ab{]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ab{');
        $token = $lexer->nextToken();// [ab\\]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ab\\');
        $token = $lexer->nextToken();// [ab\]]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ab]');
        $token = $lexer->nextToken();// [a\db]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::SLASH_D);
        $this->assertTrue($token->value->flags[1][0]->data->string() === 'ab');
    }
    function test_charset_ranges() {
        $lexer = $this->create_lexer('[a-d0-9][3-6][^\x61-\x{63}]');
        $token = $lexer->nextToken();// [a-d]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'abcd0123456789');
        $token = $lexer->nextToken();// [3-6]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '3456');
        $token = $lexer->nextToken();// [\x61-\x{63}]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data->string() === qtype_preg_unicode::code2utf8(0x61).qtype_preg_unicode::code2utf8(0x62).qtype_preg_unicode::code2utf8(0x63));
        $this->assertFalse($token->value->flags[0][0]->negative);
    }
    function test_charset_misc() {
        $lexer = $this->create_lexer('[^-\w\D][\Q][?\E][]a][^]a]');
        $token = $lexer->nextToken();// [^-\w\D]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::SLASH_W);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data === qtype_preg_charset_flag::SLASH_D);
        $this->assertTrue($token->value->flags[1][0]->negative);
        $this->assertTrue($token->value->flags[2][0]->data->string() === '-');
        $this->assertFalse($token->value->flags[2][0]->negative);
        $token = $lexer->nextToken();// [\Q][?\E]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '][?');
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// []a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === ']a');
        $this->assertFalse($token->value->negative);
        $token = $lexer->nextToken();// [^]a]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === ']a');
        $this->assertTrue($token->value->negative);
    }
    function test_quantifiers() {
        $lexer = $this->create_lexer('?*++{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}+');
        $token = $lexer->nextToken();// ?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->rightborder === 1);
        $this->assertTrue($token->value->greedy);
        $token = $lexer->nextToken();// *+
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue($token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 5);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// *?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// ??
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->rightborder === 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->greedy);
        $token = $lexer->nextToken();// {1,5}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {,5}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {1,}?
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue($token->value->lazy);
        $this->assertTrue(!$token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 5);
        $this->assertTrue($token->value->rightborder === 5);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $lexer = $this->create_lexer('{135,12755139}{135,}{,12755139}{135}');
        $token = $lexer->nextToken();// {135,12755139}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 135);
        $this->assertTrue($token->value->rightborder === 12755139);
        $this->assertTrue($token->value->greedy);
        $token = $lexer->nextToken();// {135,}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 135);
        $this->assertTrue($token->value->greedy);
        $token = $lexer->nextToken();// {,12755139}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 0);
        $this->assertTrue($token->value->rightborder === 12755139);
        $this->assertTrue($token->value->greedy);
        $token = $lexer->nextToken();// {135}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 135);
        $this->assertTrue($token->value->rightborder === 135);
        $this->assertTrue($token->value->greedy);
        $lexer = $this->create_lexer('a{1,2}{}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue($token->value->rightborder === 2);
        $this->assertTrue($token->value->greedy);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '{');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === ']');
    }
    function test_quantifiers_errors() {
        $lexer = $this->create_lexer('{127,11}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($token->value->errors[0]->indfirst === 1);
        $this->assertTrue($token->value->errors[0]->indlast === 6);
    }
    function test_backreferences() {
        $lexer = $this->create_lexer('\1\9\12\g15\g-2\g{15}\g{-15}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 9);
        $token = $lexer->nextToken();   // not a backreference, but a character with octal code 12
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(012));
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 15);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === -1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 15);
        $token = $lexer->nextToken();
        $lexer = $this->create_lexer("(?<qwe>)\k<qwe>\k'qwe'\g{qwe}\k{qwe}(?P=qwe)");
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
    }
    function test_backreferences_ambiguity() {
        $lexer = $this->create_lexer('\040\40\7\11(((((((((((\11\0113\81\378');
        $token = $lexer->nextToken();// \040
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(octdec(40)));
        $token = $lexer->nextToken();// \40
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(octdec(40)));
        $token = $lexer->nextToken();// \7
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 7);
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(octdec(11)));
        for ($i = 0; $i < 11; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
            $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        }
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 11);
        $token = $lexer->nextToken();// \0113
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(octdec(11)));
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '3');
        $token = $lexer->nextToken();// \81 - binary zero followed by '8' and '1'
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data->string() === chr(0x00));
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data->string() === '8');
        $this->assertTrue($token[2]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[2]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[2]->value->flags[0][0]->data->string() === '1');
        $token = $lexer->nextToken();// \378 - chr(octal(37)) followed by '8'
        $this->assertTrue(is_array($token));
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data->string() === chr(octdec(37)));
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data->string() === '8');
    }
    function test_backreferences_errors() {
        $lexer = $this->create_lexer('\g{1');   // \g1} is OK
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_G);
        $lexer = $this->create_lexer('\k<name');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('\kname>');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('\k\'name');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('\kname\'');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('\g{name');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_G);
        $lexer = $this->create_lexer('\gname}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_G);
        $lexer = $this->create_lexer('\k{name');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('\kname}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K);
        $lexer = $this->create_lexer('(?P=name');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
    }
    function test_subexpressions() {
        $lexer = $this->create_lexer('((?<name_1>(?\'name_2\'(?P<name_3>(?:(?|');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $map = $lexer->get_subexpr_map();
        $this->assertTrue(count($map) === 3);
        for ($i = 1; $i <= 3; $i++) {
            $this->assertTrue($map['name_' . $i] === $i + 1);   // +1 because of the first '('
        }
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'))(?P<rty>)\k<qwe>\k'qwe'\g{qwe}\k{rty}(?P=rty)");
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?'qwe'
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?P<rty>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'rty');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 'rty');
        $map = $lexer->get_subexpr_map();
        $this->assertTrue(count($map) === 2);
        $this->assertTrue(array_key_exists('qwe', $map) && $map['qwe'] === 1);
        $this->assertTrue(array_key_exists('rty', $map) && $map['rty'] === 2);
        $lexer = $this->create_lexer('((?:(?>()(');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY);
        $this->assertTrue($token->value->number === -1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
    }
    function test_subexpressions_errors() {
        $lexer = $this->create_lexer('(?<name_1(?\'name_1(?P<name_1');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $lexer = $this->create_lexer('(?name_1>');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQH);
        $lexer = $this->create_lexer('(?name_1\'');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQH);
        $lexer = $this->create_lexer('(?Pname_1)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQP);
        $lexer = $this->create_lexer('(?P<name>(?<name>(?\'name\'');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES);
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'(?'rty'(?'abc')))|(?'uio')");  // incorrect name: 'uio'
        $token = $lexer->nextToken();   // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();   // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();   // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();   // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();   // (?'qwe'
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();   // (?'rty'
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();   // (?'abc'
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBEXPR_NAMES);
    }
    function test_duplicate_subexpression_numbers_simple() {
        $lexer = $this->create_lexer('(?||()|()())()');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_simple_with_empty_alt() {
        $lexer = $this->create_lexer('(?|()||())()');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_nested() {
        $lexer = $this->create_lexer('()(?|()|()(?|()|(()))|((?|()|()())))()');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 5);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_simple_with_names() {
        $lexer = $this->create_lexer('(?|()|()(?<qwe>)()(?<rty>))()');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<rty>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
    }
    function test_duplicate_subexpression_numbers_with_names_tricky() {
        $lexer = $this->create_lexer('(?|()()|()(?<second>))()');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<second>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_from_pcre_1() {
        $lexer = $this->create_lexer('(a)(?|x(y)z|(p(q)r)|(t)u(v))(z)');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'x');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'y');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'z');
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // p
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'p');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // q
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'q');
        $token = $lexer->nextToken();    //)
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // r
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'r');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 't');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // u
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'u');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // v
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'v');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'z');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_from_pcre_2() {
        $lexer = $this->create_lexer('()(?|(|()())|())()()()');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 5);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 6);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 7);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_duplicate_subexpression_numbers_with_error_dup_subexpr_names() {
        $lexer = $this->create_lexer('(?|(?<qwe>)(?<qwe>)');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES);
        $lexer = $this->create_lexer('(?|(?<qwe>)(?<rty>)|(?<qwe>)(?<rty>)(?<rty>)');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<rty>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<rty>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<rty>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES);
        $lexer = $this->create_lexer('(?|(?<asd>)|()(?<asd>)');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES);
    }
    function test_duplicate_subexpression_numbers_with_error_different_subexpr_numbers() {
        $lexer = $this->create_lexer('(?|(?<asd>)|(?<asd1>)');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBEXPR_NAMES);
        $lexer = $this->create_lexer('(?|(?<asd>)(?<fgh>)|(?<asd>)(?<zxc>)');
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<fgh>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?<asd>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?<zxc>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBEXPR_NAMES);
    }
    function test_comment() {
        $lexer = $this->create_lexer('(?# this should be ignored)ё');    // Normal comment.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $this->assertTrue($token->value->indfirst === 27);
        $this->assertTrue($token->value->indlast === 27);
        $lexer = $this->create_lexer('(?# paren should be \) ignored as well as \\\\ the backslash)ё');    // Comment with escaped backslash and closing paren.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $this->assertTrue($token->value->indfirst === 59);
        $this->assertTrue($token->value->indlast === 59);
        $lexer = $this->create_lexer('(?#\\\\)ё');      // Empty of only slashes.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $this->assertTrue($token->value->indfirst === 6);
        $this->assertTrue($token->value->indlast === 6);
        $lexer = $this->create_lexer('(?#)ё');      // Empty comment.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $this->assertTrue($token->value->indfirst === 4);
        $this->assertTrue($token->value->indlast === 4);
        $lexer = $this->create_lexer("(?#some stuff\nanother stuff\ryet another stuff)ё");      // Multiline comment.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $this->assertTrue($token->value->indfirst === 46);
        $this->assertTrue($token->value->indlast === 46);
    }
    function test_options() {
        $lexer = $this->create_lexer('a(?i)b.(c(?s-i)d.)e');
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();// (?i)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_OPTIONS);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'b');
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();// .
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::META_DOT);
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'c');
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();// (?-i)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_OPTIONS);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'd');
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();// .
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::META_DOT);
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'e');
        $this->assertTrue($token->value->caseless);
        $lexer = $this->create_lexer('(?imsxuADSUXJ:a(?-i:b)c)');
        $token = $lexer->nextToken();// (?i:
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();// (?-i:
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'b');
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'c');
        $this->assertTrue($token->value->caseless);
        $lexer = $this->create_lexer(" \n\t(?x) \n\t#comment\n者");
        $token = $lexer->nextToken();
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) == 3);
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data->string() === ' ');
        $this->assertTrue($token[1]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[1]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[1]->value->flags[0][0]->data->string() === "\n");
        $this->assertTrue($token[2]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[2]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[2]->value->flags[0][0]->data->string() === "\t");
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_OPTIONS);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '者');
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $lexer = $this->create_lexer("#c\n");
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '#');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'c');
        $token = $lexer->nextToken();
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) == 1);
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data->string() === "\n");
    }
    function test_global_options() {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_CASELESS);
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_DOTALL);
        $lexer = $this->create_lexer('ab(?-i:.d)(?-s)e', $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'b');
        $this->assertTrue($token->value->caseless);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::META_DOT);
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'd');
        $this->assertFalse($token->value->caseless);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'e');
        $this->assertTrue($token->value->caseless);
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("\n   \t\r(?i-x)\n", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_OPTIONS);
        $token = $lexer->nextToken();
        $this->assertTrue(is_array($token));
        $this->assertTrue(count($token) == 1);
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token[0]->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token[0]->value->flags[0][0]->data->string() === "\n");
        $this->assertTrue($token[0]->value->caseless);
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("\ ", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === ' ');
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("[ ]", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === ' ');
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("^1234 #comment in extended re\n", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '1');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '2');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '3');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '4');
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("a#comment\n\Q#not comment", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '#');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'n');
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $lexer = $this->create_lexer("#comment", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token === NULL);
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_DUPNAMES);
        $lexer = $this->create_lexer("(?<name>(?'name'", $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 7);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);     // Nonetheless name is the same.
        $this->assertTrue($token->value->indfirst === 8);
        $this->assertTrue($token->value->indlast === 15);
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_DUPNAMES);
        $lexer = $this->create_lexer("(?|(?<name>)|(?<name>)(?<name>)()|())", $options);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<name>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (?'name'
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?'name'
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_DUPNAMES);
        $lexer = $this->create_lexer("(?|(?<qwe>)|()(?|(?<qwe>)))", $options);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type === qtype_preg_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();    // (?<qwe>
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
    }
    function test_lookaround_assertions() {
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
    function test_recursion() {
        $lexer = $this->create_lexer('(?<name>x)(?R)(?14)(?-1)(?+1)(?&name)(?P>name)\g<name>\g\'name\'');
        $token = $lexer->nextToken();   // (?<name>
        $token = $lexer->nextToken();   // x
        $token = $lexer->nextToken();   // )
        $token = $lexer->nextToken();   // (?R)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 0);
        $token = $lexer->nextToken();   // (?14)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 14);
        $token = $lexer->nextToken();   // (?-1)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();   // (?+1)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();   // (?&name)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 'name');
        $token = $lexer->nextToken();   // (?P>name)
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 'name');
        $token = $lexer->nextToken();   // \g<name>
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 'name');
        $token = $lexer->nextToken();   // \g'name'
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number === 'name');
    }
    function test_conditional_subexpressions() {
        $lexer = $this->create_lexer('((?:(?>(?(?=(?(?!(?(?<=(?(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB);
        $lexer = $this->create_lexer('((?(123)(?(+1)(?(-1)(?(<name_1>)(?(\'name_2\')(?(name_3)(?(R)(?(R4)(?(R&name_4)(?(DEFINE)');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->number === 123);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->number === 1);
        for ($i = 0; $i < 3; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
            $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
            $this->assertTrue($token[0]->value->number === 'name_' . ($i + 1));
        }
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 0);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->number === 'name_4');
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE);
    }
    function test_backslash() {
        $lexer = $this->create_lexer('\\\\\\*\\[\23\9\023\x\x23\x{7ff}\d\s\t\b\B\>\<\%\a\e\f\n\r\cz\c{\c;\u3f1\U\p{Greek}\P{Lt}\P{^M}\PL[ab\p{Xps}]\p{Xwd}');
        $token = $lexer->nextToken();// \\
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '\\');
        $token = $lexer->nextToken();// \*
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '*');
        $token = $lexer->nextToken();// \[
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '[');
        $token = $lexer->nextToken();// \23
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);    // No subexpressions before this token.
        $token = $lexer->nextToken();// \9
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);    // Backref to the 9th subexpression.
        $this->assertTrue($token->value->number === 9);
        $token = $lexer->nextToken();// \023
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) === 023);
        $token = $lexer->nextToken();// \x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'x');
        $token = $lexer->nextToken();// \x23
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) === 0x23);
        $token = $lexer->nextToken();// \x{7ff}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_poasquestion_string::ord($token->value->flags[0][0]->data->string()) === 0x7ff);
        $token = $lexer->nextToken();// \d
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data === qtype_preg_charset_flag::SLASH_D);
        $token = $lexer->nextToken();// \s
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type === qtype_preg_charset_flag::FLAG && $token->value->flags[0][0]->data === qtype_preg_charset_flag::SLASH_S);
        $token = $lexer->nextToken();// \t
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x09));
        $token = $lexer->nextToken();// \b
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue(!$token->value->negative);
        $token = $lexer->nextToken();// \B
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue($token->value->negative);
        $token = $lexer->nextToken();// \>
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '>');
        $token = $lexer->nextToken();// \<
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '<');
        $token = $lexer->nextToken();// \%
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '%');
        $token = $lexer->nextToken();// \a
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x07));
        $token = $lexer->nextToken();// \e
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x1B));
        $token = $lexer->nextToken();// \f
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x0C));
        $token = $lexer->nextToken();// \n
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x0A));
        $token = $lexer->nextToken();// \r
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x0D));
        $token = $lexer->nextToken();// \cz
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x1A));
        $token = $lexer->nextToken();// \c{
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x3B));
        $token = $lexer->nextToken();// \c;
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === chr(0x7B));
        $token = $lexer->nextToken();// \u
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();// 3
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '3');
        $token = $lexer->nextToken();// f
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'f');
        $token = $lexer->nextToken();// 1
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '1');
        $token = $lexer->nextToken();// \U
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();// \p{Greek}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::GREEK);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{Lt}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPLT);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \P{^M}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPM);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// \PL
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPL);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();// [ab\p{Xps}]
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPXPS);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->data->string() === 'ab');
        $this->assertFalse($token->value->flags[1][0]->negative);
        $token = $lexer->nextToken();// \p{Xwd}
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPXWD);
        $this->assertFalse($token->value->flags[0][0]->negative);
    }
    function test_anchors() {
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
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_meta_dot() {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = false;
        $lexer = $this->create_lexer('.', $options);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === "\n");
        $this->assertTrue($token->value->flags[0][0]->negative);
    }
    function test_indexes() {
        $lexer = $this->create_lexer('ab{12,57}[abc]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 0);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst === 1);
        $this->assertTrue($token->value->indlast === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst === 2);
        $this->assertTrue($token->value->indlast === 8);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->indfirst === 9);
        $this->assertTrue($token->value->indlast === 13);
    }
    function test_unicode() {
        $lexer = $this->create_lexer('^айёàéه(者)$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'а');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'й');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ё');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'à');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'é');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ه');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '者');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        $lexer = $this->create_lexer('[а-ймъ-ьяё]');    // 'ё' is not between 'е' and 'ж'.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'абвгдежзиймъыьяё');
        $lexer = $this->create_lexer('\x{430}[\x{431}-е]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'а');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'бвгде');
    }
    function test_qe() {
        $lexer = $this->create_lexer('\Q');       // Unclosed empty \Q
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $lexer = $this->create_lexer('\Qwat');    // Unclosed non-empty \Q
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'w');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 't');
        $lexer = $this->create_lexer('\Q\Ex{3,10}');
        $token = $lexer->nextToken();// x
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'x');
        $token = $lexer->nextToken();// {3,10}
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 3);
        $this->assertTrue($token->value->rightborder === 10);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $lexer = $this->create_lexer('\Qt@$t\Es+');
        $token = $lexer->nextToken();// \Qt@$t\E
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 't');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '@');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === '$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 't');
        $token = $lexer->nextToken();// s
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 's');
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder === 1);
        $this->assertTrue(!$token->value->lazy);
        $this->assertTrue($token->value->greedy);
        $this->assertTrue(!$token->value->possessive);
        $lexer = $this->create_lexer('\Qa\E[x\Q[y]\Ez]');    // \Q...\E followed by a charset containing the same thing.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'x[y]z');
        $lexer = $this->create_lexer('[z\Qa-d]\E]');         // Should not expand ranges inside \Q...E sequence.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'za-d]');
        $lexer = $this->create_lexer('[z\Qa\E-f]');          // Should expand ranges after \Q...E sequence.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'zabcdef');
        $lexer = $this->create_lexer('[\E\Qa\E-\Qf\E]');     // Should expand ranges after \Q...E sequence.
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'abcdef');
    }
    function test_control_sequences() {
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
    function test_errors() {
        // TODO: SUBTYPE_INVALID_ESCAPE_SEQUENCE, SUBTYPE_MISSING_CONTROL_ENDING, SUBTYPE_CALLOUT_BIG_NUMBER
        $lexer = $this->create_lexer('\p{C}[a\p{Squirrel}b]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::UPROPC);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ab');
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->errors[0]->indfirst === 7);
        $this->assertTrue($token->value->errors[0]->indlast === 18);
        $lexer = $this->create_lexer('[0-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[z-z]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $lexer = $this->create_lexer('[a-0]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE);
        $this->assertTrue($token->value->errors[0]->indfirst === 1);
        $this->assertTrue($token->value->errors[0]->indlast === 3);
        $lexer = $this->create_lexer('{2,2}');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $lexer = $this->create_lexer('\p{b}[\pB][[:c:]]{4,3}+[^az-yb]\pO[\p{4}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 4);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 5);
        $this->assertTrue($token->value->indlast === 9);
        $this->assertTrue($token->value->userinscription[0]->data === '\pB');
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->errors[0]->indfirst === 6);
        $this->assertTrue($token->value->errors[0]->indlast === 8);
        $this->assertTrue($token->value->errors[0]->addinfo === 'B');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->indfirst === 10);
        $this->assertTrue($token->value->indlast === 16);
        $this->assertTrue($token->value->userinscription[0]->data === '[:c:]');
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($token->value->errors[0]->indfirst === 11);
        $this->assertTrue($token->value->errors[0]->indlast === 15);
        $this->assertTrue($token->value->errors[0]->addinfo === '[:c:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::QUANT);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->indfirst === 17);
        $this->assertTrue($token->value->indlast === 22);
        $this->assertTrue($token->value->userinscription->data === '{4,3}+');
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($token->value->errors[0]->indfirst === 18);
        $this->assertTrue($token->value->errors[0]->indlast === 20);
        $this->assertTrue($token->value->errors[0]->addinfo === '4,3');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 3);
        $this->assertTrue($token->value->indfirst === 23);
        $this->assertTrue($token->value->indlast === 30);
        $this->assertTrue($token->value->userinscription[0]->data === 'a');
        $this->assertTrue($token->value->userinscription[1]->data === 'z-y');
        $this->assertTrue($token->value->userinscription[2]->data === 'b');
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'ab');
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE);
        $this->assertTrue($token->value->errors[0]->indfirst === 26);
        $this->assertTrue($token->value->errors[0]->indlast === 28);
        $this->assertTrue($token->value->errors[0]->addinfo === 'z-y');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->indfirst === 31);
        $this->assertTrue($token->value->indlast === 33);
        $this->assertTrue($token->value->addinfo === 'O');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->indfirst === 34);
        $this->assertTrue($token->value->indlast === 40);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{4}');
        $this->assertTrue(count($token->value->flags) === 0);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($token->value->errors[0]->indfirst === 35);
        $this->assertTrue($token->value->errors[0]->indlast === 39);
        $this->assertTrue($token->value->errors[0]->addinfo === '4');
        $lexer = $this->create_lexer('(?i-i)(?z-z');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_OPTIONS);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQH);
        $lexer = $this->create_lexer('\9[bc');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number === 9);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_error_nodes();
        $this->assertTrue(count($errors) === 2);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET);
        $this->assertTrue($errors[0]->indfirst === 2);
        $this->assertTrue($errors[0]->indlast === 4);
        $this->assertTrue($errors[1]->subtype === qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR);
        $this->assertTrue($errors[1]->indfirst === 0);
        $this->assertTrue($errors[1]->indlast === 1);
        $this->assertTrue($errors[1]->addinfo === '9');
        $lexer = $this->create_lexer('a\\');
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN);
        $this->assertTrue($token->value->indfirst === 1);
        $this->assertTrue($token->value->indlast === 1);
        $lexer = $this->create_lexer('b\\ca\\c');
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN);
        $this->assertTrue($token->value->indfirst === 4);
        $this->assertTrue($token->value->indlast === 5);
        $lexer = $this->create_lexer('(?#comment here)\x{FFFFFFFF}\x{d800}(?#comment without closing paren');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG);
        $this->assertTrue($token->value->indfirst === 16);
        $this->assertTrue($token->value->indlast === 27);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED);
        $this->assertTrue($token->value->indfirst === 28);
        $this->assertTrue($token->value->indlast === 35);
        $token = $lexer->nextToken();
        $this->assertTrue($token === null);
        $errors = $lexer->get_error_nodes();
        $this->assertTrue(count($errors) === 3);
        $this->assertTrue($errors[2]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING);
        $this->assertTrue($errors[2]->indfirst === 36);
        $this->assertTrue($errors[2]->indlast === 67);
        $lexer = $this->create_lexer('(?(0)(?C255(?Pn(?<name1(?\'name2(?P<name3(?<>(?\'\'(?P<>\g{}\k<>\k\'\'\k{}(?P=)\cй');
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token[0]->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONSUBEXPR_ZERO_CONDITION);
        $this->assertTrue($token[0]->value->errors[0]->indfirst === 0);
        $this->assertTrue($token[0]->value->errors[0]->indlast === 4);
        $this->assertTrue($token[1]->value === null);
        $this->assertTrue($token[2]->value === null);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING);
        $this->assertTrue($token->value->indfirst === 5);
        $this->assertTrue($token->value->indlast === 10);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQP);
        $this->assertTrue($token->value->indfirst === 11);
        $this->assertTrue($token->value->indlast === 13);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $this->assertTrue($token->value->indfirst === 15);
        $this->assertTrue($token->value->indlast === 22);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $this->assertTrue($token->value->indfirst === 23);
        $this->assertTrue($token->value->indlast === 30);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING);
        $this->assertTrue($token->value->indfirst === 31);
        $this->assertTrue($token->value->indlast === 39);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 40);
        $this->assertTrue($token->value->indlast === 43);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 44);
        $this->assertTrue($token->value->indlast === 47);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 48);
        $this->assertTrue($token->value->indlast === 52);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 53);
        $this->assertTrue($token->value->indlast === 56);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 57);
        $this->assertTrue($token->value->indlast === 60);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 61);
        $this->assertTrue($token->value->indlast === 64);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 65);
        $this->assertTrue($token->value->indlast === 68);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $this->assertTrue($token->value->indfirst === 69);
        $this->assertTrue($token->value->indlast === 73);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII);
        $this->assertTrue($token->value->indfirst === 74);
        $this->assertTrue($token->value->indlast === 76);
        $lexer = $this->create_lexer('(*MARK:)(*:)(?(R&)(?(<>)(?(\'\')(?()');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION);
        $this->assertTrue($token[0]->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token[0]->type === qtype_preg_yyParser::CONDSUBEXPR);
        $this->assertTrue($token[0]->value->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($token[0]->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $lexer = $this->create_lexer('(?(R&(?(<(?(\'(?(');
        $token = $lexer->nextToken();   // (?(R&
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING);
        $token = $lexer->nextToken();   // (?(<
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING);
        $token = $lexer->nextToken();   // (?('
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING);
        $token = $lexer->nextToken();   // (?(
        $this->assertTrue($token->type === qtype_preg_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING);
        $lexer = $this->create_lexer('\L\l\U\u\N{abracadabra}[\L\l\U\u\N{abracadabra}]\m');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->errors[1]->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->errors[2]->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->errors[3]->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $this->assertTrue($token->value->errors[4]->subtype === qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data->string() === 'm');
    }
    function test_errors_posix_classes() {
        $lexer = $this->create_lexer('[[:alpha:]][[:^cntrl:]][[:nut:]]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::POSIX_ALPHA);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::POSIX_CNTRL);
        $this->assertTrue($token->value->flags[0][0]->negative);
        $token = $lexer->nextToken();
        $this->assertTrue($token->value->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($token->value->errors[0]->indfirst === 24);
        $this->assertTrue($token->value->errors[0]->indlast === 30);
        $lexer = $this->create_lexer('[:alpha:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 8);
        $lexer = $this->create_lexer('[:^alpha:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 9);
        $lexer = $this->create_lexer('[:squirrel:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 11);
        $lexer = $this->create_lexer('[:star:wars:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($token->value->subtype === qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 12);
        // No : at the end - no error.
        $lexer = $this->create_lexer('[:alpha]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 7);
        $lexer = $this->create_lexer('[:]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 2);
        $lexer = $this->create_lexer('[:a[:alpha:]bcd]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->data === qtype_preg_charset_flag::POSIX_ALPHA);
        $this->assertFalse($token->value->flags[0][0]->negative);
        $this->assertTrue($token->value->flags[1][0]->type === qtype_preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[1][0]->data->string() === ':abcd');
        $this->assertTrue($token->value->indfirst === 0);
        $this->assertTrue($token->value->indlast === 15);
    }
    function test_userinscription() {
        $lexer = $this->create_lexer('a\p{L}\x{ab}[\pCab-de\x00-\xff[:alpha:]]\p{Squirrel}[\p{Squirrel}]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->userinscription[0]->data === 'a');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{L}');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->userinscription[0]->data === '\x{ab}');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 6);
        $this->assertTrue($token->value->userinscription[0]->data === '\pC');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $this->assertTrue($token->value->userinscription[1]->data === 'a');
        $this->assertTrue($token->value->userinscription[1]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[2]->data === 'b-d');
        $this->assertTrue($token->value->userinscription[3]->data === 'e');
        $this->assertTrue($token->value->userinscription[3]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[4]->data === '\x00-\xff');
        $this->assertTrue($token->value->userinscription[5]->data === '[:alpha:]');
        $this->assertTrue($token->value->userinscription[5]->type === qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->userinscription[0]->data === '\p{Squirrel}');
        $lexer = $this->create_lexer('[\xff\x00-\x1fA-B\t\n]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 5);
        $this->assertTrue($token->value->userinscription[0]->data === '\xff');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[1]->data === '\x00-\x1f');
        $this->assertTrue($token->value->userinscription[1]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[2]->data === 'A-B');
        $this->assertTrue($token->value->userinscription[2]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[3]->data === '\t');
        $this->assertTrue($token->value->userinscription[3]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $this->assertTrue($token->value->userinscription[4]->data === '\n');
        $this->assertTrue($token->value->userinscription[4]->type === qtype_preg_userinscription::TYPE_GENERAL);
        $lexer = $this->create_lexer('[\x20-a]');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === qtype_preg_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(count($token->value->userinscription) == 1);
        $this->assertTrue($token->value->userinscription[0]->data === '\x20-a');
        $this->assertTrue($token->value->userinscription[0]->type === qtype_preg_userinscription::TYPE_GENERAL);
    }
}
