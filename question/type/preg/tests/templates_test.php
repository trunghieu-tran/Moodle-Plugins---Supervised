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

// Set templates for testing purposes
qtype_preg\template::set_available_templates(array(
    'word' => new qtype_preg\template('word', '\w+', '', array('en' => 'word', 'ru' => 'слово')),
    'integer' => new qtype_preg\template('integer', '[+-]?\d+', '', array('en' => 'integer', 'ru' => 'integer')),
    'word_and_integer' => new qtype_preg\template('word_and_integer', '(?###word)(?###integer)', '' , array('en' => 'word', 'ru' => 'слово')),
    'parens_req' => new qtype_preg\template('parens_req', '(   \(    (?:$$1|(?-1))   \)  )', 'x', array('en' => '$$1 in parens', 'ru' => '$$1 в скобках'), 1),
    'parens_opt' => new qtype_preg\template('parens_opt', '$$1|(?###parens_req<)$$1(?###>)', '', array('en' => '$$1 in parens or not', 'ru' => '$$1 в скобках или без'), 1),
    'brackets_req' => new qtype_preg\template('brackets_req', '(\[(?:$$1|(?-1))\])', '', array('en' => '$$1 in brackets', 'ru' => '$$1 в квадратных скобках'), 1),
    'word_in_parens' => new qtype_preg\template('word_in_parens', '(?###parens_req<)(?###word)(?###>)', '', array('en' => 'word in parens', 'ru' => 'слово в скобках')),
    'word_in_parens_in_brackets' => new qtype_preg\template('word_in_parens_in_brackets', '(?###brackets_req<)(?###parens_req<)(?###word)(?###>)(?###>)', '', array('en' => 'word in parens in brackets', 'ru' => 'слово в квадратных и обычных скобках')),
));

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
        $handler = new qtype_preg_fa_matcher("(?###word_in_parens)");
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->number === 1);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
    }

    public function test_template_dependency_parsing() {
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
    }

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
    }

    public function test_template_errors() {
        $matcher = new qtype_preg_fa_matcher('(?###somethingweird)');
        $errors = $matcher->get_error_nodes();
        $root = $matcher->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 19);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ERROR);

        $matcher = new qtype_preg_fa_matcher('(?###parens_req<)a(?###,)b(?###>)');
        $errors = $matcher->get_error_nodes();
        $root = $matcher->get_ast_root();
        $this->assertTrue(count($errors) === 1);
        $this->assertTrue($errors[0]->type === qtype_preg_node::TYPE_NODE_ERROR);
        $this->assertTrue($errors[0]->subtype === qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT);
        $this->assertTrue($errors[0]->position->colfirst === 0);
        $this->assertTrue($errors[0]->position->collast === 32);
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_ERROR);
    }
}
