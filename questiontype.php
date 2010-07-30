<?php  // $Id: questiontype.php,v 1.3.2.3 2009/10/04 19:49:58 oasychev Exp $

///////////////////
/// preg ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
///
require_once($CFG->dirroot.'/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/reasc.php');
//+++extra_question_fields и definition_inner(edit_preg_form.php) нужны для выбора да/нет в окне редактирования вопроса.
//+++$question->options хранит значения опций, можно будет получить оттуда данные о выборе да/нет.
//+++print_question_submit_buttons печатает кнопку Submit, использовать её для кнопки Hint
//+++В файле preg\lang\en_utf8 хранятся имена кнопок и т.д. и т.п.
        //regex: questiontype.php: строчки 266-273, зачеркивание неправильного ответа. опциально.
//+++$state->responses[''] = текст и этот текст появится в окошке ввода.
//++default::grade_responses в переменной $state->event получает данные о событии, использовать для обработки нажатий кнопки hint(пенальти)
    
class question_preg_qtype extends question_shortanswer_qtype {
    
    var $automates;
    var $hintedresponse;
    var $result;
    
    function name() {
        return 'preg';
    }

    function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'question_preg');
        array_push($extraquestionfields, 'rightanswer', 'exactmatch','usehint','hintpenalty');
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
        if ($question->options->usehint) {
            $this->automates[$answer->id]->get_result($state->responses['']);
            if (isset($state->responses['hint'])) {
                $this->hintedresponse = substr($state->responses[''],0,$this->automates[$answer->id]->get_index()+1);
                if ($this->automates[$answer->id]->get_next_char() !== 0) {  
                    $this->hintedresponse .= $this->automates[$answer->id]->get_next_char();
                }
                $this->automates[$answer->id]->get_result($this->hintedresponse);
                $this->result[$answer->id] = $this->automates[$answer->id]->get_full();
                return $this->automates[$answer->id]->get_full();
            } else {
                return $this->automates[$answer->id]->get_full();
            }
        } else {
            // Trim the response before it is saved in the database. See MDL-10709
            $state->responses[''] = trim($state->responses['']);
            return $this->match_regex($answer->answer, trim(stripslashes_safe($state->responses[''])), $question->options->exactmatch, $question->options->usecase);
        }
    }

  /*
     * Override the parent class method, to show right answer.
     */
    function get_correct_responses(&$question, &$state) {
        return array(''=>addslashes($question->options->rightanswer));
    }
    
    function print_question_submit_buttons(&$question, &$state, $cmoptions, $options) {
        parent::print_question_submit_buttons(&$question, &$state, $cmoptions, $options);
        if (($cmoptions->optionflags & QUESTION_ADAPTIVE) and !$options->readonly and $question->options->usehint) {
            echo '<input type="submit" name="', $question->name_prefix, 'hint" value="',
                    get_string('hintbutton','qtype_preg'), '" class=" btn" onclick="',
                    "form.action = form.action + '#q", $question->id, "'; return true;", '" />';
        }
    }
    function grade_responses(&$question, &$state, $cmoptions) {
        default_questiontype::grade_responses(&$question, &$state, $cmoptions);
        if(isset($state->responses['hint'])) {
            $state->sumpenalty += $question->options->hintpenalty * $question->maxgrade;
        }
        return true;
    }
    function get_question_options(&$question) {
        $result = parent::get_question_options(&$question);
        foreach ($question->options->answers as $answer) {
            $this->automates[$answer->id] = new preg_matcher_dfa;
            $this->automates[$answer->id]->preprocess($answer->answer);
        }
        return $result;
    }
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        if (isset($state->responses['hint']) && isset($this->hintedresponse)) {
            $state->responses[''] = $this->hintedresponse;
        }
            global $CFG;
    /// This implementation is also used by question type 'numerical'
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        /// Print question text and media

        $questiontext = format_text($question->questiontext,
                $question->questiontextformat,
                $formatoptions, $cmoptions->course);
        $image = get_question_image($question);

        /// Print input controls

        if (isset($state->responses['']) && $state->responses[''] != '') {
            $value = ' value="'.s($state->responses[''], true).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';

        $feedback = '';
        $class = '';
        $feedbackimg = '';

        if ($options->feedback) {
            $class = question_get_feedback_class(0);
            $feedbackimg = question_get_feedback_image(0);
            foreach($question->options->answers as $answer) {
                if(isset($state->responses['hint'])) {
                    $flag = $this->result[$answer->id];
                } else {
                    $flag = $this->test_response($question, $state, $answer);
                }
                if ($flag) {
                    // Answer was correct or partially correct.
                    $class = question_get_feedback_class($answer->fraction);
                    $feedbackimg = question_get_feedback_image($answer->fraction);
                    if ($answer->feedback) {
                        $feedback = format_text($answer->feedback, true, $formatoptions, $cmoptions->course);
                    }
                    break;
                }
            }
        }

        /// Removed correct answer, to be displayed later MDL-7496
        include("$CFG->dirroot/question/type/shortanswer/display.html");
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_preg_qtype());
?>
