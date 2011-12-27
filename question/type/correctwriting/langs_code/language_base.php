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
     * Language name. For user.
     * @var string
     */
    private $name;

    /**
     * Technical name. For developer.
     * @var string
     */
    private $techname;

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

    /**
     * Comment string.
     * @var string
     */
    private $comment;

    /**
     * If from language table.
     * @var integer
     */
    private $id;

    /**
     * Language version.
     * @var integer
     */
    private $version;

    /**
     * True if parser enabled, false otherwise.
     * @var boolean.
     */
    private $isparserenabled;

    /**
     * True language is predefined, false otherwise.
     * @var boolean.
     */
    private $ispredefined;

    /**
     * Returns true if this language has predefined lexer and parser
     * (not generated).
     *
     * @return boolean
     */
    public function is_predefined() {
        return $this->ispredefined;
    }

    /**
     * Returns true if this language has  parser enabled.
     *
     * @return boolean
     */
    public function is_parser_enabled() {
        return $this->isparserenabled;
    }

    /**
     * Returns array of tokens.
     *
     * @param $text - input text.
     * @param $isanswer - this flag indicates passed text as student
     * response, otherwise - teacher answer.
     * @return array of tokens
     */
    public function tokenize($text, $isanswer) {
        $this->scaner->tokenize($text, $isanswer);
    }
    
    /**
     * Returns Abstract Syntax Tree.
     *
     * @param $tokens - input array of tokens
     * @return object of ast.
     */
    public function parse($tokens) {
        $this->parser->parse($tokens);
    }

    /**
     * Returns object of language created by id from lang_table.
     *
     * @param $id language id in lang_table
     * @return object of custom or predefined language class
     */
    public static function factory($id) {
        // TODO: get via DB $techname, $ispredefined.

        if ($ispredefined) {
            if (include_once 'langs_code/predefined/' . $techname . '.php') {
                $classname = 'qtype_correctwriting_predefined_' . $techname . '_language';
                return new $classname($id);
            } else {
                throw new Exception('Language not found');
            }
        } else {
            $classname = 'qtype_correctwriting_customized_language';
            return new $classname($id);
        }
    }

    /**
     * Returns count of nodes which needs description or special name.
     *
     * @return integer
     */
    public function descriptions_count() {
        if (is_parser_enabled()) {
            return $this->parser->descriptions_count();
        } else {
            return $this->scaner->tokens_count();
        }
    }

    /**
     * Returns description string for passed node.
     *
     * @param $node object of node
     * @return string - description of node
     */
    public function get_description($node, $answerid) {
        // TODO:
        // connect moodle DB
        // SELECT description FROM moodle_descriptions_table
        // AS mtul WHERE mtut.langid == $this->id AND mtut.number
        // == $node->id AND mtut.answerid == $this->answerid;
        $desc = '';
        return $desc;
    }

    /**
     * Returns list of node descriptions.
     *
     * @param $answer - moodle answer object
     * @return array of strings
     */
    public function descriptions_list($answer) {
        // connect moodle DB by answerid
        if (is_parser_enabled()) {
            $result = $this->scaner->token_name_list();
            return concatenate_arrays($result, $this->parser->nonterminal_name_list());
        } else {
            return $this->scaner->token_name_list();
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
        if (is_parser_enabled()) {
            $result = $this->scaner->token_list();
            return concatenate_arrays($result, $this->parser->nonterminal_list());
        } else {
            return $this->scaner->token_list();
        }
    }
}

/**
 * Predefined language class.
 *
 * @copyright  &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class qtype_correctwriting_predefined_language extends qtype_correctwriting_abstract_language {

    public function __construct($id, $langdbrecord = NULL) {
        $this->id = $id;

        if ($langdbrecord) {
            // get all info from it
        } else {
            // TODO: via DB get $name, $techname, $isparserenabled, $version
        }

        $scanerclass = 'qtype_correctwriting_predefined_' . $techname . '_scaner';
        $this->scaner = new $scanerclass();
        if ($isparserenabled) {
            $parserclass = 'qtype_correctwriting_predefined_' . $techname . '_parser';
            $this->parser = new $parserclass();
        }
    }
}


/**
 * Customized language class.
 *
 * @copyright  &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class qtype_correctwriting_customized_language extends qtype_correctwriting_abstract_language {

    public function __construct($id, $langdbrecord = NULL) {

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
        if ($isparserenabled) {
            $this->parser = new qtype_correctwriting_custom_parser($rules);
        }
    }
}
