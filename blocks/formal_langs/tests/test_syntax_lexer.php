<?
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
}