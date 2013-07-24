<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');

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
        $rescharset = $leaf1->intersect($leaf2);
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps() {
        $leaf1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($transition1, $resulttran, 'Result transition is not equal to expected');
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
        $restran = new qtype_preg_nfa_transition(0, $leaf1, 1);     //0->1[label="[()]"];
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
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $leaf2 = $lexer->nextToken()->value;
        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert1;
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\Aa-c]"];
        $transition1->pregleaf->mergedassertions[] = $assert2;
        $rescharset = $leaf1->intersect($leaf2);
        $rescharset->mergedassertions[] = $assert2;
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1); //0->1[label="[\\Aa]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_ununited_asserts() {
        $lexer = $this->create_lexer('[a]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $leaf1;
        $assert1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $assert2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
        $rescharset = $leaf1->intersect($leaf2);
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1);  //0->1[label="[^$a]"];
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
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $transition1 = new qtype_preg_nfa_transition(0, $leaf, 1);      //0->1[label="[(^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition1->subpatt_start[] = $subpatt;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf, 1);      //0->1[label="[(^a]"];
        $transition2->pregleaf->mergedassertions[] = $assert;
        $transition2->subpatt_start[] = $subpatt;
        $rescharset = $leaf->intersect($leaf);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1);   //0->1[label="[(^a]"];
        $restran->subpatt_start[] = $subpatt;
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_and_character() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $transition1 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition2 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[a]"];
        $rescharset = $leaf->intersect($leaf);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1);
        $resulttran = $transition1->intersect($transition2);        //0->1[label="[^a]"];
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_character_tag() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $subpatt = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $transition1 = new qtype_preg_nfa_transition(0, $leaf, 1);  //0->1[label="[(^a]"];
        $transition1->pregleaf->mergedassertions[] = $assert;
        $transition1->subpatt_start[] = $subpatt;
        $transition2 = new qtype_preg_nfa_transition(0, $leaf, 1);  //0->1[label="[a]"];
        $rescharset = $leaf->intersect($leaf);
        $rescharset->mergedassertions[] = $assert;
        $restran = new qtype_preg_nfa_transition(0, $rescharset, 1);
        $restran->subpatt_start[] = $subpatt;                       //0->1[label="[(^a]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_and_unmerged() {
        $lexer = $this->create_lexer('[a-c]');
        $leaf1 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $leaf1->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^a-c]"];
        $leaf2 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[^]"];

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($transition1, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_unmerged_asserts() {
        $leaf1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $leaf2 = $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_ESC_A);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\A]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($transition2, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_ans_assert() {
        $leaf1 = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $leaf2 = $assert = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($transition1, $resulttran, 'Result transition is not equal to expected');
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
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
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
        $assert = new qtype_preg_leaf_assert(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        $leaf1->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_nfa_transition(0, $leaf1, 1); //0->1[label="[^a]"];
        $transition2 = new qtype_preg_nfa_transition(0, $leaf2, 1); //0->1[label="[01]"];
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }
}