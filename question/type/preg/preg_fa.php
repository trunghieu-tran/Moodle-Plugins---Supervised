<?php
/**
 * Defines classes of finite automaton for regular expression matching, its states and transitions.
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
 * Represents finite automaton transitions (without subpatterns information).
 *
 * As NFA and DFA have different ways to store subpatterns information, they both should inherit this class to add necessary fields.
 */
class qtype_preg_fa_transition {

    /** @var object of qtype_preg_fa_state class - a state which transition starts from. */
    public $from;
    /** @var object of preg_leaf class - condition for this transition. */
    public $pregleaf;
    /** @var object of qtype_preg_fa_state class - state which transition leads to. */
    public $to;
    /** @var integer priority of this transitions over other - 0 means the highest priority. */
    public $priority;

    public function __clone() {
        $this->pregleaf = clone $this->pregleaf;    // When clonning a transition we also want a clone of its pregleaf.
    }

    public function __construct(&$from, &$pregleaf, &$to) {
        $this->from =& $from;
        $this->pregleaf = clone $pregleaf;
        $this->to =& $to;
    }
}

/**
* Class for finite automaton state.
*/
class qtype_preg_fa_state {

    /** @var object reference to the qtype_preg_finite_automaton object this state belongs to.
     *
     * We are violating principle "a child shouldn't know the parent" there, but the state need to signal important information back to
     * automaton during its construction: becoming non-deterministic, having eps or pure-assert transitions etc.
     */
    protected $FA;

    /** @var array of qtype_preg_fa_transition child objects, indexed. */
    protected $outtransitions;
    /** @var boolean whether state is deterministic, i.e. whether it has no characters with two or more possible outgoing transitions. */
    protected $deterministic;

    public function __construct(&$FA = null) {
        $this->FA =& $FA;
        $this->outtransitions = array();
        $this->deterministic = true;
    }

    public function set_FA(&$FA) {
        $this->FA =& $FA;
    }

    /**
     * Adds a transtition to the given state.
     *
     * @param transtion a reference to an object of child class of qtype_preg_fa_transition.
     */
    public function add_transition(&$transition, &$priority_counter) {
        $transition->from =& $this;
        $transition->priority = $priority_counter++;
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

    /**
     * Moves transitions from one state to another.
     *
     * @param with a reference to an object of qtype_preg_fa_state to take transitions from.
     */
    public function merge_transition_set(&$with) {
        $this->outtransitions = array_merge($this->outtransitions, $with->outtransitions);
    }

    /**
     * Replaces oldref with newref in each transition.
     *
     * @param oldref - a reference to the old state.
     * @param newref - a reference to the new state.
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach($this->outtransitions as $transition) {
            if ($transition->to === $oldref) {
                $transition->to =& $newref;
            }
        }
    }

    public function outgoing_transitions() {
        return $this->outtransitions;
    }

    /**
     * Returns an array of transitions possible with current string and position.
     */
    public function possible_transitions($str, $pos) {
        //TODO - use pregnode->match from transitions
    }

    /**
     * Returns true if this is accepting end state.
     *
     * End state doesn't have outgoing transitions.
     */
    /*public function is_end_state() {
        return empty($this->outtransitions);
    }*/
}


/**
 * Represents an abstract finite automaton. Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa.
 */
abstract class qtype_preg_finite_automaton {

    /** @var array of qtype_preg_fa_state, indexed by state numbers. */
    protected $states;
    /** @var object of qtype_preg_fa_state - start state. */
    protected $startstate;
    /** @var object of qtype_preg_fa_state - end state. */
    protected $endstate;

    /** @var boolean is automaton really deterministic - it can be even if it shoudn't.
     *
     * May be used for optimisation when an NFA object actually stores a DFA.
     */
    protected $deterministic;

    /** @var boolean whether automaton has epsilon-transtions. */
    protected $haseps;
    /** @var boolean whether automaton has simple assertion transtions. */
    protected $hasassertiontransitions;

    protected $statelimit;
    protected $statecount;

    protected $transitionlimit;
    protected $transitioncount;


