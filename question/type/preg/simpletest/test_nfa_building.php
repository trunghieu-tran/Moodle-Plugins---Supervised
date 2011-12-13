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

    function test_transition_cloning() {
        $leaf = new preg_leaf_charset();
        $state = new nfa_state();
        $tr = new nfa_transition($leaf, $state, false);
        $clone = clone $tr;
        $this->assertFalse($tr === $clone);
        $this->assertFalse($tr->pregleaf === $clone->pregleaf);
        $this->assertTrue($tr->state === $clone->state);
        $tr->subpatt_start[] = 4;
        $this->assertFalse($tr->subpatt_start == $clone->subpatt_start);
    }

    function test_build_concat() {
        $matcher = new nfa_preg_matcher('^abc$');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_concat.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_alt() {
        $matcher = new nfa_preg_matcher('(ab)|c{1,}|^de|f{1,2}|(gh)|i$|');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_alt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_qu() {
        $matcher = new nfa_preg_matcher('a?');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_qu.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_aster() {
        $matcher = new nfa_preg_matcher('a*');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_aster.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_plus() {
        $matcher = new nfa_preg_matcher('a+');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_plus.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_brace_infinite() {
        $matcher = new nfa_preg_matcher('a{3,}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_brace_infinite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_brace_finite() {
        $matcher = new nfa_preg_matcher('a{3,6}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_brace_finite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt() {
        $matcher = new nfa_preg_matcher('(a|b|cd|)');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_complex() {
        $matcher = new nfa_preg_matcher('^[-.\w]+@(?:[a-z\d](a|b|cd|)[-a-z\d]+\.)+[a-z]{2,6}$');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_complex.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_concat() {
        $matcher = new nfa_preg_matcher('ab(cd)(ef)g');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_concat.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_alt() {
        $matcher = new nfa_preg_matcher('a|(bc)|(de)|f');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_alt.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_brace_finite() {
        $matcher = new nfa_preg_matcher('(a){3,6}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_brace_finite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_brace_infinite() {
        $matcher = new nfa_preg_matcher('(a){3,}');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_brace_infinite.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_aster() {
        $matcher = new nfa_preg_matcher('(a*)*');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_aster.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_subpatt_uncounted() {
        $matcher = new nfa_preg_matcher('(?:ab)+');
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw_nfa('dotcode.dot', 'test_build_subpatt_uncounted.jpg');
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

}



?>