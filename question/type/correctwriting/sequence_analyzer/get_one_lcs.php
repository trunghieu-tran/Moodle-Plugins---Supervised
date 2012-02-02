<?php
/**
 * Defines an implementation for extracting one lcs for sequences of answer and response
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
 defined('MOODLE_INTERNAL') || die();
 
 require_once($CFG->dirroot.'/question/type/correctwriting/langs_code/tokens_base.php');
 
 /**
  * Private implementation of is same function for tokens
  * @param c1 object first token
  * @param c2 object second token
  * @return boolean whether tokens are equal
  */
function qtype_correctwriting_sequence_analyzer_private_is_same($c1,$c2) {
    return $c1->is_same($c2);   
}
    
/** 
  * Computes a length LCS from two sequences s1 and s2 for indexes i1 and i2, recursively filling table of subsequences
  * @param  table array of array of mixed  a table of entries 
  *                               ("length" => length(-1 if not computed),
  *                                "subx" => index of subproblem index (-1 if null),
  *                                "suby" => index of subproblem index (-1 if null) )
  * @param  s1    array   array of answer tokens
  * @param  s2    array   array of response tokens
  * @param  i1    integer  current answer index
  * @param  i2    integer  current response index
  * @return length of LCS subproblem
  */
function qtype_correctwriting_sequence_analyzer_compute_lcs(&$table,$s1,$s2,$i1,$i2) {
    if($table[$i1][$i2]["length"]==-1) {
        if (qtype_correctwriting_sequence_analyzer_private_is_same($s1[$i1],$s2[$i2])) {
            $table[$i1][$i2]["subx"]=$i1-1;
            $table[$i1][$i2]["suby"]=$i2-1;
            $table[$i1][$i2]["length"]=qtype_correctwriting_sequence_analyzer_compute_lcs($table,$s1,$s2,$i1-1,$i2-1)+1;
        } else {
            $way1=qtype_correctwriting_sequence_analyzer_compute_lcs($table,$s1,$s2,$i1-1,$i2);
            $way2=qtype_correctwriting_sequence_analyzer_compute_lcs($table,$s1,$s2,$i1,$i2-1);
            if ($way1>$way2) {
                $table[$i1][$i2]["subx"]=$i1-1;
                $table[$i1][$i2]["suby"]=$i2;
                $table[$i1][$i2]["length"]=$way1;
            } else {
                $table[$i1][$i2]["subx"]=$i1;
                $table[$i1][$i2]["suby"]=$i2-1;
                $table[$i1][$i2]["length"]=$way2;
            }
        }
    }
    return $table[$i1][$i2]["length"];
}
/**
 * Computes an lcs for two sequences 
 * @param
 * @return array longest common subsequence, where keys are indexes of answer array, and values are indexes of response array
 */
function qtype_correctwriting_sequence_analyzer_get_lcs($s1,$s2) {
    //Compute length of value
    $count1=count($s1);
    $count2=count($s2);  
    
    //If one of sequences is empty, there is no way the lcs could exist return empty array
    if ($count1==0 || $count2==0)
        return array();
      
    //Init computation table
    $table=array();
    for ($i=-1;$i<$count1;$i++) {
        $table[$i]=array();
    }
    //Fill computation table with uninitialized values
    for ($i=-1;$i<$count1;$i++) {
        for ($j=-1;$j<$count2;$j++) {
            //If this is first row is empty
            if ($i==-1 || $j==-1) {
                $table[$i][$j]=array("length" => 0, "subx" => -1, "suby" => -1);
            } else { //Orherwise set value as unitialized
                $table[$i][$j]=array("length" => -1, "subx" => -1, "suby" => -1);
            }
        }
    }
    //Compute LCS length, filling traceback table
    $length=qtype_correctwriting_sequence_analyzer_compute_lcs($table,$s1,$s2,$count1-1,$count2-1);
    //Restore LCS from table, using traceback
    $curx=$count1-1;
    $cury=$count2-1;
    $result=array();
    while($curx!=-1 && $cury!=-1) {
        if (qtype_correctwriting_sequence_analyzer_private_is_same($s1[$curx],$s2[$cury]))
            $result[$curx]=$cury;
        $tmpx=$table[$curx][$cury]["subx"];
        $tmpy=$table[$curx][$cury]["suby"];
        $curx=$tmpx;
        $cury=$tmpy;
    }
    return $result;
}
    
?>