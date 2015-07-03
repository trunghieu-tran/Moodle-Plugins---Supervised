<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

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
require_once($CFG->dirroot . '/question/type/correctwriting/cw_hints.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
require_once($CFG->dirroot . '/question/type/correctwriting/string_pair.php');

/**
 * Represents a correctwriting question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_question extends question_graded_automatically
        implements question_automatically_gradable, qtype_poasquestion\question_with_hints {
    //Fields defining a question
    /** Whether answers should be graded case-sensitively.
     *  @var boolean
     */
    public $usecase;
    /** Array of question_answer objects, presenting answers
     *  @var array
     */
    public $answers = array();
    //Typical answer objects usually contains answer (string), fraction and feedback fields
    //Our answer object should also contain elementnames array, with teacher-given sematic names
    //for either important nodes (when syntax analysis is posssible) or all tokens (otherwise).
    /** Threshold, defining maximum percent of token length mistake weight could be to provide a valid matched pair
     *  @var int
     */
    public $lexicalerrorthreshold = 0;
    /** Weight of lexical error
     *  @var float
     */
    public $lexicalerrorweight = 0.1;

    /** Language id in the languages table
     *  @var int
     */
    public $langid = 0;
    /** Language object, used in language
     *  @var block_formal_langs_abstract_language
     */
    public $usedlanguage = null;
    //Other necessary question data like penalty for each type of mistakes etc


    /** Weight of error, when one lexeme is moved from one place to another
     *  @var float
     */
    public $movedmistakeweight = 0.1;
    /** Weight of error, when one lexeme in response is absent
     *  @var float
     */
    public $absentmistakeweight = 0.1;
    /** Weight of error, when one lexeme is added to response
     *  @var float
     */
    public $addedmistakeweight = 0.1;


    /** Minimum grade for non-exact match answer
     *  @var float
     */
    public $hintgradeborder = 0.9;
    /** Maximum mistake percent to length of answer in lexemes  for answer to be matched
     *  @var float
     */
    public $maxmistakepercentage = 0.7;


    /** Penalty for "what is" hint. Penalties more than 1 will disable hint.
     *  @var float
     */
    public $whatishintpenalty = 1.1;

    /** Penalty for "where" text hint. Penalties more than 1 will disable hint.
     *  @var float
     */
    public $wheretxthintpenalty = 1.1;

    /** Penalty factor for hints, diclosing token value, for absent token mistake.
     *  @var float
     */
    public $absenthintpenaltyfactor = 1;

    /**
     * Whether lexical analyzer is enabled
     * @var int
     */
    public $islexicalanalyzerenabled = 1;

    /**
     * Whether enum analyzer is enabled
     * @var int
     */
    public $isenumanalyzerenabled = 0;

    /**
     * Whether sequence analyzer is enabled
     * @var int
     */
    public $issequenceanalyzerenabled = 1;

    /**
     * Whether syntax analyzer is enabled
     * @var int
     */
    public $issyntaxanalyzerenabled = 0;


    /** Whether cache is valid
     *  @var boolean
     */
    public $gradecachevalid = false;
    /** A cached response
     *  @var string
     */
    public $gradecachedanswer = '';
    /** A cached matched answer id
     *  @var int
     */
    public $matchedanswerid = null;
    /** A cached matched analyzer
     *  @var qtype_correctwriting_string_pair
     */
    public $matchedresults = null;
    /** A cached resulting graded state
     *  @var array
     */
    public $matchedgradestate = null;

    // Returns expected data from form
    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    /** Checks, whether two responses are the same
     *  @param array $prevresponse previous response
     *  @param array $newresponse  new response
     *  @return bool new user response
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }


    public function get_answers() {
        return $this->answers;
    }

    /** Returns a validation error for response
        @param array response user response
        @return string validation error
      */
    public function get_validation_error(array $response) {
        //print_r($response);

        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_correctwriting');
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    /** Returns used language in question
        @return block_formal_langs_abstract_language used language
     */
    public function get_used_language() {
        if ($this->usedlanguage == null) {
            $this->usedlanguage = block_formal_langs::lang_object($this->langid);
        }
        return $this->usedlanguage;
    }



    /**  Performs grading response, using lexical analyzer.
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function grade_response(array $response) {
        $this->get_best_fit_answer($response);
        return $this->matchedgradestate;
    }

    /**  Returns a best fit answer, for specified response and saves results into a cache
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function get_best_fit_answer(array $response) {

        if (!array_key_exists('answer', $response)) {
            $response = array('answer' => '');
        }
        // Check, for cache, and make it lowercase to prevent some odd executions
        if (is_a($response['answer'],'qtype_poasquestion\string') == false) {
            $response['answer'] = new qtype_poasquestion\string($response['answer']);
        }

        if (($this->gradecachevalid == true) && ($this->gradecachedanswer == $response['answer'])) {
            return $this->matchedgradestate;
        }

        foreach($this->answers as $id => $answer) {
            if (is_a($answer->answer,'qtype_poasquestion\string') == false) {
                $answer->answer = new qtype_poasquestion\string($answer->answer);
            }
        }


        $this->gradecachevalid = true;
        $this->gradecachedanswer = $response['answer'];

        $matched = $this->check_match_answers($response['answer'], $this->answers);
        if ($matched == false) {
            $this->grade_as_wrong_response_to_max_fraction($response['answer']);
        }
        return $this->answers[$this->matchedanswerid];
    }

    /** Computes a fraction of student response, based on alayzer
     *  @param float  $fraction maximum fraction of student response
     *  @param object $analyzer lexical analyzer
     *  @return float fraction
     */
    public function compute_fraction($fraction, $analyzer) {
        $result = $fraction;
        foreach($analyzer->mistakes() as $mistake) {
            $result = $result - $mistake->weight;
        }
        return $result;
    }

    /**
     * Performs exact matching  for answer
     * @param qtype_correctwriting_string_pair $results
     * @param block_formal_langs $stream answer stream data
     * @return bool whether it matches
     */
    public function matches_exact($results,  $stream) {
        return count($results->mistakes()) == 0;
    }

    /**
     * Performs exact matching  for answer
     * @param qtype_correctwriting_string_pair $results
     * @param block_formal_langs_token_stream $stream stream data
     * @return bool whether it matches
     */
    public function matches_non_exact($results, $stream) {
        $answertokencount = count($stream->tokens);
        $partiallycorrect = (count($results->mistakes())  <= ($this->maxmistakepercentage * $answertokencount));
        return $partiallycorrect;
    }

    /**
     * Computes fraction for exact match. Used as callback in check match
     * @param stdClass $answer answer type
     * @param qtype_correctwriting_string_pair $results results for analysis for two answers
     * @return float resulting fraction
     */
    public function compute_exact_match_fraction($answer, $results) {
        return $answer->fraction;
    }

    /**
     * Computes fraction for non-exact match. Used as callback in check match
     * @param stdClass $answer answer type
     * @param qtype_correctwriting_string_pair $results results for analysis for two answers
     * @return float resulting fraction
     */
    public function compute_nonexact_match_fraction($answer, $results) {
        return $this->compute_fraction($answer->fraction, $results);
    }

    /** Checks, whether student answer matches non-exact match answer and if matches, grades it
      *  @param string $response student response
      *  @param array  $answers  array of exact match answers
      *  @return bool  whether it was matched
      */

      public function check_match_answers($response, $answers) {
        // Don't scan if no need for this
        if (count($answers) == 0) {
            return false;
        }
        // Scan answers
        $matched = false;
        $matchedid = null;
        $matchedresults = null;
        $foundexactmatch = false;
        $fraction = -1;
        // Get language
        $language = $this->get_used_language();
        // Scan answers for match
        foreach($answers as $id => $answer) {
            $results = $this->compare($answer, $response);
            //Get lexeme count from answer
            $answerstring = $language->create_from_string($answer->answer);
            $answerstream= $answerstring->stream;

            $nonexact = ($answer->fraction >= $this->hintgradeborder);
            // Exact match methods
            $checkmethod = 'matches_exact';
            $fractionmethod = 'compute_exact_match_fraction';
            // Change if non-exact
            if ($nonexact) {
                $checkmethod = 'matches_non_exact';
                $fractionmethod = 'compute_nonexact_match_fraction';
            }
            // Check, whether answer is partially correct
            $currentmatched = $this->$checkmethod($results, $answerstream);
            if (($currentmatched == true) && (!$nonexact || !$foundexactmatch)) {
                $answerfraction = $this->$fractionmethod($answer, $results);
                $firstexact =  (!$foundexactmatch && !$nonexact);
                if (($fraction <= $answerfraction) || ($matched == false) || $firstexact) {
                    $fraction = $answerfraction;
                    $matchedresults = $results;
                    $matchedid = $id;

                    if (!$nonexact) {
                        $foundexactmatch = true;
                    }
                }
                $matched = true;
            }
        }

        // Normalize fraction
        if (($fraction < 0) && ($matched == true)) {
            $fraction = 0;
        }
        if (($fraction > 1) && ($matched == true)) {
            $fraction = 1;
        }

        if ($matched) {
            // Copy matched data
            $this->matchedanswerid = $matchedid;
            $this->matchedresults = $matchedresults;
            $state = question_state::graded_state_for_fraction($fraction);
            $this->matchedgradestate = array($fraction, $state);
        }

        return $matched;
    }

    /** Grades  as wrong answer to an answer of max fraction
        @param string $response student response
      */
    public function grade_as_wrong_response_to_max_fraction($response) {
        $fid = null;
        $prec = 0.0001;
        foreach($this->answers as $id => $answer) {
            if (abs($answer->fraction - 1) < $prec ) {
                $fid = $id;
                break;
            }
        }

        $this->matchedanswerid = $fid;
        $answer = $this->answers[$fid];
        $this->matchedresults = $this->compare($answer, $response);
        $this->matchedgradestate = array(0, question_state::$gradedwrong);
    }

    protected function compare($answer, $response) {
        $language = $this->get_used_language();
        $responsestring = $language->create_from_string($response);
        $answerstring = $language->create_from_db('question_answers', $answer->id, $answer->answer);
        $string = new qtype_correctwriting_string_pair($answerstring, $responsestring, null);
        if ($this->are_lexeme_sequences_equal($string)) {
            $string->assert_that_strings_are_equal();
        } else {
            $string = $this->perform_analysis_with_analyzer(0, $string);
        }
        return $string;
    }

    /**
     * Performs recursive depth first scan, working with analyzer tree and trying to hold this tree
     * as small as possible. This function should build a result set, which can be saved into results field
     * @throws moodle_exception No mistake sets! - if analyzer, which is implemented is not valid and does not have
     * at least one mistake sets
     * @param int $index index of analyzer in qtype_correctwriting::analyzers
     * @param qtype_correctwriting_string_pair $string string pair
     * @return qtype_correctwriting_string_pair a pair of string
     */
    protected function perform_analysis_with_analyzer($index, $string) {
        /** @var qtype_correctwriting $qtype */
        $qtype = $this->qtype;
        $analyzers = array_values($qtype->analyzers());
        $analyzername = $analyzers[$index];
        $createdanalyzername = 'qtype_correctwriting_' . $analyzername;
        $bypass  = $this->is_analyzer_enabled($analyzername) == false;
        $string->analyzersequence[] = $createdanalyzername;
        /** @var qtype_correctwriting_abstract_analyzer $analyzer */
        $analyzer = new $createdanalyzername($this, $string, $this->get_used_language(), $bypass);

        if (count($analyzer->result_pairs()) == 0)
            throw new moodle_exception('No pairs!');


        //  Scan pair with max fitness
        $foundmaxfitness = false;
        $maxfitness = -1;
        $maxpair = null;

        // If this is last analyzer, pick string with largest fitness
        if ($index == count($analyzers) - 1) {
            foreach($analyzer->result_pairs() as $index => $pair) {
                /** @var qtype_correctwriting_string_pair $pair */
                $fitness = $analyzer->fitness($pair->mistakes());
                if ($foundmaxfitness == false || $fitness > $maxfitness) {
                    $maxfitness = $fitness;
                    $maxpair = $pair;
                }
            }
        } else {
            $childanalyzername = $analyzers[$index + 1];
            $createdchildanalyzername = 'qtype_correctwriting_' . $childanalyzername;
            /** @var qtype_correctwriting_abstract_analyzer $childanalyzer */
            $childanalyzer =  new $createdchildanalyzername();
            foreach($analyzer->result_pairs() as $pairindex => $pair) {
                $childpair =  $this->perform_analysis_with_analyzer($index + 1, $pair);
                // Compute fitness, based on results
                $fitness = $analyzer->fitness($childpair->mistakes());
                $fitness += $childanalyzer->fitness($childpair->mistakes());

                if ($foundmaxfitness == false || $fitness > $maxfitness) {
                    $maxfitness = $fitness;
                    $maxpair = $childpair;
                }
            }

        }

        return $maxpair;
    }

    /**  Returns matching answer. Must return matching answer found when response was being graded.
         @param array $response student response  as array ( 'answer' => string of student response )
     */
    public function get_matching_answer(array $response) {
        $this->get_best_fit_answer($response);
        // Handle obstacle when no answer matched
        if ($this->matchedanswerid == null) {
            $keys = array_keys($this->answers);
            return $this->answers[$keys[0]];
        }
        // Handle fully incorrect answer
        $result = $this->answers[$this->matchedanswerid];
        $result = clone $result;
        $result->fraction =  $this->matchedgradestate[0];

        return $result;
    }

    public function get_correct_response() {
        if (count($this->answers)!=0) {
            $maxfraction = -1;
            $maxkey = null;
            foreach($this->answers as $key => $answer) {
                if ($answer->fraction > $maxfraction) {
                    $maxfraction = $answer->fraction;
                    $maxkey = $key;
                }
            }
            return array('answer' => $this->answers[$maxkey]->answer);
        }
        return null;
    }


    //////////Specific hints implementation part

     /** We need adaptive or behaviour to use hints
      * @param question_attempt $qa
      * @param string $preferredbehaviour
      * @return qbehaviour|behaviour_with_hints
      */
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
     * Returns an array of available specific hint types
     * @param block_formal_langs_token_stream|null $response
     * @return array of hints
     */
    public function available_specific_hints($response = null) {
        $hints = array();
        if (count($this->hints) > 0) {
            $hints[] = 'hintmoodle#';
        }
        if ($response !== null) {
            $this->get_best_fit_answer($response);//Be sure to have correct cached values.
            if (is_object($this->matchedresults)) {
                $mistakes = $this->matchedresults->mistakes();
                foreach ($mistakes as $mistake) {
                    /** @var qtype_correctwriting_response_mistake $mistake */
                    foreach($mistake->supported_hints() as $hintname) {
                        $classname =  'qtype_correctwriting_hint' . $hintname;
                        $key = $hintname . '_' . $mistake->mistake_key();
                        /** @var qtype_specific_hint  $hintobj */
                        $hintobj = new $classname($this, $key, $mistake);
                        if ($hintobj->hint_available()) {
                            $hints[] = $key;
                        }
                    }
                }
            }
        }
        return $hints;
    }

    public function hints_available_for_student($response = null) {
        // TODO - define behaviour when some hint used in interactive, but set to 'No' for adaptive.
        return $this->available_specific_hints($response);
    }

    /**
     * Hint object factory
     *
     * Returns a hint object for given type
     */
    public function hint_object($hintkey, $response = null) {
        //Moodle-specific hints.
        if (substr($hintkey, 0, 11) == 'hintmoodle#') {
            return new qtype_poasquestion\hintmoodle($this, $hintkey);
        }

        //CorrectWriting specific hints.
        $classname = substr($hintkey, 0, strpos($hintkey, '_'));//First '_' separates classname from mistake key.
        $mistakekey = substr($hintkey, strpos($hintkey, '_')+1);
        $hintclass = 'qtype_correctwriting_hint' . $classname;
        if ($response !== null) {
            $this->get_best_fit_answer($response);//Be sure to have correct cached values.
            if (is_object($this->matchedresults)) {
                $mistakes = $this->matchedresults->mistakes();
                $hintmistake = null;
                foreach($mistakes as $mistake) {
                    /** @var qtype_correctwriting_response_mistake $mistake */
                    if ($mistake->mistake_key() == $mistakekey) {
                        $hintmistake = $mistake;
                        break;
                    }
                }
                return new $hintclass($this, $hintkey, $hintmistake);
            }
        }
        return new $hintclass($this, $hintkey, null);
    }

    /**
     * Checks, whether two lexeme sequences are equal. Only a corrected_string lexemes and correct_string
     * tokens are checked
     * @param block_formal_langs_string_pair $stringpair a pair of strings
     * @return boolean
     */
    public function are_lexeme_sequences_equal(block_formal_langs_string_pair $stringpair) {

        $responsetokens = $stringpair->correctedstring()->stream->tokens;
        $answertokens =  $stringpair->correctstring()->stream->tokens;
        $same = false;
        $options = $this->token_comparing_options();
        if (count($responsetokens) == count($answertokens)) {
            $same = true;
            if (count($responsetokens) != 0) {
                for($i = 0; $i < count($responsetokens); $i++) {
                    /** @var block_formal_langs_token_base $token */
                    $token =   $responsetokens[$i];
                    $same = ($same && $token->is_same($answertokens[$i], $options));
                }
            }
        }
        return $same;
    }

    /**
     * Returns an options for token comparting
     * @return block_formal_langs_comparing_options
     */
    public function token_comparing_options() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = $this->usecase;
        return $options;
    }


    /**
     * Checks whether specified analyzer is enabled
     * @param string $name
     * @return bool
     */
    public function is_analyzer_enabled($name) {
        $fieldname = 'is' . str_replace('_', '', $name) . 'enabled';
        $enabled = $this->$fieldname;
        if ($name == 'syntax_analyzer') {
            $enabled = $enabled && $this->get_used_language()->could_parse();
        }
        return $enabled;
    }

}
