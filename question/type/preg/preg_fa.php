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
* Class for finite automaton transition (without subpatterns information)
*
* As NFA and DFA have different ways to store subpatterns information, they should both subclass this class to add necessary fields
*/
class qtype_preg_fa_transition {

    /** @var object of qtype_preg_fa_state class - state from which transition starts*/
    public $from;
    /** @var object  of preg_leaf class - condition for this transition*/
    public $pregleaf;
    /** @var object of qtype_preg_fa_state class - state to which transition leads*/
    public $to;
    /** @var integer priority of this transitions over other - 0 means the highest priority*/
    public $priority;
};

/**
* Class for finite automaton state
*/
class qtype_preg_fa_state {

    /** @var object reference to the qtype_preg_finite_automaton object this state belongs to
    *
    * We are violating principle "child shoudn't know the parent" there, but the state need to signal important information back to automaton during it's construction: becoming non-deterministic, having eps or pure-assert transitions etc
    */
    protected $FA;

    /** @var array of qtype_preg_fa_transition child objects, indexed*/
    protected $outtransitions;
    /** @var whether state is deterministic, i.e. whether it has no characters with two or more possible outgoing transitions*/
    protected $deterministic;

    public function __construct() {
        $this->FA = null;
        $this->outtransitions = array();
        $this->deterministic = true;
    }

    public function set_FA($FA) {
        $this->FA = &$FA;
    }

    /**
    * Adds a transtition to the state
    *
    * @param transtion object of child class of qtype_preg_fa_transition
    */
    public function add_transition($transition) {
        $transition->from =& $this;
        $this->outtransitions[] = $transition;
        //TODO - check whether it makes a node non-deterministic
        //TODO - signal automaton if a node become non-deterministic, see make_nondeterministic function in automaton class

        if ($transition->pregleaf->subtype === preg_leaf_meta::SUBTYPE_EMPTY) {
            $this->FA->epsilon_transtion_added();
        }

        if ($transition->pregleaf->type === preg_node::TYPE_LEAF_ASSERT) {
            $this->FA->assertion_transition_added();
        }

        $this->FA->transition_added();
    }

    public function outgoing_transitions() {
        return $this->outtransitions;
    }

    /**
    * Returns an array of transitions which is possible with current string and position.
    */
    public function possible_transitions($str, $pos) {
        //TODO - use pregnode->match from transitions
    }

    /**
    * Returns true if this is accepting end state
    *
    * End state doesn't have outgoing transitions
    */
    public function is_end_state() {
        return empty($this->outtransitions);
    }
};


/**
* Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa
*/
abstract class qtype_preg_finite_automaton {

    /** @var array of qtype_preg_fa_state, indexed by state number */
    protected $states;
    /** @var object of start state*/
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

    
    protected $statelimit;
    protected $statecount;

    protected $transitionlimit;
    protected $transitioncount;
    

    public function __contruct() {
        $this->states = array();
        $this->startstate = 0;
        $this->deterministic = true;
        $this->haseps = false;
        $this->hasassertiontransitions = false;
        $this->statecount = 0;
        $this->transitioncount = 0;
        $this->set_limits();
    }

    /**
    * For now, DFA and NFA have different size limits in $CFG, so let them have separate implementation of this function
    *
    * The function should set $this->statelimit and $this->transitionlimit properties using $CFG
    */
    abstract protected function set_limits();

    /**
    * Returns, whether automaton really deterministic or not
    */
    public function is_deterministic() {
        return $this->deterministic;
    }

    /**
    * Used from qype_preg_fa_state class to signal that automaton become non-deterministic
    *
    * Note that only methods of automaton could make it deterministic and set property to true
    */
    public function make_nondeterministic() {
        $this->deterministic = false;
    }

    /**
    * Returns whether this implementation support DFA or NFA
    */
    abstract public function should_be_deterministic();

    /**
    * Returns start state for automaton
    */
    public function start_state() {
        return $this->startstate;
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
    public function set_start_state($state) {
        if (in_array($state, $this->states)) {
            $this->startstate =& $state;
        } else {
            throw new qtype_preg_exception('set_start_state error: No state '.$stateindex.' in automaton');
        }
    }

    public function has_epsilons() {
        return $this->haseps;
    }

    /**
    * Used from qype_preg_fa_state class to signal that epsilon-transition was added to the automaton
    *
    * Note that only methods of automaton could delete all epsilon-transitions and make property false
    */
    public function epsilon_transtion_added() {
        $this->haseps = true;
    }

    public function has_assertion_transitions() {
        return $this->hasassertiontransitions;
    }

    /**
    * Used from qype_preg_fa_state class to signal that assert-transition was added to the automaton
    *
    * Note that only methods of automaton could merge all assert-transitions and make property false
    */
    public function assertion_transition_added() {
        $this->hasassertiontransitions = false;
    }

    /**
    * Adds a state to the automaton and returns it's index
    *
    * @param state object of qtype_preg_fa_state class
    */
    public function add_state($state) {
        
        $this->states[] =& $state;
        $state->set_FA(& $this);
        $this->statecount++;
        if ($this->statecount > $this->statelimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    public function transition_added() {
        $this->transitioncount++;
        if ($this->transitioncount > $this->transitionlimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
    * Read and create FA from dot-like language
    *
    * Mainly used for unit-testing
    */
    public function read_fa($dotstring) {
        //TODO - kolesov
    }

    /**
    * Numerates FA states starting from 0 and trying to go from left to right (in a wawe)
    *
    * Useful mainly for outputting and cloning FA.
    * @return array where states are values and states number - keys
    */
    public function numerate_states() {
        //TODO
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
    abstract public function substract_fa($anotherfa);//TODO - functions that could be implemented only for DFA should be moved to DFA class

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

    public function __clone() {
        //TODO - clone automaton
    }


};