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

class question_preg_qtype extends question_shortanswer_qtype {
    
    //key is answer id, value is matcher object
    //keys will be unique across many questions since answer id's are unique
    protected $matchers_cache = array();

    //Neded to pass hinted message to the question form, should be deleted when moving for renderers
    protected $hintmessage = '';

    /**
    * returns an array of engines
    */
    public function available_engines() {
        return array('preg_php_matcher' => get_string('preg_php_matcher','qtype_preg'),
                        'dfa_preg_matcher' => get_string('dfa_preg_matcher','qtype_preg'));
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

    /**
    * create or get suitable matcher object for given engine, regex and options
    @param engine string engine name
    @param regex string regular expression to match
    @param $exact bool exact macthing mode
    @param $usecase bool case sensitive mode
    @param $answerid integer answer id for this regex, null for cases where id is unknown - no cache
    @return matcher object
    */
    public function &get_matcher($engine, $regex, $exact = false, $usecase = true, $answerid = null) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$engine.'.php');

        if ($answerid !== null && array_key_exists($answerid,$this->matchers_cache)) {//could use cache
            $matcher =& $this->matchers_cache[$answerid];
        } else {//create and store matcher object
            $for_regexp=$regex;
            if ($exact) {
                if ($for_regexp[0]!='^') {
                    $for_regexp='^'.$for_regexp;
                }
                if ($for_regexp[strlen($for_regexp)-1]!='$' || 
                        ($for_regexp[strlen($for_regexp)-1]=='$' && $for_regexp[strlen($for_regexp)-2]=='\\')) {
                    $for_regexp=$for_regexp.'$';
                }
            }
            $modifiers = null;
            if (!$usecase) {
                $modifiers = 'i';
            }
            $matcher = new $engine($for_regexp, $modifiers);
            if ($answerid !== null) {
                $this->matchers_cache[$answerid] =& $matcher;
            }
        }

        return $matcher;
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        $matcher =& $this->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id);
        return $matcher->match($state->responses['']);
    }

  /*
     * Override the parent class method, to show right answer.
     */
    function get_correct_responses(&$question, &$state) {
        return array(''=>addslashes($question->options->correctanswer));
    }
    
    function print_question_submit_buttons(&$question, &$state, $cmoptions, $options) {
        parent::print_question_submit_buttons(&$question, &$state, $cmoptions, $options);
        if (($cmoptions->optionflags & QUESTION_ADAPTIVE) and !$options->readonly and $question->options->usehint) {
            echo '<input type="submit" name="', $question->name_prefix, 'hint" value="',
                    get_string('hintbutton','qtype_preg'), '" class=" btn" onclick="',
                    "form.action = form.action + '#q", $question->id, "'; return true;", '" />';
        }
    }

    /**
    * function additionaly fill $state->responses['__answer'] with best fit answer for further hinting
    */
    function grade_responses(&$question, &$state, $cmoptions) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/preg/'.$question->options->engine.'.php');
        $querymatcher = new $question->options->engine;//this matcher will be used to query engine capabilities
        $knowleftcharacters = $querymatcher->is_supporting(preg_matcher::CHARACTERS_LEFT);
        $ispartialmatching = $querymatcher->is_supporting(preg_matcher::PARTIAL_MATCHING);
        
        //Set an initial value for best fit. This is tricky, since when hinting we need first element within hint grade border
        reset($question->options->answers);
        $bestfitanswer = current($question->options->answers);
        if ($ispartialmatching) {
            foreach ($question->options->answers as $answer) {
                if ($answer->fraction >= $question->options->hintgradeborder) {
                    $bestfitanswer = $answer;
                    break;//anyone that fits border helps
                }
            }
        }
        //fitness = (the number of correct letters in response) or  (-1)*(the number of letters left to complete response) so we always look for maximum fitness
        $maxfitness = (-1)*(strlen($state->responses[''])+1);
        $full = false;
        foreach ($question->options->answers as $answer) {
            $matcher =& $this->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id);
            $full = $matcher->match($state->responses['']);

            //check full match
            if ($full) {//don't need to look more if we find full match
                $bestfitanswer = $answer;
                $fitness = strlen($state->responses['']);
                break;
            }

            //when hinting we should use only answers within hint border except full matching case
            //if engine doesn't support hinting we shoudn't bother with fitness too
            if (!$ispartialmatching || $answer->fraction < $question->options->hintgradeborder) {
                continue;
            }

            //calculate fitness now
            if ($knowleftcharacters) {//engine could tell us how many characters left to complete response, this is the best fitness possible
                $fitness = (-1)*$matcher->characters_left();//-1 cause the less we need to add the better
            } else {//we should rely on the length of correct response part
                $fitness = $matcher->last_correct_character_index() - $matcher->first_correct_character_index() + 1;
            }

            if ($fitness > $maxfitness) {
                $maxfitness = $fitness;
                $bestfitanswer = $answer;
            }
        }

        //save best fitted answer for further uses
        $state->responses['__answer'] = $bestfitanswer;

        if ($full) {
            $state->raw_grade = $bestfitanswer->fraction;
        } else {
            $state->raw_grade = 0;//TODO - implement partial grades for partially correct answers
        }

        // Make sure we don't assign negative or too high marks.
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;

        // Update the penalty
        if (isset($state->responses['hint'])) {
            $state->penalty = $question->hintpenalty * $question->maxgrade;
        } else {
            $state->penalty = $question->penalty * $question->maxgrade;
        }

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        if (array_key_exists('__answer',$state->responses)) {//for the first time - no hint message
            //form hint messages
            $answer = $state->responses['__answer'];//TODO - check this is working, or it is in $state->last_graded->responses
            $matcher =& $this->get_matcher($question->options->engine, $answer->answer, $question->options->exactmatch, $question->options->usecase, $answer->id);
            $response = $state->responses[''];
            $matcher->match($response);

            //Calculate strings for response coloring
            //TODO - change actual style definition to the classes to work with themes correctly, requires investigation how add a new class for plugin...
            $firstindex = $matcher->first_correct_character_index();
            $lastindex = $matcher->last_correct_character_index();
            $wronghead = '';
            if ($firstindex > 0) {//if there is wrong heading
                $wronghead = '<span style="text-decoration:line-through; color:#FF0000;">'.htmlspecialchars(substr($response, 0, $firstindex)).'</span>';
            }
            $correctpart = '';
            if ($firstindex != -1) {//there were any match
                $correctpart = '<span style="color:#0000FF;">'.htmlspecialchars(substr($response, $firstindex, $lastindex - $firstindex + 1)).'</span>';
            }
            $hintedcharacter = '';
            if (isset($state->responses['hint']) && $matcher->is_supporting(preg_matcher::NEXT_CHARACTER)) {//if hint requested and possible
                $hintedcharacter = '<span style="background-color:#FFFF00">'.htmlspecialchars($matcher->next_char()).'</span>';
            }
            $wrongtail = '';
            if ($lastindex + 1 < strlen($response)) {//if there is wrong tail
                $wrongtail = '<span style="text-decoration:line-through; color:#FF0000;">'.htmlspecialchars(substr($response, $lastindex + 1, strlen($response) - $lastindex - 1)).'</span>';
            }
        
            $this->hintmessage = $wronghead.$correctpart.$hintedcharacter.$wrongtail;
            if (!empty($this->hintmessage)) {
                $this->hintmessage .= '<br />';
            }
        }

        parent::print_question_formulation_and_controls($question, $state, $cmoptions, $options);
    }

    function get_display_html_path() {
         global $CFG;
         return $CFG->dirroot.'/question/type/preg/display.html';
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_preg_qtype());
?>
