<?php
/**
 * Defines generic node classes, generated by parser. 
 * The will be usually aggregated in engine-specific classes.
 * These classes are used primarily to store data, so their variable memebers are public
 *
 * @copyright &copy; 2010 Sychev Oleg, Kolesov Dmitriy
 * @author Sychev Oleg, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */


/**
* Generic node class
*/
abstract class preg_node {

    //////Class constants used to define type
    //Abstract node class, not representing real things
    const TYPE_ABSTRACT = 0;
    //Character or character class
    const TYPE_LEAF_CHARSET = 1;
    //Meta-character or escape sequence matching with a set of characters that couldn't be enumerated
    const TYPE_LEAF_META = 2;
    //Simple assert: ^ $ or escape-sequence
    const TYPE_LEAF_ASSERT = 3;
    //Back reference to subpattern
    const TYPE_LEAF_BACKREF = 4;
    //Recursive match
    const TYPE_LEAF_RECURSION = 5;
    //Option set
    const TYPE_LEAF_OPTIONS = 6;
    //Highest possible leaf type
    const TYPE_LEAF_MAX = 99;

    //Lowest possible node type
    const TYPE_NODE_MIN = 100;
    //Finite quantifier
    const TYPE_NODE_FINITE_QUANT = 101;
    //Infinite quantifier
    const TYPE_NODE_INFINITE_QUANT = 102;
    //Concatenation
    const TYPE_NODE_CONCAT = 103;
    //Alternative
    const TYPE_NODE_ALT = 104;
    //Assert with expression within
    const TYPE_NODE_ASSERT = 105;
    //Subpattern
    const TYPE_NODE_SUBPATT = 106;
    //Conditional subpattern
    const TYPE_NODE_COND_SUBPATT = 107;
    //error node
    const TYPE_NODE_ERROR = 108;

    //Member variables, common to all subclasses
    //Type, one of the class  - must return constants defined in this class
    public $type;
    //Subtype, defined by child class
    public $subtype;
    //Error data for the subtype
    public $error = false;
    //Indexes of first and last characters for the node, they are equal if it's one-character node
    public $indfirst = -1;
    public $indlast = -1;

    public function __construct() {
        $this->type = self::TYPE_ABSTRACT;
    }

    /**
    * Return class name without 'preg_' prefix
    */
    abstract public function name();

    /*
    //Returns interface name string for the node
    abstract public function nodename();//TODO - implement if will be useful

    //May be overloaded by childs to change name using data from $this->pregnode
    public function ui_nodename() {
        return get_string($this->nodename(), 'qtype_preg');
    }
    */
}

/**
* Generic leaf node class
* 
*/
abstract class preg_leaf extends preg_node {

    //Is matching case insensitive?
    public $caseinsensitive = false;
    //Is leaf negative?
    public $negative = false;

    /*
    * Returns true if the leaf consume character from the string during matching, false if it is an assertion
    */
    public function consumes() {
        return true;
    }

    /*
    * Returns true if character(s) starting from $str[$pos] matches with leaf, false otherwise
    * Default implementation is good for simple consuming classes
    * @param str string with which matching is supporting
    * @param pos position of character in the string, if leaf is no-consuming than position before this character analyzed
    * @param length the length of match (for backreference or recursion), can be 0 for asserts
    */
    abstract public function match($str, $pos, &$length);
    
    /*
    *Returns one of characters which contains in this leaf
    */
    abstract public function character();
}

/**
* Character or character class
* Escape-sequence scanning will lead to this class only if characters it represents could be enumerated
* I.e. \n, \s, \v, \h and \d and their negative counterparts since they are not support unicode by default and so can be enumerated 
* \w is too large to be handled by full character set
*/
class preg_leaf_charset extends preg_leaf {

    //Character set, any of which could (not) match with this node
    public $charset = '';
    
    //Flags, if character class contain \w and \W which need to convert to other type of leaf
    public $w;
    public $W;

    public function __construct() {
        $this->type = preg_node::TYPE_LEAF_CHARSET;
        $this->w = false;
        $this->W = false;
    }

    public function name() {
        return 'leaf_charset';
    }

    public function match($str, $pos, &$length) {
        $charsetcopy = $this->charset;
        $strcopy = $str;
        $textlib = textlib_get_instance();//use textlib to avoid unicode problems

        if ($this->caseinsensitive) {
            $charsetcopy = $textlib->strtolower($charsetcopy);
            $strcopy = $textlib->strtolower($strcopy);
        }

        $result = ($textlib->strpos($charsetcopy, $strcopy[$pos]) !== false);

        if ($this->negative) {
            $result = ! $result;
        }
        $length = 1;
        return $result;
    }
    
