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
 * Describes a finite automata data
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/lexical_rules_operators.php');

/**
 * Describes an finite automata state
 */
class block_formal_langs_lexical_automata_state {
    /**
     * A starting state, which determines work of automata
     * @var block_formal_langs_lexical_automata_starting_state
     */
    public $startingstate;
    /**
     * A staring position for match in text
     * @var stdClass
     */
    public $starttextpos;
    /**
     * An ending position for match in text
     * @var stdClass
     */
    public $endtextpos;
    /**
     * A starting position for match in string
     * @var int
     */
    public $startstringpos;

    /**
     * An ending position for match in string
     * @var int
     */
    public $endstringpos;

    /**
     * A buffer, were all consumed symbols are going to
     * @var array of characters
     */
    protected $buffer = array();
    /**
     * Current state of automata
     * @var int
     */
    public $state;
    /**
     * A useful zero width states,
     * @var array
     */
    public $movedfromzerowidthstates;

    /**
     * Returns all available transitions
     * @return array all availble transitions from this state
     */
    public function get_available_transitions() {
        return $this->startingstate->table->transitions_for_state($this->state);
    }
    /**
     * Tests, whether current state is acceptable
     * @return bool
     */
    public function is_acceptable() {
        $table = $this->startingstate->table;
        return in_array($this->state, $table->acceptablestates);
    }


    /**
     * Clones a state with data
     * @param int $newstate new state
     * @param string $appendsym
     * @param bool $zerowidth
     * @return block_formal_langs_lexical_automata_state|null
     */
    public function clone_with_state($newstate, $appendsym = '', $zerowidth = false) {
        if ($zerowidth && in_array($newstate, $this->movedfromzerowidthstates)) {
            return null;
        }
        $result = new block_formal_langs_lexical_automata_state();
        $result->startingstate = $this->startingstate;
        $result->state = $newstate;
        $result->starttextpos = clone $this->starttextpos;
        $result->endtextpos = clone $this->endtextpos;
        $result->startstringpos  = $this->startstringpos;
        $result->endstringpos = $this->endstringpos;
        $result->buffer = $this->buffer;
        if (core_text::strlen($appendsym)) {
            $result->append($appendsym);
        }
        if ($zerowidth) {
            $result->movedfromzerowidthstates[] = $newstate;
        }   else {
            $result->movedfromzerowidthstates = array();
        }
        return $result;
    }

    /**
     * Clears a buffer for finite automata
     */
    public function clear_buffer() {
        $this->buffer = array();
    }

    /**
     * Appends a symbol to real buffer
     * @param array $symbols symbols
     */
    public function append($symbols) {
        for($i = 0; $i < core_text::strlen($symbols); $i++) {
            $this->buffer[] = $symbols[$i];
        }
    }

    /**
     * Length of buffer
     * @return int
     */
    public function length() {
        return count($this->buffer);
    }
    /**
     * Returns a real buffer
     * @return string
     */
    public function buffer() {
        $result = '';
        if (count($this->buffer) != 0) {
            $result = implode('', $this->buffer);
        }
        return $result;
    }

    /**
     * Creates a new position for automata
     * @param $line
     * @param $pos
     * @return stdClass
     */
    public static function position($line, $pos) {
        $a = new stdClass();
        $a->line = $line;
        $a->pos = $pos;
        return $a;
    }

    public static function advance_position($oldpos, $char) {
        $a = clone $oldpos;
        if ($char == "\n") {
            $a->line += 1;
            $a->pos = 0;
        } else {
            $a->pos += 1;
        }
        return $a;
    }
}


class block_formal_langs_lexical_automata_starting_state {
    /**
     * Defines a name of state
     * @var string
     */
    public $statename = 'YYINITIAL';
    /**
     * Defines a state type
     * @var int
     */
    public $statetype = 1; // We denote new state as inclusive, to make initial state construction more simple
    /**
     * Array of matching rules
     * @var array of nodes
     */
    public $rules = array();
    /**
     * Array of actions
     * @var array
     */
    public $actions = array();

