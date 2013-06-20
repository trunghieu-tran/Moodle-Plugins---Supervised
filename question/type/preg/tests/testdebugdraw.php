<?php

/*
   To deal with $CFG->path add the following line to phpunit.xml inside <php> ... </php> tags.
   <const name="QTYPE_PREG_TEST_CONFIG_PATHTODOT" value="/usr/bin/dot"/>
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');

class qtype_preg_draw_test extends PHPUnit_Framework_TestCase {
    protected $matcher;

    function setUp() {
        $this->matcher  = new qtype_preg_nondeterministic_fa;
    }

    protected function run_parser($regex) {
        $parser = new qtype_preg_yyParser();
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
        $parser = $this->run_parser('(ab|[^c][de\\w]*$|){3,100}');
        $root = $parser->get_root();
        $regexhandler = new qtype_preg_regex_handler();
        $dir = $regexhandler->get_temp_dir('nodes');

        $dotscript = $root->dot_script(new qtype_preg_dot_node_context(true, 8));

        qtype_preg_regex_handler::execute_dot($dotscript, 'svg', $dir . 'ast_test.svg');

        $str = '<img src="data:image/svg+xml;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'svg')) . '"/>';
        $file = fopen($dir . 'testdrawhtml.html', 'w');
        fwrite($file, $str);
        fclose($file);
    }

    function test_dot_style_provider() {
        $parser = $this->run_parser('(a|)');
        $root1 = $parser->get_root();
        $etalon_dot_instructions1 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;3[label = "( ... )", tooltip = subexpression, id = 3];2[label = "|", tooltip = alternation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "emptiness", tooltip = emptiness, shape = rectangle, id = 1];3->2->0;2->1;}';

        //ERROR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $parser = $this->run_parser('^\\\\a\\b\\A\\Z\\G$');
        $root2 = $parser->get_root();
        $etalon_dot_instructions2 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "assertion ^", tooltip = assertion, shape = rectangle, id = 0];1[label = "[\\\\]", tooltip = "character class", shape = rectangle, id = 1];3[label = "a", tooltip = "character class", shape = rectangle, id = 3];5[label = "assertion \b", tooltip = assertion, shape = rectangle, id = 5];7[label = "assertion \A", tooltip = assertion, shape = rectangle, id = 7];9[label = "assertion \Z", tooltip = assertion, shape = rectangle, id = 9];11[label = "assertion \G", tooltip = assertion, shape = rectangle, id = 11];13[label = "assertion $", tooltip = assertion, shape = rectangle, id = 13];14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;}';

        $parser = $this->run_parser('abc[\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V]');
        $root3 = $parser->get_root();
        $etalon_dot_instructions3 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\v\\V\\w\\W\\s\\S\\d\\D\\h\\H]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc[\\w\\W\\s]');
        $root4 = $parser->get_root();
        $etalon_dot_instructions4 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\w\\W\\s]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc[\\S\\d\\D]');
        $root5 = $parser->get_root();
        $etalon_dot_instructions5 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\S\\d\\D]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('345[\\h\\H\\v\\V]');
        $root6 = $parser->get_root();
        $etalon_dot_instructions6 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "3", tooltip = "character class", shape = rectangle, id = 0];1[label = "4", tooltip = "character class", shape = rectangle, id = 1];3[label = "5", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\v\\V\\h\\H]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc[^\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V]');
        $root7 = $parser->get_root();
        $etalon_dot_instructions7 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[^\\v\\V\\w\\W\\s\\S\\d\\D\\h\\H]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc[^\\w\\W\\s]');
        $root8 = $parser->get_root();
        $etalon_dot_instructions8 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[^\\w\\W\\s]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc[^\\S\\d\\D]');
        $root9 = $parser->get_root();
        $etalon_dot_instructions9 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[^\\S\\d\\D]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('345[^\\h\\H\\v\\V]');
        $root10 = $parser->get_root();
        $etalon_dot_instructions10 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "3", tooltip = "character class", shape = rectangle, id = 0];1[label = "4", tooltip = "character class", shape = rectangle, id = 1];3[label = "5", tooltip = "character class", shape = rectangle, id = 3];5[label = "[^\\v\\V\\h\\H]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('abc\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V');
        $root11 = $parser->get_root();
        $etalon_dot_instructions11 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "concat", tooltip = concatenation, id = 20];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\w]", tooltip = "character class", shape = rectangle, id = 5];7[label = "[\\W]", tooltip = "character class", shape = rectangle, id = 7];9[label = "[\\s]", tooltip = "character class", shape = rectangle, id = 9];11[label = "[\\S]", tooltip = "character class", shape = rectangle, id = 11];13[label = "[\\d]", tooltip = "character class", shape = rectangle, id = 13];15[label = "[\\D]", tooltip = "character class", shape = rectangle, id = 15];17[label = "[\\h]", tooltip = "character class", shape = rectangle, id = 17];19[label = "[\\H]", tooltip = "character class", shape = rectangle, id = 19];21[label = "[\\v]", tooltip = "character class", shape = rectangle, id = 21];23[label = "[\\V]", tooltip = "character class", shape = rectangle, id = 23];24->22->20->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;20->19;22->21;24->23;}';

        $parser = $this->run_parser('abc\\w\\W\\s');
        $root12 = $parser->get_root();
        $etalon_dot_instructions12 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\w]", tooltip = "character class", shape = rectangle, id = 5];7[label = "[\\W]", tooltip = "character class", shape = rectangle, id = 7];9[label = "[\\s]", tooltip = "character class", shape = rectangle, id = 9];10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;}';

        $parser = $this->run_parser('abc\\S\\d\\D');
        $root13 = $parser->get_root();
        $etalon_dot_instructions13 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\S]", tooltip = "character class", shape = rectangle, id = 5];7[label = "[\\d]", tooltip = "character class", shape = rectangle, id = 7];9[label = "[\\D]", tooltip = "character class", shape = rectangle, id = 9];10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;}';

        $parser = $this->run_parser('345\\h\\H\\v\\V');
        $root14 = $parser->get_root();
        $etalon_dot_instructions14 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "3", tooltip = "character class", shape = rectangle, id = 0];1[label = "4", tooltip = "character class", shape = rectangle, id = 1];3[label = "5", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\h]", tooltip = "character class", shape = rectangle, id = 5];7[label = "[\\H]", tooltip = "character class", shape = rectangle, id = 7];9[label = "[\\v]", tooltip = "character class", shape = rectangle, id = 9];11[label = "[\\V]", tooltip = "character class", shape = rectangle, id = 11];12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;}';

        $parser = $this->run_parser('[a-z2-5]33*');
        $root15 = $parser->get_root();
        $etalon_dot_instructions15 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];2[label = "concat", tooltip = concatenation, id = 2];0[label = "[a-z2-5]", tooltip = "character class", shape = rectangle, id = 0];1[label = "3", tooltip = "character class", shape = rectangle, id = 1];4[label = "*", tooltip = "infinite quantifier", id = 4];3[label = "3", tooltip = "character class", shape = rectangle, id = 3];5->2->0;2->1;5->4->3;}';

        $parser = $this->run_parser('[B-D!.].{1,5}23?');
        $root16 = $parser->get_root();
        $etalon_dot_instructions16 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;8[label = "concat", tooltip = concatenation, id = 8];5[label = "concat", tooltip = concatenation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];0[label = "[!.B-D]", tooltip = "character class", shape = rectangle, id = 0];2[label = "{1,5}", tooltip = "finite quantifier", id = 2];1[label = ".", tooltip = "character class", shape = rectangle, id = 1];4[label = "2", tooltip = "character class", shape = rectangle, id = 4];7[label = "?", tooltip = "finite quantifier", id = 7];6[label = "3", tooltip = "character class", shape = rectangle, id = 6];8->5->3->0;3->2->1;5->4;8->7->6;}';

        $parser = $this->run_parser('\\A[^c-z;-](ef)+');
        $root17 = $parser->get_root();
        $etalon_dot_instructions17 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;8[label = "concat", tooltip = concatenation, id = 8];0[label = "assertion \A", tooltip = assertion, shape = rectangle, id = 0];7[label = "concat", tooltip = concatenation, id = 7];1[label = "[^;-c-z]", tooltip = "character class", shape = rectangle, id = 1];6[label = "+", tooltip = "infinite quantifier", id = 6];5[label = "( ... )", tooltip = subexpression, id = 5];4[label = "concat", tooltip = concatenation, id = 4];2[label = "e", tooltip = "character class", shape = rectangle, id = 2];3[label = "f", tooltip = "character class", shape = rectangle, id = 3];8->0;8->7->1;7->6->5->4->2;4->3;}';

        $parser = $this->run_parser('abc{3,7}');
        $root18 = $parser->get_root();
        $etalon_dot_instructions18 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];4[label = "{3,7}", tooltip = "finite quantifier", id = 4];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5->2->0;2->1;5->4->3;}';

        $parser = $this->run_parser('abc{3,}');
        $root19 = $parser->get_root();
        $etalon_dot_instructions19 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];4[label = "{3,}", tooltip = "infinite quantifier", id = 4];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5->2->0;2->1;5->4->3;}';

        $parser = $this->run_parser('abc{,7}');
        $root20 = $parser->get_root();
        $etalon_dot_instructions20 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];4[label = "{,7}", tooltip = "finite quantifier", id = 4];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5->2->0;2->1;5->4->3;}';

        $parser = $this->run_parser('abc{0,7}');
        $root21 = $parser->get_root();
        $etalon_dot_instructions21 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];4[label = "{0,7}", tooltip = "finite quantifier", id = 4];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5->2->0;2->1;5->4->3;}';

        $parser = $this->run_parser('[^a-z]*?');
        $root22 = $parser->get_root();
        $etalon_dot_instructions22 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "*?", tooltip = "infinite quantifier", id = 1];0[label = "[^a-z]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[^A-Z]+?');
        $root23 = $parser->get_root();
        $etalon_dot_instructions23 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "+?", tooltip = "infinite quantifier", id = 1];0[label = "[^A-Z]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[^3-9]{5,}?');
        $root24 = $parser->get_root();
        $etalon_dot_instructions24 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{5,}?", tooltip = "infinite quantifier", id = 1];0[label = "[^3-9]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[^\W33]*+');
        $root25 = $parser->get_root();
        $etalon_dot_instructions25 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "*+", tooltip = "infinite quantifier", id = 1];0[label = "[^33\\W]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[^\D\S]?+');
        $root26 = $parser->get_root();
        $etalon_dot_instructions26 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "?+", tooltip = "finite quantifier", id = 1];0[label = "[^\\D\\S]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[^\w\d]{5,}+');
        $root27 = $parser->get_root();
        $etalon_dot_instructions27 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{5,}+", tooltip = "infinite quantifier", id = 1];0[label = "[^\\w\\d]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('ab(xa)*+a');
        $root28 = $parser->get_root();
        $etalon_dot_instructions28 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];7[label = "concat", tooltip = concatenation, id = 7];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];6[label = "*+", tooltip = "infinite quantifier", id = 6];5[label = "( ... )", tooltip = subexpression, id = 5];4[label = "concat", tooltip = concatenation, id = 4];2[label = "x", tooltip = "character class", shape = rectangle, id = 2];3[label = "a", tooltip = "character class", shape = rectangle, id = 3];9[label = "a", tooltip = "character class", shape = rectangle, id = 9];10->8->0;8->7->1;7->6->5->4->2;4->3;10->9;}';

        $parser = $this->run_parser('a(bc|b|x)cc');
        $root29 = $parser->get_root();
        $etalon_dot_instructions29 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;13[label = "concat", tooltip = concatenation, id = 13];11[label = "concat", tooltip = concatenation, id = 11];9[label = "concat", tooltip = concatenation, id = 9];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];8[label = "( ... )", tooltip = subexpression, id = 8];7[label = "|", tooltip = alternation, id = 7];5[label = "|", tooltip = alternation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];2[label = "c", tooltip = "character class", shape = rectangle, id = 2];4[label = "b", tooltip = "character class", shape = rectangle, id = 4];6[label = "x", tooltip = "character class", shape = rectangle, id = 6];10[label = "c", tooltip = "character class", shape = rectangle, id = 10];12[label = "c", tooltip = "character class", shape = rectangle, id = 12];13->11->9->0;9->8->7->5->3->1;3->2;5->4;7->6;11->10;13->12;}';

        $parser = $this->run_parser('a(?:bc|b|x)cc');
        $root30 = $parser->get_root();
        $etalon_dot_instructions30 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];7[label = "|", tooltip = alternation, id = 7];5[label = "|", tooltip = alternation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];2[label = "c", tooltip = "character class", shape = rectangle, id = 2];4[label = "b", tooltip = "character class", shape = rectangle, id = 4];6[label = "x", tooltip = "character class", shape = rectangle, id = 6];9[label = "c", tooltip = "character class", shape = rectangle, id = 9];11[label = "c", tooltip = "character class", shape = rectangle, id = 11];12->10->8->0;8->7->5->3->1;3->2;5->4;7->6;10->9;12->11;}';

        $parser = $this->run_parser('a(?>bc|b|x)cc');
        $root31 = $parser->get_root();
        $etalon_dot_instructions31 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;13[label = "concat", tooltip = concatenation, id = 13];11[label = "concat", tooltip = concatenation, id = 11];9[label = "concat", tooltip = concatenation, id = 9];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];8[label = "(?> ... )", tooltip = subexpression, id = 8];7[label = "|", tooltip = alternation, id = 7];5[label = "|", tooltip = alternation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];2[label = "c", tooltip = "character class", shape = rectangle, id = 2];4[label = "b", tooltip = "character class", shape = rectangle, id = 4];6[label = "x", tooltip = "character class", shape = rectangle, id = 6];10[label = "c", tooltip = "character class", shape = rectangle, id = 10];12[label = "c", tooltip = "character class", shape = rectangle, id = 12];13->11->9->0;9->8->7->5->3->1;3->2;5->4;7->6;11->10;13->12;}';

        $parser = $this->run_parser('a(?>x*)xa');
        $root32 = $parser->get_root();
        $etalon_dot_instructions32 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];3[label = "(?> ... )", tooltip = subexpression, id = 3];2[label = "*", tooltip = "infinite quantifier", id = 2];1[label = "x", tooltip = "character class", shape = rectangle, id = 1];5[label = "x", tooltip = "character class", shape = rectangle, id = 5];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];8->6->4->0;4->3->2->1;6->5;8->7;}';

        $parser = $this->run_parser('(?-i)(?i:tv)set');
        $root33 = $parser->get_root();
        $etalon_dot_instructions33 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "t", tooltip = "character class", shape = rectangle, id = 0];1[label = "v", tooltip = "character class", shape = rectangle, id = 1];3[label = "s", tooltip = "character class", shape = rectangle, id = 3];5[label = "e", tooltip = "character class", shape = rectangle, id = 5];7[label = "t", tooltip = "character class", shape = rectangle, id = 7];8->6->4->2->0;2->1;4->3;6->5;8->7;}';

        $parser = $this->run_parser('my name is (?=Freedom)');
        $root34 = $parser->get_root();
        $etalon_dot_instructions34 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;35[label = "concat", tooltip = concatenation, id = 35];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "m", tooltip = "character class", shape = rectangle, id = 0];1[label = "y", tooltip = "character class", shape = rectangle, id = 1];3[label = " ", tooltip = "character class", shape = rectangle, id = 3];5[label = "n", tooltip = "character class", shape = rectangle, id = 5];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];9[label = "m", tooltip = "character class", shape = rectangle, id = 9];11[label = "e", tooltip = "character class", shape = rectangle, id = 11];13[label = " ", tooltip = "character class", shape = rectangle, id = 13];15[label = "i", tooltip = "character class", shape = rectangle, id = 15];17[label = "s", tooltip = "character class", shape = rectangle, id = 17];34[label = "concat", tooltip = concatenation, id = 34];19[label = " ", tooltip = "character class", shape = rectangle, id = 19];33[label = "assertion (?= ... )", tooltip = assertion, id = 33];32[label = "concat", tooltip = concatenation, id = 32];30[label = "concat", tooltip = concatenation, id = 30];28[label = "concat", tooltip = concatenation, id = 28];26[label = "concat", tooltip = concatenation, id = 26];24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "F", tooltip = "character class", shape = rectangle, id = 20];21[label = "r", tooltip = "character class", shape = rectangle, id = 21];23[label = "e", tooltip = "character class", shape = rectangle, id = 23];25[label = "e", tooltip = "character class", shape = rectangle, id = 25];27[label = "d", tooltip = "character class", shape = rectangle, id = 27];29[label = "o", tooltip = "character class", shape = rectangle, id = 29];31[label = "m", tooltip = "character class", shape = rectangle, id = 31];35->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;35->34->19;34->33->32->30->28->26->24->22->20;22->21;24->23;26->25;28->27;30->29;32->31;}';

        $parser = $this->run_parser('my name is (?!Freedom)');
        $root35 = $parser->get_root();
        $etalon_dot_instructions35 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;35[label = "concat", tooltip = concatenation, id = 35];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "m", tooltip = "character class", shape = rectangle, id = 0];1[label = "y", tooltip = "character class", shape = rectangle, id = 1];3[label = " ", tooltip = "character class", shape = rectangle, id = 3];5[label = "n", tooltip = "character class", shape = rectangle, id = 5];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];9[label = "m", tooltip = "character class", shape = rectangle, id = 9];11[label = "e", tooltip = "character class", shape = rectangle, id = 11];13[label = " ", tooltip = "character class", shape = rectangle, id = 13];15[label = "i", tooltip = "character class", shape = rectangle, id = 15];17[label = "s", tooltip = "character class", shape = rectangle, id = 17];34[label = "concat", tooltip = concatenation, id = 34];19[label = " ", tooltip = "character class", shape = rectangle, id = 19];33[label = "assertion (?! ... )", tooltip = assertion, id = 33];32[label = "concat", tooltip = concatenation, id = 32];30[label = "concat", tooltip = concatenation, id = 30];28[label = "concat", tooltip = concatenation, id = 28];26[label = "concat", tooltip = concatenation, id = 26];24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "F", tooltip = "character class", shape = rectangle, id = 20];21[label = "r", tooltip = "character class", shape = rectangle, id = 21];23[label = "e", tooltip = "character class", shape = rectangle, id = 23];25[label = "e", tooltip = "character class", shape = rectangle, id = 25];27[label = "d", tooltip = "character class", shape = rectangle, id = 27];29[label = "o", tooltip = "character class", shape = rectangle, id = 29];31[label = "m", tooltip = "character class", shape = rectangle, id = 31];35->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;35->34->19;34->33->32->30->28->26->24->22->20;22->21;24->23;26->25;28->27;30->29;32->31;}';

        $parser = $this->run_parser('my name is (?<=Freedom)');
        $root36 = $parser->get_root();
        $etalon_dot_instructions36 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;35[label = "concat", tooltip = concatenation, id = 35];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "m", tooltip = "character class", shape = rectangle, id = 0];1[label = "y", tooltip = "character class", shape = rectangle, id = 1];3[label = " ", tooltip = "character class", shape = rectangle, id = 3];5[label = "n", tooltip = "character class", shape = rectangle, id = 5];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];9[label = "m", tooltip = "character class", shape = rectangle, id = 9];11[label = "e", tooltip = "character class", shape = rectangle, id = 11];13[label = " ", tooltip = "character class", shape = rectangle, id = 13];15[label = "i", tooltip = "character class", shape = rectangle, id = 15];17[label = "s", tooltip = "character class", shape = rectangle, id = 17];34[label = "concat", tooltip = concatenation, id = 34];19[label = " ", tooltip = "character class", shape = rectangle, id = 19];33[label = "assertion (?<= ... )", tooltip = assertion, id = 33];32[label = "concat", tooltip = concatenation, id = 32];30[label = "concat", tooltip = concatenation, id = 30];28[label = "concat", tooltip = concatenation, id = 28];26[label = "concat", tooltip = concatenation, id = 26];24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "F", tooltip = "character class", shape = rectangle, id = 20];21[label = "r", tooltip = "character class", shape = rectangle, id = 21];23[label = "e", tooltip = "character class", shape = rectangle, id = 23];25[label = "e", tooltip = "character class", shape = rectangle, id = 25];27[label = "d", tooltip = "character class", shape = rectangle, id = 27];29[label = "o", tooltip = "character class", shape = rectangle, id = 29];31[label = "m", tooltip = "character class", shape = rectangle, id = 31];35->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;35->34->19;34->33->32->30->28->26->24->22->20;22->21;24->23;26->25;28->27;30->29;32->31;}';

        $parser = $this->run_parser('my name is (?<!Freedom)');
        $root37 = $parser->get_root();
        $etalon_dot_instructions37 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;35[label = "concat", tooltip = concatenation, id = 35];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "m", tooltip = "character class", shape = rectangle, id = 0];1[label = "y", tooltip = "character class", shape = rectangle, id = 1];3[label = " ", tooltip = "character class", shape = rectangle, id = 3];5[label = "n", tooltip = "character class", shape = rectangle, id = 5];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];9[label = "m", tooltip = "character class", shape = rectangle, id = 9];11[label = "e", tooltip = "character class", shape = rectangle, id = 11];13[label = " ", tooltip = "character class", shape = rectangle, id = 13];15[label = "i", tooltip = "character class", shape = rectangle, id = 15];17[label = "s", tooltip = "character class", shape = rectangle, id = 17];34[label = "concat", tooltip = concatenation, id = 34];19[label = " ", tooltip = "character class", shape = rectangle, id = 19];33[label = "assertion (?<! ... )", tooltip = assertion, id = 33];32[label = "concat", tooltip = concatenation, id = 32];30[label = "concat", tooltip = concatenation, id = 30];28[label = "concat", tooltip = concatenation, id = 28];26[label = "concat", tooltip = concatenation, id = 26];24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "F", tooltip = "character class", shape = rectangle, id = 20];21[label = "r", tooltip = "character class", shape = rectangle, id = 21];23[label = "e", tooltip = "character class", shape = rectangle, id = 23];25[label = "e", tooltip = "character class", shape = rectangle, id = 25];27[label = "d", tooltip = "character class", shape = rectangle, id = 27];29[label = "o", tooltip = "character class", shape = rectangle, id = 29];31[label = "m", tooltip = "character class", shape = rectangle, id = 31];35->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;35->34->19;34->33->32->30->28->26->24->22->20;22->21;24->23;26->25;28->27;30->29;32->31;}';

        $parser = $this->run_parser('(?(?<=a)m|d)');
        $root38 = $parser->get_root();
        $etalon_dot_instructions38 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "(?(?<= ... ) ... | .... )", tooltip = "conditional subexpression", id = 5];1[label = "m", tooltip = "character class", shape = rectangle, id = 1];2[label = "d", tooltip = "character class", shape = rectangle, id = 2];4[label = "assertion (?<= ... )", tooltip = assertion, id = 4];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];5->1;5->2;5->4->0;}';

        $parser = $this->run_parser('(a)?(?(?1)m|d)');
        $root39 = $parser->get_root();
        $etalon_dot_instructions39 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;13[label = "ERROR ", tooltip = error, id = 13];12[label = "|", tooltip = alternation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];2[label = "?", tooltip = "finite quantifier", id = 2];1[label = "( ... )", tooltip = subexpression, id = 1];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];6[label = "assertion (?( ... )", tooltip = assertion, id = 6];5[label = "concat", tooltip = concatenation, id = 5];3[label = "ERROR ", tooltip = error, id = 3];4[label = "1", tooltip = "character class", shape = rectangle, id = 4];9[label = "m", tooltip = "character class", shape = rectangle, id = 9];11[label = "d", tooltip = "character class", shape = rectangle, id = 11];13->12->10->8->2->1->0;8->6->5->5->4;10->9;12->11;}';

        $parser = $this->run_parser('a\.?');
        $root40 = $parser->get_root();
        $etalon_dot_instructions40 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;3[label = "concat", tooltip = concatenation, id = 3];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];2[label = "?", tooltip = "finite quantifier", id = 2];1[label = "[\\.]", tooltip = "character class", shape = rectangle, id = 1];3->0;3->2->1;}';

        $parser = $this->run_parser('a\\\\b');
        $root41 = $parser->get_root();
        $etalon_dot_instructions41 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "[\\\\]", tooltip = "character class", shape = rectangle, id = 1];3[label = "b", tooltip = "character class", shape = rectangle, id = 3];4->2->0;2->1;4->3;}';

        $parser = $this->run_parser('a\[F\]');
        $root42 = $parser->get_root();
        $etalon_dot_instructions42 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "[\\[]", tooltip = "character class", shape = rectangle, id = 1];3[label = "F", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\]]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('\Q+-*/\E');
        $root43 = $parser->get_root();
        $etalon_dot_instructions43 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "[\\Q+-*/\\E]", tooltip = "character class", shape = rectangle, id = 0];1[label = "[\\Q+-*/\\E]", tooltip = "character class", shape = rectangle, id = 1];3[label = "[\\Q+-*/\\E]", tooltip = "character class", shape = rectangle, id = 3];5[label = "[\\Q+-*/\\E]", tooltip = "character class", shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('(a+a+)+a');
        $root44 = $parser->get_root();
        $etalon_dot_instructions44 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;8[label = "concat", tooltip = concatenation, id = 8];6[label = "+", tooltip = "infinite quantifier", id = 6];5[label = "( ... )", tooltip = subexpression, id = 5];4[label = "concat", tooltip = concatenation, id = 4];1[label = "+", tooltip = "infinite quantifier", id = 1];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];3[label = "+", tooltip = "infinite quantifier", id = 3];2[label = "a", tooltip = "character class", shape = rectangle, id = 2];7[label = "a", tooltip = "character class", shape = rectangle, id = 7];8->6->5->4->1->0;4->3->2;8->7;}';

        $parser = $this->run_parser(':::1:::0:|:::1:1:0:');
        $root45 = $parser->get_root();
        $etalon_dot_instructions45 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;34[label = "|", tooltip = alternation, id = 34];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = ":", tooltip = "character class", shape = rectangle, id = 0];1[label = ":", tooltip = "character class", shape = rectangle, id = 1];3[label = ":", tooltip = "character class", shape = rectangle, id = 3];5[label = "1", tooltip = "character class", shape = rectangle, id = 5];7[label = ":", tooltip = "character class", shape = rectangle, id = 7];9[label = ":", tooltip = "character class", shape = rectangle, id = 9];11[label = ":", tooltip = "character class", shape = rectangle, id = 11];13[label = "0", tooltip = "character class", shape = rectangle, id = 13];15[label = ":", tooltip = "character class", shape = rectangle, id = 15];33[label = "concat", tooltip = concatenation, id = 33];31[label = "concat", tooltip = concatenation, id = 31];29[label = "concat", tooltip = concatenation, id = 29];27[label = "concat", tooltip = concatenation, id = 27];25[label = "concat", tooltip = concatenation, id = 25];23[label = "concat", tooltip = concatenation, id = 23];21[label = "concat", tooltip = concatenation, id = 21];19[label = "concat", tooltip = concatenation, id = 19];17[label = ":", tooltip = "character class", shape = rectangle, id = 17];18[label = ":", tooltip = "character class", shape = rectangle, id = 18];20[label = ":", tooltip = "character class", shape = rectangle, id = 20];22[label = "1", tooltip = "character class", shape = rectangle, id = 22];24[label = ":", tooltip = "character class", shape = rectangle, id = 24];26[label = "1", tooltip = "character class", shape = rectangle, id = 26];28[label = ":", tooltip = "character class", shape = rectangle, id = 28];30[label = "0", tooltip = "character class", shape = rectangle, id = 30];32[label = ":", tooltip = "character class", shape = rectangle, id = 32];34->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;34->33->31->29->27->25->23->21->19->17;19->18;21->20;23->22;25->24;27->26;29->28;31->30;33->32;}';

        $parser = $this->run_parser(':::1:::0:|:::1:1:1:::');
        $root46 = $parser->get_root();
        $etalon_dot_instructions46 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;38[label = "|", tooltip = alternation, id = 38];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = ":", tooltip = "character class", shape = rectangle, id = 0];1[label = ":", tooltip = "character class", shape = rectangle, id = 1];3[label = ":", tooltip = "character class", shape = rectangle, id = 3];5[label = "1", tooltip = "character class", shape = rectangle, id = 5];7[label = ":", tooltip = "character class", shape = rectangle, id = 7];9[label = ":", tooltip = "character class", shape = rectangle, id = 9];11[label = ":", tooltip = "character class", shape = rectangle, id = 11];13[label = "0", tooltip = "character class", shape = rectangle, id = 13];15[label = ":", tooltip = "character class", shape = rectangle, id = 15];37[label = "concat", tooltip = concatenation, id = 37];35[label = "concat", tooltip = concatenation, id = 35];33[label = "concat", tooltip = concatenation, id = 33];31[label = "concat", tooltip = concatenation, id = 31];29[label = "concat", tooltip = concatenation, id = 29];27[label = "concat", tooltip = concatenation, id = 27];25[label = "concat", tooltip = concatenation, id = 25];23[label = "concat", tooltip = concatenation, id = 23];21[label = "concat", tooltip = concatenation, id = 21];19[label = "concat", tooltip = concatenation, id = 19];17[label = ":", tooltip = "character class", shape = rectangle, id = 17];18[label = ":", tooltip = "character class", shape = rectangle, id = 18];20[label = ":", tooltip = "character class", shape = rectangle, id = 20];22[label = "1", tooltip = "character class", shape = rectangle, id = 22];24[label = ":", tooltip = "character class", shape = rectangle, id = 24];26[label = "1", tooltip = "character class", shape = rectangle, id = 26];28[label = ":", tooltip = "character class", shape = rectangle, id = 28];30[label = "1", tooltip = "character class", shape = rectangle, id = 30];32[label = ":", tooltip = "character class", shape = rectangle, id = 32];34[label = ":", tooltip = "character class", shape = rectangle, id = 34];36[label = ":", tooltip = "character class", shape = rectangle, id = 36];38->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;38->37->35->33->31->29->27->25->23->21->19->17;19->18;21->20;23->22;25->24;27->26;29->28;31->30;33->32;35->34;37->36;}';

        $parser = $this->run_parser('[[:upper:]]A');
        $root47 = $parser->get_root();
        $etalon_dot_instructions47 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;2[label = "concat", tooltip = concatenation, id = 2];0[label = "[[:upper:]]", tooltip = "character class", shape = rectangle, id = 0];1[label = "A", tooltip = "character class", shape = rectangle, id = 1];2->0;2->1;}';

        $parser = $this->run_parser('[[:lower:]]+az');
        $root48 = $parser->get_root();
        $etalon_dot_instructions48 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "+", tooltip = "infinite quantifier", id = 1];0[label = "[[:lower:]]", tooltip = "character class", shape = rectangle, id = 0];2[label = "a", tooltip = "character class", shape = rectangle, id = 2];4[label = "z", tooltip = "character class", shape = rectangle, id = 4];5->3->1->0;3->2;5->4;}';

        $parser = $this->run_parser('[[:alpha:]]*35');
        $root49 = $parser->get_root();
        $etalon_dot_instructions49 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "*", tooltip = "infinite quantifier", id = 1];0[label = "[[:alpha:]]", tooltip = "character class", shape = rectangle, id = 0];2[label = "3", tooltip = "character class", shape = rectangle, id = 2];4[label = "5", tooltip = "character class", shape = rectangle, id = 4];5->3->1->0;3->2;5->4;}';

        $parser = $this->run_parser('[[:digit:]]?Z');
        $root50 = $parser->get_root();
        $etalon_dot_instructions50 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;3[label = "concat", tooltip = concatenation, id = 3];1[label = "?", tooltip = "finite quantifier", id = 1];0[label = "[[:digit:]]", tooltip = "character class", shape = rectangle, id = 0];2[label = "Z", tooltip = "character class", shape = rectangle, id = 2];3->1->0;3->2;}';

        $parser = $this->run_parser('[[:xdigit:]]|3AZ');
        $root51 = $parser->get_root();
        $etalon_dot_instructions51 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "|", tooltip = alternation, id = 6];0[label = "[[:xdigit:]]", tooltip = "character class", shape = rectangle, id = 0];5[label = "concat", tooltip = concatenation, id = 5];3[label = "concat", tooltip = concatenation, id = 3];1[label = "3", tooltip = "character class", shape = rectangle, id = 1];2[label = "A", tooltip = "character class", shape = rectangle, id = 2];4[label = "Z", tooltip = "character class", shape = rectangle, id = 4];6->0;6->5->3->1;3->2;5->4;}';

        $parser = $this->run_parser('[[:alnum:]]**a');
        $root52 = $parser->get_root();
        $etalon_dot_instructions52 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;4[label = "concat", tooltip = concatenation, id = 4];2[label = "*", tooltip = "infinite quantifier", id = 2];1[label = "*", tooltip = "infinite quantifier", id = 1];0[label = "[[:alnum:]]", tooltip = "character class", shape = rectangle, id = 0];3[label = "a", tooltip = "character class", shape = rectangle, id = 3];4->2->1->0;4->3;}';

        $parser = $this->run_parser('[[:word:]][2-4!]');
        $root53 = $parser->get_root();
        $etalon_dot_instructions53 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;2[label = "concat", tooltip = concatenation, id = 2];0[label = "[[:word:]]", tooltip = "character class", shape = rectangle, id = 0];1[label = "[!2-4]", tooltip = "character class", shape = rectangle, id = 1];2->0;2->1;}';

        $parser = $this->run_parser('[[:punct:]][^3333]');
        $root54 = $parser->get_root();
        $etalon_dot_instructions54 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;2[label = "concat", tooltip = concatenation, id = 2];0[label = "[[:punct:]]", tooltip = "character class", shape = rectangle, id = 0];1[label = "[^3333]", tooltip = "character class", shape = rectangle, id = 1];2->0;2->1;}';

        $parser = $this->run_parser('[[:blank:]](a|b)');
        $root55 = $parser->get_root();
        $etalon_dot_instructions55 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;5[label = "concat", tooltip = concatenation, id = 5];0[label = "[[:blank:]]", tooltip = "character class", shape = rectangle, id = 0];4[label = "( ... )", tooltip = subexpression, id = 4];3[label = "|", tooltip = alternation, id = 3];1[label = "a", tooltip = "character class", shape = rectangle, id = 1];2[label = "b", tooltip = "character class", shape = rectangle, id = 2];5->0;5->4->3->1;3->2;}';

        $parser = $this->run_parser('[[:space:]]{3,}');
        $root56 = $parser->get_root();
        $etalon_dot_instructions56 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{3,}", tooltip = "infinite quantifier", id = 1];0[label = "[[:space:]]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[[:cntrl:]]{,5}');
        $root57 = $parser->get_root();
        $etalon_dot_instructions57 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{,5}", tooltip = "finite quantifier", id = 1];0[label = "[[:cntrl:]]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[[:graph:]]{0,0}');
        $root58 = $parser->get_root();
        $etalon_dot_instructions58 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{0,0}", tooltip = "finite quantifier", id = 1];0[label = "[[:graph:]]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('[[:print:]]{2,7}');
        $root59 = $parser->get_root();
        $etalon_dot_instructions59 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;1[label = "{2,7}", tooltip = "finite quantifier", id = 1];0[label = "[[:print:]]", tooltip = "character class", shape = rectangle, id = 0];1->0;}';

        $parser = $this->run_parser('(ab|cd*$|){3,100}');
        $root60 = $parser->get_root();
        $etalon_dot_instructions60 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;13[label = "{3,100}", tooltip = "finite quantifier", id = 13];12[label = "( ... )", tooltip = subexpression, id = 12];11[label = "|", tooltip = alternation, id = 11];9[label = "|", tooltip = alternation, id = 9];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "*", tooltip = "infinite quantifier", id = 5];4[label = "d", tooltip = "character class", shape = rectangle, id = 4];7[label = "assertion $", tooltip = assertion, shape = rectangle, id = 7];10[label = "emptiness", tooltip = emptiness, shape = rectangle, id = 10];13->12->11->9->2->0;2->1;9->8->6->3;6->5->4;8->7;11->10;}';

        $parser = $this->run_parser('[[-]]');
        $root61 = $parser->get_root();
        $etalon_dot_instructions61 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;2[label = "concat", tooltip = concatenation, id = 2];0[label = "[[-]", tooltip = "character class", shape = rectangle, id = 0];1[label = "]", tooltip = "character class", shape = rectangle, id = 1];2->0;2->1;}';

        $parser = $this->run_parser('[[.NIL.]]');
        $root62 = $parser->get_root();
        $etalon_dot_instructions62 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;0[label = "[[.NIL.]]", tooltip = "character class", shape = rectangle, id = 0];0;}';

        $parser = $this->run_parser('[[=aleph=]](NULL)?');
        $root63 = $parser->get_root();
        $etalon_dot_instructions63 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;11[label = "concat", tooltip = concatenation, id = 11];0[label = "[[=aleph=]]", tooltip = "character class", shape = rectangle, id = 0];10[label = "?", tooltip = "finite quantifier", id = 10];9[label = "( ... )", tooltip = subexpression, id = 9];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "N", tooltip = "character class", shape = rectangle, id = 2];3[label = "U", tooltip = "character class", shape = rectangle, id = 3];5[label = "L", tooltip = "character class", shape = rectangle, id = 5];7[label = "L", tooltip = "character class", shape = rectangle, id = 7];11->0;11->10->9->8->6->4->2;4->3;6->5;8->7;}';

        $parser = $this->run_parser('BE$33[\w!-]');
        $root64 = $parser->get_root();
        $etalon_dot_instructions64 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "B", tooltip = "character class", shape = rectangle, id = 0];1[label = "E", tooltip = "character class", shape = rectangle, id = 1];3[label = "assertion $", tooltip = assertion, shape = rectangle, id = 3];5[label = "3", tooltip = "character class", shape = rectangle, id = 5];7[label = "3", tooltip = "character class", shape = rectangle, id = 7];9[label = "[!-\\w]", tooltip = "character class", shape = rectangle, id = 9];10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;}';

        $parser = $this->run_parser('abc^');
        $root65 = $parser->get_root();
        $etalon_dot_instructions65 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "a", tooltip = "character class", shape = rectangle, id = 0];1[label = "b", tooltip = "character class", shape = rectangle, id = 1];3[label = "c", tooltip = "character class", shape = rectangle, id = 3];5[label = "assertion ^", tooltip = assertion, shape = rectangle, id = 5];6->4->2->0;2->1;4->3;6->5;}';

        $parser = $this->run_parser('11111111111111111111111111111111111111111111111111111');
        $root66 = $parser->get_root();
        $etalon_dot_instructions66 = 'digraph ' . qtype_preg_dot_style_provider::get_graph_name() . ' {rankdir = LR;104[label = "concat", tooltip = concatenation, id = 104];102[label = "concat", tooltip = concatenation, id = 102];100[label = "concat", tooltip = concatenation, id = 100];98[label = "concat", tooltip = concatenation, id = 98];96[label = "concat", tooltip = concatenation, id = 96];94[label = "concat", tooltip = concatenation, id = 94];92[label = "concat", tooltip = concatenation, id = 92];90[label = "concat", tooltip = concatenation, id = 90];88[label = "concat", tooltip = concatenation, id = 88];86[label = "concat", tooltip = concatenation, id = 86];84[label = "concat", tooltip = concatenation, id = 84];82[label = "concat", tooltip = concatenation, id = 82];80[label = "concat", tooltip = concatenation, id = 80];78[label = "concat", tooltip = concatenation, id = 78];76[label = "concat", tooltip = concatenation, id = 76];74[label = "concat", tooltip = concatenation, id = 74];72[label = "concat", tooltip = concatenation, id = 72];70[label = "concat", tooltip = concatenation, id = 70];68[label = "concat", tooltip = concatenation, id = 68];66[label = "concat", tooltip = concatenation, id = 66];64[label = "concat", tooltip = concatenation, id = 64];62[label = "concat", tooltip = concatenation, id = 62];60[label = "concat", tooltip = concatenation, id = 60];58[label = "concat", tooltip = concatenation, id = 58];56[label = "concat", tooltip = concatenation, id = 56];54[label = "concat", tooltip = concatenation, id = 54];52[label = "concat", tooltip = concatenation, id = 52];50[label = "concat", tooltip = concatenation, id = 50];48[label = "concat", tooltip = concatenation, id = 48];46[label = "concat", tooltip = concatenation, id = 46];44[label = "concat", tooltip = concatenation, id = 44];42[label = "concat", tooltip = concatenation, id = 42];40[label = "concat", tooltip = concatenation, id = 40];38[label = "concat", tooltip = concatenation, id = 38];36[label = "concat", tooltip = concatenation, id = 36];34[label = "concat", tooltip = concatenation, id = 34];32[label = "concat", tooltip = concatenation, id = 32];30[label = "concat", tooltip = concatenation, id = 30];28[label = "concat", tooltip = concatenation, id = 28];26[label = "concat", tooltip = concatenation, id = 26];24[label = "concat", tooltip = concatenation, id = 24];22[label = "concat", tooltip = concatenation, id = 22];20[label = "concat", tooltip = concatenation, id = 20];18[label = "concat", tooltip = concatenation, id = 18];16[label = "concat", tooltip = concatenation, id = 16];14[label = "concat", tooltip = concatenation, id = 14];12[label = "concat", tooltip = concatenation, id = 12];10[label = "concat", tooltip = concatenation, id = 10];8[label = "concat", tooltip = concatenation, id = 8];6[label = "concat", tooltip = concatenation, id = 6];4[label = "concat", tooltip = concatenation, id = 4];2[label = "concat", tooltip = concatenation, id = 2];0[label = "1", tooltip = "character class", shape = rectangle, id = 0];1[label = "1", tooltip = "character class", shape = rectangle, id = 1];3[label = "1", tooltip = "character class", shape = rectangle, id = 3];5[label = "1", tooltip = "character class", shape = rectangle, id = 5];7[label = "1", tooltip = "character class", shape = rectangle, id = 7];9[label = "1", tooltip = "character class", shape = rectangle, id = 9];11[label = "1", tooltip = "character class", shape = rectangle, id = 11];13[label = "1", tooltip = "character class", shape = rectangle, id = 13];15[label = "1", tooltip = "character class", shape = rectangle, id = 15];17[label = "1", tooltip = "character class", shape = rectangle, id = 17];19[label = "1", tooltip = "character class", shape = rectangle, id = 19];21[label = "1", tooltip = "character class", shape = rectangle, id = 21];23[label = "1", tooltip = "character class", shape = rectangle, id = 23];25[label = "1", tooltip = "character class", shape = rectangle, id = 25];27[label = "1", tooltip = "character class", shape = rectangle, id = 27];29[label = "1", tooltip = "character class", shape = rectangle, id = 29];31[label = "1", tooltip = "character class", shape = rectangle, id = 31];33[label = "1", tooltip = "character class", shape = rectangle, id = 33];35[label = "1", tooltip = "character class", shape = rectangle, id = 35];37[label = "1", tooltip = "character class", shape = rectangle, id = 37];39[label = "1", tooltip = "character class", shape = rectangle, id = 39];41[label = "1", tooltip = "character class", shape = rectangle, id = 41];43[label = "1", tooltip = "character class", shape = rectangle, id = 43];45[label = "1", tooltip = "character class", shape = rectangle, id = 45];47[label = "1", tooltip = "character class", shape = rectangle, id = 47];49[label = "1", tooltip = "character class", shape = rectangle, id = 49];51[label = "1", tooltip = "character class", shape = rectangle, id = 51];53[label = "1", tooltip = "character class", shape = rectangle, id = 53];55[label = "1", tooltip = "character class", shape = rectangle, id = 55];57[label = "1", tooltip = "character class", shape = rectangle, id = 57];59[label = "1", tooltip = "character class", shape = rectangle, id = 59];61[label = "1", tooltip = "character class", shape = rectangle, id = 61];63[label = "1", tooltip = "character class", shape = rectangle, id = 63];65[label = "1", tooltip = "character class", shape = rectangle, id = 65];67[label = "1", tooltip = "character class", shape = rectangle, id = 67];69[label = "1", tooltip = "character class", shape = rectangle, id = 69];71[label = "1", tooltip = "character class", shape = rectangle, id = 71];73[label = "1", tooltip = "character class", shape = rectangle, id = 73];75[label = "1", tooltip = "character class", shape = rectangle, id = 75];77[label = "1", tooltip = "character class", shape = rectangle, id = 77];79[label = "1", tooltip = "character class", shape = rectangle, id = 79];81[label = "1", tooltip = "character class", shape = rectangle, id = 81];83[label = "1", tooltip = "character class", shape = rectangle, id = 83];85[label = "1", tooltip = "character class", shape = rectangle, id = 85];87[label = "1", tooltip = "character class", shape = rectangle, id = 87];89[label = "1", tooltip = "character class", shape = rectangle, id = 89];91[label = "1", tooltip = "character class", shape = rectangle, id = 91];93[label = "1", tooltip = "character class", shape = rectangle, id = 93];95[label = "1", tooltip = "character class", shape = rectangle, id = 95];97[label = "1", tooltip = "character class", shape = rectangle, id = 97];99[label = "1", tooltip = "character class", shape = rectangle, id = 99];101[label = "1", tooltip = "character class", shape = rectangle, id = 101];103[label = "1", tooltip = "character class", shape = rectangle, id = 103];104->102->100->98->96->94->92->90->88->86->84->82->80->78->76->74->72->70->68->66->64->62->60->58->56->54->52->50->48->46->44->42->40->38->36->34->32->30->28->26->24->22->20->18->16->14->12->10->8->6->4->2->0;2->1;4->3;6->5;8->7;10->9;12->11;14->13;16->15;18->17;20->19;22->21;24->23;26->25;28->27;30->29;32->31;34->33;36->35;38->37;40->39;42->41;44->43;46->45;48->47;50->49;52->51;54->53;56->55;58->57;60->59;62->61;64->63;66->65;68->67;70->69;72->71;74->73;76->75;78->77;80->79;82->81;84->83;86->85;88->87;90->89;92->91;94->93;96->95;98->97;100->99;102->101;104->103;}';
        /*
        $regexhandler = new qtype_preg_regex_handler();
        $dir = $regexhandler->get_temp_dir('nodes');
        qtype_preg_regex_handler::execute_dot($root66->dot_script(new qtype_preg_dot_node_context(true)), 'svg', $dir . 'ast_test.svg');
        var_dump($root66->dot_script(new qtype_preg_dot_node_context(true)));
        */
        $context = new qtype_preg_dot_node_context(true);
        $this->assertTrue($etalon_dot_instructions1 === $root1->dot_script($context),'Test with regex (a|) failed!');
        $this->assertTrue($etalon_dot_instructions2 === $root2->dot_script($context),'Test with regex ^\\\\a\\b\\A\\Z\\G$ failed!');
        $this->assertTrue($etalon_dot_instructions3 === $root3->dot_script($context),'Test with regex abc[\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V] failed!');
        $this->assertTrue($etalon_dot_instructions4 === $root4->dot_script($context),'Test with regex abc[\\w\\W\\s] failed!');
        $this->assertTrue($etalon_dot_instructions5 === $root5->dot_script($context),'Test with regex abc[\\S\\d\\D] failed!');
        $this->assertTrue($etalon_dot_instructions6 === $root6->dot_script($context),'Test with regex 345[\\h\\H\\v\\V] failed!');
        $this->assertTrue($etalon_dot_instructions7 === $root7->dot_script($context),'Test with regex abc[^\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V] failed!');
        $this->assertTrue($etalon_dot_instructions8 === $root8->dot_script($context),'Test with regex abc[^\\w\\W\\s] failed!');
        $this->assertTrue($etalon_dot_instructions9 === $root9->dot_script($context),'Test with regex abc[^\\S\\d\\D] failed!');
        $this->assertTrue($etalon_dot_instructions10 === $root10->dot_script($context),'Test with regex 345[^\\h\\H\\v\\V] failed!');
        $this->assertTrue($etalon_dot_instructions11 === $root11->dot_script($context),'Test with regex abc\\w\\W\\s\\S\\d\\D\\h\\H\\v\\V failed!');
        $this->assertTrue($etalon_dot_instructions12 === $root12->dot_script($context),'Test with regex abc\\w\\W\\s failed!');
        $this->assertTrue($etalon_dot_instructions13 === $root13->dot_script($context),'Test with regex abc\\S\\d\\D failed!');
        $this->assertTrue($etalon_dot_instructions14 === $root14->dot_script($context),'Test with regex 345\\h\\H\\v\\V failed!');
        $this->assertTrue($etalon_dot_instructions15 === $root15->dot_script($context),'Test with regex [a-z2-5]33* failed!');
        $this->assertTrue($etalon_dot_instructions16 === $root16->dot_script($context),'Test with regex [B-D!.].{1,5}23? failed!');
        $this->assertTrue($etalon_dot_instructions17 === $root17->dot_script($context),'Test with regex \\A[^c-z;-](ef)+ failed!');
        $this->assertTrue($etalon_dot_instructions18 === $root18->dot_script($context),'Test with regex abc{3,7} failed!');
        $this->assertTrue($etalon_dot_instructions19 === $root19->dot_script($context),'Test with regex abc{3,} failed!');
        $this->assertTrue($etalon_dot_instructions20 === $root20->dot_script($context),'Test with regex abc{,7} failed!');
        $this->assertTrue($etalon_dot_instructions21 === $root21->dot_script($context),'Test with regex abc{0,7} failed!');
        $this->assertTrue($etalon_dot_instructions22 === $root22->dot_script($context),'Test with regex [^a-z]*? failed!');
        $this->assertTrue($etalon_dot_instructions23 === $root23->dot_script($context),'Test with regex [^A-Z]+? failed!');
        $this->assertTrue($etalon_dot_instructions24 === $root24->dot_script($context),'Test with regex [^3-9]{5,}? failed!');
        $this->assertTrue($etalon_dot_instructions25 === $root25->dot_script($context),'Test with regex [^\W33]*+ failed!');
        $this->assertTrue($etalon_dot_instructions26 === $root26->dot_script($context),'Test with regex [^\D\S]?+ failed!');
        $this->assertTrue($etalon_dot_instructions27 === $root27->dot_script($context),'Test with regex [^\w\d]{5,}+ failed!');
        $this->assertTrue($etalon_dot_instructions28 === $root28->dot_script($context),'Test with regex ab(xa)*+a failed!');
        $this->assertTrue($etalon_dot_instructions29 === $root29->dot_script($context),'Test with regex a(bc|b|x)cc failed!');
        $this->assertTrue($etalon_dot_instructions30 === $root30->dot_script($context),'Test with regex a(?:bc|b|x)cc failed!');
        $this->assertTrue($etalon_dot_instructions31 === $root31->dot_script($context),'Test with regex a(?>bc|b|x)cc failed!');
        $this->assertTrue($etalon_dot_instructions32 === $root32->dot_script($context),'Test with regex a(?>x*)xa failed!');
        $this->assertTrue($etalon_dot_instructions33 === $root33->dot_script($context),'Test with regex (?-i)(?i:tv)set failed!');
        $this->assertTrue($etalon_dot_instructions34 === $root34->dot_script($context),'Test with regex my name is (?=Freedom) failed!');
        $this->assertTrue($etalon_dot_instructions35 === $root35->dot_script($context),'Test with regex my name is (?!Freedom) failed!');
        $this->assertTrue($etalon_dot_instructions36 === $root36->dot_script($context),'Test with regex my name is (?<=Freedom) failed!');
        $this->assertTrue($etalon_dot_instructions37 === $root37->dot_script($context),'Test with regex my name is (?<!Freedom) failed!');
        $this->assertTrue($etalon_dot_instructions38 === $root38->dot_script($context),'Test with regex (?(?<=a)m|d) failed!');
        $this->assertTrue($etalon_dot_instructions39 === $root39->dot_script($context),'Test with regex (a)?(?(?1)m|d) failed!');
        $this->assertTrue($etalon_dot_instructions40 === $root40->dot_script($context),'Test with regex a\.? failed!');
        $this->assertTrue($etalon_dot_instructions41 === $root41->dot_script($context),'Test with regex a\\\\b failed!');
        $this->assertTrue($etalon_dot_instructions42 === $root42->dot_script($context),'Test with regex a\[F\] failed!');
        $this->assertTrue($etalon_dot_instructions43 === $root43->dot_script($context),'Test with regex \Q+-*/\E failed!');
        $this->assertTrue($etalon_dot_instructions44 === $root44->dot_script($context),'Test with regex (a+a+)+a failed!');
        $this->assertTrue($etalon_dot_instructions45 === $root45->dot_script($context),'Test with regex :::1:::0:|:::1:1:0: failed!');
        $this->assertTrue($etalon_dot_instructions46 === $root46->dot_script($context),'Test with regex :::1:::0:|:::1:1:1::: failed!');
        $this->assertTrue($etalon_dot_instructions47 === $root47->dot_script($context),'Test with regex [[:upper:]]A failed!');
        $this->assertTrue($etalon_dot_instructions48 === $root48->dot_script($context),'Test with regex [[:lower:]]+az failed!');
        $this->assertTrue($etalon_dot_instructions49 === $root49->dot_script($context),'Test with regex [[:alpha:]]*35 failed!');
        $this->assertTrue($etalon_dot_instructions50 === $root50->dot_script($context),'Test with regex [[:digit:]]?Z failed!');
        $this->assertTrue($etalon_dot_instructions51 === $root51->dot_script($context),'Test with regex [[:xdigit:]]|3AZ failed!');
        $this->assertTrue($etalon_dot_instructions52 === $root52->dot_script($context),'Test with regex [[:alnum:]]**a failed!');
        $this->assertTrue($etalon_dot_instructions53 === $root53->dot_script($context),'Test with regex [[:word:]][2-4!] failed!');
        $this->assertTrue($etalon_dot_instructions54 === $root54->dot_script($context),'Test with regex [[:punct:]][^3333] failed!');
        $this->assertTrue($etalon_dot_instructions55 === $root55->dot_script($context),'Test with regex [[:blank:]](a|b) failed!');
        $this->assertTrue($etalon_dot_instructions56 === $root56->dot_script($context),'Test with regex [[:space:]]{3,} failed!');
        $this->assertTrue($etalon_dot_instructions57 === $root57->dot_script($context),'Test with regex [[:cntrl:]]{,5} failed!');
        $this->assertTrue($etalon_dot_instructions58 === $root58->dot_script($context),'Test with regex [[:graph:]]{0,0} failed!');
        $this->assertTrue($etalon_dot_instructions59 === $root59->dot_script($context),'Test with regex [[:print:]]{2,7} failed!');
        $this->assertTrue($etalon_dot_instructions60 === $root60->dot_script($context),'Test with regex (ab|cd*$|){3,100} failed!');
        $this->assertTrue($etalon_dot_instructions61 === $root61->dot_script($context),'Test with regex [[-]] failed!');
        $this->assertTrue($etalon_dot_instructions62 === $root62->dot_script($context),'Test with regex [[.NIL.]] failed!');
        $this->assertTrue($etalon_dot_instructions63 === $root63->dot_script($context),'Test with regex [[=aleph=]](NULL)? failed!');
        $this->assertTrue($etalon_dot_instructions64 === $root64->dot_script($context),'Test with regex BE$33[\w!-] failed!');
        $this->assertTrue($etalon_dot_instructions65 === $root65->dot_script($context),'Test with regex abc^ failed!');
        $this->assertTrue($etalon_dot_instructions66 === $root66->dot_script($context),'Test with regex 11111111111111111111111111111111111111111111111111111 failed!');
    }
    /*function test_simple() {//[asdf]
        $this->matcher->input_fa('0->asdf->1;');
        $this->matcher->draw('simple.dot', 'simple.jpg');
    }
    function test_complex() {//(?:a|b)*abb
        $this->matcher->input_fa('0->a->1;0->b->0;1->a->1;1->b->2;2->b->3;2->a->1;3->a->1;3->b->0;');
        $this->matcher->draw('complex.dot', 'complex.jpg');
    }
    function test_subexpression() {//(a)(bc)
        $this->matcher->input_fa('0->#s1e1#a->1;1->#s2#b->2;2->#e2#c->3;');
        $this->matcher->draw('subexpression.dot', 'subexpression.jpg');
    }*/
}
