<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines a simple  english language lexer for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class block_formal_langs_c_token_base extends block_formal_langs_token_base {

    public function name() {
        $name = parent::name();
        $name = str_replace('c_token_','', $name);
        return $name;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return false;
    }
}
/** Describes a multiline comment
 */ 
class block_formal_langs_c_token_multiline_comment extends block_formal_langs_c_token_base
{

}

/** Describes a multiline comment
 */ 
class block_formal_langs_c_token_singleline_comment extends block_formal_langs_c_token_base
{

}

/** Describes a keyword comment
 */
class block_formal_langs_c_token_keyword extends block_formal_langs_c_token_base
{
    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }
}

/** Describes a typename
 */ 
class block_formal_langs_c_token_typename extends block_formal_langs_c_token_base
{
    public function additional_generation($token) {
       /* $arraypairs = array();        
        if ($this->value == 'char') {
            $arraypairs[] = new block_formal_langs_matched_tokens_pair(array($this->tokenindex), array($token->tokenindex), 0, false, '');
        }
        if ()
*/
	return $arraypairs;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }
}

/** Describes an identifier
 */ 
class block_formal_langs_c_token_identifier extends block_formal_langs_c_token_base
{
    public function check_specific_error ($token) {
        if ($this->value=='acos') {
            if ($token->value=='cos') {
                return 1;
            }
        }
        if ($this->value=='cos') {
            if ($token->value=='acos') {
                return 1;
            }
        }
        return 0;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }
}


/** Describes a numeric literal
 */ 
class block_formal_langs_c_token_numeric extends block_formal_langs_c_token_base
{
    /**
     * Returns value of token as a number
     * @return float
     */
    protected function numeric_value() {
        $v = (string)($this->value());
        $result = 0;
        if (core_text::strpos($v, '.')  === false) {
            if (core_text::strpos($v, 'x') !== false || core_text::strpos($v, 'X') !== false) {
                $result = hexdec($v);
            } else {
                if ($v != '0' && $v[0] == '0') {
                    $result = octdec($v);
                } else {
                    $result = floatval($v);
                }
            }
        } else {
            $result = floatval($v);
        }
        return $result;
    }
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *
     * @param block_formal_langs_c_token_base $other other lexeme
     * @param block_formal_langs_comparing_options $options options for comparing lexmes
     * @return boolean - if the same lexeme
     */
    public function is_same($other, $options ) {
        if (is_a($other, 'block_formal_langs_c_token_numeric')) {
            $value1 = $this->numeric_value();
            $value2 = $other->numeric_value();
            $result = abs($value1 - $value2) < 1.0E-7;
        } else {
            $result = parent::is_same($other, $options);
        }
        return $result;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }
}


/** Describes a preprocessor directive
 */ 
class block_formal_langs_c_token_preprocessor extends block_formal_langs_c_token_base
{

}
/** Describes a character literal
 */ 
class block_formal_langs_c_token_character extends block_formal_langs_c_token_base
{

}

/** Describes a string literal
 */ 
class block_formal_langs_c_token_string extends block_formal_langs_c_token_base
{
    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }
}

/** Describes an operators (mathematical and C-specific)
 */ 
class block_formal_langs_c_token_operators extends block_formal_langs_c_token_base
{
    public function check_specific_error ($token) {
        if ($this->value == '==') {
            if ($token->value == '=') {
                return 1;
            }
        }
        if ($this->value == ';') {
            if ($token->value == ',') {
                return 1;
            }
        }
        if ($this->value == ',') {
            if ($token->value == ';') {
                return 1;
            }
        }
        return 0;
    }
}

/** Describes an ellipsis
 */ 
class block_formal_langs_c_token_ellipsis extends block_formal_langs_c_token_base
{

}

/** Describes a semicolon
 */ 
class block_formal_langs_c_token_semicolon extends block_formal_langs_c_token_base
{

}

/** Describes a colon
 */ 
class block_formal_langs_c_token_colon extends block_formal_langs_c_token_base
{

}

/** Describes a comma
 */ 
class block_formal_langs_c_token_comma extends block_formal_langs_c_token_base
{

}

/** Describes a brackets
 */ 
class block_formal_langs_c_token_bracket extends block_formal_langs_c_token_base
{

}

/** Describes a question marks
 */ 
class block_formal_langs_c_token_question_mark extends block_formal_langs_c_token_base
{

}

/** Describes an unknown tokens
 */ 
class block_formal_langs_c_token_unknown extends block_formal_langs_c_token_base
{

}
?>