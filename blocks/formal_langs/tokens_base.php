<?php
/**
 * Defines generic token and node classes.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Sergey Pashaev, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

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
        traverse($root, 'print_node');
    }
    
    public function traverse($node, $callback) {
        // entering node
        if ($node->is_leaf()) {
            // leaf action
            $callback($node, $args);//TODO - what is args?
        }

        foreach($node->childs as $child) {//TODO - why no callback for non-leaf nodes?
            traverse($child, $callback);
        }
    }
}


class block_formal_langs_ast_node_base {

    /**
     * Is this node from correct answer or response.
     * @var boolean
     */
    protected $isanswer;
    
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
     * Node description.
     * @var string
     */
    protected $description;

    public function __construct($type, $value, $position, $number, $isanswer) {
        $this->number = $number;
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
        $this->isanswer = $isanswer;

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

    public function description() {
        if ('' == $this->description) {
            // TODO: calc description
            return $this->description;
        } else {
            return $this->description;
        }
    }

    public function is_answer() {
        return $this->isanswer;
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

    
    public function value() {
        return $this->value;
    }

    /**
     * Basic lexeme constructor.
     *
     * @param string $type - type of lexeme
     * @param string $value - semantic value of lexeme
     * @return base_token
     */
    public function __construct($number, $type, $value, $isanswer, $position) {
        $this->if = $number;
        $this->type = $type;
        $this->value = $value;
        $this->isanswer = $isanswer;
        $this->position = $position;
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
    }

    /**
     * Base lexical mistakes handler. Looks for possible matches for this
     * token in other answer and return an array of them.
     *
     * The functions works differently depending on token of which answer it's called.
     * For correct _answer_ it looks for typos, extra separators,
     * typical mistakes (in particular subclasses) etc - i.e. all mistakes with one token from answer.
     * For student's _response_ it looks for missing separators, extra quotes etc, i.e. mistakes which
     * have more than one token from answer, but only one from response.
     *
     * @param array $other - array of tokens  (other answer)
     * @param integer $threshold - lexical mistakes threshold
     * @return array - array of block_formal_langs_matched_tokens_pair objects with blank
     * $answertokens or $responsetokens field inside (it is filling from outside)
     */
    public function look_for_matches($other, $threshold) {
        // TODO: generic mistakes handling
    }
    
    
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *  
     * @param block_formal_langs_token_base $other other lexeme
     * @return boolean - if the same lexeme
     */
    public function is_same($other) {
        $result = false;
        if ($this->type == $other->type) {
            $result = $this->value == $other->value;
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
     * Indexes of the correct answer tokens.
     * @var array
     */
    public $answertokens;

    /**
     * Indexes of the student response tokens.
     * @var array
     */
    public $responsetokens;

    /**
     * Mistake weight (Levenshtein distance, for example).
     *
     * Zero is no mistake.
     *
     * @var integer
     */
    public $mistakeweight;
}

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

        return new block_formal_langs_node_position($minlinestart, $maxlinened, $mincolstart, $maxcolend);
    }
}
?>