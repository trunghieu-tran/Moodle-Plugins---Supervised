<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines an implementation of mistakes, that are determined by computing LCS and comparing answer and response
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from sequence_analyzer.
abstract class qtype_correctwriting_sequence_mistake extends qtype_correctwriting_response_mistake {
    /**
     * LCS as described in sequence analyzer, used in generating some hints, based on mistakes
     * @var array
     */
    protected $lcs;
    /**
     * Sets an lcs for mistake
     * @param $lcs
     */
    public function set_lcs($lcs) {
        $this->lcs = $lcs;
    }



    /**
     * Returns an lcs, which mistake is based on
     * @return array
     */
    public function lcs() {
        return $this->lcs;
    }

}


// A mistake, that consists from moving one lexeme to different position, than original.
class qtype_correctwriting_lexeme_moved_mistake extends qtype_correctwriting_sequence_mistake {

    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param block_formal_langs_string_pair  $stringpair  a string pair with information about strings
     * @param int    $answerindex   index of answer token
     * @param int    $responseindex index of response token
     */
    public function __construct($language, $stringpair, $answerindex, $responseindex) {
        $this->languagename = $language->name();

        $this->stringpair = $stringpair;
        $this->position = $this->stringpair->correctedstring()->stream->tokens[$responseindex]->position();
        $this->mistakemsg = null;
        // Fill answer data.
        $this->answermistaken = array();
        $this->answermistaken[] = $answerindex;
        // Fill response data.
        $this->responsemistaken = array();
        $this->responsemistaken[] = $responseindex;
    }

    /**
     *Performs a mistake message creation if needed
     */
    public function get_mistake_message() {
        if ($this->mistakemsg === null) {
            // Create a mistake message.
            $a = $this->token_description($this->answermistaken[0], true, true);
            $this->mistakemsg = get_string('movedmistakemessage', 'qtype_correctwriting', $a);
        }
        return parent::get_mistake_message();
    }

    /**
     * Returns a key, uniquely identifying mistake.
     */
    public function mistake_key() {
        return 'moved_'.$this->answermistaken[0].'_'.$this->responsemistaken[0];// 'movedtoken_' is better, but too long for question_attempt_step_data name column (32).
    }

    public function supported_hints() {
        return array('whatis', 'wheretxt', 'wherepic');
    }
}

// A mistake, that consists from adding a lexeme to response, that is not in answer.
class qtype_correctwriting_lexeme_added_mistake extends qtype_correctwriting_sequence_mistake {
    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param block_formal_langs_string_pair  $stringpair  a string pair with information about strings
     * @param int    $responseindex index of response token
     * @param block_formal_langs_comparing_options $options  options for comparting tokens
     */
    public function __construct($language, $stringpair, $responseindex, $options) {
        $this->languagename = $language->name();
        $this->stringpair = $stringpair;
        $this->position = $this->stringpair->correctedstring()->stream->tokens[$responseindex]->position();
        // Fill answer data.
        $this->answermistaken = array();
        // Fill response data.
        $this->responsemistaken = array($responseindex);

        // Find, if such token exists in answer (to call it extraneous) or not (to write that it should not be there).
        $exists = false;
        $answertokens = $stringpair->correctstring()->stream->tokens;
        $responsemistaken =  $stringpair->correctedstring()->stream->tokens[$responseindex];
        

        foreach ($answertokens as $answertoken) {
            if ($responsemistaken->is_same($answertoken, $options)) {
                $exists = true;
                break;
            }
        }

        // Create a mistake message.
        $data = $responsemistaken->value();
        if (!is_string($data)) {
            $data = $data->string();
        }
        if ($exists) {
            $this->mistakemsg = get_string('addedmistakemessage', 'qtype_correctwriting', $data);
        } else {
            $this->mistakemsg = get_string('addedmistakemessage_notexist', 'qtype_correctwriting', $data);
        }
    }

    public function mistake_key() {
        return 'added_'.$this->responsemistaken[0];// 'addedtoken_' is better, but too long for question_attempt_step_data name column (32).
    }
}

// A mistake, that consists of  skipping a lexeme from answer.
class qtype_correctwriting_lexeme_absent_mistake extends qtype_correctwriting_sequence_mistake {

    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param block_formal_langs_string_pair  $stringpair  a string pair with information about strings
     * @param int    $answerindex   index of answer token
     */
    public function __construct($language, $stringpair, $answerindex) {
        $this->languagename = $language->name();

        $this->stringpair = $stringpair;

        $this->position = $this->stringpair->correctstring()->stream->tokens[$answerindex]->position();
        // Fill answer data.
        $this->answermistaken=array();
        $this->answermistaken[] = $answerindex;
        // Fill response data.
        $this->responsemistaken = array();

        $this->mistakemsg = null;
    }

    /** 
     * Performs a mistake message creation if needed.
     */
    public function get_mistake_message() {
        if ($this->mistakemsg == null) {
            // Create a mistake message.
            $a = $this->token_description($this->answermistaken[0]);
            $this->mistakemsg = get_string('absentmistakemessage', 'qtype_correctwriting', $a);
        }
        return parent::get_mistake_message();
    }

    public function mistake_key() {
        return 'absent_'.$this->answermistaken[0];// 'absenttoken_' is better, but too long for question_attempt_step_data name column (32).
    }

    public function supported_hints() {
        return array('whatis', 'wheretxt', 'wherepic');
    }
}
