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
 * Defines unit-tests for token_base class
 *
 * For a complete info, see block_formal_langs_token_base
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

 /**
  * This class contains the test cases for the is_same() function of token_base.
  */
class block_formal_langs_token_base_is_same extends PHPUnit_Framework_TestCase {
    // Case, when a tokens are totally equal
    public function test_equal_tokens() {
        $answer = new block_formal_langs_token_base(null, 'type', 'value', true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'value', false, null);
        $this->assertTrue($answer->is_same($response), 'Tokens with equal types and values are detected as non-equal!');
    }
    // Case, when tokens are totally equal and both values is null
    public function test_equal_tokens_is_null() {
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', null, false, null);
        $this->assertTrue($answer->is_same($response), 'Tokens with equal types and null values are detected as non-equal!');
    }
    // Case, when tokens are not equal, because values are different
    public function test_inequal_values() {
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'test', false, null);
        $this->assertFalse($answer->is_same($response), 'Tokens with inequal values are detected as equal!');
    }
    // Case, when tokens are not equal, because types are different
    public function test_inequal_types() {
        $answer=new block_formal_langs_token_base(null, 'type', 'test', true, null);
        $response=new block_formal_langs_token_base(null, 'type2', 'test', false, null);
        $this->assertFalse($answer->is_same($response), 'Tokens with inequal types are detected as equal');
    }
}
