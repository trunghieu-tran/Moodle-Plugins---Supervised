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

// Simple entry, which will be printed
class qtype_correctwriting_abstract_label
{

}
// An empty label leaves noting in image and used in stub to skip some parts 
class qtype_correctwriting_empty_label extends qtype_correctwriting_abstract_label
{

} 
 // A label - simple text+color, which will be output into image
class qtype_correctwriting_lexeme_label extends qtype_correctwriting_abstract_label
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

class qtype_correctwriting_table_cell
{
   /**
     @var qtype_correctwriting_abstract_label Answer part
    */
   private $answer;
   /**
     @var qtype_correctwriting_abstract_label Response part
    */
   private $response;
  
   public function __construct($answer, $response) {
       $this->answer = $answer;
       $this->response = $response; 
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
   /**
     @var array array of qtype_correctwriting_table_cell, representing a table
    */
   private $table;
   /**
     @var indexes of cells for answer in $table field 
     */
   private $answertable;
   /**
     @var indexes of cells for response in $table field 
     */
   private $responsetable;
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
       // Align labels in a rows, without a links between them
       $this->create_alignments();
   }
   
   protected function is_moved_or_absent_answer($index) {
       return $this->is_in_arrays('absentlexemes','answer',$index);
   }
   protected function is_moved_or_added_response($index) {
       return $this->is_in_arrays('addedlexemes','response',$index);
   }
   
   protected function is_in_arrays($skippedarrayname, $movedfieldname, $index) {
       $result = false;
       foreach($this->$skippedarrayname as $testindex) {
           if ($index == $testindex) {
               $result = true;
           }
       }
       foreach ($this->movedlexemes as $testindex)  {
           if ($index == $testindex->$movedfieldname) {
               $result = true;
           }
       }
       return $result;
   }
   
   protected function create_alignments() {
       $answerindex = 0;
       $responseindex = 0;
       $this->table = array();
       $lcsanswers = array();
       $lcsresponses = array();
       for($answerindex = 0 ; $answerindex < count($this->answer) ; $answerindex++) {
           if ($this->is_moved_or_absent_answer($answerindex) == false) {
               $lcsanswers[] = $answerindex;
           }
       }
       for($responseindex = 0; $responseindex < count($this->response) ; $responseindex++) {
           if ($this->is_moved_or_added_response($responseindex) == false) {
               $lcsresponses[] = $responseindex;
           }
       }
       $beginanswers = 0;
       $beginresponses = 0;
       for ($i = 0 ;$i<count($lcsanswers);$i++) {
           $answerend = $lcsanswers[$i];
           $responseend = $lcsresponses[$i];
           // We assume that before LCS only moved and removed parts are added, so we can add it any way possible
           while($beginanswers < $answerend) {
               $this->create_answer($beginanswers);
               $beginanswers++;
           }
           while($beginresponses < $responseend ) {
               $this->create_response($beginresponses);
               $beginresponses++;
           }
           // Push LCS part
           $this->table[] = new qtype_correctwriting_table_cell($this->answer[$answerend], 
                                                                $this->response[$responseend]);
           $lastindex = count($this->table) - 1 ;
           $this->answertable[$answerend] = $lastindex;
           $this->responsetable[$responseend] = $lastindex;
           // Set next added
           $beginanswers = $answerend + 1;
           $beginresponses = $responseend + 1;
       }
       
       // Add all with indexes bigger than lcs
       $beginanswers = 0;
       if (count($lcsanswers)!=0)
       {
         $beginanswers = $lcsanswers[count($lcsanswers)-1]+1;
       }
       $beginresponses = 0;
       if (count($lcsresponses)!=0)
       {
        $beginresponses = $lcsresponses[count($lcsresponses)-1]+1;
       }
        
       while($beginanswers < count($this->answer)) {
           $this->create_answer($beginanswers);
           $beginanswers++;
       }
       while($beginresponses < count($this->response)) {
           $this->create_response($beginresponses);
           $beginresponses++;
       }
   }
   
   protected function create_answer($beginanswer) {
       $entry = new qtype_correctwriting_empty_label();
       $this->table[] = new qtype_correctwriting_table_cell($this->answer[$beginanswer], $entry);
       $this->answertable[$beginanswer] = count($this->table) - 1 ;
   }
   protected function create_response($beginresponse) {
       $entry = new qtype_correctwriting_empty_label();
       $this->table[] = new qtype_correctwriting_table_cell($entry, $this->response[$beginresponse]);
       $this->responsetable[$beginresponse] = count($this->table) - 1 ;
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