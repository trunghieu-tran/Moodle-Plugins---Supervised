<?php
/**
 * Defines abstract class of matcher, extend it to create a new mathcing engine
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');


class preg_matcher {


    //Constants for the capabilities which could (or could not) be supported by matching engine
    //Partial matching (returning the index of last matched character)
    const PARTIAL_MATCHING = 0;
    //Returning next possible character after partial match
    const NEXT_CHARACTER = 1;
    //Returning the smallest number of characters that needed to complete partial match
    const CHARACTERS_LEFT = 2;

    /**
    *returns true for supported capabilities
    @param capability the capability in question
    @return bool is capability supported
    */
    public function is_supporting($capability) {
        return false;//abstract class supports nothing
    }

    //Initial data
    protected $regex;
    protected $modifiers;
    protected $unchor;

    //The root of abstract syntax tree of the regular expression
    protected $ast_root;
    //The error messages array
    protected $errors;
    //Array with flags for unsupported node types
    protected $flags;

    //Matching results
    protected $full;
    //Index of first matched character
    protected $index_first;
    //Index of the last matched character
    protected $index_last;
    //Possible next character
    protected $next;
    //The number of characters left for matching
    protected $left;
    //Cache of the matching results
    protected $result_cache;


    public function name() {
        return 'preg_matcher';
    }

    /**
    *parse regex and do all necessary preprocessing
    @param regex - regular expression for which will be build finite automate
    @param modifiers - modifiers of regular expression
    */
    public function __construct($regex = null, $modifiers = null) {
        $this->errors = array();
        $this->full = false;
        $this->index_last = -1;
        $this->index_first = 0;
        $this->next = '';
        $this->left = -1;
        $this->result_cache = array();
        $this->flags = array();

        if ($regex === null) {
            return;
        }

        //Are passed modifiers supported?
        if (is_string($modifiers)) {
            $supportedmodifiers = $this->get_supported_modifiers();
            for ($i=0; $i < strlen($modifiers); $i++) {
                if (strpos($supportedmodifiers,$modifiers[$i]) === false) {
                    $a = new stdClass;
                    $a->modifier = $modifiers[$i];
                    $a->classname = $this->name();
                    $this->errors[] = get_string('unsupportedmodifier','qtype_preg',$a);
                }
            }
        }

        $this->regex = $regex;
        $this->modifiers = $modifiers;
        //do parsing
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
        } else {
            $this->ast_root = null;
        }
        if ($this->is_error_exists()) {
            //if parsing error then no tree, nothing accept.
            return;
        }
        //check regular expression for validity
        $this->accept_regex($this->ast_root);
    }

    /**
    * returns string of regular expression modifiers supported by this engine
    */
    public function get_supported_modifiers() {
        return 'i';//no modifiers support by default
    }

    /**
    * is this engine need a parsing of regular expression?
    @return bool if parsing needed
    */
    protected function is_parsing_needed() {
        //most engines will need parsing
        return true;
    }

    /**
    *check regular expression for errors using absract syntax tree
    @param node root of the tree
    @return bool is tree accepted
    */
    protected function accept_regex($node) {
        $this->accept_node($node);
        if ($node->type == NODE) {
            if ($node->subtype == NODE_CONDSUBPATT) {
                $this->accept_regex($node->thirdop);
            }
            if($node->subtype == NODE_CONC || $node->subtype == NODE_ALT || $node->subtype == NODE_CONDSUBPATT) {
                $this->accept_regex($node->secop);
            }
            $this->accept_regex($node->firop);
        }

        //Add error messages for unsupported nodes
        foreach ($this->flags as $key => $value) {
            $this->errors[] = get_string($key, 'qtype_preg').' '.get_string('unsupported','qtype_preg');
        }
        $this->errors = array_unique($this->errors);//Fix, for one message about one unsuppoerted operation.

        if (empty($this->errors)) {
            return true;
        } else {
            return false;
        }

    }

    /**
    *checks if this abstract sytax tree node supported by this matching engine, adding error messages for unsupported nodes
    *This function should add names of strings for unsupported operations to $this->flags
    @param node - node to check
    @return bool is node accepted
    */
    protected function accept_node($node) {
        $this->flags['noabstractaccept'] = true;
        return false;
    }


    /**
    match regular expression with given string, calls match_inner from a child class to do the real matching
    @param str a string to match
    @return bool true for complete match, false otherwise
    */
    public function match($str) {

        //Are there any errors?
        if (!empty($this->errors)) {
            throw new qtype_preg_exception('Error: trying to do matching on regex with errors!');
        }

        //Are results cached already?
        if (array_key_exists($str,$this->result_cache)) {
            $result = $this->result_cache[$str];
            $this->full = $result['full'];
            $this->index_last = $result['index_last'];
            $this->index_first = $result['index_first'];
            $this->next = $result['next'];
            $this->left = $result['left'];
            return $this->full;
        }

        $this->match_inner($str);

        //Save results to the cache
        $this->result_cache[$str] = array('full' => $this->full, 'index_last' => $this->index_last, 'index_first' => $this->index_first, 'next' => $this->next, 'left' => $this->left);
        return $this->full;
    }

    /**
    *do real matching, should be implemented in child classes, set properties full, index, next and left
    @param str a string to match
    */
    protected function match_inner($str) {
        throw new qtype_preg_exception('Error: matching has not been implemented for '.$this->name().' class');
    }

    /**
    *returns true if there is a complete match, false otherwise - any matching engine should support at least that
    */
    public function is_matching_complete() {
        return $this->full;
    }

    /**
    *returns first correct character index
    */
    public function first_correct_character_index() {
        return $this->index_first;
    }

    /**
    *returns the index of last correct character if engine supports partial matching
    @return the index of last correct character
    */
    public function last_correct_character_index() {
        return $this->index_last;
    }

    /**
    *returns next possible character (to hint)
    */
    public function next_char() {
        if ($this->is_supporting(preg_matcher::NEXT_CHARACTER)) {
            return $this->next;

        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports hinting');
    }

    /**
    *returns how many characters left to closest possible match
    */
    public function characters_left() {
        if ($this->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
            return $this->left;
        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports counting of the remaining characters');
    }

    /**
    * Are errors in regex?
    @return bool  errors exists
    */
    public function is_error_exists() {
        return (!empty($this->errors));
    }

    /**
    * returns errors for regex
    @return array of errors
    */
    public function get_errors() {
        return $this->errors;
    }

    /**
    *function do lexical and syntaxical analyze of regex and build tree, root saving in $this->roots[0]
    @param $regex - regular expirience for building tree
    */
    /*protected*/public function build_tree($regex) {

        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $parser = new preg_parser_yyParser;
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;

            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        if ($parser->get_error()) {
            $this->errors[] = get_string('incorrectregex', 'qtype_preg');
        } else {
            $this->ast_root = $parser->get_root();
            $this->unchor = $parser->get_unchor();
        }
        fclose($pseudofile);
    }
}
?>