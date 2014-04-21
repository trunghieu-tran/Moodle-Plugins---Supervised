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
require_once($CFG->dirroot.'/question/type/poasquestion/poasquestion_string.php');

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

    private function print_node($node, $args = NULL) {//TODO - normal printing, maybe using dot
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
            $callback($node, $args);//TODO - what is args?
        }

        foreach($node->childs as $child) {//TODO - why no callback for non-leaf nodes?
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
     *	@var int
     */	
    protected $stringstart;
    /** An end position in string, as sequence of characters
     *	@var int
     */	    
    protected $stringend;

    public function linestart(){
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
	
	public $rule;

    public function __construct($type, $position, $number, $needuserdescription) {
        $this->number = $number;
        $this->type = $type;
        $this->position = $position;
        $this->needuserdescription = $needuserdescription;

        $this->childs = array();
        $this->description = '';
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

    public function position() {
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
        $name = str_replace('block_formal_langs_','', $className);
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
     * Calculates and return editing distance from
     * $this to $token
     * @param $options - comparing options
     */
    public function editing_distance($token, block_formal_langs_comparing_options $options) {
        if ($this->is_same($token, $options->usecase)) {//If two tokens are identical, return 0.
            return 0;
        }
        if ($this->use_editing_distance()) {//Damerau-Levenshtein distance is default now.
            $distance = block_formal_langs_token_base::damerau_levenshtein($this->value(), $token->value(), $options);
        } else {//Distance not applicable, so return a big number.
            $distance = textlib::strlen($this->value()) + textlib::strlen($token->value());
        }
    }

    /* Calculates Damerau-Levenshtein distance between two strings.  
     *
     * @return int Damerau-Levenshtein distance
     */
    static public function damerau_levenshtein($str1, $str2, block_formal_langs_comparing_options $options) {
    }

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
    public function look_for_matches($other, $threshold, $iscorrect, block_formal_langs_comparing_options $options) {
        // TODO: generic mistakes handling
    }

    /**
     * Returns a string caseinsensitive semantic value of token
     * @return string
     */
    protected function string_caseinsensitive_value() {
        $value = $this->value;
        if (is_object($this->value)) {
            $value = clone $value;
            $value->tolower();
            $value = $value->string();
        } else {
            $value = textlib::strtolower($value);
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
            }  else {
                $left = $this->string_caseinsensitive_value();
                $right = $other->string_caseinsensitive_value();
                $result = $left == $right;
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


    //No mistake in this pair, all is correct.
    const TYPE_NO_MISTAKE = 0;
    //Mistake is a typo, measured by Damerau-Levenshtein distance.
    const TYPE_TYPO = 1;
    //Mistake is an extra separator.
    const TYPE_EXTRA_SEPARATOR = 2;
    //Mistake is a missing separator.
    const TYPE_MISSING_SEPARATOR = 3;
    //This is a token-type specific mistake.
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

    public function __construct($correcttokens, $comparedtokens, $mistakeweight, $specific = false, $messageid = '') {
        $this->correcttokens = $correcttokens;
        $this->comparedtokens = $comparedtokens;
        $this->mistakeweight = $mistakeweight;
        if ($specific) {//This mistake is a lexem-type specific mistake.
            if ($mistakeweight == 0) {
                $this->type = self::TYPE_NO_MISTAKE;
                $this->messageid = '';
            } else {
                $this->type = self::TYPE_SPECIFIC_MISTAKE;
                $this->messageid = $messageid;
            }
        } else {//This mistake is a general mistake.
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
        if ($this->type == self::TYPE_NO_MISTAKE) {//Full match, no mistake.
            return '';
        }

        $a = new stdClass();
        $a->mistakeweight = $this->mistakeweight;
        $a->correct = array();
        foreach ($this->correcttokens as $index) {
            $a->correct[] = $correctstring->node_description($index);
        }
        $a->compared = array();
        foreach ($this->comparedtokens as $index) {
            $a->compared[] = $comparedstring->node_description($index);
        }

        return get_string($this->messageid, 'block_formal_langs', $a);
    }
}

class block_formal_langs_typo_pair extends block_formal_langs_matched_tokens_pair {

    /**
     * A string with editing operators.
     * @var string
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
     * Set token indexes traversing array of tokens from left to right
     *
     * Use to restore indexes after inserting or removing tokens (c.e. correct_mistakes)
     */
    public function set_token_indexes() {
        //TODO Birukova
    }

    /**
     * Compares compared stream of tokens with this (correct) stream looking for
     * matches with possible errors in tokens (but not in their placement)
     *
     * @param comparedstream object of block_formal_langs_token_stream to compare with this, may contain errors
     * @param threshold editing distance threshold (in percents to token length)
     * @return array of block_formal_langs_matched_tokens_pair objects
     */
    public function look_for_token_pairs($comparedstream, $threshold, block_formal_langs_comparing_options $options) {
        //TODO Birukova
        //1. Find matched pairs (typos, typical errors etc) - Birukova
        //  - look_for_matches function
        //2. Find best groups of pairs - Birukova
        //  - group_matches function, with criteria defined by compare_matches_groups function
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
        //TODO Birukova
    }

    /**
     * Generates array of best groups of matches representing possible set of mistakes in tokens.
     *
     * Use recursive backtracking.
     * No token from correct or compared stream could appear twice in any group, groups are
     * compared using compare_matches_groups function
     *
     * @param array $matches array of matched_tokens_pair objects representing all possible pairs within threshold
     * @return array of  block_formal_langs_matches_group objects
     */
    public function group_matches($matches) {
        //TODO Birukova
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
        //TODO Birukova
    }

}

/**
 * Represents possible set of correspondes between tokens of correct and compared streams
 */
class  block_formal_langs_matches_group {
    /**
     * Array of matched pairs
     * This is main data for the group, other three fields contains agregate information from it.
     * @var array of block_formal_langs_matched_tokens_pair and it's child classes objects
     */
    public $matchedpairs;

    //Sum of mistake weights for the group
    public $mistakeweight;

    //Sorted array of all correct token indexes for tokens, covered by pairs from this group
    public $correctcoverage;

    //Sorted array of all compared token indexes for tokens, covered by pairs from this group
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
     *  Corrected token object if possible, null otherwise
     *  @var block_formal_langs_token_base
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
 *   language, lexer and parser objects stateless.
 */
class block_formal_langs_processed_string {
   
    /**
     * @var string table, where string belongs
     */
    protected $tablename;
    /**
     *@var integer an id to load/store user descriptions
     */
    protected $tableid;
    
    /**
     *@var string a string to process
     */
    protected $string='';

    /**
     *@var object a link to the language object
     */
    protected $language;

    /**
     *@var object a token stream if the string is tokenized
     */
    protected $tokenstream=null;

    /**
     *@var object a syntax tree if the string is parsed
     */
    protected $syntaxtree=null;

    /**
     * @var array strings of token descriptions
     */
    protected $descriptions=null;
    
    /**
     *  Sets a language for a string
     *  @param block_formal_langs_abstract_language $lang  language
     */
    public function __construct($lang) {
        $this->language = $lang;
    }
    
    /**
     *  Called, when user assigns field to a class
     *  @param string $name   name of field
     *  @param mixed  $value  value of string
     */
    public function __set($name, $value) {
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
     *  Called when need to determine, whether field exists
     *  @param string $name   name of field
     *  @return bool whether field exists
     */
    public function __isset($name) {
        $getters = array('string', 'stream', 'syntaxtree', 'descriptions');
        return in_array($name, $getters);
    }
    /**
     *  Called when need to get field
     *  @param string $name   name of field
     *  @return mixed field
     */
    public function __get($name) {
        $gettertable = array('string' => 'get_string', 'stream' => 'get_stream', 'syntaxtree' => 'get_syntax_tree');
        $gettertable['descriptions'] = 'node_descriptions_list';
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
        foreach($records as $record) {
            $result[$record->tableid][$record->number] = $record->description;
        }
        return $result;
    }
    
    /**
     *  Sets an inner string. Also flushes any other dependent fields (token stream, syntax tree, descriptions) 
     *  @param string $string inner string
     */
    protected function set_string($string)  {
        $this->string=$string;
        $this->tokenstream=null;
        $this->syntaxtree=null;
        $this->descriptions=null;
    }
    /**
     * Sets a token stream. Must be used by lexical analyzer, to set a corrected stream for a string
     * @param block_formal_langs_token_stream $stream stream of lexemes     
     */
    public function set_corrected_stream($stream) {
        //TODO - define, how it should differs from set_stream
        $this->tokenstream = $stream;
    }
    /**
     * Sets a token stream. Must be used by lexer, to set a stream for scan
     * @param block_formal_langs_token_stream $stream stream of lexemes     
     */
    protected function set_stream($stream) {
        $this->tokenstream = $stream;
        $this->syntaxtree=null;
    }
    /**
     *  Sets a syntax tree.
     *  @param object $tree syntax tree 
     */
    public function set_syntax_tree($tree) {
         $this->syntaxtree = $tree;
    }
    
    /**
     *  Sets a descriptions for a string. 
     *  @param array $descriptions descriptions array
     */
    protected function set_descriptions($descriptions)  {
        $this->descriptions = $descriptions;
    }

    /**
     * Returns true if string doesn't contains line breaks.
     */
    public function single_line_string() {
        return strpos($this->string, "\n") === FALSE;
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
     *  Sets a descriptions for a string. Also saves it to database (table parameters must be set).
     *  @param array $descriptions descriptions array
     */
    public function save_descriptions($descriptions)  {
        global $DB;
        $this->set_descriptions($descriptions);

        $conditions = array(" tableid='{$this->tableid}' ", "tablename = '{$this->tablename}' ");
        $oldrecords = $DB->get_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
        $index = 0;
        foreach($this->descriptions as $description) {
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
                $DB->insert_record('block_formal_langs_node_dscr',$record);
            } else {
                $DB->update_record('block_formal_langs_node_dscr',$record);
            }
            
            $index = $index + 1;
        }
        
        // If some old descriptions left - delete it.
        if ($oldrecords != null) {
            $oldrecordids = array();
            foreach($oldrecords as $oldrecord) {
                $oldrecordids[] = $oldrecord->id;    
            }
            $oldrecordin = implode(',',$oldrecordids);
            $DB->delete_records_select('block_formal_langs_node_dscr', " id IN ({$oldrecordin}) AND tablename = '{$this->tablename}' ");
        }
    }
    
    /**
     *  Set table parameters for string. Used by language.
     *  @param string $tablename source table name
     *  @param string $tableid   source id
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
    public function nodes_requiring_description_count() {//TODO - name
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
     * Returns description string for passed node.
     *
     * @param $nodenumber number of node
     * @param $quotevalue should the value be quoted if description is absent; no position on this one
     * @param $at whether include position if token description is absent
     * @return string - description of node if present, quoted node value otherwise.
     */
    public function node_description($nodenumber, $quotevalue = true, $at = false) {
        $result = '';
        if ($this->has_description($nodenumber)) {
            return $this->descriptions[$nodenumber];
        } else {
            $value = $this->tokenstream->tokens[$nodenumber]->value();
            if (!is_string($value)) {
                $value = $value->string();
            }
            if (!$quotevalue) {
                return $value;
            } else if ($at) {// Should return position information.
                $a = new stdClass();
                $a->value = $value;
                $pos = $this->tokenstream->tokens[$nodenumber]->position();
                $a->column = $pos->colstart();
                if ($this->single_line_string()) {
                    return get_string('quoteatsingleline', 'block_formal_langs', $a);
                } else {
                    $a->line = $pos->linestart();
                    return get_string('quoteat', 'block_formal_langs', $a);
                }
            } else {// Just quote.
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
        if ($this->descriptions === null)
        {
            $istablefilledincorrect = !is_string($this->tablename) || textlib::strlen($this->tablename) == 0;
            if (!is_numeric($this->tableid)  || $istablefilledincorrect) {
                throw new coding_exception('Trying to extract descriptions from unknown sources for string');
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
     *  @param int $index index of token
     */
    public function has_description($index) {
       $this->node_descriptions_list();
       if (isset($this->descriptions[$index])) {
           return strlen(trim($this->descriptions[$index])) != 0;
        }
       return false;
    }
    /**
     *  Returns a stream of tokens.
     *  @return stream of tokens
     */
    private function get_stream() {
        if ($this->tokenstream == null)
            $this->language->scan($this);
        return $this->tokenstream;
    }
    /**
     *  Returns a syntax tree
     *  @return syntax tree
     */
    protected function get_syntax_tree() {
        if ($this->syntaxtree == null && $this->language->could_parse()) {
            // TODO: Fix this inconsistency
            $this->language->parse($this, false);
        }
        return $this->syntaxtree;
    }
    /**
     *  Returns inner string
     *  @return inner string
     */
    protected function get_string() {
        return $this->string;
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

    //TODO - anyone -  access functions
    //TODO - functions for the lexical and sequence analyzers, and mistake classes.

    /**
     *  Returns a corrected string.
     *  Used in analyzers, for mistake generation and other
     *  @return   block_formal_langs_processed_string
     */
    public function correctedstring() {
        return $this->correctedstring;
    }

    /**
     *  Returns a correct string.
     *  Used in analyzers, for mistake generation and other
     *  @return   block_formal_langs_processed_string
     */
    public function correctstring() {
        return $this->correctstring;
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
     * Factory method. Returns an array of block_formal_langs_string_pair objects for each best matches group for that pair of strings
     */
    public static function best_string_pairs($lang, $correctstr, $tablename, $tableid, $compared, block_formal_langs_comparing_options $options) {
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
     * @return a new token stream where comparedtokens changed to correcttokens if mistakeweight > 0 for the pair
     */
    protected function correct_mistakes() {
        //TODO Birukova - create a new string from $comparedstring and matches
        //This is somewhat more difficult, as we need to preserve existing separators (except extra ones).
        //Also, user-visible parts of the compared string should be saved where possible (e.g. not in typos)

        // Mamontov - added a simple stub, to make possible for sequence analyzer to work with
        // corrected string
        return $this->comparedstring;
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
        //$this->node_descriptions_list(); //Not needed, since has_description will call node_descriptions_list anyway.
        /* TODO - implement, this code from processed_string may be useful
        $result = '';
        if ($this->has_description($nodenumber)) {
            return $this->descriptions[$nodenumber];
        } else {
            $value = $this->tokenstream->tokens[$nodenumber]->value();
            if (!is_string($value)) {
                $value = $value->string();
            }
            if (!$quotevalue) {
                return $value;
            } else if ($at) {//Should return position information.
                $a = new stdClass();
                $a->value = $value;
                $pos = $this->tokenstream->tokens[$nodenumber]->position();
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
        }*/
        return $this->correctstring()->node_description($nodenumber, $quotevalue, $at);
    }
}
?>