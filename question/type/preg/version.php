<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Preg is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Preg.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qtype_preg';
$plugin->version = 2013071400;
$plugin->requires = 2013051400;
$plugin->release = 'Preg 2.5';
$plugin->maturity = MATURITY_BETA;

$plugin->dependencies = array(
    'qtype_shortanswer' => 2013050100,
    'qbehaviour_adaptivehints' => 2013011800,
    'qbehaviour_adaptivehintsnopenalties' => 2013011800,
    'qbehaviour_interactivehints' => 2013060200,
    'qtype_poasquestion' => 2013062900,
    'block_formal_langs' => 2013071900
);
