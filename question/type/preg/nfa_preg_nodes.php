<?php

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * defines a transition between two states
 */
class nfa_transition
{
	public $loops = false;	// true if this transition makes a loop: for example, (...)* contains an epsilon-transition that makes a loop

	public $pregleaf;		// transition data, a reference to an object of preg_leaf

	public $state;			// the state which this transition leads to, a reference to an object of nfa_state

	public function __construct(&$_pregleaf, &$_state, $_loops) {
		$this->pregleaf = $_pregleaf;
		$this->state = $_state;
		$this->loops = $_loops;
	}

}

/**
 * defines an nfa state
 */
class nfa_state
{

	public $startsinfinitequant = false;	// true if this state starts an infinite quantifier either * or + or {m,}

	public $next = array();					// an array of objects of nfa_transition

	public $subpattstart = 0;				// number of the subpattern which starts in this state

	public $subpattend = 0;					// number of the subpattern which ends in this state

	public $id;								// id of the state, debug variable

	/**
	 * appends a next possible state
	 * @param next - a reference to the transition to be appended
	 */
	public function append_transition(&$next) {
		$exists = false;
		$size = count($this->next);
		// not unique transitions are not appended
		foreach($this->next as $curnext) {
			if ($curnext->pregleaf == $next->pregleaf && $curnext->state === $next->state) {
				$exists = true;
			}
		}
		if (!$exists) {
			array_push($this->next, $next);
		}
		return !$exists;
	}

	/**
	 * replaces oldref with newref in every transition
	 * @param oldref - a reference to the old state
	 * @param newref - a reference to the new state
	 */
	public function update_state_references(&$oldref, &$newref) {
		foreach($this->next as $curnext)
			if ($curnext->state == $oldref) {
				$curnext->state = $newref;
			}
	}

	/**
	 * merges two states
	 * @param with - a reference to state the to be merged with
	 */
	public function merge(&$with) {
		// move all transitions from $with to $this state
		foreach($with->next as $curnext) {
			$this->append_transition($curnext);
		}
		// unite fields by logical "or"
		if ($with->startsinfinitequant) {
			$this->startsinfinitequant = true;
		}
		if ($with->subpattstart) {
			$this->subpattstart = $with->subpattstart;
		}
		if ($with->subpattend) {
			$this->subpattend = $with->subpattend;
		}
	}

	/**
	 * debug function
	 */
	public function is_equal(&$to) {
		return $this->next == $to->next;		//this is quite enough
	}

}

/**
 * defines a state of an automaton when running
 * used when matching a string
 */
class processing_state {

	public $state;						// a reference to the state which automaton is in

	public $matchcnt;					// the number of characters matched

	public $isfullmatch;				// whether the match is full

	public $nextpossible;				// the next possible character

	public $assertions = array();		// an array containing last assertions matched. this field is used when generating a next possible character

	public $subpattern_indexes_first = array();	// key = subpattern number

	public $subpattern_indexes_last = array();	// key = subpattern number

	public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_nextpossible, $_assertions, $_subpattern_indexes_first, $_subpattern_indexes_last) {
		$this->state = $_state;
		$this->matchcnt = $_matchcnt;
		$this->isfullmatch = $_isfullmatch;
		$this->nextpossible = $_nextpossible;
		$this->assertions = $_assertions;
		$this->subpattern_indexes_first = $_subpattern_indexes_first;
		$this->subpattern_indexes_last = $_subpattern_indexes_last;
	}
}

/*!
 * contains information about a path to the matching state
 * used when generating a next possible character
 */
class path {

	public $state;				// a reference to the last state of the path, used when constucting $path

	public $path = array();		// an array containing transitions (objects of preg_leaf) to the matching state

	public $length;				// length of the path in characters

	public function __construct(&$_state, $_path, $_length) {
		$this->state = $_state;
		$this->path = $_path;
		$this->length = $_length;
	}
}

/**
 * defines a nondeterministic finite automaton
 */
class nfa {

	public $startstate;			// a reference to the start nfa_state of the automaton

	public $endstate;			// a reference to the end nfa_state of the automaton

	public $states = array();	// an array containing references to states of the automaton

	var $graphvizpath = 'C:\Program Files (x86)\Graphviz2.26.3\bin';	// path to dot.exe of graphviz

