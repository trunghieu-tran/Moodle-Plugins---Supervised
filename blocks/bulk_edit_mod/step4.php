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
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/question/editlib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/conditionlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');
require_once($CFG->dirroot.'/blocks/bulk_edit_mod/lib.php');
require_once($CFG->dirroot.'/blocks/bulk_edit_mod/mod_form.php');

global $PAGE, $DB, $OUTPUT;

require_login();

$idcourse = optional_param('course', 0, PARAM_INT);
$module = optional_param('module', '', PARAM_ALPHA);
$namemodule = optional_param('module', 0, PARAM_INT);
if (empty($module) && !empty($namemodule)) {
    $module = optional_param('modulename', '', PARAM_ALPHA);
}
unset($namemodule);
$instances = optional_param('instances', 0, PARAM_INT);
$fields = optional_param('fields', '', PARAM_ALPHANUMEXT);
$return = optional_param('return', 0, PARAM_BOOL);    // return to course/view.php if false or mod/modname/view.php if true
$type = optional_param('type', '', PARAM_ALPHA);

if (empty($idcourse)) {
    print_error('courseidnotfound', 'error');
} elseif (!($DB->get_record('course', array('id' => $idcourse), 'id'))) {
    print_error('invalidcourseid', 'error', '', $idcourse);
}

