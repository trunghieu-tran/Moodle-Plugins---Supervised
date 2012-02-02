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
    
    //Tests lcs for equal lexeme
    public function test_equal_correctedresponse() {
       $types=array("noun","verb","verb","exclamation_mark");
       $values=array("I","am","testing","!");
       $answer=create_answer($types,$values);
       $response=create_response($types,$values);
       $test_seq_an=new qtype_correctwriting_sequence_analyzer($response,$answer);
       $lcs=$test_seq_an->lcs();
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
       $answer=array( new qtype_correctwriting_token_base("I","noun",true,new qtype_correctwriting_node_position(0,0,0,3)),
                      new qtype_correctwriting_token_base("am","article",true,new qtype_correctwriting_node_position(0,0,5,7)),
                      new qtype_correctwriting_token_base("testing","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("!","exclamation_mark",true,new qtype_correctwriting_node_position(0,0,17,17))
                    );
       $response=array( new qtype_correctwriting_token_base("She","noun",false,new qtype_correctwriting_node_position(0,0,0,3)),
                        new qtype_correctwriting_token_base("is","article",false,new qtype_correctwriting_node_position(0,0,7,9)),
                        new qtype_correctwriting_token_base("testing","verb",false,new qtype_correctwriting_node_position(0,0,13,20)),
                        new qtype_correctwriting_token_base("!","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,22,22))
                      );
       $test_seq_an=new qtype_correctwriting_sequence_analyzer(null,$answer,null,$response);
       //Check some errors
       $this->assertTrue($test_seq_an->is_errors() );
       
       //Check LCS
       $lcs=$tests_seq_an->lcs();
       $this->assertTrue( array_key_exists($lcs[0],2) && lcs[0][2]==2 );
       $this->assertTrue( array_key_exists($lcs[0],3) && lcs[0][3]==3 );
       $this->assertTrue( count($lcs[0])==2);

       //TODO: Develop error class for testing errors. Place test for errors here
    }
    
    //Tests lcs for removed lexemes
    public function test_removed_lexemes() {
       $answer=array( new qtype_correctwriting_token_base("I","noun",true,new qtype_correctwriting_node_position(0,0,0,3)),
                      new qtype_correctwriting_token_base("am","article",true,new qtype_correctwriting_node_position(0,0,5,7)),
                      new qtype_correctwriting_token_base("testing","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("!","exclamation_mark",true,new qtype_correctwriting_node_position(0,0,17,17))
                    );
       $response=array( new qtype_correctwriting_token_base("I","noun",false,new qtype_correctwriting_node_position(0,0,0,3)),
                        new qtype_correctwriting_token_base("am","article",false,new qtype_correctwriting_node_position(0,0,7,9)),
                        new qtype_correctwriting_token_base("testing","verb",false,new qtype_correctwriting_node_position(0,0,13,20))
                      );
       $test_seq_an=new qtype_correctwriting_sequence_analyzer(null,$answer,null,$response);
       //Check some errors
       $this->assertTrue($test_seq_an->is_errors() );
       
       //Check LCS
       $lcs=$tests_seq_an->lcs();
       $this->assertTrue( array_key_exists($lcs[0],0) && lcs[0][0]==0 );
       $this->assertTrue( array_key_exists($lcs[0],1) && lcs[0][1]==1 );
       $this->assertTrue( array_key_exists($lcs[0],2) && lcs[0][2]==2 );
       $this->assertTrue( count($lcs[0])==3);

       //TODO: Develop error class for testing errors. Place test for errors here
    }
    
    //Tests lcs for added lexemes
    public function test_added_lexemes() {
       $answer=array( new qtype_correctwriting_token_base("I","noun",true,new qtype_correctwriting_node_position(0,0,0,3)),
                      new qtype_correctwriting_token_base("am","article",true,new qtype_correctwriting_node_position(0,0,5,7)),
                      new qtype_correctwriting_token_base("testing","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("!","exclamation_mark",true,new qtype_correctwriting_node_position(0,0,17,17))
                    );
       $response=array( new qtype_correctwriting_token_base("I","noun",false,new qtype_correctwriting_node_position(0,0,0,3)),
                        new qtype_correctwriting_token_base("am","article",false,new qtype_correctwriting_node_position(0,0,7,9)),
                        new qtype_correctwriting_token_base("testing","verb",false,new qtype_correctwriting_node_position(0,0,13,20))
                        new qtype_correctwriting_token_base("!","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,22,22))
                        new qtype_correctwriting_token_base("!","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,23,23))
                      );
       $test_seq_an=new qtype_correctwriting_sequence_analyzer(null,$answer,null,$response);
       //Check some errors
       $this->assertTrue($test_seq_an->is_errors() );
       
       //Check LCS
       $lcs=$tests_seq_an->lcs();
       $this->assertTrue( count($lcs)==2);
       
       $this->assertTrue( array_key_exists($lcs[0],0) && lcs[0][0]==0 );
       $this->assertTrue( array_key_exists($lcs[0],1) && lcs[0][1]==1 );
       $this->assertTrue( array_key_exists($lcs[0],2) && lcs[0][2]==2 );
       $this->assertTrue( array_key_exists($lcs[0],3) && lcs[0][3]==3 );
       $this->assertTrue( count($lcs[0])==4);

       $this->assertTrue( array_key_exists($lcs[1],3) && lcs[1][3]==4 );
       $this->assertTrue( count($lcs[1])==1);
       
       //TODO: Develop error class for testing errors. Place test for errors here
    }
    
    //Tests analyzer for empty lcs
    public function test_empty_lcs() {
       $answer=array( new qtype_correctwriting_token_base("I","noun",true,new qtype_correctwriting_node_position(0,0,0,3)) );
       $response=array( new qtype_correctwriting_token_base("I","noun",false,new qtype_correctwriting_node_position(0,0,0,3)));
       $test_seq_an=new qtype_correctwriting_sequence_analyzer(null,$answer,null,$response);
       $this->assertTrue($test_seq_an->is_errors() );
       //Check LCS
       $lcs=$tests_seq_an->lcs();
       $this->assertTrue( count($lcs)==0);
    }
    
    //Performs a common test for analyzer
    public function test_common() {
       $answer=array( new qtype_correctwriting_token_base("This","noun",true,new qtype_correctwriting_node_position(0,0,0,3)),
                      new qtype_correctwriting_token_base("is","article",true,new qtype_correctwriting_node_position(0,0,5,7)),
                      new qtype_correctwriting_token_base("example","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("of","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("correct","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base("answer","verb",true,new qtype_correctwriting_node_position(0,0,9,16)),
                      new qtype_correctwriting_token_base(".","dot",true,new qtype_correctwriting_node_position(0,0,17,17))
                    );
       $response=array( new qtype_correctwriting_token_base("This","noun",false,new qtype_correctwriting_node_position(0,0,0,3)),
                        new qtype_correctwriting_token_base("is","article",false,new qtype_correctwriting_node_position(0,0,7,9)),
                        new qtype_correctwriting_token_base("example","verb",false,new qtype_correctwriting_node_position(0,0,13,20)),
                        new qtype_correctwriting_token_base("of","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,22,22)),
                        new qtype_correctwriting_token_base("answer","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,23,23)),
                        new qtype_correctwriting_token_base("that","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,23,23)),
                        new qtype_correctwriting_token_base("is","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,23,23)),
                        new qtype_correctwriting_token_base("incorrect","exclamation_mark",false,new qtype_correctwriting_node_position(0,0,23,23)),
                        new qtype_correctwriting_token_base(".","dot",false,new qtype_correctwriting_node_position(0,0,23,23)),
                        new qtype_correctwriting_token_base("This","noun",false,new qtype_correctwriting_node_position(0,0,0,3)),
                        new qtype_correctwriting_token_base("is","article",false,new qtype_correctwriting_node_position(0,0,7,9))
                      );
       $test_seq_an=new qtype_correctwriting_sequence_analyzer(null,$answer,null,$response);
       //Check some errors
       $this->assertTrue($test_seq_an->is_errors() );
       
       //Check LCS
       $lcs=$tests_seq_an->lcs();
       $this->assertTrue( count($lcs)==4);
       
       $this->assertTrue( array_key_exists($lcs[0],0) && lcs[0][0]==0 );
       $this->assertTrue( array_key_exists($lcs[0],1) && lcs[0][1]==1 );
       $this->assertTrue( array_key_exists($lcs[0],2) && lcs[0][2]==2 );
       $this->assertTrue( array_key_exists($lcs[0],3) && lcs[0][3]==3 );
       $this->assertTrue( count($lcs[0])==4);

       $this->assertTrue( array_key_exists($lcs[1],5) && lcs[1][5]==4 );
       $this->assertTrue( count($lcs[1])==1);
       
       $this->assertTrue( array_key_exists($lcs[2],1) && lcs[2][1]==6 );
       $this->assertTrue( count($lcs[2])==1);
       
       $this->assertTrue( array_key_exists($lcs[3],0) && lcs[3][0]==8 );
       $this->assertTrue( array_key_exists($lcs[3],1) && lcs[3][1]==9 );
       $this->assertTrue( count($lcs[3])==2);
       
       //TODO: Develop error class for testing errors. Place test for errors here
    }
 }