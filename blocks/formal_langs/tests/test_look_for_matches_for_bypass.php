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
    //comparison of the two arrays
    static public function standart_array_compare($op1, $op2) {
        if (count($op1)==count($op2)) {
            for ($i=0; $i<count($op1); $i++) {
                if ($op1[$i]!=$op2[$i]) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }
    //comparison of the two pairs
    static public function eq($x, $y) {
        if ($x->mistakeweight==$y->mistakeweight && block_formal_langs_token_stream_test::standart_array_compare($x->correcttokens, $y->correcttokens) && block_formal_langs_token_stream_test::standart_array_compare($x->comparedtokens, $y->comparedtokens))
            return true;
        else
            return false;
    }
    //comparison of arrays pairs
    static public function equal_arrays($x, $y) {
        if (count($x)!=count($y)) {
            return false;//Arrays are not equal in size
        }
        for ($i=0; $i<count($x); $i++) {
            $flag=1;
            for ($j=0; $j<count($x); $j++) {
                if(block_formal_langs_token_stream_test::eq($x[$i], $y[$j]))
                    $flag=0;
                if($flag==1) {
                    return false;//Not all the elements of the array1 have found a couple
                }
            }
            for ($i=0; $i<count($x);$i++) {
                $flag=1;
                for ($j=0; $j<count($y);$j++) {
                    if (block_formal_langs_token_stream_test::eq($y[$i], $x[$j]))
                        $flag=0;
                }
                if ($flag==1) {
                    return false;//Not all the elements of the array2 have found a couple
                }
            }
            return true;
        }
    }

    public function test_look_for_matches_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'cat', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'map', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'game', null, 0);
        $array_other=array($lexem1, $lexem2);
        $this->assertTrue(count($lexem3->look_for_matches($array_other, 1, true, $options, true))==0, 'Threshold is 100. Pairs for correct token "game" are not found');
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
        $this->assertTrue(block_formal_langs_token_stream_test::equal_arrays($lexem3->look_for_matches($array_other, 0.6, true, $options, true), $array_correct));//One pair for correct token "family" found
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
        $pair2=new block_formal_langs_matched_tokens_pair(array(0), array(2), 0);
        $array_correct=array($pair1, $pair2);
        $this->assertTrue(block_formal_langs_token_stream_test::equal_arrays($lexem4->look_for_matches($array_other, 0.6, true, $options, true), $array_correct));//Two pairs for correct token "family" found
    }

    public function test_look_for_matches_4() {
        //family
        // family milk family
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;
        $lexem1=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'family', null, 0);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'milk', null, 1);
        $lexem4=new block_formal_langs_token_base(null, 'type', 'family', null, 2);
        $array_other=array($lexem1);
        $this->assertTrue(count($lexem2->look_for_matches($array_other, 0, false, $options, true))==1);
        $this->assertTrue(count($lexem3->look_for_matches($array_other, 0, false, $options, true))==0);
        $this->assertTrue(count($lexem4->look_for_matches($array_other, 0, false, $options, true))==1);
    }
}