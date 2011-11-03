<?php

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');

/**
 * defines a state of an automaton when running
 * used when matching a string
 */
class processing_state {
    public $state;                       // a reference to the state which automaton is in
    public $matchcnt;                    // the number of characters matched
    public $isfullmatch;                 // whether the match is full
    public $nextpossible;                // the next possible character
    public $left;                        // number of characters left for matching
    public $subpattern_indexes_first = array();   // key = subpattern number
    public $subpattern_indexes_last = array();    // key = subpattern number
    public $subpatterns_captured = array();       // an array containing subpatterns captured at the moment
    public $backreftransition;                    // != null if the last transition matched is a backreference
    public $backrefmatchlen;                      // length of the last match

    public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_nextpossible, $_left, $_subpattern_indexes_first, $_subpattern_indexes_last, $_subpatterns_captured, $_backreftransition = null, $_backrefmatchlen = 0) {
        $this->state = $_state;
        $this->matchcnt = $_matchcnt;
        $this->isfullmatch = $_isfullmatch;
        $this->nextpossible = $_nextpossible;
        $this->left = $_left;
        $this->subpattern_indexes_first = $_subpattern_indexes_first;
        $this->subpattern_indexes_last = $_subpattern_indexes_last;
        $this->subpatterns_captured = $_subpatterns_captured;
        $this->backreftransition = $_backreftransition;
        $this->backrefmatchlen = $_backrefmatchlen;
    }
}


class nfa_preg_matcher extends preg_matcher {

    public $automaton;    // an nfa corresponding to the given regex

    /**
    * returns prefix for engine specific classes
    */
    protected function node_prefix() {
        return 'nfa';
    }

    public function name() {
        return 'nfa_preg_matcher';
    }

    protected function get_engine_node_name($pregname) {
        switch($pregname) {
        case 'node_finite_quant':
        case 'node_infinite_quant':
        case 'node_concat':
        case 'node_alt':
        case 'node_subpatt':
            return 'nfa_preg_'.$pregname;
            break;
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
        case 'node_assert':
            return 'nfa_preg_leaf';
            break;
        }

        return parent::get_engine_node_name($pregname);
    }

    /**
    * returns true for supported capabilities
    * @param capability the capability in question
    * @return bool is capanility supported
    */
    public function is_supporting($capability) {
        switch($capability) {
        case preg_matcher::PARTIAL_MATCHING:
        //case preg_matcher::NEXT_CHARACTER:
        case preg_matcher::CHARACTERS_LEFT:
        case preg_matcher::SUBPATTERN_CAPTURING:
            return true;
            break;
        }
        return false;
    }

    function is_node_acceptable($pregnode) {
        switch ($pregnode->name()) {
        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
            return true;
            break;
        }
        return false;
    }

