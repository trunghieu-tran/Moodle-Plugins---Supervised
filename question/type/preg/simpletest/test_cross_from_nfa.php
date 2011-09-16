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
                echo "$error<br />";
            }
            return true;
        }
        return false;
    }
    
    function compare_expected_with_obtained($matcher, $expected, $obtained) {
        $this->assertTrue($expected['is_match'] == $obtained['is_match']);
        $this->assertTrue($expected['full'] == $obtained['full']);
        if ($obtained['is_match'] || $expected['is_match']) {
            if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                $this->assertTrue($expected['index_first'] == $obtained['index_first']);
            } else {
                $this->assertTrue($expected['index_first'][0] == $obtained['index_first'][0]);
            }
            if ($matcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
                $this->assertTrue($expected['index_last'] == $obtained['index_last']);
            } else {
                $this->assertTrue($expected['index_last'][0] == $obtained['index_last'][0]);
            }
            if ($matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                //$this->assertTrue(($expected['next'] == 0 && $obtained['next'] == 0) || strstr($expected['next'], $obtained['next']) != false);        // expected 'next' contains obtained 'next' 
            }
            if ($matcher->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                //$this->assertTrue($expected['left'] == $obtained['left']);
            }
        }
    }

    function test_match_subpatterns_nested() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^a((b(c))*)d$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcbcd');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0,1=>1,2=>1,3=>2), 'index_last'=>array(0=>5,1=>4,2=>2,3=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_subpatterns_concatenated() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('(ab)(cd)(ef)');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('_abcdef');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>1,1=>1,2=>3,3=>5), 'index_last'=>array(0=>6,1=>2,2=>4,3=>6), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_subpatterns_alternated() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('((ab)|(cd)|(efgh))');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcdefgh');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0,1=>0,2=>0), 'index_last'=>array(0=>1,1=>1,2=>1), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_questquant() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab?c$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('ac');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>1), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbc');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>1), 'left'=>1, 'next'=>'c');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_negative_charset() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^a[^b]cd$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcd');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>0), 'left'=>3, 'next'=>'acdefghijklmnopqrstuvwxyz0123456789!?.,');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('axcd');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>3), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_many_alternatives() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^(?:ab|cd|ef|gh)i$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abi');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('cdi');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('efi');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('ghi');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('yzi');
                $expected = array('is_match'=>false, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>0), 'left'=>3, 'next'=>'aceg');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_repeated_chars() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('(?:a|b)*abb$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('ab');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>1), 'left'=>1, 'next'=>'b');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abb');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('...ababababababababababbabababaabbbbbbbbbbbbaaaaaaaaaaaaabbbbbbbbbababababababb');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>3), 'index_last'=>array(0=>78), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_brace_finite() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab{15,35}c$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abbbbbc');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>5), 'left'=>11, 'next'=>'b');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>26), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>35), 'left'=>1, 'next'=>'c');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_brace_infinite() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab{15,}c$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abbbbbc');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>5), 'left'=>11, 'next'=>'b');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>26), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>103), 'left'=>0, 'next'=>null);
            }
        }
    }

     function test_match_plus() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('^ab+c$');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('ac');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>0), 'left'=>2, 'next'=>'b');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>2), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher->match('abbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbc');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>100), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }

    function test_match_cs() {
        foreach ($this->engines as $enginename) {
            $matcher = new $enginename('aBcD');
            if (!$this->check_for_errors($matcher))
            {
                $matcher->match('abcd');
                $expected = array('is_match'=>true, 'full'=>false, 'index_first'=>array(0=>0), 'index_last'=>array(0=>0), 'left'=>3, 'next'=>'B');
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
                $matcher = new nfa_preg_matcher('aBcD', 'i');
                $matcher->match('abcd');
                $expected = array('is_match'=>true, 'full'=>true, 'index_first'=>array(0=>0), 'index_last'=>array(0=>3), 'left'=>0, 'next'=>null);
                $this->compare_expected_with_obtained($matcher, $expected, $matcher->get_match_results());
            }
        }
    }
}

?>