    /**
     * A table for scanning
     * @var block_formal_langs_lexical_transition_table
     */
    public $table = null;

    /**
     * Array that maps states to action keys in array
     * @var array
     */
    public $statestoactions = array();
    /**
     * Defines an inclusive starting state
     * @var int
     */
    public static $INCLUSIVE = 1;

    /**
     * Defines an exclusive starting state
     * @var int
     */
    public static $EXCLUSIVE = 2;

    /**
     * Checks whether state transitions from all data are available
     * @param block_formal_langs_lexer_interaction_wrapper $wrapper
     * @param block_formal_langs_lexical_automata $automata
     * @return bool whether check was successfull
     */
    public function check_state_transitions($wrapper, $automata) {
        $error = false;
        if (count($this->actions)) {
            /**
             * @var block_formal_langs_lexical_action $action
             */
            foreach($this->actions as $action) {
                $nstate = $action->new_lexer_starting_state();
                if ($automata->get_starting_state($nstate) == null
                    && !$error) {
                    $error = false;
                    $wrapper->table_build_error(
                      block_formal_langs_lexer_table_error_type::$AUTOMATAENTERSNONEXISTENTSTATE,
                      (object)array('startingstate' => $this->statename, 'state' => $nstate)
                    );
                    $automata->table_error_occured();
                }
            }
        }
        return !$error;
    }
    /** Builds and optimizes a table
     *  @param block_formal_langs_lexer_interaction_wrapper
     *  @param block_formal_langs_lexical_automata $automata
     */
    public function build_tables($wrapper, $automata) {
        if ($this->build_nfa_table($wrapper, $automata)) {
            $this->exclude_epsilon();
        }
    }

    /**
     * Builds a basic NFA table
     * @param block_formal_langs_lexer_interaction_wrapper $wrapper
     * @param block_formal_langs_lexical_automata $automata
     * @return bool
     */
    public function build_nfa_table($wrapper, $automata) {
        if (count($this->rules) == 0) {
            $wrapper->table_build_error(block_formal_langs_lexer_table_error_type::$STATEISEMPTY, $this->statename);
            $automata->table_error_occured();
            return false;
        }
        if (count($this->rules) != count($this->actions)) {
            $wrapper->table_build_error(block_formal_langs_lexer_table_error_type::$ACTIONSARENOTEQUALOFSTATES, $this->statename);
            $automata->table_error_occured();
            return false;
        }
        $this->table = block_formal_langs_lexical_alternative_operator::build_non_concatenated($this->rules);
        $i = 0;
        foreach($this->table->acceptablestates as $state) {
            $this->statestoactions[$state] = $i;
            $i++;
        }
        return true;
    }

    /**
     * Finds first unmarked DFA state
     * @param array $dstates states
     * @return int|null number of state, null if not found
     */
    private static function find_first_unmarked_state($dstates) {
        $result = null;
        for($i = 0; ($i < count($dstates)) && ($result === null); $i++) {
            if ($dstates[$i][1] == false) {
                $result = $i;
            }
        }
        return $result;
    }

    /**
     * Finds a state in dsates, returning index on data
     * @param array $dstates
     * @param array $state
     * @return int|null
     */
    private static function find_state_in_dstates($dstates, $state) {
        $result = null;
        for($i = 0; ($i < count($dstates)) && ($result === null); $i++) {
            if ($dstates[$i][0] == $state) {
                $result = $i;
            }
        }
        return $result;
    }

