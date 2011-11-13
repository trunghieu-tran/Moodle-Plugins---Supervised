<?php  // $Id: questiontype.php,v 1.4 beta 2010/08/08 16:47:26 oasychev & dvkolesov Exp $

/**
 * Defines the question type class for the preg question type.
 *
 * @copyright &copy; 2008  Sychev Oleg 
 * @author Sychev Oleg, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
///////////////////
/// preg ///
///////////////////
 
/// QUESTION TYPE CLASS //////////////////

require_once($CFG->dirroot.'/question/type/shortanswer/questiontype.php');

class qtype_preg extends qtype_shortanswer {

    /**
    * returns an array of engines
    */
    public function available_engines() {
        return array('preg_php_matcher' => get_string('preg_php_matcher','qtype_preg'),
                        'dfa_preg_matcher' => get_string('dfa_preg_matcher','qtype_preg'),
                        'nfa_preg_matcher' => get_string('nfa_preg_matcher','qtype_preg')/*,
                        'backtracking_preg_matcher' => 'backtracking_preg_matcher'*/);
    }

    //We are a child of shortanswer question
    function requires_qtypes() {
        return array('shortanswer');
    }

    function name() {
        return 'preg';
    }

    function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'question_preg');
        array_push($extraquestionfields, 'correctanswer', 'exactmatch','usehint','hintpenalty','hintgradeborder','engine');
        return $extraquestionfields;
    }

    function save_question_options($question) {
        //Fill in some data that could be absent due to disabling form controls
        if (!isset($question->usehint)) {
            $question->usehint = false;
        }
        if (!isset($question->hintpenalty)) {
            $question->hintpenalty = 0;
        }
        if (!isset($question->hintgradeborder)) {
            $question->hintgradeborder = 1;
        }

        parent::save_question_options($question);
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        $matcher =& $this->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id);
        return $matcher->match($state->responses['']);
    }

    /*
    * Override compare responses for Hint button to work right after Submit without changing response
    * This may not be needed if the best fit answer would be saved in DB in reponses - TODO - probably could wait before new question engine
    */
/*    function compare_responses($question, $state, $teststate) {
        $result = parent::compare_responses($question, $state, $teststate);
        //if hint requiested grade and apply penalty anyway, because if $teststate isn't direct predecessor of $state, than Hint won't work if the student entered exactly same response before
        //Hinting needs grading to work for now
        if ($result && isset($state->responses['hint'])) {
            $result = false;
        }
        return $result;
    }*/


     function print_question_submit_buttons(&$question, &$state, $cmoptions, $options) {
        parent::print_question_submit_buttons(&$question, &$state, $cmoptions, $options);
        if (($cmoptions->optionflags & QUESTION_ADAPTIVE) and !$options->readonly and $question->options->usehint) {
            echo '<input type="submit" name="', $question->name_prefix, 'hint" value="',
                    get_string('hintbutton','qtype_preg'), '" class=" btn" onclick="',
                    "form.action = form.action + '#q", $question->id, "'; return true;", '" />';
        }
    }

}
//// END OF CLASS ////
?>