	/**
	 * merges assertions with next non-assertion transition
	 * @param path - a reference to the vector containing transitions to be merged
	 */
	private function merge_transitions(&$path) {	// TODO

	}

	/**
	 * generates a next possible character by a given path
	 * @param lastchar - the last character matched
	 * @param path - a reference to the path with merged assertions
	 * @param pathindex - index of the current transition
	 * @return - a character corresponding to the given path
	 */
	private function generate_character($lastchar, &$path, $pathindex) {	// TODO

	}

	/**
	 * finds the shortest path to the matching state
	 * @param lastchar - the last character matched
	 * @param state - the last successful state
	 * @return - the shortest path to complete match
	 */
	private function wave_to_the_end($lastchar, &$state) {	// TODO

	}

	/**
	 * appends the state to the automaton
	 * @param state - a regerence to the state to be appended
	 */
	public function append_state(&$state) {
		array_push($this->states, $state);
	}

	/**
	 * removes the state from the automaton
	 * @param state - a reference to the state to be removed
	 */
	public function remove_state(&$state) {
		foreach ($this->states as $key=>$curstate) {
			if ($curstate == $state) {
				unset($this->states[$key]);
			}
		}
	}

	/**
	 * moves states from the automaton referred to by $from to this automaton
	 * @param from - a reference to the automaton containing states to be moved
	 */
	public function move_states(&$from) {
		// iterate until all states are moved
		foreach ($from->states as $curstate) {
			array_push($this->states, $curstate);
		}
		// clear the source
		$from->states = array();
	}

	/**
	 * replaces oldref with newref in every transition of the automaton
	 * @param oldref - a reference to the old state
	 * @param newref - a reference to the new state
	 */
	public function update_state_references(&$oldref, &$newref) {
		foreach ($this->states as $curstate) {
			$curstate->update_state_references($oldref, $newref);
		}
	}

	/**
	 * checks if new result is better than old result
	 * @param oldres - old result, an object of processing_state
	 * @param newres - new result, an object of processing_state
	 * @return - true if new result is more suitable
	 */
	public function is_new_result_more_suitable(&$oldres, &$newres) {
		if	(($oldres->state != $this->endstate && $newres->matchcnt >= $oldres->matchcnt) ||										// new match is longer
			($newres->state == $this->endstate && $oldres->state != $this->endstate) ||												// new match is full
			($newres->state == $this->endstate && $oldres->state == $this->endstate && $newres->matchcnt >= $oldres->matchcnt)) {	// new match is full and longer
			return true;
		} else {
			return false;
		}
	}

