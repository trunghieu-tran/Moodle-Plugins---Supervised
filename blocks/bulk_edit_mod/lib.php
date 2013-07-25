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

/**
 * This function initializes the initiating data for the unit form
 *
 * @param string $module The name module
 * @param int $idmodule The id module
 * @param object $cm The course module object
 * @param object $course The course object
 * @param object $context The context object
 * @param bool $return If false return to course/view.php or mod/modname/view.php if true
 * @return object This is object data for module form
 *
 */
function get_data_for_instances($module, $idmodule, $cm, $course, $context, $return) {
    global $DB;
    global $CFG;

    $data = $data = $DB->get_record($module, array('id' => $cm->instance), '*', MUST_EXIST);

    $data->coursemodule       = $cm->id;
    $data->section            = $cm->sectionvalue;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible            = $cm->visible; //??  $cw->visible ? $cm->visible : 0; // section hiding overrides
    $data->cmidnumber         = $cm->idnumber;          // The cm IDnumber
    $data->groupmode          = groups_get_activity_groupmode($cm); // locked later if forced
    $data->groupingid         = $cm->groupingid;
    $data->groupmembersonly   = $cm->groupmembersonly;
    $data->course             = $course->id;
    $data->module             = $idmodule;
    $data->modulename         = $module;
    $data->instance           = $cm->instance;
    $data->return             = $return;
    $data->update             = $cm->id;
    $data->completion         = $cm->completion;
    $data->completionview     = $cm->completionview;
    $data->completionexpected = $cm->completionexpected;
    $data->completionusegrade = is_null($cm->completiongradeitemnumber) ? 0 : 1;

    if (!empty($CFG->enableavailability)) {
        $data->availablefrom      = $cm->availablefrom;
        $data->availableuntil     = $cm->availableuntil;
        $data->showavailability   = $cm->showavailability;
    }
    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftideditor = file_get_submitted_draft_itemid('introeditor');
        $currentintro = file_prepare_draft_area($draftideditor, $context->id, 'mod_'.$data->modulename, 
            'intro', 0, array('subdirs' => true), $data->intro);
        $data->introeditor = array(
            'text' => $currentintro,
            'format' => $data->introformat,
            'itemid' => $draftideditor
        );
    }
    $params = array(
        'itemtype' => 'mod',
        'itemmodule' => $data->modulename,
        'iteminstance' => $data->instance, 
        'courseid' => $course->id
    );
    if ($items = grade_item::fetch_all($params)) {
        // add existing outcomes
        foreach ($items as $item) {
            if (!empty($item->outcomeid)) {
                $data->{'outcome_'.$item->outcomeid} = 1;
            }
        }

        // set category if present
        $gradecat = false;
        foreach ($items as $item) {
            if ($gradecat === false) {
                $gradecat = $item->categoryid;
                continue;
            }
            if ($gradecat != $item->categoryid) {
                // mixed categories
                $gradecat = false;
                break;
            }
        }
        if ($gradecat !== false) {
            // do not set if mixed categories present
            $data->gradecat = $gradecat;
        }
    }
    return $data;
}

/**
 * This function selects repeating fields from the list of fields and them returns
 *
 * @param array The object list fields on form
 * @return array This is the return list repeat elements on form
 *
 */
