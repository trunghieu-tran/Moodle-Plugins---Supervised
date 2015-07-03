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

class block_formal_langs_token_simple_english_token extends block_formal_langs_token_base {

    public function name() {
        $name = parent::name();
        $name = str_replace('simple_english_','', $name);
        return $name;
    }
}
/** Describes an english word
 */ 
class block_formal_langs_token_simple_english_word extends block_formal_langs_token_simple_english_token
{

}

/** Describes a numeric  value
 */
class block_formal_langs_token_simple_english_numeric extends block_formal_langs_token_simple_english_token
{
} 

/** Describes a punctuation mark
 */
class block_formal_langs_token_simple_english_punctuation extends block_formal_langs_token_simple_english_token
{
    public function use_editing_distance() {
        return false;
    }
} 

/** Describes a typographic marks
 */
class block_formal_langs_token_simple_english_typographic_mark extends block_formal_langs_token_simple_english_token
{
 
} 
/** Describes other token type
 */
class block_formal_langs_token_simple_english_other extends block_formal_langs_token_simple_english_token
{
 
} 


?>