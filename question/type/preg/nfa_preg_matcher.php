<?php

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_nodes.php');


class nfa_preg_matcher extends preg_matcher {
	
	public $automaton;	// an nfa corresponding to the given regex
	
	var $subpatterns = array();	// an array containing objects of subpattern_states
	
	public function name() {
        return 'nfa_preg_matcher';
	}
	
	/**
    * returns prefix for engine specific classes
    */
    protected function nodeprefix() {
        return 'nfa';
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
				if (!$enginenode->accept() && !array_key_exists($enginenode->rejectmsg,  $this->error_flags))	// highlighting first occurence of unaccepted node
					$this->error_flags[$enginenode->rejectmsg] = array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast);
				else {	// append operands
					foreach ($pregnode->operands as $curOperand)
						array_push($enginenode->operands, $this->from_preg_node($curOperand));
					return $enginenode;
				}
			}
            
        }
		// if it's a leaf - create an nfa transition
		elseif (is_a($pregnode,'preg_leaf')) {
			$engineleaf = new nfa_preg_leaf(&$pregnode, &$this);
			return $engineleaf;			
		}
		else
			return $pregnode;				
    }

	public function __construct($regex = null, $modifiers = null) {	
		parent::__construct($regex, $modifiers);
		$stack = array();
		$this->dst_root->create_automaton(&$stack, &$this->subpatterns);
		$this->automaton = array_pop($stack);
	}

}






?>