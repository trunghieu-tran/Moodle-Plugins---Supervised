<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_copy_branches_test extends PHPUnit_Framework_TestCase {

    /*public function test_copy_whole_branch() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph example {
                        "0,";
                        "1,";"4,";
                        "0,"->"1,"[label="[df]"];
                        "0,"->"2,"[label="[0-9]"];
                        "2,"->"3,"[label="[01]"];
                        "3,"->"4,"[label="[.]"];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
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
        $dotresult = 'digraph example {
                        "0,";
                        "1,";"3,";
                        "0,"->"1,"[label="[ab]"];
                        "0,"->"2,"[label="[0-9]"];
                        "2,"->"3,"[label="[01]"];
                        "3,"->"0,"[label="[a]"];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }*/

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
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    /*public function test_copy_not_empty_direct() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph example {
                        "0,";
                        "4,";
                        "0,"->"1,"[label="[df]"];
                        "0,"->"2,"[label="[0-9]"];
                        "1,"->"3,"[label="[abc]"];
                        "2,"->"3,"[label="[01]"];
                        "3,"->"4,"[label="[.]"];
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
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_direct_has_states_for_coping() {
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
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }*/

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
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    /*public function test_coping_back() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";
                        "3,"->"4,"[label="[.]",color=violet];
                        "1,"->"3,"[label="[abc]",color=violet];
                        "2,"->"3,"[label="[01]",color=violet];
                        "0,"->"1,"[label="[df]",color=violet];
                        "0,"->"2,"[label="[0-9]",color=violet];
                        "0,"->"1,"[label="[df]",color=violet];
                        "0,"->"2,"[label="[0-9]",color=violet];
                    }';

        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('0', $numbers);
        $oldfront = array(array_search('4', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 1);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_second_automata() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph example {
                        ",0";
                        ",1";",4";
                        ",0"->",1"[label="[df]"];
                        ",0"->",2"[label="[0-9]"];
                        ",2"->",3"[label="[01]"];
                        ",3"->",4"[label="[.]"];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
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
        $dotresult = 'digraph example {
                        ",0";
                        ",1";",3";
                        ",0"->",1"[label="[ab]"];
                        ",0"->",2"[label="[0-9]"];
                        ",2"->",3"[label="[01]"];
                        ",3"->",0"[label="[a]"];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = new qtype_preg_nfa(0, 0, 0, array());
        $source->read_fa($sourcedescription, $origin);
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $direct = new qtype_preg_nfa(0, 0, 0, array());
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('1', $numbers);
        $oldfront = array(array_search('0', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_not_empty_second() {
        $sourcedescription = 'digraph example {
                                0;
                                4;
                                0->1[label="[df]"];
                                0->2[label="[0-9]"];
                                1->3[label="[abc]"];
                                2->3[label="[01]"];
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph example {
                        ",0";
                        ",4";
                        ",0"->",1"[label="[df]"];
                        ",0"->",2"[label="[0-9]"];
                        ",1"->",3"[label="[abc]"];
                        ",2"->",3"[label="[01]"];
                        ",3"->",4"[label="[.]"];
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
        $direct->read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array_search('4', $numbers);
        $oldfront = array(array_search('2', $numbers));
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult);
        var_dump($resultautomata);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }*/

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
        $resultautomata = $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $result = new qtype_preg_nfa(0, 0, 0, array());
        $result->read_fa($dotresult, $origin);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }
}