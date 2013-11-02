<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');


class preg_fa_read_fa_tests extends PHPUnit_Framework_TestCase {

    public function test_disclosure_tags() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[((/(a-z)/]"];
                    1->2[label="[b-k/)]"];
                    2->3[label="[(/c-z/))]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $from = 0;
        $to = 2;
        $transition = new qtype_preg_nfa_transition($from,$pregleaf, $to);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->subpatt_start[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->subpatt_start[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[b-k]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $from = 2;
        $to = 3;
        $transition = new qtype_preg_nfa_transition($from,$pregleaf, $to);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->subpatt_end[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[c-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $from = 3;
        $to = 1;
        $transition = new qtype_preg_nfa_transition($from,$pregleaf, $to);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->subpatt_start[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->subpatt_end[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->subpatt_end[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_loop() {
        $dotdescription = 'digraph example {
                    0;
                    4;
                    0->1[label="[0-9]"];
                    1->2[label="[abc]"];
                    1->4[label="[01]"];
                    2->2[label="[a-z]"];
                    2->3[label="[-?,]"];
                    3->4[label="[a]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('4');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[abc]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[01]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[-?,]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 4);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(4,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_indirect_loop() {
        $dotdescription = 'digraph example {
                    0;
                    4;
                    0->1[label="[a-c]"];
                    1->2[label="[0-9]"];
                    2->4[label="[a-f]"];
                    0->3[label="[01]"];
                    3->4[label="[y]"];
                    4->0[label="[bc]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('4');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[a-c]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-f]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[01]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 4);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[y]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(4,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[bc]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(1,$pregleaf, 0);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_hidden_characters() {
        $dotdescription = 'digraph example {
                    0;
                    6;
                    0->1[label="[\\\\\\-]"];
                    1->2[label="[\\$\\Z]"];
                    2->3[label="[\\[\\]]"];
                    3->4[label="[\\^\\A]"];
                    4->5[label="[\\"\\/\\.]"];
                    5->6[label="[\\(\\)]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('6');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_state('4');
        $resultautomata->add_state('5');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[\\\\\\-]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\$]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z);
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\[\\]]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 4);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\^]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        $transition = new qtype_preg_nfa_transition(4,$pregleaf, 5);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\"\\/\\.]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(5,$pregleaf, 6);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\(\\)]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(6,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_several_endstates() {
        $dotdescription = 'digraph example {
                    0;
                    1;2;4;
                    0->1[label="[a-c]"];
                    1->2[label="[0-9]"];
                    2->4[label="[a-f]"];
                    0->3[label="[01]"];
                    3->4[label="[y]"];
                    4->0[label="[bc]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('4');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        $resultautomata->add_end_state(2);
        $resultautomata->add_end_state(3);
        // Fill pregleaf.
        $chars = '[a-c]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(1,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-f]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[01]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 4);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[y]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(4,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[bc]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 0);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_character_ranges() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[a-kn-z]"];
                    1->2[label="[a-jxy]"];
                    2->3[label="[abc-hl-x]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[a-kn-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-jxy]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[abc-hl-x]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_asserts() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[0-9]"];
                    1->2 [label="[$\\\\z]"];
                    2->3 [label="[^a-z]"];
                    0->3[label="[xy]"];
                    1->3 [label="[\\\\A]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
         $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\\\z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $pregleaf->mergedassertions[] = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[xy]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\\\A]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_unitedstate() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->"1   2"[label="[0-9]"];
                    "1   2"->3 [label="[\\\\A0-9]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1   2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[\\\\A0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_different_automata(){
        $dotdescription = 'digraph example {
                    "0,";
                    ",2";
                    "0,"->"1,0"[label="[a-z]",color=violet];
                    "1,0"->"2,1"[label="[0-9]",color=red];
                    "2,1"->",2"[label="[a-z]",color=blue];
                    }';
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0,');
        $resultautomata->add_state(',2');
        $resultautomata->add_state('1,0');
        $resultautomata->add_state('2,1');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2,$pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(3,$pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $transition->set_transition_type();
        $transition->consumeschars = false;
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_eps_transition() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->2[label="[]"];
                    0->1[label="[0-9]"];
                    1->3[label="[]"];
                    2->3[label="[a-z]"];
                    }';
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('2');
        $resultautomata->add_state('1');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0, $pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition(3, $pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2, $pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_imposition_transitions() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->2[label="[.]"];
                    0->1[label="[0-9]"];
                    1->3[label="[.]"];
                    2->3[label="[a-z]"];
                    }';
        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('2');
        $resultautomata->add_state('1');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        // Fill pregleaf.
        $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition(0,$pregleaf, 2);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[0-9]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(0, $pregleaf, 3);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_nfa_transition(3, $pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);
        // Fill pregleaf.
        $chars = '[a-z]';
        StringStreamController::createRef('regex', $chars);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $pregleaf = $lexer->nextToken()->value;
        $transition = new qtype_preg_nfa_transition(2, $pregleaf, 1);
        $transition->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
        $transition->set_transition_type();
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }
}
