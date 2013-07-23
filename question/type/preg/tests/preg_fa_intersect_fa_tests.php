<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_intersect_fa_test extends PHPUnit_Framework_TestCase {

    public function test_nessesary_merging() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;5;
                                0->1[label="[]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->4[label="[.]"];
                                3->4[label="[.]"];
                                2->5[label="[01]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[01]"];
                                0->2[label="[]"];
                                0->3[label="[ab]"];
                                1->1[label="[<>]"];
                                1->3[label="[xy]"];
                                2->3[label="[cd]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0   1,";
                        "4,";
                        "0   1,"->"2,0   2"[label="[0-9]",color=violet];
                        "0   1,"->"3,"[label="[abc]",color=violet];
                        "2,0   2"->"4,1"[label="[01]",color=red];
                        "2,0   2"->"4,3"[label="[ab]",color=red];
                        "2,0   2"->"4,3"[label="[cd]",color=red];
                        "2,0   2"->"5,1"[label="[01]",color=red];
                        "3,"->"4,"[label="[.]",color=violet];
                        "4,1"->",1"[label="[<>]",color=blue];
                        "4,1"->",3"[label="[xy]",color=blue];
                        "4,3"->"4,"[label="[]",color=red];
                        "5,1"->",1"[label="[<>]",color=blue];
                        "5,1"->",3"[label="[xy]",color=blue];
                        ",1"->",1"[label="[<>]",color=blue];
                        ",1"->",3"[label="[xy]",color=blue];
                        ",3"->"4,"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_blind() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-\\\\&,]"];
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
                        "2,3"->"3,"[label="[\-\&,]",color=violet];
                        "1,1"->"2,3"[label="[abc]",color=red];
                        "0,0"->"1,1"[label="[01]",color=red];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_back_with_changing_state_for_inter() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;6;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                1->3[label="[^]"];
                                2->1[label="[a]"];
                                2->4[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[a]"];
                                4->6[label="[a]"];
                                5->5[label="[a]"];
                                5->6[label="[a]"];
                                6->6[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                5;
                                0->2[label="[a]"];
                                0->3[label="[a]"];
                                2->4[label="[a]"];
                                2->3[label="[a]"];
                                3->5[label="[a]"];
                                4->3[label="[a]"];
                                4->5[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "6,4";
                        "0,"->"1   3,0"[label="[a]",color=violet];
                        "0,"->"1   3,"[label="[a]",color=violet];
                        "1   3,0"->"2,2"[label="[a]",color=red];
                        "1   3,0"->"2,3"[label="[a]",color=red];
                        "1   3,0"->"4,2"[label="[^a]",color=red];
                        "1   3,0"->"4,3"[label="[^a]",color=red];
                        "2,2"->"1   3,4"[label="[a]",color=red];
                        "2,2"->"1   3,3"[label="[a]",color=red];
                        "2,2"->"4,4"[label="[a]",color=red];
                        "2,2"->"4,3"[label="[a]",color=red];
                        "2,3"->"1   3,5"[label="[a]",color=red];
                        "2,3"->"4,5"[label="[a]",color=red];
                        "4,2"->"5,4"[label="[a]",color=red];
                        "4,2"->"5,3"[label="[a]",color=red];
                        "4,2"->"6,4"[label="[a]",color=red];
                        "4,2"->"6,3"[label="[a]",color=red];
                        "4,3"->"5,5"[label="[a]",color=red];
                        "4,3"->"6,5"[label="[a]",color=red];
                        "1   3,4"->"2,3"[label="[a]",color=red];
                        "1   3,4"->"2,5"[label="[a]",color=red];
                        "1   3,4"->"4,3"[label="[^a]",color=red];
                        "1   3,4"->"4,5"[label="[^a]",color=red];
                        "1   3,3"->"2,5"[label="[a]",color=red];
                        "1   3,3"->"4,5"[label="[^a]",color=red];
                        "4,4"->"5,3"[label="[a]",color=red];
                        "4,4"->"5,5"[label="[a]",color=red];
                        "4,4"->"6,3"[label="[a]",color=red];
                        "4,4"->"6,5"[label="[a]",color=red];
                        "1   3,5"->"2,"[label="[a]",color=violet];
                        "1   3,5"->"4,"[label="[^a]",color=violet];
                        "4,5"->"5,"[label="[a]",color=violet];
                        "4,5"->"6,"[label="[a]",color=violet];
                        "5,4"->"5,3"[label="[a]",color=red];
                        "5,4"->"5,5"[label="[a]",color=red];
                        "5,4"->"6,3"[label="[a]",color=red];
                        "5,4"->"6,5"[label="[a]",color=red];
                        "5,3"->"5,5"[label="[a]",color=red];
                        "5,3"->"6,5"[label="[a]",color=red];
                        "6,4"->"6,3"[label="[a]",color=red];
                        "6,4"->"6,5"[label="[a]",color=red];
                        "6,3"->"6,5"[label="[a]",color=red];
                        "5,5"->"6,4"[label="[]",color=red];
                        "6,5"->"6,4"[label="[]",color=red];
                        "2,5"->"1   3,"[label="[a]",color=violet];
                        "2,5"->"4,"[label="[a]",color=violet];
                        "2,"->"1   3,"[label="[a]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "4,"->"5,"[label="[a]",color=violet];
                        "4,"->"6,"[label="[a]",color=violet];
                        "1   3,"->"2,"[label="[a]",color=violet];
                        "1   3,"->"4,"[label="[^a]",color=violet];
                        "5,"->"6,"[label="[a]",color=violet];
                        "5,"->"5,"[label="[a]",color=violet];
                        "5,"->"6,"[label="[a]",color=violet];
                        "5,"->"6,4"[label="[]",color=violet];
                        "6,"->"6,"[label="[a]",color=violet];
                        "6,"->"6,4"[label="[]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 3, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_simple() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[b]"];
                                1->2[label="[a]"];
                                1->1[label="[]"];
                                0->2[label="[a]"];
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
                        "2,";
                        "0,"->"1,0"[label="[b]",color=violet];
                        "0,"->"2,"[label="[a]",color=violet];
                        "1,0"->"2,1"[label="[a]",color=red];
                        "2,1"->",2"[label="[ab]",color=blue];
                        ",2"->"2,"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_branches() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[b]"];
                                1->2[label="[a]"];
                                0->2[label="[a]"];
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
                        "0,0";
                        "2,2";
                        "0,0"->"1,1"[label="[b]",color=red];
                        "0,0"->"2,1"[label="[a]",color=red];
                        "1,1"->"2,2"[label="[a]",color=red];
                        "2,1"->",2"[label="[ab]",color=blue];
                        ",2"->"2,2"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_eps_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[b]"];
                                1->2[label="[]"];
                                2->1[label="[]"];
                                2->3[label="[a]"];
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
                        "0,0";
                        "3,2";
                        "0,0"->"1   2,1"[label="[b]",color=red];
                        "1   2,1"->"3,2"[label="[a]",color=red];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_assert_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[b]"];
                                1->2[label="[^]"];
                                2->1[label="[^]"];
                                2->3[label="[a]"];
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
                        "0,0";
                        "3,2";
                        "0,0"->"1   2,1"[label="[b]",color=red];
                        "1   2,1"->"3,2"[label="[^a]",color=red];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_start_implicent_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";
                        "5,";
                        "0,0"->"1,1"[label="[ab]",color=red];
                        "1,1"->"2,2"[label="[01]",color=red];
                        "2,2"->"3,"[label="[a]",color=violet];
                        "3,"->"4,"[label="[a-z]",color=violet];
                        "4,"->"5,"[label="[a-z]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "2,"->"3,"[label="[a]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_three_time_in_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                9;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[ab]"];
                                5->6[label="[a]"];
                                6->7[label="[a]"];
                                7->8[label="[a]"];
                                8->9[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "5,";
                        "4,9"->"5,"[label="[a-z]",color=violet];
                        "3,8"->"4,9"[label="[a]",color=red];
                        "2,7"->"3,8"[label="[a]",color=red];
                        "1,6"->"2,7"[label="[a]",color=red];
                        "0,5"->"1,6"[label="[a]",color=red];
                        "4,5   9"->"1,6"[label="[a]",color=red];
                        "3,4   8"->"4,5   9"[label="[a]",color=red];
                        "2,3   7"->"3,4   8"[label="[a]",color=red];
                        "1,2   6"->"2,3   7"[label="[a]",color=red];
                        "1,2   6"->"(2,3   7)"[label="[a]",color=red];
                        "0,1"->"1,2   6"[label="[a]",color=red];
                        "4,1   5   9"->"1,2   6"[label="[a]",color=red];
                        "3,0   4   8"->"4,1   5   9"[label="[a]",color=red];
                        "(2,3   7)"->"3,0   4   8"[label="[a]",color=red];
                        ",4"->"0,5"[label="[ab]",color=blue];
                        ",3"->",4"[label="[a]",color=blue];
                        ",2"->",3"[label="[a]",color=blue];
                        ",1"->",2"[label="[a]",color=blue];
                        ",0"->",1"[label="[a]",color=blue];
                        ",0"->",1"[label="[a]",color=blue];
                        ",0"->"0,1"[label="[a]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_time_in_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                8;
                                0->1[label="[.]"];
                                1->2[label="[.]"];
                                2->3[label="[.]"];
                                3->4[label="[.]"];
                                4->5[label="[.]"];
                                5->6[label="[.]"];
                                6->7[label="[.]"];
                                7->8[label="[.]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "5,8";
                        "4,7"->"5,8"[label="[a-z]",color=red];
                        "3,6"->"4,7"[label="[a-z]",color=red];
                        "2,5"->"3,6"[label="[a]",color=red];
                        "1,4"->"2,5"[label="[0-9]",color=red];
                        "0,3"->"1,4"[label="[a-c]",color=red];
                        "0,3"->"1,0   4"[label="[a-c]",color=red];
                        "4,3   7"->"1,4"[label="[a]",color=red];
                        "4,3   7"->"1,0   4"[label="[a]",color=red];
                        "3,2   6"->"4,3   7"[label="[a-z]",color=red];
                        "2,1   5"->"3,2   6"[label="[a]",color=red];
                        "1,0   4"->"2,1   5"[label="[0-9]",color=red];
                        ",2"->"0,3"[label="[.]",color=blue];
                        ",1"->",2"[label="[.]",color=blue];
                        ",0"->",1"[label="[.]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 5, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_no_intersection() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_not_start() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[a-k]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "5,";
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "2,0"->"3,1"[label="[a]",color=red];
                        "3,1"->"4,2"[label="[a-k]",color=red];
                        "4,2"->"5,"[label="[a-z]",color=violet];
                        "4,2"->"1,"[label="[m]",color=violet];
                        "2,"->"3,"[label="[a]",color=violet];
                        "3,"->"4,"[label="[a-z]",color=violet];
                        "4,"->"5,"[label="[a-z]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_in_branch() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[a-z]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "7,";
                        "0,0"->"1,1"[label="[ab]",color=red];
                        "1,1"->"2,2"[label="[01]",color=red];
                        "2,2"->"4,"[label="[a]",color=violet];
                        "4,"->"6,"[label="[a-z]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "6,"->"1,"[label="[a-s]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[a-z]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "3,"->"5,"[label="[012]",color=violet];
                        "5,"->"6,"[label="[+=]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_three_times_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "5,";
                        "4,5"->"5,"[label="[a-z]",color=violet];
                        "3,4"->"4,5"[label="[ab]",color=red];
                        "2,3"->"3,4"[label="[a]",color=red];
                        "1,2"->"2,3"[label="[a]",color=red];
                        "1,2"->"(2,3)"[label="[a]",color=red];
                        "0,1"->"1,2"[label="[a]",color=red];
                        "4,1   5"->"1,2"[label="[a]",color=red];
                        "3,0   4"->"4,1   5"[label="[a]",color=red];
                        "(2,3)"->"3,0   4"[label="[a]",color=red];
                        ",0"->"0,1"[label="[a]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_in_branch() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "7,";
                        "6,2"->"7,"[label="[a-c]",color=violet];
                        "4,1"->"6,2"[label="[0]",color=red];
                        "2,0"->"4,1"[label="[a]",color=red];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[a-z]",color=violet];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "4,"->"6,"[label="[0]",color=violet];
                        "6,"->"1,"[label="[a-s]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "5,"->"6,"[label="[+=]",color=violet];
                        "3,"->"5,"[label="[012]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[a-z]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "7,";
                        "6,2"->"7,"[label="[a-c]",color=violet];
                        "4,1"->"6,2"[label="[0]",color=red];
                        "5,1"->"6,2"[label="[01]",color=red];
                        "2,0"->"4,1"[label="[a]",color=red];
                        "3,0"->"5,1"[label="[ab]",color=red];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[a-z]",color=violet];
                        "1,"->"3,0"[label="[a-z]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "4,"->"6,"[label="[0]",color=violet];
                        "6,"->"1,"[label="[a-s]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "5,"->"6,"[label="[0-9]",color=violet];
                        "3,"->"5,"[label="[a-z]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 6, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches__asserts_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[a-z]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[0-9]"];
                                6->1[label="[^]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "7,";
                        "6,2"->"7,"[label="[a-c]",color=violet];
                        "4,1"->"6,2"[label="[0]",color=red];
                        "5,1"->"6,2"[label="[01]",color=red];
                        "2,0"->"4,1"[label="[a]",color=red];
                        "3,0"->"5,1"[label="[ab]",color=red];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[a-z]",color=violet];
                        "1,"->"3,0"[label="[a-z]",color=violet];
                        "6,"->"2,0"[label="[^0-9]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "6,"->"2,"[label="[^0-9]",color=violet];
                        "6,"->"3,"[label="[^a-z]",color=violet];
                        "6,"->"3,0"[label="[^a-z]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "4,"->"6,"[label="[0]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "5,"->"6,"[label="[0-9]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "3,"->"5,"[label="[a-z]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 6, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_cycles() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[012]"];
                                3->5[label="[+=]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[0-9]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";
                        "7,";
                        "0,0"->"1,1"[label="[ab]",color=red];
                        "1,1"->"2,2"[label="[01]",color=red];
                        "2,2"->"4,3"[label="[a]",color=red];
                        "4,3"->"6,"[label="[0]",color=violet];
                        "4,3"->"1,"[label="[m]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[012]",color=violet];
                        "7,"->"1,"[label="[a-s]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                        "3,"->"5,"[label="[+=]",color=violet];
                        "4,"->"6,"[label="[0]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "5,"->"7,"[label="[0-9]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 0, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_start_states() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[012]"];
                                3->5[label="[+=]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[0-9]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";
                        "7,";
                        "7,"->"1,"[label="[a-s]",color=violet];
                        "5,"->"7,"[label="[0-9]",color=violet];
                        "5,"->"7,0"[label="[0-9]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "6,"->"7,0"[label="[a-c]",color=violet];
                        "3,"->"5,"[label="[+=]",color=violet];
                        "4,3"->"6,"[label="[0]",color=violet];
                        "4,3"->"1,"[label="[m]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[012]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "2,2"->"4,3"[label="[a]",color=red];
                        "1,1"->"2,2"[label="[01]",color=red];
                        "0,0"->"1,1"[label="[ab]",color=red];
                        "7,0"->"1,1"[label="[ab]",color=red];
                        "4,"->"6,"[label="[0]",color=violet];
                        "4,"->"1,"[label="[m]",color=violet];
                        "2,"->"4,"[label="[a]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[a-c]"];
                                1->2[label="[a-c]"];
                                2->3[label="[0-5as]"];
                                3->4[label="[+=]"];
                                4->5[label="[0]"];
                                4->2[label="[m]"];
                                5->6[label="[0-9]"];
                                5->1[label="[a-s]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "6,";
                        "0,"->"1,0"[label="[a-c]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "1,0"->"2,1"[label="[ab]",color=red];
                        "2,1"->"3,2"[label="[01]",color=red];
                        "3,2"->"4,"[label="[+=]",color=violet];
                        "4,"->"5,"[label="[0]",color=violet];
                        "4,"->"2,"[label="[m]",color=violet];
                        "5,"->"6,"[label="[0-9]",color=violet];
                        "5,"->"1,"[label="[a-s]",color=violet];
                        "2,"->"3,"[label="[as0-5]",color=violet];
                        "1,"->"2,"[label="[a-c]",color=violet];
                        "3,"->"4,"[label="[+=]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[a-c]"];
                                1->2[label="[a-c]"];
                                2->3[label="[0-5as]"];
                                3->4[label="[ab]"];
                                4->5[label="[0]"];
                                4->2[label="[m]"];
                                5->6[label="[0-9]"];
                                5->1[label="[a-s]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "6,";
                        "5,2"->"6,"[label="[0-9]",color=violet];
                        "4,1"->"5,2"[label="[0]",color=red];
                        "3,0"->"4,1"[label="[ab]",color=red];
                        "2,"->"3,"[label="[as0-5]",color=violet];
                        "2,"->"3,0"[label="[as0-5]",color=violet];
                        "1,"->"2,"[label="[a-c]",color=violet];
                        "4,"->"2,"[label="[m]",color=violet];
                        "4,"->"5,"[label="[0]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "5,"->"6,"[label="[0-9]",color=violet];
                        "5,"->"1,"[label="[a-s]",color=violet];
                        "3,"->"4,"[label="[ab]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 5, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[.]"];
                                1->2[label="[.]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";",0";
                        "7,";
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "1,"->"2,0"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[a-z]",color=violet];
                        "2,0"->"4,1"[label="[a]",color=red];
                        "3,"->"5,"[label="[012]",color=violet];
                        "5,"->"6,"[label="[+=]",color=violet];
                        "6,"->"1,"[label="[a-s]",color=violet];
                        "6,"->"7,"[label="[a-c]",color=violet];
                        "4,1"->"6,2"[label="[0]",color=red];
                        "4,1"->"1,2"[label="[m]",color=red];
                        "6,2"->"1,0"[label="[a-s]",color=red];
                        "6,2"->"7,0"[label="[a-c]",color=red];
                        "1,2"->"2,0"[label="[0-9]",color=red];
                        "1,2"->"3,0"[label="[a-z]",color=red];
                        "1,0"->"2,1"[label="[0-9]",color=red];
                        "1,0"->"3,1"[label="[a-z]",color=red];
                        "7,0"->",1"[label="[.]",color=blue];
                        "3,0"->"5,1"[label="[012]",color=red];
                        "2,1"->"4,2"[label="[a]",color=red];
                        "3,1"->"5,2"[label="[012]",color=red];
                        "5,1"->"6,2"[label="[+=]",color=red];
                        "4,2"->"6,0"[label="[0]",color=red];
                        "4,2"->"1,0"[label="[m]",color=red];
                        "5,2"->"6,0"[label="[+=]",color=red];
                        "6,0"->"1,1"[label="[a-s]",color=red];
                        "6,0"->"7,1"[label="[a-c]",color=red];
                        "1,1"->"2,2"[label="[0-9]",color=red];
                        "1,1"->"3,2"[label="[a-z]",color=red];
                        "7,1"->",2"[label="[.]",color=blue];
                        "2,2"->"4,0"[label="[a]",color=red];
                        "3,2"->"5,0"[label="[012]",color=red];
                        "4,0"->"6,1"[label="[0]",color=red];
                        "4,0"->"1,1"[label="[m]",color=red];
                        "5,0"->"6,1"[label="[+=]",color=red];
                        "6,1"->"1,2"[label="[a-s]",color=red];
                        "6,1"->"7,2"[label="[a-c]",color=red];
                        "7,2"->"7,"[label="[]",color=red];
                        ",1"->",2"[label="[.]",color=blue];
                        ",2"->",0"[label="[.]",color=blue];
                        ",2"->"7,"[label="[]",color=blue];
                        ",0"->",1"[label="[.]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_intersection() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                0->1[label="[01]"];
                                1->2[label="[ab]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";
                        "7,2";
                        "6,1"->"7,2"[label="[ab]",color=red];
                        "4,0"->"6,1"[label="[0]",color=red];
                        "2,2"->"4,0"[label="[a]",color=red];
                        "1,1"->"2,2"[label="[ab]",color=red];
                        "0,0"->"1,1"[label="[01]",color=red];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 7, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_in_intersection() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[0]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[ab]"];
                                4->1[label="[m]"];
                                5->6[label="[^a]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[ab]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "7,";
                        "6,2"->"7,"[label="[a-c]",color=violet];
                        "4,1"->"6,2"[label="[ab]",color=red];
                        "5,1"->"6,2"[label="[^a]",color=red];
                        "2,0"->"4,1"[label="[0]",color=red];
                        "3,0"->"5,1"[label="[01]",color=red];
                        "1,2"->"2,0"[label="[0-9]",color=red];
                        "1,2"->"3,0"[label="[a-z]",color=red];
                        "0,1"->"1,2"[label="[ab]",color=red];
                        ",0"->",1"[label="[01]",color=blue];
                        ",0"->"0,1"[label="[01]",color=blue];
                        ",2"->",0"[label="[.]",color=blue];
                        ",2"->"7,"[label="[]",color=blue];
                        ",1"->",2"[label="[ab]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 6, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[0-9]"];
                                1->2[label="[a-n]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                2;
                                0->1[label="[.]"];
                                1->2[label="[.]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,0";",0";
                        "7,2";
                        "6,1"->"7,2"[label="[a-c]",color=red];
                        "6,1"->"1,2"[label="[a-s]",color=red];
                        "4,0"->"6,1"[label="[0]",color=red];
                        "4,0"->"1,1"[label="[m]",color=red];
                        "5,0"->"6,1"[label="[+=]",color=red];
                        "2,2"->"4,0"[label="[a]",color=red];
                        "3,2"->"5,0"[label="[012]",color=red];
                        "1,1"->"2,2"[label="[a-n]",color=red];
                        "1,1"->"3,2"[label="[a-z]",color=red];
                        "0,0"->"1,1"[label="[0-9]",color=red];
                        "6,0"->"1,1"[label="[a-s]",color=red];
                        "4,2"->"6,0"[label="[0]",color=red];
                        "4,2"->"1,0"[label="[m]",color=red];
                        "5,2"->"6,0"[label="[+=]",color=red];
                        "2,1"->"4,2"[label="[a]",color=red];
                        "3,1"->"5,2"[label="[012]",color=red];
                        "1,0"->"2,1"[label="[a-n]",color=red];
                        "1,0"->"3,1"[label="[a-z]",color=red];
                        "0,2"->"1,0"[label="[0-9]",color=red];
                        "6,2"->"1,0"[label="[a-s]",color=red];
                        "4,1"->"6,2"[label="[0]",color=red];
                        "4,1"->"1,2"[label="[m]",color=red];
                        "5,1"->"6,2"[label="[+=]",color=red];
                        "2,0"->"4,1"[label="[a]",color=red];
                        "3,0"->"5,1"[label="[012]",color=red];
                        "1,2"->"2,0"[label="[a-n]",color=red];
                        "1,2"->"3,0"[label="[a-z]",color=red];
                        "0,1"->"1,2"[label="[0-9]",color=red];
                        ",1"->",2"[label="[.]",color=blue];
                        ",1"->"0,2"[label="[.]",color=blue];
                        ",0"->",1"[label="[.]",color=blue];
                        ",0"->"0,1"[label="[.]",color=blue];
                        ",2"->",0"[label="[.]",color=blue];
                        ",2"->"7,2"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 7, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_coping_with_intersection() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                9;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                1->3[label="[ab]"];
                                2->8[label="[as]"];
                                1->5[label="[a-z]"];
                                3->4[label="[a-c]"];
                                4->8[label="[ab]"];
                                5->6[label="[0-9]"];
                                7->8[label="[0-9]"];
                                6->7[label="[0-9]"];
                                8->9[label="[0-9]"];
                                8->1[label="[0-9]"];
                                9->2[label="[0-9]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                9;
                                0->1[label="[a-z]"];
                                1->2[label="[ab]"];
                                1->3[label="[as]"];
                                2->6[label="[a-c]"];
                                3->4[label="[.]"];
                                4->5[label="[a-d]"];
                                5->6[label="[as01]"];
                                6->7[label="[ax-y]"];
                                6->8[label="[.]"];
                                7->9[label="[a]"];
                                8->9[label="[a-c]"];
                                7->2[label="[a-z]"];
                                8->1[label="[a-f]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";"0,0";",0";
                        "9,";
                        "9,"->"2,"[label="[0-9]",color=violet];
                        "8,"->"9,"[label="[0-9]",color=violet];
                        "8,"->"9,"[label="[0-9]",color=violet];
                        "8,"->"1,"[label="[0-9]",color=violet];
                        "8,"->"1,0"[label="[0-9]",color=violet];
                        "2,"->"8,"[label="[as]",color=violet];
                        "4,"->"8,"[label="[ab]",color=violet];
                        "7,"->"8,"[label="[0-9]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"2,"[label="[0-9]",color=violet];
                        "1,"->"3,"[label="[ab]",color=violet];
                        "1,"->"5,"[label="[a-z]",color=violet];
                        "3,"->"4,"[label="[a-c]",color=violet];
                        "6,"->"7,"[label="[0-9]",color=violet];
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "0,"->"1,0"[label="[a-c]",color=violet];
                        "5,9"->"6,"[label="[0-9]",color=violet];
                        "1,7"->"5,9"[label="[a]",color=red];
                        "1,8"->"5,9"[label="[a-c]",color=red];
                        "1,8"->"3,1"[label="[ab]",color=red];
                        "0,6"->"1,7"[label="[a]",color=red];
                        "0,6"->"1,8"[label="[a-c]",color=red];
                        "8,6"->"1,8"[label="[0-9]",color=red];
                        "4,2"->"8,6"[label="[ab]",color=red];
                        "4,5"->"8,6"[label="[a]",color=red];
                        "3,1"->"4,2"[label="[ab]",color=red];
                        "3,7"->"4,2"[label="[a-c]",color=red];
                        "3,4"->"4,5"[label="[a-c]",color=red];
                        "1,0"->"3,1"[label="[ab]",color=red];
                        "1,6"->"3,7"[label="[a]",color=red];
                        "1,3"->"3,4"[label="[ab]",color=red];
                        "1,3"->"2,4"[label="[0-9]",color=red];
                        "0,2"->"1,6"[label="[a-c]",color=red];
                        "0,5"->"1,6"[label="[a]",color=red];
                        "8,5"->"1,6"[label="[01]",color=red];
                        "0,1"->"1,3"[label="[a]",color=red];
                        "2,4"->"8,5"[label="[a]",color=red];
                        "4,4"->"8,5"[label="[ab]",color=red];
                        "3,3"->"4,4"[label="[a-c]",color=red];
                        "1,1"->"3,3"[label="[a]",color=red];
                        "0,0"->"1,1"[label="[a-c]",color=red];
                        "0,8"->"1,1"[label="[a-c]",color=red];
                        ",2"->",6"[label="[a-c]",color=blue];
                        ",2"->"0,6"[label="[a-c]",color=blue];
                        ",5"->",6"[label="[as01]",color=blue];
                        ",5"->"0,6"[label="[as01]",color=blue];
                        ",1"->",2"[label="[ab]",color=blue];
                        ",1"->",3"[label="[as]",color=blue];
                        ",1"->"0,2"[label="[ab]",color=blue];
                        ",7"->",2"[label="[a-z]",color=blue];
                        ",7"->"0,2"[label="[a-z]",color=blue];
                        ",4"->",5"[label="[a-d]",color=blue];
                        ",4"->"0,5"[label="[a-d]",color=blue];
                        ",0"->",1"[label="[a-z]",color=blue];
                        ",0"->"0,1"[label="[a-z]",color=blue];
                        ",8"->",1"[label="[a-f]",color=blue];
                        ",8"->"0,1"[label="[a-f]",color=blue];
                        ",6"->",7"[label="[ax-y]",color=blue];
                        ",6"->",8"[label="[.]",color=blue];
                        ",6"->"0,8"[label="[.]",color=blue];
                        ",3"->",4"[label="[.]",color=blue];
                        "5,"->"6,"[label="[0-9]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 5, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;
                                0->1[label="[a-c]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        ",3";
                        "0,"->"1,"[label="[a-c]",color=violet];
                        "1,"->"2,0"[label="[a-z]",color=violet];
                        "2,0"->"3,1"[label="[a]",color=red];
                        "3,1"->"4,2"[label="[a-z]",color=red];
                        "3,1"->"1,2"[label="[a]",color=red];
                        "4,2"->",3"[label="[a]",color=blue];
                        "1,2"->"2,3   0"[label="[a]",color=red];
                        "2,3   0"->"3,1"[label="[a]",color=red];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_merged() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a-z]"];
                                1->3[label="[a-z]"];
                                2->4[label="[a]"];
                                3->4[label="[a-z]"];
                                4->1[label="[a]"];
                                4->5[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                9;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                                6->7[label="[ab]"];
                                7->8[label="[a-z]"];
                                8->9[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "5,9";
                        "0,"->"1,0"[label="[a-c]",color=violet];
                        "1,0"->"2,1"[label="[ab]",color=red];
                        "1,0"->"3,1"[label="[ab]",color=red];
                        "2,1"->"4,2"[label="[a]",color=red];
                        "3,1"->"4,2"[label="[a-z]",color=red];
                        "4,2"->"1,3   0"[label="[a]",color=red];
                        "4,2"->"5,3"[label="[a]",color=red];
                        "1,3   0"->"2,4   1"[label="[ab]",color=red];
                        "1,3   0"->"3,4   1"[label="[ab]",color=red];
                        "5,3"->",4"[label="[ab]",color=blue];
                        "2,4   1"->"4,5   2"[label="[a]",color=red];
                        "3,4   1"->"4,5   2"[label="[a-z]",color=red];
                        "4,5   2"->"1,6   3   0"[label="[a]",color=red];
                        "4,5   2"->"5,6"[label="[a]",color=red];
                        "1,6   3   0"->"2,7   4   1"[label="[ab]",color=red];
                        "1,6   3   0"->"3,7   4   1"[label="[ab]",color=red];
                        "5,6"->",7"[label="[ab]",color=blue];
                        "2,7   4   1"->"4,8   5   2"[label="[a]",color=red];
                        "3,7   4   1"->"4,8   5   2"[label="[a-z]",color=red];
                        "4,8   5   2"->"1,9   6   3   0"[label="[a]",color=red];
                        "4,8   5   2"->"5,9"[label="[a]",color=red];
                        "1,9   6   3   0"->"2,7   4   1"[label="[ab]",color=red];
                        "1,9   6   3   0"->"3,7   4   1"[label="[ab]",color=red];
                        ",4"->",5"[label="[a-z]",color=blue];
                        ",5"->",6"[label="[a]",color=blue];
                        ",6"->",7"[label="[ab]",color=blue];
                        ",7"->",8"[label="[a-z]",color=blue];
                        ",8"->",9"[label="[a]",color=blue];
                        ",9"->"5,9"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_and_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                3->4[label="[a]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0   1,0";
                        "4,6";
                        "0   1,0"->"2,1"[label="[ab]",color=red];
                        "2,1"->"3,2"[label="[a-z]",color=red];
                        "3,2"->"4,3"[label="[a]",color=red];
                        "3,2"->"0   1,3   0"[label="[a]",color=red];
                        "4,3"->",4"[label="[ab]",color=blue];
                        "0   1,3   0"->"2,4   1"[label="[ab]",color=red];
                        "2,4   1"->"3,5   2"[label="[a-z]",color=red];
                        "3,5   2"->"4,6"[label="[a]",color=red];
                        "3,5   2"->"0   1,6   3   0"[label="[a]",color=red];
                        "0   1,6   3   0"->"2,4   1"[label="[ab]",color=red];
                        ",4"->",5"[label="[a-z]",color=blue];
                        ",5"->",6"[label="[a]",color=blue];
                        ",6"->"4,6"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_in_both() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                3->4[label="[a]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0   1,";
                        "4,6";
                        "0   1,"->"2,0   1"[label="[a-z]",color=violet];
                        "2,0   1"->"3,2"[label="[a-z]",color=red];
                        "3,2"->"4,3"[label="[a]",color=red];
                        "3,2"->"0   1,3"[label="[a]",color=red];
                        "4,3"->",4"[label="[ab]",color=blue];
                        "0   1,3"->"2,4   0   1"[label="[ab]",color=red];
                        "2,4   0   1"->"3,5   2"[label="[a-z]",color=red];
                        "3,5   2"->"4,6"[label="[a]",color=red];
                        "3,5   2"->"0   1,6   3"[label="[a]",color=red];
                        "0   1,6   3"->"2,4   0   1"[label="[ab]",color=red];
                        ",4"->",5"[label="[a-z]",color=blue];
                        ",5"->",6"[label="[a]",color=blue];
                        ",6"->"4,6"[label="[]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_same_length_cycle() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                4;
                                0->1[label="[a-j]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                3->4[label="[a]"];
                                3->1[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[a-s]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        ",6";
                        "0,"->"1,0"[label="[a-j]",color=violet];
                        "1,0"->"2,1"[label="[a-s]",color=red];
                        "2,1"->"3,2"[label="[a-z]",color=red];
                        "2,1"->"3,3"[label="[a]",color=red];
                        "3,2"->"4,4"[label="[a]",color=red];
                        "3,2"->"1,4   0"[label="[ab]",color=red];
                        "3,3"->"4,4"[label="[a]",color=red];
                        "3,3"->"1,4   0"[label="[a]",color=red];
                        "4,4"->",5"[label="[a-z]",color=blue];
                        "1,4   0"->"2,5   1"[label="[a-s]",color=red];
                        "2,5   1"->"3,6   2"[label="[ab]",color=red];
                        "2,5   1"->"3,6   3"[label="[a]",color=red];
                        "3,6   2"->"4,4"[label="[a]",color=red];
                        "3,6   2"->"1,4   0"[label="[ab]",color=red];
                        "3,6   3"->"4,4"[label="[a]",color=red];
                        "3,6   3"->"1,4   0"[label="[a]",color=red];
                        ",5"->",6"[label="[ab]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycles_full_first() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                8;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                1->4[label="[ab]"];
                                4->5[label="[ab]"];
                                5->6[label="[ab]"];
                                6->7[label="[ab]"];
                                3->7[label="[a]"];
                                3->1[label="[a]"];
                                7->1[label="[ab]"];
                                7->8[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                6;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        "0,";
                        "8,";
                        "0,"->"1,"[label="[ab]",color=violet];
                        "1,"->"2,0"[label="[a-z]",color=violet];
                        "1,"->"2,"[label="[a-z]",color=violet];
                        "1,"->"2,"[label="[a-z]",color=violet];
                        "1,"->"4,"[label="[ab]",color=violet];
                        "2,0"->"3,1"[label="[ab]",color=red];
                        "4,"->"5,"[label="[ab]",color=violet];
                        "5,"->"6,"[label="[ab]",color=violet];
                        "6,"->"7,"[label="[ab]",color=violet];
                        "7,"->"8,"[label="[ab]",color=violet];
                        "7,"->"1,"[label="[ab]",color=violet];
                        "7,"->"8,"[label="[ab]",color=violet];
                        "3,1"->"7,2"[label="[a]",color=red];
                        "3,1"->"1,2"[label="[a]",color=red];
                        "7,2"->"1,3"[label="[a]",color=red];
                        "7,2"->"8,3"[label="[a]",color=red];
                        "1,2"->"2,3   0"[label="[a]",color=red];
                        "1,2"->"4,3"[label="[a]",color=red];
                        "1,3"->"2,4   0"[label="[ab]",color=red];
                        "1,3"->"4,4"[label="[ab]",color=red];
                        "8,3"->",4"[label="[ab]",color=blue];
                        "2,3   0"->"3,4   1"[label="[ab]",color=red];
                        "4,3"->"5,4"[label="[ab]",color=red];
                        "2,4   0"->"3,5   1"[label="[ab]",color=red];
                        "4,4"->"5,5"[label="[ab]",color=red];
                        "3,4   1"->"7,5"[label="[a]",color=red];
                        "3,4   1"->"1,5   2"[label="[a]",color=red];
                        "5,4"->"6,5"[label="[ab]",color=red];
                        "3,5   1"->"7,6   2"[label="[a]",color=red];
                        "3,5   1"->"1,6   3"[label="[a]",color=red];
                        "5,5"->"6,6"[label="[a]",color=red];
                        "7,5"->"1,6   2"[label="[a]",color=red];
                        "7,5"->"8,6"[label="[a]",color=red];
                        "1,5   2"->"2,6   3   0"[label="[a]",color=red];
                        "1,5   2"->"4,6"[label="[a]",color=red];
                        "6,5"->"7,6"[label="[a]",color=red];
                        "7,6   2"->"8,3"[label="[a]",color=red];
                        "7,6   2"->"1,"[label="[ab]",color=violet];
                        "7,6   2"->"8,"[label="[ab]",color=violet];
                        "1,6   3"->"2,4   0"[label="[ab]",color=red];
                        "1,6   3"->"4,4"[label="[ab]",color=red];
                        "6,6"->"7,"[label="[ab]",color=violet];
                        "1,6   2"->"2,3   0"[label="[a]",color=red];
                        "1,6   2"->"4,3"[label="[a]",color=red];
                        "8,6"->"8,"[label="[]",color=red];
                        "2,6   3   0"->"3,4   1"[label="[ab]",color=red];
                        "4,6"->"5,"[label="[ab]",color=violet];
                        "7,6"->"8,"[label="[]",color=red];
                        ",4"->",5"[label="[a-z]",color=blue];
                        ",5"->",6"[label="[a]",color=blue];
                        ",6"->"8,"[label="[]",color=blue];
                        "2,"->"3,"[label="[a-z]",color=violet];
                        "3,"->"7,"[label="[a]",color=violet];
                        "3,"->"1,"[label="[a]",color=violet];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycle_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                7;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[ab]"];
                                5->6[label="[ab]"];
                                6->7[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "5,";
                        "4,7"->"5,"[label="[a-z]",color=violet];
                        "3,6"->"4,7"[label="[ab]",color=red];
                        "2,5"->"3,6"[label="[a]",color=red];
                        "1,4"->"2,5"[label="[a]",color=red];
                        "1,4"->"1,0   4"[label="[]",color=red];
                        "0,3"->"1,4"[label="[a]",color=red];
                        "4,3   7"->"1,4"[label="[a]",color=red];
                        "3,2   6"->"4,3   7"[label="[a]",color=red];
                        "2,1   5"->"3,2   6"[label="[a]",color=red];
                        "1,0   4"->"2,1   5"[label="[a]",color=red];
                        ",2"->"0,3"[label="[a]",color=blue];
                        ",1"->",2"[label="[a]",color=blue];
                        ",0"->",1"[label="[a]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_three_times_in_cycle_back() {
        $dotdescription1 = 'digraph example
                            {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example
                            {
                                0;
                                8;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[ab]"];
                                5->6[label="[ab]"];
                                6->7[label="[ab]"];
                                7->8[label="[ab]"];
                            }';
        $dotresult = 'digraph res 
                    {
                        ",0";
                        "5,";
                        "4,8"->"5,"[label="[a-z]",color=violet];
                        "3,7"->"4,8"[label="[ab]",color=red];
                        "2,6"->"3,7"[label="[a]",color=red];
                        "1,5"->"2,6"[label="[a]",color=red];
                        "0,4"->"1,5"[label="[ab]",color=red];
                        "4,4   8"->"1,5"[label="[a]",color=red];
                        "3,3   7"->"4,4   8"[label="[a]",color=red];
                        "2,2   6"->"3,3   7"[label="[a]",color=red];
                        "1,1   5"->"2,2   6"[label="[a]",color=red];
                        "1,1   5"->"(2,2   6)"[label="[a]",color=red];
                        "0,0"->"1,1   5"[label="[a]",color=red];
                        "4,0   4   8"->"1,1   5"[label="[a]",color=red];
                        "(3,3   7)"->"4,0   4   8"[label="[a]",color=red];
                        "(2,2   6)"->"(3,3   7)"[label="[a]",color=red];
                        ",3"->"0,4"[label="[a]",color=blue];
                        ",2"->",3"[label="[a]",color=blue];
                        ",1"->",2"[label="[a]",color=blue];
                        ",0"->",1"[label="[a]",color=blue];
                    }';

        $search = '
                    ';
        $replace = '\n';
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect_fa($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}