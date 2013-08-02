<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_intersect_fa_test extends PHPUnit_Framework_TestCase {

    public function test_nessesary_merging() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;5;
                                0->1[label="[]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->4[label="[.]"];
                                3->4[label="[.]"];
                                2->5[label="[01]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                0->2[label="[]"];
                                0->3[label="[ab]"];
                                1->1[label="[<>]"];
                                1->3[label="[xy]"];
                                2->3[label="[cd]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1,";
                        "4,";
                        "0   1,"->"2,0   2"[label = "[0123456789]", color = violet];
                        "0   1,"->"3,"[label = "[abc]", color = violet];
                        "2,0   2"->"4,1"[label = "[dot&&01]", color = red];
                        "2,0   2"->"4,3"[label = "[dot&&abcd]", color = red];
                        "2,0   2"->"5,1"[label = "[01&&01]", color = red];
                        "3,"->"4,"[label = "[dot]", color = violet];
                        "4,1"->",1"[label = "[<>]", color = blue, style = dotted];
                        "4,1"->",3"[label = "[xy]", color = blue, style = dotted];
                        "4,3"->"4,"[label = "[]", color = red];
                        "5,1"->",1"[label = "[<>]", color = blue, style = dotted];
                        "5,1"->",3"[label = "[xy]", color = blue, style = dotted];
                        ",1"->",1"[label = "[<>]", color = blue, style = dotted];
                        ",1"->",3"[label = "[xy]", color = blue, style = dotted];
                        ",3"->"4,"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_blind() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-\\\\&,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[ab]"];
                                2->3[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";
                        "4,";
                        "1,"->"4,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                        "0,"->"1,"[label = "[0123456789]", color = violet];
                        "2,3"->"3,"[label = "[-\&,]", color = violet];
                        "1,1"->"2,3"[label = "[abc&&ab]", color = red];
                        "0,0"->"1,1"[label = "[0123456789&&01]", color = red];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_back_with_changing_state_for_inter() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        "0,";
                        "5,5";
                        "0,"->"1   3,0"[label = "[a]", color = violet];
                        "0,"->"1   3,"[label = "[a]", color = violet];
                        "1   3,0"->"2,2"[label = "[a&&a]", color = red];
                        "1   3,0"->"2,3"[label = "[a&&a]", color = red];
                        "1   3,0"->"4,2"[label = "[^a&&a]", color = red];
                        "1   3,0"->"4,3"[label = "[^a&&a]", color = red];
                        "2,2"->"1   3,4"[label = "[a&&a]", color = red];
                        "2,2"->"1   3,3"[label = "[a&&a]", color = red];
                        "2,2"->"4,4"[label = "[a&&a]", color = red];
                        "2,2"->"4,3"[label = "[a&&a]", color = red];
                        "2,3"->"1   3,5"[label = "[a&&a]", color = red];
                        "2,3"->"4,5"[label = "[a&&a]", color = red];
                        "4,2"->"5,4"[label = "[a&&a]", color = red];
                        "4,2"->"5,3"[label = "[a&&a]", color = red];
                        "4,2"->"6,4"[label = "[a&&a]", color = red];
                        "4,2"->"6,3"[label = "[a&&a]", color = red];
                        "4,3"->"5,5"[label = "[a&&a]", color = red];
                        "4,3"->"6,5"[label = "[a&&a]", color = red];
                        "1   3,4"->"2,3"[label = "[a&&a]", color = red];
                        "1   3,4"->"2,5"[label = "[a&&a]", color = red];
                        "1   3,4"->"4,3"[label = "[^a&&a]", color = red];
                        "1   3,4"->"4,5"[label = "[^a&&a]", color = red];
                        "1   3,3"->"2,5"[label = "[a&&a]", color = red];
                        "1   3,3"->"4,5"[label = "[^a&&a]", color = red];
                        "4,4"->"5,3"[label = "[a&&a]", color = red];
                        "4,4"->"5,5"[label = "[a&&a]", color = red];
                        "4,4"->"6,3"[label = "[a&&a]", color = red];
                        "4,4"->"6,5"[label = "[a&&a]", color = red];
                        "1   3,5"->"2,"[label = "[a]", color = violet];
                        "1   3,5"->"4,"[label = "[^a]", color = violet];
                        "4,5"->"5,"[label = "[a]", color = violet];
                        "4,5"->"6,"[label = "[a]", color = violet];
                        "5,4"->"5,3"[label = "[a&&a]", color = red];
                        "5,4"->"5,5"[label = "[a&&a]", color = red];
                        "5,4"->"6,3"[label = "[a&&a]", color = red];
                        "5,4"->"6,5"[label = "[a&&a]", color = red];
                        "5,3"->"5,5"[label = "[a&&a]", color = red];
                        "5,3"->"6,5"[label = "[a&&a]", color = red];
                        "6,4"->"6,3"[label = "[a&&a]", color = red];
                        "6,4"->"6,5"[label = "[a&&a]", color = red];
                        "6,3"->"6,5"[label = "[a&&a]", color = red];
                        "6,5"->"5,5"[label = "[]", color = red];
                        "2,5"->"1   3,"[label = "[a]", color = violet];
                        "2,5"->"4,"[label = "[a]", color = violet];
                        "2,"->"1   3,"[label = "[a]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "4,"->"5,"[label = "[a]", color = violet];
                        "4,"->"6,"[label = "[a]", color = violet];
                        "1   3,"->"2,"[label = "[a]", color = violet];
                        "1   3,"->"4,"[label = "[^a]", color = violet];
                        "5,"->"5,"[label = "[a]", color = violet];
                        "5,"->"6,"[label = "[a]", color = violet];
                        "5,"->"5,5"[label = "[]", color = violet];
                        "6,"->"6,"[label = "[a]", color = violet];
                        "6,"->"5,5"[label = "[]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '3', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_simple() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[b]"];
                                1->2[label="[a]"];
                                1->1[label="[]"];
                                0->2[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "2,";
                        "0,"->"1,0"[label = "[b]", color = violet];
                        "0,"->"2,"[label = "[a]", color = violet];
                        "1,0"->"2,1"[label = "[a&&ab]", color = red];
                        "2,1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",2"->"2,"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '1', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_branches() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[b]"];
                                1->2[label="[a]"];
                                0->2[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "2,2";
                        "0,0"->"1,1"[label = "[b&&ab]", color = red];
                        "0,0"->"2,1"[label = "[a&&ab]", color = red];
                        "1,1"->"2,2"[label = "[a&&ab]", color = red];
                        "2,1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",2"->"2,2"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_eps_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                3;
                                0->1[label="[b]"];
                                1->2[label="[]"];
                                2->1[label="[]"];
                                2->3[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "3,2";
                        "0,0"->"1   2,1"[label = "[b&&ab]", color = red];
                        "1   2,1"->"3,2"[label = "[a&&ab]", color = red];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_assert_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                3;
                                0->1[label="[b]"];
                                1->2[label="[^]"];
                                2->1[label="[^]"];
                                2->3[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "3,2";
                        "0,0"->"1   2,1"[label = "[b&&ab]", color = red];
                        "1   2,1"->"3,2"[label = "[^a&&ab]", color = red];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_start_implicent_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "5,";
                        "0,0"->"1,1"[label = "[abc&&ab]", color = red];
                        "1,1"->"2,2"[label = "[0123456789&&01]", color = red];
                        "2,2"->"3,"[label = "[a]", color = violet];
                        "3,"->"4,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "2,"->"3,"[label = "[a]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_three_time_in_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        ",0";
                        "5,";
                        "4,9"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,8"->"4,9"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "2,7"->"3,8"[label = "[a&&a]", color = red];
                        "1,6"->"2,7"[label = "[a&&a]", color = red];
                        "0,5"->"1,6"[label = "[abc&&a]", color = red];
                        "4,5   9"->"1,6"[label = "[a&&a]", color = red];
                        "3,4   8"->"4,5   9"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,3   7"->"3,4   8"[label = "[a&&a]", color = red];
                        "1,2   6"->"2,3   7"[label = "[a&&a]", color = red];
                        "1,2   6"->"(2,3   7)"[label = "[a&&a]", color = red];
                        "0,1"->"1,2   6"[label = "[abc&&a]", color = red];
                        "4,1   5   9"->"1,2   6"[label = "[a&&a]", color = red];
                        "3,0   4   8"->"4,1   5   9"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "(2,3   7)"->"3,0   4   8"[label = "[a&&a]", color = red];
                        ",4"->"0,5"[label = "[ab]", color = blue, style = dotted];
                        ",3"->",4"[label = "[a]", color = blue, style = dotted];
                        ",2"->",3"[label = "[a]", color = blue, style = dotted];
                        ",1"->",2"[label = "[a]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[a]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '4', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_time_in_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        ",0";
                        "5,8";
                        "4,7"->"5,8"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "3,6"->"4,7"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "2,5"->"3,6"[label = "[a&&dot]", color = red];
                        "1,4"->"2,5"[label = "[0123456789&&dot]", color = red];
                        "1,4"->"1,0   4"[label = "[]", color = red];
                        "0,3"->"1,4"[label = "[abc&&dot]", color = red];
                        "4,3   7"->"1,4"[label = "[a&&dot]", color = red];
                        "3,2   6"->"4,3   7"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "2,1   5"->"3,2   6"[label = "[a&&dot]", color = red];
                        "1,0   4"->"2,1   5"[label = "[0123456789&&dot]", color = red];
                        ",2"->"0,3"[label = "[dot]", color = blue, style = dotted];
                        ",1"->",2"[label = "[dot]", color = blue, style = dotted];
                        ",0"->",1"[label = "[dot]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '5', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_no_intersection() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $this->assertEquals($result, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_not_start() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[m]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[a-k]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "5,";
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,"->"2,0"[label = "[0123456789]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "2,0"->"3,1"[label = "[a&&ab]", color = red];
                        "3,1"->"4,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijk]", color = red];
                        "4,2"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,2"->"1,"[label = "[m]", color = violet];
                        "2,"->"3,"[label = "[a]", color = violet];
                        "3,"->"4,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_in_branch() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "7,";
                        "0,0"->"1,1"[label = "[abc&&ab]", color = red];
                        "1,1"->"2,2"[label = "[0123456789&&01]", color = red];
                        "2,2"->"4,"[label = "[a]", color = violet];
                        "4,"->"6,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "6,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "3,"->"5,"[label = "[012]", color = violet];
                        "5,"->"6,"[label = "[+=]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_three_times_back() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a]"];
                                4->5[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        "5,";
                        "4,5"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,4"->"4,5"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,3"->"3,4"[label = "[a&&a]", color = red];
                        "1,2"->"2,3"[label = "[a&&a]", color = red];
                        "1,2"->"(2,3)"[label = "[a&&a]", color = red];
                        "0,1"->"1,2"[label = "[abc&&a]", color = red];
                        "4,1   5"->"1,2"[label = "[a&&a]", color = red];
                        "3,0   4"->"4,1   5"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "(2,3)"->"3,0   4"[label = "[a&&a]", color = red];
                        ",0"->"0,1"[label = "[a]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '4', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_in_branch() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "7,";
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,"->"2,0"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "2,0"->"4,1"[label = "[a&&ab]", color = red];
                        "3,"->"5,"[label = "[012]", color = violet];
                        "5,"->"6,"[label = "[+=]", color = violet];
                        "6,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "4,1"->"6,2"[label = "[0&&01]", color = red];
                        "6,2"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "6,2"->"7,"[label = "[abc]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches_back() {
        $dotdescription1 = 'digraph example {
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
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "7,";
                        "6,2"->"7,"[label = "[abc]", color = violet];
                        "4,1"->"6,2"[label = "[0&&01]", color = red];
                        "5,1"->"6,2"[label = "[0123456789&&01]", color = red];
                        "2,0"->"4,1"[label = "[a&&ab]", color = red];
                        "3,0"->"5,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"2,0"[label = "[0123456789]", color = violet];
                        "1,"->"3,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "6,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                        "3,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '6', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches_asserts_back() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "7,";
                        "6,2"->"7,"[label = "[abc]", color = violet];
                        "4,1"->"6,2"[label = "[0&&01]", color = red];
                        "5,1"->"6,2"[label = "[0123456789&&01]", color = red];
                        "2,0"->"4,1"[label = "[a&&ab]", color = red];
                        "3,0"->"5,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"2,0"[label = "[0123456789]", color = violet];
                        "1,"->"3,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "6,"->"2,"[label = "[^0123456789]", color = violet];
                        "6,"->"3,"[label = "[^abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "6,"->"2,0"[label = "[^0123456789]", color = violet];
                        "6,"->"3,0"[label = "[^abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "3,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '6', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_cycles() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "7,";
                        "0,0"->"1,1"[label = "[abc&&ab]", color = red];
                        "1,1"->"2,2"[label = "[0123456789&&01]", color = red];
                        "2,2"->"4,3"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,3"->"6,"[label = "[0]", color = violet];
                        "4,3"->"1,"[label = "[m]", color = violet];
                        "6,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[012]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "3,"->"5,"[label = "[+=]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '0', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_start_states() {
        $dotdescription1 = 'digraph example {
                                0;
                                7;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                2->4[label="[a]"];
                                1->3[label="[012]"];
                                3->5[label="[+=]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->7[label="[0-9]"];
                                7->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";
                        "7,";
                        "7,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "5,"->"7,"[label = "[0123456789]", color = violet];
                        "5,"->"7,0"[label = "[0123456789]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "6,"->"7,0"[label = "[abc]", color = violet];
                        "3,"->"5,"[label = "[+=]", color = violet];
                        "4,3"->"6,"[label = "[0]", color = violet];
                        "4,3"->"1,"[label = "[m]", color = violet];
                        "1,"->"3,"[label = "[012]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "2,2"->"4,3"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,1"->"2,2"[label = "[0123456789&&01]", color = red];
                        "0,0"->"1,1"[label = "[abc&&ab]", color = red];
                        "7,0"->"1,1"[label = "[abcdefghijklmnopqrs&&ab]", color = red];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "6,";
                        "0,"->"1,0"[label = "[abc]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,0"->"2,1"[label = "[abc&&ab]", color = red];
                        "2,1"->"3,2"[label = "[012345as&&01]", color = red];
                        "3,2"->"4,"[label = "[+=]", color = violet];
                        "4,"->"5,"[label = "[0]", color = violet];
                        "4,"->"2,"[label = "[m]", color = violet];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                        "5,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "2,"->"3,"[label = "[012345as]", color = violet];
                        "1,"->"2,"[label = "[abc]", color = violet];
                        "3,"->"4,"[label = "[+=]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch_back() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "6,";
                        "5,2"->"6,"[label = "[0123456789]", color = violet];
                        "4,1"->"5,2"[label = "[0&&01]", color = red];
                        "3,0"->"4,1"[label = "[ab&&ab]", color = red];
                        "2,"->"3,"[label = "[012345as]", color = violet];
                        "2,"->"3,0"[label = "[012345as]", color = violet];
                        "1,"->"2,"[label = "[abc]", color = violet];
                        "4,"->"2,"[label = "[m]", color = violet];
                        "4,"->"5,"[label = "[0]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                        "5,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "3,"->"4,"[label = "[ab]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 5, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[.]"];
                                1->2[label="[.]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";",0";
                        "7,";
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,"->"2,0"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,0"->"4,1"[label = "[a&&dot]", color = red];
                        "3,"->"5,"[label = "[012]", color = violet];
                        "5,"->"6,"[label = "[+=]", color = violet];
                        "6,"->"1,"[label = "[abcdefghijklmnopqrs]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "4,1"->"6,2"[label = "[0&&dot]", color = red];
                        "4,1"->"1,2"[label = "[m&&dot]", color = red];
                        "6,2"->"1,0"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "6,2"->"7,0"[label = "[abc&&dot]", color = red];
                        "1,2"->"2,0"[label = "[0123456789&&dot]", color = red];
                        "1,2"->"3,0"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "1,0"->"2,1"[label = "[0123456789&&dot]", color = red];
                        "1,0"->"3,1"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "7,0"->",1"[label = "[dot]", color = blue, style = dotted];
                        "3,0"->"5,1"[label = "[012&&dot]", color = red];
                        "2,1"->"4,2"[label = "[a&&dot]", color = red];
                        "3,1"->"5,2"[label = "[012&&dot]", color = red];
                        "5,1"->"6,2"[label = "[+=&&dot]", color = red];
                        "4,2"->"6,0"[label = "[0&&dot]", color = red];
                        "4,2"->"1,0"[label = "[m&&dot]", color = red];
                        "5,2"->"6,0"[label = "[+=&&dot]", color = red];
                        "6,0"->"1,1"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "6,0"->"7,1"[label = "[abc&&dot]", color = red];
                        "1,1"->"2,2"[label = "[0123456789&&dot]", color = red];
                        "1,1"->"3,2"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "7,1"->",2"[label = "[dot]", color = blue, style = dotted];
                        "2,2"->"4,0"[label = "[a&&dot]", color = red];
                        "3,2"->"5,0"[label = "[012&&dot]", color = red];
                        "4,0"->"6,1"[label = "[0&&dot]", color = red];
                        "4,0"->"1,1"[label = "[m&&dot]", color = red];
                        "5,0"->"6,1"[label = "[+=&&dot]", color = red];
                        "6,1"->"1,2"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "6,1"->"7,2"[label = "[abc&&dot]", color = red];
                        "7,2"->"7,"[label = "[]", color = red];
                        ",1"->",2"[label = "[dot]", color = blue, style = dotted];
                        ",2"->",0"[label = "[dot]", color = blue, style = dotted];
                        ",2"->"7,"[label = "[]", color = blue, style = dotted];
                        ",0"->",1"[label = "[dot]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_intersection() {
        $dotdescription1 = 'digraph example {
                                0;
                                7;
                                0->1[label="[01]"];
                                1->2[label="[ab]"];
                                2->4[label="[a]"];
                                1->3[label="[a-z]"];
                                3->5[label="[012]"];
                                4->6[label="[0]"];
                                4->1[label="[m]"];
                                5->6[label="[+=]"];
                                6->1[label="[a-s]"];
                                6->7[label="[a-c]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[ab]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "7,2";
                        "6,1"->"7,2"[label = "[abc&&ab]", color = red];
                        "4,0"->"6,1"[label = "[0&&01]", color = red];
                        "2,2"->"4,0"[label = "[a&&dot]", color = red];
                        "1,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "0,0"->"1,1"[label = "[01&&01]", color = red];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 7, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_in_intersection() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[ab]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        "7,";
                        "6,2"->"7,"[label = "[abc]", color = violet];
                        "4,1"->"6,2"[label = "[ab&&ab]", color = red];
                        "5,1"->"6,2"[label = "[^a&&ab]", color = red];
                        "2,0"->"4,1"[label = "[0&&01]", color = red];
                        "3,0"->"5,1"[label = "[012&&01]", color = red];
                        "1,2"->"2,0"[label = "[0123456789&&dot]", color = red];
                        "1,2"->"3,0"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "0,1"->"1,2"[label = "[abc&&ab]", color = red];
                        ",0"->",1"[label = "[01]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[01]", color = blue, style = dotted];
                        ",2"->",0"[label = "[dot]", color = blue, style = dotted];
                        ",2"->"7,"[label = "[]", color = blue, style = dotted];
                        ",1"->",2"[label = "[ab]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 6, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots_back() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[.]"];
                                1->2[label="[.]"];
                                2->0[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";",0";
                        "7,2";
                        "6,1"->"7,2"[label = "[abc&&dot]", color = red];
                        "6,1"->"1,2"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "4,0"->"6,1"[label = "[0&&dot]", color = red];
                        "4,0"->"1,1"[label = "[m&&dot]", color = red];
                        "5,0"->"6,1"[label = "[+=&&dot]", color = red];
                        "2,2"->"4,0"[label = "[a&&dot]", color = red];
                        "3,2"->"5,0"[label = "[012&&dot]", color = red];
                        "1,1"->"2,2"[label = "[abcdefghijklmn&&dot]", color = red];
                        "1,1"->"3,2"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "0,0"->"1,1"[label = "[0123456789&&dot]", color = red];
                        "6,0"->"1,1"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "4,2"->"6,0"[label = "[0&&dot]", color = red];
                        "4,2"->"1,0"[label = "[m&&dot]", color = red];
                        "5,2"->"6,0"[label = "[+=&&dot]", color = red];
                        "2,1"->"4,2"[label = "[a&&dot]", color = red];
                        "3,1"->"5,2"[label = "[012&&dot]", color = red];
                        "1,0"->"2,1"[label = "[abcdefghijklmn&&dot]", color = red];
                        "1,0"->"3,1"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "0,2"->"1,0"[label = "[0123456789&&dot]", color = red];
                        "6,2"->"1,0"[label = "[abcdefghijklmnopqrs&&dot]", color = red];
                        "4,1"->"6,2"[label = "[0&&dot]", color = red];
                        "4,1"->"1,2"[label = "[m&&dot]", color = red];
                        "5,1"->"6,2"[label = "[+=&&dot]", color = red];
                        "2,0"->"4,1"[label = "[a&&dot]", color = red];
                        "3,0"->"5,1"[label = "[012&&dot]", color = red];
                        "1,2"->"2,0"[label = "[abcdefghijklmn&&dot]", color = red];
                        "1,2"->"3,0"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "0,1"->"1,2"[label = "[0123456789&&dot]", color = red];
                        ",1"->",2"[label = "[dot]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[dot]", color = blue, style = dotted];
                        ",0"->",1"[label = "[dot]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[dot]", color = blue, style = dotted];
                        ",2"->",0"[label = "[dot]", color = blue, style = dotted];
                        ",2"->"7,2"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 7, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_coping_with_intersection() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        "0,";"0,0";",0";
                        "9,";
                        "9,"->"2,"[label = "[0123456789]", color = violet];
                        "8,"->"9,"[label = "[0123456789]", color = violet];
                        "8,"->"1,"[label = "[0123456789]", color = violet];
                        "8,"->"1,0"[label = "[0123456789]", color = violet];
                        "2,"->"8,"[label = "[as]", color = violet];
                        "4,"->"8,"[label = "[ab]", color = violet];
                        "7,"->"8,"[label = "[0123456789]", color = violet];
                        "1,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,"->"4,"[label = "[abc]", color = violet];
                        "6,"->"7,"[label = "[0123456789]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "0,"->"1,0"[label = "[abc]", color = violet];
                        "5,9"->"6,"[label = "[0123456789]", color = violet];
                        "1,7"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,8"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&abc]", color = red];
                        "1,8"->"3,1"[label = "[ab&&abcdef]", color = red];
                        "0,6"->"1,7"[label = "[abc&&axy]", color = red];
                        "0,6"->"1,8"[label = "[abc&&dot]", color = red];
                        "8,6"->"1,8"[label = "[0123456789&&dot]", color = red];
                        "4,2"->"8,6"[label = "[ab&&abc]", color = red];
                        "4,5"->"8,6"[label = "[ab&&as01]", color = red];
                        "3,1"->"4,2"[label = "[abc&&ab]", color = red];
                        "3,7"->"4,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,4"->"4,5"[label = "[abc&&abcd]", color = red];
                        "1,0"->"3,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,6"->"3,7"[label = "[ab&&axy]", color = red];
                        "1,3"->"3,4"[label = "[ab&&dot]", color = red];
                        "1,3"->"2,4"[label = "[0123456789&&dot]", color = red];
                        "0,2"->"1,6"[label = "[abc&&abc]", color = red];
                        "0,5"->"1,6"[label = "[abc&&as01]", color = red];
                        "8,5"->"1,6"[label = "[0123456789&&as01]", color = red];
                        "0,1"->"1,3"[label = "[abc&&as]", color = red];
                        "2,4"->"8,5"[label = "[as&&abcd]", color = red];
                        "4,4"->"8,5"[label = "[ab&&abcd]", color = red];
                        "3,3"->"4,4"[label = "[abc&&dot]", color = red];
                        "1,1"->"3,3"[label = "[ab&&as]", color = red];
                        "0,0"->"1,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "0,8"->"1,1"[label = "[abc&&abcdef]", color = red];
                        ",2"->",6"[label = "[abc]", color = blue, style = dotted];
                        ",2"->"0,6"[label = "[abc]", color = blue, style = dotted];
                        ",5"->",6"[label = "[as01]", color = blue, style = dotted];
                        ",5"->"0,6"[label = "[as01]", color = blue, style = dotted];
                        ",1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->",3"[label = "[as]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[ab]", color = blue, style = dotted];
                        ",7"->",2"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",7"->"0,2"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",4"->",5"[label = "[abcd]", color = blue, style = dotted];
                        ",4"->"0,5"[label = "[abcd]", color = blue, style = dotted];
                        ",0"->",1"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",8"->",1"[label = "[abcdef]", color = blue, style = dotted];
                        ",8"->"0,1"[label = "[abcdef]", color = blue, style = dotted];
                        ",6"->",7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->",8"[label = "[dot]", color = blue, style = dotted];
                        ",6"->"0,8"[label = "[dot]", color = blue, style = dotted];
                        ",3"->",4"[label = "[dot]", color = blue, style = dotted];
                        "5,"->"6,"[label = "[0123456789]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 5, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_back() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-c]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        ",3";
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,"->"2,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,0"->"3,1"[label = "[a&&ab]", color = red];
                        "3,1"->"4,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,1"->"1,2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,2"->",3"[label = "[a]", color = blue, style = dotted];
                        "1,2"->"2,3   0"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "2,3   0"->"3,1"[label = "[a&&ab]", color = red];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '2', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_merged() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        "0,";
                        "5,9";
                        "0,"->"1,0"[label = "[abc]", color = violet];
                        "1,0"->"2,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,0"->"3,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,1"->"4,2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,1"->"4,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,2"->"1,3   0"[label = "[a&&a]", color = red];
                        "4,2"->"5,3"[label = "[a&&a]", color = red];
                        "1,3   0"->"2,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,3   0"->"3,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "5,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "2,4   1"->"4,5   2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,4   1"->"4,5   2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,5   2"->"1,6   3   0"[label = "[a&&a]", color = red];
                        "4,5   2"->"5,6"[label = "[a&&a]", color = red];
                        "1,6   3   0"->"2,7   4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,6   3   0"->"3,7   4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "5,6"->",7"[label = "[ab]", color = blue, style = dotted];
                        "2,7   4   1"->"4,8   5   2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,7   4   1"->"4,8   5   2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,8   5   2"->"1,9   6   3   0"[label = "[a&&a]", color = red];
                        "4,8   5   2"->"5,9"[label = "[a&&a]", color = red];
                        "1,9   6   3   0"->"2,7   4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,9   6   3   0"->"3,7   4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        ",4"->",5"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->",7"[label = "[ab]", color = blue, style = dotted];
                        ",7"->",8"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",8"->",9"[label = "[a]", color = blue, style = dotted];
                        ",9"->"5,9"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_and_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                3->4[label="[a]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                6;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1,0";
                        "4,6";
                        "0   1,0"->"2,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,1"->"3,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,2"->"4,3"[label = "[a&&a]", color = red];
                        "3,2"->"0   1,3   0"[label = "[a&&a]", color = red];
                        "4,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "0   1,3   0"->"2,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,4   1"->"3,5   2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,5   2"->"4,6"[label = "[a&&a]", color = red];
                        "3,5   2"->"0   1,6   3   0"[label = "[a&&a]", color = red];
                        "0   1,6   3   0"->"2,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        ",4"->",5"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->"4,6"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 1, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_in_both() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a-z]"];
                                3->4[label="[a]"];
                                3->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                6;
                                0->1[label="[]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1,";
                        "4,6";
                        "0   1,"->"2,0   1"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,0   1"->"3,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,2"->"4,3"[label = "[a&&a]", color = red];
                        "3,2"->"0   1,3"[label = "[a&&a]", color = red];
                        "4,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "0   1,3"->"2,4   0   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,4   0   1"->"3,5   2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,5   2"->"4,6"[label = "[a&&a]", color = red];
                        "3,5   2"->"0   1,6   3"[label = "[a&&a]", color = red];
                        "0   1,6   3"->"2,4   0   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        ",4"->",5"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->"4,6"[label = "[]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_same_length_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        "0,0";",0";
                        "5,";
                        "4,8"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,7"->"4,8"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,6"->"3,7"[label = "[a&&ab]", color = red];
                        "1,5"->"2,6"[label = "[a&&ab]", color = red];
                        "0,4"->"1,5"[label = "[abc&&ab]", color = red];
                        "4,4   8"->"1,5"[label = "[a&&ab]", color = red];
                        "3,3   7"->"4,4   8"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "2,2   6"->"3,3   7"[label = "[a&&a]", color = red];
                        "1,1   5"->"2,2   6"[label = "[a&&a]", color = red];
                        "1,1   5"->"(2,2   6)"[label = "[a&&a]", color = red];
                        "0,0"->"1,1   5"[label = "[abc&&a]", color = red];
                        "4,0   4   8"->"1,1   5"[label = "[a&&a]", color = red];
                        "(3,3   7)"->"4,0   4   8"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "(2,2   6)"->"(3,3   7)"[label = "[a&&a]", color = red];
                        ",3"->"0,4"[label = "[a]", color = blue, style = dotted];
                        ",2"->",3"[label = "[a]", color = blue, style = dotted];
                        ",1"->",2"[label = "[a]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycles_full_first() {
        $dotdescription1 = 'digraph example {
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
        $dotdescription2 = 'digraph example {
                                0;
                                6;
                                0->1[label="[ab]"];
                                1->2[label="[a-z]"];
                                2->3[label="[a]"];
                                3->4[label="[ab]"];
                                4->5[label="[a-z]"];
                                5->6[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "8,";
                        "0,"->"1,"[label = "[ab]", color = violet];
                        "1,"->"2,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"4,"[label = "[ab]", color = violet];
                        "1,"->"2,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,0"->"3,1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "4,"->"5,"[label = "[ab]", color = violet];
                        "5,"->"6,"[label = "[ab]", color = violet];
                        "6,"->"7,"[label = "[ab]", color = violet];
                        "7,"->"1,"[label = "[ab]", color = violet];
                        "7,"->"8,"[label = "[ab]", color = violet];
                        "3,1"->"7,2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,1"->"1,2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "7,2"->"1,3"[label = "[ab&&a]", color = red];
                        "7,2"->"8,3"[label = "[ab&&a]", color = red];
                        "1,2"->"2,3   0"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,2"->"4,3"[label = "[ab&&a]", color = red];
                        "1,3"->"2,4   0"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,3"->"4,4"[label = "[ab&&ab]", color = red];
                        "8,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "2,3   0"->"3,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "4,3"->"5,4"[label = "[ab&&ab]", color = red];
                        "2,4   0"->"3,5   1"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,4"->"5,5"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,4   1"->"7,5"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,4   1"->"1,5   2"[label = "[a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "5,4"->"6,5"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,5   1"->"7,6   2"[label = "[a&&a]", color = red];
                        "3,5   1"->"1,6   3"[label = "[a&&a]", color = red];
                        "5,5"->"6,6"[label = "[ab&&a]", color = red];
                        "7,5"->"1,6   2"[label = "[ab&&a]", color = red];
                        "7,5"->"8,6"[label = "[ab&&a]", color = red];
                        "1,5   2"->"2,6   3   0"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,5   2"->"4,6"[label = "[ab&&a]", color = red];
                        "6,5"->"7,6"[label = "[ab&&a]", color = red];
                        "7,6   2"->"1,3"[label = "[ab&&a]", color = red];
                        "7,6   2"->"8,3"[label = "[ab&&a]", color = red];
                        "1,6   3"->"2,4   0"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,6   3"->"4,4"[label = "[ab&&ab]", color = red];
                        "6,6"->"7,"[label = "[ab]", color = violet];
                        "1,6   2"->"2,3   0"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,6   2"->"4,3"[label = "[ab&&a]", color = red];
                        "8,6"->"8,"[label = "[]", color = red];
                        "2,6   3   0"->"3,4   1"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "4,6"->"5,"[label = "[ab]", color = violet];
                        "7,6"->"1,"[label = "[ab]", color = violet];
                        "7,6"->"8,"[label = "[ab]", color = violet];
                        ",4"->",5"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->"8,"[label = "[]", color = blue, style = dotted];
                        "2,"->"3,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,"->"7,"[label = "[a]", color = violet];
                        "3,"->"1,"[label = "[a]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 2, 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycle_back() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        ",0";
                        "5,";
                        "4,7"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,6"->"4,7"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,5"->"3,6"[label = "[a&&ab]", color = red];
                        "1,4"->"2,5"[label = "[a&&ab]", color = red];
                        "1,4"->"1,0   4"[label = "[]", color = red];
                        "0,3"->"1,4"[label = "[abc&&a]", color = red];
                        "4,3   7"->"1,4"[label = "[a&&a]", color = red];
                        "3,2   6"->"4,3   7"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "2,1   5"->"3,2   6"[label = "[a&&a]", color = red];
                        "1,0   4"->"2,1   5"[label = "[a&&a]", color = red];
                        ",2"->"0,3"[label = "[a]", color = blue, style = dotted];
                        ",1"->",2"[label = "[a]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_three_times_in_cycle_back() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-c]"];
                                1->2[label="[a]"];
                                2->3[label="[a]"];
                                3->4[label="[a-z]"];
                                4->5[label="[a-z]"];
                                4->1[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
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
        $dotresult = 'digraph res {
                        "0,0";",0";
                        "5,";
                        "4,8"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "3,7"->"4,8"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,6"->"3,7"[label = "[a&&ab]", color = red];
                        "1,5"->"2,6"[label = "[a&&ab]", color = red];
                        "0,4"->"1,5"[label = "[abc&&ab]", color = red];
                        "4,4   8"->"1,5"[label = "[a&&ab]", color = red];
                        "3,3   7"->"4,4   8"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "2,2   6"->"3,3   7"[label = "[a&&a]", color = red];
                        "1,1   5"->"2,2   6"[label = "[a&&a]", color = red];
                        "1,1   5"->"(2,2   6)"[label = "[a&&a]", color = red];
                        "0,0"->"1,1   5"[label = "[abc&&a]", color = red];
                        "4,0   4   8"->"1,1   5"[label = "[a&&a]", color = red];
                        "(3,3   7)"->"4,0   4   8"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "(2,2   6)"->"(3,3   7)"[label = "[a&&a]", color = red];
                        ",3"->"0,4"[label = "[a]", color = blue, style = dotted];
                        ",2"->",3"[label = "[a]", color = blue, style = dotted];
                        ",1"->",2"[label = "[a]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a]", color = blue, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, 4, 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_big_forward() {
        $dotdescription1 = 'digraph example {
                                0;
                                9;
                                0->1[label="[a-c]"];
                                1->2[label="[ab]"];
                                1->3[label="[ab]"];
                                2->8[label="[as]"];
                                1->5[label="[a-z]"];
                                3->4[label="[a-c]"];
                                4->8[label="[ab]"];
                                5->6[label="[^a]"];
                                7->8[label="[a-c]"];
                                6->7[label="[a-c]"];
                                8->9[label="[ab]"];
                                8->1[label="[ab]"];
                                9->2[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example {
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
                                9->9[label="[b-n]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "9,";
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "2,"->"8,"[label = "[as]", color = violet];
                        "3,"->"4,"[label = "[abc]", color = violet];
                        "5,0"->"6,1"[label = "[^a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,"->"9,"[label = "[ab]", color = violet];
                        "8,"->"1,"[label = "[ab]", color = violet];
                        "4,"->"8,"[label = "[ab]", color = violet];
                        "9,"->"2,"[label = "[ab]", color = violet];
                        "6,1"->"7,2"[label = "[abc&&ab]", color = red];
                        "6,1"->"7,3"[label = "[abc&&as]", color = red];
                        "7,2"->"8,6"[label = "[abc&&abc]", color = red];
                        "7,3"->"8,4"[label = "[abc&&dot]", color = red];
                        "8,6"->"9,7"[label = "[ab&&axy]", color = red];
                        "8,6"->"9,8"[label = "[ab&&dot]", color = red];
                        "8,6"->"1,7"[label = "[ab&&axy]", color = red];
                        "8,6"->"1,8"[label = "[ab&&dot]", color = red];
                        "8,4"->"9,5"[label = "[ab&&abcd]", color = red];
                        "8,4"->"1,5"[label = "[ab&&abcd]", color = red];
                        "9,7"->"2,9"[label = "[ab&&a]", color = red];
                        "9,7"->"2,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "9,8"->"2,9"[label = "[ab&&abc]", color = red];
                        "9,8"->"2,1"[label = "[ab&&abcdef]", color = red];
                        "1,7"->"2,9"[label = "[ab&&a]", color = red];
                        "1,7"->"2,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,7"->"3,9"[label = "[ab&&a]", color = red];
                        "1,7"->"3,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,7"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,7"->"5,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,8"->"2,9"[label = "[ab&&abc]", color = red];
                        "1,8"->"2,1"[label = "[ab&&abcdef]", color = red];
                        "1,8"->"3,9"[label = "[ab&&abc]", color = red];
                        "1,8"->"3,1"[label = "[ab&&abcdef]", color = red];
                        "1,8"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&abc]", color = red];
                        "1,8"->"5,1"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdef]", color = red];
                        "9,5"->"2,6"[label = "[ab&&as01]", color = red];
                        "1,5"->"2,6"[label = "[ab&&as01]", color = red];
                        "1,5"->"3,6"[label = "[ab&&as01]", color = red];
                        "1,5"->"5,6"[label = "[abcdefghijklmnopqrstuvwxyz&&as01]", color = red];
                        "2,9"->"8,"[label = "[as]", color = violet];
                        "2,2"->"8,6"[label = "[as&&abc]", color = red];
                        "2,1"->"8,2"[label = "[as&&ab]", color = red];
                        "2,1"->"8,3"[label = "[as&&as]", color = red];
                        "3,9"->"4,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "3,2"->"4,6"[label = "[abc&&abc]", color = red];
                        "5,9"->"6,"[label = "[^a]", color = violet];
                        "5,2"->"6,6"[label = "[^a&&abc]", color = red];
                        "3,1"->"4,2"[label = "[abc&&ab]", color = red];
                        "3,1"->"4,3"[label = "[abc&&as]", color = red];
                        "5,1"->"6,2"[label = "[^a&&ab]", color = red];
                        "5,1"->"6,3"[label = "[^a&&as]", color = red];
                        "2,6"->"8,7"[label = "[as&&axy]", color = red];
                        "2,6"->"8,8"[label = "[as&&dot]", color = red];
                        "3,6"->"4,7"[label = "[abc&&axy]", color = red];
                        "3,6"->"4,8"[label = "[abc&&dot]", color = red];
                        "5,6"->"6,7"[label = "[^a&&axy]", color = red];
                        "5,6"->"6,8"[label = "[^a&&dot]", color = red];
                        "8,2"->"9,6"[label = "[ab&&abc]", color = red];
                        "8,2"->"1,6"[label = "[ab&&abc]", color = red];
                        "8,3"->"9,4"[label = "[ab&&dot]", color = red];
                        "8,3"->"1,4"[label = "[ab&&dot]", color = red];
                        "4,9"->"8,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "4,6"->"8,7"[label = "[ab&&axy]", color = red];
                        "4,6"->"8,8"[label = "[ab&&dot]", color = red];
                        "6,6"->"7,7"[label = "[abc&&axy]", color = red];
                        "6,6"->"7,8"[label = "[abc&&dot]", color = red];
                        "4,2"->"8,6"[label = "[ab&&abc]", color = red];
                        "4,3"->"8,4"[label = "[ab&&dot]", color = red];
                        "6,2"->"7,6"[label = "[abc&&abc]", color = red];
                        "6,3"->"7,4"[label = "[abc&&dot]", color = red];
                        "8,7"->"9,9"[label = "[ab&&a]", color = red];
                        "8,7"->"9,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,7"->"1,9"[label = "[ab&&a]", color = red];
                        "8,7"->"1,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,8"->"9,9"[label = "[ab&&abc]", color = red];
                        "8,8"->"9,1"[label = "[ab&&abcdef]", color = red];
                        "8,8"->"1,9"[label = "[ab&&abc]", color = red];
                        "8,8"->"1,1"[label = "[ab&&abcdef]", color = red];
                        "4,7"->"8,9"[label = "[ab&&a]", color = red];
                        "4,7"->"8,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,8"->"8,9"[label = "[ab&&abc]", color = red];
                        "4,8"->"8,1"[label = "[ab&&abcdef]", color = red];
                        "6,7"->"7,9"[label = "[abc&&a]", color = red];
                        "6,7"->"7,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "6,8"->"7,9"[label = "[abc&&abc]", color = red];
                        "6,8"->"7,1"[label = "[abc&&abcdef]", color = red];
                        "9,6"->"2,7"[label = "[ab&&axy]", color = red];
                        "9,6"->"2,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"2,7"[label = "[ab&&axy]", color = red];
                        "1,6"->"2,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"3,7"[label = "[ab&&axy]", color = red];
                        "1,6"->"3,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"5,7"[label = "[abcdefghijklmnopqrstuvwxyz&&axy]", color = red];
                        "1,6"->"5,8"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "9,4"->"2,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"2,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"3,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"5,5"[label = "[abcdefghijklmnopqrstuvwxyz&&abcd]", color = red];
                        "8,9"->"9,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "8,9"->"1,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "7,7"->"8,9"[label = "[abc&&a]", color = red];
                        "7,7"->"8,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "7,8"->"8,9"[label = "[abc&&abc]", color = red];
                        "7,8"->"8,1"[label = "[abc&&abcdef]", color = red];
                        "7,6"->"8,7"[label = "[abc&&axy]", color = red];
                        "7,6"->"8,8"[label = "[abc&&dot]", color = red];
                        "7,4"->"8,5"[label = "[abc&&abcd]", color = red];
                        "9,9"->"2,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "9,9"->"9,"[label = "[]", color = red];
                        "9,2"->"2,6"[label = "[ab&&abc]", color = red];
                        "1,9"->"2,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "1,9"->"3,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "1,9"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&bcdefghijklmn]", color = red];
                        "1,2"->"2,6"[label = "[ab&&abc]", color = red];
                        "1,2"->"3,6"[label = "[ab&&abc]", color = red];
                        "1,2"->"5,6"[label = "[abcdefghijklmnopqrstuvwxyz&&abc]", color = red];
                        "9,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "9,1"->"2,3"[label = "[ab&&as]", color = red];
                        "1,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "1,1"->"2,3"[label = "[ab&&as]", color = red];
                        "1,1"->"3,2"[label = "[ab&&ab]", color = red];
                        "1,1"->"3,3"[label = "[ab&&as]", color = red];
                        "1,1"->"5,2"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,1"->"5,3"[label = "[abcdefghijklmnopqrstuvwxyz&&as]", color = red];
                        "8,1"->"9,2"[label = "[ab&&ab]", color = red];
                        "8,1"->"9,3"[label = "[ab&&as]", color = red];
                        "8,1"->"1,2"[label = "[ab&&ab]", color = red];
                        "8,1"->"1,3"[label = "[ab&&as]", color = red];
                        "7,9"->"8,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "7,1"->"8,2"[label = "[abc&&ab]", color = red];
                        "7,1"->"8,3"[label = "[abc&&as]", color = red];
                        "2,7"->"8,9"[label = "[as&&a]", color = red];
                        "2,7"->"8,2"[label = "[as&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "2,8"->"8,9"[label = "[as&&abc]", color = red];
                        "2,8"->"8,1"[label = "[as&&abcdef]", color = red];
                        "3,7"->"4,9"[label = "[abc&&a]", color = red];
                        "3,7"->"4,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,8"->"4,9"[label = "[abc&&abc]", color = red];
                        "3,8"->"4,1"[label = "[abc&&abcdef]", color = red];
                        "5,7"->"6,9"[label = "[^a&&a]", color = red];
                        "5,7"->"6,2"[label = "[^a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "5,8"->"6,9"[label = "[^a&&abc]", color = red];
                        "5,8"->"6,1"[label = "[^a&&abcdef]", color = red];
                        "2,5"->"8,6"[label = "[as&&as01]", color = red];
                        "3,5"->"4,6"[label = "[abc&&as01]", color = red];
                        "5,5"->"6,6"[label = "[^a&&as01]", color = red];
                        "8,5"->"9,6"[label = "[ab&&as01]", color = red];
                        "8,5"->"1,6"[label = "[ab&&as01]", color = red];
                        "2,3"->"8,4"[label = "[as&&dot]", color = red];
                        "3,3"->"4,4"[label = "[abc&&dot]", color = red];
                        "5,3"->"6,4"[label = "[^a&&dot]", color = red];
                        "9,3"->"2,4"[label = "[ab&&dot]", color = red];
                        "1,3"->"2,4"[label = "[ab&&dot]", color = red];
                        "1,3"->"3,4"[label = "[ab&&dot]", color = red];
                        "1,3"->"5,4"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "4,1"->"8,2"[label = "[ab&&ab]", color = red];
                        "4,1"->"8,3"[label = "[ab&&as]", color = red];
                        "6,9"->"7,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "4,4"->"8,5"[label = "[ab&&abcd]", color = red];
                        "6,4"->"7,5"[label = "[abc&&abcd]", color = red];
                        "2,4"->"8,5"[label = "[as&&abcd]", color = red];
                        "3,4"->"4,5"[label = "[abc&&abcd]", color = red];
                        "5,4"->"6,5"[label = "[^a&&abcd]", color = red];
                        "7,5"->"8,6"[label = "[abc&&as01]", color = red];
                        "4,5"->"8,6"[label = "[ab&&as01]", color = red];
                        "6,5"->"7,6"[label = "[abc&&as01]", color = red];
                        "5,"->"6,"[label = "[^a]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "7,"->"8,"[label = "[abc]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '5', 0);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_big_back() {
        $dotdescription1 = 'digraph example {
                                0;
                                9;
                                0->1[label="[a-c]"];
                                1->2[label="[ab]"];
                                1->3[label="[ab]"];
                                2->8[label="[as]"];
                                1->5[label="[a-z]"];
                                3->4[label="[a-c]"];
                                4->8[label="[ab]"];
                                5->6[label="[^a]"];
                                7->8[label="[a-c]"];
                                6->7[label="[a-c]"];
                                8->9[label="[ab]"];
                                8->1[label="[ab]"];
                                9->2[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example {
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
                                9->9[label="[b-n]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";",0";
                        "9,";
                        "9,"->"2,"[label = "[ab]", color = violet];
                        "9,"->"2,0"[label = "[ab]", color = violet];
                        "8,"->"9,"[label = "[ab]", color = violet];
                        "8,"->"1,"[label = "[ab]", color = violet];
                        "8,"->"1,0"[label = "[ab]", color = violet];
                        "8,"->"9,0"[label = "[ab]", color = violet];
                        "2,"->"8,"[label = "[as]", color = violet];
                        "2,"->"8,0"[label = "[as]", color = violet];
                        "4,"->"8,"[label = "[ab]", color = violet];
                        "4,"->"8,0"[label = "[ab]", color = violet];
                        "7,"->"8,"[label = "[abc]", color = violet];
                        "7,"->"8,0"[label = "[abc]", color = violet];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"5,0"[label = "[abcdefghijklmnopqrstuvwxyz]", color = violet];
                        "1,"->"2,0"[label = "[ab]", color = violet];
                        "1,"->"3,0"[label = "[ab]", color = violet];
                        "3,"->"4,"[label = "[abc]", color = violet];
                        "3,"->"4,0"[label = "[abc]", color = violet];
                        "6,"->"7,"[label = "[abc]", color = violet];
                        "6,"->"7,0"[label = "[abc]", color = violet];
                        "0,"->"1,"[label = "[abc]", color = violet];
                        "0,"->"1,0"[label = "[abc]", color = violet];
                        "5,9"->"6,"[label = "[^a]", color = violet];
                        "1,9"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&bcdefghijklmn]", color = red];
                        "1,9"->"3,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "1,7"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "1,7"->"2,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,7"->"3,9"[label = "[ab&&a]", color = red];
                        "1,7"->"3,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,7"->"5,2"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,8"->"5,9"[label = "[abcdefghijklmnopqrstuvwxyz&&abc]", color = red];
                        "1,8"->"3,9"[label = "[ab&&abc]", color = red];
                        "1,8"->"3,1"[label = "[ab&&abcdef]", color = red];
                        "1,8"->"5,1"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdef]", color = red];
                        "1,8"->"2,1"[label = "[ab&&abcdef]", color = red];
                        "0,9"->"1,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "0,7"->"1,9"[label = "[abc&&a]", color = red];
                        "0,7"->"1,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "0,8"->"1,9"[label = "[abc&&abc]", color = red];
                        "0,8"->"1,1"[label = "[abc&&abcdef]", color = red];
                        "8,9"->"1,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "8,7"->"1,9"[label = "[ab&&a]", color = red];
                        "8,7"->"9,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,7"->"1,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,8"->"1,9"[label = "[ab&&abc]", color = red];
                        "8,8"->"9,1"[label = "[ab&&abcdef]", color = red];
                        "8,8"->"1,1"[label = "[ab&&abcdef]", color = red];
                        "0,6"->"1,7"[label = "[abc&&axy]", color = red];
                        "0,6"->"1,8"[label = "[abc&&dot]", color = red];
                        "8,6"->"1,7"[label = "[ab&&axy]", color = red];
                        "8,6"->"1,8"[label = "[ab&&dot]", color = red];
                        "8,6"->"9,7"[label = "[ab&&axy]", color = red];
                        "8,6"->"9,8"[label = "[ab&&dot]", color = red];
                        "2,7"->"8,9"[label = "[as&&a]", color = red];
                        "2,7"->"8,2"[label = "[as&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "2,8"->"8,9"[label = "[as&&abc]", color = red];
                        "2,8"->"8,1"[label = "[as&&abcdef]", color = red];
                        "4,9"->"8,9"[label = "[ab&&bcdefghijklmn]", color = red];
                        "4,7"->"8,9"[label = "[ab&&a]", color = red];
                        "4,7"->"8,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,8"->"8,9"[label = "[ab&&abc]", color = red];
                        "4,8"->"8,1"[label = "[ab&&abcdef]", color = red];
                        "7,9"->"8,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "7,7"->"8,9"[label = "[abc&&a]", color = red];
                        "7,7"->"8,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "7,8"->"8,9"[label = "[abc&&abc]", color = red];
                        "7,8"->"8,1"[label = "[abc&&abcdef]", color = red];
                        "2,6"->"8,7"[label = "[as&&axy]", color = red];
                        "2,6"->"8,8"[label = "[as&&dot]", color = red];
                        "4,6"->"8,7"[label = "[ab&&axy]", color = red];
                        "4,6"->"8,8"[label = "[ab&&dot]", color = red];
                        "7,6"->"8,7"[label = "[abc&&axy]", color = red];
                        "7,6"->"8,8"[label = "[abc&&dot]", color = red];
                        "2,2"->"8,6"[label = "[as&&abc]", color = red];
                        "2,5"->"8,6"[label = "[as&&as01]", color = red];
                        "4,2"->"8,6"[label = "[ab&&abc]", color = red];
                        "4,5"->"8,6"[label = "[ab&&as01]", color = red];
                        "7,2"->"8,6"[label = "[abc&&abc]", color = red];
                        "7,5"->"8,6"[label = "[abc&&as01]", color = red];
                        "9,6"->"2,7"[label = "[ab&&axy]", color = red];
                        "9,6"->"2,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"2,7"[label = "[ab&&axy]", color = red];
                        "1,6"->"2,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"3,7"[label = "[ab&&axy]", color = red];
                        "1,6"->"3,8"[label = "[ab&&dot]", color = red];
                        "1,6"->"5,7"[label = "[abcdefghijklmnopqrstuvwxyz&&axy]", color = red];
                        "1,6"->"5,8"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "3,9"->"4,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "3,7"->"4,9"[label = "[abc&&a]", color = red];
                        "3,7"->"4,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "3,8"->"4,9"[label = "[abc&&abc]", color = red];
                        "3,8"->"4,1"[label = "[abc&&abcdef]", color = red];
                        "3,6"->"4,7"[label = "[abc&&axy]", color = red];
                        "3,6"->"4,8"[label = "[abc&&dot]", color = red];
                        "6,9"->"7,9"[label = "[abc&&bcdefghijklmn]", color = red];
                        "6,7"->"7,9"[label = "[abc&&a]", color = red];
                        "6,7"->"7,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "6,8"->"7,9"[label = "[abc&&abc]", color = red];
                        "6,8"->"7,1"[label = "[abc&&abcdef]", color = red];
                        "6,6"->"7,7"[label = "[abc&&axy]", color = red];
                        "6,6"->"7,8"[label = "[abc&&dot]", color = red];
                        "9,2"->"2,6"[label = "[ab&&abc]", color = red];
                        "9,5"->"2,6"[label = "[ab&&as01]", color = red];
                        "1,2"->"2,6"[label = "[ab&&abc]", color = red];
                        "1,2"->"3,6"[label = "[ab&&abc]", color = red];
                        "1,2"->"5,6"[label = "[abcdefghijklmnopqrstuvwxyz&&abc]", color = red];
                        "1,5"->"2,6"[label = "[ab&&as01]", color = red];
                        "1,5"->"3,6"[label = "[ab&&as01]", color = red];
                        "1,5"->"5,6"[label = "[abcdefghijklmnopqrstuvwxyz&&as01]", color = red];
                        "3,2"->"4,6"[label = "[abc&&abc]", color = red];
                        "3,5"->"4,6"[label = "[abc&&as01]", color = red];
                        "6,2"->"7,6"[label = "[abc&&abc]", color = red];
                        "6,5"->"7,6"[label = "[abc&&as01]", color = red];
                        "9,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "9,1"->"2,3"[label = "[ab&&as]", color = red];
                        "9,7"->"2,2"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "1,1"->"3,2"[label = "[ab&&ab]", color = red];
                        "1,1"->"5,2"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "1,1"->"5,3"[label = "[abcdefghijklmnopqrstuvwxyz&&as]", color = red];
                        "1,1"->"2,3"[label = "[ab&&as]", color = red];
                        "1,1"->"3,3"[label = "[ab&&as]", color = red];
                        "9,4"->"2,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"2,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"3,5"[label = "[ab&&abcd]", color = red];
                        "1,4"->"5,5"[label = "[abcdefghijklmnopqrstuvwxyz&&abcd]", color = red];
                        "3,1"->"4,2"[label = "[abc&&ab]", color = red];
                        "3,1"->"4,3"[label = "[abc&&as]", color = red];
                        "3,4"->"4,5"[label = "[abc&&abcd]", color = red];
                        "6,1"->"7,2"[label = "[abc&&ab]", color = red];
                        "6,1"->"7,3"[label = "[abc&&as]", color = red];
                        "6,4"->"7,5"[label = "[abc&&abcd]", color = red];
                        "8,2"->"9,6"[label = "[ab&&abc]", color = red];
                        "8,2"->"1,6"[label = "[ab&&abc]", color = red];
                        "8,5"->"9,6"[label = "[ab&&as01]", color = red];
                        "8,5"->"1,6"[label = "[ab&&as01]", color = red];
                        "0,2"->"1,6"[label = "[abc&&abc]", color = red];
                        "0,5"->"1,6"[label = "[abc&&as01]", color = red];
                        "5,7"->"6,9"[label = "[^a&&a]", color = red];
                        "5,7"->"6,2"[label = "[^a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "5,8"->"6,9"[label = "[^a&&abc]", color = red];
                        "5,8"->"6,1"[label = "[^a&&abcdef]", color = red];
                        "5,6"->"6,7"[label = "[^a&&axy]", color = red];
                        "5,6"->"6,8"[label = "[^a&&dot]", color = red];
                        "5,2"->"6,6"[label = "[^a&&abc]", color = red];
                        "5,5"->"6,6"[label = "[^a&&as01]", color = red];
                        "8,1"->"9,2"[label = "[ab&&ab]", color = red];
                        "8,1"->"1,2"[label = "[ab&&ab]", color = red];
                        "8,1"->"1,3"[label = "[ab&&as]", color = red];
                        "8,1"->"9,3"[label = "[ab&&as]", color = red];
                        "8,4"->"9,5"[label = "[ab&&abcd]", color = red];
                        "8,4"->"1,5"[label = "[ab&&abcd]", color = red];
                        "0,1"->"1,2"[label = "[abc&&ab]", color = red];
                        "0,1"->"1,3"[label = "[abc&&as]", color = red];
                        "0,4"->"1,5"[label = "[abc&&abcd]", color = red];
                        "5,1"->"6,2"[label = "[^a&&ab]", color = red];
                        "5,1"->"6,3"[label = "[^a&&as]", color = red];
                        "5,4"->"6,5"[label = "[^a&&abcd]", color = red];
                        "8,0"->"9,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,0"->"1,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "0,0"->"1,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "8,3"->"9,4"[label = "[ab&&dot]", color = red];
                        "8,3"->"1,4"[label = "[ab&&dot]", color = red];
                        "0,3"->"1,4"[label = "[abc&&dot]", color = red];
                        "1,0"->"3,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,0"->"5,1"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,0"->"2,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,3"->"3,4"[label = "[ab&&dot]", color = red];
                        "1,3"->"5,4"[label = "[abcdefghijklmnopqrstuvwxyz&&dot]", color = red];
                        "1,3"->"2,4"[label = "[ab&&dot]", color = red];
                        "5,0"->"6,1"[label = "[^a&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "5,3"->"6,4"[label = "[^a&&dot]", color = red];
                        "2,1"->"8,2"[label = "[as&&ab]", color = red];
                        "2,1"->"8,3"[label = "[as&&as]", color = red];
                        "4,1"->"8,2"[label = "[ab&&ab]", color = red];
                        "4,1"->"8,3"[label = "[ab&&as]", color = red];
                        "7,1"->"8,2"[label = "[abc&&ab]", color = red];
                        "7,1"->"8,3"[label = "[abc&&as]", color = red];
                        "2,4"->"8,5"[label = "[as&&abcd]", color = red];
                        "4,4"->"8,5"[label = "[ab&&abcd]", color = red];
                        "7,4"->"8,5"[label = "[abc&&abcd]", color = red];
                        "2,0"->"8,1"[label = "[as&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,0"->"8,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "7,0"->"8,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "2,3"->"8,4"[label = "[as&&dot]", color = red];
                        "4,3"->"8,4"[label = "[ab&&dot]", color = red];
                        "7,3"->"8,4"[label = "[abc&&dot]", color = red];
                        "9,0"->"2,1"[label = "[ab&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "9,8"->"2,1"[label = "[ab&&abcdef]", color = red];
                        "3,0"->"4,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "6,0"->"7,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "9,3"->"2,4"[label = "[ab&&dot]", color = red];
                        "3,3"->"4,4"[label = "[abc&&dot]", color = red];
                        "6,3"->"7,4"[label = "[abc&&dot]", color = red];
                        ",9"->",9"[label = "[bcdefghijklmn]", color = blue, style = dotted];
                        ",9"->"0,9"[label = "[bcdefghijklmn]", color = blue, style = dotted];
                        ",9"->"9,"[label = "[]", color = blue, style = dotted];
                        ",7"->",9"[label = "[a]", color = blue, style = dotted];
                        ",7"->",2"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",7"->"0,9"[label = "[a]", color = blue, style = dotted];
                        ",7"->"0,2"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",8"->",9"[label = "[abc]", color = blue, style = dotted];
                        ",8"->",1"[label = "[abcdef]", color = blue, style = dotted];
                        ",8"->"0,9"[label = "[abc]", color = blue, style = dotted];
                        ",8"->"0,1"[label = "[abcdef]", color = blue, style = dotted];
                        ",6"->",7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->",8"[label = "[dot]", color = blue, style = dotted];
                        ",6"->"0,7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->"0,8"[label = "[dot]", color = blue, style = dotted];
                        ",2"->",6"[label = "[abc]", color = blue, style = dotted];
                        ",2"->"0,6"[label = "[abc]", color = blue, style = dotted];
                        ",5"->",6"[label = "[as01]", color = blue, style = dotted];
                        ",5"->"0,6"[label = "[as01]", color = blue, style = dotted];
                        ",1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->",3"[label = "[as]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->"0,3"[label = "[as]", color = blue, style = dotted];
                        ",4"->",5"[label = "[abcd]", color = blue, style = dotted];
                        ",4"->"0,5"[label = "[abcd]", color = blue, style = dotted];
                        ",0"->",1"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[abcdefghijklmnopqrstuvwxyz]", color = blue, style = dotted];
                        ",3"->",4"[label = "[dot]", color = blue, style = dotted];
                        ",3"->"0,4"[label = "[dot]", color = blue, style = dotted];
                        "5,"->"6,"[label = "[^a]", color = violet];
                        "5,"->"6,0"[label = "[^a]", color = violet];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());

        $resultautomata = $firstautomata->intersect($secondautomata, '5', 1);
        $result = $resultautomata->write_fa();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}