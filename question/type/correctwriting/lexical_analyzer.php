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
            parent::bypass();
            return;
        }

        $lexicalmistakes = $this->convert_lexer_errors_to_mistakes();

        // TODO: Biryukova's new code should be placed here!
        // 1. Compute self code - Biryukova
        // 1.1. Replace result with fixed strings
        $result = array( $this->basestringpair );
        // 1.1. Compute own mistakes and place them in mistakes
        $mistakes = array( array( ) );

        // 2. Merge mistakes into one
        $this->resultstringpairs = $result;
        $this->resultmistakes = $mistakes;
        foreach($this->resultmistakes as $key => $resultmistake) {
            $currentmistakes = $resultmistake;
            if (count($currentmistakes)) {
                $currentmistakes = array_merge($currentmistakes, $lexicalmistakes);
            } else {
                $currentmistakes = $lexicalmistakes;
            }
            $this->resultmistakes[$key] = $currentmistakes;
        }
    }


    protected function bypass() {
        parent::bypass();
        $this->resultmistakes = array( $this->convert_lexer_errors_to_mistakes() );
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
                $mistake = new qtype_correctwriting_scanning_mistake();

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
     * Returns an array of mistakes objects for given matches_group object
     */
    public function matches_to_mistakes($group) {
    }

    /**
     * Returns an array of extra_question_fields used by this analyzer.
     */
    public function extra_question_fields() {
        return array('lexicalerrorthreshold', 'lexicalerrorweight');
    }

    public function name() {
        return 'lexical_analyzer';
    }


    // Form and DB related functions.
    public function float_form_fields() {
        return array(array('name' => 'lexicalerrorthreshold', 'default' => 0.33, 'advanced' => true, 'required' => false, 'min' => 0, 'max' => 1), //Lexical error threshold field
                     array('name' => 'lexicalerrorweight','default' => 0.05, 'advanced' => true, 'required' => false, 'min' => 0, 'max' => 1)      //Lexical error weight field
                    );
    }
}

?>