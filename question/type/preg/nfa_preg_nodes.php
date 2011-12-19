<?php
/**
 * Defines NFA node classes
 *
 * @copyright &copy; 2011  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/question/type/preg/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * defines a transition between two states
 */
class nfa_transition
{
    public $loops = false;                  // true if this transition makes a loop: for example, (...)* contains an epsilon-transition that makes a loop
    public $pregleaf;                       // transition data, a reference to an object of preg_leaf
    public $state;                          // the state which this transition leads to, a reference to an object of nfa_state
    public $replaceable;                    // eps-transitions are replaced by next non-eps transitions for merging simple assertions
    public $subpatt_start = array();        // an array of subpatterns which start in this transition
    public $subpatt_end = array();          // an array of subpatterns which end in this transition
    //public $belongs_to_subpatt = array();   // an array of subpatterns which this transition belongs to

    public function __construct(&$_pregleaf, &$_state, $_loops, $_replaceable = false) {
        $this->pregleaf = clone $_pregleaf;    // the leaf should be unique
        $this->state = $_state;
        $this->loops = $_loops;
        $this->replaceable = $_replaceable;
    }

    /**
    * When clonning a transition we want a copy of its pregleaf
    */
    public function __clone() {
        $this->pregleaf = clone $this->pregleaf;
    }
}

/**
 * defines an nfa state
 */
class nfa_state
{

    public $startsinfinitequant = false;    // true if this state starts an infinite quantifier either * or + or {m,}
    public $next = array();                 // an array of objects of nfa_transition
    //public $previous = array();
    public $id;                             // id of the state, debug variable

    /**
     * appends a next possible state
     * @param transition - a reference to the transition to be appended
     * $return - true if transition was appended
     */
    public function append_transition(&$transition) {
        // avoid self-loops by eps-transitions
        if ($transition->state === $this && $transition->pregleaf->subtype == preg_leaf_meta::SUBTYPE_EMPTY) {
            return false;
        }

        $exists = false;
        // not unique transitions are not appended
        foreach($this->next as $curnext) {
            if ($curnext->pregleaf == $transition->pregleaf && $curnext->state === $transition->state && $curnext->pregleaf->mergedassertions === $transition->pregleaf->mergedassertions) {
                $exists = true;
            }
        }
        if (!$exists) {
            array_push($this->next, clone $transition);
            if ($transition->loops) {
                $this->startsinfinitequant = true;
            }
        }
    }

    /**
     * removes a transition
     * @param transition - a reference to the transition to be removed
     */
    public function remove_transition(&$transition) {
        foreach($this->next as $key=>$curnext) {
            if ($curnext->pregleaf == $transition->pregleaf && $curnext->state === $transition->state) {
                // delete an element from $curnext->state->previous
                /*foreach ($curnext->state->previous as $keyprev=>$curprev) {
                    if ($curprev->pregleaf == $transition->pregleaf && $curprev->state === $this) {
                        unset($curnext->state->previous[$keyprev]);
                    }
                }*/
                unset($this->next[$key]);
            }
        }
    }

    /**
     * replaces oldref with newref in every transition
     * @param oldref - a reference to the old state
     * @param newref - a reference to the new state
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach($this->next as $curnext) {
            if ($curnext->state === $oldref) {
                $curnext->state = $newref;
            }
        }
        /*foreach($this->previous as $curprev) {
            if ($curprev->state === $oldref) {
                $curprev->state = $newref;
            }
        }*/
    }

    /**
     * merges two states
     * @param with - a reference to state the to be merged with
     */
    public function merge(&$with) {
        $this->next = array_merge($this->next, $with->next);
        //$this->previous = array_merge($this->previous, $with->previous);
        $this->startsinfinitequant = $this->startsinfinitequant || $with->startsinfinitequant;
    }

    /**
     * debug function
     */
    public function is_equal(&$to) {
        return $this->next == $to->next;        //this is quite enough
    }

}

/**
 * defines a nondeterministic finite automaton
 */
class nfa {

    public $startstate;          // a reference to the start nfa_state of the automaton
    public $endstate;            // a reference to the end nfa_state of the automaton
    public $states = array();    // an array containing references to states of the automaton

    /**
     * appends an eps-transition to the end for merging simple assertions
     */
    public function append_endeps() {
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $end = new nfa_state;
        $this->append_state($end);
        $this->endstate->append_transition(new nfa_transition($epsleaf, $end, false));
        $this->endstate = $end;
    }

