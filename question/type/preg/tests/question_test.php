<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for question/type/preg/question.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/question.php');

class qtype_preg_question_test extends PHPUnit_Framework_TestCase {

    protected $testquestion;
    protected $subexprquestion;

    /**
     * Creates a number of questions for testing.
     */
    public function setUp() {

        // Normal question with hinting on and several answers with different grades.
        $regular = new qtype_preg_question;
        $regular->usecase = true;
        $regular->correctanswer = 'Do cats eat bats?';
        $regular->exactmatch = true;
        $regular->usecharhint = true;
        $regular->uselexemhint = true;
        $regular->penalty = 0.1;
        $regular->charhintpenalty = 0.2;
        $regular->hintgradeborder = 0.6;
        $regular->engine = 'fa_matcher';
        $regular->notation = 'native';

        // Correct answer.
        $answer100 = new stdClass();
        $answer100->id = 100;
        $answer100->answer = 'Do ([cbr]at(s|)) eat ([cbr]at\2)\?';
        $answer100->fraction = 1;
        $answer100->feedback = 'Predator is {$1}. The prey is {$3}.';

        // Good answer.
        $answer90 = new stdClass();
        $answer90->id = 101;
        $answer90->answer = 'Do ([cbr]ats?) eat ([cbr]ats?)\?';
        $answer90->fraction = 0.9;
        $answer90->feedback = 'Predator is {$1}. The prey is {$2}. But mind the numbers!';

        // Worse answer.
        $answer50 = new stdClass();
        $answer50->id = 102;
        $answer50->answer = '[cbr]ats? eat [cbr]ats?';
        $answer50->fraction = 0.5;
        $answer50->feedback = 'What should start a question?';

        // Totally bad - any single word.
        $answer0 = new stdClass();
        $answer0->id = 103;
        $answer0->answer = '^\w+$';
        $answer0->fraction = 0;
        $answer0->feedback = 'Think harder!!!';

        // Special answer with second subexpression that it's possible to not match while matching the whole string.
        $answer00 = new stdClass();
        $answer00->id = 104;
        $answer00->answer = 'Do ((dogs)|frogs|mice) eat (dogs|frogs|mice)\?';
        $answer00->fraction = 0;
        $answer00->feedback = 'Oh my, that\'s another story... {$1}';

        $regular->answers = array(100=>$answer100, 101=>$answer90, 102=>$answer50, 103=>$answer0, 104=>$answer00);
        $this->testquestion = $regular;

        // Special question to test subexpression capturing and inserting.
        $subexpr = new qtype_preg_question;
        $subexpr->usecase = true;
        $subexpr->correctanswer = 'cdefgh';
        $subexpr->exactmatch = true;
        $subexpr->usecharhint = true;
        $subexpr->penalty = 0.1;
        $subexpr->charhintpenalty = 0.2;
        $subexpr->hintgradeborder = 0.6;
        $subexpr->engine = 'fa_matcher';
        $subexpr->notation = 'native';

        // Answer where it is possible to not match last subexpression.
        $answer1 = new stdClass;
        $answer1->id = 200;
        $answer1->answer = '(ab|cd(ef))gh';
        $answer1->fraction = 100;
        $answer1->feedback = '{$0}|{$1}|{$2}';

        // Answer where it is possible to not match first subexpression.
        $answer2 = new stdClass;
        $answer2->id = 201;
        $answer2->answer = '(12)|34(56)gh';
        $answer2->fraction = 100;
        $answer2->feedback = '{$0}|{$1}|{$2}';

        // Answer where it is possible to not match middle subexpression.
        $answer3 = new stdClass;
        $answer3->id = 202;
        $answer3->answer = '(z|y(x))(w)';
        $answer3->fraction = 100;
        $answer3->feedback = '{$0}|{$1}|{$2}|{$3}';

        // Answer with named subexpression.
        $answer4 = new stdClass;
        $answer4->id = 203;
        $answer4->answer = '(?P<name>value)nonvalue|(?P<noname>wrongvalue)';
        $answer4->fraction = 100;
        $answer4->feedback = '{$name}';

        $subexpr->answers = array(200=>$answer1, 201=>$answer2, 202=>$answer3, 203=>$answer4);
        $this->subexprquestion = $subexpr;

    }

