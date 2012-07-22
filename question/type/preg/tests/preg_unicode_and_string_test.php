<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Unit tests for preg string class.
 *
 * @copyright  2012 Valeriy Streltsov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_unicode_and_string_test extends PHPUnit_Framework_TestCase {

    function test_string() {
        $str1 = new qtype_poasquestion_string('аzб');
        $str2 = new qtype_poasquestion_string('йц者');
        $str3 = new qtype_poasquestion_string($str1 . $str2);

        $this->assertTrue(is_a($str3, 'qtype_poasquestion_string'));
        $this->assertTrue($str3->string() === 'аzбйц者');
        $this->assertTrue($str3->length() === 6);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'а');
        $this->assertTrue($str3[1] === 'z');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === 'ц');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === null);

        $str3[-1] = 'Q';
        $str3[0] = 'W';
        $str3[1] = 'E';
        $str3[4] = '者';
        $str3[6] = 'ه';
        $str3->concatenate('ab');
        $str3->concatenate(new qtype_poasquestion_string('ёя'));

        $this->assertTrue($str3->length() === 11);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'W');
        $this->assertTrue($str3[1] === 'E');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === '者');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === 'ه');
        $this->assertTrue($str3[7] === 'a');
        $this->assertTrue($str3[8] === 'b');
        $this->assertTrue($str3[9] === 'ё');
        $this->assertTrue($str3[10] === 'я');
        $this->assertTrue($str3[11] === null);
    }

    function test_intersect_positive_ranges() {
        $range1 = array(array('negative' => false, 0 => 0, 1 => 10));
        $range2 = array(array('negative' => false, 0 => 3, 1 => 13));
        $range3 = array(array('negative' => false, 0 => 2, 1 => 7), array('negative' => false, 0 => 8, 1 => 9));
        $result = qtype_preg_unicode::intersect_ranges(array($range1, $range2, $range3));
        $this->assertTrue(count($result) === 2);
        $this->assertTrue($result[0][0] === 3);
        $this->assertTrue($result[0][1] === 7);
        $this->assertTrue($result[1][0] === 8);
        $this->assertTrue($result[1][1] === 9);
    }

    function test_intersect_negative_ranges() {
        $range1 = array(array('negative' => true, 0 => 10, 1 => 100));
        $range2 = array(array('negative' => true, 0 => 300, 1 => 0x10FFFD));
        $range3 = array(array('negative' => true, 0 => 150, 1 => 250));
        $result = qtype_preg_unicode::intersect_ranges(array($range1, $range2, $range3));
        $this->assertTrue(count($result) === 3);
        $this->assertTrue($result[0][0] === 0);
        $this->assertTrue($result[0][1] === 10);
        $this->assertTrue($result[1][0] === 100);
        $this->assertTrue($result[1][1] === 150);
        $this->assertTrue($result[2][0] === 250);
        $this->assertTrue($result[2][1] === 300);
        $range1 = array(array('negative' => true, 0 => 0, 1 => 100));
        $range2 = array(array('negative' => true, 0 => 300, 1 => 0x10FFFD));
        $range3 = array(array('negative' => true, 0 => 150, 1 => 250));
        $result = qtype_preg_unicode::intersect_ranges(array($range1, $range2, $range3));
        $this->assertTrue(count($result) === 2);
        $this->assertTrue($result[0][0] === 100);
        $this->assertTrue($result[0][1] === 150);
        $this->assertTrue($result[1][0] === 250);
        $this->assertTrue($result[1][1] === 300);
    }

    function test_intersect_mixed_ranges() {
        $range1 = array(array('negative' => true, 0 => 200, 1 => 300));
        $range2 = array(array('negative' => false, 0 => 100, 1 => 230));
        $range3 = array(array('negative' => true, 0 => 240, 1 => 400));
        $result = qtype_preg_unicode::intersect_ranges(array($range1, $range2, $range3));
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === 100);
        $this->assertTrue($result[0][1] === 200);
    }

    function test_get_ranges_from_charset() {
        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion_string('a'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[0][1] === qtype_poasquestion_string::ord('a'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion_string('ab'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[0][1] === qtype_poasquestion_string::ord('b'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion_string('abc'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[0][1] === qtype_poasquestion_string::ord('c'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion_string('abde'));
        $this->assertTrue(count($ranges) === 2);
        $this->assertTrue($ranges[0][0] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[0][1] === qtype_poasquestion_string::ord('b'));
        $this->assertTrue($ranges[1][0] === qtype_poasquestion_string::ord('d'));
        $this->assertTrue($ranges[1][1] === qtype_poasquestion_string::ord('e'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion_string('acdfghj'));
        $this->assertTrue(count($ranges) === 4);
        $this->assertTrue($ranges[0][0] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[0][1] === qtype_poasquestion_string::ord('a'));
        $this->assertTrue($ranges[1][0] === qtype_poasquestion_string::ord('c'));
        $this->assertTrue($ranges[1][1] === qtype_poasquestion_string::ord('d'));
        $this->assertTrue($ranges[2][0] === qtype_poasquestion_string::ord('f'));
        $this->assertTrue($ranges[2][1] === qtype_poasquestion_string::ord('h'));
        $this->assertTrue($ranges[3][0] === qtype_poasquestion_string::ord('j'));
        $this->assertTrue($ranges[3][1] === qtype_poasquestion_string::ord('j'));
    }

    function test_ranges_binary_search() {
        // Casual search.
        $ranges = array(array(0, 1), array(2, 5), array(11, 13), array(14, 15));
        $this->assertTrue(qtype_preg_unicode::search_number_binary(0, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(1, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(2, $ranges) === 1);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(3, $ranges) === 1);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(4, $ranges) === 1);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(5, $ranges) === 1);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(11, $ranges) === 2);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(12, $ranges) === 2);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(13, $ranges) === 2);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(14, $ranges) === 3);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(15, $ranges) === 3);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(-1, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(6, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(7, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(8, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(9, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(10, $ranges) === false);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(16, $ranges) === false);
        // Empty ranges.
        $ranges = array();
        $this->assertTrue(qtype_preg_unicode::search_number_binary(1, $ranges) === false);
        // Ranges consisting of one character.
        $ranges = array(array(0, 0), array(1, 1), array(3, 3));
        $this->assertTrue(qtype_preg_unicode::search_number_binary(0, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(1, $ranges) === 1);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(3, $ranges) === 2);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(2, $ranges) === false);
        // Only one trivial range.
        $ranges = array(array(0, 10));
        $this->assertTrue(qtype_preg_unicode::search_number_binary(0, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(10, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(11, $ranges) === false);
        // Only one trivial range of one character.
        $ranges = array(array(10, 10));
        $this->assertTrue(qtype_preg_unicode::search_number_binary(10, $ranges) === 0);
        $this->assertTrue(qtype_preg_unicode::search_number_binary(0, $ranges) === false);
    }
}
