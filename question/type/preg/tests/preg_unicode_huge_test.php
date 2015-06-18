<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_unicode_huge_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        return new qtype_preg_lexer($pseudofile);
    }

    function test_huge_intersection() {
        $ranges1 = qtype_preg_unicode::L_ranges();
        $ranges2 = qtype_preg_unicode::Ll_ranges();
        $count = 10;
        $start = time();
        for ($i = 0; $i < $count; $i++) {
            $result = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        }
        $time = time() - $start;
        echo "$count intersections took {$time} second(s)\n";
        $this->assertTrue(count($result) == count($ranges2));   // Should get exactly the same ranges.
    }

    function test_matching() {
        $count = 10000;
        $ch = '!';
        // Test ranges intersection and matching.
        $start = time();
        $ranges1 = qtype_preg_unicode::L_ranges();
        $ranges2 = qtype_preg_unicode::Ll_ranges();
        $ranges = qtype_preg_unicode::kinda_operator($ranges1, $ranges2, true, false, false, false);
        for ($i = 0; $i < $count; $i++) {
            qtype_preg_unicode::is_in_range($ch, $ranges);
        }
        $time = time() - $start;
        echo "Unicode-way matching $count times took {$time} second(s)\n";

        $lexer = $this->create_lexer('\pL\pLl');
        $node1 = $lexer->nextToken()->value;
        $node2 = $lexer->nextToken()->value;
        $str = new qtype_poasquestion\string($ch);
        $length = 0;
        $start = time();
        $node = $node1->intersect($node2);
        for ($i = 0; $i < $count; $i++) {
            $node->match($str, 0, $length);
        }
        $time = time() - $start;
        echo "DNF-way matching $count times took {$time} second(s)\n";
    }
}
