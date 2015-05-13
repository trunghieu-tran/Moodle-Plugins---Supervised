<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines generic token and node classes.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/question/type/poasquestion/classes/string.php');
require_once($CFG->dirroot.'/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot.'/blocks/formal_langs/descriptions/descriptionrule.php');

class block_formal_langs_ast {

    /**
     * AST root node.
     * @var object of 
     */
    private $root;

    /**
     * Basic lexer constructor.
     *
     * @param array $patterns - array of object of a pattern class
     * @param array $conditions - array of strings/conditions
     * @param string $text - input text
     * @param array $options - hash table of options
     * @param string $condition - initial condition
     */
    public function __construct() {
        
    }
    
    private function print_node($node, $args = null) {// TODO - normal printing, maybe using dot
        printf('Node number: %d\n', $node->number());
        printf('Node type: %s\n', $node->type());
        printf('Node position: [%d, %d, %d, %d]\n',
               $node->position()->linestart(),
               $node->position()->colstart(),
               $node->position()->lineend(),
               $node->position()->colend());
        printf('Node description: %s\n', $node->description());
    }

    public function print_tree() {
        traverse($this->root, 'print_node');
    }
    
    public function traverse($node, $callback) {
        // entering node
        if ($node->is_leaf()) {
            // leaf action
            $callback($node, $args);// TODO - what is args?
        }
        foreach ($node->childs as $child) {
            // TODO - why no callback for non-leaf nodes?
            $this->traverse($child, $callback);
        }
    }

    /**
     * Returns list of node objects which requires description.
     *
     * @param $answer - moodle answer object
     * @return array of node objects
     */
    public function nodes_requiring_description_list() {
        // TODO: return node objects
        // TODO - get only nodes requiring user-defined description from the trees
    }
}

/**
 * Describes a position of AST node (terminal or non-terminal) in the original text
 */
class block_formal_langs_node_position {
    protected $linestart;
    protected $lineend;
    protected $colstart;
    protected $colend;
    /** A starting position in string, as sequence of characters
     *  @var int
     */
    protected $stringstart;
    /** An end position in string, as sequence of characters
     *  @var int
     */
    protected $stringend;

    public function linestart() {
        return $this->linestart;
    }

    public function lineend(){
        return $this->lineend;
    }

    public function colstart(){
        return $this->colstart;
    }

    public function colend(){
        return $this->colend;
    }

    public function stringstart() {
        return $this->stringstart;
    }

    public function stringend() {
        return $this->stringend;
    }

    public function __construct($linestart, $lineend, $colstart, $colend, $stringstart = 0, $stringend = 0) {
        $this->linestart = $linestart;
        $this->lineend = $lineend;
        $this->colstart = $colstart;
        $this->colend = $colend;
        $this->stringstart = $stringstart;
        $this->stringend = $stringend;        
    }

    /**
     * Summ positions of array of nodes into one position
     *
     * Resulting position is defined from minimum to maximum postion of nodes
     *
     * @param array $nodepositions positions of adjanced nodes
     * @return block_formal_langs_token_position
     */
    public function summ($nodepositions) {
        $minlinestart = $nodepositions[0]->linestart;
        $maxlineend = $nodepositions[0]->lineend;
        $mincolstart = $nodepositions[0]->colstart;
        $maxcolend = $nodepositions[0]->colend;
        $minstringstart = $nodepositions[0]->stringstart;
        $maxstringend = $nodepositions[0]->stringend;

        foreach ($nodepositions as $node) {
            if ($node->linestart < $minlinestart) {
                $minlinestart = $node->linestart;
                $mincolstart = $node->colstart;
            }
            
            if ($node->linestart == $minlinestart) {
                $mincolstart = min($mincolstart, $node->colstart);
            }
            if ($node->lineend > $maxlineend) {
                $maxlineend = $node->lineend;
                $maxcolend = $node->colend;
            }
            
            if ($node->lineend == $maxlineend) {
                $maxcolend = max($maxcolend, $node->colend);
            }

            $minstringstart = min($minstringstart, $node->stringstart);
            $maxstringend = max($maxstringend, $node->stringend);
        }

        return new block_formal_langs_node_position($minlinestart, $maxlineend, $mincolstart, $maxcolend, $minstringstart, $maxstringend);
    }
}

class block_formal_langs_ast_node_base {

    /**
     * Type of node.
     * @var string
     */
    protected $type;

    /**
     * Node position - c.g. block_formal_langs_node_position object
     */
    protected $position;

    /**
     * Node number in a tree.
     * @var integer
     */
    protected $number;

    /**
     * Child nodes.
     * @var array of ast_node_base
     */
    public $childs;

    /**
     * True if this node needs user-defined description
     * @var bool
     */
    protected $needuserdescription;

    /**
     * Node description.
     * @var string
     */
    protected $description;

    /**
     * A rule for generating node description
     * @var block_formal_langs_description_rule
     */
    public $rule;

    public function __construct($type, $position, $number, $needuserdescription) {
        $this->number = $number;
        $this->type = $type;
        $this->position = $position;
        $this->needuserdescription = $needuserdescription;

        $this->childs = array();
        $this->description = '';
        $this->rule = null;
    }

    /**
     * Returns actual type of the token.
     *
     * Usually will be overloaded in child classes to return constant string.
     */
    public function type() {
        return $this->type;
    }

    public function number() {
        return $this->number;
    }

    /**
     * Returns position for tokem or a position for most left position
     * @return block_formal_langs_node_position
     */
    public function position() {
        if ($this->position == null) {
            if (count($this->childs())) {
                $children = $this->childs();
                /** @var block_formal_langs_ast_node_base $firstchild */
                $firstchild = $children[0];
                /** @var block_formal_langs_ast_node_base $lastchild */
                $lastchild  = $children[count($children) - 1];
                $firstchildpos = $firstchild->position();
                $lastchildpos = $lastchild->position();
                $this->position = new block_formal_langs_node_position(
                    $firstchildpos->linestart(),
                    $lastchildpos->lineend(),
                    $firstchildpos->colstart(),
                    $lastchildpos->colend(),
                    $firstchildpos->stringstart(),
                    $lastchildpos->stringend()
                );
            } else {
                $this->position = new block_formal_langs_node_position( 0, 0, 0, 0, 0, 0);
            }
        }
        return $this->position;
    }

    public function need_user_description() {
        return $this->needuserdescription;
    }

    public function description() {
        if (!$this->needuserdescription) {
            // TODO: calc description
            return $this->description;
        } else {
            return $this->description;
        }
    }

    public function set_description($str) {
        $this->description = $str;
    }

    public function childs() {
        return $this->childs;
    }
    
    public function set_childs($childs) {
        $this->childs = $childs;
    }

    public function add_child($node) {
        array_push($this->childs, $node);
    }

    public function is_leaf() {
        if (0 == count($this->childs)) {
            return true;
        }
        return false;
    }

    /**
     * Returns value for node
     * @return string value for text of node
     */
    public function value() {
        $values = array();
        foreach($this->childs() as $child) {
            /** @var block_formal_langs_ast_node_base $child */
            $data = $child->value();
            if ($data != null) {
                if (is_object($data)) {
                    /** @var qtype_poasquestion\string $data */
                    $data = $data->string();
                }
                $values[] = $data;
            }
        }

        return implode(' ', $values);
    }

    /**
     * Returns list of tokens, covered by AST node. Tokens determined as not having any children
     * @return array list of tokens
     */
    public function tokens_list() {
        $childcount = count($this->childs());
        $children = $this->childs();
        $result = array();
        if (count($childcount) == 0 || $children === null || !is_array($children)) {
            $result[] = $this;
        } else {
            /** @var block_formal_langs_ast_node_base $child */
            foreach($children as $child) {
                $tmp = $child->tokens_list();
                if (count($result) == 0) {
                    $result = $tmp;
                } else {
                    $result = array_merge($result, $tmp);
                }
            }
        }
        return $result;
    }
}

/**
 * Class for options, controlling strings comparison process.
 */
class block_formal_langs_comparing_options {
    /**
     * @var bool true if comparing is case sensitive, false if insensitive
     */
    public $usecase;
}

/**
 * Class for base tokens.
 *
 * Class for storing tokens. Class - token, object of the token class
 * - lexeme.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_token_base extends block_formal_langs_ast_node_base {

    /**
     * Semantic value of node.
     * @var string
     */
    protected $value;

    /**
     * Index of token in the stream it belongs to.
     *
     * For tokens it's often important to know index in the stream array, not just position in the text
     * @var integer
     */
    protected $tokenindex;

    /**
     * A cache for Damerau-Levenshtein
     * @var array
     */
    protected static $dameraulevensteincache = array();

    public function number() {
        if ($this->number === null) {
            $this->number = $this->tokenindex;
        }
        return $this->number;
    }

    public function value() {
        return $this->value;
    }

    public function token_index() {
        return $this->tokenindex;
    }

    public function set_token_index($newindex) {
        $this->tokenindex = $newindex;
    }

    /**
     * Basic lexeme constructor.
     *
     * @param string $type - type of lexeme
     * @param string $value - semantic value of lexeme
     * @return base_token
     */
    public function __construct($number, $type, $value, $position, $index) {
        $this->number = $number;
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
        $this->tokenindex = $index;
    }

    /**
     * Returns name of lexeme kind
     * @return name of lexeme kind
     */
    public function name() {
        $className = get_class($this);
        $name = str_replace('block_formal_langs_', '', $className);
        return $name;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are 
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }

    /**
     * Calculates and return editing distance from current token to other token
     * @param block_formal_langs_token_base $token other token
     * @param block_formal_langs_comparing_options $options options for comparing tokens
     * @return int operations length
     */
    public function editing_distance($token, block_formal_langs_comparing_options $options) {
        if ($this->is_same($token, $options)) {// If two tokens are identical, return 0.
            return 0;
        }
        if ($this->use_editing_distance() && $token->use_editing_distance()) {
            $distance = block_formal_langs_token_base::damerau_levenshtein($this->value(), $token->value(), $options);
        } else {// Distance not applicable, so return a big number.
            $distance = core_text::strlen($this->value()) + core_text::strlen($token->value());
        }
        return $distance;
    }

    /* Calculates Damerau-Levenshtein distance between two strings
     *
     * @return int Damerau-Levenshtein distance
     */
    static public function damerau_levenshtein($str1, $str2, block_formal_langs_comparing_options $options) {
        if ($options->usecase == false) {
            $str1 = core_text::strtolower($str1);
            $str2 = core_text::strtolower($str2);
        }
        if ($str1 == $str2) {
            return 0;// words identical
        }
        $lenstr1 = core_text::strlen($str1);
        $lenstr2 = core_text::strlen($str2);
        // zero length of words
        if ($lenstr1 == 0) {
            return $lenstr2;
        } else {
            if ($lenstr2 == 0) {
                return $lenstr1;
            }
        }
        // matrix [lenstr1+1][lenstr2+1]
        for ($i = 0; $i < $lenstr1; $i++) {
            for ($j = 0; $j < $lenstr2+1; $j++) {
                    $matrix[$i][$j] = 0;
            }
        }
        // fill in the first row and column
        for ($i = 0; $i <= $lenstr1; $i++) {
            $matrix[$i][0] = $i;
        }
        for ($j = 0; $j <= $lenstr2; $j++) {
            $matrix[0][$j] = $j;
        }
        // calculation
        for ($i = 1; $i <= $lenstr1; $i++) {
            for ($j = 1; $j <= $lenstr2; $j++) {
                $cellup = $matrix[$i-1][$j]+1;// deletion
                $cellleft = $matrix[$i][$j-1]+1;// insertion
                if ($str1[$i-1] == $str2[$j-1]) {
                    $cost=0;
                } else {
                    $cost=1;
                }
                $celldiag = $matrix[$i-1][$j-1] + $cost;// replacement
                $matrix[$i][$j] = min(min($cellup, $cellleft), $celldiag);
                if ($i>1 && $j>1 && $str1[$i-1] == $str2[$j-2] && $str1[$i-2] == $str2[$j-1]) {
                    $matrix[$i][$j] = min($matrix[$i][$j], $matrix[$i-2][$j-2] + $cost);// transposition
                }
            }
        }
        return $matrix[$lenstr1][$lenstr2];
    }

    /* Calculates redaction between two strings.
     *
     * @return str redaction distance
     */
    static public function redaction($str1, $str2) {
        $sstr1 = $str1;
        if (!is_string($sstr1)) {
            $sstr1 = $sstr1->string();
        }
        $sstr2 = $str2;
        if (!is_string($sstr2)) {
            $sstr2 = $sstr2->string();
        }
        if (array_key_exists($sstr1, self::$dameraulevensteincache)) {
            if (array_key_exists($sstr2, self::$dameraulevensteincache[$sstr1])) {
                return self::$dameraulevensteincache[$sstr1][$sstr2];
            }
        }

        $cache = array();
        $dameraulevensteinrecursive = function($a, $i, $b, $j,&$cache) use(&$dameraulevensteinrecursive) {
            /** @var Closure $dameraulevensteinrecursive */
            $ki = min($i, $j);

            $cacheindex = $i . " . "  . $j;
            if (array_key_exists($cacheindex, $cache)) {
                return $cache[$cacheindex];
            }
            if ($ki == -1) {
                $ops = "";
                if ($i != -1 || $j != -1) {
                    if ($j > $i) {
                        $ops = str_repeat('i', $j + 1);
                    } else {
                        $ops = str_repeat('d', $i + 1);
                    }
                }
                $cache[$cacheindex] = array(max($i, $j), $ops);
                return array(max($i, $j), "");
            }
            $results = array();
            $ok = false;

            $calltransform = function($deci, $decj, $adddist, $prependop) use($a, $i, $b, $j, &$cache, $dameraulevensteinrecursive) {
                list($dist, $op) = $dameraulevensteinrecursive($a, $i - $deci, $b, $j - $decj, $cache);
                $dist += $adddist;
                $op =  $op . $prependop;
                return array($dist, $op);
            };
            $repop = function() use($a, $i, $b, $j) {
                $bj = core_text::substr($b, $j, 1);
                $ai = core_text::substr($a, $i, 1);

                $op = 'r';
                $add = 1;
                if ($ai == $bj) {
                    $add = 0;
                    $op = 'm';
                }
                return array($add, $op);
            };
            if ($i > 0 && $j > 0) {
                $ami = core_text::substr($a, $i - 1, 1);
                $bj = core_text::substr($b, $j, 1);

                $ai = core_text::substr($a, $i, 1);
                $bmj = core_text::substr($b, $j - 1, 1);

                if ($ami == $bj && $ai == $bmj) {
                    $results[] = $calltransform(1, 0, 1, 'd');
                    $results[] = $calltransform(0, 1, 1, 'i');

                    list($add, $op) = $repop();
                    $results[] = $calltransform(1, 1, $add, $op);
                    $results[] = $calltransform(2, 2, 1, 't');

                    $ok = true;
                }
            }

            if (!$ok)
            {
                $results[] = $calltransform(1, 0, 1, 'd');
                $results[] = $calltransform(0, 1, 1, 'i');

                list($add, $op) = $repop();
                $results[] = $calltransform(1, 1, $add, $op);
            }
            //echo "<scan $i $j>\n";
            $result = array_shift($results);
            foreach($results as $test) {
                //var_dump($test);
                if ($test[0] < $result[0]) {
                    $result = $test;
                }
            }
            $cache[$cacheindex] = $result;
            //echo "</scan>\n";
            return $result;
        };
        $result =  $dameraulevensteinrecursive($str1, core_text::strlen($str1) - 1, $str2, core_text::strlen($str2) - 1, $cache);
        /*
        echo PHP_EOL;
        echo '     ';
        for($j = -1; $j < core_text::strlen($str2); $j++) {
            $letter = '';
            if ($j > -1) {
                $letter = core_text::substr($str2, $j, 1);
            }
            echo str_pad($letter, 11, ' ');
        }
        echo PHP_EOL;
        echo '     ';
        for($j = -1; $j < core_text::strlen($str2); $j++) {
            $letter = (string)$j;
            echo str_pad($letter, 11, ' ');
        }
        echo PHP_EOL;
        for($i = -1; $i < core_text::strlen($str1); $i++) {
            $letter = ' ';
            if ($i > - 1) {
                $letter = core_text::substr($str1, $i, 1);
            }
            if ($i != -1) {
                echo str_pad($letter . ' ' . $i . ':', 5, ' ');
            } else {
                echo ' -1: ';
            }
            for($j = -1; $j < core_text::strlen($str2); $j++) {
                $cacheindex = $i . " . "  . $j;
                echo str_pad($cache[$cacheindex][0] . ',' . $cache[$cacheindex][1], 11, ' ');
            }

            echo PHP_EOL;
        }
        */
        // Index fix
        $result[0] += 1;
        if (array_key_exists((string)$sstr1, self::$dameraulevensteincache) == false) {
            self::$dameraulevensteincache[$sstr1] = array();
        }
        self::$dameraulevensteincache[$sstr1][$sstr2] = $result[1];
        gc_collect_cycles();
        return $result[1];
    }
     /* Calculates possible pair
     *
     * @return distance if possible or -1 if no possible
     */
    public function possible_pair($token, $max, $options) {
        $str1 = $this->value;
        $str2 = $token->value;
        $distance = $this->editing_distance($token, $options);// define the distance of damerau-levenshtein
        if ($distance<=$max) {
            return $distance;
        } else {
            return -1;
        }
    }

    public function check_specific_error ($token) {
        return 0;
    }
    /*
    public function search_specific_error_on_list ($token, $specific_lexems_list) {
        $flag=0;
        for ($i=0; $i<count($specific_lexems_list); $i++){
            if ($this->value==$specific_lexems_list[$i]->value) {
                for ($j=0; $j<count($specific_lexems_list);$j++) {
                    if ($token->value==$specific_lexems_list[$i]->value) {
                        $flag=1;
                    }
                }
            }
        }
        return $flag;
    }
*/
    /**
     * Base lexical mistakes handler. Looks for possible matches for this
     * token in other answer and return an array of them.
     *
     * The functions works differently depending on token of which answer it's called.
     * For correct text (e.g. _answer_) $iscorrect == true and it looks for typos, extra separators,
     * typical mistakes (in particular subclasses) etc - i.e. all mistakes with one token from correct text.
     * For compared text (e.g. student's _response_) it looks for missing separators, extra quotes etc,
     * i.e. mistakes which have more than one token from correct, but only one from compared text.
     *
     * @param array $other - array of tokens  (other text)
     * @param integer $threshold - lexical mistakes threshold
     * @param boolean $iscorrect - true if type of token is correct and we should perform full search, false for compared text
     * @param $options - comparing options (like case sensitivity)
     * @return array - array of block_formal_langs_matched_tokens_pair objects with blank
     * $answertokens or $responsetokens field inside (it is filling from outside)
     */
    public function look_for_matches($other, $threshold, $iscorrect, block_formal_langs_comparing_options $options, $bypass) {      
	    if ($bypass == true) {
            $possiblepairs = array();
            for ($k = 0; $k < count($other); $k++) {
                $str1 = $other[$k]->value;
                $str2 = $this->value;
                if ($options->usecase == false) {
                    $str1 = core_text::strtolower($str1);
                    $str2 = core_text::strtolower($str2);
                }
                if($str1 == $str2) {
                    $pair = new block_formal_langs_matched_tokens_pair(array($this->tokenindex), array($k), 0, false, '');
                    $possiblepairs[] = $pair;
                }
            }
        } else {
            // TODO: generic mistakes handling
            $result = core_text::strlen($this->value) * $threshold;
            $str = '';
            $possiblepairs = array();
            for ($k=0; $k < count($other); $k++) {
                // incorrect lexem
                if ($iscorrect == true) {
                    $max = floor($result);
                    // possible pair (typo)
                    $dist = $this->possible_pair($other[$k], $max, $options);
                    if ($dist != -1) {
                        //echo "Generated pair between " . $this->value() . " and " . $other[$k]->value();
                        //echo PHP_EOL;
                        if ($this->check_specific_error($other[$k])) {
                            $pair = new block_formal_langs_typo_pair(array($this->tokenindex), array($k), $dist, true, '');
                        } else {
//                            if ($this->search_specific_error_on_list($other[$k], $specific_lexem_list)) {
//                                $pair = new block_formal_langs_typo_pair(array($this->tokenindex), array($k), $dist, true, '');
//                            } else {
                                  $pair = new block_formal_langs_typo_pair(array($this->tokenindex), array($k), $dist, false, '');
//                            }
                        }
                        ////////////////////////////////////////////////////////////////
                        $thisvalue = $this->value;
                        $otherkvalue = $other[$k]->value;
                        if ($options->usecase == false) {
                            $thisvalue = core_text::strtolower($thisvalue);
                            $otherkvalue = core_text::strtolower($otherkvalue);
                        }
                        $pair->operations=$this->redaction($otherkvalue, $thisvalue);
                        ////////////////////////////////////////////////////////////////
                        $possiblepairs[] = $pair;
                        /*
                        $result = $this->additional_generation($other[$k]);
                        if (count ($result)>0) {
                            for ($i=0; $i<count($result); $i++) {
                                $possiblepairs[]=$result[$i];
                            }
                        }
                        */
                    }
                    // possible pair (extra separator)
                    if ($k+1 != count($other)) {
                        $max = 1;
                        $str = $str.($other[$k]->value).("\x0d").($other[$k+1]->value);
                        $lexem = new block_formal_langs_token_base(null, 'type', $str, null, 0);
                        $dist = $this->possible_pair($lexem, $max, $options);
                        if ($dist != -1) {
                            $pair = new block_formal_langs_matched_tokens_pair(array($this->tokenindex), array($k, $k+1), $dist, false, '');
                            $possiblepairs[] = $pair;
                        }
                        $str='';
                    }
                } else {
                    // possible pair (missing separator)
                    if ($k+1 != count($other)) {
                        $max = 1;
                        $str = $str.($other[$k]->value).("\x0d").($other[$k+1]->value);
                        $lexem = new block_formal_langs_token_base(null, 'type', $str, null, 0);
                        $dist = $this->possible_pair($lexem, $max, $options);
                        if ($dist != -1) {
                            $pair = new block_formal_langs_matched_tokens_pair(array($k, $k+1), array($this->tokenindex), $dist, false, '');
                            $possiblepairs[] = $pair;
                        }
                        $str = '';
                    }
                }
            }
        }
        return $possiblepairs;
    }
    
    /**
     * Returns a string caseinsensitive semantic value of token
     * @return string
     */
    protected function string_caseinsensitive_value() {
        $value = $this->value;
        if (is_object($this->value)) {
            /** @var qtype_poasquestion\string $value */
            $value = clone $value;
            $value->tolower();
            $value = $value->string();
        } else {
            $value = core_text::strtolower($value);
        }
        return $value;
    }
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *  
     * @param block_formal_langs_token_base $other other lexeme
     * @param block_formal_langs_comparing_options $options options for comparing lexmes
     * @return boolean - if the same lexeme
     */
    public function is_same($other, $options ) {
        $result = false;
        if ($this->type == $other->type) {
            if ($options->usecase) {
                $result = $this->value == $other->value;
            } else {
                $cellleft = $this->string_caseinsensitive_value();
                $right = $other->string_caseinsensitive_value();
                $result = $cellleft == $right;
            }
        }
        return $result;
    }
}

