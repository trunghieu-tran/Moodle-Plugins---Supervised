<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_del_blind_states_test extends PHPUnit_Framework_TestCase {

    public function test_without_blind_states() {
        $dotdescription = 'digraph example {
                            0;
                            3;
                            0->1[label="[01]"];
                            1->2[label="[abc]"];
                            2->3[label="[01]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[01]"];
                        1->2[label="[abc]"];
                        2->3[label="[01]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_one_blind_state() {
        $dotdescription = 'digraph example {
                            0;
                            3;
                            0->1[label="[01]"];
                            1->2[label="[abc]"];
                            1->3[label="[01]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[01]"];
                        1->3[label="[01]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_several_linked_blind_states() {
        $dotdescription = 'digraph example {
                            0;
                            2;
                            0->1[label="[01]"];
                            1->2[label="[a]"];
                            1->3[label="[01]"];
                            3->4[label="[b]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_blind_cycle() {
        $dotdescription = 'digraph example {
                            0;
                            2;
                            0->1[label="[01]"];
                            1->2[label="[a]"];
                            1->3[label="[ab]"];
                            3->4[label="[cd]"];
                            4->3[label="[xy]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle() {
        $dotdescription = 'digraph example {
                            0;
                            2;
                            0->1[label="[01]"];
                            1->2[label="[a]"];
                            1->3[label="[ab]"];
                            3->4[label="[cd]"];
                            4->1[label="[xy]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                        1->3[label="[ab]"];
                        3->4[label="[cd]"];
                        4->1[label="[xy]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_del_merged_blind_states() {
        $dotdescription = 'digraph example {
                            0;
                            5;
                            0->"1   2   3"[label="[01]"];
                            "1   2   3"->4[label="[01]"];
                            0->4[label="[a-v]"];
                            4->5[label="[kmn]"];
                            "1   2   3"->6[label="[a]"];
                            "1   2   3"->7[label="[a]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        5;
                        0->"1   2   3"[label="[01]"];
                        "1   2   3"->4[label="[01]"];
                        0->4[label="[a-v]"];
                        4->5[label="[kmn]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_del_states_with_intersection() {
        $dotdescription = 'digraph example {
                            "0,";
                            ",5";
                            "0,"->"1,2"[label="[01]"];
                            "1,2"->",3"[label="[01]"];
                            "0,"->",3"[label="[a-v]"];
                            ",3"->",5"[label="[kmn]"];
                            "1,2"->"6,"[label="[a]"];
                            "1,2"->"8,7"[label="[a]"];
                        }';
        $dotresult = 'digraph example {
                        "0,";
                        ",5";
                        "0,"->"1,2"[label="[01]"];
                        "1,2"->",3"[label="[01]"];
                        "0,"->",3"[label="[a-v]"];
                        ",3"->",5"[label="[kmn]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_blind_states_from_start() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[a]"];
                                3->1[label="[ab]"];
                                4->3[label="[cd]"];
                                5->4[label="[xy]"];
                                6->5[label="[cd]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_blind_cycle_from_start() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[a]"];
                                3->1[label="[ab]"];
                                4->3[label="[cd]"];
                                5->4[label="[xy]"];
                                6->5[label="[cd]"];
                                4->6[label="[cd]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_from_start() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[a]"];
                                3->1[label="[ab]"];
                                4->3[label="[cd]"];
                                5->4[label="[xy]"];
                                6->5[label="[cd]"];
                                4->6[label="[cd]"];
                                1->6[label="[cd]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[01]"];
                        1->2[label="[a]"];
                        3->1[label="[ab]"];
                        4->3[label="[cd]"];
                        5->4[label="[xy]"];
                        6->5[label="[cd]"];
                        4->6[label="[cd]"];
                        1->6[label="[cd]"];
                    }';
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $input->del_blind_states();
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }
}