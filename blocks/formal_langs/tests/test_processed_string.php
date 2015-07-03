<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines unit-tests for processed_string class
 *
 * For a complete info, see block_formal_langs_processed_string
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');

class block_formal_langs_processed_string_tests extends PHPUnit_Framework_TestCase {
    // Tests for processed_string single_line_string function.
    public function test_single_line_string() {
        $string = new block_formal_langs_processed_string(null);
        $string->string = 'abc';
        $this->assertTrue($string->single_line_string());
        $string->string = "ab\ncd";
        $this->assertFalse($string->single_line_string());
        $string->string = "абвг\nде";
        $this->assertFalse($string->single_line_string());
    }

    public function test_token_has_equal_to_student() {
        $lang = new block_formal_langs_language_simple_english();

        $string = $lang->create_from_string('abc Abc cde fff xxx abc str str ptr ptr');
        $string->set_descriptions_from_array(array(2 => 'first', 3 => 'first', 4 => 'second', 6 => 'only', 8 => 'other1', 9 => 'other2'));
        // Equal values (no description).
        $this->assertTrue($string->token_has_equal_to_student(0));
        $this->assertTrue($string->token_has_equal_to_student(5));
        // Case-insentitive equal values (no description).
        $this->assertFalse($string->token_has_equal_to_student(1));
        // Values equal, but only first token has description.
        $this->assertFalse($string->token_has_equal_to_student(6));
        // Values equal, but only second token has description.
        $this->assertFalse($string->token_has_equal_to_student(7));
        // Values equal, but description are not.
        $this->assertFalse($string->token_has_equal_to_student(8));
        $this->assertFalse($string->token_has_equal_to_student(9));
        // Descriptions are equal.
        $this->assertTrue($string->token_has_equal_to_student(2));
        $this->assertTrue($string->token_has_equal_to_student(3));
    }

    public function test_processed_string_creation() {
        $lang = new block_formal_langs_language_simple_english();

        $string = $lang->create_from_string('word word word');
        $classvalue = get_class($string->string);
        $this->assertTrue($classvalue == 'qtype_poasquestion\\string');
        $this->assertTrue((string)($string->string) == 'word word word');


        $string = $lang->create_from_string( new qtype_poasquestion\string('word word word 2'));
        $classvalue = get_class($string->string);
        $this->assertTrue($classvalue == 'qtype_poasquestion\\string');
        $this->assertTrue((string)($string->string) == 'word word word 2');
    }
}
 ?>