<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
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
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');

/**
 * Represents a preg question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_question extends question_graded_automatically
        implements question_automatically_gradable, qtype_poasquestion\question_with_hints {

    // Fields defining a question.
    /** @var array of question_answer objects. */
    public $answers = array();
    /** @var boolean whether answers should be graded case-sensitively. */
    public $usecase;
    /** @var string correct answer in user-readable form. */
    public $correctanswer;
    /** @var boolean should the match be exact or any match within answer is ok. */
    public $exactmatch;
    /** @var boolean availability of hints in behaviours with multiple attempts. */
    public $usecharhint;
    /** @var number penalty for a hint. */
    public $charhintpenalty;
    /** @var number only answers with fraction >= hintgradeborder would be used for hinting. */
    public $hintgradeborder;
    /** @var string matching engine to use. */
    public $engine;
    /** @var string notation, used to write answers. */
    public $notation;
    /** @var boolean availability of next lexem hints in behaviours with multiple attempts.*/
    public $uselexemhint;
    /** @var number penalty for a next lexem hint. */
    public $lexemhintpenalty;
    /** @var string id of the language, used to write answers (cf. blocks/formal_langs for more details). */
    public $langid;
    /** @var preferred name for a lexem by the teacher. */
    public $lexemusername;
    public $regextests = array();

    // Other fields.
    /** @var cache of matcher objects: key is answer id, value is matcher object. */
    protected $matchers_cache = array();
    /** @var cache of best fit answer: keys in array are 'answer' and 'match'. */
    protected $bestfitanswer = array();
    /** @var reponse for which best fit answer is calculated as a string */
    protected $responseforbestfit = '';

    public function __construct() {
        parent::__construct();
    }

    public static function question_from_regex($regex, $usecase, $exactmatch, $engine, $notation) {

        $question = new qtype_preg_question;
        $question->usecase = $usecase;
        $question->correctanswer = '';
        $question->exactmatch = $exactmatch;
        $querymatcher = $question->get_query_matcher($engine);
        $question->usecharhint = $querymatcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING);
        $question->charhintpenalty = 0;
        $question->hintgradeborder = 1;
        $question->engine = $engine;
        $question->notation = $notation;

        $answer = new stdClass();
        $answer->id = 100;
        $answer->answer = $regex;
        $answer->fraction = 1;
        $answer->feedback = '';

        $question->answers = array(100=>$answer);
        return $question;
    }

    public function get_expected_data() {
        /* Note: not using PARAM_RAW_TRIMMED because it'll interfere with next character hinting in most ungraceful way:
         disabling it by eating trailing spaces just when you try to get a first letter of the next word. */
        return array('answer' => PARAM_RAW);
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_have_same_keys_and_values($prevresponse, $newresponse);
    }

    /**
     * Calculates and fill $this->bestfitanswer if necessary.
     * @param $response Response to find best fit answer.
     * @param $gradeborder float Set this argument if you want to find answer with other border than defined in question, used to get correct answer (100% border).
     * @return array 'answer' => answer object, best fitting student's response, 'match' => matching results object @see{qtype_preg_matching_results}.
     */
    public function get_best_fit_answer(array $response, $gradeborder = null) {
        // Check cache for valid results.
        if ($response['answer']==$this->responseforbestfit && $this->bestfitanswer !== array() && $gradeborder === null) {
            return $this->bestfitanswer;
        }

        // Set $hintgradeborder.
        if ($gradeborder === null) {// No grade border set, use question one.
            $hintgradeborder = $this->hintgradeborder;
        } else {// We would still need to remember whether gradeborder === null for cache purposes, so use another variable.
            $hintgradeborder = $gradeborder;
        }

        $querymatcher = $this->get_query_matcher($this->engine);// This matcher will be used to query engine capabilities.
        $knowleftcharacters = $querymatcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT);
        $ispartialmatching = $querymatcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING);

        // Set an initial value for best fit. This is tricky, since when hinting we need first element within hint grade border.
        reset($this->answers);
        $bestfitanswer = current($this->answers);
        if ($ispartialmatching) {
            foreach ($this->answers as $answer) {
                if ($answer->fraction >= $hintgradeborder) {
                    $bestfitanswer = $answer;
                    $hintneeded = ($this->usecharhint || $this->uselexemhint);// We already know that $answer->fraction >= $hintgradeborder.
                    $matcher = $this->get_matcher($this->engine, $answer->answer, $this->exactmatch, $this->get_modifiers($this->usecase),
                                                    $answer->id, $this->notation, $hintneeded);
                    $bestmatchresult = $matcher->match($response['answer']);
                    if ($knowleftcharacters) {
                        $maxfitness = (-1)*$bestmatchresult->left;
                    } else {
                        $maxfitness = $bestmatchresult->length();
                    }
                    break;// Any one that fits border helps.
                }
            }
        } else {// Just use first answer and not bother with maxfitness. But we still should fill $bestmatchresults from matcher to correctly fill matching results arrays.
            $matcher = $this->get_matcher($this->engine, $bestfitanswer->answer, $this->exactmatch, $this->get_modifiers($this->usecase), $bestfitanswer->id, $this->notation);
            $bestmatchresult = $matcher->match($response['answer']);
        }

        // fitness = (the number of correct letters in response) or  (-1)*(the number of letters left to complete response) so we always look for maximum fitness.
        foreach ($this->answers as $answer) {
            $hintneeded = ($this->usecharhint || $this->uselexemhint) && $answer->fraction >= $hintgradeborder;
            $matcher = $this->get_matcher($this->engine, $answer->answer, $this->exactmatch, $this->get_modifiers($this->usecase), $answer->id, $this->notation, $hintneeded);
            $matchresults = $matcher->match($response['answer']);

            // Check full match.
            if ($matchresults->full) {// Don't need to look more if we find full match.
                $bestfitanswer = $answer;
                $bestmatchresult = $matchresults;
                $fitness = core_text::strlen($response['answer']);
                break;
            }

            // When hinting we should use only answers within hint border except full matching case and there is some match at all.
            // If engine doesn't support hinting we shoudn't bother with fitness too.
            if (!$ispartialmatching || !$matchresults->is_match() || $answer->fraction < $hintgradeborder) {
                continue;
            }

            // Calculate fitness.
            if ($knowleftcharacters) {// Engine could tell us how many characters left to complete response, this is the best fitness possible.
                $fitness = (-1)*$matchresults->left;// -1 cause the less we need to add the better.
            } else {// We should rely on the length of correct response part.
                $fitness = $matchresults->length[0];
            }

            if ($fitness > $maxfitness) {
                $maxfitness = $fitness;
                $bestfitanswer = $answer;
                $bestmatchresult = $matchresults;
            }
        }

        $bestfit = array();
        $bestfit['answer'] = $bestfitanswer;
        $bestfit['match'] = $bestmatchresult;
        // Save best fitted answer for further uses (default grade border only).
        if ($gradeborder === null) {
            $this->bestfitanswer = $bestfit;
            $this->responseforbestfit = $response['answer'];
        }
        return $bestfit;
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
        if ($bestfitanswer['match']->is_match() && $bestfitanswer['match']->full) {// TODO - implement partial grades for partially correct answers.
            $grade = $bestfitanswer['answer']->fraction;
            $state = question_state::graded_state_for_fraction($bestfitanswer['answer']->fraction);
        }

        return array($grade, $state);

    }

    /**
     * Return regular expression modifiers using given arguments and question settings.
     * @param $usecase bool case sensitive mode
     */
    public function get_modifiers($usecase = true) {
        $modifiers = 0;

        // Case (in)sensitivity modifier - used from question options.
        if (!$usecase) {
            $modifiers = $modifiers | qtype_preg_handling_options::MODIFIER_CASELESS;
        }

        return $modifiers;
    }

    /**
     * Create or get suitable matcher object for given engine, regex and options.
     * @param engine string engine name
     * @param regex string regular expression to match
     * @param $exact bool exact matching mode
     * @param $modifiers string modifiers for regular expression, use @link get_modifiers to get it
     * @param $answerid integer answer id for this regex, null for cases where id is unknown - no cache
     * @param $notation string notation, in which regex is written
     * @param $hintpossible boolean whether hint possible for specified answer
     * @return matcher object
     */
    public function &get_matcher($engine, $regex, $exact = false, $modifiers = 0, $answerid = null, $notation = 'native', $hintpossible = true) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'/'.$engine.'.php');

        if ($answerid !== null && array_key_exists($answerid, $this->matchers_cache)) {// Could use cache.
            $matcher = $this->matchers_cache[$answerid];
        } else {// Create and store matcher object.

            // Create and fill options object.
            $matchingoptions = new qtype_preg_matching_options();
            $matchingoptions->modifiers = $modifiers;

            // We need extension to hint next character or to generate correct answer if none is supplied.
            $matchingoptions->extensionneeded = $this->usecharhint || $this->uselexemhint || trim($this->correctanswer) == '';
            if ($answerid !== null && $answerid > 0) {
                $feedback = $this->answers[$answerid]->feedback;
                if (strpos($feedback, '{$') === false || strpos($feedback, '}') === false) {// No placeholders for subexpressions in feedback.
                    $matchingoptions->capturesubexpressions = false;
                }
            }

            $matchingoptions->notation = $notation;
            $matchingoptions->exactmatch = $exact;
            if(! is_null($CFG->qtype_preg_assertfailmode)) {
                $matchingoptions->mergeassertions = $CFG->qtype_preg_assertfailmode;
            }

            $engineclass = 'qtype_preg_'.$engine;
            $matcher = new $engineclass($regex, $matchingoptions);

            if ($matcher->errors_exist() && !$hintpossible && $engine != 'php_preg_matcher') {
                // There is one exception - regex that can not match due to empty FA.
                // PCRE does not look for this problem, FA matcher does.
                $errors = $matcher->get_errors();
                if (count($errors) > 1 || !is_a($errors[0], 'qtype_preg_empty_fa_error') || !is_a($errors[0], 'qtype_preg_backref_intersection_error')) {
                    // Custom engine can't handle regex and hints not needed, let's try preg_match instead.
                    $engine = 'php_preg_matcher';
                    require_once($CFG->dirroot . '/question/type/preg/'.$engine.'/'.$engine.'.php');
                    $engineclass = 'qtype_preg_'.$engine;
                    $newmatcher = new $engineclass($regex, $matchingoptions);
                    if (!$newmatcher->errors_exist()) {// We still prefer to show error messages from custom engine, since they are much more detailed.
                        $matcher = $newmatcher;
                    }
                }
            }

            if ($answerid !== null) {// Cache created matcher.
                $this->matchers_cache[$answerid] = $matcher;
            }
        }
        return $matcher;
    }

    /**
     * Creates and return empty matcher object, that could be used to query engine capabilities, needed notation etc.
     * Created to collect 'require_once' code with file paths to the engines from all over the question, to make changing it easier.
     */
    public function get_query_matcher($engine) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'/'.$engine.'.php');

        $engineclass = 'qtype_preg_'.$engine;
        return new $engineclass;
    }

    /**
     * Enchancing base class function with ability to generate correct response closest to student's one when given.
     */
    public function get_correct_response_ext($response) {
        $correctanswer = $this->correctanswer;
        if (trim($correctanswer) == '') {
            // No correct answer set be the teacher, so try to generate correct response.
            // TODO - should we default to generate even if teacher entered the correct answer?
            $bestfit = $this->get_best_fit_answer($response, 1);
            $matchresults = $bestfit['match'];
            if (is_object($matchresults->extendedmatch) && $matchresults->extendedmatch->full) {
                // Engine generated a full match.
                $correctanswer = $matchresults->correct_before_hint().$matchresults->string_extension();
            }
        }
        return array('answer' => $correctanswer);
    }

    /**
     * Overloading of base function to call our enchanced one.
     */
    public function get_correct_response() {
        return $this->get_correct_response_ext(array('answer' => ''));
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

    /**
     * Returns formatted feedback text to show to the user, or null if no feedback should be shown.
     */
    public function get_feedback_for_response($response, $qa) {

        $bestfit = $this->get_best_fit_answer($response);
        $feedback = '';
        // If best fit answer is found and there is a full match.
        // We should not show feedback for partial matches while question still active since student still don't get his answer correct.
        // But if the question is finished there is no harm in showing feedback for partial matching.
        $state = $qa->get_state();
        if (isset($bestfit['answer']) && ($bestfit['match']->full  || $bestfit['match']->is_match() && $state->is_finished()) ) {
            $answer = $bestfit['answer'];
            if ($answer->feedback) {
                $feedbacktext = $this->insert_subexpressions($answer->feedback, $response, $bestfit['match']);
                $feedback = $this->format_text($feedbacktext, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
            }
        }

        return $feedback;

    }

    /**
     * Insert subexpressions in the subject string instead of {$x} placeholders, where {$0} is the whole match, {$1}  - first subexpression etc.
     * @param subject string to insert subexpressions.
     * @param question question object to create matcher.
     * @param matchresults matching results object from best fitting answer.
     * @return changed string.
     */
    public function insert_subexpressions($subject, $response, $matchresults) {

        // Sanity check.
        if (core_text::strpos($subject, '{$') === false || core_text::strpos($subject, '}') === false) {
            // There are no placeholders for sure.
            return $subject;
        }

        $answer = $response['answer'];

        foreach ($matchresults->all_subexpressions() as $i) {
            $search = '{$'.$i.'}';
            $startindex = $matchresults->index_first($i);
            $length = $matchresults->length($i);
            if ($startindex != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $replace = core_text::substr($answer, $startindex, $length);
            } else {
                $replace = '';
            }
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
    }

    //          Specific hints implementation part.

    // We need adaptive or interactive behaviour to use hints.
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

        if ($preferredbehaviour == 'interactive' && file_exists($CFG->dirroot.'/question/behaviour/interactivehints/')) {
             question_engine::load_behaviour_class('interactivehints');
             return new qbehaviour_interactivehints($qa, $preferredbehaviour);
        }

        return parent::make_behaviour($qa, $preferredbehaviour);
    }

    /**
     * Returns an array of available specific hint types.
     */
    public function available_specific_hints($response = null) {
        $hinttypes = array();
        if (count($this->hints) > 0) {
            $hinttypes[] = 'hintmoodle#';
        }
        if ($this->usecharhint) {
            $hinttypes[] = 'hintnextchar';
        }
        if ($this->uselexemhint) {
            $hinttypes[] = 'hintnextlexem';
        }
        return $hinttypes;
    }

    public function hints_available_for_student($response = null) {
        // TODO - define behaviour when some hint used in interactive, but set to 'No' for adaptive.
        return $this->available_specific_hints($response);
    }

    /**
     * Hint object factory.
     *
     * Returns a hint object for given type.
     */
    public function hint_object($hintkey, $response = null) {
        // Moodle-specific hints.
        if (substr($hintkey, 0, 11) == 'hintmoodle#') {
            return new qtype_poasquestion\hintmoodle($this, $hintkey);
        }

        // Preg specific hints.
        $hintclass = 'qtype_preg_'.$hintkey;
        return new $hintclass($this, $hintkey);
    }

}
