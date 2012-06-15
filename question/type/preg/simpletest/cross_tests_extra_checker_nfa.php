<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/simpletest/cross_tester.php');

class qtype_preg_cross_tests_extra_checker_nfa extends qtype_preg_cross_tests_extra_checker {

    public function engine_name() {
        return 'nfa_matcher';
    }

}