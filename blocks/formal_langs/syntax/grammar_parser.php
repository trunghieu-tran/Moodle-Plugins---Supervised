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
 * Describes an algorithm for parsing some user-defined data
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_table.php');
/**
 *  This is a simple wrapper to handle interaction between lexer and parser
 *  It MUST be reimplemented by user to bind into existing architecture
 */
class block_formal_langs_lexer_parser_interaction_wrapper {
    /**
     * A lexer-like stuff, that is wrapped
     * @var block_formal_langs_lexer
     */
    protected $lexer;

    /**
     * Constructs a wrapper around lexer to work with parser
     * @param block_formal_langs_lexer $lexer a supplied lexer
     */
    public function __construct($lexer) {
        $this->lexer = $lexer;
    }

    /**
     * An action, which is triggered, when we are starting to tokenise some stuff
     */
    public function start_tokenizing() {

    }

    /**
     * Returns some token stuff. It must have a ::type() method, which returns a string, that
     * corresponds to a terminal stuff
     * @return mixed|null
     */
    public function next_token() {
        return $this->lexer->next_token();
    }


    /**
     * Custom error action
     * @param block_formal_langs_grammar_parser $parser a parser, which can be restarted
     * @param mixed $symbol a lexer symbol, where error was ocurred
     */
    public function error($parser, $symbol) {
        $l = new block_formal_langs_debug_log();
        if ($symbol == null) {
            $l->log('$');
        } else {
            $l->log($symbol->type());
        }
        $l->log('Error!');
    }
}



/** A parser, which works with sequence of data
 */
class block_formal_langs_grammar_parser {
    /** A tables for parsing
     * @var block_formal_langs_grammar_table
     */
    protected $tables;
    /** A wrapper for interaction  with lexer
     * @var block_formal_langs_lexer_parser_interaction_wrapper
     */
    protected $wrapper;
    /** A stack for working with data
     *  @var array
     */
    protected $stack;
    /** Whether parser is running
     * @var boolean
     */
    protected $running;
    /**
     * Whether error
     * @var boolean
     */
    protected $error;
    /**
     * Current parsing token
     * @var block_formal_langs_grammar_boxed_ast_node|null
     */
    protected $currenttoken;

    /**
     * Fetches next token from a wrapper
     */
    public function fetch_next_token() {
       $this->currenttoken = $this->wrapper->next_token();
       if ($this->currenttoken != null) {
           $type = $this->currenttoken->type();
           $tok = $this->currenttoken;
           $this->currenttoken = new block_formal_langs_grammar_boxed_ast_node($type, $tok);
       }
    }

    /**
     * Toggle error state
     * @param bool $error
     */
    public function toggle_error($error) {
        $this->error = $error;
    }
    /**
     * Stops a parser work. Used for automatic error recovery.
     */
    public function stop() {
        $this->running = false;
    }

    /**
     * Continues parsing. Used for automatic error recovery.
     */
    public function continue_parsing() {
        $this->running = true;
        $this->toggle_error(false);
    }

    public function restart_parsing() {
        $this->set_stack( array( 0 ) );
        $this->continue_parsing();
        $this->fetch_next_token();
        $this->toggle_error(false);
    }
    /**
     * Returns a stack array data
     * @return array
     */
    public function get_stack() {
        return $this->stack;
    }

    /**
     * This function is useful for automatic recovery
     * @param array $stack new stack
     */
    public function set_stack($stack) {
        $this->stack = $stack;
    }
    /** Constructs a parser with specified
     *  @param block_formal_langs_grammar_table $t tables for parsing a data
     *  @param block_formal_langs_lexer_parser_interaction_wrapper $w wrapper for interaction
     */
    public function __construct($t, $w) {
        $this->tables = $t;
        $this->wrapper= $w;
        $this->stack = array();
        $this->running = false;
    }

    /**
     * Computes goto state data
     * @param int $state old state inside of stack
     * @param block_formal_langs_grammar_boxed_ast_node $token
     * @return int
     */
    private function calcgoto($state, $token) {
        $gototable = $this->tables->gototable();
        $row = $gototable[$state];
        $nstate = -1;
        if ($token == null) {
            for($i = 0; $i < count($row); $i++) {
                /** @var  block_formal_langs_grammar_production_symbol $sym  */
                $sym = $row[$i]['symbol'];
                if ($sym->is_dollar()) {
                    $nstate = $row[$i]['goto'];
                }
            }
        } else {
            for($i = 0; $i < count($row); $i++) {
                /** @var  block_formal_langs_grammar_production_symbol $sym  */
                $sym = $row[$i]['symbol'];
                if ($sym->type() == $token->type()) {
                    $nstate = $row[$i]['goto'];
                }
            }
        }
        return $nstate;
    }

