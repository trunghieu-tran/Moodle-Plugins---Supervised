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
 * Defines unit-tests for printf language
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/language_printf_language.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');



/**
 * Tests a simple english language
 */
class block_formal_langs_printf_language_test extends PHPUnit_Framework_TestCase {
    /**
     * Utilities for testing
     * @var block_formal_langs_language_test_utils
     */
    protected $utils;

    public function __construct() {
        $this->utils = new block_formal_langs_language_test_utils('block_formal_langs_language_printf_language', $this);
    }

    /**
     * Tests a simple test with tokens
     */
    public function test_simple() {
        $str = 'ABC"ABC"';
        $lang = new block_formal_langs_language_printf_language();
        $processedstring = $lang->create_from_string($str);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 4, var_export($result, true));
        $this->assertTrue($result[0]->type() == 'TEXT', $result[0]->type());
        $this->assertTrue($result[0]->value() == 'ABC');
        $this->assertTrue($result[0]->position()->colstart() == 0);
        $this->assertTrue($result[0]->position()->colend() == 2);
        $this->assertTrue($result[1]->type() == 'QUOTE');
        $this->assertTrue($result[1]->value() == '"');
        $this->assertTrue($result[1]->position()->colstart() == 3);
        $this->assertTrue($result[1]->position()->colend() == 3);
        $this->assertTrue($result[2]->type() == 'TEXT');
        $this->assertTrue($result[2]->value() == 'ABC');
        $this->assertTrue($result[2]->position()->colstart() == 4);
        $this->assertTrue($result[2]->position()->colend() == 6);
        $this->assertTrue($result[3]->type() == 'QUOTE');
        $this->assertTrue($result[3]->value() == '"');
        $this->assertTrue($result[3]->position()->colstart() == 7);
        $this->assertTrue($result[3]->position()->colend() == 7);
    }

    /**
     * Tests unmatched quote
     */
    public function test_unmatched_quote() {
        $str = 'ABC"ABC';
        $lang = new block_formal_langs_language_printf_language();
        $processedstring = $lang->create_from_string($str);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 3, var_export($result, true));
        $this->assertTrue($result[0]->type() == 'TEXT', $result[0]->type());
        $this->assertTrue($result[0]->value() == 'ABC');
        $this->assertTrue($result[0]->position()->colstart() == 0);
        $this->assertTrue($result[0]->position()->colend() == 2);
        $this->assertTrue($result[1]->type() == 'QUOTE');
        $this->assertTrue($result[1]->value() == '"');
        $this->assertTrue($result[1]->position()->colstart() == 3);
        $this->assertTrue($result[1]->position()->colend() == 3);
        $this->assertTrue($result[2]->type() == 'TEXT');
        $this->assertTrue($result[2]->value() == 'ABC');
        $this->assertTrue($result[2]->position()->colstart() == 4);
        $this->assertTrue($result[2]->position()->colend() == 6);
    }

    /**
     * Tests unmatched percent
     */
    public function test_unmatched_percent() {
        $str = '"%"';
        $lang = new block_formal_langs_language_printf_language();
        $processedstring = $lang->create_from_string($str);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 3, var_export($result, true));
        $this->assertTrue($result[0]->type() == 'QUOTE', $result[0]->type());
        $this->assertTrue($result[0]->value() == '"');
        $this->assertTrue($result[0]->position()->colstart() == 0);
        $this->assertTrue($result[0]->position()->colend() == 0);
        $this->assertTrue($result[1]->type() == 'TEXT', $result[1]->type());
        $this->assertTrue($result[1]->value() == '%');
        $this->assertTrue($result[1]->position()->colstart() == 1);
        $this->assertTrue($result[1]->position()->colend() == 1);
        $this->assertTrue($result[2]->type() == 'QUOTE', $result[2]->type());
        $this->assertTrue($result[2]->value() == '"');
        $this->assertTrue($result[2]->position()->colstart() == 2);
        $this->assertTrue($result[2]->position()->colend() == 2);
    }


    public function test_common_with_specifier() {
        $str = '"asd%d"';
        $lang = new block_formal_langs_language_printf_language();
        $processedstring = $lang->create_from_string($str);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 4, var_export($result, true));
        $this->assertTrue($result[0]->type() == 'QUOTE', $result[0]->type());
        $this->assertTrue($result[0]->value() == '"');
        $this->assertTrue($result[0]->position()->colstart() == 0);
        $this->assertTrue($result[0]->position()->colend() == 0);
        $this->assertTrue($result[1]->type() == 'TEXT', $result[1]->type());
        $this->assertTrue($result[1]->value() == 'asd');
        $this->assertTrue($result[1]->position()->colstart() == 1);
        $this->assertTrue($result[1]->position()->colend() == 3);
        $this->assertTrue($result[2]->type() == 'SPECIFIER', $result[1]->type());
        $this->assertTrue($result[2]->value() == '%d');
        $this->assertTrue($result[2]->position()->colstart() == 4);
        $this->assertTrue($result[2]->position()->colend() == 5);
        $this->assertTrue($result[3]->type() == 'QUOTE', $result[2]->type());
        $this->assertTrue($result[3]->value() == '"');
        $this->assertTrue($result[3]->position()->colstart() == 6);
        $this->assertTrue($result[3]->position()->colend() ==6);
    }

    /**
     * Tests escaping data
     */
    public function test_escaping() {
        $str = array('0101' => 'A', 'x41' => 'A', '-' => '\\-',
                    );
        $esc = array('\'' => '\'', '"' => '"' , 'a' => "\a", 'b' => "\b", 'f' => "\f",
                    'n'  => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v", '\\' => '\\',
                    '?'  => '?');
        $str = array_merge($str, $esc);
        $lang = new block_formal_langs_language_printf_language();
        foreach($str as $easc => $eval) {
            $pstring = '"\\' . $easc . '"';
            $processedstring = $lang->create_from_string($pstring);
            $result = $processedstring->stream->tokens;
            $this->assertTrue(count($result) == 3, $pstring . ' leads to ' . var_export($result, true));
            $this->assertTrue($result[1]->type() == 'TEXT', $result[1]->type());
            $this->assertTrue($result[1]->unescapedvalue() == $eval, $pstring . ' leads to '  . $result[1]->value());
        }
    }

    public function test_escaped_as_separate_lexemes() {
        $string = '"\\"asd"';
        $lang = new block_formal_langs_language_printf_language();
        $processedstring = $lang->create_from_string($string);
        $result = $processedstring->stream->tokens;
        $this->assertTrue(count($result) == 4,  count($result) . ' tokens have returned');
    }

    /**
     * Tests all specifier
     */
    public function test_all_specifiers() {
        $lang = new block_formal_langs_language_printf_language();
        $flags = array('', '-', '+', '#', '0');
        $width = array('', '7', '77', '*');
        $precision = array('', '.7', '.77', '.*');
        $length = array('', 'hh' ,'h' , 'l', 'll', 'j', 'z', 't', 'L');
        $specifiers = array('d', 'i', 'u', 'o', 'x', 'X', 'f', 'F', 'e', 'E', 'g', 'G', 'a', 'A', 'c', 's', 'p', 'n');
        foreach($flags as $flag) {
            foreach($width as $widthspec) {
                foreach($precision as $precspec) {
                    foreach($length as $lengthspec) {
                        foreach($specifiers as $spec) {
                            $specifier = '%' . $flag . $widthspec . $precspec . $lengthspec . $spec;
                            $pstring = '"' . $specifier .'"';
                            $processedstring = $lang->create_from_string($pstring);
                            $result = $processedstring->stream->tokens;
                            $this->assertTrue(count($result) == 3, $pstring . ' leads to ' . var_export($result, true));
                            $this->assertTrue($result[1]->type() == 'SPECIFIER', $result[1]->type());
                            $this->assertTrue($result[1]->value() == $specifier, $pstring . ' leads to '  . $result[1]->value());
                        }
                    }
                }
            }
        }
    }

}