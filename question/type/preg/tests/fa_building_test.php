<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');

//$CFG->pathtodot = '/usr/bin/dot';

class qtype_preg_fa_building_test extends PHPUnit_Framework_TestCase {

    protected $dir;

    public function __construct() {
        global $CFG;
        if (!defined('QTYPE_PREG_TEST_CONFIG_PATHTODOT')) {
        } else if ($CFG->pathtodot === '') {
            $CFG->pathtodot = QTYPE_PREG_TEST_CONFIG_PATHTODOT;
        }
        $this->dir = qtype_preg_regex_handler::get_temp_dir('fa');
    }

    function draw($regex, $filename) {
        $matcher = new qtype_preg_fa_matcher($regex);
        if (!$matcher->errors_exist()) {
            $matcher->automaton->fa_to_dot('svg', $this->dir . $filename);
        } else {
            $this->assertTrue(false, "fa building failed\n");
        }
    }

    function test_build_concat() {
        $this->draw('^abc$', 'test_build_concat.svg');
    }

    function test_build_alt() {
        $this->draw('^ab|cd$', 'test_build_alt.svg');
    }

    function test_build_aster() {
        $this->draw('a*', 'test_build_aster.svg');
    }

    function test_build_brace_infinite_1() {
        $this->draw('(?:ab){1,}', 'test_build_brace_infinite_1.svg');
    }

    function test_build_brace_infinite_3() {
        $this->draw('(?:ab){3,}', 'test_build_brace_infinite_3.svg');
    }

    function test_build_plus() {
        $this->draw('a+', 'test_build_plus.svg');
    }

    function test_build_qu() {
        $this->draw('(?:ab)?', 'test_build_qu.svg');
    }

    function test_build_brace_finite_0() {
        $this->draw('(?:ab){0,3}', 'test_build_brace_finite_0.svg');
    }

    function test_build_brace_finite_2() {
        $this->draw('(?:ab){2,3}', 'test_build_brace_finite_2.svg');
    }

    function test_build_brace_finite_4() {
        $this->draw('(?:ab){4}', 'test_build_brace_finite_4.svg');
    }

    function test_build_subexpr() {
        $this->draw('(a|b|cd|)', 'test_build_subexpr.svg');
    }

    function test_build_complex_1() {
        $this->draw('^[-.\w]+@(?:[a-z\d](a|b|cd|)[-a-z\d]+\.)+[a-z]{2,6}$', 'test_build_complex_1.svg');
    }

    function test_build_complex_2() {
        $this->draw('(ab)|c{1,}|^de|f{1,2}|(gh)|i$|', 'test_build_complex_2.svg');
    }

    function test_build_subexpr_concat() {
        $this->draw('ab(cd)(ef)g', 'test_build_subexpr_concat.svg');
    }

    function test_build_subexpr_alt() {
        $this->draw('a|(bc)|(de)|f', 'test_build_subexpr_alt.svg');
    }

    function test_build_subexpr_brace_finite() {
        $this->draw('(a){3,6}', 'test_build_subexpr_brace_finite.svg');
    }

    function test_build_subexpr_brace_finite_with_alt() {
        $this->draw('(a|b|){3,6}', 'test_build_subexpr_brace_finite_with_alt.svg');
    }

    function test_build_subexpr_brace_infinite() {
        $this->draw('(a){3,}', 'test_build_subexpr_brace_infinite.svg');
    }

    function test_build_subexpr_brace_infinite_with_alt() {
        $this->draw('(a|b|){3,}', 'test_build_subexpr_brace_infinite_with_alt.svg');
    }

    function test_build_subexpr_aster() {
        $this->draw('(a*)*', 'test_build_subexpr_aster.svg');
    }

    function test_build_subexpr_uncounted() {
        $this->draw('(?:ab)+', 'test_build_subexpr_uncounted.svg');
    }

    function test_build_cond_subexpr() {
        $this->draw('(a)(?(1)yes|no)', 'test_build_cond_subexpr.svg');
    }
}
