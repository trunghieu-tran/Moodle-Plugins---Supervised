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
 * Defines unit-tests utils
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_parser.php');


/**
 * Tests exact matches for running simple english languages
 */
class block_formal_langs_language_test_utils {
    /**
     * A language, specified in constructor
     * @var block_formal_langs_predefined_language
     */
    protected $lang;
    /**
     * Test for asserting
     * @var PHPUnit_Framework_TestCase
     */
    protected $test;
    /**
     * Constructs a new utils for language
     * @param string $langname  name of language class
     * @param PHPUnit_Framework_TestCase $test test for working
     */
    public function __construct($langname, $test) {
        $this->lang = new $langname();
        $this->test = $test;
    }
    /**
     * Tests whether any token contains in expressions
     * @param array $expressions  string array of expressions
     */
    public function test_exact_matches($expressions) {
        $processedstring = $this->lang->create_from_string(implode(' ', $expressions));
        $result = $processedstring->stream->tokens;
        $tokenvalues = array();
        foreach($result as $token) {
            $tokenvalues[] = $token->value();
        }
        $this->test->assertTrue(count($expressions) == count($result), 'There must be same amount of lexemes but ' . count($result) . ' given: ' . implode("\n",$tokenvalues));
        for($i = 0; $i < count($result); $i = $i + 1) {
            $needle = $expressions[$i];
            $this->test->assertTrue(in_array($needle,$tokenvalues), $needle . ' is not found');
        }
    }
    /**
     * Tests whether any token contains in expressions an all of them has specified type
     * @param array $tokens  string array of expressions
     * @param string $class checked class
     */
    public function test_object($tokens, $class) {
        $processedstring = $this->lang->create_from_string(implode(' ', $tokens));
        $result = $processedstring->stream->tokens;
        $this->test->assertTrue(count($result) == count($tokens), 'Incorrect amount of lexemes');
        for($i = 0; $i < count($tokens); $i++) {
            $token = $result[$i];
            $correct = is_a($token, $class);
            $this->test->assertTrue($correct, 'Invalid object');
            $this->test->assertTrue($token->value() == $tokens[$i], 'Incorrect token: ' . $token->value());
        }
    }

}

/**
 * Class, that whole purpose was to make independent equality comparison on graph, with marked
 * nodes and defined as array of node description, when two equal graphs may differ
 * only differ in state numbering.
 *
 */
class block_formal_langs_language_parser_digraph {
    /**
     * States of data
     * Every state consists from ('node' => array|int|string, which reprepresents
     * a mark on node, and can be compared, using operator ==
     * and 'edges'  an array ( 'condition' string => 'state'  index of new state )
     *
     * @var array of array
     */
    protected $states;

    /**
     * Constructs a parser from array of states
     * @param array $states  states data
     */
    public function __construct($states) {
        $this->states = $states;
    }

    /**
     * Computes count of array by incidence
     * @return array ('sorted' => array of data, 'edges' => int count data)
     */
    public function compute_edge_count_sort_by_incidence() {
        $sorted = array();
        $edges = 0;
        for($i = 0; $i < count($this->states); $i++) {
            $cnt = count($this->states[$i]['edges']);
            $edges += $cnt;
            if (array_key_exists($cnt, $sorted) == false) {
                $sorted[$cnt] = array();
            }
            $sorted[$cnt][] = $i;
        }
        return array('sorted' => $sorted, 'edges' => $edges);
    }