	/**
	 * returns the longest match using a string as input. matching is proceeded from a given start position
	 * @param str - the original input string
	 * @param startpos - index of the start position to match
	 * @param cs - is matching case sensitive
	 * @return - the longest character sequence matched
	 */
	public function match($str, $startpos) {
		$curstates = array();	// states which the automaton is in
		$skipstates = array();	// contains states where infinite quantifiers start. it's used to protect from loops like ()*

		$result = new processing_state($this->startstate, 0, false, 0, array(), array(), array());

		array_push($curstates, $result);
		while (count($curstates) != 0) {
			$newstates = array();
			// we'll replace curstates with newstates by the end of this cycle
			while (count($curstates) != 0) {
				// get the current state
				$currentstate = array_pop($curstates);
				// kill epsilon-cycles
				$skip = false;
				if ($currentstate->state->startsinfinitequant) {
					// skipstates is sorted by matchcnt because transitions add characters
					for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $currentstate->matchcnt <= $skipstates[$i]->matchcnt; $i--)
						if ($skipstates[$i]->state === $currentstate->state && $skipstates[$i]->matchcnt == $currentstate->matchcnt && $skipstates[$i]->subpattern_indexes_last == $currentstate->subpattern_indexes_last) {
							$skip = true;
						}
					if (!$skip) {
						array_push($skipstates, $currentstate);
					}
				}

				// save subpattern indexes
				if (!$skip && $currentstate->state->subpattstart && !array_key_exists($currentstate->state->subpattstart, $currentstate->subpattern_indexes_first)) {
					$currentstate->subpattern_indexes_first[$currentstate->state->subpattstart] = $startpos + $currentstate->matchcnt;
					$currentstate->subpattern_indexes_last[$currentstate->state->subpattstart] = -1;
				}
				if (!$skip && $currentstate->state->subpattend && array_key_exists($currentstate->state->subpattend, $currentstate->subpattern_indexes_last) && $currentstate->subpattern_indexes_last[$currentstate->state->subpattend] == -1) {
					$currentstate->subpattern_indexes_last[$currentstate->state->subpattend] = $startpos + $currentstate->matchcnt - 1;
				}

				// iterate over all transitions
				for ($i = 0; !$skip && $i < count($currentstate->state->next); $i++) {
					$pos = $currentstate->matchcnt;
					$length = 0;
					$next = $currentstate->state->next[$i];
					if ($next->pregleaf->match($str, $startpos + $pos, &$length, !$next->pregleaf->caseinsensitive )) {
						$newstate = new processing_state($next->state, $pos + $length, false, 0, $currentstate->assertions, $currentstate->subpattern_indexes_first, $currentstate->subpattern_indexes_last);
						// clear newstate->assertions if a character matched
						if ($length > 0) {
							$newstate->assertions = array();
						} elseif (!(is_a($next->pregleaf, 'preg_leaf_meta') && $next->pregleaf->subtype == preg_leaf_meta::SUBTYPE_EMPTY)) {
							array_push($newstate->assertions, $next->pregleaf);
						}
						// save the state
						array_push($newstates, $newstate);
						// save the next state as a result if it's a matching state
						if ($next->state == $this->endstate && $this->is_new_result_more_suitable(&$result, &$newstate)) {
							$result = $newstate;
						}
					} elseif ($this->is_new_result_more_suitable(&$result, &$currentstate)) {
							$result = $currentstate;
					}
				}
			}

			// replace curstates with newstates
			for ($i = 0; $i < count($newstates); $i++) {
				array_push($curstates, $newstates[$i]);
			}
			$newstates = array();
		}
		$result->isfullmatch = ($result->state == $this->endstate);
		if ($result->matchcnt > 0) {
			$result->subpattern_indexes_first[0] = $startpos;
			$result->subpattern_indexes_last[0] = $startpos + $result->matchcnt - 1;
		} else {
			$result->subpattern_indexes_first[0] = -1;
			$result->subpattern_indexes_last[0] = -1;
		}
		if (!$result->isfullmatch) {
			// TODO character generation
		}
		/*foreach ($result->subpattern_indexes_first as $id=>$sp) {
			echo "id=".$id."index1=".$sp."index2=".$result->subpattern_indexes_last[$id]."<br />";
		}
		echo $result->matchcnt."<br />";*/
		return $result;

	}

	/**
	* debug function for generating dot code for drawing nfa
	* @param dotfilename - name of the dot file
	* @param jpgfilename - name of the resulting jpg file
	*/
	public function draw_nfa($dotfilename, $jpgfilename) {
		$dotfile = fopen($dotfilename, 'w');
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
			if (count($curstate->next) == 0) {
				fprintf($dotfile, "%s\n", "$index1");
			}
			// draw a state with transitions
			else
				foreach ($curstate->next as $curtransition) {
					$index2 = $curtransition->state->id;
					$lab = $curtransition->pregleaf->tohr();
					fprintf($dotfile, "%s\n", "$index1->$index2"."[label=\"$lab\"];");
				}
		}
		fprintf($dotfile, "};");
		chdir($this->graphvizpath);
		exec("dot.exe -Tjpg -o\"$jpgfilename\" -Kdot $dotfilename");
		echo "<IMG src=\"$jpgfilename\" width=\"90%\">";
		fclose($dotfile);
	}

}

/**
* abstract class for both operators and leafs
*/
abstract class nfa_preg_node {

	public $pregnode;	// a reference to the corresponding preg_node

	/**
	* returns true if engine support the node, false otherwise
	* when returning false should also set rejectmsg field
	*/
	public function accept() {
		return true; // accepting anything by default
	}

	/**
	 * creates an automaton corresponding to this node
	 * @param stackofautomatons - a stack which operators pop automatons off and operands push automatons onto
	 * @param issubpattern - true if epsilon transitions are needed at the beginning and at the end of the automaton
	 */
	abstract public function create_automaton(&$stackofautomatons, $issubpattern);

