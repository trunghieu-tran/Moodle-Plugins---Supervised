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
 * Describes a grammar as a set of rules and starting symbol and validity data
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_items.php');
/**
 *  A common grammar error. Also defines a set of all possible errors
 *  in grammar
 */
class block_formal_langs_grammar_error {
    /**
     * A error code, that defines, that no starting symbols can be determined
     * from grammar
     * @var int
     */
    public static $NO_STARTING_SYMBOLS = 1;
    /**
     * A error, which consists, that shift-reduce conflict is occured in grammar
     * @var int
     */
    public static $SHIFT_REDUCE_CONFLICT = 2;
    /**
     * A error, which consists, that reduce-reduce conflict is occured in grammar
     * @var int
     */
    public static $REDUCE_REDUCE_CONFLICT = 3;
    /**
     * A error, which consists, that no rules are in grammar
     * @var int
     */
    public static $NO_RULES = 4;
    /**
     * A type of grammar error as defined in upper part
     * @var int
     */
    public $type;
    /**
     * First conflict action
     * @var stdClass|null
     */
    public $action1;
    /**
     * Second conflict action
     * @var stdClass|null
     */
    public $action2;
    /**
     * A state, where conflict occured
     * @var int|null
     */
    public $state;
    /**
     * A symbol, which conflict is based
     * @var block_formal_langs_grammar_production_symbol
     */
    public $symbol;
    /**
     * Creates a grammar error with supplied type.
     * For ::$NO_STARTING_SYMBOLS - you must supply no actions,
     * For ::$SHIFT_REDUCE_CONFLICT - you must supply first shift action, than reduce action
     * For ::$REDUCE_REDUCE_CONFLICT - you must supply two reduce action
     * @param int $type type of error
     * @param int|null      $state    state, where conflict occured
     * @param block_formal_langs_grammar_production_symbol|null $symbol, which created some problem
     * @param stdClass|null $action1 first action, shift for shift-reduce conflict
     * @param stdClass|null $action2 second action, reduce for shift-reduce conflict
     */
    public function __construct($type, $state = null, $symbol = null, $action1 = null, $action2 = null) {
        $this->type = $type;
        $this->state = $state;
        $this->symbol = $symbol;
        $this->action1 = $action1;
        $this->action2 = $action2;
    }


    public function dump_error() {
        $result = 'Unknown grammar error found. New type of error?';
        if ($this->type == block_formal_langs_grammar_error::$NO_STARTING_SYMBOLS) {
            $result = 'No starting symbols can be determined';
        }
        if ($this->type == block_formal_langs_grammar_error::$NO_RULES) {
            $result = 'No rules is found in grammar';
        }
        if ($this->type == block_formal_langs_grammar_error::$SHIFT_REDUCE_CONFLICT) {
            $log = new block_formal_langs_debug_log();
            $m = 'Shift-reduce conflict in state %0 with symbol \'%1\': shift to %2 or reduce %3';
            /** @var block_formal_langs_grammar_production_symbol $s  */
            $s = $this->symbol;
            $g = ($this->action1->goto == 0) ? '0' : $this->action1->goto;
            /** @var block_formal_langs_grammar_production_rule $r  */
            $r = $this->action2->rule;
            $result = $log->get_log_message($m, $this->state, $s->type(), $g, $r->tostring());
        }
        if ($this->type == block_formal_langs_grammar_error::$REDUCE_REDUCE_CONFLICT) {
            $log = new block_formal_langs_debug_log();
            $m = 'Reduce-reduce conflict in state %0 with symbol \'%1\': reduce to %2 or reduce %3';
            /** @var block_formal_langs_grammar_production_symbol $s  */
            $s = $this->symbol;
            /** @var block_formal_langs_grammar_production_rule $r1  */
            $r1 = $this->action1->rule;
            /** @var block_formal_langs_grammar_production_rule $r2  */
            $r2 = $this->action2->rule;
            $result = $log->get_log_message($m, $this->state, $s->type(), $r1->tostring(), $r2->tostring());
        }
        return $result;
    }
}

