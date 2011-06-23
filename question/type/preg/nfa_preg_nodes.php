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

	public function __construct(&$_pregleaf, &$_state) {
		$this->pregleaf = $_pregleaf;
		$this->state = $_state;
	}
	
}

/**
 * defines an nfa state
 */
class nfa_state
{

	public $starts_infinite_quant = false;	// true if this state starts an infinite quantifier either * or + or {m,}
	
	public $next = array();					// an array of objects of nfa_transition
	
	public $id;								// id of the state, debug variable

	/**
	 * appends a next possible state
	 * @param next - a reference to the transition to be appended
	 */
	public function append_transition(&$next) {
		$exists = false;
		$size = count($this->next);
		// not unique transitions are not appended
		foreach($this->next as $cur_next)
			if ($cur_next->pregleaf == $next->pregleaf && $cur_next->state === $next->state)
				$exists = true;
		if (!$exists)
			array_push($this->next, $next);
		return !$exists;
	}
	
	/**
	 * replaces oldref with newref in every transition
	 * @param oldref - a reference to the old state
	 * @param newref - a reference to the new state
	 */
	public function update_state_references(&$oldref, &$newref) {
		foreach($this->next as $cur_next)
			if ($cur_next->state == $oldref)
				$cur_next->state = $newref;
	}