if (!empty($fields)) {
    if (empty($idcourse) || empty($module) || empty($instances)) {
        print_error('invalidqueryparam', 'error');
    }
} elseif (!empty($instances)) {
    $param = '';
    foreach ($instances as $instace) {
        $param .= '&instances[]='.$instace;
    }
    redirect($CFG->wwwroot.'/blocks/bulk_edit_mod/step3.php?course='.$idcourse.'&module='.$module.$param);
} elseif (!empty($module)) {
    redirect($CFG->wwwroot.'/blocks/bulk_edit_mod/step2.php?course='.$idcourse.'&module='.$module);
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
$extraparams = '';
if ($idcourse) {
    $urlparams['course'] = $idcourse;
    $extraparams = '&course='.$idcourse;
}

$headerstring = get_string('pluginheader', 'block_bulk_edit_mod');
$PAGE->requires->js('/blocks/bulk_edit_mod/bulk_edit.js');
$PAGE->set_pagelayout('base');
$PAGE->set_title($headerstring);
$PAGE->set_heading($headerstring);

$pluginpage = new moodle_url('/blocks/bulk_edit_mod/step1.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('plugintitle', 'block_bulk_edit_mod'), $pluginpage);

if (!empty($module)) {
    $urlparams['module'] = $module;
    if (!empty($type)) {
        $urlparams['type'] = $type;
    }
    $pluginpage = new moodle_url('/blocks/bulk_edit_mod/step2.php', $urlparams);
    $PAGE->navbar->add(get_string('modulename', $module).get_module_type_str($module, $type), $pluginpage);
}

$PAGE->set_url('/blocks/bulk_edit_mod/step4.php', $urlparams);


$cmarray = array();
$contexts = array();



$cmarraytemp = get_coursemodules_from_instances($module, $instances, 0, false);

if (empty($course)) {
    $cm = current($cmarraytemp);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
}
foreach ($cmarraytemp as $cm) {
    if ($course->id != $cm->course) {
        print_error(get_string('instances_have_equel_course', 'block_bulk_edit_mod'));
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('moodle/course:manageactivities', $context);
    $cmarray[] = $cm;
    $contexts[] = $context;
}
unset($cmarraytemp);

$cm = $cmarray[0];
$context = $contexts[0];

$idmodule = $cm->module;
$data = get_data_for_instances($module, $idmodule, $cm, $course, $context, $return);

$fullmodulename = get_string('modulename', $module);
$pageheading = get_string('updatinga', 'block_bulk_edit_mod', $fullmodulename);

$pagepath = 'mod-'.$module.'-mod';

$PAGE->set_pagetype($pagepath);

$modmoodleform = $CFG->dirroot.'/mod/'.$module.'/mod_form.php';
if (file_exists($modmoodleform)) {
    require_once($modmoodleform);
} else {
    print_error('noformdesc');
}
$modlib = $CFG->dirroot.'/mod/'.$module.'/lib.php';
if (file_exists($modlib)) {
    include_once($modlib);
} else {
    print_error('modulemissingcode', '', '', $modlib);
}

$mformclassname = 'mod_'.$module.'_mod_form';
$myform = new mod_bulkedit_mod_form(new $mformclassname($data, $cm->sectionvalue, $cm, $course),
    $data, $cm->sectionvalue, $cm, $course);

$myform->add_hidden_element('instances', $instances, PARAM_INT);
$myform->add_hidden_element('fields', $fields, PARAM_INT);
$mform = $myform->is_form();

$myform->set_data($data);

if ($myform->is_cancelled()) {
    if ($return && !empty($cm->id)) {
        redirect($CFG->wwwroot.'/mod/'.$module.'/view.php?id='.$cm->id);
    } else {
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id.'#section-'.$cm->sectionvalue);
    }
} elseif ($fromform = $myform->is_form()->get_data()) {
    $i = 0;
    foreach ( $instances as $instance) {
        $fromform = $myform->is_form()->get_data();

        $cm = $cmarray[$i];
        $context = $contexts[$i];

        $idmodule = $cm->module;

        $olddata = get_data_for_instances($module, $idmodule, $cm, $course, $context, $return);

        $tempform = new $mformclassname($olddata, $cm->sectionvalue, $cm, $course);
        $olddata = (array)$olddata;
        $tempform->data_preprocessing($olddata);
        $allfields = $myform->get_fields();

        foreach ($allfields as $groupnumber => $elementsgroup) {
             foreach ($elementsgroup as $elementnumber => $element) {
                if (!(in_array($groupnumber.'-'.$elementnumber, $fields))) {

                    $myform->replase_element_data($element, $fromform, $olddata);
                }
            }
        }

        $fromform->instance = $cm->instance;
        $fromform->coursemodule = $cm->id;

        if (!empty($fromform->coursemodule)) {
            $context = get_context_instance(CONTEXT_MODULE, $fromform->coursemodule);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
        }

        $fromform->course = $course->id;
        $fromform->modulename = clean_param($fromform->modulename, PARAM_SAFEDIR);  // For safety

        $updateinstancefunction = $fromform->modulename.'_update_instance';

        if (!isset($fromform->groupingid)) {
            $fromform->groupingid = 0;
        }

        if (!isset($fromform->groupmembersonly)) {
            $fromform->groupmembersonly = 0;
        }

        if (!isset($fromform->name)) { // label
            $fromform->name = $fromform->modulename;
        }

        if (!isset($fromform->completion)) {
            $fromform->completion = COMPLETION_DISABLED;
        }
        if (!isset($fromform->completionview)) {
            $fromform->completionview = COMPLETION_VIEW_NOT_REQUIRED;
        }

        // Convert the 'use grade' checkbox into a grade-item number: 0 if
        // checked, null if not
        if (isset($fromform->completionusegrade) && $fromform->completionusegrade) {
            $fromform->completiongradeitemnumber = 0;
        } else {
            $fromform->completiongradeitemnumber = null;
        }

        if (!empty($fromform->update)) {
            if (!empty($course->groupmodeforce) or !isset($fromform->groupmode)) {
                $fromform->groupmode = $cm->groupmode; // keep original
            }

            // update course module first
            $cm->groupmode        = $fromform->groupmode;
            $cm->groupingid       = $fromform->groupingid;
            $cm->groupmembersonly = $fromform->groupmembersonly;

            $completion = new completion_info($course);
            if ($completion->is_enabled()) {
                // Handle completion settings. If necessary, wipe existing completion
                // data first.
                if (!empty($fromform->completionunlocked)) {
                    $completion = new completion_info($course);
                    $completion->reset_all_state($cm);
                }

                $cm->completion                = $fromform->completion;
                $cm->completiongradeitemnumber = $fromform->completiongradeitemnumber;
                $cm->completionview            = $fromform->completionview;
                $cm->completionexpected        = $fromform->completionexpected;
            }

            if (!empty($CFG->enableavailability)) {
                $cm->availablefrom             = $fromform->availablefrom;
                $cm->availableuntil            = $fromform->availableuntil;
                // The form time is midnight, but because we want it to be
                // inclusive, set it to 23:59:59 on that day.
                if ($cm->availableuntil) {
                    $cm->availableuntil = strtotime('23:59:59',
                        $cm->availableuntil);
                }
                $cm->showavailability          = $fromform->showavailability;
                condition_info::update_cm_from_form($cm, $fromform, true);
            }

            $DB->update_record('course_modules', $cm);

            $modcontext = get_context_instance(CONTEXT_MODULE, $fromform->coursemodule);

            // update embedded links and save files
            if (plugin_supports('mod', $fromform->modulename, FEATURE_MOD_INTRO, true)) {
                $fromform->intro = file_save_draft_area_files($fromform->introeditor['itemid'], $modcontext->id,
                    'mod_'.$fromform->modulename, 'intro', 0,
                    array('subdirs' => true), $fromform->introeditor['text']);
                $fromform->introformat = $fromform->introeditor['format'];
                unset($fromform->introeditor);
            }

            if (!$updateinstancefunction($fromform, $mform)) {
                print_error('cannotupdatemod', '', 'view.php?id=$course->id', $fromform->modulename);
            }
        }

        after_update_instance($course, $fromform);

        $i++;
    }
    if (isset($fromform->submitbutton)) {
        redirect($CFG->wwwroot.'/mod/'.$module.'/view.php?id='.$cmarray[0]->id);
    } else {
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
    exit;
} else {
    // Display of the form to the user
    $streditinga = get_string('editinga', 'moodle', $fullmodulename);
    $strmodulenameplural = get_string('modulenameplural', $module);

    if (!empty($cm->id)) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
    }

    $PAGE->set_cacheable(false);

    echo $OUTPUT->header();

    $a = new stdClass();
    $a->current = 4;
    $a->max = 4;
    echo '<h1>'.get_string('step_str', 'block_bulk_edit_mod', $a).'</h1>';

    if (get_string_manager()->string_exists('modulename_help', $module)) {
        echo $OUTPUT->heading_with_help($pageheading, 'modulename', $module, 'icon');
    } else {
        echo $OUTPUT->heading_with_help($pageheading, '', '', 'icon');
    }
    $myform->delete_element_without($fields);

    $mform->display();
}
echo $OUTPUT->footer();