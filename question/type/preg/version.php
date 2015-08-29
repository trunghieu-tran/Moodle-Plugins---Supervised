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
$plugin->version = 2015070200;
$plugin->requires = 2014050800;
$plugin->release = 'Preg 2.8';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qtype_shortanswer' => 2013050100,
    'qbehaviour_adaptivehints' => 2015033000,
    'qbehaviour_adaptivehintsnopenalties' => 2015033000,
    'qbehaviour_interactivehints' => 2015033000,
    'qtype_poasquestion' => 2015033000,
    'block_formal_langs' => 2015070200
);
