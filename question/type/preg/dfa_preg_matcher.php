<?php //$Id: dfa_preg_matcher.php, v 0.1 beta 2010/08/08 23:47:35 dvkolesov Exp $

/**
 * Defines class dfa_preg_matcher
 *
 * @copyright &copy; 2010  Kolesov Dmitriy 
 * @author Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//fa - finite automate
//marked state, it's mean that the state is ready, all it's passages point to other states(marked and not marked), not marked state isn't ready, it's passages point to nothing.

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_nodes.php');

define('MAX_STATE_COUNT', 250);     //    if you put large constant here, than big dfa will be
define('MAX_PASSAGE_COUNT', 250);    //    correct, but big dfa will be build slow, if you small
                                     //    constant here dfa will must small, but complexy regex
                                     //    will be get error on validation

class finite_automate_state {//finite automate state
    var $asserts;
    var $passages;//contain numbers of state which can go from this
    var $marked;//if marked then true else false.
    
    function name() {
        return 'finite_automate_state';
    }
}

class dfa_preg_matcher extends preg_matcher {

    


    var $connection;//array, $connection[0] for main regex, $connection[<assert number>] for asserts
    var $roots;//array,[0] main root, [<assert number>] assert's root
    var $finiteautomates;
	var $maxnum;
    var $built;
    var $result;
    var $picnum;//number of last picture
    
    var $graphvizpath;//path to dot.exe of graphviz, used only for debugging
    
    public function name() {
        return 'dfa_preg_matcher';
    }


    /**
    *returns true for supported capabilities
    @param capability the capability in question
    @return bool is capanility supported
    */
    public function is_supporting($capability) {
        switch($capability) {
        case preg_matcher::PARTIAL_MATCHING :
        case preg_matcher::NEXT_CHARACTER :
        case preg_matcher::CHARACTERS_LEFT :
            return true;
            break;
        }
        return false;
    }
