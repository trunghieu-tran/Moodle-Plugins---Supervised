<?php
/**
 * Defines base language class.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Mamontov Dmitriy Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blocks
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/question/type/poasquestion/stringstream/stringstream.php');

abstract class block_formal_langs_abstract_language {

    /**
     * Id in language table.
     * @var integer
     */
    private $id;

    /**
     * Language version (for user-defined languages mainly).
     * @var integer
     */
    private $version;

    /**
     * Object of lexer class.
     * @var object of lexer
     */
    public $scaner;

    /**
     * Obbject of parser class.
     * @var object of parser
     */
    public $parser;
    
    //TODO - errors array (for scanning and parser errors) and access method for it

    /**
     * Returns true if this language has predefined (hardcoded) lexer and parser.
     *
     * @return boolean
     */
    abstract public function is_predefined();

    /**
     * Returns technical name of the language.
     */
    abstract public function name();

    /**
     * User-visible name of the language
     *
     * The rules for it are different for predefined and user-defined languages
     */
    abstract public function ui_name();

    /**
     * User description (help) for the language
     */
    abstract public function description();

    /**
     * Returns true if this language has parser enabled.
     *
     * @return boolean
     */
    public function could_parse() {
        return false;//A language must openly declare it support parsing
    }

    /**
     * Fills token stream field of the processed string objects
     *
     * Add lexical errors if any exists.
     * @param object block_formal_langs_processed_string object with string filled
     *
     */
    public function scan(&$processedstring) {       
        $this->scaner->tokenize($processedstring);
    }

    /**
     * Fills syntax tree field of the processed string objects.
     *
     * Add errors for answer parsing
     * @param $processedstring - block_formal_langs_processed_string object with string filled
     * @param $iscorrect boolean true if we need to reduce to start symbol (correct text parsing), false if not (compared text parts parsing)
     */
    public function parse($processedstring, $iscorrect) {
        //TODO - think about how should be done compared string parsing and what additional info is needed
        //TODO check if string isn't scanned and scan if necessary
        $this->parser->parse($processedstring, $iscorrect);
    }


    /**
     *  Creates a processed string from string
     *  @param string $string string
     *  @return block_formal_langs_processed_string string
     */
     public function create_from_string($string) {
        $result = new block_formal_langs_processed_string($this);
        $result->string = $string;
        return $result;
     }

     /**
      *  Creates a processed string from table and id in BD (string optional)
      *  @param string $tablename table name
      *  @param string $tableid    id of source table
      *  @param string|null $string string data
      *  @return block_formal_langs_processed_string processed string
      */
    public function create_from_db($tablename, $tableid, $string = null) {
        $result = new block_formal_langs_processed_string($this);
        $result->set_table_params($tablename,$tableid);
        $result->string  = $string;
        return $result;
    }

}

/**
 * Predefined language class.
 *
 * @copyright &copy; 2012  Oleg Sychev
 * @author    2012 Oleg Sychev, Mamontov Dmitriy, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
abstract class block_formal_langs_predefined_language extends block_formal_langs_abstract_language {

    
    public function __construct($id, $langdbrecord = NULL) {
        $this->id = $id;

        if ($langdbrecord) {
            // get all info from it
        } else {
            // TODO: via DB get $name, $techname, $isparserenabled, $version
        } 
        if ($this->could_parse()) {
            $parserclass = 'block_formal_langs_predefined_' . $this->name() . '_parser';
            $this->parser = new $parserclass();
        }
    }
    /** Preprocesses a string before scanning. This can be used for simplifying analyze
        and some other purposes, like merging some different variations of  same character
        into one
        @param string $string input string for scanning
        @return string
     */
    protected function preprocess_for_scan($string) {
        return $string;
    }
    /**
     * Fills token stream field of the processed string objects
     *
     * Add lexical errors if any exists.
     * @param object block_formal_langs_processed_string object with string filled
     *
     */
    public function scan(&$processedstring) {
        // Lexer must be a valid JLexPHP class, or implement next_token() and get_errors()
        // Also it can implement find_errors for seeking of deferred errors
        $scanerclass = 'block_formal_langs_predefined_' . $this->name() . '_lexer_raw';
        $string = $processedstring->string;
        if (is_a($string,'qtype_poasquestion_string') == true) {
            $string = $string->string();
        }
        $string = $this->preprocess_for_scan($string);
        $stream = new block_formal_langs_token_stream();
        $stream->tokens = array();
        $stream->errors = array();
        if ($string !== '') {
            StringStreamController::createRef('str', $string);
            $pseudofile = fopen('string://str', 'r');
            $this->scaner = new $scanerclass($pseudofile);
            //Now, we are splitting text into lexemes
            $tokens = array();
            while ($token = $this->scaner->next_token()) {
                $tokens[] = $token;
            }

            $stream->tokens = $tokens;
            $stream->errors = $this->scaner->get_errors();
        }

        $processedstring->stream = $stream;

    }
    
    /**
     * Returns true if this language has predefined (hardcoded) lexer and parser.
     *
     * @return boolean
     */
    public function is_predefined() {
        return true;
    }

    
    /**
     * User-visible name of the language
     */
    public function ui_name() {
        return get_string('lang_' . $this->name() , 'block_formal_langs');
    }

    /**
     * User description (help) for the language
     */
    public function description() {
        return get_string('lang_' . $this->name() . '_help' , 'block_formal_langs');
    }
}


/**
 * Customized language class.
 *
 * @copyright &copy; 2012  Oleg Sychev
 * @author  2012 Oleg Sychev, Mamontov Dmitriy, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class block_formal_langs_userdefined_language extends block_formal_langs_abstract_language {

    /**
     * Technical name. 
     *
     * @var string
     */
    private $name;

    /**
     * User-visible name. 
     *
     * @var string
     */
    private $uiname;

    /**
     * Description string.
     * @var string
     */
    private $description;

    /**
     * True if parser enabled, false otherwise.
     * @var boolean.
     */
    private $couldparse;//TODO - overload could_parse() function only when syntax_analyzer class would be written

    public function __construct($id, $version=1, $langdbrecord = NULL) {

        $this->id = $id;

        if ($langdbrecord) {
            // get all info from it            
        } else {
            // TODO: via DB get $name, $techname. $isparserenabled.
            // TODO: via DB get $patterns, conditions, $rules by $id.            
        }
        /* $scaner = new qtype_correctwriting_custom_scaner(); */
        /* $scaner->set_patterns($id); // TODO: get from DB or what */
        /* $scaner->set_conditions($id); // TODO: get from DB or what */
        /* $parser = new qtype_correctwriting_custom_parser(); */
        /* $parser->set_rules($id); // TODO: get from DB or what */
        /* $parser->set_terminals($id); // TODO: get from DB or what */
        
        // TODO: $scanerpatternstext = get via DB and by lang id;
        // TODO:parserpatternstext = get via DB and by lang

        $this->scaner = new qtype_correctwriting_custom_scaner($patterns, $conditions);
        if ($this->could_parse()) {
            $this->parser = new qtype_correctwriting_custom_parser($rules);
        }
    }

    /**
     * Returns true if this language has predefined (hardcoded) lexer and parser.
     *
     * @return boolean
     */
    public function is_predefined() {
        return false;
    }

    /**
     * Returns technical name of the language.
     */
    public function name() {
        return $this->name;
    }

    /**
     * User-visible name of the language
     */
    public function ui_name() {
        return $this->uiname;
    }

    /**
     * User description (help) for the language
     */
    public function description() {
        return $this->description;
    }

}
