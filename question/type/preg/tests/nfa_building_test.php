<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');

class qtype_preg_nfa_building_test extends PHPUnit_Framework_TestCase {

    protected $dir;

    public function __construct() {
        global $CFG;
        if (!defined('QTYPE_PREG_TEST_CONFIG_PATHTODOT')) {
        } else if ($CFG->pathtodot === '') {
            $CFG->pathtodot = QTYPE_PREG_TEST_CONFIG_PATHTODOT;
        }
        $this->dir = qtype_preg_regex_handler::get_temp_dir('nfa');
    }

    function draw($regex, $filename) {
        $matcher = new qtype_preg_nfa_matcher($regex);
        if (!$matcher->is_error_exists()) {
            $matcher->automaton->draw('png', $this->dir . $filename);
        } else {
            $this->assertTrue(false, "nfa building failed <br/>");
        }
    }

    function test_build_concat() {
        $this->draw('^abc$', 'test_build_concat.png');
    }

    function test_build_alt() {
        $this->draw('^ab|cd$', 'test_build_alt.png');
    }

    function test_build_aster() {
        $this->draw('a*', 'test_build_aster.png');
    }

    function test_build_brace_infinite_1() {
        $this->draw('(?:ab){1,}', 'test_build_brace_infinite_1.png');
    }

    function test_build_brace_infinite_3() {
        $this->draw('(?:ab){3,}', 'test_build_brace_infinite_3.png');
    }

    function test_build_plus() {
        $this->draw('a+', 'test_build_plus.png');
    }

    function test_build_qu() {
        $this->draw('(?:ab)?', 'test_build_qu.png');
    }

    function test_build_brace_finite_0() {
        $this->draw('(?:ab){0,3}', 'test_build_brace_finite_0.png');
    }

    function test_build_brace_finite_2() {
        $this->draw('(?:ab){2,3}', 'test_build_brace_finite_2.png');
    }

    function test_build_brace_finite_4() {
        $this->draw('(?:ab){4}', 'test_build_brace_finite_4.png');
    }

    function test_build_subpatt() {
        $this->draw('(a|b|cd|)', 'test_build_subpatt.png');
    }

    function test_build_complex_1() {
        $this->draw('^[-.\w]+@(?:[a-z\d](a|b|cd|)[-a-z\d]+\.)+[a-z]{2,6}$', 'test_build_complex_1.png');
    }

    function test_build_complex_2() {
        $this->draw('(ab)|c{1,}|^de|f{1,2}|(gh)|i$|', 'test_build_complex_2.png');
    }

    function test_build_subpatt_concat() {
        $this->draw('ab(cd)(ef)g', 'test_build_subpatt_concat.png');
    }

    function test_build_subpatt_alt() {
        $this->draw('a|(bc)|(de)|f', 'test_build_subpatt_alt.png');
    }

    function test_build_subpatt_brace_finite() {
        $this->draw('(a){3,6}', 'test_build_subpatt_brace_finite.png');
    }

    function test_build_subpatt_brace_infinite() {
        $this->draw('(a){3,}', 'test_build_subpatt_brace_infinite.png');
    }

    function test_build_subpatt_aster() {
        $this->draw('(a*)*', 'test_build_subpatt_aster.png');
    }

    function test_build_subpatt_uncounted() {
        $this->draw('(?:ab)+', 'test_build_subpatt_uncounted.png');
    }
}
