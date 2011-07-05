<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');

class nfa_building_test extends UnitTestCase {

    
    function setUp() {
    }
    
    function tearDown() {
    }
	
/****************************************************************************************************************************************************************/
/*                                                                                                                                                              */
/*                                                                tests for nfa_state::merge()                                                                  */
/*                                                                                                                                                              */
/****************************************************************************************************************************************************************/

	// both states are empty
    function test_merge_states_1() {
		$st0 = new nfa_state;
		$st1 = new nfa_state;
		$st2 = new nfa_state;
		$st0->merge(&$st1);
		$this->assertTrue($st0->is_equal(&$st2));
    }
	
	// the first state is empty, the second is not
    function test_merge_states_2() {
		$st0 = new nfa_state;
		$st1 = new nfa_state;
		$st2 = new nfa_state;
		$st3 = new nfa_state;
		
		$tr_o = new preg_leaf_charset;	$tr_o->charset = 'o';
		$tr_a = new preg_leaf_charset;	$tr_a->charset = 'a';
		$tr_eps = new preg_leaf_meta;	$tr_eps->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		
		$st1->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st1->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st1->append_transition(new nfa_transition(&$tr_eps, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_eps, &$st3));
		
		$st0->merge(&$st1);
		$this->assertTrue($st0->is_equal(&$st2));
    }
	
	// the first is not empty, the second is
    function test_merge_states_3() {
		$st0 = new nfa_state;
		$st1 = new nfa_state;
		$st2 = new nfa_state;
		$st3 = new nfa_state;
		
		$tr_o = new preg_leaf_charset;	$tr_o->charset = 'o';
		$tr_a = new preg_leaf_charset;	$tr_a->charset = 'a';
		$tr_eps = new preg_leaf_meta;	$tr_eps->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$tr_any = new preg_leaf_meta;	$tr_any->subtype = preg_leaf_meta::SUBTYPE_DOT;
		
		$st0->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_eps, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_eps, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_any, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_any, &$st3));
		
		$st0->merge(&$st1);
		$this->assertTrue($st0->is_equal(&$st2));
    }
	
	// both states have transitions
    function test_merge_states_4() {
		$st0 = new nfa_state;
		$st1 = new nfa_state;
		$st2 = new nfa_state;
		$st3 = new nfa_state;
		
		$tr_o = new preg_leaf_charset;		$tr_o->charset = 'o';
		$tr_a = new preg_leaf_charset;		$tr_a->charset = 'a';		
		$tr_eps = new preg_leaf_meta;		$tr_eps->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$tr_any = new preg_leaf_meta;		$tr_any->subtype = preg_leaf_meta::SUBTYPE_DOT;
		$tr_asrt = new preg_leaf_assert;	$tr_asrt->subtype = preg_leaf_assert::SUBTYPE_DOLLAR;
		$tr_h = new preg_leaf_charset;		$tr_a->charset = 'h';
		$tr_AZ = new preg_leaf_charset;		$tr_a->charset = 'A-Z';
		
		$st0->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_o, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_a, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_eps, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_eps, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_any, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_any, &$st3));
		$st0->append_transition(new nfa_transition(&$tr_asrt, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_asrt, &$st3));
		$st1->append_transition(new nfa_transition(&$tr_any, &$st3));
		$st1->append_transition(new nfa_transition(&$tr_h, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_h, &$st3));
		$st1->append_transition(new nfa_transition(&$tr_AZ, &$st3));
		$st2->append_transition(new nfa_transition(&$tr_AZ, &$st3));
		
		
		$st0->merge(&$st1);
		$this->assertTrue($st0->is_equal(&$st2));
    }
	
	// both states have transitions, two of them are identical
    function test_merge_states_5() {
		$st0 = new nfa_state;
		$st1 = new nfa_state;
		$st2 = new nfa_state;
		$st3 = new nfa_state;
		
		$to0 = new nfa_state;
		$to1 = new nfa_state;
		$to2 = new nfa_state;
		$to3 = new nfa_state;
		$to4 = new nfa_state;
		
		$tr_o = new preg_leaf_charset;	$tr_o->charset = 'o';
		$tr_a = new preg_leaf_charset;	$tr_a->charset = 'a';
		$tr_az = new preg_leaf_charset;	$tr_az->charset = 'a-z';
		$tr_eps = new preg_leaf_meta;	$tr_eps->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$tr_c = new preg_leaf_charset;	$tr_c->charset = 'c';
		$tr_d = new preg_leaf_charset;	$tr_d->charset = 'd';
		$tr_e = new preg_leaf_charset;	$tr_e->charset = 'e';
		
		$st0->append_transition(new nfa_transition(&$tr_a, &$to0));
		$st2->append_transition(new nfa_transition(&$tr_a, &$to0));
		$st0->append_transition(new nfa_transition(&$tr_az, &$to1));
		$st2->append_transition(new nfa_transition(&$tr_az, &$to1));
		$st0->append_transition(new nfa_transition(&$tr_eps, &$to2));
		$st2->append_transition(new nfa_transition(&$tr_eps, &$to2));
		$st1->append_transition(new nfa_transition(&$tr_a, &$to0));
		$st0->append_transition(new nfa_transition(&$tr_c, &$to2));
		$st2->append_transition(new nfa_transition(&$tr_c, &$to2));
		$st1->append_transition(new nfa_transition(&$tr_d, &$to3));
		$st2->append_transition(new nfa_transition(&$tr_d, &$to3));
		$st1->append_transition(new nfa_transition(&$tr_e, &$to4));
		$st2->append_transition(new nfa_transition(&$tr_e, &$to4));
		
		$st0->merge(&$st1);
		$this->assertTrue($st0->is_equal(&$st2));
    }
	
/****************************************************************************************************************************************************************/
/*                                                                                                                                                              */
/*                               tests for nfa_preg_node::create_automaton(). check automata building by looking at resulting jpg files!                        */
/*                                                                                                                                                              */
/****************************************************************************************************************************************************************/
	
	function test_build_concat() {
		$matcher = new nfa_preg_matcher('^abc$');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_concat.jpg');
	}
	
	function test_build_alt() {
		$matcher = new nfa_preg_matcher('^ab|cd$|');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_alt.jpg');
	}
	
	function test_build_qu() {
		$matcher = new nfa_preg_matcher('a?');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_qu.jpg');
	}
	
	function test_build_aster() {
		$matcher = new nfa_preg_matcher('a*');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_aster.jpg');
	}
	
	function test_build_plus() {
		$matcher = new nfa_preg_matcher('a+');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_plus.jpg');
	}
	
	function test_build_brace_infinite() {
		$matcher = new nfa_preg_matcher('a{3,}');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_brace_infinite.jpg');
	}
	
	function test_build_brace_finite() {
		$matcher = new nfa_preg_matcher('a{3,6}');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_brace_finite.jpg');
	}
	
	function test_build_subpatt() {
		$matcher = new nfa_preg_matcher('(a|b|cd|)');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt.jpg');
	}
	
	function test_build_complex() {
		$matcher = new nfa_preg_matcher('^[-.\w]+@(?:[a-z\d](a|b|cd|)[-a-z\d]+\.)+[a-z]{2,6}$');		
		$matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_complex.jpg');
	}

}



?>