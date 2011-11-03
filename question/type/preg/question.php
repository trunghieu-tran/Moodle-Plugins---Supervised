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
 * Short answer question definition class.
 *
 * @package    qtype
 * @subpackage preg
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Represents a preg question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_question extends question_graded_automatically_with_countback
        implements question_automatically_gradable_with_countback {

    //Fields defining a question
    /** @var array of question_answer objects. */
    public $answers = array();
    /** @var boolean whether answers should be graded case-sensitively. */
    public $usecase;
    /** @var correct answer in user-readable form. */
    public $correctanswer;
    /** @var should the match be exact or any match within answer is ok. */
    public $exactmatch;
    /** @var availability of hints in behavours with multiple attempts. */
    public $usehint;
    /** @var penalty for a hint. */
    public $hintpenalty;
    /** @var only answers with fraction >= hintgradeborder would be used for hinting. */
    public $hintgradeborder;
    /** @var matching engine to use. */
    public $engine;

    //Other fields
    /** @var cache of matcher objects: key is answer id, value is matcher object. */
    protected $matchers_cache = array();
    //Needed to pass hinted message to the question form, should be deleted when moving for renderers
    //TODO - check how to implement now
    protected $hintmessage = '';
    /** @var cache of best fit answer: keys in array are 'answer' and 'match'. */
    protected $bestfitanswer = array();
    /** @var reponse for which best fit answer is calculated as a string */
    protected $responseforbestfit = '';

    public function __construct() {
        parent::__construct();
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED, 'hint' => PARAM_BOOL);
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response) || array_key_exists('hint', $response);
    }

    /**
    * Hint button should work right after Submit without changing response
    * This may not be needed if the best fit answer would be saved in DB in reponses - TODO
    */
    public function is_same_response(array $prevresponse, array $newresponse) {//TODO - check if that now necessary, or there are new ways to deal with hint button
        return question_utils::arrays_have_same_keys_and_values($prevresponse, $newresponse);
    }

    /**
    * Calculates and fill $this->bestfitanswer if necessary.
    * @param $response response to find best fit answer
    * @return array 'answer' => answer object, that best fit student's response, 'match' => array of matching results @see{preg_matcher}
    */
    public function get_best_fit_answer(array $response) {
        global $CFG;
        //check cache for valid results
        if($response['answer']==$this->responseforbestfit && $this->bestfitanswer !== array()) {
            return $this->bestfitanswer;
        }

        require_once($CFG->dirroot . '/question/type/preg/'.$this->engine.'.php');
        $querymatcher = new $this->engine;//this matcher will be used to query engine capabilities
        $knowleftcharacters = $querymatcher->is_supporting(preg_matcher::CHARACTERS_LEFT);
        $ispartialmatching = $querymatcher->is_supporting(preg_matcher::PARTIAL_MATCHING);
        
        //Set an initial value for best fit. This is tricky, since when hinting we need first element within hint grade border
        reset($this->answers);
        $bestfitanswer = current($this->answers);
        if ($ispartialmatching) {
            foreach ($this->answers as $answer) {
                if ($answer->fraction >= $this->hintgradeborder) {
                    $bestfitanswer = $answer;
                    break;//anyone that fits border helps
                }
            }
        }
        //fitness = (the number of correct letters in response) or  (-1)*(the number of letters left to complete response) so we always look for maximum fitness
        $maxfitness = (-1)*(strlen($response['answer'])+1);
        $full = false;
        $matchresult = array();
        foreach ($this->answers as $answer) {
            $matcher =& $this->get_matcher($this->engine, $answer->answer, $this->exactmatch, $this->usecase, $answer->id);
            $full = $matcher->match($response['answer']);

            //check full match
            if ($full) {//don't need to look more if we find full match
                $bestfitanswer = $answer;
                $matchresult = $matcher->get_match_results();
                $fitness = strlen($response['answer']);
                break;
            }

            //when hinting we should use only answers within hint border except full matching case and there is some match at all
            //if engine doesn't support hinting we shoudn't bother with fitness too
            if (!$ispartialmatching || !$matcher->match_found() || $answer->fraction < $this->hintgradeborder) {
                continue;
            }

            //calculate fitness now
            if ($knowleftcharacters) {//engine could tell us how many characters left to complete response, this is the best fitness possible
                $fitness = (-1)*$matcher->characters_left();//-1 cause the less we need to add the better
            } else {//we should rely on the length of correct response part
                $fitness = $matcher->last_correct_character_index() - $matcher->first_correct_character_index() + 1;
            }

            if ($fitness > $maxfitness) {
                $maxfitness = $fitness;
                $bestfitanswer = $answer;
                $matchresult = $matcher->get_match_results();
            }
        }

        //save best fitted answer for further uses
        $this->bestfitanswer['answer'] = $bestfitanswer;
        $this->bestfitanswer['match'] = $matchresult;
        $this->responseforbestfit = $response['answer'];
        return $this->bestfitanswer;
    }

    public function get_matching_answer(array $response) {
        if ($this->get_best_fit_answer($response)['match']['full']) {
            return $this->bestfitanswer['answer'];
        }
        return array();
    }

    public function grade_response(array $response) {

        $bestfitanswer = $this->get_best_fit_answer($response);
        $grade = 0;
        $state = question_state::$gradedwrong;
        if ($bestfitanswer['match']['full']) {//TODO - implement partial grades for partially correct answers
            $grade = $bestfitanswer['answer']->fraction;
            $state = question_state::graded_state_for_fraction($fraction);
        }

        return array($grade, $state);

        /* Old code - TODO - delete when penalties would be calculated
        // Make sure we don't assign negative or too high marks.
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Update the penalty
        if (isset($state->responses['hint'])) {
            $state->penalty = $question->options->hintpenalty * $question->maxgrade;
        } else {
            $state->penalty = $question->penalty * $question->maxgrade;
        }

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;*/
    }

    /**
    * Create or get suitable matcher object for given engine, regex and options.
    @param engine string engine name
    @param regex string regular expression to match
    @param $exact bool exact macthing mode
    @param $usecase bool case sensitive mode
    @param $answerid integer answer id for this regex, null for cases where id is unknown - no cache
    @return matcher object
    */
    public function &get_matcher($engine, $regex, $exact = false, $usecase = true, $answerid = null) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'.php');

        if ($answerid !== null && array_key_exists($answerid,$this->matchers_cache)) {//could use cache
            $matcher =& $this->matchers_cache[$answerid];
        } else {//create and store matcher object
            $for_regexp=$regex;
            if ($exact) {
                //Grouping is needed in case regexp contains top-level alternatives
                //use non-capturing grouping to not mess-up with user subpattern capturing
                $for_regexp = '^(?:'.$for_regexp.')$';
            }
            $modifiers = null;
            if (!$usecase) {
                $modifiers = 'i';
            }
            $matcher = new $engine($for_regexp, $modifiers);
            if ($answerid !== null) {
                $this->matchers_cache[$answerid] =& $matcher;
            }
        }
        return $matcher;
    }

    public function get_correct_responses() {
        return array('answer'=>$this->correctanswer);
    }

    public function summarise_reponse(array $response) {
        if (isset($response['answer'])) {
            $resp = $response['answer'];
        } else {
            $resp = null;
        }
        return $resp;
    }
}
