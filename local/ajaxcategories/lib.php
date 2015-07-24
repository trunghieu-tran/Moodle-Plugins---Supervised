<?php
// This file is part of ajaxcategories plugin - https://code.google.com/p/oasychev-moodle-plugins/
//
// Ajaxcategories plugin is free software: you can redistribute it and/or modify
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
 * Integrate to menubar.
 *
 * @package    local_ajaxcategories
 * @copyright  2015 Oleg Sychev, Volgograd State Technical University
 * @author     Elena Lepilkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function local_ajaxcategories_extend_settings_navigation(settings_navigation $nav, context $context) {
    $coursenode = $nav->get('courseadmin');
    if ($coursenode && has_capability('moodle/question:managecategory', $context)) {
        $questionbank = $coursenode->find($coursenode->get_children_key_list()[count($coursenode->get_children_key_list()) - 1], navigation_node::TYPE_CONTAINER);
        if ($questionbank) {
            $params = array();
            if ($context->contextlevel == CONTEXT_COURSE) {
                $params = array('courseid' => $context->instanceid);
            }
            $questionbank->add(get_string('pluginname', 'local_ajaxcategories'),
                               new moodle_url('/local/ajaxcategories/index.php', $params), navigation_node::TYPE_SETTING);
        }
    }
    // Add to questionbank on main page.
    $frontpagenode = $nav->get('frontpage');
    if ($frontpagenode && has_capability('moodle/question:managecategory', $context)) {
        $questionbank = $frontpagenode->find($frontpagenode->get_children_key_list()[count($frontpagenode->get_children_key_list()) - 1], navigation_node::TYPE_CONTAINER);
        if ($questionbank) {
            $params = array();
            if ($context->contextlevel == CONTEXT_COURSE) {
                $params = array('courseid' => $context->instanceid);
            }
            $questionbank->add(get_string('pluginname', 'local_ajaxcategories'),
                               new moodle_url('/local/ajaxcategories/index.php', $params), navigation_node::TYPE_SETTING);
        }
    }
}
