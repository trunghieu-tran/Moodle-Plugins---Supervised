<?php

/**
 * Defines the POAS abstract question type class.
 *
 * @package    qtype_poasquestion
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/questiontypebase.php');

class qtype_poasquestion extends question_type {
    public function menu_name() {
        // Don't include this question type in the 'add new question' menu.
        return false;
    }
}
