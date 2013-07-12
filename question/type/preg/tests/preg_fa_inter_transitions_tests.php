<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_inter_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_characters_diapason_and_single() {
        $dotdescription1 = 'digraph example {1;0->1[label="[a-z]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[cd]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[cd]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps() {
        $dotdescription1 = 'digraph example {1;0->1[label="[]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_and_capturing() {
        $dotdescription1 = 'digraph example {1;0->1[label="[(]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[a-z]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[(a-z]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_with_tags() {
        $dotdescription1 = 'digraph example {1;0->1[label="[()]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[(]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[()]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_asserts() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[\\Aa-c]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_ununited_asserts() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[$a]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[^$a]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_asserts_with_tags() {
        $dotdescription1 = 'digraph example {1;0->1[label="[(^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[(^a]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[(^a]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_and_character() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[a]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[^a]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_character_tag() {
        $dotdescription1 = 'digraph example {1;0->1[label="[(^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[a]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[(^a]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_and_unmerged() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a-c]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[^]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[^a-c]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_unmerged_asserts() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[\\A]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[\\A]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_ans_assert() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[]"];}';
        $dotresult = 'digraph example {"1,1";"0,0"->"1,1"[label="[^]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $origininter = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->read_fa($dotresult, $origininter);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $tran = $resultautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertTrue($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
        $this->assertEquals($tran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_tags() {
        $dotdescription1 = 'digraph example {1;0->1[label="[(a-c)]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[(g-k)]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertFalse($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
    }

    public function test_no_intersecion_merged() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[^01]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertFalse($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
    }

    public function test_no_intersecion_assert_and_character() {
        $dotdescription1 = 'digraph example {1;0->1[label="[^a]"];}';
        $dotdescription2 = 'digraph example {1;0->1[label="[01]"];}';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);

        $tran1 = $firstautomata->states[0]->outtransitions[0];
        $tran2 = $secondautomata->states[0]->outtransitions[0];
        $resulttran = new qtype_preg_fa_transition;

        $this->assertFalse($tran1->intersection_transition($tran2, $resulttran), 'Return value isn not equal to expected');
    }
}
