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
}