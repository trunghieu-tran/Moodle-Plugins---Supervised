<?php

/**
 * Preg question type upgrade code.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class for converting attempt data for preg questions when upgrading
 * attempts to the new question engine.
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 */
class qtype_preg_qe2_attempt_updater extends question_qtype_attempt_updater {
    public function right_answer() {
        return $this->question->options->correctanswer;
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $state->answer;
        } else {
            return null;
        }
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
        }
    }
}
