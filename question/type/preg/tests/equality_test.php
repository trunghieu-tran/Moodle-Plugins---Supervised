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

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

global $CFG;
//require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');


class qtype_preg_equality_test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider trivial_provider
     */
    public function test_trivial($regex1, $regex2) {
        $h1 = new qtype_preg_regex_handler($regex1);
        $h2 = new qtype_preg_regex_handler($regex2);
        $root1 = $h1->get_ast_root();
        $ranges1 = $root1->ranges();
        $root2 = $h2->get_ast_root();
        $ranges2 = $root2->ranges();
        $this->assertTrue($h1->get_ast_root()->is_equal($h2->get_ast_root()));
    }

    public function trivial_provider()
    {
        return array(
            array('[[:digit:]abce]', '[ea-c[:digit:]]')/*,
            array('a|b|c', 'c|b|a'),
            array('abc', 'abc'),
            array('b(a|b|c)(?1)', 'b(abc)(?:a|b|c)'),
            array('b(<a>a|b|c)(?&a)', 'b(a|b|c)(?1)'),
            array("((?<a>can)\s+not|(?&a)(?:'|`|)t)", "((can)\s+not|(?2)(?:'|`|)t)"),
            //array('[abc]', 'a|b|c'),
            array("((?<a>can)\s+not|(?&a)(?:'|`|)t)", "((?<a>can)\s+not|(?&a)(?:'|`|)t)")*/
        );
    }

    public function charset_provider()
    {
        return array(
            array('\d', '[\x{0030}-\x{0039}]')
        );
    }

    public function leaf_assert_provider()
    {
        return array(
            array('^', '(?:\A|(?<=\n))'),
            array('$', '(?:\Z|(?=\n))'),
            array('^^', '^'),
            array('$$', '$'),
            array('$^', '(*FAIL)')
        );
    }

    public function leaf_backref_provider()
    {
        return array(
            array('b(<name>a)\k<name>', 'b(a)\1'),
            array('(a)\1', '(a)(?:a)'),
            array('(a{3})\1', '(a{3})(?:a{3})')
        );
    }

    public function leaf_subexpr_provider()
    {
        return array(
            array('a(?:b|c)', 'ab|ac'),
            array('a()\1(?1)', 'a')
        );
    }

    public function leaf_subexpr_call_provider()
    {
        return array(
            array('b(<a>a)(?&a)', 'b(?:a)'),
            array('(b)(a)(?1)(?2)', '(b)(a)(?:b)(?:a)')
        );
    }

    public function finite_quant_provider()
    {
        return array(
            array('a{0,1}', 'a?'),
            array('a{1,1}', 'a'),
            array('a{1,3}', 'a(?:a)?(?:a)?'),
            array('a{0,3}', '(?:a)?(?:a)?(?:a)?'),
            array('a{3}', 'aaa'),
            array('a{1,2}{2,3}', 'a{1,6}'),
            array('(a{1,3})*', 'a*')
        );
    }

    public function infinite_quant_provider()
    {
        return array(
            array('a{1,}', 'a+'),
            array('a{0,}', 'a*'),
            array('a{5,}', 'aaaaaa*'),
            array('a{0,3}', '(?:a)?(?:a)?(?:a)?'),
            array('a{3}', 'aaa')
        );
    }

    public function alt_provider()
    {
        return array(
            array('a|[bc0-9]|c|[d-f]', '[a]|[b]|[c]|[0-9]|[c]|[d-f]'),
            array('[a-c]|[0-9]|[d-f]', '[0-9]|[a-f]'),
            array('[[:ascii:]]|\d', '[[:ascii:]]')
        );
    }
}