/**
 * Class for matched pairs (correct answer and student response).
 *
 * Instances of this class map groups of tokens from correct answer
 * to groups of token in student response.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_matched_tokens_pair {

    // No mistake in this pair, all is correct.
    const TYPE_NO_MISTAKE = 0;
    // Mistake is a typo, measured by Damerau-Levenshtein distance.
    const TYPE_TYPO = 1;
    // Mistake is an extra separator.
    const TYPE_EXTRA_SEPARATOR = 2;
    // Mistake is a missing separator.
    const TYPE_MISSING_SEPARATOR = 3;
    // This is a token-type specific mistake.
    const TYPE_SPECIFIC_MISTAKE = 999999;

    /**
     * Indexes of the correct text tokens.
     * @var array
     */
    public $correcttokens;

    /**
     * Indexes of the compared text tokens.
     * @var array
     */
    public $comparedtokens;

    /**
     * Mistake weight (Damerau-Levenshtein distance, for example).
     *
     * Zero is no mistake.
     *
     * @var integer
     */
    public $mistakeweight;

    /**
     * Type of mistake - e.g. typo, extra or missing separator, specific mistake types.
     * TODO - does we really need to have subtypes (for specific mistake or no mistake pairs) with messageid which actually acts as one?
     * @var array
     */
    public $type;

    /**
     * Mistake message identifier for the get_string() function.
     * TODO - describe format for $a object
     * @var string
     */
    public $messageid;
    
    public $operations;

    public function __construct($correcttokens, $comparedtokens, $mistakeweight, $specific = false, $messageid = '') {
        $this->correcttokens = $correcttokens;
        $this->comparedtokens = $comparedtokens;
        $this->mistakeweight = $mistakeweight;
        if ($specific) {// This mistake is a lexem-type specific mistake.
            if ($mistakeweight == 0) {
                $this->type = self::TYPE_NO_MISTAKE;
                $this->messageid = '';
            } else {
                $this->type = self::TYPE_SPECIFIC_MISTAKE;
                $this->messageid = $messageid;
            }
        } else {// This mistake is a general mistake.
            if ($mistakeweight == 0) {
                $this->type = self::TYPE_NO_MISTAKE;
                $this->messageid = '';
            } else if (count($correcttokens) > 1) {
                $this->type = self::TYPE_MISSING_SEPARATOR;
                $this->messageid = 'missingseparatormsg';
            } else if (count($comparedtokens) > 1) {
                $this->type = self::TYPE_EXTRA_SEPARATOR;
                $this->messageid = 'extraseparatormsg';
            } else {
                $this->type = self::TYPE_TYPO;
                $this->messageid = 'typomsg';
            }
        }
    }

    /**
     * Returns a message about mistake give two processed strings.
     * @param correctstring block_formal_langs_processed_string object for the correct string (created from db).
     * @param comparedstring block_formal_langs_processed_string object for compared string (created from string).
     * @return user language message string, describing a possible mistake this pair represents.
     */
    public function message($correctstring, $comparedstring) {
        if ($this->type == self::TYPE_NO_MISTAKE) {// Full match, no mistake.
            return '';
        }

        // Create stringified corrected tokens
        $strings = array();
        $comparedmessage = '';
        foreach ($this->comparedtokens as $index) {
            /** @var block_formal_langs_token_base $token */
            $token = $comparedstring->stream->tokens[$index];
            /** @var qtype_poasquestion\string $string */
            $string = $token->value();
            $value = $string;
            if (is_object($string)) {
                /** @var qtype_poasquestion\string $string */
                $value = $string->string();
            }
            $strings[] = $value;
        }
        if (count($strings)) {
            $comparedmessage = implode(' ', $strings);
            $comparedmessage = get_string('quote', 'block_formal_langs', $comparedmessage);
        }

        $a = new stdClass();
        $a->mistakeweight = $this->mistakeweight;

        if ($this->type != self::TYPE_MISSING_SEPARATOR) { // Handle typo
            $i = 0;
            foreach ($this->correcttokens as $index) {
                $name = 'correct'.$i;
                if ($correctstring->has_description($index)) {
                    $a->$name = $correctstring->node_description($index);
                } else {
                    $a->$name = $comparedmessage;
                }
                $i++;
            }
        } else {
            // Test if every correct token has a description
            $hasdescriptions = true;
            foreach ($this->correcttokens as $index) {
                $hasdescriptions = $hasdescriptions && $correctstring->has_description($index);
            }
            if ($hasdescriptions) {
                $i = 0;
                foreach ($this->correcttokens as $index) {
                    $name = 'correct'.$i;
                    $a->$name = $correctstring->node_description($index);
                    $i++;
                }
            } else {
                // We should use another message to hint student
                $this->messageid = 'missingseparatornodescriptionmsg';
                $a->compared = $comparedmessage;
            }
        }

        return get_string($this->messageid, 'block_formal_langs', $a);
    }
}

