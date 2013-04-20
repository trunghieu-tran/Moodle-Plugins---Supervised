<?php
/**
 * Version information for the formal languages block.
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_formal_langs';
$plugin->version  = 2012021400;
$plugin->requires = 2011121310;
$plugin->release = 'Formal languages 2.2';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qtype_poasquestion' => 2012060900
);