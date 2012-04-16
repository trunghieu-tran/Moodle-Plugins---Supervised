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
 * Correct writing question editing form.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Correctwriting question editing form definition.
 *
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class qtype_correctwriting_edit_form extends question_edit_form {
    
    /**  Fills an inner definition of form fields
         @param object mform form data
     */
    protected function definition_inner($mform) {
        $menu = array(
            get_string('caseno', 'qtype_correctwriting'),
            get_string('caseyes', 'qtype_correctwriting')
        );
        
        $mform->addElement('select', 'casesensivity',
                get_string('casesensitive', 'qtype_shortanswer'), $menu);
    }
 
    public function qtype() {
        return 'correctwriting';
    }
 }
 