<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines a tests for lexer
 *
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/lexical_rules_operators.php');
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/lexical_finite_automata.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');


class block_formal_langs_syntax_lexer_test extends PHPUnit_Framework_TestCase {

    /**
     * Tests basic primitive rules
     */
    public function test_primitives() {
        $h = new block_formal_langs_lexical_test_helper();
        $string = '
            0,eps,1;
        ';
        $p = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $h->test_table($p,$string,$this);
        $string = '
            0,.,1;
        ';
        $p = block_formal_langs_lexical_matching_rule::all_matching_rule();
        $h->test_table($p,$string,$this);
        $string = '
            0,[abc],1;
        ';
        $p = block_formal_langs_lexical_matching_rule::charclass_rule(array('a', 'b', 'c'));
        $h->test_table($p,$string,$this);
        $string = '
            0,[^abc],1;
        ';
        $p = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b', 'c'));
        $h->test_table($p,$string,$this);
        $string = '
            0,[a],1;
        ';
        $p = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $h->test_table($p,$string,$this);
    }

    /**
     * Tests a concatenation lexing rules
     */
    public function test_concatenation() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $p2 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $c = new block_formal_langs_lexical_concat_operator(array($p1, $p2));
        $string = '
          0,[a],1;
          1,eps,2;
          2,[a],3;
        ';
        $h->test_table($c,$string,$this);
    }

    /**
     * Tests an alternative operator
     */
    public function test_alternative() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $p2 = block_formal_langs_lexical_matching_rule::simple_rule('b');
        $c = new block_formal_langs_lexical_alternative_operator(array($p1, $p2));
        $string = '
          0,eps,1:3;
          1,[a],2;
          3,[b],4;
          2,eps,5;
          4,eps,5;
        ';
        $h->test_table($c,$string,$this);
    }
    /**
     * Tests an alternative operator without concatenation
     */
    public function test_alternative_non_concatenated() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $p2 = block_formal_langs_lexical_matching_rule::simple_rule('b');
        $c = block_formal_langs_lexical_alternative_operator::build_non_concatenated(array($p1, $p2));
        $string = '
          0,eps,1:3;
          1,[a],2;
          3,[b],4;
        ';
        $table = $h->table_from_string($string);
        $dc = $c->to_digraph();
        $this->assertTrue( $table->equal($dc), $table->dump_compare($dc));
    }
    /**
     * Tests invalid quantifiers
     */
    public function test_invalid_quantifiers() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,0,0);
        $string = '
            0,eps,1;
        ';
        $h->test_table($top,$string,$this);
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,2,0);
        $string = '
            0,eps,1;
        ';
        $h->test_table($top,$string,$this);
    }
    /**
     * Tests quantifier from null to infinite
     */
    public function test_infinite_quantifiers() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,0,null);
        $string = '
            0,eps,1:3;
            1,eps,2;
            3,[a],4;
            4,eps,3:5;
            2,eps,5;
        ';
        $h->test_table($top,$string,$this);

        $top = new block_formal_langs_lexical_from_to_quantifier($p1,1,null);
        $string = '
            0,[a],1;
            1,eps,0;
        ';
        $h->test_table($top,$string,$this);
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,2,null);
        $string = '
            0,[a],1;
            1,eps,2;
            2,[a],3;
            3,eps,2;
        ';
        $h->test_table($top,$string,$this);
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,3,null);
        $string = '
           0,[a],1;
           1,eps,2;
           2,[a],3;
           3,eps,4;
           4,[a],5;
           5,eps,4;
        ';
        $h->test_table($top,$string,$this);
    }

    /**
     * Tests non-infinite quantifiers
     */
    public function test_non_infinite_qunatifiers() {
        $h = new block_formal_langs_lexical_test_helper();
        $p1 = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,0,1);
        $string = '
            0,eps,1:3;
            1,eps,2;
            2,eps,5;
            3,[a],4;
            4,eps,5;
        ';
        $h->test_table($top,$string,$this);
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,1,1);
        $string = '
            0,eps,1;
            1,[a],2;
            2,eps,3;
        ';
        $h->test_table($top,$string,$this);
        $top = new block_formal_langs_lexical_from_to_quantifier($p1,1,2);
        $string = '
            0,eps,1:3;
            1,[a],2;
            2,eps,7;
            3,[a],4;
            4,eps,5;
            5,[a],6;
            6,eps,7;
        ';
        $h->test_table($top,$string,$this);
    }
    
    /** Tests various errors, which can occur, when building tables
     */
    public function test_lexer_table_errors() {
        // Test absent YYINITIAL
        $w = new block_formal_langs_lexer_interaction_wrapper_impl('');
        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->statename = 'NOTINITIAL';
        $s->rules = array( block_formal_langs_lexical_matching_rule::all_matching_rule() );
        $s->actions = array( new block_formal_langs_lexical_simple_action() );
        
        $l = new block_formal_langs_lexical_automata(array($s), $w);
        $errors = $w->table_errors();
        $code = block_formal_langs_lexer_table_error_type::$YYINITIALISABSENT;
        $dump = var_export($errors, true);
        $this->assertTrue(in_array(array($code, null), $errors), 'YYINITIAL suddenly found in automata ' . $dump);
        
        // Test invalid action entering state
        $w = new block_formal_langs_lexer_interaction_wrapper_impl('');
        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->rules = array( block_formal_langs_lexical_matching_rule::all_matching_rule() );
        $s->actions = array( new block_formal_langs_lexical_simple_action('NOTINITIAL') );
        
        $l = new block_formal_langs_lexical_automata(array($s), $w);
        $errors = $w->table_errors();
        $code = block_formal_langs_lexer_table_error_type::$AUTOMATAENTERSNONEXISTENTSTATE;
        $error = $errors[0];
        $this->assertTrue($error[0] == $code, $error[0] . ' is not valid error code');
        $this->assertTrue($error[1]->startingstate == 'YYINITIAL', $error[1]->startingstate);
        $this->assertTrue($error[1]->state == 'NOTINITIAL', $error[1]->state);
        
        // Test empty state
        $w = new block_formal_langs_lexer_interaction_wrapper_impl('');
        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->rules = array( );
        $s->actions = array( );
        
        $l = new block_formal_langs_lexical_automata(array($s), $w);
        $errors = $w->table_errors();
        $code = block_formal_langs_lexer_table_error_type::$STATEISEMPTY;
        $error = $errors[0];
        $this->assertTrue($error[0] == $code, $error[0] . ' is not valid error code');
        $this->assertTrue($error[1] == 'YYINITIAL', $error[1]);

        // Test incorrect supplied amounts of errors
        $w = new block_formal_langs_lexer_interaction_wrapper_impl('');
        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->rules = array( block_formal_langs_lexical_matching_rule::all_matching_rule() );
        $s->actions = array( );
        
        $l = new block_formal_langs_lexical_automata(array($s), $w);
        $errors = $w->table_errors();
        $code = block_formal_langs_lexer_table_error_type::$ACTIONSARENOTEQUALOFSTATES;
        $error = $errors[0];
        $this->assertTrue($error[0] == $code, $error[0] . ' is not valid error code');
        $this->assertTrue($error[1] == 'YYINITIAL', $error[1]);        
    }

    /**
     * Tests building transition sets
     */
    public function test_building_disjoint_transition_sets() {
        $h = new block_formal_langs_lexical_test_helper();
        $q = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $c = new block_formal_langs_lexical_concat_operator(array($q, $q));
        $top = new block_formal_langs_lexical_alternative_operator(array($q, $c));

        $table = $top->build_table();
        // 0,1,3 is an actually an eps-closure of s0 - 0
        $transitions = $table->build_disjoint_transitions(array(0,1,3));
        $this->assertTrue(count($transitions) == 1);
        $this->assertTrue($transitions[0][1] == array(2,4));
        $rule = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $this->assertTrue($rule->is_same($transitions[0][0]));

        // 2,4,5,7 is an actually an eps-closure of 2,4
        $transitions = $table->build_disjoint_transitions(array(2,4,5,7));
        $this->assertTrue(count($transitions) == 1);
        $this->assertTrue($transitions[0][1] == array(6));
        $rule = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $this->assertTrue($rule->is_same($transitions[0][0]));

        //  6,7 is an actually anclosure of 6
        $transitions = $table->build_disjoint_transitions(array(6,7));
        $this->assertTrue(count($transitions) == 0);
    }

    /**
     * Tests building epsilon closure computation
     */
    public function test_epsilon_closure() {
        $q = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $c = new block_formal_langs_lexical_concat_operator(array($q, $q));
        $top = new block_formal_langs_lexical_alternative_operator(array($q, $c));

        $table = $top->build_table();
        $closure = $table->epsilon_closure(0);
        $this->assertTrue($closure == array(0, 1, 3));

        $closure = $table->epsilon_closure(array(2, 4));
        $this->assertTrue($closure == array(2, 4, 5, 7));

        $closure = $table->epsilon_closure(array(6));
        $this->assertTrue($closure == array(6, 7));
    }

    /**
     * Tests optimizing some states, with exclude epsilon function
     */
    public function test_exclude_epsilon() {
        $q = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $c = new block_formal_langs_lexical_concat_operator(array($q, $q));
        $top = new block_formal_langs_lexical_alternative_operator(array($q, $c));

        $action = new block_formal_langs_lexical_simple_action();

        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->rules = array($top);
        $s->actions = array($action);

        $w = new block_formal_langs_lexer_interaction_wrapper_impl('');
        $automata = new block_formal_langs_lexical_automata(array($s), $w);

        $this->assertTrue(count($w->table_errors()) == 0);

        $h = new block_formal_langs_lexical_test_helper();
        $string = '
           0,[a],1;
           1,[a],2;
        ';
        $h->test_built_table($s->table,$string,$this);
        $this->assertTrue($s->table->acceptablestates == array(1,2));
        $this->assertTrue($s->statestoactions == array(1 => 0, 2 => 0));
    }

    /**
     * Simple tokenization test
     */
    public function test_tokenize() {
        $q = block_formal_langs_lexical_matching_rule::simple_rule('a');
        $c = new block_formal_langs_lexical_concat_operator(array($q, $q));
        $top = new block_formal_langs_lexical_alternative_operator(array($q, $c));
        $q2 = block_formal_langs_lexical_matching_rule::simple_rule('b');
        $c2 = new block_formal_langs_lexical_concat_operator(array($q, $q2));

        $action = new block_formal_langs_lexical_simple_action();

        $s = new block_formal_langs_lexical_automata_starting_state();
        $s->rules = array($top, $c2);
        $s->actions = array($action, $action);

        $w = new block_formal_langs_lexer_interaction_wrapper_impl('aaaba');
        $automata = new block_formal_langs_lexical_automata(array($s), $w);
        $texts = array();
        do {
           $result = $w->next_token();
           if ($result != null) {
                $texts[] = $result->text;
            }
        } while ($result != null);
        $this->assertTrue(count($w->token_errors()) == 0);
        $this->assertTrue($texts[0] == 'aa');
        $this->assertTrue($texts[1] == 'ab');
        $this->assertTrue($texts[2] == 'a');
    }
}

