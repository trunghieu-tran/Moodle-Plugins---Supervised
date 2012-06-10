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
                   $mistakecodeddata = '';
                   $url  = $CFG->wwwroot . '/question/type/correctwriting/mistakesimage.php?data=' . urlencode($mistakecodeddata);
                   $imagesrc = '<image src="'.$url.'">';
                   $resulttext = $imagesrc . $resulttext; 
               }
           }
           return $resulttext . parent::correct_response($qa);
       }
}