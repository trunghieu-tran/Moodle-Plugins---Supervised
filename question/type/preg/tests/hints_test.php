<?php

/**
 * Unit tests for question/type/preg/preg_hints.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');


class qtype_preg_hints_test extends PHPUnit_Framework_TestCase {

    protected $testquestion;

    /**
     * Creates a number of questions for testing.
     */
    public function setUp() {

        // Normal question with hinting on and several answers with different grades.
        $regular = new qtype_preg_question;
        $regular->usecase = true;
        $regular->correctanswer = 'He suspects, but he does not know - not yet.';
        $regular->exactmatch = true;
        $regular->usecharhint = true;
        $regular->penalty = 0.1;
        $regular->charhintpenalty = 0.2;
        $regular->hintgradeborder = 0.6;
        $regular->engine = 'fa_matcher';
        $regular->notation = 'native';
        $regular->uselexemhint = true;
        $regular->lexemhintpenalty = 0.4;
        $regular->langid = 1;// Simple english - TODO - make a better way to get.
        $regular->lexemusername = 'word';

        // Correct answer.
        $answer100 = new stdClass();
        $answer100->id = 100;
        $answer100->answer = 'He\s+suspects,\s+but\s+he\s+does\s+not\s+know\s+\-\s+not\s+yet\.\s*';
        $answer100->fraction = 1;
        $answer100->feedback = '';

        $regular->answers = array(100=>$answer100);
        $this->testquestion = $regular;
    }

    public function test_lexem_hint() {
        $testquestion = clone $this->testquestion;
        $hint = $testquestion->hint_object('hintnextlexem');

        // Match breaks at a punctuation mark.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects; but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == ',');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at the end of the string.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects,  but he does not know - not yet!'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == '.');
        $this->assertFalse($hint->to_be_continued($matchresults));

        // Match breaks at the space between words.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects,but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue(preg_match('/\sbut/', $hint->hinted_string($matchresults)) == 1);
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a last letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspect, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 's');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a middle letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He supects, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 'spects');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a first letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He cuspects, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 'suspects');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // No match at all.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => '%^&%^*&^%'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 'He');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Test a case where generated string starts before end of match.
        $testquestion = clone $this->testquestion;
        $testquestion->answers[100]->answer = '[a-zA-Z_][\w_]*\s*=\s*127';
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'a127'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == '=');
        $this->assertTrue($hint->to_be_continued($matchresults));
    }

        public function test_char_hint() {
        $testquestion = clone $this->testquestion;
        $hint = $testquestion->hint_object('hintnextchar');

        // Match breaks at a punctuation mark.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects; but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == ',');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at the end of the string.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects,  but he does not know - not yet!'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == '.');
        $this->assertFalse($hint->to_be_continued($matchresults));

        // Match breaks at the space between words.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspects,but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue(preg_match('/\s/',$hint->hinted_string($matchresults)) == 1);
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a last letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He suspect, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 's');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a middle letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He supects, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 's');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // Match breaks at a first letter of a word.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'He cuspects, but he does not know - not yet.'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 's');
        $this->assertTrue($hint->to_be_continued($matchresults));

        // No match at all.
        $bestfit = $testquestion->get_best_fit_answer(array('answer' => '%^&%^*&^%'));
        $matchresults = $bestfit['match'];
        $this->assertTrue($hint->hinted_string($matchresults) == 'H');
        $this->assertTrue($hint->to_be_continued($matchresults));
    }

}