    public function __construct() {
        $this->states = array();
        $this->startstate = null;
        $this->endstate = null;
        $this->deterministic = true;
        $this->haseps = false;
        $this->hasassertiontransitions = false;
        $this->statecount = 0;
        $this->transitioncount = 0;
        $this->set_limits();
    }

    /**
     * The function should set $this->statelimit and $this->transitionlimit properties using $CFG.
     *
     * DFA and NFA have different size limits in $CFG, so let them have separate implementation of this function.
     */
    abstract protected function set_limits();

    /**
     * Returns whether automaton is really deterministic.
     */
    public function is_deterministic() {
        return $this->deterministic;
    }

    /**
     * Used from qype_preg_fa_state class to signal that automaton become non-deterministic.
     *
     * Note that only methods of the automaton can make it deterministic and set this property to true.
     */
    public function make_nondeterministic() {
        $this->deterministic = false;
    }

    /**
     * Returns whether this implementation support DFA or NFA.
     */
    abstract public function should_be_deterministic();

    /**
     * Returns the start state for automaton.
     */
    public function start_state() {
        return $this->startstate;
    }

    /**
     * Return the end state of the automaton.
     *
     * TODO - determine, whether we could get automaton with several end states - then return array.
     */
    public function end_state() {
        return $this->endstate;
    }

    public function get_states() {
        return $this->states;
    }

