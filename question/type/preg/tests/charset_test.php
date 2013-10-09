<?php

/**
 * Unit tests for charset.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');


class qtype_preg_charset_flag_test extends PHPUnit_Framework_TestCase {

    function test_set_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('asdf0123'));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 4, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 5, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 4, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 5, true));
    }
    function test_flag_d_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_DIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
    }
    function test_flag_xdigit_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_XDIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4, true));
    }
    function test_flag_s_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_SPACE);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 4, true));
    }
    function test_flag_w_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }
    function test_flag_alnum_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALNUM);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }
    function test_flag_alpha_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALPHA);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4, true));
    }
    function test_flag_ascii_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ASCII);
        $str = new qtype_poasquestion_string(qtype_preg_unicode::code2utf8(17).qtype_preg_unicode::code2utf8(78).qtype_preg_unicode::code2utf8(130).qtype_preg_unicode::code2utf8(131).qtype_preg_unicode::code2utf8(200));
        $this->assertTrue($flag->match($str, 0, true));
        $this->assertTrue($flag->match($str, 1, true));
        $this->assertFalse($flag->match($str, 2, true));
        $this->assertFalse($flag->match($str, 3, true));
        $this->assertFalse($flag->match($str, 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match($str, 0, true));
        $this->assertFalse($flag->match($str, 1, true));
        $this->assertTrue($flag->match($str, 2, true));
        $this->assertTrue($flag->match($str, 3, true));
        $this->assertTrue($flag->match($str, 4, true));
    }
    function test_flag_graph_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_GRAPH);
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\t"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\t"), 4, true));
    }
    function test_flag_lower_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_LOWER);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
    }
    function test_flag_upper_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_UPPER);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4, true));
    }
    function test_flag_print_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PRINT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 4, true));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 4, true));
    }
    function test_flag_punct_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PUNCT);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 4, true));
    }
    function test_flag_cntrl_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_CNTRL);
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 0, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 1, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 2, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 3, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 4, true));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 0, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 1, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 2, true));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 3, true));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 4, true));
    }
    function compare_match_results($src1, $src2, $intersected) {
        //verify input data
        if ($this->assertFalse($intersected===false, 'intersected is false instead qtype_preg_charset_flag object, look for error in test')) {
            return;
        }
        if ($this->assertFalse($intersected===null, 'intersected is null instead qtype_preg_charset_flag object, look for error in test')) {
            return;
        }
        //form string for test match of getting flag and two src flag
        $string = new qtype_poasquestion_string('');
        for ($i=1; $i<256; $i++) {
            $string .= chr($i);
        }
        //test
        $pos=0;
        while ($pos<strlen($string)) {
            $name1 = $src1->tohr();
            $name2 = $src2->tohr();
            $character = $string[$pos];
            $this->assertTrue($intersected->match($string, $pos) && (!$src1->match($string, $pos) || !$src2->match($string, $pos)), 'False positive result on intersect of "$name1" and "$name2" for character "$character"');
            $this->assertTrue(!$intersected->match($string, $pos) && $src1->match($string, $pos) || $src2->match($string, $pos), 'False negative result on intersect of "$name1" and "$name2" for character "$character"');
        }
        //TODO: May be range comparing also? It require range testing.
    }
}

class qtype_preg_charset_test extends UnitTestCase {
    function setUp() {
    }
    function teearDown() {
    }
    function test_match() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        //form charsets
        $charset = new qtype_preg_leaf_charset;
        $charset->flags[0][0] = $a;
        $charset->flags[1][0] = $b;
        $charset->flags[1][1] = $c;
        $this->assertTrue($charset->match(new qtype_poasquestion_string('bs@'), 0, $l, true));
        $this->assertFalse($charset->match(new qtype_poasquestion_string('bs@'), 1, $l, true));
        $this->assertTrue($charset->match(new qtype_poasquestion_string('bs@'), 2, $l, true));
    }
    function test_next() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        //form charsets
        $charset = new qtype_preg_leaf_charset;
        $charset->flags[0][0] = $a;
        $charset->flags[1][0] = $b;
        $charset->flags[1][1] = $c;
        $this->assertTrue(strlen($charset->next_character(new qtype_poasquestion_string(''), 0))==1, 'Not one character got by next_character()!');
        $this->assertTrue($charset->match($charset->next_character(new qtype_poasquestion_string(''), 0), 0, $l, true), 'Next character is unmatched!');
    }
    function test_intersect() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $d = new qtype_preg_charset_flag;
        $e = new qtype_preg_charset_flag;
        $f = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%)'));
        //form charsets
        $charset1 = new qtype_preg_leaf_charset;
        $charset1->flags[0][0] = $a;
        $charset1->flags[1][0] = $b;
        $charset1->flags[1][1] = $c;
        $charset2 = new qtype_preg_leaf_charset;
        $charset2->flags[0][0] = $d;
        $charset2->flags[0][1] = $e;
        $charset2->flags[1][0] = $f;
        //intersect them
        $result = $charset1->intersect($charset2);
        //verify result
        $this->assertTrue(count($result->flags)==3, 'Incorrect count of disjunct in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[0])==1, 'Incorrect count of flags in first disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[1])==1, 'Incorrect count of flags in second disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[2])==1, 'Incorrect count of flags in third disjunct of  intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead first set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[1][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[2][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[0][0]->negative, 'First set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[1][0]->negative, 'Second set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[2][0]->negative, 'Second set is negative  in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='(' || $result->flags[1][0]->set=='(' || $result->flags[2][0]->set=='(', '\'(\' not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='b%' || $result->flags[1][0]->set=='b%' || $result->flags[2][0]->set=='b%%', '\"b%\" not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='b' || $result->flags[1][0]->set=='b' || $result->flags[2][0]->set=='b', '\"b\" not exist in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 0, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 1, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(b@%)'), 2, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(b@%)'), 3, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(b@%)'), 4, $l, true), 'Incorrect matching');
    }
    function test_substract() {
        //create elemenntary charclasses
        $a = new qtype_preg_charset_flag;
        $b = new qtype_preg_charset_flag;
        $c = new qtype_preg_charset_flag;
        $d = new qtype_preg_charset_flag;
        $e = new qtype_preg_charset_flag;
        $f = new qtype_preg_charset_flag;
        $a->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $c->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string('b%)'));
        //form charsets
        $charset1 = new qtype_preg_leaf_charset;
        $charset1->flags[0][0] = $a;
        $charset1->flags[1][0] = $b;
        $charset1->flags[1][1] = $c;
        $charset1->negative = true;
        $charset2 = new qtype_preg_leaf_charset;
        $charset2->flags[0][0] = $d;
        $charset2->flags[0][1] = $e;
        $charset2->flags[1][0] = $f;
        $charset2->negative = false;
        //intersect them
        $result = $charset1->substract($charset2);
        //verify result
        $this->assertTrue(count($result->flags)==1, 'Incorrect count of disjunct in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue(count($result->flags[0])==1, 'Incorrect count of flags in first disjunct of  substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::TYPE_SET, 'Not set instead first set in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[0][0]->negative, 'First set is negative  in substraction of ^[b%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='s', '\"s\" not exist in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 0, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 1, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(bs%)'), 2, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 3, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 4, $l, true), 'Incorrect matching');
    }
}
