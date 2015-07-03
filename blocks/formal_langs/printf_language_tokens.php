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
 * Defines printf language tokens for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_utils.php');

/**
 * A basic token for block_formal_langs_printf_token_base
 */
class block_formal_langs_printf_token_base extends block_formal_langs_token_base {

    public function name() {
        $name = parent::name();
        $name = str_replace('printf_token_','', $name);
        return $name;
    }
}

/**
 * A simple text data
 */
class block_formal_langs_token_printf_text extends block_formal_langs_token_base {
    /**
     * Unescaped text data
     * @var string
     */
    private $unescapedtext;

    function octal_to_decimal_char($matches) {
        $code = $matches[0];
        $code = octdec($code);
        return chr(intval($code));
    }

    function hex_to_decimal_char($matches) {
        $code = $matches[0];
        $code = hexdec($code);
        $string = '';
        if (strlen($matches[0]) == 2) {
            $string = chr(intval($code));
        } else {
            //  mb_convert_encoding left intentionally, because
            // core_text uses iconv to convert, and iconv fails
            // to conver from entities
            $string = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
        }
        return $string;
    }

    function to_text($text) {
        $state = 0;
        $length = core_text::strlen($text);
        $result = "";
        $statetext = '';
        $esc = array('\'' => '\'', '"' => '"' , 'a' => "\a", 'b' => "\b", 'f' => "\f",
            'n'  => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v", '\\' => '\\',
            '?'  => '?');
        $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for($i = 0; $i < $length; $i++) {
            $c = $text[$i];
            $handled = false;
            if ($state == 0 && !$handled) {
                if ($c == '\\') {
                    $state = 1;
                } else {
                    $result .= $c;
                }
                $handled = true;
            }
            if ($state == 1 && !$handled) {
                $handled = true;
                if (array_key_exists($c, $esc)) {
                    $result .= $esc[$c];
                }  else {
                    if ($c == '0') {
                        $state = 2;  $
                        $statetext = '';
                    }  else {
                        if ($c == 'x' || $c == 'X') {
                            $state = 3;
                            $statetext = '';
                        } else {
                            $result .= '\\' . $c;
                        }
                    }
                }
            }
            if ($state == 2 && !$handled) {
                $handled = true;
                $ia = array($c, $numbers, $result, $state, $statetext, 'octal_to_decimal_char', '\\0', $i);
                $a =  $this->handle_cstate_transition($ia);
                list($state, $statetext, $result, $i) = $a;
            }
            if ($state == 3 && !$handled) {
                $handled = true;
                $ia = array($c, $numbers, $result, $state, $statetext, 'hex_to_decimal_char', '\\x', $i);
                $a =  $this->handle_cstate_transition($ia);
                list($state, $statetext, $result, $i) = $a;
            }
        }
        $handled = false;
        if ($state == 2 && !$handled) {
            $handled = true;
            $result .= $this->octal_to_decimal_char(array($statetext));
        }
        if ($state == 3 && !$handled) {
            $handled = true;
            $result .= $this->hex_to_decimal_char(array($statetext));
        }
        return $result;
    }

    private function handle_cstate_transition($input_array)
    {
        list($c, $numbers, $result, $state, $statetext, $fun, $d, $i) = $input_array;
        if (in_array($c, $numbers)) {
            $statetext .= $c;
        } else {
            if (core_text::strlen($statetext) != 0) {
                $funname = 'block_formal_langs_' . $fun;
                $result .= $funname($statetext);
            } else {
                $result .= $d;
            }
            $state = 0;
            --$i;
        }
        return array($state, $statetext, $result, $i);
    }

    /**
     * Lexeme constructor, which constructs a data, but also unescapes some output
     * @param int    $number  number of input
     * @param string $type - type of lexeme
     * @param string $value - semantic value of lexeme
     * @param block_formal_langs_node_position $position position of node
     * @param int  $index index of lexeme
     */
    public function __construct($number, $type, $value, $position, $index) {
        parent::__construct($number, $type, $value, $position, $index);
        $this->unescapedtext = $this->to_text($value);
    }

    public function unescapedvalue() {
        return $this->unescapedtext;
    }

    protected function string_caseinsensitive_value() {
        return core_text::strtolower($this->unescapedtext);
    }
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *
     * @param block_formal_langs_token_base|block_formal_langs_token_printf_text $other other lexeme
     * @param bool $casesensitive whether we should care about for case sensitive
     * @return boolean - if the same lexeme
     */
    public function is_same($other, $casesensitive = true) {
        $result = false;
        if ($this->type == $other->type) {
            if ($casesensitive) {
                $result = $this->unescapedtext == $other->unescapedtext;
            }  else {
                $left = $this->string_caseinsensitive_value();
                $right = $other->string_caseinsensitive_value();
                $result = $left == $right;
            }
        }
        return $result;
    }
}
/**
 * A quote
 */
class block_formal_langs_token_printf_quote extends block_formal_langs_token_base {

}

/**
 * A specifier for token data
 */
class block_formal_langs_token_printf_specifier extends block_formal_langs_token_base {

}
