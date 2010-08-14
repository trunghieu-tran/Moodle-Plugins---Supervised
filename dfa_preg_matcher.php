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

define('MAX_STATE_COUNT', 64);/* if you put large constant here, than big dfa will be
                                  correct, but big dfa will be build slow, if you small
                                  constant here dfa will must small, but complexy regex
                                  will be get error on validation.
                               */

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
    
    function name() {
        return 'dfa_preg_matcher';
    }


    /**
    *returns true for supported capabilities
    @param capability the capability in question
    @return bool is capanility supported
    */
    function is_supporting($capability) {
        switch($capability) {
        case preg_matcher::PARTIAL_MATCHING :
        case preg_matcher::NEXT_CHARACTER :
            return true;
            break;
        case preg_matcher::CHARACTERS_LEFT :
            return false;//We hope it'll be true some day
            break;
        }
        return false;
    }
    
    /**
    *Function validate regex, before built tree, it need for validation
    *@param $regex - regular expirience for validation
    *@return array of errors, if no error - return true.
    */
    static function validate($regex) {
        $errors = array();
        //building tree and dfa
        $matcher = new dfa_preg_matcher($regex);
        //validation tree
        $for_regexp=$regex;
        if (strpos($for_regexp,'/')!==false) {//escape any slashes
            $for_regexp=implode('\/',explode('/',$for_regexp));
        }
        $for_regexp='/'.$for_regexp.'/u';
        if (preg_match($for_regexp, 'something unimpotarnt') !== false) {
            $matcher->accept_tree($matcher->roots[0]);
        } else {
            $errors[0] = 'incorrectregex';
        }
        if (count($matcher->finiteautomates, COUNT_RECURSIVE) > MAX_STATE_COUNT) {
            $errors[1] = 'largedfa';
        }
        if (!$matcher->is_error_exists()) {
            return true;
        } else {
            return $matcher->get_errors();
        }
    }
    protected function accept_node($node) {
        switch ($node->subtype) {
            case LEAF_LINK:
                $this->errors[2] = 'link';
                return false;
            case NODE_SUBPATT:
                $this->errors[3] = 'subpattern';
                return false;
            case NODE_CONDSUBPATT:
                $this->errors[4] = 'condsubpatt';
                return false;
            case NODE_ASSERTTB:
                $this->errors[5] = 'asserttb';
                return false;
            case NODE_ASSERTFB:
                $this->errors[6] = 'assertfb';
                return false;
            case NODE_ASSERTFF:
                $this->errors[7] = 'assertff';
                return false;
            case NODE_QUESTQUANT:
            case NODE_ITER:
            case NODE_PLUSQUANT:
            case NODE_QUANT:
                if ($node->greed === false) {
                    $this->errors[8] = 'lazyquant';
                }
                return false;
        }
        return true;
    }
    /**
    *function form node with concatenation, first operand old root of tree, second operant leaf with sign of end regex (it match with end of string)
    *@param index - number of tree for adding end's leaf.
    */
    function append_end($index) {
        $root = $this->roots[$index];
        $this->roots[$index] = new node;
        $this->roots[$index]->type = NODE;
        $this->roots[$index]->subtype = NODE_CONC;
        $this->roots[$index]->firop = $root;
        $this->roots[$index]->secop = new node;
        $this->roots[$index]->secop->type = LEAF;
        $this->roots[$index]->secop->subtype = LEAF_END;
        $this->roots[$index]->secop->direction = true;
    }
    /**
    *Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
    *Put pair of number=>character to $this->connection[$index][].
    *@param $node current node (or leaf) for numerating.
    */
    function numeration($node, $index) {
        if ($node->type==NODE && $node->subtype == NODE_ASSERTTF) {//assert node need number
            $node->number = ++$this->maxnum + ASSERT;
        } else if ($node->type == NODE) {//not need number for not assert node, numerate operands
            $this->numeration($node->firop, $index);
            if ($node->subtype == NODE_CONC || $node->subtype == NODE_ALT) {//concatenation and alternative have second operand, numerate it.
                $this->numeration($node->secop, $index);
            }
        }
        if ($node->type==LEAF) {//leaf need number
            switch($node->subtype) {//number depend from subtype (charclass, metasymbol dot or end symbol)
                case LEAF_CHARCLASS://normal number for charclass
                    $node->number = ++$this->maxnum;
                    $this->connection[$index][$this->maxnum] = $node->chars;
                    break;
                case LEAF_END://STREND number for end leaf
                    $node->number = STREND;
                    break;
                case LEAF_METASYMBOLDOT://normal + DOT for dot leaf
                    $node->chars = 'METASYMBOL_DOT';
                    $node->number = ++$this->maxnum + DOT;
                    $this->connection[$index][$this->maxnum + DOT] = $node->chars;
                    break;
            }
        }
    }
    /**
    *Function determine: subtree with root in this node can give empty word or not.
    *@param node - node fo analyze
    *@return true if can give empty word, else false
    */
    static function nullable($node) {
        $result = false;
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT://alternative can give empty word if one operand can.
                    $result = (dfa_preg_matcher::nullable($node->firop) || dfa_preg_matcher::nullable($node->secop));
                    break;
                case NODE_CONC://concatenation can give empty word if both operands can.
                    $result = (dfa_preg_matcher::nullable($node->firop) && dfa_preg_matcher::nullable($node->secop));
                    dfa_preg_matcher::nullable($node->secop);
                    break;
                case NODE_ITER://iteration and question quantificator can give empty word without dependence from operand.
                case NODE_QUESTQUANT:
                    $result = true;
                    dfa_preg_matcher::nullable($node->firop);
                    break;
                case NODE_ASSERTTF://assert can give empty word.
                    $result = true;
                    break;//operand of assert not need for main finite automate. It form other finite automate.
            }
        }
        $node->nullable = $result;//save result in node
        return $result;
    }
    /**
    *function determine characters which can be on first position in word, which given subtree with root in this node
    *@param $node root of subtree giving word
    *@return numbers of characters (array)
    */
    static function firstpos($node) {
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge(dfa_preg_matcher::firstpos($node->firop), dfa_preg_matcher::firstpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = dfa_preg_matcher::firstpos($node->firop);
                    if ($node->firop->nullable) {
                        $result = array_merge($result, dfa_preg_matcher::firstpos($node->secop));
                    } else {
                        dfa_preg_matcher::firstpos($node->secop);
                    }
                    break;
                case NODE_QUESTQUANT:
                case NODE_ITER:
                    $result = dfa_preg_matcher::firstpos($node->firop);
                    break;
                case NODE_ASSERTTF:
                    $result = array($node->number);
                    break;
            }
        } else {
            if ($node->direction) {
                $result = array($node->number);
            } else {
                $result = array(-$node->number);
            }
        }
        $node->firstpos = $result;
        return $result;
    }
    /**
    *function determine characters which can be on last position in word, which given subtree with root in this node
    *@param $node - root of subtree
    *@return numbers of characters (array)
    */
    static function lastpos($node) {
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge(dfa_preg_matcher::lastpos($node->firop), dfa_preg_matcher::lastpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = dfa_preg_matcher::lastpos($node->secop);
                    if ($node->secop->nullable) {
                        $result = array_merge(dfa_preg_matcher::lastpos($node->firop), $result);
                    } else {
                        dfa_preg_matcher::lastpos($node->firop);
                    }
                    break;
                case NODE_ITER:
                case NODE_QUESTQUANT:
                    $result = dfa_preg_matcher::lastpos($node->firop);
                    break;
                case NODE_ASSERTTF:
                    $result = array($node->number);
                    break;
            }
        } else {
            if ($node->direction) {
                $result = array($node->number);
            } else {
                $result = array(-$node->number);
            }
        }
        $node->lastpos = $result;
        return $result;
    }
    /**
    *function determine characters which can follow characters from this node
    *@param node - current node
    *@param fpmap - map of following of characters
    */
    static function followpos($node, &$fpmap) {
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_CONC:
                    dfa_preg_matcher::followpos($node->firop, $fpmap);
                    dfa_preg_matcher::followpos($node->secop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        dfa_preg_matcher::push_unique($fpmap[$key], $node->secop->firstpos);
                    }
                    break;
                case NODE_ITER:
                    dfa_preg_matcher::followpos($node->firop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        dfa_preg_matcher::push_unique($fpmap[$key], $node->firop->firstpos);
                    }
                    break;
                case NODE_ALT:
                    dfa_preg_matcher::followpos($node->secop, $fpmap);
                case NODE_QUESTQUANT:
                    dfa_preg_matcher::followpos($node->firop, $fpmap);
                    break;
            }
        }
    }
    /**
    *function build determined finite automate, fa saving in $this->finiteautomates[$index], in $this->finiteautomates[$index][0] start state.
    *@param index number of assert (0 for main regex) for which building fa
    */
    function buildfa($index) {
        $this->maxnum = 0;//no one leaf numerated, yet.
        $this->finiteautomates[$index][0] = new finite_automate_state;
        //form the map of following
        $this->numeration($this->roots[$index], $index);
        dfa_preg_matcher::nullable($this->roots[$index]);
        dfa_preg_matcher::firstpos($this->roots[$index]);
        dfa_preg_matcher::lastpos($this->roots[$index]);
        dfa_preg_matcher::followpos($this->roots[$index], $map);
        $this->find_asserts($this->roots[$index]);
        //create start state.
        foreach ($this->roots[$index]->firstpos as $value) {
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
                    if ($follow<ASSERT) {
                        //if number less then ASSERT constant than this is character class, to passages it.
                        $newstate->passages[$follow] = -2;
                    } else {
                        //else this is number of assert
                        $this->finiteautomates[$index][$currentstateindex]->asserts[] = $follow;
                    }
                }
                if ($num!=STREND) {
                    //if this passage not point to end state
                    if ($this->state($newstate->passages, $index) === false && count($newstate->passages) != 0) {
                        //if fa hasn't other state matching with this and this state not empty
                        array_push($this->finiteautomates[$index], $newstate);//add it to fa's array
                        end($this->finiteautomates[$index]);
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = key($this->finiteautomates[$index]);
                    } else {
                        //else do passage point to state, which has in fa already
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = $this->state($newstate->passages, $index);
                    }
                } else {
                    //if this passage point to end state
                    $this->finiteautomates[$index][$currentstateindex]->passages[$num] = -1;
                }
                //end state is imagined and not match with real object, index -1 in array, which have zero and positive index only
            }
        }
    }
    /**
    *function compare regex and string, with using of finite automate builded of buildfa function
    *and determine match or not match string with regex, lenght of matching substring and character which can be on next position in string
    *@param string - string for compare with regex
    *@param assertnumber - number of assert with which string will compare, 0 for main regex
    *@return object with three property:
    *   1)index - index of last matching character (integer)
    *   2)full  - fullnes of matching (boolean)
    *   3)next  - next character (mixed, int(0) for end of string, else string with character which can be next)
    */
    function compare($string, $assertnumber) {//if main regex then assertnumber is 0
        $index = 0;//char index in string, comparing begin of first char in string
        $end = false;//current state is end state, not yet
        $full = true;//if string match with asserts
        $next = 0;// character can put on next position, 0 for full matching with regex string
        $maxindex = strlen($string);//string cannot match with regex after end, if mismatch with assert - index of last matching with assert character
        $currentstate = 0;//finite automate begin work at start state, zero index in array
        do {
        /*check current character while: 1)checked substring match with regex
                                         2)current character isn't end of string
                                         3)finite automate not be in end state
        */
            $maybeend = false;
            $found = false;//current character no accepted to fa yet
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            //finding positive character class with this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = false;
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //if character class number is positive (it's mean what character class is positive) and
                //current character is contain in character class
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                if ($key != STREND && $key < DOT && $index < strlen($string)) {
                    $found = ($key > 0 && strpos($this->connection[$assertnumber][$key], $string[$index]) !== false);
                }
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //finding metasymbol dot's passages, it accept any character.
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                $found = ($key > DOT && $index < strlen($string));
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            $foundkey = $key;
            //finding negative character class without this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                $found = ($key < 0 && strpos($this->connection[$assertnumber][abs($key)], $string[$index]) === false);
                if ($found) {
                    $foundkey = $key;
                } else {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            if (array_key_exists(STREND, $this->finiteautomates[$assertnumber][$currentstate]->passages)) {
            //if current character is end of string and fa can go to end state.
                if ($index == strlen($string)) { //must be end   
                    $found = true;
                    $foundkey = STREND;
                } elseif(count($this->finiteautomates[$assertnumber][$currentstate]->passages) == 1) {//must be end
                    $foundkey = STREND;
                }
                $maybeend = true;//may be end.
            }
            $index++;
            if (count($this->finiteautomates[$assertnumber][$currentstate]->asserts)) { // if there are asserts in this state
                foreach ($this->finiteautomates[$assertnumber][$currentstate]->asserts as $assert) {
                    $tmpres = $this->compare(substr($string, $index), $assert);//result of compare substring starting at next character with current assert
                    if ($tmpres->next !== 0) {
                    /* if string not match with assert then assert give borders
                       match string with regex can't be after mismatch with assert
                       p.s. string can match if it not end when assert end
                    */
                        $full = false;
                        if ($maxindex > $tmpres->index + $index) {
                            $next = $tmpres->next;
                            $maxindex = $tmpres->index + $index;
                        }
                    }
                }
            }
            //form results of check this character
            if ($found) { //if finite automate did accept this character
                $correct = true;
                if ($foundkey != STREND) {// if finite automate go to not end state
                    $currentstate = $this->finiteautomates[$assertnumber][$currentstate]->passages[$key];
                    $end = false;
                } else { 
                    $end = true;
                }
            } else {
                $correct = false;
            }
        } while($correct && !$end && $index <= strlen($string));//index - 1, becase index was incrimented
        //form result comparing string with regex
        $result = new stdClass;
        if ($index - 2 < $maxindex) {//if asserts not give border to lenght of matching substring
            $result->index = $index - 2;
            $assertrequirenext = false;
        } else {
            $result->index = $maxindex;
            $assertrequirenext = true;
        }
       if (strlen($string) == $result->index + 1 && $end && $full && $correct) {//if all string match with regex.
            $result->full = true;
        } else {
            $result->full = false;
        }
        if (($result->full || $maybeend || $end) && !$assertrequirenext) {//if string must be end on end of matching substring.
            $result->next = 0;
        //determine next character, which will be correct and increment lenght of matching substring.
        } elseif ($full && $index-2 < $maxindex) {//if assert not border next character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
            if ($key > 0 && $key < DOT) {//if positive character class
                $result->next = $this->connection[$assertnumber][$key][0];
                if($key > DOT && next($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) {
                    $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                    $result->next = $this->connection[$assertnumber][$key][0];
                }
            }elseif ($key > DOT) {
                $result->next = 'D';
            } else {
                for($c = 'a'; strpos($this->connection[$assertnumber][abs($key)], $c) !== false; $c++);//TODO: need better algorithm for determine next character in negative CC
                $result->next = $c;
            }
        } else {
            $result->next = $next;
        }
        return $result;
    }
    
    
    /**
    *function append array2 to array1, non unique values not add
    *@param arr1 - first array
    *@param arr2 - second array, which will appended to arr1
    */
    static function push_unique(&$arr1, $arr2) {// to static
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
    *function find asserts' nodes in tree and put link to root of it to $this->roots[<number of assert>]
    *@param node - current nod for recursive search
    */
    function find_asserts($node) {
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ASSERTTF:
                    $this->roots[$node->number] = $node->firop;
                    break;
                case NODE_ALT:
                case NODE_CONC:
                    $this->find_asserts($node->secop);
                case NODE_ITER:
                case NODE_QUESTQUANT:
                    $this->find_asserts($node->firop);
                    break;
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
    static function is_include_characters($string1, $string2) {// to static
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
        if ($number == STREND) {
            $res = array();
            return $res;
        }
        $str1 = $this->connection[$index][abs($number)];//for this charclass will found equivalent numbers
        $equnum = array();
        foreach ($this->connection[$index] as $num => $cc) {//forming vector of equivalent numbers
            $str2 = $cc;
            if (dfa_preg_matcher::is_include_characters($str1, $str2) && array_key_exists($num, $passages) && $number>0) {//if charclass 1 and 2 equivalenta and number exist in passages
                array_push($equnum, $num);
            } else if (dfa_preg_matcher::is_include_characters($str1, $str2) && array_key_exists(-$num, $passages) && $number<0) {
                array_push($equnum, -$num);
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
    /**
    *function copying subtree with root in this node
    *@param node - root of copying subtree
    *@return link to copy of subtree
    */
    static function copy_subtree($node) {
        $result = new node;
        $result->type = $node->type;
        $result->subtype = $node->subtype;
        $result->greed = $node->greed;
        $result->direction = $node->direction;
        $result->chars = $node->chars;
        if ($node->type == NODE) {
            $result->firop = dfa_preg_matcher::copy_subtree($node->firop);
            if ($node->subtype == NODE_ALT || $node->subtype == NODE_CONC) {
                $result->secop = dfa_preg_matcher::copy_subtree($node->secop);
            }
        }
        return $result;
    }
    /**
    *function convert the tree, replace operand+ on operandoperand*, operand{x,y} replace on x times of operand and x-y times of operand?
    *(operand|) replace on operand?, character class METASYMBOL_DOT replace on METASYBOLD_, because METASYMBOL_DOT is service word
    *param node - current node of converting tree
    */
    static function convert_tree($node) {
        if ($node->type == NODE) {
            switch ($node->subtype) {
                case NODE_PLUSQUANT:
                    dfa_preg_matcher::convert_tree($node->firop);
                    if ($node->firop->type == LEAF &&$node->firop->subtype == LEAF_EMPTY) {
                        $node->type = LEAF;
                        $node->subtype = LEAF_EMPTY;
                    } else {
                        $node->subtype = NODE_CONC;
                        $node->secop = new node;
                        $node->secop->type = NODE;
                        $node->secop->subtype = NODE_ITER;
                        $node->secop->firop = dfa_preg_matcher::copy_subtree($node->firop);
                    }
                    break;
                case NODE_QUANT:
                    dfa_preg_matcher::convert_tree($node->firop);
                    if ($node->firop->type == LEAF &&$node->firop->subtype == LEAF_EMPTY) {
                        $node->type = LEAF;
                        $node->subtype = LEAF_EMPTY;
                    } else {
                        $operand = dfa_preg_matcher::copy_subtree($node->firop);
                        if ($node->leftborder != 0) {
                            $count = $node->leftborder;
                            $currsubroot = $node->firop;
                            for ($i=1; $i<$count; $i++) {
                                $tmp = new node;
                                $tmp->type = NODE;
                                $tmp->subtype = NODE_CONC;
                                $tmp->firop = $currsubroot;
                                $tmp->secop = dfa_preg_matcher::copy_subtree($operand);
                                $currsubroot = $tmp;
                                
                            }
                            if ($node->leftborder < $node->rightborder) {
                                $tmp = new node;
                                $tmp->type = NODE;
                                $tmp->subtype = NODE_CONC;
                                $tmp->firop = $currsubroot;
                                $currsubroot = $tmp;
                                $tmp = new node;
                                $tmp->type = NODE;
                                $tmp->subtype = NODE_QUESTQUANT;
                                $tmp->firop = dfa_preg_matcher::copy_subtree($operand);
                                $operand = $tmp;
                                $currsubroot->secop = $tmp;
                            }
                        } else {
                            $currsubroot = new node;
                            $currsubroot->type = NODE;
                            $currsubroot->subtype = NODE_QUESTQUANT;
                            $currsubroot->firop = $operand;
                            $operand = $currsubroot;
                        }
                        if ($node->rightborder != -1) {
                            $count = $node->rightborder - $node->leftborder;
                            for ($i=1; $i<$count; $i++) {
                                $tmp = new node;
                                $tmp->type = NODE;
                                $tmp->subtype = NODE_CONC;
                                $tmp->firop = $currsubroot;
                                $tmp->secop = dfa_preg_matcher::copy_subtree($operand);
                                $currsubroot = $tmp;
                            }
                        } else {
                            $tmp = new node;
                            $tmp->type = NODE;
                            $tmp->subtype = NODE_CONC;
                            $tmp->firop = $currsubroot;
                            $tmp->secop = new node;
                            $tmp->secop->type = NODE;
                            $tmp->secop->subtype = NODE_ITER;
                            $tmp->secop->firop = dfa_preg_matcher::copy_subtree($operand);
                            $currsubroot = $tmp;
                        }
                        $node->subtype = $currsubroot->subtype;
                        $node->firop = $currsubroot->firop;
                        $node->secop = $currsubroot->secop;
                    }
                    break;
                case NODE_ALT:
                    if ($node->firop->type == LEAF &&$node->firop->subtype == LEAF_EMPTY) {
                        $node->subtype = NODE_QUESTQUANT;
                        $node->firop = $node->secop;
                        dfa_preg_matcher::convert_tree($node->firop);
                    } elseif ($node->secop->type == LEAF &&$node->secop->subtype == LEAF_EMPTY) {
                        $node->subtype = NODE_QUESTQUANT;
                        dfa_preg_matcher::convert_tree($node->firop);
                    }
                    dfa_preg_matcher::convert_tree($node->firop);
                    dfa_preg_matcher::convert_tree($node->secop);
                    break;
                case NODE_CONC:
                    dfa_preg_matcher::convert_tree($node->secop);
                default:
                    dfa_preg_matcher::convert_tree($node->firop);
                    break;
            }
        } elseif ($node->subtype == LEAF_CHARCLASS && $node->chars == 'METASYMBOL_DOT') {
            $node->chars = 'METASYBOLD_';//METASYMBOL_DOT is service word, METASYBOLD_ is equivalent character class.
        }  
    }

    /**
    *get regex and build finite automates
    @param regex - regular expirience for which will be build finite automate
    @param modifiers - modifiers of regular expression
    */
    function __construct($regex = null, $modifiers = null) {
        if (!isset($regex)) {//not build tree and dfa, if regex not given
            return;
        }
        parent::__construct($regex, $modifiers);
        $this->roots[0] = $this->ast_root;
        //building finite automates
        dfa_preg_matcher::convert_tree($this->roots[0]);
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
    function build_tree($regex) {
        parent::build_tree($regex);
        $this->roots[0] = $this->ast_root;
    }
    /**
    *function get string and compare it with regex
    *@param response - string which will be compared with regex
    *@return result of compring, see compare function for format of result
    */
    function match_inner($response) {
        $result = $this->compare($response, 0);
        $this->full = $result->full;
        $this->index = $result->index;
        $this->next = $result->next;
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
}
?>