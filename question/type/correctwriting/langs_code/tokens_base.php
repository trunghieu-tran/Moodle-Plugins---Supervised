<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Defines generic token class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Sergey Pashaev, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 * Class for base tokens.
 *
 * Class for storing tokens. Class - token, object of the token class
 * - lexeme.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class qtype_correctwriting_token_base {

    /**
     * Type of token.
     * @var string
     */
    protected $type;

    /**
     * Semantic value of token.
     * @var string
     */
    protected $value;

    /**
     * Is this token from correct answer or response.
     * @var boolean
     */
    protected $isanswer;

    /**
     * Token position - c.g. qtype_correctwriting_node_position class
     */
    protected $position;

    /**
     * Basic lexeme constructor.
     *
     * @param string $type - type of lexeme
     * @param string $value - semantic value of lexeme
     * @return base_token
     */
    public function __construct($type, $value, $isanswer, $position) {
        $this->type = $type;
        $this->value = $value;
        $this->isanswer = $isanswer;
        $this->position = $position;
    }

    /**
     * Returns actual type of the token.
     *
     * Usually will be overloaded in child classes to return constant string.
     */
    public function token_type() {
        return $this->type;
    }

    /**
     * This function returns true if Damerau-Levenshtein distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are 
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_levenshtein() {
        return true;
    }

    /* Calculates Damerau-Levenshtein distance between two strings.  
     *
     * @return int Damerau-Levenshtein distance
     */
    public function damerau_levenshtein($str1, $str2) {
    }

    /**
     * Base lexical mistakes handler. Looks for possible matches for this
     * token in other answer and return an array of them.
     *
     * The functions works differently depending on token of which answer it's called.
     * For correct _answer_ it looks for typos, extra separators,
     * typical mistakes (in particular subclasses) etc - i.e. all mistakes with one token from answer.
     * For student's _response_ it looks for missing separators, extra quotes etc, i.e. mistakes which
     * have more than one token from answer, but only one from response.
     *
     * @param array $other - array of tokens  (other answer)
     * @param integer $threshold - lexical mistakes threshold
     * @return array - array of qtype_correctwriting_matched_tokens_pair objects with blank
     * $answertokens or $responsetokens field inside (it is filling from outside)
     */
    public function look_for_matches($other, $threshold) {
        // TODO: generic mistakes handling
    }
    
    
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *  
     * @param qtype_correctwriting_token_base $other other lexeme
     * @return boolean - if the same lexeme
     */
    public function is_same($other) {
       $result=false;
       if ($this->type==$other->type)
            $result=$this->value==$other->value;
       return $result;
    }
    
    /**
     * Returns a position of token
     */
    public function position() {
        return $this->position;
    }
}

/**
 * Class for matched pairs (correct answer and student response).
 *
 * Instances of this class map groups of tokens from correct answer
 * to groups of token in student response.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class qtype_correctwriting_matched_tokens_pair {

    /**
     * Indexes of the correct answer tokens.
     * @var array
     */
    public $answertokens;

    /**
     * Indexes of the student response tokens.
     * @var array
     */
    public $responsetokens;

    /**
     * Mistake weight (Levenshtein distance, for example).
     *
     * Zero is no mistake.
     *
     * @var integer
     */
    public $mistakeweight;
}

class qtype_correctwriting_node_position {
    protected $linestart;
    protected $lineend;
    protected $colstart;
    protected $colend;

    public function __construct($linestart, $lineend, $colstart, $colend) {
        $this->linestart = $linestart;
        $this->lineend = $lineend;
        $this->colstart = $colstart;
        $this->colend = $colend;
    }

    /**
     * Summ positions of array of nodes into one position
     *
     * Resulting position is defined from minimum to maximum postion of nodes
     *
     * @param array $nodepositions positions of adjanced nodes
     */
    public function summ($nodepositions) {//Pashaev
    }
    
    /**
     * Returns a line, where node is located (starting line). Used. when producing sequence errors
     */
    public function line() {
        return $this->linestart;
    }
    /**
     * Returns a position, where node is located (starting column). Used. when producing sequence errors
     */
    public function column() {
        return $this->colstart;
    }
}
?>