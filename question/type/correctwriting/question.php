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

/**
 * Represents a correctwriting question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_question /*extends question_graded_automatically
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

    
    // @var int weight of error, when one lexeme is moved from one place to another
    public $movedmistakeweight = 0.1;
    // @var int weight of error, when one lexeme in response is absent
    public $absentmistakeweight = 0.1;
     // @var int weight of error, when one lexeme is added to response
    public $addedmistakeweight = 0.1;
    
    /**  Returns expected data from forms for question
         @return array expected
     */
    public function get_expected_data() {
        return array('response' => PARAM_RAW);
    }
    /**  Checks, whether user response is complete
         @param  array  user response
         @return bool whether response is complete
     */
    public function is_complete_response(array $response) {
        return array_key_exists('response', $response) &&
                ($response['response'] || $response['response'] === '0');
    }

    /** Checks, whether two responses are the same
        @param array prevresponse previous response
        @param array newresponse  new response
        @return bool new user response
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'response');
    }
    /** Returns a validation error for response
        @param array response user response
        @return string validation error
      */
    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_correctwriting');
    }
    
}
 ?>