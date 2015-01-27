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
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

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

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.6, $options);

        $this->assertTrue(count($result) == 1);
    }

    function test_best_string_pairs_1_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_simple_english();

        $comparedstring = $lang->create_from_string('winter mappp');
        $correctstring = $lang->create_from_string('winteri mippp');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.6, $options);

        $this->assertTrue(count($result) == 1);
    }

    //specific errors
    //acos-cos
    function test_best_string_pairs_2() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('cos');
        $correctstring = $lang->create_from_string('acos');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.6, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs)==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
    }

    //acos-cos
    function test_best_string_pairs_21() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('acos');
        $correctstring = $lang->create_from_string('cos');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.6, $options);

        $this->assertTrue(count($result[0]->matches()->matchedpairs)==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
    }

    function test_best_string_pairs_2_all_functions() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('acos asin atan itoa strcmp strspn strcpy strchr calloc printf fputc abs fopen scanf');
        $correctstring = $lang->create_from_string('cos sin tan ltoa strncmp strnspn strncpy strnchr malloc fprintf putc fabs open fscanf');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.6, $options);
      /*  for($i=0; $i<count($result[0]->matches()->matchedpairs); $i++) {
	    $this->assertTrue($result[0]->matches()->matchedpairs[$i]->type==999999);
        }*/
    }

    function test_best_string_pairs_21_all_functions() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('cos sin tan atoi atof #if #else fputc fgets fabs setbuf rand strtok ctime fgetws wctok');
        $correctstring = $lang->create_from_string('cosh sinh tanh atol if else fputs gets abs  time setvbuf srand strtok fgetwc wctol');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.7, $options);
      /*  for($i=0; $i<count($result[0]->matches()->matchedpairs); $i++) {
	    $this->assertTrue($result[0]->matches()->matchedpairs[$i]->type==999999);
        }*/
    }

    // =-==
    function test_best_string_pairs_3() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang =');
        $correctstring = $lang->create_from_string('value ==');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
    }

    // =-==
    function test_best_string_pairs_31() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang ==');
        $correctstring = $lang->create_from_string('value =');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==1);
    }

    function test_best_string_pairs_op_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang ||');
        $correctstring = $lang->create_from_string('value or');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_2() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang !');
        $correctstring = $lang->create_from_string('value not');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_3() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lakkng &&');
        $correctstring = $lang->create_from_string('value and');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_4() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang ^');
        $correctstring = $lang->create_from_string('value xor');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_5() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang &=');
        $correctstring = $lang->create_from_string('value and_eq');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_6() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang |=');
        $correctstring = $lang->create_from_string('value or_eq');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
    function test_best_string_pairs_op_7() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('lang !=');
        $correctstring = $lang->create_from_string('value not_eq');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);
        $this->assertTrue(count($result[0]->matches()->matchedpairs) ==1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->mistakeweight==0);
    }
  

/*
    // char-signed char
    function test_best_string_pairs_4() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('signed char value');
        $correctstring = $lang->create_from_string('char value');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 3);
    }

    // char-signed char
    function test_best_string_pairs_41() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('signed char value');
        $correctstring = $lang->create_from_string('signed signed char value');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 6);
    }
*/
/*
    function test_best_string_pairs_5() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('argv');
        $correctstring = $lang->create_from_string('argc');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
    }
    function test_best_string_pairs_5_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('argv');
        $correctstring = $lang->create_from_string('argc');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 1);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
    }
    function test_best_string_pairs_6() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('\'\"');
        $correctstring = $lang->create_from_string('\'');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 2);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
	$this->assertTrue($result[0]->matches()->matchedpairs[1]->type==999999);
    }
    function test_best_string_pairs_7() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('({<');
        $correctstring = $lang->create_from_string('[');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 3);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
	$this->assertTrue($result[0]->matches()->matchedpairs[1]->type==999999);
	$this->assertTrue($result[0]->matches()->matchedpairs[2]->type==999999);
    }
    function test_best_string_pairs_8() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string(')}>');
        $correctstring = $lang->create_from_string(']');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 3);
	$this->assertTrue($result[0]->matches()->matchedpairs[0]->type==999999);
	$this->assertTrue($result[0]->matches()->matchedpairs[1]->type==999999);
	$this->assertTrue($result[0]->matches()->matchedpairs[2]->type==999999);
    }
    function test_best_string_pairs_9() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('const int * value');
        $correctstring = $lang->create_from_string('int const * value');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result) == 2);
	$this->assertTrue($result[0]->matches()->mistakeweight==0);
	$this->assertTrue($result[1]->matches()->mistakeweight==0);
    }
    function test_best_string_pairs_10() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $lang = new block_formal_langs_language_c_language();

        $comparedstring = $lang->create_from_string('int');
        $correctstring = $lang->create_from_string('signed int');

        $correctstringstream = $correctstring->stream;
        $comparedstringstream  = $comparedstring->stream;

        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, array());

        $result = $stringpair->best_string_pairs($correctstring, $comparedstring, 0.5, $options);

        $this->assertTrue(count($result[0]->matches()->mathedpairs) == 2);
	$this->assertTrue($result[0]->matches()->mistakeweight==0);

    }
*/
}