    public function test_get_best_fit_answer() {
        //      Normal question with hinting on and several answers with different grades.
        $testquestion = clone $this->testquestion;

        //  Full match testing.
        // 100% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat cats?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100% partial match, 90% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do rats eat bat?'));
        $this->assertTrue($bestfit['answer']->fraction == 0.9);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100% and 90% partial match, 50% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats eat cats'));
        $this->assertTrue($bestfit['answer']->fraction == 0.5);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100%, 90%, 50% partial matches, 0% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats'));
        $this->assertTrue($bestfit['answer']->fraction == 0);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);

        //  Partial match testing
        // 100% is closest partial match by characters left, thought 90% is just as good - first should win!
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bat eat fat?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === false);
        // Now 90% is better because it allows to omit second 's' even if first is present.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat fat?'));
        $this->assertTrue($bestfit['answer']->fraction == 0.9);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === false);
        // 50% is better, but it isn't within hint grade border, while all answer within border have no matches.
        // So 100% is choosen as first answer within border with no match at all.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bat eat fat?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === false);
        $this->assertTrue($bestfit['match']->full === false);
        // If we lower hint grade border, 50% should have partial match.
        $testquestion1 = clone $this->testquestion;
        $testquestion1->hintgradeborder = 0.4;
        $bestfit = $testquestion1->get_best_fit_answer(array('answer' => 'bat eat fat?'));
        $this->assertTrue($bestfit['answer']->fraction == 0.5);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === false);
        // Partial match ends so early there is no difference between 100% and 90%, 100% should be selected as first.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do hats eat cats?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === false);

        //      Question with engine that doesn't allow partial matching (php_preg_matcher).
        $testquestion = clone $this->testquestion;
        $testquestion->engine = 'php_preg_matcher';
        //  Full match testing.
        // 100% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat cats?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100% partial match, 90% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do rats eat bat?'));
        $this->assertTrue($bestfit['answer']->fraction == 0.9);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100% and 90% partial match, 50% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats eat cats'));
        $this->assertTrue($bestfit['answer']->fraction == 0.5);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        // 100%, 90%, 50% partial matches, 0% full match.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats'));
        $this->assertTrue($bestfit['answer']->fraction == 0);
        $this->assertTrue($bestfit['match']->is_match() === true);
        $this->assertTrue($bestfit['match']->full === true);
        //  Partial match testing - no partial matching, so we should get no match at all.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bat eat fat?'));
        $this->assertTrue($bestfit['answer']->fraction == 1);
        $this->assertTrue($bestfit['match']->is_match() === false);
        $this->assertTrue($bestfit['match']->full === false);

        //      TODO question with engine which supports partial matching, but not characters left - when we would have such engine - like backtracking.
    }

    public function test_matchresults_parts() {
        $testquestion = clone $this->testquestion;
        $testquestion->exactmatch = false;// Disable exact matching to be able to have wrong head and tail.
        $hintobj = new qtype_preg_hintmatchingpart($testquestion, 'hintmatchingpart');

        // There is wrong head, wrong tail, correct part and next character.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Oh! Do cats eat hats?'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == 'Oh! ');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat ');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue(strstr('crb', $hintstr[0]) !== false);
        $this->assertTrue($matchresults->match_tail() == 'hats?');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));
        // Matching breaks inside the word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Oh! Do cats eat bets?'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == 'Oh! ');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat b');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue(strstr('a', $hintstr[0]) !== false);
        $this->assertTrue($matchresults->match_tail() == 'ets?');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));
        // No wrong head.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do cats eat hats?'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == '');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat ');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue(strstr('crb', $hintstr[0]) !== false);
        $this->assertTrue($matchresults->match_tail() == 'hats?');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));
        // No wrong tail.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Oh! Do cats eat '));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == 'Oh! ');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat ');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue(strstr('crb', $hintstr[0]) !== false);
        $this->assertTrue($matchresults->match_tail() == '');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));
        // No wrong tail and hinted character.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Oh! Do cats eat rats?'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == 'Oh! ');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat rats?');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue($hintstr === '');
        $this->assertTrue($matchresults->match_tail() == '');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));
        // No correct part - so no guess except hinting.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => '!@#$^%&'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading().$matchresults->match_tail() == '!@#$^%&');
        $this->assertTrue($matchresults->matched_part() == '');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue(strstr('D', $hintstr[0]) !== false);
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));

        //  Engine without partial matching support should show colored parts only when there is a match.
        $testquestion1 = clone $this->testquestion;
        $testquestion1->exactmatch = false;// Disable exact matching to be able to have wrong head and tail.
        $testquestion1->engine = 'php_preg_matcher';
        $hintobj = new qtype_preg_hintmatchingpart($testquestion1, 'hintmatchingpart');

        // Full match with wrong head a tail - there is colored string.
        $bestfit = $testquestion1->get_best_fit_answer(array('answer' => 'Oh! Do cats eat rats? Really?'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($matchresults->match_heading() == 'Oh! ');
        $this->assertTrue($matchresults->matched_part() == 'Do cats eat rats?');
        $hintstr = $matchresults->string_extension();
        $this->assertTrue($hintstr === '');
        $this->assertTrue($matchresults->match_tail() == ' Really?');
        $this->assertTrue($hintobj->could_show_hint($matchresults, false));

        // Partial match but no colored string since engine don't supports partial matching.
        $bestfit = $testquestion1->get_best_fit_answer(array('answer' => 'Oh! Do cats eat hats? Really?'));
        $matchresults = $bestfit['match'];
        $this->assertFalse($hintobj->could_show_hint($matchresults, false));
    }

    public function test_get_matcher() {
        global $CFG;
        // Test case insensitivity.
        $testquestion = clone $this->testquestion;
        $matcher = $testquestion->get_matcher($testquestion->engine, $testquestion->answers[100]->answer, true, $testquestion->get_modifiers(false));
        $matchresults = $matcher->match('do CaTs eat bAtS?');
        $this->assertTrue($matchresults->full);

        // Test case sensitivity.
        $matcher = $testquestion->get_matcher($testquestion->engine, $testquestion->answers[100]->answer, true, $testquestion->get_modifiers(true));
        $matchresults = $matcher->match('do CaTs eat bAtS?');
        $this->assertFalse($matchresults->full);
        $matchresults = $matcher->match('Do cats eat bats?');
        $this->assertTrue($matchresults->full);

        // Test extended notation.
        $regex = "Do\\s+#question verb\nc a t s   \\s+#subject\n eat[ ]+#verb\nbats\?#comment";
        $matcher = $testquestion->get_matcher($testquestion->engine, $regex, true, $testquestion->get_modifiers(false), null, 'pcreextended');
        $matchresults = $matcher->match('Do cats eat bats?');
        $this->assertTrue($matchresults->full);
        // Test space inside square brackets.
        $matchresults = $matcher->match('Do cats eatbats?');
        $this->assertFalse($matchresults->full);

        // -------------------- Tests for empty automata. ----------------------------
        // Regular expression that can not match due to start/end string assertions.
        // In that case get_matcher should return fa_matcher even if PHP matcher can match expression.
        $CFG->qtype_preg_assertfailmode = 1; // Fail mode merge on.
        $matcher = $testquestion->get_matcher('fa_matcher', 'a^b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(1, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a^b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(4, $errors[0]->position->indfirst);
        $this->assertEquals(5, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a$b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(1, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a$b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(5, $errors[0]->position->indfirst);
        $this->assertEquals(6, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\n^b^c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(4, $errors[0]->position->indfirst);
        $this->assertEquals(5, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a\n^b^c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(8, $errors[0]->position->indfirst);
        $this->assertEquals(9, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a[a-z\n]^b[a-z]^c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(14, $errors[0]->position->indfirst);
        $this->assertEquals(19, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a$[a-z]b[a-z]^c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(12, $errors[0]->position->indfirst);
        $this->assertEquals(17, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a[a-z\n]^b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a(\n|c)^b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(ac^|d)b', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)b$(cd|\n)', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', 'a|b$c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(b$)*c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(b^)+c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(1, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)(b$)+\n', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(b^)?a', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(?m)a(\n|c)^b[a-z]^c', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(13, $errors[0]->position->indfirst);
        $this->assertEquals(18, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\bb', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\B\t', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\B\t', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\B(\t| )', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\b(\t|s)', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\b*\t', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', '(a\b)+\t', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\bb c\b$', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(a\b)+\t\Bc', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(6, $errors[0]->position->indfirst);
        $this->assertEquals(9, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(a|\b)\t\B(c|d)', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(6, $errors[0]->position->indfirst);
        $this->assertEquals(9, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', 'a\B ( |\t)\ba', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(1, count($errors));
        $this->assertTrue(is_a($errors[0], 'qtype_preg_empty_fa_error'));
        $this->assertEquals(0, $errors[0]->position->indfirst);
        $this->assertEquals(2, $errors[0]->position->indlast);

        $matcher = $testquestion->get_matcher('fa_matcher', '(?(DEFINE)(?<byte>2[0-4]\d|25[0-5]|1\d\d|[1-9]?\d))\b(?&byte)(\.(?&byte)){3}', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));

        $matcher = $testquestion->get_matcher('fa_matcher', 'a{0,1}(^bc)', false, 0, null, 'native', false);
        $errors = $matcher->get_errors();
        $this->assertTrue(is_a($matcher, 'qtype_preg_fa_matcher'));
        $this->assertEquals(0, count($errors));
    }

    public function test_insert_subexpressions() {
        $testquestion = clone $this->testquestion;

        //      All subexpression is matched, or not matched by partial match.
        // Test inserting all subexpressions - anything is matched with some string.
        $response = array('answer' => 'Do cats eat bats?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subexpressions('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do cats eat bats?|cats|s|bats');
        // Second subexpression is matched with empty string.
        $response = array('answer' => 'Do cat eat bat?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subexpressions('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do cat eat bat?|cat||bat');
        // Second subexpression doesn't matched at all.
        $response = array('answer' => 'Do frogs eat mice?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subexpressions('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do frogs eat mice?|frogs||mice');

        //      Some subexpressions not matched while full match.
        //  Engine using custom parser.
        $customengine = clone $this->subexprquestion;
        // Last subexpression isn't captured.
        $response = array('answer' => 'abgh');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == 'abgh|ab|');
        // First subexpression isn't captured.
        $response = array('answer' => '3456gh');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '3456gh||56');
        // Middle subexpression isn't captured.
        $response = array('answer' => 'zw');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subexpressions('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'zw|z||w');
        // No match at all - then no string returned.
        $response = array('answer' => '*&^%&^');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced === '||');
        // Named subexpression test (matched and not matched one).
        $response = array('answer' => 'valuenonvalue');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subexpressions('{$name}|{$noname}', $response, $matchresults);
        $this->assertTrue($replaced === 'value|');

        //  Engine using PHP preg_match function.
        $phpengine = clone $this->subexprquestion;
        $phpengine->engine = 'php_preg_matcher';
        // Last subexpression isn't captured.
        $response = array('answer' => 'abgh');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == 'abgh|ab|');
        // First subexpression isn't captured.
        $response = array('answer' => '3456gh');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '3456gh||56');
        // Middle subexpression isn't captured.
        $response = array('answer' => 'zw');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subexpressions('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'zw|z||w');
        // No match at all - then no string returned.
        $response = array('answer' => '*&^%&^');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subexpressions('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '||');
        // Named subexpression test (matched and not matched one).
        $response = array('answer' => 'valuenonvalue');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subexpressions('{$name}|{$noname}', $response, $matchresults);
        $this->assertTrue($replaced === 'value|');
        /* '(ab|cd(ef))gh'
         '(12)|34(56)gh'
         '(z|y(x))(w)'
         '(?P<name>value)nonvalue|(?P<noname>wrongvalue)'*/
    }

}
