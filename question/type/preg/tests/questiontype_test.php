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
 * Unit tests for question/type/preg/questiontype.php.
 *
 * @package    qtype_preg
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/preg/questiontype.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/preg/edit_preg_form.php');

class qtype_preg_questiontype_test extends advanced_testcase {

    // Question type object.
    protected $qtype;
    // Id of the saved question.
    protected $questionid;
    // Original question data.
    protected $questiondata;
    // Category
    protected $cat;

    /**
     * Not using setUp for now to avoid looking for
     * how resetAfterTest(true) interact with it.
     */
    protected function setup_db_question() {

        $this->questiondata = test_question_maker::get_question_data('preg', 'five_regexes_two_tests');
        $formdata = test_question_maker::get_question_form_data('preg', 'five_regexes_two_tests');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $this->cat = $generator->create_question_category(array());

        $formdata->category = "{$this->cat->id},{$this->cat->contextid}";
        qtype_preg_edit_form::mock_submit((array)$formdata);

        $form = qtype_preg_test_helper::get_question_editing_form($this->cat, $this->questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $this->qtype = question_bank::get_qtype('preg');
        $returnedfromsave = $this->qtype->save_question($this->questiondata, $fromform);
        $this->questionid = $returnedfromsave->id;
    }

    protected function compare_answers($questiondata, $loadedquestion) {
        foreach ($questiondata->options->answers as $answer) {
            $actualanswer = array_shift($loadedquestion->options->answers);
            foreach ($answer as $ansproperty => $ansvalue) {
                // This question does not use 'answerformat', will ignore it.
                if (!in_array($ansproperty, array('id', 'question', 'answerformat'))) {
                    $this->assertAttributeEquals($ansvalue, $ansproperty, $actualanswer);
                }
            }
        }
    }

    // Test creating new question.
    public function test_question_creation() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->setup_db_question();

        $actualquestionsdata = question_load_questions(array($this->questionid));
        $actualquestiondata = end($actualquestionsdata);

        $this->compare_answers($this->questiondata, $actualquestiondata);
    }

    // User added one answer but deleted one piece of extra data
    public function test_add_answers_delete_extra_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->setup_db_question();

        $questiondata = test_question_maker::get_question_data('preg', 'six_regexes_one_test');
        $formdata = test_question_maker::get_question_form_data('preg', 'six_regexes_one_test');

        $formdata->category = "{$this->cat->id},{$this->cat->contextid}";

        $this->qtype = question_bank::get_qtype('preg');
        $questiondata->id = $this->questionid;
        $returnedfromsave = $this->qtype->save_question($questiondata, $formdata);

        $actualquestionsdata = question_load_questions(array($this->questionid));
        $actualquestiondata = end($actualquestionsdata);
        $this->compare_answers($questiondata, $actualquestiondata);
    }

    // User deleted one answer with extra data, but added three other pieces of extra data
    public function test_delete_answers_add_extra_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->setup_db_question();

        $questiondata = test_question_maker::get_question_data('preg', 'four_regexes_three_tests');
        $formdata = test_question_maker::get_question_form_data('preg', 'four_regexes_three_tests');

        $formdata->category = "{$this->cat->id},{$this->cat->contextid}";

        $this->qtype = question_bank::get_qtype('preg');
        $questiondata->id = $this->questionid;
        $returnedfromsave = $this->qtype->save_question($questiondata, $formdata);

        $actualquestionsdata = question_load_questions(array($this->questionid));
        $actualquestiondata = end($actualquestionsdata);
        $this->compare_answers($questiondata, $actualquestiondata);
    }

    // User deleted one answer with extra data, existing extra data is moved due to it
    public function test_delete_answers_move_extra_data() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->setup_db_question();

        $questiondata = test_question_maker::get_question_data('preg', 'four_regexes_three_tests');
        $formdata = test_question_maker::get_question_form_data('preg', 'four_regexes_three_tests');

        // Remove first extra data, so that extra data for the fourth regex will now be first.
        $questiondata->options->answers[13]->regextests = null;
        $formdata->regextests[0] = '';

        $formdata->category = "{$this->cat->id},{$this->cat->contextid}";

        $this->qtype = question_bank::get_qtype('preg');
        $questiondata->id = $this->questionid;
        $returnedfromsave = $this->qtype->save_question($questiondata, $formdata);

        $actualquestionsdata = question_load_questions(array($this->questionid));
        $actualquestiondata = end($actualquestionsdata);
        $this->compare_answers($questiondata, $actualquestiondata);
    }
}
