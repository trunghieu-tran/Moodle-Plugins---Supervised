<?php
/**
 * Defines unit-tests for token_base
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2012  
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class block_formal_langs_tokens_base_test extends UnitTestCase {
    function test_possible_pair() {
        $lexem=new block_formal_langs_token_base(null,'type','hello',null,0);
        $lexem1=new block_formal_langs_token_base(null,'type','hllo',null,1);
        $lexem2=new block_formal_langs_token_base(null,'type','hillo',null,2);
        $lexem3=new block_formal_langs_token_base(null,'type','heello',null,3);
        $lexem4=new block_formal_langs_token_base(null,'type','hl',null,4);
        $lexem5=new block_formal_langs_token_base(null,'type','hel o',null,5);
        $lexem6=new block_formal_langs_token_base(null,'type','hi llo',null,6);
        $lexem7=new block_formal_langs_token_base(null,'type','he lo',null,7);
        
        $this->assertTrue($lexem->possible_pair($lexem,2)==0);      //threshold 0.7
        $this->assertTrue($lexem->possible_pair($lexem2,2)==1);     //threshold 0.7
        $this->assertTrue($lexem->possible_pair($lexem1,3)==1);      //threshold 0.5
        $this->assertTrue($lexem->possible_pair($lexem3,1)==1);    //threshold 0.9 
        $this->assertTrue($lexem->possible_pair($lexem4,2)==-1);        //threshold 0.7
        $this->assertTrue($lexem->possible_pair($lexem5,3)==1);     //threshold 0.5
        $this->assertTrue($lexem->possible_pair($lexem6,3)==2);    //threshold 0.5
        $this->assertTrue($lexem7->possible_pair($lexem,3)==1);     //threshold 0.5
    }
}
?>