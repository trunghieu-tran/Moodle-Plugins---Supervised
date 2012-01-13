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
require_once($CFG->dirroot.'/question/type/preg/question.php');

class qtype_preg extends qtype_shortanswer {
    private $graphvizpath = '';    // path to dot.exe of graphviz

    public function __construct() {
        global $CFG;
        if (isset($CFG->qtype_preg_graphvizpath)) {
            $this->graphvizpath = $CFG->qtype_preg_graphvizpath;
        } else {
            $this->graphvizpath = '';
        }
    }

    /*public function questionid_column_name() {
        return 'questionid';
    }*/

    /**
    * returns an array of engines
    * key = engine indentifier, value = interface string with engine name
    */
    public function available_engines() {
        return array(   'php_preg_matcher' => get_string('php_preg_matcher','qtype_preg'),
                        'dfa_matcher' => get_string('dfa_matcher','qtype_preg'),
                        'nfa_matcher' => get_string('nfa_matcher','qtype_preg')/*,
                        'backtracking_matcher' => 'backtracking_matcher'*/
                    );
    }

    /**
    * returns an array of supported notations
    * key = notation indentifier, value = interface string with notation name
    */
    public function available_notations() {
        return array(   'native' => get_string('notation_native', 'qtype_preg'),
                        'mdlshortanswer' => get_string('notation_mdlshortanswer', 'qtype_preg')
                    );
    }

    //We are a child of shortanswer question
    function requires_qtypes() {
        return array('shortanswer');
    }

    function name() {
        return 'preg';
    }

    public function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'qtype_preg');
        array_push($extraquestionfields, 'correctanswer', 'exactmatch', 'usehint', 'hintpenalty', 'hintgradeborder', 'engine', 'notation');
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

        //Sanity check for engine capabilities - disabling form controls works really strange...
        $questionobj = new qtype_preg_question;
        $querymatcher = $questionobj->get_query_matcher($question->engine);
        if (!$querymatcher->is_supporting(qtype_preg_matcher::NEXT_CHARACTER)) {
            $question->usehint = false;
        }

        parent::save_question_options($question);
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        $matcher =& $this->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id);
        return $matcher->match($state->responses['']);
    }

}
//// END OF CLASS ////
?>