class block_formal_langs_typo_pair extends block_formal_langs_matched_tokens_pair {

    /**
     * A string with editing operators.
     * @var string
     * derived from function redaction($str1, $str2)
     * 'i' - insert, 'm' - no operation, 'd' - deletion, 'r' - replacement
     */
     public $editops='';
}

/**
 * Represents a stream of tokens
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_token_stream {
    /**
     * Tokens's array
     *
     * @var array of block_formal_langs_token_base childs
     */
    public $tokens;

    /**
     * Lexical errors
     *
     * @var array of block_formal_langs_lexical_errors object
     */
    public $errors;

    /**
     * Returns time limit for recursive backtracking
     * @return int
     */
    public static function time_limit() {
        global $CFG;
        if ($CFG->block_formal_langs_maximum_lexical_backracking_execution_time <= 0) {
            return 30; // Default value is 30 seconds
        }
        return $CFG->block_formal_langs_maximum_lexical_backracking_execution_time;
    }

    public function __clone() {
        // PHP 5.3.3, which is required by Moodle 2.5, supports anonymous functions
        // so we go for that
        $clonearray = function($array) {
            $clone = function($o) {
                return clone $o;
            };
            $result = array();
            if (is_array($array)) {
                $result = array_map($clone, $array);
            }
            return $result;
        };
        $this->tokens = $clonearray($this->tokens);
        $this->errors = $clonearray($this->errors);
    }

    /**
     * Set token indexes traversing array of tokens from cellleft to right
     *
     * Use to restore indexes after inserting or removing tokens (c.e. correct_mistakes)
     */
    public function set_token_indexes() {
        // TODO Birukova
    }

    /**
     * Compares compared stream of tokens with this (correct) stream looking for
     * matches with possible errors in tokens (but not in their placement)
     *
     * @param block_formal_langs_token_stream $comparedstream object of block_formal_langs_token_stream to compare with this, may contain errors
     * @param float $threshold editing distance threshold (in percents to token length)
     * @param block_formal_langs_comparing_options $options options data
     * @param bool $bypass bypass for pairs
     * @return array of block_formal_langs_matches_group objects
     */
    public function look_for_token_pairs($comparedstream, $threshold, block_formal_langs_comparing_options $options, $bypass) {
        // TODO Birukova
        // 1. Find matched pairs (typos, typical errors etc) - Birukova
        //  - look_for_matches function
        // 2. Find best groups of pairs - Birukova
        //  - group_matches function, with criteria defined by compare_matches_groups function
        if ($bypass == false) {
            $bestgroups = array();
            $startingtime = time() - 1; // 1 second is precision offset to make sure, that we won't go far from limit
            $allpossiblepairs = $this->look_for_matches($comparedstream, $threshold, $options);
            if (count($allpossiblepairs)>0) {
                $bestgroups = $this->group_matches($allpossiblepairs, $startingtime);
            }
        } else {
            $bestgroups = array();
            $allpossiblepairs = $this->look_for_matches_for_bypass($comparedstream, $threshold, $options);
            if (count($allpossiblepairs)>0) {
                $bestgroups = $this->group_matches_for_bypass($allpossiblepairs);
            }
        }
        return $bestgroups;
    }
    
    /**
     * Creates an array of all possible matched pairs using this stream as correct one.
     *
     * Uses token's look_for_matches function and fill necessary fields in matched_tokens_pair objects.
     *
     * @param comparedstream object of block_formal_langs_token_stream to compare with this, may contain errors
     * @param $threshold threshold as a fraction of token length for creating pairs
     * @return array array of matched_tokens_pair objects representing all possible pairs within threshold
     */
    public function look_for_matches($comparedstream, $threshold, block_formal_langs_comparing_options $options) {
        $bypass=false;
        // TODO Birukova
        $tokens = $this->tokens;
        $allpossiblepairs = array();
        $mappings = array(
            'lockedcorrect' => array(),
            'lockedcompared' => array(),
            'correct' => array(),
        );
        // search for correct tokens
        for ($i=0; $i<count($tokens); $i++) {
            /** @var block_formal_langs_token_base $token */
            $token  = $tokens[$i];
            $pairs = $token->look_for_matches($comparedstream->tokens, $threshold, true, $options, $bypass);
            for ($j=0; $j<count($pairs); $j++) {
                /** @var block_formal_langs_matched_tokens_pair $pair */
                $pair = $pairs[$j];
                self::try_add_mapping_for_pair($pair, $mappings, count($allpossiblepairs));
                $allpossiblepairs[] = $pair;
            }
        }
        // search for compared tokens
        for ($i=0; $i<count($comparedstream->tokens); $i++) {
            /** @var block_formal_langs_token_base $token */
            $token  = $comparedstream->tokens[$i];
            $pairs = $token->look_for_matches($this->tokens, $threshold, false, $options, $bypass);
            for ($j=0; $j<count($pairs); $j++) {
                /** @var block_formal_langs_matched_tokens_pair $pair */
                $pair = $pairs[$j];
                self::try_add_mapping_for_pair($pair, $mappings, count($allpossiblepairs));
                $allpossiblepairs[] = $pair;
            }
        }

        self::filter_matches_with_correct_indexes_and_zero_weight($allpossiblepairs, $mappings);
        return $allpossiblepairs;
    }


    /**
     * Adds mapping for pair if it consists of one part
     * @param block_formal_langs_matched_tokens_pair $pair a pair
     * @param array $correctmappings a mappings
     * @param int $count count
     */
    public static function try_add_mapping_for_pair($pair, &$mappings, $count) {
        if (count($pair->correcttokens) == 1
            && count($pair->comparedtokens) == 1
            && (abs($pair->mistakeweight) < 0.00001)) {
            $tokenindex = $pair->correcttokens[0];
            $comparedtokenindex = $pair->comparedtokens[0];
            if (array_key_exists($tokenindex, $mappings['lockedcorrect']) == false
                && array_key_exists($comparedtokenindex, $mappings['lockedcompared']) == false) {
                if (array_key_exists($tokenindex, $mappings['correct']) == false) {
                    $mappings['correct'][$tokenindex] = array(
                        'correct' => array($tokenindex),
                        'compared'=> array($comparedtokenindex => 1),
                        'pairs' => array(
                            $comparedtokenindex => array( $count )
                        )
                    );
                } else {
                    $mappings['correct'][$tokenindex]['compared'][$comparedtokenindex] = 1;
                    if (array_key_exists($comparedtokenindex, $mappings['correct'][$tokenindex]['pairs'])) {
                        $mappings['correct'][$tokenindex]['pairs'][$comparedtokenindex][] = $count;
                    } else {
                        $mappings['correct'][$tokenindex]['pairs'][$comparedtokenindex] = array( $count );
                    }
                }
            }
        } else {
            for($i = 0; $i < count($pair->correcttokens); $i++) {
                $tokenindex = $pair->correcttokens[$i];
                $mappings['lockedcorrect'][$tokenindex] = 1;
                if (array_key_exists($tokenindex, $mappings['correct'])) {
                    unset($mappings['correct'][$tokenindex]);
                }
            }
            for($i = 0; $i < count($pair->comparedtokens); $i++) {
                $tokenindex = $pair->comparedtokens[$i];
                $mappings['lockedcompared'][$tokenindex] = 1;
                foreach($mappings['correct'] as $id => &$setcomponents) {
                    if (array_key_exists($tokenindex, $setcomponents['compared'])) {
                        unset($setcomponents['compared'][$tokenindex]);
                    }
                    if (array_key_exists($tokenindex, $setcomponents['pairs'])) {
                        unset($setcomponents['pairs'][$tokenindex]);
                    }
                }
            }
        }
    }

    /**
     * Filters odd matches, using Ford-Fulkerson algorithm
     * @param array $allpossiblepairs list of all pairs in matches
     * @param array $correct list of correct token indexes
     * @param array $compared list of compared token indexes
     * @param array $pairindexes list of pair indexes
     */
    public static function filter_matches_via_ffa(
        &$allpossiblepairs,
        &$correct,
        &$compared,
        &$pairindexes
    ) {
        $max = max(count($correct), count($compared));
        $sources = array();
        $sinks = array();
        $flows  = array();
        for($i = 0; $i < count($pairindexes); $i++) {
            $flows[$pairindexes[$i]] = 0;
        }
        if (count($correct) > count($compared)) {
            for($i = 0; $i < count($correct); $i++) {
                $sources[$correct[$i]] = 1;
            }
            $countcompared = count($compared);
            for($i = 0; $i < $countcompared - 1; $i++) {
                $sinks[$compared[$i]] = 1;
            }
            $sinks[$compared[$countcompared - 1]] =  1 + $max - $countcompared;
        } else {
            $countcorrect = count($correct);
            for($i = 0; $i < $countcorrect - 1; $i++) {
                $sources[$correct[$i]] = 1;
            }
            $sources[$correct[$countcorrect - 1]] = 1 + $max - $countcorrect;
            for($i = 0; $i < count($compared); $i++) {
                $sinks[$compared[$i]] = 1;
            }
        }
        // run FFA for flow, with maximal flow through edge as 1
        foreach($flows as $pairindex => &$flowvalue) {
            /** @var block_formal_langs_matched_tokens_pair $pair */
            $pair = $allpossiblepairs[$pairindex];
            $correctindex = $pair->correcttokens[0];
            $comparedindex = $pair->comparedtokens[0];
            // If both source and sink are available, decrement their availability
            if ($sources[$correctindex] > 0 && $sinks[$comparedindex] > 0) {
                $sources[$correctindex] -= 1;
                $sinks[$comparedindex] -= 1;
                $flowvalue++;
            }
        }
        // Filter out pairs with zero flow
        foreach($flows as $pairindex => &$flowvalue) {
            if ($flowvalue == 0) {
                unset($allpossiblepairs[$pairindex]);
            }
        }
    }
    /**
     * Filters exchangeable matches with zero weight
     * @param array $allpossiblepairs pairs
     * @param array $mappings mappings
     */
    public static function filter_matches_with_correct_indexes_and_zero_weight(&$allpossiblepairs, &$mappings) {
        $changed = false;
        /**   Create a triples as <Indexes of correct tokens; Set of adjacent compared tokens (index to 1); list of pairs,
         *    which create this set>
         */
        $mappings = $mappings['correct'];
        if (count($mappings)) {
            foreach($mappings as $key => &$value) {
                $pairs = array();
                foreach($value['pairs'] as $index => $sourcepairs) {
                    if (count($pairs)) {
                        $pairs = array_merge($pairs, $sourcepairs);
                    } else {
                        $pairs = $sourcepairs;
                    }
                }
                $value['pairs'] = $pairs;
            }
            $mappings = array_values($mappings);
            /**
             *  Transfrom mappings into sets of complete bipartite graphs, where pairs represent edged
             *  and compared and correct sets are vertices
             */
            for($i = 0; $i < count($mappings); $i++) {
                for($j = $i + 1; $j < count($mappings); $j++) {
                    if ($mappings[$i]['compared'] == $mappings[$j]['compared']) {
                        $mappings[$i]['correct'] = array_merge($mappings[$i]['correct'], $mappings[$j]['correct']);
                        $mappings[$i]['pairs'] = array_merge($mappings[$i]['pairs'], $mappings[$j]['pairs']);
                        unset($mappings[$j]);
                        $mappings = array_values($mappings);
                        $j--;
                    }
                }
                $mappings[$i]['compared'] = array_keys($mappings[$i]['compared']);
                if (count($mappings[$i]['correct']) > 1 && count($mappings[$i]['compared']) > 0) {
                    $changed = true;
                    self::filter_matches_via_ffa(
                        $allpossiblepairs,
                        $mappings[$i]['correct'],
                        $mappings[$i]['compared'],
                        $mappings[$i]['pairs']
                    );
                }
            }
        }

        if ($changed) {
            $allpossiblepairs = array_values($allpossiblepairs);
        }
    }

    public function look_for_matches_for_bypass($comparedstream, $threshold, block_formal_langs_comparing_options $options) {
        $bypass = true;
        $tokens = $this->tokens;
        $allpossiblepairs = array();
        $mappings = array(
            'lockedcorrect' => array(),
            'lockedcompared' => array(),
            'correct' => array(),
        );
        // search for correct tokens
        for ($i=0; $i<count($tokens); $i++) {
            /** @var block_formal_langs_token_base $token */
            $token = $tokens[$i];
            $pairs = $token->look_for_matches($comparedstream->tokens, $threshold, true, $options, $bypass);
            for ($j=0; $j<count($pairs); $j++) {
                /** @var block_formal_langs_matched_tokens_pair $pair */
                $pair = $pairs[$j];
                self::try_add_mapping_for_pair($pair, $mappings, count($allpossiblepairs));
                $allpossiblepairs[] = $pair;
            }
        }

        self::filter_matches_with_correct_indexes_and_zero_weight($allpossiblepairs, $mappings);
        return $allpossiblepairs;
    }

    /** Create mapping from indexes of compared and correct tokens of pairs to indexes
     *  of pairs, which contain them, allowing us to remove pairs, related to compared or correct token from
     *  array of pairs in (O(1) + O(m), m <= count($matches))
     *  @param array $matches of block_formal_langs_matched_tokens_pair
     *  @return stdClass a pair of mappings ("compared" field contains mappings from compared string, "correct"
     *                   fields contains mappings from correct string)
     */
    public function generate_mapping_of_token_indexes_to_matches($matches) {
        /**
         * This stdClass contains mappings from indexes of compared and correct tokens
         * to indexes from matches array, allowing us to remove pairs, related to current compared and correct tokens
         * from array of pairs very fast ( O(1) + O(m), m <= count($matches) )
         *
         * After current part, we fill it with mapping
         */
        $tokenindexestomatches = new stdClass();
        $tokenindexestomatches->compared = array();
        $tokenindexestomatches->correct = array();
        for($i = 0; $i < count($matches); $i++) {
            /** @var block_formal_langs_matched_tokens_pair $match */
            $match = $matches[$i];
            for($j = 0; $j < count($match->correcttokens); $j++) {
                $index = $match->correcttokens[$j];
                if (array_key_exists($index, $tokenindexestomatches->correct) == false) {
                    $tokenindexestomatches->correct[$index] = array();
                }
                $tokenindexestomatches->correct[$index][] = $i;
            }

            for($j = 0; $j < count($match->comparedtokens); $j++) {
                $index = $match->comparedtokens[$j];
                if (array_key_exists($index, $tokenindexestomatches->compared) == false) {
                    $tokenindexestomatches->compared[$index] = array();
                }
                $tokenindexestomatches->compared[$index][] = $i;
            }
        }
        return $tokenindexestomatches;
    }

    /**
     * Splits set of matches, stored in $matches into set of non-competing matches, which cover token from compared
     * and correct string only once, non-interfering by it with any other matches (stored in $prefix, key presered) and
     * set of conflicting matches, stored in $matches.
     * @param array $prefix of block_formal_langs_matched_tokens_pair a set of non-competing matches (will be filled after
     *                                                                execution);
     * @param array $matches of block_formal_langs_matched_tokens_pair a set of token matches, from which will be
     *                                                                 removed all non-competing matches;
     * @param stdClass $tokenindexestomatches a pair of mappings ("compared" field contains mappings from compared string,
     *                                        "correct" fields contains mappings from correct string).
     */
    public function split_set_of_matched_into_set_of_noncompeting_matches_and_candidates(
        &$prefix,
        &$matches,
        $tokenindexestomatches
    ) {
        // If no matches exists, do not loop on them.
        if (count($matches) == 0) {
            return;
        }
        // Here we take advantage of temporal immutability of matches.
        foreach($matches as $key => $match) {
            /** @var block_formal_langs_matched_tokens_pair $match */
            $isnoncompetingmatch = true;

            // Check, whether no interfering by token indexes from correct string pairs exists.
            for($i = 0; $i < count($match->correcttokens); $i++) {
                $index = $match->correcttokens[$i];
                if (array_key_exists($index, $tokenindexestomatches->correct)) {
                    // 1 pair is fine, it must be our pair
                    $isnoncompetingmatch = $isnoncompetingmatch && count($tokenindexestomatches->correct[$index]) <= 1;
                }
            }

            // Check, whether no interfering by token indexes from compared string pairs exists.
            for($i = 0; $i < count($match->comparedtokens); $i++) {
                $index = $match->comparedtokens[$i];
                if (array_key_exists($index, $tokenindexestomatches->compared)) {
                    // 1 pair is fine, it must be our pair
                    $isnoncompetingmatch = $isnoncompetingmatch && count($tokenindexestomatches->compared[$index]) <= 1;
                }
            }

            if ($isnoncompetingmatch) {
                $prefix[$key] = $match;
                unset($matches[$key]);
            }
        }
    }
    /**
     * Generates array of best groups of matches representing possible set of mistakes in tokens.
     *
     * Use recursive backtracking.
     * No token from correct or compared stream could appear twice in any group, groups are
     * compared using compare_matches_groups function
     *
     * @param array $matches array of matched_tokens_pair objects representing all possible pairs within threshold
     * @param int $time starting time for analyzing tokens, should be less than or same as current
     * @return array of block_formal_langs_matches_group objects
     */
    public function group_matches($matches, $time = null) {
        global $CFG;
        $setspairs = array();
        $arraybestgroupsmatches = array();
        if ($time == null) {
            $time = time();
        }
        $tokenindexestomatches = $this->generate_mapping_of_token_indexes_to_matches($matches);
        // Prefix is a set, where matches will be in in any case.
        // Matched pairs will be in any case in set only in one case: if tokens from it's corrected and
        // compared strings are not covered by any other token pair
        $prefix = array();
        $this->split_set_of_matched_into_set_of_noncompeting_matches_and_candidates(
            $prefix,
            $matches,
            $tokenindexestomatches
        );
        $this->recursive_backtracking($prefix, $matches, $tokenindexestomatches, $setspairs, $time);
        if (count($setspairs)>0) {
            // first is the best
            $arraybestgroupsmatches[] = $setspairs[0];
            // write the best
            for ($i = 1; $i<count($setspairs); $i++) {
                // equal
                if ($this->compare_matches_groups($arraybestgroupsmatches[0], $setspairs[$i]) == 0) {
                    $canadd = intval($CFG->block_formal_langs_maximum_variations_of_typo_correction) < 0;
                    if (!$canadd) {
                        $limit = intval($CFG->block_formal_langs_maximum_variations_of_typo_correction);
                        $canadd = count($arraybestgroupsmatches) < $limit;
                    }
                    if ($canadd) {
                        $arraybestgroupsmatches[] = $setspairs[$i];
                    }
                } else {
                    if ($this->compare_matches_groups($arraybestgroupsmatches[0], $setspairs[$i]) < 0) {
                        // clear
                        $arraybestgroupsmatches = array();
                        $arraybestgroupsmatches[] = $setspairs[$i];
                    }
                }
            }
        }
        // array of results
        return $arraybestgroupsmatches;
    }
    
    public function group_matches_for_bypass($matches) {
        $arraybestgroupsmatches = array();
        $tokenindexestomatches = $this->generate_mapping_of_token_indexes_to_matches($matches);
        // Prefix is a set, where matches will be in in any case.
        // Matched pairs will be in any case in set only in one case: if tokens from it's corrected and
        // compared strings are not covered by any other token pair
        $resultingpairs = array();
        $this->split_set_of_matched_into_set_of_noncompeting_matches_and_candidates(
            $resultingpairs,
            $matches,
            $tokenindexestomatches
        );
        // After that, resulting pairs contains only non-competing pairs.
        // We should just inject to it any related pair, one by one.
        while(count($matches)) {
            // Get key for first element of queue.
            $matcheskeys = array_keys($matches);
            $firstmatcheskey = $matcheskeys[0];

            /** @var block_formal_langs_matched_token_pair $match */
            $match = $matches[$firstmatcheskey];
            $resultingpairs[$firstmatcheskey] = $match;
            $this->remove_matches_related_by_token_indexes_to_match($matches, $match, $tokenindexestomatches);
        }

        if (count($resultingpairs)) {
            $arraybestgroupsmatches = array( $this->make_matches_group(array_values($resultingpairs)) );
        }
        // array of results
        return $arraybestgroupsmatches;
    }

    /**
     * Create matches group from specified matches.
     * @param array $matches of block_formal_langs_matched_tokens_pair a pairs, from which group should be constructed.
     * @return block_formal_langs_matches_group
     */
    public function make_matches_group($matches) {
        $group = new block_formal_langs_matches_group();
        $group->matchedpairs = array();
        $group->mistakeweight = 0;
        $group->correctcoverage = array();
        $group->comparedcoverage = array();

        $safemerge = function(&$a, $b) {
            if (count($a) == 0) {
                $a = $b;
            } else {
                if (count($b) != 0) {
                    $a = array_merge($a, $b);
                }
            }
        };
        // find used pairs
        for ($i = 0; $i < count($matches); $i++) {
            /** @var block_formal_langs_matched_tokens_pair $match */
            $match = $matches[$i];
            $group->matchedpairs[] = $match;
            $group->mistakeweight += $match->mistakeweight;
            $safemerge($group->correctcoverage, $match->correcttokens);
            $safemerge($group->comparedcoverage, $match->comparedtokens);
        }
        if (count($group->correctcoverage)) {
            sort($group->correctcoverage);
        }
        if (count($group->comparedcoverage)) {
            sort($group->comparedcoverage);
        }
        gc_collect_cycles();
        return $group;
    }

    /**
     * Removes matches, covering the same correct or compared token indexes from list of matches.
     * @param array $matches of block_formal_langs_matched_tokens_pair an indexed array of matches as map, not an array;
     * @param block_formal_langs_matched_tokens_pair $match a match;
     * @param stdClass $tokenindexestomatches a mapping, which defines which, indexes of correct tokens belong
     *                               to some pairs;
     *                               @see block_formal_langs_token_stream::generate_mapping_of_token_indexes_to_matches
     */
    public function remove_matches_related_by_token_indexes_to_match(&$matches, $match, $tokenindexestomatches) {
        for($i = 0; $i < count($match->correcttokens); $i++) {
            $index = $match->correcttokens[$i];
            if (array_key_exists($index, $tokenindexestomatches->correct)) {
                $relatedmatchindexes = $tokenindexestomatches->correct[$index];
                foreach($relatedmatchindexes as $relatedindex) {
                    if (array_key_exists($relatedindex, $matches)) {
                        unset($matches[$relatedindex]);
                    }
                }
            }
        }

        for($i = 0; $i < count($match->comparedtokens); $i++) {
            $index = $match->comparedtokens[$i];
            if (array_key_exists($index, $tokenindexestomatches->compared)) {
                $relatedmatchindexes = $tokenindexestomatches->compared[$index];
                foreach($relatedmatchindexes as $relatedindex) {
                    if (array_key_exists($relatedindex, $matches)) {
                        unset($matches[$relatedindex]);
                    }
                }
            }
        }
    }
    /**
     * Finds sets of pairs (stored in setspairs), which should cover as much of tokens
     * from compared and correct string as possible, using
     * recursion. Note, that if both $prefix and $candidates are empty, sets are not generated.
     * @param array $prefix of block_formal_langs_matched_tokens_pair prefix part, which contains matched pairs, which
     *                         must be used in creating set of pairs. Note that, indexes of pairs is preserved to be
     *                         used to exclude those pairs from creating other sets, based on permutations of pairs;
     * @param array $candidates of block_formal_langs_matched_tokens_pair a token pairs, which could be injected
     *                         into current set, defined in prefix. Note, that indexes of pairs is preserved to make
     *                         sure, that candidates will not be failed to be removed in moving to inner loop;
     * @param stdClass $tokenindexestomatches a mapping, which defines, which indexes of correct or compared tokens
     *                               belong to some pairs;
     *                               @see block_formal_langs_token_stream::generate_mapping_of_token_indexes_to_matches
     * @param array $setspairs of block_formal_langs_matches_group a resulting set of pairs with coverage;
     * @param $time a starting time, to constrain backtracking to not be failed with out of time errors;
     * @return array of keys of already used pairs, which could be keys in prefix or candidates
     */
    public function recursive_backtracking($prefix, $candidates, $tokenindexestomatches, &$setspairs, $time) {
        // If no candidates, are presented, than new set is discovered
        // and it could be created from prefix part.
        $usedcanddates = array();
        if (count($candidates) == 0) {
            // If prefix is empty, then it's a rare case, when no matches are found
            // so we mustn't generare any set.
            if (count($prefix) != 0) {
                $setspairs[] = $this->make_matches_group(array_values($prefix));
                $usedcanddates = array_keys($prefix);
            }
        } else {
            $usedcanddates  = array();
            // Set queue to make sure, that each candidate will be viewed for new possible sets.
            // But also, we need to erase from it the candidates, that already used in set to make sure,
            // that no permutation will emerge from recursion.
            $candidatequeue = $candidates;
            // This variable determines, whether time limit is reached.
            $timelimitisnotreached = time() - $time - 1 < self::time_limit();
            // While we do have candidates, let's iterate on them, inserting them on new prefix and
            // moving to next iteration of building set.
            while(count($candidatequeue) && $timelimitisnotreached) {
                // Get key for first element of queue
                $candidatequeuekeys = array_keys($candidatequeue);
                $firstcandidatequeuekey = $candidatequeuekeys[0];

                $match = $candidatequeue[$firstcandidatequeuekey];

                $newprefix = $prefix;
                $newprefix[$firstcandidatequeuekey] = $match;

                $newcandidates = $candidates;
                // Remove matches from new candidates, that are related to new match, by tokne indexes.
                $this->remove_matches_related_by_token_indexes_to_match($newcandidates, $match, $tokenindexestomatches);

                $localusedcandidates = $this->recursive_backtracking(
                    $newprefix,
                    $newcandidates,
                    $tokenindexestomatches,
                    $setspairs,
                    $time
                );

                // Unset local used candidates from queue, don't start inner iteration call from them.
                for($i = 0; $i < count($localusedcandidates); $i++) {
                    $index = $localusedcandidates[$i];
                    if (array_key_exists($index, $candidatequeue)) {
                        unset($candidatequeue[$index]);
                    }
                }

                // Merge local candidates and global candidates, to ensure, that no permutation sets will emerge
                // on upper level of recursion.
                if (count($usedcanddates)) {
                    if (count($localusedcandidates)) {
                        $usedcanddates = array_values(array_unique(array_merge($usedcanddates, $localusedcandidates)));
                    }
                } else {
                    $usedcanddates = $localusedcandidates;
                }
                // Check, whether time limit is not reached
                $timelimitisnotreached = time() - $time - 1 < self::time_limit();
            }
        }
        return $usedcanddates;
    }

    /**
     * Compares two matches groups.
     *
     * Basic strategy is to have as much tokens in both correct and compared streams covered as possible.
     * If the number of tokens covered are equal, than choose group with less summ of mistake weights.
     *
     * @return number <0 if $group1 worse than $group2; 0 if $group1==$group2; >0 if $group1 better than $group2
     */
    public function compare_matches_groups($group1, $group2) {
        // TODO Birukova
        // count tokens
        if (count($group1->correctcoverage) + count($group1->comparedcoverage) == count($group2->correctcoverage) + count($group2->comparedcoverage)) {
            // mistakeweight tokens
            if ($group1->mistakeweight == $group2->mistakeweight) {
                return 0;
            } else {
                if ($group1->mistakeweight < $group2->mistakeweight) {
                    return 1;
                } else {
                    return -1;
                }
            }
        } else {
            if (count($group1->correctcoverage) + count($group1->comparedcoverage) > count($group2->correctcoverage) + count($group2->comparedcoverage)) {
                return 1;
            } else {
                return -1;
            }
        }
    }

    /**
     * Create a copy of this stream and correct mistakes in tokens using given array of matched pairs
     *
     * @param correctstream object of block_formal_langs_token_stream for correct stream
     * @param matchedpairsgroup array of block_formal_langs_matched_tokens_pair
     * @return a new token stream where comparedtokens changed to correcttokens if mistakeweight > 0 for the pair
     */
    public function correct_mistakes($correctstream, $matchedpairsgroup) {
    }
}
/**
 * Represents possible set of correspondes between tokens of correct and compared streams
 */