    public function character() {
        if ($this->negative) {
            $i = ord(' ');
            while (strchr(chr($i), $this->charset) !== false) {
                $i++;
            }
            $res = chr($i);
            return $res;
        } else {
            return $this->charset[0];
        }
    }
}

/**
* Meta-character or escape sequence defining character set that couldn't be enumerated
*/
class preg_leaf_meta extends preg_leaf {

    //. - any character except \n
    const SUBTYPE_DOT = 1;
    //\p{L} or \pL
    const SUBTYPE_UNICODE_PROP = 2;
    // \w 
    //Should be locale-aware, but not Unicode for PCRE-compatibility
    const SUBTYPE_WORD_CHAR = 3;
    //Leaf with empty in alternative (something|)
    const SUBTYPE_EMPTY = 4;
    //Service subtype - end of regex, but not end of string
    const SUBTYPE_ENDREG = 5;
    //Unicode property name, used in case of SUBTYPE_UNICODE_PROP
    public $propname = '';

    public function __construct() {
        $this->type = preg_node::TYPE_LEAF_META;
    }
    public function name() {
        return 'leaf_meta';
    }
    public function character() {
        switch ($this->subtype) {
            case preg_leaf_meta::SUBTYPE_DOT:
                $result = 'D';
                break;
            //TODO: unicode property
            case preg_leaf_meta::SUBTYPE_WORD_CHAR:
                if ($this->negative) {
                    $result = '#';
                } else {
                    $result = 'W';
                }
                break;
        }
        return $result;
    }
    public function match($str, $pos, &$length) {
    switch ($this->subtype) {
            case preg_leaf_meta::SUBTYPE_DOT:
                $lenght = 1;
                if ($pos<strlen($str) && $str[$pos] != "\n") {
                    return true;
                } else {
                    return false;
                }
                break;
            //TODO: unicode property
            case preg_leaf_meta::SUBTYPE_WORD_CHAR:
                if (ctype_alnum($str[$pos]) || $str[$pos] === '_') {
                    $result =  true;
                } else {
                    $result =  false;
                }
                break;
        }
        if ($this->negative) {
            $result = !$result;
        }
        $lenght = 1;
        return $result;
    }
}

/**
* Meta-character or escape sequence defining character set that couldn't be enumerated
*/
class preg_leaf_assert extends preg_leaf {

    //^
    const SUBTYPE_CIRCUMFLEX = 1;
    //$
    const SUBTYPE_DOLLAR = 2;
    // \b
    const SUBTYPE_WORDBREAK = 3;
    // \A
    const SUBTYPE_ESC_A = 4;
    // \z
    const SUBTYPE_ESC_Z = 5;
    // \G
    const SUBTYPE_ESC_G = 6;

    public function __construct() {
        $this->type = preg_node::TYPE_LEAF_ASSERT;
    }

    public function consumes() {
        return false;
    }

    public function name() {
        return 'leaf_assert';
    }

    public function match($str, $pos, &$length) {
        $lenght = 0;
        switch ($this->subtype) {
            case preg_leaf_assert::SUBTYPE_ESC_A://because may be one line only is response
            case preg_leaf_assert::SUBTYPE_CIRCUMFLEX:
                if($pos == 0) {
                    $result = true;
                } else {
                    $result = false;
                }
                break;
            case preg_leaf_assert::SUBTYPE_ESC_Z://because may be one line only is response
            case preg_leaf_assert::SUBTYPE_DOLLAR:
                if ($pos == strlen($str)) {
                    $result = true;
                } else {
                    $result = false;
                }
                break;
            case preg_leaf_assert::SUBTYPE_WORDBREAK:
                $start = $pos==0 && ($str[0]=='_' || ctype_alnum($str[0]));
                $end = $pos==strlen($str) && ($str[$pos-1]=='_' || ctype_alnum($str[$pos-1]));
                $wW = ($str[$pos-1]=='_' || ctype_alnum($str[$pos-1])) && !($str[$pos]=='_' || ctype_alnum($str[$pos]));
                $Ww = !($str[$pos-1]=='_' || ctype_alnum($str[$pos-1])) && ($str[$pos]=='_' || ctype_alnum($str[$pos]));
                if ($start||$end||$wW||$Ww) {
                    $result = true;
                } else {
                    $result = false;
                }
                break;
            /*case preg_leaf_assert::SUBTYPE_ESC_G:
                TODO: matching with SUBTYPE_ESC_G
                trouble, because this function has not information about offset!
                break;*/
        }
        if ($this->negative) {
            $result = !$result;
        }
        return $result;
    }
    public function character() {
        echo 'TODO: implements abstract function character for preg_leaf_backref class before use it!';
    }
}

