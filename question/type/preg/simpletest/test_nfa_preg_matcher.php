<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/simpletest/cross_tester.php');

class qtype_preg_nfa_cross_tester extends qtype_preg_cross_tester {

    public function engine_name() {
        return 'nfa_preg_matcher';
    }

}

?>