class  block_formal_langs_matches_group {
    /**
     * Array of matched pairs - set of pairs
     * This is main data for the group, other three fields contains agregate information from it.
     * @var array of block_formal_langs_matched_tokens_pair and it's child classes objects
     */
    public $matchedpairs;
    /**
     * Sum of mistake weights
     * @var int
    */
    public $mistakeweight;
    /**
     * Sorted array of all correct token indexes for tokens, covered by pairs from this group
     * @var array - array of int indexs
    */
    public $correctcoverage;
    
    /*
     * Sorted array of all compared token indexes for tokens, covered by pairs from this group
     * @var array - array of int indexs
    */
    public $comparedcoverage;
    
    /**
     * Returns an array of token indexes from compared string, which matches tokens from correct string
     *
     * @param correcttokens array of token indexes from correct string
     */
    public function get_relevant_compared_tokens($correcttokens) {
    }
}

/**
 * Represents a lexical error in the token
 *
 * A lexical error is a rare case where single lexem violates the rules of the language
 * and can not be interpreted.
 */
class  block_formal_langs_lexical_error {

    public $tokenindex;

    /**
     * User interface string (i.e. received using get_string) describing error to the user
     * @var string
     */
    public $errormessage;

    /**
     * Corrected token object if possible, null otherwise
     * @var block_formal_langs_token_base
     */
    public $correctedtoken;
    /**
     * A string, which determines a specific error kind.
     * Can be used by external interface (like CorrectWriting's lexical analyzer)
     * to handle specifical lexical error
     * @var string
     */
    public $errorkind = null;
}