	/**
	 * merges two states
	 * @param with - a reference to state the to be merged with
	 */
	public function merge(&$with) {
		// move all transitions from $with to $this state
		foreach($with->next as $cur_next)
			$this->append_transition($cur_next);
		$with->next = array();
		// unite starts_infinite_quant by logical "or"
		if ($with->starts_infinite_quant)
			$this->starts_infinite_quant = true;
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

	public $state;					// a reference to the state which automaton is in
	
	public $matchcnt;				// the number of characters matched
	
	public $isfullmatch;			// whether the match is full
		
	public $nextpossible;			// the next possible character
	
	public $assertions = array();	// an array containing last assertions matched. this field is used when generating a next possible character

	public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_nextpossible, $_assertions) {
		$this->state = $_state;
		$this->matchcnt = $_matchcnt;
		$this->isfullmatch = $_isfullmatch;
		$this->nextpossible = $_nextpossible;
		$this->assertions = $_assertions;
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
 * contains information about a subpattern
 */
class subpattern_states {

	public $node;			// a reference to the corresponding object of nfa_preg_node
	
	public $startstate;		// a reference to the nfa_state where the subpattern starts
	
	public $endstate;		// a reference to the nfa_state where the subpattern ends
	
	public function __construct(&$_node, &$_startstate, &$_endstate) {
		$this->node = $_node;
		$this->startstate = $_startstate;
		$this->endstate = $_endstate;
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
		$size = count($this->states);
		$removed = false;
		// iterate until the desired state found and removed
		for ($i = 0; $i < $size && !$removed; $i++)
			if ($this->states[$i] == $state)
			{
				unset($this->states[$i]);
				$removed = true;
			}
	}
	
	/**
	 * moves states from the automaton referred to by $from to this automaton
	 * @param from - a reference to the automaton containing states to be moved
	 */
	public function move_states(&$from) {
		// iterate until all states are moved
		foreach ($from->states as $cur_state)
			array_push($this->states, $cur_state);
		// clear the source
		$from->states = array();		
	}
	 
	/**
	 * replaces oldref with newref in every transition of the automaton
	 * @param oldref - a reference to the old state
	 * @param newref - a reference to the new state
	 */
	public function update_state_references(&$oldref, &$newref) {
		foreach ($this->states as $cur_state)
			$cur_state->update_state_references($oldref, $newref);
	}
	
	/**
	 * checks if new result is better than old result
	 * @param oldres - old result, an object of processing_state
	 * @param newres - new result, an object of processing_state
	 * @return - true if new result is more suitable
	 */
	public function is_new_result_more_suitable(&$oldres, &$newres) {	// TODO
	
	}
	
	/**
	 * returns the longest match using a string as input
	 * @param str - the original input string
	 * @param startpos - index of the start position to match
	 * @param issubpattern - true if matching a subpattern
	 * @return - the longest character sequence matched
	 */
	public function match($str, $startpos, $issubpattern) {	// TODO
	
	}
	
	/**
    * debug function for generating dot code for drawing nfa
    */
	public function nfa2dot() {
		// numerate all states
		$tmp = 0;
		foreach ($this->states as $cur_state)
		{
			$cur_state->id = $tmp;
			$tmp++;
		}
		// generate dot code
		$dotcode = array();
		$dotcode[] = 'digraph {';
		$dotcode[] = 'rankdir = LR;';
		foreach ($this->states as $cur_state) {			
			$index1 = $cur_state->id;
			// draw a single state
			if (count($cur_state->next) == 0)
				$dotcode[] = "$index1";
			// draw a state with transitions
			else
				foreach ($cur_state->next as  $cur_transition) {
					$index2 = $cur_transition->state->id;
					$lab = $cur_transition->pregleaf->tohr();
					$dotcode[] = "$index1->$index2"."[label=\"$lab\"];";
				}
		}
		$dotcode[] = '};';
		return $dotcode;
	}
	
	/**
    * debug function for drawing nfa
    */
	public function draw_nfa($dot_filename, $jpg_filename) {
		$dotcode = $this->nfa2dot();
        $dotfile = fopen($dot_filename, 'w');
        foreach ($dotcode as $str) {
            fprintf($dotfile, "%s\n", $str);
        }
        chdir($this->graphvizpath);
        exec("dot.exe -Tjpg -o\"$jpg_filename\" -Kdot $dot_filename");
        echo "<IMG src=\"$jpg_filename\" width=\"90%\">";
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
	 * updates references to states where subpatterns start
	 * @param subpatterns - an array containing subpatterns of the regexp
	 * @param oldstate - a reference to be replaced
	 * @param newstate - a reference to replace the old reference
	 */
	protected function update_subpattern_references(&$subpatterns, &$oldstate, &$newstate) {
		// states are merged from "front" to "back" so check only $startstate
		$size = count($subpatterns);
		for ($i = 0; $i < $size; $i++)
			if ($subpatterns[$i]->startstate == $oldstate)
				$subpatterns[$i]->startstate = $newstate;
	}
	
	/**
	 * creates an automaton corresponding to this node
	 * @param stack_of_automatons - a stack which operators pop automatons off and operands push automatons onto
	 * @param subpatterns - an array containing subpatterns of the regexp
	 */
	abstract public function create_automaton(&$stack_of_automatons, &$subpatterns);
	
	public function __construct(&$node, &$matcher) {
		$this->pregnode = $node;
	}

}


/**
* class for nfa transitions
*/
class nfa_preg_leaf extends nfa_preg_node {
	
	public $mergedassertions = array();	// an array containing assertions merged to this transition
		
	/**
	 * creates an automaton corresponding to this node
	 * @param stack_of_automatons - a stack which operators pop automatons off and operands push automatons onto
	 * @param subpatterns - an array containing subpatterns of the regexp
	 */
	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		// create start and end states of the resulting automaton
		$start = new nfa_state;
		$end = new nfa_state;
		$start->append_transition(new nfa_transition($this->pregnode, $end));
		
		$res = new nfa;
		$res->append_state($start);
		$res->append_state($end);
		$res->startstate = $start;
		$res->endstate = $end;
		array_push($stack_of_automatons, $res);
	}
	
}

/**
* abstract class for nfa operators
*/
abstract class nfa_preg_operator extends nfa_preg_node {
	
	public $operands = array();	// an array of operands

}

/**
* defines concatenation
*/
class nfa_preg_node_concat extends nfa_preg_operator {

	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		// first, operands create their automatons
		$size = count($this->operands);
		for ($i = $size - 1; $i >= 0; $i--)
			$this->operands[$i]->create_automaton($stack_of_automatons, $subpatterns);
		// then concatenate them
		$first = array_pop($stack_of_automatons);
		for ($i = 0; $i < $size - 1; $i++) {
			// take a new automaton and update subpattern_state references because of merging states
			$second = array_pop($stack_of_automatons);
			$second->update_state_references($second->startstate, $first->endstate);			
			$this->update_subpattern_references($subpatterns, $second->startstate, $first->endstate);
			// merge and move states
			$first->endstate->merge($second->startstate);
			$second->remove_state($second->startstate);
			$first->endstate = $second->endstate;
			$first->move_states($second);
		}
		array_push($stack_of_automatons, $first);
	}

}

/**
* defines alternation
*/
class nfa_preg_node_alt extends nfa_preg_operator {
	
	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		// first, operands create their automatons
		$size = count($this->operands);
		for ($i = $size - 1; $i >= 0; $i--)
			$this->operands[$i]->create_automaton($stack_of_automatons, $subpatterns);
		// then alternate them
		$first = array_pop($stack_of_automatons);
		$endstate = new nfa_state;
		$first->append_state($endstate);
		// create an eps-transition
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$first->endstate->append_transition(new nfa_transition($epsleaf, $endstate));
		$first->endstate = $endstate;		
		for ($i = 0; $i < $size - 1; $i++) {
			// create an eps-transition
			$epsleaf = new preg_leaf_meta;
			$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
			// take a new automaton and update subpattern_state references because of merging states
			$second = array_pop($stack_of_automatons);
			$second->endstate->append_transition(new nfa_transition($epsleaf, $endstate));
			$second->update_state_references($second->startstate, $first->startstate);
			$this->update_subpattern_references($subpatterns, $second->startstate, $first->startstate);
			// merge and move states
			$first->startstate->merge($second->startstate);
			$second->remove_state($second->startstate);
			$first->move_states($second);		
		}
		array_push($stack_of_automatons, $first);
	}
	
}

