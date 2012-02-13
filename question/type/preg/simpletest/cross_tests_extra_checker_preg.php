<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/simpletest/cross_tester.php');

class qtype_preg_cross_tests_extra_checker_preg extends qtype_preg_cross_tests_extra_checker {

    public function engine_name() {
        return 'php_preg_matcher';
    }

}

?>