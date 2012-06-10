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

class qtype_correctwriting_lexeme_label
{
    /**
     @var string string of text
     */
    public $text;
    /**
     @var bool  whether it should be painted red (red means, it was fixed)
     */
    public $red;
    
    public function __construct($text) {
        $this->text = $text;
        $red  = false;
    }
} 
 
class qtype_correctwriting_image_generator
{
   /**
     @var array  array of strings, representing an answer
    */
   private $answer;
   /**
     @var array  array of strings, representing an response
    */
   private $response;
   /**
     @var array array of indexes of absent lexemes
    */
   private $absentlexemes;
   /**
     @var array array of indexes of added lexemes
    */
   private $addedlexemes;
   /**
     @var array array of indexes of added lexemes as stdClass( 'answer' => answer index, 'response' => response index) 
    */
   private $movedlexemes;
   
   /** Constructs a generator, scanning section
     @param array $sections used sections
    */
   public function __construct($sections) {
       // Preprocessed data
       
       // Preprocess answers
       $base64answers = explode(',,,',$sections[0]);
       foreach($base64answers as $answer) {
           $this->answer[] = new qtype_correctwriting_lexeme_label(base64_decode($answer));
       }
       
       $base64responses = explode(',,,',$sections[1]);
       foreach($base64responses as $response) {
           $this->response[] = new qtype_correctwriting_lexeme_label(base64_decode($response));
       }
       // Make fixed lexemes red
       $fixedlexemeindexes = array_diff(explode(',,,',$sections[2]), array(''));
       foreach($fixedlexemeindexes as $index) {
            $this->response[$index]->red = true;
       }
       
       $this->absentlexemes = array_diff(explode(',,,',$sections[3]), array(''));
       $this->addedlexemes = array_diff(explode(',,,',$sections[4]), array(''));
       $movedlexemes = array_diff(explode(',,,',$sections[5]), array(''));
       foreach($movedlexemes as $entry) {
           $tmp = explode('_',$entry);
           $data = new stdClass();
           $data->answer = $tmp[0];
           $data->response = $tmp[1];
           $this->movedlexemes[] = $data;
       }
       
   }
   // Produces an image
   public function produce_image() {
       
   }
}
   
// Scan parameter and check, whether it's malformed
if (strlen($_GET['data']) != 0) {
   $data = base64_decode($_GET['data']);
   $sections = explode(';;;',$data);
   if (count($sections) == 6 ) {
       // If everything is ok
       $generator = new qtype_correctwriting_image_generator($sections);
       $generator->produce_image();
       print_r($generator);
   } else {
       echo 'Error generating image: malformed data';
   }       
} else {
       echo 'Error generating image: no data supplied';
}
?>