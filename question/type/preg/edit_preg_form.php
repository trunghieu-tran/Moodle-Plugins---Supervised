<?php
/**
 * Defines the editing form for the preg question type.
 *
 * @copyright &copy; 2008  Sychev Oleg 
 * @author Sychev Oleg, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
require_once($CFG->dirroot.'/question/type/shortanswer/edit_shortanswer_form.php');
/**
 * preg editing form definition.
 */
class question_edit_preg_form extends question_edit_shortanswer_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        global $CFG;
        global $QTYPES;

        $engines = $QTYPES[$this->qtype()]->available_engines();
        $mform->addElement('select','engine',get_string('engine','qtype_preg'),$engines);
        $mform->setDefault('engine','preg_php_matcher');
        $mform->addHelpButton('engine','engine','qtype_preg');
        $mform->addElement('selectyesno', 'usehint', get_string('usehint','qtype_preg'));
        $mform->setDefault('usehint',0);
        $mform->addHelpButton('usehint','usehint','qtype_preg');
        $mform->addElement('text', 'hintpenalty', get_string('hintpenalty','qtype_preg'), array('size' => 3));
        $mform->setDefault('hintpenalty','0.2');
        $mform->setType('hintpenalty', PARAM_NUMBER);
        $mform->addHelpButton('hintpenalty','hintpenalty','qtype_preg');
        $creategrades = get_grade_options();
        $mform->addElement('select','hintgradeborder',get_string('hintgradeborder','qtype_preg'),$creategrades->gradeoptions);
        $mform->setDefault('hintgradeborder',1);
        $mform->addHelpButton('hintgradeborder','hintgradeborder','qtype_preg');
        $mform->addElement('selectyesno', 'exactmatch', get_string('exactmatch','qtype_preg'));
        $mform->addHelpButton('exactmatch','exactmatch','qtype_preg');
        $mform->setDefault('exactmatch',1);
        $mform->addElement('text', 'correctanswer', get_string('correctanswer','qtype_preg'), array('size' => 54));
        $mform->addHelpButton('correctanswer','correctanswer','qtype_preg');

        //Set hint availability determined by engine capabilities
        foreach ($engines as $engine => $enginename) {
            require_once($CFG->dirroot . '/question/type/preg/'.$engine.'.php');
            $querymatcher = new $engine;
            if (!$querymatcher->is_supporting(preg_matcher::PARTIAL_MATCHING)) {
                $mform->disabledIf('hintgradeborder','engine', 'eq', $engine);
            }
            if (!$querymatcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                $mform->disabledIf('usehint','engine', 'eq', $engine);
                $mform->disabledIf('hintpenalty','engine', 'eq', $engine);
            }
        }

        parent::definition_inner($mform);

        $answersinstruct =& $mform->getElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_preg'));

    }

    function validation($data, $files) {
        global $QTYPES;
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $trimmedcorrectanswer = trim($data['correctanswer']);
        $correctanswermatch = ($trimmedcorrectanswer=='');
        $passhintgradeborder = false;
        $fractions = $data['fraction'];

        //Fill in some default data that could be absent due to disabling relevant form controls
        if (!array_key_exists('hintgradeborder', $data)) {
            $data['hintgradeborder'] = 1;
        }

        if (!array_key_exists('usehint', $data)) {
            $data['usehint'] = false;
        }

        $i = 0;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $matcher =& $QTYPES[$this->qtype()]->get_matcher($data['engine'],$trimmedanswer, $data['exactmatch'], $data['usecase'], (-1)*$i);
                if($matcher->is_error_exists()) {//there are errors in the matching process
                    $regexerrors = $matcher->get_errors();
                    $errors['answer['.$key.']'] = '';
                    foreach ($regexerrors as $regexerror) {
                        $errors['answer['.$key.']'] .= $regexerror.'<br/>';
                    }
                } elseif ($trimmedcorrectanswer != '' && $data['fraction'][$key] == 1 && $matcher->match($trimmedcorrectanswer)) {
                    $correctanswermatch=true;
                }
                if ($fractions[$key] >= $data['hintgradeborder']) {
                    $passhintgradeborder = true;
                }
            }
            $i++;
        }
        
        if ($correctanswermatch == false) {
            $errors['correctanswer']=get_string('nocorrectanswermatch','qtype_preg');
        }

        if ($passhintgradeborder == false && $data['usehint']) {//no asnwer pass hint grade border
            $errors['hintgradeborder']=get_string('nohintgradeborderpass','qtype_preg');
        }

        $querymatcher = new $data['engine'];
        //If engine doesn't support subpattern capturing, than no placeholders should be in feedback
        if (!$querymatcher->is_supporting(preg_matcher::SUBPATTERN_CAPTURING)) {
            $feedbacks = $data['feedback'];
            foreach ($feedbacks as $key => $feedback) {
                if (is_array($feedback)) {//On some servers feedback is HTMLEditor, on another it is simple text area
                    $feedback = $feedback['text'];
                }
                if (!empty($feedback) && preg_match('/\{\$[1-9][0-9]*\}/', $feedback) == 1) {
                    $errors['feedback['.$key.']'] = get_string('nosubpatterncapturing','qtype_preg',$querymatcher->name());
                }
            }
        }

        return $errors;
    }

    function qtype() {
        return 'preg';
    }
}
?>