/**
 * A special class for error for scanning
 */
class block_formal_langs_scanning_error extends block_formal_langs_lexical_error {

}

/**
 * Represents a processed string
 *
 * Contains a string, a token stream (if the string is tokenized) and a syntax tree (or array of trees) if parsed
 * This class is needed to encapsulate a processed string and centralize a code for it's handling while having
 * language, lexer and parser objects stateless.
 */
class block_formal_langs_processed_string {

    /**
     * @var string table, where string belongs
     */
    protected $tablename;

    /**
     * @var integer an id to load/store user descriptions
     */
    protected $tableid;

    /**
     * @var string a string to process
     */
    protected $string='';

    /**
     * @var object a link to the language object
     */
    protected $language;

    /**
     * @var object a token stream if the string is tokenized
     */
    protected $tokenstream=null;

    /**
     * @var object a syntax tree if the string is parsed
     */
    protected $syntaxtree=null;

    /**
     * @var array strings of token descriptions
     */
    protected $descriptions=null;

    /**
     * @var array lexical and syntax errors
     * 
     * Empty array means no errors was found, null - no error search done.
     * Error must be ast_node_base children object with correct position.
     */
    protected $errors=null;

    /**
     * Sets a language for a string
     * @param block_formal_langs_abstract_language $lang  language
     */
    public function __construct($lang) {
        $this->language = $lang;
    }
    
