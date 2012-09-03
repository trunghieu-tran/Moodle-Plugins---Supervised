<?php

/**
 * Version information for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qtype_preg';
$plugin->version  = 2012072300;
$plugin->requires = 2012062501;
$plugin->release = 'Preg 2.3';
$plugin->maturity = MATURITY_BETA;

$plugin->dependencies = array(
    'qtype_shortanswer' => 2011102700,
    'qbehaviour_adaptivehints' => 2011111902,
    'qbehaviour_adaptivehintsnopenalties' => 2011111902,
    'qtype_poasquestion' => 2012060900,
    'block_formal_langs' => 2012021400
);