function find_repeat_elements($fields) {
    $elements = array();
    // We sort out all fields in shape and it is brought in an array $elements only that field which aren't hidden and are an array
    foreach ($fields as $key => $value) {
        if (ereg('^[A-Za-z0-9]+\[[0-9]+]$', $value['name']) && $value['type']!='hidden') {
            $elements[$key] = $value;
        }
    }
    // We sort the selected elements in the order of decrease of number weeding in shape
    krsort($elements);
    $repeatelements = array();
    $j = 0;
    $last = -1;
    $first = -1;
    $labels = array();
    // We fulfill search of repeating elements
    foreach ($elements as $key => $value) {
        if ($last == -1 || $key + 1 == $last) {
            if ($first == -1) {
                $first = $key;
            }
            $labels[$value['label']] = 1;
            $repeatelements[$j]['elements'][$key] = $value;
            $last = $key;
            if (!array_key_exists($key-1, $elements)) {
                $id = $key -1;
                $repeatelements[$j]['elements'][$id] = $fields[$id];
                // Search of the beginning of repeating elements
                while ($id != -1 && $fields[$id]['type'] != 'header') {
                    if (array_key_exists($fields[$id]['label'], $labels)) {
                        $repeatelements[$j]['elements'][$id] = $fields[$id];
                    }
                    $id--;
                }
                $id = $first + 1;
                $size = count($fields);
                // Search of the end of repeating elements
                while ($id != $size && $fields[$id]['type'] != 'header') {
                    $repeatelements[$j]['elements'][$id] = $fields[$id];
                    if ($fields[$id]['type'] == 'submit') {
                        break;
                    }
                    $id++;
                }
            }
        } else {
            ksort($repeatelements[$j]['elements']);
            $repeatelements[$j]['name'] = 'repeat Elements';
            $repeatelements[$j]['type'] = 'repeatElements';
            $repeatelements[$j]['label'] = 'repeat Elements';
            $labels = array();
            $j++;
            $last = -1;
            $first = -1;
        }
    }
    if ((count($repeatelements) > $j) && count($repeatelements[$j]['elements'])) {
        ksort($repeatelements[$j]['elements']);
        $repeatelements[$j]['name'] = 'repeat Elements';
        $repeatelements[$j]['type'] = 'repeatElements';
        $repeatelements[$j]['label'] = 'repeat Elements';
    }
    $labels = array();
    return $repeatelements;
}

/**
 * This function returns name of module subtype
 *
 * @param string $module The module name
 * @param string $typename The type submodule 
 * @return string This is the return name submodule
 *
 */
function get_module_type_str($module, $typename) {
    global $CFG;

    $filelib = $CFG->dirroot.'/mod/'.$module.'/lib.php';

    if (!file_exists($filelib)) {
        return '';
    } else {
        include_once($filelib);
        $gettypesfunc =  $module.'_get_types';

        if (function_exists($gettypesfunc)) {
            if ($types = $gettypesfunc()) {
                foreach($types as $type) {
                    // The list of subtypes comes to an end with type with a title "-", we fulfill the pass of this subtype
                    if ($type->typestr === '--') {
                        continue;
                    }
                    // The type title is designated by type with a title containing substring "-", we pass the data a subtype
                    if (strpos($type->typestr, '--') === 0) {
                        continue;
                    }
                    $type->type = str_replace('&amp;', '&', $type->type);
                    // If there was necessary a type return his name
                    if($type->type === $module.'&type='.$typename)
                        return ' -- '.$type->typestr;
                }
            }
        }
    }
    return '';
}

/**
 * This function fulfills actions after update instance
 *
 * @param object $course The course object
 * @param object $fromform The data of forma
 *
 */
