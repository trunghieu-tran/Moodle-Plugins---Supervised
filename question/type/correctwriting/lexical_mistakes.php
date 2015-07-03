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
 * Defines an implementation of mistakes, that are determined by lexical analyzer
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author  Oleg Sychev, Dmitriy Mamontov,Birukova Maria Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from lexical analyzer. We need them to indicate
// what lexemes was corrected by analyzer.
class qtype_correctwriting_lexical_mistake extends qtype_correctwriting_response_mistake {
    /** A pair of tokens, linked with lexeical mistake
     *  @var block_formal_langs_matched_tokens_pair
     */
    public $tokenpair;
    
    /*! Mistakekey
        @var string
    */
    public $str;
    
    public function mistake_key() {
        return $this->str;//TODO - implement actually
    }
    
    public function supported_hints() {
        return array('whatis', 'howtofixpic');
    }
    
    public function __construct($tokenpair){
        $this->tokenpair = $tokenpair;
        $this->str='typo_'.$this->tokenpair->correcttokens[0];
    }

    /**
     * Returns description for whatis hint as text
     * @return string
     */
    public function what_is_description() {
        $description = $this->token_descriptions();
        $a = new stdClass();
        $a->tokendescr = $description;

        $comparedstring = '';
        $indexes = $this->tokenpair->comparedtokens;
        if (count($indexes)) {
            sort($indexes);
            $strings = array();
            foreach($indexes as $index) {
                /** @var block_formal_langs_token_base $token */
                $token = $this->stringpair->comparedstring()->stream->tokens[$index];
                $string = $token->value();
                if (is_object($string)) {
                    /** @var qtype_poasquestion\string $string */
                    $string = $string->string();
                }
                $strings[]=$string;
            }
            $result = implode(' ', $strings);
            $comparedstring = $result;
        }

        $a->tokenvalue = $comparedstring;
        $a->inthiscase =  get_string('inyouranswer', 'qtype_correctwriting', $a);
        if (!is_string($a->tokenvalue)) {
            $a->tokenvalue = $a->tokenvalue->string();
        }
        $description = get_string('whatishint', 'qtype_correctwriting', $a);
        return $description;
    }

    public function token_descriptions($andvalue = false) {
        if ($this->tokenpair->type != block_formal_langs_matched_tokens_pair::TYPE_MISSING_SEPARATOR) {
            return parent::token_descriptions($andvalue);
        }
        $correctstring = $this->stringpair->correctstring();
        $hasdescriptions = true;
        foreach ($this->tokenpair->correcttokens as $index) {
            $hasdescriptions = $hasdescriptions && $correctstring->has_description($index);
        }
        if ($hasdescriptions) {
            return parent::token_descriptions($andvalue);
        }
        return null;
    }

    public function token_descriptions_as_mistake($andvalue = false) {
        return parent::token_descriptions($andvalue);
    }
}

class qtype_correctwriting_scanning_mistake extends qtype_correctwriting_response_mistake {
    public function mistake_key() {
        return '';//TODO - implement actually
    }
}

?>