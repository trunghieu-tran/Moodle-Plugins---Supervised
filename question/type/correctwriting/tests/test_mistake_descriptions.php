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
 * Defines unit-tests for creating a descriptions for mistakes
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/lexical_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

/**
 * Tests creating descriptions for various mistake combinations
 * in various hints. Should be used to test various problems with obtaining data
 * from other combination
 */
class qtype_correctwriting_test_mistake_descriptions extends PHPUnit_Framework_TestCase {

    /**
     * Used question
     * @var qtype_correctwriting_question
     */
    private $question;

    /**
     * Language
     * @var block_formal_langs_language_c_language
     */
    private $language;
    /**
     *  A closure for finding mistakes
     * @var callable calls a find mistake
     */
    private $findmistake;

    /**
     * Inits environment for testing descriptions
     */
    protected function setUp() {
        $this->language = new block_formal_langs_language_c_language();
        $this->question = new qtype_correctwriting_question();
        $this->question->usecase = true;
        $this->question->lexicalerrorthreshold = 0.9;
        $this->question->lexicalerrorweight = 0.1;
        $this->question->usedlanguage = $this->language;
        $this->question->movedmistakeweight = 0.2;
        $this->question->absentmistakeweight = 0.11;
        $this->question->addedmistakeweight = 0.12;
        $this->question->hintgradeborder = 0.75;
        $this->question->maxmistakepercentage = 1.0;
        $this->question->qtype = new qtype_correctwriting();
    }

    /**
     * Finds mistakes
     * @param string $classname name of classes
     * @param array $mistakes list of mistakes
     * @return array descriptions of mistakes
     */
    protected function find_mistakes($classname, $mistakes) {
        $result = array();
        $largeclassname = 'qtype_correctwriting_' .$classname . '_mistake';
        if (count($mistakes)) {
            foreach($mistakes as $mistake) {
                if (is_a($mistake, $largeclassname)) {
                    /** @var qtype_correctwriting_response_mistake $mistake */
                    $result[] = $mistake->get_mistake_message();
                }
            }
        }
        return $result;
    }

    /**
     * Makes pair for analyzer
     * @param string $answer answer
     * @param string $response a response
     * @param array $descriptions a descriptions of array
     * @return qtype_correctwriting_string_pair
     */
    protected function create_pair(
        $answer,
        $response,
        $descriptions = array()
    ) {
        $answer = $this->language->create_from_string($answer);
        $answer->set_descriptions_from_array($descriptions);

        $response = $this->language->create_from_string($response);
        $pair = new qtype_correctwriting_string_pair($answer,$response, array());
        return $pair;
    }

    /**
     * Return list of mistakes for given conditions
     * @param string $answer answer
     * @param string $response response
     * @param array $descriptions descriptions list
     * @param string $enumanswer enum answer
     * @param array $enumcorrecttocorrect list of matches
     * @return array
     */
    public function get_mistakes_for_case(
        $answer,
        $response,
        $descriptions = array(),
        $enumanswer = '',
        $enumcorrecttocorrect = array()
    ) {
        $pair = $this->create_pair(
            $answer,
            $response,
            $descriptions
        );

        $analyzer = new qtype_correctwriting_lexical_analyzer($this->question, $pair, $this->language,false);
        $result = $analyzer->result_pairs();
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        if (core_text::strlen($enumanswer)) {
            $pair->set_enum_correct_string($this->language->create_from_string($enumanswer));
        }
        if (count($enumcorrecttocorrect)) {
            $pair->set_enum_correct_to_correct($enumcorrecttocorrect);
        }
        $analyzer = new qtype_correctwriting_sequence_analyzer($this->question, $pair, $this->language,false);
        $result = $analyzer->result_pairs();
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $result[0];
        return $pair->mistakes();
    }

    /**
     *  Tests generating a description with enum reset nodes, when one ob lexemes is absent
     */
    public function test_with_description_enum_typo_absent() {
        $allmistakes = $this->get_mistakes_for_case(
            'int a = 23 + 54',
            'it    = 54 + 23',
            array(
                'type',
                'identifier',
                'equality',
                'number1',
                'sum',
                'number2',
                'semicolon'
            ),
            'int a = 54 + 23',
            array(3 => 5, 5 => 3)
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be a typo in type'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue($mistakes == array('identifier is missing'), var_export($mistakes, true));
    }

    /**
     *  Tests generating a description with enum reset nodes, when one ob lexemes is absent
     */
    public function test_without_description_enum_typo_absent() {
        $allmistakes = $this->get_mistakes_for_case(
            'int a = 23 + 54',
            'it    = 54 + 23',
            array(
                3 => 'number',
                4 => 'sum',
                5 => 'number'
            ),
            'int a = 54 + 23',
            array(3 => 5, 5 => 3)
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be a typo in "it"'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue($mistakes == array('"a" is missing'), var_export($mistakes, true));
    }

    /**
     * Tests missing separator, when moved a lexeme
     */
    public function test_with_description_enum_moved_missing_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int a = 23 + 54 ;',
            '      =    + 23 inta',
            array(
                0 => 'type',
                1 => 'identifier',
                3 => 'number1',
                4 => 'sum',
                5 => 'number2'
            ),
            'int a = 54 + 23',
            array(3 => 5, 5 => 3)
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there is no separator between type and identifier'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue(in_array('type misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('identifier misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('number2 is missing', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('";" is missing', $mistakes), var_export($mistakes, true));
    }

    /**
     * Tests missing separator, when moved a lexeme, without mistakes
     */
    public function test_without_description_enum_moved_missing_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int a = 23 + 54 ;',
            '      =    + 23 inta',
            array(
                3 => 'number1',
                4 => 'sum',
                5 => 'number2'
            ),
            'int a = 54 + 23',
            array(3 => 5, 5 => 3)
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there is missing separator in "inta"'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue(in_array('"inta" misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('"inta" misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('number2 is missing', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('";" is missing', $mistakes), var_export($mistakes, true));
    }

    /**
     * Tests extra separator, when added lexeme
     */
    public function test_with_description_missing_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int separator = 23 + 54 ;',
            'int se parator odd = + 23 54;',
            array(
                0 => 'type',
                1 => 'identifier',
                3 => 'number1',
                4 => 'sum',
                5 => 'number2'
            )
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be an extra separator inside identifier'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue(in_array('number1 misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('"odd" should not be in response', $mistakes), var_export($mistakes, true));
    }

    /**
     * Tests extra separator, when added lexeme
     */
    public function test_without_description_missing_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int separator = 23 + 54 ;',
            'int se parator odd = + 23 54;',
            array(
                0 => 'type',
            )
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be an extra separator inside "se parator"'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue(in_array('"23" misplaced', $mistakes), var_export($mistakes, true));
        $this->assertTrue(in_array('"odd" should not be in response', $mistakes), var_export($mistakes, true));
    }

    public function test_typo_and_missing_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int separator ;',
            'intseparadir ;',
            array(
                0 => 'type',
            )
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be a typo in "intseparadir"'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue($mistakes == array('type is missing'), var_export($mistakes, true));
    }

    public function test_typo_and_extra_separator() {
        $allmistakes = $this->get_mistakes_for_case(
            'int separator ;',
            'int separa dir ;',
            array(
                0 => 'type',
            )
        );

        $mistakes = $this->find_mistakes('lexical', $allmistakes);
        $this->assertTrue($mistakes == array('there may be a typo in "separa"'), var_export($mistakes, true));
        $mistakes = $this->find_mistakes('sequence', $allmistakes);
        $this->assertTrue($mistakes == array('"dir" should not be in response'), var_export($mistakes, true));

    }
}