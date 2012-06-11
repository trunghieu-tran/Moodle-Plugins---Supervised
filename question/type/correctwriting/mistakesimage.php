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
 * Mistake image generator
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// A vertical space between answer and response 
define('ANSWER_RESPONSE_VERTICAL_SPACE', 50);
// A horizontal space between two lexemes on image
define('ROW_HORIZONTAL_SPACE', 7);
// A tiny space between arrow connector and label
define('TINY_SPACE', 2);
// Used font size
define('FONT_SIZE', 4);
// Defines a width for drawing lines of moving, removing or adding
define('LINE_WIDTH', 2);
// A length of arrow end part
define('ARROW_LENGTH', 5);
// An arrow angle in radians
define('ARROW_ANGLE', 0.5);

// Simple entry, which will be printed
class qtype_correctwriting_abstract_label
{
  /**
    @var array  array(width, height)
    */
  public $wh = array(0,0);
  /**
    @var array  array(width, height) - connection point for each arrow
    */
  public $connection = array(0,0);
  
  /** Paints at position
   */
  public function paint(&$im, $palette, $currentrect, $up ) {
       $this->connection = array();
       $this->connection[] = $currentrect->x + $currentrect->width/2;
       if ($up == true) {
           $this->connection[] = $currentrect->y+$currentrect->height+TINY_SPACE;
       } else {
           $this->connection[] = $currentrect->y - TINY_SPACE; 
       }
  }
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
       $width = imagefontwidth(FONT_SIZE);
       $height = imagefontheight(FONT_SIZE);
       $this->wh = array($width*strlen($text), $height);
       $this->text = $text;
       $red  = false;
   }
    
   public function paint(&$im, $palette, $currentrect, $up) {
       parent::paint($im, $palette, $currentrect, $up);
       $color = $palette['black'];
       if ($this->red) {
           $color = $palette['red'];
       }
       $x = $currentrect->x + $currentrect->width/2 - $this->wh[0]/2;
       imagestring($im, FONT_SIZE, $x, $currentrect->y, $this->text, $color);
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
   protected function labelsize() {
       return array(max($this->answer->wh[0],$this->response->wh[0]) ,
                    max($this->answer->wh[1],$this->response->wh[1]));
   }
   public function size() {
       $width = max($this->answer->wh[0],$this->response->wh[0]) + ROW_HORIZONTAL_SPACE;
       $height =  max($this->answer->wh[1],$this->response->wh[1]) * 2 + ANSWER_RESPONSE_VERTICAL_SPACE;
       return array($width, $height);
   }
   
   public function get_answer_connection_point() {
       return $this->answer->connection;
   }
   
   public function get_response_connection_point() {
       return $this->response->connection;
   }
   
   public function paint(&$im, $palette, $currentpos) {
       $labelsize = $this->labelsize();
       $answerrect = (object)array('x' => $currentpos[0], 'y' => $currentpos[1], 
                                   'width' => $labelsize[0] + ROW_HORIZONTAL_SPACE, 'height' => $labelsize[1]);
       $responserect = (object)array('x' => $currentpos[0], 'y' => $currentpos[1] + ANSWER_RESPONSE_VERTICAL_SPACE, 
                                     'width' => $labelsize[0], 'height' => $labelsize[1]);
       $this->answer->paint($im, $palette, $answerrect, true );
       $this->response->paint($im, $palette, $responserect, false );
       
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
     @var  array indexes of cells for answer in $table field 
     */
   private $answertable;
   /**
     @var array indexes of cells for response in $table field 
     */
   private $responsetable;
   /**
     @var array indexes of answer lexemes, put on LCS
     */
   private $lcs;
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
       
       // Compute size of image
       $height = 0;
       $width  = 0;
       foreach($this->table as $entry) {
           $size = $entry->size();
           $width  = $width + $size[0];
           $height = $size[1];
       }
      
       $im = imagecreatetruecolor($width, $height);
       $white = imagecolorallocate($im, 255, 255, 255);
       $black = imagecolorallocate($im, 0, 0, 0);
       $red = imagecolorallocate($im, 255, 0, 0);
       imagefill($im,0,0,$white);
       // Generate image
       $this->generate_image($im, array('black' => $black, 'red' => $red));
       // Output image
       header('Content-type: image/png');
       imagepng($im);
       imagedestroy($im);
   }
   public function generate_image(&$im , $palette) {
       $currentpos = array(0, 0);
       foreach($this->table as $tableentry)
       {
         $size = $tableentry->size();
         $tableentry->paint($im, $palette, $currentpos);
         $currentpos[0] = $currentpos[0] + $size[0];
       }
       
       imagesetthickness($im, LINE_WIDTH);
       // Draw absent lexemes arrows
       foreach($this->absentlexemes as $index) {
           $p1 = $this->get_answer_connection_point($index);
           $p2 = $this->get_answer_response_connection_point($index);
           $this->draw_arrow($im, $palette['red'], $p1, $p2, false);
       }
       // Draw added lexemes arrows
       foreach($this->addedlexemes as $index) {
           $p1 = $this->get_response_answer_connection_point($index);
           $p2 = $this->get_response_connection_point($index);
           $this->draw_arrow($im, $palette['red'], $p1, $p2, true);
       }
       // Draw moved lexemes arrows
       foreach($this->movedlexemes as $entry) {
           $p1 = $this->get_answer_connection_point($entry->answer);
           $p2 = $this->get_response_connection_point($entry->response);
           $this->draw_arrow($im, $palette['red'], $p1, $p2, false);
       }
       // Draw LCS
       foreach($this->lcs as $index) {
           $p1 = $this->get_answer_connection_point($index);
           $p2 = $this->get_answer_response_connection_point($index);
           $this->draw_arrow($im, $palette['black'], $p1, $p2, false);
       }
   }
   protected function draw_arrow(&$im, $color, $p1, $p2, $markbegin) {
       // Draw arrow shaft
       imageline($im, $p1[0], $p1[1], $p2[0], $p2[1], $color);
       // Draw arrow parts
       $angle = atan2($p1[1] - $p2[1],$p1[0] - $p2[0]);
       $point = $p2;
       if ($markbegin == true) {
           //TODO: Uncomment it if we want upward arrows
           //$point = $p1;
           //$angle = atan2($p2[1]-$p1[1],$p2[0]-$p1[0]); 
       }
       
       $pmin = array($point[0] + ARROW_LENGTH * cos($angle + ARROW_ANGLE),$point[1] + ARROW_LENGTH * sin($angle + ARROW_ANGLE));
       $pmax = array($point[0] + ARROW_LENGTH * cos($angle - ARROW_ANGLE),$point[1] + ARROW_LENGTH * sin($angle - ARROW_ANGLE));
       imageline($im, $point[0], $point[1], $pmin[0], $pmin[1], $color);
       imageline($im, $point[0], $point[1], $pmax[0], $pmax[1], $color);
   }
   protected function get_answer_connection_point($answerindex) {
       return $this->table[$this->answertable[$answerindex]]->get_answer_connection_point();
   }
   protected function get_answer_response_connection_point($answerindex) {
       return $this->table[$this->answertable[$answerindex]]->get_response_connection_point();
   }
   protected function get_response_connection_point($responseindex) {
       return $this->table[$this->responsetable[$responseindex]]->get_response_connection_point();
   }
   protected function get_response_answer_connection_point($responseindex) {
       return $this->table[$this->responsetable[$responseindex]]->get_answer_connection_point();
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
       $this->lcs = $lcsanswers;
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
   } else {
       echo 'Error generating image: malformed data';
   }       
} else {
       echo 'Error generating image: no data supplied';
}
?>