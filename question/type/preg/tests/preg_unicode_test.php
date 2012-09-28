<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_unicode_test extends PHPUnit_Framework_TestCase {

    function test_next_part_and_reduce_range() {
        // Case 1: 2nd range starts in the middle of the 1st one, and the 1st ends in the middle of the 2nd.
        $range1 = array(0, 10);
        $range2 = array(5, 15);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 0);
        $this->assertTrue($result[1] === 4);
        $this->assertTrue($range1[0] === 5);
        $this->assertTrue($range2[0] === 5);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 5);
        $this->assertTrue($result[1] === 10);
        $this->assertTrue($range1 === null);
        $this->assertTrue($range2[0] === 11);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 11);
        $this->assertTrue($result[1] === 15);
        $this->assertTrue($range1 === null);
        $this->assertTrue($range2 === null);

        // Case 2: the 2nd range starts before the 1st one, but both end at the same point.
        $range1 = array(5, 10);
        $range2 = array(0, 10);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 0);
        $this->assertTrue($result[1] === 4);
        $this->assertTrue($range1[0] === 5);
        $this->assertTrue($range2[0] === 5);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 5);
        $this->assertTrue($result[1] === 10);
        $this->assertTrue($range1 === null);
        $this->assertTrue($range2 === null);

        // Case 3: 2 identical ranges.
        $range1 = array(5, 10);
        $range2 = array(5, 10);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 5);
        $this->assertTrue($result[1] === 10);
        $this->assertTrue($range1 === null);
        $this->assertTrue($range2 === null);

        // Case 4: 2 identical ranges of the only point.
        $range1 = array(5, 5);
        $range2 = array(5, 5);

        $result = qtype_preg_unicode::next_part($range1, $range2);
        qtype_preg_unicode::reduce_range($range1, $result);
        qtype_preg_unicode::reduce_range($range2, $result);
        $this->assertTrue($result[0] === 5);
        $this->assertTrue($result[1] === 5);
        $this->assertTrue($range1 === null);
        $this->assertTrue($range2 === null);
    }

    function test_intersect_positive_ranges() {
        $ranges1 = array(array(1, 10));
        $ranges2 = array(array(2, 7), array(8, 9));
        //$result = qtype_preg_unicode::intersect_ranges($ranges1, $ranges2);
        //$result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false, false, false);
        //$this->assertTrue(count($result) === 2);
       /* $this->assertTrue($result[0][0] === 3);
        $this->assertTrue($result[0][1] === 7);
        $this->assertTrue($result[1][0] === 8);
        $this->assertTrue($result[1][1] === 9);*/
    }

    /*function test_intersect_negative_ranges() {
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
    }*/

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

    function test_ranges_negation() {
        $maxcode = qtype_preg_unicode::max_possible_code();
        // Empty array.
        $ranges = array();
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, $maxcode)));
        // One big range from 0 to maxcode.
        $ranges = array(array(0, $maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array());
        // Only 0.
        $ranges = array(array(0, 0));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(1, $maxcode)));
        // Only maxcode.
        $ranges = array(array($maxcode, $maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, $maxcode - 1)));
        // Only one number.
        $ranges = array(array(4, 4));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 3), array(5, $maxcode)));
        // Range from 1 to $maxcode - 1
        $ranges = array(array(1, $maxcode - 1));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 0), array($maxcode, $maxcode)));
        // Few ranges starting from 0.
        $ranges = array(array(0, 3), array(5, 5), array(9, 15));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(4, 4), array(6, 8), array(16, $maxcode)));
        // Few ranges ending with maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, $maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 2), array(4, 6), array(8, 8)));
        // Few ranges starting from 0 and ending with maxcode.
        $ranges = array(array(0, 3), array(7, 7), array(9, 9), array(11, 11), array(14, 20), array(23, $maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(4, 6), array(8, 8), array(10, 10), array(12, 13), array(21, 22)));
        // Few ranges not including 0 and maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, 18));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 2), array(4, 6), array(8, 8), array(19, $maxcode)));
    }
}
