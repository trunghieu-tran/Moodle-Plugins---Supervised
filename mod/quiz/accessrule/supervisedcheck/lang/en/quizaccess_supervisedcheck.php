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
 * Strings for the quizaccess_supervisedcheck plugin.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */


defined('MOODLE_INTERNAL') || die();


$string['pluginname'] = 'Surervised block access rule';

$string['checknotrequired']         = 'No';
$string['checkforall']              = 'Yes, for all lesson types in this course (include "Not specified" lesson type)';
$string['customcheck']              = 'Yes, for custom lesson types:';
$string['allowcontrol']             = 'Allow supervised control?';
$string['uncheckedlessontypes']     = 'You must check at least one lesson type';
$string['noaccess']                 = 'You can\'t start the quiz. No active session at this moment for appropriate lesson type.';
$string['iperror']                  = 'You can\'t start the quiz because your ip isn\'t in classroom subnet.';