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
 
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');



/**
 * Computes every lcs between answer and response
 * @param array $answer - array of tokens
 * @param array $response - array of tokens
 * @return array  array of lcs, where keys are answer indexes and response indexes are values
 */
function qtype_correctwriting_sequence_analyzer_compute_lcs($answer,$response) {
    //An array of matches, where keys are indexes of answer and values are arrays of 
    //indexes from response
    $matches = array();
    //Fill an array of matches filling an lcs data
    $answercount = count($answer);
    $responsecount = count($response);
    //Flag, that determines whether we found a match
    $hasmatch = false;
    for ($i = 0;$i < $answercount;$i++) {
        $matches[$i] = array();
        for ($j = 0;$j < $responsecount;$j++) {
            if ($answer[$i]->is_same($response[$j])) {
              $matches[$i][] = $j;
              $hasmatch = true;
            }
        }
    }
    
    //If no matches are found, stop right there
    if ($hasmatch == false) {
        return array();
    }
    //An array of found common subsequences, where a subsequence is hash data, to current subsequence,
    //where ["maxind"] - maximum index, which can be taken when appending 
    //      ["lcs"]    - array, which is represented an lcs, as described in description of function
    $tmplcs = array();
    
    //Compute temporary lcs data
    for($currenttoken = $answercount - 1;$currenttoken > -1;$currenttoken--) {
        $newtmplcs = $tmplcs;
        for($currentmatch = 0;$currentmatch < count($matches[$currenttoken]);$currentmatch++) {
            //Scan existing suffixes and push match to it if can, changing maxind to current match
            for ($currentcs = 0;$currentcs < count($tmplcs);$currentcs++) {
                //If we can append to current match (found symbol index is lesser then bound)
                if($tmplcs[$currentcs]["maxind"] > $matches[$currenttoken][$currentmatch]) {
                    //Copy suffix and prepend our token to it
                    $suffix = $tmplcs[$currentcs];
                    $suffix["maxind"] = $matches[$currenttoken][$currentmatch];
                    $suffix["lcs"][$currenttoken] = $matches[$currenttoken][$currentmatch];
                    $newtmplcs[] = $suffix;
                }
            }
            //Create new suffix and add it to a tmplcs
            $suffix["maxind"] = $matches[$currenttoken][$currentmatch];
            $suffix["lcs"] = array();
            $suffix["lcs"][$currenttoken] = $matches[$currenttoken][$currentmatch];
            $newtmplcs[] = $suffix;
        }
        $tmplcs = $newtmplcs;
    }
    
    //Find length of LCS
    $lcslen = 0;
    for($i = 0;$i < count($tmplcs);$i++) {
        if (count($tmplcs[$i]["lcs"]) > $lcslen) {
            $lcslen = count($tmplcs[$i]["lcs"]);
        }
    }
    
    //Filter LCS from array of CS
    $lcs = array();
    for($i=0;$i < count($tmplcs);$i++) {
        if (count($tmplcs[$i]["lcs"]) == $lcslen) {
            $lcs[] = $tmplcs[$i]["lcs"];
        }
    }
    
    return $lcs;
}    