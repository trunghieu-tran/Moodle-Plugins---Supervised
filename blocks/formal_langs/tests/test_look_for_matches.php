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

class blocks_formal_langs_token_base_look_for_matches_test extends UnitTestCase {
//comparison of the two arrays
        static public function standart_array_compare($op1, $op2) {
        if(count($op1)==count($op2))
        {
            for($i=0;$i<count($op1);$i++)
            {
                if($op1[$i]!=$op2[$i])
                    return false;
            }
        }
        else
            return false;
        return true;
    }
    //comparison of the two pairs
        static public function eq($x, $y) {
        if($x->mistakeweight==$y->mistakeweight && blocks_formal_langs_token_base_look_for_matches_test::standart_array_compare($x->correcttokens, $y->correcttokens) && blocks_formal_langs_token_base_look_for_matches_test::standart_array_compare($x->comparedtokens, $y->comparedtokens))
            return true;
        else
            return false;
    }
    //comparison of arrays pairs
    static public function equal_arrays($x, $y) {
        if(count($x)!=count($y))
        {
            return false;//Arrays are not equal in size
        }
        for($i=0;$i<count($x);$i++)
        {
            $flag=1;
            for($j=0; $j<count($x);$j++)
            {
                if(blocks_formal_langs_token_base_look_for_matches_test::eq($x[$i],$y[$j]))
                    $flag=0;
                if($flag==1)
                {
                    return false;//Not all the elements of the array1 have found a couple
                }
            }
            for($i=0; $i<count($x);$i++)
            {
                $flag=1;
                for($j=0; $j<count($y);$j++)
                {
                    if(blocks_formal_langs_token_base_look_for_matches_test::eq($y[$i],$x[$j]))
                        $flag=0;
                }
                if($flag==1)
                {
                    return false;//Not all the elements of the array2 have found a couple
                }
            }
            return true;
        }
    }


    //Pairs are not found. Threshold is 1
    function test_look_for_matches_1() {
        $lexem1=new block_formal_langs_token_base(null,'type','cat',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','map',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','game',null,0);
        $array_other=array($lexem1,$lexem2);
        $this->assertTrue(count($lexem3->look_for_matches($array_other,1,true))==0,'Threshold is 100. Pairs for correct token "game" are not found');
        $this->assertTrue(count($lexem3->look_for_matches($array_other,1,false))==0,'Threshold is 100. Pairs for incorrect token "game" are not found');
    }
    //One pair for correct token and null pairs for incorrect token
    public function test_look_for_matches_2() {
        $lexem1=new block_formal_langs_token_base(null,'type','amily',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','milk',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','family',null,0);
        $array_other=array($lexem1,$lexem2);
        $pair1=new block_formal_langs_matched_tokens_pair(array(0),array(0),1);
        $array_correct=array($pair1);
        $this->assertTrue(blocks_formal_langs_token_base_look_for_matches_test::equal_arrays($lexem3->look_for_matches($array_other,0.6,true),$array_correct));//One pair for correct token "family" found
        $this->assertTrue(count($lexem3->look_for_matches($array_other,0.6,false))==0);//Pairs for incorrect token "family" are not found
    }
    //Pairs are not found. Words are short
    public function test_look_for_matches_3() {
        $lexem1=new block_formal_langs_token_base(null,'type','cat',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','red',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','blue',null,0);
        $array_other=array($lexem1,$lexem2);
        $this->assertTrue(count($lexem3->look_for_matches($array_other,0.5,false))==0,'Pairs are not found');
        $this->assertTrue(count($lexem3->look_for_matches($array_other,0.5,true))==0,'Pairs are not found');
    }
    //Test for correct token
    public function test_look_for_matches_4() {
        $lexem1=new block_formal_langs_token_base(null,'type','amily',null,0);
        $lexem2=new block_formal_langs_token_base(null,'type','milk',null,1);
        $lexem3=new block_formal_langs_token_base(null,'type','family',null,0);
        $lexem4=new block_formal_langs_token_base(null,'type','yellow',null,2);
        $lexem5=new block_formal_langs_token_base(null,'type','fa',null,3);
        $lexem6=new block_formal_langs_token_base(null,'type','mily',null,4);
        $array_other=array($lexem1,$lexem2,$lexem4,$lexem5,$lexem6);
        $pair1=new block_formal_langs_matched_tokens_pair(array(0),array(0),1);
        $pair2=new block_formal_langs_matched_tokens_pair(array(0),array(3,4),1);
        $pair3=new block_formal_langs_matched_tokens_pair(array(3,4),array(0),1);
        $array_correct=array($pair1,$pair2);
        $array_incorrect=array($pair3);
        //$this->assertTrue(equal_arrays($lexem3->look_for_matches($array_other,0.6,true),$array_correct),'Two pairs (a typo and extra separator) for correct token');
        //$this->assertTrue(equal_arrays($lexem3->look_for_matches($array_other,0.6,false),$array_incorrect),'One pair (missed separator) for incorrect token');
   }
}


?>