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

define('qtype_preg_templates_test', true);

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');

class qtype_preg_templates_test extends PHPUnit_Framework_TestCase {

    function test_template_parsing() {
        $handler = new qtype_preg_fa_matcher("(?###word_in_parens)");
        $root = $handler->get_ast_root();
        $this->assertTrue($root->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue(count($root->operands) === 1);
        $this->assertTrue($root->operands[0]->type === qtype_preg_node::TYPE_NODE_CONCAT);
        $this->assertTrue($root->operands[0]->operands[0]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->type === qtype_preg_node::TYPE_NODE_SUBEXPR);
        $this->assertTrue($root->operands[0]->operands[2]->type === qtype_preg_node::TYPE_LEAF_CHARSET);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->type === qtype_preg_node::TYPE_NODE_ALT);
        $this->assertTrue($root->operands[0]->operands[1]->operands[0]->operands[0]->type === qtype_preg_node::TYPE_NODE_INFINITE_QUANT);
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
}
