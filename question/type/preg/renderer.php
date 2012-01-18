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
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

/**
 * Generates the output for preg questions.
 *
 * @copyright  2011 Oleg Sychev
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_renderer extends qtype_shortanswer_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $result = parent::formulation_and_controls($qa,$options);

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
        //TODO - decide exact conditions to show colored string. $options->correctness may be not best variant, because it associated with moodle 'hints' for questions like multichoice
        //if ($options->correctness == question_display_options::VISIBLE) {
        $hintkey = '';
        $hints = $question->available_specific_hint_types();
        foreach ($hints as $key => $value) {
            if ($qa->get_last_step()->has_behaviour_var('_render_'.$key)) {
                $hintkey = $key;
                break;//One hint per time for now - TODO - decide what to do with several hints per time - maybe make $parts['hintedending'] an array - and $hintkey too?
            }
        }
        if ($options->feedback == question_display_options::VISIBLE || $hintkey !== '') {//specific feedback is possible or hint is requested
            //Calculate strings for response coloring
            $parts = $question->response_correctness_parts(array('answer' => $currentanswer), $hintkey);
            if ($parts !== null) {

                $wronghead = '';
                if ($parts['wronghead'] !== '') {//if there is wrong heading
                    $wronghead = html_writer::tag('span', htmlspecialchars($parts['wronghead']), array('class' => $this->feedback_class(0)));
                }

                $correctpart = '';
                if ($parts['correctpart'] != '') {//there were any match
                    $correctpart = html_writer::tag('span', htmlspecialchars($parts['correctpart']), array('class' => $this->feedback_class(1)));
                }

                $hintpart = '';
                $correctbeforehint = '';
                $tobecontinued = '';
                //if hinting possible
                if ($parts['hintedpart'] !== null) {
                    $correctbeforehint = html_writer::tag('span', htmlspecialchars($parts['correctbeforehint']), array('class' => $this->feedback_class(1)));

                    //Next character hint was requested 
                    if (!$parts['deltail']) {
                        $hintpart = html_writer::tag('span', htmlspecialchars($parts['hintedpart']->str), array('class' => $this->feedback_class(0.5)));
                        //For one-character hint the conditions are hinted ending have more than one character or incomplete
                        if ($parts['hintedpart']->tobecontinued) {
                            $tobecontinued = get_string('tobecontinued', 'qtype_preg', null);
                        }
                    }
                }


                $wrongtail = '';
                if ($parts['wrongtail']) {//if there is wrong tail
                    $wrongtail =  html_writer::tag('span', htmlspecialchars($parts['wrongtail']), array('class' => $this->feedback_class(0)));
                }

                if ($hintpart === '' || $parts['correctbeforehint'] == $parts['correctpart']) {
                    //Correct ending starts from partial matching fail position, show hint after correct part
                    $hintmessage = $wronghead.$correctpart.$hintpart.$tobecontinued.$wrongtail.html_writer::empty_tag('br');
                } elseif ($parts['deltail']) {//student must delete tail to complete matching
                    $hintmessage = $wronghead.$correctpart.html_writer::tag('del', $wrongtail).html_writer::empty_tag('br');
                } else {//Hints starts inside correct part, show hint on separate string
                    $hintmessage = $wronghead.$correctpart.$wrongtail.html_writer::empty_tag('br');//correctness of response
                    $hintmessage .= $wronghead.$correctbeforehint.$hintpart.$tobecontinued.html_writer::empty_tag('br');//hint on the separate string
                }
            }
        }

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

        //TODO - If there is complete correct ending for a student's response available, show $correctpart.$hintedending instead of teacher-entered correct answer
        $correctresponse = $qa->get_question()->get_correct_response(); 
        $answer = $correctresponse['answer'];
        if (!$answer) { //Correct answer isn't set by the teacher
            return '';
        }

        return get_string('correctansweris', 'qtype_shortanswer', s($answer));
    }
}
