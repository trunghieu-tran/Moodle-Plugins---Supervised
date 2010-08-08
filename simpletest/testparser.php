<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_parser.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */
 
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');

class parser_test extends UnitTestCase {

    //Unit test for lexer
    function test_lexer_quantificators() {
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\mainlexems.txt', 'r'));//?*+{1,5}{,5}{1,}{5}??*?+?{1,5}?{,5}?{1,}?{5}?
        $token = $lexer->nextToken();//?
        $this->assertTrue($token->type === preg_parser_yyParser::QUEST);
        $token = $lexer->nextToken();//*
        $this->assertTrue($token->type === preg_parser_yyParser::ITER);
        $token = $lexer->nextToken();//+
        $this->assertTrue($token->type === preg_parser_yyParser::PLUS);
        $token = $lexer->nextToken();//{1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == -1 && $token->value->greed);
        $token = $lexer->nextToken();//{5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//*?
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_ITER);
        $token = $lexer->nextToken();//??
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_QUEST);
        $token = $lexer->nextToken();//+?
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_PLUS);
        $token = $lexer->nextToken();//{1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == -1 && !$token->value->greed);
        $token = $lexer->nextToken();//{5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && !$token->value->greed);
    }
    function test_lexer_backslach() {
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\backslash.txt', 'r'));//\\\*\[\23\023\x23\d\s\t
        $token = $lexer->nextToken();//\\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '\\');
        $token = $lexer->nextToken();//\*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '*');
        $token = $lexer->nextToken();//\[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '[');
        $token = $lexer->nextToken();//\23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_LINK);
        $token = $lexer->nextToken();//\023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && ord($token->value->chars) == 023);
        $token = $lexer->nextToken();//\x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && ord($token->value->chars) == 0x23);
        $token = $lexer->nextToken();//\d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '0123456789');
        $token = $lexer->nextToken();//\s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == ' ');
        $token = $lexer->nextToken();//\t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == chr(9));
    }
    function test_lexer_charclass() {
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\charclass.txt', 'r'));//[a][abc][ab{][ab\\][ab\]][a\db][a-d][3-6]
        $token = $lexer->nextToken();//[a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'a');
        $token = $lexer->nextToken();//[abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'abc');
        $token = $lexer->nextToken();//[ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab{');
        $token = $lexer->nextToken();//[ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab\\');
        $token = $lexer->nextToken();//[ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab]');
        $token = $lexer->nextToken();//[a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'a0123456789b');
        $token = $lexer->nextToken();//[a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'abcd');
        $token = $lexer->nextToken();//[3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '3456');
    }
    function test_lexer_few_number_in_quant() {
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\few_number_in_quant.txt', 'r'));//{135,12755139}{135,}{,12755139}{135}
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 12755139);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == -1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 12755139);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 135);
    }
    //Unit tests for parser
    function test_parser_easy_regex() {//a|b
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\easyre.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_quantification() {//ab+
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\quantification.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_CONC);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == NODE && $root->secop->subtype == NODE_PLUSQUANT);
        $this->assertTrue($root->secop->firop->type == LEAF && $root->secop->firop->subtype == LEAF_CHARCLASS && $root->secop->firop->chars == 'b');
    }
    function test_parser_alt_and_quantif() {//a*|b
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\alt_and_quantif.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == NODE && $root->firop->subtype == NODE_ITER);
        $this->assertTrue($root->firop->firop->type == LEAF && $root->firop->firop->subtype == LEAF_CHARCLASS && $root->firop->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_concatenation() {//ab
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\concatenation.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_CONC);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_alt_and_conc() {//ab|cd
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\alt_and_conc.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == NODE && $root->firop->subtype == NODE_CONC);
        $this->assertTrue($root->firop->firop->type == LEAF && $root->firop->firop->subtype == LEAF_CHARCLASS && $root->firop->firop->chars == 'a');
        $this->assertTrue($root->firop->secop->type == LEAF && $root->firop->secop->subtype == LEAF_CHARCLASS && $root->firop->secop->chars == 'b');
        $this->assertTrue($root->secop->type == NODE && $root->secop->subtype == NODE_CONC);
        $this->assertTrue($root->secop->firop->type == LEAF && $root->secop->firop->subtype == LEAF_CHARCLASS && $root->secop->firop->chars == 'c');
        $this->assertTrue($root->secop->secop->type == LEAF && $root->secop->secop->subtype == LEAF_CHARCLASS && $root->secop->secop->chars == 'd');
    }
    function test_parser_long_regex() {//(?:a|b)*abb
        $parser = new preg_parser_yyParser;
        $lexer = new Yylex(fopen('C:\\denwer\\installed\\home\\moodle19\\www\\question\\type\\preg\\simpletest\\longre.txt', 'r'));
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
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
}
?>