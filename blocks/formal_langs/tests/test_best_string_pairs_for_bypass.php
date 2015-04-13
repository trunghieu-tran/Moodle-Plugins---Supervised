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
 * Defines unit-tests for block_formal_langs_token_stream::best_string_pairs
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2012
 * @author Oleg Sychev, Mamontov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');

class blocks_formal_langs_token_base_best_string_pairs extends UnitTestCase {

    function test_best_string_pairs_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('winter map');
        $correctstring = $lang->create_from_string('winte map');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());
        $result = $stringpair->best_string_pairs_for_bypass($correctstring, $comparedstring, 1.0, $options);
        $this->assertTrue(count($result) == 1);
	//$this->assetTrue(count($result[0]->matches()->matchedpairs)==1);
    }
    function test_best_string_pairs_2() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('winter map');
        $correctstring = $lang->create_from_string('winte map');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs_for_bypass($correctstring, $comparedstring, 0, $options);

        $this->assertTrue(count($result) == 1);
    }
    function test_best_string_pairs_3() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('winter map');
        $correctstring = $lang->create_from_string('winter map');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs_for_bypass($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result) == 1);
    }
    function test_best_string_pairs_4() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=false;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('winter map');
        $correctstring = $lang->create_from_string('winte map');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs_for_bypass($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result) == 1);
    }
    function test_best_string_pairs_5() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=false;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('win mappping');
        $correctstring = $lang->create_from_string('winter map');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs_for_bypass($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result) == 1);
    }
}