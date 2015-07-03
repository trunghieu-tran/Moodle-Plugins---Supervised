<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
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
 * Settings for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot.'/question/type/preg/questiontype.php');
require_once($CFG->dirroot.'/blocks/formal_langs/settingslib.php');

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('questioneditingheading', get_string('questioneditingheading', 'qtype_preg'), ''));
    $qtypeobj = new qtype_preg;
    $engines = $qtypeobj->available_engines();
    $settings->add(new admin_setting_configselect('qtype_preg_defaultengine', get_string('defaultenginelabel', 'qtype_preg'),
                    get_string('defaultenginedescription', 'qtype_preg'), 'fa_matcher', $engines));
    $notations = $qtypeobj->available_notations();
    $settings->add(new admin_setting_configselect('qtype_preg_defaultnotation', get_string('defaultnotationlabel', 'qtype_preg'),
                    get_string('defaultnotationdescription', 'qtype_preg'), 'native', $notations));
    $a = get_string('objectname', 'qtype_preg');
    $settings->add(new block_formal_langs_admin_setting_language('qtype_preg_defaultlang',
                    get_string('defaultlanglabel', 'block_formal_langs'), get_string('defaultlangdescription', 'block_formal_langs', $a), '1', null));
    $settings->add(new admin_setting_configtext('qtype_preg_maxerrorsshown', get_string('maxerrorsshownlabel', 'qtype_preg'),
                    get_string('maxerrorsshowndescription', 'qtype_preg'), 5, PARAM_INT));
    $settings->add(new admin_setting_heading('debugheading', get_string('debugheading', 'qtype_preg'), ''));

    /******* FA limitations *******/
    $settings->add(new admin_setting_heading('fa_settings_heading', get_string('fa_settings_heading', 'qtype_preg'),
                    get_string('engine_heading_descriptions', 'qtype_preg')));
    $settings->add(new admin_setting_configtext('qtype_preg_fa_state_limit', get_string('fa_state_limit', 'qtype_preg'),
                    get_string('fa_state_limit_description', 'qtype_preg'), 250, PARAM_INT));
    $settings->add(new admin_setting_configtext('qtype_preg_fa_transition_limit', get_string('fa_transition_limit', 'qtype_preg'),
                    get_string('fa_transition_limit_description', 'qtype_preg'), 250, PARAM_INT));
    $settings->add(new admin_setting_configtext('qtype_preg_fa_simulation_state_limit', get_string('fa_simulation_state_limit', 'qtype_preg'),
                    get_string('fa_simulation_state_limit_description', 'qtype_preg'), 2000, PARAM_INT));
    $assertionsupport = array ('0' => get_string('assertfailmodeasis', 'qtype_preg'), '1' => get_string('assertfailmodemerge', 'qtype_preg'));
    $settings->add(new admin_setting_configselect('qtype_preg_assertfailmode', get_string('assertfailmodelabel', 'qtype_preg'),
                    get_string('assertfailmodedescription', 'qtype_preg'), '0', $assertionsupport));
}