/**
 * Symbol map is a key-value holder for and their associated data symbols
 * When constructing key for symbol in key array must be the same as key data in
 * second array
 */
class block_formal_langs_grammar_symbol_map {
    /**
     * Key values for map
     * @var array of block_formal_langs_grammar_production_symbol
     */
    protected $keys;
    /**
     * Values for map
     * @var array of mixed
     */
    protected $values;
    /**
     * Default value, which is returned when element is not found
     * @var mixed
     */
    protected $defaultvalue;
    /**
     * A maximum key in map
     * @var int
     */
    protected $maxkey;
    /**
     * A strategy for getting a key
     * @var int
     */
    protected $strategy;
    /**
     * Describes whether key is not inside of container, it should not be inseted
     * @var int
     */
    public static $NO_INSERTION = 1;
    /**
     * Describes, that we need to insert new key data
     * @var int
     */
    public static $INSERT = 2;
    /**
     * Constructs new symbol map
     * @param array $keys
     * @param array $values
     * @param null|mixed $default
     * @param int   $strategy strategy, applied when item doesn't exist
     */
    public function __construct($keys = array(), $values = array(), $default = null,
                                $strategy = 1) {
        $this->keys = $keys;
        $this->values = $values;
        $this->defaultvalue = $default;
        $this->strategy = $strategy;
        $maxkey = 0;
        $maxkeyset = false;
        foreach($this->keys as $key => $value) {
           if ($maxkeyset == false || $key > $maxkey) {
               $maxkeyset = true;
               $maxkey = $key;
           }
           if (array_key_exists($key, $values) == false) {
               die('Key ' . $key . ' doesnt exists in values');
           }
        }
        $this->maxkey = $maxkey;
    }

    /**
     * Returns keys
     * @return array
     */
    public function keys() {
        return $this->keys;
    }

    /**
     * Return values
     * @return array
     */
    public function values() {
        return $this->values;
    }
    /**
     * Fetches key slot value
     * @param block_formal_langs_grammar_production_symbol $key key data
     * @param bool $found found data
     * @return int index
     */
    public function fetch_key_slot($key, &$found) {
        $found = true;
        /**
         * @var block_formal_langs_grammar_production_symbol $value
         */
        foreach($this->keys as $keyindex => $value) {
            if ($value->is_same($key)) {
                return $keyindex;
            }
        }
        $found = false;
        return -1;
    }
    /**
     * Defines, whether map contains a data
     * @param block_formal_langs_grammar_production_symbol $key key data
     * @return bool whether it contains a key
     */
    public function contains($key) {
        $found = true;
        $i = $this->fetch_key_slot($key, $found);
        return $found;
    }
    /**
     * Defines, whether map contains a data
     * @param block_formal_langs_grammar_production_symbol $key key data
     * @return mixed associated value
     */
    public function get($key) {
        $found = true;
        $i = $this->fetch_key_slot($key, $found);
        if ($found) {
            return $this->values[$i];
        }  else {
            if ($this->strategy == block_formal_langs_grammar_symbol_map::$INSERT) {
                $newval = $this->defaultvalue;
                if (is_object($newval)) {
                    $newval = clone $newval;
                }
                $this->insert($key, $newval);
                return $this->get($key);
            }
        }
        return $this->defaultvalue;
    }

    /**
     * Inserts a new key-value pair
     * @param block_formal_langs_grammar_production_symbol $key key value
     * @param mixed $value
     */
    public function insert($key, $value) {
        $found = true;
        $i = $this->fetch_key_slot($key, $found);
        if ($found) {
            $this->values[$i] = $value;
        }  else {
            $this->maxkey += 1;
            $this->keys[$this->maxkey] = $key;
            $this->values[$this->maxkey] = $value;
        }
    }

    /**
     * Removes a key value
     * @param  block_formal_langs_grammar_production_symbol $key
     */
    public function remove($key) {
        $found = true;
        $i = $this->fetch_key_slot($key, $found);
        if ($found) {
            unset($this->keys[$i]);
            unset($this->values[$i]);
        }
    }
}

