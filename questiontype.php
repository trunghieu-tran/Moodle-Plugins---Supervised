<?php  // $Id: questiontype.php,v 1.3.2.3 2009/10/04 19:49:58 oasychev Exp $

///////////////////
/// preg ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

define('HINT_GRADE_BORDER', 1);//if $answer->fraction >= HINT_GRADE_BORDER that hint will use this variant of answer.
require_once($CFG->dirroot.'/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/reasc.php');

class question_preg_qtype extends question_shortanswer_qtype {
    
    var $automates;
    var $result;
    var $tempresult;
    
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
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        if ($question->options->usehint) {
            $this->tempresult = $this->automates[$answer->id]->get_result($state->responses['']);
            return $this->automates[$answer->id]->get_full();
        } else {
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
        if (!$this->built && $question->options->usehint) {
            foreach ($question->options->answers as $answer) {
                $this->automates[$answer->id] = new preg_matcher_dfa;
                $this->automates[$answer->id]->preprocess($answer->answer);
            }
            $this->built = true;
        }
        if(isset($state->responses['hint'])) {
            $this->result->index = -2;
            $this->result->full = false;
            $state->raw_grade = 0;
            foreach($question->options->answers as $answer) {
                if($answer->fraction >= HINT_GRADE_BORDER) {
                    if($this->test_response($question, $state, $answer)) {
                        $state->raw_grade = $answer->fraction;
                        $this->result = $this->tempresult;
                        break;
                    }
                    //determine hint
                    $old = $state->responses[''];
                    $state->responses[''] = substr($state->responses[''], 0, $this->tempresult->index + 1);
                    if ($this->result->next !== 0) {
                        $state->responses[''] .= $this->result->next;
                    }
                    $prevtemp = $this->tempresult;
                    if ($this->test_response($question, $state, $answer)) {
                        $state->raw_grade = $answer->fraction;
                        $this->result = $prevtemp;
                        $state->responses[''] = $old;
                        break;
                    } elseif ($this->tempresult->index > $this->result->index || !isset($this->result)) {
                        $this->result = $this->tempresult;
                        $state->responses[''] = $old;
                    } else {
                        $state->responses[''] = $old;
                    }
                }
            }
            if ($this->result->next === 0) {
                $this->result->next = '';
            }
            
            // Make sure we don't assign negative or too high marks.
            $state->raw_grade = min(max((float) $state->raw_grade,
                                0.0), 1.0) * $question->maxgrade;
    
            // Update the penalty.
            if ($this->result->full) {
                $state->sumpenalty += $question->options->hintpenalty * $question->maxgrade;
            } else {
                $state->penalty = $question->options->hintpenalty * $question->maxgrade;
            }
            $state->event = QUESTION_EVENTGRADE;
        } else {
            default_questiontype::grade_responses(&$question, &$state, $cmoptions);//use parent function, because hint's functional not using at this call.
        }
        return true;
    }
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
            global $CFG;
        //form hint messages
        $hintedresponse = substr($state->responses[''], 0 , $this->result->index + 1) . $this->result->next;
        $lenght = strlen($hintedresponse) - 1;
        $hintmessage = '<span style="color:#0000FF;">'.substr($hintedresponse, 0, $lenght).'</span><span style="text-decoration:line-through; color:#FF0000;">'.
                    substr($state->responses[''], $lenght)."</span><br />";
        if (isset($state->responses['hint'])) {
            $state->responses[''] = $hintedresponse;
        }
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

                if ($this->test_response($question, $state, $answer) || $this->result->full) {
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
        if ($question->options->usehint && isset($state->responses['hint'])) {
            if (!$this->result->full) {
                //for display hint message, concatenate it with feedback
                $feedback = $hintmessage . $feedback;
            }
        }

        /// Removed correct answer, to be displayed later MDL-7496
        include("$CFG->dirroot/question/type/preg/display.html");
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_preg_qtype());
?>
