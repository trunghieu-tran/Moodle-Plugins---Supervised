<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');
//see carefully commented example of test on lines 617-644
class qtype_preg_fa_draw_test extends UnitTestCase {
	protected $matcher;
	function setUp() {
		$this->matcher  = new qtype_preg_nondeterministic_fa;
	}
	
	function test_simple() {//[asdf]
		$this->matcher->input_fa('0->asdf->1;');
		$this->matcher->draw('simple.dot', 'simple.jpg');
	}
	function test_complex() {//(?:a|b)*abb
		$this->matcher->input_fa('0->a->1;0->b->0;1->a->1;1->b->2;2->b->3;2->a->1;3->a->1;3->b->0;');
		$this->matcher->draw('complex.dot', 'complex.jpg');
	}
	function test_subpattern() {//(a)(bc)
		$this->matcher->input_fa('0->#s1e1#a->1;1->#s2#b->2;2->#e2#c->3;');
		$this->matcher->draw('subpattern.dot', 'subpattern.jpg');
	}
}

?>