/**
* defines infinite quantifiers * + {m,}
*/
class nfa_preg_node_infinite_quant extends nfa_preg_operator {

	/**
	 * creates an automaton for * or {0,} quantifier
	 */
	private function create_aster(&$stack_of_automatons, &$subpatterns) {
		$this->operands[0]->create_automaton($stack_of_automatons, $subpatterns);
		$body = array_pop($stack_of_automatons);
		$body->startstate->starts_infinite_quant = true;
		// create the first eps-transition
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate));
		// create the second eps-transition
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$tr = new nfa_transition($epsleaf, $body->startstate);
		$tr->loops = true;	// important! it kills eps-loops when executing
		$body->endstate->append_transition($tr);
		array_push($stack_of_automatons, $body);
	}
	
	/**
	 * creates an automaton for {m,} quantifier
	 */
	private function create_brace(&$stack_of_automatons, &$subpatterns) {
		// create an automaton for body ($leftborder + 1) times
		$leftborder = $this->pregnode->leftborder;
		for ($i = 0; $i < $leftborder + 1; $i++)
			$this->operands[0]->create_automaton($stack_of_automatons, $subpatterns);
		$res = null;	// the resulting automaton
		// linking automatons to the resulting one
		for ($i = 0; $i < $leftborder + 1; $i++) {
			$cur = array_pop($stack_of_automatons);			
			if ($i > 0) {
				// the last block is repeated
				if ($i == $leftborder) {
					$cur->startstate->starts_infinite_quant = true;
					$epsleaf = new preg_leaf_meta;
					$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
					$cur->startstate->append_transition(new nfa_transition($epsleaf, $cur->endstate));
					$epsleaf = new preg_leaf_meta;
					$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
					$tr = new nfa_transition($epsleaf, $cur->startstate);
					$tr->loops = true;
					$cur->endstate->append_transition($tr);
				}
				// merging
				$cur->update_state_references($cur->startstate, $res->endstate);
				$this->update_subpattern_references($subpatterns, $cur->startstate, $res->startstate);
				$res->endstate->merge($cur->startstate);
				$cur->remove_state($cur->startstate);
				$res->move_states($cur);
				$res->endstate = $cur->endstate;
			} else
				$res = $cur;
		}
		array_push($stack_of_automatons, $res);
	}
	
	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		if ($this->pregnode->leftborder == 0)
			$this->create_aster($stack_of_automatons, $subpatterns);
		else
			$this->create_brace($stack_of_automatons, $subpatterns);
	}
	
}