    /**
     * replaces all eps-transitions with next non-eps transitions
     */
    /*public function replace_eps_transitions() {
        foreach ($this->states as $keystate=>$curstate) {
            do {    // iterate until all transitions aren't replaceable
                foreach ($curstate->next as $keynext=>$curnext) {
                    if ($curnext->replaceable) {
                        foreach ($curnext->state->next as $newnext) {
                            $this->states[$keystate]->append_transition($newnext);
                        }
                        $this->states[$keystate]->remove_transition($curnext);
                    }
                }
                $replaceable_cnt = 0;
                foreach ($curstate->next as $keynext=>$curnext) {
                    if ($curnext->replaceable) {
                        $replaceable_cnt++;
                    }
                }
            } while ($replaceable_cnt);

        }
    }*/

    /*public function merge_simple_assertions() {
        $i=0;
        $this->draw_nfa("C:/dotfile/dotcode.dot", "C:/dotfile/0.jpg");
        foreach ($this->states as $keystate=>$curstate) {
            do {
                $tobeappended = array();
                foreach ($curstate->next as $keynext=>$curnext) {
                    if (is_a($curnext->pregleaf, 'preg_leaf_assert')) {
                        // saving next transitions
                        foreach ($curnext->state->next as $newnext) {
                            $newnext->loops = $newnext->loops || $curnext->loops;
                            if ($newnext->pregleaf != $curnext->pregleaf)
                                $tobeappended[] = $newnext;
                            //$this->states[$keystate]->append_transition($newnext, $curnext->pregleaf);
                        }
                        $state = $curnext->state;
                        $this->states[$keystate]->remove_transition($curnext);
                        $in = 0;
                        foreach ($curnext->state->previous as $curprev)
                            //if (!$curprev->loops)
                                $in++;
                        if ($in == 0) {
                            $this->remove_state($state);
                        }
                        $i++;
                        $this->draw_nfa("C:/dotfile/dotcode.dot", "C:/dotfile/$i.jpg");
                    }
                }
                foreach ($tobeappended as $a) {
                    if (isset($this->states[$keystate]))
                    $this->states[$keystate]->append_transition($a, $curnext->pregleaf);
                }

                $assertcnt = 0;
                foreach ($curstate->next as $keynext=>$curnext) {
                    if (is_a($curnext->pregleaf, 'preg_leaf_assert')) {
                        $assertcnt++;
                    }
                }
            } while ($assertcnt > 0);

        }
    }*/

    /**
     * there are unreachable states after merging simple assertions. this function deletes them
     */
    /*public function delete_unreachable_states() {
        $curstates = array();    // states which the automaton is in
        $reachedstates = array();
        array_push($curstates, $this->startstate);
        // detecting reachable states
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $currentstate = array_pop($curstates);
                array_push($reachedstates, $currentstate);
                foreach ($currentstate->next as $next) {
                    if (!$next->loops) {
                        array_push($newstates, $next->state);
                    }
                }
            }
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        // deleting unreachable states
        foreach ($this->states as $state) {
            $found = false;
            foreach ($reachedstates as $reached) {
                if ($reached === $state) {
                    $found = true;
                }
            }
            if (!$found) {
                $this->remove_state($state);
            }
        }
    }*/

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
            if ($curstate === $state) {
                // removing all connections with this state
                foreach ($curstate->next as $keynext=>$next) {
                    $this->states[$key]->remove_transition($next);
                }
                unset($this->states[$key]);
            }
        }
    }

    /**
     * moves states from the automaton referred to by $from to this automaton
     * @param from - a reference to the automaton containing states to be moved
     */
    public function move_states(&$from) {
        $this->states = array_merge($this->states, $from->states);
        $from->states = array();    // clear the source
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
    * debug function for generating human-readable leafs including merged assertions
    * @param pregleaf - object of preg_leaf_...
    * @param str - a string for storing the result
    */
    private function tohr_recursive($pregleaf, &$str) {
        $str = $str . $pregleaf->tohr();
        foreach ($pregleaf->mergedassertions as $assert) {
            $str = $str . $assert->tohr();
            if ($assert->mergedassertions != array()) {
                $this->tohr_recursive($assert, $str);
            }
        }
    }

    /**
    * debug function for generating dot code for drawing nfa
    * @param dotfilename - name of the dot file
    * @param jpgfilename - name of the resulting jpg file
    */
    public function draw_nfa($dotfilename, $jpgfilename) {
        $qtypeobj = new qtype_preg();
        $dir = $qtypeobj->get_temp_dir('nfa');
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
            if (count($curstate->next) == 0) {
                fprintf($dotfile, "%s\n", "$index1");
            }
            // draw a state with transitions
            else
                foreach ($curstate->next as $curtransition) {
                    $index2 = $curtransition->state->id;
                    $lab = "";
                    $this->tohr_recursive($curtransition->pregleaf, $lab);
                    // information about subpatterns
                    if (count($curtransition->subpatt_start) > 0) {
                        $lab = $lab."starts";
                        foreach ($curtransition->subpatt_start as $key=>$val) {
                            $lab = $lab."$key,";
                        }
                    }
                    if (count($curtransition->subpatt_end) > 0) {
                        $lab = $lab."ends";
                        foreach ($curtransition->subpatt_end as $key=>$val) {
                            $lab = $lab."$key,";
                        }
                    }
                    fprintf($dotfile, "%s\n", "$index1->$index2"."[label=\"$lab\"];");
                }
        }
        fprintf($dotfile, "};");
        fclose($dotfile);
        $qtypeobj->execute_dot($dotfn, $jpgfilename);        
        unlink($dotfn);
    }

}

