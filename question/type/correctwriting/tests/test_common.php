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
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');

class qtype_correctwriting_common_test extends PHPUnit_Framework_TestCase {

    // пропуск, перемещение, добавление лексемы
    public function test_drop_move_addition_lexemes_001() {
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
        // Input data.
        $answers = array((object)array('id' => 1, 'answer' => 'a data template', 'fraction' => 1.0));
        $question->answers = $answers;
        $state = $question->grade_response(array('answer' => 'r template a'));
        $this->assertEquals(count($question->matchedresults->mistakes()),3);
        $this->assertEquals(get_class($question->matchedresults->mistakes()[0]),'qtype_correctwriting_lexeme_moved_mistake');
        $this->assertEquals(get_class($question->matchedresults->mistakes()[1]),'qtype_correctwriting_lexeme_absent_mistake');
        $this->assertEquals(get_class($question->matchedresults->mistakes()[2]),'qtype_correctwriting_lexeme_added_mistake');
    }
}
