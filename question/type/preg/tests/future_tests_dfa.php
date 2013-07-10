<?php

/**
 * Unit tests for DFA matcher methods.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher/dfa_preg_matcher.php');

class qtype_preg_dfa_future_test extends PHPUnit_Framework_TestCase {
    var $qtype;

    function setUp() {
    }

    function tearDown() {
    }

    function test_assert_nesting() {
        $matcher = new qtype_preg_dfa_preg_matcher('g(?=[bcd]*(?=[cd]*b)a)[abcd]*');
        $matcher->match('gccbadcdcd');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length()==10);
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1)=='');
        $matcher->match('gccabdcdcd');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length()==10);
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1)=='');
        $matcher->match('gccaddcdcd');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length()==10);
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1)=='b');
    }
    function test_next_character() {
        $matcher = new qtype_preg_dfa_preg_matcher('a(?=[%asd])\W');
        $matcher->match('aa');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length()==0);
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1)=='%');
    }
}

