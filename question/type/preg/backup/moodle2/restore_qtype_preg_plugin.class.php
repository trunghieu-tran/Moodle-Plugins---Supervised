<?php

/**
 * Preg question type restore code.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/restore_poasquestion_preg_plugin.class.php');

class restore_qtype_preg_plugin extends restore_qtype_poasquestion_plugin {

    public function process_preg($data) {
        $this->process_poasquestion($data);
    }
}
