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
defined('MOODLE_INTERNAL') || die;

/**
 * A library of settings classes for the plugins, using languages from block
 *
 * @package    formal_langs
 * @copyright  2013 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;

require_once($CFG->dirroot.'/blocks/formal_langs/settingslib.php');

if($ADMIN->fulltree) {

$settings->add(new block_formal_langs_admin_setting_showable_languages('block_formal_langs_showablelangs', get_string('showedlangslabel', 'block_formal_langs'), get_string('showedlangsdescription', 'block_formal_langs'), array_flip(array('1', '2', '3', '4')), null));

}