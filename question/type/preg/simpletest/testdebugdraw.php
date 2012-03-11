<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');
//see carefully commented example of test on lines 617-644
class qtype_preg_fa_draw_test extends UnitTestCase {
	protected $matcher;
	function setUp() {
		$this->matcher  = new qtype_preg_nfa_matcher;
	}
	
	function test_simple() {
		
	}
}

?>