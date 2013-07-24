<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_go_round_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_one_uncapturing_transition() {
        $dotdescription = 'digraph example {
                        0;
                        4;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        2->3[label="[^]"];
                        0->3[label="[xy]"];
                        3->4[label="[a-c]"];
                    }';
        $dotresult = 'digraph example {
                    0;
                    4;
                    0->1[label="[a-z]"];
                    1->2[label="[0-9]"];
                    0->3[label="[xy]"];
                    3->4[label="[a-c]"];
                    2->4[label="[^a-c]"];
                }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($input->get_state_outtransitions(3)[1]);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccessful_merging_last_state_with_tag() {
        $dotdescription = 'digraph example {
                            0;
                            3;
                            0->1[label="[a-z]"];
                            1->2[label="[0-9]"];
                            0->2[label="[a-c]"];
                            2->3[label="[(/)/]"];
                            0->3[label="[xy]"];
                        }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        0->2[label="[a-c]"];
                        2->3[label="[(/)/]"];
                        0->3[label="[xy]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccessful_merging_last_state_with_assert() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                0->2[label="[a-c]"];
                                2->3[label="[\\Z]"];
                                0->3[label="[xy]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        0->2[label="[a-c]"];
                        2->3[label="[\\Z]"];
                        0->3[label="[xy]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($input);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_last_state() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                0->2[label="[a-c]"];
                                2->3[label="[]"];
                                0->3[label="[xy]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        0->2[label="[a-c]"];
                        2->3[label="[]"];
                        0->3[label="[xy]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_capturing_transitions_between_states() {
        $dotdescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                2->3[label="[]"];
                                2->3[label="[01]"];
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        4;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        2->3[label="[01]"];
                        2->4[label="[a-c]"];
                        0->3[label="[xy]"];
                        3->4[label="[a-c]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_several_transitions() {
        $dotdescription = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                2->3[label="[^]"];
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                                3->5[label="[01]"];
                            }';
        $dotresult = 'digraph example {
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
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccsessful_merging_state_for_intersection() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                2->3[label="[]"];
                                0->3[label="[xy]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        2->3[label="[]"];
                        0->3[label="[xy]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_state_outtransitions($number);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }
}