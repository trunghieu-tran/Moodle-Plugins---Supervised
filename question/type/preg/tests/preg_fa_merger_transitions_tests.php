<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_merger_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_merging_first_state() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        2;
                        "0   1"->2[label="[0-9]"];
                    }';
        
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[0]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccessful_merging_last_state_with_tag() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[()]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        2;
                        0->1[label="[0-9]"];
                        1->2[label="[()]"];
                    }';
        
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[1]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
}

    public function test_unsuccessful_merging_last_state_with_assert() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        2;
                        0->1[label="[0-9]"];
                        1->2[label="[^]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[1]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_last_state() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "1   2";
                        0->"1   2"[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[1]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_several_intotransitions() {
        $dotdescription = 'digraph example 
                            {
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[a-z]"];
                                2->3[label="[]"];
                                0->3[label="[xy]"];
                                1->3[label="[01]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "2   3";
                        0->1[label="[0-9]"];
                        1->"2   3"[label="[a-z]"];
                        0->"2   3"[label="[xy]"];
                        1->"2   3"[label="[01]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[2]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_several_outtransitions() {
        $dotdescription = 'digraph example 
                            {
                                3;
                                0->1[label="[\\A]"];
                                1->2[label="[01]"];
                                1->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        3;
                        "0   1"->2[label="[\\A01]"];
                        "0   1"->3[label="[\\Aa-z]\"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[0]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_start_of_cycle() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                                2->1[label="[a-z]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        2;
                        "0   1"->2[label="[0-9]"];
                        2->"0   1"[label="[a-z]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[0]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_end_of_cycle() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[]"];
                                1->0[label="[a-f]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "1   2";
                        0->"1   2"[label="[0-9]"];
                        "1   2"->0[label="[a-f]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[1]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_middle_of_implicit_cycle() {
        $dotdescription = 'digraph example 
                            {
                                2;
                                0->1[label="[^]"];
                                1->2[label="[x-z]"];
                                1->0[label="[a-f]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        2;
                        "0   1"->2[label="[^x-z]"];
                        "0   1"->"0   1"[label="[^a-f]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[1]->outtransitions[0];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_in_cycle() {
        $dotdescription = 'digraph example 
                            {
                                1;
                                0->1[label="[b]"];
                                0->0[label="[^]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        1;
                        0->1[label="[b]"];
                        0->0[label="[^]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $del = $input->states[0]->outtransitions[1];
        $input->merger_transitions($del);

        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}