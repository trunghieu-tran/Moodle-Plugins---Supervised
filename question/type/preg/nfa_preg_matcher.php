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

    public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_nextpossible, $_left, $_subpattern_indexes_first, $_subpattern_indexes_last, $_subpatterns_captured) {
        $this->state = $_state;
        $this->matchcnt = $_matchcnt;
        $this->isfullmatch = $_isfullmatch;
        $this->nextpossible = $_nextpossible;
        $this->left = $_left;
        $this->subpattern_indexes_first = $_subpattern_indexes_first;
        $this->subpattern_indexes_last = $_subpattern_indexes_last;
        $this->subpatterns_captured = $_subpatterns_captured;
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
        array_push($curstates, $laststate);
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $currentstate = array_pop($curstates);
                if (count($currentstate->state->next) == 0  && ($result == -1 || ($result != -1 && $currentstate->matchcnt < $result))) {
                    $result = $currentstate->matchcnt;
                }
                for ($i = 0; $i < count($currentstate->state->next); $i++) {
                    if (!$currentstate->state->next[$i]->loops) {
                        $next = $currentstate->state->next[$i];
                        $length = 0;
                        //if (is_a($next->pregleaf, 'preg_leaf_backref'))
                            //echo count($this->index_last);
                            //$length = $this->index_last[$next->pregleaf->number] - $this->index_first[$next->pregleaf->number];
                        /*else*/ if ($next->pregleaf->consumes())
                            $length = 1;
                        $newstate = new processing_state($next->state, $currentstate->matchcnt + $length/*$next->length()*/, false, 0, -1, array(), array(), array());
                        array_push($newstates, $newstate);
                    }
                }
            }
            for ($i = 0; $i < count($newstates); $i++) {
                array_push($curstates, $newstates[$i]);
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

        $result = new processing_state($this->automaton->startstate, 0, false, 0, -1, array(), array(), array());
        $this->index_first = array();
        $this->index_last = array();
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
                        if ($skipstates[$i]->state === $currentstate->state && $skipstates[$i]->matchcnt == $currentstate->matchcnt) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $currentstate);
                    }
                }
                // iterate over all transitions
                for ($i = 0; !$skip && $i < count($currentstate->state->next); $i++) {
                    $pos = $currentstate->matchcnt;
                    $length = 0;
                    $next = $currentstate->state->next[$i];
                    if ($next->pregleaf->match($str, $startpos + $pos, &$length, !$next->pregleaf->caseinsensitive )) {
                        // save subpattern indexes
                        foreach ($next->subpatt_start as $key=>$subpatt) {
                            if (!isset($currentstate->subpattern_indexes_first[$key])) {
                                $currentstate->subpattern_indexes_first[$key] = $startpos + $pos;
                                $this->index_first[$key] = $startpos + $pos;    // save it for backreference capturing
                            }
                        }
                        foreach ($next->subpatt_end as $key=>$subpatt) {
                            if (isset($currentstate->subpattern_indexes_first[$key]) && !(isset($currentstate->subpatterns_captured[$key]) && $currentstate->subpatterns_captured[$key])) {
                                $currentstate->subpattern_indexes_last[$key] = $startpos + $pos + $length - 1;
                                $this->index_last[$key] = $startpos + $pos + $length - 1;    // save it for backreference capturing
                            }
                        }
                        foreach ($currentstate->subpattern_indexes_first as $key=>$subpatt) {
                            if (!isset($next->belongs_to_subpatt[$key]) && isset($currentstate->subpattern_indexes_last[$key])) {
                                $currentstate->subpatterns_captured[$key] = true;
                            }
                        }                        
                        $newstate = new processing_state($next->state, $pos + $length, false, 0, -1, $currentstate->subpattern_indexes_first, $currentstate->subpattern_indexes_last, $currentstate->subpatterns_captured);
                        // save the state
                        array_push($newstates, $newstate);
                        // save the next state as a result if it's a matching state
                        if ($next->state == $this->automaton->endstate && $this->is_new_result_more_suitable(&$result, &$newstate)) {
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
        $result->isfullmatch = ($result->state == $this->automaton->endstate);
        if ($result->matchcnt > 0) {
            $result->subpattern_indexes_first[0] = $startpos;
            $this->index_first[0] = $startpos;
            $result->subpattern_indexes_last[0] = $startpos + $result->matchcnt - 1;
            $this->index_last[0] = $startpos + $result->matchcnt - 1;
        } else {
            $result->subpattern_indexes_first[0] = -1;
            $result->subpattern_indexes_last[0] = -1;
            $this->index_first[0] = -1;
            $this->index_last[0] = -1;
        }
        if (!$result->isfullmatch) {
            // TODO character generation
            $result->left = $this->characters_left($result);
        } else {
            $result->left = 0;
        }
        /*foreach ($result->subpattern_indexes_first as $id=>$sp) {
            echo "id=".$id."index1=".$sp."index2=".$result->subpattern_indexes_last[$id]."<br />";
        }
        echo $result->matchcnt."<br />";*/
        return $result;

    }

    /**
    * do real matching
    * @param str a string to match
    */
    function match_inner($str) {
        $curresult = new processing_state($this->automaton->startstate, 0, false, 0, -1, array(), array(), array());
        $startpos = 0;
        $len = strlen($str);
        // match from all indexes
        for ($j = 0; $j < $len && !$curresult->isfullmatch; $j++) {
            $tmp = $this->match_from_pos($str, $j);
            if ($this->is_new_result_more_suitable(&$curresult, &$tmp)) {
                $curresult = $tmp;
                $startpos = $j;
            }
        }
        // save the result
        $this->is_match = ($curresult->matchcnt > 0);
        $this->full = $curresult->isfullmatch;
        foreach ($curresult->subpattern_indexes_last as $key=>$index) {
            $this->index_last[$key] = $index;
            $this->index_first[$key] = $curresult->subpattern_indexes_first[$key];
        }
        $this->next = $curresult->nextpossible;
        $this->left = $curresult->left;
    }
    
    /**
    * numerates subpatterns
    * @param pregnode - preg_node child class instance
    * @param cnt - current subpattern count
    */
    protected function numerate_subpatterns(&$pregnode, &$cnt) {
        if (is_a($pregnode, 'preg_operator')) {
            if (is_a($pregnode, 'preg_node_subpatt')) {
                $cnt++;
                $pregnode->number = $cnt;
            }
            foreach ($pregnode->operands as $curop) {
                $this->numerate_subpatterns($curop, $cnt);
            }
         }
    }

    public function __construct($regex = null, $modifiers = null) {
        parent::__construct($regex, $modifiers);
        if (!isset($regex) || !empty($this->errors)) {
            return;
        }
        $subpattcnt = 0;
        $this->numerate_subpatterns($this->ast_root, $subpattcnt);
        $stack = array();
        $this->dst_root->create_automaton(&$stack);
        $this->automaton = array_pop($stack);
    }

}

?>