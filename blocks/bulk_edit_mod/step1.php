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

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/tablelib.php');

global $PAGE, $DB, $OUTPUT;

require_login();

$idcourse = optional_param('course', 0, PARAM_INT);

if (empty($idcourse)) {
    print_error('courseidnotfound', 'error');
} elseif (!($DB->get_record('course', array('id' => $idcourse), 'id'))) {
    print_error('invalidcourseid', 'error', '', $idcourse);
}

if ($idcourse == SITEID) {
    $idcourse = 0;
}
if ($idcourse) {
    $course = $DB->get_record('course', array('id' => $idcourse), '*', MUST_EXIST);
    $PAGE->set_course($course);
    $context = $PAGE->context;
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
    $PAGE->set_context($context);
}

$managesharedfeeds = has_capability('block/bulk_edit_mod:manageanyfeeds', $context);
if (!$managesharedfeeds) {
    require_capability('block/bulk_edit_mod:manageownfeeds', $context);
}

$urlparams = array();
if ($idcourse) {
    $urlparams['course'] = $idcourse;
}

$headerstring = get_string('pluginheader', 'block_bulk_edit_mod');
$PAGE->requires->js('/blocks/bulk_edit_mod/bulk_edit.js');
$PAGE->set_pagelayout('base');
$PAGE->set_title($headerstring);
$PAGE->set_heading($headerstring);

$pluginpage = new moodle_url('/blocks/bulk_edit_mod/step1.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('plugintitle', 'block_bulk_edit_mod'), $pluginpage);
$PAGE->set_url('/blocks/bulk_edit_mod/step1.php', $urlparams);

echo $OUTPUT->header();

$a = new stdClass();
$a->current = 1;
$a->max = 4;
echo '<h1>'.get_string('step_str', 'block_bulk_edit_mod', $a).'</h1>';
echo '<div style = "text-align: center;"> <h2>'.get_string('select_module', 'block_bulk_edit_mod').'</h2>';

$coursemods = get_course_mods($idcourse);

if (empty($coursemods)) {
    echo $OUTPUT->heading(get_string('nomodules', 'debug'));
} else {
    $table = new html_table();
    $table->tablealign = 'center';
    $table->head = array(get_string('activitymodules', 'block_bulk_edit_mod'));
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
            $icon = '<img src = "'.$OUTPUT->pix_url('icon', $module->name).'" class = "icon" alt = "" />';
            $strmodulename = $icon;
            include_once($CFG->dirroot.'/mod/'.$module->name.'/lib.php');
            $gettypesfunc =  $module->name.'_get_types';

            if (function_exists($gettypesfunc)) {
                if ($types = $gettypesfunc()) {
                    $groupname = null;
                    $moduleinstances = $DB->get_records_select($module->name, 'course = ?',
                        array($idcourse), '', $module->name.'type type');
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
                $table->data[] = array(
                    '<div style = "text-align: left"><span>'.$strmodulename.' <a '.$linkcss.
                    ' href = "step2.php?course='.$idcourse.'&amp;module='.$type.'">'.
                    get_string('modulename', $module->name).' -- '.$typestr.'</a></span></div>'
                    );
            }
        } else {
            $table->data[] = array(
                '<div style = "text-align: left"><span>'.$strmodulename.' <a '.$linkcss.' href = "step2.php?course='.
                $idcourse.'&amp;module='.$module->name.'">'.get_string('modulename', $module->name).'</a></span></div>'
                );
        }
    }
    echo html_writer::table($table);
}
echo '</div>';

echo $OUTPUT->footer();