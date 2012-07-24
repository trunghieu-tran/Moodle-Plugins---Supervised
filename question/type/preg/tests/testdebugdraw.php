<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

class qtype_preg_draw_test extends PHPUnit_Framework_TestCase {
    protected $matcher;

    function setUp() {
        $this->matcher  = new qtype_preg_nondeterministic_fa;
    }

    protected function run_parser($regex) {
        $parser = new preg_parser_yyParser;
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
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
        fclose($pseudofile);
        return $parser;
    }

    function test_draw_ast() {
        $parser = $this->run_parser('(ab|cd*$|){3,100}');
        $root = $parser->get_root();
        $regexhandler = new qtype_preg_regex_handler();
        $dir = $regexhandler->get_temp_dir('nodes');
        qtype_preg_regex_handler::execute_dot($root->dot_script(new qtype_preg_dot_style_provider()), $dir . 'ast_test.png');
    }
    /*function test_simple() {//[asdf]
        $this->matcher->input_fa('0->asdf->1;');
        $this->matcher->draw('simple.dot', 'simple.jpg');
    }
    function test_complex() {//(?:a|b)*abb
        $this->matcher->input_fa('0->a->1;0->b->0;1->a->1;1->b->2;2->b->3;2->a->1;3->a->1;3->b->0;');
        $this->matcher->draw('complex.dot', 'complex.jpg');
    }
    function test_subpattern() {//(a)(bc)
        $this->matcher->input_fa('0->#s1e1#a->1;1->#s2#b->2;2->#e2#c->3;');
        $this->matcher->draw('subpattern.dot', 'subpattern.jpg');
    }*/
}
