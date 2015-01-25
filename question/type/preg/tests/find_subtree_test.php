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

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');


//require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');


class qtype_preg_find_subtree_test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider trivial_provider
     */
    public function test_trivial($regex1, $regex2, $expected) {
        $h1 = new qtype_preg_regex_handler($regex1);
        $h2 = new qtype_preg_regex_handler($regex2);
        $root1 = $h1->get_ast_root();
        //$ranges1 = $root1->ranges();
        $root2 = $h2->get_ast_root();
        //$ranges2 = $root2->ranges();
        $actual = $root1->find_all_subtrees($root2, 0);
        $actual = array_map(function($e) {return $e->id;}, $actual);
        $this->assertEquals($expected, $actual, "\"$regex1\" find_all_subtrees \"$regex2\"" );
    }

    public function trivial_provider()
    {
        return array(
            array('ab', 'ab', array(1)),
            array('(?(?=a)b|c)', 'b|c', array()),
            array("((?<a>can)\s+not|(?&a)(?:'|`|)t)", "((can)\s+not|(?2)(?:'|`|)t)", array(1)),
            array('(a)(a(a)(?3))(a(a(a)(?6)))', '(a(a)(?2))', array(4, 13)),
            array('(a)(a(a)\3)(a(a(a)\6))', '(a(a)\2)', array(4, 13)),
            array('(a)(a(a))(a(a(a)))', '(a(a))', array(4, 12)),
            array('(a)(a)', '(a)', array(2, 4)),
            array('(a)(a)(a)(?(1)a)', '(a)', array(2, 4, 6)),
            array('(?(?=ab)ab)', 'ab', array(3, 6)),
            array('(?(?=a)b)', '(?=a)', array(2)),
            array('(a)(a)(a)(?(1)a)', 'a', array(3,5,7,9))
        );
    }
}
