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
require_once('../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');


require_login();

$action  = optional_param('action', '', PARAM_RAW);
if ($action == 'removeformallanguage') {
    $langid = required_param('languageid', PARAM_INT);
    $DB->delete_records('block_formal_langs', array('id' => $langid));
    $DB->delete_records('block_formal_langs_perms', array('languageid' => $langid));
}
if ($action == 'flanguagevisibility') {
    $langid = required_param('languageid', PARAM_INT);
    $visible = required_param('visible', PARAM_INT);
    $context = required_param('context', PARAM_INT);
    block_formal_langs::update_language_visibility($langid, $visible, $context);
}
