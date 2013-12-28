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
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');

global $USER;

require_login();
$contextid = required_param('context', PARAM_INT);
$context = context::instance_by_id($contextid);
if ($context !== false) {
    $PAGE->set_context($context);
    require_capability('block/formal_langs:addinstance', $context);
    $action  = optional_param('action', '', PARAM_RAW);
    $isglobal  = optional_param('global', '', PARAM_RAW);
    $isglobal =  ($isglobal == 'Y');
    $langid = required_param('languageid', PARAM_INT);
    $lang = $DB->get_record('block_formal_langs', array('id' => $langid));
    $caneditall = has_capability('block/formal_langs:editalllanguages', $context);
    $caneditown = has_capability('block/formal_langs:editownlanguages', $context);
    $canchagevisibility = has_capability('block/formal_langs:changelanguagevisibility', $context);
    if ($action == 'removeformallanguage' && $lang !== false) {
        if ($caneditall || ($caneditown && $lang->author == $USER->id)) {
            $DB->delete_records('block_formal_langs', array('id' => $langid));
            $DB->delete_records('block_formal_langs_perms', array('languageid' => $langid));
        }
    }
    if ($action == 'flanguagevisibility' && $lang !== false) {
        $visible = required_param('visible', PARAM_INT);
        $context = required_param('context', PARAM_INT);
        if ($canchagevisibility) {
            $scope = block_formal_langs::update_language_visibility($langid, $visible, $context);

            if ($isglobal) {
                $result = $DB->get_records_sql('SELECT  `id` ,  `shortname`   FROM  {course} course  WHERE NOT  EXISTS (
                                                    SELECT *  FROM  {block_formal_langs_perms} p,  {context} context
                                                    WHERE p.contextid = context.id
                                                    AND context.contextlevel = ' . CONTEXT_COURSE . '
                                                    AND context.instanceid = course.id
                                                    AND p.languageid = ?
                                               )',  array($langid)
                                              );
                echo json_encode(array_values($result));
            } else {
                echo json_encode('(' . get_string('inherited_' . $scope, 'block_formal_langs') . ')');
            }
        }
    }

}