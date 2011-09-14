<?php

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');


class nfa_preg_matcher extends preg_matcher {

    public $automaton;    // an nfa corresponding to the given regex

    /**
    * returns prefix for engine specific classes
    */
    protected function nodeprefix() {
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
        case preg_matcher::NEXT_CHARACTER:
        case preg_matcher::CHARACTERS_LEFT:
        case preg_matcher::SUBPATTERN_CAPTURING:
            return true;
            break;
        }
        return false;
    }

    /**
    * do real matching
    * @param str a string to match
    */
    function match_inner($str) {
        $curresult = new processing_state($this->automaton->startstate, 0, false, 0, array(), array(), array());
        $startpos = 0;
        $len = strlen($str);
        // match from all indexes
        for ($j = 0; $j < $len && !$curresult->isfullmatch; $j++) {
            $tmp = $this->automaton->match($str, $j);
            if ($this->automaton->is_new_result_more_suitable(&$curresult, &$tmp)) {
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