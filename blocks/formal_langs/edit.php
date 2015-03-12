<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines a page for editing language
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Mamontov Dmitriy Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blocks
 */

require_once('../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_editing_form.php');

global $USER;

function insert_language_from_form($formdata, $systemcontextid) {
    global $DB;
    $record =  $formdata;
    unset($record->id);
    unset($record->new);
    $record->name = 'user-defined language';
    $languageid = $DB->insert_record('block_formal_langs', $record);

    $perms = array(
        'languageid' => $languageid,
        'contextid' => $systemcontextid,
        'visible' => $record->visible
    );
    $perms = (object)$perms;
    $DB->insert_record('block_formal_langs_perms', $perms);
}
$backurl = optional_param('backurl', $CFG->wwwroot . '/', PARAM_RAW);
$languageid = optional_param('id', 0, PARAM_INT);
$isnew = optional_param('new', false, PARAM_BOOL);
$contextid = optional_param('context', 0, PARAM_INT);


if ($isnew == false && $languageid == 0) {
    throw new moodle_exception('lang_not_found', 'block_formal_langs');
}
/** @var moodle_database $DB */
if ($isnew == false) {
    $languageobject = $DB->get_record('block_formal_langs', array('id' => $languageid));
    if ($languageobject == false) {
        throw new moodle_exception('lang_not_found', 'block_formal_langs');
    } else {
        $languageobject->new = 0;
        $languagestring = $languageobject->uiname . ' ' . $languageobject->version;
        $heading = get_string('editinglanguage', 'block_formal_langs', $languagestring);
    }
} else {
    $languageobject = null;
    $heading = get_string('editingnewlanguage', 'block_formal_langs');
}

$url = new moodle_url('/blocks/formal_langs/edit.php', array('id'=> $languageid));

require_login();
$PAGE->requires->jquery();
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);
if ($contextid > 0) {
    $context = context::instance_by_id($contextid);
}
if (!has_capability('moodle/course:manageactivities', $context) && !has_capability('moodle/course:managequestion', $context)) {
    throw new required_capability_exception($context, 'moodle/course:manageactivities', 'nopermissions', '');
}

if ($isnew && !has_capability('block/formal_langs:addlanguage', $context)) {
    throw new required_capability_exception($context, 'block/formal_langs:addlanguage', 'nopermissions', '');
}

$PAGE->set_pagelayout('admin');

$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->navbar->add($heading);

$caneditall = has_capability('block/formal_langs:editalllanguages', $context);
$caneditown = has_capability('block/formal_langs:editownlanguages', $context);
$cannoteditanything =  !$caneditall && !$caneditown;

if ($cannoteditanything) {
    redirect($backurl);
}

$form = new language_editing_form();
if ($languageobject != null) {
    if (textlib::strlen($languageobject->scanrules) == 0) {
        redirect($backurl);
    }


    if (!$caneditall && ($languageobject->author != $USER->id)) {
        redirect($backurl);
    }

    $form->set_data($languageobject);
}

if ($formdata = $form->get_data())
{
    $systemcontextid =  context_system::instance()->id;
    $submit = $formdata->submitbutton;
    unset($formdata->submitbutton);

    if ($formdata->new) {
        $formdata->author = $USER->id;
    } else {
        $formdata->author = $languageobject->author;
    }

    $formdataasarray = (array)$formdata;
    if (array_key_exists('visible', $formdataasarray) == false) {
        $formdata->visible = 0;
    }
    if ($formdata->new) {
        insert_language_from_form($formdata, $systemcontextid);
    } else {
        $record =  $formdata;
        unset($record->new);

        if ($submit == get_string('language_editing_submit_save_as_new', 'block_formal_langs')) {
            insert_language_from_form($formdata, $systemcontextid);
        } else {
            $DB->update_record('block_formal_langs', $record);

            $conditions = array(
                'languageid' => $record->id,
                'contextid' => $systemcontextid
            );
            $DB->set_field('block_formal_langs_perms', 'visible', $record->visible, $conditions);
        }
    }
    redirect($backurl);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();