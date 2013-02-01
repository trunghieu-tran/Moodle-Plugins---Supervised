<?php
/**
 * Defines an implementation of mistakes, that are determined by computing LCS and comparing answer and response
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from sequence_analyzer
abstract class qtype_correctwriting_sequence_mistake extends qtype_correctwriting_response_mistake {
    /**
     * LCS as described in sequence analyzer, used in generating some hints, based on mistakes
     * @var array
     */
    protected $lcs;

    /**
     * Sets an lcs for mistake
     * @param $lcs
     */
    public function set_lcs($lcs) {
        $this->lcs = $lcs;
    }

    /**
     * Returns an lcs, which mistake is based on
     * @return array
     */
    public function lcs() {
        return $this->lcs;
    }
}


// A mistake, that consists from moving one lexeme to different position, than original
class qtype_correctwriting_lexeme_moved_mistake extends qtype_correctwriting_sequence_mistake {

    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     * @param int    $responseindex index of response token
     */
    public function __construct($language,$answer,$answerindex,$response,$responseindex) {
        $this->languagename = $language->name();

        $this->answer = $answer->stream->tokens;
        $this->response = $response->stream->tokens;

        $this->position = $this->response[$responseindex]->position();

        $this->answerstring = $answer;
        $this->mistakemsg = null;
        //Fill answer data
        $this->answermistaken = array();
        $this->answermistaken[] = $answerindex;
        //Fill response data
        $this->responsemistaken = array();
        $this->responsemistaken[] = $responseindex;
    }

    /** Performs a mistake message creation if needed
     */
    public function get_mistake_message() {
        if ($this->mistakemsg == null) {
            //Create a mistake message
            $a = new stdClass();
            $answerindex = $this->answermistaken[0];
            $a->description = $this->token_descriptions();
            if ($a->description !== null) {
                $this->mistakemsg = get_string('movedmistakemessage','qtype_correctwriting',$a);
            } else {
                $data = $this->answer[$answerindex]->value();
                if (!is_string($data)) {
                    $data = $data->string();
                }
                $a->value = $data;
                $a->line = $this->answer[$answerindex]->position()->linestart();
                $a->position = $this->answer[$answerindex]->position()->colstart();
                $this->mistakemsg = get_string('movedmistakemessagenodescription','qtype_correctwriting',$a);
            }
        }
        return parent::get_mistake_message();
    }

    /**
     * Returns a key, uniquely identifying mistake
     */
    public function mistake_key() {
        return 'moved_'.$this->answermistaken[0].'_'.$this->responsemistaken[0];//'movedtoken_' is better, but too long for question_attempt_step_data name column (32)
    }

    public function supported_hints() {
        return array('whatis', 'wheretxt', 'wherepic');
    }
}

// A mistake, that consists from adding a lexeme to response, that is not in answer
class qtype_correctwriting_lexeme_added_mistake extends qtype_correctwriting_sequence_mistake {
   /**
    * Constructs a new error, filling it with constant message
    * @param object $language      a language object
    * @param array  $answer        answer tokens
    * @param array  $response      array response tokens
    * @param int    $responseindex index of response token
    */
    public function __construct($language,$answer,$response,$responseindex) {
        $this->languagename = $language->name();

        $this->answer = $answer->stream->tokens;
        $this->response = $response->stream->tokens;

        $this->position = $this->response[$responseindex]->position();
        //Fill answer data
        $this->answermistaken = array();
        //Fill response data
        $this->responsemistaken = array($responseindex);

        $this->answerstring = null;

        //Find, if such token exists in answer (to call it extraneous) or not (to write that it should not be there)
        $exists = false;
        foreach ($this->answer as $answertoken) {
            if ($answertoken->value() == $this->response[$responseindex]->value()) {
                $exists = true;
                break;
            }
        }

        //Create a mistake message
        $a = new stdClass();
        $data = $this->response[$responseindex]->value();
        if (!is_string($data)) {
            $data = $data->string();
        }
        $a->value = $data;
        $a->line = $this->position->linestart();
        $a->position = $this->position->colstart();
        if ($exists) {
            $this->mistakemsg = get_string('addedmistakemessage','qtype_correctwriting',$a);
        } else {
            $this->mistakemsg = get_string('addedmistakemessage_notexist','qtype_correctwriting',$a);
        }
    }

        public function mistake_key() {
        return 'added_'.$this->responsemistaken[0];//'addedtoken_' is better, but too long for question_attempt_step_data name column (32)
    }
}

// A mistake, that consists of  skipping a lexeme from answer
class qtype_correctwriting_lexeme_absent_mistake extends qtype_correctwriting_sequence_mistake {

    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     */
    public function __construct($language,$answer,$answerindex,$response) {
       $this->languagename = $language->name();

       $this->answer = $answer->stream->tokens;
       $this->response = $response->stream->tokens;

       $this->position = $this->answer[$answerindex]->position();
       //Fill answer data
       $this->answermistaken=array();
       $this->answermistaken[] = $answerindex;
       //Fill response data
       $this->responsemistaken = array();

       $this->answerstring = $answer;
       $this->mistakemsg = null;      
    }

    /** Performs a mistake message creation if needed
     */
    public function get_mistake_message() {
        if ($this->mistakemsg == null) {
            //Create a mistake message
            $a = new stdClass();
            $answerindex = $this->answermistaken[0];
            $a->description = $this->token_descriptions();
            if ($a->description !== null) {
                $this->mistakemsg = get_string('absentmistakemessage','qtype_correctwriting',$a);
            } else {
                $data = $this->answer[$answerindex]->value();
                if (!is_string($data)) {
                    $data = $data->string();
                }
                $a->value = $data;
                $this->mistakemsg = get_string('absentmistakemessagenodescription','qtype_correctwriting',$a);
            }
        }
        return parent::get_mistake_message();
    }

    public function mistake_key() {
        return 'absent_'.$this->answermistaken[0];//'absenttoken_' is better, but too long for question_attempt_step_data name column (32)
    }

    public function supported_hints() {
        return array('whatis', 'wheretxt', 'wherepic');
    }
}

?>