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
 * Defines the editing form for the preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_text_and_button.php');

/**
 * Preg editing form definition.
 */
class qtype_preg_edit_form extends qtype_shortanswer_edit_form {
    /**
     * This is overloaded method.
     * Get the list of form elements to repeat, one for each answer.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options
     *      field holding an array of answers
     * @return array of form fields.
     */

    protected function get_per_answer_fields($mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] = $mform->createElement('hidden', 'regextests', '');
        $repeated[] = $mform->createElement('preg_text_and_button', 'answer', $label, 'regex_test');
        $repeated[] = $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'feedback', get_string('feedback', 'question'),
            array('rows' => 5), $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['regextests']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        $mform = $this->_form;
        list($repeated, $repeatedoptions) = parent::get_hint_fields($withclearwrong, $withshownumpartscorrect);

        $langselect = $mform->getElement('langid');
        $langs = $langselect->getSelected();
        $langobj = block_formal_langs::lang_object($langs[0]);
        $hintoptions = array(
            'hintmatchingpart' => get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('hintcolouredstring', 'qtype_preg')),
            'hintnextchar' => get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('hintnextchar', 'qtype_preg')),
            'hintnextlexem' => get_string('hintbtn', 'qbehaviour_adaptivehints', get_string('hintnextlexem', 'qtype_preg', $langobj->lexem_name()))
        );

        $repeated[] = $mform->createElement('select', 'interactivehint',
            get_string('hintbtn', 'qbehaviour_adaptivehints', ''), $hintoptions);
        return array($repeated, $repeatedoptions);
    }

    /**
     * Perform the necessary preprocessing for the hint fields.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_hints($question, $withclearwrong = false,
            $withshownumpartscorrect = false) {
        if (empty($question->hints)) {
            return $question;
        }
        $question = parent::data_preprocessing_hints($question, $withclearwrong, $withshownumpartscorrect);

        foreach ($question->hints as $hint) {
            $question->interactivehint[] = $hint->options;
        }

        return $question;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        global $CFG, $COURSE;

        question_bank::load_question_definition_classes($this->qtype());
        $qtypeclass = 'qtype_'.$this->qtype();
        $qtype = new $qtypeclass;

        $engines = $qtype->available_engines();
        $mform->addElement('select', 'engine', get_string('engine', 'qtype_preg'), $engines);
        $mform->setDefault('engine', $CFG->qtype_preg_defaultengine);
        $mform->addHelpButton('engine', 'engine', 'qtype_preg');

        $notations = $qtype->available_notations();
        $mform->addElement('select', 'notation', get_string('notation', 'qtype_preg'), $notations);
        $mform->setDefault('notation', $CFG->qtype_preg_defaultnotation);
        $mform->addHelpButton('notation', 'notation', 'qtype_preg');

        $mform->addElement('selectyesno', 'usecharhint', get_string('usecharhint', 'qtype_preg'));
        $mform->setDefault('usecharhint', 0);
        $mform->addHelpButton('usecharhint', 'usecharhint', 'qtype_preg');
        $mform->addElement('text', 'charhintpenalty', get_string('charhintpenalty', 'qtype_preg'), array('size' => 3));
        $mform->setDefault('charhintpenalty', '0.2');
        $mform->setType('charhintpenalty', PARAM_FLOAT);
        $mform->addHelpButton('charhintpenalty', 'charhintpenalty', 'qtype_preg');

        $mform->addElement('selectyesno', 'uselexemhint', get_string('uselexemhint', 'qtype_preg'));
        $mform->setDefault('uselexemhint', 0);
        $mform->addHelpButton('uselexemhint', 'uselexemhint', 'qtype_preg');
        $mform->addElement('text', 'lexemhintpenalty', get_string('lexemhintpenalty', 'qtype_preg'), array('size' => 3));
        $mform->setDefault('lexemhintpenalty', '0.4');
        $mform->setType('lexemhintpenalty', PARAM_FLOAT);
        $mform->addHelpButton('lexemhintpenalty', 'lexemhintpenalty', 'qtype_preg');

        // Fetch course context if it is possible.
        $context = null;
        if ($COURSE != null) {
            if (is_a($COURSE, 'stdClass')) {
                $context = context_course::instance($COURSE->id);
            } else {
                $context = $COURSE->get_context();
            }
        }

        $currentlanguages = block_formal_langs::available_langs( $context );
        //$langs = block_formal_langs::available_langs();// TODO - add context.
        $mform->addElement('select', 'langid', get_string('langselect', 'qtype_preg'), $currentlanguages);
        $mform->setDefault('langid', $CFG->qtype_preg_defaultlang);
        $mform->addHelpButton('langid', 'langselect', 'qtype_preg');
        $mform->addElement('text', 'lexemusername', get_string('lexemusername', 'qtype_preg'), array('size' => 54));
        $mform->setDefault('lexemusername', '');
        $mform->addHelpButton('lexemusername', 'lexemusername', 'qtype_preg');
        $mform->setAdvanced('lexemusername');
        $mform->setType('lexemusername', PARAM_TEXT);

        $gradeoptions = question_bank::fraction_options();
        $mform->addElement('select', 'hintgradeborder', get_string('hintgradeborder', 'qtype_preg'), $gradeoptions);
        $mform->setDefault('hintgradeborder', 1);
        $mform->addHelpButton('hintgradeborder', 'hintgradeborder', 'qtype_preg');
        $mform->setAdvanced('hintgradeborder');

        $mform->addElement('selectyesno', 'exactmatch', get_string('exactmatch', 'qtype_preg'));
        $mform->addHelpButton('exactmatch', 'exactmatch', 'qtype_preg');
        $mform->setDefault('exactmatch', 1);

        $mform->addElement('text', 'correctanswer', get_string('correctanswer', 'qtype_preg'), array('size' => 54));
        $mform->addHelpButton('correctanswer', 'correctanswer', 'qtype_preg');
        $mform->setType('correctanswer', PARAM_RAW);

        // Set hint availability determined by engine capabilities.
        foreach ($engines as $engine => $enginename) {
            $questionobj = new qtype_preg_question;
            $querymatcher = $questionobj->get_query_matcher($engine);
            if (!$querymatcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING) ||
                !$querymatcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)
                ) {
                $mform->disabledIf('hintgradeborder', 'engine', 'eq', $engine);
                $mform->disabledIf('usecharhint', 'engine', 'eq', $engine);
                $mform->disabledIf('charhintpenalty', 'engine', 'eq', $engine);
                $mform->disabledIf('uselexemhint', 'engine', 'eq', $engine);
                $mform->disabledIf('lexemhintpenalty', 'engine', 'eq', $engine);
                $mform->disabledIf('langid', 'engine', 'eq', $engine);
                $mform->disabledIf('lexemusername', 'engine', 'eq', $engine);
            }
        }

        parent::definition_inner($mform);

        $answersinstruct = $mform->getElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_preg'));

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $trimmedcorrectanswer = trim($data['correctanswer']);
        // If no correct answer is entered, we should think it is correct to not force teacher;
        // otherwise we must check that it match with at least one 100% grade answer.
        $correctanswermatch = ($trimmedcorrectanswer=='');
        $passhintgradeborder = false;
        $fractions = $data['fraction'];

        // Fill in some default data that could be absent due to disabling relevant form controls.
        if (!array_key_exists('hintgradeborder', $data)) {
            $data['hintgradeborder'] = 1;
        }

        if (!array_key_exists('usecharhint', $data)) {
            $data['usecharhint'] = false;
        }

        if (!array_key_exists('uselexemhint', $data)) {
            $data['uselexemhint'] = false;
        }

        $i = 0;
        question_bank::load_question_definition_classes($this->qtype());
        $questionobj = new qtype_preg_question;

        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $hintused = ($data['usecharhint'] || $data['uselexemhint']) && $fractions[$key] >= $data['hintgradeborder'];
                // Create matcher to check regex for errors and try to match correct answer.
                $matcher = $questionobj->get_matcher($data['engine'], $trimmedanswer, $data['exactmatch'],
                        $questionobj->get_modifiers($data['usecase']), (-1)*$i, $data['notation'], $hintused);
                if ($matcher->errors_exist()) {// There were errors in the matching process.
                    $regexerrors = $matcher->get_error_messages();// Show no more than max errors.
                    $errors['answer['.$key.']'] = '';
                    foreach ($regexerrors as $regexerror) {
                        $errors['answer['.$key.']'] .= $regexerror.'<br />';
                    }
                } else if ($trimmedcorrectanswer != '' && $data['fraction'][$key] == 1) {
                    // Correct answer (if supplied) should match at least one 100% grade answer.
                    if ($matcher->match($trimmedcorrectanswer)->full) {
                        $correctanswermatch=true;
                    }
                }
                if ($fractions[$key] >= $data['hintgradeborder']) {
                    $passhintgradeborder = true;
                }
            }
            $i++;
        }

        if ($correctanswermatch == false) {
            $errors['correctanswer'] = get_string('nocorrectanswermatch', 'qtype_preg');
        }

        if ($passhintgradeborder == false && $data['usecharhint']) {// No answer pass hint grade border.
            $errors['hintgradeborder'] = get_string('nohintgradeborderpass', 'qtype_preg');
        }

        $querymatcher = $questionobj->get_query_matcher($data['engine']);
        // If engine doesn't support subexpression capturing, than no placeholders should be in feedback.
        if (!$querymatcher->is_supporting(qtype_preg_matcher::SUBEXPRESSION_CAPTURING)) {
            $feedbacks = $data['feedback'];
            foreach ($feedbacks as $key => $feedback) {
                if (is_array($feedback)) {// On some servers feedback is HTMLEditor, on another it is simple text area.
                    $feedback = $feedback['text'];
                }
                if (!empty($feedback) && preg_match('/\{\$([1-9][0-9]*|\w+)\}/', $feedback) == 1) {
                    $errors['feedback['.$key.']'] = get_string('nosubexprcapturing', 'qtype_preg', $querymatcher->name());
                }
            }
        }

        // Check that interactive hint settings doesn't contradict overall hint settings.
        $interactivehints = $data['interactivehint'];
        foreach($interactivehints as $key => $hint) {
            if ($hint == 'hintnextchar' && $data['usecharhint'] != true) {
                $errors['interactivehint['.$key.']'] = get_string('unallowedhint', 'qtype_preg', get_string('hintnextchar', 'qtype_preg'));
            }
            if ($hint == 'hintnextlexem' && $data['uselexemhint'] != true) {
                $langs = block_formal_langs::available_langs();
                $langobj = block_formal_langs::lang_object(array_keys($langs)[0]);
                $errors['interactivehint['.$key.']'] = get_string('unallowedhint', 'qtype_preg', get_string('hintnextlexem', 'qtype_preg', $langobj->lexem_name()));
            }
        }

        return $errors;
    }

    public function qtype() {
        return 'preg';
    }

}