    public function state_exists(&$state) {
        foreach ($this->states as $curstate) {
            if ($curstate === $state) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set the start state of the automaton to given state.
     */
    public function set_start_state(&$state) {
        if ($this->state_exists($state)) {
            $this->startstate =& $state;
        } else {
            throw new qtype_preg_exception('set_start_state error: No state '.$stateindex.' in automaton');
        }
    }

    /**
     * Set the end state of the automaton to given state.
     */
    public function set_end_state(&$state) {
        if ($this->state_exists($state)) {
            $this->endstate =& $state;
        } else {
            throw new qtype_preg_exception('set_end_state error: No state '.$stateindex.' in automaton');
        }
    }

    /**
     * Replaces oldref with newref in every transition of the automaton.
     *
     * @param oldref - a reference to the old state.
     * @param newref - a reference to the new state.
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach ($this->states as $curstate) {
            $curstate->update_state_references($oldref, $newref);
        }
    }

    public function has_epsilons() {
        return $this->haseps;
    }

    /**
     * Used from qype_preg_fa_state class to signal that a transition was added to the automaton.
     */
    public function transition_added() {
        $this->transitioncount++;
        if ($this->transitioncount > $this->transitionlimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
     * Used from qype_preg_fa_state class to signal that an epsilon-transition was added to the automaton.
     * Note that only methods of the automaton can delete all epsilon-transitions and make property false.
     */
    public function epsilon_transtion_added() {
        $this->haseps = true;
    }

    public function has_assertion_transitions() {
        return $this->hasassertiontransitions;
    }

    /**
     * Used from qype_preg_fa_state class to signal that an assert-transition was added to the automaton.
     * Note that only methods of the automaton the merge all assert-transitions and make property false.
     */
    public function assertion_transition_added() {
        $this->hasassertiontransitions = false;
    }

    /**
     * Adds a state to the automaton.
     *
     * @param state a reference to an object of qtype_preg_fa_state class.
     */
    public function add_state(&$state) {
        $this->states[] =& $state;
        $state->set_FA(&$this);
        $this->statecount++;
        if ($this->statecount > $this->statelimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
     * Removes a state from the automaton.
     *
     * @param state a reference to the state to be removed.
     */
    public function remove_state(&$state) {
        foreach ($this->states as $key=>$curstate) {
            if ($curstate === $state) {
                $this->transitioncount -= count($curstate->outgoing_transitions());
                $this->statecount--;
                unset($this->states[$key]);
                break;
            }
        }
    }

    /**
     * Read and create a FA from dot-like language. Mainly used for unit-testing.
     */
    public function read_fa($dotstring) {
        //TODO - kolesov
    }

    /**
     * Numerates FA states starting from 0 and trying to go from left to right (in a wawe).
     * Useful mainly for outputting and cloning FA.
     *
     * @return array where states are values and states number - keys.
     */
    public function numerate_states() {
        //TODO
    }

    /**
     * Creates a dot-file for the given FA. Mainly used for debugging.
     */
    public function write_fa_to_dot($file) {
        //TODO - kolesov
    }

    /**
     * Compares to FA and returns whether they are equal. Mainly used for unit-testing.
     *
     * @param another qtype_preg_finite_automaton object - FA to compare.
     * @return boolean true if this FA equal to $another.
     */
    public function compare_fa($another) {
        //TODO - streltsov
    }

    /**
     * Merges simple assertion transitions into other transtions.
     */
    public function merge_simple_assertions() {
        if (!$this->hasassertiontransitions) {    //Nothing to merge
            return;
        }
        //TODO - merge
        $this->hasassertiontransitions = false;
    }

    /**
     * Deletes epsilon-transitions from the automaton.
     */
    public function aviod_eps() {
        if (!$this->haseps) {    //Nothing to delete.
            return;
        }
        //TODO - delete eps
        $this->haseps = false;
    }

    /**
     * Changes automaton to not contain wordbreak  simple assertions (\b and \B).
     */
    public function avoid_wordbreaks() {
    //TODO - delete \b and \B
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex integer index of state of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     */
    public function instersect_fa($anotherfa, $stateidnex, $isstart) {
        //TODO
    }

    /**
     * Return set substraction: $this - $anotherfa. Used to get negation.
     */
    abstract public function substract_fa($anotherfa);//TODO - functions that could be implemented only for DFA should be moved to DFA class

    /**
     * Return inversion of fa.
     */
    abstract public function invert_fa();

    abstract public function match($str, $pos);
    abstract public function next_character();//TODO - define arguments

    /**
     * Finds shortest possible string, completing partial given match.
     */
    abstract public function complete_match();//TODO - define arguments

    public function __clone() {
        //TODO - clone automaton
    }

    /**
     * Generates dot code for drawing FA.
     *
     * @param dotfilename - name of the dot file.
     * @param jpgfilename - name of the resulting jpg file.
     */
    public function draw($dotfilename, $jpgfilename) {
        $regexhandler = new qtype_preg_regex_handler();
        $dir = $regexhandler->get_temp_dir('nfa');
        $dotfn = $dir.$dotfilename;
        $dotfile = fopen($dotfn, 'w');
        // numerate all states
        $tmp = 0;
        foreach ($this->states as $curstate)
        {
            $curstate->id = $tmp;
            $tmp++;
        }
        // generate dot code
        fprintf($dotfile, "digraph {\n");
        fprintf($dotfile, "rankdir = LR;\n");
        foreach ($this->states as $curstate) {
            $index1 = $curstate->id;
            // draw a single state
            if (count($curstate->outgoing_transitions()) == 0) {
                fprintf($dotfile, "%s\n", "$index1");
            } else {    // draw a state with transitions
                foreach ($curstate->outgoing_transitions() as $curtransition) {
                    $index2 = $curtransition->to->id;
                    $lab = $curtransition->pregleaf->tohr().',';
                    // information about subpatterns
                    $subpatt_start = array();
                    $subpatt_end = array();
                    foreach ($curtransition->tags as $value) {
                        if ($value % 2 == 0) {
                            $subpatt_start[] = $value / 2;
                        } else {
                            $subpatt_end[] = ($value - 1) / 2;
                        }
                    }
                    if (count($subpatt_start) > 0) {
                        $lab = $lab."starts";
                        foreach ($subpatt_start as $num) {
                            $lab = $lab."$num,";
                        }
                    }
                    if (count($subpatt_end) > 0) {
                        $lab = $lab."ends";
                        foreach ($subpatt_end as $num) {
                            $lab = $lab."$num,";
                        }
                    }
                    $lab = $lab."priority=$curtransition->priority";
                    fprintf($dotfile, "%s\n", "$index1->$index2"."[label=\"$lab\"];");
                }
            }
        }
        fprintf($dotfile, "};");
        fclose($dotfile);
        $regexhandler->execute_dot($dotfn, $jpgfilename);
        //unlink($dotfn);
    }
	
	
	/**
	*function read fa from special code and modif current object
	*code format: i->abc->j;k->charset->l; e.t.c. надеюсь не перепутал английское сокращение
	*maximum count of subpatterns in reading fa is 9 in current implementation
	*@param facode string with code of finite automata
	*/
	public function input_fa($facode) {
		$this->read_code_member($facode);
		$this->set_start_state($this->states[0]);
		$this->set_end_state($this->states[$this->statecount-1]);
	}

	/**
	*function read one code member
	*@param facode string with code of finite automata
	*@param start index of first character of current member in facode
	*@param counter priority of transition
	*/
	protected function read_code_member($facode, $start=0, &$counter=0) {
		if ($start >= strlen($facode)) {
			return;
		}
		$end=$start;
		$tmpstr='';
		while ($facode[$end]!='-') {
			$tmpstr .= $facode[$end];
			$end++;
		}
		$end+=2;
		$fir = (int)$tmpstr;
		$tmpstr = '';
		$transition = self::read_transition($facode, $end);
		$end++;
		while($facode[$end-2]!='-' || $facode[$end-1]!='>') {
			$end++;
		}
		while ($facode[$end]!=';') {
			$tmpstr .= $facode[$end];
			$end++;
		}
		$lst = (int)$tmpstr;
		if (!isset($this->states[$fir])) {
			$this->states[$fir] = new qtype_preg_fa_state();
			$this->states[$fir]->set_FA(&$this);
			$this->statecount++;
			if ($this->statecount > $this->statelimit) {
				throw new qtype_preg_toolargefa_exception('');
			}
		}
		if (!isset($this->states[$lst])) {
			$this->states[$lst] = new qtype_preg_fa_state();
			$this->states[$lst]->set_FA(&$this);
			$this->statecount++;
			if ($this->statecount > $this->statelimit) {
				throw new qtype_preg_toolargefa_exception('');
			}
		}
		$transition->to =& $this->states[$lst];
		$end++;
		$this->states[$fir]->add_transition($transition, $counter);
		$this->read_code_member($facode, $end, $counter);
	}
	
	/**
	*function read one leaf of regex from code of finite automata
	*@param facode string with code of finite automata
	*@param start index of first character of current leaf in facode
	*/
	static protected function read_transition($facode, $start) {
		$i = $start;
		$subpattstarts = array();
		$subpattends = array();
		$charset = '';
		$error = false;
		//input subpatterns
		if ($facode[$start]=='#') {
			$i = $start+1;
			do {
				if ($i>=strlen($facode)) {
					$error = true;
					echo "<BR><BR><BR>Incorrect fa code!<BR><BR><BR>";
					//TODO: error message
				} else if ($facode[$i]=='s') {
					$subpattstarts[] = (int)$facode[$i+1];
				} else if ($facode[$i]=='e') {
					$subpattends[] = (int)$facode[$i+1];
				} else {
					$error = true;
					echo "<BR><BR><BR>Incorrect fa code!<BR><BR><BR>";
					//TODO: error message
				}
				$i+=2;
			} while (!$error && $i<strlen($facode) && $facode[$i]!='#');
			$i++;
		}
		if ($error || $i>=strlen($facode)) {
			return;
		}
		//input transition leaf
		while ($facode[$i]!='-' || $facode[$i+1]!='>') {
			if ($facode[$i]=='\\') {
				$charset .= $facode[$i+1];
				$i+=2;
			} else {
				$charset .= $facode[$i];
				$i++;
			}
		}
		$leaf = new preg_leaf_charset();
		$leaf->charset = $charset;
		//TODO: input for dfa
		$trash =  new qtype_preg_fa_state();
		$transition = new qtype_preg_nfa_transition($trash, $leaf, $trash);
		$transition->tags = array();
		foreach ($subpattstarts as $val) {
			$transition->tags[] = $val*2;
		}
		foreach ($subpattends as $val) {
			$transition->tags[] = $val*2+1;
		}
		return $transition;
	}
};