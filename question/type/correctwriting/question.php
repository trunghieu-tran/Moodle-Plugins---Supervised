<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/correctwriting/lexical_analyzer.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');

/**
 * Represents a correctwriting question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_question extends qtype_shortanswer_question  {
    //Fields defining a question
    /** @var array of question_answer objects. */
    public $answers = array();
    //Typical answer objects usually contains answer (string), fraction and feedback fields
    //Our answer object should also contain elementnames array, with teacher-given sematic names 
    //for either important nodes (when syntax analysis is posssible) or all tokens (otherwise).
    //Whether question is casesensitivity
    public $casesensivity = true;
    //Threshold, defining maximum percent of token length mistake weight could be to provide a valid matched pair
    public $lexicalerrorthreshold = 0;
    //Weight of lexical error 
    public $lexicalerrorweight = 0.1;
    
    //Language id in the languages table
    public $langid = 0;
    
    
    //Other necessary question data like penalty for each type of mistakes etc

    
    // @var float weight of error, when one lexeme is moved from one place to another
    public $movedmistakeweight = 0.1;
    // @var float weight of error, when one lexeme in response is absent
    public $absentmistakeweight = 0.1;
    // @var float weight of error, when one lexeme is added to response
    public $addedmistakeweight = 0.1;
    
    
    // @var float minimum grade for non-exact match answer
    public $hintgradeborder = 0.9;
    // @var maximum mistake percent to length of answer in lexemes  for answer to be matched
    public $maxmistakepercentage = 0.7;
    
    // Some cached grade results in order to not recompute everything, because sometimes grade_response 
    // is being called two times
    public $gradecachevalid = false;
    public $matchedanswerid = null;
    public $matchedanalyzer = null;
    public $matchedgradestate = null;
    
    /** Checks, whether two responses are the same
        @param array prevresponse previous response
        @param array newresponse  new response
        @return bool new user response
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }
    
    /** Returns a validation error for response
        @param array response user response
        @return string validation error
      */
    public function get_validation_error(array $response) {        
        print_r($response);
        
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_correctwriting');
    }
    
    /**  Performs grading response, using lexical analyzer.
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function grade_response(array $response) {
        if ($this->gradecachevalid == true) {
            return $this->matchedgradestate;
        }
        
        // Make all symbols lowercase, when non case-sensitive settings
        if (!$this->usecase) {
            $response['answer'] = strtolower($response['answer']);
            foreach($this->answers as $id => $answer) {
                $answer->answer = strtolower($answer->answer);
            }
        }
        
        $this->gradecachevalid = true;
        $this->matchedanswerid = null;
        $maxfraction = 0.0;
        $language = block_formal_langs::lang_object($this->langid);
        // Scan every answer
        foreach($this->answers as $id => $answer) {
            $analyzer = new  qtype_correctwriting_lexical_analyzer($this, $answer, $response['answer']);
            $mistakes = $analyzer->mistakes();
            // Check whether answer allows non exact match
            $allowsnonexactmatch = $answer->fraction > $this->hintgradeborder;
            // Check, whether response has mistakes
            $hasmistakes = count($analyzer->mistakes()) != 0 ; 
            // Check whether mistakes more than percentage of lexemes
            $language = block_formal_langs::lang_object($this->langid);
            $answerstring = $language->create_from_db('question_answers', $answer->id, $answer->answer);
            $answertokencount = count($answerstring->stream->tokens);
            $fullyincorrect = count($analyzer->mistakes())  > $this->maxmistakepercentage * $answertokencount;
            // Check, whether we could use it
            if (($allowsnonexactmatch || !$hasmistakes) && !$fullyincorrect) {
                //Compute fraction
                $fraction = $this->compute_fraction($answer->fraction, $analyzer);
                // 0.000001 stands for precision control
                if ($fraction >= $maxfraction - 0.000001) {
                    $maxfraction = $fraction;
                    $this->matchedanswerid = $id;
                    $this->matchedanalyzer = $analyzer;
                }
            }
        }
        
        if ($this->matchedanswerid != null ) {
            $state = question_state::graded_state_for_fraction($maxfraction);
            $this->matchedgradestate = array($maxfraction, $state);
        } else {
            $this->matchedgradestate = array(0, question_state::$gradedwrong);
        }
        
        return $this->matchedgradestate;
    }
    /** Computes a fraction of student response, based on alayzer
        @param float  $fraction maximum fraction of student response
        @param object $analyzer lexical analyzer
     */
    public function compute_fraction($fraction, $analyzer) {
        $result = $fraction;
        foreach($analyzer->mistakes() as $mistake) {
            $result = $result - $mistake->weight;
        }
        return $result;
    }
    /**  Returns matching answer. Must return matching answer found when response was being graded.
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function get_matching_answer(array $response) {
        if ($this->gradecachevalid == false) {
            $this->matchedgradestate = $this->grade_response($response);
        }
        // Handle obstacle when no answer matched
        if ($this->matchedanswerid == null) {
            return null;
        }
        return $this->answers[$this->matchedanswerid];
    }
}
 ?>