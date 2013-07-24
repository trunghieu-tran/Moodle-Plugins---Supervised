<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

class qtype_preg_nodes_inter_asserts_test extends PHPUnit_Framework_TestCase {

    public function test_with_and_without_assert() {
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $assert2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $result = $assert1->intersect_asserts($assert2);
        $resassert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $this->assertEquals($assert1, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_a_and_circumflex() {
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $mergedassert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        $mergedassert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        //$assert2->mergedassertions = array($assert2);

        $result = $assert1->intersect_asserts($assert2);
        $this->assertEquals($assert2, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_z_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        $mergedassert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_Z);
        $mergedassert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_Z);
        

        $result = $assert1->intersect_asserts($assert2);

        $this->assertEquals($assert2, $result, 'Result assert is not equal to expected');
        //$this->assertEquals($assert2->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_circumflex_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $mergedassert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        $mergedassert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        //$assert1->mergedassertions = array($mergedassert2);

        $assertresult = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $assertresult->mergedassertions = array(1=>$assert2);

        $result = $assert1->intersect_asserts($assert2);
        $this->assertEquals($assertresult, $result, 'Result assert is not equal to expected');
        //$this->assertEquals($assertresult->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_esc_b_and_esc_a() {
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_B);
        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        $assertresult = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_B); 
        $assertresult->mergedassertions = array(1=>$assert2);

        $result = $assert1->intersect_asserts($assert2);
        var_dump($assertresult);
        var_dump($result);
        $this->assertEquals($assertresult, $result, 'Result assert is not equal to expected');
    }
}
