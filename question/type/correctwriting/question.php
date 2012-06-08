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

/**
 * Represents a correctwriting question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_question extends qtype_shortanswer_question /*extends question_graded_automatically
        implements question_automatically_gradable*/ {
        //TODO - commented out temporarily to use class as passive container for unit-testing - uncomment when real question class would be implemented

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
        
        return array(1.0, question_state::graded_state_for_fraction(1.0));
        
        $answer = $this->get_best_fit_answer($response['answer']);
        if ($answer) {
            return array($answer->fraction,
                    question_state::graded_state_for_fraction($answer->fraction));
        } else {
            return array(0, question_state::$gradedwrong);
        }
    }
    
    /**  Returns matching answer. Must return matching answer found when response was being graded.
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function get_matching_answer(array $response) {
        $keys = array_keys($this->answers);
        return $this->answers[$keys[0]];
    }
}
 ?>