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
 * A library of settings classes for the plugins, using languages from block
 *
 * @package    formal_langs
 * @copyright  2013 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Admin settings class for a language select with lazy loading
 */
class block_formal_langs_admin_setting_language extends admin_setting_configselect {
    public function load_choices() {
        global $CFG;

        if (is_array($this->choices)) {
            return true;
        }

        require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
        $this->choices = block_formal_langs::available_langs();

        return true;
    }

}

/**
 * Admin settings class for showed languages at language select
 */
class block_formal_langs_admin_setting_showable_languages extends admin_setting_configmulticheckbox {

    public function load_choices() {
        $this->choices = block_formal_langs::all_languages();
        return true;
    }

}

