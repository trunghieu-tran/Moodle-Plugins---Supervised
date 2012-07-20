<?php
/**
 * Defines an implementation of mistakes, that are determined by lexical analyzer
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author  Oleg Sychev, Dmitriy Mamontov,Birukova Maria Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from lexical analyzer. We need them to indicate
// what lexemes was corrected by analyzer.
class qtype_correctwriting_lexical_mistake extends qtype_correctwriting_response_mistake {
    // An array of corrected response lexemes
    public $correctedresponse;
    // An arraty of fixed lexemes indexes
    public $correctedresponseindex;
}

// A C-specific lexical mistake
class qtype_correctwriting_c_language_lexical_mistake extends qtype_correctwriting_lexical_mistake {
    /**
     *   Creates a specific error
     *   @param qtype_correctwriting_question $question question object
     *   @param block_formal_langs_processed_string $response response object
     *   @param block_formal_langs_token_base $token basic token
     *   @param string  $mesg string from locale, that will be used to create some language stuff
     */
    public function __construct($question, $response, $token, $mesg = null) {
        $this->languagename = $question->get_used_language()->name();
        $this->position = $token->position();
        $this->answermistaken = null;
        $this->answer = null;
        $this->response = $response;
        $this->responsemistaken = $token->number();
        $this->weight = $question->lexicalerrorweight; 
        
        $this->correctedresponse = null;
        $this->correctedresponseindex = null;
        
        if ($mesg != null) {
            $a = new stdClass();
            $a->line = $this->position->linestart();
            $a->col = $this->position->colstart();
            $a->value = $token->value();
            $this->mistakemsg = get_string($mesg, 'qtype_correctwriting', $a);
        }
    }
}

// A mistake, which consists of unmatched quote in student response
class qtype_correctwriting_c_language_unmatched_quote_mistake extends qtype_correctwriting_c_language_lexical_mistake {

    /**
     *   Creates a specific error
     *   @param qtype_correctwriting_question $question question object
     *   @param block_formal_langs_processed_string $response response object
     *   @param block_formal_langs_token_base $token basic token
     */
    public function __construct($question, $response, $token) {
        parent::__construct($question, $response, $token, 'clanguageunmatchedquote');
    }    
}

// A mistake, which consists of unmatched single quote in student response
class qtype_correctwriting_c_language_unmatched_single_quote_mistake extends qtype_correctwriting_c_language_lexical_mistake {

    /**
     *   Creates a specific error
     *   @param qtype_correctwriting_question $question question object
     *   @param block_formal_langs_processed_string $response response object
     *   @param block_formal_langs_token_base $token basic token
     */
    public function __construct($question, $response, $token) {
        parent::__construct($question, $response, $token, 'clanguageunmatchedsquote');
    }
}
// A mistake, that consists, that some unmatched symbol found
class qtype_correctwriting_c_language_unknown_symbol_mistake extends qtype_correctwriting_c_language_lexical_mistake {

    /**
     *   Creates a specific error
     *   @param qtype_correctwriting_question $question question object
     *   @param block_formal_langs_processed_string $response response object
     *   @param block_formal_langs_token_base $token basic token
     */
    public function __construct($question, $response, $token) {
        parent::__construct($question, $response, $token, 'clanguageunknownsymbol');
    }
}

class qtype_correctwriting_c_language_multicharacter_literal extends qtype_correctwriting_c_language_lexical_mistake {

    /**
     *   Creates a specific error
     *   @param qtype_correctwriting_question $question question object
     *   @param block_formal_langs_processed_string $response response object
     *   @param block_formal_langs_token_base $token basic token
     */
    public function __construct($question, $response, $token) {
        parent::__construct($question, $response, $token, 'clanguagemulticharliteral');
    }
}

?>