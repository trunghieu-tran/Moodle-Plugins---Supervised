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
 * Defines unit-tests for syntax analysis
 *
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_parser.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');


/**
 * Tests a parser table data
 */
class block_formal_langs_syntax_parser_test extends PHPUnit_Framework_TestCase {

    /**
     * Tests complex grammar
     */
    public function test_complex() {
        $grammar = '
            %nonassoc then
            %nonassoc else
            %left + -
            %left *
            %left )
            program ::= stmt
            program ::= program stmt
            stmt    ::= id = expr ;
            stmt    ::= fun_call ;
            expr    ::= fun_call
            expr    ::= num
            expr    ::= ( expr )
            expr    ::= expr + expr
            expr    ::= expr * expr
            stmt    ::= if expr then stmt
            stmt    ::= if expr then stmt else stmt
            fun_call ::= id args
            args    ::= ( )
            args    ::= ( expr_list )
            expr_list ::= expr
            expr_list ::= expr_list , expr
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->parser_test_grammar($grammar);
        $t = $r->table($g);
        $this->assertTrue(count($g->errors()) == 0, 'Grammar is still ambiguous');

        $testtree = $r->parse($t, 'if num then id ( num ) ;');
        $validtree = array( 'program', array( array( 'stmt',
            array( 'if', array( 'expr', array( 'num' )), 'then', array( 'stmt',
                array(
                    array( 'fun_call', array(
                        'id',
                        array( 'args',
                            array( '(',
                                array( 'expr_list',
                                    array( array( 'expr', array( 'num' )) )),
                                   ')'
                            ))
                    )),
                    ';'
                ))
            ))
        ));
        $this->assertTrue($testtree == $validtree, 'Build another tree instead of valid one' . var_export($testtree, true));
        $testtree = $r->parse($t, 'id ( ( num + num ) * num , num ) ; id = num + num * num ;', false);
        $validtree = array( 'program',
            array( array( 'program', array(
                array( 'stmt',
                    array( array( 'fun_call',
                        array( 'id',
                            array( 'args', array(
                                '(',
                                array( 'expr_list', array(
                                    array( 'expr_list',
                                        array(
                                            array( 'expr',
                                                array( array( 'expr', array(
                                                    '(',
                                                    array( 'expr',
                                                        array( array( 'expr',
                                                            array( 'num' )),
                                                            '+',
                                                            array( 'expr', array( 'num' ))
                                                        )
                                                    ),
                                                    ')'
                                                )),
                                                '*',
                                                array( 'expr', array( 'num' ))
                                                ))
                                        )),
                                    ',',
                                    array( 'expr', array( 'num' ))
                                )),
                                ')'
                            ))
                        )),
                        ';'
                    ))
            )),
            array( 'stmt',
                array(
                    'id', '=',
                    array( 'expr',
                        array( array( 'expr',
                            array( 'num' )),
                        '+',
                        array( 'expr',
                            array( array( 'expr', array( 'num' )),
                            '*',
                            array( 'expr', array( 'num' ))
                        ))
                        )),
                    ';'
                ))
         ));
        $this->assertTrue($testtree == $validtree, 'Build another tree instead of valid one' . var_export($testtree, true));
        $testtree = $r->parse($t, 'if num then if num then id = num ; else id ( ) ;', false);
        $validtree = array( 'program', array(
            array( 'stmt',
                array( 'if',
                       array( 'expr', array( 'num' )),
                      'then',
                       array( 'stmt', array(
                            'if',
                            array( 'expr', array( 'num' )),
                            'then',
                            array( 'stmt', array( 'id', '=', array( 'expr', array( 'num' )), ';' )),
                            'else',
                            array( 'stmt',
                                array( array( 'fun_call',
                                    array( 'id', array( 'args', array( '(', ')' ))
                                )),
                                ';'
                                ))
                       ))
                ))
        ));
        $this->assertTrue($testtree == $validtree, 'Build another tree instead of valid one' . var_export($testtree, true));
    }
    /**
     * Tests grammar with parenthesis
     */
    public function test_parenthesis() {
        $grammar = '
            %left + -
            %left *
            %left )
            program ::= stmt
            program ::= program stmt
            stmt    ::= id = expr ;
            expr    ::= num
            expr    ::= expr + expr
            expr    ::= expr - expr
            expr    ::= expr * expr
            expr    ::= ( expr )
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->parser_test_grammar($grammar);
        $t = $r->table($g);

        $this->assertTrue(count($g->errors()) == 0, 'Grammar is still ambiguous');
        $testtree = $r->parse($t, 'id = num ; id = num * ( num + ( num ) ) ;');
        $validtree = array( 'program', array( array( 'program', array(
            array( 'stmt', array(
                'id', '=',
                array( 'expr',
                    array( 'num' )
                ),
                ';'
            ))
        )),
            array( 'stmt', array(
                'id', '=',
                array( 'expr',
                    array( array( 'expr',  array( 'num' ) ),
                        '*',
                        array( 'expr',  array( '(',
                            array( 'expr', array(
                                array( 'expr', array( 'num' )),
                                '+',
                                array( 'expr', array(
                                    '(',
                                    array( 'expr', array( 'num' )),
                                    ')'
                                ))
                            )),
                            ')'
                        ))
                    )),
                ';'
            ))
        ));
        $this->assertTrue($testtree == $validtree, 'Build another tree instead of valid one' . var_export($testtree, true));

    }
    /**
     * Tests dangling else grammar
     */
    public function test_if_then() {
        $grammar = '
            %nonassoc then
            %nonassoc else
            program ::= stmt
            program ::= program stmt
            stmt ::= expr ;
            stmt ::= if_stmt
            if_stmt ::= if expr then stmt
            if_stmt ::= if expr then stmt else stmt
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->parser_test_grammar($grammar);
        $t = $r->table($g);

        $this->assertTrue(count($g->errors()) == 0, 'Grammar is still ambiguous');

        $testtree = $r->parse($t, 'if expr then if expr then expr ; else expr ;');
        $validtree = array('program', array(array('stmt',
            array(array('if_stmt',
                array('if', 'expr', 'then',
                    array('stmt', array (
                        array('if_stmt', array(
                            'if', 'expr', 'then',
                            array('stmt', array('expr', ';')),
                            'else',
                            array('stmt', array('expr', ';'))
                        ))
                    ))
                )
            ))
        )));

        $this->assertTrue($testtree == $validtree, 'Build another tree instead of valid one' . var_export($testtree, true));
    }

    /**
     * Tests reduce-reduce conflict deducing on grammar, given as ambiguous
     * in bison grammar, denoted here
     * @url http://www.gnu.org/software/bison/manual/html_node/Reduce_002fReduce.html
     */
    public function test_reduce_reduce_bison() {
        $grammar = '
            sequence ::= epsilon
            sequence ::= maybeword
            sequence ::= sequence word
            maybeword ::= epsilon
            maybeword ::= word
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);

        $tbl = new block_formal_langs_grammar_table($g);
        $e = $g->errors();
        /**
         * Currently, somehow we resolved some problems with it
         * If some bugs will be found, we can reduce stuff to
         * Chomsky normal form, through eliminating epsilon productions
         * http://www.idt.mdh.se/kurser/cd5560/02_03/senaste_nytt/cf-languages-crib-sheet.pdf
         */
        $this->assertTrue(count($e) == 2  , 'There must be two conflicts!');
        // Two conflicts are  . $ and  . word
        $this->assertTrue($e[0]->type == block_formal_langs_grammar_error::$REDUCE_REDUCE_CONFLICT, 'Not reduce-reduce conflict at 0');
        $this->assertTrue($e[1]->type == block_formal_langs_grammar_error::$REDUCE_REDUCE_CONFLICT, 'Not Reduce-reduce conflict at 1');
        /**
         *  @var block_formal_langs_grammar_error $err
         */
        $err = $e[0];
        /**
         * @var block_formal_langs_grammar_production_rule $action1rule
         */
        $action1rule = $err->action1->rule;
        /**
         * @var block_formal_langs_grammar_production_rule $action2rule
         */
        $action2rule = $err->action2->rule;
        $naw = $action1rule->left()->type() == 'maybeword' || $action2rule->left()->type() == 'sequence';
        $wan = $action1rule->left()->type() == 'sequence' || $action2rule->left()->type() == 'maybeword';
        $this->assertTrue($naw || $wan, 'Weird reduce-reduce conflict: ' . var_export($err, true));
        /**
         *  @var block_formal_langs_grammar_error $err
         */
        $err = $e[1];
        /**
         * @var block_formal_langs_grammar_production_rule $action1rule
         */
        $action1rule = $err->action1->rule;
        /**
         * @var block_formal_langs_grammar_production_rule $action2rule
         */
        $action2rule = $err->action2->rule;
        $naw = $action1rule->left()->type() == 'maybeword' || $action2rule->left()->type() == 'sequence';
        $wan = $action1rule->left()->type() == 'sequence' || $action2rule->left()->type() == 'maybeword';
        $this->assertTrue($naw || $wan, 'Weird reduce-reduce conflict: ' . var_export($err, true));


    }

    /**
     *  Tests reduce-reduce conflict deducing
     */
    public function test_reduce_reduce() {
        $grammar = '
            nonterminal ::= num + num
            nonterminal ::= num + weird
            weird ::= num
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);
        $tbl = new block_formal_langs_grammar_table($g);
        // And here we go - what rule we should use in sequence while num + num . - reduce to weird
        // or nonterminal ?
        $e = $g->errors();
        $this->assertTrue(count($e) == 1  , 'There must be one conflict!');
        /**
         *  @var block_formal_langs_grammar_error $err
         */
        $err = $e[0];
        $this->assertTrue($err->type == block_formal_langs_grammar_error::$REDUCE_REDUCE_CONFLICT, 'This is not reduce-reduce conflict');
        /**
         * @var block_formal_langs_grammar_production_rule $action1rule
         */
        $action1rule = $err->action1->rule;
        /**
         * @var block_formal_langs_grammar_production_rule $action2rule
         */
        $action2rule = $err->action2->rule;
        $naw = $action1rule->left()->type() == 'nonterminal' || $action2rule->left()->type() == 'weird';
        $wan = $action1rule->left()->type() == 'weird' || $action2rule->left()->type() == 'nonterminal';

        $this->assertTrue($naw || $wan, 'Weird reduce-reduce conflict: ' . var_export($err, true));
    }

    /**
     * Tests conflict resolving, using precedence
     */
    public function test_precedence() {
        $grammar = '
            %left +
            %left *
            stmt ::= expr ;
            expr ::= expr + expr
            expr ::= expr * expr
            expr ::= num
        ';

        $action = array(
            0 => array('node' => array(), 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => array('$' => 'accept'), 'edges' => array()),
            2 => array('node' => array(), 'edges' => array(';' => 4, '+' => 5, '*' => 6)),
            3 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr', '*' => 'reduce to expr'), 'edges' => array()),
            4 => array('node' => array('$' => 'reduce to stmt'), 'edges' => array()),
            5 => array('node' => array(), 'edges' => array('expr' => 7, 'num' => 3)),
            6 => array('node' => array(), 'edges' => array('expr' => 8, 'num' => 3)),
            7 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr'), 'edges' => array('*' => 6)),
            8 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr', '*' => 'reduce to expr'), 'edges' => array()),
        );
        $goto = array(
            0 => array('node' => 1, 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => 1, 'edges' => array()),
            2 => array('node' => 1, 'edges' => array(';' => 4, '+' => 5, '*' => 6)),
            3 => array('node' => 1, 'edges' => array()),
            4 => array('node' => 1, 'edges' => array()),
            5 => array('node' => 1, 'edges' => array('expr' => 7, 'num' => 3)),
            6 => array('node' => 1, 'edges' => array('expr' => 8, 'num' => 3)),
            7 => array('node' => 1, 'edges' => array('+' => 5, '*' => 6)),
            8 => array('node' => 1, 'edges' => array('+' => 5, '*' => 6)),
        );
        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }
    /**
     * Tests conflict resolving, using non-associativity
     */
    public function test_nonassociativity() {
        $grammar = '
            %nonassoc +
            stmt ::= expr ;
            expr ::= expr + expr
            expr ::= num
        ';
        $action = array(
            0 => array('node' => array(), 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => array('$' => 'accept'), 'edges' => array()),
            2 => array('node' => array(), 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr'), 'edges' => array()),
            4 => array('node' => array('$' => 'reduce to stmt'), 'edges' => array()),
            5 => array('node' => array(), 'edges' => array('expr' => 6, 'num' => 3)),
            6 => array('node' => array(';' => 'reduce to expr'), 'edges' => array())
        );
        $goto = array(
            0 => array('node' => 1, 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => 1, 'edges' => array()),
            2 => array('node' => 1, 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => 1, 'edges' => array()),
            4 => array('node' => 1, 'edges' => array()),
            5 => array('node' => 1, 'edges' => array('expr' => 6, 'num' => 3)),
            6 => array('node' => 1, 'edges' => array('+' => 5))
        );
        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }

    /**
     * Tests conflict resolving, using right associativity
     */
    public function test_right_associativity() {
        $grammar = '
            %right +
            stmt ::= expr ;
            expr ::= expr + expr
            expr ::= num
        ';
        $action = array(
            0 => array('node' => array(), 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => array('$' => 'accept'), 'edges' => array()),
            2 => array('node' => array(), 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr'), 'edges' => array()),
            4 => array('node' => array('$' => 'reduce to stmt'), 'edges' => array()),
            5 => array('node' => array(), 'edges' => array('expr' => 6, 'num' => 3)),
            6 => array('node' => array(';' => 'reduce to expr'), 'edges' => array('+' => 5))
        );
        $goto = array(
            0 => array('node' => 1, 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => 1, 'edges' => array()),
            2 => array('node' => 1, 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => 1, 'edges' => array()),
            4 => array('node' => 1, 'edges' => array()),
            5 => array('node' => 1, 'edges' => array('expr' => 6, 'num' => 3)),
            6 => array('node' => 1, 'edges' => array('+' => 5))
        );
        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }
    /**
     * Tests conflict resolving, using left associativity
     */
    public function test_left_associativity() {
        $grammar = '
             %left +
             stmt ::= expr ;
             expr ::= expr + expr
             expr ::= num
        ';
        $action = array (
            0 => array('node' => array(), 'edges' => array('stmt' => 1 , 'expr' => 2 , 'num' => 3)),
            1 => array('node' => array('$' => 'accept'), 'edges' => array()),
            2 => array('node' => array(), 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr'), 'edges' => array()),
            4 => array('node' => array('$' => 'reduce to stmt'), 'edges' => array ()),
            5 => array('node' => array(), 'edges' => array('expr' => 6, 'num' => 3)),
            6 => array('node' => array(';' => 'reduce to expr', '+' => 'reduce to expr'), 'edges' => array())
        );
        $goto =   array (
            0 => array('node' => 1, 'edges' => array('stmt' => 1, 'expr' => 2, 'num' => 3)),
            1 => array('node' => 1, 'edges' => array()),
            2 => array('node' => 1, 'edges' => array(';' => 4, '+' => 5)),
            3 => array('node' => 1, 'edges' => array()),
            4 => array('node' => 1, 'edges' => array()),
            5 => array('node' => 1, 'edges' => array('expr' => 6,'num' => 3)),
            6 => array('node' => 1, 'edges' => array('+' => 5))
        );
        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }
    /**
     * Tests whether grammar is ambiguous and conflict is resolved using
     * associativity
     */
    public function test_shift_reduce_conflict_assoc() {
        $grammar = '
            program ::= stmt
            program ::= program stmt
            stmt ::= expr ;
            expr ::= expr + expr
            expr ::= num
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);
        $tbl = new block_formal_langs_grammar_table($g);
        $e = $g->errors();
        $this->assertTrue(count($e) == 1  , 'There must be one conflict!');
        /**
         *  @var block_formal_langs_grammar_error $err
         */
        $err = $e[0];
        $this->assertTrue($err->type == block_formal_langs_grammar_error::$SHIFT_REDUCE_CONFLICT, 'This is non shift-reduce conflict!');
        $this->assertTrue($err->symbol->type() == '+', 'Conflict lookahead symbol must be +');


    }

    /**
     * Tests, whether grammar is ambiguous and resolved using precedence
     */
    public function test_shift_reduce_conflict_precedence() {
        $grammar = '
            program ::= stmt
            program ::= program stmt
            stmt ::= expr ;
            expr ::= expr + expr
            expr ::= expr * expr
            expr ::= num
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);
        $tbl = new block_formal_langs_grammar_table($g);
        $e = $g->errors();
        $this->assertTrue(count($e) == 4  , 'There must exact 4 conflicts!');
        // This is a bit tricky, but real errors will occur in some cases when
        // [expr + expr, +] - reduce to expr or shift
        // [expr * expr, +] - reduce to expr or shift
        // [expr + expr, *] - reduce to expr or shift
        // [expr * expr, *] - reduce to expr or shift
        // so, if you can see there are 4 kinds of errors
        $nonshiftreduce = array();
        $plus = array();
        $multiply = array();
        for($i = 0; $i < count($e); $i++) {
            /**
             * @var block_formal_langs_grammar_error $fe
             */
            $fe = $e[$i];
            if ($fe->type != block_formal_langs_grammar_error::$SHIFT_REDUCE_CONFLICT) {
                $nonshiftreduce[] = $fe;
            }
            if ($fe->symbol->type() == '+') {
                $plus[] = $fe;
            }
            if ($fe->symbol->type() == '*') {
                $multiply[] = $fe;
            }
        }
        $this->assertTrue(count($nonshiftreduce) == 0, 'Found non shift-reduce errors: '. var_export($nonshiftreduce, true));
        $this->assertTrue(count($plus) == 2, 'Some +-based conflicts found' . var_export($plus, true));
        $this->assertTrue(count($multiply) == 2, 'Some *-based conflicts found' . var_export($multiply, true));
    }
    /**
     * Tests grammar when no rules is supplied
     */
    public function test_no_rules() {
        $grammar = '';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);
        $this->assertTrue(count($g->errors()) == 1 , 'There must be one error!');
        $e = $g->errors();
        $e = $e[0];
        $code = block_formal_langs_grammar_error::$NO_RULES;
        $this->assertTrue($e->type == $code, 'Instead of ::NO_RULES a ' . $e->type . ' given ');
    }

    /**
     * Tests grammar, when starting symbol cannot be determined
     */
    public function test_undeterminable_starting_symbols() {
        $grammar = '
            A ::= B
            B ::= A
        ';
        $r = new block_formal_langs_parser_rule_helper();
        $g = $r->grammar($grammar);
        $this->assertTrue(count($g->errors()) == 1 , 'There must be one error!');
        $e = $g->errors();
        $e = $e[0];
        $code = block_formal_langs_grammar_error::$NO_STARTING_SYMBOLS;
        $this->assertTrue($e->type == $code, 'Instead of ::NO_STARTING_SYMBOLS a ' . $e->type . ' given ');
    }
    /**
     * A simple test for regular grammar, taken from Aho, Ulman
     */
    public function test_aho() {
        $grammar = '
            S ::= C C
            C ::= c C
            C ::= d
        ';

        $action = array(
            0 => array('node'  => array (),
                       'edges' => array ('S' => 1 , 'C' => 2 ,  'c' => 3 ,  'd' => 4 ) ),
            1 => array('node'  => array ('$' => 'accept'),
                       'edges' => array ()),
            2 => array('node'  => array (),
                       'edges' => array ('C' => 5 , 'c' => 3 , 'd' => 4 ) ),
            3 => array('node'  => array (),
                       'edges' => array ('C' => 6 , 'c' => 3 , 'd' => 4 ) ),
            4 => array('node'  => array ('c' => 'reduce to C' , 'd' => 'reduce to C' , '$' => 'reduce to C'),
                       'edges' => array () ),
            5 => array('node'  => array ('$' => 'reduce to S'),
                       'edges' => array ()),
            6 => array('node'  => array ('c' => 'reduce to C' , 'd' => 'reduce to C' , '$' => 'reduce to C' ),
                       'edges' => array () )
        );

        $goto = array (
            0 => array('node'  => 1,
                       'edges' => array('S' => 1 , 'C' => 2 , 'c' => 3 , 'd' => 4) ),
            1 => array('node'  => 1,
                       'edges' => array()),
            2 => array('node'  => 1,
                       'edges' => array('C' => 5 , 'c' => 3 , 'd' => 4 )),
            3 => array('node'  => 1,
                       'edges' => array ('C' => 6 , 'c' => 3 , 'd' => 4 )),
            4 => array('node'  => 1,
                       'edges' => array ()),
            5 => array('node'  => 1,
                       'edges' => array ()),
            6 => array('node'  => 1,
                       'edges' => array ()),
        );

        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }

    /**
     * Tests very simple grammar for common case
     */
    public function test_very_simple() {
        $grammar = '
            program ::=  program stmt
            program ::=  stmt
            stmt    ::=  id = expr ;
            expr    ::=  expr + num
            expr    ::=  num
        ';

        $goto = array (
            0  => array ( 'node' => 1, 'edges' => array ('program' => 1 , 'stmt' => 2 , 'id' => 3) ),
            1  => array ( 'node' => 1, 'edges' => array ('stmt' => 4 , 'id' => 3) ),
            2  => array ( 'node' => 1, 'edges' => array () ),
            3  => array ( 'node' => 1, 'edges' => array ('=' => 5) ),
            4  => array ( 'node' => 1, 'edges' => array () ),
            5  => array ( 'node' => 1, 'edges' => array ('expr' => 6 , 'num' => 7) ),
            6  => array ( 'node' => 1, 'edges' => array (';' => 8 , '+' => 9) ),
            7  => array ( 'node' => 1, 'edges' => array () ),
            8  => array ( 'node' => 1, 'edges' => array () ),
            9  => array ( 'node' => 1, 'edges' => array ('num' => 10) ),
            10 => array ( 'node' => 1, 'edges' => array () ),
        );

        $action = array (
            0 => array  ('node'  => array (),
                         'edges' => array( 'program' => 1 , 'stmt' => 2 , 'id' => 3 ) ),
            1 =>  array ('node'  => array ('$' => 'accept'),
                         'edges' => array('stmt' => 4 , 'id' => 3) ),
            2 =>  array ('node'  => array ('$' => 'reduce to program' , 'id' => 'reduce to program' ),
                         'edges' => array ()),
            3 =>  array ('node'  => array (),
                         'edges' => array ( '=' => 5 )),
            4 =>  array ('node'  => array ('$' => 'reduce to program' , 'id' => 'reduce to program', ),
                         'edges' => array ()),
            5 =>  array ('node'  => array (),
                         'edges' => array ('expr' => 6 , 'num' => 7,),),
            6 =>  array ('node'   => array(),
                         'edges'  => array(';' => 8 , '+' => 9 ) ),
            7 =>  array ('node'   => array(';' => 'reduce to expr' , '+' => 'reduce to expr', ),
                         'edges'  => array()),
            8 =>  array ('node'  => array('$' => 'reduce to stmt' , 'id' => 'reduce to stmt' ),
                         'edges' => array()),
            9 =>  array ('node'  => array(),
                         'edges'  => array('num' => 10)),
            10 => array ('node'  => array(';' => 'reduce to expr' , '+' => 'reduce to expr'),
                         'edges' => array()),
        );

        $r = new block_formal_langs_parser_rule_helper();
        $r->test($grammar, $action, $goto, $this);
    }
}
