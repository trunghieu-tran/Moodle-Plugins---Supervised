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
 * Perl-compatible regular expression question definition class.
 *
 * @package    qtype
 * @subpackage preg
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');

/**
 * Question which could return some specific hints and want to use *withhint behaviours should implement this
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_with_qtype_specific_hints {

    /**
     * Returns an array of available specific hint types depending on question settings
     *
     * The keys are hint type indentifiers, unique for the qtype
     * The values are interface strings with the hint description (without "hint" word!)
     */
    public function available_specific_hint_types();

    /**
     * Hint object factory
     *
     * Returns a hint object for given type
     */
    public function hint_object($hintkey);
}

/**
 * Base class for question-type specific hints
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_specific_hint {

    /** @var object Question object, created this hint*/
    protected $question;

    /**
     * Constructs hint object, remember question to use
     */
    public function __construct($question) {
        $this->question = $question;
    }

    /**
     * Is hint based on response or not?
     *
     * @return boolean true if response is used to calculate hint (and, possibly, penalty)
     */
    abstract public function hint_response_based();

    /** 
     * Returns whether question and response allows for the hint to be done
     */
    abstract public function hint_available($response = null);

    /** 
     * Returns specific hint value for given response
     */
    abstract public function specific_hint($response = null);

    /**
     * Returns whether response is used to calculate penalty (cost) for the hint
     */
    public function penalty_response_based() {
        return false;//Most hint have fixed penalty (cost)
    }

    /** 
     * Returns penalty (cost) for using specific hint of given hint type (possibly for given response)
     *
     * Even if response is used to calculate penalty, hint object should still return an approximation to show to the student if $response is null
     */
    abstract public function penalty_for_specific_hint($response = null);

}

