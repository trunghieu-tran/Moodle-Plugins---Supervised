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
 * Defines mistakes classes for the correct writing question.
 *
 * Mistakes are student errors: e.g. lexical, sequence and syntax errors
 * that displays how response differ from answer. Or we could say that
 * each mistake represent an operation, whole set of which would convert
 * response to correct answer.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

//Other necessary requires

//Base class for answer error
abstract class  qtype_correctwriting_response_mistake {
    //Error position as qtype_correctwriting_node_position object
    public $position;
    //Language name
    public $languagename;
    //Mistake message, can be changed from other parts and handled from some other classes
    public $mistakemsg;
    //Answer as array of tokens
    public $answer;
    //Response as array of tokens
    public $response;
    //Indexes of answer tokens involved (if applicable)
    public $answermistaken;
    //Indexes of response tokens involved (if applicable)
    public $responsemistaken;
    //Weight of mistake used in mark computation
    public $weight;

    /**
     * @var block_formal_langs_processed_string processed string of answer
     */
    protected $answerstring;

    /**
     * Returns a descriptions or token value for specified token
     * @param block_formal_langs_token_base   $token
     * @return string
     */
    protected function description_or_value_for($token) {
        $description = $this->token_descriptions();
        if ($description === null) {
            $description = $token->value();
            if (!is_string($description)) {
                $description = $description->string();
            }
        }
        return $description;
    }
    /**
     * Return a comma-separated list of token desciprions of these tokens, null if there is none
     * @param bool $andvalue  get strings like "{descr} is {value}"
     * @return string
     */
    public function token_descriptions($andvalue = false) {
        $descripts = array();
        foreach ($this->answermistaken as $answerindex) {
            if (is_object($this->answerstring) && $this->answerstring->has_description($answerindex)) {//TODO should we check "has_description" or just add quoted value instead?
                $description = $this->answerstring->node_description($answerindex);
                if ($andvalue) {
                    $a = new stdClass;
                    $a->tokendescr = $description;
                    $a->tokenvalue = $this->answer[$answerindex]->value();
                    if (!is_string($a->tokenvalue)) {
                        $a->tokenvalue = $a->tokenvalue->string();
                    }
                    $description = get_string('whatishint', 'qtype_correctwriting', $a);
                }
                $descripts[] = $description;
            }
        }

        if (count($descripts) == 0) {//Return null if no descriptions available
            $descript = null;
        } else {
            $descript = $this->comma_and_list($descripts);
        }

        return $descript;
    }

    /**
     * Returns a comma-separated list of strings, with 'and' as last separator
     */
    public function comma_and_list($strings) {
        $last = array_pop($strings);
        $list = '';
        if (count($strings) > 0) {
            $list = implode(', ', $strings);
            $list .= get_string('and', 'qtype_correctwriting');
        }
        $list .= $last;
        return $list;
    }

    /**
      * Returns token description if available, token value in quotes otherwise.
      */
    public function token_description($answerindex, $quotevalue = true, $at = false) {
        return $this->answerstring->node_description($answerindex, $quotevalue, $at);
    }

    /** Returns a message for mistakes. Used for lazy message initiallization.
        @return string mistake message
     */
    public function get_mistake_message() {
        return $this->mistakemsg;
    }

    /**
     * Returns a key, uniquely identifying mistake
     *
     * Used for finding mistake for hinting etc.
     */
    abstract public function mistake_key();

    /**
     * Returns an array of supported hint class names (without qtype_correctwriting prefix)
     */
    public function supported_hints() {
        return array();
    }
}
?>