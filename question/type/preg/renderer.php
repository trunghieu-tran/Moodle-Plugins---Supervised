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
            return parent::feedback($qa, $options);;
        }

        //Determine requested hint(s)
        $hintmessage = '';
        $hintkey = '';
        $hints = $question->available_specific_hint_types();
        foreach ($hints as $key => $value) {
            if ($qa->get_last_step()->has_behaviour_var('_render_'.$key)) {
                $hintkey = $key;
                break;//One hint per time for now - TODO - decide what to do with several hints per time
            }
        }

        //Render hints
        //TODO - decide exact conditions to show colored string. $options->correctness may be not best variant, because it associated with moodle 'hints' for questions like multichoice
        //if ($options->correctness == question_display_options::VISIBLE) {
        if ($hintkey !== '') {//hint requested
            $hintobj = $question->hint_object($hintkey);
            $hintmessage = $hintobj->render_hint($this, array('answer' => $currentanswer));
            $hintmessage .= html_writer::empty_tag('br');
        } elseif ($options->feedback == question_display_options::VISIBLE) {//specific feedback is possible, render correctness - TODO - decide when to render correctness
            $hintobj =  $question->hint_object('hintmatchingpart');
            $hintmessage = $hintobj->render_hint($this, array('answer' => $currentanswer));
            $hintmessage .= html_writer::empty_tag('br');
        }

        $output = parent::feedback($qa, $options);
        return $hintmessage.$output;
    }

    /** Renders matched part of the response */
    public function render_matched($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => $this->feedback_class(1)));
        }
        return '';
    }

    /** Renders unmatched part of the response */
    public function render_unmatched($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => $this->feedback_class(0)));
        }
        return '';
    }

    /** Renders hinted part of the response*/
    public function render_hinted($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => $this->feedback_class(0.5)));
        }
        return '';
    }

    /** Renders part of the response that should be deleted*/
    public function render_deleted($str) {
        if ($str !== '') {
            return html_writer::tag('span', html_writer::tag('del', htmlspecialchars($str)), array('class' => $this->feedback_class(0)));
        }
        return '';
    }

    /** Renders part of the response that should be inserted*/
    public function render_inserted($str) {
        if ($str !== '') {
            return html_writer::tag('ins', htmlspecialchars($str));
        }
        return '';
    }

    /** Renders to be continued specifier*/
    public function render_tobecontinued() {
        return get_string('tobecontinued', 'qtype_preg', null);
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