function after_update_instance($course, $fromform) {
    global $USER;
    // make sure visibility is set correctly (in particular in calendar)
    set_coursemodule_visible($fromform->coursemodule, $fromform->visible);

    if (isset($fromform->cmidnumber)) { // label
        // set cm idnumber - uniqueness is already verified by form validation
        set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);
    }

    // Trigger mod_updated event with information about this module.
    $eventdata = new stdClass();
    $eventdata->modulename = $fromform->modulename;
    $eventdata->name       = $fromform->name;
    $eventdata->cmid       = $fromform->coursemodule;
    $eventdata->courseid   = $course->id;
    $eventdata->userid     = $USER->id;

    events_trigger('mod_updated', $eventdata);

    add_to_log($course->id, 'course', 'update mod',
        '../mod/'.$fromform->modulename.'/view.php?id='.$fromform->coursemodule,
        $fromform->modulename.' '.$fromform->instance);
    add_to_log($course->id, $fromform->modulename, 'update',
        'view.php?id='.$fromform->coursemodule,
        $fromform->instance, $fromform->coursemodule);


    // sync idnumber with grade_item
    if ($gradeitem = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => $fromform->modulename,
                'iteminstance' => $fromform->instance, 'itemnumber' => 0, 'courseid' => $course->id))) {
        if ($gradeitem->idnumber != $fromform->cmidnumber) {
            $gradeitem->idnumber = $fromform->cmidnumber;
            $gradeitem->update();
        }
    }

    $items = grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => $fromform->modulename,
            'iteminstance' => $fromform->instance, 'courseid' => $course->id));

    // create parent category if requested and move to correct parent category
    if ($items and isset($fromform->gradecat)) {
        if ($fromform->gradecat == -1) {
            $gradecategory = new grade_category();
            $gradecategory->courseid = $course->id;
            $gradecategory->fullname = $fromform->name;
            $gradecategory->insert();
            if ($gradeitem) {
                $parent = $gradeitem->get_parent_category();
                $gradecategory->set_parent($parent->id);
            }
            $fromform->gradecat = $gradecategory->id;
        }
        foreach ($items as $itemid => $unused) {
            $items[$itemid]->set_parent($fromform->gradecat);
            if ($itemid == $gradeitem->id) {
                // use updated grade_item
                $gradeitem = $items[$itemid];
            }
        }
    }

    // add outcomes if requested
    if ($outcomes = grade_outcome::fetch_all_available($course->id)) {
        $gradeitems = array();

        // Outcome grade_item.itemnumber start at 1000, there is nothing above outcomes
        $maxitemnumber = 999;
        if ($items) {
            foreach($items as $item) {
                if ($item->itemnumber > $maxitemnumber) {
                    $maxitemnumber = $item->itemnumber;
                }
            }
        }

        foreach($outcomes as $outcome) {
            $elname = 'outcome_'.$outcome->id;

            if (property_exists($fromform, $elname) and $fromform->$elname) {
                // so we have a request for new outcome grade item?
                if ($items) {
                    foreach($items as $item) {
                        if ($item->outcomeid == $outcome->id) {
                            // outcome aready exists
                            continue 2;
                        }
                    }
                }

                $maxitemnumber++;

                $outcomeitem = new grade_item();
                $outcomeitem->courseid     = $course->id;
                $outcomeitem->itemtype     = 'mod';
                $outcomeitem->itemmodule   = $fromform->modulename;
                $outcomeitem->iteminstance = $fromform->instance;
                $outcomeitem->itemnumber   = $maxitemnumber;
                $outcomeitem->itemname     = $outcome->fullname;
                $outcomeitem->outcomeid    = $outcome->id;
                $outcomeitem->gradetype    = GRADE_TYPE_SCALE;
                $outcomeitem->scaleid      = $outcome->scaleid;
                $outcomeitem->insert();

                // move the new outcome into correct category and fix sortorder if needed
                if ($gradeitem) {
                    $outcomeitem->set_parent($gradeitem->categoryid);
                    $outcomeitem->move_after_sortorder($gradeitem->sortorder);

                } else if (isset($fromform->gradecat)) {
                    $outcomeitem->set_parent($fromform->gradecat);
                }
            }
        }
    }

    rebuild_course_cache($course->id);
    grade_regrade_final_grades($course->id);
    plagiarism_save_form_elements($fromform); // save plagiarism settings
}

/**
 * Given an instances number of a module, finds the coursemodule description
 *
 * @global object
 * @param string $modulename name of module type, eg. resource, assignment,...
 * @param array $instance module instances number (id in resource, assignment etc. table)
 * @param int $courseid optional course id for extra validation
 * @param bool $sectionnum include relative section number (0,1,2 ...)
 * @return stdClass
 */
function get_coursemodules_from_instances($modulename, $instances, $courseid=0, $sectionnum=false) {
    global $DB;

    list($sqlforinstances,$params) = $DB->get_in_or_equal($instances,SQL_PARAMS_NAMED,'instance000');

    $params['modulename'] = $modulename;

    $courseselect = "";
    $sectionfield = "";
    $sectionjoin  = "";

    if ($courseid) {
        $courseselect = "AND cm.course = :courseid";
        $params['courseid'] = $courseid;
    }

    if ($sectionnum) {
        $sectionfield = ", cw.section AS sectionnum";
        $sectionjoin  = "LEFT JOIN {course_sections} cw ON cw.id = cm.section";
    }

    $sql = 'SELECT cm.*, m.name, md.name AS modname, s.section as sectionvalue '.$sectionfield.'
              FROM {course_modules} cm
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {'.$modulename.'} m ON m.id = cm.instance'.$sectionjoin.'
                   JOIN {course_sections} s ON s.id = cm.section
             WHERE m.id '.$sqlforinstances.' AND md.name = :modulename
                   '.$courseselect;

    return $DB->get_records_sql($sql, $params);
}