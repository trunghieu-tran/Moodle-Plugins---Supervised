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

    /** List of floating value fields of the form - to automatically process them
     * Key is field name, value contains default value and whether field is advanced
     * There should be strings with "key" and "key_help" in the language file.
     */
    private $floatfields = array('hintgradeborder' => array('default' => 0.9, 'advanced' => true, 'min' => 0, 'max' => 1),           // Hint grade border.
                                 'maxmistakepercentage' => array('default' => 0.7, 'advanced' => true, 'min' => 0, 'max' => 1),      // Max mistake percentage.
                                 'absentmistakeweight' => array('default' => 0.1, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true),  //Absent token mistake weight field
                                 'addedmistakeweight' => array('default' => 0.1, 'advanced' => true, 'min' => 0, 'max' => 1, 'required' => true),    //Extra token mistake weight field
                                );

    private $hintfloatfields = array('whatishintpenalty' => array('default' => 1.1, 'advanced' => false, 'min' => 0, 'max' => 2),       // "What is" hint penalty.
                                     'wheretxthintpenalty' => array('default' => 1.1, 'advanced' => false, 'min' => 0, 'max' => 2),     // "Where" text hint penalty.
                                     'absenthintpenaltyfactor' => array('default' => 1.0, 'advanced' => true, 'min' => 0, 'max' => 100),// Absent token mistake hint penalty factor.
                                     'wherepichintpenalty' => array('default' => 1.1, 'advanced' => false, 'min' => 0, 'max' => 2),     // "Where" picture hint penalty.
                                     'howtofixpichintpenalty' => array('default' => 1.1, 'advanced' => false, 'min' => 0, 'max' => 2)   // "How to fix" picture hint penalty.
                                    );

    private $analyzers = null;
    /** Contains list of answer ids, that should be hidden
     *  @var array of hidden descriptions
     */
    private $hiddendescriptions;
    /** Contains total count of answers
     *  @var int
     */
    private $answercount;

    protected $jsmodule = array(
        'name' => 'question_type_correctwriting',
        'fullpath' => '/question/type/correctwriting/module.js'
    );

    /**  Fills an inner definition of form fields
     *    @param MoodleQuickForm $mform form data
     */
    protected function definition_inner($mform) {
        global $CFG, $PAGE, $COURSE, $DB;

        $PAGE->requires->jquery();

        // Create global floating fields before changing array.
        foreach ($this->floatfields as $name => $params) {
            $mform->addElement('text', $name, get_string($name, 'qtype_correctwriting'), array('size' => 6));
            $mform->setType($name, PARAM_FLOAT);
            $mform->setDefault($name, $params['default']);
            $mform->addRule($name, null, 'required', null, 'client');
            $mform->addHelpButton($name, $name, 'qtype_correctwriting');
            if ($params['advanced']) {
                $mform->setAdvanced($name);
            }
        }


        // Now change floating fields to include ones from analyzers for data preprocessing and validation purposes.
        question_bank::load_question_definition_classes($this->qtype());
        $qtypeclass = 'qtype_'.$this->qtype();
        /** @var qtype_correctwriting $qtype */
        $qtype = new $qtypeclass;
        $this->analyzers = $qtype->analyzers();
        foreach ($this->analyzers as $name) {
            $classname = 'qtype_correctwriting_' . $name;
            /** @var qtype_correctwriting_abstract_analyzer $analyzer */
            $analyzer = new $classname;
            $fields = $analyzer->float_form_fields();
            foreach ($fields as $field) {
                $name = $field['name'];
                unset($field['name']);
                $this->floatfields[$name] = $field;
            }
        }
        // Fetch course context if can
        $context = null;
        if ($COURSE != null) {
            if (is_a($COURSE, 'stdClass')) {
                $context = context_course::instance($COURSE->id);
            } else {
                $context = $COURSE->get_context();
            }
        }

        $currentlanguages = block_formal_langs::available_langs( $context );
        $languages = $currentlanguages;
        $mform->addElement('select', 'langid', get_string('langid', 'qtype_correctwriting'), $languages);
        $mform->setDefault('langid', $CFG->qtype_correctwriting_defaultlang);
        $mform->addHelpButton('langid', 'langid', 'qtype_correctwriting');

        // Determine whether this is first time, second time or another time form.
        $name = optional_param('name', '', PARAM_TEXT);
        if ($name != '') {// Not first time form.
            $confirmed = optional_param('confirmed', false, PARAM_BOOL);
            if (!$confirmed) {
                $this->secondtimeform = true;
                if (array_key_exists('options', (array)($this->question))) {
                    $this->secondtimeform = !array_key_exists('answers', $this->question->options);
                }
            }
            $mform->addElement('hidden', 'confirmed', true);
            // Warning in Moodle 2.5 shows, that we must explicitly setType for
            // this field
            $mform->setType('confirmed', PARAM_BOOL);
        }





        parent::definition_inner($mform);

        // Move answer instructions before answers, as we inserted other sections betweens them and answers fields.
        /** @var HTML_QuickForm_static $answersinstruct */
        $answersinstruct = $mform->removeElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_correctwriting'));
        $mform->insertElementBefore($answersinstruct, 'answerhdr');
    }

    /** Overload parent function to add other controls before answer fields.
     *  @param MoodleQuickForm $mform
     *  @param $label
     *  @param $gradeoptions
     *  @param $minoptions
     *  @param $addoptions
     */
    protected function add_per_answer_fields(&$mform, $label, $gradeoptions,
            $minoptions = QUESTION_NUMANS_START, $addoptions = QUESTION_NUMANS_ADD) {
        // Adding custom sections.
        $this->definition_additional_sections($mform);
        // Calling parent to actually add fields.
        parent::add_per_answer_fields($mform, $label, $gradeoptions, $minoptions, $addoptions);
    }

    /** Place additional sections on the form:
     * one section for each analyzer and a hinting options section.
     * @var MoodleQuickForm $mform
     */
    protected function definition_additional_sections(&$mform) {
        // Analyzer sections.
        $analyzers = $this->analyzers;
        unset($analyzers[0x400]);
        //  Disable syntax analyzers
        $mform->addElement('hidden', 'issyntaxanalyzerenabled', true);
        $mform->setType('issyntaxanalyzerenabled', PARAM_BOOL);
        $mform->setDefault('issyntaxanalyzerenabled', 0);
        foreach ($analyzers as $name) {
            $classname = 'qtype_correctwriting_' . $name;
            /** @var qtype_correctwriting_abstract_analyzer $analyzer */
            $analyzer = new $classname;
            // Start section.
            $uiname = get_string($name, 'qtype_correctwriting');
            $mform->addElement('header', $name . 'hdr', $uiname);
            $mform->addHelpButton($name . 'hdr', $name, 'qtype_correctwriting');
            // Add control whether to use analyzer.
            $a = core_text::strtolower(core_text::substr($uiname, 0, 1)) . core_text::substr($uiname, 1);// Decapitalise first letter.
            $formname = 'is' . str_replace('_', '', $name) . 'enabled';
            $mform->addElement('selectyesno', $formname, get_string('usesomething', 'qtype_correctwriting', $a));
            $mform->setType($formname, PARAM_BOOL);
            // Disable all groups but enable sequence analyzer
            $default = 1;
            if ($formname != 'issequenceanalyzerenabled' && $formname != 'islexicalanalyzerenabled') {
                $default = 0;
            }
            if ($formname == 'islexicalanalyzerenabled') {
                $mform->addRule($formname, null, 'required', null, 'client');
            }
            $mform->setDefault($formname, $default);
            // TODO - default to admin config setting - use or not.
            // Add analyzer controls.
            $analyzer->form_section_definition($mform);
        }

        //Hinting section.
        $mform->addElement('header', 'hintinghdr', get_string('hinting', 'qtype_correctwriting'));
        $mform->addHelpButton('hintinghdr', 'hinting', 'qtype_correctwriting');
        foreach ($this->hintfloatfields as $name => $params) {
            $mform->addElement('text', $name, get_string($name, 'qtype_correctwriting'), array('size' => 6));
            $mform->setType($name, PARAM_FLOAT);
            $mform->setDefault($name, $params['default']);
            $mform->addRule($name, null, 'required', null, 'client'); // TODO - should they be really required?



            $mform->addHelpButton($name, $name, 'qtype_correctwriting');
            if ($params['advanced']) {
                $mform->setAdvanced($name);
            }
        }
        $this->floatfields = $this->floatfields + $this->hintfloatfields;

        // TODO - add rules controlling enabling/disabling of hints due to using of analyzers.
    }

    /**
     * Computes label for data
     * @param $textdata
     * @return string
     */
    protected function get_label($textdata) {
        $rows = count($textdata);
        $cols = 1;
        for ($i = 0; $i < count($textdata); $i++) {
            $len = core_text::strlen($textdata[$i]);
            if ($len > $cols) {
                $cols = core_text::strlen($textdata[$i]);
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
        // Extract created question, loaded by get_options
        $question = (array)$this->question;
        // Get information about field data
        if (array_key_exists('answer', $data)) {
            $lang = block_formal_langs::lang_object($data['langid']);
            if ($lang!=null) {
                $index = 0;
                //Parse descriptions to populate script
                foreach($data['answer'] as $key => $value) {//This loop will pass only on non-empty answers.
                    $processedstring = $lang->create_from_string($value);
                    $tokens = $processedstring->stream->tokens;
                    $fraction = 0;
                    $fractionloaded = false;
                    // If submitted form, take  fraction from POST-array
                    // otherwise, we can use submitted question to get information on answer
                    if (array_key_exists('fraction' , $data)) {
                        if (array_key_exists($key, $data['fraction'])) {
                            $fraction = floatval($data['fraction'][$key]);
                            $fractionloaded = true;
                        }
                    }

                    // If loading from post array failed, try get fraction from base question options
                    if ($fractionloaded == false) {
                        // If we created question for first time, there will be no options in question
                        // so we skip them
                        if (array_key_exists('options', $question)) {
                            $answers = $question['options']->answers;
                            $answerids = array_keys($answers);
                            $answerid = $answerids[$index];
                            $fraction = floatval($answers[$answerid]->fraction);
                        }
                    }
                    $index++;

                    if (count($tokens) > 0 && ($fraction >= $data['hintgradeborder'])) {//Answer needs token descriptions.
                        $textdata = array();
                        if ($lang->could_parse() && $data['issyntaxanalyzerenabled']) {
                            $tree = $processedstring->syntaxtree;
                            $treelist = $processedstring->tree_to_list();
                            foreach($treelist as $node) {
                                /** @var block_formal_langs_ast_node_base $node */
                                $string = $node->value();
                                if (is_object($string)) {
                                    $string = $string->string();
                                }
                                $textdata[] = htmlspecialchars($string);
                            }
                        } else {
                            foreach($tokens as $token) {
                                /** @var block_formal_langs_token_base $token */
                                $textdata[] = htmlspecialchars($token->value());
                            }
                        }
                        $newtext = $this->get_label($textdata);
                        $element=$mform->getElement('lexemedescriptions[' . $key . ']');
                        $element->setLabel($newtext);
                        $element->setRows(count($textdata));
                        if (array_key_exists($key, $data['lexemedescriptions'])) {
                            $element->setCols($this->compute_columns_for_text($data['lexemedescriptions'][$key]));
                        }

                    } else {
                        //No need to enter token descriptions.
                        // Force element to be hidden
                        $this->hide_answer_lexeme_description($key);

                        // Create empty textarea for label
                        $newtext = $this->get_label(array("", ""));
                        $element=$mform->getElement('lexemedescriptions[' . $key . ']');
                        $element->setLabel($newtext);

                        //$mform->removeElement('lexemedescriptions[' . $key . ']');
                        //$mform->removeElement('descriptionslabel[' . $key . ']');
                        //$mform->addElement('hidden', 'lexemedescriptions[' . $key . ']', '');//Adding hidden element with empty string to not confuse save_question_options.
                    }
                }
            }
        }

        //Now we should pass empty answers too.
        $answercount = $data['noanswers'];
        $this->answercount = $answercount;
        $this->init_text_input($answercount);

        for ($i = 0; $i < $answercount; $i++) {
            if (!array_key_exists('answer', $data) || !array_key_exists($i, $data['answer'])) {//This answer is empty and was not processed by previous loop.
                // Force element to be hidden
                $exists = false;
                if (count($this->hiddendescriptions) != 0) {
                   $exists = in_array($i, $this->hiddendescriptions);
                }
                if (!$exists) {
                    $this->hiddendescriptions[] = $i;
                    $this->hide_answer_lexeme_description($i);


                    // Create empty textarea for label
                    $newtext = $this->get_label(array("", ""));
                    $element=$mform->getElement('lexemedescriptions[' .  $i . ']');
                    $element->setLabel($newtext);
                }


                //$mform->removeElement('lexemedescriptions[' . $i . ']');
                //$mform->removeElement('descriptionslabel[' . $i . ']');
                //$mform->addElement('hidden', 'lexemedescriptions[' . $i . ']', '');
            }
        }
    }

     protected function init_text_input($count) {
         global $PAGE;
         $lexerurl = new moodle_url('/question/type/correctwriting/scanstring.php');
         $PAGE->requires->js_init_call(
             'M.question_type_correctwriting.form.init_text_input',
             array( $count, $lexerurl->out(true) ),
             false,
             $this->jsmodule
         );
     }

     protected function hide_answer_lexeme_description($i) {
        global $PAGE;
        $PAGE->requires->js_init_call(
             'M.question_type_correctwriting.form.hide_description_field',
             array( (string)$i ),
             false,
             $this->jsmodule
        );
     }

     /**
      * Returns column count for specified text
      * @param string $text a text
      * @return int a count of columns
      */
     protected function compute_columns_for_text($text)  {
        $lines = explode("\n", $text);
        $max = 0;
        if (count($lines) != 0) {
            foreach($lines as $line) {
                $max = max($max, core_text::strlen($line));
            }
        }
        if ($max < 80) {
            $max = 80;
        }
        return $max;
    }


    public function display() {
        $this->hiddendescriptions = array();
        $this->answercount = 0;
        parent::display();
        $this->print_javascript_for_hiding_and_changing_text();
    }

     public function print_javascript_for_hiding_and_changing_text() {
         global $PAGE;
         $params = array(
             $this->hiddendescriptions
         );
         $PAGE->requires->js_init_call(
             'M.question_type_correctwriting.form.hide_descriptions',
             $params,
             false,
             $this->jsmodule
         );
     }

    function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption)
    {
        // A replace for standard get_per_answer_fields, extending a fields for
        // answer and moving fraction to a next line
        $repeated = array();
        $repeated[] = $mform->createElement('textarea', 'answer',
            $label, array('rows' => 1, 'cols' => 80));
        $repeated[] = $mform->createElement('select', 'fraction',
            get_string('grade'), $gradeoptions);
        /**
         * @var HTML_QuickForm_static $static
         */
        $static = $mform->createElement('static', 'descriptionslabel', get_string('tokens', 'qtype_correctwriting'), get_string('lexemedescriptions', 'qtype_correctwriting'));
        $repeated[] = $static;
        $repeated[] = $mform->createElement('textarea', 'lexemedescriptions',
                                            get_string('lexemedescriptions', 'qtype_correctwriting'),
                                            array('rows' => 2, 'cols' => 80));
        $repeated[] = $mform->createElement('editor', 'feedback',
            get_string('feedback', 'question'), array('rows' => 5), $this->editoroptions);

        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['lexemedescriptions']['type'] = PARAM_TEXT;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';

        return $repeated;
    }

    protected function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);

        //Remove trailing 0s from floating value fields
        foreach ($this->floatfields as $name => $params) {
            if (isset($question->$name)) {
                $question->$name = 0 + $question->$name;
            }
        }

        return $question;
    }

    /**
     * Perform setting data for lexemes
     * @param object $question the data being passed to the form.
     * @param  boolean $withanswerfiles
     * @return object $question the modified data.
     */
    protected function data_preprocessing_answers($question, $withanswerfiles = false) {
        global $DB;
        $question = parent::data_preprocessing_answers($question, $withanswerfiles);
        $key = 0;

        $aquestion = (array)$question;

        if (array_key_exists('options',$aquestion) && array_key_exists('answers',$question->options)) {
            //$lang = block_formal_langs::lang_object($question->options->langid);

            $answerids = $DB->get_fieldset_select('question_answers', 'id', " question = '{$question->id}' ");
            $descriptions = array();
            if ($answerids != null) {
                $descriptions = block_formal_langs_processed_string::get_descriptions_as_array('question_answers', $answerids);
            }

            foreach ($question->options->answers as $id => $answer) {
                if ($answer->fraction >= $question->hintgradeborder) {
                    // $string = $lang->create_from_db('question_answers',$id);
                    $string = '';
                    if (count($descriptions[$id]) != 0) {
                        if (strlen(trim($descriptions[$id][0])) == 0) {
                            $string = "\n";
                        }
                    }
                    $string = $string . implode("\n", $descriptions[$id]);
                    $question->options->answers[$id]->lexemedescriptions = $string;

                    $question->lexemedescriptions[$key] = $answer->lexemedescriptions;
                }
                $key++;
            }
        }
        return $question;
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        $mform = $this->_form;
        list($repeated, $repeatedoptions) = parent::get_hint_fields($withclearwrong, $withshownumpartscorrect);

        $repeated[] = $mform->createElement('advcheckbox', 'whatis_', get_string('options', 'question'),
                    get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('whatis', 'qtype_correctwriting', get_string('mistakentokens', 'qtype_correctwriting'))));
        $repeated[] = $mform->createElement('advcheckbox', 'wheretxt_', '',
                    get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('wheretxthint', 'qtype_correctwriting', get_string('mistakentokens', 'qtype_correctwriting'))));
        $repeated[] = $mform->createElement('advcheckbox', 'wherepic_', '',
                    get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('wherepichint', 'qtype_correctwriting', get_string('mistakentokens', 'qtype_correctwriting'))));
        return array($repeated, $repeatedoptions);
    }

    /**
     * Perform the necessary preprocessing for the hint fields.
     * @param object $question the data being passed to the form.
     * @param boolean $withclearwrong
     * @param boolean $withshownumpartscorrect
     * @return object $question the modified data.
     */
    protected function data_preprocessing_hints($question, $withclearwrong = false,
            $withshownumpartscorrect = false) {
        if (empty($question->hints)) {
            return $question;
        }
        $question = parent::data_preprocessing_hints($question, $withclearwrong, $withshownumpartscorrect);


        foreach ($question->hints as $hint) {
            $hints = explode("\n", $hint->options);
            $question->whatis_[] = in_array('whatis_', $hints);
            $question->wheretxt_[] = in_array('wheretxt_', $hints);
            $question->wherepic_[] = in_array('wherepic_', $hints);
        }

        return $question;
    }
    /** Converts errors from the stream to HTML formatted mistakes
     *  @param string $value a parsed string
     *  @param block_formal_langs_token_stream  $stream a tokenized stream
     *  @return string of error representation
     */
    static public function convert_tokenstream_errors_to_formatted_messages($value, $stream) {
        $result = '';
        $br = html_writer::empty_tag('br');
        if (count($stream->errors) != 0) {
            $errormessages = array(get_string('foundlexicalerrors', 'qtype_correctwriting'));
            foreach($stream->errors as $error) {
                /** @var block_formal_langs_token_base $token */
                $token = $stream->tokens[$error->tokenindex];
                /** @var block_formal_langs_node_position $tokenpos */
                $tokenpos = $token->position();
                $emesg = $error->errormessage . $br;
                $left = $tokenpos->colstart();
                $emesg .= ($left <= 0) ? '' : core_text::substr($value, 0, $left);
                $left =  $tokenpos->colend() -  $tokenpos->colstart() + 1;
                $middlepart = ($left <= 0) ? '' : core_text::substr($value,  $tokenpos->colstart() , $left);
                $emesg .= '<b>' . $middlepart . '</b>';
                $emesg .= core_text::substr($value, $tokenpos->colend() + 1);
                $errormessages[] = $emesg;
                $result = implode($br, $errormessages);
            }
        }
        return $result;
    }

    /**
     * Returns position of last or first token in tree
     * @param block_formal_langs_ast_node_base $root root node
     * @param bool $first whether we should take first token (or last if false)
     * @return block_formal_langs_node_position
     */
    protected function get_position_for_token_in_tree($root, $first) {
        $children = $root->childs();
        $result = null;
        if (count($children)  == 0) {
            $result = $root->position();
        } else {
            if ($first) {
                $result = self::get_position_for_token_in_tree($children[0], $first);
            } else {
                $lastindex = count($children) - 1;
                $result = self::get_position_for_token_in_tree($children[$lastindex], $first);
            }
        }
        return $result;
    }

    /**
     * Formats string for  parsing error. First position must be before second position
     * @param string $text text
     * @param block_formal_langs_node_position $position1 position of first node
     * @param block_formal_langs_node_position $position2 position of second node
     * @return string error part
     */
    protected function format_string_for_parse_error($text, $position1, $position2) {
        $e = function($a) {
            $a = htmlspecialchars($a);
            $t = str_repeat('&nbsp;', 4);
            return str_replace(array("\t", ' '), array($t, '&nbsp;'), $a);
        };
        $result = $e(core_text::substr($text, 0 , $position1->stringstart()));
        $lengthoffirst = $position1->stringend() - $position1->stringstart() + 1;
        $result .= html_writer::tag('b', $e(core_text::substr($text, $position1->stringstart(), $lengthoffirst)));
        if ($position1->stringend() + 1 != $position2->stringstart()) {
            $lengthofspacebetween = $position2->stringstart() - $position1->stringend() - 1;
            $result .= $e(core_text::substr($text, $position1->stringend() + 1, $lengthofspacebetween));
        }
        $lengthofsecond = $position2->stringend() - $position2->stringstart() + 1;
        $result .= html_writer::tag('b', $e(core_text::substr($text, $position2->stringstart(), $lengthofsecond)));
        $result .= $e(core_text::substr($text, $position2->stringend()+1));
        return $result;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $islexicalanalyzerenabled = false;
        if (array_key_exists('islexicalanalyzerenabled', $data)) {
            $islexicalanalyzerenabled = intval($data['islexicalanalyzerenabled']);
        }

        $issequenceanalyzerenabled = false;
        if (array_key_exists('issequenceanalyzerenabled', $data)) {
            $issequenceanalyzerenabled = intval($data['issequenceanalyzerenabled']);
        }

        $issyntaxanalyzerenabled = false;
        if (array_key_exists('issyntaxanalyzerenabled', $data)) {
            $issyntaxanalyzerenabled = intval($data['issyntaxanalyzerenabled']);
        }

        $isenumanalyzerenabled = false;
        if (array_key_exists('isenumanalyzerenabled', $data)) {
            $isenumanalyzerenabled = intval($data['isenumanalyzerenabled']);
        }

        question_bank::load_question_definition_classes($this->qtype());
        $qtypeclass = 'qtype_'.$this->qtype();
        /** @var qtype_correctwriting $qtype */
        $qtype = new $qtypeclass;
        $this->analyzers = $qtype->analyzers();
        foreach ($this->analyzers as $name) {
            $analyzername = str_replace('_', '', $name);
            $variablename = 'is' . $analyzername . 'enabled';
            $isanalyzerenabled = $$variablename;
            if ($isanalyzerenabled) {
                $classname = 'qtype_correctwriting_' . $name;
                /** @var qtype_correctwriting_abstract_analyzer $analyzer */
                $analyzer = new $classname;
                $requiredanalyzers = $analyzer->require_analyzers();
                $alreadyerror = false;
                if (count($requiredanalyzers)) {
                    foreach($requiredanalyzers as $requiredanalyzerbasename) {
                        $requiredanalyzerbasename = str_replace('qtype_correctwriting_', '', $requiredanalyzerbasename);
                        $requiredanalyzerbasename = str_replace('_', '', $requiredanalyzerbasename);
                        $isrequiredanalyzerenabled = ${'is' . $requiredanalyzerbasename . 'enabled'};
                        if (!$isrequiredanalyzerenabled) {
                            $errors[$variablename] = get_string(
                                $analyzername . 'require' . $requiredanalyzerbasename,
                                'qtype_correctwriting'
                            );
                            $alreadyerror = true;
                        }
                    }
                }
                if (!$alreadyerror) {
                    if (array_key_exists('langid', $data)) {
                        if (is_number($data['langid'])) {
                            $langid = intval($data['langid']);
                            $lang = block_formal_langs::lang_object($langid);
                            if (is_object($lang)) {
                                if ($analyzer->is_lang_compatible($lang) == false) {
                                    $errors[$variablename] = get_string(
                                        $analyzername . 'isincompatiblewithlang',
                                        'qtype_correctwriting'
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$islexicalanalyzerenabled
            && !$issequenceanalyzerenabled
            && !$isenumanalyzerenabled
            && !$issyntaxanalyzerenabled) {
            $errors['islexicalanalyzerenabled'] = get_string('analyzersaredisabled','qtype_correctwriting');
        }

        // TODO: Remove, when nice version of syntax analyzer will be implemented
        if ($issyntaxanalyzerenabled) {
            $errors['issyntaxanalyzerenabled'] = get_string('syntaxanalyzerisdisabled','qtype_correctwriting');
        }
        
        // Validate floating fields for min/max borders.
        foreach ($this->floatfields as $name => $params) {
            if ($data[$name] < $params['min']) {
                $errors[$name] = get_string('toosmallfloatvalue', 'qtype_correctwriting', $params['min']);
            }
            if ($data[$name] > $params['max']) {
                $errors[$name] = get_string('toobigfloatvalue', 'qtype_correctwriting', $params['max']);
            }
        }

        // Scan for errors
        $lang = block_formal_langs::lang_object($data['langid']);
        $br = html_writer::empty_tag('br');
        foreach($data['answer'] as $key => $value) {
            $processedstring = $lang->create_from_string($value);
            $stream = $processedstring->stream;

            if (is_object($lang)) {
                if (count($stream->errors) == 0
                    && ($isenumanalyzerenabled || $issyntaxanalyzerenabled)
                    && $lang->could_parse()
                ) {
                    $syntaxtree = $processedstring->syntaxtree;
                    if (count($syntaxtree) > 1) {
                        $position1 = self::get_position_for_token_in_tree($syntaxtree[0], false);
                        $position2 = self::get_position_for_token_in_tree($syntaxtree[1], true);
                        $text = self::format_string_for_parse_error($value, $position1, $position2);
                        $errors["answer[$key]"] = get_string(
                            'analyzersrequirevalidsyntaxtree',
                            'qtype_correctwriting',
                            $text
                        );
                    }
                }
            }

            if (count($stream->errors) != 0) {
                $form = 'qtype_correctwriting_edit_form';
                $errormessages = $form::convert_tokenstream_errors_to_formatted_messages($value, $stream);
                $errors["answer[$key]"] = $errormessages;
            }
        }

        if ($this->secondtimeform) {//Second time form is a unique case: first appearance of token descriptions before user.
            $mesg = get_string('enterlexemedescriptions', 'qtype_correctwriting');
            $errors['descriptionslabel[0]'] = $mesg;
        } else {//More than second time form, so check descriptions count.
            $fractions = $data['fraction'];
            foreach($data['answer'] as $key => $value) {
                $processedstring = $lang->create_from_string($value);
                $stream = $processedstring->stream;
                if ($lang->could_parse() && $data['issyntaxanalyzerenabled']) {
                    $tree = $processedstring->syntaxtree;
                    $treelist = $processedstring->tree_to_list();
                    $tokens = $treelist;
                    if (count($tree) > 1) {
                        $fieldkey =  "answer[$key]";
                        if (array_key_exists($fieldkey, $errors) == false) {
                            $errors[$fieldkey] = get_string('parseerror', 'qtype_correctwriting');
                        } else {
                            if (core_text::strlen($errors[$fieldkey]) == 0) {
                                $errors[$fieldkey] = get_string('parseerror', 'qtype_correctwriting');
                            } else {
                                $errors[$fieldkey] .= $br . get_string('parseerror', 'qtype_correctwriting');
                            }
                        }
                    }
                } else {
                    $tokens = $stream->tokens;
                }
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
                                if (core_text::strlen($errors[$fieldkey]) == 0) {
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
