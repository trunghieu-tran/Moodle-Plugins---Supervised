<?
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
        $result->starttextpos = clone $this->starttextpos;
        $result->endtextpos = clone $this->endtextpos;
        $result->startstringpos  = $this->startstringpos;
        $result->endstringpos = $this->endstringpos;
        $result->buffer = $this->buffer;
        if (textlib::strlen($appendsym)) {
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
        for($i = 0; $i < textlib::strlen($symbols); $i++) {
            $this->buffer[] = $symbols[$i];
        }
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
     * Builds an exclude epsilon data
     */
    public function exclude_epsilon() {

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
     * @param block_formal_langs_lexical_simple_action $action
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
     * Constructs a wrapper from a string
     * @param $string
     */
    public function __construct($string) {
        $this->string = $string;
        $this->tokens = array();
    }

    public function tokens() {
        return $this->tokens;
    }
    /**
     * Determines an action, which should be performed when table build error occurs
     * @param $type
     * @param $data
     */
    public function table_build_error($type, $data) {
        echo 'Error' . $type . ' with ata : ' . $data;
    }

    /**
     * Determines an action, performed when tokenizing error occurs
     * @param block_formal_langs_lexical_automata_state $state
     */
    public function tokenize_error($state) {
        echo 'Accepted';
        echo $state->buffer();
    }

    /**
     * Determines an action, which is performed when action was toggled in state
     * after action finished it's work
     * @param block_formal_langs_lexical_simple_action $action
     * @param block_formal_langs_lexical_automata_state  $state
     */
    public function accept($action, $state) {
        $this->tokens[] = $this->lexer->result();
    }

    /**
     * Returns a symbol from position. Returns EOF symbol at end
     * @param int $pos
     * @return string|int
     */
    public function get($pos) {
        if ($pos >= textlib::strlen($this->string)) {
            return block_formal_langs_lexical_matching_rule_type::$EOF_SYMBOL;
        }
        return textlib::substr($this->string, $pos, 1);
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
     * Contructs a lexer with following starting states
     * @param array $startingstates
     * @param block_formal_langs_lexer_interaction_wrapper $wrapper wrapper for interaction with string
     */
    public function __construct($startingstates, $wrapper) {
        $this->startingstates = $startingstates;
        $this->wrapper = $wrapper;
        $this->wrapper->set_lexer($this);
        // TODO:
        // 1. Build a tables and handle exceptions
        // 2. Optimize tables and exclude epsilon moves
        // 3. If exceptions, handle it with wrapper
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


    public function lex() {
        // TODO: Implement
    }

}