    /**
     * Dumps a stack element
     * @param int|block_formal_langs_grammar_boxed_ast_node $sym
     * @return string
     */
    private function dump_stack_element($sym) {
        if ($sym === null) {
            return '$';
        } else {
            if (is_object($sym)) {
                return $sym->type();
            } else {
                return ($sym == 0) ? '0' : $sym;
            }
        }
    }

    /**
     * Returns a dump of parser's stack
     * @return parser stack
     */
    private function dump_stack() {
        $string = '';
        for($i = 0; $i < count($this->stack); $i++ ){
            $string.= $this->dump_stack_element($this->stack[$i]);
            $string.= ' ';
        }
        return $string;
    }

    /**
     * Returns top of stack. If stack is not broken it must be int
     * @return int
     */
    public function current_state() {
        return $this->stack[count($this->stack) - 1];
    }

    /**
     * Returns current itemset, defined by current set
     * @return array of all possible actions
     */
    public function current_actions() {
        $a = $this->tables->action();
        return  $a[$this->current_state()];
    }

    /**
     * Parses a stream of data
     * @return mixed top node
     */
    public function parse() {
        $this->wrapper->start_tokenizing();
        $this->restart_parsing();
        $log = new block_formal_langs_debug_log();
        while($this->running) {
            $state = $this->current_state();
            /** @var stdClass|null $action  */
            $action = null;
            $row = $this->current_actions();
            $log->log('Current state %0 and token %1', $state, $this->dump_stack_element($this->currenttoken));
            $log->log('Current stack: %0', $this->dump_stack());
            if ($this->currenttoken == null) {
                for($i = 0; $i < count($row); $i++) {
                    /** @var block_formal_langs_grammar_production_symbol $sym  */
                    $sym = $row[$i]['symbol'];
                    if ($sym->is_dollar()) {
                        $action = $row[$i]['action'];
                    }
                }
            } else {
                for($i = 0; $i < count($row); $i++) {
                    /** @var block_formal_langs_grammar_production_symbol $sym  */
                    $sym = $row[$i]['symbol'];
                    if ($sym->type() == $this->currenttoken->type()) {
                        $action = $row[$i]['action'];
                    }
                }
            }

            if ($action != null) {
                if ($action->type == 'shift') {
                    $log->log('Shift: %0', $action->goto);
                    $this->stack[] = $this->currenttoken;
                    $this->stack[] = $action->goto;
                    $this->fetch_next_token();
                }
                if ($action->type == 'reduce') {
                    /** @var block_formal_langs_grammar_production_rule $rule  */
                    $rule = $action->rule;
                    $log->log('Reduce: %0', $rule->tostring());

                    $j = 0;
                    $children = array();
                    for($i = count($this->stack) - 2* $rule->rightcount(); $i < count($this->stack); $i++) {
                        if (($j % 2) == 0) {
                            $children[] = $this->stack[$i];
                        }
                        $j++;
                    }
                    array_splice($this->stack, count($this->stack) - 2 * $rule->rightcount());
                    $currentstate = $this->current_state();
                    $A = $rule->action()->apply($rule, $children);
                    $kstate = $this->calcgoto($currentstate, $A);
                    if ($kstate != -1) {
                        $this->stack[] = $A;
                        $this->stack[] = $kstate;
                    } else {
                        $log->log('Error while reducing');
                        $this->stop();
                        $this->toggle_error(true);
                        $this->wrapper->error($this, $A->unbox());
                    }
                    $log->log('After: %0', $this->dump_stack());
                }
                if ($action->type == 'accept') {
                    $this->stop();
                    $log->log('Accept: %0', $this->dump_stack());

                }
            } else {
                $log->log('Common state error');
                $wtoken = $this->currenttoken;
                if ($wtoken != null) {
                    $wtoken = $wtoken->unbox();
                }
                $this->stop();
                $this->toggle_error(true);
                $this->wrapper->error($this, $wtoken);
            }
        }
        $object = null;
        if ($this->error == false) {
            for($i = count($this->stack)-1; ($i > 0) && ($object == null) ; $i--) {
                if (is_object($this->stack[$i])) {
                    /** @var block_formal_langs_grammar_boxed_ast_node $o  */
                    $o = $this->stack[$i];
                    $object = $o->unbox();
                }
            }
        }
        $log->dump_log();
        return $object;
    }
}