    /**
     * Finds NFA state in DFA state
     * @param array $dstates
     * @param int $state
     * @return array DFA states, which contains NFA state
     */
    private static function find_nfa_state_in_dfa($dstates, $state) {
        $result = array();
        for($i = 0; $i < count($dstates); $i++) {
            if (in_array($state, $dstates[$i][0])) {
                $result[] = $i;
            }
        }
        return $result;
    }
    /**
     * Builds an exclude epsilon data
     */
    public function exclude_epsilon() {

        // Here we build some DFA
        $dstates = array(array($this->table->epsilon_closure(0), false));
        $dtran = array();
        $firstunmarked = 0;
        do {
            $dstates[$firstunmarked][1] = true;
            $states = $dstates[$firstunmarked][0];
            $disjointtransitions = $this->table->build_disjoint_transitions($states);
            for($i = 0; $i < count($disjointtransitions); $i++) {
                $transition = $disjointtransitions[$i];
                $rule = $transition[0];
                $nstates = $this->table->epsilon_closure($transition[1]);
                $stateindex = $this->find_state_in_dstates($dstates, $nstates);
                if ($stateindex === null) {
                    $stateindex = count($dstates);
                    $dstates[] = array($nstates, false);
                }
                $drule = new block_formal_langs_lexical_transition_rule($firstunmarked, $rule, array($stateindex));
                $dtran[] = $drule;
            }
            $firstunmarked = self::find_first_unmarked_state($dstates);
        } while($firstunmarked !== null);


        // 1. Fill DFA states mapping to acceptable states
        // 2. Fill acceptable states
        $dfatonfacceptable = array();
        $newacceptable = array();
        for ($i = 0; $i < count($this->table->acceptablestates); $i++) {
            $s = $this->table->acceptablestates[$i];
            $dfafoundstates = self::find_nfa_state_in_dfa($dstates, $s);
            for($j = 0; $j < count($dfafoundstates); $j++) {
                $dfafoundstate = $dfafoundstates[$j];
                if (array_key_exists($dfafoundstate, $dfatonfacceptable) == false) {
                    $dfatonfacceptable[$dfafoundstate] = array();
                }
                $dfatonfacceptable[$dfafoundstate][] = $s;
            }
            $newacceptable = block_formal_langs_lexical_matching_rule::union($newacceptable, $dfafoundstates);
        }
        sort($newacceptable);
        // Rebuild states to action table
        $newstatestoaction = array();
        for ($i = 0; $i < count($newacceptable); $i++) {
            $newacceptablestate = $newacceptable[$i];
            // Get list of acceptables, which state is linked to
            $oldacceptablelist = $dfatonfacceptable[$newacceptablestate];
            // Find mapped action (if few can be selected, chose one with lesser
            // number)
            $currentselectedaction = 0;
            $foundmin = false;
            for($j = 0; $j < count($oldacceptablelist); $j++) {
                $possibleaction = $this->statestoactions[$oldacceptablelist[$j]];
                if ($foundmin == false || $possibleaction < $currentselectedaction) {
                    $foundmin = true;
                    $currentselectedaction = $possibleaction;
                }
            }
            $newstatestoaction[$newacceptablestate] = $currentselectedaction;
        }
        $newtable = new block_formal_langs_lexical_transition_table();
        $newtable->transitions = $dtran;
        $newtable->acceptablestates = $newacceptable;
        $this->table = $newtable;
        $this->statestoactions = $newstatestoaction;
    }
}


/**
 * A error type enum for building states for automata
 */
class block_formal_langs_lexer_table_error_type {
    /**
     * A error, when automata enters a starting state, which doesn't exists at all
     * @var int
     */
    public static $AUTOMATAENTERSNONEXISTENTSTATE = 1;
    /**
     * Error, when initial state is abdent in array
     * @var int
     */
    public static $YYINITIALISABSENT = 2;
    /**
     * Error, when no rules, are assigned to state
     * @var int
     */
    public static $STATEISEMPTY = 3;

    /**
     * Occurs, when there are more or less actions or equal of states
     * @var int
     */
    public static $ACTIONSARENOTEQUALOFSTATES = 4;
}
/**
 * Simple interface for interaction with other primitives
 */
interface block_formal_langs_lexer_interaction_wrapper {
    /**
     * Determines an action, which should be performed when table build error occurs
     * @param $type
     * @param $data
     */
    public function table_build_error($type, $data);

