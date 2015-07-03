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
 * Defines unit-tests for testing mapping functions between different string pairs
 *
 * For a complete info, see qtype_correctwriting_string_pair
 *
 * @copyright &copy; 2011
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;

require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

/**
 * A main class for testing mapping functions between different string pairs
 */
class qtype_correctwriting_test_map extends PHPUnit_Framework_TestCase {

    /**
     * Sets an environment for tests
     */
    public function setUp() {
        $this->lang = new block_formal_langs_language_simple_english();

        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 0.7;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $this->lang;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();

        $this->question = $question;
    }


    /**
     * Tests mapping from corrected to compared, when string is exactly matched
     */
    public function test_corrected_to_compared_exact_match() {
        $bestmatchpair = $this->make_pair('abc cde', 'abc  cde');
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'abc cde', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));
    }

    /**
     * Tests mapping from corrected to compared, when second string has swapped lexemes
     */
    public function test_corrected_to_compared_moved() {
        $bestmatchpair = $this->make_pair('abc cde', 'cde abc');
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'cde abc', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));
    }

    /**
     * Tests mapping from corrected to compared, when analyzer is set to bypass mode
     */
    public function test_corrected_to_compared_bypass() {
        $bestmatchpair = $this->make_pair('abc cde', 'cae abo sizeo');
        $this->question->lexicalerrorthreshold = 0.99;
        $this->question->lexicalerrorweight = 0.1;
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, true);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'cae abo sizeo', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(2);
        $this->assertTrue($index == array( 2 ), implode(' ', $index));
    }

    /**
     * Tests mapping from corrected to compared in case, when typo in strings is presented
     */
    public function test_corrected_to_compared_typo() {
        $bestmatchpair = $this->make_pair('abc cde', 'cae abo sizeo');
        $this->question->lexicalerrorthreshold = 0.99;
        $this->question->lexicalerrorweight = 0.1;
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'cde abc sizeo', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(2);
        $this->assertTrue($index == array( 2 ), implode(' ', $index));
    }

    /**
     * Tests mapping  from corrected to compared in case, when odd separator is presented
     */
    public function test_corrected_to_compared_odd_separator() {
        $bestmatchpair = $this->make_pair('multicanal', 'is multi canal receiver');
        $this->question->lexicalerrorthreshold = 0.99;
        $this->question->lexicalerrorweight = 0.1;
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'is multicanal receiver', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1, 2 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(2);
        $this->assertTrue($index == array( 3 ), implode(' ', $index));
    }

    /**
     * Tests mapping  from corrected to compared in case, when separator is absent
     */
    public function test_corrected_to_compared_absent_separator() {
        $bestmatchpair = $this->make_pair('hyper bowl is', 'is hyperbowl');
        $this->question->lexicalerrorthreshold = 0.99;
        $this->question->lexicalerrorweight = 0.1;
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'is hyper bowl', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(2);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));
    }

    /**
     * Tests mapping  from corrected to compared in case, when there are absent and odd lexemes and lexemes with
     * typos in them.
     */
    public function test_corrected_to_compared_odd_absent_with_typos() {
        $bestmatchpair = $this->make_pair('are there a typos', 'ther mistakes');
        $this->question->lexicalerrorthreshold = 0.99;
        $this->question->lexicalerrorweight = 0.1;
        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $bestmatchpair, $this->lang, false);
        $result = $analyzer->result_pairs();

        $this->assertTrue(count($result) == 1);
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        $string = $this->corrected_to_string($pair);
        $this->assertTrue($string == 'there mistakes', $string);

        $index  = $pair->map_from_corrected_string_to_compared_string(0);
        $this->assertTrue($index == array( 0 ), implode(' ', $index));

        $index  = $pair->map_from_corrected_string_to_compared_string(1);
        $this->assertTrue($index == array( 1 ), implode(' ', $index));
    }

    /**
     * Tests re-arranging objects to corrected
     */
    public function test_corrected_to_enum_corrected() {
        $pair = $this->make_pair('item1 , item2, item3', 'item1');

        // We don't have enum analyzer, so we need to re-arrange objects by myself
        $correctstream = $pair->correctstring()->stream->tokens;
        $tokens = array();
        $tokens[] = $correctstream[2];
        $tokens[] = $correctstream[1];
        $tokens[] = $correctstream[4];
        $tokens[] = $correctstream[3];
        $tokens[] = $correctstream[0];

        $stream = new block_formal_langs_token_stream();
        $stream->tokens = $tokens;

        $string = new block_formal_langs_processed_string($this->lang);
        $string->stream = $stream;

        $pair->set_enum_correct_string($string);
        $pair->set_enum_correct_to_correct(array(
           0 => 2,
           1 => 1,
           2 => 4,
           3 => 3,
           4 => 0
        ));

        $index = $pair->map_from_enum_correct_string_to_correct_string(0);
        $this->assertTrue($index == 2, $index);

        $index = $pair->map_from_enum_correct_string_to_correct_string(1);
        $this->assertTrue($index == 1, $index);

        $index = $pair->map_from_enum_correct_string_to_correct_string(2);
        $this->assertTrue($index == 4, $index);

        $index = $pair->map_from_enum_correct_string_to_correct_string(3);
        $this->assertTrue($index == 3, $index);

        $index = $pair->map_from_enum_correct_string_to_correct_string(4);
        $this->assertTrue($index == 0, $index);
    }

    public function test_enum_corrected_to_corrected() {
        $pair = $this->make_pair('item1 , item2, item3', 'item1');

        // We don't have enum analyzer, so we need to re-arrange objects by myself
        $correctstream = $pair->correctstring()->stream->tokens;
        $tokens = array();
        $tokens[] = $correctstream[2];
        $tokens[] = $correctstream[1];
        $tokens[] = $correctstream[4];
        $tokens[] = $correctstream[3];
        $tokens[] = $correctstream[0];

        $stream = new block_formal_langs_token_stream();
        $stream->tokens = $tokens;

        $string = new block_formal_langs_processed_string($this->lang);
        $string->stream = $stream;

        $pair->set_enum_correct_string($string);
        $pair->set_enum_correct_to_correct(array(
            0 => 2,
            1 => 1,
            2 => 4,
            3 => 3,
            4 => 0
        ));

        $index = $pair->map_from_correct_string_to_enum_correct_string(2);
        $this->assertTrue($index == 0, $index);

        $index = $pair->map_from_correct_string_to_enum_correct_string(1);
        $this->assertTrue($index == 1, $index);

        $index = $pair->map_from_correct_string_to_enum_correct_string(4);
        $this->assertTrue($index == 2, $index);

        $index = $pair->map_from_correct_string_to_enum_correct_string(3);
        $this->assertTrue($index == 3, $index);

        $index = $pair->map_from_correct_string_to_enum_correct_string(0);
        $this->assertTrue($index == 4, $index);
    }

    /**
     * Transforms corrected string to a simple string
     * @param qtype_correctwriting_string_pair $pair a pair
     * @return value
     */
    protected function corrected_to_string($pair) {
        $corrected = $pair->correctedstring()->stream->tokens;
        $values = array();
        foreach($corrected as $token) {
            /** @var block_formal_langs_token_base $token */
            $values[] = $token->value();
        }
        return implode(' ', $values);
    }
    /**
     * Returns new string pair
     * @param string $correctstring a correct string
     * @param string $comparedstring a compared string
     * @return qtype_correctwriting_string_pair
     */
    protected function make_pair($correctstring, $comparedstring) {
        $result = new qtype_correctwriting_string_pair($this->lang->create_from_string($correctstring), $this->lang->create_from_string($comparedstring), null);
        return $result;
    }
    /**
     * A simple language to be tested on
     * @var block_formal_langs_language_simple_english
     */
    protected $lang;
    /**
     * A source question
     * @var qtype_correctwriting_question
     */
    protected $question;



}