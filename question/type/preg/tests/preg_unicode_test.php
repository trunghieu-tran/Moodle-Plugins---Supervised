<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_unicode_test extends PHPUnit_Framework_TestCase {

    private $mincode;
    private $maxcode;

    protected function setUp() {
        $this->mincode = qtype_preg_unicode::min_possible_code();
        $this->maxcode = qtype_preg_unicode::max_possible_code();
    }

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

    /**
     * Tests kinda_operator() for intersection and intersects()
     */
    function test_ranges_kinda_intersection_and_intersect() {
        // Case 1: the first set includes the second.
        $ranges1 = array(array(1, 10));
        $ranges2 = array(array(2, 6), array(8, 9));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        $this->assertTrue(count($result) === 2);
        $this->assertTrue($result[0][0] === 2);
        $this->assertTrue($result[0][1] === 6);
        $this->assertTrue($result[1][0] === 8);
        $this->assertTrue($result[1][1] === 9);
        $this->assertTrue(qtype_preg_unicode::intersects($ranges1, $ranges2));

        // Case 2: the sets intersect in different ways
        $ranges1 = array(array(0, 10), array(12, 12), array(14, 18), array(24, 30));
        $ranges2 = array(array(2, 4), array(6, 8), array(12, 12), array(16, 20), array(22, 26), array(28, 32));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        $this->assertTrue(count($result) === 6);
        $this->assertTrue($result[0][0] === 2);
        $this->assertTrue($result[0][1] === 4);
        $this->assertTrue($result[1][0] === 6);
        $this->assertTrue($result[1][1] === 8);
        $this->assertTrue($result[2][0] === 12);
        $this->assertTrue($result[2][1] === 12);
        $this->assertTrue($result[3][0] === 16);
        $this->assertTrue($result[3][1] === 18);
        $this->assertTrue($result[4][0] === 24);
        $this->assertTrue($result[4][1] === 26);
        $this->assertTrue($result[5][0] === 28);
        $this->assertTrue($result[5][1] === 30);
        $this->assertTrue(qtype_preg_unicode::intersects($ranges1, $ranges2));

        // Case 3: two same sets.
        $ranges1 = array(array($this->mincode, $this->maxcode));
        $ranges2 = array(array($this->mincode, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === $this->mincode);
        $this->assertTrue($result[0][1] === $this->maxcode);
        $this->assertTrue(qtype_preg_unicode::intersects($ranges1, $ranges2));

        // Case 4: two same sets of the only point.
        $ranges1 = array(array(5, 5));
        $ranges2 = array(array(5, 5));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === 5);
        $this->assertTrue($result[0][1] === 5);
        $this->assertTrue(qtype_preg_unicode::intersects($ranges1, $ranges2));

        // Case 5: empty sets.
        $ranges1 = array();
        $ranges2 = array();
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        $this->assertTrue(count($result) === 0);
        $this->assertFalse(qtype_preg_unicode::intersects($ranges1, $ranges2));
    }

    function test_ranges_kinda_union() {
        // Case 1: the first set includes the second.
        $ranges1 = array(array(1, 10));
        $ranges2 = array(array(2, 6), array(8, 9));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, true, true, false);
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === 1);
        $this->assertTrue($result[0][1] === 10);

        // Case 2: the sets intersect in different ways
        $ranges1 = array(array(0, 10), array(12, 12), array(14, 18), array(24, 30));
        $ranges2 = array(array(2, 4), array(6, 8), array(12, 12), array(16, 20), array(22, 26), array(28, 32));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, true, true, false);
        $this->assertTrue(count($result) === 4);
        $this->assertTrue($result[0][0] === 0);
        $this->assertTrue($result[0][1] === 10);
        $this->assertTrue($result[1][0] === 12);
        $this->assertTrue($result[1][1] === 12);
        $this->assertTrue($result[2][0] === 14);
        $this->assertTrue($result[2][1] === 20);
        $this->assertTrue($result[3][0] === 22);
        $this->assertTrue($result[3][1] === 32);

        // Case 3: two same sets.
        $ranges1 = array(array($this->mincode, $this->maxcode));
        $ranges2 = array(array($this->mincode, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, true, true, false);
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === $this->mincode);
        $this->assertTrue($result[0][1] === $this->maxcode);

        // Case 4: two same sets of the only point.
        $ranges1 = array(array(5, 5));
        $ranges2 = array(array(5, 5));
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, true, true, false);
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result[0][0] === 5);
        $this->assertTrue($result[0][1] === 5);

        // Case 5: empty sets.
        $ranges1 = array();
        $ranges2 = array();
        $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, true, true, false);
        $this->assertTrue(count($result) === 0);
    }

    function test_ranges_kinda_negation() {
        $tmp = array(array($this->mincode, $this->maxcode));
        // Empty array.
        $ranges = array();
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array()); // Can't deal with empty sets.
        // One big range from 0 to maxcode.
        $ranges = array(array(0, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array());
        // Only 0.
        $ranges = array(array(0, 0));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(1, $this->maxcode)));
        // Only maxcode.
        $ranges = array(array($this->maxcode, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(0, $this->maxcode - 1)));
        // Only one number.
        $ranges = array(array(4, 4));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(0, 3), array(5, $this->maxcode)));
        // Range from 1 to $this->maxcode - 1
        $ranges = array(array(1, $this->maxcode - 1));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(0, 0), array($this->maxcode, $this->maxcode)));
        // Few ranges starting from 0.
        $ranges = array(array(0, 3), array(5, 5), array(9, 15));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(4, 4), array(6, 8), array(16, $this->maxcode)));
        // Few ranges ending with maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(0, 2), array(4, 6), array(8, 8)));
        // Few ranges starting from 0 and ending with maxcode.
        $ranges = array(array(0, 3), array(7, 7), array(9, 9), array(11, 11), array(14, 20), array(23, $this->maxcode));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(4, 6), array(8, 8), array(10, 10), array(12, 13), array(21, 22)));
        // Few ranges not including 0 and maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, 18));
        $result = qtype_preg_unicode::kinda_operator($ranges, $tmp, false, false, true, false);
        $this->assertTrue($result === array(array(0, 2), array(4, 6), array(8, 8), array(19, $this->maxcode)));
    }

    function test_get_ranges_from_charset() {
        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion\string('a'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[0][1] === core_text::utf8ord('a'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion\string('ab'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[0][1] === core_text::utf8ord('b'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion\string('abc'));
        $this->assertTrue(count($ranges) === 1);
        $this->assertTrue($ranges[0][0] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[0][1] === core_text::utf8ord('c'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion\string('abde'));
        $this->assertTrue(count($ranges) === 2);
        $this->assertTrue($ranges[0][0] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[0][1] === core_text::utf8ord('b'));
        $this->assertTrue($ranges[1][0] === core_text::utf8ord('d'));
        $this->assertTrue($ranges[1][1] === core_text::utf8ord('e'));

        $ranges = qtype_preg_unicode::get_ranges_from_charset(new qtype_poasquestion\string('acdfghj'));
        $this->assertTrue(count($ranges) === 4);
        $this->assertTrue($ranges[0][0] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[0][1] === core_text::utf8ord('a'));
        $this->assertTrue($ranges[1][0] === core_text::utf8ord('c'));
        $this->assertTrue($ranges[1][1] === core_text::utf8ord('d'));
        $this->assertTrue($ranges[2][0] === core_text::utf8ord('f'));
        $this->assertTrue($ranges[2][1] === core_text::utf8ord('h'));
        $this->assertTrue($ranges[3][0] === core_text::utf8ord('j'));
        $this->assertTrue($ranges[3][1] === core_text::utf8ord('j'));
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
        // Empty array.
        $ranges = array();
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === qtype_preg_unicode::dot_ranges());
        // One big range from 0 to maxcode.
        $ranges = array(array(0, $this->maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array());
        // Only 0.
        $ranges = array(array(0, 0));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(1, $this->maxcode)));
        // Only maxcode.
        $ranges = array(array($this->maxcode, $this->maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, $this->maxcode - 1)));
        // Only one number.
        $ranges = array(array(4, 4));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 3), array(5, $this->maxcode)));
        // Range from 1 to $this->maxcode - 1
        $ranges = array(array(1, $this->maxcode - 1));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 0), array($this->maxcode, $this->maxcode)));
        // Few ranges starting from 0.
        $ranges = array(array(0, 3), array(5, 5), array(9, 15));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(4, 4), array(6, 8), array(16, $this->maxcode)));
        // Few ranges ending with maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, $this->maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 2), array(4, 6), array(8, 8)));
        // Few ranges starting from 0 and ending with maxcode.
        $ranges = array(array(0, 3), array(7, 7), array(9, 9), array(11, 11), array(14, 20), array(23, $this->maxcode));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(4, 6), array(8, 8), array(10, 10), array(12, 13), array(21, 22)));
        // Few ranges not including 0 and maxcode.
        $ranges = array(array(3, 3), array(7, 7), array(9, 18));
        $this->assertTrue(qtype_preg_unicode::negate_ranges($ranges) === array(array(0, 2), array(4, 6), array(8, 8), array(19, $this->maxcode)));
    }
}
