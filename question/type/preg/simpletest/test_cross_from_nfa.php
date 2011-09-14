<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');
require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');

class test_cross_from_nfa extends UnitTestCase {

    var $question;            // an object of question_preg_qtype
    var $engines = array();   // an array of available engines

    public function __construct() {
        $question = new question_preg_qtype();
        $en = $question->available_engines();
        foreach ($en as $key=>$value) {
            if ($key != 'preg_php_matcher') {
                $this->engines[] = $key;
            }
        }
    }
    
    function check_for_errors($matcher) {
        if ($matcher->is_error_exists()) {
            $errors = $matcher->get_errors();
            foreach ($errors as $error) {
                echo "$error->errormsg<br />";
            }
            return true;
        }
        return false;
    }

    function test_match_subpatterns_nested() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^a((b(c))*)d$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcbcd');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 5);
                if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {                    
                    $this->assertTrue(    $matcher->first_correct_character_index(1) == 1 && $matcher->last_correct_character_index(1) == 4 &&
                                        $matcher->first_correct_character_index(2) == 1 && $matcher->last_correct_character_index(2) == 2);
                } else {
                    echo "subpattern capturing is not supported by $enginename<br />";
                }
                
            }
        }
    }

    function test_match_subpatterns_concatenated() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('(ab)(cd)(ef)');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('_abcdef');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->first_correct_character_index() == 1 && $matcher->last_correct_character_index() == 6);
                if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                    $this->assertTrue(    $matcher->first_correct_character_index(1) == 1 && $matcher->last_correct_character_index(1) == 2 &&
                                        $matcher->first_correct_character_index(2) == 3 && $matcher->last_correct_character_index(2) == 4 &&
                                        $matcher->first_correct_character_index(3) == 5 && $matcher->last_correct_character_index(3) == 6);
                } else {
                    echo "subpattern capturing is not supported by $enginename<br />";
                }
            }
        }
    }

    function test_match_subpatterns_alternated() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('((ab)|(cd)|(efgh))');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcdefgh');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->first_correct_character_index() == 0 && $matcher->last_correct_character_index() == 1);
                if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                    $this->assertTrue($matcher->first_correct_character_index(2) == 0 && $matcher->last_correct_character_index(2) == 1);                    
                } else {
                    echo "subpattern capturing is not supported by $enginename<br />";
                }
            }
        }
    }

    function test_match_questquant() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab?c$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('ac');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 1);
                $res = $matcher->match('abc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
                $res = $matcher->match('abbc');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 1);
            }
        }
    }

    function test_match_negative_charset() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^a[^b]cd$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('abcd');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 0);
                $res = $matcher->match('axcd');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 3);
            }
        }
    }

    function test_match_many_alternatives() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^(?:ab|cd|ef|gh)i$');
            $res = $matcher->match('abi');
            if (!$this->check_for_errors($matcher))
            {
                $this->assertTrue($matcher->is_matching_complete());
                $this->assertTrue($matcher->last_correct_character_index() == 2);
                $res = $matcher->match('cdi');
                $this->assertTrue($matcher->is_matching_complete());
                $this->assertTrue($matcher->last_correct_character_index() == 2);
                $res = $matcher->match('efi');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
                $res = $matcher->match('ghi');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
                $res = $matcher->match('yzi');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
            }
        }
    }

    function test_match_repeated_chars() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('(?:a|b)*abb$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('ab');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 1);
                $res = $matcher->match('abb');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
                $res = $matcher->match('...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 78);
            }
        }
    }

    function test_match_brace_finite() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab{15,35}c$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('abbbbbc');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 5);
                $res = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 26);
                $res = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 35);
            }
        }
    }

    function test_match_brace_infinite() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab{15,}c$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('abbbbbc');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 5);
                $res = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 26);
                $res = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 103);
            }
        }
    }

     function test_match_plus() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab+c$');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('ac');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 0);
                $res = $matcher->match('abc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 2);
                $res = $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 100);
            }
        }
    }

    function test_match_cs() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('aBcD');
            if (!$this->check_for_errors($matcher))
            {
                $res = $matcher->match('abcd');
                $this->assertTrue(!$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 0);
                $matcher = new nfa_preg_matcher('aBcD', 'i');
                $res = $matcher->match('abcd');
                $this->assertTrue($matcher->is_matching_complete() && $matcher->last_correct_character_index() == 3);
            }
        }
    }
}

?>