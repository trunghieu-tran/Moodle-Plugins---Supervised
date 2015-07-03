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
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class block_formal_langs_token_base_test extends UnitTestCase {
    function test_damerau_levenshtein_tb() {
        $options = new block_formal_langs_comparing_options();
        $options->usecase = true;
        $options1 = new block_formal_langs_comparing_options();
        $options1->usecase = false;
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'ehllo', $options)==1);// transposition
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hello', $options)==0);// words identical        
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hollo', $options)==1);// substitution
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hello', 'hell', $options)==1);// deletion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('helo', 'hello', $options)==1);// insertion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('hi lo', 'hello', $options)==2);// word has space
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('HELLO', 'heLlo', $options)==4);// toUpper
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('HELLO', 'heLlo', $options1)==0);// toUpper
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('aaaaa', 'ààààà', $options)==5);// different languages
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('1!2-', '1?6:', $options)==3);// numbers and punctuashion
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('mamma', 'ma', $options)==3);// place of word
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein(' ', '	', $options)==1);// spaces and tab
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('kmix ail', 'ksx aali', $options)==4);// large word
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('helllo', 'hlello', $options)==1);// 1 transposition
        
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('misha', 'masha', $options1)==1);
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('misha', 'mish', $options1)==1);
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('miha', 'misHa', $options1)==1);
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('mamma', 'ma', $options1)==3);
        $this->assertTrue(block_formal_langs_token_base::damerau_levenshtein('ca', 'abc', $options1)==3);
    }
}
?>
