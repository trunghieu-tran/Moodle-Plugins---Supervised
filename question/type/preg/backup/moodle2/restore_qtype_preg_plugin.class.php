<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * restore plugin class that provides the necessary information
 * needed to restore one preg qtype plugin
 *
 * @author     Valeriy Streltsov, Volgograd State Technical University
 * @copyright  2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_preg_plugin extends restore_qtype_plugin {

    /**
     * Process the qtype/preg element
     */
    public function process_preg($data) {
		$this->process_extra_question_fields($data);
	}

    protected function remap_extra_question_fields(&$data) {
        $answersarr = explode(',', $data->answers);
        foreach ($answersarr as $key => $answer) {
            $answersarr[$key] = $this->get_mappingid('question_answer', $answer);
         }
        $data->answers = implode(',', $answersarr);
    }
}