    /**
     * Determines an action, performed when tokenizing error occurs
     * @param block_formal_langs_lexical_automata_state $state
     */
    public function tokenize_error($state);

    /**
     * Determines an action, which is performed when action was toggled in state
     * after action finished it's work
     * @param block_formal_langs_lexical_simple_action|null $action
     * @param block_formal_langs_lexical_automata_state  $state
     */
    public function accept($action, $state);

    /**
     * Returns a symbol from position. Returns EOF symbol at end
     * @param int $pos
     * @return string|int
     */
    public function get($pos);
    /**
     * Sets assigned lexical automata
     * @param block_formal_langs_lexical_automata $lexer
     */
    public function set_lexer($lexer);
}

class block_formal_langs_lexer_interaction_wrapper_impl {
    /**
     * An inner string for tokenizing
     * @var string
     */
    protected $string;
    /**
     * A lexer for toggling data
     * @var  block_formal_langs_lexical_automata
     */
    protected $lexer;
    /**
     * Array of matched tokens
     * @var array
     */
    protected $tokens;

    /**
     * All errors, which  occured while building tables
     * @var array of array(code, null)
     */
    protected $tableerrors;
    /**
     * Whether token errors were found
     * @var array token errors
     */
    protected $tokenerrors;
    /**
     * Preprocesses a CP1251 string, or MAC, removing and replacing all to "\n"
     * @param string $str
     * @return string
     */
    public static function preprocess($str) {
        $fstr = str_replace("\r\n", "\n", $str);
        $fstr = str_replace("\r", "\n", $fstr);
        return $fstr;
    }

    /**
     * Constructs a wrapper from a string
     * @param $string
     */
    public function __construct($string) {
        $this->string = self::preprocess($string);
        $this->tokens = array();
        $this->tableerrors = array();
    }
    /**
     * Returns occured table errors
     * @return array of table errors
     */
    public function table_errors() {
        return $this->tableerrors;
    }

    /**
     * Returns count of token errors
     * @return array
     */
    public function token_errors() {
        return $this->tokenerrors;
    }
    /**
     * Returns array of tokens
     * @return array 
     */
    public function tokens() {
        return $this->tokens;
    }
    /**
     * Determines an action, which should be performed when table build error occurs
     * @param $type
     * @param $data
     */
    public function table_build_error($type, $data) {
        $this->tableerrors[] = array($type, $data);        
    }

    /**
     * Determines an action, performed when tokenizing error occurs
     * @param block_formal_langs_lexical_automata_state $state
     */
    public function tokenize_error($state) {
        $this->tokenerrors[] =  $state;
    }

    /**
     * Determines an action, which is performed when action was toggled in state
     * after action finished it's work
     * @param block_formal_langs_lexical_simple_action|null $action  null on EOF
     * @param block_formal_langs_lexical_automata_state  $state
     */
    public function accept($action, $state) {
        if ($this->lexer->result() != null) {
            $this->tokens[] = $this->lexer->result();
        }
    }

    /**
     * Computes next token from lexer
     * @return mixed|null
     */
    public function next_token() {
        $r = null;
        if (count($this->tokenerrors) == 0) {
            $this->lexer->lex();
            $r = $this->lexer->result();
        }
        return $r;
    }

    /**
     * Returns a symbol from position. Returns EOF symbol at end
     * @param int $pos
     * @return string|int
     */
    public function get($pos) {
        if ($pos >= core_text::strlen($this->string)) {
            return block_formal_langs_lexical_matching_rule_type::$EOF_SYMBOL;
        }
        return core_text::substr($this->string, $pos, 1);
    }
    /**
     * Sets assigned lexical automata
     * @param block_formal_langs_lexical_automata $lexer
     */
    public function set_lexer($lexer) {
        $this->lexer = $lexer;
    }

}
/**
 * A lexical automata for analyzing string sequences
 */