    /**
     * Computes a flat form array, when a node described as array(
     *  'node' => data,
     *  'edges' => edges
     * )
     * @return array('keys' => key data as node and edges , 'values' => array of initial indexes)
     */
    public function flat_form() {
        $keys = array();
        $values = array();
        for($i = 0; $i < count($this->states); $i++) {
            // Create node description
            $node = array('node' => $this->states[$i]['node'], 'inedges' => array(), 'outedges' => array());
            foreach($this->states[$i]['edges'] as $key => $index) {
                $node['outedges'][$key] = 1;
            }
            foreach($this->states as $index => $testnode) {
                foreach($testnode['edges'] as $edgekey => $testindex) {
                    if ($testindex == $i) {
                        $node['inedges'][$edgekey] = 1;
                    }
                }
            }
            $found = false;
            for($j = 0; $j < count($keys); $j++) {
                if ($keys[$j] == $node) {
                    $found = true;
                    $values[$j][] = $i;
                }
            }
            if ($found == false) {
                $index = count($keys);
                $keys[$index] = $node;
                $values[$index] = array( $i );
            }
        }
        return array('keys' => $keys, 'values' => $values);
    }
    /**
     * Compares two directed graphs. It doesn't compare edges types, so
     * it may work incorrectly on some specific rare cases.
     * @param block_formal_langs_language_parser_digraph $o other graph
     * @return bool compare result
     */
    public function equal($o) {
        if (count($this->states) != count($o->states())) {
            return false;
        }
        $d1 = $this->compute_edge_count_sort_by_incidence();
        $d2 = $o->compute_edge_count_sort_by_incidence();
        if ($d1['edges'] != $d2['edges']) {
            return false;
        }
        foreach($d1['sorted'] as $key => $indexes) {
            if (array_key_exists($key, $d1['sorted']) == false) {
                return false;
            }
            if (count($indexes) != count($d2['sorted'][$key])) {
                return false;
            }
        }
        $f1 = $this->flat_form();
        $f2 = $o->flat_form();
        return $this->descriptions_are_equal($f1, $f2)
            && $this->descriptions_are_equal($f2, $f1);
    }

    /**
     * Checks whether entries from first array are presented in second
     * @param array $f1 first array
     * @param array $f2 seconf array
     * @return bool new data
     */
    public function descriptions_are_equal($f1, $f2) {
        $ok = true;
        for($i = 0; $i < count($f1['keys']); $i++) {
            $node = $f1['keys'][$i];
            $f1_node_count = count($f1['values'][$i]);
            $f2_node_count = 0;
            for($j = 0; $j < count($f2['keys']); $j++) {
                if ($f2['keys'][$j] == $node) {
                    $f2_node_count = count($f2['values'][$j]);
                }
            }
            $ok = $ok && ($f1_node_count == $f2_node_count);
        }
        return $ok;
    }



    /**
     * Returns a graph, built from GOTO
     * @param array $goto GOTO parser table
     * @return block_formal_langs_language_parser_digraph resulting built graph
     */
    public static function build_from_goto($goto) {
        $result = array();
        for($i = 0; $i < count($goto); $i++) {
            $edges = array();
            for($j = 0; $j < count($goto[$i]); $j++) {
                /**
                 * @var  block_formal_langs_grammar_production_symbol $sym
                 */
                $sym = $goto[$i][$j]['symbol'];
                $edges[$sym->type()] = $goto[$i][$j]['goto'];
            }
            $result[] = array('node' => 1, 'edges' => $edges);
        }
        return new block_formal_langs_language_parser_digraph($result);
    }

    /**
     * Returns a graph, built from ACTION
     * @param array $action ACTION parser table
     * @return block_formal_langs_language_parser_digraph resulting built graph
     */
    public static function build_from_action($action) {
        $result = array();
        for($i = 0; $i < count($action); $i++) {
            $edges = array();
            $node = array();
            for($j = 0; $j < count($action[$i]); $j++) {
                /**
                 * @var  block_formal_langs_grammar_production_symbol $sym
                 */
                $sym = $action[$i][$j]['symbol'];
                /**
                 * @var stdClass $action
                 */
                $caction = $action[$i][$j]['action'];
                if ($caction->type == 'shift') {
                    $edges[$sym->type()] = $caction->goto;
                } else {
                    if ($caction->type == 'reduce') {
                        /**
                         * @var block_formal_langs_grammar_production_rule $rule
                         */
                        $rule = $caction->rule;
                        $to = $rule->left()->type();
                        $node[$sym->type()] = 'reduce to ' . $to;
                    }  else {
                        $node[$sym->type()] = 'accept';
                    }
                }
            }
            $result[] = array('node' => $node, 'edges' => $edges);
        }
        return new block_formal_langs_language_parser_digraph($result);
    }
    /**
     * Returns array of states data
     * @return array of states
     */
    public function states() {
        return $this->states;
    }

    /**
     * Exports a graph to a PHP-code with a variable named, as such.
     * Simplifies a creation of tests
     * @param string $variable variable name
     * @return string data of graph
     */
    public function export($variable) {
        return '$' . $variable . ' = ' . var_export($this->states, $variable) . ';';
    }

