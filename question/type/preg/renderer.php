<?php

/**
 * Preg question renderer class.
 *
 * @package    qtype
 * @subpackage preg
 * @copyright  2011 Oleg Sychev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');

/**
 * Generates the output for short answer questions.
 *
 * @copyright  2011 Oleg Sychev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_renderer extends qtype_shortanswer_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        $result = parent::formulation_and_controls($qa,$options);

        //Show colored string if appropriable (i.e. any answer is given)
        //We must show colored string along with specific_feedback if after student answered wrong specific feedback is shown, but general feedback is not
        //If all feedback is shown alike, colored string should be shown there
        return $result;
    }

    //Overloading feedback to add colored string 
    public function feedback(question_attempt $qa, question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        if(!$currentanswer) {
            return '';
        }

        //////Colored string
        $hintmessage = '';
        //TODO - decide exact conditions to show colored string. $options->correctness seems too tight - in adaptive mode it isn't shown until all is graded
        //if ($options->correctness == question_display_options::VISIBLE) {
        if ($options->feedback == question_display_options::VISIBLE || $qa->get_last_step()->has_behaviour_var('_render_hintnextchar')) {//specific feedback is possible or hint is requested
            //Calculate strings for response coloring
            $parts = $question->response_correctness_parts(array('answer' => $currentanswer));
            if ($parts !== null) {

                $wronghead = '';
                if ($parts['wronghead'] !== '') {//if there is wrong heading
                    $wronghead = html_writer::tag('span', htmlspecialchars($parts['wronghead']), array('class' => $this->feedback_class(0)));
                }

                $correctpart = '';
                if ($parts['correctpart'] != '') {//there were any match
                    $correctpart = html_writer::tag('span', htmlspecialchars($parts['correctpart']), array('class' => $this->feedback_class(1)));
                }

                $hintedcharacter = '';
                if ($qa->get_last_step()->has_behaviour_var('_render_hintnextchar') && $parts['hintedcharacter'] !== '') {//if hint requested and possible
                    $hintedcharacter = html_writer::tag('span', htmlspecialchars($parts['hintedcharacter']), array('class' => $this->feedback_class(0.5)));
                }

                $wrongtail = '';
                if ($parts['wrongtail']) {//if there is wrong tail
                    $wrongtail =  html_writer::tag('span', htmlspecialchars($parts['wrongtail']), array('class' => $this->feedback_class(0)));
                }

                $hintmessage = $wronghead.$correctpart.$hintedcharacter.$wrongtail.html_writer::empty_tag('br');
            }
        }

        /*TODO - find out how to define classes in plugins and add separate class for hint message*/
        //$hintmessage = html_writer::tag('div', $hintmessage, array('class' => 'specificfeedback'));//TODO  - this may not be needed as rendererbase.php provides div on it's own

        $output = parent::feedback($qa, $options);
        return $hintmessage.$output;
    }

    public function specific_feedback(question_attempt $qa) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        if (!$currentanswer) {
            return '';
        }

        //////Teacher-defined feedback text for that answer
        return $question->get_feedback_for_response(array('answer' => $currentanswer), $qa);
    }

    public function correct_response(question_attempt $qa) {

        $correctresponse = $qa->get_question()->get_correct_response(); 
        $answer = $correctresponse['answer'];
        if (!$answer) { //Correct answer isn't set by the teacher
            return '';
        }

        return get_string('correctansweris', 'qtype_shortanswer', s($answer));
    }
}
