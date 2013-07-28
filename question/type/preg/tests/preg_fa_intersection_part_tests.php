<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_get_intersection_part_test extends PHPUnit_Framework_TestCase {

    public function test_no_intersection() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;4;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                3->4[label="[\\/]"];
                                4->4[label="[\\[\\]\\(\\)]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;4;
                                0->1[label="[01]"];
                                1->2[label="[4]"];
                                1->0[label="[,\\.!]"];
                                2->4[label="[*+]"];
                                2->3[label="[xy]"];
                                0->2[label="[-?]"];
                            }';
        $dotresult = 'digraph example {
                        "0,0";
                        "0,0";
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotresult);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('0,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_end() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-&,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[a]"];
                                2->3[label="[01]"];
                            }';
        $dotdirect = 'digraph example {
                            "0,";
                            "2,3";
                            "0,"->"1,"[label="[0-9]"];
                            "1,"->"4,"[label="[01]"];
                            "3,"->"4,"[label="[a]"];
                            "2,3"->"3,"[label="[\\-?,]"];
                        }';
        $dotresult = 'digraph res {
                        "0,";"0,0";
                        "2,3";
                        "0,"->"1,"[label = "[0123456789]", color = violet];
                        "2,3"->"3,"[label = "[-?,]", color = violet];
                        "1,"->"4,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                        "1,1"->"2,3"[label = "[abc&&a]", color = red];
                        "2,1"->"2,3"[label = "[abcdefghijklmnopqrstuvwxyz&&a]", color = red];
                        "0,0"->"1,1"[label = "[0123456789&&01]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('2,3', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_start() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[01]"];
                                1->2[label="[a-z]"];
                                2->2[label="[a-c]"];
                                1->1[label="[0-9]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[a-z]"];
                                1->2[label="[a-c]"];
                                0->2[label="[0-9]"];
                                1->1[label="[\\.,]"];
                                2->2[label="[ab]"];
                            }';
        $dotdirect = 'digraph example {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label="[01]"];
                        }';
        $dotresult = 'digraph res {
                        "0,";
                        "2,2";
                        "0,"->"1,0"[label = "[01]", color = violet];
                        "1,0"->"2,1"[label = "[abcdefghijklmnopqrstuvwxyz&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "1,0"->"1,2"[label = "[0123456789&&0123456789]", color = red];
                        "2,1"->"2,2"[label = "[abc&&abc]", color = red];
                        "1,2"->"2,2"[label = "[abcdefghijklmnopqrstuvwxyz&&ab]", color = red];
                        "2,2"->"2,2"[label = "[abc&&ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('1,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycles() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                0->2[label="[ab]"];
                                1->2[label="[ab]"];
                                2->1[label="[ab]"];
                            }';
        $dotdirect = 'digraph example {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label="[ab]"];
                        }';
        $dotresult = 'digraph res {
                        "0,";
                        "1,1";"1,2";
                        "0,"->"1,0"[label = "[ab]", color = violet];
                        "1,0"->"2,1"[label = "[ab&&ab]", color = red];
                        "1,0"->"2,2"[label = "[ab&&ab]", color = red];
                        "2,1"->"0,2"[label = "[ab&&ab]", color = red];
                        "2,2"->"0,1"[label = "[ab&&ab]", color = red];
                        "0,2"->"1,1"[label = "[ab&&ab]", color = red];
                        "0,1"->"1,2"[label = "[ab&&ab]", color = red];
                        "1,1"->"2,2"[label = "[ab&&ab]", color = red];
                        "1,2"->"2,1"[label = "[ab&&ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('1,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_unmerged_eps() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[a]"];
                                1->2[label="[(/)/]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[a]"];
                                1->2[label="[(/)/]"];
                            }';
        $dotdirect = 'digraph example {
                            "2,2";
                            "2,2";
                        }';
        $dotresult = 'digraph res {
                        "0,0";
                        "2,2";
                        "1,1"->"2,2"[label = "[(/)/]", color = red];
                        "0,0"->"1,1"[label = "[a&&a]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('2,2', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_merged_asserts() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[^a]"];
                                1->2[label="[$b]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[^a]"];
                                1->2[label="[b]"];
                            }';
        $dotdirect = 'digraph example {
                            "2,2";
                            "2,2";
                        }';
        $dotresult = 'digraph res {
                        "0,0";
                        "2,2";
                        "1,1"->"2,2"[label = "[$b&&b]", color = red];
                        "0,0"->"1,1"[label = "[^a&&a]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('2,2', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_unmerged_asserts() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[^]"];
                                1->2[label="[(/$/)]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[^a]"];
                                1->2[label="[b]"];
                            }';
        $dotdirect = 'digraph example {
                            "2,2";
                            "2,2";
                        }';
        $dotresult = 'digraph res {
                        "0,0";
                        "2,2";
                        "1,1"->"2,2"[label = "[(/$b/)]", color = blue, style = dotted];
                        "0,0"->"1,1"[label = "[^a]", color = blue, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('2,2', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                5;
                                0->1[label="[01]"];
                                1->2[label="[a-k]"];
                                1->3[label="[0-9]"];
                                2->4[label="[a-c]"];
                                3->4[label="[ab]"];
                                4->5[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[c-n]"];
                                1->1[label="[a-z]"];
                                1->2[label="[a-z]"];
                            }';
        $dotdirect = 'digraph example {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label="[01]"];
                        }';
        $dotresult = 'digraph res {
                        "0,";
                        "5,1";"5,2";
                        "0,"->"1,0"[label = "[01]", color = violet];
                        "1,0"->"2,1"[label = "[abcdefghijk&&cdefghijklmn]", color = red];
                        "2,1"->"4,1"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "2,1"->"4,2"[label = "[abc&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,1"->"5,1"[label = "[xy&&abcdefghijklmnopqrstuvwxyz]", color = red];
                        "4,1"->"5,2"[label = "[xy&&abcdefghijklmnopqrstuvwxyz]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('1,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_implicit_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[01]"];
                                1->2[label="[c-k]"];
                                2->3[label="[a-c]"];
                                3->4[label="[ab]"];
                                3->2[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[cd]"];
                                1->2[label="[a]"];
                                2->3[label="[ab]"];
                                2->1[label="[xy]"];
                            }';
        $dotdirect = 'digraph example {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label="[01]"];
                        }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,3";
                        "0,"->"1,0"[label = "[01]", color = violet];
                        "1,0"->"2,1"[label = "[cdefghijk&&cd]", color = red];
                        "2,1"->"3,2"[label = "[abc&&a]", color = red];
                        "3,2"->"4,3"[label = "[ab&&ab]", color = red];
                        "3,2"->"2,1"[label = "[xy&&xy]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($dotdirect);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('1,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->write_fa();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}