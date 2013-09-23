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

    protected function analyze() {
        $answertokens = $this->basestringpair->correctstring()->stream;
        $responsetokens = $this->basestringpair->correctedstring()->stream;
        $options = $this->question->token_comparing_options();
        $alllcs = qtype_correctwriting_sequence_analyzer::lcs($answertokens, $responsetokens, $options);

        $weights = new stdClass;
        $weights->movedweight = $this->question->movedmistakeweight;
        $weights->absentweight = $this->question->absentmistakeweight;
        $weights->addedweight = $this->question->addedmistakeweight;

        if (count($alllcs)) {
            foreach ($alllcs as $lcs) {
                $pair = $this->basestringpair->copy_with_lcs($lcs);
                $this->resultstringpairs[] = $pair;
                $this->resultmistakes[] = $this->matches_to_mistakes($pair, $weights);
            }
        } else {
            $pair = $this->basestringpair->copy_with_lcs(array());
            $this->resultstringpairs[] = $pair;
            $this->resultmistakes[] = $this->matches_to_mistakes($pair, $weights);
        }
    }


    /**
     * Compute and return longest common subsequence (tokenwise) of answer and corrected response.
     *
     * Array of individual lcs contains answer indexes as keys and response indexes as values.
     * There may be more than one lcs for a given pair of strings.
     * @param  block_formal_langs_token_stream $answerstream  array of answer tokens
     * @param  block_formal_langs_token_stream $responsestream array of response tokens
     * @param  block_formal_langs_comparing_options $options options for comparing lexemes
     * @return array array of individual lcs arrays
     */
    public static function lcs($answerstream, $responsestream, $options) {
        // Extract data from method
        $answer = $answerstream->tokens;
        $response = $responsestream->tokens;
        // Find all matches, they become nodes of a graph.
        // After that we can use Floyd-Warshall algorithm
        $matches = array();
        // Match is defined as tuple <i,j>
        for ($i = 0; $i < count($answer); $i++) {
            for($j = 0; $j < count($response); $j++) {
                /** @var block_formal_langs_token_base $answertoken */
                $answertoken = $answer[$i];
                if ($answertoken->is_same($response[$j], $options)) {
                    $matches[] = array($i, $j);
                }
            }
        }


        // If nothing found - no matches, return
        if (count($matches) === 0)
            return array();
        // Matrix of longest paths on graph, defined as matches
        // nodes, where edged defined as for all n1,n2
        // edge between n1 and n2 exist, if n1 < n2 <=> n1[0] < n2[0] && n1[1] < n2[1]
        // Way in matrix is defined as tuple <sum of weight, center node index>
        // A predecessor matrix, needed for computing is merged with common way matrix
        // We fill only upper-right part, because we can easily prove, that
        // only upper-right part will be filled
        $waymatrix = array();
        for ($i = 0; $i < count($matches); $i++) {
            $waymatrix[$i] = array();
            for($j = $i; $j < count($matches); $j++) {
                $waymatrix[$i][$j] = array(0, null);
                if ($matches[$i][0] < $matches[$j][0] && $matches[$i][1] < $matches[$j][1]) {
                    $waymatrix[$i][$j][0] = -1;
                }
            }
        }



        // This is slightly modified Floyd-Warshall algorithm runned for this graph
        // He sets a center node, so restoration of path will be more complicated
        // An unusual boundaries is set because there must exist all of element
        // and due to some fill method the bounds can be deduced.
        for ($k = 0; ($k < count($matches)); $k++) {

            for($i = 0; $i <= $k; $i++) {
                for($j = $k;  $j < count($matches); $j++) {
                    $iklength = $waymatrix[$i][$k][0];
                    $kjlength = $waymatrix[$k][$j][0];
                    $newlength = $iklength + $kjlength;
                    $oldlength = $waymatrix[$i][$j][0];
                    if ($newlength < $oldlength && $iklength !== 0 && $kjlength !== 0) {
                        $waymatrix[$i][$j][0] = $newlength;
                        $waymatrix[$i][$j][1] = $k;
                    }
                }
            }

        }




        // Minimal weight of way in this method refers to a longest sequence
        $minimalweight = 0;
        // Array of ways as a tuple <from_match, to_match>, where matches are nodes
        $ways = array();

        // Find minimal weight
        for($i = 0; $i < count($matches); $i++) {
            for($j = $i; $j < count($matches); $j++) {
                if ($waymatrix[$i][$j][0] < $minimalweight)
                    $minimalweight = $waymatrix[$i][$j][0];
            }
        }

        $onematches = ($minimalweight ===0);

        // Find ways, matched for minimal weight
        for($i = 0; ($i < count($matches)) && !$onematches; $i++) {
            for($j = $i; $j < count($matches); $j++) {
                if ($waymatrix[$i][$j][0] == $minimalweight)
                    $ways[] = array($i, $j);
            }
        }



        // In case of one match, we can simly reconstruct it as array of matches
        $matchesways = array();
        if ($onematches)
        {
            for ($i=0;$i<count($matches);$i++) {
                $matchesways[] = array( $matches[$i] );
            }
        }

        // Reconstruct ways as array of match indexes
        for($i = 0; ($i < count($ways)) && !$onematches; $i++) {
            // Creates a new global way task for finding a way
            $waytask = new stdClass();
            $waytask->i = $ways[$i][0]; // These fields defines nodes, where
            $waytask->j = $ways[$i][1]; // way between them must be reconstructed
            $waytask->iktask = null;    // These are references
            $waytask->kjtask = null;    // which will be filled, when we are deferring loops
            $waytask->result = array(); // In this field result is stored


            $evalqueue = array( $waytask );
            $deferqueue = array();
            // When this loop is over, some primitive ways are computed
            // and deferqueue is filled backwards with new items
            while ( count($evalqueue) ) {
                $task = array_shift($evalqueue);
                $ti = $task->i;
                $tj = $task->j;
                $k = $waymatrix[$ti][$tj][1];
                if ($k === null) {
                    $task->result = array( $matches[$ti], $matches[$tj] );
                } else {
                    // Create task for reconstructing a way from i to k
                    $iktask = new stdClass();
                    $iktask->i = $ti;
                    $iktask->j = $k;
                    $iktask->iktask = null;
                    $iktask->kjtask = null;
                    $iktask->result = array();
                    $task->iktask = $iktask;
                    $evalqueue[] = $iktask;

                    $kjtask = new stdClass();
                    $kjtask->i = $k;
                    $kjtask->j = $tj;
                    $kjtask->iktask = null;
                    $kjtask->kjtask = null;
                    $kjtask->result = array();
                    $task->kjtask = $kjtask;
                    $evalqueue[] = $kjtask;

                    if (count($deferqueue) == 0) {
                        $deferqueue[] = $task;
                    } else {
                        array_unshift($deferqueue, $task);
                    }
                }
            }

            // Now, we could just reconstruct ways for data
            while( count($deferqueue) ) {
                $task = array_shift($deferqueue);
                // Now we merge ways from task and put them into result
                $task->result = array_merge( $task->iktask->result, array_slice($task->kjtask->result, 1) );
            }
            $matchesways[] = $waytask->result;
        }

        // Now we convert LCS to result format
        for($i = 0; $i < count($matchesways); $i++) {
            $lcs = array();
            for($j = 0; $j < count($matchesways[$i]); $j++) {
                $lcs[$matchesways[$i][$j][0]] = $matchesways[$i][$j][1];
            }
            $matchesways[$i] = $lcs;
        }

        return $matchesways;
    }
    /**
     * Creates a new mistake, that represents case, when one lexeme moved to other position
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $answerindex   index of lexeme in answer
     * @param int $responseindex index of lexeme in response
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_moved_mistake($pair, $answerindex,$responseindex) {
        return new qtype_correctwriting_lexeme_moved_mistake($this->language, $pair,
                                                             $answerindex,
                                                             $responseindex);
    }
    /**
     * Creates a new mistake, that represents case, when odd lexeme is insert to index
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $responseindex index of lexeme in response
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_added_mistake($pair, $responseindex) {
        return new qtype_correctwriting_lexeme_added_mistake($this->language,
                                                             $pair,
                                                             $responseindex, $this->question->token_comparing_options());
    }
    /**
     * Creates a new mistake, that represents case, when lexeme is skipped
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param int $answerindex   index of lexeme in answer
     * @return qtype_correctwriting_lexeme_moved_mistake a mistake
     */
    private function create_absent_mistake($pair, $answerindex) {
        return new qtype_correctwriting_lexeme_absent_mistake($this->language,
                                                              $pair,
                                                              $answerindex
                                                             );
    }
    /**
     * Returns an array of mistakes objects for given individual lcs array.
     * Also sets fitness to fitness, that computed from function.
     * @param qtype_correctwriting_string_pair $pair a source pair
     * @param object $weights weights of errors
     * @return array array of mistake objects
     */
    public function matches_to_mistakes($pair,$weights) {
        $answer = &$this->basestringpair->correctstring()->stream->tokens;
        $response = &$this->basestringpair->correctedstring()->stream->tokens;
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
                    $mistake->set_lcs($lcs);
                    $mistake->weight = $weights->movedweight;
                    $result[] = $mistake;
                    $counts->moved  = $counts->moved + 1;
                } else {
                    $mistake = $this->create_absent_mistake($pair, $i);
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
        return array(array ('name' => 'absentmistakeweight', 'default' => 0.1, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true),  //Absent token mistake weight field
                     array ('name' =>'addedmistakeweight', 'default' => 0.1, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true),    //Extra token mistake weight field
                     array ('name' =>'movedmistakeweight', 'default' => 0.05, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true)    //Moved token mistake weight field
                    );
    }

    public function extra_question_fields() {
        return array('absentmistakeweight',  'addedmistakeweight', 'movedmistakeweight');
    }

}
