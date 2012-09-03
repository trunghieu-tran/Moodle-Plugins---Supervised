<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/tests/cross_tester.php');

class qtype_preg_nfa_cross_tester extends qtype_preg_cross_tester {

    public function engine_name() {
        return 'nfa_matcher';
    }
}