    /**
     * Dumps graph as DOT language data
     * @return string
     */
    public function to_dot() {
        $result = 'digraph G {' . PHP_EOL;
        foreach($this->states as $index => $data) {
            $result .= 'A' . $index . ' [label="' . $index . ':' . addslashes(json_encode($data['node'])) . '"];' .PHP_EOL;
        }

        foreach($this->states as $index => $data) {
            foreach($data['edges']  as $condition => $to ) {
            $result .= 'A' . $index . ' -> A' . $to . ' [label="' . addslashes($condition) . '"];' .PHP_EOL;
            }
        }
        $result .= '}';
        return $result;
    }

    /**
     * Used for dumping from other graph. Called from original graph
     * @param block_formal_langs_language_parser_digraph  $other other
     * @return string result
     */
    public function dump_compare($other) {
        $result = 'Original graph: ' . PHP_EOL;
        $result .= $this->to_dot();
        $result .= PHP_EOL . 'Another graph: ' . PHP_EOL;
        $result .= $other->to_dot();
        return $result;
    }
}

/**
 * Basic node for terminal test stuff
 */
abstract class block_formal_langs_parser_test_node_base {
    /**
     * Type of terminal
     * @var string
     */
    public $type;

    /**
     * Constructs a new simple terminal node
     * @param string $type
     */
    public function __construct($type) {
        $this->type = $type;
    }

    /**
     * Return type of node
     * @return string
     */
    public function type() {
        return $this->type;
    }

    /**
     * Outputs node as type data
     * @return mixed
     */
    abstract public function output();

    /**
     * Dumps a data  into string
     * @return string
     */
    abstract public function dump();
}

/**
 * Terminal node for test
 */
class  block_formal_langs_parser_test_terminal_node extends block_formal_langs_parser_test_node_base {

    public function output() {
        return $this->type();
    }

    public function dump() {
        return '\'' . $this->type() . '\'';
    }
}

/**
 * Non-terminal node for test
 */
class block_formal_langs_parser_test_nonterminal_node extends block_formal_langs_parser_test_node_base {
    /**
     * Children array
     * @var array of  block_formal_langs_parser_test_node_base
     */
    protected $children;

    /**
     * Constructs new nonterminal node
     * @param string $type
     * @param array $children
     */
    public function __construct($type, $children) {
        parent::__construct($type);
        $this->children = $children;
    }

    public function output() {
        $c = array();
        /**
         * @var block_formal_langs_parser_test_node_base $child
         */
        foreach($this->children as $child) {
            $c[] = $child->output();
        }
        return array( $this->type(),  $c);
    }

    public function dump() {

        $c = array();
        /**
         * @var block_formal_langs_parser_test_node_base $child
         */
        foreach($this->children as $child) {
            $c[] = $child->dump();
        }
        $ftype = '\'' . $this->type() . '\'';
        return 'array( ' . $ftype  . ', array( ' . implode(', ', $c) . ' ))';
    }

}

/**
 * A simple action, which creates some action stuff
 */
class block_formal_langs_test_parser_action extends block_formal_langs_grammar_action {


    public function reduce($rule, $children) {
        return new block_formal_langs_parser_test_nonterminal_node($rule->left()->type(), $children);
    }

}

/**
 * An interaction wrapper on parser
 */
class block_formal_langs_parser_test_wrapper extends block_formal_langs_lexer_parser_interaction_wrapper {
    /**
     * A tokens array
     * @var array
     */
    protected $tokens;
    /**
     * Current token index
     * @var int
     */
    protected $current;

    public function __construct($string) {
        parent::__construct(null);
        $s1 = preg_replace('/[ ]+/', ' ', $string);
        $this->tokens = explode(' ', $s1);
        $this->current = 0;
    }

    public function next_token() {
        if ($this->current == count($this->tokens)) {
            return null;
        }
        $result = $this->tokens[$this->current];
        $this->current += 1;
        return new block_formal_langs_parser_test_terminal_node($result);
    }

    public function error($parser, $symbol) {
        parent::error($parser, $symbol);
        throw new Exception('Parse failed!');
    }
}

/**
 * Class, designed to support fast grammar creation
 */
