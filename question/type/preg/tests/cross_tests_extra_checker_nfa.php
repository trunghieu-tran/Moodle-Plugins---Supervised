<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/tests/cross_tester.php');

class qtype_preg_cross_tests_extra_checker_nfa extends qtype_preg_cross_tests_extra_checker {

    public function engine_name() {
        return 'nfa_matcher';
    }
}
