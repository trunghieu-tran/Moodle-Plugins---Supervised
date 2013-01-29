<?php
// This file is part of Correct Writing question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Correct Writing is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Correct Writing is distributed in the hope that it will be useful,
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
 * @copyright  2011 Sychev Oleg
 * @author     Mamontov Dmitry, Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class qtype_correctwriting_edit_form extends qtype_shortanswer_edit_form {

    /** Determines second time form, where descriptions controls is first shown.
        @var boolean
     */
    private $secondtimeform = false;

    /**  Fills an inner definition of form fields
         @param object mform form data
     */
    protected function definition_inner($mform) {
        global $CFG;
        // Add lexical error threshold field
        // Uncomment  some unused field
        /*
        $mform->addElement('text', 'lexicalerrorthreshold',
                           get_string('lexicalerrorthreshold', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('lexicalerrorthreshold', PARAM_FLOAT);
        $mform->setDefault('lexicalerrorthreshold', 0.33);
        $mform->addRule('lexicalerrorthreshold', null, 'required', null, 'client');
        $mform->addHelpButton('lexicalerrorthreshold', 'lexicalerrorthreshold', 'qtype_correctwriting');
        // Add lexical error weight field
        $mform->addElement('text', 'lexicalerrorweight',
                           get_string('lexicalerrorweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('lexicalerrorweight', PARAM_FLOAT);
        $mform->setDefault('lexicalerrorweight', 0.05);
        $mform->addRule('lexicalerrorweight', null, 'required', null, 'client');
        $mform->addHelpButton('lexicalerrorweight', 'lexicalerrorweight', 'qtype_correctwriting');
        */
        // Add absent mistake weight field
        $mform->addElement('text', 'absentmistakeweight',
                           get_string('absentmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('absentmistakeweight', PARAM_FLOAT);
        $mform->setDefault('absentmistakeweight', 0.1);
        $mform->addRule('absentmistakeweight', null, 'required', null, 'client');
        $mform->addHelpButton('absentmistakeweight', 'absentmistakeweight', 'qtype_correctwriting');
        // Add added mistake weight field
        $mform->addElement('text', 'addedmistakeweight',
                           get_string('addedmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('addedmistakeweight', PARAM_FLOAT);
        $mform->setDefault('addedmistakeweight', 0.1);
        $mform->addRule('addedmistakeweight', null, 'required', null, 'client');
        $mform->addHelpButton('addedmistakeweight', 'addedmistakeweight', 'qtype_correctwriting');
        // Add moved mistake weight field
        $mform->addElement('text', 'movedmistakeweight',
                           get_string('movedmistakeweight', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('movedmistakeweight', PARAM_FLOAT);
        $mform->setDefault('movedmistakeweight', 0.05);
        $mform->addRule('movedmistakeweight', null, 'required', null, 'client');
        $mform->addHelpButton('movedmistakeweight', 'movedmistakeweight', 'qtype_correctwriting');
        // Add hint grade border
        $mform->addElement('text', 'hintgradeborder',
                           get_string('hintgradeborder', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('hintgradeborder', PARAM_FLOAT);
        $mform->setDefault('hintgradeborder', 0.9);
        $mform->addRule('hintgradeborder', null, 'required', null, 'client');
        $mform->addHelpButton('hintgradeborder', 'hintgradeborder', 'qtype_correctwriting');
        //Add max mistake percentage
        $mform->addElement('text', 'maxmistakepercentage',
                           get_string('maxmistakepercentage', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('maxmistakepercentage', PARAM_FLOAT);
        $mform->setDefault('maxmistakepercentage', 0.7);
        $mform->addRule('maxmistakepercentage', null, 'required', null, 'client');
        $mform->addHelpButton('maxmistakepercentage', 'maxmistakepercentage', 'qtype_correctwriting');
        //Add "what is" hint penalty
        $mform->addElement('text', 'whatishintpenalty',
                           get_string('whatishintpenalty', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('whatishintpenalty', PARAM_FLOAT);
        $mform->setDefault('whatishintpenalty', 1.1);
        $mform->addRule('whatishintpenalty', null, 'required', null, 'client');
        $mform->addHelpButton('whatishintpenalty', 'whatishintpenalty', 'qtype_correctwriting');
        //Add "where" text hint penalty
        $mform->addElement('text', 'wheretxthintpenalty',
                           get_string('wheretxthintpenalty', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('wheretxthintpenalty', PARAM_FLOAT);
        $mform->setDefault('wheretxthintpenalty', 1.1);
        $mform->addRule('wheretxthintpenalty', null, 'required', null, 'client');
        $mform->addHelpButton('wheretxthintpenalty', 'wheretxthintpenalty', 'qtype_correctwriting');
        //Absent token hint penalty factor
        $mform->addElement('text', 'absenthintpenaltyfactor',
                           get_string('absenthintpenaltyfactor', 'qtype_correctwriting'),
                           array('size' => 6));
        $mform->setType('absenthintpenaltyfactor', PARAM_FLOAT);
        $mform->setDefault('absenthintpenaltyfactor', 1);
        $mform->addRule('absenthintpenaltyfactor', null, 'required', null, 'client');
        $mform->addHelpButton('absenthintpenaltyfactor', 'absenthintpenaltyfactor', 'qtype_correctwriting');

        $mform->setAdvanced('lexicalerrorthreshold');
        $mform->setAdvanced('lexicalerrorweight');
        $mform->setAdvanced('absentmistakeweight');
        $mform->setAdvanced('addedmistakeweight');
        $mform->setAdvanced('movedmistakeweight');
        $mform->setAdvanced('hintgradeborder');
        $mform->setAdvanced('maxmistakepercentage');
        $mform->setAdvanced('absenthintpenaltyfactor');

        $languages = block_formal_langs::available_langs();

        $mform->addElement('select', 'langid', get_string('langid', 'qtype_correctwriting'), $languages);
        $mform->addRule('langid', null, 'required', null, 'client');
        $mform->setDefault('langid', $CFG->qtype_correctwriting_defaultlang);
        $mform->addHelpButton('langid', 'langid', 'qtype_correctwriting');

        //Determine whether this is first time, second time or another time form.
        $name = optional_param('name', '', PARAM_TEXT);
        if ($name != '') {//Not first time form.
            $confirmed = optional_param('confirmed', false, PARAM_BOOL);
            if (!$confirmed) {
                $this->secondtimeform = true;
                if (array_key_exists('options', $this->question)) {
                    $this->secondtimeform = !array_key_exists('answers', $this->question->options);
                }
            }
            $mform->addElement('hidden', 'confirmed', true);
        }

        parent::definition_inner($mform);

        $answersinstruct = $mform->getElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_correctwriting'));
    }

    /**
     * Computes label for data
     * @param $textdata
     * @return string
     */
    function get_label($textdata) {
        $rows = count($textdata);
        $cols = 1;
        for ($i = 0; $i < count($textdata); $i++) {
            $len = textlib::strlen($textdata[$i]);
            if ($len > $cols) {
                $cols = textlib::strlen($textdata[$i]);
            }
        }
        // A tab for IE-like browser
        $cols += 2;
        $lf = '&#10;';
        $newtext = implode($lf, $textdata);
        // display: inline is used because label accepts only inline entities inside
        $attrs = array('style' => 'display: inline;', 'readonly' => 'readonly');
        $attrs['rows'] = $rows;
        $attrs['cols'] = $cols;
        $begin = html_writer::start_tag('textarea', $attrs);
        $end = html_writer::end_tag('textarea');
        return $begin . $newtext . $end;
    }

    function definition_after_data() {
        parent::definition_after_data();

        $mform =& $this->_form;
        $data = $mform->exportValues();
        //Get information about field data
        if (array_key_exists('answer', $data)) {
            $lang = block_formal_langs::lang_object($data['langid']);
            if ($lang!=null) {
                //Parse descriptions to populate script
                foreach($data['answer'] as $key => $value) {//This loop will pass only on non-empty answers.
                    $processedstring = $lang->create_from_string($value);
                    $tokens = $processedstring->stream->tokens;
                    $fractionel = $mform->getElementValue('fraction[' . $key .']');
                    $fraction = floatval($fractionel[0]);
                    if (count($tokens) > 0 && ($fraction >= $data['hintgradeborder'])) {//Answer needs token descriptions.
                        $textdata = array();
                        foreach($tokens as $token) {
                            $textdata[] = htmlspecialchars($token->value());
                        }
                        $newtext = $this->get_label($textdata);
                        $element=$mform->getElement('lexemedescriptions[' . $key . ']');
                        $element->setLabel($newtext);
                        $element->setRows(count($textdata));
                    } else {//No need to enter token descriptions.
                        $mform->removeElement('lexemedescriptions[' . $key . ']');
                        $mform->addElement('hidden', 'lexemedescriptions[' . $key . ']', '');//Adding hidden element with empty string to not confuse save_question_options.
                    }
                }
            }
        }

        //Now we should pass empty answers too.
        $answercount = $data['noanswers'];
        for ($i = 0; $i < $answercount; $i++) {
            if (!array_key_exists('answer', $data) || !array_key_exists($i, $data['answer'])) {//This answer is empty and was not processed by previous loop.
                $mform->removeElement('lexemedescriptions[' . $i . ']');
                $mform->addElement('hidden', 'lexemedescriptions[' . $i . ']', '');
            }
        }
        
    }

    function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption)
    {

        $repeated = parent::get_per_answer_fields($mform,$label,$gradeoptions,$repeatedoptions,$answersoption);

        $repeated[] = $mform->createElement('textarea', 'lexemedescriptions',
                                            get_string('lexemedescriptions', 'qtype_correctwriting'),
                                            array('rows' => 2, 'cols' => 80));
        $repeatedoptions['lexemedescriptions']['type'] = PARAM_TEXT;
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
        global $DB;
        $question = parent::data_preprocessing_answers($question, $withanswerfiles);
        $key = 0;


        if (array_key_exists('options',$question) && array_key_exists('answers',$question->options)) {
            $lang = block_formal_langs::lang_object($question->options->langid);

            $answerids = $DB->get_fieldset_select('question_answers', 'id', " question = '{$question->id}' ");
            $descriptions = array();
            if ($answerids != null) {
                $descriptions = block_formal_langs_processed_string::get_descriptions_as_array('question_answers', $answerids);
            }

            foreach ($question->options->answers as $id => $answer) {
                $string = $lang->create_from_db('question_answers',$id);
                $string = '';
                if (count($descriptions[$id]) != 0) {
                   if (strlen(trim($descriptions[$id][0])) == 0) {
                       $string = "\n";
                   }
                }
                $string = $string . implode("\n", $descriptions[$id]);
                $question->options->answers[$id]->lexemedescriptions = $string;

                $question->lexemedescriptions[$key] = $answer->lexemedescriptions;
                $key++;
            }
        }
        return $question;
    }

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        // Scan for errors
        $lang = block_formal_langs::lang_object($data['langid']);
        $br = html_writer::empty_tag('br');
        foreach($data['answer'] as $key => $value) {
            $processedstring = $lang->create_from_string($value);
            $stream = $processedstring->stream;

            if (count($stream->errors) != 0) {
                $errormessages = array(get_string('foundlexicalerrors', 'qtype_correctwriting'));
                foreach($stream->errors as $error) {
                    $token = $stream->tokens[$error->tokenindex];
                    $tokenpos = $token->position();
                    $emesg = $error->errormessage . $br;
                    $left = $tokenpos->colstart();
                    $emesg .= ($left <= 0) ? '' : textlib::substr($value, 0, $left);
                    $left =  $tokenpos->colend() -  $tokenpos->colstart();
                    $middlepart = ($left <= 0) ? '' : textlib::substr($value,  $tokenpos->colstart() , $left);
                    $emesg .= '<b>' . $middlepart . '</b>';
                    $emesg .= textlib::substr($value, $tokenpos->colend());
                    $errormessages[] = $emesg;
                }
                $errors["answer[$key]"] = implode($br, $errormessages);
            }
        }

        if ($this->secondtimeform) {//Second time form is a unique case: first appearance of token descriptions before user.
            // We place it here, because it will look nicer and won't shift any of strings
            // in lexeme descriptions field
            $mesg = get_string('enterlexemedescriptions', 'qtype_correctwriting');
            if (array_key_exists('answer[0]', $errors) != 0 ) {
                $errors['answer[0]'] .= $br . $mesg;
            }  else {
                $errors['answer[0]'] = $mesg;
            }
        } else {//More than second time form, so check descriptions count.
            $fractions = $data['fraction'];
            foreach($data['answer'] as $key => $value) {
                $processedstring = $lang->create_from_string($value);
                $stream = $processedstring->stream;
                $tokens = $stream->tokens;

                if (count($tokens) > 0 && $fractions[$key] >= $data['hintgradeborder']) {//Token descriptions needed for this answer.
                    $descriptionstring = $data['lexemedescriptions'][$key];
                    if (trim($value) != '' /*&& trim($descriptionstring) != ''*/) {//Uncomment if empty descriptions will be good as "no descriptions" variant.
                        $descriptions = explode(PHP_EOL, $descriptionstring);
                        $fieldkey =  "answer[$key]";
                        $mesg = null;
                        if (count($tokens) > count($descriptions)) {
                            $mesg = get_string('writemoredescriptions', 'qtype_correctwriting');
                        }
                        if (count($tokens) < count($descriptions)) {
                            $mesg = get_string('writelessdescriptions', 'qtype_correctwriting');
                        }
                        if ($mesg) {
                            if (array_key_exists($fieldkey, $errors) == false) {
                                $errors[$fieldkey] = $mesg;
                            } else {
                                if (textlib::strlen($errors[$fieldkey]) == 0) {
                                    $errors[$fieldkey] = $mesg;
                                } else {
                                    $errors[$fieldkey] .= $br . $mesg;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $errors;
    }

    public function qtype() {
        return 'correctwriting';
    }
 }
