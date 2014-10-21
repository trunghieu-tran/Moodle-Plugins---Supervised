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


/**
 * Defines unit-tests for analyzer
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011  
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_c_language.php');

class qtype_correctwriting_lexical_analyzer_test extends PHPUnit_Framework_TestCase {
	public function test_lexical_analyzer1() {
    $language1 = new block_formal_langs_language_simple_english();

    $question = new qtype_correctwriting_question();
    $question->usecase = true;
    $question->lexicalerrorthreshold = 0.5;
    $question->lexicalerrorweight = 0.1;
    $question->usedlanguage = $language1;
    $question->movedmistakeweight = 0.1;
    $question->absentmistakeweight = 0.11;
    $question->addedmistakeweight = 0.12;
    $question->hintgradeborder = 0.75;
    $question->maxmistakepercentage = 0.95;
    $question->qtype = new qtype_correctwriting();

	$bestmatchpair1 = new qtype_correctwriting_string_pair();
	$bestmatchpair1->correctstring = $language1->create_from_string('abc cde');
	$bestmatchpair1->comparedstring = $language1->create_from_string('abc cde');

	$analyzer1 = new qtype_correctwriting_lexical_analyzer($question, $bestmatchpair1, $language1);
	$this->assertTrue(count($analyzer1->bestmatchstring->matches)==2);
	}
	
	public function test_lexical_analyzer2() {
	$language2 = new block_formal_langs_language_c_language();
	
    $question = new qtype_correctwriting_question();
    $question->usecase = true;
    $question->lexicalerrorthreshold = 0.5;
    $question->lexicalerrorweight = 0.1;
    $question->usedlanguage = $language2;
    $question->movedmistakeweight = 0.1;
    $question->absentmistakeweight = 0.11;
    $question->addedmistakeweight = 0.12;
    $question->hintgradeborder = 0.75;
    $question->maxmistakepercentage = 0.95;
    $question->qtype = new qtype_correctwriting();

	$bestmatchpair1 = new qtype_correctwriting_string_pair();
	$bestmatchpair1->correctstring = $language2->create_from_string('abc dpoc');
	$bestmatchpair1->comparedstring = $language2->create_from_string('abc naklc');

	$analyzer2 = new qtype_correctwriting_lexical_analyzer($question, $bestmatchpair1, $language2);
	$this->assertTrue(count($analyzer2->bestmatchstring->matches)==1);
	}
	}