class block_formal_langs_lexical_automata {
    /**
     * An interaction wrapper
     * @var block_formal_langs_lexer_interaction_wrapper
     */
    protected $wrapper;
    /**
     * An array of starting states
     * @var array of block_formal_langs_lexical_automata_starting_state
     */
    protected $startingstates;
    /**
     * Sets current starting state
     * @var string
     */
    protected $currentstartingstate = 'YYINITIAL';
    /**
     * An action, which must be performed, when state is accepted
     * @var int stateaction
     */
    protected $acceptedstateaction = 1;
    /**
     * An action, in which automata must restart, when accepting rule
     * @var int
     */
    public static $RESTARTAUTOMATA = 1;
    /**
     * An action, in which automata must return a result
     * @var int
     */
    public static $RETURN = 2;
    /**
     * A result, which is toggled, when accepting some rule
     * @var mixed
     */
    protected $acceptedresult = null;
    /**
     * Determines, whether fatal error on building table occurs
     * @var bool
     */
    protected $error = false;
    /**
     * Last starting string position data
     * @var int
     */
    protected $laststartposition = 0;
    /**
     *  Last starting text position data
     * @var stdClass
     */
    protected $laststarttextposition = null;
    /**
     * Determines, whether error, when creating tables occured
     */
    public function table_error_occured() {
        $this->error = true;
    }


    /**
     * A buffer state data
     * @var array block_formal_langs_lexical_automata_state
     */
    public $buffers = array();

    /**
     * Returns a starting state by name
     * @param string $startingstate a string state
     * @return block_formal_langs_lexical_automata_starting_state|null null if not found
     */
    public function get_starting_state($startingstate) {
        $result = null;
        if (count($this->startingstates)) {
            /**
             *  block_formal_langs_lexical_automata_starting_state $state
             */
            foreach($this->startingstates as $state) {
                if ($state->statename  == $startingstate) {
                    $result = $state;
                }
            }
        }
        return $result;
    }

    /**
     * Returns a starting state index by name
     * @param string $startingstate a string state
     * @return int
     */
    public function get_starting_state_index($startingstate) {
        $result = null;
        if (count($this->startingstates)) {
            $i = 0;
            /**
             *  block_formal_langs_lexical_automata_starting_state $state
             */
            foreach($this->startingstates as $state) {
                if ($state->statename  == $startingstate) {
                    $result = $i;
                }
                $i++;
            }
        }
        return $result;
    }
    protected function check_action_transitions() {
        if (count($this->startingstates) != 0) {
            /**
             *  @var block_formal_langs_lexical_automata_starting_state $state
             */
            foreach($this->startingstates as $state) {
                $state->check_state_transitions($this->wrapper, $this);
            }
        }
        return !($this->error);
    }
    /**
     * Contructs a lexer with following starting states
     * @param array $startingstates
     * @param block_formal_langs_lexer_interaction_wrapper $wrapper wrapper for interaction with string
     */
    public function __construct($startingstates, $wrapper) {
        $this->startingstates = $startingstates;
        $this->wrapper = $wrapper;
        $this->wrapper->set_lexer($this);
        if ($this->get_starting_state('YYINITIAL') == null) {
            $this->wrapper->table_build_error(
                block_formal_langs_lexer_table_error_type::$YYINITIALISABSENT, null
            );
            $this->table_error_occured();
        }  else {
            if ($this->check_action_transitions()) {
                /**
                 * @var block_formal_langs_lexical_automata_starting_state $state
                 */
                foreach ($this->startingstates as $state) {
                    $state->build_tables($wrapper, $this);
                }
                $this->currentstartingstate = 'YYINITIAL';
                $this->laststartposition  = 0;
                $this->laststarttextposition = block_formal_langs_lexical_automata_state::position(0, 0);
            }
        }
    }

    /**
     * Inits a local buffer
     * @param string $name
     * @return block_formal_langs_lexical_automata_state
     */
    public function init_local_buffer($name) {
        $this->buffers[$name] = new block_formal_langs_lexical_automata_state();
        return $this->buffers[$name];
    }

