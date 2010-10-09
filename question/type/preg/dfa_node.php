<?php
if (!defined ('PREG_DFA_NODE')) {
    define('PREG_DFA_NODE', true);
    require_once($CFG->dirroot . '/question/type/preg/node.php');
    //////////////////////////////
    interface preg_dfa_interface {
        /**
        *Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
        *Put pair of number=>character to $this->connection[$index][].
        */
        function numeration(&$connection, &$maxnum);
        /**
        *Function determine: subtree with root in this node can give empty word or not.
        *@return true if can give empty word, else false
        */
        function _nullable();
        /**
        *function determine characters which can be on first position in word, which given subtree with root in this node
        *@return numbers of characters (array)
        */
        function first_pos();
        /**
        *function determine characters which can be on last position in word, which given subtree with root in this node
        *@return numbers of characters (array)
        */
        function last_pos();
        /**
        *function determine characters which can follow characters from this node
        *@param fpmap - map of following of characters
        */
        function follow_pos(&$fpmap);
        /**
        *function find asserts' nodes in tree and put link to root of it to $this->roots[<number of assert>]
        *@param roots - array of tree roots for saving link to assert node
        */
        function find_asserts(&$roots);
        /**
        *checks if this abstract sytax tree node supported by this matching engine, adding error messages for unsupported nodes
        *This function should add names of strings for unsupported operations to $flags
        @param flags - flags for result
        @return bool is node accepted
        */
        function accept_node(&$flags);
        /**
        *function convert the tree, replace operand+ on operandoperand*, operand{x,y} replace on x times of operand and y-x times of operand?
        *and replace subpattern on it's operand (operand) on operand, because subbpattern is unsupported, 
        *but it can use as grouping () == (?:) if link isn't exist (for this matcher).
        *(operand|) replace on operand?, character class METASYMBOL_DOT replace on METASYBOLD_, because METASYMBOL_DOT is service word
        *param node - current node of converting tree
        */
        function convert();
        /**
        *Function coping subtree with root in this node
        *@return copied node
        */
        function copy();
    }
    /**
    *function append array2 to array1, non unique values not add
    *@param arr1 - first array
    *@param arr2 - second array, which will appended to arr1
    */
    function qtype_preg_dfa_push_unique(&$arr1, $arr2) {// to static
        if (!is_array($arr1)) {
            $arr1 = array();
        }
        foreach ($arr2 as $value) {
            if (!in_array($value, $arr1)) {
                $arr1[] = $value;
            }
        }
    }
    class dfa_preg_node_leaf extends preg_node_leaf implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            switch ($this->subtype) {
                case preg_node::leaf_charclass:
                    $this->number = ++$maxnum;
                    $connection[$this->number] = &$this;
                    break;
                case preg_node::leaf_dot:
                    $this->number = ++$maxnum + preg_node::dot;
                    break;
                case preg_node::leaf_strend:
                    $this->number = ++$maxnum + preg_node::strend;
                    break;
            }
        }
        function _nullable() {
            $this->nullable = false;
            return false;
        }
        function first_pos() {
            return array($this->number);
        }
        function last_pos() {
            return array($this->number);
        }
        function follow_pos(&$fpmap) {
            ;//this function not need for leaf
        }
        function find_asserts(&$roots) {
            ;//this function not need for leaf
        }
        function accept_node(&$flags)  {
            if ($this->subtype === preg_node::leaf_link) {
                $flags['link'] = true;
                return false;
            }
            return true;
        }
        function convert() {
            if ($this->chars == 'METASYMBOL_DOT' && $this->subtype != preg_node::leaf_dot) {
                $this->chars = 'METASYBOLD_';
            } elseif ($this->subtype == preg_node::leaf_dot) {
                $this->chars = 'METASYMBOL_DOT';
            }
        }
        function copy() {
            $result = new dfa_preg_node_leaf;
            $result = clone $this;
            return $result;
        }
    }
    class dfa_preg_node_conc extends preg_node_conc implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            $this->firop->numeration($connection, $maxnum);
            $this->secop->numeration($connection, $maxnum);
        }
        function _nullable() {
            $this->nullable = $this->firop->_nullable() && $this->secop->_nullable();
            return $this->nullable;
        }
        function first_pos() {
            $this->firstpos = $this->firop->first_pos();
            return $this->firstpos;
        }
        function last_pos() {
            $this->lastpos = $this->secop->last_pos();
            return $this->lastpos;
        }
        function follow_pos(&$fpmap) {
            $this->firop->follow_pos($fpmap);
            $this->secop->follow_pos($fpmap);
            foreach ($this->firop->lastpos as $key) {
                qtype_preg_dfa_push_unique($fpmap[$key], $this->secop->firstpos);
            }
        }
        function find_asserts(&$roots) {
            $this->firop->find_asserts($roots);
            $this->secop->find_asserts($roots);
        }
        function accept_node(&$flags) {
            return true;
        }
        function convert() {
            $this->firop->convert();
            $this->secop->convert();
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->secop = new stdClass;
            $result->firop = $this->firop->copy();
            $result->secop = $this->secop->copy();
            return $result;
        }
    }
    class dfa_preg_node_alt extends preg_node_alt implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            $this->firop->numeration($connection, $maxnum);
            $this->secop->numeration($connection, $maxnum);
        }
        function _nullable() {
            $this->nullable = $this->firop->_nullable() || $this->secop->_nullable();
            return $this->nullable;
        }
        function first_pos() {
            $first = $this->firop->first_pos();
            $second = $this->secop->first_pos();
            $this->firstpos = array_merge($first, $second);
            return $this->firstpos;
        }
        function last_pos() {
            $first = $this->firop->last_pos();
            $second = $this->secop->last_pos();
            $this->lastpos = array_merge($first, $second);
            return $this->lastpos;
        }
        function follow_pos(&$fpmap) {
            $this->firop->follow_pos($fpmap);
            $this->secop->follow_pos($fpmap);
        }
        function find_asserts(&$roots) {
            $this->firop->find_asserts($roots);
            $this->secop->find_asserts($roots);
        }
        function accept_node(&$flags) {
            return true;
        }
        function convert() {
            $this->firop->convert();
            $this->secop->convert();
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->secop = new stdClass;
            $result->firop = $this->firop->copy();
            $result->secop = $this->secop->copy();
            return $result;
        }
    }
    class dfa_preg_node_iter extends preg_node_iter implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            $this->firop->numeration($connection, $maxnum);
        }
        function _nullable() {
            $this->nullable = true;
            return true;
        }
        function first_pos() {
            $this->firstpos = $this->firop->first_pos();
            return $this->firstpos;
        }
        function last_pos() {
            $this->lastpos = $this->firop->last_pos();
            return $this->lastpos;
        }
        function follow_pos(&$fpmap) {
            $this->firop->follow_pos($fpmap);
            foreach ($this->firop->firstpos as $key) {
                qtype_preg_dfa_push_unique($fpmap[$key], $this->firop->firstpos);
            }
        }
        function find_asserts(&$roots) {
            $this->firop->find_asserts($roots);
        }
        function accept_node(&$flags) {
            if (!$this->greed) {
                $flags['lazyquant'] = true;
                return false;
            }
            return true;
        }
        function convert() {
            $this->firop->convert();
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->firop = $this->firop->copy();
            return $result;
        }
    }
    class dfa_preg_node_quest extends preg_node_quest implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            $this->firop->numeration($connection, $maxnum);
        }
        function _nullable() {
            $this->nullable = true;
            return true;
        }
        function first_pos() {
            $this->firstpos = $this->firop->first_pos();
            return $this->firstpos;
        }
        function last_pos() {
            $this->lastpos = $this->firop->last_pos();
            return $this->lastpos;
        }
        function follow_pos(&$fpmap) {
            $this->firop->follow_pos($fpmap);
        }
        function find_asserts(&$roots) {
            $this->firop->find_asserts($roots);
        }
        function accept_node(&$flags) {
            if (!$this->greed) {
                $flags['lazyquant'] = true;
                return false;
            }
            return true;
        }
        function convert() {
            $this->firop->convert();
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->firop = $this->firop->copy();
            return $result;
        }
    }
    class dfa_preg_node_quant extends preg_node_quant implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            ;//do nothing, because will be converted before call this function
        }
        function _nullable() {
            ;//do nothing, because will be converted before call this function
        }
        function first_pos() {
            ;//do nothing, because will be converted before call this function
        }
        function last_pos() {
            ;//do nothing, because will be converted before call this function
        }
        function follow_pos(&$fpmap) {
            ;//do nothing, because will be converted before call this function
        }
        function find_asserts(&$roots) {
            ;//do nothing, because will be converted before call this function
        }
        function accept_node(&$flags) {
            if (!$this->greed) {
                $flags['lazyquant'] = true;
                return false;
            }
            return true;
        }
        function convert() {
            $this->firop->convert();
            $operand = $this->firop->copy();
            if ($this->leftborder != 0) {
                $count = $this->leftborder;
                $currsubroot = $this->firop;
                for ($i=1; $i<$count; $i++) {
                    $tmp = new dfa_preg_node_conc;
                    $tmp->firop = $currsubroot;
                    $tmp->secop = $operand->copy();
                    $currsubroot = $tmp;         
                }
                if ($this->leftborder < $this->rightborder) {//TODO: else error, but no error branch, error must find in accept_node, delete it
                    $tmp = new dfa_preg_node_conc;
                    $tmp->firop = $currsubroot;
                    $currsubroot = $tmp;
                    $tmp = new dfa_preg_node_quest;
                    $tmp->firop = $operand->copy();
                    $operand = $tmp;
                    $currsubroot->secop = $tmp;
                }
            } else {
                $currsubroot = new dfa_preg_node_quest;
                $currsubroot->firop = $operand;
                $operand = $currsubroot;
            }
            if ($this->rightborder != -1) {
                $count = $node->rightborder - $this->leftborder;
                for ($i=1; $i<$count; $i++) {
                    $tmp = new dfa_preg_node_conc;
                    $tmp->firop = $currsubroot;
                    $tmp->secop = $operand->copy();
                    $currsubroot = $tmp;
                }
            } else {
                $tmp = new dfa_preg_node_conc;
                $tmp->firop = $currsubroot;
                $tmp = new dfa_preg_node_iter;
                $tmp->secop->firop = $operand->copy();
                $currsubroot = $tmp;
            }
            $this->subtype = $currsubroot->subtype;
            $this->firop = $currsubroot->firop;
            $this->secop = $currsubroot->secop;
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->firop = $this->firop->copy();
            return $result;
        }
    }
    class dfa_preg_node_assert extends preg_node_assert implements preg_dfa_interface {
        var $number; 
        var $nullable; 
        var $firstpos; 
        var $lastpos; 
        var $followpos;
        function numeration(&$connection, &$maxnum) {
            $this->number = ++$maxnum + preg_node::assert;
        }
        function _nullable() {
            $this->nullable = true;
            return true;
        }
        function first_pos() {
            $this->firstpos = array($this->number);
            return $this->firstpos;
        }
        function last_pos() {
            $this->lastpos = array($this->number);
            return $this->lastpos;
        }
        function follow_pos(&$fpmap) {
            ;//not need any actions
        }
        function find_asserts(&$roots) {
            $roots[$this->number] = &$this;
        }
        function accept_node(&$flags) {
            switch ($this->subtype) {
                case preg_node::node_assertff:
                    $flags['assertff'] = true;
                    return false;
                    break;
                case preg_node::node_assertfb:
                    $flags['assertfb'] = true;
                    return false;
                    break;
                case preg_node::node_asserttb:
                    $flags['asserttb'] = true;
                    return false;
                    break;
            }
            return true;
        }
        function convert() {
            $this->firop->convert();
        }
        function copy() {
            $result = new dfa_preg_node_conc;
            $result = clone $this;
            $result->firop = new stdClass;
            $result->firop = $this->firop->copy();
            return $result;
        }
    }
    class dfa_preg_node_subpatt extends preg_node_subpatt {
        function numeration(&$connection, &$maxnum) {
            ;//do nothing, because unsupported
        }
        function _nullable() {
            ;//do nothing, because unsupported
        }
        function first_pos() {
            ;//do nothing, because unsupported
        }
        function last_pos() {
            ;//do nothing, because unsupported
        }
        function follow_pos(&$fpmap) {
            ;//do nothing, because unsupported
        }
        function find_asserts(&$roots) {
            ;//do nothing, because unsupported
        }
        function accept_node(&$flags) {
            $flags['subpatt'] = true;
            return false;
        }
        function convert() {
            ;//do nothing, because unsupported
        }
        function copy() {
            ;//do nothing, because unsupported
        }
    }
    class dfa_preg_node_condsubpatt extends preg_node_condsubpatt {
        function numeration(&$connection, &$maxnum) {
            ;//do nothing, because unsupported
        }
        function _nullable() {
            ;//do nothing, because unsupported
        }
        function first_pos() {
            ;//do nothing, because unsupported
        }
        function last_pos() {
            ;//do nothing, because unsupported
        }
        function follow_pos(&$fpmap) {
            ;//do nothing, because unsupported
        }
        function find_asserts(&$roots) {
            ;//do nothing, because unsupported
        }
        function accept_node(&$flags) {
            $flags['condsubpatt'] = true;
            return false;
        }
        function convert() {
            ;//do nothing, because unsupported
        }
        function copy() {
            ;//do nothing, because unsupported
        }
    }
    class dfa_preg_node_onetimesubpatt extends preg_node_onetimesubpatt {
        function numeration(&$connection, &$maxnum) {
            ;//do nothing, because unsupported
        }
        function _nullable() {
            ;//do nothing, because unsupported
        }
        function first_pos() {
            ;//do nothing, because unsupported
        }
        function last_pos() {
            ;//do nothing, because unsupported
        }
        function follow_pos(&$fpmap) {
            ;//do nothing, because unsupported
        }
        function find_asserts(&$roots) {
            ;//do nothing, because unsupported
        }
        function accept_node(&$flags) {
            $flags['onetimesubpatt'] = true;
            return false;
        }
        function convert() {
            ;//do nothing, because unsupported
        }
        function copy() {
            ;//do nothing, because unsupported
        }
    }
}
?>