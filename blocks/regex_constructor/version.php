<?php
// This file is part of Preg project - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg project is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines version information for Regex Constructor block.
 *
 * @copyright &copy; 2013 Oleg Sychev, Volgograd State Technical University
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blocks
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_regex_constructor';
$plugin->version  = 2015070200;
$plugin->requires = 2014111000;
$plugin->release = 'Regex Constructor 2.8';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qtype_preg' => 2015070200
);