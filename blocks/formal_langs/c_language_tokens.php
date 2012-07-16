<?php
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

/** Describes a multiline comment
 */ 
class block_formal_langs_c_language_multiline_comment extends block_formal_langs_token_base
{

}

/** Describes a multiline comment
 */ 
class block_formal_langs_c_language_singleline_comment extends block_formal_langs_token_base
{

}

/** Describes a keyword comment
 */
class block_formal_langs_c_language_keyword extends block_formal_langs_token_base
{

}

/** Describes a typename
 */ 
class block_formal_langs_c_language_typename extends block_formal_langs_token_base
{

}

/** Describes an identifier
 */ 
class block_formal_langs_c_language_identifier extends block_formal_langs_token_base
{

}


/** Describes a numeric literal
 */ 
class block_formal_langs_c_language_numeric extends block_formal_langs_token_base
{

}


/** Describes a preprocessor directive
 */ 
class block_formal_langs_c_language_preprocessor extends block_formal_langs_token_base
{

}
/** Describes a character literal
 */ 
class block_formal_langs_c_language_character extends block_formal_langs_token_base
{

}

/** Describes a string literal
 */ 
class block_formal_langs_c_language_string extends block_formal_langs_token_base
{

}

/** Describes an operators (mathematical and C-specific)
 */ 
class block_formal_langs_c_language_operators extends block_formal_langs_token_base
{

}

/** Describes an ellipsis
 */ 
class block_formal_langs_c_language_ellipsis extends block_formal_langs_token_base
{

}

/** Describes a semicolon
 */ 
class block_formal_langs_c_language_semicolon extends block_formal_langs_token_base
{

}

/** Describes a colon
 */ 
class block_formal_langs_c_language_colon extends block_formal_langs_token_base
{

}

/** Describes a comma
 */ 
class block_formal_langs_c_language_comma extends block_formal_langs_token_base
{

}

/** Describes a brackets
 */ 
class block_formal_langs_c_language_brackets extends block_formal_langs_token_base
{

}

/** Describes a question marks
 */ 
class block_formal_langs_c_language_question_mark extends block_formal_langs_token_base
{

}

/** Describes an unknown tokens
 */ 
class block_formal_langs_c_language_unknown extends block_formal_langs_token_base
{

}
?>