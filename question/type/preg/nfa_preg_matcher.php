<?php

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');


class nfa_preg_matcher extends preg_matcher {

	public $automaton;	// an nfa corresponding to the given regex

	/**
	* returns prefix for engine specific classes
	*/
	protected function nodeprefix() {
		return 'nfa';
	}

	public function name() {
		return 'nfa_preg_matcher';
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
	* DST node factory, overloaded, recursive
	* @param pregnode - preg_node child class instance
	* @return corresponding nfa_preg_node child class instance
	*/
	public function &from_preg_node(&$pregnode) {
		// if it's an operator - do some recursion
		if (is_a($pregnode,'preg_operator')) {
			$enginenodename = 'nfa_preg_'.$pregnode->name();
			if (class_exists($enginenodename)) {
				$enginenode = new $enginenodename(&$pregnode, $this);
				if (!$enginenode->accept() && !array_key_exists($enginenode->rejectmsg, $this->error_flags)) {	// highlighting first occurence of unaccepted node
					$this->error_flags[$enginenode->rejectmsg] = array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast);
				} else {	// append operands
					foreach ($pregnode->operands as $curOperand) {
						array_push($enginenode->operands, $this->from_preg_node($curOperand));
					}
					return $enginenode;
				}
			}
		}
		// if it's a leaf - create an nfa transition
		elseif (is_a($pregnode,'preg_leaf')) {
			$engineleaf = new nfa_preg_leaf(&$pregnode, &$this);
			return $engineleaf;
		} else {
			return $pregnode;
		}
	}

	/**
	* do real matching
	* @param str a string to match
	*/
	function match_inner($str) {
		$cs = false;	// is matching case sensitive
		if (strpos($this->modifiers, 'i') === false) {
			$cs = true;
		}
		$curresult = new processing_state($this->automaton->startstate, 0, false, 0, array(), array(), array());
		$startpos = 0;
		$len = strlen($str);
		// match from all indexes
		for ($j = 0; $j < $len; $j++) {
			$tmp = $this->automaton->match($str, $j, $cs);
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

	public function __construct($regex = null, $modifiers = null) {
		parent::__construct($regex, $modifiers);
		$stack = array();
		$this->dst_root->create_automaton(&$stack, true);
		$this->automaton = array_pop($stack);

	}

}

?>