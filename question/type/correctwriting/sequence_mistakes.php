<?php
/**
 * Defines an implementation of mistakes, that are determined by computing LCS and comparing answer and response
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A mistake, that consists from moving one lexeme to different position, than original
class qtype_correctwriting_lexeme_moved_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     * @param int    $responseindex index of response token
     */
    public function __construct($language,$answer,$answerindex,$response,$responseindex) {
        $this->position = $response[$responseindex]->position();
        $this->languagename = $language->name();
        
        $this->answer = $answer;
        $this->response = $response;
        //Fill answer data
        $this->answermistaken = array();
        $this->answermistaken[] = $answerindex;
        //Fill response data
        $this->responsemistaken = array();
        $this->responsemistaken[] = $responseindex;
        
        //Create a mistake message
        $a = new stdClass;
        $a->type = $answer[$answerindex]->type();
        $a->answerline = $answer[$answerindex]->position()->linestart();
        $a->answerposition = $answer[$answerindex]->position()->colstart();
        $a->responseline = $response[$responseindex]->position()->linestart();
        $a->responseposition = $response[$responseindex]->position()->colstart();
        $this->mistakemsg = get_string('movedmistakemessage','qtype_correctwriting',$a);
    }
}

// A mistake, that consists from adding a lexeme to response, that is not in answer
class qtype_correctwriting_lexeme_added_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param array  $response      array response tokens
     * @param int    $responseindex index of response token
     */
    public function __construct($language,$answer,$response,$responseindex) {
        $this->position = $response[$responseindex]->position();
        $this->languagename = $language->name();
        
        $this->answer = $answer;
        $this->response = $response;
        //Fill answer data
        $this->answermistaken = array();
        //Fill response data
        $this->responsemistaken = array();
        $this->responsemistaken[] = $responseindex;
        
        //Create a mistake message
        $a = new stdClass;
        $a->type = $response[$responseindex]->type();
        $a->line = $response[$responseindex]->position()->linestart();
        $a->position = $response[$responseindex]->position()->colstart();
        $this->mistakemsg = get_string('addedmistakemessage','qtype_correctwriting',$a);
    }
}

// A mistake, that consists of  skipping a lexeme from answer
class qtype_correctwriting_lexeme_skipped_mistake extends qtype_correctwriting_response_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     */
    public function __construct($language,$answer,$answerindex,$response) {
        $this->position = $answer[$answerindex]->position();
        $this->languagename = $language->name();
        
        $this->answer = $answer;
        $this->response = $response;
        //Fill answer data
        $this->answermistaken=array();
        $this->answermistaken[] = $answer_index;
        //Fill response data
        $this->responsemistaken = array();
        
        //Create a mistake message
        $a = $answer[$answerindex]->type();
        $this->mistakemsg = get_string('skippedmistakemessage','qtype_correctwriting',$a);
    }
}

?>