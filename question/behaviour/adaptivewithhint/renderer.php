<?php

defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour with hinting.
 *
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptivewithhint_renderer extends qbehaviour_adaptive_renderer {

    public function controls(question_attempt $qa, question_display_options $options) {
        $output = parent::controls($qa, $options);//submit button

        //hinting buttons  $qa->get_behaviour()
         foreach ($qa->get_behaviour()->question->available_specific_hint_types() as $hintkey => $hintdescription) {
            $attributes = array(
                'type' => 'submit',
                'id' => $qa->get_behaviour_field_name($hintkey.'btn'),
                'name' => $qa->get_behaviour_field_name($hintkey.'btn'),
                'value' => get_string('hintbtn', 'adaptivewithhint', $hintdescription),
                'class' => 'submit btn',
            );
            if ($options->readonly) {
                $attributes['disabled'] = 'disabled';
            }
            $output. = html_writer::empty_tag('input', $attributes);
            
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

