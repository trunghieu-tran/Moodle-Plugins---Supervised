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
 * Defines class of lexical analyzer for correct writing question.
 *
 * Lexical analyzer object is created for each correct answer and
 * is responsible for tokenizing, looking for lexical mistakes (typos,
 * missing and extra separators etc) and other mistakes involving individual tokens,
 * merging resulting array of mistakes from all analyzers and determine
 * answer fitness for the response.
 *
 * Lexical analyzers create and use sequence analyzers to determine structural mistakes.
 * There may be more than one sequence analyzer created if there are several equal groups of
 * lexical mistakes possible.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Birukova Maria, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/lexical_mistakes.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');
//Other necessary requires

class qtype_correctwriting_lexical_analyzer extends qtype_correctwriting_abstract_analyzer {

    /**
     * Do all processing and fill resultstringpairs and resultmistakes fields.
     *
     * You are normally don't want to overload it. Overload analyze() and bypass() instead.
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     * When called without params just creates empty object to call analyzer-dependent functions on.
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
     * Do real analyzing and fill resultstringpairs and resultmistakes fields.
     *
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     */
    protected function analyze() {
        if ($this->question->are_lexeme_sequences_equal($this->basestringpair)) {
            $this->bypass();
            $this->resultstringpairs[0]->assert_that_strings_are_equal();
            return;
        }

        $lexicalmistakes = $this->convert_lexer_errors_to_mistakes();
        // TODO: Biryukova's new code should be placed here!
        // 1. Compute self code - Mamontov
        // 1.1. Replace result with fixed strings
        $this->resultstringpairs = block_formal_langs_string_pair::best_string_pairs(
            $this->basestringpair->correctstring(),
            $this->basestringpair->comparedstring(),
            $this->question->lexicalerrorthreshold,
            $this->question->token_comparing_options(),
            'qtype_correctwriting_string_pair'
        );

        /** @var qtype_correctwriting_string_pair $pair */
        $sequenceanalyzerdisabled = ($this->question->is_analyzer_enabled('sequence_analyzer') == false);
        foreach($this->resultstringpairs as $pair) {
            //$pair->tokenmappings[get_class($this)] = $pair->pairs_between_corrected_compared();
            /** qtype_correctwriting_string_pair */
            $pair->analyzersequence[] = get_class($this);
            if($pair->matches()==null){
                $pair->setcorrectedstring($pair->comparedstring());
                return;
            }
            $setmatches = $pair->matches()->matchedpairs;
            $lexmistakes=array();
            foreach ($setmatches as $onematch) {
                /** @var block_formal_langs_matched_tokens_pair $onematch */
                if ($onematch->mistakeweight > 0) {
                    $lexmistake = new qtype_correctwriting_lexical_mistake($onematch);
                    $lexmistake->stringpair = $pair;
                    $lexmistake->answermistaken = $onematch->correcttokens;
                    $lexmistake->responsemistaken = $onematch->comparedtokens;
                    $lexmistake->mistakemsg = $onematch->message($this->basestringpair->correctstring(), $this->basestringpair->comparedstring());
                    $lexmistake->weight = $onematch->mistakeweight;
                    $lexmistakes[] = $lexmistake;
                }
            }
            if ($sequenceanalyzerdisabled) {
                $this->convert_non_covered_tokens_to_mistakes($pair);
            }
            $pair->append_mistakes($lexmistakes);
        }
    }

    /**
     * Converts non-covered tokens from answer and response to mistakes
     * @param qtype_correctwriting_string_pair $pair a pair to be converted
     */
    protected function convert_non_covered_tokens_to_mistakes($pair) {
        $mistakes = array();
        $noncoveredanswertokens = array();
        $noncoveredresponsetokens = array();

        // Fill non-covered tokens with all tokens
        $correctpairtokens = $pair->correctstring()->stream->tokens;
        for($i = 0; $i < count($correctpairtokens); $i++) {
            $noncoveredanswertokens[$i] = 1;
        }

        $comparedpairtokens = $pair->comparedstring()->stream->tokens;
        for($i = 0; $i < count($comparedpairtokens); $i++) {
            $noncoveredresponsetokens[$i] = 1;
        }

        $setmatches = $pair->matches()->matchedpairs;
        foreach ($setmatches as $onematch) {
            /** @var block_formal_langs_matched_tokens_pair $onematch */
            for($i = 0; $i < count($onematch->correcttokens); $i++) {
                if (array_key_exists($onematch->correcttokens[$i], $noncoveredanswertokens)) {
                    unset($noncoveredanswertokens[$onematch->correcttokens[$i]]);
                }
            }
            /** @var block_formal_langs_matched_tokens_pair $onematch */
            for($i = 0; $i < count($onematch->comparedtokens); $i++) {
                if (array_key_exists($onematch->comparedtokens[$i], $noncoveredresponsetokens)) {
                    unset($noncoveredresponsetokens[$onematch->comparedtokens[$i]]);
                }
            }
        }

        if (count($noncoveredanswertokens)) {
            foreach($noncoveredanswertokens as $answerkey => $id) {
                $mistakes[] = $this->create_absent_mistake($pair, $answerkey);
            }
        }
        if (count($noncoveredresponsetokens)) {
            foreach($noncoveredresponsetokens as $responsekey => $id) {
                $mistakes[] = $this->create_added_mistake($pair, $responsekey);
            }
        }

        if (count($mistakes)) {
            $pair->append_mistakes($mistakes);
        }
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
        $result->weight =  $this->question->addedmistakeweight;
        $result->mapfromcorrectedtocompared = false;
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
        $result->weight =  $this->question->absentmistakeweight;
        $result->mapfromcorrectedtocompared = false;
        $result->source = get_class($this);
        return $result;
    }


