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
    use qtype_preg_equality_test_provider;

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
        $actual = $root1->is_equal($root2, 0);
        $this->assertEquals($expected, $actual, "\"$regex1\" is_equal \"$regex2\"");
    }
}

trait qtype_preg_equality_test_provider {

    public function trivial_provider() {
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

    public function charset_provider() {
        return array(
            array('\d', '[\x{0030}-\x{0039}]')
        );
    }

    public function leaf_assert_provider() {
        return array(
            array('^', '(?:\A|(?<=\n))'),
            array('$', '(?:\Z|(?=\n))'),
            array('^^', '^'),
            array('$$', '$'),
            array('$^', '(*FAIL)')
        );
    }

    public function leaf_backref_provider() {
        return array(
            array('b(<name>a)\k<name>', 'b(a)\1'),
            array('(a)\1', '(a)(?:a)'),
            array('(a{3})\1', '(a{3})(?:a{3})')
        );
    }

    public function leaf_subexpr_provider() {
        return array(
            array('a(?:b|c)', 'ab|ac'),
            array('a()\1(?1)', 'a')
        );
    }

    public function leaf_subexpr_call_provider() {
        return array(
            array('b(<a>a)(?&a)', 'b(?:a)'),
            array('(b)(a)(?1)(?2)', '(b)(a)(?:b)(?:a)')
        );
    }

    public function finite_quant_provider() {
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

    public function infinite_quant_provider() {
        return array(
            array('a{1,}', 'a+'),
            array('a{0,}', 'a*'),
            array('a{5,}', 'aaaaaa*'),
            array('a{0,3}', '(?:a)?(?:a)?(?:a)?'),
            array('a{3}', 'aaa')
        );
    }

    public function alt_provider() {
        return array(
            array('a|[bc0-9]|c|[d-f]', '[a]|[b]|[c]|[0-9]|[c]|[d-f]'),
            array('[a-c]|[0-9]|[d-f]', '[0-9]|[a-f]'),
            array('[[:ascii:]]|\d', '[[:ascii:]]')
        );
    }

    public function complex_provider() {
        return array(
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // ВНИМАНИЕ!!!!
            // КОММЕНТАРИИ В ЭТОМ ПРОВАЙДЕРЕ СЪЕХАЛИ ПОСЛЕ ПЕРЕЕЗДА ИЗ GOOGLE DOCS
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // Вход Выход
            // Лишние повторяющиеся символы
            // Одинаковые подряд идущие простые утверждения
            array('^^a', '^a', true),
            array('a$$', 'a$', true),
            array('^^a$$', '^a$', true),
            array('a(?:)', 'a', true),
            array('a(?:){3}', 'a', true),
            array('(?:(?:a))', '(?:a)', true),
            array('a()', 'a', true),
            array('a(){3}', 'a', true),
            array('((a))', '(a)', true),
            // Приведение к квантификаторам одного или нескольких повторяющихся
            array('aaa', 'a{3}', true),
            array('aaab', 'a{3}b', true),
            // Простые утверждения, не имеющие смысла
            // Пустые группировки
            // Пустые подмаски
            // Приведение к квантификаторам: простые тесты
            // символов
            array('baaa', 'ba{3}', true),
            array('abab', '(?:ab){2}', true),
            array('ababa', '(?:ab){2}', true), //или a(?:ba){2}
            array('cbaba', 'c(?:ba){2}', true),
            array('cbabc', 'cbabc', true),
            array('cbabb', 'cbab{2}', true),
            array('bbaba', 'b{2}aba', true), // или b(?:ba){2}
            array('bbabb', 'b{2}ab{2}', true),
            array('babac', '(?:ba){2}c', true),
            array('cbbababa', 'cb{2}(?:ab){2}a', true), //или cb(?:ba){3}
            array('aababab', 'a{2}(?:ba){2}b', true), //или a(?:ab){3}
            array('(aaa)', '(a{3})', true),
            array('(?:aaa)', 'a{3}', true),
            array('(abab)', '((?:ab){2})', true),
            array('(?:abab)', 'ab', true),
            array('aa(?:a)', 'a{3}', true),
            array('(?:a)aa', 'a{3}', true),
            array('aa(?:a)', 'a{3}', true),
            array('(?:aa)aa', 'a{4}', true),
            array('aa(?:aa)aa', 'a{6}', true),
            array('aa(?:aa)', 'a{4}', true),
            array('(?:ab)aaaa', '(?:ab)a{4}', true),
            array('aa(?:ab)aa', 'a{2}(?:ab)a{2}', true),
            array('aaaa(?:ab)', 'a{4}(?:ab)', true),
            array('(?:ab)aa', '(?:ab)a{2}', true),
            array('aa(?:ab)', 'a{2}(?:ab)', true),
            array('(?:ba)aa', 'ba{3}', true),
            array('aa(?:ba)aa', 'a{2}ba{3}', true),
            array('aa(?:ba)', 'a{2}(?:ba)', true),
            array('(?:ab)ab', '(?:ab){2}', true),
            array('ab(?:ab)ab', '(?:ab){3}', true),
            array('ab(?:ab)', '(?:ab){2}', true),
            array('(?:abab)ab', '(?:ab){3}', true),
            array('ab(?:abab)ab', '(?:ab){4}', true),
            array('ab(?:abab)', '(?:ab){3}', true),
            array('(?:a)bab', '(?:ab){2}', true),
            array('a(?:b)ab', '(?:ab){2}', true),
            array('ab(?:a)b', '(?:ab){2}', true),
            array('aba(?:b)', '(?:ab){2}', true),
            array('[a]aa', 'a{3}', true),
            array('a[a]a', 'a{3}', true),
            array('aa[a]', 'a{3}', true),
            array('[a]bab', '(?:ab){2}', true),
            array('a[b]ab', '(?:ab){2}', true),
            array('ab[a]b', '(?:ab){2}', true),
            array('aba[b]', '(?:ab){2}', true),
            // Приведение к квантификаторам одного или несколько повторяющихся
            array('[ab]a|b', '[ab]{2}', true),
            array('a|aa', 'a{1,2}', true),
            array('(a|aa)', '(a{1,2})', true),
            array('(?:a|aa)', 'a{1,2}', true),
            array('(ab|a)', '(ab?)', true),
            array('(?:ab|a)', 'ab?', true),
            array('ab|a', 'ab?', true),
            array('(?:a*)+', 'a*', true),
            array('(?:a*)?', 'a*', true),
            array('(?:a*)*', 'a*', true),
            array('(?:a?)+', 'a*', true),
            array('(?:a?)?', 'a?', true),
            array('(?:a?)*', 'a*', true),
            array('(?:a+)+', 'a+', true),
            array('(?:a+)?', 'a*', true),
            array('(?:a+)*', 'a*', true),
            array('a{1,}', 'a+', true),
            array('a{0,1}', 'a?', true),
            array('a{0,}', 'a*', true),
            array('aa?', 'a{1,2}', true),
            array('aa+', 'a{2,}', true),
            array('aa*', 'a+', true),
            array('символов,', 'содержащих', true), //
            //квантификаторы и/или альтернативы
            // Приведение к квантификаторам: комплексные тесты
            // Квантификатор ? , множество одиночных символов
            array('aaa?', 'a{2,3}', true),
            array('aa?a', 'a{2,3}', true),
            array('a?aa', 'a{2,3}', true),
            array('a?a?a', 'a{1,3}', true),
            array('aa?a?', 'a{1,3}', true),
            array('a?aa?', 'a{1,3}', true),
            array('a?a?a?', 'a{0,3}', true),
            array('aa(?:a)?', 'a{2,3}', true),
            array('aa?(?:a)?', 'a{1,3}', true),
            array('a?a(?:a)?', 'a{1,3}', true),
            array('a?a?(?:a)?', 'a{0,3}', true),
            array('aa(?:a?)', 'a{2,3}', true),
            array('aa?(?:a?)', 'a{1,3}', true),
            array('a?a(?:a?)', 'a{1,3}', true),
            array('a?a?(?:a?)', 'a{0,3}', true),
            array('aa(?:a)?(?:a)?', 'a{2,4}', true),
            array('aa?(?:a)?(?:a)?', 'a{1,4}', true),
            array('a?a(?:a)?(?:a)?', 'a{1,4}', true),
            array('a?a?(?:a)?(?:a)?', 'a{0,4}', true),
            array('aa(?:a?)(?:a?)', 'a{2,4}', true),
            array('aa?(?:a?)(?:a?)', 'a{1,4}', true),
            array('a?a(?:a?)(?:a?)', 'a{1,4}', true),
            array('a?a?(?:a?)(?:a?)', 'a{0,4}', true),
            array('aa(?:a?)(?:a)?', 'a{2,4}', true),
            array('aa?(?:a?)(?:a)?', 'a{1,4}', true),
            array('a?a(?:a?)(?:a)?', 'a{1,4}', true),
            array('a?a?(?:a?)(?:a)?', 'a{0,4}', true),
            array('aa(?:a)?(?:a?)', 'a{2,4}', true),
            array('aa?(?:a)?(?:a?)', 'a{1,4}', true),
            array('a?a(?:a)?(?:a?)', 'a{1,4}', true),
            array('a?a?(?:a)?(?:a?)', 'a{0,4}', true),
            array('aa(a)?', 'a{2}(a)?', true),
            array('aa?(a)?', 'a{1,2}(a)?', true),
            array('a?a(a)?', 'a{1,2}(a)?', true),
            array('a?a?(a)?', 'a{0,2}(a)?', true),
            array('aa(a?)', 'a{2}(a?)', true),
            array('aa?(a?)', 'a{1,2}(a?)', true),
            array('a?a(a?)', 'a{1,2}(a?)', true),
            array('a?a?(a?)', 'a{0,2}(a?)', true),
            array('aa(a)?(a)?', 'a{2}(a)?(a)?', true),
            array('aa?(a)?(a)?', 'a{1,2}(a)?(a)?', true),
            array('a?a(a)?(a)?', 'a{1,2}(a)?(a)?', true),
            array('a?a?(a)?(a)?', 'a{0,2}(a)?(a)?', true),
            array('aa(a?)(a?)', 'a{2}(a?)(a?)', true),
            array('aa?(a?)(a?)', 'a{1,2}(a?)(a?)', true),
            array('a?a(a?)(a?)', 'a{1,2}(a?)(a?)', true),
            array('a?a?(a?)(a?)', 'a{0,2}(a?)(a?)', true),
            array('aa(a?)(a)?', 'a{2}(a?)(a)?', true),
            array('aa?(a?)(a)?', 'a{1,2}(a?)(a)?', true),
            array('a?a(a?)(a)?', 'a{1,2}(a?)(a)?', true),
            array('a?a?(a?)(a)?', 'a{0,2}(a?)(a)?', true),
            array('aa(a)?(a?)', 'a{2}(a)?(a?)', true),
            array('aa?(a)?(a?)', 'a{1,2}(a)?(a?)', true),
            array('a?a(a)?(a?)', 'a{1,2}(a)?(a?)', true),
            array('a?a?(a)?(a?)', 'a{0,2}(a)?(a?)', true),
            array('(aa)(a)?', '(a{2})(a)?', true),
            array('(aa)?(a)?', '(a{2})?(a)?', true),
            array('(aa?)(a)?', '(a{1,2})(a)?', true),
            array('(a?a)(a)?', '(a{1,2})(a)?', true),
            array('(a?a?)(a)?', '(a{0,2})(a)?', true),
            array('(a?a)?(a)?', '(a{1,2})?(a)?', true),
            array('(aa?)?(a)?', '(a{1,2})?(a)?', true),
            array('(a?a?)?(a)?', '(a{0,2})?(a)?', true),
            array('(aa)(a?)', '(a{2})(a?)', true),
            array('(aa)?(a?)', '(a{2})?(a?)', true),
            array('(aa?)(a?)', '(a{1,2})(a?)', true),
            array('(a?a)(a?)', '(a{1,2})(a?)', true),
            array('(a?a?)(a?)', '(a{0,2})(a?)', true),
            array('(a?a)?(a?)', '(a{1,2})?(a?)', true),
            array('(aa?)?(a?)', '(a{1,2})?(a?)', true),
            array('(a?a?)?(a?)', '(a{0,2})?(a?)', true),
            array('(aa)(?:a)?', '(a{2})a?', true),
            array('(aa)?(?:a)?', '(a{2})?a?', true),
            array('(aa?)(?:a)?', '(a{1,2})a?', true),
            array('(a?a)(?:a)?', '(a{1,2})a?', true),
            array('(a?a?)(?:a)?', '(a{0,2})a?', true),
            array('(a?a)?(?:a)?', '(a{1,2})?a?', true),
            array('(aa?)?(?:a)?', '(a{1,2})?a?', true),
            array('(a?a?)?(?:a)?', '(a{0,2})?a?', true),
            array('(aa)(?:a?)', '(a{2})a?', true),
            array('(aa)?(?:a?)', '(a{2})?a?', true),
            array('(aa?)(?:a?)', '(a{1,2})a?', true),
            array('(a?a)(?:a?)', '(a{1,2})a?', true),
            array('(a?a?)(?:a?)', '(a{0,2})a?', true),
            array('(a?a)?(?:a?)', '(a{1,2})?a?', true),
            array('(aa?)?(?:a?)', '(a{1,2})?a?', true),
            array('(a?a?)?(?:a?)', '(a{0,2})?a?', true),
            array('(?:aa)(?:a)?', 'a{2,3}', true),
            array('(?:aa)?(?:a)?', 'a{0,3}', true),
            array('(?:aa?)(?:a)?', 'a{1,3}', true),
            array('(?:a?a)(?:a)?', 'a{1,3}', true),
            array('(?:a?a?)(?:a)?', 'a{0,3}', true),
            array('(?:a?a)?(?:a)?', 'a{0,3}', true),
            array('(?:aa?)?(?:a)?', 'a{0,3}', true),
            array('(?:a?a?)?(?:a)?', 'a{0,3}', true),
            array('(?:aa)(?:a?)', 'a{2,3}', true),
            array('(?:aa)?(?:a?)', 'a{0,3}', true),
            array('(?:aa?)(?:a?)', 'a{1,3}', true),
            array('(?:a?a)(?:a?)', 'a{1,3}', true),
            array('(?:a?a?)(?:a?)', 'a{0,3}', true),
            array('(?:a?a)?(?:a?)', 'a{0,3}', true),
            array('(?:aa?)?(?:a?)', 'a{0,3}', true),
            array('(?:a?a?)?(?:a?)', 'a{0,3}', true),
            array('abab?', 'abab?', true),
            array('aba?b', 'aba?b', true),
            array('ab?ab', 'ab?ab', true),
            array('a?bab', 'a?bab', true),
            array('ababab?', '(?:ab){2}ab?', true),
            array('ababa?b', '(?:ab){1,2}a?b', true),
            array('abab(?:ab)?', '(?:ab){2,3}', true),
            array('abab(?:ab?)', '(?:ab){2}ab?', true),
            array('abab(?:a?b)', '(?:ab){2}a?b', true),
            array('abab(?:ab)?(?:ab)?', '(?:ab){2,4}', true),
            array('abab(?:ab?)(?:ab?)', '(?:ab){2}(?:ab?){2}', true),
            array('abab(?:a?b)(?:ab?)', '(?:ab){2}(?:a?b)(?:ab?)', true),
            array('abab(?:ab?)(?:a?b)', '(?:ab){2}(?:ab?)(?:a?b)', true),
            array('abab(?:ab?)(?:ab)?', '(?:ab){2}(?:ab?)(?:ab)?', true),
            array('abab(?:a?b)(?:ab)?', '(?:ab){2}(?:a?b)(?:ab)?', true),
            array('abab(?:ab)?(?:ab?)', '(?:ab){2,3}(?:ab?)', true),
            array('abab(?:ab)?(?:a?b)', '(?:ab){2,3}(?:a?b)', true),
            array('abab(ab)?', '(?:ab){2}(ab)?', true),
            array('abab(ab)?(ab)?', '(?:ab){2}(ab)?(ab)?', true),
            array('abab(ab?)(ab?)', '(?:ab){2}(ab?)(ab?)', true),
            array('abab(a?b)(ab?)', '(?:ab){2}(a?b)(ab?)', true),
            array('abab(ab?)(a?b)', '(?:ab){2}(ab?)(a?b)', true),
            array('abab(ab?)(ab)?', '(?:ab){2}(ab?)(ab)?', true),
            array('abab(a?b)(ab)?', '(?:ab){2}(a?b)(ab)?', true),
            array('abab(ab)?(ab?)', '(?:ab){2}(ab)?(a?b)', true),
            array('abab(ab)?(a?b)', '(?:ab){2}(ab)?(a?b)', true),
            array('(abab)(ab)?', '((?:ab){2})(ab)?', true),
            array('(abab)(ab?)', '((?:ab){2})(ab?)', true),
            array('(abab)(a?b)', '((?:ab){2})(a?b)', true),
            array('(abab)(?:ab)?', '((?:ab){2})(?:ab)?', true),
            array('(abab)(?ab?)', '((?:ab){2})(?:ab?)', true),
            array('(abab)(?a?b)', '((?:ab){2})(?:a?b)', true),
            array('(?:abab)(?:ab)?', '(?:ab){2,3}', true),
            array('(?:abab)(?:ab?)', '(?:ab){2}(?:ab?)', true),
            array('(?:abab)(?:a?b)', '(?:ab){2}(?:a?b)', true),
            array('aaa+', 'a{3,}', true),
            array('aa+a', 'a{3,}', true),
            array('a+aa', 'a{3,}', true),
            // Квантификатор ? множество парных символов
            // Квантификатор множество одиночных символов
            array('a+a+a', 'a{3,}', true),
            array('aa+a+', 'a{3,}', true),
            array('a+aa+', 'a{3,}', true),
            array('a+a+a+', 'a{3,}', true),
            array('aa(?:a)+', 'a{3,}', true),
            array('aa+(?:a)+', 'a{3,}', true),
            array('a+a(?:a)+', 'a{3,}', true),
            array('a+a+(?:a)+', 'a{3,}', true),
            array('aa(?:a+)', 'a{3,}', true),
            array('aa+(?:a+)', 'a{3,}', true),
            array('a+a(?:a+)', 'a{3,}', true),
            array('a+a+(?:a+)', 'a{3,}', true),
            array('aa(?:a)+(?:a)+', 'a{4,}', true),
            array('aa+(?:a)+(?:a)+', 'a{4,}', true),
            array('a+a(?:a)+(?:a)+', 'a{4,}', true),
            array('a+a+(?:a)+(?:a)+', 'a{4,}', true),
            array('aa(?:a+)(?:a+)', 'a{4,}', true),
            array('aa+(?:a+)(?:a+)', 'a{4,}', true),
            array('a+a(?:a+)(?:a+)', 'a{4,}', true),
            array('a+a+(?:a+)(?:a+)', 'a{4,}', true),
            array('aa(?:a+)(?:a)+', 'a{4,}', true),
            array('aa+(?:a+)(?:a)+', 'a{4,}', true),
            array('a+a(?:a+)(?:a)+', 'a{4,}', true),
            array('a+a+(?:a+)(?:a)+', 'a{4,}', true),
            array('aa(?:a)+(?:a+)', 'a{4,}', true),
            array('aa+(?:a)+(?:a+)', 'a{4,}', true),
            array('a+a(?:a)+(?:a+)', 'a{4,}', true),
            array('a+a+(?:a)+(?:a+)', 'a{4,}', true),
            array('aa(a)+', 'a{2}(a)+', true),
            array('aa+(a)+', 'a{2,}(a)+', true),
            array('a+a(a)+', 'a{2,}(a)+', true),
            array('a+a+(a)+', 'a{2,}(a)+', true),
            array('aa(a+)', 'a{2}(a+)', true),
            array('aa+(a+)', 'a{2,}(a+)', true),
            array('a+a(a+)', 'a{2,}(a+)', true),
            array('a+a+(a+)', 'a{2,}(a+)', true),
            array('aa(a)+(a)+', 'a{2}(?:(a)(a))+', true),
            array('aa+(a)+(a)+', 'a{2,}(?:(a)(a))+', true),
            array('a+a(a)+(a)+', 'a{2,}(?:(a)(a))+', true),
            array('a+a+(a)+(a)+', 'a{2,}(?:(a)(a))+', true),
            array('aa(a+)(a+)', 'a{2}(a+)(a+)', true),
            array('aa+(a+)(a+)', 'a{2,}(a+)(a+)', true),
            array('a+a(a+)(a+)', 'a{2,}(a+)(a+)', true),
            array('a+a+(a+)(a+)', 'a{2,}(a+)(a+)', true),
            array('aa(a+)(a)+', 'a{2}(a+)(a)+', true),
            array('aa+(a+)(a)+', 'a{2,}(a+)(a)+', true),
            array('a+a(a+)(a)+', 'a{2,}(a+)(a)+', true),
            array('a+a+(a+)(a)+', 'a{2,}(a+)(a)+', true),
            array('aa(a)+(a+)', 'a{2}(a)+(a+)', true),
            array('aa+(a)+(a+)', 'a{2,}(a)+(a+)', true),
            array('a+a(a)+(a+)', 'a{2,}(a)+(a+)', true),
            array('a+a+(a)+(a+)', 'a{2,}(a)+(a+)', true),
            array('(aa)(a)+', '(a{2})(a)+', true),
            array('(aa)+(a)+', '(a{2})+(a)+', true),
            array('(aa+)(a)+', '(a{2,})(a)+', true),
            array('(a+a)(a)+', '(a{2,})(a)+', true),
            array('(a+a+)(a)+', '(a{2,})(a)+', true),
            array('(a+a)+(a)+', '(a{2,})+(a)+', true),
            array('(aa+)+(a)+', '(a{2,})+(a)+', true),
            array('(a+a+)+(a)+', '(a{2,})+(a)+', true),
            array('(aa)(a+)', '(a{2})(a+)', true),
            array('(aa)+(a+)', '(a{2})+(a+)', true),
            array('(aa+)(a+)', '(a{2,})(a+)', true),
            array('(a+a)(a+)', '(a{2,})(a+)', true),
            array('(a+a+)(a+)', '(a{2,})(a+)', true),
            array('(a+a)+(a+)', '(a{2,})+(a+)', true),
            array('(aa+)+(a+)', '(a{2,})+(a+)', true),
            array('(a+a+)+(a+)', '(a{2,})+(a+)', true),
            array('(aa)(?:a)+', '(a{2})a+', true),
            array('(aa)+(?:a)+', '(a{2})+a+', true),
            array('(aa+)(?:a)+', '(a{2,})a+', true),
            array('(a+a)(?:a)+', '(a{2,})a+', true),
            array('(a+a+)(?:a)+', '(a{2,})a+', true),
            array('(a+a)+(?:a)+', '(a{2,})+a+', true),
            array('(aa+)+(?:a)+', '(a{2,})+a+', true),
            array('(a+a+)+(?:a)+', '(a{2,})+a+', true),
            array('(aa)(?:a+)', '(a{2})a+', true),
            array('(aa)+(?:a+)', '(a{2})+a+', true),
            array('(aa+)(?:a+)', '(a{2,})a+', true),
            array('(a+a)(?:a+)', '(a{2,})a+', true),
            array('(a+a+)(?:a+)', '(a{2,})a+', true),
            array('(a+a)+(?:a+)', '(a{2,})+a+', true),
            array('(aa+)+(?:a+)', '(a{2,})+a+', true),
            array('(a+a+)+(?:a+)', '(a{2,})+a+', true),
            array('(?:aa)(?:a)+', 'a{3,}', true),
            array('(?:aa)+(?:a)+', 'a{3,}', true),
            array('(?:aa+)(?:a)+', 'a{3,}', true),
            array('(?:a+a)(?:a)+', 'a{3,}', true),
            array('(?:a+a+)(?:a)+', 'a{3,}', true),
            array('(?:a+a)+(?:a)+', 'a{3,}', true),
            array('(?:aa+)+(?:a)+', 'a{3,}', true),
            array('(?:a+a+)+(?:a)+', 'a{3,}', true),
            array('(?:aa)(?:a+)', 'a{3,}', true),
            array('(?:aa)+(?:a+)', 'a{3,}', true),
            array('(?:aa+)(?:a+)', 'a{3,}', true),
            array('(?:a+a)(?:a+)', 'a{3,}', true),
            array('(?:a+a+)(?:a+)', 'a{3,}', true),
            array('(?:a+a)+(?:a+)', 'a{3,}', true),
            array('(?:aa+)+(?:a+)', 'a{3,}', true),
            array('(?:a+a+)+(?:a+)', 'a{3,}', true),
            array('abab+', 'abab+', true),
            array('aba+b', 'aba+b', true),
            array('ab+ab', 'ab+ab', true),
            array('a+bab', 'a+bab', true),
            array('ababab+', '(?:ab){2}ab+', true),
            array('ababa+b', '(?:ab){1,2}a+b', true),
            array('abab(?:ab)+', '(?:ab){3,}', true),
            array('abab(?:ab+)', '(?:ab){2}ab+', true),
            array('abab(?:a+b)', '(?:ab){2}a+b', true),
            array('abab(?:ab)+(?:ab)+', '(?:ab){4,}', true),
            array('abab(?:ab+)(?:ab+)', '(?:ab){2}(?:ab+){2}', true),
            array('abab(?:a+b)(?:ab+)', '(?:ab){2}(?:a+b)(?:ab+)', true),
            array('abab(?:ab+)(?:a+b)', '(?:ab){2}(?:ab+)(?:a+b)', true),
            array('abab(?:ab+)(?:ab)+', '(?:ab){2}(?:ab+)(?:ab)+', true),
            array('abab(?:a+b)(?:ab)+', '(?:ab){2}(?:a+b)(?:ab)+', true),
            array('abab(?:ab)+(?:ab+)', '(?:ab){3,}(?:ab+)', true),
            array('abab(?:ab)+(?:a+b)', '(?:ab){3,}(?:a+b)', true),
            array('abab(ab)+', '(?:ab){2}(ab)+', true),
            array('abab(ab)+(ab)+', '(?:ab){2}(ab)+(ab)+', true),
            array('abab(ab+)(ab+)', '(?:ab){2}(ab+)(ab+)', true),
            array('abab(a+b)(ab+)', '(?:ab){2}(a+b)(ab+)', true),
            array('abab(ab+)(a+b)', '(?:ab){2}(ab+)(a+b)', true),
            array('abab(ab+)(ab)+', '(?:ab){2}(ab+)(ab)+', true),
            array('abab(a+b)(ab)+', '(?:ab){2}(a+b)(ab)+', true),
            array('abab(ab)+(ab+)', '(?:ab){2}(ab)+(a+b)', true),
            array('abab(ab)+(a+b)', '(?:ab){2}(ab)+(a+b)', true),
            array('(abab)(ab)+', '((?:ab){2})(ab)+', true),
            array('(abab)(ab+)', '((?:ab){2})(ab+)', true),
            // Квантификатор +, множество парных символов
            array('(abab)(a+b)', '((?:ab){2})(a+b)', true),
            array('(abab)(?:ab)+', '((?:ab){2})(?:ab)+', true),
            array('(abab)(?ab+)', '((?:ab){2})(?:ab+)', true),
            array('(abab)(?a+b)', '((?:ab){2})(?:a+b)', true),
            array('(?:abab)(?:ab)+', '(?:ab){3,}', true),
            array('(?:abab)(?:ab+)', '(?:ab){2}(?:ab+)', true),
            array('(?:abab)(?:a+b)', '(?:ab){2}(?:a+b)', true),
            array('aaa*', 'a{2,}', true),
            array('aa*a', 'a{2,}', true),
            array('a*aa', 'a{2,}', true),
            array('a*a*a', 'a+', true),
            array('aa*a*', 'a+', true),
            array('a*aa*', 'a+', true),
            array('a*a*a*', 'a*', true),
            array('aa(?:a)*', 'a{2,}', true),
            array('aa*(?:a)*', 'a+', true),
            array('a*a(?:a)*', 'a+', true),
            array('a*a*(?:a)*', 'a*', true),
            array('aa(?:a*)', 'a{2,}', true),
            array('aa*(?:a*)', 'a+', true),
            array('a*a(?:a*)', 'a+', true),
            array('a*a*(?:a*)', 'a*', true),
            array('aa(?:a)*(?:a)*', 'a{2,}', true),
            array('aa*(?:a)*(?:a)*', 'a+', true),
            array('a*a(?:a)*(?:a)*', 'a+', true),
            array('a*a*(?:a)*(?:a)*', 'a*', true),
            array('aa(?:a*)(?:a*)', 'a{2,}', true),
            array('aa*(?:a*)(?:a*)', 'a+', true),
            array('a*a(?:a*)(?:a*)', 'a+', true),
            array('a*a*(?:a*)(?:a*)', 'a*', true),
            array('aa(?:a*)(?:a)*', 'a{2,}', true),
            array('aa*(?:a*)(?:a)*', 'a+', true),
            array('a*a(?:a*)(?:a)*', 'a+', true),
            array('a*a*(?:a*)(?:a)*', 'a*', true),
            array('aa(?:a)*(?:a*)', 'a{2,}', true),
            array('aa*(?:a)*(?:a*)', 'a+', true),
            array('a*a(?:a)*(?:a*)', 'a+', true),
            array('a*a*(?:a)*(?:a*)', 'a*', true),
            array('aa(a)*', 'a{2}(a)*', true),
            array('aa*(a)*', 'a+(a)*', true),
            array('a*a(a)*', 'a+(a)*', true),
            array('a*a*(a)*', 'a*(a)*', true),
            // Квантификатор * множество одиночных символов
            array('aa(a*)', 'a{2}(a*)', true),
            array('aa*(a*)', 'a+(a*)', true),
            array('a*a(a*)', 'a+(a*)', true),
            array('a*a*(a*)', 'a*(a*)', true),
            array('aa(a)*(a)*', 'a{2}(?:(a)(a))*', true),
            array('aa*(a)*(a)*', 'a+(?:(a)(a))*', true),
            array('a*a(a)*(a)*', 'a+(?:(a)(a))*', true),
            array('a*a*(a)*(a)*', 'a*(?:(a)(a))*', true),
            array('aa(a*)(a*)', 'a{2}(a*)(a*)', true),
            array('aa*(a*)(a*)', 'a+(a*)(a*)', true),
            array('a*a(a*)(a*)', 'a+(a*)(a*)', true),
            array('a*a*(a*)(a*)', 'a*(a*)(a*)', true),
            array('aa(a*)(a)*', 'a{2}(a*)(a)*', true),
            array('aa*(a*)(a)*', 'a+(a*)(a)*', true),
            array('a*a(a*)(a)*', 'a+(a*)(a)*', true),
            array('a*a*(a*)(a)*', 'a*(a*)(a)*', true),
            array('aa(a)*(a*)', 'a{2}(a)*(a*)', true),
            array('aa*(a)*(a*)', 'a+(a)*(a*)', true),
            array('a*a(a)*(a*)', 'a+(a)*(a*)', true),
            array('a*a*(a)*(a*)', 'a*(a)*(a*)', true),
            array('(aa)(a)*', '(a{2})(a)*', true),
            array('(aa)*(a)*', '(a{2})*(a)*', true),
            array('(aa*)(a)*', '(a+)(a)*', true),
            array('(a*a)(a)*', '(a+)(a)*', true),
            array('(a*a*)(a)*', '(a*)(a)*', true),
            array('(a*a)*(a)*', '(a+)*(a)*', true),
            array('(aa*)*(a)*', '(a+)*(a)*', true),
            array('(a*a*)*(a)*', '(a*)*(a)*', true),
            array('(aa)(a*)', '(a{2})(a*)', true),
            array('(aa)*(a*)', '(a{2})*(a*)', true),
            array('(aa*)(a*)', '(a+)(a*)', true),
            array('(a*a)(a*)', '(a+)(a*)', true),
            array('(a*a*)(a*)', '(a*)(a*)', true),
            array('(a*a)*(a*)', '(a+)*(a*)', true),
            array('(aa*)*(a*)', '(a+)*(a*)', true),
            array('(a*a*)*(a*)', '(a*)*(a*)', true),
            array('(aa)(?:a)*', '(a{2})a*', true),
            array('(aa)*(?:a)*', '(a{2})*a*', true),
            array('(aa*)(?:a)*', '(a+)a*', true),
            array('(a*a)(?:a)*', '(a+)a*', true),
            array('(a*a*)(?:a)*', '(a*)a*', true),
            array('(a*a)*(?:a)*', '(a+)*a*', true),
            array('(aa*)*(?:a)*', '(a+)*a*', true),
            array('(a*a*)*(?:a)*', '(a*)*a*', true),
            array('(aa)(?:a*)', '(a{2})a*', true),
            array('(aa)*(?:a*)', '(a{2})*a*', true),
            array('(aa*)(?:a*)', '(a+)a*', true),
            array('(a*a)(?:a*)', '(a+)a*', true),
            array('(a*a*)(?:a*)', '(a*)a*', true),
            array('(a*a)*(?:a*)', '(a+)*a*', true),
            array('(aa*)*(?:a*)', '(a+)*a*', true),
            array('(a*a*)*(?:a*)', '(a*)*a*', true),
            array('(?:aa)(?:a)*', 'a{2,}', true),
            array('(?:aa)*(?:a)*', 'a*', true),
            array('(?:aa*)(?:a)*', 'a+', true),
            array('(?:a*a)(?:a)*', 'a+', true),
            array('(?:a*a*)(?:a)*', 'a*', true),
            array('(?:a*a)*(?:a)*', 'a*', true),
            array('(?:aa*)*(?:a)*', 'a*', true),
            array('(?:a*a*)*(?:a)*', 'a*', true),
            array('(?:aa)(?:a*)', 'a{2,}', true),
            array('(?:aa)*(?:a*)', 'a*', true),
            array('(?:aa*)(?:a*)', 'a+', true),
            array('(?:a*a)(?:a*)', 'a+', true),
            array('(?:a*a*)(?:a*)', 'a*', true),
            array('(?:a*a)*(?:a*)', 'a*', true),
            array('(?:aa*)*(?:a*)', 'a*', true),
            array('(?:a*a*)*(?:a*)', 'a*', true),
            array('abab*', 'abab*', true),
            array('aba*b', 'aba*b', true),
            array('ab*ab', 'ab*ab', true),
            array('a*bab', 'a*bab', true),
            array('ababab*', '(?:ab){2}ab*', true),
            array('ababa*b', '(?:ab){1,2}a*b', true),
            array('abab(?:ab)*', '(?:ab){2,}', true),
            array('abab(?:ab*)', '(?:ab){2}ab*', true),
            array('abab(?:a*b)', '(?:ab){2}a*b', true),
            array('abab(?:ab)*(?:ab)*', '(?:ab){2,}', true),
            array('abab(?:ab*)(?:ab*)', '(?:ab){2}(?:ab*){2}', true),
            array('abab(?:a*b)(?:ab*)', '(?:ab){2}(?:a*b)(?:ab*)', true),
            array('abab(?:ab*)(?:a*b)', '(?:ab){2}(?:ab*)(?:a*b)', true),
            array('abab(?:ab*)(?:ab)*', '(?:ab){2}(?:ab*)(?:ab)*', true),
            array('abab(?:a*b)(?:ab)*', '(?:ab){2}(?:a*b)(?:ab)*', true),
            array('abab(?:ab)*(?:ab*)', '(?:ab){2,}(?:ab*)', true),
            array('abab(?:ab)*(?:a*b)', '(?:ab){2,}(?:a*b)', true),
            // Квантификатор *, множество парных символов
            array('abab(ab)*', '(?:ab){2}(ab)*', true),
            array('abab(ab)*(ab)*', '(?:ab){2}(ab)*(ab)*', true),
            array('abab(ab*)(ab*)', '(?:ab){2}(ab*)(ab*)', true),
            array('abab(a*b)(ab*)', '(?:ab){2}(a*b)(ab*)', true),
            array('abab(ab*)(a*b)', '(?:ab){2}(ab*)(a*b)', true),
            array('abab(ab*)(ab)*', '(?:ab){2}(ab*)(ab)*', true),
            array('abab(a*b)(ab)*', '(?:ab){2}(a*b)(ab)*', true),
            array('abab(ab)*(ab*)', '(?:ab){2}(ab)*(a*b)', true),
            array('abab(ab)*(a*b)', '(?:ab){2}(ab)*(a*b)', true),
            array('(abab)(ab)*', '((?:ab){2})(ab)*', true),
            array('(abab)(ab*)', '((?:ab){2})(ab*)', true),
            array('(abab)(a*b)', '((?:ab){2})(a*b)', true),
            array('(abab)(?:ab)*', '((?:ab){2})(?:ab)*', true),
            array('(abab)(?ab*)', '((?:ab){2})(?:ab*)', true),
            array('(abab)(?a*b)', '((?:ab){2})(?:a*b)', true),
            array('(?:abab)(?:ab)*', '(?:ab){2,}', true),
            array('(?:abab)(?:ab*)', '(?:ab){2}(?:ab*)', true),
            array('(?:abab)(?:a*b)', '(?:ab){2}(?:a*b)', true),
            array('(?:a?)?', 'a?', true),
            array('(?:a?)+', 'a*', true),
            array('(?:a?)*', 'a*', true),
            array('(?:a+)?', 'a*', true),
            array('(?:a+)+', 'a+', true),
            array('(?:a+)*', 'a*', true),
            array('(?:a*)?', 'a*', true),
            array('(?:a*)+', 'a*', true),
            array('(?:a*)*', 'a*', true),
            array('(a?)?', '(a?)?', true),
            array('(a?)+', '(a?)+', true),
            array('(a?)*', '(a?)*', true),
            array('(a+)?', '(a*)', true),
            array('(a+)+', '(a+)+', true),
            array('(a+)*', '(a+)*', true),
            array('(a*)?', '(a*)', true),
            array('(a*)+', '(a*)', true),
            array('(a*)*', '(a*)', true),
            array('(?:a?){1,2}', 'a{0,2}', true),
            array('(?:a+){1,2}', 'a+', true),
            array('(?:a*){1,2}', 'a*', true),
            array('(a?){1,2}', '(a){0,2}', true),
            array('(a+){1,2}', '(a)+', true),
            array('(a*){1,2}', '(a*)', true),
            // Простые тесты на сочетания квантификаторов
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(?:a?)?', 'aa?', true), // или a{1,2}
            array('a(?:a?)+', 'a+', true),
            array('a(?:a?)*', 'a+', true),
            array('a(?:a+)?', 'a*', true),
            array('a(?:a+)+', 'aa+', true),
            array('a(?:a+)*', 'a+', true),
            array('a(?:a*)?', 'a+', true),
            array('a(?:a*)+', 'a+', true),
            array('a(?:a*)*', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(a?)?', 'a(a?)?', true),
            array('a(a?)+', 'a(a?)+', true),
            array('a(a?)*', 'a(a?)*', true),
            array('a(a+)?', 'a(a*)', true),
            array('a(a+)+', 'a(a+)+', true),
            array('a(a+)*', 'a(a+)*', true),
            array('a(a*)?', 'a(a*)', true),
            array('a(a*)+', 'a(a*)', true),
            array('a(a*)*', 'a(a*)', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(?:a?)?a', 'a{1,2}', true),
            array('(?:a?)+a', 'a+', true),
            array('(?:a?)*a', 'a+', true),
            array('(?:a+)?a', 'a+', true),
            array('(?:a+)+a', 'a{2,}', true),
            array('(?:a+)*a', 'a+', true),
            array('(?:a*)?a', 'a+', true),
            array('(?:a*)+a', 'a+', true),
            array('(?:a*)*a', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(a?)?a', '(a?)?a', true),
            array('(a?)+a', '(a?)+a', true),
            array('(a?)*a', '(a?)*a', true),
            array('(a+)?a', '(a*)a', true),
            array('(a+)+a', '(a+)+a', true),
            array('(a+)*a', '(a+)*a', true),
            array('(a*)?a', '(a*)a', true),
            // Комплексные тесты на сочетания квантификаторов
            // символа внутри группировки с одиночным символом слева
            // символа внутри подмаски с одиночным символом слева
            // символа внутри группировки с одиночным символом справа
            // символа внутри подмаски с одиночным символом справа
            array('(a*)+a', '(a*)a', true),
            array('(a*)*a', '(a*)a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(?:a?){1,2}', 'a{1,3}', true),
            array('a(?:a+){1,2}', 'a{2,}', true),
            array('a(?:a*){1,2}', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(a?){1,2}', 'a(a?){1,2}', true),
            array('a(a+){1,2}', 'a(a+)', true),
            array('a(a*){1,2}', 'a(a*)', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(?:a?){1,2}a', 'a{1,3}', true),
            array('(?:a+){1,2}a', 'a{2,}', true),
            array('(?:a*){1,2}a', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(a?){1,2}a', '(a?){1,2}a', true),
            array('(a+){1,2}a', '(a+)a', true),
            array('(a*){1,2}a', '(a*)a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с одиночным символом слева
            // символа внутри подмаски с одиночным символом слева
            // символа внутри группировки с одиночным символом справа
            // символа внутри подмаски с одиночным символом справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a?)?', 'aba?', true),
            array('ab(?:a?)+', 'aba*', true),
            array('ab(?:a?)*', 'aba*', true),
            array('ab(?:a+)?', 'aba*', true),
            array('ab(?:a+)+', 'aba+', true),
            array('ab(?:a+)*', 'aba*', true),
            array('ab(?:a*)?', 'aba*', true),
            array('ab(?:a*)+', 'aba*', true),
            array('ab(?:a*)*', 'aba*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('ab(a?)?', 'ab(a?)?', true),
            array('ab(a?)+', 'ab(a?)+', true),
            array('ab(a?)*', 'ab(a?)*', true),
            array('ab(a+)?', 'ab(a*)', true),
            array('ab(a+)+', 'ab(a+)+', true),
            array('ab(a+)*', 'ab(a+)*', true),
            // символов слева
            array('ab(a*)?', 'ab(a*)', true),
            array('ab(a*)+', 'ab(a*)', true),
            array('ab(a*)*', 'ab(a*)', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним левым символом из
            // последовательности символов справа
            array('(?:a?)?ab', 'a{0,2}b', true),
            array('(?:a?)+ab', 'a+b', true),
            array('(?:a?)*ab', 'a+b', true),
            array('(?:a+)?ab', 'a*b', true),
            array('(?:a+)+ab', 'a{2,}b', true),
            array('(?:a+)*ab', 'a+b', true),
            array('(?:a*)?ab', 'a+b', true),
            array('(?:a*)+ab', 'a+b', true),
            array('(?:a*)*ab', 'a+b', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('(a?)?ab', '(a?)?ab', true),
            array('(a?)+ab', '(a?)+ab', true),
            array('(a?)*ab', '(a?)*ab', true),
            array('(a+)?ab', '(a*)ab', true),
            array('(a+)+ab', '(a+)+ab', true),
            array('(a+)*ab', '(a+)*ab', true),
            array('(a*)?ab', '(a*)ab', true),
            array('(a*)+ab', '(a*)ab', true),
            array('(a*)*ab', '(a*)ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символов справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a?){1,2}', 'aba{0,2}', true),
            array('ab(?:a+){1,2}', 'aba+', true),
            array('ab(?:a*){1,2}', 'aba*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('ab(a?){1,2}', 'ab(a?){1,2}', true),
            array('ab(a+){1,2}', 'ab(a+){1,2}', true),
            array('ab(a*){1,2}', 'ab(a*)', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символов слева
            // символа внутри группировки с крайним левым символом из
            // последовательности символов справа
            array('(?:a?){1,2}ab', 'a{1,3}b', true),
            array('(?:a+){1,2}ab', 'a+b', true),
            array('(?:a*){1,2}ab', 'a+b', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('(a?){1,2}ab', '(a?){1,2}ab', true),
            array('(a+){1,2}ab', '(a+){1,2}ab', true),
            array('(a*){1,2}ab', '(a*)ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('a(?:ab?)?', 'a(?:ab?)?', true),
            array('a(?:ab?)+', 'a(?:ab?)+', true),
            array('a(?:ab?)*', 'a(?:ab?)*', true),
            array('a(?:ab+)?', 'a(?:ab+)?', true),
            array('a(?:ab+)+', 'a(?:ab+)+', true),
            array('a(?:ab+)*', 'a(?:ab+)*', true),
            array('a(?:ab*)?', 'a(?:ab*)?', true),
            array('a(?:ab*)+', 'a(?:ab*)+', true),
            array('a(?:ab*)*', 'a(?:ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('a(ab?)?', 'a(ab?)?', true),
            array('a(ab?)+', 'a(ab?)+', true),
            array('a(ab?)*', 'a(ab?)*', true),
            array('a(ab+)?', 'a(ab+)?', true),
            array('a(ab+)+', 'a(ab+)+', true),
            array('a(ab+)*', 'a(ab+)*', true),
            array('a(ab*)?', 'a(ab*)?', true),
            array('a(ab*)+', 'a(ab*)+', true),
            array('a(ab*)*', 'a(ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('(?:ab?)?a', '(?:ab?)?a', true),
            array('(?:ab?)+a', '(?:ab?)+a', true),
            array('(?:ab?)*a', '(?:ab?)*a', true),
            array('(?:ab+)?a', '(?:ab+)?a', true),
            array('(?:ab+)+a', '(?:ab+)+a', true),
            array('(?:ab+)*a', '(?:ab+)*a', true),
            array('(?:ab*)?a', '(?:ab*)?a', true),
            array('(?:ab*)+a', '(?:ab*)+a', true),
            // символов справа
            // символом слева
            // символом слева
            // символом справа
            array('(?:ab*)*a', '(?:ab*)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('(ab?)?a', '(ab?)?a', true),
            array('(ab?)+a', '(ab?)+a', true),
            array('(ab?)*a', '(ab?)*a', true),
            array('(ab+)?a', '(ab+)?a', true),
            array('(ab+)+a', '(ab+)+a', true),
            array('(ab+)*a', '(ab+)*a', true),
            array('(ab*)?a', '(ab*)?a', true),
            array('(ab*)+a', '(ab*)+a', true),
            array('(ab*)*a', '(ab*)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('a(?:ab?){1,2}', 'a(?:ab?){1,2}', true),
            array('a(?:ab+){1,2}', 'a(?:ab+){1,2}', true),
            array('a(?:ab*){1,2}', 'a(?:ab*)', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('a(ab?){1,2}', 'a(ab?){1,2}', true),
            array('a(ab+){1,2}', 'a(ab+){1,2}', true),
            array('a(ab*){1,2}', 'a(ab*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('(?:ab?){1,2}a', '(?:ab?){1,2}a', true),
            array('(?:ab+){1,2}a', '(?:ab+){1,2}a', true),
            array('(?:ab*){1,2}a', '(?:ab*){1,2}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('(ab?){1,2}a', '(ab?){1,2}a', true),
            array('(ab+){1,2}a', '(ab+){1,2}a', true),
            array('(ab*){1,2}a', '(ab*)a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ab?)?', 'ab(?:ab?)?', true),
            array('ab(?:ab?)+', 'ab(?:ab?)+', true),
            array('ab(?:ab?)*', 'ab(?:ab?)*', true),
            // символом справа
            // символом слева
            // символом слева
            // символом справа
            // символом слева
            array('ab(?:ab+)?', 'ab(?:ab+)?', true),
            array('ab(?:ab+)+', 'ab(?:ab+)+', true),
            array('ab(?:ab+)*', 'ab(?:ab+)*', true),
            array('ab(?:ab*)?', 'ab(?:ab*)?', true),
            array('ab(?:ab*)+', 'ab(?:ab*)+', true),
            array('ab(?:ab*)*', 'ab(?:ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ab(ab?)?', 'ab(ab?)?', true),
            array('ab(ab?)+', 'ab(ab?)+', true),
            array('ab(ab?)*', 'ab(ab?)*', true),
            array('ab(ab+)?', 'ab(ab+)?', true),
            array('ab(ab+)+', 'ab(ab+)+', true),
            array('ab(ab+)*', 'ab(ab+)*', true),
            array('ab(ab*)?', 'ab(ab*)?', true),
            array('ab(ab*)+', 'ab(ab*)+', true),
            array('ab(ab*)*', 'ab(ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ab?)?ab', '(?:ab?)?ab', true),
            array('(?:ab?)+ab', '(?:ab?)+ab', true),
            array('(?:ab?)*ab', '(?:ab?)*ab', true),
            array('(?:ab+)?ab', '(?:ab+)?ab', true),
            array('(?:ab+)+ab', '(?:ab+)+ab', true),
            array('(?:ab+)*ab', '(?:ab+)*ab', true),
            array('(?:ab*)?ab', '(?:ab*)?ab', true),
            array('(?:ab*)+ab', '(?:ab*)+ab', true),
            array('(?:ab*)*ab', '(?:ab*)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab?)?ab', '(ab?)?ab', true),
            array('(ab?)+ab', '(ab?)+ab', true),
            array('(ab?)*ab', '(ab?)*ab', true),
            array('(ab+)?ab', '(ab+)?ab', true),
            array('(ab+)+ab', '(ab+)+ab', true),
            array('(ab+)*ab', '(ab+)*ab', true),
            array('(ab*)?ab', '(ab*)?ab', true),
            array('(ab*)+ab', '(ab*)+ab', true),
            array('(ab*)*ab', '(ab*)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левым символом из последовательности символов слева
            // левым символом из последовательности символов справа
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ab?){1,2}', 'ab(?:ab?){1,2}', true),
            array('ab(?:ab+){1,2}', 'ab(?:ab+){1,2}', true),
            array('ab(?:ab*){1,2}', 'ab(?:ab*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ab(ab?){1,2}', 'ab(ab?){1,2}', true),
            array('ab(ab+){1,2}', 'ab(ab+){1,2}', true),
            array('ab(ab*){1,2}', 'ab(ab*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ab?){1,2}ab', '(?:ab?){1,2}ab', true),
            array('(?:ab+){1,2}ab', '(?:ab+){1,2}ab', true),
            array('(?:ab*){1,2}ab', '(?:ab*){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab?){1,2}ab', '(ab?){1,2}ab', true),
            array('(ab+){1,2}ab', '(ab+){1,2}ab', true),
            array('(ab*){1,2}ab', '(ab*){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // левым символом из последовательности символов слева
            // левым символом из последовательности символов справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a?)?', 'aba?', true),
            array('ab(?:a?)+', 'aba*', true),
            array('ab(?:a?)*', 'aba*', true),
            array('ab(?:a+)?', 'aba*', true),
            array('ab(?:a+)+', 'aba+', true),
            array('ab(?:a+)*', 'aba*', true),
            array('ab(?:a*)?', 'aba*', true),
            array('ab(?:a*)+', 'aba*', true),
            array('ab(?:a*)*', 'aba*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(a?)?', 'ab(a?)?', true),
            array('ab(a?)+', 'ab(a?)+', true),
            array('ab(a?)*', 'ab(a?)*', true),
            array('ab(a+)?', 'ab(a+)?', true),
            array('ab(a+)+', 'ab(a+)+', true),
            array('ab(a+)*', 'ab(a+)*', true),
            array('ab(a*)?', 'ab(a*)?', true),
            array('ab(a*)+', 'ab(a*)+', true),
            array('ab(a*)*', 'ab(a*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(?:a?)?', 'ba{1,2}', true),
            array('ba(?:a?)+', 'ba+', true),
            array('ba(?:a?)*', 'ba+', true),
            array('ba(?:a+)?', 'ba+', true),
            array('ba(?:a+)+', 'ba{2,}', true),
            array('ba(?:a+)*', 'ba+', true),
            array('ba(?:a*)?', 'ba+', true),
            array('ba(?:a*)+', 'ba+', true),
            array('ba(?:a*)*', 'ba+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(a?)?', 'ba(a?)?', true),
            array('ba(a?)+', 'ba(a?)+', true),
            array('ba(a?)*', 'ba(a?)*', true),
            array('ba(a+)?', 'ba(a+)?', true),
            array('ba(a+)+', 'ba(a+)+', true),
            array('ba(a+)*', 'ba(a+)*', true),
            array('ba(a*)?', 'ba(a*)?', true),
            array('ba(a*)+', 'ba(a*)+', true),
            array('ba(a*)*', 'ba(a*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(?:a?)?ba', 'a?ba', true),
            array('(?:a?)+ba', 'a*ba', true),
            array('(?:a?)*ba', 'a*ba', true),
            array('(?:a+)?ba', 'a*ba', true),
            array('(?:a+)+ba', 'a+ba', true),
            array('(?:a+)*ba', 'a*ba', true),
            array('(?:a*)?ba', 'a*ba', true),
            array('(?:a*)+ba', 'a*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(a?)?ba', '(a?)?ba', true),
            array('(a?)+ba', '(a?)+ba', true),
            array('(a?)*ba', '(a?)*ba', true),
            array('(a+)?ba', '(a+)?ba', true),
            array('(a+)+ba', '(a+)+ba', true),
            array('(a+)*ba', '(a+)*ba', true),
            array('(a*)?ba', '(a*)?ba', true),
            array('(a*)+ba', '(a*)+ba', true),
            array('(a*)*ba', '(a*)*ba', true),
            array('(a*)*ba', '(a*)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ba(?:a?){1,2}', 'ba{1,3}', true),
            array('ba(?:a+){1,2}', 'ba{2,}', true),
            array('ba(?:a*){1,2}', 'ba+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(a?){1,2}', 'ba(a?){1,2}', true),
            array('ba(a+){1,2}', 'ba(a+){1,2}', true),
            array('ba(a*){1,2}', 'ba(a*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(?:a?){1,2}ba', 'a{0,2}ba', true),
            array('(?:a+){1,2}ba', 'a+ba', true),
            array('(?:a*){1,2}ba', 'a*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов справа
            array('(a?){1,2}ba', '(a?){1,2}ba', true),
            array('(a+){1,2}ba', '(a+){1,2}ba', true),
            array('(a*){1,2}ba', '(a*){1,2}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('a(?:ba?)?', 'a(?:ba?)?', true),
            array('a(?:ba?)+', 'a(?:ba?)+', true),
            array('a(?:ba?)*', 'a(?:ba?)*', true),
            array('a(?:ba+)?', 'a(?:ba+)?', true),
            array('a(?:ba+)+', 'a(?:ba+)+', true),
            array('a(?:ba+)*', 'a(?:ba+)*', true),
            array('a(?:ba*)?', 'a(?:ba*)?', true),
            // символом слева
            array('a(?:ba*)+', 'a(?:ba*)+', true),
            array('a(?:ba*)*', 'a(?:ba*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('a(ba?)?', 'a(ba?)?', true),
            array('a(ba?)+', 'a(ba?)+', true),
            array('a(ba?)*', 'a(ba?)*', true),
            array('a(ba+)?', 'a(ba+)?', true),
            array('a(ba+)+', 'a(ba+)+', true),
            array('a(ba+)*', 'a(ba+)*', true),
            array('a(ba*)?', 'a(ba*)?', true),
            array('a(ba*)+', 'a(ba*)+', true),
            array('a(ba*)*', 'a(ba*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('(?:ba?)?a', '(?:ba?)?a', true),
            array('(?:ba?)+a', '(?:ba?)+a', true),
            array('(?:ba?)*a', '(?:ba?)*a', true),
            array('(?:ba+)?a', '(?:ba+)?a', true),
            array('(?:ba+)+a', '(?:ba+)+a', true),
            array('(?:ba+)*a', '(?:ba+)*a', true),
            array('(?:ba*)?a', '(?:ba*)?a', true),
            array('(?:ba*)+a', '(?:ba*)+a', true),
            array('(?:ba*)*a', '(?:ba*)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('(ba?)?a', '(ba?)?a', true),
            array('(ba?)+a', '(ba?)+a', true),
            array('(ba?)*a', '(ba?)*a', true),
            array('(ba+)?a', '(ba+)?a', true),
            array('(ba+)+a', '(ba+)+a', true),
            array('(ba+)*a', '(ba+)*a', true),
            array('(ba*)?a', '(ba*)?a', true),
            array('(ba*)+a', '(ba*)+a', true),
            array('(ba*)*a', '(ba*)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('a(?:ba?){1,2}', 'a(?:ba?){1,2}', true),
            array('a(?:ba+){1,2}', 'a(?:ba+){1,2}', true),
            // символом слева
            // символом справа
            // символом справа
            // символом слева
            array('a(?:ba*){1,2}', 'a(?:ba*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('a(ba?){1,2}', 'a(ba?){1,2}', true),
            array('a(ba+){1,2}', 'a(ba+){1,2}', true),
            array('a(ba*){1,2}', 'a(ba*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('(?:ba?){1,2}a', '(?:ba?){1,2}a', true),
            array('(?:ba+){1,2}a', '(?:ba+){1,2}a', true),
            array('(?:ba*){1,2}a', '(?:ba*){1,2}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('(ba?){1,2}a', '(ba?){1,2}a', true),
            array('(ba+){1,2}a', '(ba+){1,2}a', true),
            array('(ba*){1,2}a', '(ba*){1,2}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов слева
            array('ba(?:ab?)?', 'ba(?:ab?)?', true),
            array('ba(?:ab?)+', 'ba(?:ab?)+', true),
            array('ba(?:ab?)*', 'ba(?:ab?)*', true),
            array('ba(?:ab+)?', 'ba(?:ab+)?', true),
            array('ba(?:ab+)+', 'ba(?:ab+)+', true),
            array('ba(?:ab+)*', 'ba(?:ab+)*', true),
            array('ba(?:ab*)?', 'ba(?:ab*)?', true),
            array('ba(?:ab*)+', 'ba(?:ab*)+', true),
            array('ba(?:ab*)*', 'ba(?:ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ba?)?', 'ab(?:ba?)?', true),
            array('ab(?:ba?)+', 'ab(?:ba?)+', true),
            array('ab(?:ba?)*', 'ab(?:ba?)*', true),
            array('ab(?:ba+)?', 'ab(?:ba+)?', true),
            array('ab(?:ba+)+', 'ab(?:ba+)+', true),
            array('ab(?:ba+)*', 'ab(?:ba+)*', true),
            array('ab(?:ba*)?', 'ab(?:ba*)?', true),
            array('ab(?:ba*)+', 'ab(?:ba*)+', true),
            array('ab(?:ba*)*', 'ab(?:ba*)*', true),
            // символом слева
            // символом справа
            // символом слева
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ba(ab?)?', 'ba(ab?)?', true),
            array('ba(ab?)+', 'ba(ab?)+', true),
            array('ba(ab?)*', 'ba(ab?)*', true),
            array('ba(ab+)?', 'ba(ab+)?', true),
            array('ba(ab+)+', 'ba(ab+)+', true),
            array('ba(ab+)*', 'ba(ab+)*', true),
            array('ba(ab*)?', 'ba(ab*)?', true),
            array('ba(ab*)+', 'ba(ab*)+', true),
            array('ba(ab*)*', 'ba(ab*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов слева
            array('ab(ba?)?', 'ab(ba?)?', true),
            array('ab(ba?)+', 'ab(ba?)+', true),
            array('ab(ba?)*', 'ab(ba?)*', true),
            array('ab(ba+)?', 'ab(ba+)?', true),
            array('ab(ba+)+', 'ab(ba+)+', true),
            array('ab(ba+)*', 'ab(ba+)*', true),
            array('ab(ba*)?', 'ab(ba*)?', true),
            array('ab(ba*)+', 'ab(ba*)+', true),
            array('ab(ba*)*', 'ab(ba*)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов справа
            array('(?:ab?)?ba', '(?:ab?)?ba', true),
            array('(?:ab?)+ba', '(?:ab?)+ba', true),
            array('(?:ab?)*ba', '(?:ab?)*ba', true),
            array('(?:ab+)?ba', '(?:ab+)?ba', true),
            array('(?:ab+)+ba', '(?:ab+)+ba', true),
            array('(?:ab+)*ba', '(?:ab+)*ba', true),
            array('(?:ab*)?ba', '(?:ab*)?ba', true),
            array('(?:ab*)+ba', '(?:ab*)+ba', true),
            array('(?:ab*)*ba', '(?:ab*)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ba?)?ab', '(?:ba?)?ab', true),
            array('(?:ba?)+ab', '(?:ba?)+ab', true),
            array('(?:ba?)*ab', '(?:ba?)*ab', true),
            array('(?:ba+)?ab', '(?:ba+)?ab', true),
            // правым символом из последовательности символов слева
            array('(?:ba+)+ab', '(?:ba+)+ab', true),
            array('(?:ba+)*ab', '(?:ba+)*ab', true),
            array('(?:ba*)?ab', '(?:ba*)?ab', true),
            array('(?:ba*)+ab', '(?:ba*)+ab', true),
            array('(?:ba*)*ab', '(?:ba*)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab?)?ba', '(ab?)?ba', true),
            array('(ab?)+ba', '(ab?)+ba', true),
            array('(ab?)*ba', '(ab?)*ba', true),
            array('(ab+)?ba', '(ab+)?ba', true),
            array('(ab+)+ba', '(ab+)+ba', true),
            array('(ab+)*ba', '(ab+)*ba', true),
            array('(ab*)?ba', '(ab*)?ba', true),
            array('(ab*)+ba', '(ab*)+ba', true),
            array('(ab*)*ba', '(ab*)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов справа
            array('(ba?)?ab', '(ba?)?ab', true),
            array('(ba?)+ab', '(ba?)+ab', true),
            array('(ba?)*ab', '(ba?)*ab', true),
            array('(ba+)?ab', '(ba+)?ab', true),
            array('(ba+)+ab', '(ba+)+ab', true),
            array('(ba+)*ab', '(ba+)*ab', true),
            array('(ba*)?ab', '(ba*)?ab', true),
            array('(ba*)+ab', '(ba*)+ab', true),
            array('(ba*)*ab', '(ba*)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов слева
            array('ba(?:ab?){1,2}', 'ba(?:ab?){1,2}', true),
            array('ba(?:ab+){1,2}', 'ba(?:ab+){1,2}', true),
            array('ba(?:ab*){1,2}', 'ba(?:ab*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ba?){1,2}', 'ab(?:ba?){1,2}', true),
            array('ab(?:ba+){1,2}', 'ab(?:ba+){1,2}', true),
            array('ab(?:ba*){1,2}', 'ab(?:ba*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            // правым символом из последовательности символов справа
            // правым символом из последовательности символов слева
            array('ba(ab?){1,2}', 'ba(ab?){1,2}', true),
            array('ba(ab+){1,2}', 'ba(ab+){1,2}', true),
            array('ba(ab*){1,2}', 'ba(ab*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов слева
            array('ab(ba?){1,2}', 'ab(ba?){1,2}', true),
            array('ab(ba+){1,2}', 'ab(ba+){1,2}', true),
            array('ab(ba*){1,2}', 'ab(ba*){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов справа
            array('(?:ab?){1,2}ba', '(?:ab?){1,2}ba', true),
            array('(?:ab+){1,2}ba', '(?:ab+){1,2}ba', true),
            array('(?:ab*){1,2}ba', '(?:ab*){1,2}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ba?){1,2}ab', '(?:ba?){1,2}ab', true),
            array('(?:ba+){1,2}ab', '(?:ba+){1,2}ab', true),
            array('(?:ba*){1,2}ab', '(?:ba*){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab?){1,2}ba', '(ab?){1,2}ba', true),
            array('(ab+){1,2}ba', '(ab+){1,2}ba', true),
            array('(ab*){1,2}ba', '(ab*){1,2}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов справа
            array('(ba?){1,2}ab', '(ba?){1,2}ab', true),
            array('(ba+){1,2}ab', '(ba+){1,2}ab', true),
            array('(ba*){1,2}ab', '(ba*){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: не упрощаются
            array('a(?:cb?)?', 'a(?:cb?)?', true),
            array('a(?:cb?)+', 'a(?:cb?)+', true),
            array('a(?:cb?)*', 'a(?:cb?)*', true),
            array('a(?:cb+)?', 'a(?:cb+)?', true),
            array('a(?:cb+)+', 'a(?:cb+)+', true),
            array('a(?:cb+)*', 'a(?:cb+)*', true),
            array('a(?:cb*)?', 'a(?:cb*)?', true),
            array('a(?:cb*)+', 'a(?:cb*)+', true),
            // правым символом из последовательности символов справа
            array('a(?:cb*)*', 'a(?:cb*)*', true),
            array('a(cb?)?', 'a(cb?)?', true),
            array('a(cb?)+', 'a(cb?)+', true),
            array('a(cb?)*', 'a(cb?)*', true),
            array('a(cb+)?', 'a(cb+)?', true),
            array('a(cb+)+', 'a(cb+)+', true),
            array('a(cb+)*', 'a(cb+)*', true),
            array('a(cb*)?', 'a(cb*)?', true),
            array('a(cb*)+', 'a(cb*)+', true),
            array('a(cb*)*', 'a(cb*)*', true),
            array('(?:cb?)?a', '(?:cb?)?a', true),
            array('(?:cb?)+a', '(?:cb?)+a', true),
            array('(?:cb?)*a', '(?:cb?)*a', true),
            array('(?:cb+)?a', '(?:cb+)?a', true),
            array('(?:cb+)+a', '(?:cb+)+a', true),
            array('(?:cb+)*a', '(?:cb+)*a', true),
            array('(?:cb*)?a', '(?:cb*)?a', true),
            array('(?:cb*)+a', '(?:cb*)+a', true),
            array('(?:cb*)*a', '(?:cb*)*a', true),
            array('(cb?)?a', '(cb?)?a', true),
            array('(cb?)+a', '(cb?)+a', true),
            array('(cb?)*a', '(cb?)*a', true),
            array('(cb+)?a', '(cb+)?a', true),
            array('(cb+)+a', '(cb+)+a', true),
            array('(cb+)*a', '(cb+)*a', true),
            array('(cb*)?a', '(cb*)?a', true),
            array('(cb*)+a', '(cb*)+a', true),
            array('(cb*)*a', '(cb*)*a', true),
            array('a(?:cb?){1,2}', 'a(?:cb?){1,2}', true),
            array('a(?:cb+){1,2}', 'a(?:cb+){1,2}', true),
            array('a(?:cb*){1,2}', 'a(?:cb*){1,2}', true),
            array('a(cb?){1,2}', 'a(cb?){1,2}', true),
            array('a(cb+){1,2}', 'a(cb+){1,2}', true),
            array('a(cb*){1,2}', 'a(cb*){1,2}', true),
            array('(?:cb?){1,2}a', '(?:cb?){1,2}a', true),
            array('(?:cb+){1,2}a', '(?:cb+){1,2}a', true),
            array('(?:cb*){1,2}a', '(?:cb*){1,2}a', true),
            array('(cb?){1,2}a', '(cb?){1,2}a', true),
            array('(cb+){1,2}a', '(cb+){1,2}a', true),
            array('(cb*){1,2}a', '(cb*){1,2}a', true),
            array('ab(?:cb?)?', 'ab(?:cb?)?', true),
            array('ab(?:cb?)+', 'ab(?:cb?)+', true),
            array('ab(?:cb?)*', 'ab(?:cb?)*', true),
            array('ab(?:cb+)?', 'ab(?:cb+)?', true),
            array('ab(?:cb+)+', 'ab(?:cb+)+', true),
            array('ab(?:cb+)*', 'ab(?:cb+)*', true),
            array('ab(?:cb*)?', 'ab(?:cb*)?', true),
            array('ab(?:cb*)+', 'ab(?:cb*)+', true),
            array('ab(?:cb*)*', 'ab(?:cb*)*', true),
            array('ab(cb?)?', 'ab(cb?)?', true),
            array('ab(cb?)+', 'ab(cb?)+', true),
            array('ab(cb?)*', 'ab(cb?)*', true),
            array('ab(cb+)?', 'ab(cb+)?', true),
            array('ab(cb+)+', 'ab(cb+)+', true),
            array('ab(cb+)*', 'ab(cb+)*', true),
            array('ab(cb*)?', 'ab(cb*)?', true),
            array('ab(cb*)+', 'ab(cb*)+', true),
            array('ab(cb*)*', 'ab(cb*)*', true),
            array('(?:cb?)?ab', '(?:cb?)?ab', true),
            array('(?:cb?)+ab', '(?:cb?)+ab', true),
            array('(?:cb?)*ab', '(?:cb?)*ab', true),
            array('(?:cb+)?ab', '(?:cb+)?ab', true),
            array('(?:cb+)+ab', '(?:cb+)+ab', true),
            array('(?:cb+)*ab', '(?:cb+)*ab', true),
            array('(?:cb*)?ab', '(?:cb*)?ab', true),
            array('(?:cb*)+ab', '(?:cb*)+ab', true),
            array('(?:cb*)*ab', '(?:cb*)*ab', true),
            array('(cb?)?ab', '(cb?)?ab', true),
            array('(cb?)+ab', '(cb?)+ab', true),
            array('(cb?)*ab', '(cb?)*ab', true),
            array('(cb+)?ab', '(cb+)?ab', true),
            array('(cb+)+ab', '(cb+)+ab', true),
            array('(cb+)*ab', '(cb+)*ab', true),
            array('(cb*)?ab', '(cb*)?ab', true),
            array('(cb*)+ab', '(cb*)+ab', true),
            array('(cb*)*ab', '(cb*)*ab', true),
            array('ab(?:cb?){1,2}', 'ab(?:cb?){1,2}', true),
            array('ab(?:cb+){1,2}', 'ab(?:cb+){1,2}', true),
            array('ab(?:cb*){1,2}', 'ab(?:cb*){1,2}', true),
            array('ab(cb?){1,2}', 'ab(cb?){1,2}', true),
            array('ab(cb+){1,2}', 'ab(cb+){1,2}', true),
            array('ab(cb*){1,2}', 'ab(cb*){1,2}', true),
            array('(?:cb?){1,2}ab', '(?:cb?){1,2}ab', true),
            array('(?:cb+){1,2}ab', '(?:cb+){1,2}ab', true),
            array('(?:cb*){1,2}ab', '(?:cb*){1,2}ab', true),
            array('(cb?){1,2}ab', '(cb?){1,2}ab', true),
            array('(cb+){1,2}ab', '(cb+){1,2}ab', true),
            array('(cb*){1,2}ab', '(cb*){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(?:a)?', 'aa?', true), // или a{1,2}
            array('a(?:a)+', 'a{2,}', true),
            array('a(?:a)*', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(a)?', 'a(a)?', true),
            array('a(a)+', 'a(a)+', true),
            array('a(a)*', 'a(a)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(?:a)?a', 'a{1,2}', true),
            array('(?:a)+a', 'a+', true),
            array('(?:a)*a', 'a+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(a)?a', '(a)?a', true),
            array('(a)+a', '(a)+a', true),
            array('(a)*a', '(a)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(?:a){1,2}', 'a{1,3}', true),
            array('a(?:a){3,4}', 'aa{3,4}', true),
            array('a(?:a)?{3,4}', 'aa?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('a(a){1,2}', 'a(a){1,2}', true),
            array('a(a){3,4}', 'a(a){3,4}', true),
            array('a(a)?{3,4}', 'a(a)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(?:a){1,2}a', 'a{1,3}', true),
            array('(?:a){3,4}a', '(?:a){3,4}a', true),
            array('(?:a)?{3,4}a', '(?:a)?{3,4}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            array('(a){1,2}a', '(a){1,2}a', true),
            array('(a){3,4}a', '(a){3,4}a', true),
            array('(a)?{3,4}a', '(a)?{3,4}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с одиночным символом слева
            // символа внутри подмаски с одиночным символом слева
            // символа внутри группировки с одиночным символом справа
            // символа внутри подмаски с одиночным символом справа
            // символа внутри группировки с одиночным символом слева
            // символа внутри подмаски с одиночным символом слева
            // символа внутри группировки с одиночным символом справа
            // символа внутри подмаски с одиночным символом справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a)?', 'aba?', true),
            array('ab(?:a)+', 'aba+', true),
            array('ab(?:a)*', 'aba*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('ab(a)?', 'ab(a)?', true),
            array('ab(a)+', 'ab(a)+', true),
            array('ab(a)*', 'ab(a)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символов слева
            // символа внутри группировки с крайним левым символом из
            // последовательности символов справа
            array('(?:a)?ab', 'a{0,2}b', true),
            array('(?:a)+ab', 'a{2,}b', true),
            array('(?:a)*ab', 'a+b', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('(a)?ab', '(a)?ab', true),
            array('(a)+ab', '(a)+ab', true),
            array('(a)*ab', '(a)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символов справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a){1,2}', 'aba{1,2}', true),
            array('ab(?:a){3,4}', 'aba{3,4}', true),
            array('ab(?:a)?{3,4}', 'aba?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            array('ab(a){1,2}', 'ab(a){1,2}', true),
            array('ab(a){3,4}', 'ab(a){3,4}', true),
            array('ab(a)?{3,4}', 'ab(a)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символов слева
            // символа внутри группировки с крайним левым символом из
            // последовательности символов справа
            array('(?:a){1,2}ab', 'a{1,3}b', true),
            array('(?:a){3,4}ab', 'a{4,5}b', true),
            array('(?:a)?{3,4}ab', 'a?{3,4}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним левым символом из последовательности
            // символов справа
            array('(a){1,2}ab', '(a){1,2}ab', true),
            array('(a){3,4}ab', '(a){3,4}ab', true),
            array('(a)?{3,4}ab', '(a)?{3,4}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('a(?:ab)?', 'a(?:ab)?', true),
            array('a(?:ab)+', 'a(?:ab)+', true),
            array('a(?:ab)*', 'a(?:ab)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('a(ab)?', 'a(ab?)?', true),
            array('a(ab)+', 'a(ab?)+', true),
            array('a(ab)*', 'a(ab?)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('(?:ab)?a', '(?:ab)?a', true),
            array('(?:ab)+a', '(?:ab)+a', true),
            array('(?:ab)*a', '(?:ab)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('(ab)?a', '(ab)?a', true),
            array('(ab)+a', '(ab)+a', true),
            array('(ab)*a', '(ab)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('a(?:ab){1,2}', 'a(?:ab){1,2}', true),
            array('a(?:ab){3,4}', 'a(?:ab){3,4}', true),
            array('a(?:ab)?{3,4}', 'a(?:ab)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с
            array('a(ab){1,2}', 'a(ab){1,2}', true),
            array('a(ab){3,4}', 'a(ab){3,4}', true),
            array('a(ab)?{3,4}', 'a(ab)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            array('(?:ab){1,2}a', '(?:ab){1,2}a', true),
            // символом слева
            // символом слева
            // символом справа
            // символом справа
            // символом слева
            // символом слева
            // символом справа
            array('(?:ab){3,4}a', '(?:ab){3,4}a', true),
            array('(?:ab)?{3,4}a', '(?:ab)?{3,4}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с имволом
            array('(ab){1,2}a', '(ab){1,2}a', true),
            array('(ab){3,4}a', '(ab){3,4}a', true),
            array('(ab)?{3,4}a', '(ab)?(3,4)a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ab)?', '(?:ab){1,2}', true),
            array('ab(?:ab)+', '(?:ab){2,}', true),
            array('ab(?:ab)*', '(?:ab)+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ab(ab)?', 'ab(ab?)?', true),
            array('ab(ab)+', 'ab(ab?)+', true),
            array('ab(ab)*', 'ab(ab?)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ab)?ab', '(?:ab){1,2}', true),
            array('(?:ab)+ab', '(?:ab?){2,}', true),
            array('(?:ab)*ab', '(?:ab?)+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab)?ab', '(ab)?ab', true),
            array('(ab)+ab', '(ab)+ab', true),
            array('(ab)*ab', '(ab)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ab){1,2}', '(?:ab){2,3}', true),
            array('ab(?:ab){3,4}', '(?:ab+){4,5}', true),
            array('ab(?:ab)?{3,4}', 'ab(?:ab)?{1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ab(ab){1,2}', 'ab(ab){1,2}', true),
            array('ab(ab){3,4}', 'ab(ab){3,4}', true),
            // слева
            // левым символом из последовательности символов слева
            // левым символом из последовательности символов справа
            // левым символом из последовательности символов слева
            array('ab(ab)?{3,4}', 'ab(ab)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ab){1,2}ab', '(?:ab){1,2}ab', true),
            array('(?:ab){3,4}ab', '(?:ab){4,5}', true),
            array('(?:ab)?{3,4}ab', '(?:ab)?{3,4}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab){1,2}ab', '(ab){1,2}ab', true),
            array('(ab){3,4}ab', '(ab){3,4}ab', true),
            array('(ab)?{3,4}ab', '(ab)?{3,4}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // левым символом из последовательности символов справа
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(?:a)?', 'aba?', true),
            array('ab(?:a)+', 'aba+', true),
            array('ab(?:a)*', 'aba*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ab(a)?', 'ab(a)?', true),
            array('ab(a)+', 'ab(a)+', true),
            array('ab(a)*', 'ab(a)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(?:a)?', 'ba{1,2}', true),
            array('ba(?:a)+', 'ba{2,}', true),
            array('ba(?:a)*', 'ba+', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(a)?', 'ba(a)?', true),
            array('ba(a)+', 'ba(a)+', true),
            array('ba(a)*', 'ba(a)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(?:a)?ba', 'a?ba', true),
            array('(?:a)+ba', 'a+ba', true),
            array('(?:a)*ba', 'a*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(a)?ba', '(a)?ba', true),
            array('(a)+ba', '(a)+ba', true),
            array('(a)*ba', '(a)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним левым символом из
            // последовательности символов слева
            array('ba(?:a){1,2}', 'ba{1,3}', true),
            array('ba(?:a){3,4}', 'baa{3,4}', true),
            array('ba(?:a)?{3,4}', 'ba(?:a)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов слева
            array('ba(a){1,2}', 'ba(a){1,2}', true),
            array('ba(a){3,4}', 'ba(a){3,4}', true),
            array('ba(a)?{3,4}', 'ba(a)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри группировки с крайним правым символом из
            // последовательности символов справа
            array('(?:a){1,2}ba', 'a{1,2}ba', true),
            array('(?:a){3,4}ba', 'a{3,4}ba', true),
            array('(?:a)?{3,4}ba', 'a?{3,4}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение одиночного
            // символа внутри подмаски с крайним правым символом из
            // последовательности символов справа
            array('(a){1,2}ba', '(a){1,2}ba', true),
            array('(a){3,4}ba', '(a){3,4}ba', true),
            array('(a)?{3,4}ba', '(a)?{3,4}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('a(?:ba)?', 'a(?:ba)?', true),
            array('a(?:ba)+', 'a(?:ba)+', true),
            array('a(?:ba)*', 'a(?:ba)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('a(ba)?', 'a(ba)?', true),
            array('a(ba)+', 'a(ba?)+', true),
            array('a(ba)*', 'a(ba?)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // символом слева
            // символом слева
            // правого символа из последовательности символов внутри подмаски с
            array('a(ba)?', 'a(ba)?', true),
            array('a(ba)+', 'a(ba)+', true),
            array('a(ba)*', 'a(ba)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('(?:ba)?a', '(?:ba)?a', true),
            array('(?:ba)+a', '(?:ba)+a', true),
            array('(?:ba)*a', '(?:ba)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('(ba)?a', '(ba)?a', true),
            array('(ba)+a', '(ba)+a', true),
            array('(ba)*a', '(ba)*a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('a(?:ba){1,2}', 'a(?:ba){1,2}', true),
            array('a(?:ba){3,4}', 'a(?:ba){1,2}', true),
            array('a(?:ba)?{3,4}', 'a(?:ba){1,2}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('a(ba){1,2}', 'a(ba){1,2}', true),
            array('a(ba){3,4}', 'a(ba){3,4}', true),
            array('a(ba)?{3,4}', 'a(ba)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            array('(?:ba){1,2}a', '(?:ba){1,2}a', true),
            array('(?:ba){3,4}a', '(?:ba){3,4}a', true),
            array('(?:ba)?{3,4}a', '(?:ba)?{3,4}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            array('(ba){1,2}a', '(ba){1,2}a', true),
            array('(ba){3,4}a', '(ba){3,4}a', true),
            array('(ba){3,4}a', '(ba){3,4}a', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов слева
            // символом слева
            // символом справа
            // символом справа
            // символом слева
            // символом слева
            // символом справа
            // символом слева
            array('ba(?:ab)?', 'ba(?:ab)?', true),
            array('ba(?:ab)+', 'ba(?:ab)+', true),
            array('ba(?:ab)*', 'ba(?:ab)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ba)?', 'ab(?:ba)?', true),
            array('ab(?:ba)+', 'ab(?:ba)+', true),
            array('ab(?:ba)*', 'ab(?:ba)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ba(ab)?', 'ba(ab)?', true),
            array('ba(ab)+', 'ba(ab)+', true),
            array('ba(ab)*', 'ba(ab)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов слева
            array('ab(ba)?', 'ab(ba)?', true),
            array('ab(ba)+', 'ab(ba)+', true),
            array('ab(ba)*', 'ab(ba)*', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов справа
            array('(?:ab)?ba', '(?:ab)?ba', true),
            array('(?:ab)+ba', '(?:ab)+ba', true),
            array('(?:ab)*ba', '(?:ab)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ba)?ab', '(?:ba)?ab', true),
            array('(?:ba)+ab', '(?:ba)+ab', true),
            array('(?:ba)*ab', '(?:ba)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab)?ba', '(ab)?ba', true),
            array('(ab)+ba', '(ab)+ba', true),
            array('(ab)*ba', '(ab)*ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов справа
            array('(ba)?ab', '(ba)?ab', true),
            // правым символом из последовательности символов слева
            // правым символом из последовательности символов справа

            array('(ba)+ab', '(ba)+ab', true),
            array('(ba)*ab', '(ba)*ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов слева
            array('ba(?:ab){1,2}', 'ba(?:ab){1,2}', true),
            array('ba(?:ab){3,4}', 'ba(?:ab){3,4}', true),
            array('ba(?:ab)?{3,4}', 'ba(?:ab)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов слева
            array('ab(?:ba){1,2}', 'ab(?:ba){1,2}', true),
            array('ab(?:ba){3,4}', 'ab(?:ba){3,4}', true),
            array('ab(?:ba)?{3,4}', 'ab(?:ba)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('ba(ab){1,2}', 'ba(ab){1,2}', true),
            array('ba(ab){3,4}', 'ba(ab){3,4}', true),
            array('ba(ab)?{3,4}', 'ba(ab)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов слева
            array('ab(ba){1,2}', 'ab(ba){1,2}', true),
            array('ab(ba){3,4}', 'ab(ba){3,4}', true),
            array('ab(ba)?{3,4}', 'ab(ba)?{3,4}', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри группировки с
            // крайним правым символом из последовательности символов справа
            array('(?:ab){1,2}ba', '(?:ab?){1,2}ba', true),
            array('(?:ab){3,4}ba', '(?:ab+){3,4}ba', true),
            array('(?:ab)?{3,4}ba', '(?:ab*)?{3,4}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри группировки с
            // крайним левым символом из последовательности символов справа
            array('(?:ba){1,2}ab', '(?:ba){1,2}ab', true),
            array('(?:ba){3,4}ab', '(?:ba){1,2}ab', true),
            array('(?:ba)?{3,4}ab', '(?:ba){1,2}ab', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // левого символа из последовательности символов внутри подмаски с крайним
            array('(ab){1,2}ba', '(ab){1,2}ba', true),
            array('(ab){3,4}ba', '(ab){3,4}ba', true),
            // правым символом из последовательности символов слева
            // правым символом из последовательности символов справа
            array('(ab)?{3,4}ba', '(ab)?{3,4}ba', true),
            // Комплексные тесты на сочетания квантификаторов: совпадение крайнего
            // правого символа из последовательности символов внутри подмаски с
            // крайним левым символом из последовательности символов справа
            array('(ba){1,2}ab', '(ba){1,2}ab', true),
            array('(ba){3,4}ab', '(ba){3,4}ab', true),
            array('(ba)?{3,4}ab', '(ba)?{3,4}ab', true),
            // Комплексные тесты на сочетания квантификаторов: три и более
            array('(?:(?:a*)?)?', 'a*', true),
            array('(?:(?:a*)?)+', 'a*', true),
            array('(?:(?:a*)?)*', 'a*', true),
            array('(?:(?:a*)+)?', 'a*', true),
            array('(?:(?:a*)+)+', 'a*', true),
            array('(?:(?:a*)+)*', 'a*', true),
            array('(?:(?:a*)*)?', 'a*', true),
            array('(?:(?:a*)*)+', 'a*', true),
            array('(?:(?:a*)*)*', 'a*', true),
            array('(?:(?:a+)?)?', 'a*', true),
            array('(?:(?:a+)?)+', 'a*', true),
            array('(?:(?:a+)?)*', 'a*', true),
            array('(?:(?:a+)+)?', 'a*', true),
            array('(?:(?:a+)+)+', 'a+', true),
            array('(?:(?:a+)+)*', 'a*', true),
            array('(?:(?:a+)*)?', 'a*', true),
            array('(?:(?:a+)*)+', 'a*', true),
            array('(?:(?:a+)*)*', 'a*', true),
            array('(?:(?:a?)?)?', 'a?', true),
            array('(?:(?:a?)?)+', 'a*', true),
            array('(?:(?:a?)?)*', 'a*', true),
            array('(?:(?:a?)+)?', 'a*', true),
            array('(?:(?:a?)+)+', 'a*', true),
            array('(?:(?:a?)+)*', 'a*', true),
            array('(?:(?:a?)*)?', 'a*', true),
            array('(?:(?:a?)*)+', 'a*', true),
            array('(?:(?:a?)*)*', 'a*', true),
            array('(?:(?:aa*)?)?', 'a*', true),
            array('(?:(?:aa*)?)+', 'a*', true),
            array('(?:(?:aa*)?)*', 'a*', true),
            array('(?:(?:aa*)+)?', 'a*', true),
            array('(?:(?:aa*)+)+', 'a+', true),
            array('(?:(?:aa*)+)*', 'a*', true),
            array('(?:(?:aa*)*)?', 'a*', true),
            // квантификаторов
            array('(?:(?:aa*)*)+', 'a*', true),
            array('(?:(?:aa*)*)*', 'a*', true),
            array('(?:(?:aa+)?)?', 'a*', true),
            array('(?:(?:aa+)?)+', 'a*', true),
            array('(?:(?:aa+)?)*', 'a*', true),
            array('(?:(?:aa+)+)?', 'a*', true),
            array('(?:(?:aa+)+)+', 'a{2,}', true),
            array('(?:(?:aa+)+)*', 'a*', true),
            array('(?:(?:aa+)*)?', 'a*', true),
            array('(?:(?:aa+)*)+', 'a*', true),
            array('(?:(?:aa+)*)*', 'a*', true),
            array('(?:(?:aa?)?)?', 'a{1,2}', true),
            array('(?:(?:aa?)?)+', 'a*', true),
            array('(?:(?:aa?)?)*', 'a*', true),
            array('(?:(?:aa?)+)?', 'a*', true),
            array('(?:(?:aa?)+)+', 'a*', true),
            array('(?:(?:aa?)+)*', 'a*', true),
            array('(?:(?:aa?)*)?', 'a*', true),
            array('(?:(?:aa?)*)+', 'a*', true),
            array('(?:(?:aa?)*)*', 'a*', true),
            array('ba|a', 'b?a', true),
            array('ba|a?', 'ba|a?', true),
            array('ba|a+', 'ba|+', true),
            array('ba|a*', 'ba|a*', true),
            array('ba?|a', 'ba?|a', true),
            array('ba+|a', 'b?a+', true),
            array('ba*|a', 'b?a*', true),
            array('b?a|a', 'b?a', true),
            array('b+a|a', 'b+a|a', true),
            array('b*a|a', 'b*a', true),
            array('(?:ba|a)?', '(b?a)?', true),
            array('(?:ba|a)+', '(b?a)+', true),
            array('(?:ba|a)*', '(b?a)*', true),
            array('ab|a', 'ab?', true),
            array('ab|a?', '(?:ab|a|)', true),
            array('ab|a+', 'ab|a+', true),
            array('ab|a*', 'ab|a*', true),
            array('ab?|a', 'ab?', true),
            array('ab+|a', 'ab*', true),
            array('ab*|a', 'ab*', true),
            array('a?b|a', 'a?b?', true),
            array('a+b|a', 'a+b?', true),
            // Преобразование альтернатив
            array('a*b|a', 'a+b?', true),
            array('(?:ab|a)?', '(?:a|ab|)', true),
            array('(?:ab|a)+', '(?ab)+|a+', true),
            array('(?:ab|a)*', '(?:ab)*|a*', true),
            array('(?:a|)?', 'a?', true),
            array('(?:a|)+', 'a*', true),
            array('(?:a|)*', 'a*', true),
            array('(?:a|)', 'a?', true),
            array('(?:a|b)+', 'a+|b+', true),
            array('(?:a|b)*', 'a*|b*', true),
            array('a|aaa|aaaa', 'a|a{3,4}', true),
            array('a|aa?', 'aa?', true),
            array('a|aaa?', 'a{1,3}', true),
            array('a|aaaa?', 'a|a{3,4}', true),
            array('a|(?:ab)', '(?:ab?)', true),
            array('a|(?:aab)', 'a(?:ab)?', true),
            // Альтернатива
            // Альтернатива с пустотой
            array('a?|', 'a?', true),
            array('a*|', 'a*', true),
            array('a+|', 'a*', true),
            array('a(a?|)', 'a(a?)', true),
            array('a(a+|)', 'a(a*)', true),
            array('a(a*|)', 'a(a*)', true),
            array('a(?:a?|)', 'a{0,2}', true),
            array('a(?:a+|)', 'a*', true),
            array('a(?:a*|)', 'a*', true),
            array('a{0,}|', 'a*', true),
            array('a{1,}|', 'a*', true),
            array('a{0,1}|', 'a?', true),
            array('a{0,3}|', 'a{0,3}', true),
            array('a|b', '[ab]', true),
            array('a|b|', '[ab]?', true),
            array('a|c|b|d', '[a-d]', true),
            array('a|b|c|e|f|g', '[a-ce-g]', true),
            array('a|b|[c-d]', '[a-d]', true),
            array('a|b|c|[e-g]', '[a-be-g]', true),
            array('a|b|c|[x-z]?', '[a-cx-z]?', true),
            array('a|b|c|[x-z]+', '[a-c]|[x-z]+', true),
            array('a|b|c|[x-z]*', '[a-c]|[x-z]*', true),
            array('\s|a', '[\sa]', true),
            array('\S|a', '[\Sa]', true),
            // Приведение к символьным классам
            array('\w|a', '[\wa]', true),
            array('\W|a', '[\Wa]', true),
            array('\d|a', '[\da]', true),
            array('\D|a', '[\Da]', true),
            array('a?|b', '[ab]?', true),
            array('a+|b', '(a+|b)', true),
            array('a*|b', '(a*|b)', true),
            array('a|b?', '[ab]?', true),
            array('a|b+', 'a|b+', true),
            array('a|b*', 'a|b*', true),
            array('(?:a|b)?', '(?:[ab])?', true),
            array('[0-9]', '\d', true),
            array('[^0-9]', '\D', true),
            array('[ \f\n\r\t\v]', '\s', true),
            array('[^ \f\n\r\t\v]', '\S', true),
            array('[[:word:]]', '\w', true),
            array('[^[:word:]]', '\W', true)
            // Эквивалентная замена
        );
    }
}
