<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
/**
 * Generates the output for short answer questions.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_renderer extends qtype_shortanswer_renderer {

    public function specific_feedback(question_attempt $qa) {
       $question = $qa->get_question();
       $shortanswerfeedback = parent::specific_feedback($qa);
       $myfeedback = '';
       $analyzer = $question->matchedanalyzer;
       if ($analyzer!=null) {
           //Output mistakes messages
           if (count($analyzer->mistakes())!=0) {
               $mistakemesgs = array();
               $mistakemesgs[] = get_string('foundmistakes', 'qtype_correctwriting');
               foreach($analyzer->mistakes() as $mistake) {
                   $mistakemesgs[] = $mistake->mistakemsg;
               }
               $myfeedback  = implode('<BR>', $mistakemesgs) . "<BR>";
           }
       }
       return $myfeedback . $shortanswerfeedback; 
   }
   //This wil be shown only if show right answer is setup 
   public function correct_response(question_attempt $qa) {
       global $CFG;
       $question = $qa->get_question();
       $resulttext  = '<BR>';
       // This data should contain base64_encoded data about user mistakes
       $analyzer = $question->matchedanalyzer;
       if ($analyzer!=null) {
           if (count($analyzer->mistakes()) != 0) {
               $mistakecodeddata = $this->create_image_information($qa,$analyzer);
               $url  = $CFG->wwwroot . '/question/type/correctwriting/mistakesimage.php?data=' . urlencode($mistakecodeddata);
               $imagesrc = '<image src="'.$url.'">';
               $resulttext = $imagesrc . $resulttext; 
           }
       }
       return $resulttext . parent::correct_response($qa);
   }
   //Creates all information about mistakes, passed into mistakes    
   protected function create_image_information($qa,$analyzer) {
       $question = $qa->get_question();
       $answer  = $question->answers[$question->matchedanswerid]->answer;
       $language = block_formal_langs::lang_object($qa->get_question()->langid);
       //Create sections, that will be passed into an URL
       $resultsections = array();
       
       //Create answer section
       $answertokenvalues = array();
       $answertokens = $language->create_from_string($answer);
       foreach($answertokens as $token) {
           $answertokenvalues[] = base64_encode($token->value());
       }
       $resultsections[] = implode(',,,',$answertokenvalues);
       //Create response section
       $responsetokenvalues = array();
       $responsetokens = $analyzer->get_corrected_response();
       foreach($responsetokens as $token) {
           $responsetokenvalues[] = base64_encode($token->value());
       }
       $resultsections[] = implode(',,,',$responsetokenvalues);
       
       $fixedlexemes = array();
       $absentlexemes = array();
       $addedlexemes  = array();
       $movedlexemes = array();

       
       foreach($analyzer->mistakes() as $mistake) {
           // If this is lexical mistake, we should mark some lexeme as fixed
           if (is_a($mistake,'qtype_correctwriting_lexical_mistake')) {
               $fixedlexemes[] = $mistake->correctedresponseindex;
           // Track added mistakes
           } elseif (count($mistake->answermistaken) == 0) {
               foreach ($mistake->responsemistaken as $index) {
                   $addedlexemes[] = $index;
               }
           // Track absent mistakes
           }  elseif (count($mistake->responsemistaken)==0) {
                foreach ($mistake->answermistaken as $index) {
                   $absentlexemes[] = $index;
                }
            } else {
                for($i = 0;$i < count($mistake->answermistaken);$i++) {
                    $movedlexemes[] = $mistake->answermistaken[$i] . '_' . $mistake->responsemistaken[$i]; 
                }
            } 
       }
       
       //Gather all section
       $resultsections[] = implode(',,,',$fixedlexemes);
       $resultsections[] = implode(',,,',$absentlexemes);
       $resultsections[] = implode(',,,',$addedlexemes);
       $resultsections[] = implode(',,,',$movedlexemes);
       
       return  base64_encode(implode(';;;',$resultsections));
   }
}