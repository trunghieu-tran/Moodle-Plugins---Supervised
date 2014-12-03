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
        $flag->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('asdf0123'));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 4));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 5));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc015'), 4));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc015'), 5));
    }
    function test_flag_d_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4));
    }
    function test_flag_xdigit_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::XDIGIT);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('12Afg'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('12Afg'), 4));
    }
    function test_flag_s_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 4));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('a bc  '), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('a bc '), 4));
    }
    function test_flag_w_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
    }
    function test_flag_alnum_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALNUM);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
    }
    function test_flag_alpha_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALPHA);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('1a_@5'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('1a_@5'), 4));
    }
    function test_flag_ascii_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ASCII);
        $str = new qtype_poasquestion_string(qtype_poasquestion_string::code2utf8(17).qtype_poasquestion_string::code2utf8(78).qtype_poasquestion_string::code2utf8(130).qtype_poasquestion_string::code2utf8(131).qtype_poasquestion_string::code2utf8(200));
        $this->assertTrue($flag->match($str, 0));
        $this->assertTrue($flag->match($str, 1));
        $this->assertFalse($flag->match($str, 2));
        $this->assertFalse($flag->match($str, 3));
        $this->assertFalse($flag->match($str, 4));
        $flag->negative = true;
        $this->assertFalse($flag->match($str, 0));
        $this->assertFalse($flag->match($str, 1));
        $this->assertTrue($flag->match($str, 2));
        $this->assertTrue($flag->match($str, 3));
        $this->assertTrue($flag->match($str, 4));
    }
    function test_flag_graph_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::GRAPH);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab 5	'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab 5	'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab 5	'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab 5	'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab 5	'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab 5	'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab 5	'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab 5	'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab 5	'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab 5	'), 4));
    }
    function test_flag_lower_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::LOWER);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4));
    }
    function test_flag_upper_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::UPPER);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 4));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('aB!De'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('aB!De'), 4));
    }
    function test_flag_print_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 4));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("ab 5\0"), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("ab 5\0"), 4));
    }
    function test_flag_punct_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PUNCT);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 4));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('ab, c'), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('ab, c'), 4));
    }
    function test_flag_cntrl_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::CNTRL);
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 2));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 3));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 4));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 2));
        $this->assertFalse($flag->match(new qtype_poasquestion_string("abc\26d"), 3));
        $this->assertTrue($flag->match(new qtype_poasquestion_string("abc\26d"), 4));
    }
    function test_unicode_property_matching() {
        $up = new qtype_preg_charset_flag;
        $up->set_data(qtype_preg_charset_flag::UPROP, 'L');
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 0));
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 1));
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 2));
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 3));
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 4));
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 5));
        $up->negative = true;
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 0));
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 1));
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 2));
        $this->assertFalse($up->match(new qtype_poasquestion_string('12qw21'), 3));
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 4));
        $this->assertTrue($up->match(new qtype_poasquestion_string('12qw21'), 5));
    }
    function test_flag_circumflex_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::CIRCUMFLEX, null);
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 2));
        $flag->negative = true;
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 2));
    }
    function test_flag_dollar_match() {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::DOLLAR, null);
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 0));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 1));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 2));
        $flag->negative = true;
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 0));
        $this->assertTrue($flag->match(new qtype_poasquestion_string('abc'), 1));
        $this->assertFalse($flag->match(new qtype_poasquestion_string('abc'), 2));
    }

    function test_flag_intersect() {//substract is intersect with negation second operand
        //form all types of flag with negative and positive variant
        $digit = new qtype_preg_charset_flag;
        $xdigit = new qtype_preg_charset_flag;
        $space = new qtype_preg_charset_flag;
        $wordchar = new qtype_preg_charset_flag;
        $alnum = new qtype_preg_charset_flag;
        $alpha = new qtype_preg_charset_flag;
        $ascii = new qtype_preg_charset_flag;
        $cntrl = new qtype_preg_charset_flag;
        $graph = new qtype_preg_charset_flag;
        $lower = new qtype_preg_charset_flag;
        $upper = new qtype_preg_charset_flag;
        $print = new qtype_preg_charset_flag;
        $punct = new qtype_preg_charset_flag;
        $ndigit = new qtype_preg_charset_flag;
        $nxdigit = new qtype_preg_charset_flag;
        $nspace = new qtype_preg_charset_flag;
        $nwordchar = new qtype_preg_charset_flag;
        $nalnum = new qtype_preg_charset_flag;
        $nalpha = new qtype_preg_charset_flag;
        $nascii = new qtype_preg_charset_flag;
        $ncntrl = new qtype_preg_charset_flag;
        $ngraph = new qtype_preg_charset_flag;
        $nlower = new qtype_preg_charset_flag;
        $nupper = new qtype_preg_charset_flag;
        $nprint = new qtype_preg_charset_flag;
        $npunct = new qtype_preg_charset_flag;
        $digit->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT);
        $xdigit->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::XDIGIT);
        $space->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE);
        $wordchar->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $alnum->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALNUM);
        $alpha->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALPHA);
        $ascii->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ASCII);
        $cntrl->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::CNTRL);
        $graph->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::GRAPH);
        $lower->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::LOWER);
        $upper->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::UPPER);
        $print->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
        $punct->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PUNCT);
        $ndigit->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT);
        $nxdigit->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::XDIGIT);
        $nspace->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE);
        $nwordchar->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $nalnum->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALNUM);
        $nalpha->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALPHA);
        $nascii->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ASCII);
        $ncntrl->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::CNTRL);
        $ngraph->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::GRAPH);
        $nlower->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::LOWER);
        $nupper->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::UPPER);
        $nprint->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
        $npunct->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PUNCT);
        $ndigit->negative = true;
        $nxdigit->negative = true;
        $nspace->negative = true;
        $nwordchar->negative = true;
        $nalnum->negative = true;
        $nalpha->negative = true;
        $nascii->negative = true;
        $ncntrl->negative = true;
        $ngraph->negative = true;
        $nlower->negative = true;
        $nupper->negative = true;
        $nprint->negative = true;
        $npunct->negative = true;
        //put them in two array for loop test
        $flags1 = $flags2 = array($digit, $xdigit, $space, $wordchar, $alnum, $alpha, $ascii, $cntrl, $graph, $lower, $upper, $print, $punct, $ndigit, $nxdigit, $nspace, $nwordchar, $nalnum, $nalpha, $nascii, $ncntrl, $ngraph, $nlower, $nupper, $nprint, $npunct);
        //form array of correct result
        $correct = array( //                digit,      xdigit,     space,      wordchar,   alnum,      alpha,      ascii,      cntrl,      graph,      lower,      upper,      print,      punct,      ndigit,     nxdigit,    nspace,     nwordchar,  nalnum,     nalpha,     nascii,     ncntrl,     ngraph,     nlower,     nupper,     nprint,     npunct
                            /*digit*/       $digit,     $digit,     null,       $digit,     $digit,     null,       'set',      null,       $digit,     null,       null,       $digit,     null,       null,       null,       $digit,     null,       null,       $digit,     false,      $digit,     null,       $digit,     $digit,     null,       $digit,
                            /*xdigit*/      $digit,     $xdigit,    null,       $xdigit,    $xdigit,    'set',      'set',      null,       $xdigit,    'set',      'set',      $xdigit,    null,       'set',      null,       $xdigit,    null,       null,       $digit,     false,      $xdigit,    null,       false,      false,      null,       $xdigit,
                            /*space*/       null,       null,       $space,     null,       null,       null,       'set',      null,       null,       null,       null,       $space,     null,       $space,     $space,     null,       $space,     $space,     $space,     false,      $space,     $space,     $space,     $space,     null,       $space,
                            /*wordchar*/    $digit,     $xdigit,    null,       $wordchar,  $alnum,     $alpha,     'set',      null,       $wordchar,  $lower,     $upper,     $wordchar,  null,       $alnum,     false,      $wordchar,  null,       'set',      false,      false,      $wordchar,  null,       false,      false,      null,       $wordchar,
                            /*alnum*/       $digit,     $xdigit,    null,       $alnum,     $alnum,     $alpha,     'set',      null,       $alnum,     $lower,     $upper,     $alnum,     null,       $alpha,     false,      $alnum,     null,       null,       $digit,     false,      $alnum,     null,       false,      false,      null,       $alnum,
                            /*alpha*/       null,       false,      null,       $alpha,     $alpha,     $alpha,     'set',      null,       $alpha,     $lower,     $upper,     $alpha,     null,       $alpha,     false,      $alpha,     null,       null,       null,       false,      $alpha,     null,       $upper,     $lower,     null,       $alpha,
                            /*ascii*/       'set',      'set',      'set',      'set',      'set',      'set',       $ascii,    'set',      'set',      'set',      'set',      'set',      'set',      'set',      'set',      'set',      'set',      'set',      'set',      null,       'set',      'set',      'set',      'set',      'set',      'set',
                            /*cntrl*/       null,       null,       null,       null,       null,       null,       'set',      $cntrl,     false,      null,       null,       false,      null,       $cntrl,     $cntrl,     $cntrl,     $cntrl,     $cntrl,     $cntrl,     false,      null,       false,      $cntrl,     $cntrl,     false,      false,
                            /*graph*/       $digit,     $xdigit,    null,       $wordchar,  $alnum,     $alpha,     'set',      false,      $graph,     $lower,     $upper,     $print,     false,      false,      false,      false,      false,      false,      false,      false,      false,      null,       false,      false,      false,      false,
                            /*lower*/       null,       null,       null,       $lower,     $lower,     $lower,     'set',      null,       $lower,     $lower,     null,       $lower,     null,       $lower,     false,      $lower,     null,       null,       null,       false,      $lower,     null,       null,       $lower,     null,       $lower,
                            /*upper*/       null,       null,       null,       $upper,     $upper,     $upper,     'set',      null,       $upper,     null,       $upper,     $upper,     null,       $upper,     false,      $upper,     null,       null,       null,       false,      $upper,     null,       $upper,     null,       null,       $upper,
                            /*print*/       $digit,     $xdigit,    $space,     $wordchar,  $alnum,     $alpha,     'set',      false,      $graph,     $lower,     $upper,     $print,     $punct,     false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      null,       false,
                            /*punct*/       null,       null,       null,       null,       null,       null,       'set',      false,      null,       null,       null,       $punct,     $punct,     $punct,     $punct,     $punct,     $punct,     $punct,     $punct,     false,      false,      $punct,     $punct,     $punct,     null,       null,
                            /*ndigit*/      null,       'set',      $space,     $alnum,     $alpha,     $alpha,     'set',      $cntrl,     false,      $lower,     $upper,     false,      $punct,     $ndigit,    $nxdigit,   false,      $nwordchar, $nalnum,    $nalnum,    false,      false,      $ngraph,    false,      false,      $nprint,    false,
                            /*nxdigit*/     null,       null,       $space,     false,      false,      false,      'set',      $cntrl,     false,      $lower,     false,      false,      $punct,     $nxdigit,   $nxdigit,   false,      $nwordchar, $nalnum,    $nalnum,    false,      false,      $ngraph,    false,      false,      $nprint,    false,
                            /*nspace*/      $digit,     $xdigit,    null,       $wordchar,  $alnum,     $alpha,     'set',      $cntrl,     false,      $lower,     $upper,     false,      $punct,     false,      false,      $nspace,    false,      false,      false,      false,      false,      false,      false,      false,      $nprint,    false,
                            /*nwordchar*/   null,       null,       $space,     null,       null,       null,       'set',      $cntrl,     false,      null,       null,       false,      $punct,     $nwordchar, $nwordchar, false,      $nwordchar, $nwordchar, $nwordchar, false,      false,      $ngraph,    $nwordchar, $nwordchar, $nprint,    false,
                            /*nalnum*/      null,       null,       $space,     'set',      null,       null,       'set',      $cntrl,     false,      null,       null,       false,      $punct,     $nalnum,    $nalnum,    false,      $nwordchar, $nalnum,    $nalnum,    false,      false,      $ngraph,    $nalnum,    $nalnum,    $nprint,    false,
                            /*nalpha*/      $digit,     $digit,     $space,     false,      $digit,     null,       'set',      $cntrl,     false,      null,       null,       false,      $punct,     $nalnum,    $nalnum,    false,      $nwordchar, $nalnum,    $nalpha,    false,      false,      $ngraph,    $nalpha,    $nalpha,    $nprint,    false,
                            /*nascii*/      false,      false,      false,      false,      false,      false,      null,       false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      $nascii,    false,      false,      false,      false,      false,      false,
                            /*ncntrl*/      $digit,     $xdigit,    $space,     $wordchar,  $alnum,     $alpha,     'set',      null,       false,      $lower,     $upper,     false,      false,      false,      false,      false,      false,      false,      false,      false,      $ncntrl,    false,      false,      false,      false,      false,
                            /*ngraph*/      null,       null,       $space,     null,       null,       null,       'set',      false,      null,       null,       null,       false,      $punct,     $ngraph,    $ngraph,    false,      $ngraph,    $ngraph,    $ngraph,    false,      false,      $ngraph,    $ngraph,    $ngraph,    $nprint,    false,
                            /*nlower*/      $digit,     false,      $space,     false,      false,      $upper,     'set',      $cntrl,     false,      null,       $upper,     false,      $punct,     false,      false,      false,      $nwordchar, $nalnum,    $nalpha,    false,      false,      $ngraph,    $nlower,    $nalpha,    $nprint,    false,
                            /*nupper*/      $digit,     false,      $space,     false,      false,      $lower,     'set',      $cntrl,     false,      $lower,     null,       false,      $punct,     false,      false,      false,      $nwordchar, $nalnum,    $nalpha,    false,      false,      $ngraph,    $nalpha,    $nupper,    $nprint,    false,
                            /*nprint*/      null,       null,       null,       null,       null,       null,       'set',      false,      false,      null,       null,       null,       null,       $nprint,    $nprint,    $nprint,    $nprint,    $nprint,    $nprint,    false,      false,      $nprint,    $nprint,    $nprint,    $nprint,    $nprint,
                            /*npunct*/      $digit,     $xdigit,    $space,     $wordchar,  $alnum,     $alpha,     'set',      false,      false,      $lower,     $upper,     false,      null,       false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      false,      $nprint,    $npunct
                        );
        //try intersect
        $result = array();
        foreach ($flags1 as $flag1) {
            foreach ($flags2 as $flag2) {
                $result[] = $flag1->intersect($flag2);
            }
        }
        //compare result and correct values
        for ($i=0; $i<676; $i++) {
            if ($correct[$i]===false || $correct[$i]==='set') {//TODO correct work for set result of intersection
                $this->assertTrue($result[$i]===false, 'failed: result[$i]===false');
            } else if ($correct[$i]===null) {
                $this->assertTrue($result[$i]===null, 'failed: result[$i]===null');
            } else {
                if ($this->assertFalse($result[$i]===false, 'result[$i] is false instead qtype_preg_charset_flag object') &&
                    $this->assertFalse($result[$i]===null, 'result[$i] is null instead qtype_preg_charset_flag object' )) {
                    $this->compare_match_results($flags1[$i/26], $flags2[$i%26], $result[$i]);
                }
            }
        }
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
    function test_set_set_intersection() {
        $set1 = new qtype_preg_charset_flag;
        $set1->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('asdfyz'));
        $set2 = new qtype_preg_charset_flag;
        $set2->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('qwertyz'));
        $res1 = $set2->intersect($set1);
        $set1->negative = true;
        $res2 = $set2->intersect($set1);
        $set2->negative = true;
        $res3 = $set2->intersect($set1);
        //++
        $this->assertTrue(is_object($res1), 'Not object got by intersect two positive sets!');
        $this->assertTrue($res1->type===qtype_preg_charset_flag::SET, 'Not set got by intersect two positive sets!');
        $this->assertTrue($res1->data=='yz', 'Incorrect set got by intersect two positive sets!');
        $this->assertFalse($res1->negative, 'Negative set got by intersect two positive set!');
        //-+
        $this->assertTrue(is_object($res2), 'Not object got by intersect positive and negative sets!');
        $this->assertTrue($res2->type===qtype_preg_charset_flag::SET, 'Not set got by intersect positive and negative sets!');
        $this->assertTrue($res2->data=='qwert', 'Incorrect set got by intersect positive and negative sets!');
        $this->assertFalse($res2->negative, 'Negative charset got by intersect positive and negative charset!');
        //--
        $this->assertTrue(is_object($res3), 'Not object got by intersect two negative sets!');
        $this->assertTrue($res3->type===qtype_preg_charset_flag::SET, 'Not set got by intersect two negative sets!');
        $this->assertTrue($res3->data=='qwertyzasdf' || $res3->data=='asdfyzqwert', 'Incorrect set got by intersect two negative sets!');
        $this->assertFalse($res3->data=='qwertyzasdfyz' || $res3->data=='asdfyzqwertyz', 'few chars exist two time in one set after intersection two negative sets!');
        $this->assertTrue($res3->negative, 'Positive charset got by intersect two negative charset!');
    }
    //intersection character's set with any flag or unicode property has one algorithm and only one test need.
    function test_set_flag_intersect() {//intersect set and set hase same algorithm and testing by this test also
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::XDIGIT);
        $set = new qtype_preg_charset_flag;
        $set->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('0123456789abcdEFGHjklmno+-*/!%@#$z'));
        $res1 = $set->intersect($flag);
        $res2 = $flag->intersect($set);
        $this->assertTrue(is_object($res1), 'Not object got by intersect set and flag!');
        $this->assertTrue(is_object($res2), 'Not object got by intersect flag and set!');
        $this->assertTrue($res1->type===qtype_preg_charset_flag::SET, 'Not set got by intersect set and flag!');
        $this->assertTrue($res2->type===qtype_preg_charset_flag::SET, 'Not set got by intersect flag and set!');
        $this->assertTrue($res1->data=='0123456789abcdEF', 'Incorrerct set got by intersect set and flag!');
        $this->assertTrue($res1->data=='0123456789abcdEF', 'Incorrerct set got by intersect flag and set!');
        $this->assertFalse($res1->negative, 'Negative charset got by intersect set and flag!');
        $this->assertFalse($res2->negative, 'Negative charset got by intersect flag and set!');
        $set->negative = true;
        $res1 = $set->intersect($flag);
        $res2 = $flag->intersect($set);
        $this->assertTrue($res1===false, 'Negative set was intersected with flag!');
        $this->assertTrue($res2===false, 'Flag was intersected with negative set!');
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
        $a->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $c->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('s@'));
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
        $a->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b@('));
        $b->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $c->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('s@'));
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
        $a->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $c->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b%)'));
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
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::SET, 'Not set instead first set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[1][0]->type===qtype_preg_charset_flag::SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[2][0]->type===qtype_preg_charset_flag::SET, 'Not set instead second set in intersection of [b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
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
        $a->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b%('));
        $b->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $c->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('s@'));
        $c->negative = true;
        $d->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD);
        $d->negative = true;
        $e->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('a%'));
        $e->negative = true;
        $f->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string('b%)'));
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
        $this->assertTrue($result->flags[0][0]->type===qtype_preg_charset_flag::SET, 'Not set instead first set in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->flags[0][0]->negative, 'First set is negative  in substraction of ^[b%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertTrue($result->flags[0][0]->set=='s', '\"s\" not exist in substraction of ^[b%%(]U\w[^s@] and \W[^a%%]U[b%%)]!');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 0, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 1, $l, true), 'Incorrect matching');
        $this->assertTrue($result->match(new qtype_poasquestion_string('(bs%)'), 2, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 3, $l, true), 'Incorrect matching');
        $this->assertFalse($result->match(new qtype_poasquestion_string('(bs%)'), 4, $l, true), 'Incorrect matching');
    }
}
