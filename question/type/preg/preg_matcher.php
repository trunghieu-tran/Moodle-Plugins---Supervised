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
    //Subpattern capturing during matching
    const SUBPATTERN_CAPTURING = 3;

    /**
    *returns true for supported capabilities
    @param capability the capability in question
    @return bool is capability supported
    */
    public function is_supporting($capability) {
        return false;//abstract class supports nothing
    }

    //////Initial data
    //Regular expression as string
    protected $regex;
    //Modifiers for regular expression
    protected $modifiers;


    //The root of abstract syntax tree of the regular expression
    protected $ast_root;
    //The error messages array
    protected $errors;
    //Array with flags for unsupported node types
    protected $flags;
    //Anchoring - object,  with 'start' and 'end' logical fields, which are true if ancho
    protected $anchor;

    //Matching results
    //Is any match found?
    protected $is_match;
    //Is match full or partial?
    protected $full;
    //Indexes of first matched character - array where 0 => full match, 1=> first subpattern etc
    protected $index_first;
    //Indexes of the last matched character - array where 0 => full match, 1=> first subpattern etc
    protected $index_last;
    //Possible next character
    protected $next;
    //The number of characters left for matching
    protected $left;
    //Cache of the matching results,  string for matching is the key
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
        $this->index_last = array();
        $this->index_first = array();
        $this->next = '';
        $this->left = -1;
        $this->result_cache = array();
        $this->flags = array();
        $this->is_match = false;

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
            $this->errors[] = get_string('unsupported','qtype_preg',get_string($key, 'qtype_preg'));
        }
        $this->errors = array_unique($this->errors);//Fix, for one message about one unsupported operation.

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
            $this->is_match = $result['is_match'];
            return $this->full;
        }

        $this->match_inner($str);

        //Set all string as incorrect if there were no matching
        if (!$this->is_match) {
            $this->index_first[0] = strlen($str);//first correct character is outside the string, so all string is the wrong heading
            $this->index_last[0] = $this->index_first[0] - 1 ;//there are no correct characters
        } else {//do some sanity checks
            if(!$this->is_supporting(preg_matcher::SUBPATTERN_CAPTURING) && $this->count_subpatterns() > 0) {
                throw new qtype_preg_exception('Error: subpatterns returned while engine '.$this->name().' doesn\'t support subpattern matching');
            }
            if(!isset($this->index_first[0]) || !isset($this->index_last[0])) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }
        }

        //Save results to the cache
        $this->result_cache[$str] = array('full' => $this->full, 'index_last' => $this->index_last, 'index_first' => $this->index_first, 'next' => $this->next, 'left' => $this->left, 'is_match' => $this->is_match);
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
    * is there a matching at all?
    */
    public function match_found() {
        return $this->is_match;
    }

    /**
    *returns true if there is a complete match, false otherwise - any matching engine should support at least that
    */
    public function is_matching_complete() {
        return $this->full;
    }

    /**
    returns the number of subpatterns (except full match) in the match
    */
    public function count_subpatterns() {
        return count($this->index_first) - 1;//-1 to not include full match
    }

    /**
    @param subpattern subpattern number, 0 for the whole match
    *returns first correct character index
    */
    public function first_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->count_subpatterns()) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->count_subpatterns().' available');
        }
        return $this->index_first[$subpattern];
    }

    /**
    *returns the index of last correct character if engine supports partial matching
    @param subpattern subpattern number, 0 for the whole match
    @return the index of last correct character
    */
    public function last_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->count_subpatterns()) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->count_subpatterns().' available');
        }
        return $this->index_last[$subpattern];
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
            $this->errors = array_merge($this->errors, $parser->get_error_messages());
        } else {
            $this->ast_root = $parser->get_root();
            $this->anchor = $parser->get_anchor();
        }
        fclose($pseudofile);
    }
}
?>