class block_formal_langs_parser_rule_helper {
    /**
     *  A delegate for constructing action
     *  It may be any delegate, which implements method action(string $type)
     *  @var block_formal_langs_grammar_action
     */
    public $action = null;
    /**
     * Returns a production symbol, according to type
     * @param string $type string type data
     * @return block_formal_langs_grammar_production_symbol
     */
    public function symbol($type) {
        $result = new block_formal_langs_grammar_common_symbol($type);
        if ($type=='$') {
            $result = new block_formal_langs_grammar_dollar_symbol();
        }
        if ($type=='epsilon') {
            $result = new block_formal_langs_grammar_epsilon_symbol();
        }
        return $result;
    }

    /**
     * Returns a rule
     * @param string $s a string like 'symbol' ::= 'symbol1' 'symbol2' 'symbol3', for rule
     * @return array block_formal_langs_grammar_production_rule rule, precedence symbol prec
     */
    public function rule($s) {
        $s1 = preg_replace('/[ ]+/', ' ', $s);
        $ruleparts = explode('::=', $s1);
        if (count($ruleparts) !=2) {
            die('Incorrect rule: ' . $s);
        }
        $symbol = null;
        $leftpart = explode(' ', $ruleparts[1]);
        $leftrule = array();
        $prec = false;
        foreach($leftpart as $part) {
            $trimpart = trim($part);
            if (core_text::strlen($trimpart) != 0) {
                if ($trimpart == '%prec') {
                    $prec = true;
                } else {
                    if ($prec) {
                        $symbol = $this->symbol($trimpart);
                    } else {
                        $leftrule[] = $this->symbol($trimpart);
                    }
                    $prec = false;
                }
            }
        }
        if ($this->action == null) {
            $r = new block_formal_langs_grammar_replace_action();
        } else {
            $r = clone $this->action;
        }
        $right = $this->symbol(trim($ruleparts[0]));
        return array('rule' => new block_formal_langs_grammar_production_rule($right , $leftrule, $r),
                     'prec' => $symbol
                    );
    }

    /**
     * Builds a new grammar from string
     * @param string $s description of grammar
     * @return block_formal_langs_grammar
     */
    public function grammar($s) {
        $wdata = explode("\n", $s);
        $rules = array();
        $ruleprecs = array();
        $codes = array(
            '%left' => block_formal_langs_grammar_associativity::$left,
            '%right' => block_formal_langs_grammar_associativity::$right,
            '%nonassoc' => block_formal_langs_grammar_associativity::$nonassoc
        );
        $ekeys = array();
        $evalues = array();
        $prec = 1;
        foreach($wdata as $string) {
            $tr = trim($string);
            if (core_text::strlen($tr) != 0) {
                if ($tr[0] != '%') {
                    $ruledata = $this->rule($tr);
                    $rules[] = $ruledata['rule'];
                    if ($ruledata['prec']!=null) {
                        $ruleprecs[count($rules)-1] = $ruledata['prec'];
                    }
                } else {
                    $trs = preg_replace('/[ ]+/', ' ', $tr);
                    $markups = explode(' ', $trs);
                    if (array_key_exists($markups[0], $codes)) {
                       $code = $codes[$markups[0]];
                        for ($i = 1; $i < count($markups); $i++) {
                            $ekeys[] = $this->symbol($markups[$i]);
                            $evalues[] = array('assoc' => $code, 'prec' => $prec);
                        }
                    }
                    $prec++;
                }
            }
        }
        $d = array('assoc' => null, 'prec' => null);
        $ap = new block_formal_langs_grammar_symbol_map($ekeys, $evalues, $d);

        $g = new block_formal_langs_grammar($rules);
        $g->set_associativity_precedence_table($ap,$ruleprecs);
        return $g;
    }

    /**
     * Creates a grammar for parsing tests
     * @param string $s  string with grammar
     * @return block_formal_langs_grammar
     */
    public function parser_test_grammar($s) {
        $this->action = new block_formal_langs_test_parser_action();
        $g  = $this->grammar($s);
        $this->action = null;
        return $g;
    }

    /**
     * Returns a table for grammar
     * @param block_formal_langs_grammar $g grammar
     * @return block_formal_langs_grammar_table
     */
    public function table($g) {
        return new block_formal_langs_grammar_table($g);
    }

