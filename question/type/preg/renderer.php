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
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot . '/question/engine/states.php');

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

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_var('answer');

        if ($response) {//Generate response-specific correct answer if there is response
            $correctanswer = $question->get_correct_response_ext(array('answer' => $response));
        } else {
            $correctanswer = $question->get_correct_response();
        }

        if (!$correctanswer) {
            return '';
        }

        return get_string('correctansweris', 'qtype_shortanswer', s($correctanswer['answer']));
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
            $hintmessage = $hintobj->render_hint(array('answer' => $currentanswer));
            $hintmessage .= html_writer::empty_tag('br');
        } elseif ($options->feedback == question_display_options::VISIBLE) {//specific feedback is possible, render correctness - TODO - decide when to render correctness
            $hintobj =  $question->hint_object('hintmatchingpart');
            $hintmessage = $hintobj->render_hint(array('answer' => $currentanswer));
            if (qtype_poasquestion_string::strlen($hintmessage) > 0) {
                $hintmessage .= html_writer::empty_tag('br');
            }
        }

        $output = parent::feedback($qa, $options);
        return $hintmessage.$output;
    }

    public static function feedback_class_static($fraction) {
        return question_state::graded_state_for_fraction($fraction)->get_feedback_class();
    }

    /** Renders matched part of the response */
    public static function render_matched($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => self::feedback_class_static(1)));
        }
        return '';
    }

    /** Renders unmatched part of the response */
    public static function render_unmatched($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => self::feedback_class_static(0)));
        }
        return '';
    }

    /** Renders hinted part of the response*/
    public static function render_hinted($str) {
        if ($str !== '') {
            return html_writer::tag('span', htmlspecialchars($str), array('class' => self::feedback_class_static(0.5)));
        }
        return '';
    }

    /** Renders part of the response that should be deleted*/
    public static function render_deleted($str) {
        if ($str !== '') {
            return html_writer::tag('span', html_writer::tag('del', htmlspecialchars($str)), array('class' => self::feedback_class_static(0)));
        }
        return '';
    }

    /** Renders part of the response that should be inserted*/
    public static function render_inserted($str) {
        if ($str !== '') {
            return html_writer::tag('ins', htmlspecialchars($str));
        }
        return '';
    }

    /** Renders to be continued specifier*/
    public static function render_tobecontinued() {
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

}
