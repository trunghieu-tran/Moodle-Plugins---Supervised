<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_parser.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

class qtype_preg_lexer_test extends UnitTestCase {

    function create_lexer($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        return new qtype_preg_lexer($pseudofile);
    }

    function test_lexer_quantificators() {
        $lexer = $this->create_lexer('?*+{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}?');
        $token = $lexer->nextToken();// ?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// *
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// +
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// *?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// ??
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// +?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();// {5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
    }
    function test_lexer_backslash() {
        $lexer = $this->create_lexer('\\\\\\*\\[\\23\\9\\023\\x23\\d\\s\\t\\b\\B\\>\\<\\%((((((((((((\\g15\\12\\g{15}\\g{-2}\\a');
        $token = $lexer->nextToken();// \\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type == preg_charset_flag::SET);
        $this->assertTrue($token->value->flags[0][0]->set == '\\');
        $token = $lexer->nextToken();// \*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == '*');
        $token = $lexer->nextToken();// \[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == '[');
        $token = $lexer->nextToken();// \23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);    // No subpatterns before this token.
        $token = $lexer->nextToken();// \9
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);    // Backref to the 9th subpattern.
        $this->assertTrue($token->value->number == 9);
        $token = $lexer->nextToken();// \023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(qtype_preg_unicode::ord($token->value->flags[0][0]->set) == 023);
        $token = $lexer->nextToken();// \x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(ord($token->value->flags[0][0]->set) == 0x23);
        $token = $lexer->nextToken();// \d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type==preg_charset_flag::FLAG && $token->value->flags[0][0]->flag==preg_charset_flag::DIGIT);
        $token = $lexer->nextToken();// \s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->type==preg_charset_flag::FLAG && $token->value->flags[0][0]->flag==preg_charset_flag::SPACE);
        $token = $lexer->nextToken();// \t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == chr(9));
        $token = $lexer->nextToken();// \b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue(!$token->value->negative);
        $token = $lexer->nextToken();// \B
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue($token->value->negative);
        $token = $lexer->nextToken();// \>
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->set == '>');
        $token = $lexer->nextToken();// \<
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->set == '<');
        $token = $lexer->nextToken();// \%
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->flags[0][0]->set == '%');
        for ($i = 0; $i < 12; $i++) {
            $lexer->nextToken();// skip 12 subpatterns
        }
        $token = $lexer->nextToken();// \g15
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \12
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 12);
        $token = $lexer->nextToken();// \g{15}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 15);
        $token = $lexer->nextToken();// \g{-2}
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $flag = false;
        try {
            $token = $lexer->nextToken();// \a
        } catch(Exception $exep) {
            $flag = true;
        }
        $this->assertTrue($flag);// \a mst not match
    }
    function test_lexer_named_backref() {
        $lexer = $this->create_lexer('\\k<name_1>\\k\'name_2\'\\k{name_3}\\g{name_4}(?P=name_5)');
        for ($i = 0; $i < 5; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
            $this->assertTrue($token->value->number == 'name_'.($i + 1));
        }
    }
    function test_lexer_tricky_backref() {
        $lexer = $this->create_lexer('\\040\\40\\7\\11(((((((((((\\11\\0113\\81\\378');
        $token = $lexer->nextToken();// \040
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == chr(octdec(40)));
        $token = $lexer->nextToken();// \40
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == chr(octdec(40)));
        $token = $lexer->nextToken();// \7
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 7);
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == chr(octdec(11)));
        for ($i = 0; $i < 11; $i++) {
            $token = $lexer->nextToken();
            $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
            $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        }
        $token = $lexer->nextToken();// \11
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 11);
        $token = $lexer->nextToken();// \0113
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == chr(octdec(11)));
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == '3');
        $token = $lexer->nextToken();// \81 - binary zero followed by '8' and '1'
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->type == preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[0]->value->flags[0][0]->set == chr(0));
            $this->assertTrue($token[1]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[1]->value->type == preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[1]->value->flags[0][0]->set == '8');
            $this->assertTrue($token[2]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[2]->value->type == preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[2]->value->flags[0][0]->set == '1');
            $token = $lexer->nextToken();// \378 - chr(octal(37)) followed by '8'
        }
        if ($this->assertTrue(is_array($token))) {
            $this->assertTrue($token[0]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[0]->value->type == preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[0]->value->flags[0][0]->set == chr(octdec(37)));
            $this->assertTrue($token[1]->type === preg_parser_yyParser::PARSLEAF);
            $this->assertTrue($token[1]->value->type == preg_node::TYPE_LEAF_CHARSET);
            $this->assertTrue($token[1]->value->flags[0][0]->set == '8');
        }
    }
    function test_lexer_named_subpatterns_and_backreferences() {
        $lexer = $this->create_lexer("(?|(?<qwe>)|(?'qwe'))(?P<rty>)\k<qwe>\k'qwe'\g{qwe}\k{rty}(?P=rty)");
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'qwe');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'rty');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $this->assertTrue($token->value->number == 'rty');
        $map = $lexer->get_subpattern_map();
        $this->assertTrue(count($map) === 2);
        $this->assertTrue(array_key_exists('qwe', $map) && $map['qwe'] === 1);
        $this->assertTrue(array_key_exists('rty', $map) && $map['rty'] === 2);
    }
    function test_lexer_lexems() {
        $lexer = $this->create_lexer('(?#this should be skipped)(?#{{)(?#{{)(?#}})(?#}})');
        $token = $lexer->nextToken();    // (?#{{)
        $this->assertTrue($token->type == preg_parser_yyParser::OPENLEXEM);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === -1);
        $token = $lexer->nextToken();    // (?#{{)
        $this->assertTrue($token->type == preg_parser_yyParser::OPENLEXEM);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === -2);
        $token = $lexer->nextToken();    // (?#}})
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSELEXEM);
        $token = $lexer->nextToken();    // (?#}})
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSELEXEM);
    }
    function test_lexer_charclass() {
        $lexer = $this->create_lexer('[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d][3-6]');
        $token = $lexer->nextToken();// [a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'a');
        $token = $lexer->nextToken();// [abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'abc');
        $token = $lexer->nextToken();// [ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'ab{');
        $token = $lexer->nextToken();// [ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'ab\\');
        $token = $lexer->nextToken();// [ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'ab]');
        $token = $lexer->nextToken();// [a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->flag === preg_charset_flag::DIGIT);
        $this->assertTrue($token->value->flags[1][0]->set == 'ab');
        $token = $lexer->nextToken();// [a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'abcd');
        $token = $lexer->nextToken();// [3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == '3456');
    }
    function test_lexer_few_number_in_quant() {
        $lexer = $this->create_lexer('{135,12755139}{135,}{,12755139}{135}');
        $token = $lexer->nextToken();// {135,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();// {135}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 135);
        $this->assertTrue($token->value->greed);
    }
    function test_lexer_anchors() {
        $lexer = $this->create_lexer('^a|b$');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_lexer_asserts() {
        $lexer = $this->create_lexer('(?=(?!(?<=(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_assert::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_assert::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_assert::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_assert::SUBTYPE_NLB);
    }
    function test_lexer_metasymbol_dot() {
        $lexer = $this->create_lexer('.');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->flag === preg_charset_flag::PRIN);
    }
    function test_lexer_subpatterns() {
        $lexer = $this->create_lexer('((?:(?>(?(?=(?(?!(?(?<=(?(?<!');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_ONCEONLY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === preg_node_cond_subpatt::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === preg_node_cond_subpatt::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === preg_node_cond_subpatt::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $this->assertTrue($token->value->subtype === preg_node_cond_subpatt::SUBTYPE_NLB);
    }
    function test_lexer_subpatterns_nested() {
        $lexer = $this->create_lexer('((?:(?>()(');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_ONCEONLY);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
    }
    function test_lexer_duplicate_subpattern_numbers_from_pcre() {
        $lexer = $this->create_lexer('(a)(?|x(y)z|(p(q)r)|(t)u(v))(z)');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'a');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // x
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'x');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'y');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'z');
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // p
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'p');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // q
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'q');
        $token = $lexer->nextToken();    //)
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // r
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'r');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // y
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 't');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // u
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'u');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // v
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'v');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 4);
        $token = $lexer->nextToken();    // z
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'z');
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
    }
    function test_lexer_duplicate_subpattern_numbers_nested() {
        $lexer = $this->create_lexer('()(?|()|()(?|()|(()))|())()');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (?|
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === 'grouping');
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // |
        $this->assertTrue($token->type == preg_parser_yyParser::ALT);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 3);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
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
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 2);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();    // (
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 5);
        $token = $lexer->nextToken();    // )
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
    }
    function test_lexer_recursion() {
        $lexer = $this->create_lexer('(?R)(?14)');
        $token = $lexer->nextToken();// (?R)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==0);
        $token = $lexer->nextToken();// (?14)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==14);
    }
    function test_lexer_options() {
        $lexer = $this->create_lexer('a(?i)b(c(?-i)d)e');
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'a');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'c');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_options2() {
        $lexer = $this->create_lexer('(?i:a(?-i:b)c)');
        $token = $lexer->nextToken();// (?i:
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (?-i:
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'b');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'c');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_global_options() {
        $lexer = $this->create_lexer('ab(?-i:cd)e');
        $lexer->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
        $token = $lexer->nextToken();// a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();// (
        $token = $lexer->nextToken();// c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'c');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();// )
        $token = $lexer->nextToken();// e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'e');
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
        $this->assertTrue($token->value->type === preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'а');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'й');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'ё');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'à');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'é');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == 'ه');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $this->assertTrue($token->value->subtype === preg_node_subpatt::SUBTYPE_SUBPATT);
        $this->assertTrue($token->value->number === 1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->flags[0][0]->set == '者');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CLOSEBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type === preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype === preg_leaf_assert::SUBTYPE_DOLLAR);
    }
}
?>