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


require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');

/**
 * Correctwriting question editing form definition.
 *
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class qtype_correctwriting_edit_form extends qtype_shortanswer_edit_form {
    
    /**  Fills an inner definition of form fields
         @param object mform form data
     */
    protected function definition_inner($mform) {
        // Add lexical error threshold field     
        $mform->addElement('text', 'lexicalerrorthreshold', 
                           get_string('lexicalerrorthreshold', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('lexicalerrorthreshold', PARAM_FLOAT);
        $mform->setDefault('lexicalerrorthreshold', 0.33);
        $mform->addRule('lexicalerrorthreshold', null, 'required', null, 'client');
        // Add lexical error weight field
        $mform->addElement('text', 'lexicalerrorweight', 
                           get_string('lexicalerrorweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('lexicalerrorweight', PARAM_FLOAT);
        $mform->setDefault('lexicalerrorweight', 0.05);
        $mform->addRule('lexicalerrorweight', null, 'required', null, 'client');
        // Add absent mistake weight field
        $mform->addElement('text', 'absentmistakeweight', 
                           get_string('absentmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('absentmistakeweight', PARAM_FLOAT);
        $mform->setDefault('absentmistakeweight', 0.1);
        $mform->addRule('absentmistakeweight', null, 'required', null, 'client');
        // Add added mistake weight field
        $mform->addElement('text', 'addedmistakeweight', 
                           get_string('addedmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('addedmistakeweight', PARAM_FLOAT);
        $mform->setDefault('addedmistakeweight', 0.1);
        $mform->addRule('addedmistakeweight', null, 'required', null, 'client');
        // Add moved mistake weight field
        $mform->addElement('text', 'movedmistakeweight', 
                           get_string('movedmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('movedmistakeweight', PARAM_FLOAT);
        $mform->setDefault('movedmistakeweight', 0.05);
        $mform->addRule('movedmistakeweight', null, 'required', null, 'client');
        
        parent::definition_inner($mform);
    }
    
    function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption)
    {
        $repeated = parent::get_per_answer_fields($mform,$label,$gradeoptions,$repeatedoptions,$answersoption);
        $repeated[] = $mform->createElement('textarea', 'lexemedescriptions',
                                            get_string('lexemedescriptions', 'qtype_correctwriting'), 
                                            array('rows' => 12, 'cols' => 80));
        $repeatedoptions['lexemedescriptions']['type'] = PARAM_TEXT;
        
        return $repeated;
    }
    public function qtype() {
        return 'correctwriting';
    }
 }
 