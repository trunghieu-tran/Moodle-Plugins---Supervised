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
 * Preg question renderer class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

/**
 * Generates the output for preg questions.
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
            $currentanswer = '';
        }

        //Determine requested hint(s)
        $hintmessage = '';
        $hintkeys = array();
        $hints = $question->available_specific_hints(array('answer' => $currentanswer));
        foreach ($hints as $hintkey => $value) {
            $behaviour = $qa->get_behaviour();
            $copykey = $hintkey;
            $hintkey = $behaviour->adjust_hintkey($hintkey, true);
            if ($copykey != $hintkey) {//Adjusting changed key, so there may be other instances to render.
                $maxnumber = substr($hintkey, strpos($hintkey, '#') + 1);
                for ($i = 0; $i < $maxnumber; $i++) {
                    if ($qa->get_last_step()->has_behaviour_var('_render_'.$copykey.$i)) {
                        $hintkeys[] = $copykey.$i;
                    }
                }
            }
            if ($qa->get_last_step()->has_behaviour_var('_render_'.$hintkey)) {
                $hintkeys[] = $hintkey;
            }
        }

        //Render hints
        $coloredhintrendered = false;
        if (!empty($hintkeys)) {//Hint requested.
            foreach ($hintkeys as $hintkey) {
                $hintobj = $question->hint_object($hintkey);
                $hintmessage .= $hintobj->render_hint($this, $qa, $options, array('answer' => $currentanswer));
                $hintmessage .= html_writer::empty_tag('br');
                if ($hintkey == 'hintnextchar' || $hintkey == 'hintnextlexem') {
                    $coloredhintrendered = true;
                }
            }
        } 
        if (!$coloredhintrendered && $options->feedback == question_display_options::VISIBLE) {
            //Specific feedback is possible and no hint including colored string is rendered, so render colored string.
            $hintobj = $question->hint_object('hintmatchingpart');
            $hintmessage = $hintobj->render_hint($this, $qa, $options, array('answer' => $currentanswer));
            if (qtype_poasquestion_string::strlen($hintmessage) > 0) {
                $hintmessage .= html_writer::empty_tag('br');
            }
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
}
