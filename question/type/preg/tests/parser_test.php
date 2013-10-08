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
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');

class qtype_preg_parser_test extends PHPUnit_Framework_TestCase {

    /**
     * Service function to run parser on regex.
     * @param regex regular expression to parse.
     * @param options qtype_preg_handling_options
     * @return parser object.
     */
    protected function run_parser($regex, &$errors, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }

        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);

        $parser = new qtype_preg_yyParser($options);

        while ($token = $lexer->nextToken()) {
            if (!is_array($token)) {
                $parser->doParse($token->type, $token->value);
            } else {
                 foreach ($token as $curtoken) {
                    $parser->doParse($curtoken->type, $curtoken->value);
                }
            }
        }
        $parser->doParse(0, 0);
        $errors = array();
        foreach ($lexer->get_error_nodes() as $node) {
            $errors[] = $node;
        }
        foreach($parser->get_error_nodes() as $node) {
            $errors[] = $node;
        }
        fclose($pseudofile);
        return $parser;
    }
    function test_parser_dummy_1() {
        $parser = $this->run_parser('a', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(1));
        $this->assertTrue($root->lastpos == array(1));
        $this->assertTrue($followpos === array());
    }
    function test_parser_dummy_2() {
        $parser = $this->run_parser('$', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(1));
        $this->assertTrue($root->lastpos == array(1));
        $this->assertTrue($followpos === array());
    }
    function test_parser_concatenation() {
        $parser = $this->run_parser('ab', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(3));
        $this->assertTrue($followpos == array(2 => array(3)));
    }
    function test_parser_alt() {
        $parser = $this->run_parser('a|b|c|d', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos === array());
        $parser = $this->run_parser('a|', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos === array());
        $parser = $this->run_parser('|a', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->position->indfirst === 0 && $root->position->indlast === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[0]->position->indfirst === 0 && $root->operands[0]->position->indlast === -1);
    }
    function test_parser_grouping() {
        $parser = $this->run_parser('(?:ab)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(3 => array(4)));
    }
    function test_parser_subexpr() {
        $parser = $this->run_parser('(ab)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
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
        $this->assertTrue($followpos == array(3 => array(4)));
    }
    function test_parser_qu() {
        $parser = $this->run_parser('(?:ab)??', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(4 => array(5)));
    }
    function test_parser_aster() {
        $parser = $this->run_parser('(?:[a-z\w]b)*', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '*');
        $this->assertTrue($root->greedy);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription === array());
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[1]->data === 'a-z');
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[2]->data === '\w');
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->userinscription[2]->type === qtype_preg_userinscription::TYPE_FLAG);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(4));
        $this->assertTrue($root->lastpos == array(5));
        $this->assertTrue($followpos == array(4 => array(5), 5 => array(4)));
    }
    function test_parser_plus() {
        $parser = $this->run_parser('(?:[\wab-yz\d])++', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '++');
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 0);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 16);
        $this->assertTrue($root->possessive);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[1]->data === '\w');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[1]->type === qtype_preg_userinscription::TYPE_FLAG);
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[2]->data === 'a');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[3]->data === 'b-y');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[4]->data === 'z');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[5]->data === '\d');
        $this->assertTrue($root->operands[0]->operands[0]->userinscription[5]->type === qtype_preg_userinscription::TYPE_FLAG);
        $this->assertTrue($root->operands[0]->operands[0]->position->colfirst === 3);
        $this->assertTrue($root->operands[0]->operands[0]->position->collast === 13);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
        $this->assertTrue($followpos == array(3 => array(3)));
    }
    function test_parser_brace() {
        $parser = $this->run_parser('[^\p{Egyptian_Hieroglyphs}]{8,}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->userinscription[0]->data === '{8,}');
        $this->assertTrue($root->greedy);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription[1]->data === '\p{Egyptian_Hieroglyphs}');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos == array(2 => array(2)));
    }
    function test_parser_cond_subexpr() {
        $parser = $this->run_parser('(?(?=a)b|cd)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        // TODO: add tests for followpos when it's implemented for conditional subexpressions.
        $parser = $this->run_parser('(?(DEFINE)a|b)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->userinscription[0]->data === '(?(DEFINE)...|...)');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->userinscription[0]->data === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->userinscription[0]->data === 'b');
        // TODO: add tests for followpos when it's implemented for conditional subexpressions.
    }
    function test_parser_easy_regex() {
        $parser = $this->run_parser('a|b', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos === array());
    }
    function test_parser_quantifier() {
        $parser = $this->run_parser('ab+', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(2 => array(4), 4 => array(4)));
    }
    function test_parser_concat_and_quant() {
        $parser = $this->run_parser('abc?d?ef?', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(2 => array(3), 3 => array(5, 7, 8), 5 => array(7, 8), 7 => array(8), 8 => array(10)));
    }
    function test_parser_alt_and_quantifier() {
        $parser = $this->run_parser('a*|b', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(3 => array(3)));
    }
    function test_parser_alt_and_concat() {
        $parser = $this->run_parser('ab|cd', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(3 => array(4), 6 => array(7)));
    }
    function test_parser_conditional_subexpression() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser('(?(name)a|b|c)', $errornodes, $options);
        $this->assertTrue(count($errornodes) == 2);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->number === 'name');
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        // TODO: add tests for followpos when it's implemented for conditional subexpressions.
    }
    function test_parser_long_regex() {
        $parser = $this->run_parser('(?:a|b)*abb', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->operands[0]->nullable === true);
        $this->assertTrue($root->operands[1]->nullable === false);
        $this->assertTrue($root->firstpos == array(5, 6, 7));
        $this->assertTrue($root->lastpos == array(9));
        $this->assertTrue($followpos == array(5 => array(5, 6, 7), 6 => array(5, 6, 7), 7 => array(8), 8 => array(9)));
    }
    function test_parser_two_anchors() {
        $parser = $this->run_parser('^a$', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_A);   // Converted by lexer.
        $this->assertTrue($root->operands[0]->userinscription[0]->data === '^');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[2]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_Z);   // Converted by lexer.
        $this->assertTrue($root->operands[2]->negative);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(4));
        $this->assertTrue($followpos == array(2 => array(3), 3 => array(4)));
    }
    function test_parser_start_anchor() {
        $parser = $this->run_parser('^a', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_A);   // Converted by lexer.
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
    }
    function test_parser_end_anchor() {
        $parser = $this->run_parser('a$', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_Z);   // Converted by lexer.
        $this->assertTrue($root->operands[1]->negative);
    }
    function test_parser_error() {
        $parser = $this->run_parser('^((ab|cd)ef$', $errornodes);
        $this->assertTrue(count($errornodes) > 0);
    }
    function test_parser_no_error() {
        $parser = $this->run_parser('((ab|cd)ef)', $errornodes);
        $this->assertTrue(empty($errornodes));
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(6, 9));
        $this->assertTrue($root->lastpos == array(12));
        $this->assertTrue($followpos == array(6 => array(7), 9 => array(10), 7 => array(11), 10 => array(11), 11 => array(12)));
    }
    function test_parser_asserts() {
        $parser = $this->run_parser('(?<=\w)(?<!_)a*(?=\w)(?!_)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array(2 => array(4), 4 => array(7, 8), 7 => array(7, 8), 8 => array(10)));
    }
    function test_parser_metasymbol_dot() {
        $parser = $this->run_parser('.', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->flags[0][0]->data->string() === "\n");
        $this->assertTrue($root->flags[0][0]->negative);
    }
    function test_parser_word_break() {
        $parser = $this->run_parser('a\b', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertFalse($root->operands[1]->negative);
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(3));
        $this->assertTrue($followpos == array(2 => array(3)));
    }
    function test_parser_word_not_break() {
        $parser = $this->run_parser('a\B', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_ASSERT);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $this->assertTrue($root->operands[1]->negative);
    }
    function test_parser_alt_all_forms() {
        $parser = $this->run_parser('a|b', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2, 3));
        $this->assertTrue($root->lastpos == array(2, 3));
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('a|', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('|a', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('a|b|', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('a||', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue(count($root->operands) == 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('||a', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue(count($root->operands) == 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(3));
        $this->assertTrue($root->lastpos == array(3));
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('|', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('||', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('(?:|)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[0]->nullable === true);
        $this->assertTrue($root->operands[0]->firstpos == array());
        $this->assertTrue($root->operands[0]->lastpos == array());
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('(|||||)', $errornodes);    // баян
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array());
        $this->assertTrue($root->lastpos == array());
        $this->assertTrue($followpos == array());
        $parser = $this->run_parser('(|a||b|c||)', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
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
        $this->assertTrue($followpos == array());
    }
    function test_parser_subexpressions() {
        $parser = $this->run_parser('((?:(?(?=a)(?>b)|a)))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY);
    }
    function test_parser_duplicate_subexpression_numbers() {
        $parser = $this->run_parser('(?|a|b|c)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->flags[0][0]->data->string() === 'a');
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->flags[0][0]->data->string() === 'b');
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[2]->flags[0][0]->data->string() === 'c');
    }
    function test_parser_index() {
        $parser = $this->run_parser('abcdefgh|(abcd)*', $errornodes);
        $root = $parser->get_root();
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
        $parser = $this->run_parser('\89', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->flags[0][0]->data->string() === chr(0));
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->flags[0][0]->data->string() === '8');
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->flags[0][0]->data->string() === '9');
    }
    function test_parser_nested_subexprs() {
        $parser = $this->run_parser('((?|(a)|(b(c)))(d))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
    }
    function test_preserve_all_nodes() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser('(?:a)', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
    }
    function test_pcre_strict() {
        // Empty parentheses should be empty subexpression.
        $parser = $this->run_parser('()', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Nested empty parentheses.
        $parser = $this->run_parser('((?=))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty parentheses with concatenation.
        $parser = $this->run_parser('a()b', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        // Empty assertion.
        $parser = $this->run_parser('(?=)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->subtype === qtype_preg_node_assert::SUBTYPE_PLA);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty conditional subexpression.
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser('(?(<name>))', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->number === 'name');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Empty conditional subexpression with empty assertion but not empty branches.
        $parser = $this->run_parser('(?(?<=)a)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_PLB);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        // Conditional subexpression with assertion and empty body.
        $parser = $this->run_parser('(?(?!a))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_NLA);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with empty assertion and empty body.
        $parser = $this->run_parser('(?(?<!))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(empty($errornodes));
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_ASSERT);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_node_assert::SUBTYPE_NLB);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with some condition and empty body.
        $parser = $this->run_parser('(?(+1))', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 5);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->number === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Conditional subexpression with some condition and empty body (same as the previous one but named).
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser('(?(<name>))', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) == 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 9);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR);
        $this->assertTrue($root->number === 'name');
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    }
    function test_errors() {
        // Unclosed square brackets.
        $parser = $this->run_parser('ab(c|d)[fg\\]', $errornodes);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET);
        $this->assertTrue($errornodes[0]->position->colfirst === 7);
        // Unclosed parenthesis.
        $parser = $this->run_parser('a(b(?:c(?=d(?!e(?<=f(?<!g(?>h', $errornodes);
        $this->assertTrue(count($errornodes) === 7);
        $this->assertFalse(empty($errornodes[0]->operands));
        $this->assertFalse(empty($errornodes[1]->operands));
        $this->assertFalse(empty($errornodes[2]->operands));
        $this->assertFalse(empty($errornodes[3]->operands));
        $this->assertFalse(empty($errornodes[4]->operands));
        $this->assertFalse(empty($errornodes[5]->operands));
        $this->assertFalse(empty($errornodes[6]->operands));
        $root = $parser->get_root();
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->operands[1]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        // Unopened parenthesis.
        $parser = $this->run_parser(')ab(c|d)eg)', $errornodes);
        $this->assertTrue(count($errornodes) === 2);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errornodes[1]->position->colfirst === 10);
        $root = $parser->get_root();
        $this->assertTrue($errornodes[1]->operands[0] === $root->operands[0]);
        // Several unopened and unclosed parenthesis.
        $parser = $this->run_parser(')a)b)e(((g(', $errornodes);
        $this->assertTrue(count($errornodes) === 7);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertFalse(empty($errornodes[1]->operands));
        $this->assertFalse(empty($errornodes[2]->operands));
        $this->assertTrue(empty($errornodes[3]->operands));
        $this->assertFalse(empty($errornodes[4]->operands));
        $this->assertFalse(empty($errornodes[5]->operands));
        $this->assertFalse(empty($errornodes[6]->operands));
        // Quantifiers without argument inside parentheses.
        $parser = $this->run_parser('?a({2,3})c(+)e(+)(*s)f', $errornodes);
        $this->assertTrue(count($errornodes) === 5);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errornodes[0]->position->colfirst === 17);
        $this->assertTrue($errornodes[0]->position->collast === 20);
        $this->assertTrue($errornodes[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[1]->position->colfirst === 0);
        $this->assertTrue($errornodes[1]->position->collast === 0);
        $this->assertTrue($errornodes[2]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[2]->position->colfirst === 3);
        $this->assertTrue($errornodes[2]->position->collast === 7);
        $this->assertTrue($errornodes[3]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[3]->position->colfirst === 11);
        $this->assertTrue($errornodes[3]->position->collast === 11);
        $this->assertTrue($errornodes[4]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[4]->position->colfirst === 15);
        $this->assertTrue($errornodes[4]->position->collast === 15);
        $this->assertTrue(empty($errornodes[0]->operands));
        $this->assertTrue(empty($errornodes[1]->operands));
        $this->assertTrue(empty($errornodes[2]->operands));
        $this->assertTrue(empty($errornodes[3]->operands));
        $this->assertTrue(empty($errornodes[4]->operands));
        // Test error reporting for conditional subexpressions, which are particulary tricky.
        // Three or more alternations in conditional subexpression.
        $parser = $this->run_parser('(?(?=bc)dd|e*f|hhh)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue($root->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($root->errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($root->errors[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER);
        $this->assertTrue($root->errors[0]->position->colfirst === 0);
        $this->assertTrue($root->errors[0]->position->collast === 18);
        // Correct situation: alternations are nested within two alternations for conditional subexpression.
        $parser = $this->run_parser('(?(?=bc)(dd|e*f)|(hhh|ff))', $errornodes);
        $this->assertFalse($parser->errors_exist());
        // Unclosed second parenthesis.
        $parser = $this->run_parser('a(?(?=bc)dd|e*f|hhh', $errornodes);
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->position->colfirst === 1);
        $this->assertTrue($errornodes[0]->position->collast === 5);
        $this->assertTrue(is_a($errornodes[0]->operands[0], 'qtype_preg_node_alt'));//There should be two operands for such error: alternation and expression inside assertion
        $this->assertTrue(is_a($errornodes[0]->operands[1], 'qtype_preg_node_concat'));
        // Two parentheses unclosed.
        $parser = $this->run_parser('(?(?=bce*f|hhh', $errornodes);
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 4);
        $this->assertTrue(is_a($errornodes[0]->operands[0], 'qtype_preg_node_alt'));
        // Conditional subexpression starts at the end of expression.
        $parser = $this->run_parser('ab(?(?=', $errornodes);
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errornodes[0]->position->colfirst === 2);
        $this->assertTrue($errornodes[0]->position->collast === 6);
        $this->assertTrue(empty($errornodes[1]->operands));
        // Conditional subexpression with empty condition is error.
        $parser = $this->run_parser('(?()a)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('(?()yes|no)', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        // Conditional subexpression with empty condition is error (same as the previous one but with empty body).
        $parser = $this->run_parser('(?())', $errornodes);
        $root = $parser->get_root();
        $this->assertTrue(count($errornodes) === 1);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 3);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->operands[0]->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        // Everything possible.
        $parser = $this->run_parser('(*UTF9))((?(?=x)a|b|c)({5,4})(?i-i)[[:hamster:]]\p{Squirrel}', $errornodes);
        $this->assertTrue(count($errornodes) === 9);
        $this->assertTrue(count($errornodes[0]->operands) === 0);
        $this->assertTrue($errornodes[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errornodes[0]->position->colfirst === 0);
        $this->assertTrue($errornodes[0]->position->collast === 6);
        $this->assertTrue($errornodes[0]->addinfo === '(*UTF9)');
        $this->assertTrue(count($errornodes[1]->operands) === 0);
        $this->assertTrue($errornodes[1]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[1]->subtype === qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE);
        $this->assertTrue($errornodes[1]->position->colfirst === 23);
        $this->assertTrue($errornodes[1]->position->collast === 27);
        $this->assertTrue(count($errornodes[2]->operands) === 0);
        $this->assertTrue($errornodes[2]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[2]->subtype === qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER);
        $this->assertTrue($errornodes[2]->position->colfirst === 29);
        $this->assertTrue($errornodes[2]->position->collast === 34);
        $this->assertTrue(count($errornodes[3]->operands) === 0);
        $this->assertTrue($errornodes[3]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[3]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS);
        $this->assertTrue($errornodes[3]->position->colfirst === 36);
        $this->assertTrue($errornodes[3]->position->collast === 46);
        $this->assertTrue($errornodes[3]->addinfo === '[:hamster:]');
        $this->assertTrue(count($errornodes[4]->operands) === 0);
        $this->assertTrue($errornodes[4]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[4]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY);
        $this->assertTrue($errornodes[4]->position->colfirst === 48);
        $this->assertTrue($errornodes[4]->position->collast === 59);
        $this->assertTrue($errornodes[4]->addinfo === 'Squirrel');
        $this->assertTrue(count($errornodes[5]->operands) === 1);
        $this->assertTrue($errornodes[5]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[5]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN);
        $this->assertTrue($errornodes[5]->position->colfirst === 7);
        $this->assertTrue($errornodes[5]->position->collast === 7);
        $this->assertTrue($errornodes[5]->operands[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[5]->operands[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE);
        $this->assertTrue($errornodes[6]->subtype === qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER);
        $this->assertTrue($errornodes[6]->position->colfirst === 9);
        $this->assertTrue($errornodes[6]->position->collast === 21);
        $this->assertTrue(count($errornodes[7]->operands) === 0);
        $this->assertTrue($errornodes[7]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[7]->subtype === qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER);
        $this->assertTrue($errornodes[7]->position->colfirst === 23);
        $this->assertTrue($errornodes[7]->position->collast === 27);
        $this->assertTrue(count($errornodes[8]->operands) === 1);
        $this->assertTrue($errornodes[8]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errornodes[8]->subtype === qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN);
        $this->assertTrue($errornodes[8]->position->colfirst === 8);
        $this->assertTrue($errornodes[8]->position->collast === 8);
        $this->assertTrue(is_a($errornodes[8]->operands[0], 'qtype_preg_node_concat'));
    }
    function test_parser_followpos_infinite_quant() {
        $parser = $this->run_parser('a*', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array(2 => array(2)));
        $parser = $this->run_parser('a+', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array(2 => array(2)));
        $parser = $this->run_parser('a{2,}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array(2 => array(2)));
        $parser = $this->run_parser('a{10,}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array(2 => array(2)));
    }
    function test_parser_followpos_finite_quant() {
        $parser = $this->run_parser('a{0}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === true);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array());
        $parser = $this->run_parser('a{1}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array());
        $parser = $this->run_parser('a{2}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array());
        $parser = $this->run_parser('a{2,10}', $errornodes);
        $root = $parser->get_root();
        $followpos = $parser->get_followpos();
        $this->assertTrue($root->nullable === false);
        $this->assertTrue($root->firstpos == array(2));
        $this->assertTrue($root->lastpos == array(2));
        $this->assertTrue($followpos === array());  // TODO: is this right?
    }
    function test_parser_quant_expanding() {
        $options = new qtype_preg_handling_options;
        $options->expandquantifiers = true;
        $parser = $this->run_parser('a{0}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_LEAF_META);
        $this->assertTrue($root->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $parser = $this->run_parser('a{1}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $parser = $this->run_parser('a{2}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{4}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 4);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[3]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{2,3}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 3);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->operands[2]->leftborder === 0);
        $this->assertTrue($root->operands[2]->rightborder === 1);
        $this->assertTrue($root->operands[2]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{2,4}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 4);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->operands[2]->leftborder === 0);
        $this->assertTrue($root->operands[2]->rightborder === 1);
        $this->assertTrue($root->operands[2]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[3]->type === qtype_preg_node::TYPE_NODE_FINITE_QUANT);
        $this->assertTrue($root->operands[3]->leftborder === 0);
        $this->assertTrue($root->operands[3]->rightborder === 1);
        $this->assertTrue($root->operands[3]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a*', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->leftborder === 0);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{1,}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->leftborder === 1);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{2,}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 2);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[1]->leftborder === 1);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $parser = $this->run_parser('a{3,}', $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue(count($root->operands) === 3);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->operands[2]->leftborder === 1);
        $this->assertTrue($root->operands[2]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
    }
    function test_multiline_regex() {
        $options = new qtype_preg_handling_options;
        $options->preserveallnodes = true;
        $parser = $this->run_parser("a\nbcd\nef", $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 2);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 1);
        $parser = $this->run_parser("(?:a(?#com\r\nment\nhere)bcd\nef)+", $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 3);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 3);
        $parser = $this->run_parser("(a\nbcd\n\r\n\nef)", $errornodes, $options);
        $root = $parser->get_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->position->linefirst === 0);
        $this->assertTrue($root->position->linelast === 4);
        $this->assertTrue($root->position->colfirst === 0);
        $this->assertTrue($root->position->collast === 2);
    }
}
