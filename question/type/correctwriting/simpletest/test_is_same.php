<?php
/**
 * Defines unit-tests for token_base is same
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
 require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');

class qtype_correctwriting_sequence_analyzer_token_base_is_same extends UnitTestCase {
    //Test when a tokens are totally equal
    public function test_equal_tokens() {
        $answer=new qtype_correctwriting_token_base(null,"type","value",true,null);
        $response=new qtype_correctwriting_token_base(null,"type","value",false,null);
        $this->assertTrue( $answer->is_same($response) );
    }
    //Test when tokens are totally equal and value is null
    public function test_equal_tokens_is_null() {
        $answer=new qtype_correctwriting_token_base(null,"type",null,true,null);
        $response=new qtype_correctwriting_token_base(null,"type",null,false,null);
        $this->assertTrue( $answer->is_same($response) );
    }
    //Test when tokens are not equal and values are different
    public function test_inequal_values() {
        $answer=new qtype_correctwriting_token_base(null,"type",null,true,null);
        $response=new qtype_correctwriting_token_base(null,"type","test",false,null);
        $this->assertFalse( $answer->is_same($response) );
    }
    //Test when tokens are not equal and types are different
    public function test_inequal_types() {
        $answer=new qtype_correctwriting_token_base(null,"type","test",true,null);
        $response=new qtype_correctwriting_token_base(null,"type2","test",false,null);
        $this->assertFalse( $answer->is_same($response) );
    }
     
}
 ?>