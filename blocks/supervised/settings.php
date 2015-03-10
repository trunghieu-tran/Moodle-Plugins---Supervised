<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Oleg Sychev <oasychev@gmail.com>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Default session time.
    $settings->add(new admin_setting_configtext('block_supervised_session_duration',
        get_string('settingsdurationtitle', 'block_supervised'),
        get_string('settingsdurationdesc', 'block_supervised'), 90, PARAM_INT));
    // How much days should settings table show by default.
    $settings->add(new admin_setting_configtext('block_supervised_sessions_days_past',
        get_string('settingsdayspasttitle', 'block_supervised'),
        get_string('settingsdayspastdesc', 'block_supervised'), 7, PARAM_INT));
}