/**
* defines finite quantifiers {m, n}
*/
class nfa_preg_node_finite_quant extends nfa_preg_operator {
	
	/**
	 * creates an automaton for ? quantifier
	 */
	private function create_qu(&$stack_of_automatons, &$subpatterns) {
		$this->operands[0]->create_automaton($stack_of_automatons, $subpatterns);
		$body = array_pop($stack_of_automatons);
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate));
		array_push($stack_of_automatons, $body);
	}
	
	/**
	 * creates an automaton for {m, n} quantifier
	 */
	private function create_brace(&$stack_of_automatons, &$subpatterns) {
		// create an automaton for body ($leftborder + 1) times
		$leftborder = $this->pregnode->leftborder;
		$rightborder = $this->pregnode->rightborder;
		for ($i = 0; $i < $rightborder; $i++)
			$this->operands[0]->create_automaton($stack_of_automatons, $subpatterns);
		$res = null;		// the resulting automaton
		$endstate = null;	// the end state, required if $leftborder != $rightborder
		if ($leftborder != $rightborder)
			$endstate = new nfa_state;
		// linking automatons to the resulting one
		for ($i = 0; $i < $rightborder; $i++) {
			$cur = array_pop($stack_of_automatons);
			if ($i >= $leftborder && $leftborder != $rightborder) {
				$epsleaf = new preg_leaf_meta;
				$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
				$cur->startstate->append_transition(new nfa_transition($epsleaf, $endstate));
			}			
			if ($i > 0) {
				$cur->update_state_references($cur->startstate, $res->endstate);
				$this->update_subpattern_references($subpatterns, $cur->startstate, $res->endstate);
				$res->endstate->merge($cur->startstate);
				$cur->remove_state($cur->startstate);
				$res->move_states($cur);
				$res->endstate = $cur->endstate;
			} else
				$res = $cur;
		}
		if ($leftborder != $rightborder) {
			array_push($res->states, $endstate);
			$epsleaf = new preg_leaf_meta;
			$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
			$res->endstate->append_transition(new nfa_transition($epsleaf, $endstate));
			$res->endstate = $endstate;
		}
		array_push($stack_of_automatons, $res);
	}
	
	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1)
			$this->create_qu($stack_of_automatons, $subpatterns);
		else
			$this->create_brace($stack_of_automatons, $subpatterns);
	}
	
}

/**
* defines subpatterns
*/
class nfa_preg_node_subpatt extends nfa_preg_operator {	// TODO

	public function create_automaton(&$stack_of_automatons, &$subpatterns) {
		$this->operands[0]->create_automaton($stack_of_automatons, $subpatterns);
		$body = array_pop($stack_of_automatons);
		$startstate = new nfa_state;
		$endstate = new nfa_state;
		array_push($body->states, $startstate);
		array_push($body->states, $endstate);
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$startstate->append_transition(new nfa_transition($epsleaf, $body->startstate));
		$epsleaf = new preg_leaf_meta;
		$epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
		$body->endstate->append_transition(new nfa_transition($epsleaf, $endstate));
		$body->startstate = $startstate;
		$body->endstate = $endstate;		
		array_push($stack_of_automatons, $body);		
		if (isset($subpatterns)) {
			// check the subpattern for existance not to add it if it's looped
			$exists = false;
			$size = count($subpatterns);
			for ($i = 0; $i < $size && !$exists; $i++)
				if ($subpatterns[$i]->node == $this)
					$exists = true;
			if (!$exists)
				array_push($subpatterns, new subpattern_states($this, $body->startstate, $body->endstate));
		}		
	}
	
}

?>