/**
 * Represents a preg question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_question extends question_graded_automatically
        implements question_automatically_gradable, question_with_qtype_specific_hints {

    //Fields defining a question
    /** @var array of question_answer objects. */
    public $answers = array();
    /** @var boolean whether answers should be graded case-sensitively. */
    public $usecase;
    /** @var string correct answer in user-readable form. */
    public $correctanswer;
    /** @var boolean should the match be exact or any match within answer is ok. */
    public $exactmatch;
    /** @var boolean availability of hints in behavours with multiple attempts. */
    public $usehint;
    /** @var number penalty for a hint. */
    public $hintpenalty;
    /** @var number only answers with fraction >= hintgradeborder would be used for hinting. */
    public $hintgradeborder;
    /** @var string matching engine to use. */
    public $engine;
    /** @var string notation, used to write answers. */
    public $notation;

    //Other fields
    /** @var cache of matcher objects: key is answer id, value is matcher object. */
    protected $matchers_cache = array();
    /** @var cache of best fit answer: keys in array are 'answer' and 'match'. */
    protected $bestfitanswer = array();
    /** @var reponse for which best fit answer is calculated as a string */
    protected $responseforbestfit = '';

    public function __construct() {
        parent::__construct();
    }

    public function get_expected_data() {
        //Note: not using PARAM_RAW_TRIMMED cause it'll interfere with next character hinting is most ungraceful way: disabling it just when you try to get a first letter of the next word
        return array('answer' => PARAM_RAW);
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
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
    * @return array 'answer' => answer object, best fitting student's response, 'match' => matching results object @see{qtype_preg_matching_results}
    */
    public function get_best_fit_answer(array $response) {
        //Check cache for valid results
        if($response['answer']==$this->responseforbestfit && $this->bestfitanswer !== array()) {
            return $this->bestfitanswer;
        }

        $querymatcher = $this->get_query_matcher($this->engine);//this matcher will be used to query engine capabilities
        $knowleftcharacters = $querymatcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT);
        $ispartialmatching = $querymatcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING);
        
        //Set an initial value for best fit. This is tricky, since when hinting we need first element within hint grade border.
        reset($this->answers);
        $bestfitanswer = current($this->answers);
        $matchresult = new qtype_preg_matching_results();
        if ($ispartialmatching) {
            foreach ($this->answers as $answer) {
                if ($answer->fraction >= $this->hintgradeborder) {
                    $bestfitanswer = $answer;
                    $matcher =& $this->get_matcher($this->engine, $answer->answer, $this->exactmatch, $this->usecase, $answer->id, $this->notation);
                    $matcher->match($response['answer']);
                    $matchresult = $matcher->get_match_results();
                    if ($knowleftcharacters) {
                        $maxfitness = (-1)*$matcher->characters_left();
                    } else {
                        $maxfitness = $matcher->match_length();
                    }
                    break;//Any one that fits border helps
                }
            }
        }

        //fitness = (the number of correct letters in response) or  (-1)*(the number of letters left to complete response) so we always look for maximum fitness.
        $full = false;
        foreach ($this->answers as $answer) {
            $matcher =& $this->get_matcher($this->engine, $answer->answer, $this->exactmatch, $this->usecase, $answer->id, $this->notation);
            $full = $matcher->match($response['answer']);

            //Check full match.
            if ($full) {//Don't need to look more if we find full match.
                $bestfitanswer = $answer;
                $matchresult = $matcher->get_match_results();
                $fitness = strlen($response['answer']);
                break;
            }

            //When hinting we should use only answers within hint border except full matching case and there is some match at all.
            //If engine doesn't support hinting we shoudn't bother with fitness too.
            if (!$ispartialmatching || !$matcher->match_found() || $answer->fraction < $this->hintgradeborder) {
                continue;
            }

            //Calculate fitness.
            if ($knowleftcharacters) {//Engine could tell us how many characters left to complete response, this is the best fitness possible.
                $fitness = (-1)*$matcher->characters_left();//-1 cause the less we need to add the better
            } else {//We should rely on the length of correct response part.
                $fitness = $matcher->match_length();
            }

            if ($fitness > $maxfitness) {
                $maxfitness = $fitness;
                $bestfitanswer = $answer;
                $matchresult = $matcher->get_match_results();
            }
        }

        //Save best fitted answer for further uses.
        $this->bestfitanswer['answer'] = $bestfitanswer;
        $this->bestfitanswer['match'] = $matchresult;
        $this->responseforbestfit = $response['answer'];
        return $this->bestfitanswer;
    }

    public function get_matching_answer(array $response) {
        $bestfit = $this->get_best_fit_answer($response);
        if ($bestfit['match']->full) {
            return $bestfit['answer'];
        }
        return array();
    }

    public function grade_response(array $response) {

        $bestfitanswer = $this->get_best_fit_answer($response);
        $grade = 0;
        $state = question_state::$gradedwrong;
        if ($bestfitanswer['match']->is_match() && $bestfitanswer['match']->full) {//TODO - implement partial grades for partially correct answers
            $grade = $bestfitanswer['answer']->fraction;
            $state = question_state::graded_state_for_fraction($bestfitanswer['answer']->fraction);
        }

        return array($grade, $state);

    }

    /**
    * Create or get suitable matcher object for given engine, regex and options.
    @param engine string engine name
    @param regex string regular expression to match
    @param $exact bool exact macthing mode
    @param $usecase bool case sensitive mode
    @param $answerid integer answer id for this regex, null for cases where id is unknown - no cache
    @param $notation notation, in which regex is written
    @return matcher object
    */
    public function &get_matcher($engine, $regex, $exact = false, $usecase = true, $answerid = null, $notation = 'native') {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'/'.$engine.'.php');

        if ($answerid !== null && array_key_exists($answerid,$this->matchers_cache)) {//could use cache
            $matcher =& $this->matchers_cache[$answerid];
        } else {//create and store matcher object

            $modifiers = null;
            if (!$usecase) {
                $modifiers = 'i';
            }

            //Convert to actually used notation if necessary
            $engineclass = 'qtype_preg_'.$engine;
            $queryengine = new $engineclass;
            $usednotation = $queryengine->used_notation();
            if ($notation !== null && $notation != $usednotation) {//Conversion is necessary
                $notationclass = 'qtype_preg_notation_'.$notation;
                $notationobj = new $notationclass($regex, $modifiers);
                $regex = $notationobj->convert_regex($usednotation);
                $modifiers = $notationobj->convert_modifiers($usednotation);
            }

            //Modify regex according with question properties
            $for_regexp=$regex;
            if ($exact) {
                //Grouping is needed in case regexp contains top-level alternatives
                //use non-capturing grouping to not mess-up with user subpattern capturing
                $for_regexp = '^(?:'.$for_regexp.')$';
            }

            $matcher = new $engineclass($for_regexp, $modifiers);
            if ($answerid !== null) {
                $this->matchers_cache[$answerid] =& $matcher;
            }
        }
        return $matcher;
    }

    /**
     * Creates and return empty matcher object, that could be used to query engine capabilities, needed notation etc
     * Created to collect 'require_once' code with file paths to the engines from all over the question, to make changing it easier
     */
    public function get_query_matcher($engine) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'/'.$engine.'.php');

        $engineclass = 'qtype_preg_'.$engine;
        return new $engineclass;
    }

    public function get_correct_response() {
        return array('answer' => $this->correctanswer);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            $resp = $response['answer'];
        } else {
            $resp = null;
        }
        return $resp;
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_shortanswer');
    }

    /*
    * Returns colored string parts: array with indexes 'wronghead', 'correctpart', 'hintedending', 'wrongtail', 'hintedendingstart', 'hintedendingcomplete'
    */
    public function response_correctness_parts($response) {
        $bestfit = $this->get_best_fit_answer($response);
        $answer = $bestfit['answer'];
        $matchresults = $bestfit['match'];
        $currentanswer = $response['answer'];

        if ($matchresults->is_match()) {
            $firstindex = $matchresults->index_first[0];
            $length = $matchresults->length[0];

            //For pure-assert expression full matching no colored string should be shown
            //It is usually a case when match means there is NO something in the student's answer
            if ($length == 0) {
                return null;//TODO - add unit-test when engines will be restored
            }

            $wronghead = '';
            if ($firstindex > 0) {//if there is wrong heading
                $wronghead = substr($currentanswer, 0, $firstindex);
            }
            $correctpart = '';
            if ($firstindex != qtype_preg_matching_results::NO_MATCH_FOUND) {//there were any matched characters
                $correctpart = substr($currentanswer, $firstindex, $length);
            }
            $hintedending = '';
            if ($matchresults->correctending !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) {//if hint possible
                $hintedending = $matchresults->correctending;
            }
            $wrongtail = '';
            if ($firstindex + $length < strlen($currentanswer)) {//if there is wrong tail
                $wrongtail =  substr($currentanswer, $firstindex + $length, strlen($currentanswer) - $firstindex - $length);
            }
            return array('wronghead' => $wronghead, 'correctpart' => $correctpart, 'hintedending' => $hintedending, 'wrongtail' => $wrongtail, 
                            'hintedendingstart' => $matchresults->correctendingstart, 'hintedendingcomplete' => $matchresults->correctendingcomplete);
        }

        //No match - all response is wrong, but we could hint the very first character still
        $queryengine = $this->get_query_matcher($this->engine);
        if ($queryengine->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
            $result = array('wronghead' => $currentanswer, 'correctpart' => '', 'hintedending' => '', 'wrongtail' => '');
            if ($matchresults->correctending !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) {//if hint possible
                $result['hintedending'] = $matchresults->correctending;
                $result['hintedendingstart'] = $matchresults->correctendingstart;
                $result['hintedendingcomplete'] = $matchresults->correctendingcomplete;
            }
        } else {//If there is no partial matching hide colored string when no match to not mislead the student who start his answer correctly
            $result = null;
        }
        return $result;
    }

    /**
     * Returns formatted feedback text to show to the user, or null if no feedback should be shown
     */
    public function get_feedback_for_response($response, $qa) {
    
        $bestfit = $this->get_best_fit_answer($response);
        $feedback = '';
        //If best fit answer is found and there is a full match
        //We should not show feedback for partial matches while question still active since student still don't get his answer correct
        //But if the question is finished there is no harm in showing feedback for partial matching
        $state = $qa->get_state();
        if (isset($bestfit['answer']) && ($bestfit['match']->full  || $bestfit['match']->is_match() && $state->is_finished()) ) {
            $answer = $bestfit['answer'];
            if ($answer->feedback) {
                $feedbacktext = $this->insert_subpatterns($answer->feedback, $response, $bestfit['match']);
                $feedback = $this->format_text($feedbacktext, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
            }
        }

        return $feedback;

    }

    /**
    * Insert subpatterns in the subject string instead of {$x} placeholders, where {$0} is the whole match, {$1}  - first subpattern ets
    @param subject string to insert subpatterns
    @param question question object to create matcher
    @param matchresults matching results object from best fitting answer
    @return changed string
    */
    public function insert_subpatterns($subject, $response, $matchresults) {

        //Sanity check 
        if (strpos($subject,'{$') === false || strpos($subject,'}') === false) {
            //There are no placeholders for sure 
            return $subject;
        }

        $answer = $response['answer'];

        //TODO - fix bug 72 leading to not replaced placeholder when using php_preg_matcher and last subpatterns isn't captured
        // c.f. failed test in simpletest/testquestion.php

        if ($matchresults->is_match()) {
            foreach ($matchresults->index_first as $i => $startindex) {
                $search = '{$'.$i.'}';
                $length = $matchresults->length[$i];
                if ($startindex != qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $replace = substr($answer, $startindex, $length);
                } else {
                    $replace = '';
                }
                $subject = str_replace($search, $replace, $subject);
            }
        } else {
            //No match, so no feedback should be shown.
            //It is possible to have best fit answer with no match to hint first character from first answer for which hint is possible.
            $subject = '';
        }

        return $subject;
    }

    //////////Specific hints implementation part

    //We need adaptive (TODO interactive) behaviour to use hints
     public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        global $CFG;

        if ($preferredbehaviour == 'adaptive' && file_exists($CFG->dirroot.'/question/behaviour/adaptivehints/')) {
             question_engine::load_behaviour_class('adaptivehints');
             return new qbehaviour_adaptivehints($qa, $preferredbehaviour);
        }

        if ($preferredbehaviour == 'adaptivenopenalty' && file_exists($CFG->dirroot.'/question/behaviour/adaptivehintsnopenalties/')) {
             question_engine::load_behaviour_class('adaptivehintsnopenalties');
             return new qbehaviour_adaptivehintsnopenalties($qa, $preferredbehaviour);
        }

        return parent::make_behaviour($qa, $preferredbehaviour);
     }
    /**
    * Returns an array of available specific hint types
    */
    public function available_specific_hint_types() {
        $hinttypes = array();
        if ($this->usehint) {
            $hinttypes['hintnextchar'] = get_string('hintnextchar','qtype_preg');
        }
        return $hinttypes;
    }

    /**
     * Hint object factory
     *
     * Returns a hint object for given type
     */
    public function hint_object($hintkey) {
        $hintclass = 'qtype_preg_'.$hintkey;
        return new $hintclass($this);
    }

}
