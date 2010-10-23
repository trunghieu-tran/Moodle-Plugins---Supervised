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

require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');

//$err = find_illegal_isnt_object($matcher->roots[0], 'root');if($err !== false){ echo '<br/> NON OBJECT!!!' . $err . '<br/>';}

function find_illegal_isnt_object($node, $path) {
    if(!is_object($node)) {
        return $path;
    } elseif ($node->type == NODE) {
        if ($node->subtype == NODE_CONC || $node->subtype == NODE_ALT) {
            $result = find_illegal_isnt_object($node->operands[2], $path . '->operands[2]');
            if ($result !== false) {
                return $result;
            }
        }
        $result = find_illegal_isnt_object($node->operands[1], $path . '->operands[1]');
        if ($result !== false) {
            return $result;
        }
    }
    return false;
}

class parser_test extends UnitTestCase {

    //Unit test for lexer
    function test_lexer_quantificators() {
        $regex = '?*+{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}?';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 1 && $token->value->greed);
        $token = $lexer->nextToken();//*
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 0 && $token->value->greed);
        $token = $lexer->nextToken();//+
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 1 && $token->value->greed);
        $token = $lexer->nextToken();//{1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 1 && $token->value->greed);
        $token = $lexer->nextToken();//{5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//*?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 0 && !$token->value->greed);
        $token = $lexer->nextToken();//??
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 1 && !$token->value->greed);
        $token = $lexer->nextToken();//+?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT && $token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 1 && !$token->value->greed);
        $token = $lexer->nextToken();//{1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 1 && !$token->value->greed);
        $token = $lexer->nextToken();//{5}?
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && !$token->value->greed);
    }
    function test_lexer_backslach() {
        $regex = '\\\\\\*\\[\\23\\023\\x23\\d\\s\\t\\b\\B';//\\\*\[\23\023\x23\d\s\t\b\B
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//\\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == '\\');
        $token = $lexer->nextToken();//\*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == '*');
        $token = $lexer->nextToken();//\[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == '[');
        $token = $lexer->nextToken();//\23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_BACKREF);
        $token = $lexer->nextToken();//\023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && ord($token->value->charset) == 023);
        $token = $lexer->nextToken();//\x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && ord($token->value->charset) == 0x23);
        $token = $lexer->nextToken();//\d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == '0123456789');
        $token = $lexer->nextToken();//\s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == ' ');
        $token = $lexer->nextToken();//\t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == chr(9));
        $token = $lexer->nextToken();//\b
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF && $token->value->type == preg_node::TYPE_LEAF_ASSERT && $token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK && !$token->value->negative);
        $token = $lexer->nextToken();//\B
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF && $token->value->type == preg_node::TYPE_LEAF_ASSERT && $token->value->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK && $token->value->negative);
    }
    function test_lexer_charclass() {
        //[a][abc][ab{][ab\\][ab\]][a\db][a-d][3-6]
        $regex = '[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d][3-6]';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//[a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'a');
        $token = $lexer->nextToken();//[abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'abc');
        $token = $lexer->nextToken();//[ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'ab{');
        $token = $lexer->nextToken();//[ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'ab\\');
        $token = $lexer->nextToken();//[ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'ab]');
        $token = $lexer->nextToken();//[a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'a0123456789b');
        $token = $lexer->nextToken();//[a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == 'abcd');
        $token = $lexer->nextToken();//[3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == preg_node::TYPE_LEAF_CHARSET && $token->value->charset == '3456');
    }
    function test_lexer_few_number_in_quant() {
        //{135,12755139}{135,}{,12755139}{135}
        $regex = '{135,12755139}{135,}{,12755139}{135}';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//{135,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 12755139 && $token->value->greed);
        $token = $lexer->nextToken();//{135,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_INFINITE_QUANT && $token->value->leftborder == 135 && $token->value->greed);
        $token = $lexer->nextToken();//{,12755139}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 12755139 && $token->value->greed);
        $token = $lexer->nextToken();//{135}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == preg_node::TYPE_NODE_FINITE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 135 && $token->value->greed);
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
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK && $token->value === preg_node_assert::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK && $token->value === preg_node_assert::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK && $token->value === preg_node_assert::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::OPENBRACK && $token->value === preg_node_assert::SUBTYPE_NLB);
    }
    function test_lexer_metasymbol_dot() {
        $regex = '.';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF && $token->value->type == preg_node::TYPE_LEAF_META && $token->value->subtype === preg_leaf_meta::SUBTYPE_DOT);
    }
    function test_lexer_subpatterns() {
        $regex = '((?:(?>(?(?=(?(?!(?(?<=(?(?<!';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK && $token->value === preg_node::TYPE_NODE_SUBPATT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK && $token->value === 'grouping');
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK && $token->value === preg_node_subpatt::SUBTYPE_ONCEONLY);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT && $token->value === preg_node_cond_subpatt::SUBTYPE_PLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT && $token->value === preg_node_cond_subpatt::SUBTYPE_NLA);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT && $token->value === preg_node_cond_subpatt::SUBTYPE_PLB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT && $token->value === preg_node_cond_subpatt::SUBTYPE_NLB);
        }
    //Unit tests for parser
    function test_parser_easy_regex() {//a|b
        $parser =& $this->run_parser('a|b');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->charset == 'b');
    }
    function test_parser_quantification() {//ab+
        $parser =& $this->run_parser('ab+');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_NODE_INFINITE_QUANT && $root->operands[2]->leftborder == 1);
        $this->assertTrue($root->operands[2]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->operands[1]->charset == 'b');
    }
    function test_parser_alt_and_quantif() {//a*|b
        $parser =& $this->run_parser('a*|b');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_NODE_INFINITE_QUANT && $root->operands[1]->leftborder == 0);
        $this->assertTrue($root->operands[1]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->charset == 'b');
    }
    function test_parser_concatenation() {//ab
        $parser =& $this->run_parser('ab');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->charset == 'b');
    }
    function test_parser_alt_and_conc() {//ab|cd
        $parser =& $this->run_parser('ab|cd');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[1]->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->operands[2]->charset == 'b');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[2]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->operands[1]->charset == 'c');
        $this->assertTrue($root->operands[2]->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->operands[2]->charset == 'd');
    }
    function _test_parser_long_regex() {//(?:a|b)*abb
        $parser =& $this->run_parser('(?:a|b)*abb');
        $matcher = new dfa_preg_matcher;
        $matcher->roots[0] = $parser->get_root();
        $matcher->append_end(0);
        $matcher->buildfa(0);
        $res = $matcher->compare('ab', 0);
        $this->assertTrue(!$res->full && $res->index == 1 && ($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abb', 0);
        $this->assertTrue($res->full && $res->index == 2 && $res->next == 0);
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbab', 0);
        $this->assertTrue(!$res->full && $res->index == 57 && ($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbabb', 0);
        $this->assertTrue($res->full && $res->index == 58 && $res->next == 0);  
    }
    function test_parser_two_anchors() {
        $parser =& $this->run_parser('^a$');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[1]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[1]->operands[1]->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[1]->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->operands[2]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[2]->subtype == preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_parser_start_anchor() {
        $parser =& $this->run_parser('^a');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[1]->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[2]->charset == 'a');
    }
    function test_parser_end_anchor() {
        $parser =& $this->run_parser('a$');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET && $root->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[2]->subtype == preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_parser_no_anchors() {
        $parser =& $this->run_parser('a');
        $root = $parser->get_root();
        $anchor = $parser->get_anchor();
        $this->assertTrue($root->type == preg_node::TYPE_LEAF_CHARSET && $root->charset === 'a');
        $this->assertTrue($anchor->start === false && $anchor->end === false);
    }
    function test_parser_error() {
        $parser =& $this->run_parser('^((ab|cd)ef$');
        $this->assertTrue($parser->get_error());
    }
    function test_parser_no_error() {
        $parser =& $this->run_parser('((ab|cd)ef)');
        $this->assertFalse($parser->get_error());
    }
    function test_parser_asserts() {
        $parser =& $this->run_parser('(?<=\w)(?<!_)a*(?=\w)(?!_)');
        $root = $parser->get_root();
        /* Old-style concatenation layout (strictly left-associative)
        $ff = $root->operands[2];
        $tf = $root->operands[1]->operands[2];
        $fb = $root->operands[1]->operands[1]->operands[1]->operands[2];
        $tb = $root->operands[1]->operands[1]->operands[1]->operands[1];*/
        /*New-style concatenation layout (with no associativity defined) - more balanced tree*/
        $tb = $root->operands[1]->operands[1];
        $fb = $root->operands[1]->operands[2];
        $tf = $root->operands[2]->operands[2]->operands[1];
        $ff = $root->operands[2]->operands[2]->operands[2];
        $this->assertTrue($tf->type == preg_node::TYPE_NODE_ASSERT && $tf->subtype == preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($ff->type == preg_node::TYPE_NODE_ASSERT && $ff->subtype == preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($fb->type == preg_node::TYPE_NODE_ASSERT && $fb->subtype == preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($tb->type == preg_node::TYPE_NODE_ASSERT && $tb->subtype == preg_node_assert::SUBTYPE_PLB);
    }
    function test_parser_metasymbol_dot() {
        $parser =& $this->run_parser('.');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_LEAF_META && $root->subtype == preg_leaf_meta::SUBTYPE_DOT);
    }
    function test_parser_word_break() {
        $parser =& $this->run_parser('a\b');
        $root = $parser->get_root();
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[2]->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK && !$root->operands[2]->negative);
    }
    function test_parser_word_not_break() {
        $parser =& $this->run_parser('a\B');
        $root = $parser->get_root();
        $this->assertTrue($root->operands[2]->type == preg_node::TYPE_LEAF_ASSERT && $root->operands[2]->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK && $root->operands[2]->negative);
    }
    function test_parser_subpatterns() {
        $parser =& $this->run_parser('((?:(?(?=a)(?>b)|a)))');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->operands[1]->operands[1]->type == preg_node::TYPE_NODE_SUBPATT && $root->operands[1]->operands[1]->subtype == preg_node_subpatt::SUBTYPE_ONCEONLY);
    }
    function test_syntax_errors() {//Test error reporting
        //Unclosed square brackets
        $parser =& $this->run_parser('ab(c|d)[fg\\]');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(in_array(get_string('unclosedsqbrackets', 'qtype_preg'), $errormsgs));
        //Unclosed parenthesis
        $parser =& $this->run_parser('a(b(?:c(?=d(?!e(?<=f(?<!g(?>h');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 7);
        //Unopened parenthesis
        $parser =& $this->run_parser(')ab(c|d)eg)');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 2);
        $this->assertTrue(in_array(get_string('unopenedparen', 'qtype_preg'), $errormsgs));
        $this->assertTrue(in_array(get_string('closeparenatverystart', 'qtype_preg'), $errormsgs));
        //Several unopened and unclosed parenthesis
        $parser =& $this->run_parser(')a)b)e(((g(');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 7);
        //Empty parenthesis
        $parser =& $this->run_parser(')abeg(?!)f');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 2);
        $this->assertTrue(in_array(get_string('closeparenatverystart', 'qtype_preg'), $errormsgs));
        $this->assertTrue(in_array(get_string('emptyparens', 'qtype_preg', '(?!'), $errormsgs));
        //Several empty parenthesis
        $parser =& $this->run_parser(')ab()eg(?!)f');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) >= 3);
        $this->assertTrue(in_array(get_string('closeparenatverystart', 'qtype_preg'), $errormsgs));
        $this->assertTrue(in_array(get_string('emptyparens', 'qtype_preg', '(?!'), $errormsgs));
        $this->assertTrue(in_array(get_string('emptyparens', 'qtype_preg', '('), $errormsgs));
        //Quantifiers without argument inside parenthesis
        $parser =& $this->run_parser('?a({2,3})c(*)e(+)f');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 4);
        /*Old style error reporting
        $this->assertTrue(in_array(get_string('quantifieratstart', 'qtype_preg', '?'), $errormsgs));
        $this->assertTrue(in_array(get_string('quantifieratstart', 'qtype_preg', '+'), $errormsgs));
        $this->assertTrue(in_array(get_string('quantifieratstart', 'qtype_preg', '*'), $errormsgs));
        $this->assertTrue(in_array(get_string('quantifieratstart', 'qtype_preg', '{...}'), $errormsgs));
        */
        $this->assertTrue(in_array(get_string('quantifieratstart', 'qtype_preg'), $errormsgs));
    }

    function test_condsubpattern_syntax_errors() {//Test error reporting for conditional subpatterns, which are particulary tricky
        //Three or more alternatives in conditional subpattern
        $parser =& $this->run_parser('(?(?=bc)dd|e*f|hhh)');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 1);
        //Correct situation: alternatives are nested within two alternatives for conditional subpattern
        $parser =& $this->run_parser('(?(?=bc)(dd|e*f)|(hhh|ff))');
        $this->assertFalse($parser->get_error());
        //Unclosed second parenthesis
        $parser =& $this->run_parser('a(?(?=bc)dd|e*f|hhh');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 1);
        $this->assertTrue(in_array(get_string('unclosedparen', 'qtype_preg', '(?(?='), $errormsgs));
        //Two parethesis unclosed
        $parser =& $this->run_parser('(?(?=bce*f|hhh');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 2);
        $this->assertTrue(in_array(get_string('unclosedparen', 'qtype_preg', '(?(?='), $errormsgs));
        //Empty assert in conditional subpattern
        $parser =& $this->run_parser('a(?(?=)');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 2);
        $this->assertTrue(in_array(get_string('emptyparens', 'qtype_preg', '(?(?='), $errormsgs));
        $this->assertTrue(in_array(get_string('unclosedparen', 'qtype_preg', '(?(?='), $errormsgs));
        //Empty yes-expr in conditional subpattern
        $parser =& $this->run_parser('(?(?=ab))');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 1);
        $this->assertTrue(in_array(get_string('emptyparens', 'qtype_preg', '(?(?='), $errormsgs));
        //Conditional subpattern starts at the end of expression
        $parser =& $this->run_parser('ab(?(?=');
        $this->assertTrue($parser->get_error());
        $errormsgs = $parser->get_error_messages();
        $this->assertTrue(count($errormsgs) == 1);
        $this->assertTrue(in_array(get_string('openparenatend', 'qtype_preg', '(?(?='), $errormsgs));

        }
    /** 
    *Service function to run parser on regex
    *@param regex Regular expression to parse
    *@return parser object
    */
    protected function &run_parser($regex) {
        $parser = new preg_parser_yyParser;
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        while ($token = $lexer->nextToken()) {
            $parser->doParse($token->type, $token->value);
        }
        $lexerrors = $lexer->get_errors();
        foreach ($lexerrors as $errstring) {
            $parser->doParse(preg_parser_yyParser::LEXERROR, $errstring);
        }
        $parser->doParse(0, 0);
        return $parser;
    }
}
?>