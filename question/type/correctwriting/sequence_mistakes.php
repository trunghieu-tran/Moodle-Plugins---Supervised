<?php
/**
 * Defines an implementation of errors, that are determined by computing LCS
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

//A mistake, that consists from moving one lexeme to other
class qtype_correctwriting_lexeme_moving_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Creates a string with position of lexeme
     */
    private function get_position($lexeme) {
        return $lexeme->position()->line() . ", " . $lexeme->position()->column();
    }
    /**
     * Constructs a new error, filling it with constant message
     * @param language object a language object
     * @param answer  array   answer tokens
     * @param answer_index int   index of answer token
     * @param response     array response tokens
     * @param response_index int  index of response index
     */
    public function __construct($language,$answer,$answer_index,$response,$response_index) {
        $this->position=$response[$response_index]->position();
        $this->languagename=$language;
        
        $this->answer=$answer;
        $this->respomse=$response;
        //Fill answer data
        $this->answermistaken=array();
        array_push($this->answermistaken,$answer_index);
        //Fill response part
        $this->responsemistaken=array();
        array_push($this->responsemistaken,$response_index);
        
        //Create a mistake message
        $this->mistakemsg="A lexeme \"".$answer[$answer_index]->token_type()."\" has been moved from ";
        $this->mistakemsg=$this->mistakemsg . $this->get_position($answer[$answer_index]);
        $this->mistakemsg=$this->mistakemsg . "  to  " ;
        $this->mistakemsg=$this->mistakemsg .  $this->get_position($response[$response_index]);
    }
}

//A mistake, that consists from adding a lexeme to response, that is not in an answer
class qtype_correctwriting_lexeme_adding_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param language object a language object
     * @param answer  array   answer tokens
     * @param response     array response tokens
     * @param response_index int  index of response index
     */
    public function __construct($language,$answer,$response,$response_index) {
        $this->position=$response[$response_index]->position();
        $this->languagename=$language;
        
        $this->answer=$answer;
        $this->respomse=$response;
        //Fill answer data
        $this->answermistaken=array();
        //Fill response part
        $this->responsemistaken=array();
        array_push($this->responsemistaken,$response_index);
        
        //Create a mistake message
        $this->mistakemsg="A lexeme \"". $response[$response_index]->token_type(). "\" is odd in answer";
    }
}

//A mistake, that  consits of  skipping a lexeme from answer
class qtype_correctwriting_lexeme_removing_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param language object a language object
     * @param answer  array   answer tokens
     * @param answer_index int   index of answer token
     * @param response     array response tokens
     */
    public function __construct($language,$answer,$answer_index,$response) {
        $this->position=$response[$response_index]->position();
        $this->languagename=$language;
        
        $this->answer=$answer;
        $this->respomse=$response;
        //Fill answer data
        $this->answermistaken=array();
        array_push($this->answermistaken,$answer_index);
        //Fill response part
        $this->responsemistaken=array();
        
        //Create a mistake message
        $this->mistakemsg="A lexeme \"". $answer[$answer_index]->token_type(). "\" is missing";
    }
}

//A factory for creating all kinds of these errors
class qtype_correctwriting_sequence_error_factory {
    
    private $language;  //Language object
    private $answer;    //Array of answer tokens
    private $response;  //Array of response tokens
    
    /**
     * Constructs a factory for crating sequence errors
     * @param language object used language
     * @param answer   array  of answer tokens
     * @param response array  of response tokens
     */
    public function __construct($language,$answer,$respone) {
        $this->language=$language;
        $this->answer=$answer;
        $this->response=$response;
    }
    /**
     * Creates moved lexeme error
     * @param int answer_index index of lexeme from answer
     * @param int response_index index of lexeme from response
     */
    public function create_moved_error($answer_index,$response_index) {
        return qtype_correctwriting_lexeme_moving_mistake($this->language,$this->answer,
                                                          $answer_index,
                                                          $this->response,
                                                          $response_index);
    }
    /**
     *  Creates a removed lexeme error
     *  @param int answer_index index of lexeme from answer
     */
    public function create_removing_error($answer_index) {
        return qtype_correctwriting_lexeme_removing_mistake($this->language,
                                                            $this->answer,
                                                            $answer_index,
                                                            $this->response);
    }
    /**
     * Creates an odd lexeme error
     * @param int response_index index of lexeme from response
     */
    public function create_added_error($response_index) {
        return qtype_correctwriting_lexeme_adding_mistake($this->language,
                                                          $this->answer,
                                                          $this->response,
                                                          $response_index);
    }
}
?>