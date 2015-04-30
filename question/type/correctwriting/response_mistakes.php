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


// Base class for answer error.
abstract class  qtype_correctwriting_response_mistake {
    /** @var Error position as qtype_correctwriting_node_position object. */
    public $position;
    /** @var Language name. */
    public $languagename;
    /** @var Mistake message, can be changed from other parts and handled from some other classes. */
    public $mistakemsg;

    /**
     * A string pair with data of answer and response.
     * @var block_formal_langs_string_pair
     */
    public $stringpair;
    /** @var Indexes of answer tokens involved (if applicable). */
    public $answermistaken;
    /** @var Indexes of response tokens involved (if applicable). */
    public $responsemistaken;
    /** @var Weight of mistake used in mark computation. */
    public $weight;

    /** @var  A source analyzer to make possible for string to climb to tokens
     * That has been entered by user
     */
    public $source;

    /**
     * Maps current answer string to correct string
     * @param $answerindex
     * @return mixed
     */
    public function map_from_current_answer_string_to_correct_string($answerindex) {
        return $answerindex;
    }

    /**
     * Return a comma-separated list of token desciprions of these tokens, null if there is none.
     * @param bool $andvalue  get strings like "{descr} is {value}"
     * @return string
     */
    public function token_descriptions($andvalue = false) {
        $descripts = array();
        foreach ($this->answermistaken as $answerindex) {
            $answerindex = $this->map_from_current_answer_string_to_correct_string($answerindex);
            // TODO should we check "has_description" or just add quoted value instead?
            if (is_object($this->stringpair->correctstring()) && $this->stringpair->correctstring()->has_description($answerindex)) {
                $description = $this->stringpair->correctstring()->node_description($answerindex);
                if ($andvalue) {
                    $a = new stdClass;
                    $a->tokendescr = $description;
                    $a->tokenvalue = $this->stringpair->correctstring()->stream->tokens[$answerindex]->value();
                    $a->inthiscase =  get_string('inthiscase', 'qtype_correctwriting', $a);
                    if (!is_string($a->tokenvalue)) {
                        $a->tokenvalue = $a->tokenvalue->string();
                    }
                    $description = get_string('whatishint', 'qtype_correctwriting', $a);
                }
                if (!is_string($description)) {
                    $description = $description->string();
                }
                $descripts[] = $description;
            }
        }

        if (count($descripts) == 0) {// Return null if no descriptions available.
            $descript = null;
        } else {
            $descript = $this->comma_and_list($descripts);
        }

        return $descript;
    }

    /**
     * Returns a comma-separated list of strings, with 'and' as last separator.
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
        return $this->stringpair->node_description($answerindex, $quotevalue, $at);
    }

    /** 
     * Returns a message for mistakes. Used for lazy message initiallization.
     * @return string mistake message
     */
    public function get_mistake_message() {
        return $this->mistakemsg;
    }

    /**
     * Returns a key, uniquely identifying mistake.
     *
     * Used for finding mistake for hinting etc.
     */
    abstract public function mistake_key();

    /**
     * Returns an array of supported hint class names (without qtype_correctwriting prefix).
     */
    public function supported_hints() {
        return array();
    }
}