class block_formal_langs_syntax_lexer_intersection_test extends PHPUnit_Framework_TestCase {
    /**
     * Tests epsilon node intersection
     */
    public function test_epsilon() {
        $fst = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $snd = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 1);
        $h = new block_formal_langs_lexical_test_helper();
        $h->is_in_states(array($fst, array(0, 1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::all_matching_rule();
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a'));
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);
    }

    /**
     * Tests all matching rule intersection with others
     */
    public function test_all_matching() {
        $fst = block_formal_langs_lexical_matching_rule::all_matching_rule();
        $snd = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $result = $fst->intersect($snd);
        $h = new block_formal_langs_lexical_test_helper();
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::all_matching_rule();
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 1);
        $h->is_in_states(array($fst, array(0, 1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $r2 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a'));
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($r1, array(0,1)), $result, $this);
        $h->is_in_states(array($r2, array(0)), $result, $this);

        $result = $fst->intersect($r2);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($r1, array(0)), $result, $this);
        $h->is_in_states(array($r2, array(0,1)), $result, $this);
    }

    /**
     * Tests character class intersection
     */
    public function test_charclass() {
        $fst = block_formal_langs_lexical_matching_rule::charclass_rule(array('a', 'b'));
        $snd = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $result = $fst->intersect($snd);
        $h = new block_formal_langs_lexical_test_helper();
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::all_matching_rule();
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a', 'b'));
        $r2 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b'));
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($r1, array(1, 0)), $result, $this);
        $h->is_in_states(array($r2, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::charclass_rule(array('c','d'));
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::charclass_rule(array('a','b'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a', 'b'));
        $this->assertTrue(count($result) == 1);
        $h->is_in_states(array($r1, array(0, 1)), $result, $this);


        $snd = block_formal_langs_lexical_matching_rule::charclass_rule(array('b','c'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $r2 = block_formal_langs_lexical_matching_rule::charclass_rule(array('b'));
        $r3 = block_formal_langs_lexical_matching_rule::charclass_rule(array('c'));
        $this->assertTrue(count($result) == 3);
        $h->is_in_states(array($r1, array(0)), $result, $this);
        $h->is_in_states(array($r2, array(0, 1)), $result, $this);
        $h->is_in_states(array($r3, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('c','d'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a', 'b'));
        $r2 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b', 'c', 'd'));
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($r1, array(0, 1)), $result, $this);
        $h->is_in_states(array($r2, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('b','c'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $r2 = block_formal_langs_lexical_matching_rule::charclass_rule(array('b'));
        $r3 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a','b','c'));
        $this->assertTrue(count($result) == 3);
        $h->is_in_states(array($r1, array(0, 1)), $result, $this);
        $h->is_in_states(array($r2, array(0)), $result, $this);
        $h->is_in_states(array($r3, array(1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a','b'));
        $result = $fst->intersect($snd);
        $this->assertTrue(count($result) == 2);
        $h->is_in_states(array($fst, array(0)), $result, $this);
        $h->is_in_states(array($snd, array(1)), $result, $this);
    }

    /**
     * Tests negative charclass intersection with others
     * Since swapping was tested in other tests, we test only intersections between
     * two negative charclasses
     */
    public function test_neg_char_class() {
        $fst = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b'));
        $result = $fst->intersect($fst);
        $h = new block_formal_langs_lexical_test_helper();
        $this->assertTrue(count($result) == 1);
        $h->is_in_states(array($fst, array(0, 1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('c', 'd'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a','b'));
        $r2 = block_formal_langs_lexical_matching_rule::charclass_rule(array('c','d'));
        $r3 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b', 'c', 'd'));
        $this->assertTrue(count($result) == 3, var_export($result, true));
        $h->is_in_states(array($r1, array(1)), $result, $this);
        $h->is_in_states(array($r2, array(0)), $result, $this);
        $h->is_in_states(array($r3, array(0, 1)), $result, $this);

        $snd = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('b', 'c'));
        $result = $fst->intersect($snd);
        $r1 = block_formal_langs_lexical_matching_rule::charclass_rule(array('a'));
        $r2 = block_formal_langs_lexical_matching_rule::charclass_rule(array('c'));
        $r3 = block_formal_langs_lexical_matching_rule::neg_charclass_rule(array('a', 'b', 'c'));
        $this->assertTrue(count($result) == 3, var_export($result, true));
        $h->is_in_states(array($r1, array(1)), $result, $this);
        $h->is_in_states(array($r2, array(0)), $result, $this);
        $h->is_in_states(array($r3, array(0, 1)), $result, $this);
    }
}