    /**
     * Copies state from other string
     * @param block_formal_langs_processed_string $string other string
     */
    public function copy_state_from($string) {
        $refclass = new ReflectionClass(get_class($this));
        $props = $refclass->getProperties();
        foreach($props as $prop) {
            /** @var ReflectionProperty $prop */
            $name = $prop->getName();
            $this->$name = $string->$name;
        }
    }

    /**
     * Called, when user assigns field to a class
     * @param string $name   name of field
     * @param mixed  $value  value of string
     */
    public function __set($name, $value) { //TODO - is there any need to write set_errors funtion?
        $settertable = array('string' => 'set_string', 'stream' => 'set_stream', 'syntaxtree' => 'set_syntax_tree');
        $settertable['descriptions'] = 'set_descriptions';
        
        if (array_key_exists($name, $settertable)) {
            $method = $settertable[$name];
            $this->$method($value);
        } else {
            $trace = debug_backtrace();
            $error  = 'Unknown property: ' . $name . ' in file: ' . $trace[0]['file'] . ', line: ' . $trace[0]['line'];
            trigger_error($error, E_USER_NOTICE);
        }
    }

    /**
     * Called when need to determine, whether field exists
     * @param string $name   name of field
     * @return bool whether field exists
     */
    public function __isset($name) {
        $getters = array('string', 'stream', 'syntaxtree', 'descriptions', 'language', 'errors');
        return in_array($name, $getters);
    }

    /**
     * Called when need to get field
     * @param string $name   name of field
     * @return mixed field
     */
    public function __get($name) {
        $gettertable = array('string' => 'get_string', 'stream' => 'get_stream', 'syntaxtree' => 'get_syntax_tree');
        $gettertable['descriptions'] = 'node_descriptions_list';
        $gettertable['language'] = 'get_lang';
        $gettertable['errors'] = 'get_errors';
        if (array_key_exists($name, $gettertable)) {
            $method = $gettertable[$name];
            return $this->$method();
        } else {
            $trace = debug_backtrace();
            $error  = 'Unknown property: ' . $name . ' in file: ' . $trace[0]['file'] . ', line: ' . $trace[0]['line'];
            trigger_error($error, E_USER_NOTICE);
        }
    }
   
    
    /** Removes a descriptions from a DB
     * @param string $tablename  name of source table
     * @param mixed $tableid    id or ids in table      
     */
    public static function delete_descriptions_by_id($tablename, $tableid ) {
        global $DB;
        $conditions = array();
        $conditions[] = "tablename = '{$tablename}' ";
        if (is_array($tableid)) {
            $in = implode(',', $tableid);
            $conditions[] = " tableid IN ($in) ";
        } else {
            $conditions[] = " tableid='{$tableid}' ";
        }
        return $DB->delete_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
    }

