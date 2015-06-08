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
    $a = get_string('objectname', 'qtype_correctwriting');
    $settings->add(new block_formal_langs_admin_setting_language('qtype_correctwriting_defaultlang',
                    get_string('defaultlanglabel', 'block_formal_langs'), get_string('defaultlangdescription', 'block_formal_langs', $a), '1', null));

    $settings->add(new admin_setting_configtext('qtype_correctwriting_maxorderscount', get_string('maxorderscountlabel', 'qtype_correctwriting'),
                    get_string('maxorderscount', 'qtype_correctwriting'), 5000, PARAM_INT));
    $settings->add(new admin_setting_configtextarea(
        'qtype_correctwriting_special_tokens_list',
        get_string('lexicalanalyzerlistsettingname', 'qtype_correctwriting'),
        get_string('lexicalanalyzerlistsettingdescription', 'qtype_correctwriting'),
        "",
        PARAM_RAW,
        60,
        20
    ));

    $settings->add(new admin_setting_configtext(
        'qtype_correctwriting_max_temp_lcs',
        get_string('maxtemplcssettingname', 'qtype_correctwriting'),
        get_string('maxtemplcssettingdescription', 'qtype_correctwriting'),
        30000,
        PARAM_INT,
        20
    ));
}
