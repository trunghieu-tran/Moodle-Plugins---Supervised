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

?>