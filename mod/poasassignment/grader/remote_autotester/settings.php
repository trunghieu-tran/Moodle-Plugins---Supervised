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

$settings->add(new admin_setting_configtext('poasassignment_remote_autotester/ip', get_string('ip', 'poasassignment_remote_autotester'),
    get_string('ip_help', 'poasassignment_remote_autotester'), "127.0.0.1"));

$settings->add(new admin_setting_configtext('poasassignment_remote_autotester/port', get_string('port', 'poasassignment_remote_autotester'),
    get_string('port_help', 'poasassignment_remote_autotester'), 55556, PARAM_INT));

$settings->add(new admin_setting_configtext('poasassignment_remote_autotester/login', get_string('login', 'poasassignment_remote_autotester'),
    get_string('login_help', 'poasassignment_remote_autotester'), "Arkanif"));

$settings->add(new admin_setting_configtext('poasassignment_remote_autotester/password', get_string('password', 'poasassignment_remote_autotester'),
    get_string('password_help', 'poasassignment_remote_autotester'), "123"));