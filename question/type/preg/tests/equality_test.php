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


class qtype_preg_equality_test extends PHPUnit_Framework_TestCase {

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
        $actual = $root1->is_equal($root2);
        $this->assertEquals($expected, $actual, "\"$regex1\" is_equal \"$regex2\"" );
    }

    public function trivial_provider()
    {
        return array(
            array('(?(?=a)b|c)', '(?(?=a)b|c)', true),
            array('(?(?=a)b)', '(?(?=a)b)', true),
            array('(?(?=a)b)', '(?(?=a)b|c)', false),
            array('(a)(a)(a)(?(1)a)', '(a)(a)(a)(?(1)a)', true),
            array('(?<name>a)(?(name)a)', '(?<name2>a)(?(name2)a)', true),
            array('(?<name>a)(?(name)a)', '(a)(?<name2>a)(?(name2)a)', false),
            array('(?<name>a)(?(name)a)', '(?<name>a)(?(<name>)a|b)', false),
            array('(?(DEFINE)(?<name>a))', '(?(DEFINE)(?<name>a))', true),

            array('b(?<a>a|b|c)(?&a)', 'b(a|b|c)(?1)', true),
            array("((?<a>can)\s+not|(?&a)(?:'|`|)t)", "((can)\s+not|(?2)(?:'|`|)t)", true),
            array("((?<a>can)\s+not|(?&a)(?:'|`|)t)", "((?<a>can)\s+not|(?&a)(?:'|`|)t)", true),

            array('(?###totallynonexistentname<)a(?###>)', '(?###totallynonexistentname<)a(?###>)', true),
            array('(?###totallynonexistentname<)a(?###>)', '(?###totallynonexistentname2<)a(?###>)', false),

            array('(a)', '(a)', true),
            array('(?<name>a)', '(?<name>a)', true),
            array('(a)', '(?<name>a)', true),
            array('(?:a)', '(?:a)', true),
            array('(a)', '(?:a)', false),

            array('(?=a|b|c)a', '(?=a|b|c)a', true),
            array('(?!a|b|c)a', '(?=a|b|c)a', false),

            array('a{5,}', 'a{5,}', true),
            array('a{3,}', 'a{4,}', false),
            array('a{3,}?', 'a{3,}', false),
            array('a++', 'a+', false),
            array('a?', 'b?', false),
            array('a+', 'a+', true),

            array('a{1,3}', 'a{1,3}', true),
            array('a{1,3}', 'a{1,4}', false),
            array('a{1}?', 'a{1}', false),
            array('a{1}+', 'a{1}', false),
            array('a{1}', 'b{1}', false),
            array('a{1}', 'a{1}', true),

            array('(?###totallynonexistentname)', '(?###totallynonexistentname2)', false),
            array('(?###totallynonexistentname)', '(?###totallynonexistentname)', true),

            array('(?&name)', '(?&eman)', true), // because both reference to nothing
            array('(?<name>a)(?&name)', '(?<eman>a)(?&eman)', true),
            array('(?<name>a)(?&name)', '(?<name>a)(?&name)', true),
            array('(a)(b)(?1)', '(a)(b)(?2)', false),
            array('(a)(b)(?1)', '(a)(b)(?1)', true),
            array('(a)(b)(c)(?+1)', '(a)(b)(c)(?+2)', false),
            array('(a)(b)(c)(?+1)', '(a)(b)(c)(?+1)', true),

            array('(?<name>a)\g{name}', '(?<name>a)\g{name}', true),
            array('(?<name>a)\g{name}', '(?<name2>a)\g{name2}', true),
            array('(a)(b)(c)\1', '(a)(b)(c)\1', true),
            array('(a)(b)(c)\1', '(a)(b)(c)\2', false),

            array('a|b|', 'a|b|', true),
            array('a|b|c', 'a|b|c', true),
            array('a|b|c', 'b|c|a', true),
            array('a|b|c', 'b|a|c', true),
            array('a|b|c', 'b|a', false),
            array('b|a', 'a|b|c' , false),

            array('abc', 'abc', true),

            array('[[:digit:]abce]', '[ea-c[:digit:]]', true),
            array('[[:digit:]abce]', '[a-c[:digit:]]', false),
            array('[a-c][[:digit:]abce]', '[[:digit:]abce][a-c]', false),

            array('\b\A\a\Z\z^$', '\b\A\a\Z\z^$', true)
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
