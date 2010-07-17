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


class node{
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
	
	function name(){
		return 'node';
	}
}

class fas{//finite automate state
	var $asserts;
	var $passages;//хранит номера состояний к которым перейти
}

class reasc{
	var $connection;//array, $connection[0] for main regex, $connection[<assert number>] for asserts
	var $cconn;//for current connection
	var $roots;//array,[0] main root, [assert number] assert's root
	var $croot;//for current root
	var $maxnum;
	var $finiteautomate;// for current finite  automate
	var $assertautomates;
	
	function name(){
		return 'reasc';
	}
	function append_end(){
	}
	function numeration($node){
		return -1;
	}
	function nullable($node){
		return true;
	}
	function firstpos($node){
		$result = array(0,0,0);
		return $result;
	}
	function lastpos($node){
		$result = array(0,0,0);
		return $result;
	}
	function followpos($node, $fpmap){
		$fpmap=array(
			array(0,0,0),
			array(0,0,0),
			array(0,0,0));
	}
	function buildfa(){//Начальное состояние ДКА сохраняется в поле finiteautomate[0] остальные состояния в прочих эл-тах этого массива
	}
}
?>