/**
* abstract class for both operators and leafs
*/
abstract class nfa_preg_node {

    public $pregnode;    // a reference to the corresponding preg_node

    /**
    * returns true if engine support the node, rejection string otherwise
    */
    public function accept() {
        return true; // accepting anything by default
    }

    /**
     * increases size of an nfa
     * @param matcher - a reference to the matcher
     * @param ds - number of states to add
     * @param dt - number of transitions to add
     * @param statecount - current number of states in automaton
     * @param transitioncount - current number of transitions in automaton
     * @return - true if size successfully enlarged and we can keep building fa, false otherwise
     */
    public function inc_fa_size(&$matcher, $ds, $dt, &$statecount, &$transitioncount) {
        $statecount += $ds;
        $transitioncount += $dt;
        return !($statecount > $matcher->get_state_limit() || $transitioncount > $matcher->get_transition_limit());
    }

    /**
     * creates an automaton corresponding to this node
     * @param matcher - a reference to the matcher
     * @param stackofautomatons - a stack which operators pop automatons off and operands push automatons onto
     * @param issubpattern - true if epsilon transitions are needed at the beginning and at the end of the automaton
     * @param statecount - current number of states in automaton
     * @param transitioncount - current number of transitions in automaton
     * @return - this node if building failed due to the nfa size, null otherwise
     */
    abstract public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount);

    public function __construct(&$node, &$matcher) {
        $this->pregnode = $node;
    }

}

/**
* class for nfa transitions
*/
class nfa_preg_leaf extends nfa_preg_node {

    public function accept() {
        if ($this->pregnode->type == preg_node::TYPE_LEAF_ASSERT && $this->pregnode->subtype == preg_leaf_assert::SUBTYPE_ESC_G) {
            $leafdesc = get_string($this->pregnode->name(), 'qtype_preg');
            return $leafdesc . ' \G';
        }
        return true;

    }

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        // create start and end states of the resulting automaton
        if (!$this->inc_fa_size($matcher, 2, 1, $statecount, $transitioncount)) {
            return $this;
        }
        $start = new nfa_state;
        $end = new nfa_state;
        $start->append_transition(new nfa_transition($this->pregnode, $end, false, $this->pregnode->subtype == preg_leaf_meta::SUBTYPE_EMPTY));
        $res = new nfa;
        $res->append_state($start);
        $res->append_state($end);
        $res->startstate = $start;
        $res->endstate = $end;
        array_push($stackofautomatons, $res);
        return null;
    }

}

/**
* abstract class for nfa operators
*/
abstract class nfa_preg_operator extends nfa_preg_node {

    public $operands = array();    // an array of operands

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

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        // first, operands create their automatons
        $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        $err = $this->operands[1]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
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
        return null;
    }

}

/**
* defines alternation
*/
class nfa_preg_node_alt extends nfa_preg_operator {

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        // first, operands create their automatons
        $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        $err = $this->operands[1]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        // take automata and alternate them
        $second = array_pop($stackofautomatons);
        $first = array_pop($stackofautomatons);
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        // add a new end state if the current end state is looped    (for both of automata)
        $automata = array($first, $second);
        foreach ($automata as $cur) {
            $endlooped = false;
            foreach ($cur->endstate->next as $curnext) {
                if ($curnext->loops) {
                    $endlooped = true;
                }
            }
            if ($endlooped) {
                if (!$this->inc_fa_size($matcher, 1, 1, $statecount, $transitioncount)) {
                    return $this;
                }
                $endstate = new nfa_state;
                $cur->append_state($endstate);
                $cur->endstate->append_transition(new nfa_transition($epsleaf, $endstate, false, true));
                $cur->endstate = $endstate;
            }
        }
        // start and end states are merged
        $second->update_state_references($second->startstate, $first->startstate);
        $second->update_state_references($second->endstate, $first->endstate);
        $first->startstate->merge($second->startstate);
        $first->endstate->merge($second->endstate);
        $second->remove_state($second->startstate);
        $second->remove_state($second->endstate);
        $first->move_states($second);
        array_push($stackofautomatons, $first);
        return null;
    }

}

