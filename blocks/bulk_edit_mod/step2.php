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
require_once($CFG->dirroot.'/blocks/bulk_edit_mod/lib.php');

global $PAGE, $DB, $OUTPUT;

require_login();

$idcourse = optional_param('course', 0, PARAM_INT);
$module = optional_param('module', '', PARAM_ALPHA);
$namemodule = optional_param('module', 0, PARAM_INT);
if (empty($module) && !empty($namemodule)) {
    $module = optional_param('modulename', '', PARAM_ALPHA);
}
unset($namemodule);
$type = optional_param('type', '', PARAM_ALPHA);

if (empty($idcourse)) {
    print_error('courseidnotfound', 'error');
} elseif (!($DB->get_record('course', array('id' => $idcourse), 'id'))) {
    print_error('invalidcourseid', 'error', '', $idcourse);
}

if (!empty($module)) {
    if (empty($idcourse)) {
        print_error('invalidqueryparam', 'error');
    } elseif (!($DB->get_record('modules', array('name' => $module), 'id') &&
            ($DB->get_record($module, array('course' => $idcourse), 'id')))) {
        print_error('invalidmodulename', 'error', '', $module);
    }
} else {
    redirect($CFG->wwwroot.'/blocks/bulk_edit_mod/step1.php?course='.$idcourse);
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
$PAGE->requires->js_function_call('startJSForTable("instances[]")');
$PAGE->set_pagelayout('base');
$PAGE->set_title($headerstring);
$PAGE->set_heading($headerstring);

$pluginpage = new moodle_url('/blocks/bulk_edit_mod/step1.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('plugintitle', 'block_bulk_edit_mod'), $pluginpage);
$urlparams['module'] = $module;
if (!empty($type)) {
    $urlparams['type'] = $type;
}
$pluginpage = new moodle_url('/blocks/bulk_edit_mod/step2.php', $urlparams);
$PAGE->navbar->add(get_string('modulename', $module).get_module_type_str($module, $type), $pluginpage);

$PAGE->set_url('/blocks/bulk_edit_mod/step2.php', $urlparams);

echo $OUTPUT->header();

$a = new stdClass();
$a->current = 2;
$a->max = 4;
echo '<h1>'.get_string('step_str', 'block_bulk_edit_mod', $a).'</h1>';
echo '<div style = "text-align: center;"> <h2>'.get_string('select_instances', 'block_bulk_edit_mod').'</h2>';
echo '<form method = "post" action = "step3.php?course='.$idcourse.'&amp;module='.$module.(empty($type) ? '' : '&amp;type='.$type).'">';

$table = new html_table();
$table->tablealign = 'center';
$table->head = array('<div id = "select_all"></div><label for = "select_all" >'.
        get_string('select', 'block_bulk_edit_mod').'</label>', get_string('instances', 'block_bulk_edit_mod'));

$rowcount = 0;
$gettype = $module.'type';

try {
    $cmodules = get_coursemodules_in_course($module, $idcourse, 'm.'.$gettype);
} catch (Exception $e) {
    $cmodules = get_coursemodules_in_course($module, $idcourse);
}

foreach ($cmodules as $cm) {
    if (!empty($cm->$gettype) && ($cm->$gettype != $type)) {
        continue;
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (has_capability('moodle/course:manageactivities', $context)) {
        $table->data[] = array(
                '<div style = "text-align: left"><input type = "checkbox" id = "id'.$rowcount.'" name = "instances[]" value = "'.
                 $cm->instance.'" onclick = "selectItem(\'instances[]\');"/></div>',
                '<div style = "text-align: left"><span>'.'<label class = "notifyproblem" for = "id'.$rowcount.'">'.$cm->name.
                '</label>'.'</span></div>'
                );
        $rowcount++;
    }
}

if ($rowcount == 0 && count($cm) != 0) {
    throw new required_capability_exception($context, 'moodle/course:manageactivities', 'Access is forbidden');
}
echo html_writer::table($table);
echo '<div><input type = "submit" name = "selectInstances" value = "'.get_string('next', 'block_bulk_edit_mod').'"/></div>';
echo '</form>';
echo '</div>';

echo $OUTPUT->footer();