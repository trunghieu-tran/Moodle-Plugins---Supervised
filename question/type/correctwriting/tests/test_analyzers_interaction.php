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

require_once(dirname(__FILE__) . '/../../../../config.php'); 
global $CFG;
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/question/type/edit_question_form.php');
require_once($CFG->dirroot.'/question/engine/tests/helpers.php');
require_once($CFG->dirroot.'/question/type/correctwriting/edit_correctwriting_form.php');
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_cpp_parseable_language.php');

class qtype_correctwriting_analyzers_interaction_test extends advanced_testcase {

    // Test drop, move and additional lexemes. Only sequence analyzer.
    public function test_drop_move_addition_lexemes_sequence() {
        $language = new block_formal_langs_language_simple_english();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 0;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'r template a'));
        $this->assertEquals(3, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexeme_added_mistake', get_class($question->matchedresults->mistakes()[2]));
    }
    // Test typo, move and additional lexemes. Only lexical analyzer.
    public function test_typo_drop_addition_lexemes_lexical() {
        $language = new block_formal_langs_language_simple_english();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 1;
        $question->isenumanalyzerenabled = 0;
        $question->issequenceanalyzerenabled = 0;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'r date tenplate function'));
        $this->assertEquals(4, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_added_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[3]));
    }
    // Test typo, drop, move and additional lexemes. Lexical and sequence analyzers.
    public function test_typo_drop_moved_addition_lexemes_lexical_sequence() {
        $language = new block_formal_langs_language_simple_english();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 1;
        $question->isenumanalyzerenabled = 0;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'a data template string', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'date tenplate function a'));
        $this->assertEquals(4, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[3]));
    }
    // Test typo, drop, move and additional lexemes in enumerations. Enumeration and sequence analyzers.
    public function test_typo_drop_moved_addition_lexemes_in_enumerations_enum_sequence() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int i,j,k, hash, fraction;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int k,je,fraction;f'));
        $this->assertEquals(7, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[3]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[4]));
        $this->assertEquals('qtype_correctwriting_lexeme_added_mistake', get_class($question->matchedresults->mistakes()[5]));
        $this->assertEquals('qtype_correctwriting_lexeme_added_mistake', get_class($question->matchedresults->mistakes()[6]));
    }
    // Test typo, drop, move and additional lexemes in enumerations. Lexical, enumeration and sequence analyzers.
    public function test_typo_drop_moved_addition_lexemes_in_enumerations_lexical_enum_sequence() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 1;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int i,j,k, hash, fraction;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int k,je,fruction;f'));
        $this->assertEquals(8, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[3]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[4]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[5]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[6]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[7]));
    }
    // Test typo, drop, move and additional lexemes. Enumeration and sequence analyzers.
    public function test_typo_drop_moved_addition_lexemes_with_enumerations_enum_sequence() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int i,j,k, hash, fraction; j = k / fraction - hash;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int k,j,i; f = j / fraction - ;'));
        $this->assertEquals(8, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[3]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[4]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[5]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[6]));
        $this->assertEquals('qtype_correctwriting_lexeme_added_mistake', get_class($question->matchedresults->mistakes()[7]));
    }
    // Test typo, drop, move and additional lexemes. Lexical, enumeration and sequence analyzers.
    public function test_typo_drop_moved_addition_lexemes_with_enumerations_lexical_enum_sequence() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 1;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int i,j,k, hash, fraction; j = k / fraction - hash;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'imt k,j,i; f = j / fruction - ;'));
        $this->assertEquals(14, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[1]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[2]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[3]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[4]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[5]));
        $this->assertEquals('qtype_correctwriting_lexical_mistake', get_class($question->matchedresults->mistakes()[6]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[7]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[8]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[9]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[10]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[11]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[12]));
        $this->assertEquals('qtype_correctwriting_lexeme_absent_mistake', get_class($question->matchedresults->mistakes()[13]));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher struct definition rule test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_struct() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'struct  MyNiceStructure { int  FirstField;  long Padding;  char  SmallPart; } DefaultValue;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'struct  MyNiceStructure { long Padding;  int char  SmallPart; FirstField;} DefaultValue;'));
        $this->assertEquals(1, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[0]));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher enumeration definition rule test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_enum() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'enum MyNiceEnumeration { FirstField,  Padding = 1,SmallPart};', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'enum MyNiceEnumeration { Padding, SmallPart = 1, FirstField};'));
        $this->assertEquals(2, count($question->matchedresults->mistakes()),2);
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[1]));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher arithmetic operations rules test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_arithmetic() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'float k = ( a * b + c / d / e - f - g ) % t % m ;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'float k = ( c / e / d +  b * a - g - f ) % m % t ;'));
        $this->assertEquals(0, count($question->matchedresults->mistakes()));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher logi operations rules test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_logic() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 't == a && b || c && k != true;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'k != true && c || b && a == t;'));
        $this->assertEquals(0, count($question->matchedresults->mistakes()));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher binary operations rules test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_binary() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 't & a & b | c & k ^ r;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'r ^ k & c | b & a &  t;'));
        $this->assertEquals(0, count($question->matchedresults->mistakes()));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher assign list rule test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_assign() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int t = a = b = c = k = 5 ;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int t = b = c = k = a = 5 ;'));
        $this->assertEquals(0, count($question->matchedresults->mistakes()));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher array definition test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_array() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int t[3] = { 1 , 2 , 4 } ;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int t[3] = { 2 , 4 , 1 } ;'));
        $this->assertEquals(2, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[0]));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[1]));
    }
    // Test move lexemes. Enumeration and sequence analyzers. Enumeration catcher definition list rules test.
    public function test_moved_lexemes_with_enumerations_enum_sequence_catcher_defenition() {
        $language = new block_formal_langs_language_cpp_parseable_language();
        $question = new qtype_correctwriting_question();
        $question->usecase = true;
        $question->lexicalerrorthreshold = 3000;
        $question->lexicalerrorweight = 0.1;
        $question->usedlanguage = $language;
        $question->movedmistakeweight = 0.1;
        $question->absentmistakeweight = 0.11;
        $question->addedmistakeweight = 0.12;
        $question->hintgradeborder = 0.75;
        $question->maxmistakepercentage = 0.95;
        $question->qtype = new qtype_correctwriting();
        $question->islexicalanalyzerenabled = 0;
        $question->isenumanalyzerenabled = 1;
        $question->issequenceanalyzerenabled = 1;
        $question->issyntaxanalyzerenabled = 0;
        $answers = array((object)array('id' => 1, 'answer' => 'int t, *h , k ;', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'int * t, k , h ;'));
        $this->assertEquals(1, count($question->matchedresults->mistakes()));
        $this->assertEquals('qtype_correctwriting_lexeme_moved_mistake', get_class($question->matchedresults->mistakes()[0]));
    }
}