    /**
     * Returns a local buffer
     * @param string $name
     * @return block_formal_langs_lexical_automata_state
     */
    public function get_local_buffer($name) {
        if (array_key_exists($name, $this->buffers) == false) {
            $this->buffers[$name] = new block_formal_langs_lexical_automata_state();
        }
        return $this->buffers[$name];
    }

    /**
     * Sets a starting state
     * @param string $state
     */
    public function set_starting_state($state) {
        $this->currentstartingstate = $state;
    }

    /**
     * Returns a starting state
     * @return string
     */
    public function starting_state() {
        return $this->currentstartingstate;
    }

    /**
     * Sets an acception state for automata
     * @param  int $action new action
     */
    public function set_accept_action($action) {
        $this->acceptedstateaction = $action;
    }

    /**
     * Returns an action, performed on accept
     * @return int
     */
    public function accept_action() {
        return $this->acceptedstateaction;
    }

    /**
     * Sets an accepted result
     * @param mixed $result
     */
    public function set_result($result) {
        $this->acceptedresult = $result;
    }

    /**
     * Returns a result. Returns null if nothing found
     * @return mixed|null
     */
    public function result() {
        return $this->acceptedresult;
    }

    /**
     * Returns last starting position character
     * @return int|string
     */
    public function get() {
        return $this->wrapper->get($this->laststartposition);
    }

