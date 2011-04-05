<?php
/**
 * The question type class for the QTYPENAME question type.
 *
 * @copyright &copy; 2006 YOURNAME
 * @author YOUREMAILADDRESS
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package YOURPACKAGENAME
 *//** */

 define('NO_TEST', 0);
 define('COMMON_TEST',1);
 define('INDIVIDUAL_TEST',2);

 define('SHOW_TESTING_PROGRAM_FEEDBACK',1);
 define('SHOW_TEST_INPUT_DATA',2);
 define('SHOW_OUTPUT_STUDENT_DATA',4);
 define('SHOW_DIFF',8);
 define('SHOW_TESTS_NAMES',16);
 define('SHOW_NUMBER_OF_PASSED_TESTS',32);
 define('SHOW_RATING',64);
 
/**
 * Autograder parent class
 */
class grader_qtype extends default_questiontype {


    function name() {
        return 'grader';
    }
    
    /**
     * Returns current test mode of the grader.
     * It can be NO_TEST, COMMON_TEST or INDIVIDUAL_TEST
     * @return test mode
     */
    function get_test_mode() {
        return NO_TEST;
    }

    function add_tests($tests) {
        return true;
    }

    
    
    /**
     * @return boolean to indicate success of failure.
     */
    function get_question_options(&$question) {
        echo '<br>'.__FUNCTION__;
        // TODO code to retrieve the extra data you stored in the database into
        // $question->options.
        return true;
    }

    function process_options($form,$whom) {
        $flag = 0;

        $field =$whom.'showfeedback';
        if(isset($form->$field))
            $flag += SHOW_TESTING_PROGRAM_FEEDBACK;

        $field =$whom.'showtestinputdata';
        if(isset($form->$field))
            $flag += SHOW_TEST_INPUT_DATA;

        $field =$whom.'showtestoutputdata';
        if(isset($form->$field))
            $flag += SHOW_OUTPUT_STUDENT_DATA;

        $field =$whom.'showdiff';
        if(isset($form->$field))
            $flag += SHOW_DIFF;

        $field =$whom.'showtestsnames';
        if(isset($form->$field))
            $flag += SHOW_TESTS_NAMES;

        $field =$whom.'shownumberofpassedtest';
        if(isset($form->$field))
            $flag += SHOW_NUMBER_OF_PASSED_TESTS;

        $field =$whom.'showrating';
        if(isset($form->$field))
            $flag += SHOW_RATING;

        return $flag;
    }
    
    function save_question($question, $form) {
        GLOBAL $DB;
        echo '<br>'.__FUNCTION__;
        $question->studentshowoptionsgrp = $this->process_options($form,'student');
        $question->teachershowoptionsgrp = $this->process_options($form,'teacher');

        $record = new stdClass();
        $record->studentshowoptionsgrp = $this->process_options($form,'student');
        $record->teachershowoptionsgrp = $this->process_options($form,'teacher');
        $record->id = $DB->insert_record('question_grader', $record);
        return parent::save_question($question, $form);
    }
    /**
     * Save the units and the answers associated with this question.
     * @return boolean to indicate success of failure.
     */
    function save_question_options($question) {
        echo '<br>'.__FUNCTION__;
        //if(isset($this->studentshowoptionsgrp))
        //        echo 'yes!';
        //$question->showstudent = $this->configure_flag($question->studentshowoptionsgrp);
        //$question->showteacher = $this->configure_flag($question->teachershowoptionsgrp);
        
        // TODO code to save the extra data to your database tables from the
        // $question object, which has all the post data from editquestion.html
        return true;
    }

    /**
     * Deletes question from the question-type specific tables
     *
     * @param integer $questionid The question being deleted
     * @return boolean to indicate success of failure.
     */
    function delete_question($questionid) {
        echo '<br>'.__FUNCTION__;
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        echo '<br>'.__FUNCTION__;
        // TODO create a blank repsonse in the $state->responses array, which    
        // represents the situation before the student has made a response.
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        echo '<br>'.__FUNCTION__;
        // TODO unpack $state->responses[''], which has just been loaded from the
        // database field question_states.answer into the $state->responses array.
        return true;
    }
    
    function save_session_and_responses(&$question, &$state) {
        echo '<br>'.__FUNCTION__;
        // TODO package up the students response from the $state->responses
        // array into a string and save it in the question_states.answer field.
    
        $responses = '';
    
        return set_field('question_states', 'answer', $responses, 'id', $state->id);
    }
    
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        echo '<br>'.__FUNCTION__;
        global $CFG;

        $readonly = empty($options->readonly) ? '' : 'disabled="disabled"';

        // Print formulation
        $questiontext = $this->format_text($question->questiontext,
                $question->questiontextformat, $cmoptions);
        $image = false;
        //$image = get_question_image($question, $cmoptions->course);
    
        // TODO prepare any other data necessary. For instance
        $feedback = '';
        if ($options->feedback) {
    
        }
        echo '';
        //$this->print_question_submit_buttons($question, $state, $cmoptions, $options);
        include("$CFG->dirroot/question/type/grader/display.html");
    }
    
    function grade_responses(&$question, &$state, $cmoptions) {
        echo '<br>'.__FUNCTION__;
        // TODO assign a grade to the response in state.
        $state->raw_grade = $question->maxgrade;
        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;
        return true;
    }
    
    function compare_responses($question, $state, $teststate) {
        echo '<br>'.__FUNCTION__;
        return false;
    }

    /**
     * Checks whether a response matches a given answer, taking the tolerance
     * and units into account. Returns a true for if a response matches the
     * answer, false if it doesn't.
     */
    function test_response(&$question, &$state, $answer) {
        echo '<br>'.__FUNCTION__;
        // TODO if your code uses the question_answer table, write a method to
        // determine whether the student's response in $state matches the    
        // answer in $answer.
        return false;
    }

    function check_response(&$question, &$state){
        echo '<br>'.__FUNCTION__;
        // TODO
        return false;
    }

    function get_correct_responses(&$question, &$state) {
        echo '<br>'.__FUNCTION__;
        // TODO
        return false;
    }

    function get_all_responses(&$question, &$state) {
        echo '<br>'.__FUNCTION__;
        $result = new stdClass;
        // TODO
        return $result;
    }

    function get_actual_response($question, $state) {
        echo '<br>'.__FUNCTION__;
        // TODO
        $responses = '';
        return $responses;
    }

    /**
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {
        echo '<br>'.__FUNCTION__;
        $status = true;

        // TODO write code to backup an instance of your question type.

        return $status;
    }

    /**
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {
        echo '<br>'.__FUNCTION__;
        $status = true;

        // TODO write code to restore an instance of your question type.

        return $status;
    }
    

}

// Register this question type with the system.
question_register_questiontype(new grader_qtype());
?>