    /** Returns a descriptions from a DB
     * @param string $tablename  name of source table
     * @param mixed $tableid     ids in table
     * @return array like ['id'] => array( number => description)      
     */
    public static function get_descriptions_as_array($tablename, $tableid ) {
        global $DB;
        $conditions = array();
        $conditions[] = "tablename = '{$tablename}' ";
        if (is_array($tableid)) {
            $in = implode(',', $tableid);
            $conditions[] = " tableid IN ($in) ";
        } else {
            $conditions[] = " tableid='{$tableid}' ";
        }
        $records = $DB->get_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
        $result = array();
        foreach ($records as $record) {
            $result[$record->tableid][$record->number] = $record->description;
        }
        return $result;
    }

    /**
     * Sets an inner string. Also flushes any other dependent fields (token stream, syntax tree, descriptions) 
     * @param string $string inner string
     */
    protected function set_string($string) {
        $this->string=$string;
        $this->tokenstream = null;
        $this->syntaxtree = null;
        $this->descriptions = null;
    }
    /**
     * Sets a token stream. Must be used by lexical analyzer, to set a corrected stream for a string
     * @param block_formal_langs_token_stream $stream stream of lexemes     
     */
    public function set_corrected_stream($stream) {
        // TODO - change string to match $stream
        $this->tokenstream = $stream;
        $this->syntaxtree=null;
    }

    /**
     * Sets a token stream. Must be used by lexer, to set a stream for scan
     * @param block_formal_langs_token_stream $stream stream of lexemes     
     */
    protected function set_stream($stream) {
        $this->tokenstream = $stream;
        $this->syntaxtree = null;
    }

    /**
     * Sets a syntax tree.
     * @param object $tree syntax tree 
     */
    public function set_syntax_tree($tree) {
         $this->syntaxtree = $tree;
    }

    /**
     * Sets a descriptions for a string. 
     * @param array $descriptions descriptions array
     */
    protected function set_descriptions($descriptions) {
        $this->descriptions = $descriptions;
    }

    /**
     * Returns true if string doesn't contains line breaks.
     */
    public function single_line_string() {
        return strpos($this->string, "\n") === false;
    }

