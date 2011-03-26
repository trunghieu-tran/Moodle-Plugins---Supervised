<?php
/**
 * The question type class for the QTYPENAME question type.
 *
 * @copyright &copy; 2006 YOURNAME
 * @author YOUREMAILADDRESS
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package YOURPACKAGENAME
 *//** */

/**
 * The QTYPENAME question class
 *
 * TODO give an overview of how the class works here.
 */
class grader_qtype extends default_questiontype {

    function name() {
        return 'grader';
    }
    
    // TODO think about whether you need to override the is_manual_graded or
    // is_usable_by_random methods form the base class. Most the the time you
    // Won't need to.

    /**
     * @return boolean to indicate success of failure.
     */
    function get_question_options(&$question) {
        echo '<br>'.__FUNCTION__;
        // TODO code to retrieve the extra data you stored in the database into
        // $question->options.
        return true;
    }

    /**
     * Save the units and the answers associated with this question.
     * @return boolean to indicate success of failure.
     */
    function save_question_options($question) {
        echo '<br>'.__FUNCTION__;
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
        // TODO delete any    
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
    
        include("$CFG->dirroot/question/type/grader/display.html");
    }
    
    function grade_responses(&$question, &$state, $cmoptions) {
        echo '<br>'.__FUNCTION__;
        // TODO assign a grade to the response in state.
        return true;
    }
    
    function compare_responses($question, $state, $teststate) {
        echo '<br>'.__FUNCTION__;
        // TODO write the code to return two different student responses, and
        // return two if the should be considered the same.
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
