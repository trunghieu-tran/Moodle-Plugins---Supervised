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

require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');

class qtype_preg_parser_test extends UnitTestCase {

    //Unit tests for parser
    function test_parser_easy_regex() {//a|b
        $parser =& $this->run_parser('a|b');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->charset == 'b');
    }
    function test_parser_quantification() {//ab+
        $parser =& $this->run_parser('ab+');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[1]->leftborder == 1);
        $this->assertTrue($root->operands[1]->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->charset == 'b');
    }
    function test_parser_alt_and_quantif() {//a*|b
        $parser =& $this->run_parser('a*|b');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[0]->leftborder == 0);
        $this->assertTrue($root->operands[0]->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->charset == 'b');
    }
    function test_parser_concatenation() {//ab
        $parser =& $this->run_parser('ab');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->charset == 'b');
    }
    function test_parser_alt_and_conc() {//ab|cd
        $parser =& $this->run_parser('ab|cd');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->charset == 'b');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->charset == 'c');
        $this->assertTrue($root->operands[1]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[1]->charset == 'd');
    }
    function _test_parser_long_regex() {//(?:a|b)*abb
        $parser =& $this->run_parser('(?:a|b)*abb');
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
        $parser =& $this->run_parser('^a$');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->operands[0]->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[0]->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == preg_leaf_assert::SUBTYPE_DOLLAR);
    }
    function test_parser_start_anchor() {
        $parser =& $this->run_parser('^a');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype == preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->charset == 'a');
    }
    function test_parser_end_anchor() {
        $parser =& $this->run_parser('a$');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->charset == 'a');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == preg_leaf_assert::SUBTYPE_DOLLAR);
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
        $ff = $root->operands[1];
        $tf = $root->operands[0]->operands[1];
        $fb = $root->operands[0]->operands[0]->operands[0]->operands[1];
        $tb = $root->operands[0]->operands[0]->operands[0]->operands[0];*/
        /*New-style concatenation layout (with no associativity defined) - more balanced tree*/
        $tb = $root->operands[0]->operands[0];
        $fb = $root->operands[0]->operands[1];
        $tf = $root->operands[1]->operands[1]->operands[0];
        $ff = $root->operands[1]->operands[1]->operands[1];
        $this->assertTrue($tf->type == preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tf->subtype == preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($ff->type == preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($ff->subtype == preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($fb->type == preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($fb->subtype == preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($tb->type == preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tb->subtype == preg_node_assert::SUBTYPE_PLB);
    }
    function test_parser_metasymbol_dot() {
        $parser =& $this->run_parser('.');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype == preg_leaf_meta::SUBTYPE_DOT);
    }
    function test_parser_word_break() {
        $parser =& $this->run_parser('a\b');
        $root = $parser->get_root();
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue(!$root->operands[1]->negative);
    }
    function test_parser_word_not_break() {
        $parser =& $this->run_parser('a\B');
        $root = $parser->get_root();
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype == preg_leaf_assert::SUBTYPE_WORDBREAK);
        $this->assertTrue($root->operands[1]->negative);
    }
    function test_parser_subpatterns() {
        $parser =& $this->run_parser('((?:(?(?=a)(?>b)|a)))');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_NODE_COND_SUBPATT);
        $this->assertTrue($root->operands[0]->operands[0]->type == preg_node::TYPE_NODE_SUBPATT);
        $this->assertTrue($root->operands[0]->operands[0]->subtype == preg_node_subpatt::SUBTYPE_ONCEONLY);
    }
    function test_parser_index() {
        $parser =& $this->run_parser('abcdefgh|(abcd)*');
        $root = $parser->get_root();
        $this->assertTrue($root->indfirst == 0);
        $this->assertTrue($root->indlast == 15);
        $this->assertTrue($root->operands[0]->indfirst == 0);
        $this->assertTrue($root->operands[0]->indlast == 7);
        $this->assertTrue($root->operands[1]->indfirst == 9);
        $this->assertTrue($root->operands[1]->indlast == 15);
    }
    function test_parser_array_of_tokens() {//\88
        $parser =& $this->run_parser('\89');
        $root = $parser->get_root();
        $this->assertTrue($root->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type == preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->charset == chr(0));
        $this->assertTrue($root->operands[0]->operands[1]->charset == '8');
        $this->assertTrue($root->operands[1]->type == preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->charset == '9');
    }
    function test_syntax_errors() {//Test error reporting
        //Unclosed square brackets
        $parser =& $this->run_parser('ab(c|d)[fg\\]');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_UNCLOSED_CHARCLASS);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 7);
        //Unclosed parenthesis
        $parser =& $this->run_parser('a(b(?:c(?=d(?!e(?<=f(?<!g(?>h');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 7);
        //Unopened parenthesis
        $parser =& $this->run_parser(')ab(c|d)eg)');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 2);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[1]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[1]->firstindxs[0] == 10);
        //Several unopened and unclosed parenthesis
        $parser =& $this->run_parser(')a)b)e(((g(');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 7);
        //Empty parenthesis
        $parser =& $this->run_parser(')abeg(?!)f');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 2);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[2]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype == preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[2]->firstindxs[0] == 5);
        $this->assertTrue($errornodes[2]->lastindxs[0] == 8);
        //Several empty parenthesis
        $parser =& $this->run_parser(')ab()eg(?!)f');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 3);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[3]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype == preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[3]->firstindxs[0] == 7);
        $this->assertTrue($errornodes[3]->lastindxs[0] == 10);
        $this->assertTrue($errornodes[4]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype == preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[4]->firstindxs[0] == 3);
        $this->assertTrue($errornodes[4]->lastindxs[0] == 4);
        //Quantifiers without argument inside parenthesis
        $parser =& $this->run_parser('?a({2,3})c(*)e(+)f');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 4);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[0]->lastindxs[0] == 0);
        $this->assertTrue($errornodes[1]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[1]->firstindxs[0] == 3);
        $this->assertTrue($errornodes[1]->lastindxs[0] == 7);
        $this->assertTrue($errornodes[2]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype == preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[2]->firstindxs[0] == 11);
        $this->assertTrue($errornodes[2]->lastindxs[0] == 11);
        $this->assertTrue($errornodes[3]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype == preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[3]->firstindxs[0] == 15);
        $this->assertTrue($errornodes[3]->lastindxs[0] == 15);
    }
    function test_condsubpattern_syntax_errors() {//Test error reporting for conditional subpatterns, which are particulary tricky
        //Three or more alternatives in conditional subpattern
        $parser =& $this->run_parser('(?(?=bc)dd|e*f|hhh)');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[0]->lastindxs[0] == 18);
        //Correct situation: alternatives are nested within two alternatives for conditional subpattern
        $parser =& $this->run_parser('(?(?=bc)(dd|e*f)|(hhh|ff))');
        $this->assertFalse($parser->get_error());
        //Unclosed second parenthesis
        $parser =& $this->run_parser('a(?(?=bc)dd|e*f|hhh');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 1);
        $this->assertTrue($errornodes[0]->lastindxs[0] == 5);
        //Two parethesis unclosed
        $parser =& $this->run_parser('(?(?=bce*f|hhh');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[0]->lastindxs[0] == 4);
        //Empty assert in conditional subpattern
        $parser =& $this->run_parser('a(?(?=)');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[1]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[1]->firstindxs[0] == 1);
        $this->assertTrue($errornodes[1]->lastindxs[0] == 6);
        //Empty yes-expr in conditional subpattern
        $parser =& $this->run_parser('(?(?=ab))');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[1]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype == preg_node_error::SUBTYPE_EMPTY_PARENS);
        $this->assertTrue($errornodes[1]->firstindxs[0] == 0);
        $this->assertTrue($errornodes[1]->lastindxs[0] == 8);
        //Conditional subpattern starts at the end of expression
        $parser =& $this->run_parser('ab(?(?=');
        $this->assertTrue($parser->get_error());
        $errornodes = $parser->get_error_nodes();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type == preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype == preg_node_error::SUBTYPE_WRONG_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->firstindxs[0] == 2);
        $this->assertTrue($errornodes[0]->lastindxs[0] == 6);
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
            if (!is_array($token)) {
                $parser->doParse($token->type, $token->value);
            } else {
                 foreach ($token as $curtoken) {
                    $parser->doParse($curtoken->type, $curtoken->value);
                }
            }
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