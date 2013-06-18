<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information for the correctwriting question type.
 *
 * @package    correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qtype_correctwriting';
$plugin->version  = 2013061900;
$plugin->requires = 2012120301;
$plugin->release = 'Correct Writing 2.4';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qtype_shortanswer' => 2012112900,
    'qbehaviour_adaptivehints' => 2013011800,
    'qbehaviour_adaptivehintsnopenalties' => 2013011800,
    'qbehaviour_interactivehints' => 2013060200,
    'qtype_poasquestion' => 2013020200,
    'block_formal_langs' => 2012021400
);