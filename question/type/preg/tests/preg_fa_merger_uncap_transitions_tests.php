<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_merger_uncap_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_merging_first_state() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1";
                        2;
                        "0   1"->2[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_capturing_transitions_between_states() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[^]"];
                                0->1[label="[a]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1";
                        2;
                        "0   1"->2[label="[^ab]"];
                        "0   1"->"0   1"[label="[a]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_several_outtransitions() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                2;
                                0->1[label="[\\A]"];
                                0->1[label="[^]"];
                                1->2[label="[0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1";
                        2;
                        "0   1"->2[label="[\\A0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_two_asserts() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                                2->3[label="[\\A0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        0;
                        3;
                        0->"1   2"[label="[0-9]"];
                        "1   2"->3[label="[\\A0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;

        $number = 0;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_one_state_several_times() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[^]"];
                                1->2[label="[]"];
                                2->3[label="[0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1   2";
                        3;
                        "0   1   2"->3[label="[^0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_BOTH;

        $number = 3;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_only_eps_transitions() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[]"];
                                1->2[label="[]"];
                                2->3[label="[]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1   2   3";
                        "0   1   2   3";
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_another_way_without_state_for_intersection() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                5;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                2->3[label="[^]"];
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                                3->5[label="[01]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        0;
                        5;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        0->3[label="[xy]"];
                        3->4[label="[a-c]"];
                        3->5[label="[01]"];
                        2->4[label="[^a-c]"];
                        2->5[label="[^01]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_different_ways_of_merging() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                5;
                                0->1[label="[$]"];
                                1->2[label="[0-9]"];
                                2->3[label="[^]"];
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                                3->5[label="[01]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1";
                        5;
                        "0   1"->2[label="[$0-9]"];
                        "0   1"->3[label="[xy]"];
                        3->4[label="[a-c]"];
                        3->5[label="[01]"];
                        2->4[label="[^a-c]"];
                        2->5[label="[^01]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_state_for_intersection() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                                2->3[label="[\\A0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        0;
                        3;
                        0->"1   2"[label="[0-9]"];
                        "1   2"->3[label="[\\A0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_with_cycle() {
        $dotdescription = 'digraph example 
                            {
                                0;
                                3;
                                0->1[label="[^]"];
                                1->2[label="[a-z]"];
                                2->3[label="[0-9]"];
                                3->1[label="[0-9]"];
                            }';
        $dotresult = 'digraph example 
                    {
                        "0   1";
                        3;
                        "0   1"->2[label="[^a-z]"];
                        2->3[label="[0-9]"];
                        3->"0   1"[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);

        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;

        $number = 2;
        
        $input->merger_uncapturing_transitions($transitiontype, $number);
        $result = $input->write_fa();
        $search = '
                    ';
        $replace = '\n';
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}