/**
 * A common grammar class as a set of rules and validity data
 * Also it has a starting symbol and all of symbols
 */
class block_formal_langs_grammar {
    /** A reserved non-terminal, when concatenating multiple start-symbol
     */
    protected static $reserved_concat_nonterminal = '__________S';
    /** A reserved starting non-terminal rule
     */
    public static $reserved_start_nonterminal = "__________S'";
    /** Array of production rules. It is extended set, which
     *  includes starting rule $reserved_start_nonterminal -> starting symbol which will be
     *  last.
     *  Also it includes rules for concatenations of multiple starting symbols
     *  @var array of block_formal_langs_grammar_production_rule
     */
    protected $rules;
    /**
     * An array of errors in grammar.
     * @var array of block_formal_langs_grammar_error
     */
    protected $errors;
    /** Array of all terminals and non-terminals of grammar
     *  @var array of block_formal_langs_grammar_production_symbol
     */
    protected $symbols;
    /**
     * Keys for associativity table
     * @var block_formal_langs_grammar_symbol_map with values as array ['assoc'] int (associativity) , ['prec'] int (precedence)
     */
    protected $symbolprec;

    /**
     * Constructs new grammar with following rules
     * @param array $rules of block_formal_langs_grammar_production_rule supplied rules
     */
    public function __construct($rules) {
        $this->rules = $rules;
        $this->errors = array();
        $this->symbols = array();
        $e = array();
        $d = array('assoc' => null, 'prec' => null);
        $this->symbolprec = new block_formal_langs_grammar_symbol_map($e, $e, $d);
        if (count($this->rules) !=0) {
            /**
             * Extend a grammar with new rules
             */
            $this->extend();
            /**
             * Extract a symbols from all of grammar
             */
            $this->symbols = $this->extract_symbols();
        } else {
            $code = block_formal_langs_grammar_error::$NO_RULES;
            $e = new block_formal_langs_grammar_error($code);
            $this->errors[] = $e;
        }
    }

    /**
     * Sets a table and recomputes a rule priorities
     * @param block_formal_langs_grammar_symbol_map $table new table
     * @param array $ruleps  array of priorities for rules as 'rule' => 'symbol' name
     */
    public function set_associativity_precedence_table($table, $ruleps = array()) {
        $this->symbolprec = $table;
        for($i = 0;  $i < count($this->rules); $i++ ) {
            /**
             * @var block_formal_langs_grammar_production_rule $rule
             */
            $rule = $this->rules[$i];
            $lastterminalprecedence = null;
            for($j = 0; $j < $rule->rightcount(); $j++) {
                if($this->is_terminal($rule->right($j)->type())) {
                    $lastterminalprecedence = $this->precedence_for($rule->right($j));
                }
            }
            $rule->set_precedence($lastterminalprecedence);
        }
        if (count($ruleps) != 0) {
            foreach($ruleps as $ruleindex => $priority) {
                $ps = $this->precedence_for($priority);
                if (array_key_exists($ruleindex, $this->rules) && $ps != null) {
                    /**
                     * @var block_formal_langs_grammar_production_rule $rule
                     */
                    $rule = $this->rules[$ruleindex];
                    $rule->set_precedence($ps);
                }
            }
        }
    }

    /**
     * Returns an associativity for a symbol
     * @param block_formal_langs_grammar_production_symbol  $symbol symbol
     * @return int|null associativity
     */
    public function associativity_for($symbol) {
        $a = $this->symbolprec->get($symbol);
        return $a['assoc'];
    }
    /**
     * Returns a precedence for a symbol
     * @param block_formal_langs_grammar_production_symbol  $symbol symbol
     * @return int|null precedence
     */
    public function precedence_for($symbol) {
        $a = $this->symbolprec->get($symbol);
        return $a['prec'];
    }
    /**
     * Return all of rules of grammar
     * @return array of block_formal_langs_grammar_production_rule
     */
    public function rules() {
        return $this->rules;
    }

    /**
     * Returns all of terminal and non-terminal of grammar
     * @return array of block_formal_langs_grammar_production_symbol
     */
    public function symbols() {
        return $this->symbols;
    }

