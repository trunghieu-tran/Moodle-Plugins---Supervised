<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for the CorrectWriting question type.
 *
 * @package    qtype_correctwriting
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/questiontype.php');
require_once($CFG->dirroot.'/blocks/formal_langs/settingslib.php');

if($ADMIN->fulltree) {

$settings->add(new admin_setting_heading('questioneditingheading', get_string('questioneditingheading', 'qtype_correctwriting'), ''));
$settings->add(new block_formal_langs_admin_setting_showable_languages('qtype_correctwriting_showablelangs', get_string('showedlangslabel', 'qtype_correctwriting'), get_string('showedlangsdescription', 'qtype_correctwriting'), array_flip(array('1', '2', '3', '4')), null));
$settings->add(new block_formal_langs_admin_setting_language('qtype_correctwriting_defaultlang', get_string('defaultlanglabel', 'qtype_correctwriting'), get_string('defaultlangdescription', 'qtype_correctwriting'), '1', null));

}
