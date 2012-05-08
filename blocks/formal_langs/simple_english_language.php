<?php
/**
 * Defines a support of simplified english language, used to parse an objects
 *
 * Here all code that related to language, like lexer and language object definition.
 *
 * @package    block
 * @subpackage formal_langs
 * @copyright &copy; 2011 Dmitry Mamontov, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/langs_code/predefined/scaners.php'); 
require_once($CFG->dirroot.'/blocks/formal_langs/langs_code/predefined/eng_simple_lexer.php'); 
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php'); 
 
  
class block_formal_langs_predefined_simple_english_scaner extends block_formal_langs_scaner {
    
    /*! We must directly specify constructor for this one
     */
    public function __construct() {
        parent::__construct(null);
    }
    

    /**
     *  Performs lexical analysis of text
     */
    public function tokenize($text) {
        $lexer = new eng_simple_lexer(fopen('data://text/plain;base64,' . base64_encode($text), 'r'));
        //Now, we are splitting text into lexemes
        $tokens = array();
        $counter = 0;
        while ($token = $lexer->next_token()) {
            $token->set_token_index($counter);
            $tokens[] = $token;
            $counter = $counter + 1; 
        }
        $result = new block_formal_langs_token_stream();
        $result->tokens = $tokens; 
        return $result;
    }
}

class block_formal_langs_simple_english_language extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    
    public function name() {
        return 'simple_english';
    }
        
}