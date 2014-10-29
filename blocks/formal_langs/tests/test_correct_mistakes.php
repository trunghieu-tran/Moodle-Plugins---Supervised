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
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
class blocks_formal_langs_token_base_correct_mistakes_test extends UnitTestCase {
    /*
    // masha eat cake
    // dasha eat my cake
    function test_correct_mistakes_1() {
        $lexem1=new block_formal_langs_token_base(null,'type','masha',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','eat',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','cake',null,2);
        $array_other1=array($lexem1,$lexem2,$lexem3);
        $correctstream=new block_formal_langs_token_stream(); //correct
        $correctstream->tokens=$array_other1;
        $lexem4=new block_formal_langs_token_base(null,'type','dasha',null,0);
        $lexem5=new block_formal_langs_token_base(null,'type','eat',null,1);
        $lexem6=new block_formal_langs_token_base(null,'type','my',null,2);
        $lexem7=new block_formal_langs_token_base(null,'type','cake',null,3);
        $array_other2=array($lexem4,$lexem5,$lexem6, $lexem7);
        $incorrectstream=new block_formal_langs_token_stream(); //incorrect
        $incorrectstream->tokens=$array_other2;
        $pair11=new block_formal_langs_matched_tokens_pair(array(0),array(0),1);
        $pair12=new block_formal_langs_matched_tokens_pair(array(1),array(1),0);
        $pair13=new block_formal_langs_matched_tokens_pair(array(2),array(3),0);
        $group=array($pair11,$pair12,$pair13);
        
        $strcorrect=new block_formal_langs_processed_string();
        $strcorrect->stream=$correctstream;
        $strincorrect=new block_formal_langs_processed_string();
        $strincorrect->stream=$incorrectstream;
        $strpair=new block_formal_langs_string_pair($strcorrect,$strincorrect,$group);
                
        $res=$strpair->correct_mistakes();
        $this->assertTrue(count($res->correctedstring->stream->tokens)==4);
    }
    
        function test_correct_mistakes_2() {
        $lexem1=new block_formal_langs_token_base(null,'type','masha',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','eat',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','cake',null,2);
        $array_other1=array($lexem1,$lexem2,$lexem3);
        $correctstream=new block_formal_langs_token_stream();//correct
        $correctstream->tokens=$array_other1;
        $lexem4=new block_formal_langs_token_base(null,'type','dasha',null,0);
        $lexem5=new block_formal_langs_token_base(null,'type','eat',null,1);
        $array_other2=array($lexem4,$lexem5);
        $incorrectstream=new block_formal_langs_token_stream();//incorrect
        $incorrectstream->tokens=$array_other2;
        $pair11=new block_formal_langs_matched_tokens_pair(array(0),array(0),1);
        $pair12=new block_formal_langs_matched_tokens_pair(array(1),array(1),0);
        $group=array($pair11,$pair12);
        $res=$incorrectstream->correct_mistakes($correctstream,$group);
        $this->assertTrue(count($res->tokens)==2);
    }
    
        function test_correct_mistakes_3() {
        $lexem1=new block_formal_langs_token_base(null,'type','masha',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','eat',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','cake',null,2);
        $array_other1=array($lexem1,$lexem2,$lexem3);
        $correctstream=new block_formal_langs_token_stream(); //correct
        $correctstream->tokens=$array_other1;
        $lexem4=new block_formal_langs_token_base(null,'type','mashaeat',null,0);
        $array_other2=array($lexem4);
        $incorrectstream=new block_formal_langs_token_stream(); //incorrect
        $incorrectstream->tokens=$array_other2;
        $pair11=new block_formal_langs_matched_tokens_pair(array(0,1),array(0),1);
        $group=array($pair11);
        $res=$incorrectstream->correct_mistakes($correctstream,$group);
        $this->assertTrue(count($res->tokens)==2);
    }
    
        function test_correct_mistakes_4() {
        $lexem1=new block_formal_langs_token_base(null,'type','mashaeat',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','cake',null,1);
        $array_other1=array($lexem1,$lexem2);
        $correctstream=new block_formal_langs_token_stream(); //correct
        $correctstream->tokens=$array_other1;
        $lexem4=new block_formal_langs_token_base(null,'type','masha',null,0);
        $lexem5=new block_formal_langs_token_base(null,'type','eat',null,1);
        $array_other2=array($lexem4,$lexem5);
        $incorrectstream=new block_formal_langs_token_stream(); //incorrect
        $incorrectstream->tokens=$array_other2;
        $pair11=new block_formal_langs_matched_tokens_pair(array(0),array(0,1),1);
        $group=array($pair11);
        $res=$incorrectstream->correct_mistakes($correctstream,$group);
        $this->assertTrue(count($res->tokens)==1);
        
    }*/
    
}
?>