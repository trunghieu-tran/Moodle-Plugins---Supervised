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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/poasassignment/model.php');

// Add admin panel first-level element called "POAS assignment plugins"
$ADMIN->add('modules', new admin_category('modpoasassignmentplugins',
    new lang_string('poasassignmentplugins', 'poasassignment'), !$module->is_enabled()));

// Add admin panel item for graders
$ADMIN->add('modpoasassignmentplugins', new admin_category('poasassignmentplugins',
    new lang_string('subplugintype_poasassignment_plural', 'poasassignment'), !$module->is_enabled()));

// Add admin panel item for answer plugins
$ADMIN->add('modpoasassignmentplugins', new admin_category('poasassignmentanswertypesplugins',
    new lang_string('subplugintype_poasassignmentanswertypes_plural', 'poasassignment'), !$module->is_enabled()));

// Add admin panel item for taskgivers plugins
$ADMIN->add('modpoasassignmentplugins', new admin_category('poasassignmenttaskgiversplugins',
    new lang_string('subplugintype_poasassignmenttaskgivers_plural', 'poasassignment'), !$module->is_enabled()));

// Add admin panel item for additional plugins
$ADMIN->add('modpoasassignmentplugins', new admin_category('poasassignmentadditionalplugins',
    new lang_string('subplugintype_poasassignmentadditional_plural', 'poasassignment'), !$module->is_enabled()));

poasassignment_model::add_admin_plugin_settings('poasassignment', $ADMIN, $settings, $module);
poasassignment_model::add_admin_plugin_settings('poasassignmentanswertypes', $ADMIN, $settings, $module);
poasassignment_model::add_admin_plugin_settings('poasassignmenttaskgivers', $ADMIN, $settings, $module);
poasassignment_model::add_admin_plugin_settings('poasassignmentadditional', $ADMIN, $settings, $module);