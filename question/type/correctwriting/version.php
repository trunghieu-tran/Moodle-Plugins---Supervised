<?php
/**
 * Version information for the correctwriting question type.
 *
 * @package    correctwriting
 * @copyright  2011 Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qtype_correctwriting';
$plugin->version  = 2011121310;
$plugin->requires = 2011121310;
$plugin->release = 'Correct Writing 2.2';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'block_formal_langs' => 2012021400,
    'qtype_poasquestion' => 2012060900
);