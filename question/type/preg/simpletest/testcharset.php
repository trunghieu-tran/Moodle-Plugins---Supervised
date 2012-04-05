<?php

/**
 * Unit tests for (some of) question/type/preg/question.php.
 *
 * @copyright &copy; 2011 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */


if (!defined("MOODLE_INTERNAL")) {
    die("Direct access to this script is forbidden.");    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot . "/question/type/preg/preg_nodes.php");

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

	function test_flag_intersect() {//substract is intersect with negation second operand
		//form all types of flag with negative and positive variant
		$digit = new preg_charset_flag;
		$xdigit = new preg_charset_flag;
		$space = new preg_charset_flag;
		$wordchar = new preg_charset_flag;
		$alnum = new preg_charset_flag;
		$alpha = new preg_charset_flag;
		$ascii = new preg_charset_flag;
		$cntrl = new preg_charset_flag;
		$graph = new preg_charset_flag;
		$lower = new preg_charset_flag;
		$upper = new preg_charset_flag;
		$print = new preg_charset_flag;
		$punct = new preg_charset_flag;
		$ndigit = new preg_charset_flag;
		$nxdigit = new preg_charset_flag;
		$nspace = new preg_charset_flag;
		$nwordchar = new preg_charset_flag;
		$nalnum = new preg_charset_flag;
		$nalpha = new preg_charset_flag;
		$nascii = new preg_charset_flag;
		$ncntrl = new preg_charset_flag;
		$ngraph = new preg_charset_flag;
		$nlower = new preg_charset_flag;
		$nupper = new preg_charset_flag;
		$nprint = new preg_charset_flag;
		$npunct = new preg_charset_flag;
		$digit->set_flag(preg_charset_flag::DIGIT);
		$xdigit->set_flag(preg_charset_flag::XDIGIT);
		$space->set_flag(preg_charset_flag::SPACE);
		$wordchar->set_flag(preg_charset_flag::WORDCHAR);
		$alnum->set_flag(preg_charset_flag::ALNUM);
		$alpha->set_flag(preg_charset_flag::ALPHA);
		$ascii->set_flag(preg_charset_flag::ASCII);
		$cntrl->set_flag(preg_charset_flag::CNTRL);
		$graph->set_flag(preg_charset_flag::GRAPH);
		$lower->set_flag(preg_charset_flag::LOWER);
		$upper->set_flag(preg_charset_flag::UPPER);
		$print->set_flag(preg_charset_flag::PRIN);
		$punct->set_flag(preg_charset_flag::PUNCT);
		$ndigit->set_flag(preg_charset_flag::DIGIT);
		$nxdigit->set_flag(preg_charset_flag::XDIGIT);
		$nspace->set_flag(preg_charset_flag::SPACE);
		$nwordchar->set_flag(preg_charset_flag::WORDCHAR);
		$nalnum->set_flag(preg_charset_flag::ALNUM);
		$nalpha->set_flag(preg_charset_flag::ALPHA);
		$nascii->set_flag(preg_charset_flag::ASCII);
		$ncntrl->set_flag(preg_charset_flag::CNTRL);
		$ngraph->set_flag(preg_charset_flag::GRAPH);
		$nlower->set_flag(preg_charset_flag::LOWER);
		$nupper->set_flag(preg_charset_flag::UPPER);
		$nprint->set_flag(preg_charset_flag::PRIN);
		$npunct->set_flag(preg_charset_flag::PUNCT);
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
		//form string for test match of getting flag and two src flag
		$string = '';
		for ($i=1; $i<256; $i++) {
			$string .= chr($i);
		}
		//try intersect
		$result = array();
		foreach ($flags1 as $flag1) {
			foreach ($flags2 as $flag2) {
				$result[] = $flag1->intersect($flag2);
			}
		}
		//TODO: form array of correct result
		//$correct = array(676 values)
		//compare result and correct values
		for ($i=0; $i<676; $i++) {
			if ($correct[$i]===false) {
				$this->assertTrue($result[$i]===false, "failed: result [ $i ]===false");
			} else (
				if ($this->assertFalse($result[$i]===false, "result [ $i ] is false instead preg_charset_flag object")) {
					$this->compare_match_results($flags1[$i/26], $flags2[$i%26], $result[$i]);
				}
			}
		}
	}
	function compare_match_results($src1, $src2, $intersected) {
		//verify input data
		if (!$this->assertTrue($intersected===false, 'intersected is false instead preg_charset_flag object, look for error in test')) {
			return;
		}
		//form string for test match of getting flag and two src flag
		$string = '';
		for ($i=1; $i<256; $i++) {
			$string .= chr($i);
		}
		$pos=0;
		while ($pos<strlen($string)) {
			$name1 = $src1->tohr();
			$name2 = $src2->tohr();
			$character = $string[$pos];
			$this->assertTrue($intersected->match($string, $pos) && (!$src1->match($string, $pos) || !$src2->match($string, $pos)), "False positive result on intersect of '$name1' and '$name2' for character '$character'");
			$this->assertTrue(!$intersected->match($string, $pos) && $src1->match($string, $pos) || $src2->match($string, $pos), "False negative result on intersect of '$name1' and '$name2' for character '$character'");
		}
	}
}
?>