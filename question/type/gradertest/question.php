<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/gradertest/questiontype.php');


/**
 * Represents a numerical question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gradertest_question extends question_graded_by_strategy {

	public function __construct() {
        parent::__construct(new question_grader_strategy($this));
    }
	
	public function get_expected_data() {
        return array();
    }
	public function is_complete_response(array $response) {
		//TODO Проверка, загрузил ли студент файлы
        return true;
    }
	
	public function is_same_response(array $prevresponse, array $newresponse) {
        return true;
    }
	
	public function summarise_response(array $response) {
        return null;
    }
	
	public function get_validation_error(array $response) {
        return '';
    }
}
class question_grader_strategy implements question_grading_strategy {
	protected $question;
	public function __construct(qtype_gradertest_question $question) {
        $this->question = $question;
    }
	public function grade(array $response) {
		print_r($response);
		$answer->fraction = 0.8;
		$answer->feedback = 'excellent';
		$answer->answer = 'answer';
		return $answer;
		//return null;
	}

    public function get_correct_answer() {
		$answer = new stdClass();
		$answer->fraction = 1.0;
		$answer->feedback = 'excellent';
		$answer->answer = 'answer';
		return $answer;
		//return null;
	}
}