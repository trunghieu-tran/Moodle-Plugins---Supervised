<?php
/**
 * Defines unit-tests for token_base
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2012  
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/token_base.php');

class qtype_correctwriting_token_base_test_1 extends UnitTestCase {
    function test_damerau_levenshtein_tb() {
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'ehllo')==1);//transposition
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hello')==0);//words identical        
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hollo')==1);//substitution
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hell')==1);//deletion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('helo', 'hello')==1);//insertion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hi lo', 'hello')==2);//word has space
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('HELLO', 'heLlo')==4);//toUpper
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('aaaaa', 'aaaaa')==5);//different languages
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('1!2-', '1?6:')==3);//numbers and punctuashion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('mamma', 'ma')==3);//place of word
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('       ', '    ')==1);//spaces and tab
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('mix ail', 'sx aali')==4);//large word
    }
}
?>
