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

}

/** Describes a typename
 */ 
class block_formal_langs_c_token_typename extends block_formal_langs_c_token_base
{

}

/** Describes an identifier
 */ 
class block_formal_langs_c_token_identifier extends block_formal_langs_c_token_base
{

}


/** Describes a numeric literal
 */ 
class block_formal_langs_c_token_numeric extends block_formal_langs_c_token_base
{

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

}

/** Describes an operators (mathematical and C-specific)
 */ 
class block_formal_langs_c_token_operators extends block_formal_langs_c_token_base
{

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