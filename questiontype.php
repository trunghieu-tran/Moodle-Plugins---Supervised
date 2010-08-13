<?php  // $Id: questiontype.php,v 1.4 beta 2010/08/08 16:47:26 oasychev & dvkolesov Exp $

///////////////////
/// preg ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

define('HINT_GRADE_BORDER', 1);//if $answer->fraction >= HINT_GRADE_BORDER that hint will use this variant of answer.
require_once($CFG->dirroot.'/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');

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
            $this->automates[$answer->id]->match($state->responses['']);
            return $this->automates[$answer->id]->is_matching_complete();
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
                $this->automates[$answer->id] = new dfa_preg_matcher($answer->answer);
            }
            $this->built = true;
        }
        if(isset($state->responses['hint'])) {
            $this->index = -2;
            $this->full = false;
            $state->raw_grade = 0;
            foreach($question->options->answers as $answer) {
                if($answer->fraction >= HINT_GRADE_BORDER) {
                    if($this->test_response($question, $state, $answer)) {
                        $state->raw_grade = $answer->fraction;
                        $this->index = $this->automates[$answer->id]->last_correct_character_index();
                        $this->full = $this->automates[$answer->id]->is_matching_complete();
                        $this->next = $this->automates[$answer->id]->next_char();
                        break;
                    }
                    //determine hint
                    $old = $state->responses[''];
                    $state->responses[''] = substr($state->responses[''], 0, $this->automates[$answer->id]->last_correct_character_index() + 1);
                    if ($this->automates[$answer->id]->next_char() !== 0) {
                        $state->responses[''] .= $this->automates[$answer->id]->next_char();
                    }
                    $prevtempindex = $this->automates[$answer->id]->last_correct_character_index();
                    $prevtempnext = $this->automates[$answer->id]->next_char();
                    if ($this->test_response($question, $state, $answer)) {
                        $state->raw_grade = $answer->fraction;
                        $this->index = $prevtempindex;
                        $this->full = true;
                        $this->next = $prevtempnext;
                        $state->responses[''] = $old;
                        break;
                    } elseif ($this->automates[$answer->id]->last_correct_character_index() > $this->index) {
                        $this->index = $this->automates[$answer->id]->last_correct_character_index();
                        $this->full = $this->automates[$answer->id]->is_matching_complete();
                        if ($this->full) {
                            $state->raw_grade = $answer->fraction;
                        }
                        $this->next = $this->automates[$answer->id]->next_char();
                        $state->responses[''] = $old;
                    } else {
                        $state->responses[''] = $old;
                    }
                }
                if ($this->full) {
                    $state->raw_grade = $answer->fraction;
                }
            }
            if ($this->next === 0) {
                $this->next = '';
            }

            // Update the penalty.
            if ($this->full) {
                $state->sumpenalty += $question->options->hintpenalty * $question->maxgrade;
            } else {
                $state->penalty = $question->options->hintpenalty * $question->maxgrade;
            }
    
            // Make sure we don't assign negative or too high marks.
            $state->raw_grade = min(max((float) $state->raw_grade,
                                0.0), 1.0) * $question->maxgrade;
    
            $state->event = QUESTION_EVENTGRADE;
        } else {
            default_questiontype::grade_responses(&$question, &$state, $cmoptions);//use parent function, because hint's functional not using at this call.
        }
        return true;
    }
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
            global $CFG;
        if (isset($state->responses['hint']) && isset($this->next) && isset($this->index) && isset($this->full)) {// if hint need and there is result of matching
            //form hint messages
            $hintmessage = 'This message not formed yet!';
            $hintedresponse = substr($state->responses[''], 0 , $this->index + 1) . $this->next;
            $lenght = strlen($hintedresponse) - 1;
            $hintmessage = '<span style="color:#0000FF;">'.htmlentities(substr($hintedresponse, 0, $lenght)).'</span><span style="text-decoration:line-through; color:#FF0000;">'.
                        htmlentities(substr($state->responses[''], $lenght))."</span><br />";
            if (isset($state->responses['hint'])) {
                $state->responses[''] = $hintedresponse;
            } else {$hint = 'error in line 151-153';}
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

                if ($this->test_response($question, $state, $answer) || $this->full) {
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
            if (!$this->full) {
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
