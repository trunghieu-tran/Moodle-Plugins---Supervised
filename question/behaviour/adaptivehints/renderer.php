<?php

defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour with hinting.
 *
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/behaviour/adaptive/renderer.php');

class qbehaviour_adaptivehints_renderer extends qbehaviour_adaptive_renderer {

     public function button_cost($str, $penalty, $options) {
        return '  '.get_string($str, 'qbehaviour_adaptivehints', format_float($penalty, $options->markdp));
     }

    public function controls(question_attempt $qa, question_display_options $options) {
        $question = $qa->get_question();
        $output = parent::controls($qa, $options);//submit button
        $penalty = $question->penalty;
        if ($penalty != 0) {
            $output .= $this->button_cost('withpossiblepenalty', $penalty, $options);
        }
        $output .= html_writer::empty_tag('br');

        //hinting buttons
         foreach ($question->available_specific_hint_types() as $hintkey => $hintdescription) {
            $attributes = array(
                'type' => 'submit',
                'id' => $qa->get_behaviour_field_name($hintkey.'btn'),
                'name' => $qa->get_behaviour_field_name($hintkey.'btn'),
                'value' => get_string('hintbtn', 'qbehaviour_adaptivehints', $hintdescription),
                'class' => 'submit btn',
            );
            if ($options->readonly) {
                $attributes['disabled'] = 'disabled';
            }
            $output .= html_writer::empty_tag('input', $attributes);

            //Cost message
            $hintobj = $question->->hint_object($hintkey);
            if ($hintobj->penalty_response_based()) {//if penalty is response-based
                //try to get last response
                $response = $qa->get_last_qt_data();
                if (empty($response)) {
                    $response = null;
                }
                $penalty = $hintobj->penalty_for_specific_hint($response);
                if ($penalty != 0) {
                    $output .= $this->button_cost('withpenaltyapprox', $penalty, $options);//note that reported penalty is approximation since user could change response in adaptive
                }
            } else {
                $penalty = $hintobj->penalty_for_specific_hint(null);
                if ($penalty != 0) {
                    $output .= $this->button_cost('withpenalty', $penalty, $options);
                }
            }
            $output .= html_writer::empty_tag('br');
            
            /*if (!$options->readonly) {
            $this->page->requires->js_init_call('M.core_question_engine.init_submit_button',
                    array($attributes['id'], $qa->get_slot()));
            }*/
        }

        return $output;
    }

    //Overload penalty_info to show actual penalty
    protected function penalty_info(question_attempt $qa, $mark,
            question_display_options $options) {
        if (!$qa->get_question()->penalty && !$qa->get_last_behaviour_var('_hashint', false)) {//no penalty for the attempts and no hinting done
            return '';
        }
        $output = '';

        // Print details of grade adjustment due to penalties
        if ($mark->raw != $mark->cur) {
            $output .= ' ' . get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark);
        }

        // Print information about any new penalty, only relevant if the answer can be improved.
        if ($qa->get_behaviour()->is_state_improvable($qa->get_state())) {
            $output .= ' ' . get_string('gradingdetailspenalty', 'qbehaviour_adaptive',
                    format_float($qa->get_last_step()->get_behaviour_var('_penalty'), $options->markdp));
        }

        return $output;
    }
}

