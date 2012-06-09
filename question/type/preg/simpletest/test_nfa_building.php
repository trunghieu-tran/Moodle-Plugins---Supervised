<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

class qtype_preg_nfa_building_test extends UnitTestCase {

    function setUp() {
    }

    function tearDown() {
    }

    /*function test_transition_cloning() {
        $leaf = new preg_leaf_charset();
        $state = new qtype_preg_fa_state();
        $tr = new qtype_preg_nfa_transition($leaf, $state, false);
        $clone = clone $tr;
        $this->assertFalse($tr === $clone);
        $this->assertFalse($tr->pregleaf === $clone->pregleaf);
        $this->assertTrue($tr->state === $clone->state);
        $tr->subpatt_start[] = 4;
        $this->assertFalse($tr->subpatt_start == $clone->subpatt_start);
    }*/

    function test_build_concat() {
        $matcher = new qtype_preg_nfa_matcher('^abc$');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_concat.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_alt() {
        $matcher = new qtype_preg_nfa_matcher('^ab|cd$');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_alt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_aster() {
        $matcher = new qtype_preg_nfa_matcher('a*');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_aster.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_brace_infinite_1() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab){1,}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_brace_infinite_1.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }
	
	function test_build_brace_infinite_3() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab){3,}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_brace_infinite_3.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_plus() {
        $matcher = new qtype_preg_nfa_matcher('a+');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_plus.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_qu() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab)?');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_qu.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_brace_finite_0() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab){0,3}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_brace_finite_0.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }
	
	function test_build_brace_finite_2() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab){2,3}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_brace_finite_2.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }
	
	function test_build_brace_finite_4() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab){4}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_brace_finite_4.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt() {
        $matcher = new qtype_preg_nfa_matcher('(a|b|cd|)');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_complex_1() {
        $matcher = new qtype_preg_nfa_matcher('^[-.\w]+@(?:[a-z\d](a|b|cd|)[-a-z\d]+\.)+[a-z]{2,6}$');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_complex_1.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_complex_2() {
        $matcher = new qtype_preg_nfa_matcher('(ab)|c{1,}|^de|f{1,2}|(gh)|i$|');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_complex_2.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_concat() {
        $matcher = new qtype_preg_nfa_matcher('ab(cd)(ef)g');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_concat.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_alt() {
        $matcher = new qtype_preg_nfa_matcher('a|(bc)|(de)|f');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_alt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_brace_finite() {
        $matcher = new qtype_preg_nfa_matcher('(a){3,6}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_brace_finite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_brace_infinite() {
        $matcher = new qtype_preg_nfa_matcher('(a){3,}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_brace_infinite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_aster() {
        $matcher = new qtype_preg_nfa_matcher('(a*)*');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_aster.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_uncounted() {
        $matcher = new qtype_preg_nfa_matcher('(?:ab)+');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('dotcode.dot', 'test_build_subpatt_uncounted.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

}



?>