    /**
     * Parses some data in tables
     * @param  block_formal_langs_grammar_table $table
     * @param string $string parsed string
     * @param bool $dump whether we should dump result to string
     * @return mixed output tree
     */
    public function parse($table, $string, $dump = false) {
        $w = new block_formal_langs_parser_test_wrapper($string);
        $p = new block_formal_langs_grammar_parser($table, $w);
        /**
         * @var block_formal_langs_parser_test_node_base $result
         */
        $result = $p->parse();
        if ($dump == true) {
            return $result->dump();
        }
        return $result->output();
    }
    /**
     * Performs a new test
     * @param string $grammar grammar data
     * @param array $correctaction action table
     * @param array $correctgoto   goto table
     * @param PHPUnit_Framework_TestCase $test test data
     */
    public function test($grammar, $correctaction, $correctgoto, $test) {
        $table = new block_formal_langs_grammar_table($this->grammar($grammar));
        $testaction = block_formal_langs_language_parser_digraph::build_from_action($table->action());
        $testgoto =  block_formal_langs_language_parser_digraph::build_from_goto($table->gototable());
        $caction = new block_formal_langs_language_parser_digraph($correctaction);
        $cgoto = new block_formal_langs_language_parser_digraph($correctgoto);
        $test->assertTrue( $cgoto->equal($testgoto), $cgoto->dump_compare($testgoto));
        $test->assertTrue( $caction->equal($testaction), $caction->dump_compare($testaction));
    }
}

/**
 * A rule helper, for testing lexer parts
 */
class block_formal_langs_lexical_test_helper {
    /**
     * Builds table from string. String is formatted as
     * rule; rule, where rule is defined as int,type,[int][:int]
     * where
     * @param $string
     * @return block_formal_langs_language_parser_digraph a table from string
     */
    public function table_from_string($string) {
        $str = str_replace(array(' ', "\n", "\r"), array('', '', ''), $string);
        $rules = explode(';', $str);
        $denormalizedrules = array();
        $result = array();
        foreach($rules as $rule) {
            if (core_text::strlen($rule)) {
                $ruleparts = explode(',', $rule);
                $oldstate = intval($ruleparts[0]);
                $type = $ruleparts[1];
                $newstates = explode(':', $ruleparts[2]);
                $newstates = array_map('intval', $newstates);
                $denormalizedrules[] = array($oldstate, $type, $newstates);

                $result[$oldstate] = array('node' => 1, 'edges' => array());
                foreach($newstates as $state) {
                    $result[$state] = array('node' => 1, 'edges' => array());
                }
            }
        }
        foreach($denormalizedrules as $rule) {
           if (count($rule[2]) > 1) {
              $i = 0;
              foreach($rule[2] as $newstate) {
                  $result[$rule[0]]['edges'][$rule[1] . $i] = $newstate;
                  $i++;
              }
           } else {
               $result[$rule[0]]['edges'][$rule[1]] = $rule[2][0];
           }
        }
        return new block_formal_langs_language_parser_digraph($result);
    }

    /**
     * Performs a testing on built table
     * @param block_formal_langs_lexical_transition_table $table
     * @param string $string
     * @param PHPUnit_Framework_TestCase $test
     */
    public function test_built_table($table, $string, $test) {
        $testtable = $table->to_digraph();
        $table = $this->table_from_string($string);
        $test->assertTrue( $table->equal($testtable), $table->dump_compare($testtable));
    }
    /**
     * Performs a new test
     * @param block_formal_langs_lexical_matching_rule $node node to test against
     * @param string $string
     * @param PHPUnit_Framework_TestCase $test test data
     */
    public function test_table($node, $string, $test) {
        $this->test_built_table($node->build_table(), $string, $test);
    }

    /**
     * Determines, that pair (matching rule, state) is in resulting array for
     * intersection testing
     * @param array $pair
     * @param array $result resulting state
     * @param PHPUnit_Framework_TestCase $test test data
     */
    public function is_in_states($pair, $result, $test) {
        /**
         * @var block_formal_langs_lexical_matching_rule $rule
         */
        $rule = $pair[0];
        $state = $pair[1];
        $contains = false;
        for($i = 0; $i < count($result); $i++) {
            /**
             * @var block_formal_langs_lexical_matching_rule $nrule
             */
            $nrule = $result[$i][0];
            $nstate = $result[$i][1];
            if ($rule->is_same($nrule) && $nstate == $state) {
                $contains = true;
            }
        }
        $test->assertTrue( $contains, var_export($result,true) );
    }
}