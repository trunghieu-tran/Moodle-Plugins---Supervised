<?php

defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour with hinting (no penalties version).
 *
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/behaviour/adaptivehints/renderer.php');

class qbehaviour_adaptivehintsnopenalties_renderer extends qbehaviour_adaptivehints_renderer {

    protected function penalty_info(question_attempt $qa, $mark,
            question_display_options $options) {
        return '';
    }

    public function button_cost($str, $penalty, $options) {
        return '';
    }
}

