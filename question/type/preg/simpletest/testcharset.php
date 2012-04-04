<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/question.php.
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */


if (!defined("MOODLE_INTERNAL")) {
    die("Direct access to this script is forbidden.");    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot . "/question/type/preg/preg_nodes.php");
//preg_charset_flag
//preg_leaf_charset
class qtype_preg_charset_test extends UnitTestCase {
	function setUp() {
	}
	function teearDown() {
	}
	function test_set_match() {
		$flag = new preg_charset_flag;
		$flag->set_set("asdf0123");
		$this->assertTrue($flag->match("abc015", 0));
		$this->assertFalse($flag->match("abc015", 1));
		$this->assertFalse($flag->match("abc015", 2));
		$this->assertTrue($flag->match("abc015", 3));
		$this->assertTrue($flag->match("abc015", 4));
		$this->assertFalse($flag->match("abc015", 5));
		$flag->negative = true;
		$this->assertFalse($flag->match("abc015", 0));
		$this->assertTrue($flag->match("abc015", 1));
		$this->assertTrue($flag->match("abc015", 2));
		$this->assertFalse($flag->match("abc015", 3));
		$this->assertFalse($flag->match("abc015", 4));
		$this->assertTrue($flag->match("abc015", 5));
	}
/*
	const DIGIT = "ctype_ digit";			//\d AND [:digit:]
	const XDIGIT = "ctype_ xdigit";			//[:xdigit:]
	const SPACE = "ctype_ space"; 			//\s AND [:space:]
	const WORDCHAR = "self::is_wordchar";	//\w AND [:word:]
	const ALNUM = "ctype_alnum";			//[:alnum:]
	const ALPHA = "ctype_alpha";			//[:alpha:]
	const ASCII = "self::is_ascii";			//[:ascii:]
	const CNTRL = "ctype_ cntrl";			//[:cntrl:]
	const GRAPH = "ctype_ graph";			//[:graph:]
	const LOWER = "ctype_ lower";			//[:lower:]
	const UPPER = "ctype_ upper";			//[:upper:]
	const PRIN = "ctype_ print";			//[:print:] PRIN, because PRINT is php keyword
	const PUNCT = "ctype_ punct";			//[:punct:]
*/
	function test_flag_d_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::DIGIT);
		$this->assertTrue($flag->match("12Afg", 0));
		$this->assertTrue($flag->match("12Afg", 1));
		$this->assertFalse($flag->match("12Afg", 2));
		$this->assertFalse($flag->match("12Afg", 3));
		$this->assertFalse($flag->match("12Afg", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("12Afg", 0));
		$this->assertFalse($flag->match("12Afg", 1));
		$this->assertTrue($flag->match("12Afg", 2));
		$this->assertTrue($flag->match("12Afg", 3));
		$this->assertTrue($flag->match("12Afg", 4));
	}
	function test_flag_xdigit_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::XDIGIT);
		$this->assertTrue($flag->match("12Afg", 0));
		$this->assertTrue($flag->match("12Afg", 1));
		$this->assertTrue($flag->match("12Afg", 2));
		$this->assertTrue($flag->match("12Afg", 3));
		$this->assertFalse($flag->match("12Afg", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("12Afg", 0));
		$this->assertFalse($flag->match("12Afg", 1));
		$this->assertFalse($flag->match("12Afg", 2));
		$this->assertFalse($flag->match("12Afg", 3));
		$this->assertTrue($flag->match("12Afg", 4));
	}
	function test_flag_s_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::SPACE);
		$this->assertFalse($flag->match("a bc	", 0));
		$this->assertTrue($flag->match("a bc	", 1));
		$this->assertFalse($flag->match("a bc	", 2));
		$this->assertFalse($flag->match("a bc	", 3));
		$this->assertTrue($flag->match("a bc	", 4));
		$flag->negative = true;
		$this->assertTrue($flag->match("a bc	", 0));
		$this->assertFalse($flag->match("a bc	", 1));
		$this->assertTrue($flag->match("a bc	", 2));
		$this->assertTrue($flag->match("a bc	", 3));
		$this->assertFalse($flag->match("a bc	", 4));
	}
	function test_flag_w_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::WORDCHAR);
		$this->assertTrue($flag->match("1a_@5", 0));
		$this->assertTrue($flag->match("1a_@5", 1));
		$this->assertTrue($flag->match("1a_@5", 2));
		$this->assertFalse($flag->match("1a_@5", 3));
		$this->assertTrue($flag->match("1a_@5", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("1a_@5", 0));
		$this->assertFalse($flag->match("1a_@5", 1));
		$this->assertFalse($flag->match("1a_@5", 2));
		$this->assertTrue($flag->match("1a_@5", 3));
		$this->assertFalse($flag->match("1a_@5", 4));
	}
	function test_flag_alnum_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::ALNUM);
		$this->assertTrue($flag->match("1a_@5", 0));
		$this->assertTrue($flag->match("1a_@5", 1));
		$this->assertFalse($flag->match("1a_@5", 2));
		$this->assertFalse($flag->match("1a_@5", 3));
		$this->assertTrue($flag->match("1a_@5", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("1a_@5", 0));
		$this->assertFalse($flag->match("1a_@5", 1));
		$this->assertTrue($flag->match("1a_@5", 2));
		$this->assertTrue($flag->match("1a_@5", 3));
		$this->assertFalse($flag->match("1a_@5", 4));
	}
	function test_flag_alpha_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::ALPHA);
		$this->assertFalse($flag->match("1a_@5", 0));
		$this->assertTrue($flag->match("1a_@5", 1));
		$this->assertFalse($flag->match("1a_@5", 2));
		$this->assertFalse($flag->match("1a_@5", 3));
		$this->assertFalse($flag->match("1a_@5", 4));
		$flag->negative = true;
		$this->assertTrue($flag->match("1a_@5", 0));
		$this->assertFalse($flag->match("1a_@5", 1));
		$this->assertTrue($flag->match("1a_@5", 2));
		$this->assertTrue($flag->match("1a_@5", 3));
		$this->assertTrue($flag->match("1a_@5", 4));
	}
	function test_flag_ascii_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::ASCII);
		$str = chr(17).chr(78).chr(130).chr(131).chr(200);
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
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::GRAPH);
		$this->assertTrue($flag->match("ab 5\0", 0));
		$this->assertTrue($flag->match("ab 5\0", 1));
		$this->assertFalse($flag->match("ab 5\0", 2));
		$this->assertTrue($flag->match("ab 5\0", 3));
		$this->assertFalse($flag->match("ab 5\0", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("ab 5\0", 0));
		$this->assertFalse($flag->match("ab 5\0", 1));
		$this->assertTrue($flag->match("ab 5\0", 2));
		$this->assertFalse($flag->match("ab 5\0", 3));
		$this->assertTrue($flag->match("ab 5\0", 4));
	}
	function test_flag_lower_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::LOWER);
		$this->assertTrue($flag->match("aB!De", 0));
		$this->assertFalse($flag->match("aB!De", 1));
		$this->assertFalse($flag->match("aB!De", 2));
		$this->assertFalse($flag->match("aB!De", 3));
		$this->assertTrue($flag->match("aB!De", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("aB!De", 0));
		$this->assertTrue($flag->match("aB!De", 1));
		$this->assertTrue($flag->match("aB!De", 2));
		$this->assertTrue($flag->match("aB!De", 3));
		$this->assertFalse($flag->match("aB!De", 4));
	}
	function test_flag_upper_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::UPPER);
		$this->assertFalse($flag->match("aB!De", 0));
		$this->assertTrue($flag->match("aB!De", 1));
		$this->assertFalse($flag->match("aB!De", 2));
		$this->assertTrue($flag->match("aB!De", 3));
		$this->assertFalse($flag->match("aB!De", 4));
		$flag->negative = true;
		$this->assertTrue($flag->match("aB!De", 0));
		$this->assertFalse($flag->match("aB!De", 1));
		$this->assertTrue($flag->match("aB!De", 2));
		$this->assertFalse($flag->match("aB!De", 3));
		$this->assertTrue($flag->match("aB!De", 4));
	}
	function test_flag_print_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::PRIN);
		$this->assertTrue($flag->match("ab 5\0", 0));
		$this->assertTrue($flag->match("ab 5\0", 1));
		$this->assertTrue($flag->match("ab 5\0", 2));
		$this->assertTrue($flag->match("ab 5\0", 3));
		$this->assertFalse($flag->match("ab 5\0", 4));
		$flag->negative = true;
		$this->assertFalse($flag->match("ab 5\0", 0));
		$this->assertFalse($flag->match("ab 5\0", 1));
		$this->assertFalse($flag->match("ab 5\0", 2));
		$this->assertFalse($flag->match("ab 5\0", 3));
		$this->assertTrue($flag->match("ab 5\0", 4));
	}
	function test_flag_punct_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::PUNCT);
		$this->assertFalse($flag->match("ab, c", 0));
		$this->assertFalse($flag->match("ab, c", 1));
		$this->assertTrue($flag->match("ab, c", 2));
		$this->assertFalse($flag->match("ab, c", 3));
		$this->assertFalse($flag->match("ab, c", 4));
		$flag->negative = true;
		$this->assertTrue($flag->match("ab, c", 0));
		$this->assertTrue($flag->match("ab, c", 1));
		$this->assertFalse($flag->match("ab, c", 2));
		$this->assertTrue($flag->match("ab, c", 3));
		$this->assertTrue($flag->match("ab, c", 4));
	}
	function test_flag_cntrl_match() {
		$flag = new preg_charset_flag;
		$flag->set_flag(preg_charset_flag::CNTRL);
		$this->assertFalse($flag->match("abc\26d", 0));
		$this->assertFalse($flag->match("abc\26d", 1));
		$this->assertFalse($flag->match("abc\26d", 2));
		$this->assertTrue($flag->match("abc\26d", 3));
		$this->assertFalse($flag->match("abc\26d", 4));
		$flag->negative = true;
		$this->assertTrue($flag->match("abc\26d", 0));
		$this->assertTrue($flag->match("abc\26d", 1));
		$this->assertTrue($flag->match("abc\26d", 2));
		$this->assertFalse($flag->match("abc\26d", 3));
		$this->assertTrue($flag->match("abc\26d", 4));
	}
	//TODO: test unicode property
	function test_flag_circumflex_match() {
		$flag = new preg_charset_flag;
		$flag->set_circumflex();
		$this->assertTrue($flag->match("abc", 0));
		$this->assertFalse($flag->match("abc", 1));
		$this->assertFalse($flag->match("abc", 2));
		$flag->negative = true;
		$this->assertFalse($flag->match("abc", 0));
		$this->assertTrue($flag->match("abc", 1));
		$this->assertTrue($flag->match("abc", 2));
	}
	function test_flag_dollar_match() {
		$flag = new preg_charset_flag;
		$flag->set_dollar();
		$this->assertFalse($flag->match("abc", 0));
		$this->assertFalse($flag->match("abc", 1));
		$this->assertTrue($flag->match("abc", 2));
		$flag->negative = true;
		$this->assertTrue($flag->match("abc", 0));
		$this->assertTrue($flag->match("abc", 1));
		$this->assertFalse($flag->match("abc", 2));
	}
}
?>