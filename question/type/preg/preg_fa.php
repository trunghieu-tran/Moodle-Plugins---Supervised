<?php
/**
 * Defines classes of finite automaton for regular expression matching, it's state and transition.
 *
 * The class is intended for use by FA-based matching engines (DFA and NFA), and maybe other regex handlers.
 * Main purpose of the class is to enchance testability, code reuse and standartisation between FA-based matching engines.
 *
 * @copyright &copy; 2012  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

/**
* Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa
*/
abstract class qtype_preg_finite_automaton {

    /** @var array of qtype_preg_fa_state, indexed by state number */
    protected $states;
    /** @var integer index of start state in the states array */
    protected $startstate;

    /** @var boolean is automaton really deterministic - it could be even if it shoudn't 
    *
    * May be used for optimisation when NFA class actually store DFA
    */
    protected $deterministic;

    /** @var boolean whether automaton has epsilon-transtions*/
    protected $haseps;
    /** @var boolean whether automaton has simple assertion transtions*/
    protected $hasassertiontransitions;

    public function __contruct() {
        $this->states = array();
        $this->startstate = 0;
        $this->deterministic = true;
        $this->haseps = false;
        $this->hasassertiontransitions = false;
    }

    /**
    * Returns, whether automaton really deterministic or not
    */
    public function is_deterministic() {
        return $this->deterministic;
    }
    /**
    * Returns whether this implementation support DFA or NFA
    */
    abstract public function should_be_deterministic();

    /**
    * Returns start state for automaton
    */
    public function start_state() {
        return $this->states[$this->startstate];
    }

    /**
    * Return an end state of the automaton
    *
    * TODO - determine, whether we could get automaton with several end states - then return array
    */
    public function end_state() {
        foreach($this->states as $state) {
            if ($state->is_end_state()) {
                return $state;
            }
        }
        return null;
    }

    /**
    * Set start state of automaton to be the state with given index
    */
    public function set_start_state($stateindex) {
        if (array_key_exists($stateindex, $this->states)) {
            $this->startstate = $stateindex;
        } else {
            throw new qtype_preg_exception('set_start_state error: No state '.$stateindex.' in automaton');
        }
    }

    public function has_epsilons() {
        return $this->haseps;
    }

    public function has_assertion_transitions() {
        return $this->hasassertiontransitions;
    }

    /**
    * Adds a state to the automaton and returns it's index
    *
    * @param state object of qtype_preg_fa_state class
    */
    public function add_state($state) {
        $state->FA =& $this;
        //TODO - add to $this->states and return given index
    }

    //TODO - does we really need function returning state for index? I'd prefer to avoid it. It could be useful only when construction automaton

    /**
    * Read and create FA from dot-like language
    *
    * Mainly used for unit-testing
    */
    public function read_fa($dotstring) {
        //TODO - kolesov
    }

    /**
    * Output dot-file for given FA
    *
    * Mainly used for debugging
    */
    public function write_fa_to_dot($file) {
        //TODO - kolesov
    }

    /**
    * Compares to FA and returns whether they are equal
    *
    * Mainly used for unit-testing
    * @param another qtype_preg_finite_automaton object - FA to compare
    * @return boolean true if this FA equal to $another
    */
    public function compare_fa($another) {
        //TODO - streltsov
    }

    /**
    * Merges simple assertion transitions into other transtions
    */
    public function merge_simple_assertions() {
        if (!$this->hasassertiontransitions) {//Nothing to merge
            return;
        }
        //TODO - merge
        $this->hasassertiontransitions = false;
    }

    /**
    * Deletes epsilon-transitions from automaton
    */
    public function aviod_eps() {
        if (!$this->haseps) {//Nothing to delete
            return;
        }
        //TODO - delete eps
        $this->haseps = false;
    }

    /**
    * Changes automaton to not contain wordbreak  simple assertions (\b and \B)
    */
    public function avoid_wordbreaks() {
    //TODO - delete \b and \B
    }

    /**
    * Intersect automaton with another one
    *
    * @param anotherfa object automaton to intersect
    * @param stateindex integer index of state of $this automaton with which to start intersection
    * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state
    */
    public function instersect_fa($anotherfa, $stateidnex, $isstart) {
        //TODO
    }

    /**
    * Return set substraction: $this - $anotherfa
    *
    * Used to get negation
    */
    abstract public function substract_fa($anotherfa);

    /**
    * Return inversion of fa
    */
    abstract public function invert_fa();

    abstract public function match($str, $pos);
    abstract public function next_character();//TODO - define arguments
    /**
    * Finds shortest possible string, completing partial given match
    */
    abstract public function complete_match();//TODO - define arguments
};