    /**
     * Returns an array of errors in grammar
     * @return array of block_formal_langs_grammar_error
     */
    public function errors() {
        return $this->errors;
    }

    /**
     * Returns, whether grammar is valid
     * @return bool whether grammar is valid
     */
    public function valid() {
        return count($this->errors) == 0;
    }

    /**
     * Adds an error into grammar. Used by table_builder to add some conflicts
     * @param block_formal_langs_grammar_error $error error
     */
    public function add_error($error) {
        $this->errors[] = $error;
    }

    /** Builds extended grammar,
     *  merging all starting symbols into one non-terminal if several found,
     *  and adding one special rule,
     *  which will be last and reducing to it turns to be accept.
     */
    private function extend() {
        $syms = $this->get_starting_symbols();
        if (count($syms) == 0) {
            $type = block_formal_langs_grammar_error::$NO_STARTING_SYMBOLS;
            $this->add_error(new block_formal_langs_grammar_error($type));
            return;
        }
        $action = new block_formal_langs_grammar_replace_action();
        if (count($syms) > 1) {
            $start = block_formal_langs_grammar::$reserved_concat_nonterminal;
            for($i = 0; $i < count($syms); $i++) {
                $this->rules[] = new block_formal_langs_grammar_production_rule(
                    new block_formal_langs_grammar_common_symbol($start),
                    array(new block_formal_langs_grammar_common_symbol($syms[$i])), $action);
            }
        } else {
            $start = $syms[0];
        }
        $fstart = block_formal_langs_grammar::$reserved_start_nonterminal;
        $this->rules[] = new block_formal_langs_grammar_production_rule(
            new block_formal_langs_grammar_common_symbol($fstart),
            array(new block_formal_langs_grammar_common_symbol($start)), $action);
    }

    /** Returns an array of starting symbols, determined from the rules.
     *  The starting symbol is determined as non-terminal,
     *  with which is not found in right part of other rules, except for
     *  rule, that defines himself.
     * Thus, grammar with rules program = stmt and
     * program = program stmt will be considered like a
     * grammar with starting symbol program
     * @return array of string with types of possible symbols.
     */
    private function get_starting_symbols() {
        $result = array();
        for($i = 0 ; $i < count($this->rules); $i++) {
            /** @var block_formal_langs_grammar_production_symbol $sym  */
            /** @var block_formal_langs_grammar_production_rule $rule  */
            $rule = $this->rules[$i];
            $sym = $rule->left();
            if ($sym->is_epsilon() == false && $sym->is_dollar() == false) {
                $left = $sym->type();
                if ($this->is_starting_symbol($left) && !in_array($left, $result)) {
                    $result[] = $left;
                }
            }
        }
        return $result;
    }