class preg_leaf_backref extends preg_leaf {
    public $number;
    
    public function match($str, $pos, &$length) {
        die ('TODO: implements abstract function match for preg_leaf_backref class before use it!');
    }
    public function name() {
        return 'leaf_backref';
    }
    public function __construct() {
        $this->type = preg_node::TYPE_LEAF_BACKREF;
    }
    public function character() {
        die ('TODO: implements abstract function character for preg_leaf_backref class before use it!');
    }
}


/**
* Operator node
*/
abstract class preg_operator extends preg_node {

    //An array of operands
    public $operands = array();

}


/**
* Finite quantifier node with left and right border
* Unary
* Possible errors: left border is greater than right one
*/
class preg_node_finite_quant extends preg_operator {

    //Is quantifier greed?
    public $greed;
    //Is quantifier posessive?
    public $posessive;
    //Smallest possible repetition number
    public $leftborder;
    //Biggest possible repetition number
    public $rightborder;

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_FINITE_QUANT;
    }

    public function name() {
        return 'node_finite_quant';
    }

}

/**
* Infinite quantifier node with left border only
* Unary
*/
class preg_node_infinite_quant extends preg_operator {

    //Is quantifier greed?
    public $greed;
    //Is quantifier posessive?
    public $posessive;
    //Smallest possible repetition number
    public $leftborder;

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_INFINITE_QUANT;
    }

    public function name() {
        return 'node_infinite_quant';
    }

}

/**
* Concatenation operator
* Binary
*/
class preg_node_concat extends preg_operator {
    public function __construct() {
        $this->type = preg_node::TYPE_NODE_CONCAT;
    }

    public function name() {
        return 'node_concat';
    }

}

/**
* Alternative operator
* Binary
*/
class preg_node_alt extends preg_operator {

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_ALT;
    }

    public function name() {
        return 'node_alt';
    }

}

/**
* Assert with expression within
* Unary
*/
class preg_node_assert extends preg_operator {

    //Positive lookahead assert
    const SUBTYPE_PLA = 1;
    //Negative lookahead assert
    const SUBTYPE_NLA = 2;
    //Positive lookbehind assert
    const SUBTYPE_PLB = 3;
    //Negative lookbehind assert
    const SUBTYPE_NLB = 4;

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_ASSERT;
    }

    public function name() {
        return 'node_assert';
    }

}

/**
* Subpattern
* Unary
*/
class preg_node_subpatt extends preg_operator {

    //Subpattern
    const SUBTYPE_SUBPATT = 1;
    //Once-only subpattern
    const SUBTYPE_ONCEONLY = 22;//for mismatching with SUBTYPE_NLA in parser

    //Subpattern number
    public $number = 0;
    //Subpattern match (if supported)
    public $match = null;

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_SUBPATT;
    }

    public function name() {
        return 'node_subpatt';
    }

}

/**
* Conditional subpattern
* Unary, binary or ternary, first operand is assert expression (if any),  second - yes-pattern, third - no-pattern
* Possible errors: there is no backreference with such number in expression
*/
class preg_node_cond_subpatt extends preg_operator {

    //Subtypes define a type of condition for subpatern
    //Positive lookahead assert
    const SUBTYPE_PLA = 1;
    //Negative lookahead assert
    const SUBTYPE_NLA = 2;
    //Positive lookbehind assert
    const SUBTYPE_PLB = 3;
    //Negative lookbehind assert
    const SUBTYPE_NLB = 4;
    //Backreference 
    const SUBTYPE_BACKREF = 5;
    //Recursive
    const SUBTYPE_RECURSIVE = 6;

    //Subpattern number
    public $number = 0;
    //Subpattern match (if supported)
    public $match = null;
    //Is condition satisfied?
    public $condbranch = null;
    //Backreference number
    public $backrefnumber = -1;

    public function __construct() {
        $this->type = preg_node::TYPE_NODE_COND_SUBPATT;
    }

    public function name() {
        return 'node_cond_subpatt';
    }

}
class preg_node_error extends preg_node {
    public function name() {
        return 'node_error';
    }
    public function __construct() {
        $this->type = preg_node::TYPE_NODE_ERROR;
    }
}


?>