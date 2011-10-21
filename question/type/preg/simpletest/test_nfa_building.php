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

    function test_build_concat() {
        $matcher = new nfa_preg_matcher('^abc$');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_concat.jpg');
    }

    function test_build_alt() {
        $matcher = new nfa_preg_matcher('(ab)|c{1,}|^de|f{1,2}|(gh)|i$|');
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

    function test_build_subpatt_concat() {
        $matcher = new nfa_preg_matcher('ab(cd)(ef)g');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_concat.jpg');
    }

    function test_build_subpatt_alt() {
        $matcher = new nfa_preg_matcher('a|(bc)|(de)|f');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_alt.jpg');
    }

    function test_build_subpatt_brace_finite() {
        $matcher = new nfa_preg_matcher('(a){3,6}');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_brace_finite.jpg');
    }

    function test_build_subpatt_brace_infinite() {
        $matcher = new nfa_preg_matcher('(a){3,}');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_brace_infinite.jpg');
    }

    function test_build_subpatt_aster() {
        $matcher = new nfa_preg_matcher('(a*)*');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_aster.jpg');
    }

    function test_build_subpatt_uncounted() {
        $matcher = new nfa_preg_matcher('(?:ab)+');
        $matcher->automaton->draw_nfa('C:/dotfile/dotcode.dot', 'C:/dotfile/test_build_subpatt_uncounted.jpg');
    }

}



?>