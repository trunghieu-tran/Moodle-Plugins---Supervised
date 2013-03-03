<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_unicode_test extends PHPUnit_Framework_TestCase {

    private $mincode;
    private $maxcode;

    protected function setUp() {
        $this->mincode = qtype_preg_unicode::min_possible_code();
        $this->maxcode = qtype_preg_unicode::max_possible_code();
    }

    function test_huge_intersection() {
        $ranges1 = qtype_preg_unicode::L_ranges();
        $ranges2 = qtype_preg_unicode::Ll_ranges();
        $count = 100;
        $start = time();
        for ($i = 0; $i < $count; $i++) {
            $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        }
        $end = time();
        $time = $end - $start;
        echo "$count intersections took {$time} second(s)\n";
        $this->assertTrue(count($result) == count($ranges2));   // Should get exactly the same ranges.
    }
}
