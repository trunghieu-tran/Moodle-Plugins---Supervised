<?php
/**
 * Defines unit-tests for Simple English language
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011  Dmitry Mamontov
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
        $this->assertTrue(count($g->errors()) > 0  , 'There must be conflicts!');

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
        $this->assertTrue(count($g->errors()) > 0  , 'There must be conflicts!');
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
