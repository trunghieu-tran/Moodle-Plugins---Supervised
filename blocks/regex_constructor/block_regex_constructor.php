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

 require_once($CFG->dirroot.'/blocks/moodleblock.class.php');

class block_regex_constructor extends block_base {

    private $id = 'regex_conscructor_block_window_opener_a';

    public function init() {
        $this->title = get_string('regex_constructor', 'block_regex_constructor');
    }

    public function get_content() {
    global $CFG;
    if ($this->content !== null) {
      return $this->content;
    }
 
    $this->content         =  new stdClass;
    $this->content->text   = '<a id="'.$this->id.'">' . get_string('openwindow', 'block_regex_constructor') . '</a>';
    $this->content->footer = '';

    $this->page->requires->jquery();
    $this->page->requires->jquery_plugin('ui');
    $this->page->requires->jquery_plugin('ui-css');

    $jsmodule = array('name' => 'poasquestion_text_and_button',
                                'fullpath' => '/question/type/poasquestion/poasquestion_text_and_button.js');
    $jsargs = array(
                '90%',
                'todo' // TODO dialog header
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
    $this->page->requires->js_init_call('M.preg_authoring_tools_script.init', $pregjsargs, true, $pregjsmodule);
 
    return $this->content;
  }
}