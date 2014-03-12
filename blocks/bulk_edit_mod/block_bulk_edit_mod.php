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
 * A block which Bulk Edit Instances
 *
 * @package    bulk_edit_mod
 * @author     Bastrykin Sergey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

class block_bulk_edit_mod extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_bulk_edit_mod');
    }

    function preferred_width() {
        return 210;
    }

    function applicable_formats() {
        return array(
            'all' => true,
            'tag' => false);   // Needs work to make it work on tags MDL-11960
    }

    function get_content() {
        global $CFG, $DB, $COURSE, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // initalise block content object
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }
        // The block has yet to be configured - just display configure message in
        // the block if user has permission to configure it
        if (has_capability('block/bulk_edit_mod:manageanyfeeds', $this->context)) {
            $coursemods = get_course_mods($COURSE->id);
            if (empty($coursemods)) {
                $this->content->text = get_string('nomodules', 'debug');
            } else {
                $html = '<ul class = "section img-text">';
                $idmodules = array();    // Initialize the empty array
                $modules = array();
                $items = array();
                foreach ($coursemods as $cm) {
                    if (!in_array($cm->module, $items)) {
                        $items[] = $cm->module;
                    }
                }
                list($sql, $params) = $DB->get_in_or_equal($items);
                $modules = $DB->get_records_select('modules', 'id '.$sql, $params, '', 'name, visible');

                foreach ($modules as $module) {
                    unset($type);
                    $stringtypes = array();
                    $linkcss = $module->visible ? '' : ' class = "dimmed" ';
                    if (!file_exists($CFG->dirroot.'/mod/'.$module->name.'/lib.php')) {
                        $strmodulename = '';
                    } else {
                        // took out hspace = "\10\", because it does not validate. don't know what to replace with.
                        $icon = '<img src = "'.$OUTPUT->pix_url('icon', $module->name).
                            '" class = "icon" alt = "" />';
                        $strmodulename = $icon;
                        include_once($CFG->dirroot.'/mod/'.$module->name.'/lib.php');
                        $gettypesfunc =  $module->name.'_get_types';

                        if (function_exists($gettypesfunc)) {
                            if ($types = $gettypesfunc()) {
                                $groupname = null;
                                $moduleinstances = $DB->get_records_select($module->name,
                                    'course = ?', array($COURSE->id), '', $module->name.'type type');
                                $typeinstances = array();

                                foreach ($moduleinstances as $instance) {
                                    $typeinstances[] = $module->name.'&amp;type='.$instance->type;
                                }

                                foreach ($types as $type) {
                                    if ($type->typestr === '--') {
                                        continue;
                                    }
                                    if (strpos($type->typestr, '--') === 0) {
                                        $groupname = str_replace('--', '', $type->typestr);
                                        continue;
                                    }
                                    if (in_array($type->type, $typeinstances)) {
                                        $stringtypes[$type->type] = $type->typestr;
                                    }
                                }
                            }
                        }
                    }
                    if (count($stringtypes)) {
                        foreach ($stringtypes as $type => $typestr) {
                            $html = $html.'<li style = "list-style-type: none" class = "activity '.
                                $module->name.' modtype_'.$module->name.'"><span class = "instancename">'.
                                $strmodulename.' <a '.$linkcss.' href = "/blocks/bulk_edit_mod/step2.php?course='.
                                $COURSE->id.'&amp;module='.$type.'">'.get_string('modulename', $module->name).' -- '.
                                $typestr.'</a></span></li>';
                        }
                    } else {
                        $html = $html.'<li style = "list-style-type: none" class = "activity '.
                        $module->name.' modtype_'.$module->name.'"><span class = "instancename">'.
                        $strmodulename.' <a '.$linkcss.' href = "/blocks/bulk_edit_mod/step2.php?course='.
                        $COURSE->id.'&amp;module='.$module->name.'">'.get_string('modulename', $module->name).'</a></span></li>';
                    }
                }
                $html = $html.'</ul>';
                $this->content->text = $html;
            }
        }

        return $this->content;
    }

    function has_config() {
        return false;
    }

    function instance_allow_config() {
        return false;
    }
}