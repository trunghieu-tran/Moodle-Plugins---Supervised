<?php

/**
 * Preg question type backup code.
 * 
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/backup_poasquestion_preg_plugin.class.php');

class backup_qtype_preg_plugin extends backup_qtype_poasquestion_plugin {

}
