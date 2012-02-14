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
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer/common.php');



/**
 * Computes every lcs between answer and response
 * @param array answer - array of tokens
 * @param response response - array of tokens
 * @return array of lcs, where keys are answer indexes and response indexes are values
 */
function qtype_correctwriting_sequence_analyzer_compute_lcs($answer,$response) {
    //An array of matches, where keys are indexes of answer and values are arrays of 
    //indexes from response
    $matches=array();
    //Fill an array of matches filling an lcs data
    $answer_count=qtype_correctwriting_sequence_analyzer_count($answer);
    $response_count=qtype_correctwriting_sequence_analyzer_count($response);
    //Flag, that determines whether we found a match
    $has_match=false;
    for ($i=0;$i<$answer_count;$i++) {
        $matches[$i]=array();
        for ($j=0;$j<$response_count;$j++) {
            if (qtype_correctwriting_sequence_analyzer_is_same($answer[$i],$response[$j])) {
              array_push($matches[$i],$j);
              $has_match=true;
            }
        }
    }
    
    //If matches are not found, stop right there
    if ($has_match==false) {
        return array();
    }
    //An array of found common subsequences, where a subsequence is
    //mixed data, where ["maxind"] - maximum index, which can be taken when appending 
    //to current subsequence, ["lcs"] - array, which is represented an lcs, as described in
    //description of function
    $tmplcs=array();
    
    //Compute temporary lcs data
    for($current_token=$answer_count-1;$current_token>-1;$current_token--) {
        $newtmplcs=qtype_correctwriting_sequence_analyzer_array_clone($tmplcs);
        for($current_match=0;$current_match<count($matches[$current_token]);$current_match++) {
            //Scan existing suffixes and push match to it if can, changing maxind to current match
            for ($current_cs=0;$current_cs<count($tmplcs);$current_cs++) {
                //If we can append to current match (found symbol index is lesser then bound)
                if($tmplcs[$current_cs]["maxind"]>$matches[$current_token][$current_match]) {
                    //Copy suffix and prepend our token to it
                    $suffix=qtype_correctwriting_sequence_analyzer_array_clone($tmplcs[$current_cs]);
                    $suffix["maxind"]=$matches[$current_token][$current_match];
                    $suffix["lcs"][$current_token]=$matches[$current_token][$current_match];
                    array_push($newtmplcs,$suffix);
                }
            }
            //Create new suffix and add it to a tmplcs
            $suffix["maxind"]=$matches[$current_token][$current_match];
            $suffix["lcs"]=array();
            $suffix["lcs"][$current_token]=$matches[$current_token][$current_match];
            array_push($newtmplcs,$suffix);
        }
        $tmplcs=$newtmplcs;
    }
    
    //Select length of LCS
    $lcslen=0;
    for($i=0;$i<count($tmplcs);$i++) {
        if (count($tmplcs[$i]["lcs"])>$lcslen) {
            $lcslen=count($tmplcs[$i]["lcs"]);
        }
    }
    
    //Filter LCS from array of CS
    $lcs=array();
    for($i=0;$i<count($tmplcs);$i++) {
        if (count($tmplcs[$i]["lcs"])==$lcslen) {
            array_push($lcs,$tmplcs[$i]["lcs"]);
        }
    }
    
    return $lcs;
}    
    
?>