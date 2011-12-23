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

/**
 * question which could return some specific hints and want to use *withhint behaviours should implement this
 */
interface question_with_specific_hints {

    /**
     * returns an array of available specific hint types depending on question settings
     * the keys are hint type indentifiers, unique for the qtype
     * the values are interface strings with the hint description (without "hint" word!)
     */
    public function available_specific_hint_types();

    /** 
     * returns whether response allows for the hint to be done
     */
    public function hint_available($hinttype, $response);

    /** 
     * returns specific hint value of given hint type for given response
     */
    public function specific_hint($hinttype, $response);

    /** 
     * returns penalty for using specific hint of given hint type (possibly for given response)
     */
    public function penalty_for_specific_hint($hinttype, $response);
}


/**
 * Represents a preg question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_question extends question_graded_automatically
        implements question_automatically_gradable, question_with_specific_hints {

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
    /** @var notation, used to write answers. */
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
    * @return array 'answer' => answer object, that best fit student's response, 'match' => array of matching results @see{preg_matcher}
    */
    public function get_best_fit_answer(array $response) {
        //check cache for valid results
        if($response['answer']==$this->responseforbestfit && $this->bestfitanswer !== array()) {
            return $this->bestfitanswer;
        }

        $querymatcher = $this->get_query_matcher($this->engine);//this matcher will be used to query engine capabilities
        $knowleftcharacters = $querymatcher->is_supporting(preg_matcher::CHARACTERS_LEFT);
        $ispartialmatching = $querymatcher->is_supporting(preg_matcher::PARTIAL_MATCHING);
        
        //Set an initial value for best fit. This is tricky, since when hinting we need first element within hint grade border.
        reset($this->answers);
        $bestfitanswer = current($this->answers);
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
                        $maxfitness = $matcher->last_correct_character_index() - $matcher->first_correct_character_index() + 1;
                    }
                    break;//anyone that fits border helps
                }
            }
        } else {
            $matchresult = array('is_match' => false, 'full' => false);
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
                $fitness = $matcher->last_correct_character_index() - $matcher->first_correct_character_index() + 1;
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
        if ($bestfit['match']['is_match'] && $bestfit['match']['full']) {
            return $bestfit['answer'];
        }
        return array();
    }

    public function grade_response(array $response) {

        $bestfitanswer = $this->get_best_fit_answer($response);
        $grade = 0;
        $state = question_state::$gradedwrong;
        if ($bestfitanswer['match']['is_match'] && $bestfitanswer['match']['full']) {//TODO - implement partial grades for partially correct answers
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
            $queryengine = new $engine;
            $usednotation = $queryengine->used_notation();
            if ($notation !== null && $notation != $usednotation) {//Conversion is necessary
                $notationclass = 'preg_notation_'.$notation;
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

            $matcher = new $engine($for_regexp, $modifiers);
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

        return new $engine;
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
    * Returns colored string parts: array with indexes 'wronghead', 'correctpart', 'hintedcharacter', 'wrongtail'
    */
    public function response_correctness_parts($response) {
        $bestfit = $this->get_best_fit_answer($response);
        $answer = $bestfit['answer'];
        $matchresults = $bestfit['match'];
        $currentanswer = $response['answer'];

        if ($matchresults['is_match']) {
            $firstindex = $matchresults['index_first'][0];
            $lastindex = $matchresults['index_last'][0];

            $wronghead = '';
            if ($firstindex > 0) {//if there is wrong heading
                $wronghead = substr($currentanswer, 0, $firstindex);
            }
            $correctpart = '';
            if ($firstindex != -1) {//there were any match
                $correctpart = substr($currentanswer, $firstindex, $lastindex - $firstindex + 1);
            }
            $hintedcharacter = '';
            if (isset($matchresults['next'])) {//if hint possible
                $hintedcharacter = $matchresults['next'];
            }
            $wrongtail = '';
            if ($lastindex + 1 < strlen($currentanswer)) {//if there is wrong tail
                $wrongtail =  substr($currentanswer, $lastindex + 1, strlen($currentanswer) - $lastindex - 1);
            }
            return array('wronghead' => $wronghead, 'correctpart' => $correctpart, 'hintedcharacter' => $hintedcharacter, 'wrongtail' => $wrongtail);
        }

        //No match - all response is wrong, but we could hint the very first character still
        $queryengine = $this->get_query_matcher($this->engine);
        if ($queryengine->is_supporting(preg_matcher::PARTIAL_MATCHING)) {
            $result = array('wronghead' => $currentanswer, 'correctpart' => '', 'hintedcharacter' => '', 'wrongtail' => '');
            if (isset($matchresults['next'])) {//if hint possible
                $result['hintedcharacter'] = $matchresults['next'];
            }
        } else {//If there is no partial matching hide colored string when no match to not mislead the student who start his answer correctly
            $result = null;
        }
        return $result;
    }

    /*
    * Insert subpatterns in the subject string instead of {$x} placeholders, where {$0} is the whole match, {$1}  - first subpattern ets
    @param subject string to insert subpatterns
    @param question question object to create matcher
    @param state state of the question attempt to get response
    @return changed string
    */
    public function insert_subpatterns($subject, $response) {

        //To be sure best fit answer is calculated
        $this->get_best_fit_answer($response);

        //Sanity check 
        if (strpos($subject,'{$') === false || strpos($subject,'}') === false) {
            //There are no placeholders for sure 
            return $subject;
        }

        $answer = $response['answer'];
        $matchresults = $this->bestfitanswer['match'];
        //TODO - fix bug 72 leading to not replaced placeholder when using php_preg_matcher and last subpatterns isn't captured
        // c.f. failed test in simpletest/testquestion.php

        if ($matchresults['is_match']) {
            foreach ($matchresults['index_first'] as $i => $startindex) {
                $search = '{$'.$i.'}';
                $endindex = $matchresults['index_last'][$i];
                $replace = substr($answer, $startindex, $endindex - $startindex + 1);
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

    //we need adaptive (TODO interactive) behavour to use hints
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
    * returns an array of available specific hint types
    */
    public function available_specific_hint_types() {
        if ($this->usehint) {
        return array('hintnextchar' => get_string('hintnextchar','qtype_preg')
                    );
        }

        return array();
    }

    /** 
     * returns whether response allows for the hint to be done
     */
    public function hint_available($hinttype, $response) {
        switch($hinttype) {
            case 'hintnextchar':
            return true;// next character hint available anywhere - TODO check where answer is correct or no next character could be generated
        }
    }

        /** 
     * returns specific hint value of given hint type for given response
     */
    public function specific_hint($hinttype, $response) {
        switch($hinttype) {
            case 'hintnextchar':
                $bestfitanswer = $this->get_best_fit_answer($response);
                return $bestfitanswer['match']['next'];
        }
    }

    /** 
     * returns penalty for using specific hint of given hint type (possibly for given response)
     * $response could be null if we need to show possible penalty in advance
     */
    public function penalty_for_specific_hint($hinttype, $response) {
        switch($hinttype) {
            case 'hintnextchar':
                return $this->hintpenalty;
        }
        return 0;
    }
}
