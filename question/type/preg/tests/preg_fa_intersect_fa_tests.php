<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

class qtype_preg_fa_copy_branches_test extends PHPUnit_Framework_TestCase {

     public function test_copy_whole_branch() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "1,";"4,";
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0-9]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_impliciment_cycle() {
        $sourcedescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[ab]"];
                                0->2[label="[0-9]"];
                                1->3[label="[.]"];
                                2->3[label="[01]"];
                                3->0[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "1,";"3,";
                        "0,"->"1,"[label = "[ab]", color = violet];
                        "0,"->"2,"[label = "[0-9]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "3,"->"0,"[label = "[a]", color = violet];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_cycle_end() {
        $sourcedescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->1[label="[0-9]"];
                                1->2[label="[a]"];
                            }';
        $dotresult = 'digraph example {
                        "0,";
                        "1,";
                        "0,"->"1,"[label="[ab]"];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($direct, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_not_empty_direct() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[abc]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                    }';
        $directdescription = 'digraph example {
                                "0,";
                                "1,";
                                "0,"->"1,"[label="[df]"];
                            }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('4', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_direct_has_states_for_coping() {
        $sourcedescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "2,";
                        "0,"->"1,"[label = "[ab]", color = violet];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "2,"->"0,"[label = "[ab]", color = violet];
                    }';
        $directdescription = 'digraph example {
                                "0,";
                                "1,";
                                "0,"->"1,"[label="[ab]"];
                            }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_not_nessesary() {
        $sourcedescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotresult = 'digraph example {
                        "0,";
                        "2,";
                        "0,"->"1,"[label="[ab]"];
                        "1,"->"2,"[label="[ab]"];
                        "2,"->"0,"[label="[ab]"];
                    }';
        $directdescription = 'digraph example {
                                "0,";
                                "2,";
                                "0,"->"1,"[label="[ab]"];
                                "1,"->"2,"[label="[ab]"];
                                "2,"->"0,"[label="[ab]"];
                            }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('0', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($direct, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_back() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";
                        "3,"->"4,"[label = "[a]", color = violet];
                        "1,"->"3,"[label = "[abc]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0-9]", color = violet];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('0', $numbers);
        $oldfront = array(array_search('4', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 1);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_second_automata() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        ",1";",4";
                        ",0"->",1"[label = "[df]", color = blue, style = dotted];
                        ",0"->",2"[label = "[0-9]", color = blue, style = dotted];
                        ",2"->",3"[label = "[01]", color = blue, style = dotted];
                        ",3"->",4"[label = "[a]", color = blue, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_second_cycle() {
        $sourcedescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[ab]"];
                                0->2[label="[0-9]"];
                                1->3[label="[.]"];
                                2->3[label="[01]"];
                                3->0[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        ",1";",3";
                        ",0"->",1"[label = "[ab]", color = blue, style = dotted];
                        ",0"->",2"[label = "[0-9]", color = blue, style = dotted];
                        ",2"->",3"[label = "[01]", color = blue, style = dotted];
                        ",3"->",0"[label = "[a]", color = blue, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_not_empty_second() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        ",4";
                        ",0"->",1"[label = "[df]", color = blue, style = dotted];
                        ",0"->",2"[label = "[0-9]", color = blue, style = dotted];
                        ",1"->",3"[label = "[abc]", color = blue, style = dotted];
                        ",2"->",3"[label = "[01]", color = blue, style = dotted];
                        ",3"->",4"[label = "[a]", color = blue, style = dotted];
                    }';
        $directdescription = 'digraph example {
                                ",0";
                                ",1";
                                ",0"->",1"[label="[df]"];
                            }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($directdescription, $origin);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('4', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_not_nessesary_second() {
        $sourcedescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                                2->0[label="[ab]"];
                            }';
        $dotresult = 'digraph example {
                        ",0";
                        ",2";
                        ",0"->",1"[label="[ab]"];
                        ",1"->",2"[label="[ab]"];
                        ",2"->",0"[label="[ab]"];
                    }';
        $directdescription = 'digraph example {
                                ",0";
                                ",2";
                                ",0"->",1"[label="[ab]"];
                                ",1"->",2"[label="[ab]"];
                                ",2"->",0"[label="[ab]"];
                            }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $direct->read_fa($directdescription, $origin);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('0', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult, $origin);
        $this->assertEquals($direct, $result, 'Result automata is not equal to expected');
    }
}

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
                    1->3[label="[^0-9]"]
                    0->3[label="[xy]"];
                    3->4[label="[a-c]"];
                }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
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
        $outtransitions = $input->get_adjacent_transitions($number, true);
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
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
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
        $outtransitions = $input->get_adjacent_transitions($number, true);
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
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                                2->3[label="[01]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        4;
                        0->1[label="[a-z]"];
                        1->2[label="[0-9]"];
                        3->4[label="[a-c]"];
                        2->"/3"[label="[01]"];
                        "/3"->4[label="[a-c]"];
                        2->4[label="[a-c]"];
                        0->3[label="[xy]"];
                        0->"/3"[label="[xy]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
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
                                2->3[label="[$]"];
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
                        2->4[label="[$a-c]"];
                        2->5[label="[$01]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
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
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->go_round_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }
}

class qtype_preg_fa_inter_transitions_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_handling_options();
            $options->preserveallnodes = true;
        }
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        return $lexer;
    }

    public function test_characters_diapason_and_single() {
        $lexer = $this->create_lexer('[a-z][cd]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[a-z]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[cd]"];
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);

        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps() {
        $leaf1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $resulttran = $transition1->intersect($transition2);
        $restran = new qtype_preg_nfa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_and_capturing() {
        $lexer = $this->create_lexer('[a-z]');
        $leaf1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf2 = $lexer->nextToken()->value;
        $restran = new qtype_preg_nfa_transition(0, $leaf2, 1);     //0->1[label="[(a-z)]"];
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[a-z]"];
        $transition1->subpatt_start[] = $leaf;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[()]"];
        $restran->subpatt_start[] = $leaf;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_with_tags() {
        $leaf1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $subpatt1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $subpatt2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $restran = new qtype_preg_nfa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);//0->1[label="[()]"];
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[(]"];
        $transition1->subpatt_start[] = $subpatt1;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[)]"];
        $transition2->subpatt_end[] = $subpatt2;
        $restran->subpatt_start[] = $subpatt1;
        $restran->subpatt_end[] = $subpatt2;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_asserts() {
        $lexer = $this->create_lexer('[a][a-c]');
        $leaf1 = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = $lexer->nextToken()->value;
        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert1;
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\Aa-c]"];
        $transition1->pregleaf->mergedassertions[] = $assert2;
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $rescharset->mergedassertions[] = $assert2;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER); //0->1[label="[\\Aa]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_ununited_asserts() {
        $lexer = $this->create_lexer('[a]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $leaf1;
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $assert2 = new qtype_preg_leaf_assert_dollar;
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);  //0->1[label="[^$a]"];
        $restran->pregleaf->mergedassertions[] = $assert1;
        $restran->pregleaf->mergedassertions[] = $assert2;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);   //0->1[label="[^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert1;
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);   //0->1[label="[$a]"];
        $transition2->pregleaf->mergedassertions[] = $assert2;
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_asserts_with_tags() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $subpatt = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $assert = new qtype_preg_leaf_assert_circumflex;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf, 1);//0->1[label="[(^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition1->subpatt_start[] = $subpatt;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf, 1);      //0->1[label="[(^a]"];
        $transition2->pregleaf->mergedassertions[] = $assert;
        $transition2->subpatt_start[] = $subpatt;
        $rescharset = $leaf->intersect_leafs($leaf, true, true);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);   //0->1[label="[(^a]"];
        $restran->subpatt_start[] = $subpatt;
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_and_character() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $transition1 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition2 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[a]"];
        $rescharset = $leaf->intersect_leafs($leaf, false, false);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);        //0->1[label="[^a]"];
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_character_tag() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $subpatt = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $assert = new qtype_preg_leaf_assert_circumflex;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf, 1);  //0->1[label="[(^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition1->subpatt_start[] = $subpatt;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf, 1);  //0->1[label="[a]"];
        $rescharset = $leaf->intersect_leafs($leaf, true, false);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $restran->subpatt_start[] = $subpatt;                       //0->1[label="[(^a]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_and_unmerged() {
        $lexer = $this->create_lexer('[a-c]');
        $leaf1 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf1->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1);  //0->1[label="[^a-c]"];
        $leaf2 = new qtype_preg_leaf_assert_circumflex;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1);  //0->1[label="[^]"];

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($transition1, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_unmerged_asserts() {
        $leaf1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = new qtype_preg_leaf_assert_esc_a;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\A]"];
        $restran = new qtype_preg_nfa_transition(0, $leaf2, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_and_assert() {
        $leaf1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = $assert = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $restran = new qtype_preg_nfa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_tags() {
        $lexer = $this->create_lexer('[a-c][g-k]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $subpatt1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $subpatt2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[(a-c)]"];
        $transition1->subpatt_start[] = $subpatt1;
        $transition1->subpatt_end[] = $subpatt2;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[(g-k)]"];
        $transition2->subpatt_start[] = $subpatt1;
        $transition2->subpatt_end[] = $subpatt2;
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_merged() {
        $lexer = $this->create_lexer('[a][01]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf1->mergedassertions[] = $assert;
        $leaf2->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[^a]"];
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[^01]"];
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_assert_and_character() {
        $lexer = $this->create_lexer('[a][01]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf1->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[^a]"];
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[01]"];
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }
}

class qtype_preg_fa_merge_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_merging_first_state() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                            }';
        $dotresult = 'digraph example {
                        "0   1";
                        2;
                        "0   1"->2[label="[0-9]"];
                    }';
        
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('1', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccessful_merging_last_state_with_tag() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[(/)/]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        2;
                        0->1[label="[0-9]"];
                        1->2[label="[(/)/]"];
                    }';
        
        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('1', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('2', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_unsuccessful_merging_last_state_with_assert() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        "1   2";
                        0->"1   2"[label = "[0-9^]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('1', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('2', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_last_state() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        "1   2";
                        0->"1   2"[label = "[0-9]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('1', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('2', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_several_intotransitions() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[a-c]"];
                                2->3[label="[]"];
                                0->3[label="[xy]"];
                                1->3[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        "2   3";
                        0->1[label = "[0-9]", color = violet];
                        0->"2   3"[label = "[xy]", color = violet];
                        1->"2   3"[label = "[a-c01]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('3', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_several_outtransitions() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[\\A]"];
                                1->2[label="[01]"];
                                1->3[label="[a-c]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1";
                        3;
                        "0   1"->2[label = "[\\A01]", color = violet];
                        "0   1"->3[label = "[\\Aa-c]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('1', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_start_of_cycle() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                                2->1[label="[a-z]"];
                            }';
        $dotresult = 'digraph example {
                        "0   1";
                        2;
                        "0   1"->2[label="[0-9]"];
                        2->"0   1"[label="[a-z]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('1', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_end_of_cycle() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[]"];
                                1->0[label="[a-f]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        "1   2";
                        0->"1   2"[label = "[0-9]", color = violet];
                        "1   2"->0[label = "[a-f]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('1', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('2', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_middle_of_implicit_cycle() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[^]"];
                                1->2[label="[x-z]"];
                                1->0[label="[a-f]"];
                            }';
        $dotresult = 'digraph example {
                        "0   1";
                        2;
                        "0   1"->2[label="[x-z]"];
                        "0   1"->"0   1"[label="[^a-f]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('1', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_in_cycle() {
        $dotdescription = 'digraph example {
                                0;
                                1;
                                0->1[label="[b]"];
                                0->0[label="[^]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        1;
                        0->1[label="[b]"];
                        0->0[label="[^]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $outtransitions = $input->get_adjacent_transitions($number, true);
        $number = array_search('0', $realnumbers);
        $del = $outtransitions[$number];
        $input->merge_transitions($del);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }
}

class qtype_preg_fa_merge_uncap_transitions_test extends PHPUnit_Framework_TestCase {

    public function test_merging_first_state() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[]"];
                                1->2[label="[0-9]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1";
                        2;
                        "0   1"->2[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;
        $number = 2;
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_capturing_transitions_between_states() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[^]"];
                                1->2[label="[ab]"];
                                0->1[label="[a]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        2;
                        0->1[label="[^]"];
                        1->2[label="[ab]"];
                        0->1[label="[a]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        $number = 2;
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_several_outtransitions() {
        $dotdescription = 'digraph example {
                                0;
                                2;
                                0->1[label="[\\A]"];
                                1->2[label="[0-9]"];
                                0->1[label="[^]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1";
                        2;
                        "0   1"->"/1"[label = "[^]", color = violet];
                        "0   1"->2[label = "[\A0-9]", color = violet];
                        "/1"->2[label = "[0-9]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merging_two_asserts() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                                2->3[label="[\\A0-9]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->"1   2"[label="[^0-9]"];
                        "1   2"->3[label="[\\A0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('0', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    /*public function test_merging_one_state_several_times() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[^]"];
                                1->2[label="[]"];
                                2->3[label="[0-9]"];
                            }';
        $dotresult = 'digraph example {
                        0;
                        3;
                        0->"1   2"[label="[^]"];
                        "1   2"->3[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_BOTH;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('3', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }*/

    public function test_only_eps_transitions() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[]"];
                                1->2[label="[]"];
                                2->3[label="[]"];
                            }';
        $dotresult = 'digraph example {
                        "0   1   2   3";
                        "0   1   2   3";
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_EPS;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    public function test_another_way_without_state_for_intersection() {
        $dotdescription = 'digraph example {
                                0;
                                5;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                                2->3[label="[$]"];
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
                        2->4[label="[$a-c]"];
                        2->5[label="[$01]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }

    /*public function test_different_ways_of_merging() {
        $dotdescription = 'digraph example {
                                0;
                                5;
                                0->1[label="[$]"];
                                1->2[label="[0-9]"];
                                2->3[label="[^]"];
                                0->3[label="[xy]"];
                                3->4[label="[a-c]"];
                                3->5[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0   1";
                        5;
                        "0   1"->3[label = "[xy]", color = violet];
                        "0   1"->2[label = "[$0-9]", color = violet];
                        2->4[label = "[^a-c]", color = violet];
                        2->5[label = "[^01]", color = violet];
                        3->4[label = "[a-c]", color = violet];
                        3->5[label = "[01]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }*/

    public function test_merging_state_for_intersection() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[0-9]"];
                                1->2[label="[^]"];
                                2->3[label="[\\A0-9]"];
                            }';
        $dotresult = 'digraph res {
                        0;
                        3;
                        0->"1   2"[label = "[0-9^]", color = violet];
                        "1   2"->3[label = "[\\A0-9]", color = violet];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_BOTH;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers);
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $input->fa_to_dot();
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    /*public function test_merging_with_cycle() {
        $dotdescription = 'digraph example {
                                0;
                                3;
                                0->1[label="[^]"];
                                1->2[label="[a-c]"];
                                2->3[label="[0-9]"];
                                3->1[label="[0-9]"];
                            }';
        $dotresult = 'digraph res {
                                0;
                                3;
                                0->1[label="[^]"];
                                1->2[label="[a-c]"];
                                2->3[label="[0-9]"];
                                3->1[label="[0-9]"];
                    }';

        $input = new qtype_preg_nfa(0, 0, 0, array());
        $input->read_fa($dotdescription);
        $transitiontype = qtype_preg_fa_transition::TYPE_TRANSITION_BOTH;
        $realnumbers = $input->get_state_numbers();
        $number = array_search('2', $realnumbers); 
        $input->merge_uncapturing_transitions($transitiontype, $number);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($input, $result, 'Result automata is not equal to expected');
    }*/
}

class qtype_preg_nodes_inter_asserts_test extends PHPUnit_Framework_TestCase {

    public function test_with_and_without_assert() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $assert2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $result = $assert1->intersect_asserts($assert2);
        $resassert = new qtype_preg_leaf_assert_circumflex;
        $this->assertEquals($assert1, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_a_and_circumflex() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $mergedassert1 = new qtype_preg_leaf_assert_circumflex;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $mergedassert2 = new qtype_preg_leaf_assert_esc_a;
        //$assert2->mergedassertions = array($assert2);

        $result = $assert1->intersect_asserts($assert2);
        $this->assertEquals($assert2, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_z_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $mergedassert1 = new qtype_preg_leaf_assert_dollar;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_esc_z;
        $mergedassert2 = new qtype_preg_leaf_assert_esc_z;
        

        $result = $assert1->intersect_asserts($assert2);

        $this->assertEquals($assert2, $result, 'Result assert is not equal to expected');
        //$this->assertEquals($assert2->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_circumflex_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $mergedassert1 = new qtype_preg_leaf_assert_circumflex;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_dollar;
        $mergedassert2 = new qtype_preg_leaf_assert_dollar;
        //$assert1->mergedassertions = array($mergedassert2);

        $assertresult = new qtype_preg_leaf_assert_dollar;
        $assertresult->assertionsafter= array($assert1);

        $result = $assert1->intersect_asserts($assert2);
        $this->assertEquals($assertresult, $result, 'Result assert is not equal to expected');
        //$this->assertEquals($assertresult->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_esc_b_and_esc_a() {
        $assert1 = new qtype_preg_leaf_assert_esc_b;
        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $assertresult = new qtype_preg_leaf_assert_esc_b; 
        $assertresult->assertionsbefore = array(1=>$assert2);

        $result = $assert1->intersect_asserts($assert2);
        $this->assertEquals($assertresult, $result, 'Result assert is not equal to expected');
    }
}

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
                        "0,"->"1,"[label = "[0-9]", color = violet];
                        "2,3"->"3,"[label = "[\-?,]", color = violet];
                        "1,"->"4,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                        "1,1"->"2,3"[label = "[abc ∩ a]", color = red];
                        "2,1"->"2,3"[label = "[a-z ∩ a]", color = red];
                        "0,0"->"1,1"[label = "[0-9 ∩ 01]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,0"->"2,1"[label = "[a-z ∩ a-z]", color = red];
                        "1,0"->"1,2"[label = "[0-9 ∩ 0-9]", color = red];
                        "2,1"->"2,2"[label = "[a-c ∩ a-c]", color = red];
                        "1,2"->"2,2"[label = "[a-z ∩ ab]", color = red];
                        "2,2"->"2,2"[label = "[a-c ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,0"->"2,1"[label = "[ab ∩ ab]", color = red];
                        "1,0"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "2,1"->"0,2"[label = "[ab ∩ ab]", color = red];
                        "2,2"->"0,1"[label = "[ab ∩ ab]", color = red];
                        "0,2"->"1,1"[label = "[ab ∩ ab]", color = red];
                        "0,1"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "1,2"->"2,1"[label = "[ab ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1,1"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,1"->"2,2"[label = "[$b ∩ b]", color = red];
                        "0,0"->"1,1"[label = "[^a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,0"->"2,1"[label = "[a-k ∩ c-n]", color = red];
                        "2,1"->"4,1"[label = "[a-c ∩ a-z]", color = red];
                        "2,1"->"4,2"[label = "[a-c ∩ a-z]", color = red];
                        "4,1"->"5,1"[label = "[xy ∩ a-z]", color = red];
                        "4,1"->"5,2"[label = "[xy ∩ a-z]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,0"->"2,1"[label = "[c-k ∩ cd]", color = red];
                        "2,1"->"3,2"[label = "[a-c ∩ a]", color = red];
                        "3,2"->"4,3"[label = "[ab ∩ ab]", color = red];
                        "3,2"->"2,1"[label = "[xy ∩ xy]", color = red];
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
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}

class qtype_preg_fa_intersect_automata_test extends PHPUnit_Framework_TestCase {

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

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('0', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_end() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-\\&,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";
                        "4,";
                        "1,"->"4,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                        "0,"->"1,"[label = "[0-9]", color = violet];
                        "2,3"->"3,"[label = "[\-\&,]", color = violet];
                        "1,1"->"2,3"[label = "[abc ∩ abc]", color = red];
                        "2,1"->"2,3"[label = "[a-z ∩ abc]", color = red];
                        "0,0"->"1,1"[label = "[0-9 ∩ 01]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 1);
        $result = $resultautomata->fa_to_dot();
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
                                0->1[label="[01]"];
                                1->2[label="[a-z]"];
                                1->1[label="[0-9]"];
                                2->2[label="[a-c]"];
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
        $dotresult = 'digraph res {
                        "0,";
                        "2,2";
                        "0,"->"1,0"[label = "[01]", color = violet];
                        "1,0"->"2,1"[label = "[a-z ∩ a-z]", color = red];
                        "1,0"->"1,2"[label = "[0-9 ∩ 0-9]", color = red];
                        "2,1"->"2,2"[label = "[a-c ∩ a-c]", color = red];
                        "1,2"->"2,2"[label = "[a-z ∩ ab]", color = red];
                        "2,2"->"2,2"[label = "[a-c ∩ ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_implicent_cycles() {
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
        $dotresult = 'digraph res {
                        "0,";
                        "2,2";
                        "0,"->"1,0"[label = "[ab]", color = violet];
                        "1,0"->"2,1"[label = "[ab ∩ ab]", color = red];
                        "1,0"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "2,1"->"0,2"[label = "[ab ∩ ab]", color = red];
                        "2,2"->"0,1"[label = "[ab ∩ ab]", color = red];
                        "0,2"->"1,1"[label = "[ab ∩ ab]", color = red];
                        "0,1"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "1,2"->"2,1"[label = "[ab ∩ ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_end_state() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[xy]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                                3->4[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[a-z]"];
                                2->3[label="[\\.,-]"];
                            }';
        $dotresult = 'digraph res {
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_start_state() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[xy]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                                3->4[label="[xy]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[0-9]"];
                                2->3[label="[a-z]"];
                            }';
        $dotresult = 'digraph res {
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('3', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 1);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_branches_from_first_and_second() {
        $dotdescription1 = 'digraph example {
                                0;
                                2;
                                0->1[label="[a-z]"];
                                1->2[label="[0-9]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[0-9]"];
                                1->2[label="[a-z]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        ",2";
                        "0,"->"1,0"[label = "[a-z]", color = violet];
                        "1,0"->"2,1"[label = "[0-9 ∩ 0-9]", color = red];
                        "2,1"->",2"[label = "[a-z]", color = blue, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[abc0-9]"];
                                1->4[label="[01]"];
                                2->3[label="[\\-?,]"];
                                2->2[label="[a-z]"];
                                3->4[label="[a]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->1[label="[01]"];
                                1->2[label="[?]"];
                                1->3[label="[ab]"];
                                2->3[label="[<>]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";
                        "4,";
                        "1,"->"4,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[a]", color = violet];
                        "0,"->"1,"[label = "[0-9]", color = violet];
                        "0,"->"1,0"[label = "[0-9]", color = violet];
                        "2,3"->"3,"[label = "[\-?,]", color = violet];
                        "1,1"->"2,3"[label = "[abc0-9 ∩ ab]", color = red];
                        "2,1"->"2,3"[label = "[a-z ∩ ab]", color = red];
                        "0,0"->"1,1"[label = "[0-9 ∩ 01]", color = red];
                        "1,0"->"2,1"[label = "[abc0-9 ∩ 01]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 1);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_divarication_into_cycle() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[a-c]"];
                                1->2[label="[0-9]"];
                                0->3[label="[01]"];
                                3->4[label="[y]"];
                                2->4[label="[a-f]"];
                                4->0[label="[bc]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                4;
                                0->1[label="[c-z]"];
                                1->2[label="[ab]"];
                                2->3[label="[0-9]"];
                                3->4[label="[x-z]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";"4,4   1";
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "0,"->"3,"[label = "[01]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "3,"->"4,"[label = "[y]", color = violet];
                        "2,0"->"4,1"[label = "[a-f ∩ c-z]", color = red];
                        "4,"->"0,"[label = "[bc]", color = violet];
                        "4,1"->"0,2"[label = "[bc ∩ ab]", color = red];
                        "0,2"->"3,3"[label = "[01 ∩ 0-9]", color = red];
                        "3,3"->"4,4   1"[label = "[y ∩ x-z]", color = red];
                        "4,4   1"->"0,2"[label = "[bc ∩ ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_big_cycle() {
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
        $dotresult = 'digraph res {
                        "0,0";"0,";
                        "2,";"2,2";
                        "2,"->"0,"[label = "[ab]", color = violet];
                        "1,2"->"2,"[label = "[ab]", color = violet];
                        "1,2"->"2,1"[label = "[ab ∩ ab]", color = red];
                        "0,0"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "0,0"->"1,1"[label = "[ab ∩ ab]", color = red];
                        "0,1"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "2,0"->"0,1"[label = "[ab ∩ ab]", color = red];
                        "2,0"->"0,2"[label = "[ab ∩ ab]", color = red];
                        "2,2"->"0,1"[label = "[ab ∩ ab]", color = red];
                        "1,0"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "1,0"->"2,1"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "0,2"->"1,1"[label = "[ab ∩ ab]", color = red];
                        "2,1"->"0,2"[label = "[ab ∩ ab]", color = red];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "1,"->"2,0"[label = "[ab]", color = violet];
                        "0,"->"1,"[label = "[ab]", color = violet];
                        "0,"->"1,0"[label = "[ab]", color = violet];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 1);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_merged_states() {
        $dotdescription1 = 'digraph example {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                2;
                                0->1[label="[ab]"];
                                1->2[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        ",2";
                        "0,"->"1   2,0"[label = "[ab]", color = violet];
                        "1   2,0"->"3,1"[label = "[ab ∩ ab]", color = red];
                        "3,1"->",2"[label = "[ab]", color = blue, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1   2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_intersection() {
        $dotdescription1 = 'digraph example {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotdescription2 = 'digraph example {
                                0;
                                3;
                                0->"1   2"[label="[ab]"];
                                "1   2"->3[label="[ab]"];
                            }';
        $dotresult = 'digraph res {
                        "0,0";
                        "3,3";
                        "0,0"->"1   2,1   2"[label = "[ab ∩ ab]", color = red];
                        "1   2,1   2"->"3,3"[label = "[ab ∩ ab]", color = red];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = new qtype_preg_nfa(0, 0, 0, array());
        $firstautomata->read_fa($dotdescription1);
        $secondautomata = new qtype_preg_nfa(0, 0, 0, array());
        $secondautomata->read_fa($dotdescription2, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('0', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, $stateforinter, 0);
        $result = $resultautomata->fa_to_dot();
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}

class qtype_preg_fa_intersect_fa_test extends PHPUnit_Framework_TestCase {

    public function test_nessesary_merging() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;5;
                                0->1[label="[]"];
                                0->2[label="[0-9]"];
                                1->3[label="[a-c]"];
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
                        "0   1,"->"2,0   2"[label = "[0-9]", color = violet];
                        "0   1,"->"3,"[label = "[a-c]", color = violet];
                        "2,0   2"->"4,1"[label = "[. ∩ 01]", color = red];
                        "2,0   2"->"4,3"[label = "[. ∩ abcd]", color = red];
                        "2,0   2"->"5,1"[label = "[01 ∩ 01]", color = red];
                        "3,"->"4,"[label = "[.]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_blind() {
        $dotdescription1 = 'digraph example {
                                0;
                                4;
                                0->1[label="[0-9]"];
                                1->2[label="[a-c]"];
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
                        "0,"->"1,"[label = "[0-9]", color = violet];
                        "2,3"->"3,"[label = "[\-\\\\&,]", color = violet];
                        "1,1"->"2,3"[label = "[a-c ∩ ab]", color = red];
                        "0,0"->"1,1"[label = "[0-9 ∩ 01]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1   3,0"->"2,2"[label = "[a ∩ a]", color = red];
                        "1   3,0"->"2,3"[label = "[a ∩ a]", color = red];
                        "1   3,0"->"4,2"[label = "[^a ∩ a]", color = red];
                        "1   3,0"->"4,3"[label = "[^a ∩ a]", color = red];
                        "2,2"->"1   3,4"[label = "[a ∩ a]", color = red];
                        "2,2"->"1   3,3"[label = "[a ∩ a]", color = red];
                        "2,2"->"4,4"[label = "[a ∩ a]", color = red];
                        "2,2"->"4,3"[label = "[a ∩ a]", color = red];
                        "2,3"->"1   3,5"[label = "[a ∩ a]", color = red];
                        "2,3"->"4,5"[label = "[a ∩ a]", color = red];
                        "4,2"->"5,4"[label = "[a ∩ a]", color = red];
                        "4,2"->"5,3"[label = "[a ∩ a]", color = red];
                        "4,2"->"6,4"[label = "[a ∩ a]", color = red];
                        "4,2"->"6,3"[label = "[a ∩ a]", color = red];
                        "4,3"->"5,5"[label = "[a ∩ a]", color = red];
                        "4,3"->"6,5"[label = "[a ∩ a]", color = red];
                        "1   3,4"->"2,3"[label = "[a ∩ a]", color = red];
                        "1   3,4"->"2,5"[label = "[a ∩ a]", color = red];
                        "1   3,4"->"4,3"[label = "[^a ∩ a]", color = red];
                        "1   3,4"->"4,5"[label = "[^a ∩ a]", color = red];
                        "1   3,3"->"2,5"[label = "[a ∩ a]", color = red];
                        "1   3,3"->"4,5"[label = "[^a ∩ a]", color = red];
                        "4,4"->"5,3"[label = "[a ∩ a]", color = red];
                        "4,4"->"5,5"[label = "[a ∩ a]", color = red];
                        "4,4"->"6,3"[label = "[a ∩ a]", color = red];
                        "4,4"->"6,5"[label = "[a ∩ a]", color = red];
                        "1   3,5"->"2,"[label = "[a]", color = violet];
                        "1   3,5"->"4,"[label = "[^a]", color = violet];
                        "4,5"->"5,"[label = "[a]", color = violet];
                        "4,5"->"6,"[label = "[a]", color = violet];
                        "5,4"->"5,3"[label = "[a ∩ a]", color = red];
                        "5,4"->"5,5"[label = "[a ∩ a]", color = red];
                        "5,4"->"6,3"[label = "[a ∩ a]", color = red];
                        "5,4"->"6,5"[label = "[a ∩ a]", color = red];
                        "5,3"->"5,5"[label = "[a ∩ a]", color = red];
                        "5,3"->"6,5"[label = "[a ∩ a]", color = red];
                        "6,4"->"6,3"[label = "[a ∩ a]", color = red];
                        "6,4"->"6,5"[label = "[a ∩ a]", color = red];
                        "6,3"->"6,5"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,0"->"2,1"[label = "[a ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1,1"[label = "[b ∩ ab]", color = red];
                        "0,0"->"2,1"[label = "[a ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[a ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1   2,1"[label = "[b ∩ ab]", color = red];
                        "1   2,1"->"3,2"[label = "[a ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1   2,1"[label = "[b ∩ ab]", color = red];
                        "1   2,1"->"3,2"[label = "[^a ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1,1"[label = "[a-c ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[0-9 ∩ 01]", color = red];
                        "2,2"->"3,"[label = "[a]", color = violet];
                        "3,"->"4,"[label = "[a-z]", color = violet];
                        "4,"->"5,"[label = "[a-z]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,9"->"5,"[label = "[a-z]", color = violet];
                        "3,8"->"4,9"[label = "[a-z ∩ a]", color = red];
                        "2,7"->"3,8"[label = "[a ∩ a]", color = red];
                        "1,6"->"2,7"[label = "[a ∩ a]", color = red];
                        "0,5"->"1,6"[label = "[a-c ∩ a]", color = red];
                        "4,5   9"->"1,6"[label = "[a ∩ a]", color = red];
                        "3,4   8"->"4,5   9"[label = "[a-z ∩ ab]", color = red];
                        "2,3   7"->"3,4   8"[label = "[a ∩ a]", color = red];
                        "1,2   6"->"2,3   7"[label = "[a ∩ a]", color = red];
                        "1,2   6"->"(2,3   7)"[label = "[a ∩ a]", color = red];
                        "0,1"->"1,2   6"[label = "[a-c ∩ a]", color = red];
                        "4,1   5   9"->"1,2   6"[label = "[a ∩ a]", color = red];
                        "3,0   4   8"->"4,1   5   9"[label = "[a-z ∩ a]", color = red];
                        "(2,3   7)"->"3,0   4   8"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,7"->"5,8"[label = "[a-z ∩ .]", color = red];
                        "3,6"->"4,7"[label = "[a-z ∩ .]", color = red];
                        "2,5"->"3,6"[label = "[a ∩ .]", color = red];
                        "1,4"->"2,5"[label = "[0-9 ∩ .]", color = red];
                        "1,4"->"1,0   4"[label = "[]", color = red];
                        "0,3"->"1,4"[label = "[a-c ∩ .]", color = red];
                        "4,3   7"->"1,4"[label = "[a ∩ .]", color = red];
                        "3,2   6"->"4,3   7"[label = "[a-z ∩ .]", color = red];
                        "2,1   5"->"3,2   6"[label = "[a ∩ .]", color = red];
                        "1,0   4"->"2,1   5"[label = "[0-9 ∩ .]", color = red];
                        ",2"->"0,3"[label = "[.]", color = blue, style = dotted];
                        ",1"->",2"[label = "[.]", color = blue, style = dotted];
                        ",0"->",1"[label = "[.]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "2,0"->"3,1"[label = "[a ∩ ab]", color = red];
                        "3,1"->"4,2"[label = "[a-z ∩ a-k]", color = red];
                        "4,2"->"5,"[label = "[a-z]", color = violet];
                        "4,2"->"1,"[label = "[m]", color = violet];
                        "2,"->"3,"[label = "[a]", color = violet];
                        "3,"->"4,"[label = "[a-z]", color = violet];
                        "4,"->"5,"[label = "[a-z]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1,1"[label = "[a-c ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[0-9 ∩ 01]", color = red];
                        "2,2"->"4,"[label = "[a]", color = violet];
                        "4,"->"6,"[label = "[a-z]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "6,"->"1,"[label = "[a-s]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[a-z]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,5"->"5,"[label = "[a-z]", color = violet];
                        "3,4"->"4,5"[label = "[a-z ∩ ab]", color = red];
                        "2,3"->"3,4"[label = "[a ∩ a]", color = red];
                        "1,2"->"2,3"[label = "[a ∩ a]", color = red];
                        "1,2"->"(2,3)"[label = "[a ∩ a]", color = red];
                        "0,1"->"1,2"[label = "[a-c ∩ a]", color = red];
                        "4,1   5"->"1,2"[label = "[a ∩ a]", color = red];
                        "3,0   4"->"4,1   5"[label = "[a-z ∩ a]", color = red];
                        "(2,3)"->"3,0   4"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[a-z]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "2,0"->"4,1"[label = "[a ∩ ab]", color = red];
                        "3,"->"5,"[label = "[012]", color = violet];
                        "5,"->"6,"[label = "[+=]", color = violet];
                        "6,"->"1,"[label = "[a-s]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "4,1"->"6,2"[label = "[0 ∩ 01]", color = red];
                        "6,2"->"1,"[label = "[a-s]", color = violet];
                        "6,2"->"7,"[label = "[a-c]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "6,2"->"7,"[label = "[a-c]", color = violet];
                        "4,1"->"6,2"[label = "[0 ∩ 01]", color = red];
                        "5,1"->"6,2"[label = "[0-9 ∩ 01]", color = red];
                        "2,0"->"4,1"[label = "[a ∩ ab]", color = red];
                        "3,0"->"5,1"[label = "[a-z ∩ ab]", color = red];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[a-z]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "1,"->"3,0"[label = "[a-z]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "6,"->"1,"[label = "[a-s]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "5,"->"6,"[label = "[0-9]", color = violet];
                        "3,"->"5,"[label = "[a-z]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "6,2"->"7,"[label = "[a-c]", color = violet];
                        "4,1"->"6,2"[label = "[0 ∩ 01]", color = red];
                        "5,1"->"6,2"[label = "[0-9 ∩ 01]", color = red];
                        "2,0"->"4,1"[label = "[a ∩ ab]", color = red];
                        "3,0"->"5,1"[label = "[a-z ∩ ab]", color = red];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[a-z]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "1,"->"3,0"[label = "[a-z]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "6,"->"2,"[label = "[^0-9]", color = violet];
                        "6,"->"3,"[label = "[^a-z]", color = violet];
                        "6,"->"2,0"[label = "[^0-9]", color = violet];
                        "6,"->"3,0"[label = "[^a-z]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "5,"->"6,"[label = "[0-9]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "3,"->"5,"[label = "[a-z]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,0"->"1,1"[label = "[a-c ∩ ab]", color = red];
                        "1,1"->"2,2"[label = "[0-9 ∩ 01]", color = red];
                        "2,2"->"4,3"[label = "[a ∩ a-z]", color = red];
                        "4,3"->"6,"[label = "[0]", color = violet];
                        "4,3"->"1,"[label = "[m]", color = violet];
                        "6,"->"1,"[label = "[a-s]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[012]", color = violet];
                        "2,"->"4,"[label = "[a]", color = violet];
                        "3,"->"5,"[label = "[+=]", color = violet];
                        "4,"->"6,"[label = "[0]", color = violet];
                        "4,"->"1,"[label = "[m]", color = violet];
                        "5,"->"6,"[label = "[0-9]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "7,"->"1,"[label = "[a-s]", color = violet];
                        "5,"->"7,"[label = "[0-9]", color = violet];
                        "5,"->"7,0"[label = "[0-9]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "6,"->"7,0"[label = "[a-c]", color = violet];
                        "3,"->"5,"[label = "[+=]", color = violet];
                        "4,3"->"6,"[label = "[0]", color = violet];
                        "4,3"->"1,"[label = "[m]", color = violet];
                        "1,"->"3,"[label = "[012]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "2,2"->"4,3"[label = "[a ∩ a-z]", color = red];
                        "1,1"->"2,2"[label = "[0-9 ∩ 01]", color = red];
                        "0,0"->"1,1"[label = "[a-c ∩ ab]", color = red];
                        "7,0"->"1,1"[label = "[a-s ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,0"[label = "[a-c]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,0"->"2,1"[label = "[a-c ∩ ab]", color = red];
                        "2,1"->"3,2"[label = "[0-5as ∩ 01]", color = red];
                        "3,2"->"4,"[label = "[+=]", color = violet];
                        "4,"->"5,"[label = "[0]", color = violet];
                        "4,"->"2,"[label = "[m]", color = violet];
                        "5,"->"6,"[label = "[0-9]", color = violet];
                        "5,"->"1,"[label = "[a-s]", color = violet];
                        "2,"->"3,"[label = "[0-5as]", color = violet];
                        "1,"->"2,"[label = "[a-c]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "5,2"->"6,"[label = "[0-9]", color = violet];
                        "4,1"->"5,2"[label = "[0 ∩ 01]", color = red];
                        "3,0"->"4,1"[label = "[ab ∩ ab]", color = red];
                        "2,"->"3,"[label = "[0-5as]", color = violet];
                        "2,"->"3,0"[label = "[0-5as]", color = violet];
                        "1,"->"2,"[label = "[a-c]", color = violet];
                        "4,"->"2,"[label = "[m]", color = violet];
                        "4,"->"5,"[label = "[0]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "5,"->"6,"[label = "[0-9]", color = violet];
                        "5,"->"1,"[label = "[a-s]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,"->"2,0"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[a-z]", color = violet];
                        "2,0"->"4,1"[label = "[a ∩ .]", color = red];
                        "3,"->"5,"[label = "[012]", color = violet];
                        "5,"->"6,"[label = "[+=]", color = violet];
                        "6,"->"1,"[label = "[a-s]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "4,1"->"6,2"[label = "[0 ∩ .]", color = red];
                        "4,1"->"1,2"[label = "[m ∩ .]", color = red];
                        "6,2"->"1,0"[label = "[a-s ∩ .]", color = red];
                        "6,2"->"7,0"[label = "[a-c ∩ .]", color = red];
                        "1,2"->"2,0"[label = "[0-9 ∩ .]", color = red];
                        "1,2"->"3,0"[label = "[a-z ∩ .]", color = red];
                        "1,0"->"2,1"[label = "[0-9 ∩ .]", color = red];
                        "1,0"->"3,1"[label = "[a-z ∩ .]", color = red];
                        "7,0"->",1"[label = "[.]", color = blue, style = dotted];
                        "3,0"->"5,1"[label = "[012 ∩ .]", color = red];
                        "2,1"->"4,2"[label = "[a ∩ .]", color = red];
                        "3,1"->"5,2"[label = "[012 ∩ .]", color = red];
                        "5,1"->"6,2"[label = "[+= ∩ .]", color = red];
                        "4,2"->"6,0"[label = "[0 ∩ .]", color = red];
                        "4,2"->"1,0"[label = "[m ∩ .]", color = red];
                        "5,2"->"6,0"[label = "[+= ∩ .]", color = red];
                        "6,0"->"1,1"[label = "[a-s ∩ .]", color = red];
                        "6,0"->"7,1"[label = "[a-c ∩ .]", color = red];
                        "1,1"->"2,2"[label = "[0-9 ∩ .]", color = red];
                        "1,1"->"3,2"[label = "[a-z ∩ .]", color = red];
                        "7,1"->",2"[label = "[.]", color = blue, style = dotted];
                        "2,2"->"4,0"[label = "[a ∩ .]", color = red];
                        "3,2"->"5,0"[label = "[012 ∩ .]", color = red];
                        "4,0"->"6,1"[label = "[0 ∩ .]", color = red];
                        "4,0"->"1,1"[label = "[m ∩ .]", color = red];
                        "5,0"->"6,1"[label = "[+= ∩ .]", color = red];
                        "6,1"->"1,2"[label = "[a-s ∩ .]", color = red];
                        "6,1"->"7,2"[label = "[a-c ∩ .]", color = red];
                        "7,2"->"7,"[label = "[]", color = red];
                        ",1"->",2"[label = "[.]", color = blue, style = dotted];
                        ",2"->",0"[label = "[.]", color = blue, style = dotted];
                        ",2"->"7,"[label = "[]", color = blue, style = dotted];
                        ",0"->",1"[label = "[.]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "6,1"->"7,2"[label = "[a-c ∩ ab]", color = red];
                        "4,0"->"6,1"[label = "[0 ∩ 01]", color = red];
                        "2,2"->"4,0"[label = "[a ∩ .]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "0,0"->"1,1"[label = "[01 ∩ 01]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "6,2"->"7,"[label = "[a-c]", color = violet];
                        "4,1"->"6,2"[label = "[ab ∩ ab]", color = red];
                        "5,1"->"6,2"[label = "[^a ∩ ab]", color = red];
                        "2,0"->"4,1"[label = "[0 ∩ 01]", color = red];
                        "3,0"->"5,1"[label = "[012 ∩ 01]", color = red];
                        "1,2"->"2,0"[label = "[0-9 ∩ .]", color = red];
                        "1,2"->"3,0"[label = "[a-z ∩ .]", color = red];
                        "0,1"->"1,2"[label = "[a-c ∩ ab]", color = red];
                        ",0"->",1"[label = "[01]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[01]", color = blue, style = dotted];
                        ",2"->",0"[label = "[.]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "6,1"->"7,2"[label = "[a-c ∩ .]", color = red];
                        "6,1"->"1,2"[label = "[a-s ∩ .]", color = red];
                        "4,0"->"6,1"[label = "[0 ∩ .]", color = red];
                        "4,0"->"1,1"[label = "[m ∩ .]", color = red];
                        "5,0"->"6,1"[label = "[+= ∩ .]", color = red];
                        "2,2"->"4,0"[label = "[a ∩ .]", color = red];
                        "3,2"->"5,0"[label = "[012 ∩ .]", color = red];
                        "1,1"->"2,2"[label = "[a-n ∩ .]", color = red];
                        "1,1"->"3,2"[label = "[a-z ∩ .]", color = red];
                        "0,0"->"1,1"[label = "[0-9 ∩ .]", color = red];
                        "6,0"->"1,1"[label = "[a-s ∩ .]", color = red];
                        "4,2"->"6,0"[label = "[0 ∩ .]", color = red];
                        "4,2"->"1,0"[label = "[m ∩ .]", color = red];
                        "5,2"->"6,0"[label = "[+= ∩ .]", color = red];
                        "2,1"->"4,2"[label = "[a ∩ .]", color = red];
                        "3,1"->"5,2"[label = "[012 ∩ .]", color = red];
                        "1,0"->"2,1"[label = "[a-n ∩ .]", color = red];
                        "1,0"->"3,1"[label = "[a-z ∩ .]", color = red];
                        "0,2"->"1,0"[label = "[0-9 ∩ .]", color = red];
                        "6,2"->"1,0"[label = "[a-s ∩ .]", color = red];
                        "4,1"->"6,2"[label = "[0 ∩ .]", color = red];
                        "4,1"->"1,2"[label = "[m ∩ .]", color = red];
                        "5,1"->"6,2"[label = "[+= ∩ .]", color = red];
                        "2,0"->"4,1"[label = "[a ∩ .]", color = red];
                        "3,0"->"5,1"[label = "[012 ∩ .]", color = red];
                        "1,2"->"2,0"[label = "[a-n ∩ .]", color = red];
                        "1,2"->"3,0"[label = "[a-z ∩ .]", color = red];
                        "0,1"->"1,2"[label = "[0-9 ∩ .]", color = red];
                        ",1"->",2"[label = "[.]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[.]", color = blue, style = dotted];
                        ",0"->",1"[label = "[.]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[.]", color = blue, style = dotted];
                        ",2"->",0"[label = "[.]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                                6->7[label="[axy]"];
                                6->8[label="[.]"];
                                7->9[label="[a]"];
                                8->9[label="[a-c]"];
                                7->2[label="[a-z]"];
                                8->1[label="[a-f]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";"0,0";",0";
                        "9,";
                        "9,"->"2,"[label = "[0-9]", color = violet];
                        "8,"->"9,"[label = "[0-9]", color = violet];
                        "8,"->"1,"[label = "[0-9]", color = violet];
                        "8,"->"1,0"[label = "[0-9]", color = violet];
                        "2,"->"8,"[label = "[as]", color = violet];
                        "4,"->"8,"[label = "[ab]", color = violet];
                        "7,"->"8,"[label = "[0-9]", color = violet];
                        "1,"->"2,"[label = "[0-9]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,"[label = "[a-z]", color = violet];
                        "3,"->"4,"[label = "[a-c]", color = violet];
                        "6,"->"7,"[label = "[0-9]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "0,"->"1,0"[label = "[a-c]", color = violet];
                        "5,9"->"6,"[label = "[0-9]", color = violet];
                        "1,7"->"5,9"[label = "[a-z ∩ a]", color = red];
                        "1,8"->"5,9"[label = "[a-z ∩ a-c]", color = red];
                        "1,8"->"3,1"[label = "[ab ∩ a-f]", color = red];
                        "0,6"->"1,7"[label = "[a-c ∩ axy]", color = red];
                        "0,6"->"1,8"[label = "[a-c ∩ .]", color = red];
                        "8,6"->"1,8"[label = "[0-9 ∩ .]", color = red];
                        "4,2"->"8,6"[label = "[ab ∩ a-c]", color = red];
                        "4,5"->"8,6"[label = "[ab ∩ as01]", color = red];
                        "3,1"->"4,2"[label = "[a-c ∩ ab]", color = red];
                        "3,7"->"4,2"[label = "[a-c ∩ a-z]", color = red];
                        "3,4"->"4,5"[label = "[a-c ∩ a-d]", color = red];
                        "1,0"->"3,1"[label = "[ab ∩ a-z]", color = red];
                        "1,6"->"3,7"[label = "[ab ∩ axy]", color = red];
                        "1,3"->"3,4"[label = "[ab ∩ .]", color = red];
                        "1,3"->"2,4"[label = "[0-9 ∩ .]", color = red];
                        "0,2"->"1,6"[label = "[a-c ∩ a-c]", color = red];
                        "0,5"->"1,6"[label = "[a-c ∩ as01]", color = red];
                        "8,5"->"1,6"[label = "[0-9 ∩ as01]", color = red];
                        "0,1"->"1,3"[label = "[a-c ∩ as]", color = red];
                        "2,4"->"8,5"[label = "[as ∩ a-d]", color = red];
                        "4,4"->"8,5"[label = "[ab ∩ a-d]", color = red];
                        "3,3"->"4,4"[label = "[a-c ∩ .]", color = red];
                        "1,1"->"3,3"[label = "[ab ∩ as]", color = red];
                        "0,0"->"1,1"[label = "[a-c ∩ a-z]", color = red];
                        "0,8"->"1,1"[label = "[a-c ∩ a-f]", color = red];
                        ",2"->",6"[label = "[a-c]", color = blue, style = dotted];
                        ",2"->"0,6"[label = "[a-c]", color = blue, style = dotted];
                        ",5"->",6"[label = "[as01]", color = blue, style = dotted];
                        ",5"->"0,6"[label = "[as01]", color = blue, style = dotted];
                        ",1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->",3"[label = "[as]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[ab]", color = blue, style = dotted];
                        ",7"->",2"[label = "[a-z]", color = blue, style = dotted];
                        ",7"->"0,2"[label = "[a-z]", color = blue, style = dotted];
                        ",4"->",5"[label = "[a-d]", color = blue, style = dotted];
                        ",4"->"0,5"[label = "[a-d]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a-z]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[a-z]", color = blue, style = dotted];
                        ",8"->",1"[label = "[a-f]", color = blue, style = dotted];
                        ",8"->"0,1"[label = "[a-f]", color = blue, style = dotted];
                        ",6"->",7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->",8"[label = "[.]", color = blue, style = dotted];
                        ",6"->"0,8"[label = "[.]", color = blue, style = dotted];
                        ",3"->",4"[label = "[.]", color = blue, style = dotted];
                        "5,"->"6,"[label = "[0-9]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,"->"2,0"[label = "[a-z]", color = violet];
                        "2,0"->"3,1"[label = "[a ∩ ab]", color = red];
                        "3,1"->"4,2"[label = "[a-z ∩ a-z]", color = red];
                        "3,1"->"1,2"[label = "[a ∩ a-z]", color = red];
                        "4,2"->",3"[label = "[a]", color = blue, style = dotted];
                        "1,2"->"2,3   0"[label = "[a-z ∩ a]", color = red];
                        "2,3   0"->"3,1"[label = "[a ∩ ab]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0,"->"1,0"[label = "[a-c]", color = violet];
                        "1,0"->"2,1"[label = "[a-z ∩ ab]", color = red];
                        "1,0"->"3,1"[label = "[a-z ∩ ab]", color = red];
                        "2,1"->"4,2"[label = "[a ∩ a-z]", color = red];
                        "3,1"->"4,2"[label = "[a-z ∩ a-z]", color = red];
                        "4,2"->"1,3   0"[label = "[a ∩ a]", color = red];
                        "4,2"->"5,3"[label = "[a ∩ a]", color = red];
                        "1,3   0"->"2,4   1"[label = "[a-z ∩ ab]", color = red];
                        "1,3   0"->"3,4   1"[label = "[a-z ∩ ab]", color = red];
                        "5,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "2,4   1"->"4,5   2"[label = "[a ∩ a-z]", color = red];
                        "3,4   1"->"4,5   2"[label = "[a-z ∩ a-z]", color = red];
                        "4,5   2"->"1,6   3   0"[label = "[a ∩ a]", color = red];
                        "4,5   2"->"5,6"[label = "[a ∩ a]", color = red];
                        "1,6   3   0"->"2,7   4   1"[label = "[a-z ∩ ab]", color = red];
                        "1,6   3   0"->"3,7   4   1"[label = "[a-z ∩ ab]", color = red];
                        "5,6"->",7"[label = "[ab]", color = blue, style = dotted];
                        "2,7   4   1"->"4,8   5   2"[label = "[a ∩ a-z]", color = red];
                        "3,7   4   1"->"4,8   5   2"[label = "[a-z ∩ a-z]", color = red];
                        "4,8   5   2"->"1,9   6   3   0"[label = "[a ∩ a]", color = red];
                        "4,8   5   2"->"5,9"[label = "[a ∩ a]", color = red];
                        "1,9   6   3   0"->"2,7   4   1"[label = "[a-z ∩ ab]", color = red];
                        "1,9   6   3   0"->"3,7   4   1"[label = "[a-z ∩ ab]", color = red];
                        ",4"->",5"[label = "[a-z]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->",7"[label = "[ab]", color = blue, style = dotted];
                        ",7"->",8"[label = "[a-z]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0   1,0"->"2,1"[label = "[a-z ∩ ab]", color = red];
                        "2,1"->"3,2"[label = "[a-z ∩ a-z]", color = red];
                        "3,2"->"4,3"[label = "[a ∩ a]", color = red];
                        "3,2"->"0   1,3   0"[label = "[a ∩ a]", color = red];
                        "4,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "0   1,3   0"->"2,4   1"[label = "[a-z ∩ ab]", color = red];
                        "2,4   1"->"3,5   2"[label = "[a-z ∩ a-z]", color = red];
                        "3,5   2"->"4,6"[label = "[a ∩ a]", color = red];
                        "3,5   2"->"0   1,6   3   0"[label = "[a ∩ a]", color = red];
                        "0   1,6   3   0"->"2,4   1"[label = "[a-z ∩ ab]", color = red];
                        ",4"->",5"[label = "[a-z]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "0   1,"->"2,0   1"[label = "[a-z]", color = violet];
                        "2,0   1"->"3,2"[label = "[a-z ∩ a-z]", color = red];
                        "3,2"->"4,3"[label = "[a ∩ a]", color = red];
                        "3,2"->"0   1,3"[label = "[a ∩ a]", color = red];
                        "4,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "0   1,3"->"2,4   0   1"[label = "[a-z ∩ ab]", color = red];
                        "2,4   0   1"->"3,5   2"[label = "[a-z ∩ a-z]", color = red];
                        "3,5   2"->"4,6"[label = "[a ∩ a]", color = red];
                        "3,5   2"->"0   1,6   3"[label = "[a ∩ a]", color = red];
                        "0   1,6   3"->"2,4   0   1"[label = "[a-z ∩ ab]", color = red];
                        ",4"->",5"[label = "[a-z]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,8"->"5,"[label = "[a-z]", color = violet];
                        "3,7"->"4,8"[label = "[a-z ∩ ab]", color = red];
                        "2,6"->"3,7"[label = "[a ∩ ab]", color = red];
                        "1,5"->"2,6"[label = "[a ∩ ab]", color = red];
                        "0,4"->"1,5"[label = "[a-c ∩ ab]", color = red];
                        "4,4   8"->"1,5"[label = "[a ∩ ab]", color = red];
                        "3,3   7"->"4,4   8"[label = "[a-z ∩ a]", color = red];
                        "2,2   6"->"3,3   7"[label = "[a ∩ a]", color = red];
                        "1,1   5"->"2,2   6"[label = "[a ∩ a]", color = red];
                        "1,1   5"->"(2,2   6)"[label = "[a ∩ a]", color = red];
                        "0,0"->"1,1   5"[label = "[a-c ∩ a]", color = red];
                        "4,0   4   8"->"1,1   5"[label = "[a ∩ a]", color = red];
                        "(3,3   7)"->"4,0   4   8"[label = "[a-z ∩ a]", color = red];
                        "(2,2   6)"->"(3,3   7)"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "1,"->"2,0"[label = "[a-z]", color = violet];
                        "1,"->"4,"[label = "[ab]", color = violet];
                        "1,"->"2,"[label = "[a-z]", color = violet];
                        "2,0"->"3,1"[label = "[a-z ∩ ab]", color = red];
                        "4,"->"5,"[label = "[ab]", color = violet];
                        "5,"->"6,"[label = "[ab]", color = violet];
                        "6,"->"7,"[label = "[ab]", color = violet];
                        "7,"->"1,"[label = "[ab]", color = violet];
                        "7,"->"8,"[label = "[ab]", color = violet];
                        "3,1"->"7,2"[label = "[a ∩ a-z]", color = red];
                        "3,1"->"1,2"[label = "[a ∩ a-z]", color = red];
                        "7,2"->"1,3"[label = "[ab ∩ a]", color = red];
                        "7,2"->"8,3"[label = "[ab ∩ a]", color = red];
                        "1,2"->"2,3   0"[label = "[a-z ∩ a]", color = red];
                        "1,2"->"4,3"[label = "[ab ∩ a]", color = red];
                        "1,3"->"2,4   0"[label = "[a-z ∩ ab]", color = red];
                        "1,3"->"4,4"[label = "[ab ∩ ab]", color = red];
                        "8,3"->",4"[label = "[ab]", color = blue, style = dotted];
                        "2,3   0"->"3,4   1"[label = "[a-z ∩ ab]", color = red];
                        "4,3"->"5,4"[label = "[ab ∩ ab]", color = red];
                        "2,4   0"->"3,5   1"[label = "[a-z ∩ a-z]", color = red];
                        "4,4"->"5,5"[label = "[ab ∩ a-z]", color = red];
                        "3,4   1"->"7,5"[label = "[a ∩ a-z]", color = red];
                        "3,4   1"->"1,5   2"[label = "[a ∩ a-z]", color = red];
                        "5,4"->"6,5"[label = "[ab ∩ a-z]", color = red];
                        "3,5   1"->"7,6   2"[label = "[a ∩ a]", color = red];
                        "3,5   1"->"1,6   3"[label = "[a ∩ a]", color = red];
                        "5,5"->"6,6"[label = "[ab ∩ a]", color = red];
                        "7,5"->"1,6   2"[label = "[ab ∩ a]", color = red];
                        "7,5"->"8,6"[label = "[ab ∩ a]", color = red];
                        "1,5   2"->"2,6   3   0"[label = "[a-z ∩ a]", color = red];
                        "1,5   2"->"4,6"[label = "[ab ∩ a]", color = red];
                        "6,5"->"7,6"[label = "[ab ∩ a]", color = red];
                        "7,6   2"->"1,3"[label = "[ab ∩ a]", color = red];
                        "7,6   2"->"8,3"[label = "[ab ∩ a]", color = red];
                        "1,6   3"->"2,4   0"[label = "[a-z ∩ ab]", color = red];
                        "1,6   3"->"4,4"[label = "[ab ∩ ab]", color = red];
                        "6,6"->"7,"[label = "[ab]", color = violet];
                        "1,6   2"->"2,3   0"[label = "[a-z ∩ a]", color = red];
                        "1,6   2"->"4,3"[label = "[ab ∩ a]", color = red];
                        "8,6"->"8,"[label = "[]", color = red];
                        "2,6   3   0"->"3,4   1"[label = "[a-z ∩ ab]", color = red];
                        "4,6"->"5,"[label = "[ab]", color = violet];
                        "7,6"->"1,"[label = "[ab]", color = violet];
                        "7,6"->"8,"[label = "[ab]", color = violet];
                        ",4"->",5"[label = "[a-z]", color = blue, style = dotted];
                        ",5"->",6"[label = "[a]", color = blue, style = dotted];
                        ",6"->"8,"[label = "[]", color = blue, style = dotted];
                        "2,"->"3,"[label = "[a-z]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,7"->"5,"[label = "[a-z]", color = violet];
                        "3,6"->"4,7"[label = "[a-z ∩ ab]", color = red];
                        "2,5"->"3,6"[label = "[a ∩ ab]", color = red];
                        "1,4"->"2,5"[label = "[a ∩ ab]", color = red];
                        "1,4"->"1,0   4"[label = "[]", color = red];
                        "0,3"->"1,4"[label = "[a-c ∩ a]", color = red];
                        "4,3   7"->"1,4"[label = "[a ∩ a]", color = red];
                        "3,2   6"->"4,3   7"[label = "[a-z ∩ a]", color = red];
                        "2,1   5"->"3,2   6"[label = "[a ∩ a]", color = red];
                        "1,0   4"->"2,1   5"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                        "4,8"->"5,"[label = "[a-z]", color = violet];
                        "3,7"->"4,8"[label = "[a-z ∩ ab]", color = red];
                        "2,6"->"3,7"[label = "[a ∩ ab]", color = red];
                        "1,5"->"2,6"[label = "[a ∩ ab]", color = red];
                        "0,4"->"1,5"[label = "[a-c ∩ ab]", color = red];
                        "4,4   8"->"1,5"[label = "[a ∩ ab]", color = red];
                        "3,3   7"->"4,4   8"[label = "[a-z ∩ a]", color = red];
                        "2,2   6"->"3,3   7"[label = "[a ∩ a]", color = red];
                        "1,1   5"->"2,2   6"[label = "[a ∩ a]", color = red];
                        "1,1   5"->"(2,2   6)"[label = "[a ∩ a]", color = red];
                        "0,0"->"1,1   5"[label = "[a-c ∩ a]", color = red];
                        "4,0   4   8"->"1,1   5"[label = "[a ∩ a]", color = red];
                        "(3,3   7)"->"4,0   4   8"[label = "[a-z ∩ a]", color = red];
                        "(2,2   6)"->"(3,3   7)"[label = "[a ∩ a]", color = red];
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
        $result = $resultautomata->fa_to_dot();
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
                                6->7[label="[axy]"];
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
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,0"[label = "[a-z]", color = violet];
                        "1,"->"5,"[label = "[a-z]", color = violet];
                        "2,"->"8,"[label = "[as]", color = violet];
                        "3,"->"4,"[label = "[a-c]", color = violet];
                        "5,0"->"6,1"[label = "[^a ∩ a-z]", color = red];
                        "8,"->"9,"[label = "[ab]", color = violet];
                        "8,"->"1,"[label = "[ab]", color = violet];
                        "4,"->"8,"[label = "[ab]", color = violet];
                        "9,"->"2,"[label = "[ab]", color = violet];
                        "6,1"->"7,2"[label = "[a-c ∩ ab]", color = red];
                        "6,1"->"7,3"[label = "[a-c ∩ as]", color = red];
                        "7,2"->"8,6"[label = "[a-c ∩ a-c]", color = red];
                        "7,3"->"8,4"[label = "[a-c ∩ .]", color = red];
                        "8,6"->"9,7"[label = "[ab ∩ axy]", color = red];
                        "8,6"->"9,8"[label = "[ab ∩ .]", color = red];
                        "8,6"->"1,7"[label = "[ab ∩ axy]", color = red];
                        "8,6"->"1,8"[label = "[ab ∩ .]", color = red];
                        "8,4"->"9,5"[label = "[ab ∩ a-d]", color = red];
                        "8,4"->"1,5"[label = "[ab ∩ a-d]", color = red];
                        "9,7"->"2,9"[label = "[ab ∩ a]", color = red];
                        "9,7"->"2,2"[label = "[ab ∩ a-z]", color = red];
                        "9,8"->"2,9"[label = "[ab ∩ a-c]", color = red];
                        "9,8"->"2,1"[label = "[ab ∩ a-f]", color = red];
                        "1,7"->"2,9"[label = "[ab ∩ a]", color = red];
                        "1,7"->"2,2"[label = "[ab ∩ a-z]", color = red];
                        "1,7"->"3,9"[label = "[ab ∩ a]", color = red];
                        "1,7"->"3,2"[label = "[ab ∩ a-z]", color = red];
                        "1,7"->"5,9"[label = "[a-z ∩ a]", color = red];
                        "1,7"->"5,2"[label = "[a-z ∩ a-z]", color = red];
                        "1,8"->"2,9"[label = "[ab ∩ a-c]", color = red];
                        "1,8"->"2,1"[label = "[ab ∩ a-f]", color = red];
                        "1,8"->"3,9"[label = "[ab ∩ a-c]", color = red];
                        "1,8"->"3,1"[label = "[ab ∩ a-f]", color = red];
                        "1,8"->"5,9"[label = "[a-z ∩ a-c]", color = red];
                        "1,8"->"5,1"[label = "[a-z ∩ a-f]", color = red];
                        "9,5"->"2,6"[label = "[ab ∩ as01]", color = red];
                        "1,5"->"2,6"[label = "[ab ∩ as01]", color = red];
                        "1,5"->"3,6"[label = "[ab ∩ as01]", color = red];
                        "1,5"->"5,6"[label = "[a-z ∩ as01]", color = red];
                        "2,9"->"8,"[label = "[as]", color = violet];
                        "2,2"->"8,6"[label = "[as ∩ a-c]", color = red];
                        "2,1"->"8,2"[label = "[as ∩ ab]", color = red];
                        "2,1"->"8,3"[label = "[as ∩ as]", color = red];
                        "3,9"->"4,9"[label = "[a-c ∩ b-n]", color = red];
                        "3,2"->"4,6"[label = "[a-c ∩ a-c]", color = red];
                        "5,9"->"6,"[label = "[^a]", color = violet];
                        "5,2"->"6,6"[label = "[^a ∩ a-c]", color = red];
                        "3,1"->"4,2"[label = "[a-c ∩ ab]", color = red];
                        "3,1"->"4,3"[label = "[a-c ∩ as]", color = red];
                        "5,1"->"6,2"[label = "[^a ∩ ab]", color = red];
                        "5,1"->"6,3"[label = "[^a ∩ as]", color = red];
                        "2,6"->"8,7"[label = "[as ∩ axy]", color = red];
                        "2,6"->"8,8"[label = "[as ∩ .]", color = red];
                        "3,6"->"4,7"[label = "[a-c ∩ axy]", color = red];
                        "3,6"->"4,8"[label = "[a-c ∩ .]", color = red];
                        "5,6"->"6,7"[label = "[^a ∩ axy]", color = red];
                        "5,6"->"6,8"[label = "[^a ∩ .]", color = red];
                        "8,2"->"9,6"[label = "[ab ∩ a-c]", color = red];
                        "8,2"->"1,6"[label = "[ab ∩ a-c]", color = red];
                        "8,3"->"9,4"[label = "[ab ∩ .]", color = red];
                        "8,3"->"1,4"[label = "[ab ∩ .]", color = red];
                        "4,9"->"8,9"[label = "[ab ∩ b-n]", color = red];
                        "4,6"->"8,7"[label = "[ab ∩ axy]", color = red];
                        "4,6"->"8,8"[label = "[ab ∩ .]", color = red];
                        "6,6"->"7,7"[label = "[a-c ∩ axy]", color = red];
                        "6,6"->"7,8"[label = "[a-c ∩ .]", color = red];
                        "4,2"->"8,6"[label = "[ab ∩ a-c]", color = red];
                        "4,3"->"8,4"[label = "[ab ∩ .]", color = red];
                        "6,2"->"7,6"[label = "[a-c ∩ a-c]", color = red];
                        "6,3"->"7,4"[label = "[a-c ∩ .]", color = red];
                        "8,7"->"9,9"[label = "[ab ∩ a]", color = red];
                        "8,7"->"9,2"[label = "[ab ∩ a-z]", color = red];
                        "8,7"->"1,9"[label = "[ab ∩ a]", color = red];
                        "8,7"->"1,2"[label = "[ab ∩ a-z]", color = red];
                        "8,8"->"9,9"[label = "[ab ∩ a-c]", color = red];
                        "8,8"->"9,1"[label = "[ab ∩ a-f]", color = red];
                        "8,8"->"1,9"[label = "[ab ∩ a-c]", color = red];
                        "8,8"->"1,1"[label = "[ab ∩ a-f]", color = red];
                        "4,7"->"8,9"[label = "[ab ∩ a]", color = red];
                        "4,7"->"8,2"[label = "[ab ∩ a-z]", color = red];
                        "4,8"->"8,9"[label = "[ab ∩ a-c]", color = red];
                        "4,8"->"8,1"[label = "[ab ∩ a-f]", color = red];
                        "6,7"->"7,9"[label = "[a-c ∩ a]", color = red];
                        "6,7"->"7,2"[label = "[a-c ∩ a-z]", color = red];
                        "6,8"->"7,9"[label = "[a-c ∩ a-c]", color = red];
                        "6,8"->"7,1"[label = "[a-c ∩ a-f]", color = red];
                        "9,6"->"2,7"[label = "[ab ∩ axy]", color = red];
                        "9,6"->"2,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"2,7"[label = "[ab ∩ axy]", color = red];
                        "1,6"->"2,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"3,7"[label = "[ab ∩ axy]", color = red];
                        "1,6"->"3,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"5,7"[label = "[a-z ∩ axy]", color = red];
                        "1,6"->"5,8"[label = "[a-z ∩ .]", color = red];
                        "9,4"->"2,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"2,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"3,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"5,5"[label = "[a-z ∩ a-d]", color = red];
                        "8,9"->"9,9"[label = "[ab ∩ b-n]", color = red];
                        "8,9"->"1,9"[label = "[ab ∩ b-n]", color = red];
                        "7,7"->"8,9"[label = "[a-c ∩ a]", color = red];
                        "7,7"->"8,2"[label = "[a-c ∩ a-z]", color = red];
                        "7,8"->"8,9"[label = "[a-c ∩ a-c]", color = red];
                        "7,8"->"8,1"[label = "[a-c ∩ a-f]", color = red];
                        "7,6"->"8,7"[label = "[a-c ∩ axy]", color = red];
                        "7,6"->"8,8"[label = "[a-c ∩ .]", color = red];
                        "7,4"->"8,5"[label = "[a-c ∩ a-d]", color = red];
                        "9,9"->"2,9"[label = "[ab ∩ b-n]", color = red];
                        "9,9"->"9,"[label = "[]", color = red];
                        "9,2"->"2,6"[label = "[ab ∩ a-c]", color = red];
                        "1,9"->"2,9"[label = "[ab ∩ b-n]", color = red];
                        "1,9"->"3,9"[label = "[ab ∩ b-n]", color = red];
                        "1,9"->"5,9"[label = "[a-z ∩ b-n]", color = red];
                        "1,2"->"2,6"[label = "[ab ∩ a-c]", color = red];
                        "1,2"->"3,6"[label = "[ab ∩ a-c]", color = red];
                        "1,2"->"5,6"[label = "[a-z ∩ a-c]", color = red];
                        "9,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "9,1"->"2,3"[label = "[ab ∩ as]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"2,3"[label = "[ab ∩ as]", color = red];
                        "1,1"->"3,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"3,3"[label = "[ab ∩ as]", color = red];
                        "1,1"->"5,2"[label = "[a-z ∩ ab]", color = red];
                        "1,1"->"5,3"[label = "[a-z ∩ as]", color = red];
                        "8,1"->"9,2"[label = "[ab ∩ ab]", color = red];
                        "8,1"->"9,3"[label = "[ab ∩ as]", color = red];
                        "8,1"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "8,1"->"1,3"[label = "[ab ∩ as]", color = red];
                        "7,9"->"8,9"[label = "[a-c ∩ b-n]", color = red];
                        "7,1"->"8,2"[label = "[a-c ∩ ab]", color = red];
                        "7,1"->"8,3"[label = "[a-c ∩ as]", color = red];
                        "2,7"->"8,9"[label = "[as ∩ a]", color = red];
                        "2,7"->"8,2"[label = "[as ∩ a-z]", color = red];
                        "2,8"->"8,9"[label = "[as ∩ a-c]", color = red];
                        "2,8"->"8,1"[label = "[as ∩ a-f]", color = red];
                        "3,7"->"4,9"[label = "[a-c ∩ a]", color = red];
                        "3,7"->"4,2"[label = "[a-c ∩ a-z]", color = red];
                        "3,8"->"4,9"[label = "[a-c ∩ a-c]", color = red];
                        "3,8"->"4,1"[label = "[a-c ∩ a-f]", color = red];
                        "5,7"->"6,9"[label = "[^a ∩ a]", color = red];
                        "5,7"->"6,2"[label = "[^a ∩ a-z]", color = red];
                        "5,8"->"6,9"[label = "[^a ∩ a-c]", color = red];
                        "5,8"->"6,1"[label = "[^a ∩ a-f]", color = red];
                        "2,5"->"8,6"[label = "[as ∩ as01]", color = red];
                        "3,5"->"4,6"[label = "[a-c ∩ as01]", color = red];
                        "5,5"->"6,6"[label = "[^a ∩ as01]", color = red];
                        "8,5"->"9,6"[label = "[ab ∩ as01]", color = red];
                        "8,5"->"1,6"[label = "[ab ∩ as01]", color = red];
                        "2,3"->"8,4"[label = "[as ∩ .]", color = red];
                        "3,3"->"4,4"[label = "[a-c ∩ .]", color = red];
                        "5,3"->"6,4"[label = "[^a ∩ .]", color = red];
                        "9,3"->"2,4"[label = "[ab ∩ .]", color = red];
                        "1,3"->"2,4"[label = "[ab ∩ .]", color = red];
                        "1,3"->"3,4"[label = "[ab ∩ .]", color = red];
                        "1,3"->"5,4"[label = "[a-z ∩ .]", color = red];
                        "4,1"->"8,2"[label = "[ab ∩ ab]", color = red];
                        "4,1"->"8,3"[label = "[ab ∩ as]", color = red];
                        "6,9"->"7,9"[label = "[a-c ∩ b-n]", color = red];
                        "4,4"->"8,5"[label = "[ab ∩ a-d]", color = red];
                        "6,4"->"7,5"[label = "[a-c ∩ a-d]", color = red];
                        "2,4"->"8,5"[label = "[as ∩ a-d]", color = red];
                        "3,4"->"4,5"[label = "[a-c ∩ a-d]", color = red];
                        "5,4"->"6,5"[label = "[^a ∩ a-d]", color = red];
                        "7,5"->"8,6"[label = "[a-c ∩ as01]", color = red];
                        "4,5"->"8,6"[label = "[ab ∩ as01]", color = red];
                        "6,5"->"7,6"[label = "[a-c ∩ as01]", color = red];
                        "5,"->"6,"[label = "[^a]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "7,"->"8,"[label = "[a-c]", color = violet];
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
        $result = $resultautomata->fa_to_dot();
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
                                6->7[label="[axy]"];
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
                        "7,"->"8,"[label = "[a-c]", color = violet];
                        "7,"->"8,0"[label = "[a-c]", color = violet];
                        "1,"->"2,"[label = "[ab]", color = violet];
                        "1,"->"3,"[label = "[ab]", color = violet];
                        "1,"->"5,"[label = "[a-z]", color = violet];
                        "1,"->"5,0"[label = "[a-z]", color = violet];
                        "1,"->"2,0"[label = "[ab]", color = violet];
                        "1,"->"3,0"[label = "[ab]", color = violet];
                        "3,"->"4,"[label = "[a-c]", color = violet];
                        "3,"->"4,0"[label = "[a-c]", color = violet];
                        "6,"->"7,"[label = "[a-c]", color = violet];
                        "6,"->"7,0"[label = "[a-c]", color = violet];
                        "0,"->"1,"[label = "[a-c]", color = violet];
                        "0,"->"1,0"[label = "[a-c]", color = violet];
                        "5,9"->"6,"[label = "[^a]", color = violet];
                        "1,9"->"5,9"[label = "[a-z ∩ b-n]", color = red];
                        "1,9"->"3,9"[label = "[ab ∩ b-n]", color = red];
                        "1,7"->"5,9"[label = "[a-z ∩ a]", color = red];
                        "1,7"->"2,2"[label = "[ab ∩ a-z]", color = red];
                        "1,7"->"3,9"[label = "[ab ∩ a]", color = red];
                        "1,7"->"3,2"[label = "[ab ∩ a-z]", color = red];
                        "1,7"->"5,2"[label = "[a-z ∩ a-z]", color = red];
                        "1,8"->"5,9"[label = "[a-z ∩ a-c]", color = red];
                        "1,8"->"3,9"[label = "[ab ∩ a-c]", color = red];
                        "1,8"->"3,1"[label = "[ab ∩ a-f]", color = red];
                        "1,8"->"5,1"[label = "[a-z ∩ a-f]", color = red];
                        "1,8"->"2,1"[label = "[ab ∩ a-f]", color = red];
                        "0,9"->"1,9"[label = "[a-c ∩ b-n]", color = red];
                        "0,7"->"1,9"[label = "[a-c ∩ a]", color = red];
                        "0,7"->"1,2"[label = "[a-c ∩ a-z]", color = red];
                        "0,8"->"1,9"[label = "[a-c ∩ a-c]", color = red];
                        "0,8"->"1,1"[label = "[a-c ∩ a-f]", color = red];
                        "8,9"->"1,9"[label = "[ab ∩ b-n]", color = red];
                        "8,7"->"1,9"[label = "[ab ∩ a]", color = red];
                        "8,7"->"9,2"[label = "[ab ∩ a-z]", color = red];
                        "8,7"->"1,2"[label = "[ab ∩ a-z]", color = red];
                        "8,8"->"1,9"[label = "[ab ∩ a-c]", color = red];
                        "8,8"->"9,1"[label = "[ab ∩ a-f]", color = red];
                        "8,8"->"1,1"[label = "[ab ∩ a-f]", color = red];
                        "0,6"->"1,7"[label = "[a-c ∩ axy]", color = red];
                        "0,6"->"1,8"[label = "[a-c ∩ .]", color = red];
                        "8,6"->"1,7"[label = "[ab ∩ axy]", color = red];
                        "8,6"->"1,8"[label = "[ab ∩ .]", color = red];
                        "8,6"->"9,7"[label = "[ab ∩ axy]", color = red];
                        "8,6"->"9,8"[label = "[ab ∩ .]", color = red];
                        "2,7"->"8,9"[label = "[as ∩ a]", color = red];
                        "2,7"->"8,2"[label = "[as ∩ a-z]", color = red];
                        "2,8"->"8,9"[label = "[as ∩ a-c]", color = red];
                        "2,8"->"8,1"[label = "[as ∩ a-f]", color = red];
                        "4,9"->"8,9"[label = "[ab ∩ b-n]", color = red];
                        "4,7"->"8,9"[label = "[ab ∩ a]", color = red];
                        "4,7"->"8,2"[label = "[ab ∩ a-z]", color = red];
                        "4,8"->"8,9"[label = "[ab ∩ a-c]", color = red];
                        "4,8"->"8,1"[label = "[ab ∩ a-f]", color = red];
                        "7,9"->"8,9"[label = "[a-c ∩ b-n]", color = red];
                        "7,7"->"8,9"[label = "[a-c ∩ a]", color = red];
                        "7,7"->"8,2"[label = "[a-c ∩ a-z]", color = red];
                        "7,8"->"8,9"[label = "[a-c ∩ a-c]", color = red];
                        "7,8"->"8,1"[label = "[a-c ∩ a-f]", color = red];
                        "2,6"->"8,7"[label = "[as ∩ axy]", color = red];
                        "2,6"->"8,8"[label = "[as ∩ .]", color = red];
                        "4,6"->"8,7"[label = "[ab ∩ axy]", color = red];
                        "4,6"->"8,8"[label = "[ab ∩ .]", color = red];
                        "7,6"->"8,7"[label = "[a-c ∩ axy]", color = red];
                        "7,6"->"8,8"[label = "[a-c ∩ .]", color = red];
                        "2,2"->"8,6"[label = "[as ∩ a-c]", color = red];
                        "2,5"->"8,6"[label = "[as ∩ as01]", color = red];
                        "4,2"->"8,6"[label = "[ab ∩ a-c]", color = red];
                        "4,5"->"8,6"[label = "[ab ∩ as01]", color = red];
                        "7,2"->"8,6"[label = "[a-c ∩ a-c]", color = red];
                        "7,5"->"8,6"[label = "[a-c ∩ as01]", color = red];
                        "9,6"->"2,7"[label = "[ab ∩ axy]", color = red];
                        "9,6"->"2,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"2,7"[label = "[ab ∩ axy]", color = red];
                        "1,6"->"2,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"3,7"[label = "[ab ∩ axy]", color = red];
                        "1,6"->"3,8"[label = "[ab ∩ .]", color = red];
                        "1,6"->"5,7"[label = "[a-z ∩ axy]", color = red];
                        "1,6"->"5,8"[label = "[a-z ∩ .]", color = red];
                        "3,9"->"4,9"[label = "[a-c ∩ b-n]", color = red];
                        "3,7"->"4,9"[label = "[a-c ∩ a]", color = red];
                        "3,7"->"4,2"[label = "[a-c ∩ a-z]", color = red];
                        "3,8"->"4,9"[label = "[a-c ∩ a-c]", color = red];
                        "3,8"->"4,1"[label = "[a-c ∩ a-f]", color = red];
                        "3,6"->"4,7"[label = "[a-c ∩ axy]", color = red];
                        "3,6"->"4,8"[label = "[a-c ∩ .]", color = red];
                        "6,9"->"7,9"[label = "[a-c ∩ b-n]", color = red];
                        "6,7"->"7,9"[label = "[a-c ∩ a]", color = red];
                        "6,7"->"7,2"[label = "[a-c ∩ a-z]", color = red];
                        "6,8"->"7,9"[label = "[a-c ∩ a-c]", color = red];
                        "6,8"->"7,1"[label = "[a-c ∩ a-f]", color = red];
                        "6,6"->"7,7"[label = "[a-c ∩ axy]", color = red];
                        "6,6"->"7,8"[label = "[a-c ∩ .]", color = red];
                        "9,2"->"2,6"[label = "[ab ∩ a-c]", color = red];
                        "9,5"->"2,6"[label = "[ab ∩ as01]", color = red];
                        "1,2"->"2,6"[label = "[ab ∩ a-c]", color = red];
                        "1,2"->"3,6"[label = "[ab ∩ a-c]", color = red];
                        "1,2"->"5,6"[label = "[a-z ∩ a-c]", color = red];
                        "1,5"->"2,6"[label = "[ab ∩ as01]", color = red];
                        "1,5"->"3,6"[label = "[ab ∩ as01]", color = red];
                        "1,5"->"5,6"[label = "[a-z ∩ as01]", color = red];
                        "3,2"->"4,6"[label = "[a-c ∩ a-c]", color = red];
                        "3,5"->"4,6"[label = "[a-c ∩ as01]", color = red];
                        "6,2"->"7,6"[label = "[a-c ∩ a-c]", color = red];
                        "6,5"->"7,6"[label = "[a-c ∩ as01]", color = red];
                        "9,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "9,1"->"2,3"[label = "[ab ∩ as]", color = red];
                        "9,7"->"2,2"[label = "[ab ∩ a-z]", color = red];
                        "1,1"->"2,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"3,2"[label = "[ab ∩ ab]", color = red];
                        "1,1"->"5,2"[label = "[a-z ∩ ab]", color = red];
                        "1,1"->"5,3"[label = "[a-z ∩ as]", color = red];
                        "1,1"->"2,3"[label = "[ab ∩ as]", color = red];
                        "1,1"->"3,3"[label = "[ab ∩ as]", color = red];
                        "9,4"->"2,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"2,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"3,5"[label = "[ab ∩ a-d]", color = red];
                        "1,4"->"5,5"[label = "[a-z ∩ a-d]", color = red];
                        "3,1"->"4,2"[label = "[a-c ∩ ab]", color = red];
                        "3,1"->"4,3"[label = "[a-c ∩ as]", color = red];
                        "3,4"->"4,5"[label = "[a-c ∩ a-d]", color = red];
                        "6,1"->"7,2"[label = "[a-c ∩ ab]", color = red];
                        "6,1"->"7,3"[label = "[a-c ∩ as]", color = red];
                        "6,4"->"7,5"[label = "[a-c ∩ a-d]", color = red];
                        "8,2"->"9,6"[label = "[ab ∩ a-c]", color = red];
                        "8,2"->"1,6"[label = "[ab ∩ a-c]", color = red];
                        "8,5"->"9,6"[label = "[ab ∩ as01]", color = red];
                        "8,5"->"1,6"[label = "[ab ∩ as01]", color = red];
                        "0,2"->"1,6"[label = "[a-c ∩ a-c]", color = red];
                        "0,5"->"1,6"[label = "[a-c ∩ as01]", color = red];
                        "5,7"->"6,9"[label = "[^a ∩ a]", color = red];
                        "5,7"->"6,2"[label = "[^a ∩ a-z]", color = red];
                        "5,8"->"6,9"[label = "[^a ∩ a-c]", color = red];
                        "5,8"->"6,1"[label = "[^a ∩ a-f]", color = red];
                        "5,6"->"6,7"[label = "[^a ∩ axy]", color = red];
                        "5,6"->"6,8"[label = "[^a ∩ .]", color = red];
                        "5,2"->"6,6"[label = "[^a ∩ a-c]", color = red];
                        "5,5"->"6,6"[label = "[^a ∩ as01]", color = red];
                        "8,1"->"9,2"[label = "[ab ∩ ab]", color = red];
                        "8,1"->"1,2"[label = "[ab ∩ ab]", color = red];
                        "8,1"->"1,3"[label = "[ab ∩ as]", color = red];
                        "8,1"->"9,3"[label = "[ab ∩ as]", color = red];
                        "8,4"->"9,5"[label = "[ab ∩ a-d]", color = red];
                        "8,4"->"1,5"[label = "[ab ∩ a-d]", color = red];
                        "0,1"->"1,2"[label = "[a-c ∩ ab]", color = red];
                        "0,1"->"1,3"[label = "[a-c ∩ as]", color = red];
                        "0,4"->"1,5"[label = "[a-c ∩ a-d]", color = red];
                        "5,1"->"6,2"[label = "[^a ∩ ab]", color = red];
                        "5,1"->"6,3"[label = "[^a ∩ as]", color = red];
                        "5,4"->"6,5"[label = "[^a ∩ a-d]", color = red];
                        "8,0"->"9,1"[label = "[ab ∩ a-z]", color = red];
                        "8,0"->"1,1"[label = "[ab ∩ a-z]", color = red];
                        "0,0"->"1,1"[label = "[a-c ∩ a-z]", color = red];
                        "8,3"->"9,4"[label = "[ab ∩ .]", color = red];
                        "8,3"->"1,4"[label = "[ab ∩ .]", color = red];
                        "0,3"->"1,4"[label = "[a-c ∩ .]", color = red];
                        "1,0"->"3,1"[label = "[ab ∩ a-z]", color = red];
                        "1,0"->"5,1"[label = "[a-z ∩ a-z]", color = red];
                        "1,0"->"2,1"[label = "[ab ∩ a-z]", color = red];
                        "1,3"->"3,4"[label = "[ab ∩ .]", color = red];
                        "1,3"->"5,4"[label = "[a-z ∩ .]", color = red];
                        "1,3"->"2,4"[label = "[ab ∩ .]", color = red];
                        "5,0"->"6,1"[label = "[^a ∩ a-z]", color = red];
                        "5,3"->"6,4"[label = "[^a ∩ .]", color = red];
                        "2,1"->"8,2"[label = "[as ∩ ab]", color = red];
                        "2,1"->"8,3"[label = "[as ∩ as]", color = red];
                        "4,1"->"8,2"[label = "[ab ∩ ab]", color = red];
                        "4,1"->"8,3"[label = "[ab ∩ as]", color = red];
                        "7,1"->"8,2"[label = "[a-c ∩ ab]", color = red];
                        "7,1"->"8,3"[label = "[a-c ∩ as]", color = red];
                        "2,4"->"8,5"[label = "[as ∩ a-d]", color = red];
                        "4,4"->"8,5"[label = "[ab ∩ a-d]", color = red];
                        "7,4"->"8,5"[label = "[a-c ∩ a-d]", color = red];
                        "2,0"->"8,1"[label = "[as ∩ a-z]", color = red];
                        "4,0"->"8,1"[label = "[ab ∩ a-z]", color = red];
                        "7,0"->"8,1"[label = "[a-c ∩ a-z]", color = red];
                        "2,3"->"8,4"[label = "[as ∩ .]", color = red];
                        "4,3"->"8,4"[label = "[ab ∩ .]", color = red];
                        "7,3"->"8,4"[label = "[a-c ∩ .]", color = red];
                        "9,0"->"2,1"[label = "[ab ∩ a-z]", color = red];
                        "9,8"->"2,1"[label = "[ab ∩ a-f]", color = red];
                        "3,0"->"4,1"[label = "[a-c ∩ a-z]", color = red];
                        "6,0"->"7,1"[label = "[a-c ∩ a-z]", color = red];
                        "9,3"->"2,4"[label = "[ab ∩ .]", color = red];
                        "3,3"->"4,4"[label = "[a-c ∩ .]", color = red];
                        "6,3"->"7,4"[label = "[a-c ∩ .]", color = red];
                        ",9"->",9"[label = "[b-n]", color = blue, style = dotted];
                        ",9"->"0,9"[label = "[b-n]", color = blue, style = dotted];
                        ",9"->"9,"[label = "[]", color = blue, style = dotted];
                        ",7"->",9"[label = "[a]", color = blue, style = dotted];
                        ",7"->",2"[label = "[a-z]", color = blue, style = dotted];
                        ",7"->"0,9"[label = "[a]", color = blue, style = dotted];
                        ",7"->"0,2"[label = "[a-z]", color = blue, style = dotted];
                        ",8"->",9"[label = "[a-c]", color = blue, style = dotted];
                        ",8"->",1"[label = "[a-f]", color = blue, style = dotted];
                        ",8"->"0,9"[label = "[a-c]", color = blue, style = dotted];
                        ",8"->"0,1"[label = "[a-f]", color = blue, style = dotted];
                        ",6"->",7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->",8"[label = "[.]", color = blue, style = dotted];
                        ",6"->"0,7"[label = "[axy]", color = blue, style = dotted];
                        ",6"->"0,8"[label = "[.]", color = blue, style = dotted];
                        ",2"->",6"[label = "[a-c]", color = blue, style = dotted];
                        ",2"->"0,6"[label = "[a-c]", color = blue, style = dotted];
                        ",5"->",6"[label = "[as01]", color = blue, style = dotted];
                        ",5"->"0,6"[label = "[as01]", color = blue, style = dotted];
                        ",1"->",2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->",3"[label = "[as]", color = blue, style = dotted];
                        ",1"->"0,2"[label = "[ab]", color = blue, style = dotted];
                        ",1"->"0,3"[label = "[as]", color = blue, style = dotted];
                        ",4"->",5"[label = "[a-d]", color = blue, style = dotted];
                        ",4"->"0,5"[label = "[a-d]", color = blue, style = dotted];
                        ",0"->",1"[label = "[a-z]", color = blue, style = dotted];
                        ",0"->"0,1"[label = "[a-z]", color = blue, style = dotted];
                        ",3"->",4"[label = "[.]", color = blue, style = dotted];
                        ",3"->"0,4"[label = "[.]", color = blue, style = dotted];
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
        $result = $resultautomata->fa_to_dot();
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}