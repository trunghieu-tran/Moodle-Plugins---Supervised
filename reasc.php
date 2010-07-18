<?php //$Id: reasc.php,put version put time dvkolesov Exp $

define('LEAF','0');
define('NODE','1');
define('LEAF_CHARCLASS','2');
define('LEAF_EMPTY','3');
define('LEAF_END','4');
define('LEAF_LINK','5');
define('LEAF_METASYMBOLDOT','6');
define('NODE_CONC','7');
define('NODE_ALT','8');
define('NODE_ITER','9');
define('NODE_SUBPATT','10');
define('NODE_CONDSUBPATT','11');
define('NODE_QUESTQUANT','12');
define('NODE_PLUSQUANT','13');
define('NODE_QUANT','14');
define('NODE_ASSERTTF','15');
define('NODE_ASSERTTB','16');
define('NODE_ASSERTFF','17');
define('NODE_ASSERTFB','18');
define('ASSERT','107741824');
define('DOT','987654321');
define('STREND','123456789');


class node {
	var $type;
	var $subtype;
	var $firop;
	var $secop;
	var $thirdop;
	var $nullable;
	var $number;
	var $firstpos;
	var $lastpos;
	var $followpos;
	var $direction;
	var $greed;
	var $chars;
	
	function name() {
		return 'node';
	}
}

class fas {//finite automate state
	var $asserts;
	var $passages;//хранит номера состояний к которым перейти
	
	function name() {
		return 'fas';
	}
}

class compare_result {
	var $index;
	var $full;
	var $next;
	
	function name() {
		return 'compare_result';
	}
}

class reasc {
	var $connection;//array, $connection[0] for main regex, $connection[<assert number>] for asserts
	var $cconn;//for current connection
	var $roots;//array,[0] main root, [<assert number>] assert's root
	var $croot;//for current root
	var $maxnum;
	var $finiteautomate;// for current finite  automate
	var $finiteautomates;
	
	function name() {
		return 'reasc';
	}
	function append_end() {
	}
	/**
	*Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
	*Put pair of number=>character to $this->cconn.
	*@param $node current node (or leaf) for numerating.
	*/
	function numeration(&$node) {
		if($node->type==NODE&&$node->subtype==ASSERTTF) {//assert node need number
			$node->number = ++$this->$maxnum + ASSERTTF;
		} else if($node->type==NODE) {//not need number for not assert node, numerate operands
			$this->numeration($node->firop);
			if ($node->subtype==NODE_CONC||$node->subtype==NODE_ALT) {//concatenation and alternative have second operand, numerate it.
				$this->numeration($node->secop);
			}
		}
		if($node->type==LEAF) {//leaf need number
			switch($node->subtype) {//number depend from subtype (charclass, metasymbol dot or end symbol)
				case LEAF_CHARCLASS://normal number for charclass
					$node->number = ++$this->maxnum;
					$this->cconn[$this->maxnum] = $node->chars;
					break;
				case LEAF_END://STREND number for end leaf
					$node->number = STREND;
					break;
				case LEAF_METASYMBOLDOT://normal + DOT for dot leaf
					$node->number = ++$this->maxnum + DOT;
					break;
			}
		}
	}
	/**
	*Function determine: subtree with root in this node can give empty word or not.
	*@param node - node fo analyze
	*@return true if can give empty word, else false
	*/
	function nullable(&$node) {
		$result = false;
		if($node->type==NODE) {
			switch($node->subtype) {
				case NODE_ALT://alternative can give empty word if one operand can.
					$result = ($this->nullable($node->firop)||$this->nullable($node->secop));
					break;
				case NODE_CONC://concatenation can give empty word if both operands can.
					$result = ($this->nullable($node->firop)&&$this->nullable($node->secop));
					$this->nullable($node->secop);
					break;
				case NODE_ITER://iteration and question quantificator can give empty word without dependence from operand.
				case NODE_QUESTQUANT:
					$result = true;
					$this->nullable($node->firop);
					break;
				case NODE_ASSERTTF://assert can give empty word.
					$result = true;
					break;//operand of assert not need for main finite automate. It form other finite automate.
			}
		}
		$node->nullable = $result;//save result in node
		return $result;
	}
	function firstpos($node) {
		$result = array(0,0,0);
		return $result;
	}
	function lastpos($node) {
		$result = array(0,0,0);
		return $result;
	}
	function followpos($node, $fpmap) {
		$fpmap=array(
			array(0,0,0),
			array(0,0,0),
			array(0,0,0));
	}
	function buildfa() {//Начальное состояние ДКА сохраняется в поле finiteautomates[0][0] остальные состояния в прочих эл-тах этого массива,finiteautomate[!=0] - asserts' fa
	
	}
	function compare($string, $assertnumber) {//if main regex then assertnumber is 0
		$result = new compare_result;
		return $result;
	}
}
?>