    protected function bypass() {
        // You must create mistakes of skipped lexemes and additional lexemes
        // Also, you need to create matched 1:1 by simply comparing lexemes of two sequences
        // with ::is_same
        $this->resultstringpairs = block_formal_langs_string_pair::best_string_pairs_for_bypass(
            $this->basestringpair->correctstring(),
            $this->basestringpair->comparedstring(),
            $this->question->lexicalerrorthreshold,
            $this->question->token_comparing_options(),
            'qtype_correctwriting_string_pair'
        );

        /** @var qtype_correctwriting_string_pair $string */
        $this->resultstringpairs[0]->set_mistakes($this->convert_lexer_errors_to_mistakes());

    }

    /**
     * Lexical analyzer does not have any hints, currently
     * @return array
     */
    public function supported_hints() {
        return array();
    }

    /**
     * Converts lexer errors  to mistakes
     * @return array of qtype_correctwriting_scanning_mistake
     */
    protected function convert_lexer_errors_to_mistakes() {
        //3. Set array of mistakes from lexer errors - Mamontov
        $mistakes = array();
        // Mapping from error kind to our own language string
        $mistakecustomhandling = array('clanguagemulticharliteral' => 'clanguagemulticharliteral');
        if (count($this->basestringpair->comparedstring()->stream->errors) != 0) {
            /**
             * @var block_formal_langs_lexical_error $error
             */
            foreach($this->basestringpair->comparedstring()->stream->errors as $index => $error) {
                $mistake = new qtype_correctwriting_scanning_mistake(null);

                $message =  $error->errormessage;
                $mistake->languagename = $this->question->get_used_language()->name();
                /** @var block_formal_langs_token_base $token */
                $token = $this->basestringpair->comparedstring()->stream->tokens[$error->tokenindex];
                $mistake->position = $token->position();
                $mistake->answermistaken = null;
                $mistake->responsemistaken = array( $error->tokenindex );
                $mistake->weight = $this->question->lexicalerrorweight;
                $mistake->stringpair = $this->basestringpair;
                if (array_key_exists($error->errorkind, $mistakecustomhandling)) {
                    $a = new stdClass();
                    /**
                     * @var qtype_correctwriting_node_position $pos
                     */
                    $pos = $mistake->position;

                    $a->linestart = $pos->linestart();
                    $a->colstart = $pos->colstart();
                    $a->lineend = $pos->lineend();
                    $a->colend = $pos->colend();
                    $a->value = $token->value();
                    $message = get_string($mistakecustomhandling[$error->errorkind],  'qtype_correctwriting', $a);
                }
                $mistake->mistakemsg = $message;
                $mistakes[] = $mistake;
            }
        }
        return $mistakes;
    }

    /**
     * Returns a mistake type for a error, used by this analyzer
     * @return string
     */
    protected function own_mistake_type() {
        return 'qtype_correctwriting_lexical_mistake';
    }

    /**
     * Returns an array of mistakes objects for given matches_group object
     */
    public function matches_to_mistakes($group) {
    }

    /**
     * Returns an array of extra_question_fields used by this analyzer.
     */
    public function extra_question_fields() {
        return array('lexicalerrorthreshold', 'lexicalerrorweight', 'addedmistakeweight', 'movedmistakeweight');
    }

    public function name() {
        return 'lexical_analyzer';
    }


    // Form and DB related functions.
    public function float_form_fields() {
        return array(
            array('name' => 'lexicalerrorweight', 'default' => 0.05, 'advanced' => true, 'required' => false, 'min' => 0, 'max' => 1),      //Lexical error weight field
            array('name' => 'lexicalerrorthreshold', 'default' => 0.33, 'advanced' => true, 'required' => false, 'min' => 0, 'max' => 1), //Lexical error threshold field
        );
    }
}

?>