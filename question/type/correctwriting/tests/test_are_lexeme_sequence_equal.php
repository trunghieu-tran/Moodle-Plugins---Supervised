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
 * Defines unit-tests for function qtype_correctwriting::are_lexeme_sequence_equal
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');


/**
 * @class qtype_correctwriting_are_lexeme_sequence_equal_test_utils
 * Utilities for testing  @see  qtype_correctwriting_question::are_lexeme_sequence_equal
 */
class qtype_correctwriting_are_lexeme_sequence_equal_test_utils {

    /**
     * Inner test for assertions
     * @var qtype_correctwriting_are_lexeme_sequence_equal_test
     */
    protected $test;

    /**
     * Creates an utils with test
     * @param qtype_correctwriting_are_lexeme_sequence_equal_test $test a test
     */
    public function __construct($test) {
        $this->test = $test;
    }
    /**
     * Tests function, using specified information
     * @param boolean $usecase question use case option
     * @param boolean $equals  whether sequences should be eqyual
     * @param string $string1 first string
     * @param string $string2 second string
     */
    public function test_with_english($usecase, $equals, $string1, $string2) {
        $q = new qtype_correctwriting_question();
        $q->usecase = $usecase;

        $l = new block_formal_langs_language_simple_english();
        $s1 = $l->create_from_string($string1);
        $s2 = $l->create_from_string($string2);
        $s = new block_formal_langs_string_pair($s1, $s2, array());
        $this->test->assertTrue($q->are_lexeme_sequences_equal($s) == $equals);
    }
}

/**
 * This class contains the test cases for @see  qtype_correctwriting_question::are_lexeme_sequence_equal.
 */
class qtype_correctwriting_are_lexeme_sequence_equal_test extends PHPUnit_Framework_TestCase {

    /**
     *  Test equal sensitive with insensitiive case
     */
    public function test_equal_insensitive() {
        $t = new qtype_correctwriting_are_lexeme_sequence_equal_test_utils($this);
        $t->test_with_english(false, true, 'Those sequences are equal', 'those sequences are equal');
        $q = new qtype_correctwriting_question();
        $q->usecase = false;
    }

    /**
     *  Test non-equal sensitive with insensitiive case
     */
    public function test_non_equal_insensitive() {
        $t = new qtype_correctwriting_are_lexeme_sequence_equal_test_utils($this);
        $t->test_with_english(false, false, 'Those sequences are equal', 'those sequences aren\'t equal');
        $q = new qtype_correctwriting_question();
        $q->usecase = false;
    }

    /**
     *  Test equal sensitive with sensitiive case
     */
    public function test_equal_sensitive() {
        $t = new qtype_correctwriting_are_lexeme_sequence_equal_test_utils($this);
        $t->test_with_english(true, true, 'those sequences are equal', 'those sequences are equal');
        $q = new qtype_correctwriting_question();
        $q->usecase = false;
    }

    /**
     *  Test non-equal sensitive with sensitiive case
     */
    public function test_non_equal_sensitive() {
        $t = new qtype_correctwriting_are_lexeme_sequence_equal_test_utils($this);
        $t->test_with_english(true, false, 'Those sequences are equal', 'those sequences are equal');
        $q = new qtype_correctwriting_question();
        $q->usecase = false;
    }

}