    /**
     * checks if new result is better than old result
     * @param oldres - old result, an object of processing_state
     * @param newres - new result, an object of processing_state
     * @return - true if new result is more suitable
     */
    public function is_new_result_more_suitable(&$oldres, &$newres) {
        if (($oldres->state != $this->automaton->endstate && $newres->matchcnt >= $oldres->matchcnt) ||                                                    // new match is longer
            ($newres->state == $this->automaton->endstate && $oldres->state != $this->automaton->endstate) ||                                              // new match is full
            ($newres->state == $this->automaton->endstate && $oldres->state == $this->automaton->endstate && $newres->matchcnt >= $oldres->matchcnt)) {    // new match is full and longer
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns the minimal number of characters left for matching
     * @param laststate - the last state of the automaton, an object of processing_state
     * @return - number of characters left for matching
     */
    public function characters_left($laststate) {
        $curstates = array();    // states which the automaton is in
        $result = -1;
        if ($laststate->backrefmatchlen == 0) {
            array_push($curstates, $laststate);
        } else {
            $transition = $laststate->backreftransition;
            $length = $laststate->subpattern_indexes_last[$transition->pregleaf->number] - $laststate->subpattern_indexes_first[$transition->pregleaf->number] + 1 - $laststate->backrefmatchlen;
            $newstate = new processing_state($transition->state, $laststate->matchcnt + $length, false, 0, -1, $laststate->subpattern_indexes_first, $laststate->subpattern_indexes_last, $laststate->subpatterns_captured);
            array_push($curstates, $newstate);
        }
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $currentstate = array_pop($curstates);
                if (count($currentstate->state->next) == 0  && ($result == -1 || ($result != -1 && $currentstate->matchcnt < $result))) {
                    $result = $currentstate->matchcnt;
                }
                foreach ($currentstate->state->next as $next) {
                    if (!$next->loops) {
                        $skip = false;
                        if (is_a($next->pregleaf, 'preg_leaf_backref')) {
                            // only generated subpatterns can be passed
                            if (array_key_exists($next->pregleaf->number, $currentstate->subpattern_indexes_last)) {
                                $length = $currentstate->subpattern_indexes_last[$next->pregleaf->number] - $currentstate->subpattern_indexes_first[$next->pregleaf->number] + 1;
                            } else {
                                $skip = true;
                            }
                        } else {
                            $length = $next->pregleaf->consumes();
                        }
                        if (!$skip) {
                            $newstate = new processing_state($next->state, $currentstate->matchcnt + $length, false, 0, -1, $currentstate->subpattern_indexes_first, $currentstate->subpattern_indexes_last, $currentstate->subpatterns_captured);
                            // save subpattern indexes
                            foreach ($next->subpatt_start as $key=>$subpatt) {
                                if (!isset($newstate->subpattern_indexes_first[$key])) {
                                    $newstate->subpattern_indexes_first[$key] = $currentstate->matchcnt + $length;    // saving to index_first for backreference capturing
                                }
                            }
                            foreach ($next->subpatt_end as $key=>$subpatt) {
                                if (!isset($newstate->subpattern_indexes_last[$key])) {
                                    $newstate->subpattern_indexes_last[$key] = $currentstate->matchcnt + $length;    // saving to index_last
                                }
                            }
                            array_push($newstates, $newstate);
                        }
                    }
                }
            }
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        return $result - $laststate->matchcnt;
    }

    /**
     * returns the longest match using a string as input. matching is proceeded from a given start position
     * @param str - the original input string
     * @param startpos - index of the start position to match
     * @param cs - is matching case sensitive
     * @return - the longest character sequence matched
     */
    public function match_from_pos($str, $startpos) {
        $curstates = array();    // states which the automaton is in
        $skipstates = array();   // contains states where infinite quantifiers start. it's used to protect from loops like ()*

        $this->reset_subpattern_indexes();
        $result = new processing_state($this->automaton->startstate, 0, false, 0, -1, $this->index_first, $this->index_last, array());
        
        array_push($curstates, $result);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $currentstate = array_pop($curstates);
                // saving the result
                if ($this->is_new_result_more_suitable(&$result, &$currentstate)) {
                    $result = $currentstate;
                }
                // kill epsilon-cycles
                $skip = false;
                if ($currentstate->state->startsinfinitequant) {
                    // skipstates is sorted by matchcnt because transitions add characters
                    for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $currentstate->matchcnt <= $skipstates[$i]->matchcnt; $i--)
                        if ($skipstates[$i]->state === $currentstate->state && $skipstates[$i]->matchcnt == $currentstate->matchcnt) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $currentstate);
                    }
                }
                // iterate over all transitions
                if (!$skip) {
                    $this->index_first = $currentstate->subpattern_indexes_first;
                    $this->index_last = $currentstate->subpattern_indexes_last;
                    foreach ($currentstate->state->next as $next) {
                        $pos = $currentstate->matchcnt;
                        $length = 0;
                        if ($next->pregleaf->match($str, $startpos + $pos, &$length, !$next->pregleaf->caseinsensitive )) {
                            // create a new state
                            $newstate = new processing_state($next->state, $pos + $length, false, 0, -1, $currentstate->subpattern_indexes_first, $currentstate->subpattern_indexes_last, $currentstate->subpatterns_captured);
                            // save subpattern indexes
                            foreach ($next->subpatt_start as $key=>$subpatt) {
                                if ($newstate->subpattern_indexes_first[$key] < 0) {
                                    $newstate->subpattern_indexes_first[$key] = $startpos + $pos;
                                }
                            }
                            foreach ($next->subpatt_end as $key=>$subpatt) {
                                if ($newstate->subpattern_indexes_first[$key] >= 0 && !(isset($newstate->subpatterns_captured[$key]) && $newstate->subpatterns_captured[$key])) {
                                    $newstate->subpattern_indexes_last[$key] = $startpos + $pos + $length - 1;
                                    $subpatt_exists = false;
                                    foreach ($newstate->state->next as $nextnext) {        // search for this subpattern in next transitions
                                        $subpatt_exists = $subpatt_exists || isset($nextnext->belongs_to_subpatt[$key]);
                                    }
                                    if (!$subpatt_exists) {
                                        $newstate->subpatterns_captured[$key] = true;
                                    }
                                }
                            }
                            // save the state
                            array_push($newstates, $newstate);
                        } else if ($length > 0) {    // (length > 0) equals to (next->pregleaf is a backreference)
                            $currentstate->matchcnt += $length;
                            $currentstate->backreftransition = $next;
                            $currentstate->backrefmatchlen = $length;
                            if ($this->is_new_result_more_suitable(&$result, &$currentstate)) {
                                $result = $currentstate;
                            }
                        }
                    }
                    $this->reset_subpattern_indexes();
                }
            }
            // replace curstates with newstates
            foreach ($newstates as $state) {
                array_push($curstates, $state);
            }
            $newstates = array();
        }
        $result->isfullmatch = ($result->state == $this->automaton->endstate);
        if ($result->matchcnt > 0) {
            $result->subpattern_indexes_first[0] = $startpos;
            $result->subpattern_indexes_last[0] = $startpos + $result->matchcnt - 1;
        } else {
            $textlib = textlib_get_instance();
            $result->subpattern_indexes_first[0] = $textlib->strlen($str);
            $result->subpattern_indexes_last[0] = $result->subpattern_indexes_first[0] - 1;
        }
        return $result;
    }

    /**
    * do real matching
    * @param str a string to match
    */
    function match_inner($str) {
        $result = new processing_state($this->automaton->startstate, 0, false, 0, -1, array(), array(), array());
        $startpos = 0;
        $textlib = textlib_get_instance();
        $len = $textlib->strlen($str);
        // match from all indexes
        for ($j = 0; $j < $len && !$result->isfullmatch; $j++) {
            $tmp = $this->match_from_pos($str, $j);
            if ($this->is_new_result_more_suitable(&$result, &$tmp)) {
                $result = $tmp;
                $startpos = $j;
            }
        }
        // save the result
        $this->is_match = ($result->matchcnt > 0);
        $this->full = $result->isfullmatch;
        if (!$result->isfullmatch) {
            // TODO character generation
            $result->left = $this->characters_left($result);
        } else {
            $result->left = 0;
        }
        foreach ($result->subpattern_indexes_last as $key=>$subpatt) {
            $this->index_first[$key] = $result->subpattern_indexes_first[$key];
            $this->index_last[$key] = $result->subpattern_indexes_last[$key];
        }
        $this->next = $result->nextpossible;
        $this->left = $result->left;
    }

    public function __construct($regex = null, $modifiers = null) {
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }
        $stack = array();
        $this->dst_root->create_automaton(&$stack);
        $this->automaton = array_pop($stack);
        //$this->automaton->append_endeps();
        //$this->automaton->replace_eps_transitions();
        //$this->automaton->merge_simple_assertions();
        //$this->automaton->delete_unreachable_states();
    }

}

?>