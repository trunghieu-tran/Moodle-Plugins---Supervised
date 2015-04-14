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

global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/enum_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/enum_catcher.php');
require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/question/type/correctwriting/processed_string.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_cpp_parseable_language.php');
require_once($CFG->dirroot.'/question/type/poasquestion/poasquestion_string.php');

class qtype_correctwriting_enum_catcher_test extends PHPUnit_Framework_TestCase {

    public function testdefinition() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int k = j / h + t + o - r ; bool g = kill = live ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(3,5);
        $expected_result[0][] = array(7,7);
        $expected_result[0][] = array(9,9);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of definitions.');
    }
    public function testsequence_of_mod() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j % h % t % o % r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of mod');

    }
    public function testsequence_of_plus_with_multiple() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = ( j + h + t + o + r ) * f ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array()];
        $expected_result[1][] = array(2,12);
        $expected_result[1][] = array(14,14);
        $expected_result[0][] = array(3,3);
        $expected_result[0][] = array(5,5);
        $expected_result[0][] = array(7,7);
        $expected_result[0][] = array(9,9);
        $expected_result[0][] = array(11,11);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of plus with multiple');
    }
    public function testsequence_of_plus_and_multiple() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = ( j * h + t * o + r ) * f ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array(),array()];
        $expected_result[1][] = array(2,12);
        $expected_result[1][] = array(14,14);
        $expected_result[0][] = array(3,5);
        $expected_result[0][] = array(7,9);
        $expected_result[0][] = array(11,11);
        $expected_result[3][] = array(7,7);
        $expected_result[3][] = array(9,9);
        $expected_result[2][] = array(3,3);
        $expected_result[2][] = array(5,5);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of plus and multiple');
    }
    public function testsequence_of_plus_and_multiple_with_type_casting() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = (int)( j * h + t * o + r ) * f ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array(),array()];
        $expected_result[1][] = array(2,15);
        $expected_result[1][] = array(17,17);
        $expected_result[0][] = array(6,8);
        $expected_result[0][] = array(10,12);
        $expected_result[0][] = array(14,14);
        $expected_result[3][] = array(10,10);
        $expected_result[3][] = array(12,12);
        $expected_result[2][] = array(6,6);
        $expected_result[2][] = array(8,8);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of plus and multiple with type casting.');
    }
    public function testsequence_of_plus_and_multiple_mod_and_div() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = ( j * h + t / o / r ) % f % g ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array(),array()];
        $expected_result[3][] = array(14,14);
        $expected_result[3][] = array(16,16);
        $expected_result[0][] = array(3,5);
        $expected_result[0][] = array(7,11);
        $expected_result[2][] = array(9,9);
        $expected_result[2][] = array(11,11);
        $expected_result[1][] = array(3,3);
        $expected_result[1][] = array(5,5);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of plus and multiple, mod and div!');
    }
    public function testsequence_of_sub() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j - h - t - o - r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of sub');
    }
    public function testsequence_of_div() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j / h / t / o / r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of div');
    }
    public function testsequence_of_plus() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j + h + t + o + r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence plus');
    }
    public function testsequence_of_mul() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j * h * t * o * r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of mul');
    }
    public function testsequence_of_assign() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j = h = t = o = r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,6);
        $expected_result[0][] = array(8,8);
        $expected_result[0][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Sequence of assign');
    }
    public function testbit_operations() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j & h | t ^ o & r ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(2,4);
        $expected_result[0][] = array(6,6);
        $expected_result[2][] = array(2,2);
        $expected_result[2][] = array(4,4);
        $expected_result[3][] = array(2,6);
        $expected_result[3][] = array(8,8);
        $expected_result[1][] = array(2,8);
        $expected_result[1][] = array(10,10);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Bit operators!');
    }
    public function testdefinition_variables_and_pointers() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int * k , j , *h;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(1,2);
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,7);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition of variables and pointers!');
    }
    public function testdefinition_variables_and_pointers_1() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int * k , j , *h=z=e;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(1,2);
        $expected_result[0][] = array(4,4);
        $expected_result[0][] = array(6,11);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition of variables and pointers!');
    }
    public function testdefinition_variables() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int k , j , h;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(1,1);
        $expected_result[0][] = array(3,3);
        $expected_result[0][] = array(5,5);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition variales!');
    }
    public function testdefinition_variables_with_assign() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int k=2 , j=u , h;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(1,3);
        $expected_result[0][] = array(5,7);
        $expected_result[0][] = array(9,9);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition variables with assign');
    }
    public function testdefinition_variables_with_assign_heavy() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int k=2 , j=k , h;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(1,3);
        $expected_result[0][] = array(5,7);
        $expected_result[0][] = array(9,9);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition variables with assign, heavy');
    }
    public function testdefinition_array() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'int k [ 5 ] = { 1 , 2 , 4 , 3 , 0 } ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [];
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition array');
    }
    public function testdefinition_enum() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'enum types { Int , Char , Double , Float } ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(3,3);
        $expected_result[0][] = array(5,5);
        $expected_result[0][] = array(7,7);
        $expected_result[0][] = array(9,9);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition enum');
    }
    public function testdefinition_enum_with_assign() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'enum suit { diamond = 1 , heart } ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(3,5);
        $expected_result[0][] = array(7,7);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition enum with assign');
    }
    public function testdefinition_struct() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'struct suit { int a ; char * b ; float k ; } ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array()];
        $expected_result[0][] = array(3,5);
        $expected_result[0][] = array(6,9);
        $expected_result[0][] = array(10,12);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition struct');
    }
    public function testlogical_eq_ne_and_or() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j == h && t != o && r  || f ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array(),array()];
        $expected_result[0][] = array(2,10);
        $expected_result[0][] = array(12,12);
        $expected_result[1][] = array(2,4);
        $expected_result[1][] = array(6,8);
        $expected_result[1][] = array(10,10);
        $expected_result[2][] = array(2,2);
        $expected_result[2][] = array(4,4);
        $expected_result[3][] = array(6,6);
        $expected_result[3][] = array(8,8);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Definition enum');
    }
    public function testlogical_eq_ne_and_or_bracket() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j == h && (t != o && r  || f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array(),array(),array()];
        $expected_result[1][] = array(2,4);
        $expected_result[1][] = array(6,14);
        $expected_result[4][] = array(7,7);
        $expected_result[4][] = array(9,9);
        $expected_result[3][] = array(2,2);
        $expected_result[3][] = array(4,4);
        $expected_result[2][] = array(7,9);
        $expected_result[2][] = array(11,11);
        $expected_result[0][] = array(7,11);
        $expected_result[0][] = array(13,13);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Logical_operations and brackets');
    }
    public function test_use_shortform_plus() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k += j + h - (3 - o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[2][] = array(11,11);
        $expected_result[2][] = array(13,13);
        $expected_result[1][] = array(7,9);
        $expected_result[1][] = array(11,13);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of plus');
    }
    public function test_use_shortform_sub() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k -= j + h - (3 - o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[2][] = array(11,11);
        $expected_result[2][] = array(13,13);
        $expected_result[1][] = array(7,9);
        $expected_result[1][] = array(11,13);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of sub');
    }
    public function test_use_shortform_mul() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k *= j + h - (3 - o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[2][] = array(11,11);
        $expected_result[2][] = array(13,13);
        $expected_result[1][] = array(7,9);
        $expected_result[1][] = array(11,13);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of mul');
    }
    public function test_use_shortform_div() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k /= j + h - (3 - o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[2][] = array(11,11);
        $expected_result[2][] = array(13,13);
        $expected_result[1][] = array(7,9);
        $expected_result[1][] = array(11,13);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,4);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of div');
    }
    public function test_use_shortform_inc() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j + h++ - (3 - ++o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[2][] = array(13,13);
        $expected_result[2][] = array(15,15);
        $expected_result[1][] = array(8,11);
        $expected_result[1][] = array(13,15);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,5);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of inc');
    }
    public function test_use_shortform_dec() {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $string = 'k = j + h-- - (3 - --o + r  * f );';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $tree = $correct->syntaxtree;
        $temp = new qtype_correctwriting_enum_catcher($tree);
        $expected_result = [array(),array(),array()];
        $expected_result[1][] = array(8,11);
        $expected_result[1][] = array(13,15);
        $expected_result[0][] = array(2,2);
        $expected_result[0][] = array(4,5);
        $expected_result[2][] = array(13,13);
        $expected_result[2][] = array(15,15);
        $this->assertEquals($expected_result, $temp->getEnums(), 'Error enumeration catcher found!Shortform of dec');
    }
}
