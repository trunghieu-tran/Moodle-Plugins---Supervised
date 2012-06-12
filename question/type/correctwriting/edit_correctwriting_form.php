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
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
/**
 * Correctwriting question editing form definition.
 *
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class qtype_correctwriting_edit_form extends qtype_shortanswer_edit_form {
    
    /** Determines, whether lexeme descriptions is shown
        @var boolean
     */
    private $hasdescriptions = false;
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
        // Add hint grade border
        $mform->addElement('text', 'hintgradeborder', 
                           get_string('hintgradeborder', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('hintgradeborder', PARAM_FLOAT);
        $mform->setDefault('hintgradeborder', 0.9);
        $mform->addRule('hintgradeborder', null, 'required', null, 'client');
        //Add max mistake percentage
        $mform->addElement('text', 'maxmistakepercentage', 
                           get_string('maxmistakepercentage', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('maxmistakepercentage', PARAM_FLOAT);
        $mform->setDefault('maxmistakepercentage', 0.7);
        $mform->addRule('maxmistakepercentage', null, 'required', null, 'client');
        
        $languages = block_formal_langs::available_langs();
        
        $mform->addElement('select', 'langid', get_string('langid', 'qtype_correctwriting'), $languages);
        $mform->addRule('langid', null, 'required', null, 'client');
        
        parent::definition_inner($mform);
    }
    
    function definition_after_data() {
        parent::definition_after_data();
        
        //Get information about field data
        if ($this->hasdescriptions == true) {
            $mform =& $this->_form;
            $data = $mform->exportValues();
         
            
            $lang = block_formal_langs::lang_object($data['langid']);
            if ($lang!=null) {
                //Parse descriptions to populate script
                foreach($data['answer'] as $key => $value) {
                    $processedstring = $lang->create_from_string($value);
                    $tokens = $processedstring->stream->tokens;
                    $textdata = array();
                    foreach($tokens as $token) {
                        $textdata[] = $token->value();
                    }
                    $newtext = implode('<br />', $textdata);
                    $element=$mform->getElement('lexemedescriptions[' . $key . ']');
                    $element->setLabel($newtext);
                }
         
            }
        }
    }
    
    function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption)
    {
        global $_REQUEST;
        
        
        $repeated = parent::get_per_answer_fields($mform,$label,$gradeoptions,$repeatedoptions,$answersoption);

        // We use this, because this method is called before validation
        // But we still can look into request to find out what we are looking to 
        $show_lexeme_descriptions = array_key_exists('lexemedescriptions', $_REQUEST);
        $second_time_form = array_key_exists('name', $_REQUEST) && 
                            !array_key_exists('lexemedescriptions', $_REQUEST);
        $show_lexeme_descriptions = $show_lexeme_descriptions || $second_time_form;
        if (array_key_exists('options', $this->question)) {
            $show_lexeme_descriptions = $show_lexeme_descriptions || array_key_exists('answers', $this->question->options);
        }
        if ($show_lexeme_descriptions) {
            $this->hasdescriptions = true;
            $repeated[] = $mform->createElement('textarea', 'lexemedescriptions',
                                                get_string('lexemedescriptions', 'qtype_correctwriting'), 
                                                array('rows' => 25, 'cols' => 80));
            $repeatedoptions['lexemedescriptions']['type'] = PARAM_TEXT;
        }
        return $repeated;
    }
    
    protected function data_preprocessing($question) {
        
        $question = parent::data_preprocessing($question);
        
        return $question;
    }
    
    /**
     * Perform setting data for lexemes
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_answers($question, $withanswerfiles = false) {        
        $question = parent::data_preprocessing_answers($question, $withanswerfiles);
        $key = 0;
        if (array_key_exists('options',$question) && array_key_exists('answers',$question->options)) {
            foreach ($question->options->answers as $answer) {
                $question->lexemedescriptions[$key] = $answer->lexemedescriptions;
                $key++;
            }
        }
        return $question;
    }
    
    public function validation($data, $files) {
        
        $errors = parent::validation($data, $files);
        if (array_key_exists('lexemedescriptions', $data) == false) {
            $this->first_time = false;
            // We place it here, because it will look nicer and won't shift any of strings 
            // in lexeme descriptions field
            $errors['category'] = get_string('enterlexemedescriptions', 'qtype_correctwriting');
        } else {
            $lang = block_formal_langs::lang_object($data['langid']);
            foreach($data['answer'] as $key => $value) {
                $processedstring = $lang->create_from_string($value);
                $stream = $processedstring->stream;
                $tokens = $stream->tokens;
                if (count($stream->errors) != 0) {
                    $errormessages = array(get_string('foundlexicalerrors', 'qtype_correctwriting'));
                    foreach($stream->errors as $error) {
                         $errormessages[] = $error->errormessage;
                    }
                    $errors["answer[$key]"] = implode("<BR>", $errormessages);
                }
                $descriptions = explode(PHP_EOL, $data['lexemedescriptions'][$key]);
                if (strlen($value) != 0 && count($descriptions)!=0 ) {
                    if (count($tokens) > count($descriptions)) {
                        $errors["lexemedescriptions[$key]"] = get_string('writemoredescriptions', 'qtype_correctwriting');
                    }
                    if (count($tokens) < count($descriptions)) {
                        $errors["lexemedescriptions[$key]"] = get_string('writelessdescriptions', 'qtype_correctwriting');
                    }
                }
            }
        }
        
        // If errors don't found - exit
        if (count($errors) !=0 ) {
            return $errors;
        }
        
        return $errors;
    }
    public function qtype() {
        return 'correctwriting';
    }
 }
 