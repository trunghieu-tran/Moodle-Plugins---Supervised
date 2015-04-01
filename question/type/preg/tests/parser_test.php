<?php

/**
 * Unit tests for question/type/preg/preg_parser.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>, Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once('override_templates.php');

class qtype_preg_parser_test extends PHPUnit_Framework_TestCase {

    /**
     * Service function to run regex handler.
     * @param regex regular expression to parse.
     * @param options qtype_preg_handling_options
     * @return qtype_preg_regex_handler object.
     */
    protected function run_handler($regex, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }
        return new qtype_preg_regex_handler($regex, $options);
    }
    function test_parser_dummy_1() {
        $handler = $this->run_handler('a');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(1));
        $this->assertTrue($root->lastpos == array(1));
    }
    function test_parser_dummy_2() {
        $handler = $this->run_handler('$');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(1));
        $this->assertTrue($root->lastpos == array(1));
    }
    function test_parser_concatenation() {
        $handler = $this->run_handler('ab');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(3));
    }
    function test_parser_alt() {
        $handler = $this->run_handler('a|b|c|d');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 6);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 0);
        for ($i = 0; $i < count($root->operands); $i++) {
            $this->assertTrue($root->operands[$i]->nullable === false);
            $this->assertTrue($root->operands[$i]->firstpos == array($i + 2));
            $this->assertTrue($root->operands[$i]->lastpos == array($i + 2));
        }
        $handler = $this->run_handler('a|');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->position->indfirst === 2 && $root->operands[1]->position->indlast === 1);
        $this->assertTrue($root->position->indfirst === 0 && $root->position->indlast === 1);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $handler = $this->run_handler('|a');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->position->indfirst === 0 && $root->position->indlast === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[0]->position->indfirst === 0 && $root->operands[0]->position->indlast === -1);
    }
    function test_parser_grouping() {
        $handler = $this->run_handler('(?:ab)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->userinscription === array());
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[0]->nullable === false);
        $this->assertTrue($root->operands[0]->firstpos == array(3));
        $this->assertTrue($root->operands[0]->lastpos == array(4));
    }
    function test_parser_subexpr() {
        $handler = $this->run_handler('(ab)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 3);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 0);
        $this->assertTrue($root->userinscription[0]->data === '(...)');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->userinscription === array());
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(4));
    }
    function test_parser_qu() {
        $handler = $this->run_handler('(?:ab)??');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '??');
        $this->assertTrue($root->lazy);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription === array());
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(4));
        $this->assertTrue($root->lastpos == array(5));
    }
    function test_parser_aster() {
        $handler = $this->run_handler('(?:[a-z\w]b)*');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '*');
        $this->assertTrue($root->greedy);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription === array());
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[1]->data === 'a-z');
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[2]->data === '\w');
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[2]->isflag === qtype_preg_charset_flag::SLASH_W);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(4));
        $this->assertTrue($root->lastpos == array(5));
    }
    function test_parser_plus() {
        $handler = $this->run_handler('(?:[\wab-yz\d])++');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '++');
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 0);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 16);
        $this->assertTrue($root->possessive);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[1]->data === '\w');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[1]->isflag === qtype_preg_charset_flag::SLASH_W);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[2]->data === 'a');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[3]->data === 'b-y');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[4]->data === 'z');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[5]->data === '\d');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[5]->isflag === qtype_preg_charset_flag::SLASH_D);
        $this->assertTrue($root->operands[0]->operands[0]->position->colfirst === 3);
        $this->assertTrue($root->operands[0]->operands[0]->position->collast === 13);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
    }
    function test_parser_brace() {
        $handler = $this->run_handler('[^\p{Egyptian_Hieroglyphs}]{8,}');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '{8,}');
        $this->assertTrue($root->greedy);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription[1]->data === '\p{Egyptian_Hieroglyphs}');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
    }
    function test_parser_cond_subexpr() {
        $handler = $this->run_handler('(?(?=a)b|cd)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->userinscription[0]->data === '(?(?=...)...|...)');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->userinscription[0]->data === '(?=...)');
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[0]->data === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->userinscription[0]->data === 'b');
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[2]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->operands[0]->flags[0][0]->data->string() === 'c');
        $this->assertTrue($root->operands[2]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->operands[1]->flags[0][0]->data->string() === 'd');
        $handler = $this->run_handler('(?(DEFINE)a)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->userinscription[0]->data === '(?(DEFINE)...)');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription[0]->data === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $handler = $this->run_handler('(?<DEFINE>a)(?(DEFINE)a|b)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
    }
    function test_parser_easy_regex() {
        $handler = $this->run_handler('a|b');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->userinscription[0]->data === '|');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->userinscription[0]->data === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[1]->userinscription[0]->data === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2, 3));
        $this->assertTrue($root->lastpos == array(2, 3));
    }
    function test_parser_quantifier() {
        $handler = $this->run_handler('ab+');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->userinscription === array());
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->userinscription[0]->data === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[1]->leftborder === 1);
        $this->assertTrue($root->operands[1]->userinscription[0]->data === '+');
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[1]->operands[0]->userinscription[0]->data === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(4));
    }
    function test_parser_concat_and_quant() {
        $handler = $this->run_handler('abc?d?ef?');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->operands[0]->nullable === false);
        $this->assertTrue($root->operands[1]->nullable === false);
        $this->assertTrue($root->operands[2]->nullable === true);
        $this->assertTrue($root->operands[3]->nullable === true);
        $this->assertTrue($root->operands[4]->nullable === false);
        $this->assertTrue($root->operands[5]->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(10, 8));
    }
    function test_parser_alt_and_quantifier() {
        $handler = $this->run_handler('a*|b');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[0]->userinscription[0]->data === '*');
        $this->assertTrue($root->operands[0]->leftborder === 0);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->operands[0]->nullable === true);
        $this->assertTrue($root->operands[1]->nullable === false);
        $this->assertTrue($root->firstpos == array(3, 4));
        $this->assertTrue($root->lastpos == array(3, 4));
    }
    function test_parser_alt_and_concat() {
        $handler = $this->run_handler('ab|cd');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->flags[0][0]->data->string() === 'c');
        $this->assertTrue($root->operands[1]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[1]->flags[0][0]->data->string() === 'd');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->operands[0]->nullable === false);
        $this->assertTrue($root->operands[1]->nullable === false);
        $this->assertTrue($root->firstpos == array(3, 6));
        $this->assertTrue($root->lastpos == array(4, 7));
    }
    function test_parser_conditional_subexpression() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $handler = $this->run_handler('(?(name)a|b|c)', $options);
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) == 2);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->name === 'name');
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
    }
    function test_parser_long_regex() {
        $handler = $this->run_handler('(?:a|b)*abb');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->operands[0]->nullable === true);
        $this->assertTrue($root->operands[1]->nullable === false);
        $this->assertTrue($root->firstpos == array(5, 6, 7));
        $this->assertTrue($root->lastpos == array(9));
    }
    function test_parser_two_anchors() {
        $handler = $this->run_handler('^a$');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_A);   // Converted by lexer.
        $this->assertTrue($root->operands[0]->userinscription[0]->data === '^');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[2]->subtype === qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z);   // Converted by lexer.
        $this->assertFalse($root->operands[2]->negative);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(4));
    }
    function test_parser_start_anchor() {
        $handler = $this->run_handler('^a');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_A);   // Converted by lexer.
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
    }
    function test_parser_end_anchor() {
        $handler = $this->run_handler('a$');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z);   // Converted by lexer.
        $this->assertFalse($root->operands[1]->negative);
    }
    function test_parser_error() {
        $handler = $this->run_handler('^((ab|cd)ef$');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) > 0);
    }
    function test_parser_no_error() {
        $handler = $this->run_handler('((ab|cd)ef)');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(empty($errors));
        $root = $handler->get_ast_root();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(6, 9));
        $this->assertTrue($root->lastpos == array(12));
    }
    function test_parser_asserts() {
        $handler = $this->run_handler('(?<=\w)(?<!_)a*(?=\w)(?!_)');
        $root = $handler->get_ast_root();
        $tb = $root->operands[0];
        $fb = $root->operands[1];
        $tf = $root->operands[3];
        $ff = $root->operands[4];
        $this->assertTrue($tf->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tf->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($tf->userinscription[0]->data === '(?=...)');
        $this->assertTrue($ff->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($ff->subtype === qtype_preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($ff->userinscription[0]->data === '(?!...)');
        $this->assertTrue($fb->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($fb->subtype === qtype_preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($fb->userinscription[0]->data === '(?<!...)');
        $this->assertTrue($tb->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($tb->subtype === qtype_preg_node_assert::SUBTYPE_PLB);
        $this->assertTrue($tb->userinscription[0]->data === '(?<=...)');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->operands[2]->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(10));
    }
    function test_parser_metasymbol_dot() {
        $handler = $this->run_handler('.');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->flags[0][0]->data->string() === "\n");
        $this->assertTrue($root->flags[0][0]->negative);
    }
    function test_parser_word_break() {
        $handler = $this->run_handler('a\b');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertFalse($root->operands[1]->negative);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(3));
    }
    function test_parser_word_not_break() {
        $handler = $this->run_handler('a\B');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue($root->operands[1]->negative);
    }
    function test_parser_alt_all_forms() {
        $handler = $this->run_handler('a|b');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2, 3));
        $this->assertTrue($root->lastpos == array(2, 3));
        $handler = $this->run_handler('a|');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $handler = $this->run_handler('|a');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
        $handler = $this->run_handler('a|b|');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[2]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2, 3));
        $this->assertTrue($root->lastpos == array(2, 3));
        $handler = $this->run_handler('a||');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue(count($root->operands) == 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $handler = $this->run_handler('||a');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue(count($root->operands) == 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
        $handler = $this->run_handler('|');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $handler = $this->run_handler('||');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $handler = $this->run_handler('(?:|)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[0]->nullable === true);
        $this->assertTrue($root->operands[0]->firstpos == array());
        $this->assertTrue($root->operands[0]->lastpos == array());
        $handler = $this->run_handler('(|||||)');    // боян
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $handler = $this->run_handler('(|a||b|c||)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue(count($root->operands[0]->operands) === 4);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[2]->flags[0][0]->data->string() === 'c');
        $this->assertTrue($root->operands[0]->operands[3]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[3]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(3, 4, 5));
        $this->assertTrue($root->lastpos == array(3, 4, 5));
    }
    function test_parser_subexpressions() {
        $handler = $this->run_handler('((?:(?(?=a)(?>b)|a)))');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY);
    }
    function test_parser_duplicate_subexpression_numbers() {
        $handler = $this->run_handler('(?|a|b|c)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[2]->flags[0][0]->data->string() === 'c');
    }
    function test_parser_duplicate_subexpression_names_with_J() {
        $options = new qtype_preg_handling_options;
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_DUPNAMES);
        $handler = $this->run_handler('(?<n>A)(?:(?<n>foo)|(?<n>bar))\\k<n>', $options);  // taken from PCRE
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->number === 1);
        $this->assertTrue($root->operands[0]->name === 'n');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->operands[0]->operands[0]->number === 2);
        $this->assertTrue($root->operands[1]->operands[0]->operands[0]->name === 'n');
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->number === 3);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->name === 'n');
        $this->assertTrue($root->operands[2]->name === 'n');
    }
    function test_parser_index() {
        $handler = $this->run_handler('abcdefgh|(abcd)*');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 0);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 15);
        $this->assertTrue($root->operands[0]->position->colfirst === 0);
        $this->assertTrue($root->operands[0]->position->collast === 7);
        $this->assertTrue($root->operands[1]->position->colfirst === 9);
        $this->assertTrue($root->operands[1]->position->collast === 15);
    }
    function test_parser_array_of_tokens() {
        $handler = $this->run_handler('\89');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === '8');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === '9');
    }
    function test_parser_nested_subexprs() {
        $handler = $this->run_handler('((?|(a)|(b(c)))(d))');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
    }
    function test_preserve_all_nodes() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $handler = $this->run_handler('(?:a)', $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
    }
    function test_pcre_strict() {
        // Empty parentheses should be empty subexpression.
        $handler = $this->run_handler('()');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Nested empty parentheses.
        $handler = $this->run_handler('((?=))');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty parentheses with concatenation.
        $handler = $this->run_handler('a()b');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        // Empty assertion.
        $handler = $this->run_handler('(?=)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty conditional subexpression.
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $handler = $this->run_handler('(?(<name>))', $options);
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) == 1);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->name === 'name');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty conditional subexpression with empty assertion but not empty branches.
        $handler = $this->run_handler('(?(?<=)a)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_PLB);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        // Conditional subexpression with assertion and empty body.
        $handler = $this->run_handler('(?(?!a))');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with empty assertion and empty body.
        $handler = $this->run_handler('(?(?<!))');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(empty($errors));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with some condition and empty body.
        $handler = $this->run_handler('(?(+1))');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) == 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 5);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->number === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with some condition and empty body (same as the previous one but named).
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $handler = $this->run_handler('(?(<name>))', $options);
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) == 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 9);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->name === 'name');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    }
    function test_errors() {
        // Unclosed square brackets.
        $handler = $this->run_handler('a[');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) == 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET);
        $this->assertTrue($errors[0]->position->indfirst === 1);
        $this->assertTrue($errors[0]->position->indlast === 1);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1] === $errors[0]);
        // Unclosed parenthesis.
        $handler = $this->run_handler('a(b(?:c(?=d(?!e(?<=f(?<!g(?>h');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 7);
        $this->assertFalse(empty($errors[0]->operands));
        $this->assertFalse(empty($errors[1]->operands));
        $this->assertFalse(empty($errors[2]->operands));
        $this->assertFalse(empty($errors[3]->operands));
        $this->assertFalse(empty($errors[4]->operands));
        $this->assertFalse(empty($errors[5]->operands));
        $this->assertFalse(empty($errors[6]->operands));
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        // Unopened parenthesis.
        $handler = $this->run_handler(')ab(c|d)eg)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 2);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[1]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errors[1]->position->colfirst === 10);
        $this->assertTrue($errors[1]->operands[0] === $root->operands[0]);
        // Unclosed template.
        $handler = $this->run_handler('(?###smth<)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 2);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE);
        $this->assertTrue($errors[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[1]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        $handler = $this->run_handler('(?###parens_req<)a');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        $handler = $this->run_handler('(?###parens_req<)a(?###,)b');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        $handler = $this->run_handler('(?###parens_req<)a(?###,)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        $handler = $this->run_handler('(?###parens_req<)(?###,)b');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        $handler = $this->run_handler('(?###parens_req<)(?###,)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN);
        // Unopened template.
        $handler = $this->run_handler('(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        $handler = $this->run_handler('a(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        $handler = $this->run_handler('a(?###,)b(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        $handler = $this->run_handler('a(?###,)(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        $handler = $this->run_handler('(?###,)b(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        $handler = $this->run_handler('(?###,)(?###>)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN);
        // Several unopened and unclosed parenthesis.
        $handler = $this->run_handler(')a)b)e(((g(');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 7);
        $this->assertTrue(empty($errors[0]->operands));
        $this->assertFalse(empty($errors[1]->operands));
        $this->assertFalse(empty($errors[2]->operands));
        $this->assertTrue(empty($errors[3]->operands));
        $this->assertFalse(empty($errors[4]->operands));
        $this->assertFalse(empty($errors[5]->operands));
        $this->assertFalse(empty($errors[6]->operands));
        // Quantifiers without argument inside parentheses.
        $handler = $this->run_handler('?a({2,3})c(+)e(+)(*s)f');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 5);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errors[0]->position->colfirst === 17);
        $this->assertTrue($errors[0]->position->collast === 20);
        $this->assertTrue($errors[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[1]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errors[1]->position->colfirst === 0);
        $this->assertTrue($errors[1]->position->collast === 0);
        $this->assertTrue($errors[2]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[2]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errors[2]->position->colfirst === 3);
        $this->assertTrue($errors[2]->position->collast === 7);
        $this->assertTrue($errors[3]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[3]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errors[3]->position->colfirst === 11);
        $this->assertTrue($errors[3]->position->collast === 11);
        $this->assertTrue($errors[4]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[4]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errors[4]->position->colfirst === 15);
        $this->assertTrue($errors[4]->position->collast === 15);
        $this->assertTrue(empty($errors[0]->operands));
        $this->assertTrue(empty($errors[1]->operands));
        $this->assertTrue(empty($errors[2]->operands));
        $this->assertTrue(empty($errors[3]->operands));
        $this->assertTrue(empty($errors[4]->operands));
        // Test error reporting for conditional subexpressions, which are particulary tricky.
        // Three or more alternations in conditional subexpression.
        $handler = $this->run_handler('(?(?=bc)dd|e*f|hhh)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($root->errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER);
        $this->assertTrue($root->errors[0]->position->colfirst === 0);
        $this->assertTrue($root->errors[0]->position->collast === 18);
        $handler = $this->run_handler('(?(DEFINE)x|y)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->userinscription[0]->data === '(?(DEFINE)...|...)');
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($root->errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER);
        $this->assertTrue($root->errors[0]->position->colfirst === 0);
        $this->assertTrue($root->errors[0]->position->collast === 13);
        // Unclosed second parenthesis.
        $handler = $this->run_handler('a(?(?=bc)dd|e*f|hhh');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errors[0]->position->colfirst === 1);
        $this->assertTrue($errors[0]->position->collast === 5);
        $this->assertTrue(is_a($errors[0]->operands[0], 'qtype_preg_node_alt'));//There should be two operands for such error: alternation and expression inside assertion
        $this->assertTrue(is_a($errors[0]->operands[1], 'qtype_preg_node_concat'));
        // Two parentheses unclosed.
        $handler = $this->run_handler('(?(?=bce*f|hhh');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 4);
        $this->assertTrue(is_a($errors[0]->operands[0], 'qtype_preg_node_alt'));
        // Conditional subexpression starts at the end of expression.
        $handler = $this->run_handler('ab(?(?=');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errors[0]->position->colfirst === 2);
        $this->assertTrue($errors[0]->position->collast === 6);
        $this->assertTrue(empty($errors[1]->operands));
        // Conditional subexpression with empty condition is error.
        $handler = $this->run_handler('(?()a)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $handler = $this->run_handler('(?()yes|no)');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        // Conditional subexpression with empty condition is error (same as the previous one but with empty body).
        $handler = $this->run_handler('(?())');
        $errors = $handler->get_error_nodes();
        $root = $handler->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Everything possible.
        $handler = $this->run_handler('(*UTF9))((?(?=x)a|b|c)({5,4})(?i-i)[[:hamster:]]\p{Squirrel}');
        $errors = $handler->get_error_nodes();
        $this->assertTrue(count($errors) === 9);
        $this->assertTrue(count($errors[0]->operands) === 0);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 6);
        $this->assertTrue($errors[0]->addinfo === '(*UTF9)');
        $this->assertTrue(count($errors[1]->operands) === 0);
        $this->assertTrue($errors[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[1]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($errors[1]->position->colfirst === 23);
        $this->assertTrue($errors[1]->position->collast === 27);
        $this->assertTrue(count($errors[2]->operands) === 0);
        $this->assertTrue($errors[2]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[2]->subtype === qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER);
        $this->assertTrue($errors[2]->position->colfirst === 29);
        $this->assertTrue($errors[2]->position->collast === 34);
        $this->assertTrue(count($errors[3]->operands) === 0);
        $this->assertTrue($errors[3]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[3]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($errors[3]->position->colfirst === 36);
        $this->assertTrue($errors[3]->position->collast === 46);
        $this->assertTrue($errors[3]->addinfo === '[:hamster:]');
        $this->assertTrue(count($errors[4]->operands) === 0);
        $this->assertTrue($errors[4]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[4]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($errors[4]->position->colfirst === 48);
        $this->assertTrue($errors[4]->position->collast === 59);
        $this->assertTrue($errors[4]->addinfo === 'Squirrel');
        $this->assertTrue(count($errors[5]->operands) === 1);
        $this->assertTrue($errors[5]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[5]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errors[5]->position->colfirst === 7);
        $this->assertTrue($errors[5]->position->collast === 7);
        $this->assertTrue($errors[5]->operands[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[5]->operands[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errors[6]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER);
        $this->assertTrue($errors[6]->position->colfirst === 9);
        $this->assertTrue($errors[6]->position->collast === 21);
        $this->assertTrue(count($errors[7]->operands) === 0);
        $this->assertTrue($errors[7]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[7]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errors[7]->position->colfirst === 23);
        $this->assertTrue($errors[7]->position->collast === 27);
        $this->assertTrue(count($errors[8]->operands) === 1);
        $this->assertTrue($errors[8]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[8]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errors[8]->position->colfirst === 8);
        $this->assertTrue($errors[8]->position->collast === 8);
        $this->assertTrue(is_a($errors[8]->operands[0], 'qtype_preg_node_concat'));
    }
    function test_multiline_regex() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $handler = $this->run_handler("a\nbcd\nef", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 2);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 1);
        $handler = $this->run_handler("(?:a(?#com\r\nment\nhere)bcd\nef)+", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 3);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 3);
        $handler = $this->run_handler("(a\nbcd\n\r\n\nef)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 4);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 2);
    }
    function test_subexpr_calls() {
        $handler = $this->run_handler("()(?R)(?1)(?<name>)(?&name)");
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->number === 1);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL);
        $this->assertTrue($root->operands[1]->number === 0);
        $this->assertTrue($root->operands[1]->isrecursive);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL);
        $this->assertTrue($root->operands[2]->number === 1);
        $this->assertFalse($root->operands[2]->isrecursive);
        $this->assertTrue($root->operands[3]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[3]->number === 2);
        $this->assertTrue($root->operands[4]->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL);
        $this->assertTrue($root->operands[4]->number === 2);
        $this->assertFalse($root->operands[4]->isrecursive);
    }
    function test_templates() {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        $handler = $this->run_handler("(?###word)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_TEMPLATE);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 9);
        $handler = $this->run_handler("(?###smth<)a(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 18);
        $handler = $this->run_handler("(?###smth<)ab?|c(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 22);
        $handler = $this->run_handler("(?###smth<)a(?###,)b?(?###,)c|(d)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 39);
        $handler = $this->run_handler("(?###custom_parens_req<)a(?###,)b(?###,)c(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 3);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        // Okay, these guys can be nested
        $handler = $this->run_handler("(?###outer<)a(?###,)(?###inner<)a(?###,)b?(?###,)(c)(?###>)(?###,)c|(d)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[1]->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->operands[1]->operands[2]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_ALT);
        // Weird cases with emptiness
        $handler = $this->run_handler("(?###smth<)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 17);
        $handler = $this->run_handler("(?###smth<)(?###,)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $handler = $this->run_handler("(?###smth<)(?###,)(?###,)(?###,)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 38);
        $handler = $this->run_handler("(?###smth<)abcde(?###,)(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 29);
        $handler = $this->run_handler("(?###smth<)(?###,)abcde(?###>)", $options);
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_TEMPLATE);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->position->indfirst === 0);
        $this->assertTrue($root->position->indlast === 29);
    }
}
