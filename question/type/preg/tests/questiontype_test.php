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

    /**
     * Not using setUp for now to avoid looking for
     * how resetAfterTest(true) interact with it.
     */
    protected function setup_db_question() {

        $questiondata = test_question_maker::get_question_data('preg', 'five_regexes_two_tests');
        $formdata = test_question_maker::get_question_form_data('preg', 'five_regexes_two_tests');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_preg_edit_form::mock_submit((array)$formdata);

        $form = qtype_preg_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $this->qtype = question_bank::get_qtype('preg');
        $this->questionid = $returnedfromsave->id;
    }

    // Test creating new question.
    public function test_question_creation() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->setup_db_question();

        $actualquestionsdata = question_load_questions(array($this->questionid));
        $actualquestiondata = end($actualquestionsdata);

        $this->assertTrue(count($question->options->answers) == 5);
        $this->assertTrue($question->options->answers[0]->answer == '000');
        $this->assertTrue($question->options->answers[1]->answer == '111');
        $this->assertTrue($question->options->answers[2]->answer == '222');
        $this->assertTrue($question->options->answers[3]->answer == '333');
        $this->assertTrue($question->options->answers[4]->answer == '444');

        $this->assertTrue($question->options->answers[1]->regextests == '111');
        $this->assertTrue($question->options->answers[3]->regextests == '333');
        $this->assertTrue(empty($question->options->answers[0]->regextests));
        $this->assertTrue(empty($question->options->answers[2]->regextests));
        $this->assertTrue(empty($question->options->answers[4]->regextests));
    }
}