    /** Checks, whether following symbol can be considered as starting
     *  @param string $test symbol name
     *  @return boolean true if starting
     */
    private function is_starting_symbol($test) {
        $result = true;
        for($i = 0 ; $i < count($this->rules); $i++) {
            /** @var block_formal_langs_grammar_production_symbol $sym  */
            /** @var block_formal_langs_grammar_production_rule $rule  */
            $rule = $this->rules[$i];
            $sym = $rule->left();
            if ($sym->type() != $test) {
                for($j = 0; $j < $rule->rightcount(); $j++) {
                    if ($rule->right($j)->type()  == $test) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Extracts all non-terminal and terminal symbols, defined in grammar
     * @return array of block_formal_langs_grammar_production_symbol
     */
    public function extract_symbols() {
        $res = array( new block_formal_langs_grammar_dollar_symbol() );
        for($i = 0; $i < count($this->rules); $i++) {
            /** @var block_formal_langs_grammar_production_symbol $sym  */
            /** @var block_formal_langs_grammar_production_rule $rule  */
            $rule = $this->rules[$i];
            $sym = $rule->left();
            if ($this->is_symbol_in_symbol_set($sym, $res) == false  /** && $sym->is_epsilon() == false*/) {
                $res[] = clone $sym;
            }
            for($j = 0; $j < $rule->rightcount(); $j++) {
                $sym = $rule->right($j);
                if ($this->is_symbol_in_symbol_set($sym, $res) == false /** && $sym->is_epsilon() == false */) {
                    $res[] = clone $sym;
                }
            }
        }
        return $res;
    }

    /**
     * Determines, whether symbol is presented in symbol set
     * @param block_formal_langs_grammar_production_symbol $symbol symbol to be scanned with
     * @param array $set of block_formal_langs_grammar_production_symbol of symbols
     * @return bool true if presented
     */
    protected function is_symbol_in_symbol_set($symbol, $set) {
        for($i = 0; $i < count($set); $i++) {
            /** @var block_formal_langs_grammar_production_symbol $el  */
            $el = $set[$i];
            if ($el->is_same($symbol)) {
                return true;
            }
        }
        return false;
    }


    /** Returns a non-terminal definitions
     *  @param string $nonterminal nonterminal definition
     *  @return array of block_formal_langs_grammar_production_rule rules
     */
    public function get_definitions_for($nonterminal) {
        $result = array();
        for($i = 0; $i < count($this->rules); $i++) {
            /** @var block_formal_langs_grammar_production_rule $rule  */
            $rule = $this->rules[$i];
            if ($rule->left()->type() == $nonterminal)
                $result[] = $this->rules[$i];
        }
        return $result;
    }
    /** Returns true, when symbol is non-terminal
     *  @param string $symbol symbol
     *  @return bool whether it's non-terminal
     */
    public function is_nonterminal($symbol) {
        return count($this->get_definitions_for($symbol)) != 0;
    }

    /** Returns true, when it's a terminal
     *  @param string $symbol symbol
     *  @return bool whether it's terminal
     */
    public function is_terminal($symbol) {
        return !($this->is_nonterminal($symbol));
    }

    /** Dumps grammar info as a string data
     *  @return string
     */
    public function tostring()  {
        $strings = array();
        $strings[] = 'Rules: ';
        for($i = 0; $i < count($this->rules); $i++) {
            /** @var block_formal_langs_grammar_production_rule $rule  */
            $rule = $this->rules[$i];
            $strings[] = ' ' . $rule->tostring();
        }

        $symbols = array();
        for($i = 0; $i < count($this->symbols); $i++) {
            /** @var block_formal_langs_grammar_production_symbol $symbol  */
            $symbol = $this->symbols[$i];
            $symbols[] = $symbol->type();
        }
        if (count($symbols)) {
            $strings[] = 'Symbols: \'' . implode('\',\'', $symbols) . '\'';
        }
        if (count($this->errors)) {
            $strings[] = 'Errors: ';
            for ($i = 0; $i < count($this->errors); $i++) {
                /** @var block_formal_langs_grammar_error $error  */
                $error = $this->errors[$i];
                $strings[] = ' ' . $error->dump_error();
            }
        }  else {
            $strings[] = 'No errors found!';
        }
        if (count($strings) != 0) {
           return implode(PHP_EOL, $strings) . PHP_EOL;
        }
        return '';
    }

    /**
     * Returns starting rule
     * @return block_formal_langs_grammar_production_rule
     */
    public function starting_rule() {
        return $this->rules[count($this->rules) - 1];
    }

    /**
     * Returns starting LR(1) item [S' -> .S, $]
     * @return block_formal_langs_grammar_lr_one_item
     */
    public function starting_lr1_item() {
       $a = new block_formal_langs_grammar_lr_zero_item($this->starting_rule(), 0);
       $d = new block_formal_langs_grammar_dollar_symbol();
       return new block_formal_langs_grammar_lr_one_item($a, $d);
    }
    /**
     * Returns accepting LR(1) item [S' -> S., $]
     * @return block_formal_langs_grammar_lr_one_item
     */
    public function accept_lr1_item() {
        $rc = $this->starting_rule()->rightcount();
        $a = new block_formal_langs_grammar_lr_zero_item($this->starting_rule(), $rc);
        $d = new block_formal_langs_grammar_dollar_symbol();
        return new block_formal_langs_grammar_lr_one_item($a, $d);
    }
}