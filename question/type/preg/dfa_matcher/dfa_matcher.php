<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines DFA matcher class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//marked state, it means that the state is ready, all its transitions point to other states(marked and not marked), not marked state isn't ready, its transitions point to nothing.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_nodes.php');

class finite_automate_state {//finite automate state
    var $asserts;
    var $passages;//contain numbers of state which can go from this
    var $marked;//if marked then true else false.

    function name() {
        return 'finite_automate_state';
    }
}

class fptab {//member of follow's map table, use on merge time only
    public $number;
    public $inaccessible;
    //arrays, use member with identically indexes
    public $aindex;//index of symbol in assert's connection
    public $mindex;//index of symbol in main's connection
    //reference
    public $leaf;//contain leaf, which be on crossing of assert's and main's leaf
    public function __construct() {
        $this->inaccessible = true;
        $this->aindex = array();
        $this->mindex = array();
    }
}

class qtype_preg_dfa_matcher extends qtype_preg_matcher {




    var $connection;//array, $connection[0] for main regex, $connection[<assert number>] for asserts
    var $roots;//array,[0] main root, [<assert number>] assert's root
    var $finiteautomates;
    var $maxnum;
    var $built;
    var $result;
    var $picnum;//number of last picture
    protected $map;//map of symbol's following
    protected $statelimit;
    protected $transitionlimit;
    protected $zeroquantdeleted;

    public function name() {
        return 'dfa_matcher';
    }

    protected function node_infix() {
        return 'dfa';
    }

