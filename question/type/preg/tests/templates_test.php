<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/php_preg_matcher/php_preg_matcher.php');
require_once('override_templates.php');

class qtype_preg_templates_test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider descriptions_provider
     */
    public function test_descriptions($name, $en, $ru) {
        $template = qtype_preg\template::available_templates()[$name];
        // TODO - set langauge
        $descr = $template->get_description();
        $this->assertEquals($en, $descr);
    }

    public function descriptions_provider()
    {
        return array(
            array('word', 'word', 'слово'),
            array('parens_req', '$$1 in parens', '$$1 в скобках')
        );
    }

    public function test_template_parsing() {
        $options = new qtype_preg_handling_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_MULTILINE);
        $lexer = null;
        $processed = '';

        $original = 'abc(def)+[a-zs-!f]';
        qtype_preg\template::process_regex($original, $options, $lexer, $processed);
        $this->assertTrue($processed === $original);

        $original = 'ab(?###word)cd';
        qtype_preg\template::process_regex($original, $options, $lexer, $processed);
        $this->assertTrue($processed === 'ab(?i-msx:\w+)cd');

        $original = 'ab(?###two_words)cd';
        qtype_preg\template::process_regex($original, $options, $lexer, $processed);
        $this->assertTrue($processed === 'ab(?-imsx:(?i-msx:\w+)(?i-msx:\w+))cd');

        $original = '(?###parens_req<)a(?###>)';
        qtype_preg\template::process_regex($original, $options, $lexer, $processed);
        //$this->assertTrue($processed === 'ab(?-imsx:(?i-msx:\w+)(?i-msx:\w+))cd');

        $original = '(?###parens_opt<)(?###brackets_req<) x (?###>)(?###>)';
        qtype_preg\template::process_regex($original, $options, $lexer, $processed);

        /*$handler = new qtype_preg_fa_matcher("(?###word_in_parens)");
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->number === 1);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);*/
    }

    /*public function test_template_dependency_parsing() {
        $handler = new qtype_preg_fa_matcher("(?###parens_opt<)(a)(?###>)");
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->number === 1);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->number === 2);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->operands[0]->type === $root->operands[0]->type);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->operands[0] !== $root->operands[0]);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL);
        $this->assertTrue($root->operands[1]->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
    }

    public function test_template_subexpr_numbering() {
        $handler = new qtype_preg_fa_matcher('(?###parens_req<)(a)(b)(?###>)(?###parens_req<)(c)(?###>)(d)');
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_CONCAT);

        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->number === 1);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->operands[0]->operands[0]->number === 2);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[0]->operands[1]->operands[0]->operands[0]->operands[1]->number === 3);

        $this->assertTrue($root->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->number === 4);
        $this->assertTrue($root->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[1]->operands[0]->operands[1]->operands[0]->operands[0]->number === 5);

        $this->assertTrue($root->operands[2]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[2]->number === 6);
    }*/

    public function test_template_leaf() {
        $matcher = new qtype_preg_fa_matcher('(?###word)');

        $res = $matcher->match('kind of word');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 4);

        $res = $matcher->match('kindaword');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 9);

        $res = $matcher->match('124');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 3);
    }

    public function test_templates_concat() {
        $matcher = new qtype_preg_fa_matcher('(?###word)(?###integer)');

        $res = $matcher->match('a-1');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 3);

        $matcher = new qtype_preg_fa_matcher('(?###parens_req<)a(?###>)' .
                                             '(?###parens_opt<)b(?###>)' .
                                             '(?###brackets_req<)(?###parens_opt<)c(?###>)(?###>)'
                                             );

        $res = $matcher->match('(a)b[c]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 7);

        $res = $matcher->match('(a)b[(c)]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 9);

        $res = $matcher->match('((a))((b))[(((c)))]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 19);
    }

    public function test_template_node_simple() {
        $matcher = new qtype_preg_fa_matcher('(?###parens_req<)a(?###>)');

        $res = $matcher->match('(a)');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 3);

        $res = $matcher->match('((a))');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 5);

        $res = $matcher->match('((a)');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 1);
        $this->assertTrue($res->length[0] === 3);

        $res = $matcher->match('(a))');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 3);
    }

    public function test_template_node_emptiness() {
        $matcher = new qtype_preg_fa_matcher('(?###parens_opt<)(?###>)');

        $res = $matcher->match('');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 0);

        $res = $matcher->match('()');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 2);

        $res = $matcher->match('(())');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 4);

        $res = $matcher->match('(a)');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 0);
    }

    public function test_template_leaf_in_template_node() {
        $matcher = new qtype_preg_fa_matcher('(?###parens_req<)(?###word)(?###>)');

        $res = $matcher->match('(word)');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 6);

        $res = $matcher->match('((adjective))');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 13);

        $res = $matcher->match('(((pronoun)))');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 13);

        $res = $matcher->match('[(adverb)]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 1);
        $this->assertTrue($res->length[0] === 8);

        $matcher = new qtype_preg_fa_matcher('(?###brackets_req<)(?###word)(?###>)');

        $res = $matcher->match('[word]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 6);

        $res = $matcher->match('[[adjective]]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 13);

        $res = $matcher->match('[[[pronoun]]]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 13);

        $res = $matcher->match('([adverb])');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 1);
        $this->assertTrue($res->length[0] === 8);
    }

    public function test_template_node_in_template_node() {
        $matcher = new qtype_preg_fa_matcher('(?###brackets_req<)(?###parens_req<)(?###word)(?###>)(?###>)');

        $res = $matcher->match('[(run_on_and_on_and_on)]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 24);

        $res = $matcher->match('[[((where_my_gerunds_at))]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 1);
        $this->assertTrue($res->length[0] === 25);

        $res = $matcher->match('[[(parenthetical)]]]');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 19);
    }

    public function test_template_dependency_matching() {
        $matcher = new qtype_preg_fa_matcher('(?###parens_opt<)(?###word)(?###>)');

        $res = $matcher->match('word');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 4);

        $res = $matcher->match('(anotherword)');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 13);

        $res = $matcher->match('(spaghetti');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 1);
        $this->assertTrue($res->length[0] === 9);

        $matcher = new qtype_preg_fa_matcher('(?###custom_parens_opt<)<(?###,)(?###word)(?###,)>(?###>)');

        $res = $matcher->match('word');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 4);

        $res = $matcher->match('<word>');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 6);

        $res = $matcher->match('<<<<word>>>>');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 12);
    }

    public function test_template_errors() {
        // Wrong leaf name
        $matcher = new qtype_preg_fa_matcher('(?###somethingweird)');
        $errors = $matcher->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 19);

        // Wrong node name
        $matcher = new qtype_preg_fa_matcher('(?###somethingweird<)a(?###,)b(?###>)');
        $errors = $matcher->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 20);

        // Leaf called as a node
        $matcher = new qtype_preg_fa_matcher('(?###word<)(?###>)');
        $errors = $matcher->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 17);

        // Node called as a leaf
        $matcher = new qtype_preg_fa_matcher('(?###parens_req)');
        $errors = $matcher->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 15);

        // Wrong parameters count
        $matcher = new qtype_preg_fa_matcher('(?###parens_req<)a(?###,)b(?###>)');
        $errors = $matcher->get_error_nodes();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 32);
    }

    public function test_recursive_dependencies() {
        $available = qtype_preg\template::available_templates();

        // Check correct case
        $res = qtype_preg\template::check_dependencies($available);
        $this->assertTrue($res === true);

        // Check direct recursion
        $res = qtype_preg\template::check_dependencies(array_merge($available, array('recursive' => new qtype_preg\template('recursive', '(?###recursive<)', '', array()))));
        $this->assertTrue($res === false);

        // Check indirect recursion
        $res = qtype_preg\template::check_dependencies(array_merge($available, array('recursive1' => new qtype_preg\template('recursive1', '(?###recursive2<)', '', array()),
                                                                                     'recursive2' => new qtype_preg\template('recursive2', '(?###recursive1<)', '', array())
                                                                                     )));
        $this->assertTrue($res === false);

        // Check unexisting dependencies
        $res = qtype_preg\template::check_dependencies(array_merge($available, array('coffe' => new qtype_preg\template('coffe', '(?###milk<)', '', array()))));
        $this->assertTrue($res === false);
    }

    public function test_template_realworld_1() {
        $matcher = new qtype_preg_fa_matcher('^(?###parens_req<)(?###parens_req<)(?###word)(?###>)\+(?###parens_req<)1(?###>)(?###>)$');

        $str = '((((sdf)+(1))))';
        $res = $matcher->match($str);
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === strlen($str));

        $str = '(((((((sdf))))))+((((((1)))))))';
        $res = $matcher->match($str);
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === strlen($str));
    }

    public function test_template_realworld_2() {   // a+b
        $regex = '
        (?###parens_opt<)
            (?###parens_opt<)a(?###>)
            \+
            (?###parens_opt<)b(?###>)
        (?###>)';
        $options = new qtype_preg_matching_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $matcher = new qtype_preg_fa_matcher('^' . $regex . '$', $options);

        // Full matches
        $strings = array('a+b', '(a+b)', '((a+b))', '((((a))+((((b))))))');
        foreach ($strings as $str) {
            $res = $matcher->match($str);
            $this->assertTrue($res->full);
            $this->assertTrue($res->indexfirst[0] === 0);
            $this->assertTrue($res->length[0] === strlen($str));
        }

        // Partial matches
        $res = $matcher->match('(a+b');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 4);

        $res = $matcher->match('a+b)');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 3);

        $res = $matcher->match('(a)+(b');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 6);
    }

    public function test_template_realworld_3() {   // sin(a+b+c)
        $regex = '
        sin\s*
        (?###parens_req<)

            (?|

            # optional group a+b

                (?###parens_opt<)
                    (?###parens_opt<)a(?###>)
                    \+
                    (?###parens_opt<)b(?###>)
                (?###>)
                \+
                (?###parens_opt<)c(?###>)

            |

            # optional group b+c

                (?###parens_opt<)a(?###>)
                \+
                (?###parens_opt<)
                    (?###parens_opt<)b(?###>)
                    \+
                    (?###parens_opt<)c(?###>)
                (?###>)

            )

        (?###>)';
        $options = new qtype_preg_matching_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $matcher = new qtype_preg_fa_matcher('^' . $regex . '$', $options);

        // Full matches
        $strings = array('sin (a+b+c)', 'sin(a+(b)+c)', 'sin((a+b+c))', 'sin (((a))+(b)+(c))', 'sin((((a))+((b)))+((c)))', 'sin ((a)+(b+c))');
        foreach ($strings as $str) {
            $res = $matcher->match($str);
            $this->assertTrue($res->full);
            $this->assertTrue($res->indexfirst[0] === 0);
            $this->assertTrue($res->length[0] === strlen($str));
        }

        // Partial matches
        $res = $matcher->match('sin((a+b+c)');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 11);

        $res = $matcher->match('sin(a+b+c))');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 10);

        $res = $matcher->match('sin(((a)+b)');
        $this->assertFalse($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 11);
    }

    public function test_template_realworld_4() {   // a*b+c*d
        $regex = '
        (?###parens_opt<)
            (?###parens_opt<)
                (?###parens_opt<)a(?###>)
                \*
                (?###parens_opt<)b(?###>)
            (?###>)
            \+
            (?###parens_opt<)
                (?###parens_opt<)c(?###>)
                \*
                (?###parens_opt<)d(?###>)
            (?###>)
        (?###>)';
        $options = new qtype_preg_matching_options();
        $options->set_modifier(qtype_preg_handling_options::MODIFIER_EXTENDED);
        $matcher = new qtype_preg_fa_matcher('^' . $regex . '$', $options);

        // Full matches
        $strings = array('a*b+c*d', '((a*b+c*d))', '((a)*((b)))+(((c)*(((d)))))');
        foreach ($strings as $str) {
            $res = $matcher->match($str);
            $this->assertTrue($res->full);
            $this->assertTrue($res->indexfirst[0] === 0);
            $this->assertTrue($res->length[0] === strlen($str));
        }
    }

    public function test_php_preg_matcher() {
        $matcher = new qtype_preg_php_preg_matcher('(?###parens_req<)a(?###>)');

        $res = $matcher->match('((a))');
        $this->assertTrue($res->full);
        $this->assertTrue($res->indexfirst[0] === 0);
        $this->assertTrue($res->length[0] === 5);
    }
}
