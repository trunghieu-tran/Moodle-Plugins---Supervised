<?php
// This file is part of Preg project - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg project is free software: you can redistribute it and/or modify
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
 * Defines block class for Regex Constructor block.
 *
 * @copyright &copy; 2013 Oleg Sychev, Volgograd State Technical University
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blocks
 */

global $CFG;
global $PAGE;

require_once($CFG->dirroot.'/blocks/moodleblock.class.php');

class block_regex_constructor extends block_base {

    private $id = 'regex_conscructor_block_window_opener_a';

    public function init() {
        $this->title = get_string('regex_constructor', 'block_regex_constructor');
    }

    function get_required_javascript() {
        parent::get_required_javascript();

        $this->page->requires->jquery_plugin('poasquestion-jquerymodule', 'qtype_poasquestion');
        $this->page->requires->jquery();
        $this->page->requires->jquery_plugin('ui');
        $this->page->requires->jquery_plugin('ui-css');
        $this->page->requires->jquery_plugin('poasquestion-jquerymodule', 'qtype_poasquestion');

        $this->page->requires->string_for_js('collapseall', 'moodle');
        $this->page->requires->string_for_js('expandall', 'moodle');
        $this->page->requires->string_for_js('savechanges', 'moodle');
        $this->page->requires->string_for_js('cancel', 'moodle');
        $this->page->requires->string_for_js('close', 'editor');
    }

    public function get_content() {
        global $CFG;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content =  new stdClass;
        $this->content->text = '<noscript>' . get_string('jsrequired', 'block_regex_constructor') . '</noscript>' .
                               '<a href id="' . $this->id . '" style="display:none">' . get_string('openwindow', 'block_regex_constructor') . '</a>' .
                               '<script type="text/javascript">document.getElementById("' . $this->id . '").style.display="block";</script>';
        $this->content->footer = '';

        $jsmodule = array('name' => 'poasquestion_text_and_button',
                                    'fullpath' => '/question/type/poasquestion/poasquestion_text_and_button.js');

        $jsargs = array(
                    '90%',
                    get_string('regex_constructor', 'block_regex_constructor')
                );
        $this->page->requires->js_init_call('M.poasquestion_text_and_button.init', $jsargs, true, $jsmodule);

        $jsargshandler = array(
                $this->id,
                ''
            );

        $this->page->requires->js_init_call('M.poasquestion_text_and_button.set_handler', $jsargshandler, true, $jsmodule);

        $pregjsmodule = array(
                    'name' => 'preg_authoring_tools_script',
                    'fullpath' => '/question/type/preg/authoring_tools/preg_authoring_tools_script.js'
                );
        $pregjsargs = array(
            $CFG->wwwroot,
            'TODO - poasquestion_text_and_button_objname',  // 'M.poasquestion_text_and_button' ?
        );
        $this->page->requires->yui_module('moodle-form-shortforms',
            'M.form.shortforms',
            array(array("formid"=>"mformauthoring"))
        );
        $this->page->requires->js_init_call('M.preg_authoring_tools_script.init', $pregjsargs, true, $pregjsmodule);

        return $this->content;
    }
}
