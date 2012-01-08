<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_parser.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

/*
*   Need test for next operations:
*nothing
*/
 
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class qtype_preg_lexer_test extends UnitTestCase {

    //Unit test for lexer
    function test_lexer_quantificators() {
        $regex = '?*+{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}?';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//*
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//+
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//*?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//??
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//+?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//{1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//{,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//{1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 1);
        $this->assertTrue(!$token->value->greed);
        $token = $lexer->nextToken();//{5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 5);
        $this->assertTrue($token->value->rightborder == 5);
        $this->assertTrue(!$token->value->greed);
    }
    function test_lexer_backslach() {
        $regex = '\\\\\\*\\[\\23\\023\\x23\\d\\s\\t\\b\\B\\>\\<\\%\\a';//\\\*\[\23\023\x23\d\s\t\b\B\>\<\%\a
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//\\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == '\\');
        $token = $lexer->nextToken();//\*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == '*');
        $token = $lexer->nextToken();//\[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == '[');
        $token = $lexer->nextToken();//\23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $token = $lexer->nextToken();//\023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(ord($token->value->charset) == 023);
        $token = $lexer->nextToken();//\x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue(ord($token->value->charset) == 0x23);
        $token = $lexer->nextToken();//\d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == '0123456789');
        $token = $lexer->nextToken();//\s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == ' ');
        $token = $lexer->nextToken();//\t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == chr(9));
        $token = $lexer->nextToken();//\b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue(!$token->value->negative);
        $token = $lexer->nextToken();//\B
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue($token->value->negative);
        $token = $lexer->nextToken();//\>
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->charset == '>');
        $token = $lexer->nextToken();//\<
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->charset == '<');
        $token = $lexer->nextToken();//\%
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertFalse($token->value->negative);
        $this->assertTrue($token->value->charset == '%');
        $flag = false;
        try {
            $token = $lexer->nextToken();//\a
        } catch(Exception $exep) {
            $flag = true;
        }
        $this->assertTrue($flag);//\a mst not match
    }
    function test_lexer_charclass() {
        //[a][abc][ab{][ab\\][ab\]][a\db][a-d][3-6]
        $regex = '[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d][3-6]';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//[a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'a');
        $token = $lexer->nextToken();//[abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'abc');
        $token = $lexer->nextToken();//[ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'ab{');
        $token = $lexer->nextToken();//[ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'ab\\');
        $token = $lexer->nextToken();//[ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'ab]');
        $token = $lexer->nextToken();//[a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'a0123456789b');
        $token = $lexer->nextToken();//[a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'abcd');
        $token = $lexer->nextToken();//[3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == '3456');
    }
    function test_lexer_few_number_in_quant() {
        //{135,12755139}{135,}{,12755139}{135}
        $regex = '{135,12755139}{135,}{,12755139}{135}';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//{135,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{135,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 0);
        $this->assertTrue($token->value->rightborder == 12755139);
        $this->assertTrue($token->value->greed);
        $token = $lexer->nextToken();//{135}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($token->value->leftborder == 135);
        $this->assertTrue($token->value->rightborder == 135);
        $this->assertTrue($token->value->greed);
    }
    function test_lexer_anchors() {
        //^a|b$
        $regex = '^a|b$';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
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
        $regex = '(?=(?!(?<=(?<!';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
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
        $regex = '.';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_META);
        $this->assertTrue($token->value->subtype === preg_leaf_meta::SUBTYPE_DOT);
    }
    function test_lexer_subpatterns() {
        $regex = '((?:(?>(?(?=(?(?!(?(?<=(?(?<!';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
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
        $regex = '((?:(?>()(';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
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
    function test_lexer_recursion() {
        $regex = '(?R)(?14)';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//(?R)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==0);
        $token = $lexer->nextToken();//(?14)
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type==preg_node::TYPE_LEAF_RECURSION);
        $this->assertTrue($token->value->number==14);
    }
    function test_lexer_options() {
        $regex = 'a(?i)b(c(?-i)d)e';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'a');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();//c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'c');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();//d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//)
        $token = $lexer->nextToken();//e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_options2() {
        $regex = '(?i:a(?-i:b)c)';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//(?i:
        $token = $lexer->nextToken();//a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(?-i:
        $token = $lexer->nextToken();//b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'b');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();//c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'c');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_global_options() {
        $regex = 'ab(?-i:cd)e';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $lexer->mod_top_opt('i', '');
        $token = $lexer->nextToken();//a
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'a');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();//b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'b');
        $this->assertTrue($token->value->caseinsensitive);
        $token = $lexer->nextToken();//(
        $token = $lexer->nextToken();//c
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'c');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'd');
        $this->assertFalse($token->value->caseinsensitive);
        $token = $lexer->nextToken();//)
        $token = $lexer->nextToken();//e
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($token->value->charset == 'e');
        $this->assertTrue($token->value->caseinsensitive);
    }
    function test_lexer_index() {
        $regex = 'ab{12,57}[abc]';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
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
}
?>