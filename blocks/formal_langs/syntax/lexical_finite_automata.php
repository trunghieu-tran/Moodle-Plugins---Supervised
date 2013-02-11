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
    public $startinstate;
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
     */
    public function build_tables() {
        $this->build_nfa_table();
        $this->exclude_epsilon();
    }

    /**
     * Builds a basic NFA table
     * TODO
     */
    public function build_nfa_table() {

    }

    /**
     * Builds an exclude epsilon data
     */
    public function exclude_epsilon() {

    }
}

// TODO: Interface for lexical wrapper

/**
 * A lexical automata for analyzing string sequences
 */
class block_formal_langs_lexical_automata {
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
     * A buffer state data
     * @var array block_formal_langs_lexical_automata_state
     */
    public $buffers = array();

    /**
     * Contructs a lexer with following starting states
     * @param array $startingstates
     * @param $wrapper wrapper for interaction with string
     */
    public function __construct($startingstates, $wrapper) {
        $this->startingstates = $startingstates;
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


}