<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_nodes.php');

class qtype_preg_fa_comparison_test extends PHPUnit_Framework_TestCase {

    public function test_equiv_dfas() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m05-8]"];
                                1->3[label="[0-9a-h]"];
                                2->3[label="[0-9a-h]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-dh-m0-35-8]"];
                                1->3[label="[a-h0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_dfas_with_direct_loop() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m05-8]"];
                                1->2[label="[z]"];
                                2->1[label="[z]"];
                                1->3[label="[0-9a-h]"];
                                2->3[label="[0-9a-h]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-dh-m0-35-8]"];
                                1->1[label="[z]"];
                                1->3[label="[a-h0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_dfas_with_indirect_loop() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m05-8]"];
                                1->3[label="[0-9a-h]"];
                                2->3[label="[0-9a-h]"];
                                3->0[label="[z]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-dh-m0-35-8]"];
                                1->3[label="[a-h0-9]"];
                                3->0[label="[z]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_dfa_and_nfa_without_empty_transition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->12[label="[a-h]"];
                                0->2[label="[i-z]"];
                                12->2[label="[e-s]"];
                                12->3[label="[a-d0-9]"];
                                2->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-h]"];
                                0->2[label="[a-z]"];
                                1->2[label="[e-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_dfa_and_nfa_with_empty_transition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->12[label="[a-h]"];
                                0->2[label="[i-z]"];
                                12->2[label="[e-s]"];
                                12->3[label="[a-d0-9]"];
                                2->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-h]"];
                                0->2[label="[i-z]"];
                                1->2[label="[e-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                1->2[label="[]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_dfa_and_nfa_with_direct_loop() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->2[label="[0-5]"];
                                2->3[label="[3-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;23;
                                0->2[label="[abi-z]"];
                                0->12[label="[c-h]"];
                                2->2[label="[0-2]"];
                                2->23[label="[3-5]"];
                                2->3[label="[6-9]"];
                                12->2[label="[0-2]"];
                                12->23[label="[3-5]"];
                                12->3[label="[6-9c-h]"];
                                23->23[label="[3-5]"];
                                23->2[label="[0-2]"];
                                23->3[label="[6-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_nfas() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-h]"];
                                0->2[label="[a-z]"];
                                1->2[label="[e-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-h]"];
                                0->2[label="[i-z]"];
                                1->2[label="[e-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                1->2[label="[]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_nfas_with_direct_loop() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->2[label="[0-5]"];
                                2->3[label="[3-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->4[label="[0-5]"];
                                4->2[label="[]"];
                                2->3[label="[3-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_nfas_with_indirect_loop() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->3[label="[3-9]"];
                                3->1[label="[k-o]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->3[label="[3-9]"];
                                3->1[label="[k-o]"];
                                3->4[label="[]"];
                                4->3[label="[]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m05-8]"];
                                1->3[label="[0-9]"];
                                2->3[label="[a-h]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m0]"];
                                0->3[label="[5-8]"];
                                1->3[label="[0-9]"];
                                2->3[label="[a-h]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m05-8]"];
                                1->3[label="[0-9]"];
                                2->3[label="[a-h]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                0->2[label="[h-m0]"];
                                1->3[label="[0-9]"];
                                2->3[label="[a-h]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_direct_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                1->3[label="[a-h0-9]"];
                                1->1[label="[z]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                2;4;
                                0->1[label="[a-d1-3]"];
                                1->2[label="[a-h0-9]"];
                                1->3[label="[z]"];
                                3->4[label="[z]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_direct_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                1->3[label="[a-h0-9]"];
                                1->1[label="[z]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-d1-3]"];
                                1->3[label="[a-h0-9]"];
                                1->2[label="[z]"];
                                2->4[label="[z]"];
                                4->2[label="[z]"];
                                2->3[label="[a-h0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_indirect_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-d]"];
                                1->2[label="[1-3]"];
                                1->3[label="[h-m]"];
                                2->0[label="[a-h]"];
                                2->4[label="[i-n]"];
                                3->4[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-d]"];
                                1->2[label="[1-3]"];
                                1->3[label="[h-m]"];
                                2->0[label="[i-n]"];
                                2->4[label="[a-h]"];
                                3->4[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfas_with_indirect_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-d]"];
                                1->2[label="[1-3]"];
                                1->3[label="[h-m]"];
                                2->4[label="[a-h]"];
                                3->4[label="[0-9]"];
                                4->0[label="[i-n]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-d]"];
                                1->2[label="[1-3]"];
                                1->3[label="[h-m]"];
                                2->4[label="[a-h]"];
                                3->4[label="[0-9]"];
                                4->1[label="[i-n]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfa_and_nfa_with_empty_transition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->12[label="[a-h]"];
                                0->2[label="[i-z]"];
                                12->23[label="[a-d]"];
                                12->3[label="[e-s]"];
                                2->3[label="[0-9]"];
                                23->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                0->2[label="[a-h]"];
                                1->3[label="[]"];
                                2->3[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfa_and_nfa_with_direct_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                2;3;
                                0->1[label="[c-h]"];
                                1->1[label="[xz]"];
                                1->2[label="[xz]"];
                                1->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                2;3;
                                0->1[label="[c-h]"];
                                1->1[label="[xz]"];
                                1->2[label="[5-9]"];
                                1->3[label="[0-4]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfa_and_nfa_with_direct_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->12[label="[a-z]"];
                                12->3[label="[c-h3-9]"];
                                12->4[label="[0-2]"];
                                4->3[label="[c-h3-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->2[label="[0-5]"];
                                2->3[label="[3-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfa_and_nfa_with_indirect_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->12[label="[c-h]"];
                                0->2[label="[abi-z]"];
                                12->3[label="[c-h3-9]"];
                                2->0[label="[3-9]"];
                                3->0[label="[k-o]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->3[label="[3-9]"];
                                3->0[label="[k-o]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_dfa_and_nfa_with_indirect_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                5;
                                0->1[label="[0-9]"];
                                0->4[label="[abix1-4]"];
                                4->1[label="[kln]"];
                                1->5[label="[09]"];
                                1->2[label="[a-h]"];
                                2->3[label="[i-n0-5]"];
                                3->1[label="[zxkt]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                5;
                                0->1[label="[05-9]"];
                                0->4[label="[abix]"];
                                0->14[label="[1-4]"];
                                4->1[label="[kln]"];
                                14->1[label="[kln]"];
                                14->5[label="[09]"];
                                1->2[label="[a-h]"];
                                2->3[label="[i-n0-5]"];
                                3->14[label="[zxkt]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                0->2[label="[a-h]"];
                                1->2[label="[a-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                0->2[label="[a-h]"];
                                1->2[label="[a-s]"];
                                1->3[label="[a-s]"];
                                2->3[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                0->2[label="[a-h]"];
                                1->2[label="[a-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                0->2[label="[a-h]"];
                                1->2[label="[g-s]"];
                                1->3[label="[a-d]"];
                                2->3[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_direct_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->2[label="[0-5]"];
                                2->3[label="[3-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[c-h]"];
                                2->4[label="[0-5]"];
                                4->2[label="[0-5]"];
                                2->3[label="[3-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_indirect_loop_and_difftransition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                5;
                                0->1[label="[0-9]"];
                                0->4[label="[abix1-4]"];
                                4->1[label="[kln]"];
                                1->5[label="[09]"];
                                1->2[label="[a-h]"];
                                2->3[label="[i-n0-5]"];
                                3->1[label="[zxkt]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                5;
                                0->1[label="[0-9]"];
                                0->4[label="[abix1-4]"];
                                4->1[label="[kln]"];
                                1->5[label="[09]"];
                                1->2[label="[a-h]"];
                                2->3[label="[i-n0-5]"];
                                3->1[label="[zxkt]"];
                                3->2[label="[]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_direct_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                3;
                                0->1[label="[0-5a-h]"];
                                0->2[label="[3-8c-z]"];
                                2->2[label="[z]"];
                                2->3[label="[a-y]"];
                                1->3[label="[3-9xy]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[0-5a-h]"];
                                0->2[label="[3-8c-z]"];
                                2->2[label="[z]"];
                                2->3[label="[a-z]"];
                                1->3[label="[3-9xy]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_not_equiv_nfas_with_indirect_loop_and_early_endstate() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                6;
                                0->1[label="[0-5a-h]"];
                                1->2[label="[3-8c-z]"];
                                1->6[label="[c-z]"];
                                1->4[label="[1-7]"];
                                2->3[label="[abkl]"];
                                4->5[label="[hklo]"];
                                3->1[label="[yz]"];
                                5->1[label="[1-9]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                6;
                                0->1[label="[0-5a-h]"];
                                1->2[label="[3-8c-z]"];
                                1->6[label="[h-z]"];
                                1->4[label="[1-7]"];
                                2->3[label="[abkl]"];
                                4->5[label="[hklo]"];
                                3->1[label="[yz]"];
                                5->1[label="[1-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, false, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }

    public function test_equiv_nfas_with_indirect_loop_from_empty_transition() {
        $differences = array();
        $resultdifferences = array();
        $firstautomata = new qtype_preg_fa();
        $secondautomata = new qtype_preg_fa();

        $dotdescriptionfirst = 'digraph example {
                                0;
                                6;7;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->7[label="[a-h]"];
                                2->6[label="[0-9]"];
                                1->3[label="[]"];
                                3->4[label="[]"];
                                4->5[label="[]"];
                                5->1[label="[]"];
                                }';
        $dotdescriptionsecond = 'digraph example {
                                0;
                                3;
                                0->1[label="[c-h]"];
                                0->2[label="[a-z]"];
                                1->3[label="[a-h]"];
                                2->3[label="[0-9]"];
                                }';

        $firstautomata->read_fa($dotdescriptionfirst);
        $secondautomata->read_fa($dotdescriptionsecond);

        $firstautomata->remove_unreachable_states();
        $secondautomata->remove_unreachable_states();

        $equiv = $firstautomata->compare_fa($secondautomata, $differences);

        $this->assertEquals($equiv, true, 'Result is not equal to expected');
        $this->assertEquals($differences, $resultdifferences, 'Result differences are not equal to expected');
    }
}
