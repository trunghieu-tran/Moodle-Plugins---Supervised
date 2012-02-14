<?php
/**
 * Defines base language class.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Sergey Pashaev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

abstract class qtype_correctwriting_abstract_language {

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
     * Returns array of tokens.
     *
     * Add errors for answer scanning
     * @param $text - input text.
     * @param $isanswer - this flag indicates passed text as student
     * response, otherwise - teacher answer.
     * @return array of tokens
     */
    public function scan($text, $isanswer) {
        $this->scaner->tokenize($text, $isanswer);
    }
    
    /**
     * Returns Abstract Syntax Tree.
     *
     * Add errors for answer parsing
     * @param $tokens - input array of tokens
     * @param $isanswer boolean true if we need to reduce to start symbol (answer parsing), false if not (response parts parsing)
     * @return array of objects of ast (or tree roots) - should contain one element for answer parsing
     */
    public function parse($tokens, $isanswer) {
        $this->parser->parse($tokens);
    }

    /**
     * Returns object of language created by id from lang_table.
     *
     * @param $id language id in lang_table
     * @param $version language version in lang_table
     * @return object of custom or predefined language class
     */
    public static function factory($id, $version=1) {
        //TODO: get via DB $techname, $ispredefined.
        //TODO - implement version support

        if ($this->ispredefined) {
            require_once('langs_code/predefined/' . $this->name() . '.php');
            $classname = 'qtype_correctwriting_predefined_' . $this->name() . '_language';
            return new $classname($id);
        } else {
            $classname = 'qtype_correctwriting_userdefined_language';
            return new $classname($id, $version);
        }
    }

    /**
     * Returns count of nodes which needs description or special name.
     *
     * @return integer
     */
    public function nodes_requiring_description_count() {//TODO - name
        if ($this->could_parse()) {
            return $this->parser->nodes_requiring_description_count();
        } else {
            return $this->scaner->tokens_count();
        }
    }

    /**
     * Returns list of node objects which requires description.
     *
     * @param $answer - moodle answer object
     * @return array of node objects
     */
    public function nodes_requiring_description_list($answer) {
        // TODO: return node objects
        // connect moodle DB by answerid
        if ($this->could_parse()) {
            /*$result = $this->scaner->token_list();
            return concatenate_arrays($result, $this->parser->nonterminal_list());
            */ // TODO - get only nodes requiring user-defined description from the parser
        } else {
            return $this->scaner->token_list();
        }
    }

    /**
     * Returns description string for passed node.
     *
     * @param $nodenumber number of node
     * @return string - description of node
     */
    public function node_description($nodenumber, $answerid) {
        // TODO:
        // connect moodle DB
        // SELECT description FROM moodle_descriptions_table
        // AS mtul WHERE mtut.langid == $this->id AND mtut.number
        // == $node->id AND mtut.answerid == $this->answerid;
        //cache descriptions
        //Parser, if enabled, could generate descriptions for the nodes not stored in DB
        $desc = '';
        return $desc;
    }

    /**
     * Returns list of node descriptions.
     *
     * @param $answer - moodle answer object
     * @return array of strings, keys are node numbers
     */
    public function node_descriptions_list($answer) {
        // connect moodle DB by answerid
        //cache descriptions
        //Parser, if enabled, could generate descriptions for the nodes not stored in DB
        //TODO - should the function return only nodes with user-defined description or descpriptions for all nodes? Probably first...
        if (is_parser_enabled()) {
            $result = $this->scaner->token_name_list();
            return concatenate_arrays($result, $this->parser->nonterminal_name_list());
        } else {
            return $this->scaner->token_name_list();
        }
    }

}

/**
 * Predefined language class.
 *
 * @copyright  &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
abstract class qtype_correctwriting_predefined_language extends qtype_correctwriting_abstract_language {

    public function __construct($id, $langdbrecord = NULL) {
        $this->id = $id;

        if ($langdbrecord) {
            // get all info from it
        } else {
            // TODO: via DB get $name, $techname, $isparserenabled, $version
        }

        $scanerclass = 'qtype_correctwriting_predefined_' . $this->name() . '_scaner';
        $this->scaner = new $scanerclass();
        if ($this->could_parse()) {
            $parserclass = 'qtype_correctwriting_predefined_' . $this->name() . '_parser';
            $this->parser = new $parserclass();
        }
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
        return get_string('lang_' . $this->name() , 'qtype_correctwriting');
    }

    /**
     * User description (help) for the language
     */
    public function description() {
        return get_string('lang_' . $this->name() . '_help' , 'qtype_correctwriting');
    }
}


/**
 * Customized language class.
 *
 * @copyright  &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class qtype_correctwriting_userdefined_language extends qtype_correctwriting_abstract_language {

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