	public function __construct(&$node, &$matcher) {
		$this->pregnode = $node;
	}

}


/**
* class for nfa transitions
*/
class nfa_preg_leaf extends nfa_preg_node {

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		// create start and end states of the resulting automaton
		$start = new nfa_state;
		$end = new nfa_state;
		$start->append_transition(new nfa_transition($this->pregnode, $end, false));
		$res = new nfa;
		$res->append_state($start);
		$res->append_state($end);
		$res->startstate = $start;
		$res->endstate = $end;
		array_push($stackofautomatons, $res);
	}

}

/**
* abstract class for nfa operators
*/
abstract class nfa_preg_operator extends nfa_preg_node {

	public $operands = array();	// an array of operands
	
	public function __construct($node, &$matcher) {
		parent::__construct($node, $matcher);
		foreach ($this->pregnode->operands as &$operand) {
			array_push($this->operands, $matcher->from_preg_node($operand));
		}
	}

}

/**
* defines concatenation
*/
class nfa_preg_node_concat extends nfa_preg_operator {

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		// first, operands create their automatons
		$this->operands[0]->create_automaton(&$stackofautomatons, $issubpattern);
		$this->operands[1]->create_automaton(&$stackofautomatons, $issubpattern);
		// take automata and concatenate them
		$second = array_pop($stackofautomatons);
		$first = array_pop($stackofautomatons);
		// update references because of merging states
		$second->update_state_references($second->startstate, $first->endstate);
		// merge and move states
		$first->endstate->merge($second->startstate);
		$second->remove_state($second->startstate);
		$first->endstate = $second->endstate;
		$first->move_states($second);
		array_push($stackofautomatons, $first);
	}

}

/**
* defines alternation
*/
class nfa_preg_node_alt extends nfa_preg_operator {

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		// first, operands create their automatons
		$this->operands[0]->create_automaton(&$stackofautomatons, $issubpattern);
		$this->operands[1]->create_automaton(&$stackofautomatons, $issubpattern);
		// take automata and alternate them
		$second = array_pop($stackofautomatons);
		$first = array_pop($stackofautomatons);
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		// add a new end state if the end state of the first automaton is looped or if it is the end of a subpattern
		$endlooped = false;
		foreach ($first->endstate->next as $curnext) {
			if ($curnext->loops) {
				$endlooped = true;
			}
		}
		if ($endlooped || $first->endstate->subpattend != 0) {
			$endstate = new nfa_state;
			$first->append_state($endstate);
			$first->endstate->append_transition(new nfa_transition($epsleaf, $endstate, false));
			$first->endstate = $endstate;
		}
		// start states are merged, end states are alternated by an epsilon-transition for correct subpattern and loop capturing
		$second->update_state_references($second->startstate, $first->startstate);
		$first->startstate->merge($second->startstate);
		$second->remove_state($second->startstate);
		$second->endstate->append_transition(new nfa_transition($epsleaf, $first->endstate, false));
		$first->move_states($second);
		array_push($stackofautomatons, $first);
	}

}

/**
* defines infinite quantifiers * + {m,}
*/
class nfa_preg_node_infinite_quant extends nfa_preg_operator {