    /**
     * Returns true, if there is token, equal to given one from the student's viewpoint (i.e. node_description without position).
     *
     * Two tokens are equal if they have equal description, or if they values are same if the have no description.
     */
    public function token_has_equal_to_student($tokenindex) {
        $result = false;
        $tokens = $this->get_stream(); // Make sure string is tokenized.
        $tokencount = count($this->tokenstream->tokens);
        if($this->has_description($tokenindex)) {
            $givendescription = $this->node_description($tokenindex);
            // There is description of the given token.
            for ($i = 0; $i < $tokencount; $i++) {
                if ($i != $tokenindex && $this->has_description($i) && $givendescription == $this->node_description($i)) {
                    $result = true;
                }
            }
        } else {
            // There is no description, compare the values instead.
            $options = new block_formal_langs_comparing_options();
            $options->usecase = true;
            for ($i = 0; $i < $tokencount; $i++) {
                // Use case-sensitive search, since user could see case in the message.
                if ($i != $tokenindex && !$this->has_description($i) && $this->tokenstream->tokens[$tokenindex]->is_same($this->tokenstream->tokens[$i], $options)) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Sets a descriptions for a string. Also saves it to database (table parameters must be set).
     * @param array $descriptions descriptions array
     */
    public function save_descriptions($descriptions) {
        global $DB;
        $this->set_descriptions($descriptions);

        $conditions = array(" tableid='{$this->tableid}' ", "tablename = '{$this->tablename}' ");
        $oldrecords = $DB->get_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
        $index = 0;
        foreach ($this->descriptions as $description) {
            $record = null;
            $mustinsert  = ($oldrecords == null);
            if ($oldrecords != null) {
                $record = array_shift($oldrecords);
            }
            
            if ($record == null) {
                $record = new stdClass();
            }
            $record->tablename = $this->tablename;
            $record->tableid = $this->tableid;
            $record->number = $index;
            $record->description = $description;
            
            if ($mustinsert) {
                $DB->insert_record('block_formal_langs_node_dscr', $record);
            } else {
                $DB->update_record('block_formal_langs_node_dscr', $record);
            }
            
            $index = $index + 1;
        }
        
        // If some old descriptions left - delete it.
        if ($oldrecords != null) {
            $oldrecordids = array();
            foreach ($oldrecords as $oldrecord) {
                $oldrecordids[] = $oldrecord->id;
            }
            $oldrecordin = implode(',', $oldrecordids);
            $DB->delete_records_select('block_formal_langs_node_dscr', " id IN ({$oldrecordin}) AND tablename = '{$this->tablename}' ");
        }
    }

    /**
     * Set table parameters for string. Used by language.
     * @param string $tablename source table name
     * @param string $tableid   source id
     */
    public function set_table_params($tablename, $tableid) {
        $this->tablename=$tablename;
        $this->tableid=$tableid;
    }

    /**
     * Returns count of nodes which needs description or special name.
     *
     * @return integer
     */
    public function nodes_requiring_description_count() {// TODO - name
        if ($this->language->could_parse()) {
            return count($this->syntaxtree->nodes_requiring_description_list());
        } else {
            return count($this->tokenstream->tokens);
        }
    }

    /**
     * Returns list of node objects which requires description.
     *
     * @param $answer - moodle answer object
     * @return array of node objects
     */
    public function nodes_requiring_description_list() {
        // TODO: return node objects
        if ($this->language->could_parse()) {
            return $this->syntaxtree->nodes_requiring_description_list();
        } else {
            return $this->tokenstream->tokens;
        }
    }

    /**
     * Returns node by number
     * @param $nodenumber
     * @param array|block_formal_langs_ast_node_base $root a root node
     * @return null|block_formal_langs_ast_node_base
     */
    public function find_node($nodenumber, $root = array()) {
        if (is_array($root) && count($root) == 0) {
            $result = null;
            $tree = $this->get_syntax_tree();
            foreach($tree as $v) {
                if ($result == null) {
                    $result = $this->find_node($nodenumber, $v);
                }
            }
            return $result;
        }

        if ($root->number() == $nodenumber) {
            return $root;
        }
        $children = $root->childs();
        $result = null;
        if (count($children)) {
            foreach($children as $child) {
                if ($result == null) {
                    $result = $this->find_node($nodenumber, $child);
                }
            }
        }
        return $result;
    }

    /**
     * Returns tree, converted to list and sorted by number
     * @param null|block_formal_langs_ast_node_base $root a root node
     * @return array array of nodes, sorted by number
     */
    public function tree_to_list($root = null) {
        $result = array();
        if ($root == null) {
            $arraytobescanned = $this->get_syntax_tree();
        } else {
            $result = array( $root );
            $arraytobescanned = $root->childs();
        }
        if (count($arraytobescanned)) {
            foreach($arraytobescanned as $node) {
                $tmp = $this->tree_to_list($node);
                if (count($result) == 0) {
                    $result = $tmp;
                } else {
                    $result = array_merge($result, $tmp);
                }
            }
        }

        if (count($result)) {
            /**
             * Comparator for sorting all of nodes
             * @param  block_formal_langs_ast_node_base $a
             * @param  block_formal_langs_ast_node_base $b
             * @return int
             */
            $cmp = function($a, $b) {
                if ($a->number() == $b->number()) {
                    return 0;
                }
                return ($a->number() < $b->number()) ? -1 : 1;
            };
            usort($result, $cmp);
        }
        return $result;
    }

    /**
     * Returns description string for passed node.
     *
     * @param int $nodenumber number of node
     * @param boolean $quotevalue should the value be quoted if description is absent; no position on this one
     * @param boolean $at whether include position if token description is absent
     * @return string - description of node if present, quoted node value otherwise.
     */
    public function node_description($nodenumber, $quotevalue = true, $at = false) {
        //$this->node_descriptions_list(); //Not needed, since has_description will call node_descriptions_list anyway.
        $result = '';
        if ($this->has_description($nodenumber)) {
            return $this->descriptions[$nodenumber];
        } else {
            $tokens = $this->tokenstream->tokens;
            /** @var block_formal_langs_node_position $pos */
            $pos = null;
            /** @var null|qtype_poasquestion\string $value */
            $value = null;
            if (array_key_exists($nodenumber, $tokens)) {
                /** @var block_formal_langs_token_base $token */
                $token = $tokens[$nodenumber];
                $value = $token->value();
                $pos = $token->position();
            } else {
                $node = $this->find_node($nodenumber, array());
                if ($node == null) {
                    return '';
                }
                $value = $node->value();
                $pos = $node->position();
            }
            if (!is_string($value)) {
                $value = $value->string();
            }
            if (!$quotevalue) {
                return $value;
            } else if ($at) {// Should return position information.
                $a = new stdClass();
                $a->value = $value;
                $a->column = $pos->colstart();
                if ($this->single_line_string()) {
                    return get_string('quoteatsingleline', 'block_formal_langs', $a);
                } else {
                    $a->line = $pos->linestart();
                    return get_string('quoteat', 'block_formal_langs', $a);
                }
            } else {//Just quote
                return get_string('quote', 'block_formal_langs', $value);
            }
        }
    }

    /**
     * Returns list of node descriptions.
     *
     * @return array of strings, keys are node numbers
     * @throws coding_exception on error
     */
    public function node_descriptions_list() {
        global $DB;
        if ($this->descriptions === null) {
            $istablefilledincorrect = !is_string($this->tablename) || core_text::strlen($this->tablename) == 0;
            if (!is_numeric($this->tableid)  || $istablefilledincorrect) {
                return array();
                //throw new coding_exception('Trying to extract descriptions from unknown sources for string');
            }
            $conditions = array(" tableid='{$this->tableid}' ", "tablename = '{$this->tablename}' ");
            $records = $DB->get_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
            foreach($records as $record) {
                $this->descriptions[$record->number] = $record->description;
            }
        }
        return $this->descriptions;
    }

    /**
     * A unit-testing method for setting descriptions from associative array
     *
     * An example for such array is
     * array(0 => 'A description for first lexeme',
     *       1 => 'A description for second lexeme')
     *
     * DO NOT USE THIS FUNCTION IN PRODUCTION, USE FOR UNIT-TESTING ONLY.
     *
     * @param array $descriptions descriptions for lexemes
     */
    public function set_descriptions_from_array($descriptions) {
        $this->descriptions = $descriptions;
    }

    /** Test, whether we have a lexeme descriptions for token with specified index
     * @param int $index index of token
     */
    public function has_description($index) {
        $this->node_descriptions_list();
        if (isset($this->descriptions[$index])) {
            return strlen(trim($this->descriptions[$index]))!=0;
        }
        return false;
    }

    /**
     * Returns a stream of tokens.
     * @return stream of tokens
     */
    private function get_stream() {
        if ($this->tokenstream == null) {
            $this->language->scan($this);
        }
        return $this->tokenstream;
    }

    /**
     * Returns a syntax tree
     * @return syntax tree
     */
    protected function get_syntax_tree() {
        if ($this->syntaxtree == null && $this->language->could_parse()) {
            // TODO: Fix this inconsistency
            $this->language->parse($this, true);
        }
        return $this->syntaxtree;
    }

    protected function get_errors() {
        if ($this->errors === null) {
            // No lexing and parsing was done, do now to look for errors.
            $this->get_stream();
            $this->get_syntax_tree();
        }
        return $this->errors;
    }

    /**
     * Returns inner string
     * @return inner string
     */
    protected function get_string() {
        return $this->string;
    }
    protected function get_lang() {
        return $this->language;
    }
}

/**
 * Represents a pair of correct and compared strings with group of pairs, matching their tokens.
 *
 * Use it when you need mistakes in individual lexemes functionality.
 * Both strings are block_formal_langs_processed_string objects, but correct one created from DB, while compared one from string.
 * The class incapsulate block_formal_matches_group describing pairing between both strings and corrected string, created from this pairing.
 * 
 */
class block_formal_langs_string_pair {

    /**
     * Correct string, entered by a teacher.
     *
     * @var block_formal_langs_processed_string, created from DB.
     */
    protected $correctstring;

    /**
     * Compared (possibly incorrect) string, entered by a student.
     *
     * @var block_formal_langs_processed_string, created from string.
     */
    protected $comparedstring;

    /**
     * Matches - define a connection between correct and compared strings.
     *
     * @var block_formal_langs_matches_group
     */
    protected $matches;

    /**
     * Corrected string - string, created from compared string by applying all matches.
     *
     * @var block_formal_langs_processed_string, created from token_array.
     */
    protected $correctedstring;

    /**
     * Array of matches between corrected and compared, where keys are indexes from corrected string
     * and values are array of matched indexes from compared
     * @var array
     */
    protected $correctedtocompared;

    public function __clone() {
        $this->correctstring = clone $this->correctstring;
        if (is_object($this->correctedstring)) {
             $this->correctedstring = clone $this->correctedstring;
        }
        if (is_object($this->comparedstring)) {
            $this->comparedstring = clone $this->comparedstring;
        }
        if (is_object($this->matches)) {
            $this->matches = clone $this->matches;
        }
    }

    // TODO - anyone -  access functions
    // TODO - functions for the lexical and sequence analyzers, and mistake classes.
    /**
     * Returns a corrected string.
     * Used in analyzers, for mistake generation and other
     * @return   block_formal_langs_processed_string
     */
    public function correctedstring() {
        return $this->correctedstring;
    }
    /**
     *  Returns a compared string.
     *  Used in analyzers, for mistake generation and other
     *  @return   block_formal_langs_processed_string
     */
    public function comparedstring() {
        return $this->comparedstring;
    }

    /**
     * Returns a correct string.
     * Used in analyzers, for mistake generation and other
     * @return   block_formal_langs_processed_string
     */
    public function correctstring() {
        return $this->correctstring;
    }

    public function matches() {
        return $this->matches;
    }

    public function setcorrectedstring($string) {
        $this->correctedstring=$string;
    }

    /**
     * Returns list of string pairs creating them grom groups
     * @param block_formal_langs_processed_string $correctstring a correct string, entered by teacher
     * @param block_formal_langs_processed_string $comparedstring a compared string, entered by student
     * @param string $classname a class name to of new pair
     * @param array $bestgroups array of best groups
     * @return array list of new string pairs
     */
    protected static  function make_list_of_string_pairs_from_groups(
        $correctstring,
        $comparedstring,
        $classname,
        &$bestgroups
    ) {
        global $CFG;
        $returnallpairs = intval($CFG->block_formal_langs_maximum_variations_of_typo_correction) <= 0;
        $limit = intval($CFG->block_formal_langs_maximum_variations_of_typo_correction);
        if(count($bestgroups) == 0) {
            $stringpair = new $classname($correctstring, $comparedstring, array());
            $arraystringpairs = array();
            $arraystringpairs[] = $stringpair;
            return $arraystringpairs;
        }
        $arraystringpairs = array();
        for ($i = 0; ($i < count($bestgroups)) && (($i < $limit) || $returnallpairs); $i++) {
            $stringpair = new $classname($correctstring, $comparedstring, $bestgroups[$i]);
            $arraystringpairs[] = $stringpair;
        }
        return $arraystringpairs;
    }

    public static function best_string_pairs_for_bypass($correctstring, $comparedstring, $threshold, block_formal_langs_comparing_options $options, $classname = 'block_formal_langs_string_pair') {
        /** @var block_formal_langs_token_stream $correctstream */
        $correctstream = $correctstring->stream;
        $comparedstream = $comparedstring->stream;
        $bestgroups = $correctstream->look_for_token_pairs($comparedstream, $threshold, $options, true);
        return self::make_list_of_string_pairs_from_groups($correctstring, $comparedstring, $classname, $bestgroups);
    }
    
    /**
     * Factory method. Returns an array of block_formal_langs_string_pair objects for each best matches group for that pair of strings
     */
    public static function best_string_pairs($correctstring, $comparedstring, $threshold, block_formal_langs_comparing_options $options, $classname = 'block_formal_langs_string_pair') {
        /** @var block_formal_langs_token_stream $correctstream */
        $correctstream = $correctstring->stream;
        $comparedstream = $comparedstring->stream;
        $bestgroups = $correctstream->look_for_token_pairs($comparedstream, $threshold, $options, false);
        return self::make_list_of_string_pairs_from_groups($correctstring, $comparedstring, $classname, $bestgroups);
    }

    public function __construct($correct, $compared, $matches) {
        $this->correctstring = $correct;
        $this->comparedstring = $compared;
        $this->matches = $matches;
        $this->correctedstring = $this->correct_mistakes();
    }

    /**
     * Correct mistakes in compared string using array of matched pairs and correct string.
     *
     * @return block_formal_langs_processed_string , with a new token stream where comparedtokens changed to correcttokens if mistakeweight > 0 for the pair and
     * other array, where matches between corrected and compared are stored
     */
    public function correct_mistakes() {
        // TODO Birukova - create a new string from $comparedstring and matches
        // This is somewhat more difficult, as we need to preserve existing separators (except extra ones).
        // Also, user-visible parts of the compared string should be saved where possible (e.g. not in typos)
        $newstream = $this->comparedstring->stream;   // incorrect lexems
        $correctstream = $this->correctstring->stream;   // correct lexems
        $streamcorrected = new block_formal_langs_token_stream();
        $streamcorrected->tokens = array();     // corrected lexems
        $matchedpairs = array();
        $correctedtocompared = array();
        if (is_object($this->matches())) {
            $matchedpairs = $this->matches()->matchedpairs;
        }
        // TODO Birukova - change tokens using pairs
        for ($i = 0; $i < count($newstream->tokens); $i++) {
            $ispresentedinmatches = false;
            for ($j = 0; $j < count($matchedpairs); $j++) {
                /**
                 * @var block_formal_langs_matched_tokens_pair $matchedpair
                 */
                $matchedpair = $matchedpairs[$j];
                if (in_array($i, $matchedpair->comparedtokens)) {
                    $ispresentedinmatches = true;
                    if (count($matchedpair->comparedtokens) != 1) {
                        // Note, that we must update $i if multiple tokens are merged into one
                        // because next should walk into next compared token
                        $i = max($matchedpair->comparedtokens);
                    }
                    // Multiple tokens can be merged into one
                    for($k = 0; $k < count($matchedpair->correcttokens); $k++) {
                        $correctedtocompared[count($streamcorrected->tokens)] = $matchedpair->comparedtokens;
                        $streamcorrected->tokens[] = $correctstream->tokens[$matchedpair->correcttokens[$k]];
                    }
                }
            }
            // write compared token if no stuff is presented
            if (!$ispresentedinmatches) {
                $correctedtocompared[count($streamcorrected->tokens)] = array( $i );
                $streamcorrected->tokens[] = $newstream->tokens[$i];
            }
        }
        $lang = $this->correctstring->language;
        $this->correctedstring = new block_formal_langs_processed_string ($lang);
        $this->correctedstring->set_corrected_stream($streamcorrected);
        $this->correctedtocompared = $correctedtocompared;
        return $this->correctedstring;
    }


    /**
     * Returns mapped index from from corrected string to compared string.
     * Array of indexes could be returned if both are mapped.
     * @param $index
     * @return array of indexes from compared string (array with -1 if not found)
     */
    public function map_from_corrected_string_to_compared_string($index) {
        return $this->map($index, $this->correctedtocompared, false, array($index));
    }


    /**
     * Maps from source to destination
     * @param int $index index in source string
     * @param array $source array of source mapping
     * @param bool $flip whether we should flip the mappings
     * @param mixed $default a default value for flipping
     * @return mixed result
     */
    protected function map($index, $source, $flip, $default) {
        $result = $default;
        if (count($source)) {
            $f = $source;
            if ($flip) {
                $f = array_flip($f);
            }
            if (array_key_exists($index, $f)) {
                return $f[$index];
            }
        }
        return $result;
    }

    /**
     * Returns description string for passed node. If there is no description, token value from compared string is used, 
     * if it is not available too, than token value from correct string is used.  TODO - check the rules.
     *
     * @param $nodenumber number of node in the correct string
     * @param $quotevalue should the value be quoted if description is absent; no position on this one
     * @param $at whether include position if token description is absent
     * @return string - description of node if present, quoted node value otherwise.
     */
    public function node_description($nodenumber, $quotevalue = true, $at = false) {
        return $this->correctstring()->node_description($nodenumber, $quotevalue, $at);
    }
}
?>