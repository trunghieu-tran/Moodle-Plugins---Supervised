<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/question.php.
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot . '/question/type/preg/question.php');

class qtype_preg_question_test extends UnitTestCase {

    protected $testquestion;
    protected $subpattquestion;

    /**
     * Creates a number of questions for testing
     */
    public function setUp() {

        //Normal question with hinting on and several answers with different grades
        $regular = new qtype_preg_question;
        $regular->usecase = false;
        $regular->correctanswer = 'Do cats eat bats?';
        $regular->exactmatch = true;
        $regular->usehint = true;
        $regular->penalty = 0.1;
        $regular->hintpenalty = 0.2;
        $regular->hintgradeborder = 0.6;
        $regular->engine = 'nfa_matcher';
        $regular->notation = 'native';

        //correct answer
        $answer100 = new stdClass();
        $answer100->id = 100;
        $answer100->answer = 'Do ([cbr]at(s|)) eat ([cbr]at\2)\?';
        $answer100->fraction = 1;
        $answer100->feedback = 'Predator is {$1}. The prey is {$3}.';

        //good answer
        $answer90 = new stdClass();
        $answer90->id = 101;
        $answer90->answer = 'Do ([cbr]ats?) eat ([cbr]ats?)\?';
        $answer90->fraction = 0.9;
        $answer90->feedback = 'Predator is {$1}. The prey is {$2}. But mind the numbers!';

        //worse answer
        $answer50 = new stdClass();
        $answer50->id = 102;
        $answer50->answer = '[cbr]ats? eat [cbr]ats?';
        $answer50->fraction = 0.5;
        $answer50->feedback = 'What should start a question?';

        //totally bad - any single word
        $answer0 = new stdClass();
        $answer0->id = 103;
        $answer0->answer = '^\w+$';
        $answer0->fraction = 0;
        $answer0->feedback = 'Think harder!!!';

        //Special answer with second subpattern that it's possible to not match while matching the whole string
        $answer00 = new stdClass();
        $answer00->id = 104;
        $answer00->answer = 'Do ((dogs)|frogs|mice) eat (dogs|frogs|mice)\?';
        $answer00->fraction = 0;
        $answer00->feedback = 'Oh my, that\'s another story...';

        $regular->answers = array($answer100, $answer90, $answer50, $answer0, $answer00);
        $this->testquestion = $regular;

        //Special question to test subpattern capturing and inserting
        $subpatt = new qtype_preg_question;
        $subpatt->usecase = false;
        $subpatt->correctanswer = 'cdefgh';
        $subpatt->exactmatch = true;
        $subpatt->usehint = true;
        $subpatt->penalty = 0.1;
        $subpatt->hintpenalty = 0.2;
        $subpatt->hintgradeborder = 0.6;
        $subpatt->engine = 'nfa_matcher';
        $subpatt->notation = 'native';

        //Answer where it is possible to not match last subpattern
        $answer1 = new stdClass;
        $answer1->id = 200;
        $answer1->answer = '(ab|cd(ef))gh';
        $answer1->fraction = 100;
        $answer1->feedback = '{$0}|{$1}|{$2}';

        //Answer where it is possible to not match first subpattern
        $answer2 = new stdClass;
        $answer2->id = 201;
        $answer2->answer = '(12)|34(56)gh';
        $answer2->fraction = 100;
        $answer2->feedback = '{$0}|{$1}|{$2}';

        //Answer where it is possible to not match middle subpattern
        $answer3 = new stdClass;
        $answer3->id = 202;
        $answer3->answer = '(z|y(x))(w)';
        $answer3->fraction = 100;
        $answer3->feedback = '{$0}|{$1}|{$2}|{$3}';

        $subpatt->answers = array($answer1, $answer2, $answer3);
        $this->subpattquestion = $subpatt;


    }

