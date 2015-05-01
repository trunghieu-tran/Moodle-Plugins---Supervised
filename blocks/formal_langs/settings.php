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
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');

if(is_object($ADMIN)) {
    $string = get_string('formallangsvisibilitysettings', 'block_formal_langs');
    $ADMIN->add('blocksettings', new admin_externalpage('formallangsglobalsettings', $string,  $CFG->wwwroot . '/blocks/formal_langs/globalsettings.php'));
    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configtext(
            'block_formal_langs_maximum_lexical_backracking_execution_time',
            get_string('maximumlexicalbacktrackingexecutuiontimesettingname', 'block_formal_langs'),
            get_string('maximumlexicalbacktrackingexecutuiontimesettingdescription', 'block_formal_langs'),
            30,
            PARAM_INT,
            20
        ));
        $settings->add(new admin_setting_configtext(
            'block_formal_langs_maximum_variations_of_typo_correction',
            get_string('maximumvariationsoftypocorrectionsettingname', 'block_formal_langs'),
            get_string('maximumvariationsoftypocorrectionsettingdescription', 'block_formal_langs'),
            5,
            PARAM_INT,
            20
        ));
    }
}
