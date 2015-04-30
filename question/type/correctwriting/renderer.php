<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');
require_once($CFG->dirroot . '/question/type/correctwriting/mistakesimage.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
/**
 * Generates the output for short answer questions.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_renderer extends qtype_shortanswer_renderer {

    /**
     * Overloading feedback method to pass options to specific_feedback
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        $output = '';
        $output .= $this->specific_feedback_with_options($qa, $options);

        $output .= parent::feedback($qa, $options);

        return $output;
    }

    /**
     * All work is done is  qtype_correctwriting_renderer::specific_feedback_with_options,
     * so we don't need to return anythong from this functions, also it leads to duplicating
     * of output
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa) {
        return '';
    }

    protected function specific_feedback_with_options(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        /** @var qtype_correctwriting_question $question */
        $question = $qa->get_question();
        $shortanswerfeedback = parent::specific_feedback($qa);
        $myfeedback = '';
        $results = $question->matchedresults;
        $br = html_writer::empty_tag('br');

        $currentanswer = $qa->get_last_qt_var('answer');
        if(!$currentanswer) {
            $currentanswer = '';
        }
        $hints = $question->available_specific_hints(array('answer' => $currentanswer));
        $behaviour = $qa->get_behaviour();
        /** @var qbehaviour_adaptivehints_renderer|qbehaviour_adaptivehintsnopenalties_renderer|qbehaviour_interactivehints_renderer $behaviourrenderer */
        $behaviourrenderer =$behaviour->get_renderer($PAGE);
        $step = $qa->get_last_step();
        if ($results!=null && $options->feedback) {//Show feedback message only witho $options->feedback set, but Moodle hints - anyway
            //Output mistakes messages
            if (count($results->mistakes()) > 0) {
                $mistakescnt = count($results->mistakes());
                if ($mistakescnt == 1) {
                    $myfeedback = get_string('foundmistake', 'qtype_correctwriting');
                } else {
                    $myfeedback = get_string('foundmistakes', 'qtype_correctwriting');
                }
                $myfeedback .= $br;

                $i = 1;
                /** @var qtype_correctwriting_response_mistake $mistake */
                foreach($results->mistakes() as $mistake) {
                    //Render mistake message.
                    $msg = $i.') '.$mistake->get_mistake_message();
                    if ($i < $mistakescnt) {
                        $msg .= ';';
                    } else {
                        $msg .= '.';
                    }
                    //Render hint buttons and/or hints.
                    if (is_a($behaviour, 'qtype_poasquestion\\behaviour_with_hints'))
                    {
                        /** @var behaviour_with_hints $behaviour */
                        /** @var qbehaviour_adaptivehints_renderer|qbehaviour_adaptivehintsnopenalties_renderer|qbehaviour_interactivehints_renderer $behaviourrenderer */
                        foreach ($mistake->supported_hints() as $hintname) {
                            $hintkey = $hintname . '_' . $mistake->mistake_key();
                            if (in_array($hintkey, $hints)) {//There is hint for that mistake.
                                $hints = array_diff($hints, array($hintkey));
                                $classname =  'qtype_correctwriting_hint' . $hintname;
                                /** @var qtype_specific_hint $hintobj */
                                $hintobj = new $classname($question, $hintkey, $mistake);
                                if ($hintobj->hint_available()) {//There could be no hint object if response was changed in adaptive behaviour.
                                    if ($qa->get_last_step()->has_behaviour_var('_render_'.$hintkey)) {//Hint is requested, so render hint.
                                        $msg .= $br . $hintobj->render_hint($this, $qa, $options, array('answer' => $currentanswer));
                                    } else if ($hintobj->hint_available(array('answer' => $currentanswer)) && $step->has_behaviour_var('_resp_hintbtns')){//Hint is not requested, render button to be able to request it.
                                        $msg .= $br . $behaviourrenderer->render_hint_button($qa, $options, $hintobj);
                                    }
                                }
                            }
                        }
                    }
                    $myfeedback .= $msg;
                    $myfeedback .= $br;
                    $i++;
                }
            }
        }
        //Render non-mistake hints if requested.
        if (is_a($behaviour, 'qtype_poasquestion\\behaviour_with_hints')) {
            /** @var behaviour_with_hints $behaviour */
            $hints = $behaviour->adjust_hints($hints);
            foreach ($hints as $hintkey) {
                if ($qa->get_last_step()->has_behaviour_var('_render_'.$hintkey)) {
                    $hintobj = $question->hint_object($hintkey);
                    $myfeedback .= $hintobj->render_hint($this, $qa, $options, array('answer' => $currentanswer));
                    $myfeedback .= $br;
                }
            }
        }
        return $myfeedback . $shortanswerfeedback;
   }

   //This wil be shown only if show right answer is setup
   public function correct_response(question_attempt $qa) {
       global $CFG;
       /** @var qtype_correctwriting_question $question */
       $question = $qa->get_question();
       $resulttext  = html_writer::empty_tag('br');
       // This data should contain base64_encoded data about user mistakes
       $results = $question->matchedresults;
       if ($results!=null) {
           if (count($results->mistakes()) != 0) {
               $image = new qtype_correctwriting_image_generator($results, $question);
               $imagebinary = $image->produce_image();
               $imagetext  = 'data:image/png;base64,' . base64_encode($imagebinary);
               $imagesrc = html_writer::empty_tag('image', array('src' => $imagetext));
               $resulttext = $imagesrc . $resulttext;
           }
       }
       // TODO: Uncomment if we need original shortanswer hint
       return $resulttext /*. parent::correct_response($qa) */;
   }

}