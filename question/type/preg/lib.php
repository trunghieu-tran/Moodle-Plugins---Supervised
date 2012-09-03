<?php

/**
 * Serve question type files
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks file access for preg questions.
 */
function qtype_preg_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_preg', $filearea, $args, $forcedownload, $options);
}