    /**
     *returns true for supported capabilities
     *@param capability the capability in question
     *@return bool is capanility supported
     */
    public function is_supporting($capability) {
        switch($capability) {
        case qtype_preg_matcher::PARTIAL_MATCHING :
        case qtype_preg_matcher::CORRECT_ENDING :
        case qtype_preg_matcher::CHARACTERS_LEFT :
            return true;
        }
        return false;
    }

    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
        case qtype_preg_node::TYPE_LEAF_CHARSET:
        case qtype_preg_node::TYPE_LEAF_META:
        case qtype_preg_node::TYPE_LEAF_ASSERT:
        case qtype_preg_node::TYPE_NODE_ERROR:
            return true;
        }
        return get_string($pregnode->type, 'qtype_preg');
    }

    /**
    *function form node with concatenation, first operand old root of tree, second operant leaf with sign of end regex
    *@param index - number of tree for adding end's leaf.
    */
    function append_end($index) {
        $endreg = new qtype_preg_leaf_meta;
        $endreg->subtype = qtype_preg_leaf_meta::SUBTYPE_ENDREG;

        $root = $this->from_preg_node(new qtype_preg_node_concat);
        $root->operands[0] = $this->roots[0];
        $root->operands[1] = $this->from_preg_node($endreg);
        $this->roots[0] = $root;
        /*
        $lastindex = count($this->roots[0]);
        $this->roots[0]->operands[$lastindex] = new qtype_preg_leaf_meta;
        $this->roots[0]->operands[$lastindex]->subtype = qtype_preg_leaf_meta::SUBTYPE_ENDREG;
        $this->roots[0]->operands[$lastindex] = $this->from_preg_node($this->roots[0]->operands[$lastindex]);
        */
    }

    /**
    *function build determined finite automate, fa saving in $this->finiteautomates[$index], in $this->finiteautomates[$index][0] start state.
    *@param index number of assert (0 for main regex) for which building fa
    */
    function buildfa($index=0) {
        if ($index==0) {
            $root = $this->roots[0];
        } else {
            $root = $this->roots[$index]->operands[0];
        }
        $statecount = 0;
        $passcount = 0;
        $this->maxnum = 0;//no one leaf numerated, yet.
        $this->finiteautomates[$index][0] = new finite_automate_state;
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
                $statecount++;
                $fpU = $this->followposU($num, $this->map[0], $this->finiteautomates[$index][$currentstateindex]->passages, $index);
                foreach ($fpU as $follow) {
                    if ($follow < qtype_preg_dfa_node_assert::ASSERT_MIN_NUM) {
                        //if number less then dfa_preg_node_assert::ASSERT_MIN_NUM constant than this is character class, to passages it.
                        $newstate->passages[$follow] = -2;
                        $passcount++;
                    }
                }
                if ($this->connection[$index][$num]->pregnode->type === qtype_preg_node::TYPE_LEAF_META &&
                    $this->connection[$index][$num]->pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_ENDREG) {
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
                    } else {
                        //else do passage point to state, which has in fa already
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = $this->state($newstate->passages, $index);
                    }
                }
                if (($passcount > $this->transitionlimit || $statecount > $this->statelimit) && $this->statelimit != 0 && $this->transitionlimit != 0) {
                    $this->errors[] = get_string('too_large_fa', 'qtype_preg');
                    return;
                }
            }
        }
        /*
        foreach ($this->finiteautomates[$index] as $key=>$state) {
            $this->del_double($this->finiteautomates[$index][$key]->passages, $index);
        }*/
    }

    public function match_from_pos($str, $offset) {
        $result = $this->compare($str, 0, $offset, false);
        if ($result===false) {
            $errres = new qtype_preg_matching_results(false, array(0), array(0), qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, null);
            $errres->set_source_info(new qtype_poasquestion_string(''), $this->get_max_subexpr(), $this->get_subexpr_map());
            return $errres;
        }
        $extstr = substr($str, 0, $result->offset + $result->index+1);
        if ($result->next===0) {
        } else {
            if ($result->next=='stringstart') {
                $extstr = '';
            } elseif ($result->next=='stringend' || $result->next=='notstringstart' || $result->next=='notstringend') {
            } elseif ($result->next=='wordchar') {
                $tmpflag = new qtype_preg_charset_flag;
                $tmpflag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SLASH_W);
                $tmp = new qtype_preg_leaf_charset;
                $tmp->flags = array(array($tmpflag));
                $length=1;
                $extstr .= $tmp->next_character($extstr, $offset+$result->index, $length);
            } elseif ($result->next=='notwordchar') {
                $tmpflag = new qtype_preg_charset_flag;
                $tmpflag->set_data(qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SLASH_W);
                $tmpflag->negative = true;
                $tmp = new qtype_preg_leaf_charset;
                $tmp->flags = array(array($tmpflag));
                $length=1;
                $extstr .= $tmp->next_character($extstr, $offset+$result->index, $length);
            } else {
                $extstr .= $result->next;
            }
        }
        if (!is_object($extstr)) {
               $extstr = new qtype_poasquestion_string($extstr);
        }
        if ($result->full) {
            $extmatch = null;
        } else {
            $ext=$result;
            $extmatch = new qtype_preg_matching_results($ext->full, array($ext->offset), array($ext->index+1), $ext->left-1, null);
            $extmatch->set_source_info($extstr, $this->get_max_subexpr(), $this->get_subexpr_map());
        }
        $res = new qtype_preg_matching_results($result->full, array($result->offset), array($result->index+1), $result->left, $extmatch);
        $res->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_map());
        return $res;
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
 *   If matching is impossible, return bool(false)
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
        $ismatch = false;
        $laststates = array();//array of states without changing index

        $casesens = !$this->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_CASELESS);

        do {
        /*check current character while: 1)checked substring match with regex
                                         2)current character isn't end of string
                                         3)finite automate not be in end state
        */
            $maybeend = false;
            $found = false;//current character no accepted to fa yet
            $afound = false;
            $akey = false;
            $mfound = false;
            $mkey = false;
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            //finding leaf with this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = false;
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //current character is contain in character class
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                if ($key != qtype_preg_dfa_leaf_meta::ENDREG && $offset + $index <= strlen($string)) {
                    $found = $this->connection[$assertnumber][$key]->pregnode->match($string, $offset + $index, $length, $casesens);
                }
                if ($found && $this->connection[$assertnumber][$key]->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {
                    $mfound = true;
                    $mkey = $key;
                    $found  = false;
                }
                if ($found && $this->connection[$assertnumber][$key]->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
                    $afound = true;
                    $akey = $key;
                    $found  = false;
                }
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            if (!$found && $mfound) {
                $found = true;
                $key = $mkey;
                $length = 1;
            }
            if (!$found && $afound) {
                $found = true;
                $key = $akey;
                $length = 0;
            }
            if ($found) {
                $foundkey = $key;
                $ismatch = true;
            }
            if (isset($this->finiteautomates[$assertnumber][$currentstate]->passages[qtype_preg_dfa_leaf_meta::ENDREG])) {
            //if current character is end of string and fa can go to end state.
                if ($offset + $index == strlen($string)) { //must be end
                    $found = true;
                    $foundkey = qtype_preg_dfa_leaf_meta::ENDREG;
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
            if ($found && $foundkey != qtype_preg_dfa_leaf_meta::ENDREG) {
                $acceptedcharcount += $length;
            }
            //form results of check this character
            if ($found) { //if finite automate did accept this character
                $correct = true;
                if ($foundkey != qtype_preg_dfa_leaf_meta::ENDREG) {// if finite automate go to not end state
                    if ($length == 0) {
                        foreach ($laststates as $state) {
                            if ($state == $currentstate) {
                                return false;
                            }
                        }
                        $laststates[] = $currentstate;
                    } else {
                        $laststates = array();
                    }
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
            $result->next = $this->connection[$assertnumber][$key]->pregnode->next_character($string, $result->index);
        } else {
            $result->next = $next;
            $wres = $this->wave($currentstate, $assertnumber);
            $result->left = $wres->left;
        }
        $result->ismatch = $ismatch;
        return $result;
    }

    /**
     *Function search for shortest way from current state to end state
     *@param current - number of current state dfa
     *@param assertnum - number of dfa for which do search
     *@return number of state, which is first step on shortest way to end state and count of left character, as class
     */
    function wave($current, $assertnum) {
        //form start state of waves: start chars, current states of dfa and states of next step
        $i = 0;
        $left = 1;
        foreach ($this->finiteautomates[$assertnum][$current]->passages as $key => $passage) {
            $endafterassert = false;
            if ($this->connection[$assertnum][$key]->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
                foreach ($this->finiteautomates[$assertnum][$passage]->passages as $secondpass) {
                    if ($secondpass == -1) {
                        $endafterassert = true;
                    }
                }
            }
            if ($passage == -1 || $endafterassert) {
                $res = new stdClass;
                $res->nextkey = $key;
                $res->left = 0;
                return $res;
            }
            $front[$i] = new stdClass;
            $front[$i]->charnum = $key;
            $front[$i]->currentstep[] = $passage;
            $front[$i]->assertinpath = 0;
            $i++;
        }
        $found = false;
        $counter = 0;
        while (!$found) {//while not found way to end state
            foreach ($front as $i => $curr) {//for each start char and it's subfront
                if ($counter > 10000) {
                //TODO set wave error flag!
                    $res = new stdClass;
                    $res->nextkey = '1';
                    $res->left = 0;
                    return $res;
                    return new stdClass;
                } else {
                    $counter++;
                }
                foreach ($curr->currentstep as $step) {//for all state if current subfront
                    foreach ($this->finiteautomates[$assertnum][$step]->passages as $passkey => $passage) {//for all passage in this state
                        if ($counter > 1000000) {
                        //TODO set wave error flag!
                            $res = new stdClass;
                            $res->nextkey = '1';
                            $res->left = 0;
                            return $res;
                            return new stdClass;
                        } else {
                            $counter++;
                        }
                        if ($passage != $step) {//if passage not to self
                            $endafterassert = false;
                            if ($this->connection[$assertnum][$passkey]->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
                                foreach ($this->finiteautomates[$assertnum][$passage]->passages as $secondpass) {
                                    if ($secondpass == -1) {
                                        $endafterassert = true;
                                    }
                                }
                            }
                            if ($passage == -1 || $endafterassert) {//if passage to end state
                                $found = true;
                                $result = new stdClass;
                                $result->left = $left - $front[$i]->assertinpath;
                                $result->nextkey = $front[$i]->charnum;
                                return $result;
                            } else if ($this->connection[$assertnum][$passkey]->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
                                foreach ($this->finiteautomates[$assertnum][$passage]->passages as $secondpass) {
                                    $front[$i]->nextstep[] = $secondpass;
                                }
                            } else {
                                $front[$i]->nextstep[] = $passage;
                            }
                        }
                    }
                }
                if (!isset($front[$i]->nextstep)) {
                         throw new Exception("wave error on regex: $this->regex\n");
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
    static protected function push_unique(&$arr1, $arr2) {
        if (!is_array($arr1)) {
            $arr1 = array();
        }
        if (is_array($arr2)) {//TODO: why at some time $arr2 isn't array?
            foreach ($arr2 as $value) {
                if (!in_array($value, $arr1)) {
                    $arr1[] = $value;
                }
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
                if ($this->connection[$index][$member]->pregnode->type==qtype_preg_node::TYPE_LEAF_CHARSET && $this->connection[$index][$leaf]->pregnode->type==qtype_preg_node::TYPE_LEAF_CHARSET) {
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
    *function concatenate list of follow character for this number of character and other number match with character which mean this number
    *@param number - for this number will concatenate list of follow chars
    *@param fpmap - map of following characters
    *@param passages - passges of current state fa
    *@param index - number of assert (number of connection map if $this->connection array)
    *@return concatenated list of follow chars
    */
    function followposU($number, $fpmap, $passages, $index) {
        if ($this->connection[$index][$number]->pregnode->type == qtype_preg_node::TYPE_LEAF_META &&
            $this->connection[$index][$number]->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_ENDREG) {
            $res = array();
            return $res;
        }
        $equnum = array();
        if ($this->connection[$index][$number]->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET) {//if this leaf is character class
            foreach ($this->connection[$index] as $num => $cc) {
                if ($cc->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET) {

                    //$this->connection[$index][$number]->pregnode->push_negative();
                    if (isset($passages[$num]) && $this->connection[$index][$number]->pregnode->is_include($cc->pregnode)) {
                        array_push($equnum, $num);
                    }
                }
            }
        } elseif ($this->connection[$index][$number]->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {//if this leaf is metacharacter
            echo '<BR>'.'LEAF_META used:'.$this->connection[$index][$number]->pregnode->type.'<BR>';
            foreach ($this->connection[$index] as $num => $cc) {
                if ($cc->pregnode->type == qtype_preg_node::TYPE_LEAF_META && $cc->pregnode->subtype == $this->connection[$index][$number]->pregnode->subtype && isset($passages[$num])) {
                    array_push($equnum, $num);
                } /*else if ($cc->pregnode->type == qtype_preg_node::TYPE_LEAF_META && $cc->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_DOT && isset($passages[$num])) {
                    array_push($equnum, $num);
                }*/
            }
        } elseif ($this->connection[$index][$number]->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT) {//if this leaf is assert
            foreach ($this->connection[$index] as $num => $cc) {
                if ($cc->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT && $cc->pregnode->subtype == $this->connection[$index][$number]->pregnode->subtype && isset($passages[$num])) {
                    array_push($equnum, $num);
                }
            }
        }
       /* $equnum = array_unique($equnum, SORT_NUMERIC);*/
        $followU = array();
        foreach ($equnum as $num) {//forming map of following numbers
            qtype_preg_dfa_matcher::push_unique($followU, $fpmap[$num]);
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
            if(is_array($state)) {//TODO: why at some time state==null?
                reset($state);
            }
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
     *get regex and build finite automates
     * @param regex - regular expirience for which will be build finite automate
     * @param options - options of regular expression
     */
    public function __construct($regex = null, $options = null) {
        global $CFG;

        if (!isset($regex)) {
            return;
        }

        $this->picnum = 0;
        $this->zeroquantdeleted = false;
        $this->statelimit = 250;
        $this->transitionlimit = 250;
        if (isset($CFG->qtype_preg_dfa_state_limit)) {
            $this->statelimit = $CFG->qtype_preg_dfa_state_limit;
        }
        if (isset($CFG->qtype_preg_dfa_transition_limit)) {
            $this->transitionlimit = $CFG->qtype_preg_dfa_transition_limit;
        }

        // Call the parent constructor.
        if ($options === null) {
            $options = new qtype_preg_matching_options();
        }
        $options->expandquantifiers = true;
        parent::__construct($regex, $options);

        $bigquant = $this->parser->get_max_finite_quant_borders_difference();
        if ($bigquant > qtype_preg_dfa_node_finite_quant::MAX_SIZE) {
            $this->errors[] = get_string('too_large_fa', 'qtype_preg');
        }

        $this->roots[0] = $this->dst_root;//place dst root in engine specific place
        // Build finite automata
        if ($this->errors_exist()) {
            return;
        }
        $this->append_end(0);
        //form the followpos map
        $this->roots[0]->number($this->connection[0], $this->maxnum);
        $this->roots[0]->nullable();
        $this->roots[0]->firstpos();
        $this->roots[0]->lastpos();
        $this->roots[0]->followpos($this->map[0]);
        //$this->split_leafs(0);
        $this->prepare_map(0);
        $this->roots[0]->find_asserts($this->roots);
        foreach ($this->roots as $key => $value) {
            if ($key!=0) {
                //TODO: use subtype of assert, when few subtype will be supported.
                $this->roots[$key] = $this->roots[$key]->operands[0];
                $this->append_end($key);
                $this->roots[$key]->number($this->connection[$key], $this->maxnum);
                $this->roots[$key]->nullable();
                $this->roots[$key]->firstpos();
                $this->roots[$key]->lastpos();
                $this->roots[$key]->followpos($this->map[$key]);
                //$this->split_leafs($key);
                $this->merge_fp_maps($key);
            }
        }
        $this->buildfa();
        $this->built = true;
        return;
    }
    protected function prepare_map($index) {
        foreach ($this->connection[$index] as $i=>$leaf1) {
            foreach ($this->connection[$index] as $j=>$leaf2) {
                if (is_a($leaf1, 'qtype_preg_dfa_leaf_charset') && is_a($leaf2, 'qtype_preg_dfa_leaf_charset') && $leaf1->pregnode->is_part_ident($leaf2->pregnode)) {
                    $leaf1and2 = $leaf1->pregnode->intersect($leaf2->pregnode);
                    $leaf1not2 = $leaf1->pregnode->substract($leaf2->pregnode);
                    $leaf2not1 = $leaf2->pregnode->substract($leaf1->pregnode);
                    $num1and2 = $this->save_new_leaf($index, $leaf1and2);
                    $num2and1 = $this->save_new_leaf($index, $leaf1and2);
                    $num1not2 = $this->save_new_leaf($index, $leaf1not2);
                    $num2not1 = $this->save_new_leaf($index, $leaf2not1);
                    $this->replace_num_in_map($index, $i, $num1and2, $num1not2);
                    $this->replace_num_in_map($index, $j, $num2and1, $num2not1);
                    unset($this->connection[$index][$i]);
                    unset($this->connection[$index][$j]);
                }
            }
        }
        foreach ($this->connection[$index] as $i=>$leaf) {
            if ($leaf->pregnode->type==qtype_preg_node::TYPE_LEAF_CHARSET && $leaf->pregnode->flags===null) {
                unset($this->connection[$index][$i]);
            }
        }
        foreach ($this->map[$index] as $ccn=>$follows) {
            if ($this->connection[$index][$ccn]===null) {
                unset($this->map[$index][$ccn]);
            } else {
                foreach ($follows as $j=>$leafnum) {
                    if ($this->connection[$index][$leafnum]===null) {
                        unset($this->map[$index][$j]);
                    }
                }
            }
        }
    }
    /**
    * Function merge simple assert in map of character following
    * @param $num number of map for merging simple asserts
    */
    protected function split_leafs($num) {
        foreach ($this->map[$num] as $prev => $arrnext) {
            foreach ($arrnext as $i => $first) {
                foreach ($arrnext as $j => $second) {
                    if (self::is_leafs_part_match($this->connection[$num][$first], $this->connection[$num][$second]) && isset($this->map[$num][$first]) && isset($this->map[$num][$second])) {
                        $arrres = $this->get_unequ_leafs($this->connection[$num][$first], $this->connection[$num][$second]);//get unique or equivalent leaf, but not partial equivalent with diff
                        $firstnexts = $this->map[$num][$first];
                        $secondnexts = $this->map[$num][$second];
                        unset ($this->map[$num][$i]);
                        unset ($this->map[$num][$j]);
                        foreach ($arrres as $key => $newleaf) {
                            $arrresnumbers[$key] = $this->save_new_leaf($num, $newleaf);
                        }
                        $this->map[$num][$arrresnumbers[0]] = $firstnexts;
                        $this->map[$num][$arrresnumbers[1]] = $firstnexts;
                        $this->map[$num][$arrresnumbers[2]] = $secondnexts;
                        $this->map[$num][$arrresnumbers[3]] = $secondnexts;
                        $this->replace_num_in_map($num, $first, $arrresnumbers[0], $arrresnumbers[1]);
                        $this->replace_num_in_map($num, $second, $arrresnumbers[2], $arrresnumbers[3]);
                    }
                }
            }
        }
        foreach ($this->map[$num] as $prev => $arrnext) {
            if (!is_array($arrnext))
                $this->map[$num][$prev] = array();
            }
    }
    /**
    * Function verify is two leaf partial match
    * partial match mean, that not equivalent, but can match with one character
    * @param $first first leaf
    * @param $second second leaf
    * @return is partial match
    */
    static protected function is_leafs_part_match($first, $second) {
        if ($first->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT || $second->pregnode->type == qtype_preg_node::TYPE_LEAF_ASSERT ||
            $first->pregnode->type == qtype_preg_node::TYPE_NODE_ASSERT || $second->pregnode->type == qtype_preg_node::TYPE_NODE_ASSERT) {
            return false;
        } elseif ($first->pregnode->type == qtype_preg_node::TYPE_LEAF_META && $second->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {
            return $first->pregnode->subtype != $second->pregnode->subtype;
        } elseif ($first->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET && $second->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET) {
            if ($first->pregnode->negative && $second->pregnode->negative) {
                return $first->pregnode->charset != $second->pregnode->charset;
            } elseif (!$first->pregnode->negative && !$second->pregnode->negative) {
                $flag = false;
                for ($i=0; $i<strlen($first->pregnode->charset); $i++) {
                    for ($j=0; $j<strlen($second->pregnode->charset); $j++) {
                        if ($first->pregnode->charset[$i] == $second->pregnode->charset[$j]) {
                            $flag = true;
                        }
                    }
                }
                return $flag && $first->pregnode->charset != $second->pregnode->charset;
            } else {
                return false;
            }
        } else {//meta and charset
            if ($second->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {
                $tmp = $first;
                $first = $second;
                $second = $tmp;
            }
            //first is meta, second is charset
            if ($first->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_ENDREG) {
                return false;//ENDREG not partial match with any other operand
            }
            for ($j=0; $j<strlen($second->pregnode->charset); $j++) {
                if ($first->pregnode->match($second->pregnode->charset, $j, $trash, false)) {
                    return true;// \w \W or . can't be equivalent to enumerable charset
                }
            }
            return $second->pregnode->negative;
        }
    }
    /**
    * Function split two leaf to four UNique and EQUivalen leafs
    * @param $first first leaf
    * @param $second second leaf
    * @return unique and equivalent leafs array
    */
    protected function get_unequ_leafs($first, $second) {
        if ($first->pregnode->type == qtype_preg_node::TYPE_LEAF_META && $second->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {
        //two meta leafs
            $result[0] = $this->cross_meta_leafs(clone $first, clone $second, false, true);
            $result[1] = $this->cross_meta_leafs(clone $first, clone $second, false, false);
            $result[2] = $this->cross_meta_leafs(clone $first, clone $second, false, false);
            $result[3] = $this->cross_meta_leafs(clone $first, clone $second, true, false);
        } elseif ($first->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET && $second->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET) {
        //two charset leafs
            $result[0] = $this->cross_charsets(clone $first, clone $second, false, true);
            $result[1] = $this->cross_charsets(clone $first, clone $second, false, false);
            $result[2] = $this->cross_charsets(clone $first, clone $second, false, false);
            $result[3] = $this->cross_charsets(clone $first, clone $second, true, false);
        } else {
        //meta and charset
            $fixindex1 = 0;
            $fixindex2 = 2;
            if ($second->pregnode->type == qtype_preg_node::TYPE_LEAF_META) {
                $tmp = $first;
                $first = $second;
                $second = $tmp;
                $fixindex1 = 2;
                $fixindex2 = 0;
            }
            //first is meta, second is charset
            $result[$fixindex1] = $this->cross_meta_charset(clone $first, clone $second, false, true);
            $result[$fixindex1+1] = $this->cross_meta_charset(clone $first, clone $second, false, false);
            $result[$fixindex2] = $this->cross_meta_charset(clone $first, clone $second, false, false);
            $result[$fixindex2+1] = $this->cross_meta_charset(clone $first, clone $second, true, false);
        }
        if ($result[0] === false) {
            $result[0] = $result[1];
        }
        if ($result[1] === false) {
            $result[1] = $result[0];
        }
        if ($result[2] === false) {
            $result[2] = $result[3];
        }
        if ($result[3] === false) {
            $result[3] = $result[2];
        }
        return $result;
    }
    protected function cross_charsets($leaf1, $leaf2, $invert1, $invert2) {
        $result = new qtype_preg_leaf_charset;
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        $str = '';
        if (!$leaf1->pregnode->negative && !$leaf2->pregnode->negative) {//++
            for ($i=0; $i<strlen($leaf1->pregnode->charset); $i++) {
                if (strchr($leaf2->pregnode->charset, $leaf1->pregnode->charset[$i]) !== false) {
                    $str .= $leaf1->pregnode->charset[$i];
                }
            }
        } else if (!$leaf1->pregnode->negative && $leaf2->pregnode->negative) {//+-
            for ($i=0; $i<strlen($leaf1->pregnode->charset); $i++) {
                if (strchr($leaf2->pregnode->charset, $leaf1->pregnode->charset[$i]) === false) {
                    $str .= $leaf1->pregnode->charset[$i];
                }
            }
        } else if ($leaf1->pregnode->negative && !$leaf2->pregnode->negative) {//-+
            for ($i=0; $i<strlen($leaf2->pregnode->charset); $i++) {
                if (strchr($leaf1->pregnode->charset, $leaf2->pregnode->charset[$i]) === false) {
                    $str .= $leaf2->pregnode->charset[$i];
                }
            }
        } else {//--
            $str = $leaf2->pregnode->charset . $leaf1->pregnode->charset;
            $result->negative = true;
        }
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        if ($str=='') {
            return false;
        }
        $result->charset = $str;
        $result = $this->from_preg_node($result);
        return $result;
    }
    protected function cross_meta_leafs($leaf1, $leaf2, $invert1, $invert2) {
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        //one of leaf is \w, other is metadot
        $flag = false;
        if ($leaf1->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_WORD_CHAR) {
            $tmp = $leaf1;
            $leaf1 = $leaf2;
            $second = $tmp;
            $flag = true;
        }
        //now first is meta dot, second is wordchar
        if ($leaf1->pregnode->negative) {
            $result =  false;//impossible to match
        } else {
            $result = new qtype_preg_leaf_meta;
            $result->negative = $leaf2->pregnode->negative;
            $result->subtype = qtype_preg_leaf_meta::SUBTYPE_WORD_CHAR;
            $result = $this->from_preg_node($result);
        }
        if ($flag) {
            $tmp = $leaf1;
            $leaf1 = $leaf2;
            $second = $tmp;
        }
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        return $result;
    }
    protected function cross_meta_charset($leaf1, $leaf2, $invert1, $invert2) {
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        $flag = false;
        if ($leaf1->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET) {
            $tmp = $leaf1;
            $leaf1 = $leaf2;
            $second = $tmp;
            $flag = true;
        }
        //now leaf1 is meta and leaf2 is charset
        if ($leaf1->pregnode->subtype == qtype_preg_leaf_meta::SUBTYPE_DOT) {
            if ($leaf1->pregnode->negative) {
                $result =  false;//impossible to match
            } else {
                $result = new qtype_preg_leaf_charset;
                $result->negative = $leaf2->pregnode->negative;
                $result->charset = $leaf2->pregnode->charset;
                $result = $this->from_preg_node($result);
            }
        } else {//leaf1 is word char
            if ($leaf2->pregnode->negative) {
                $result = preg_leaf_combo::get_cross($leaf1->pregnode, $leaf2->pregnode);
                $result = $this->from_preg_node($result);
            } else {
                $result = new qtype_preg_leaf_charset;
                $result->negative = $leaf2->pregnode->negative;
                $result->charset = '';
                for ($i=0; $i < strlen($leaf2->pregnode->charset); $i++) {
                    if ($leaf1->pregnode->match($leaf2->pregnode->charset, $i, $l, false)) {
                        $result->charset .= $leaf2->pregnode->charset[$i];
                    }
                }
                $result = $this->from_preg_node($result);
                if ($result->pregnode->charset == '') {
                    return false;
                }
            }
        }
        if ($flag) {
            $tmp = $leaf1;
            $leaf1 = $leaf2;
            $second = $tmp;
        }
        if ($invert1) {
            $leaf1->pregnode->negative = !$leaf1->pregnode->negative;
        }
        if ($invert2) {
            $leaf2->pregnode->negative = !$leaf2->pregnode->negative;
        }
        return $result;
    }
    protected function save_new_leaf($num, $leaf) {
        $index = 0;
        $leaf = new dfa_preg_leaf_charset($leaf);
        foreach ($this->connection[$num] as $key => $val) {
            if ($key > $index && $val->pregnode->type != qtype_preg_node::TYPE_NODE_ASSERT && $key < 186759556) {
                $index = $key;
            }
        }
        $this->connection[$num][++$index] = $leaf;
        return $index;
    }
    protected function replace_num_in_map($num, $old, $new1, $new2) {
        foreach ($this->map[$num] as $cur => $arrnext) {
            if (is_array($arrnext)) {
                foreach ($arrnext as $i => $leafnum) {
                    if ($leafnum == $old) {
                        $this->map[$num][$cur][$i] = $new1;
                        $this->map[$num][$cur][] = $new2;
                        break;
                    }
                }
            }
        }
        $this->map[$num][$new1] = $this->map[$num][$old];
        $this->map[$num][$new2] = $this->map[$num][$old];
        unset($this->map[$num][$old]);
        foreach ($this->roots[$num]->firstpos as $key => $val) {
            if ($val == $old) {
                $this->roots[$num]->firstpos[$key] = $new1;
                $this->roots[$num]->firstpos[] = $new2;
            }
        }
    }
    /**
    * Function merge map of symbol's following, first operand $this->map[0], second operand $this->map[$num]
    * and put result in $this->map[0]
    * @param $num number of other map to merging
    */
    protected function merge_fp_maps($num) {
        //create table of crossing
        $table = array();
        foreach ($this->map[$num] as $akey=>$aleaf) {
            foreach ($this->map[0] as $mkey=>$mleaf) {
                if ($akey!=dfa_preg_leaf_meta::ENDREG && $mkey!=dfa_preg_leaf_meta::ENDREG && $mkey!=$num) {
                    $newleaf = preg_leaf_combo::get_cross($this->connection[0][$mkey]->pregnode, $this->connection[$num][$akey]->pregnode);
                    $table[$akey][$mkey] = new fptab;
                    $table[$akey][$mkey]->leaf = $this->from_preg_node($newleaf);
                }
            }
        }
        foreach ($this->map[$num] as $akey=>$aleaf) {
            $table[$akey][dfa_preg_leaf_meta::ENDREG] = false;
        }
        foreach ($this->map[0] as $mkey=>$mleaf) {
            if ($mkey!=$num) {
                $table[dfa_preg_leaf_meta::ENDREG][$mkey] = new fptab;
                $table[dfa_preg_leaf_meta::ENDREG][$mkey]->leaf = $this->connection[0][$mkey];
            }
        }
        $newleaf = new qtype_preg_leaf_meta;
        $newleaf->subtype = qtype_preg_leaf_meta::SUBTYPE_ENDREG;
        $newleaf = $this->from_preg_node($newleaf);
        $table[dfa_preg_leaf_meta::ENDREG][dfa_preg_leaf_meta::ENDREG] = new fptab;
        $table[dfa_preg_leaf_meta::ENDREG][dfa_preg_leaf_meta::ENDREG]->leaf = $newleaf;
        //forming ?passages?
        foreach ($table as $akey=>$str) {
            if ($akey!=dfa_preg_leaf_meta::ENDREG) {
                foreach ($str as $mkey=>$member) {
                    if ($mkey!=dfa_preg_leaf_meta::ENDREG) {
                        foreach ($this->map[$num][$akey] as $afollow) {
                            foreach ($this->map[0][$mkey] as $mfollow) {
                                if (($afollow==dfa_preg_leaf_meta::ENDREG || $mfollow!=dfa_preg_leaf_meta::ENDREG) &&
                                    ($akey!=dfa_preg_leaf_meta::ENDREG || $mkey!=dfa_preg_leaf_meta::ENDREG)) {
                                    if ($mfollow!=$num) {
                                        $table[$akey][$mkey]->aindex[] = $afollow;
                                        $table[$akey][$mkey]->mindex[] = $mfollow;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ($str as $mkey=>$member) {
                    if ($mkey!=dfa_preg_leaf_meta::ENDREG) {
                        foreach ($this->map[0][$mkey] as $follow) {
                            if ($follow!=$num) {
                                $table[$akey][$mkey]->aindex[] = $akey;//meta endreg
                                $table[$akey][$mkey]->mindex[] = $follow;//copying passage
                            } else {
                                foreach ($this->roots[$num]->firstpos as $afirpos) {
                                    foreach ($this->map[0][$num] as $mainnext) {
                                        if ($mainnext!=dfa_preg_leaf_meta::ENDREG || $afirpos==dfa_preg_leaf_meta::ENDREG) {
                                            $table[$akey][$mkey]->aindex[] = $afirpos;
                                            $table[$akey][$mkey]->mindex[] = $mainnext;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //wave for deleting inaccessible postion
            //form start front
        $firsta = $afront = array();
        $firstm = $mfront = array();
        foreach ($this->roots[0]->firstpos as $firstpos) {
            if ($firstpos!=$num) {
                $firsta[] = $afront[] = dfa_preg_leaf_meta::ENDREG;
                $firstm[] = $mfront[] = $firstpos;
                $table[dfa_preg_leaf_meta::ENDREG][$firstpos]->inaccessible = false;
            } else {
                foreach ($this->roots[$num]->firstpos as $afirpos) {
                    foreach ($this->map[0][$num] as $mainnext) {
                        if ($mainnext!=dfa_preg_leaf_meta::ENDREG || $afirpos==dfa_preg_leaf_meta::ENDREG) {
                            $firsta[] = $afront[] = $afirpos;
                            $firstm[] = $mfront[] = $mainnext;
                            $table[$afirpos][$mainnext]->inaccessible = false;
                        }
                    }
                }
            }
        }
            //wave
        do {
            $newafront = array();
            $newmfront = array();
            $newfirstposfortree = array();
            foreach ($afront as $key=>$val) {
                foreach ($table[$val][$mfront[$key]]->aindex as $newkey=>$apass) {
                    if ($table[$apass][$table[$val][$mfront[$key]]->mindex[$newkey]]->inaccessible) {
                        $newafront[] = $apass;
                        $newmfront[] = $table[$val][$mfront[$key]]->mindex[$newkey];
                        $table[$apass][$table[$val][$mfront[$key]]->mindex[$newkey]]->inaccessible = false;
                    }
                }
            }
            $afront = $newafront;
            $mfront = $newmfront;
        } while (count($afront)!=0);
            //deleting
        foreach ($table as $akey=>$str) {
            foreach ($str as $mkey=>$member) {
                if ($member!==false && $member->inaccessible) {
                    $table[$akey][$mkey] = false;
                }
            }
        }
        //formin fpmap from table
            //numerating
        $maxnum = 0;
        $this->connection[0] = array();
        foreach ($table as $akey=>$str) {
            foreach ($str as $mkey=>$member) {
                if ($member!==false) {
                    if ($akey==dfa_preg_leaf_meta::ENDREG && $mkey==dfa_preg_leaf_meta::ENDREG) {
                        $table[$akey][$mkey]->number = dfa_preg_leaf_meta::ENDREG;
                        $this->connection[0][dfa_preg_leaf_meta::ENDREG] = $table[$akey][$mkey]->leaf;
                    } else {
                        $table[$akey][$mkey]->number = ++$maxnum;
                        $this->connection[0][$maxnum] = $table[$akey][$mkey]->leaf;
                    }
                    //forming firstpos
                    if (in_array($akey, $firsta) && in_array($mkey, $firstm) && !($table[$akey][$mkey]->leaf->pregnode->type==qtype_preg_node::TYPE_LEAF_CHARSET && $table[$akey][$mkey]->leaf->pregnode->charset == '')) {
                        $newfirstposfortree[] = $table[$akey][$mkey]->number;
                    }
                }
            }
        }
        $this->roots[0]->firstpos = $newfirstposfortree;
            //forming fpmap
        $this->map[0] = array();
        foreach ($table as $akey=>$str) {
            foreach ($str as $mkey=>$member) {
                if ($member!==false) {
                    foreach ($member->aindex as $key=>$aind) {
                        $this->map[0][$member->number][] = $table[$aind][$member->mindex[$key]]->number;
                    }
                }
            }
        }
        //delete empty symbols
        foreach ($this->map[0] as $key=>$val) {
            if ($this->connection[0][$key]->pregnode->type==qtype_preg_node::TYPE_LEAF_CHARSET && $this->connection[0][$key]->pregnode->charset == '') {
                unset($this->map[0][$key]);
                unset($this->connection[0][$key]);
            }
        }
        foreach ($this->map[0] as $key=>$val) {
            foreach ($val as $key2=>$val2) {
                if (!isset($this->connection[0][$val2])) {
                    unset($this->map[0][$key][$key2]);
                }
            }
        }
    }
    /**
    * DFA node factory
    * @param pregnode qtype_preg_node child class instance
    * @return corresponding dfa_preg_node child class instance
    */
    public function from_preg_node($pregnode) {
        if (!is_a($pregnode,'qtype_preg_node')) {
            return $pregnode;   // The node is already converted.
        }

        if (!$this->zeroquantdeleted) {
            $res = self::delete_zero_quant($pregnode);
            if ($res != true && $res != false) {
                $pregnode = $res;
            }
            $this->zeroquantdeleted = true;
        }

        $name = $pregnode->type;
        switch ($name) {
            case 'node_subexpr':
                return $this->from_preg_node($pregnode->operands[0]);
            case 'node_alt':
                $pregnode = $this->convert_alternation($pregnode);
                break;
        }
        return parent::from_preg_node($pregnode);
    }

    /**
     * Function delete zero quants subtree from syntax tree
     * @param node is the subroot of syntax tree, don't need call this function for other node
     * return true if subtree full deleted or new subroot if changed, false otherwise
     */
    protected function delete_zero_quant($node) {
        $name = $node->type;
        switch ($name) {
            case 'node_finite_quant':
                $res=self::delete_zero_quant($node->operands[0]);
                if ($node->rightborder==0 || $res===true) {
                    return true;
                } elseif (is_object($res)) {
                    $node->operands[0] = $res;
                }
                break;
            case 'node_assert':
            case 'node_subexpr':
            case 'node_infinite_quant':
                $res=self::delete_zero_quant($node->operands[0]);
                if ($res===true) {
                    return true;
                } elseif (is_object($res)) {
                    $node->operands[0] = $res;
                }
                break;
            case 'node_alt':
                $res = array();
                $res[0] = self::delete_zero_quant($node->operands[0]);
                $res[1] = self::delete_zero_quant($node->operands[1]);
                if (is_object($res)) {
                    $node->operands[0] = $res[0];
                }
                if (is_object($res)) {
                    $node->operands[1] = $res[1];
                }
                if ($res[0]===true && $res[1]===true) {
                    return true;
                } else if ($res[0]===true) {
                    $newsubroot = new qtype_preg_node_finite_quant;
                    $newsubroot->operands[0] = $node->operands[1];
                    return $newsubroot;
                } else if ($res[1]===true) {
                    $newsubroot = new qtype_preg_node_finite_quant;
                    $newsubroot->operands[0] = $node->operands[0];
                    return $newsubroot;
                }
                break;
            case 'node_concat':
                $res = array();
                $res[0]=self::delete_zero_quant($node->operands[0]);
                $res[1]=self::delete_zero_quant($node->operands[1]);
                if (is_object($res[0])) {
                    $node->operands[0] = $res[0];
                }
                if (is_object($res[1])) {
                    $node->operands[1] = $res[1];
                }
                if ($res[0]===true && $res[1]===true) {
                    return true;
                } else if ($res[0]===true) {
                    return $node->operands[1];
                } else if ($res[1]===true) {
                    return $node->operands[0];
                }
                break;
        }
        return false;
    }

    protected function convert_alternation($node) {
        $newoperands = array();
        $nullable = false;
        foreach ($node->operands as $operand) {
            if ($operand->type == qtype_preg_node::TYPE_LEAF_META && $operand->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                $nullable = true;
            } else {
                $newoperands[] = $operand;
            }
        }

        if (!$nullable) {
            return $node;
        }

        // Convert alternation to {0,1} quantifier.
        $quant = new qtype_preg_node_finite_quant(0, 1);
        if (count($newoperands) > 1) {
            $quant->operands[0] = $node;
        } else {
            $quant->operands[0] = $newoperands[0];
        }
        return $quant;
    }

    public function print_connection($index) {
        echo "\n\n\n\n\n\n\n";
        foreach ($this->connection[$index] as $num=>$leaf) {
            echo 'number: ', $num, "\n";
            $leaf->print_self(0);
            echo "\n";
        }
        echo "\n\n\n\n\n\n\n";
    }
    /**
    * Debug function draw finite automate with number number in human readable form
    * don't work without right to execute file
    * @param number number of drawing finite automate
    * @param $subject type of drawing, may be: 'dfa', 'tree', 'fp'
    */
    public function draw($number, $subject) {
        $dir = $this->get_temp_dir('dfa');
        $dotcode = call_user_func(array('qtype_preg_dfa_matcher', 'generate_'.$subject.'_dot_code'), $number);
        $dotfn = $dir.'/dotcode.dot';
        $dotfile = fopen($dotfn, 'w');
        foreach ($dotcode as $dotstring) {
            fprintf($dotfile, "%s\n", $dotstring);
        }
        fclose($dotfile);
        $jpgfilename = $subject.$this->picnum.'.jpg';
        $this->execute_dot($dotfn, $jpgfilename);
        unlink($dotfn);
        //exec('dot.exe -Tjpg -o"'.$tempfolder..'.jpg" -Kdot "'.$tempfolder.'dotcode.dot"');
        //echo '<IMG src="/question/type/preg/temp/'.$subject.$this->picnum.'.jpg" alt="Can\'t display '.$subject.' #'.$this->picnum.' graph.">';
        //'IMG src="/question/type/preg/temp/'.$subject.$this->picnum.'.jpg" alt="Can\'t display '.$subject.' #'.$this->picnum.' graph."';
        $this->picnum++;
    }
    /**
    * Debug function generate dot code for drawing finite automate
    * @param number number of drawing finite automate
    */
    protected function generate_dfa_dot_code($number) {
        $dotcode = array();
        $dotcode[] = 'digraph {';
        $dotcode[] = 'rankdir = LR;';
        foreach ($this->finiteautomates[$number] as $index=>$state) {
            foreach ($state->passages as  $leafcode=>$target) {
                if (is_object($this->connection[$number][$leafcode]->pregnode)) {
                    //$symbol = $this->connection[$number][$leafcode]->pregnode->tohr();
                    $symbol = $leafcode;
                } else {
                    $symbol = 'ERROR: '.var_export($this->connection[$number][$leafcode]->pregnode, true);
                }
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
    /**
    * Debug function generate dot code for drawing syntax tree
    * @param number number of drawing syntax tree
    */
    protected function generate_tree_dot_code($number) {
        $dotcode = array();
        $dotcode[] = 'digraph {';
        $dotcode[] = 'rankdir = TB;';
        $this->roots[$number]->generate_dot_code($dotcode, $maxnum=0);
        $dotcode[] = '};';
        return $dotcode;
    }
    /**
    * Debug function generate dot code for drawing follow position map
    * @param number number of drawing finite automate
    */
    protected function generate_fp_dot_code($number) {
        $dotcode = array('digraph {', 'rankdir=LR');
        foreach ($this->map[$number] as $start=>$ends) {
            foreach ($ends as $end) {
                $dotcode[] = '"'.$start.': '.$this->connection[$number][$start]->pregnode->tohr().
                        '"->"'.$end.': '.$this->connection[$number][$end]->pregnode->tohr().'";';
            }
        }
        $dotcode[] = '};';
        return $dotcode;
    }
}
