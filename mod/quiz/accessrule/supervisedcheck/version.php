<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * Version information for the quizaccess_supervisedcheck plugin.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */


defined('MOODLE_INTERNAL') || die();


$plugin->version   = 2014022202;                    // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2013051400;                    // Requires this Moodle version
$plugin->component = 'quizaccess_supervisedcheck';  // Full name of the plugin (used for diagnostics).
$plugin->dependencies = array('block_supervised' => 2014022201);