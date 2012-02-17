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
 
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
 
 /**
  * Determines a mistakes by scanning lcs , answer and response
  * @param array  $answer   array of tokens, that belong to answer
  * @param array  $response array of tokens, that belong to student reponse
  * @param array  $lcs      longest common subsequence as described at sequence_analyzer
  * @return array   key "moved" contains array of moved lexemes as key - index from answer and value -
  *                 index from response.
  *                 key "removed" contains array of removed lexemes as indexes from answer
  *                 key "added" contains array of added lexemes as indexes from response
  */
function qtype_correctwriting_sequence_analyzer_determine_mistakes($answer,$response,$lcs) {
    // Determines, whether answer tokens are used in error computation
    $answerused = array();
    $answercount = count($answer);
    for ($i = 0;$i < $answercount;$i++) {
        $answerused[] = false;
    }
    
    // Determines, whether response tokens are used in error computation
    $responseused = array();
    $responsecount = count($response);
    for ($i = 0;$i < $responsecount;$i++) {
        $responseused[] = false;
    }
    
    //This result will be returned from function
    $result = array();
    $result['moved'] = array();
    $result['removed'] = array();
    $result['added'] = array();
    
    //Scan lcs to mark excluded lexemes
    foreach($lcs as $answerindex => $responseindex) {
        //Mark lexemes as used
        $answerused[$answerindex] = true;
        $responseused[$responseindex] = true;
    }
    
    //Determine removed and moved lexemes by scanning answer 
    for ($i = 0;$i < $answercount;$i++) {
      //If this lexeme is not in LCS
      if ($answerused[$i] == false) {
        //Determine, whether lexeme is simply moved by scanning response or removed
        $ismoved = false;
        $movedpos = -1;
        for ($j = 0;($j < $responsecount) && ($ismoved == false);$j++) {
          if (($answer[$i]->is_same($response[$j]))
              && ($responseused[$j] == false)) {
           $ismoved = true;
           $movedpos = $j;
           $responseused[$j] = true;
          }
        }
        //Determine type of mistake (moved or removed)
        if ($ismoved) {
            $result['moved'][$i] = $movedpos;
        } else {
            $result['removed'][] = $i;
        }
      }
    }
    
    //Determine added lexemes from reponse
    for ($i = 0;$i < $responsecount;$i++) {
        if ($responseused[$i] == false) {
            $result['added'][] = $i;            
        }
    }
    
    return $result;
}

?>