	/**
	 * creates an automaton for * or {0,} quantifier
	 */
	private function create_aster(&$stackofautomatons, $issubpattern) {
		$this->operands[0]->create_automaton(&$stackofautomatons, $issubpattern);
		$body = array_pop($stackofautomatons);
		$from = $body->startstate;
		if ($body->endstate->subpattend != 0) {
			$from = $body->startstate->next[0]->state;
		}
		foreach ($from->next as $curnext) {
			$body->endstate->append_transition(new nfa_transition($curnext->pregleaf, $curnext->state, true));
			$curnext->state->startsinfinitequant = true;
		}
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false));
		array_push($stackofautomatons, $body);
	}

	/**
	 * creates an automaton for {m,} quantifier
	 */
	private function create_brace(&$stackofautomatons, $issubpattern) {
		// create an automaton for body ($leftborder + 1) times
		$leftborder = $this->pregnode->leftborder;
		for ($i = 0; $i < $leftborder + 1; $i++) {
			$this->operands[0]->create_automaton(&$stackofautomatons, ($i == $leftborder));
		}
		$res = null;	// the resulting automaton
		// linking automatons to the resulting one
		for ($i = 0; $i < $leftborder + 1; $i++) {
			$cur = array_pop($stackofautomatons);
			if ($i > 0) {
				// the last block is repeated
				if ($i == $leftborder) {
					foreach ($cur->startstate->next as $curnext) {
						$cur->endstate->append_transition(new nfa_transition($curnext->pregleaf, $curnext->state, true));
						$curnext->state->startsinfinitequant = true;
					}
					$epsleaf = new preg_leaf_meta;
					$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
					$cur->startstate->append_transition(new nfa_transition($epsleaf, $cur->endstate, false));
				}
				// merging
				$cur->update_state_references($cur->startstate, $res->endstate);
				$res->endstate->merge($cur->startstate);
				$cur->remove_state($cur->startstate);
				$res->move_states($cur);
				$res->endstate = $cur->endstate;
			} else {
				$res = $cur;
			}
		}
		array_push($stackofautomatons, $res);
	}

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		if ($this->pregnode->leftborder == 0) {
			$this->create_aster(&$stackofautomatons, $issubpattern);
		} else {
			$this->create_brace(&$stackofautomatons, $issubpattern);
		}
	}

}

/**
* defines finite quantifiers {m, n}
*/
class nfa_preg_node_finite_quant extends nfa_preg_operator {

	/**
	 * creates an automaton for ? quantifier
	 */
	private function create_qu(&$stackofautomatons, $issubpattern) {
		$this->operands[0]->create_automaton(&$stackofautomatons, $issubpattern);
		$body = array_pop($stackofautomatons);
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false));
		array_push($stackofautomatons, $body);
	}

	/**
	 * creates an automaton for {m, n} quantifier
	 */
	private function create_brace(&$stackofautomatons, $issubpattern) {
		// create an automaton for body ($leftborder + 1) times
		$leftborder = $this->pregnode->leftborder;
		$rightborder = $this->pregnode->rightborder;
		for ($i = 0; $i < $rightborder; $i++) {
			$this->operands[0]->create_automaton(&$stackofautomatons, ($i == $rightborder - 1));
		}
		$res = null;		// the resulting automaton
		$endstate = null;	// the end state, required if $leftborder != $rightborder
		if ($leftborder != $rightborder) {
			$endstate = new nfa_state;
		}
		// linking automatons to the resulting one
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		for ($i = 0; $i < $rightborder; $i++) {
			$cur = array_pop($stackofautomatons);
			if ($i >= $leftborder && $leftborder != $rightborder) {
				$cur->startstate->append_transition(new nfa_transition($epsleaf, $endstate, false));
			}
			if ($i > 0) {
				$cur->update_state_references($cur->startstate, $res->endstate);
				$res->endstate->merge($cur->startstate);
				$cur->remove_state($cur->startstate);
				$res->move_states($cur);
				$res->endstate = $cur->endstate;
			} else {
				$res = $cur;
			}
		}
		if ($leftborder != $rightborder) {
			$res->update_state_references($endstate, $res->endstate);
		}
		array_push($stackofautomatons, $res);
	}

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
			$this->create_qu(&$stackofautomatons, $issubpattern);
		} else {
			$this->create_brace(&$stackofautomatons, $issubpattern);
		}
	}

}

/**
* defines subpatterns
*/
class nfa_preg_node_subpatt extends nfa_preg_operator {

	public function create_automaton(&$stackofautomatons, $issubpattern) {
		$this->operands[0]->create_automaton(&$stackofautomatons, $issubpattern);
		if ($issubpattern) {
			$body = array_pop($stackofautomatons);
			$startstate = new nfa_state;
			$endstate = new nfa_state;
			$body->startstate->subpattstart = $this->pregnode->number;
			$endstate->subpattend = $this->pregnode->number;
			array_push($body->states, $startstate);
			array_push($body->states, $endstate);
			$epsleaf = new preg_leaf_meta;
			$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
			$startstate->append_transition(new nfa_transition($epsleaf, $body->startstate, false));
			$body->endstate->append_transition(new nfa_transition($epsleaf, $endstate, false));
			$body->startstate = $startstate;
			$body->endstate = $endstate;
			array_push($stackofautomatons, $body);
		}
	}

}

?>