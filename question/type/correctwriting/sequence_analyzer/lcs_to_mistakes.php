<?php
/**
 * Defines an implementation for common function, that determines a removed lexemes, replaced lexemes and added lexemes
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/blocks/formal_langs/base_token.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer/common.php');
 
 /**
  * Determines a mistakes by scanning lcs , answer and response
  * @param answer array of tokens, that belong to answer
  * @param response array of tokens, that belong to student reponse
  * @param lcs      longest common subsequence as described at sequence_analyzer
  * @return mixed   key "moved" contains array of moved lexemes as key - index from answer and value -
  *                 index from response.
  *                 key "removed" contains array of removed lexemes as indexes from answer
  *                 key "added" contains array of added lexemes as indexes from response
  */
function qtype_correctwriting_sequence_analyzer_determine_mistakes($answer,$response,$lcs) {
    //Determines, whether answer tokens are used in error computation
    $answer_used=array();
    $answer_count=qtype_correctwriting_sequence_analyzer_count($answer);
    for ($i=0;$i<$answer_count;$i++) {
        array_push($answer_used,false);
    }
    
    //Determines, whether response tokens are used in error computation
    $response_used=array();
    $response_count=qtype_correctwriting_sequence_analyzer_count($response);
    for ($i=0;$i<$response_count;$i++) {
        array_push($response_used,false);
    }
    
    //This result will be returned from function
    $result=array();
    $result["moved"]=array();
    $result["removed"]=array();
    $result["added"]=array();
    
    //Scan lcs to mark excluded lexemes
    foreach($lcs as $answer_index => $response_index) {
        //If lexeme is moved find it
        if ($answer_index!=$response_index) {
            $result["moved"][$answer_index]=$response_index;
        }
        //Mark lexemes as used
        $answer_used[$answer_index]=true;
        $response_used[$response_index]=true;
    }
    
    //Determine removed and moved lexemes by scanning answer 
    for ($i=0;$i<$answer_count;$i++) {
      //If this lexeme is not in LCS
      if ($answer_used[$i]==false) {
        //Determine, whether lexeme is simply moved by scanning response or removed
        $is_moved=false;
        $moved_pos=-1;
        for ($j=0;($j<$response_count) && ($is_moved==false);$j++) {
          if (qtype_correctwriting_sequence_analyzer_is_same($answer[$i],$response[$j])
              && ($response_used[$j]==false)) {
           $is_moved=true;
           $moved_pos=$j;
           $response_used[$j]==true;
          }
        }
        //Determine type of error (moved or removed)
        if ($is_moved) {
            $result["moved"][$i]=$moved_pos;
        } else {
            array_push($result["removed"],$i);
        }
      }
    }
    
    //Determine added lexemes from reponse
    for ($i=0;$i<$response_count;$i++) {
        if ($response_used[$i]==false) {
            array_push($result["added"],$i);            
        }
    }
    
    return $result;
}

?>