/**
* defines infinite quantifiers * + {m,}
*/
class nfa_preg_node_infinite_quant extends nfa_preg_operator {

    public function accept() {
        if (!$this->pregnode->greed) {
            return get_string('ungreedyquant', 'qtype_preg');
        }
        return true;
    }

    /**
     * creates an automaton for * or {0,} quantifier
     */
    private function create_aster(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        $body = array_pop($stackofautomatons);
        if (!$this->inc_fa_size($matcher, 0, count($body->startstate->next) + 1, $statecount, $transitioncount)) {
            return $this;
        }
        foreach ($body->startstate->next as $curnext) {
            $clone = clone $curnext;
            $clone->loops = true;
            $body->endstate->append_transition($clone);
        }
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false, true));
        array_push($stackofautomatons, $body);
        return null;
    }

    /**
     * creates an automaton for {m,} quantifier
     */
    private function create_brace(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        // create an automaton for body ($leftborder + 1) times
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
            if ($err != null) {
                return $err;
            }
        }
        $res = null;    // the resulting automaton
        // linking automatons to the resulting one
        for ($i = 0; $i < $leftborder; $i++) {
            $cur = array_pop($stackofautomatons);
            $firststep = false;
            if ($res === null) {
                $res = $cur;
                $firststep = true;
            }
            // the last block is repeated
            if ($i == $leftborder - 1) {
                if (!$this->inc_fa_size($matcher, 0, count($cur->startstate->next) + 1, $statecount, $transitioncount)) {
                    return $this;
                }
                foreach ($cur->startstate->next as $curnext) {
                    $clone = clone $curnext;
                    $clone->loops = true;
                    $cur->endstate->append_transition($clone);
                }
            }
            // merging
            if (!$firststep) {
                $cur->update_state_references($cur->startstate, $res->endstate);
                $res->endstate->merge($cur->startstate);
                $cur->remove_state($cur->startstate);
                $res->move_states($cur);
                $res->endstate = $cur->endstate;
            }
        }
        array_push($stackofautomatons, $res);
        return null;
    }

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        if ($this->pregnode->leftborder == 0) {
            return $this->create_aster($matcher, $stackofautomatons, $statecount, $transitioncount);
        } else {
            return $this->create_brace($matcher, $stackofautomatons, $statecount, $transitioncount);
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
    private function create_qu(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        if (!$this->inc_fa_size($matcher, 0, 1, $statecount, $transitioncount)) {
            return $this;
        }
        $body = array_pop($stackofautomatons);
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false, true));
        array_push($stackofautomatons, $body);
        return null;
    }

    /**
     * creates an automaton for {m, n} quantifier
     */
    private function create_brace(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        // create an automaton for body ($leftborder + 1) times
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;
        for ($i = 0; $i < $rightborder; $i++) {
            $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
            if ($err != null) {
                return $err;
            }
        }
        if (!$this->inc_fa_size($matcher, 0, $rightborder - $leftborder, $statecount, $transitioncount)) {
            return $this;
        }
        $res = null;        // the resulting automaton
        $endstate = null;   // temporary variable, required if $leftborder != $rightborder
        if ($leftborder != $rightborder) {
            $endstate = new nfa_state;
        }
        // linking automatons to the resulting one
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($stackofautomatons);
            if ($i >= $leftborder && $leftborder != $rightborder) {
                $cur->startstate->append_transition(new nfa_transition($epsleaf, $endstate, false, true));
            }
            if ($i > 0) {
                if ($endstate !== null) {
                    $endstate->update_state_references($cur->startstate, $res->endstate);
                }
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
            $res->endstate->merge($endstate);
        }
        array_push($stackofautomatons, $res);
        return null;
    }

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            return $this->create_qu($matcher, $stackofautomatons, $statecount, $transitioncount);
        } else {
            return $this->create_brace($matcher, $stackofautomatons, $statecount, $transitioncount);
        }
    }

}

/**
* defines subpatterns
*/
class nfa_preg_node_subpatt extends nfa_preg_operator {

    public function create_automaton(&$matcher, &$stackofautomatons, &$statecount, &$transitioncount) {
        $err = $this->operands[0]->create_automaton($matcher, $stackofautomatons, $statecount, $transitioncount);
        if ($err != null) {
            return $err;
        }
        $body = array_pop($stackofautomatons);
        foreach ($body->startstate->next as $next) {
            $next->subpatt_start[$this->pregnode->number] = true;
        }
        foreach ($body->states as $state) {
            foreach ($state->next as $next) {
                //$next->belongs_to_subpatt[$this->pregnode->number] = true;
                if ($next->state === $body->endstate) {
                    $next->subpatt_end[$this->pregnode->number] = true;
                }
            }
        }
        array_push($stackofautomatons, $body);
        return null;
    }

}

?>