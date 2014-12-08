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
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

 /**
  * This class contains the test cases for the is_same() function of token_base.
  */
class block_formal_langs_token_base_is_same extends PHPUnit_Framework_TestCase {
    // Case, when a tokens are totally equal
    public function test_equal_tokens() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = true;
        $answer = new block_formal_langs_token_base(null, 'type', 'value', true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'value', false, null);
        $this->assertTrue($answer->is_same($response, $options), 'Tokens with equal types and values are detected as non-equal!');
    }
    // Case, when tokens are totally equal and both values is null
    public function test_equal_tokens_is_null() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = true;
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', null, false, null);
        $this->assertTrue($answer->is_same($response, $options), 'Tokens with equal types and null values are detected as non-equal!');
    }
    // Case, when tokens are not equal, because values are different
    public function test_inequal_values() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = true;
        $answer = new block_formal_langs_token_base(null, 'type', null, true, null);
        $response = new block_formal_langs_token_base(null, 'type', 'test', false, null);
        $this->assertFalse($answer->is_same($response, $options), 'Tokens with inequal values are detected as equal!');
    }
    // Case, when tokens are not equal, because types are different
    public function test_inequal_types() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = true;
        $answer=new block_formal_langs_token_base(null, 'type', 'test', true, null);
        $response=new block_formal_langs_token_base(null, 'type2', 'test', false, null);
        $this->assertFalse($answer->is_same($response, $options), 'Tokens with inequal types are detected as equal');
    }
}

/**
 * This class is used for testing token stream
 */
class block_formal_langs_token_stream_test extends PHPUnit_Framework_TestCase {

    // Test cloning facilities (see issue 227 for explanations)
    public function test_clone() {
        $lang = new block_formal_langs_language_c_language();
        $stream = $lang->create_from_string('id1 id2');
        $tokenstream1  = $stream->stream;
        $tokenstream2  = clone $tokenstream1;
        // Trying to change first stream, second stream should become different
        /** @var block_formal_langs_token_base $token1 */
        /** @var block_formal_langs_token_base $token2 */
        $token1 = $tokenstream1->tokens[1];
        $token2 = $tokenstream2->tokens[1];
        $token1->set_token_index(22);
        $this->assertFalse($token1->token_index() == $token2->token_index());
    }
}

