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
 * Defines a function utilities for languages
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 * Convert first match from array of matches from an octal number to a real character
 * @param array|string $matches array (if array first element is taken), otherwise whole string
 * @return string
 */
function block_formal_langs_octal_to_decimal_char($matches) {
    if (is_array($matches)) {
        $code = $matches[0];
    }   else {
        $code = $matches;
    }
    $code = octdec($code);
    return chr(intval($code));
}
/**
 * Convert first match from array of matches from an hexadecimal number to a real character
 * @param array|string $matches array (if array first element is taken), otherwise whole string
 * @return string
 */
function block_formal_langs_hex_to_decimal_char($matches) {
    if (is_array($matches)) {
        $code = $matches[0];
    }   else {
        $code = $matches;
    }
    $code = hexdec($code);
    $string = '';
    if (strlen($matches[0]) == 2) {
        $string = chr(intval($code));
    } else {
        //  mb_convert_encoding left intentionally, because
        // textlib uses iconv to convert, and iconv fails
        // to convert from entities
        $string = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
    }
    return $string;
}