    /**
     * Returns last starting position character and advances a position
     * @return int|string
     */
    public function  getc() {
        $c = $this->wrapper->get($this->laststartposition);
        $this->laststartposition = $this->laststartposition + 1;
        return $c;
    }
    /**
     * Performa actual lexical analysis
     */
    public function lex() {
        // If error occured, return nothing
        if ($this->error) {
            return;
        }
        if ($this->currentstartingstate == '') {
            $this->currentstartingstate = 'YYINITIAL';
        }
        // Create initial state stuff
        $startingstate = $this->get_starting_state($this->currentstartingstate);
        $inc = block_formal_langs_lexical_automata_starting_state::$INCLUSIVE;
        $astates = array();

        $state = new block_formal_langs_lexical_automata_state();
        $state->startingstate = $startingstate;
        $state->state = 0;
        $state->startstringpos = $this->laststartposition;
        $state->starttextpos = clone $this->laststarttextposition;
        $state->movedfromzerowidthstates = array();
        $state->endstringpos = $this->laststartposition;
        $state->endtextpos = clone $this->laststarttextposition;

        $astates[] = $state;

        if ($startingstate->statetype == $inc) {
            $sstate = $this->get_starting_state('');
            if ($sstate != null) {
                $cstate = clone $state;
                $cstate->startingstate = $sstate;
                $cstate->starttextpos = clone $this->laststarttextposition;
                $cstate->endtextpos = clone $this->laststarttextposition;
                $astates[] = $state;
            }
        }

        $this->set_result(null);
        // Create current maximum scanned position
        $maximumpos = new stdClass();
        $maximumpos->stringpos = $this->laststartposition;
        $maximumpos->textpos = clone $this->laststarttextposition;
        $maximumpos->state = $state;
        $acceptedstates = array();
        // Iterate loop for matching stuff data
        while(count($astates)) {
            /**
             * @var block_formal_langs_lexical_automata_state $curstate
             */
            $curstate = array_shift($astates);
            // Compute new maximum position
            $maximumpos = self::maximumpos($maximumpos, $curstate);
            // If state is acceptable, compute accepted states
            if ($curstate->is_acceptable()) {
                $acceptedstates = self::pick_maximum_acceptable_states($acceptedstates, $curstate);
            }

            $transitions = $curstate->get_available_transitions();
            $matchchar = $this->wrapper->get($curstate->endstringpos);
            for($i = 0; $i < count($transitions); $i++) {
                /**
                 * @var block_formal_langs_lexical_transition_rule $transition
                 */
                $transition = $transitions[$i];
                // Is this is zerowidth assertions, we not advance
                if ($transition->rule->match($matchchar, $curstate->endtextpos)) {
                    $zwidth = $transition->rule->is_zero_width();
                    $mchar = ($zwidth) ? '' : $matchchar;
                    for($j = 0; $j < count($transition->newstates); $j++) {
                        $newstate = $transition->newstates[$j];
                        $newautostate = $curstate->clone_with_state($newstate,$mchar,$zwidth);

                        if ($zwidth == false) {
                            $newautostate->endstringpos += 1;
                            $fka = $newautostate->advance_position($newautostate->endtextpos, $matchchar);
                            $newautostate->endtextpos = $fka;
                        }

                        if ($newautostate != null) {
                            $astates[] = $newautostate;
                        }
                    }
                }
            }
        }
        if (count($acceptedstates) == 0) {
            $char = $this->wrapper->get($maximumpos->stringpos);
            $this->laststartposition = $maximumpos->stringpos;
            $this->laststarttextposition = $maximumpos->textpos;
            if ($char == block_formal_langs_lexical_matching_rule_type::$EOF_SYMBOL) {
               $this->wrapper->accept(null, $maximumpos->state);
            }  else {
               $this->wrapper->tokenize_error($maximumpos->state);
            }
        }  else {

            // Take all starting states from states and pick one with less index
            $stateindexestonames = array();
            for($i = 0; $i < count($acceptedstates); $i++) {
                $curstate = $acceptedstates[$i];
                $index = $this->get_starting_state_index($curstate->startingstate->statename);
                $stateindexestonames[$index] = $curstate->startingstate->statename;
            }
            $minstartingstate = $stateindexestonames[min(array_keys($stateindexestonames))];
            // Now, when we picked a state with some least indexes,
            $servedacceptedtates = array();
            for($i = 0; $i < count($acceptedstates); $i++) {
                $curstate = $acceptedstates[$i];
                if ($curstate->startingstate->statename == $minstartingstate) {
                    $actionindex = $curstate->startingstate->statestoactions[$curstate->state];
                    $servedacceptedtates[$actionindex] = $curstate;
                }
            }

            /**
             * @var block_formal_langs_lexical_automata_state $minactionstate
             * @var block_formal_langs_lexical_action $action
             */
            // Apply rules
            $minactionstate = $servedacceptedtates[min(array_keys($servedacceptedtates))];
            $actionindex = $minactionstate->startingstate->statestoactions[$minactionstate->state];
            $action = $minactionstate->startingstate->actions[$actionindex];
            $action->accept($this, $minactionstate);
            $this->wrapper->accept($action, $minactionstate);
            $this->laststartposition = $minactionstate->endstringpos;
            $this->laststarttextposition = $minactionstate->endtextpos;
        }
    }

    /**
     * Picks maximum acceptable states
     * @param array $oldacceptables
     * @param block_formal_langs_lexical_automata_state $state
     * @return array
     */
    public static function pick_maximum_acceptable_states($oldacceptables, $state) {
        $result = array();
        if (count($oldacceptables)) {
            /**
             * @var block_formal_langs_lexical_automata_state $tstate
             */
            $tstate = $oldacceptables[0];
            if ($tstate->length() == $state->length()) {
                $result = $oldacceptables;
                $result[] = $state;
            } else {
                if ($tstate->length() < $state->length()) {
                    $result = array($state);
                } else {
                    $result = $oldacceptables;
                }
            }
        }  else {
            $result = array($state);
        }
        return $result;
    }
    /**
     * Computes maximum position of state
     * @param stdClass $maximumpos old maximum position
     * @param block_formal_langs_lexical_automata_state $state
     * @return stdClass
     */
    private static function maximumpos($maximumpos, $state) {
        if ($maximumpos->stringpos > $state->endstringpos) {
            $maximumpos->stringpos = $state->endstringpos;
            $maximumpos->textpos = clone $state->endtextpos;
            $maximumpos->state = $state;
        }
        return $maximumpos;
    }

}