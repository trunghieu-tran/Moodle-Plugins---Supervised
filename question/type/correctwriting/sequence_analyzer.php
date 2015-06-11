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
 * Defines class of sequence analyzer for correct writing question.
 *
 * Sequence analyzer object is created for each possible set of lexical mistakes and
 * is responsible for finding common parts of answer regarding sequence of tokens.
 * Longest common sequence algorithm is used to determine it.
 *
 * Sequence analyzers create and use syntax analyzers to determine structural mistakes using
 * language grammar. When using grammar analyzer is impossible, it determines sequence mistakes
 * using lcs, i.e. misplaced, extra and missing tokens.
 * There may be more than one syntax analyzer created if there are several LCS'es of
 * answer and response.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

//Other necessary requires
require_once($CFG->dirroot . '/question/type/correctwriting/abstract_analyzer.php');
require_once($CFG->dirroot . '/question/type/correctwriting/syntax_analyzer.php');
require_once($CFG->dirroot . '/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot . '/question/type/correctwriting/sequence_mistakes.php');
require_once($CFG->dirroot . '/question/type/correctwriting/string_pair.php');


class  qtype_correctwriting_sequence_analyzer extends qtype_correctwriting_abstract_analyzer {



    public function name() {
        return 'sequence_analyzer';
    }

    /**
     * Do all processing and fill all member variables.
     * Passed response could be null, than object used just to find errors in the answers, token count etc...
     * @throws moodle_exception if invalid number of string pairs
     * @param qtype_correctwriting_question $question
     * @param qtype_correctwriting_string_pair $basepair a pair, passed as input
     * @param block_formal_langs_abstract_language $language a language
     * @param bool $bypass false if analyzer should work, true if it should just allow subsequent analyzers to work.
     */
    public function __construct($question = null, $basepair = null, $language = null, $bypass = true) {
        parent::__construct($question, $basepair, $language, $bypass);
    }

    /**
     * Returns maximal LCS count, which shold be returned
     * @return int
     */
    protected function lcs_count_threshold() {
        global $CFG;
        if (intval($CFG->qtype_correctwriting_max_temp_lcs) <= 0) {
            return 0;
        }
        return intval($CFG->qtype_correctwriting_max_temp_lcs);
    }

    protected function analyze() {
        $answertokens = $this->basestringpair->enum_correct_string()->stream;
        $responsetokens = $this->basestringpair->correctedstring()->stream;
        /*echo "<pre>";
        echo "===================\n";
        echo "enumcorrectstring: ";
        foreach($answertokens->tokens as $token) {
            echo $token->value() . " ";
        }
        echo "\n";
        echo "correctedstring:   ";
        foreach($responsetokens->tokens as $token) {
            echo $token->value() . " ";
        }
        echo "\n";*/
        $options = $this->question->token_comparing_options();
        $alllcs = qtype_correctwriting_sequence_analyzer::lcs($answertokens, $responsetokens, $options, $this->lcs_count_threshold());

        $weights = new stdClass;
        $weights->movedweight = $this->question->movedmistakeweight;
        $weights->absentweight = $this->question->absentmistakeweight;
        $weights->addedweight = $this->question->addedmistakeweight;

        if (count($alllcs)) {
            foreach ($alllcs as $lcs) {
                $pair = $this->basestringpair->copy_with_lcs($lcs);
                $this->resultstringpairs[] = $pair;
                $this->fill_matches($pair);
                $pair->append_mistakes($this->matches_to_mistakes($pair, $weights));
            }
        } else {
            $pair = $this->basestringpair->copy_with_lcs(array());
            $this->resultstringpairs[] = $pair;
            $this->fill_matches($pair);
            $pair->append_mistakes($this->matches_to_mistakes($pair, $weights));
        }
        /*foreach($this->resultstringpairs as $pair) {
            echo "pair:            \n";
            foreach($pair->mistakes() as $mistake) {
                echo str_replace(array('qtype_correctwriting_', '_mistake'), array('', ''), get_class($mistake)) . " ";
            }
            echo "\n";
        }
        echo "===================\n";
        echo "</pre>";*/
    }

    /**
     * Returns a mistake type for a error, used by this analyzer
     * @return string
     */
    protected function own_mistake_type() {
        return 'qtype_correctwriting_sequence_mistake';
    }

    /**
     * Compute and return longest common subsequence (tokenwise) of answer and corrected response.
     *
     * Array of individual lcs contains answer indexes as keys and response indexes as values.
     * There may be more than one lcs for a given pair of strings.
     * @param  block_formal_langs_token_stream $answerstream  array of answer tokens
     * @param  block_formal_langs_token_stream $responsestream array of response tokens
     * @param  block_formal_langs_comparing_options $options options for comparing lexemes
     * @param  int $threshold maximal count of found LCS (0 - is unbounded). We shold care, because
     * there could be answers with 70 000+ possible LCS and it can consume a lot of memory
     * @return array array of individual lcs arrays
     */
    public static function lcs($answerstream, $responsestream, $options, $threshold = 0) {
        // Extract answer and response array of stream
        $answer = $answerstream->tokens;
        $response = $responsestream->tokens;

        // http://stackoverflow.com/questions/18496665/longest-common-subsequence-print-all-subsequences
        
        // An array of matches, where keys are indexes of answer and values are arrays of
        // indexes from response
        $matches = array();
        // Fill an array of matches filling an lcs data
        $answercount = count($answer);
        $responsecount = count($response);

        // Compute C as lcs
        $C = array();
        for($i = 0; $i <= $answercount; $i++) {
            $C[$i] = array();
            for($j = 0; $j <= $responsecount; $j++) {
                $C[$i][$j] = 0;
            }
        }
				
        for($i =  0; $i < $answercount; $i++) {
            for($j =  0; $j < $responsecount; $j++) {
                if ($answer[$i]->is_same($response[$j], $options)) {
                    $C[$i + 1][$j + 1] = $C[$i][$j] + 1;
                } else {
                    $C[$i + 1][$j + 1] = max($C[$i+1][$j], $C[$i][$j + 1]);
                }
            }
        }
        $cache = array();
        $backtrackall = function($i, $j) use($C, $answer, $response, $options, &$backtrackall, &$cache, $threshold) {
            gc_collect_cycles();
            $globalcachekey = $i . ' . ' . $j;
            if (array_key_exists($globalcachekey, $cache)) {
                return $cache[$globalcachekey];
            }
            if ($i == 0 || $j == 0) {
                $result = array();
            } else {
                if ($answer[$i - 1]->is_same($response[$j - 1], $options)) {
                    $newmatch = array(($i - 1) => ($j - 1));
                    $result = $backtrackall($i - 1, $j - 1);
                    if (count($result)) {
                        foreach ($result as $key => $match) {
                            if (count($match)) {
                                $match = $match + $newmatch;
                            } else {
                                $match = $newmatch;
                            }
                            $result[$key] = $match;
                        }
                    } else {
                        $result = array($newmatch);
                    }
                    return $result;
                } else {
                    $result = array();
                    if ($C[$i][$j - 1] >= $C[$i - 1][$j]) {
                        $result = $backtrackall($i, $j - 1);
                    }
                    if ($C[$i - 1][$j] >= $C[$i][$j - 1]) {
                        $result2 = $backtrackall($i - 1, $j);
                        if (count($result)) {
                            $result = array_merge($result, $result2);
                        } else {
                            $result = $result2;
                        }
                    }
                }
            }
            if ($threshold != 0) {
                if (count($result) > $threshold) {
                    $result = array_slice($result, 0, $threshold);
                }
            }
            $cache[$globalcachekey] = $result;
            return $result;
        };
        
		// We modify this algorithm, since if we just call on last element, only one LCS could be returned. So, we pull all of maxlength and backtrack them
        $verticalcount = count($C);
        $horizontalcount = count($C[0]);
        
        $poses = array();
        $maxvalue = 0;
        
        for($i = 1; $i < $verticalcount - 1; $i++) {
            if ($C[$i][$horizontalcount - 1] > $maxvalue) {
                $maxvalue = $C[$i][$horizontalcount - 1];
                $poses = array( array($i, $horizontalcount - 1) );
            } else {
                if ($C[$i][$horizontalcount - 1] == $maxvalue && $maxvalue != 0) {
                    $poses[] = array($i, $horizontalcount - 1);
                }
            }
        }
        
        for($i = 1; $i < $horizontalcount; $i++) {
            if ($C[$verticalcount - 1][$i] > $maxvalue) {
                $maxvalue = $C[$verticalcount - 1][$i];
                $poses = array( array($verticalcount - 1, $i) );
            } else {
                if ($C[$verticalcount - 1][$i] == $maxvalue && $maxvalue !=0) {
                    $poses[] = array($verticalcount - 1, $i) ;
                }
            }
        }
        
        if (count($poses) == 0) {
            $lcs = $backtrackall($answercount, $responsecount);    
        } else {
            $lcs = array();
            foreach($poses as $pos) {
                $lcs2 = $backtrackall($pos[0], $pos[1]);
                if (count($lcs2)) {
                    foreach($lcs2 as $lcstmp) {
                        if (!in_array($lcstmp, $lcs)) {
                            $lcs[] = $lcstmp;
                        }
                    }
                }            
            }
        }
        return $lcs;
    }
    /**
     * Creates a new mistake, that represents case, when one lexeme moved to other position
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $answerindex   index of lexeme in answer
     * @param int $responseindex index of lexeme in response
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_moved_mistake($pair, $answerindex,$responseindex) {
        $result = new qtype_correctwriting_lexeme_moved_mistake($this->language, $pair,
                                                             $answerindex,
                                                             $responseindex);
        $result->source = get_class($this);
        return $result;
    }
    /**
     * Creates a new mistake, that represents case, when odd lexeme is insert to index
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $responseindex index of lexeme in response
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_added_mistake($pair, $responseindex) {
        $result =  new qtype_correctwriting_lexeme_added_mistake($this->language,
                                                             $pair,
                                                             $responseindex, $this->question->token_comparing_options());
        $result->source = get_class($this);
        return $result;
    }
    /**
     * Creates a new mistake, that represents case, when lexeme is skipped
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $answerindex   index of lexeme in answer
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_absent_mistake($pair, $answerindex) {
        $result = new qtype_correctwriting_lexeme_absent_mistake($this->language,
                                                              $pair,
                                                              $answerindex
                                                             );
        $result->source = get_class($this);
        return $result;
    }

    /**
     * Creates token matches for analyzer. Since no moving operations
     * are performed, then matches are filled 1:1
     * @param qtype_correctwriting_string_pair $pair a resulting pair
     */
    protected function fill_matches($pair) {
        $result = array(array(), array());
        $response = $pair->correctedstring()->stream->tokens;
        $responsecount = count($response);
        for($i = 0; $i < $responsecount; $i++) {
            $result[0] = array( $i );
            $result[1] = array( $i );
        }
        if (property_exists($pair, 'tokenmappings')) {
            $pair->tokenmappings[get_class($this)] = $result;
        }
    }
    /**
     * Returns an array of mistakes objects for given individual lcs array.
     * Also sets fitness to fitness, that computed from function.
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param object $weights weights of errors
     * @return array array of mistake objects
     */
    public function matches_to_mistakes($pair,$weights) {
        $answer = &$this->basestringpair->enum_correct_string()->stream->tokens;
        $response = &$this->basestringpair->correctedstring()->stream->tokens;
        $pair->addedlexemesindexes = array();
        $pair->skippedlexemesindexes = array();
        $pair->movedlexemesindexes = array();
        $lcs = $pair->lcs();
        // Determines, whether answer tokens are used in mistake computation
        $answerused = array();
        $answercount = count($answer);
        for ($i = 0;$i < $answercount;$i++) {
            $answerused[] = false;
        }

        // Determines, whether response tokens are used in mistake computation
        $responseused = array();
        $responsecount = count($response);
        for ($i = 0;$i < $responsecount;$i++) {
            $responseused[] = false;
        }

        // This result will be returned from function
        $result = array();

        // These are counts of each types of errors, used to compute fitness
        $counts = new stdClass;
        $counts->moved = 0;
        $counts->added = 0;
        $counts->absent = 0;

        // Scan lcs to mark excluded lexemes
        foreach($lcs as $answerindex => $responseindex) {
            // Mark lexemes as used
            $answerused[$answerindex] = true;
            $responseused[$responseindex] = true;
        }


        $options = $this->question->token_comparing_options();

        // Determine removed and moved lexemes by scanning answer
        for ($i = 0;$i < $answercount;$i++) {
            // If this lexeme is not in LCS
            if ($answerused[$i] == false) {
                // Determine, whether lexeme is simply moved by scanning response or removed
                $ismoved = false;
                $movedpos = -1;
                for ($j = 0;$j < $responsecount && $ismoved == false;$j++) {
                    /** @var block_formal_langs_token_base $answertoken */
                    $answertoken = $answer[$i];
                    // Check whether lexemes are equal
                    $isequal = $answertoken->is_same($response[$j], $options);
                    if ($isequal == true && $responseused[$j] == false) {
                        $ismoved = true;
                        $movedpos = $j;
                        $responseused[$j] = true;
                    }
                }
                // Determine type of mistake (moved or removed)
                if ($ismoved) {
                    $mistake = $this->create_moved_mistake($pair, $i, $movedpos);
                    $pair->movedlexemesindexes[$movedpos] = $i;
                    $mistake->set_lcs($lcs);
                    $mistake->weight = $weights->movedweight;
                    $result[] = $mistake;
                    $counts->moved  = $counts->moved + 1;
                } else {
                    $mistake = $this->create_absent_mistake($pair, $i);
                    $pair->skippedlexemesindexes[] = $i;
                    $mistake->set_lcs($lcs);
                    $mistake->weight = $weights->absentweight;
                    $result[] = $mistake;
                    $counts->absent = $counts->absent + 1;
                }
            }
        }

        //Determine added lexemes from reponse
        for ($i = 0;$i < $responsecount;$i++) {
            if ($responseused[$i] == false) {
                $pair->addedlexemesindexes[] = $i;
                $mistake = $this->create_added_mistake($pair, $i);
                $result[] = $mistake;
                $mistake->set_lcs($lcs);
                $mistake->weight = $weights->addedweight;
                $counts->added = $counts->added + 1;
            }
        }

        return $result;
    }




    public function has_errors() {
        return !empty($this->errors);
    }


    public function supported_hints() {
        return array('whatis', 'wheretxt', 'wherepic');
    }
    // Form and DB related functions.
    public function float_form_fields() {
        return array(array ('name' =>'movedmistakeweight', 'default' => 0.05, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true)    //Moved token mistake weight field
                    );
    }

    public function extra_question_fields() {
        return array('absentmistakeweight');
    }

}
