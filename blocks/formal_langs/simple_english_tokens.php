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

/** Describes an english word
 */ 
class block_formal_langs_token_simple_english_word extends block_formal_langs_token_base
{

}

/** Describes a numeric  value
 */
class block_formal_langs_token_simple_english_numeric extends block_formal_langs_token_base
{
 
} 

/** Describes a punctuation mark
 */
class block_formal_langs_token_simple_english_punctuation extends block_formal_langs_token_base
{
 
} 

/** Describes a typographic marks
 */
class block_formal_langs_token_simple_english_typographic_mark extends block_formal_langs_token_base
{
 
} 
/** Describes other token type
 */
class block_formal_langs_token_simple_english_other extends block_formal_langs_token_base
{
 
} 


?>