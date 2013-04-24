<?php
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
    
    public function __construct($linestart, $lineend, $colstart, $colend) {
        $this->linestart = $linestart;
        $this->lineend = $lineend;
        $this->colstart = $colstart;
        $this->colend = $colend;
    }

    /**
     * Summ positions of array of nodes into one position
     *
     * Resulting position is defined from minimum to maximum postion of nodes
     *
     * @param array $nodepositions positions of adjanced nodes
     */
    public function summ($nodepositions) {
        $minlinestart = $nodepositions[0]->linestart;
        $maxlineend = $nodepositions[0]->lineend;
        $mincolstart = $nodepositions[0]->colstart;
        $maxcolend = $nodepositions[0]->colend;

        foreach ($nodepositions as $node) {
            if ($node->linestart < $minlinestart)
                $minlinestart = $node->linestart;
            if ($node->colstart < $mincolstart)
                $mincolstart = $node->colstart;
            if ($node->lineend > $maxlineend)
                $maxlineend = $node->lineend;
            if ($node->colend > $maxcolend)
                $maxcolend = $node->colend;
        }

        return new block_formal_langs_node_position($minlinestart, $maxlineend, $mincolstart, $maxcolend);
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
     */
    public function editing_distance($token) {
        if ($this->use_editing_distance()) {//Damerau-Levenshtein distance is default now
            $distance = block_formal_langs_token_base::damerau_levenshtein($this->value(), $token->value());
        } else {//Distance not applicable, so return a big number 
            $distance = strlen($this->value()) + strlen($token->value());
        }
    }

    /* Calculates Damerau-Levenshtein distance between two strings.  
     *
     * @return int Damerau-Levenshtein distance
     */
    static public function damerau_levenshtein($str1, $str2) {
        if ($str1 == $str2) 
            return 0;//words identical
        $str1_len = strlen($str1);
        $str2_len = strlen($str2);
        //zero length of words
        if ($str1_len == 0) {
            return $str2_len;
        } 
        else 
            if ($str2_len == 0) {
                return $str1_len;
            }
        //matrix [str1_len+1][str2_len+1]
        for($i=0;$i<$str1_len;$i++)
            for ($j=0;$j<$str2_len+1;$j++) 
                    $mas[$i][$j]=0;
        //fill in the first row and column  
        for($i=0;$i<=$str1_len;$i++)
            $mas[$i][0]=$i;
        for($j=0;$j<=$str2_len;$j++)
            $mas[0][$j]=$j;
        //calculation
        for($i=1;$i<=$str1_len;$i++)
        {
            for($j=1;$j<=$str2_len;$j++)
            {
                $up=$mas[$i-1][$j]+1;//deletion
                $left=$mas[$i][$j-1]+1;//insertion
                if($str1[$i-1]==$str2[$j-1])
                    $cost=0;
                else
                    $cost=1;
                $diag=$mas[$i-1][$j-1]+$cost;//replacement
                $mas[$i][$j]=min(min($up,$left),$diag);
                if($i>1 && $j>1 && $str1[$i-1]==$str2[$j-2] && $str1[$i-2]==$str2[$j-1])
                    $mas[$i][$j]=min($mas[$i][$j], $mas[$i-2][$j-2]+$cost);//transposition
            }
        }
        return $mas[$str1_len][$str2_len];
    }

    
    /* Determines the possibility existence of a pair
    *
    * @return int Damerau-Levenstein distance or -1 if pair is not possible
    */
    public function possible_pair($token, $max)
    {
        $str1= $this->value;
        $str2= $token->value;
        $length_of_str1=strlen($str1);                 //define the length of str1
        $length_of_str2=strlen($str2);                 //define the length of str2
        if(!($length_of_str1-$max<=$length_of_str2 && $length_of_str2<=$length_of_str1+$max))
            return -1;
        //$distance=$this->editing_distance($token);    //define the distance of damerau-levenshtein 
        $distance = block_formal_langs_token_base::damerau_levenshtein($str1,$str2);
        if($distance<=$max)
            return $distance;
        else
            return -1;
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
     * @return array - array of block_formal_langs_matched_tokens_pair objects with blank
     * $answertokens or $responsetokens field inside (it is filling from outside)
     */
    public function look_for_matches($other, $threshold, $iscorrect) {
        $result=strlen($this->value)-strlen($this->value)*$threshold;
        $max=round($result);
        $str='';
        $possible_pairs=array();
        for ($k=0; $k<count($other); $k++)
        {
            //preparation wrong words to the function         
            if($iscorrect==true)
            {
                //check on the possibility of (typo)
                $dist=$this->possible_pair($other[$k],$max);
                if($dist!=-1)
                {
                    $pair=new block_formal_langs_matched_tokens_pair(array($this->tokenindex),array($k),$dist);
                    array_push($possible_pairs,$pair);
                }
                //check on the possibility of (extra separator - gluing the wrong words)
                if($k+1!=count($other))
                {
                    $str=$str.($other[$k]->value).("\x0d").($other[$k+1]->value);
                    $lexem=new block_formal_langs_token_base(null,'type',$str,null,0);
                    $dist=$this->possible_pair($lexem, $max);
                    if($dist!=-1)
                    {
                        $pair=new block_formal_langs_matched_tokens_pair(array($this->tokenindex),array($k, $k+1),$dist);
                        array_push($possible_pairs, $pair);
                    }
                    $str='';
                }
            }
            else
            {
                //check on the possibility of (missed separator - gluing the right words)
                if($k+1!=count($other))
                {
                    $str=$str.($other[$k]->value).("\x0d").($other[$k+1]->value);
                    $lexem=new block_formal_langs_token_base(null,'type',$str,null,0);
                    $dist=$this->possible_pair($lexem, $max);
                    if($dist!=-1)
                    {
                        $pair=new block_formal_langs_matched_tokens_pair(array($k,$k+1),array($this->tokenindex),$dist);
                        array_push($possible_pairs,$pair);
                    }
                    $str='';
                }
             }
        }
        return $possible_pairs;
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
     * @param bool $casesensitive whether we should care about for case sensitive
     * @return boolean - if the same lexeme
     */
    public function is_same($other, $casesensitive = true) {
        $result = false;
        if ($this->type == $other->type) {
            if ($casesensitive) {
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
     * Tokens pair constructor.
     *
     * @param array $correcttokens - Indexes of the correct text tokens.
     * @param array $comparedtokens - Indexes of the compared text tokens.
     * @param integer $mistakeweight -  Mistake weight
     * @return tokens pair
     */
    public function __construct($correcttokens, $comparedtokens, $mistakeweight) {
        $this->correcttokens = $correcttokens;
        $this->comparedtokens = $comparedtokens;
        $this->mistakeweight = $mistakeweight;
    }
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
        $this->tokens = clone $this->tokens;
        $this->errors = clone $this->errors;
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
    public function look_for_token_pairs($comparedstream, $threshold) {
        //TODO Birukova
        //1. Find matched pairs (typos, typical errors etc) - Birukova
        //  - look_for_matches function
        //2. Find best groups of pairs - Birukova
        //  - group_matches function, with criteria defined by compare_matches_groups function
        $all_possible_pairs=array();
        $best_groups=array();
        $all_possible_pairs=$this->look_for_matches($comparedstream,$threshold);
        $best_groups=$this->group_matches($all_possible_pairs);
        return $best_groups;
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
    public function look_for_matches($comparedstream, $threshold) {
        //TODO Birukova
        $tokens=$this->tokens;
        $all_possible_pairs=array();
        for ($i=0; $i<count($this->tokens); $i++)
        {
            array_push($all_possible_pairs, $tokens[$i]->look_for_matches($comparedstream,$threshold,true));
        }
        for($i=0; $i<count($comparedstream); $i++)
        {
            array_push($all_possible_pairs, $comparedstream[$i]->look_for_matches($this->tokens,$threshold,false));
        }
        return $all_possible_pairs;
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
        $status = array();
        for($i=0; $i<count($matches); $i++)
        {
            $status[] = 0;
        }
        $sets_of_pairs = array();        
        $array_of_best_groups_of_matches = array();
        //recurcive_backtracking
        $this->recurcive_backtracking($matches, $status, $sets_of_pairs);
        //believe that the first set of the best 
        array_push($array_of_best_groups_of_matches, $sets_of_pairs[0]);

        //compare and entered only the best
        for($i=1; $i<count($sets_of_pairs); $i++)
        {
            //equality
            if($this->compare_matches_groups($array_of_best_groups_of_matches[0], $sets_of_pairs[$i]) == 0)
            {
                array_push($array_of_best_groups_of_matches, $sets_of_pairs[$i]);
            }
            else
            {
                //a new set better than the previous
                if($this->compare_matches_groups($array_of_best_groups_of_matches[0],$sets_of_pairs[$i]) < 0)
                {
                    //cleaning and entered a new set    
                    $array_of_best_groups_of_matches = array();
                    array_push($array_of_best_groups_of_matches, $sets_of_pairs[$i]);
                }
            }
        }
        //returns the resulting array
        return $array_of_best_groups_of_matches;
    }

    
    public function recurcive_backtracking(&$matches,&$status, &$sets_of_pairs){
        $place=-1;
        for($i=0; $i<count($status); $i++){
            if($status[$i]==1)
                $place=$i;
        }
        $place=$place+1;
        $count_status=count($status);
        for($i=$place; $i<$count_status; $i++)
        {
            if($status[$i]==0)
            {
                $status[$i]=1;
                $this->bloking($i, $matches, $status);
                $flag=-1;
                for($j=$i;$j<count($status);$j++){
                    if($status[$j]==0)
                        $flag=1;
                }
                if($flag!=-1)
                    $this->recurcive_backtracking($matches, $status, $sets_of_pairs);
                else
                {              
                    $set_of_pairs=new block_formal_langs_matches_group();
                    $set_of_pairs->matchedpairs=array();
                    $set_of_pairs->mistakeweight=0;
                    $set_of_pairs->correctcoverage=array();
                    $set_of_pairs->comparedcoverage=array();
                    for($k=0; $k<count($status);$k++)
                    {
                        if($status[$k]==1){
                            array_push($set_of_pairs->matchedpairs, $matches[$k]);
                            $set_of_pairs->mistakeweight+=$matches[$k]->mistakeweight;
                            for($g=0; $g<count($matches[$k]->correcttokens); $g++)
                            array_push($set_of_pairs->correctcoverage,$matches[$k]->correcttokens[$g]);
                            for($g=0; $g<count($matches[$k]->comparedtokens); $g++)
                            array_push($set_of_pairs->comparedcoverage,$matches[$k]->comparedtokens[$g]);
                        }
                    }
                    sort($set_of_pairs->correctcoverage);
                    sort($set_of_pairs->comparedcoverage);
                    array_push($sets_of_pairs,$set_of_pairs);
                }
                //unlock
                $this->unlock($i, $matches, $status);
                $status[$i]=0;
                //bloking
                for($j=0; $j<count($status); $j++)
                {
                    if($status[$j]==1)
                        $this->bloking($j, $matches, $status);
                }
            }
        }
    }
    
        public function bloking(&$place, &$matches, &$status){
        $count_pairs=count($matches);
        for($i=$place+1; $i<$count_pairs; $i++)
        {
            if($status[$i]!=-1)
            {
                if($matches[$place]->correcttokens[0]==$matches[$i]->correcttokens[0]||$matches[$place]->comparedtokens[0]==$matches[$i]->comparedtokens[0])
                    $status[$i]=-1;
                if(count($matches[$place]->correcttokens)==2)
                {
                    if($matches[$place]->correcttokens[1]==$matches[$i]->correcttokens[0])
                        $status[$i]=-1;
                    if(count($matches[$i]->correcttokens)==2)
                    {
                        if($matches[$place]->correcttokens[1]==$matches[$i]->correcttokens[1])
                            $status[$i]=-1;
                    }
                }
                if(count($matches[$i]->correcttokens)==2)
                {
                    if($matches[$place]->correcttokens[0]==$matches[$i]->correcttokens[1])
                        $status[$i]=-1;
                }
                if(count($matches[$place]->comparedtokens)==2)
                {
                    if($matches[$place]->comparedtokens[1]==$matches[$i]->comparedtokens[0])
                        $status[$i]=-1;
                    if(count($matches[$i]->comparedtokens)==2)
                    {
                        if($matches[$place]->comparedtokens[1]==$matches[$i]->comparedtokens[1])
                            $status[$i]=-1;
                    }
                }
                if(count($matches[$i]->comparedtokens)==2)
                {
                    if($matches[$place]->comparedtokens[0]==$matches[$i]->comparedtokens[1])
                        $status[$i]=-1;
                }
            }
        }
    }
    public function unlock(&$place, &$matches, &$status){
        $count_status=count($status)-1;
        for($i=$count_status; $i>$place; $i--)
        {
            if($status[$i]!=0)
            {
                if($matches[$place]->correcttokens[0]==$matches[$i]->correcttokens[0]||$matches[$place]->comparedtokens[0]==$matches[$i]->comparedtokens[0])
                    $status[$i]=0;
                if(count($matches[$place]->correcttokens)==2)
                {
                    if($matches[$place]->correcttokens[1]==$matches[$i]->correcttokens[0])
                        $status[$i]=0;
                    if(count($matches[$i]->correcttokens)==2)
                    {
                        if($matches[$place]->correcttokens[1]==$matches[$i]->correcttokens[1])
                            $status[$i]=0;
                    }
                }
                if(count($matches[$i]->correcttokens)==2)
                {
                    if($matches[$place]->correcttokens[0]==$matches[$i]->correcttokens[1])
                        $status[$i]=0;
                }
                if(count($matches[$place]->comparedtokens)==2)
                {
                    if($matches[$place]->comparedtokens[1]==$matches[i]->comparedtokens[0])
                        $status[$i]=0;
                    if(count($matches[$i].comparedtokens)==2)
                    {
                        if($matches[$place]->comparedtokens[1]==$matches[$i]->comparedtokens[1])
                            $status[$i]=0;
                    }
                }
                if(count($matches[$i]->comparedtokens)==2)
                {
                    if($matches[$place]->comparedtokens[0]==$matches[$i]->comparedtokens[1])
                        $status[$i]=0;
                }
            }
        }
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
        if(count($group1->correctcoverage)+count($group1->comparedcoverage) == count($group2->correctcoverage)+count($group2->comparedcoverage))
        {
            if($group1->mistakeweight == $group2->mistakeweight)
                return 0;
            else
            {
                if($group1->mistakeweight < $group2->mistakeweight)
                    return 1;
                else
                    return -1;
            }
        }
        else
        {
            if(count($group1->correctcoverage)+count($group1->comparedcoverage) > count($group2->correctcoverage)+count($group2->comparedcoverage))
                return 1;
            else
                return -1;
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
        $newstream = clone $this;
        //TODO Birukova - change tokens using pairs
    }
}

/**
 * Represents possible set of correspondes between tokens of correct and compared streams
 */
class  block_formal_langs_matches_group {
    /**
     * Array of matched pairs
     */
    public $matchedpairs;

    //Sum of mistake weights
    public $mistakeweight;

    //Sorted array of all correct token indexes for tokens, covered by pairs from this group
    public $correctcoverage;

    //Sorted array of all compared token indexes for tokens, covered by pairs from this group
    public $comparedcoverage;
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
    protected function set_syntax_tree($tree) {
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
        
        //If some old descriptions left - delete it
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
     * @return string - description of node
     */
    public function node_description($nodenumber) {
        $this->node_descriptions_list();
        return $this->descriptions[$nodenumber];
    }

    /**
     * Returns list of node descriptions.
     *
     * @return array of strings, keys are node numbers
     */
    public function node_descriptions_list() {
        global $DB;
        if ($this->descriptions == null)
        {
         $conditions = array(" tableid='{$this->tableid}' ", "tablename = '{$this->tablename}' ");
         $records = $DB->get_records_select('block_formal_langs_node_dscr', implode(' AND ', $conditions));
         foreach($records as $record) {
            $this->descriptions[$record->number] = $record->description; 
         }
        }
        return $this->descriptions;
    }
    /** Test, whether we have a lexeme descriptions for token with specified index
     *  @param int $index index of token
     */
    public function has_description($index) {
       $this->node_descriptions_list();
       if (array_key_exists($index, $this->descriptions) == true)
           return strlen(trim($this->descriptions[$index]))!=0;
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
        if ($this->syntaxtree == null && $this->language->could_parse())
            $this->language->parse($this);
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
?>