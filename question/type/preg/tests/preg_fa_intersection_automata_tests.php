<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_intersection_automata_test extends PHPUnit_Framework_TestCase {

    public function test_no_intersection() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                2;4;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                3->4[label="[\\/]"];
                                4->4[label="[\\[\\]\\(\\)]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;4;
                                0->1[label="[01]"];
                                1->2[label="[4]"];
                                1->0[label="[,\\.!]"];
                                2->4[label="[*+]"];
                                2->3[label="[xy]"];
                                0->2[label="[-?]"];
                            }';
        $dotresult = 'digraph example 
                    {
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_end() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-\\&,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[.]"];
                                2->3[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";"0,0";
                        "4,";
                        "1,"->"4,"[label="[01]",color=violet];
                        "3,"->"4,"[label="[a]",color=violet];
                        "0,"->"1,"[label="[0-9]",color=violet];
                        "2,3"->"3,"[label="[\\-\\&,]",color=violet];
                        "1,1"->"2,3"[label="[abc]",color=red];
                        "2,1"->"2,3"[label="[a-z]",color=red];
                        "0,0"->"1,1"[label="[01]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 2, 1);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycles() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[a-z]"];
                                1->1[label="[0-9]"];
                                2->2[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[a-z]"];
                                1->2[label="[a-c]"];
                                0->2[label="[0-9]"];
                                1->1[label="[\\.,]"];
                                2->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "2,2";
                        "0,"->"1,0"[label="[01]",color=violet];
                        "1,0"->"2,1"[label="[a-z]",color=red];
                        "1,0"->"1,2"[label="[0-9]",color=red];
                        "2,1"->"2,2"[label="[a-c]",color=red];
                        "1,2"->"2,2"[label="[ab]",color=red];
                        "2,2"->"2,2"[label="[ab]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_implicent_cycles() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                0->2[label="[ab]"];
                                1->2[label="[ab]"];
                                2->1[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "2,2";
                        "0,"->"1,0"[label="[ab]",color=violet];
                        "1,0"->"2,1"[label="[ab]",color=red];
                        "1,0"->"2,2"[label="[ab]",color=red];
                        "2,1"->"0,2"[label="[ab]",color=red];
                        "2,2"->"0,1"[label="[ab]",color=red];
                        "0,2"->"1,1"[label="[ab]",color=red];
                        "0,1"->"1,2"[label="[ab]",color=red];
                        "1,1"->"2,2"[label="[ab]",color=red];
                        "1,2"->"2,1"[label="[ab]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_end_state() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[xy]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                                3->4[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[a-z]"];
                                2->3[label="[\\.,-]"];
                            }';
        $dotresult = 'digraph res 
                    {
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_start_state() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[xy]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                                3->4[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res 
                    {
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 3, 1);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_branches_from_first_and_second() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[a-z]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        ",2";
                        "0,"->"1,0"[label="[a-z]",color=violet];
                        "1,0"->"2,1"[label="[0-9]",color=red];
                        "2,1"->",2"[label="[a-z]",color=blue];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycle() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc0-9]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-?,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[.]"];
                                2->3[label="[<>]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";"0,0";
                        "4,";
                        "1,"->"4,"[label="[01]",color=violet];
                        "3,"->"4,"[label="[a]",color=violet];
                        "0,"->"1,"[label="[0-9]",color=violet];
                        "0,"->"1,"[label="[0-9]",color=violet];
                        "0,"->"1,0"[label="[0-9]",color=violet];
                        "2,3"->"3,"[label="[\\-?,]",color=violet];
                        "1,1"->"2,3"[label="[abc0-9]",color=red];
                        "2,1"->"2,3"[label="[a-z]",color=red];
                        "0,0"->"1,1"[label="[01]",color=red];
                        "1,0"->"2,1"[label="[01]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 2, 1);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_divarication_into_cycle() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                0->3[label="[01]"];
                                3->4[label="[y]"];
                                2->4[label="[a-f]"];
                                4->0[label="[bc]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                4;
                                0->1[label="[c-n]"];
                                1->2[label="[ab]"];
                                2->3[label="[0-9]"];
                                3->4[label="[x-z]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "4,";"4,4";
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "0,"->"3,"[label="[01]",color=violet];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "3,"->"4,"[label="[y]",color=violet];
                        "2,0"->"4,1"[label="[c-f]",color=red];
                        "4,"->"0,"[label="[bc]",color=violet];
                        "4,1"->"0,2"[label="[b]",color=red];
                        "0,2"->"3,3"[label="[01]",color=red];
                        "3,3"->"4,4"[label="[y]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_big_cycle() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                0->2[label="[ab]"];
                                1->2[label="[ab]"];
                                2->1[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";"0,0";
                        "2,";
                        "2,"->"0,"[label="[ab]",color=violet];
                        "1,2"->"2,"[label="[ab]",color=violet];
                        "1,2"->"2,1"[label="[ab]",color=red];
                        "0,0"->"1,2"[label="[ab]",color=red];
                        "0,0"->"1,1"[label="[ab]",color=red];
                        "0,1"->"1,2"[label="[ab]",color=red];
                        "2,0"->"0,1"[label="[ab]",color=red];
                        "2,0"->"0,2"[label="[ab]",color=red];
                        "2,2"->"0,1"[label="[ab]",color=red];
                        "1,0"->"2,2"[label="[ab]",color=red];
                        "1,0"->"2,1"[label="[ab]",color=red];
                        "1,1"->"2,2"[label="[ab]",color=red];
                        "0,2"->"1,1"[label="[ab]",color=red];
                        "2,1"->"0,2"[label="[ab]",color=red];
                        "1,"->"2,"[label="[ab]",color=violet];
                        "1,"->"2,"[label="[ab]",color=violet];
                        "1,"->"2,"[label="[ab]",color=violet];
                        "1,"->"2,0"[label="[ab]",color=violet];
                        "0,"->"1,"[label="[ab]",color=violet];
                        "0,"->"1,"[label="[ab]",color=violet];
                        "0,"->"1,0"[label="[ab]",color=violet];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 1);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_merged_states() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        ",2";
                        "0,"->"1   2,0"[label="[ab]",color=violet];
                        "1   2,0"->"3,1"[label="[ab]",color=red];
                        "3,1"->",2"[label="[ab]",color=blue];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_intersection() {
        $dotdescription1 = 'digraph example 
                            {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example 
                            {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";
                        "3,3";
                        "0,0"->"1   2,1   2"[label="[ab]",color=red];
                        "1   2,1   2"->"3,3"[label="[ab]",color=red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersection_automata($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}