/*
    protected function accept_node($node) {
        switch ($node->type) {
            case preg_node::TYPE_LEAF_BACKREF:
            case preg_node::TYPE_NODE_COND_SUBPATT:
                $this->flags[$node->name()] = true;
                return false;
            case preg_node::TYPE_LEAF_RECURSION:
                $this->flags['leafrecursion'] = true;//TODO - add to parser, preg_nodes and strings file
                return false;
            case preg_node::TYPE_LEAF_OPTIONS:
                $this->flags['leafoptions'] = true;//TODO - add to parser, preg_nodes and strings file
                return false;
            default:
                $unsupported = $node->not_supported();//TODO - check that there is a preg_node there (not dfa_preg_node) and convert accept_node to do this job by itself without not_supported()
                if ($unsupported) {
                    $this->flags[$unsupported] = true;
                }
                break;//?? why return after break?
                return false;
        }
        return true;
    }*/

    /**
    *function form node with concatenation, first operand old root of tree, second operant leaf with sign of end regex (it match with end of string)
    *@param index - number of tree for adding end's leaf.
    */
    function append_end($index) {
        if ($index==0) {
            $root =& $this->roots[0];
        } else {
            $root =& $this->roots[$index]->pregnode->operands[0];
        }
        $oldroot = $root;
        $root = new preg_node_concat;
        $root->operands[1] = new preg_leaf_meta;
        $root->operands[1]->subtype = preg_leaf_meta::SUBTYPE_ENDREG;
        $root = $this->from_preg_node($root);
		$root->pregnode->operands[0] = $oldroot;
    }
    
    /**
    *function build determined finite automate, fa saving in $this->finiteautomates[$index], in $this->finiteautomates[$index][0] start state.
    *@param index number of assert (0 for main regex) for which building fa
    */
    function buildfa($index) {
		if ($index==0) {
            $root = $this->roots[0];
        } else {
            $root = $this->roots[$index]->pregnode->operands[0];
        }
        $statecount = 0;
        $passcount = 0;
        $this->maxnum = 0;//no one leaf numerated, yet.
        $this->finiteautomates[$index][0] = new finite_automate_state;
        //form the map of following
        $root->number($this->connection[$index], $this->maxnum);
        $root->nullable();
        $root->firstpos();
        $root->lastpos();
        $root->followpos($map);
        $root->find_asserts($this->roots);
        //create start state.
        foreach ($root->firstpos as $value) {
            $this->finiteautomates[$index][0]->passages[$value] = -2;
        }
        $this->finiteautomates[$index][0]->marked = false;//start state not marked, because not readey, yet
        //form the determined finite automate
        while ($this->not_marked_state($index) !== false) {
			//while has one or more not ready state.
            $currentstateindex = $this->not_marked_state($index);
            $this->finiteautomates[$index][$currentstateindex]->marked = true;//mark current state, because it will be ready on this step of loop
            //form not marked state for each passage of current state
            foreach ($this->finiteautomates[$index][$currentstateindex]->passages as $num => $passage) {
                $newstate = new finite_automate_state;
                $fpU = $this->followposU($num, $map, $this->finiteautomates[$index][$currentstateindex]->passages, $index);
                foreach ($fpU as $follow) {
                    if ($follow<dfa_preg_node_assert::ASSERT_MIN_NUM) {
                        //if number less then dfa_preg_node_assert::ASSERT_MIN_NUM constant than this is character class, to passages it.
                        $newstate->passages[$follow] = -2;
                    } else {
                        //else this is number of assert
                        $this->finiteautomates[$index][$currentstateindex]->asserts[] = $follow;
                    }
                }
                if ($this->connection[$index][$num]->pregnode->type === preg_node::TYPE_LEAF_META && 
                    $this->connection[$index][$num]->pregnode->subtype === preg_leaf_meta::SUBTYPE_ENDREG) {
                    //if this passage point to end state
                    //end state is imagined and not match with real object, index -1 in array, which have zero and positive index only
                    $this->finiteautomates[$index][$currentstateindex]->passages[$num] = -1;
                } else {
                    //if this passage not point to end state
                    if ($this->state($newstate->passages, $index) === false && count($newstate->passages) != 0) {
                        //if fa hasn't other state matching with this and this state not empty
                        array_push($this->finiteautomates[$index], $newstate);//add it to fa's array
                        end($this->finiteautomates[$index]);
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = key($this->finiteautomates[$index]);
                        $statecount++;
                    } else {
                        //else do passage point to state, which has in fa already
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = $this->state($newstate->passages, $index);
                    }
                }
                $passcount++;
                if (($passcount > MAX_PASSAGE_COUNT || $statecount > MAX_STATE_COUNT) && MAX_STATE_COUNT != 0 && MAX_PASSAGE_COUNT != 0) {
                    $this->errors[] = get_string('toolargedfa', 'qtype_preg');
                    return;
                }
            }
        }
		foreach ($this->finiteautomates[$index] as $key=>$state) {
			$this->del_double($this->finiteautomates[$index][$key]->passages, $index);
		}
		foreach ($this->finiteautomates[$index] as $key=>$state) {
			$this->unite_parallel($this->finiteautomates[$index][$key]->passages, $index);
		}
	}
    /**
    *function compare regex and string, with using of finite automate builded of buildfa function
    *and determine match or not match string with regex, lenght of matching substring and character which can be on next position in string
    *@param string - string for compare with regex
    *@param assertnumber - number of assert with which string will compare, 0 for main regex
    *@param offset - index of character in string which must be beginning for match
    *@param endanchor - if endanchor == false than string can continue after end of matching, else string must end on end of matching
    *@return object with three property:
    *   1)index - index of last matching character (integer)
    *   2)full  - fullnes of matching (boolean)
    *   3)next  - next character (mixed, int(0) for end of string, else string with character which can be next)
    */
    function compare($string, $assertnumber, $offset = 0, $endanchor = true) {//if main regex then assertnumber is 0
        $index = 0;//char index in string, comparing begin of first char in string
        $length = 0;//count of character matched with current leaf
        $end = false;//current state is end state, not yet
        $full = true;//if string match with asserts
        $next = 0;// character can put on next position, 0 for full matching with regex string
        $maxindex = strlen($string);//string cannot match with regex after end, if mismatch with assert - index of last matching with assert character
        $currentstate = 0;//finite automate begin work at start state, zero index in array
        $substringmatch = new stdClass;
        $substringmatch->full = false;
        $substringmatch->index = -1;
        $acceptedcharcount = -1;
        if (strpos($this->modifiers, 'i') === false) {
            $casesens=true;
        } else {
            $casesens=false;
        }
        do {
        /*check current character while: 1)checked substring match with regex
                                         2)current character isn't end of string
                                         3)finite automate not be in end state
        */
            $maybeend = false;
            $found = false;//current character no accepted to fa yet
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            //finding leaf with this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = false;
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //current character is contain in character class
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                if ($key != dfa_preg_leaf_meta::ENDREG && $offset + $index <= strlen($string)) {
                    $found = $this->connection[$assertnumber][$key]->pregnode->match($string, $offset + $index, &$length, $casesens);
                }
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            if ($found) {
                $foundkey = $key;
            }
            if (array_key_exists(dfa_preg_leaf_meta::ENDREG, $this->finiteautomates[$assertnumber][$currentstate]->passages)) {
            //if current character is end of string and fa can go to end state.
                if ($offset + $index == strlen($string)) { //must be end   
                    $found = true;
                    $foundkey = dfa_preg_leaf_meta::ENDREG;
                    $length = 0;
                } elseif(count($this->finiteautomates[$assertnumber][$currentstate]->passages) == 1) {//must be end
                    //$foundkey = dfa_preg_leaf_meta::ENDREG;
                    $length = 0;
                }
                $maybeend = true;//may be end.
                $substringmatch->full = true;
                $substringmatch->index = $index;
            }
            $index += $length;
            if ($found && $foundkey != dfa_preg_leaf_meta::ENDREG) {
                $acceptedcharcount += $length;
            }
            if (count($this->finiteautomates[$assertnumber][$currentstate]->asserts)) { // if there are asserts in this state
                foreach ($this->finiteautomates[$assertnumber][$currentstate]->asserts as $assert) {
                    $tmpres = $this->compare($string, $assert, $index+$offset, false);//result of compare substring starting at next character with current assert
                    $full = $tmpres->full && $full;
                    if (!$tmpres->full) {
                    /* if string not match with assert then assert give borders
                       match string with regex can't be after mismatch with assert
                       p.s. string can match if it not end when assert end
                    */
                        if ($maxindex >= $tmpres->index + $tmpres->offset) {
                            $next = $tmpres->next;
                            $maxindex = $tmpres->index + $tmpres->offset;
                        }
                    }
                }
            }
            //form results of check this character
            if ($found) { //if finite automate did accept this character
                $correct = true;
                if ($foundkey != dfa_preg_leaf_meta::ENDREG) {// if finite automate go to not end state
                    $currentstate = $this->finiteautomates[$assertnumber][$currentstate]->passages[$key];
                    $end = false;
                } else { 
                    $end = true;
                }
            } else {
                $correct = false;
            }
        } while($correct && !$end && $offset + $index <= strlen($string));
        //form result comparing string with regex
        $result = new stdClass;
        $result->offset = $offset;
        if ($full) {//if asserts not give border to lenght of matching substring
            $result->index = $acceptedcharcount;
            $assertrequirenext = false;
        } else {
            $result->index = $maxindex;
            $assertrequirenext = true;
        }
        if (strlen($string) == $result->index + 1 && $end && $full && $correct || $maybeend && !$endanchor && $full) {//if all string match with regex.
            $result->full = true;
        } elseif ($substringmatch->full && !$endanchor && $full) {
            $result->full = true;
            $result->index = $substringmatch->index -1;
        } else {
            $result->full = false;
        }
        if (($result->full || $maybeend || $end) && !$assertrequirenext) {//if string must be end on end of matching substring.
            $result->next = 0;
            $result->left = 0;
        //determine next character, which will be correct and increment lenght of matching substring.
        } elseif (!$assertrequirenext) {//if assert not border next character //$full && $offset + $index-1 < $maxindex && 
            $wres = $this->wave($currentstate, $assertnumber);
            $key = $wres->nextkey;
            $result->left = $wres->left;
            $result->next = $this->connection[$assertnumber][$key]->pregnode->character();
        } else {
            $result->next = $next;
            $wres = $this->wave($currentstate, $assertnumber);
            $result->left = $wres->left;
        }
        return $result;
    }
    
    /**
    *Function search for shortest way from current state to end state
    @param current - number of current state dfa
    @param assertnum - number of dfa for which do search
    @return number of state, which is first step on shortest way to end state and count of left character, as class
    */
    function wave($current, $assertnum) {
        //form start state of waves: start chars, current states of dfa and states of next step
        $i = 0;
        $left = 1;
        foreach ($this->finiteautomates[$assertnum][$current]->passages as $key => $passage) {
            if ($passage == -1) {
                $res = new stdClass;
                $res->nextkey = $key;
                $res->left = 0;
                return $res;
            }
            $front[$i] = new stdClass;
            $front[$i]->charnum = $key;
            $front[$i]->currentstep[] = $passage;
            $i++;
        }
        $found = false;
        while (!$found) {//while not found way to end state
            foreach ($front as $i => $curr) {//for each start char and it's subfront
                foreach ($curr->currentstep as $step) {//for all state if current subfront
                    foreach ($this->finiteautomates[$assertnum][$step]->passages as $passage) {//for all passage in this state
                        if ($passage != $step) {//if passage not to self
                            if ($passage == -1) {//if passage to end state
                                $found = true;
                                $result = new stdClass;
                                $result->left = $left;
                                $result->nextkey = $front[$i]->charnum;
                                return $result;
                            } else {
                                $front[$i]->nextstep[] = $passage;
                            }
                        }
                    }
                }
                $front[$i]->currentstep = $front[$i]->nextstep;
                $front[$i]->nextstep = array();
            }
            $left++;
        }
    }
    
    /**
    *function append array2 to array1, non unique values not add
    *@param arr1 - first array
    *@param arr2 - second array, which will appended to arr1
    *@param $index index of dfa for which do verify sybol unique
    */
    static protected function push_unique(&$arr1, $arr2) {// to static
        if (!is_array($arr1)) {
            $arr1 = array();
        }
        foreach ($arr2 as $value) {
            if (!in_array($value, $arr1)) {
                $arr1[] = $value;
            }
        }
    }
    /**
    *function delete repeat passages frm state of dfa
    *@param $array array of passages of state of dfa
    *@param $index index of dfa for which do verify sybol unique
    */
    protected function del_double(&$array, $index) {
        foreach ($array as $leaf=>$Passage) {
            foreach ($array as $member=>$passage) {//variable [Pp]assage not use, need only leaf and member
                $typeequ = $this->connection[$index][$member]->pregnode->type==$this->connection[$index][$leaf]->pregnode->type;
                $subtypeequ = $this->connection[$index][$member]->pregnode->subtype==$this->connection[$index][$leaf]->pregnode->subtype;
                $directionequ = $this->connection[$index][$member]->pregnode->negative==$this->connection[$index][$leaf]->pregnode->negative;
                if ($this->connection[$index][$member]->pregnode->type==preg_node::TYPE_LEAF_CHARSET && $this->connection[$index][$leaf]->pregnode->type==preg_node::TYPE_LEAF_CHARSET) {
                    $charsetequ = $this->connection[$index][$member]->pregnode->charset==$this->connection[$index][$leaf]->pregnode->charset;
                } else {
                    $charsetequ = true;
                }
                if ($leaf!=$member && $typeequ && $subtypeequ && $directionequ && $charsetequ) {
                    unset($array[$leaf]);
                }
            }
        }
    }
   /**
    *function unite parallel passages in dfa state
    *@param $array array of passages of state of dfa
    *@param $index index of dfa for which do verify sybol unique
    */
	protected function unite_parallel(&$array, $index) {
		foreach ($array as $key1=>$passage1) {
            foreach ($array as $key2=>$passage2) {
               if($passage1==$passage2 && $key1!=$key2) {
					$newleaf = preg_leaf_combo::get_unite($this->connection[$index][$key1]->pregnode, $this->connection[$index][$key2]->pregnode);
					$newleaf = $this->from_preg_node($newleaf);
					$this->connection[$index][++$this->maxnum] = $newleaf;
					$array[$this->maxnum] = $passage1;
					unset($array[$key1]);
					unset($array[$key2]);
			   }
            }
        }
	}
    /**
    *function search not marked state if finite automate, while one not marked state will be found, searching will be stopped.
    *@param index - number of automate
    *@return link to not marked state
    */
    function not_marked_state($index) {
        $notmarkedstate = false;
        $size = count($this->finiteautomates[$index]);
        for ($i = 0; $i < $size && $notmarkedstate === false; $i++) {
            if (!$this->finiteautomates[$index][$i]->marked) {
                $notmarkedstate = $i;
            }
        }
        return $notmarkedstate;
    }
    /**
    *function check: string1 include string2, or not include, without stock of sequence character
    *@param string1 - string which may contain string2 
    *@param string2 - string which may be included in string1
    *@return true if string1 include string2
    */
    static function is_include_characters($string1, $string2) {
        $result = true;
        $size = strlen($string2);
        for ($i = 0; $i < $size && $result; $i++) {
            if (strpos($string1, $string2[$i]) === false) {
                $result = false;
            }
        }
        return $result;
    }
    /**
    *function concatenate list of follow character for this number of character and other number match with character which mean this number
    *@param number - for this number will concatenate list of follow chars
    *@param fpmap - map of following characters
    *@param passages - passges of current state fa
    *@param index - number of assert (number of connection map if $this->connection array)
    *@return concatenated list of follow chars
    */
    function followposU($number, $fpmap, $passages, $index) {
        if ($this->connection[$index][$number]->pregnode->type == preg_node::TYPE_LEAF_META && 
            $this->connection[$index][$number]->pregnode->subtype == preg_leaf_meta::SUBTYPE_ENDREG) {
            $res = array();
            return $res;
        }
        $equnum = array();
        if ($this->connection[$index][$number]->pregnode->type == preg_node::TYPE_LEAF_CHARSET) {//if this leaf is character class
            $str1 = $this->connection[$index][$number]->pregnode->charset;//for this charclass will found equivalent numbers
            foreach ($this->connection[$index] as $num => $cc) {//forming vector of equivalent numbers
                if ($cc->pregnode->type == preg_node::TYPE_LEAF_CHARSET) {
                    $str2 = $cc->pregnode->charset;
                    $equdirection = $cc->pregnode->negative === $this->connection[$index][$number]->pregnode->negative; 
                    if (dfa_preg_matcher::is_include_characters($str1, $str2) && array_key_exists($num, $passages) && $equdirection) {//if charclass 1 and 2 equivalenta and number exist in passages
                        array_push($equnum, $num);
                    }
                }
            }
        } elseif ($this->connection[$index][$number]->pregnode->type == preg_node::TYPE_LEAF_META) {//if this leaf is metacharacter
            foreach ($this->connection[$index] as $num => $cc) {
                if ($cc->pregnode->type == preg_node::TYPE_LEAF_META && $cc->pregnode->subtype == $this->connection[$index][$number]->pregnode->subtype) {
                    array_push($equnum, $num);
                }
            }
        } elseif ($this->connection[$index][$number]->pregnode->type == preg_node::TYPE_LEAF_ASSERT) {//if this leaf is metacharacter
            foreach ($this->connection[$index] as $num => $cc) {
                if ($cc->pregnode->type == preg_node::TYPE_LEAF_ASSERT && $cc->pregnode->subtype == $this->connection[$index][$number]->pregnode->subtype) {
                    array_push($equnum, $num);
                }
            }
        }
        $followU = array();
        foreach ($equnum as $num) {//forming map of following numbers
            dfa_preg_matcher::push_unique($followU, $fpmap[$num]);
        }
        return $followU;
    }
    /**
    *function search state in fa
    *@param state - state which be finding
    *@param index - assert number (index in $this->finiteautomates array on which will be search)
    *@return false if state not found, else number of found state
    */
    function state($state, $index) {
        $passcount = count($state);
        $result = false;
        $fas = count($this->finiteautomates[$index]);
        for ($i=0; $i < $fas && $result === false; $i++) {
            $flag = true;
            if ($passcount != count($this->finiteautomates[$index][$i]->passages)) {
                $flag = false;
            }
            reset($state);
            reset($this->finiteautomates[$index][$i]->passages);
            for ($j=0; $flag && $j < $passcount; $j++) {
                if (key($state) != key($this->finiteautomates[$index][$i]->passages)) {
                    $flag = false;
                }
                next($state);
                next($this->finiteautomates[$index][$i]->passages);
            }
            if ($flag) {
                $result =$i;
            }
        }
        return $result;
    }
	public function build_tree($regex) {
		parent::build_tree($regex);
		$this->roots[0]= $this->dst_root;
	}
    /**
    *get regex and build finite automates
    @param regex - regular expirience for which will be build finite automate
    @param modifiers - modifiers of regular expression
    */
    function __construct($regex = null, $modifiers = null) {
        $this->picnum=0;
        $this->graphvizpath = 'C:\Program Files (x86)\Graphviz2.26.3\bin';//in few unit tests dfa_preg_matcher objects create without regex,
																		  //but dfa will be build later and need for drawing dfa may be
		if (!isset($regex)) {//not build tree and dfa, if regex not given
            return;
        }
        parent::__construct($regex, $modifiers);
        $this->roots[0] = $this->dst_root;//place dst root in engine specific place
        //building finite automates
        if ($this->is_error_exists()) {
            return;
        }
        $this->append_end(0);
        $this->buildfa(0);
        foreach ($this->roots as $key => $value) {
            if ($key) {
                $this->append_end($key);
                $this->buildfa($key);
            }
        }
        $this->built = true;
        return;
    }

    /**
    * DFA node factory
    * @param pregnode preg_node child class instance
    * @return corresponding dfa_preg_node child class instance
    */
    public function &from_preg_node($pregnode) {
        $name = $pregnode->name();
        switch ($name) {
            case 'node_finite_quant':
                $pregnode =& $this->convert_finite_quant($pregnode);
                break;
            case 'node_infinite_quant':
                $pregnode =& $this->convert_infinite_quant($pregnode);
                break;
            //TODO write dfa_preg_node_subpatt to process situations like subpattern inside subpattern
            case 'node_subpatt':
                $pregnode =& $pregnode->operands[0];
                break;
        }
        return parent::from_preg_node($pregnode);
    }

    /**
    * Returns prefix for engine specific classes
    */
    protected function nodeprefix() {
        return 'dfa';
    }

    /**
    * Function converts operand{} quantificator to operand and operand? combination
    * @param node node with {}
    * @return node subtree with ? 
    */
    protected function &convert_finite_quant($node) {
        if (!($node->leftborder==0 && $node->rightborder==1 || $node->leftborder==1 && $node->rightborder==1)) {
            $tmp = $node->operands[0];
            $subroot = new preg_node_concat;
            $subroot->operands[0] = $this->copy_preg_node($tmp);
            $subroot->operands[1] = $this->copy_preg_node($tmp);
            $count = $node->leftborder;
            for ($i=2; $i<$count; $i++) {
                $newsubroot = new preg_node_concat;
                $newsubroot->operands[0] = $subroot;
                $newsubroot->operands[1] = $this->copy_preg_node($tmp);
                $subroot = $newsubroot;
            }
            $tmp = new preg_node_finite_quant;
            $tmp->leftborder = 0;
            $tmp->rightborder = 1;
            $tmp->operands[0] = $node->operands[0];
            if ($node->leftborder == 0) {
                $subroot->operands[0] =& $this->copy_preg_node($tmp);
                $subroot->operands[1] =& $this->copy_preg_node($tmp);
                $count = $node->rightborder - 2;
            } else if ($node->leftborder == 1) {
                $subroot->operands[1] =& $this->copy_preg_node($tmp);
                $count = $node->rightborder - 2;
            } else {
                $count = $node->rightborder - $node->leftborder;
            }
            for ($i=0; $i<$count; $i++) {
                $newsubroot = new preg_node_concat;
                $newsubroot->operands[0] = $subroot;
                $newsubroot->operands[1] =& $this->copy_preg_node($tmp);
                $subroot = $newsubroot;
            }
            return $subroot;
        }
        return $node;
    }

    /**
    * Function convert operand{} quantificater to operand, operand? and operand* combination
    * @param node node with {}
    * @return node subtree with ? *
    */
    protected function &convert_infinite_quant($node) {
        if ($node->leftborder == 0) {
            return $node;
        } else if ($node->leftborder == 1) {
            $tmp = $node->operands[0];
            $subroot = new preg_node_concat;
            $subroot->operands[0] =& $this->copy_preg_node($tmp);
            $subroot->operands[1] =& $this->copy_preg_node($node);
            $subroot->operands[1]->leftborder = 0;
        } else {
            $tmp = $node->operands[0];
            $subroot = new preg_node_concat;
            $subroot->operands[0] =& $this->copy_preg_node($tmp);
            $subroot->operands[1] =& $this->copy_preg_node($tmp);
            $count = $node->leftborder;
            for ($i=2; $i<$count; $i++) {
                $newsubroot = new preg_node_concat;
                $newsubroot->operands[0] = $subroot;
                $newsubroot->operands[1] =& $this->copy_preg_node($tmp);
                $subroot = $newsubroot;
            }
            $newsubroot = new preg_node_concat;
            $newsubroot->operands[0] =& $this->copy_preg_node($subroot);
            $newsubroot->operands[1] =& $this->copy_preg_node($node);
            $newsubroot->operands[1]->leftborder = 0;
            $subroot = $newsubroot;
        }
        return $subroot;
    }

    /**
    *function get string and compare it with regex
    *@param response - string which will be compared with regex
    *@return result of compring, see compare function for format of result
    */
    function match_inner($response) {
        if ($this->anchor->start) {
            $result = $this->compare($response, 0, 0, $this->anchor->end);
        } else {
            $result = new stdClass;
            $result->full = false;
            $result->index = -1;
            $result->left = 999999;
            for ($i=0; $i<strlen($response) && !$result->full; $i++) {
                $tmpres = $this->compare($response, 0, $i, $this->anchor->end);
                if ($tmpres->full || $tmpres->left < $result->left || !isset($result->next)) {
                    $result = $tmpres;
                }
            }
        }

        $this->is_match =  ($result->index > -1);
        $this->full = $result->full;
        $this->index_first[0] = $result->offset;
        $this->index_last[0] = $result->index+$result->offset;
		if ($result->index==-1) {
			$this->index_last[0]=-1;
		}
        if ($result->next === 0) {
            $this->next = '';
        } else {
            $this->next = $result->next;
        }
        $this->left = $result->left;
        return;
    }
    /**
    *@return list of supported operation as array of string
    */
    static function list_of_supported_operations_and_operands() {
        $result = array(
                        'character                                  - a',
                        'character class                            - [abc][a-c] and other formats of CC',
                        'negative character class                   - [^abc] ...',
                        'character class in \w\W\d\D\s\S\t format',
                        'empty                                      - something|',
                        'metasymbol dot                             - .',
                        'concatenation',
                        'alternative                                - ab|cd',
                        'greed iteration                            - a*',
                        'greed quantificator plus                   - a+',
                        'greed quantificator in curly               - a{15,137}',
                        'greed question quantificator               - a?',
                        'true forward assert                        - (?=...)',
                        'grouping                                   - (?:...)'
                       );
        return $result;               
    }
    public function print_connection($index) {
        foreach ($this->connection[$index] as $num=>$leaf) {
            echo 'number: ', $num, '</br>';
            $leaf->print_self(0);
            echo '</br>';
        }
    }
    /**
    * Debug function draw finite automate with number number in human readable form
    * don't work without right to execute file
    * @param number number of drawing finite automate
    */
    public function draw_fa ($number) {
        $fadotcode = $this->generate_fa_dot_code($number);
        $dotfile = fopen('C:/dotfile/dotcode.dot', 'w');
        foreach ($fadotcode as $fadotstring) {
            fprintf($dotfile, "%s\n", $fadotstring);
        }
        chdir($this->graphvizpath);
        exec('dot.exe -Tjpg -o"W:/home/moodle.local/www/question/type/preg/ZZZdfagraph'.$this->picnum.'.jpg" -Kdot C:/dotfile/dotcode.dot');
        echo '<IMG src="http://moodle.local/question/type/preg/ZZZdfagraph'.$this->picnum.'.jpg" width="90%"><br><br><br>';
        $this->picnum++;
		fclose($dotfile);
    }
    /**
    * Debug function generate dot code for drawing finite automate
    * @param number number of drawing finite automate
    */
    protected function generate_fa_dot_code($number) {
        $dotcode = array();
        $dotcode[] = 'digraph {';
        $dotcode[] = 'rankdir = LR;';
        foreach ($this->finiteautomates[$number] as $index=>$state) {
            foreach ($state->passages as  $leafcode=>$target) {
                $symbol = $this->connection[$number][$leafcode]->pregnode->tohr();
                if ($target==-2) {
                    $target = '"Not build yet."';
                } elseif ($target==-1) {
                    $target = '"End state."';
                }
                $dotcode[] = "$index->$target"."[label=\"$symbol\"];";
            }
        }
        $dotcode[] = '};';
        return $dotcode;
    }
}
?>