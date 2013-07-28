<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class qtype_preg_fa_copy_branches_test extends PHPUnit_Framework_TestCase {

    public function test_copy_whole_branch() {
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
                        "1,";"4,";
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0123456789]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[.]", color = violet];
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
        $result = $direct->write_fa();
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
                        "0,"->"2,"[label = "[0123456789]", color = violet];
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
        $result = $direct->write_fa();
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
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0123456789]", color = violet];
                        "1,"->"3,"[label = "[abc]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "3,"->"4,"[label = "[.]", color = violet];
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
        $result = $direct->write_fa();
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
        $result = $direct->write_fa();
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
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        "0,";
                        "4,";
                        "3,"->"4,"[label = "[.]", color = violet];
                        "1,"->"3,"[label = "[abc]", color = violet];
                        "2,"->"3,"[label = "[01]", color = violet];
                        "0,"->"1,"[label = "[df]", color = violet];
                        "0,"->"2,"[label = "[0123456789]", color = violet];
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
        $result = $direct->write_fa();
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
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        ",1";",4";
                        ",0"->",1"[label = "[df]", color = blue];
                        ",0"->",2"[label = "[0123456789]", color = blue];
                        ",2"->",3"[label = "[01]", color = blue];
                        ",3"->",4"[label = "[.]", color = blue];
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
        $result = $direct->write_fa();
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
                        ",0"->",1"[label = "[ab]", color = blue];
                        ",0"->",2"[label = "[0123456789]", color = blue];
                        ",2"->",3"[label = "[01]", color = blue];
                        ",3"->",0"[label = "[a]", color = blue];
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
        $result = $direct->write_fa();
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
                                3->4[label="[.]"];
                            }';
        $dotresult = 'digraph res {
                        ",0";
                        ",4";
                        ",0"->",1"[label = "[df]", color = blue];
                        ",0"->",2"[label = "[0123456789]", color = blue];
                        ",1"->",3"[label = "[abc]", color = blue];
                        ",2"->",3"[label = "[01]", color = blue];
                        ",3"->",4"[label = "[.]", color = blue];
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
        $result = $direct->write_fa();
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