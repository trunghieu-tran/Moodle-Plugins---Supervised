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

class block_formal_langs_token_stream_test extends UnitTestCase {
    public function test_look_for_matches_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'cat', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'map', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'game', null, 0);
        $array_other=array($lexem1, $lexem2);
        $this->assertTrue(count($lexem3->look_for_matches($array_other, 1, true, $options))==0, 'Threshold is 100. Pairs for correct token "game" are not found');
    }
        
    public function test_look_for_matches_2() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'milk', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $array_other=array($lexem1, $lexem2);
        $pair1=new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $array_correct=array($pair1);
        $this->assertTrue(blocks_formal_langs_token_base_look_for_matches_test::equal_arrays($lexem3->look_for_matches($array_other, 0.6, true, $options), $array_correct));//One pair for correct token "family" found
    }
    
    public function test_look_for_matches_3() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'milk', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'family', null, 2);
        $lexem4=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $array_other=array($lexem1, $lexem2, $lexem3);
        $pair1=new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair2=new block_formal_langs_matched_tokens_pair(array(2), array(0), 0);
        $array_correct=array($pair1, $pair2);
        $this->assertTrue(blocks_formal_langs_token_base_look_for_matches_test::equal_arrays($lexem3->look_for_matches($array_other, 0.6, true, $options), $array_correct));//Two pairs for correct token "family" found
    }

    public function test_look_for_matches_4() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'milk', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'family', null, 2);
        $lexem4=new block_formal_langs_token_base(null, 'type', 'milk', null, 3);
        $lexem5=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem6=new block_formal_langs_token_base(null, 'type', 'milk', null, 1);
        $lexem7=new block_formal_langs_token_base(null, 'type', 'tonus', null, 2);
        $array_other=array($lexem1, $lexem2, $lexem3);
        $pair1=new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $pair2=new block_formal_langs_matched_tokens_pair(array(3), array(1), 0);
        $array_correct=array($pair1, $pair2);
        $this->assertTrue(blocks_formal_langs_token_base_look_for_matches_test::equal_arrays($lexem6->look_for_matches($array_other, 0.6, true, $options), $array_correct));
        $this->assertTrue(count($lexem7->look_for_matches($array_other, 1, true, $options))==0);
    }
}