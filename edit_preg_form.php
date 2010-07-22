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

        $mform->addElement('selectyesno', 'usehint', get_string('usehint','qtype_preg'));
        $mform->setDefault('usehint',0);
        $mform->addElement('selectyesno', 'exactmatch', get_string('exactmatch','qtype_preg'));
        $mform->setHelpButton('exactmatch', array('exactmatch',get_string('exactmatch','qtype_preg'),'qtype_preg'));
        $mform->setDefault('exactmatch',1);
        $mform->addElement('text', 'rightanswer', get_string('correctanswer','qtype_preg'), array('size' => 54));
        $mform->addElement('text', 'hintpenalty', get_string('hintpenalty','qtype_preg'), array('size' => 3));
        $mform->setDefault('hintpenalty','0.1');

        parent::definition_inner($mform);

        $answersinstruct =& $mform->getElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_preg'));

    }

    function validation($data, $files) {
        global $QTYPES;
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $trimmedrightanswer=trim(stripslashes_safe($data['rightanswer']));
        $rightanswermatch=($trimmedrightanswer=='');
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '' && $data['fraction'][$key] == 1 && $trimmedrightanswer != '') {
                if ($QTYPES[$this->qtype()]->match_regex(stripslashes_safe($trimmedanswer), $trimmedrightanswer, $data['exactmatch'], $data['usecase'])) {
                    $rightanswermatch=true;
                }
            }
        }

        if ($rightanswermatch == false) {
            $errors['rightanswer']=get_string('norightanswermatch','qtype_preg');
        }
        return $errors;
    }

    function qtype() {
        return 'preg';
    }
}
?>