    function test_get_best_fit_answer() {
    //////Normal question with hinting on and several answers with different grades
    $testquestion = clone $this->testquestion;
    ////Full match testing
    //100% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat cats?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100% partial match, 90% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do rats eat bat?'));
    $this->assertTrue($bestfit['answer']->fraction == 0.9);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100% and 90% partial match, 50% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats eat cats'));
    $this->assertTrue($bestfit['answer']->fraction == 0.5);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100%, 90%, 50% partial matches, 0% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats'));
    $this->assertTrue($bestfit['answer']->fraction == 0);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);

    ////Partial match testing
    //100% is closest partial match by characters left, thought 90% is just as good - first should win!
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bat eat fat?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === false);
    //Now 90% is better because it allows to omit second 's' even if first is present
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat fat?'));
    $this->assertTrue($bestfit['answer']->fraction == 0.9);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === false);
    //50% is better, but it isn't within hint grade border, while all answer within border have no matches
    //So 100% is choosen as first answer within border with no match at all
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bat eat fat?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === false);
    $this->assertTrue($bestfit['match']->full === false);
    //If we lower hint grade border, 50% should have partial match
    $testquestion1 = clone $this->testquestion;
    $testquestion1->hintgradeborder = 0.4;
    $bestfit = $testquestion1->get_best_fit_answer(array('answer' => 'bat eat fat?'));
    $this->assertTrue($bestfit['answer']->fraction == 0.5);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === false);
    //Partial match ends so early there is no difference between 100% and 90%, 100% should be selected as first
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do hats eat cats?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === false);


    //////Question with engine that doesn't allow partial matching (php_preg_matcher)
    $testquestion = clone $this->testquestion;
    $testquestion->engine = 'php_preg_matcher';
    ////Full match testing
    //100% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bats eat cats?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100% partial match, 90% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do rats eat bat?'));
    $this->assertTrue($bestfit['answer']->fraction == 0.9);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100% and 90% partial match, 50% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats eat cats'));
    $this->assertTrue($bestfit['answer']->fraction == 0.5);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    //100%, 90%, 50% partial matches, 0% full match
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'bats'));
    $this->assertTrue($bestfit['answer']->fraction == 0);
    $this->assertTrue($bestfit['match']->is_match() === true);
    $this->assertTrue($bestfit['match']->full === true);
    ////Partial match testing - no partial matching, so we should get no match at all
    $bestfit = $testquestion->get_best_fit_answer(array('answer' => 'Do bat eat fat?'));
    $this->assertTrue($bestfit['answer']->fraction == 1);
    $this->assertTrue($bestfit['match']->is_match() === false);
    $this->assertTrue($bestfit['match']->full === false);

    //////TODO question with engine which supports partial matching, but not characters left - when we would have such engine - like backtracking
    }

    function test_response_correctness_parts() {
        $testquestion = clone $this->testquestion;
        $testquestion->exactmatch = false;//Disable exact matching to be able to have wrong head and tail

        //There is wrong head, wrong tail, correct part and next character
        $parts = $testquestion->response_correctness_parts(array('answer' => 'Oh! Do cats eat hats?'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == 'Oh! ');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat ');
        $this->assertTrue(strstr('crb', $parts['hintedpart']->str));
        $this->assertTrue($parts['wrongtail'] == 'hats?');
        //Matching breaks inside the word
        $parts = $testquestion->response_correctness_parts(array('answer' => 'Oh! Do cats eat bets?'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == 'Oh! ');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat b');
        $this->assertTrue(strstr('a', $parts['hintedpart']->str));
        $this->assertTrue($parts['wrongtail'] == 'ets?');
        //No wrong head
        $parts = $testquestion->response_correctness_parts(array('answer' => 'Do cats eat hats?'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == '');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat ');
        $this->assertTrue(strstr('crb', $parts['hintedpart']->str));
        $this->assertTrue($parts['wrongtail'] == 'hats?');
        //No wrong tail
        $parts = $testquestion->response_correctness_parts(array('answer' => 'Oh! Do cats eat '), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == 'Oh! ');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat ');
        $this->assertTrue(strstr('crb', $parts['hintedpart']->str));
        $this->assertTrue($parts['wrongtail'] == '');
        //No wrong tail and hinted character
        $parts = $testquestion->response_correctness_parts(array('answer' => 'Oh! Do cats eat rats?'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == 'Oh! ');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat rats?');
        $this->assertTrue($parts['hintedpart'] == null);
        $this->assertTrue($parts['wrongtail'] == '');
        //No correct part - so no guess except hinting
        $parts = $testquestion->response_correctness_parts(array('answer' => '!@#$^%&'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'].$parts['wrongtail'] == '!@#$^%&');
        $this->assertTrue($parts['correctpart'] == '');
        $this->assertTrue(strstr('D', $parts['hintedpart']->str));

        ////Engine without partial matching support should show colored parts only when there is a match
        $testquestion1 = clone $this->testquestion;
        $testquestion1->exactmatch = false;//Disable exact matching to be able to have wrong head and tail
        $testquestion1->engine = 'php_preg_matcher';

        //Full match with wrong head a tail - there is colored string
        $parts = $testquestion1->response_correctness_parts(array('answer' => 'Oh! Do cats eat rats? Really?'), 'hintnextchar');
        $this->assertTrue($parts['wronghead'] == 'Oh! ');
        $this->assertTrue($parts['correctpart'] == 'Do cats eat rats?');
        $this->assertTrue($parts['hintedpart'] == null);
        $this->assertTrue($parts['wrongtail'] == ' Really?');

        //Partial match but no colored string since engine don't supports partial matching
        $parts = $testquestion1->response_correctness_parts(array('answer' => 'Oh! Do cats eat hats? Really?'), 'hintnextchar');
        $this->assertTrue($parts === null);
    }

    function test_insert_subpatterns() {
        $testquestion = clone $this->testquestion;

        //All subpattern is matched, or not matched by partial match
        //Test inserting all subpatterns - anything is matched with some string
        $response = array('answer' => 'Do cats eat bats?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subpatterns('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do cats eat bats?|cats|s|bats');
        //Second subpattern is matched with empty string
        $response = array('answer' => 'Do cat eat bat?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subpatterns('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do cat eat bat?|cat||bat');
        //Second subpattern doesn't matched at all
        $response = array('answer' => 'Do frogs eat mice?');
        $bestfit = $testquestion->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $testquestion->insert_subpatterns('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'Do frogs eat mice?|frogs||mice');

        //////Some subpatterns not matched while full match
        ////Engine using custom parser
        $customengine = clone $this->subpattquestion;
        //Last subpattern isn't captured
        $response = array('answer' => 'abgh');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == 'abgh|ab|');
        //First subpattern isn't captured
        $response = array('answer' => '3456gh');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '3456gh||56');
        //Middle subpattern isn't captured
        $response = array('answer' => 'zw');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subpatterns('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'zw|z||w');
        //No match at all - then no string returned
        $response = array('answer' => '*&^%&^');
        $bestfit = $customengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $customengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced === '');

        ////Engine using PHP preg_match function
        $phpengine = clone $this->subpattquestion;
        $phpengine->engine = 'php_preg_matcher';
        //Last subpattern isn't captured
        $response = array('answer' => 'abgh');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        ECHO $replaced.'</br>';
        $this->assertTrue($replaced == 'abgh|ab|');
        //First subpattern isn't captured
        $response = array('answer' => '3456gh');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '3456gh||56');
        //Middle subpattern isn't captured
        $response = array('answer' => 'zw');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subpatterns('{$0}|{$1}|{$2}|{$3}', $response, $matchresults);
        $this->assertTrue($replaced == 'zw|z||w');
        //No match at all - then no string returned
        $response = array('answer' => '*&^%&^');
        $bestfit = $phpengine->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        $replaced = $phpengine->insert_subpatterns('{$0}|{$1}|{$2}', $response, $matchresults);
        $this->assertTrue($replaced == '');
        //'(ab|cd(ef))gh'
        //'(12)|34(56)gh'
        //'(z|y(x))(w)'
    }

}
?>