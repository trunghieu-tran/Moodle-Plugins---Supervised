<?php

/**
 * Defines unit-tests for sequence analyzer
 *
 * For a complete info, see qtype_correctwriting_sequence_analyzer
 *
 * @copyright &copy; 2011  Dmitry Mamontov
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
 require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
 
 /**
  *  Creates a specified tokens. Used for testing.
  *  @param array types - array of token types
  *  @param array values - values of tokens
  *  @param bool is_answer - is it an answer or responses
  *  @return array array of tokens
  */
function create_tokens($types,$values,$is_answer) {
    $result=array();
    for($i=0;$i<count($type);$i++) {
      array_push($result,new qtype_correctwriting_token_base($types[$i],$values[$i],$is_answer,null);
    }
    return $result;
}

/** Creates an answer lexemes. Used for testing
 */
function create_answer($types,$values) {
    return create_tokens($types,$values,true);
}

/** Creates a response lexemes. Used for testing
 */
function create_response($types,$values) {
    return create_tokens($types,$values,false);
}


 //Class of unit-test for sequence analyzer
 class qtype_correctwriting_sequence_analyzer_simpletest extends UnitTestCase {
    
    /**
     *  Returns lcs for test-cases
     */
    private function get_test_lcs($answer_types,$answer_values,$response_types,$response_values) {
       $answer=create_answer($answer_types,$answer_values);
       $response=create_response($response_types,$response_values);
       $test_seq_an=new qtype_correctwriting_sequence_analyzer($response,$answer);
       return $test_seq_an->lcs();
    }
    //Tests lcs for equal lexeme
    public function test_equal_correctedresponse() {
       $types=array("noun","verb","verb","exclamation_mark");
       $values=array("I","am","testing","!");
       $lcs=$this->get_test_lcs($types,$values,$types,$values);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==1 );
       //Check LCS
       $this->assertTrue( $lcs[0]==0);
       $this->assertTrue( $lcs[1]==1);
       $this->assertTrue( $lcs[2]==2);
       $this->assertTrue( $lcs[3]==3);
       
    }
    //Tests lcs for replaced lexemes
    public function test_replaced_lexemes() {
       $types=array("noun","verb","verb","exclamation_mark");
       $values=array("I","am","testing","!");
       $rvalues=array("She","is","testing","!");
       $lcs=$this->get_test_lcs($types,$values,$types,$rvalues);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==1 );
       
       //Check LCS
       $this->assertTrue( $lcs[0][2]==2 );
       $this->assertTrue( $lcs[0][3]==3 );
       $this->assertTrue( count($lcs[0])==2);
    }
    
    //Tests lcs for removed lexemes
    public function test_removed_lexemes() {
       $atypes=array("noun","verb","verb","exclamation_mark");
       $avalues=array("I","am","testing","!");
       $rtypes=array("noun","verb","verb");
       $rvalues=array("I","am","testing");
       $lcs=$this->get_test_lcs($atypes,$avalues,$rtypes,$rvalues);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==1 );
       
       $this->assertTrue( $lcs[0][0]==0 );
       $this->assertTrue( $lcs[0][1]==1 );
       $this->assertTrue( $lcs[0][2]==2 );
       $this->assertTrue( count($lcs[0])==3);   
    }
    
    //Tests lcs for added lexemes
    public function test_added_lexemes() {
       $atypes=array("noun","verb","verb","exclamation_mark");
       $avalues=array("I","am","testing","!");
       $atypes=array("noun","verb","verb","exclamation_mark","exclamation_mark");
       $avalues=array("I","am","testing","!","!");
       $lcs=$this->get_test_lcs($atypes,$avalues,$rtypes,$rvalues);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==2 );
       
       //Check LCS   
       $this->assertTrue( count($lcs[0])==4);
       $this->assertTrue( $lcs[0][0]==0 );
       $this->assertTrue( $lcs[0][1]==1 );
       $this->assertTrue( $lcs[0][2]==2 );
       $this->assertTrue( $lcs[0][3]==3 );
       
       $this->assertTrue( count($lcs[1])==1);
       $this->assertTrue( $lcs[1][3]==4 );
    }
    
    //Tests analyzer for empty lcs
    public function test_empty_lcs() {
       $types=array("noun");
       $avalues=array("I");
       $rvalues=array("She");
       $lcs=$this->get_test_lcs($types,$avalues,$types,$rvalues);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==0 );
    }
    
    //Performs a common test for analyzer
    public function test_common() {
       $atypes=array("data","data","data","data","data");
       $avalues=array("This","is","correct","answer",".");
       $rtypes=array( "data","data","data",  "data",  "data","data"      ,"data");
       $rvalues=array("This","not","correct","answer","This","definitely","not");
       $lcs=$this->get_test_lcs($types,$avalues,$types,$rvalues);
       //Check LCS props
       $this->assertTrue( $lcs!=null );
       $this->assertTrue( count($lcs)==3 );
       
       $this->assertTrue( count($lcs[0])==2);
       $this->assertTrue( $lcs[0][2]==2 );
       $this->assertTrue( $lcs[0][3]==3 );       

       $this->assertTrue( count($lcs[1])==1);
       $this->assertTrue( $lcs[1][0]==0 );
      
       $this->assertTrue( count($lcs[2])==1);
       $this->assertTrue( $lcs[2][0]==4 );
    }
 }