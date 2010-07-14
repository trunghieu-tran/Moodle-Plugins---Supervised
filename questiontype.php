<?php  // $Id: questiontype.php,v 1.3.2.3 2009/10/04 19:49:58 oasychev Exp $

///////////////////
/// preg ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
///
require_once($CFG->dirroot.'/question/type/shortanswer/questiontype.php');

class question_preg_qtype extends question_shortanswer_qtype {

    function name() {
        return 'preg';
    }

    function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'question_preg');
        array_push($extraquestionfields, 'rightanswer', 'exactmatch');
        return $extraquestionfields;
    }

    function match_regex($regex, $str, $exact, $usecase) {
        $for_regexp=$regex;
        if (strpos($for_regexp,'/')!==false) {//escape any slashes
            $for_regexp=implode('\/',explode('/',$for_regexp));
        }
        if ($exact){
            if ($for_regexp[0]!='^'){
                $for_regexp='^'.$for_regexp;
            }
            if ($for_regexp[strlen($for_regexp)-1]!='$' && $for_regexp[strlen($for_regexp)-2]!='\\') {
                $for_regexp=$for_regexp.'$';
            }
        }
        $for_regexp='/'.$for_regexp.'/u';
        if (!$usecase) {
            $for_regexp .= 'i';
        }
        return preg_match($for_regexp, $str);
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        return $this->match_regex($answer->answer, trim(stripslashes_safe($state->responses[''])), $question->options->exactmatch, $question->options->usecase);
    }

  /*
     * Override the parent class method, to show right answer.
     */
    function get_correct_responses(&$question, &$state) {
        return array(''=>addslashes($question->options->rightanswer));
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_preg_qtype());
?>
