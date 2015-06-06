<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Mistake image generator, used in correctwriting to show student error
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
// Defines a width for drawing lines of moving, removing or adding
define('LINE_WIDTH', 2);
// A line width for strikethrought
define('STRIKETHROUGH_LINE_WIDTH', 1);
// A length of arrow end part
define('ARROW_LENGTH', 5);
// An arrow angle in radians
define('ARROW_ANGLE', 0.5);
// Define a space for frame
define('FRAME_SPACE', 5);
// A thickness of drawn frame
define('FRAME_THICKNESS', 1);
// Defines a padding for absent frame
define('ABSENT_FRAME_PADDING', 2);
// Additional length for big strkethrough  inner lines
define('BIG_STRIKETHROUGH_ADDITIONAL_LENGTH', 2);
// Padding for move items
define('MOVE_GROUP_PADDING', 2);
/**
 * This style of require_once is used intentionally, due to non-availability of Moodle here
 */
require_once(dirname(__FILE__) . '/textimagerenderer.php');
/* Defines a simple label, which can be printed on image.
   Must contain size and point for arrow connection, which will be used  to draw arrow to or from this point.
 */
class qtype_correctwriting_abstract_label
{
   /**
     @var array  array(width, height) a requested size of label on screen
    */
   protected $labelsize = array(0,0);
   /**
     @var array  array(x, y) connection point where connection to point can be put to.
     */
   protected $connection = array(0,0);
   /**
    * @var stdClass|null bounding rectangle
    */
   protected $rectangle = null;

    /**
     * Returns a rectangle for data
     * @return null|stdClass
     */
    public function rect() {
       return $this->rectangle;
   }
   /** Returns a connection point for drawing arrows
     @return array  of two coordinates x and y as array(x,y)
     */
   public function get_connection_point() {
       return $this->connection;
   }
   /** Returns a requested size of label for drawing
     @return array of two coordinates width and height as array(width,height)
     */
   public function get_size() {
       return $this->labelsize;
   }
   /** Sets a connection point for drawing an arrow
        @param stdClass $currentrect rectangle, where it should be placed with fields x,y,width, height.
        @param bool  $bottom whether point should placed on bottom part of rectangle, or top
     */
   protected function set_connection_point($currentrect,$bottom) {
       $this->connection = array();
       $this->connection[] = $currentrect->x + $currentrect->width/2;
       // If we must place it on bottom, than place it there (because we are in Decart space).
       if ($bottom == true) {
           $this->connection[] = $currentrect->y+$currentrect->height+TINY_SPACE;
       } else {
           $this->connection[] = $currentrect->y - TINY_SPACE;
       }
       $this->rectangle = $currentrect;
   }
   /** Paints a label at specific position, specified by rectangle, also setting a connection point
       for drawing arrows
       @param resource $im image resource, where it should be painted
       @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
       @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
       @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
     */
   public function paint(&$im, $palette, $currentrect, $bottom) {
       $this->set_connection_point($currentrect, $bottom);
   }
   /**
    * Stub for returnin text
    * @return string
    */
   public function text() {
        return '';
   }
}
/** An empty label is used as a stub, when we are skipping parts in the table in cases,
    when we draw an absent lexemes or added lexemes.
 */
class qtype_correctwriting_empty_label extends qtype_correctwriting_abstract_label
{

}
 /** A label, that prints a lexeme at specified point. Also contains an info about, whether
     it was fixed, which is used, when painting a label
  */
class qtype_correctwriting_lexeme_label extends qtype_correctwriting_abstract_label
{
   /**
    @var string string of text and value of lexeme, which should be painted on image
    */
   protected $text;
   /**
    @var bool  whether this lexeme was fixed. Fixed means, it should be painted red
    */
   protected $fixed;

   /** Constructs new non-fixed lexeme label with specified text
       @param string $text text of lexeme
    */
   public function __construct($text) {
       $bbox = qtype_correctwriting_get_text_bounding_box($text);
       // Set label size according to computes metrics
       $this->labelsize = array($bbox->width, $bbox->height);
       // As a default we assume the lexeme is correct
       $this->text = $text;
       $this->fixed  = false;
   }
   /** Marks a lexeme in this label as fixed. Fixed lexemes a drawn not the same way, it
       draws usually, so this info can be useful
     */
   public function make_fixed() {
       $this->fixed  = true;
   }

   /**
    * Returns text of lexeme label
    * @return string
    */
   public function text() {
       return $this->text;
   }
   /** Paints a label at specific position, specified by rectangle. If it set as fixed, we paint it as red.
       Label is painted at center of specified rectangle on a horizontal, and on top on vertical
       @param resource $im image resource, where it should be painted
       @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
       @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
       @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
     */
   public function paint(&$im, $palette, $currentrect, $bottom) {
       // Set connection point
       parent::paint($im, $palette, $currentrect, $bottom);

       // Set color according to fixed parameter
       $color = $palette['black'];
       if ($this->fixed) {
           $color = $palette['red'];
       }
       // Compute a middle parameter at center
       $x = $currentrect->x + $currentrect->width/2 - $this->labelsize[0]/2;

       // Debug drawing of lexeme rectangles
       /*
       $fx1 = $currentrect->x;
       $fx2 = $currentrect->x + $currentrect->width;
       $fy1 = $currentrect->y;
       $fy2 = $currentrect->y + $currentrect->height;
       $points = array( array($fx1, $fy1), array($fx2, $fy1),
                        array($fx2, $fy2), array($fx1, $fy2), array($fx1, $fy1) );
       for($i = 1; $i < count($points); $i++) {
           imageline($im, $points[$i-1][0], $points[$i-1][1], $points[$i][0], $points[$i][1], $palette['red']);
       }
       */

       // Paint a string
       qtype_correctwriting_render_text($im, $x, $currentrect->y, $this->text, $color);
   }
}
/** A table cell consists of two rows - answer and response. Answer is placed in the top of image,
    response is placed in bottom of image. It can paint itself to image and must return
    connection and coordinates and size
  */
class qtype_correctwriting_table_cell
{
   /**
    * @var qtype_correctwriting_abstract_label answer label part
    */
   private $answer;
   /**
    * @var qtype_correctwriting_abstract_label response label part
    */
   private $response;
   /** Creates a cell with specified answer and response
     @param qtype_correctwriting_abstract_label $answer answer label part
     @param qtype_correctwriting_abstract_label $response response label part
     */
   public function __construct($answer, $response) {
       $this->answer = $answer;
       $this->response = $response;
   }
   /** Returns a maximum size of labels in a cell
       @return array of coordinates as array(width, height)
     */
   protected function get_max_label_size() {
       // Save answer and response size
       $answersize = $this->answer->get_size();
       $responsesize = $this->response->get_size();
       // Returns maximum size of label
       return array(max($answersize[0],$responsesize[0]) ,
                    max($answersize[1],$responsesize[1]));
   }

   /**
    * Returns true if response text length bigger then
    * @param int $length
    * @return bool result
    */
   public function is_response_length_bigger($length) {
       return core_text::strlen($this->response->text()) > $length;
   }
   /** Returns a total size of cell
       @return array of coordinates as array(width, height)
    */
   public function size() {
       // Get sizes of label
       $size = $this->get_max_label_size();
       // Compute size
       $width = $size[0] + ROW_HORIZONTAL_SPACE;
       $height =  $size[1] * 2 + ANSWER_RESPONSE_VERTICAL_SPACE;
       // Return result
       return array($width, $height);
   }
    /** Returns an answer rect
     *  @return stdClass rect
     */
    public function get_answer_rect() {
        return $this->answer->rect();
    }
    /** Returns an response rect
     *  @return stdClass rect
     */
    public function get_response_rect() {
        return $this->response->rect();
    }
    /** Returns a connection point for answer part of cell
       @return array coordinates of point as array(x, y)
    */
   public function get_answer_connection_point() {
       return $this->answer->get_connection_point();
   }
   /** Returns a connection point for response part of cell
       @return array coordinates of point as array(x, y)
    */
   public function get_response_connection_point() {
       return $this->response->get_connection_point();
   }
   /** Draws an answer and response parts on image. Answer part is drawn nearly currentpos coordinates,
       Response part - at bottom of image, starting from this point.
       @param resource $im image resource, where it should be painted
       @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
       @param array    $currentpos upper-left coordinates for drawing a table cell as array(x, y).
     */
   public function paint(&$im, $palette, $currentpos) {
       // Compute max label size part
       $labelsize = $this->get_max_label_size();
       // Compute rectangles for painting answer and response
       $responserect = (object)array('x' => $currentpos[0], 'y' => $currentpos[1] + FRAME_SPACE,
                                   'width' => $labelsize[0], 'height' => $labelsize[1]);
       $height = imagesy($im);
       $answerrect = (object)array('x' => $currentpos[0], 'y' => $height - $labelsize[1] - FRAME_SPACE,
                                     'width' => $labelsize[0], 'height' => $labelsize[1]);
       // Draw an answer and response
       $this->answer->paint($im, $palette, $answerrect, false );
       $this->response->paint($im, $palette, $responserect, true );
   }
}

/**  A special container for mistakes, found in student response
 */
class qtype_correctwriting_mistake_container
{
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
    *  Constructs a container, from other string
    *  @param absentstring string with absent lexeme indexes, separated by ',,,'
    *  @param addedstring string with added lexeme indexes, separated by ',,,'
    *  @param movedstring string with moved lexeme indexes as pattern answer_response, separated by ',,,'
    */
   public function __construct($absentstring, $addedstring, $movedstring) {
       $this->absentlexemes = array_diff(explode(',,,',$absentstring), array(''));
       $this->addedlexemes = array_diff(explode(',,,',$addedstring), array(''));
       $movedlexemes = array_diff(explode(',,,',$movedstring), array(''));
       foreach($movedlexemes as $entry) {
           $tmp = explode('_',$entry);
           $data = new stdClass();
           $data->answer = $tmp[0];
           $data->response = $tmp[1];
           $this->movedlexemes[] = $data;
       }
   }
   /** Checks, whether answer lexeme with specified index was moved or absent in student response
       @param int $index index of specified answer lexeme
    */
   public function is_moved_or_absent_answer($index) {
       return $this->is_in_arrays('absentlexemes','answer',$index);
   }
   /** Checks, whether response lexeme with specified index is a moved answer lexeme
       @param int $index index of specified response lexeme
    */
   public function is_moved_or_added_response($index) {
       return $this->is_in_arrays('addedlexemes','response',$index);
   }

   /** Checks, whether an index lexeme is in some arrays
       @param string  $skippedarrayname  a skipped array name for skipped lexemes. Must be absentlexemes pr addedlexemes
       @param string  $movedfieldname    describes, whether it answer or response. Used as field name for moved lexeme
       @param int     $index             index of analyzed lexeme
    */
   protected function is_in_arrays($skippedarrayname, $movedfieldname, $index) {
       $result = false;
       // Check skipped array
       if(count($this->$skippedarrayname) != 0 ) {
           foreach($this->$skippedarrayname as $testindex) {
               if ($index == $testindex) {
                   $result = true;
               }
           }
       }
       //Check moved array
       if(count($this->movedlexemes) != 0 ) {
           foreach ($this->movedlexemes as $testindex)  {
               if ($index == $testindex->$movedfieldname) {
                   $result = true;
               }
           }
       }
       return $result;
   }

   /** Returns an absent lexeme indexes
     @return array array of absent lexemes
     */
   public function get_absent_lexeme_indexes() {
       return $this->absentlexemes;
   }
   /** Returns an added lexeme indexes
     @return array array of absent lexemes
     */
   public function get_added_lexeme_indexes() {
       return $this->addedlexemes;
   }
   /** Returns a moved lexemes
     @return  array array of indexes of added lexemes as stdClass( 'answer' => answer index, 'response' => response index)
    */
   public function get_moves() {
       return $this->movedlexemes;
   }
}
/**  A container, which contains lcs, found in student answe
 */
class qtype_correctwriting_lcs_extractor
{
   /**
     @var lcs lcs as array of stdClass('answer' => answer index, 'response' => response index)
    */
   private $lcs;
   /** Extracts LCS from mistakes and answer
     @param int $answercount count of lexemes in answer
     @param int $responsecount count of lexemes in response
     @param qtype_correctwriting_mistake_container $mistakes mistake container
    */
   public function __construct($answercount, $responsecount , $mistakes) {
       $answerindex = 0;
       $responseindex = 0;
       $lcsanswers = array();
       $lcsresponses = array();
       // Extract answer part as array
       for($answerindex = 0 ; $answerindex < $answercount ; $answerindex++) {
           if ($mistakes->is_moved_or_absent_answer($answerindex) == false) {
               $lcsanswers[] = $answerindex;
           }
       }

       // Extract response part as array
       for($responseindex = 0; $responseindex < $responsecount ; $responseindex++) {
           if ($mistakes->is_moved_or_added_response($responseindex) == false) {
               $lcsresponses[] = $responseindex;
           }
       }

       // Merge parts
       $this->lcs = array();
       for ($i = 0; $i < count($lcsanswers); $i++)
       {
           $entry = new stdClass();
           $entry->answer = $lcsanswers[$i];
           $entry->response = $lcsresponses[$i];
           $this->lcs[] = $entry;
       }

   }

   public function lcs() {
       return $this->lcs;
   }
}
/** A table, which represens a printed data.  Can be built using LCS and, arrays of lexemes and mistake
    container
*/
class qtype_correctwriting_table
{
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
     @var qtype_correctwriting_lcs_extractor  extractor of lcs, which represents used lcs
     */
   private $lcs;
   /**
     @var qtype_correctwriting_mistake_container container of mistakes, used to build lcs
     */
   private $mistakes;
   /** Builds a new table, using following parameters
       @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
       @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
       @param qtype_correctwriting_lcs_extractor $lcs lcs data for building lcs result
       @param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
     */
   public function __construct($answer, $response, $lcs, $mistakes) {
       $this->lcs = $lcs;

       // Create a labels for table
       $this->mistakes = $mistakes;
       $this->table = array();
       $answerlabel = new qtype_correctwriting_lexeme_label(get_string('imageanswer', 'qtype_correctwriting'));
       $responselabel = new qtype_correctwriting_lexeme_label(get_string('imageresponse', 'qtype_correctwriting'));
       $this->table[] = new qtype_correctwriting_table_cell($answerlabel, $responselabel);

       // Build table
       if (count($lcs->lcs()) != 0) {
           $this->build_table_using_lcs($answer, $response, $lcs->lcs(), $mistakes);
       } else {
           $this->build_table_without_lcs($answer, $response, $mistakes);
       }
   }
   /** Builds a new table, using LCS
       @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
       @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
       @param array of stdClass $lcs lcs data for building lcs result
       @param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
     */
   private function build_table_using_lcs($answer, $response, $lcs, $mistakes) {
       // Add first odd part
       $this->create_cell_range($answer, 0, $lcs[0]->answer, 'create_answer_cell');
       $this->create_cell_range($response, 0, $lcs[0]->response , 'create_response_cell');
       for($i = 0; $i < count($lcs); $i++) {
           // Compute begin part of region
           $answerbegin = $lcs[$i]->answer + 1;
           $responsebegin = $lcs[$i]->response + 1;

           // Add  LCS part
           $this->table[] = new qtype_correctwriting_table_cell($answer[$answerbegin - 1],
                                                                $response[$responsebegin - 1]);
           $lastindex = count($this->table) - 1 ;
           $this->answertable[$answerbegin - 1] = $lastindex;
           $this->responsetable[$responsebegin - 1] = $lastindex;

           // Compute end part of lcs
           $answerend = count($answer);
           $responseend = count($response);
           $notendpart = ($i + 1 < count($lcs));
           if ($notendpart) {
               $answerend = $lcs[$i+1]->answer;
               $responseend = $lcs[$i+1]->response;
           }

           // Add odd parts
           $this->create_cell_range($answer, $answerbegin, $answerend, 'create_answer_cell');
           $this->create_cell_range($response, $responsebegin, $responseend , 'create_response_cell');

       }
   }
   /** Builds a new table, without LCS use
       @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
       @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
       @param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
     */
   private function build_table_without_lcs($answer, $response, $mistakes) {
       $this->create_cell_range($answer, 0, count($answer) , 'create_answer_cell');
       $this->create_cell_range($response, 0, count($response) , 'create_response_cell');
   }
   /** Returns an extractor, for which it was build
       @return qtype_correctwriting_lcs_extractor used extractor
     */
   public function lcs_extractor() {
       return $this->lcs;
   }
   /** Returns a mistake container, for which it was build
       @return qtype_correctwriting_mistake_container a mistake container
     */
   public function mistakes() {
       return $this->mistakes;
   }
   /** Returns a connection point for lexeme in student answer
       @param int $answerindex index of lexeme in answer
       @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   public function get_connections_by_answer_index($answerindex) {
       //Get current cell
       $cell = $this->table[$this->answertable[$answerindex]];
       return $this->get_connections_from_cell($cell);
   }
    /** Returns a connection point for lexeme in student answer
        @param int $answerindex index of lexeme in answer
        @return stdClass bounding rectangle
     */
   public function get_rect_by_answer_index($answerindex) {
       $cell = $this->table[$this->answertable[$answerindex]];
       return $cell->get_answer_rect();
   }
   /** Returns a connection point for lexeme in student response
       @param int $responseindex index of lexeme in responses
       @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   public function get_connections_by_response_index($responseindex) {
        //Get current cell
       $cell = $this->table[$this->responsetable[$responseindex]];
       return $this->get_connections_from_cell($cell);
   }
   /** Returns a connection point for lexeme in student answer
     @param int $answerindex index of lexeme in answer
     @return stdClass bounding rectangle
    */
   public function get_rect_by_response_index($responseindex) {
       $cell = $this->table[$this->responsetable[$responseindex]];
       return $cell->get_response_rect();
   }
    /** Returns a connection point for lexeme in student answer
     * @param int $responseindex of lexeme in answer
     * @param int $length supposed length of lexeme
     * @return bool
     */
    public function is_response_text_bigger_than($responseindex,$length) {
        /**
         * @var qtype_correctwriting_table_cell $cell
         */
        $cell = $this->table[$this->responsetable[$responseindex]];
        return $cell->is_response_length_bigger($length);
    }
   /** Returns a connection for arrow for cell $cell
       @param qtype_correctwriting_table_cell $cell cell, which connection point is extracted
       @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   protected function get_connections_from_cell($cell) {
       $a = new stdClass();
       $a->answer = $cell->get_answer_connection_point();
       $a->response = $cell->get_response_connection_point();
       return $a;
   }
   /** Creates a cell with only answer part filled.
       @param  array $answer answer array of labels
       @param  int   $answerindex index of lexeme, which is added
    */
   protected function create_answer_cell($answer, $answerindex) {
       $entry = new qtype_correctwriting_empty_label();
       $this->table[] = new qtype_correctwriting_table_cell($answer[$answerindex], $entry);
       $this->answertable[$answerindex] = count($this->table) - 1 ;
   }
   /** Creates a cell with only response part filled.
       @param  array $response response array of labels
       @param  int   $responseindex index of lexeme, which is added
    */
   protected function create_response_cell($response, $responseindex) {
       $entry = new qtype_correctwriting_empty_label();
       $this->table[] = new qtype_correctwriting_table_cell($entry, $response[$responseindex]);
       $this->responsetable[$responseindex] = count($this->table) - 1 ;
   }
   /** Creates a cell range with cells of specified type
       @param array $labelarray array of labels, for which is created range
       @param int   $begin starting index of $labelarray, for which is range created
       @param int   $end  ending index of $labelarray, which is not included into range
       @param string $method creation method for range. Must be either of 'create_answer_cell', 'create_response_cell'
     */
   protected function create_cell_range ($labelarray, $begin, $end , $method) {
       $i = $begin;
       while($i < $end) {
           $this->$method($labelarray, $i);
            $i++;
       }
   }
   /** Returns a total size of table
       @return array size of table as array(width,height)
     */
   public function get_size() {
       $height = 0;
       $width  = 0;
       if (count($this->table) != 0) {
           foreach($this->table as $entry) {
               $size = $entry->size();
               $width  = $width + $size[0];
               $height = $size[1];
           }
       }
       return array($width, $height);
   }
   /** Draws a table
       @param resource $im image
       @param array $palette associative array of colors
     */
   public function paint(&$im , $palette) {
       $currentpos = array(FRAME_SPACE, 0);
       if (count($this->table) != 0) {
           foreach($this->table as $tableentry) {
               $size = $tableentry->size();
               $tableentry->paint($im, $palette, $currentpos);
               $currentpos[0] = $currentpos[0] + $size[0];
           }
       }
   }
}
/** A class, that builds an arrows of tables. Used to express some errors and LCS data
  */
class qtype_correctwriting_arrow_builder {
   /**
    * @var qtype_correctwriting_table built table of lexemes
    */
   private $table;
   /** Creates a new builder with associated data
    * @param qtype_correctwriting_table $table built table of lexemes
    */
   public function __construct($table) {
       $this->table = $table;
   }

    public function merge_rects($rects) {
        $x = 10000000000000;
        $y = 10000000000000;
        $maxx = 0;
        $maxy = 0;
        foreach($rects as $rect) {
            $x = min($x, $rect->x);
            $y = min($y, $rect->y);
            $maxx = max($maxx, $rect->x + $rect->width);
            $maxy = max($maxy, $rect->y + $rect->height);
        }
        $r = new stdClass();
        $r->x = $x;
        $r->y = $y;
        $r->width = $maxx - $x;
        $r->height = $maxy - $y;
        return $r;
    }

   /** Draws an arrows  for mistakes
       @param resource $im image
       @param array $palette associative array of colors
     */
   public function paint(&$im, $palette) {
       global $_REQUEST;

       $groupmovements = false;
       if (array_key_exists('group', $_REQUEST)) {
           $groupmovements = intval($_REQUEST['group']) > 0;
       }

       // Set thickness
       imagesetthickness($im, LINE_WIDTH);
       // Draw absent lexemes arrows
       if (count($this->table->mistakes()->get_absent_lexeme_indexes()) != 0) {
           if ($groupmovements) {
               $groups = array();
               $indexes = $this->table->mistakes()->get_absent_lexeme_indexes();
               foreach($indexes as $index) {
                    if (count($groups) == 0) {
                        $groups[] = array( $index );
                    } else {
                        $lastgroup = $groups[count($groups) - 1];
                        $lastindex = $lastgroup[count($lastgroup) - 1];
                        if ($index == $lastindex + 1) {
                            $groups[count($groups) - 1][] = $index;
                        } else {
                            $groups[] = array( $index );
                        }
                    }
               }

               foreach($groups as $group) {
                   $rects = array();
                   foreach($group as $index) {
                       $rect = $this->table->get_rect_by_answer_index($index);
                       $rects[] = $rect;
                   }
                   $rect = $this->merge_rects($rects);
                   $this->draw_rectangle($im, $palette['red'], $rect);
               }

           } else {
               foreach($this->table->mistakes()->get_absent_lexeme_indexes() as $index) {
                   $rect = $this->table->get_rect_by_answer_index($index);
                   $this->draw_rectangle($im, $palette['red'], $rect);
               }
           }
       }
       imagesetthickness($im, STRIKETHROUGH_LINE_WIDTH);
       // Draw added lexemes arrows
       if (count($this->table->mistakes()->get_added_lexeme_indexes()) != 0 ) {
           if ($groupmovements) {
               $groups = array();
               $indexes = $this->table->mistakes()->get_added_lexeme_indexes();
               foreach($indexes as $index) {
                   if (count($groups) == 0) {
                       $groups[] = array( $index );
                   } else {
                       $lastgroup = $groups[count($groups) - 1];
                       $lastindex = $lastgroup[count($lastgroup) - 1];
                       if ($index == $lastindex + 1) {
                           $groups[count($groups) - 1][] = $index;
                       } else {
                           $groups[] = array( $index );
                       }
                   }
               }

               foreach($groups as $group) {
                   if (count($group) == 1) {
                       // Perform common root if group consists of one token
                       $index = $group[0];
                       $rect = $this->table->get_rect_by_response_index($index);
                       if ($this->table->is_response_text_bigger_than($index, 1)) {
                           $this->draw_big_strikethrough($im, $palette['red'], $rect);
                       } else {
                           $this->draw_strikethrough($im, $palette['red'], $rect);
                       }
                   } else {
                       $rects = array();
                       foreach($group as $index) {
                           $rect = $this->table->get_rect_by_response_index($index);
                           $rects[] = $rect;
                       }
                       $rect = $this->merge_rects($rects);
                       $miny = $rect->y + $rect->height / 2;
                       $maxy = $miny + 2 * BIG_STRIKETHROUGH_ADDITIONAL_LENGTH;
                       $miny -= 2 * BIG_STRIKETHROUGH_ADDITIONAL_LENGTH;
                       imageline($im, $rect->x, $miny, $rect->x + $rect->width, $maxy, $palette['red']);
                       imageline($im, $rect->x, $maxy, $rect->x + $rect->width, $miny, $palette['red']);
                   }
               }
           } else {
               foreach($this->table->mistakes()->get_added_lexeme_indexes() as $index) {
                   $rect = $this->table->get_rect_by_response_index($index);
                   if ($this->table->is_response_text_bigger_than($index, 1)) {
                       $this->draw_big_strikethrough($im, $palette['red'], $rect);
                   } else {
                       $this->draw_strikethrough($im, $palette['red'], $rect);
                   }
               }
           }
       }
       imagesetthickness($im, LINE_WIDTH);
       // Draw moved lexemes arrows
       if (count($this->table->mistakes()->get_moves()) != 0) {
           if ($groupmovements) {
               $moves = $this->table->mistakes()->get_moves();
               $groups = array();
               foreach($moves as $entry) {
                   if (count($groups) == 0) {
                       $groups[] = array( $entry );
                   } else {
                       $lastgroup = $groups[count($groups) - 1];
                       $lastentry = $lastgroup[count($lastgroup) - 1];
                       if ($entry->answer == $lastentry->answer + 1 && $entry->response == $lastentry->response + 1) {
                           $groups[count($groups) - 1][] = $entry;
                       } else {
                           $groups[] = array( $entry );
                       }
                   }
               }

               foreach($groups as $group) {
                    if (count($group) == 1) {
                        $p1 = $this->table->get_connections_by_answer_index($group[0]->answer)->answer;
                        $p2 = $this->table->get_connections_by_response_index($group[0]->response)->response;
                        $this->draw_arrow($im, $palette['red'], $p2, $p1, false);
                    }   else {
                        $answerrects = array();
                        $responserects = array();
                        foreach($group as $entry) {
                            $responserects[] = $this->table->get_rect_by_response_index($entry->response);
                            $answerrects[] =  $this->table->get_rect_by_answer_index($entry->answer);
                        }

                        $answerrect = $this->merge_rects($answerrects);
                        $responserect = $this->merge_rects($responserects);

                        $connectionpointresponse = array();
                        $connectionpointresponse[0] = $responserect->x + $responserect->width / 2;
                        $connectionpointresponse[1] = $responserect->y + $responserect->height + MOVE_GROUP_PADDING; // 2 is a slight padding


                        $connectionpointanswer = array();
                        $connectionpointanswer[0] = $answerrect->x + $answerrect->width / 2;
                        $connectionpointanswer[1] = $answerrect->y - MOVE_GROUP_PADDING; // 2 is a slight padding

                        $this->draw_arrow($im, $palette['red'], $connectionpointresponse, $connectionpointanswer, false);

                        $y = $answerrect->y - MOVE_GROUP_PADDING / 2;
                        imageline($im, $answerrect->x - MOVE_GROUP_PADDING, $y, $answerrect->x + $answerrect->width + MOVE_GROUP_PADDING,  $y,  $palette['red']);
                        $x = $answerrect->x - MOVE_GROUP_PADDING;
                        imageline($im, $x, $y - MOVE_GROUP_PADDING / 2, $x,  $y + MOVE_GROUP_PADDING,  $palette['red']);
                        $x = $answerrect->x + $answerrect->width + MOVE_GROUP_PADDING;
                        imageline($im, $x, $y - MOVE_GROUP_PADDING / 2, $x,  $y + MOVE_GROUP_PADDING,  $palette['red']);

                        $y = $responserect->y + $responserect->height + MOVE_GROUP_PADDING / 2;
                        $miny = $responserect->y + $responserect->height - MOVE_GROUP_PADDING;
                        $minx = $responserect->x - MOVE_GROUP_PADDING;
                        $maxx = $responserect->x + $responserect->width + MOVE_GROUP_PADDING;
                        imageline($im, $minx, $y, $maxx,  $y,  $palette['red']);
                        imageline($im, $minx, $y, $minx,  $miny,  $palette['red']);
                        imageline($im, $maxx, $y, $maxx,  $miny,  $palette['red']);
                    }
               }
           } else {
               foreach($this->table->mistakes()->get_moves() as $entry) {
                   $p1 = $this->table->get_connections_by_answer_index($entry->answer)->answer;
                   $p2 = $this->table->get_connections_by_response_index($entry->response)->response;
                   $this->draw_arrow($im, $palette['red'], $p2, $p1, false);
               }
           }
       }
       // Draw LCS
       if (count($this->table->lcs_extractor()->lcs()) != 0) {
           foreach($this->table->lcs_extractor()->lcs() as $entry) {
               $p1 = $this->table->get_connections_by_answer_index($entry->answer)->answer;
               $p2 = $this->table->get_connections_by_response_index($entry->response)->response;
               $p2[0] = $p1[0];
               $this->draw_arrow($im, $palette['black'], $p2, $p1, false);
           }
       }
   }
   /**
    * Draws a strikethrough line in specified rect
    * @param resource $im image
    * @param int      $color  color
    * @param stdClass $rect   rectangle
    */
   protected function draw_strikethrough(&$im, $color, $rect) {
       $p1x = $rect->x;
       $py = $rect->y + $rect->height * 0.50;
       $p2x = $rect->x + $rect->width;
       imageline($im, $p1x, $py, $p2x, $py, $color);
   }

    /**
     * Draws a big strikethrough line (for lexemes, which contains more lexemes that 1 and is odd in response)
     * @param resource $im image
     * @param int      $color  color
     * @param stdClass $rect   rectangle
     */
   protected function draw_big_strikethrough(&$im, $color, $rect) {
       $centerx = $rect->x + $rect->width / 2;
       $centery = $rect->y + $rect->height / 2;
       $hheight = $rect->height / 2 + BIG_STRIKETHROUGH_ADDITIONAL_LENGTH;
       $points = array(
            array($centerx - $hheight, $centery - $hheight),
            array($centerx - $hheight, $centery + $hheight),
            array($centerx + $hheight, $centery + $hheight),
            array($centerx + $hheight, $centery - $hheight),
       );
       for($i = 0; $i < count($points); $i++) {
           imageline($im, $centerx, $centery, $points[$i][0], $points[$i][1], $color);
       }

   }
   /**
    * Draws a rectangle with specified color
    * @param resource $im image
    * @param int      $color  color
    * @param stdClass $rect   rectangle
    */
   protected function draw_rectangle(&$im, $color, $rect) {
       $points = array(  array(-1 * ABSENT_FRAME_PADDING, -1 * ABSENT_FRAME_PADDING),
                         array(-1 * ABSENT_FRAME_PADDING, $rect->height + ABSENT_FRAME_PADDING),
                         array($rect->width + ABSENT_FRAME_PADDING, $rect->height + ABSENT_FRAME_PADDING),
                         array($rect->width + ABSENT_FRAME_PADDING, -1 * ABSENT_FRAME_PADDING)
                      );
       $lines = array( array($points[0], $points[1]),
                       array($points[1], $points[2]),
                       array($points[2], $points[3]),
                       array($points[3], $points[0])
                     );
       for($i = 0;$i < count($lines); $i++) {
           $line = $lines[$i];
           $p1x = $rect->x + $line[0][0];
           $p1y = $rect->y + $line[0][1];
           $p2x = $rect->x + $line[1][0];
           $p2y = $rect->y + $line[1][1];
           imageline($im, $p1x, $p1y, $p2x, $p2y, $color);
       }
   }
   /** Draws a directed arrow
       @param resource $im image
       @param int      $color color, which it should be painted by
       @param array    $p1   first point as array(x, y)
       @param array    $p2   second point as array(x, y)
       @param bool     $markbegin whether arrow tail should be at p1, otherwise at p2
     */
   protected function draw_arrow(&$im, $color, $p1, $p2, $markbegin) {
       // Draw arrow shaft
       imageline($im, $p1[0], $p1[1], $p2[0], $p2[1], $color);
       // Draw arrow parts
       $angle = atan2($p1[1] - $p2[1],$p1[0] - $p2[0]);
       $point = $p2;
       if ($markbegin == true) {
           $point = $p1;
           $angle = atan2($p2[1]-$p1[1],$p2[0]-$p1[0]);
       }
       // Draw tail
       $pmin = array($point[0] + ARROW_LENGTH * cos($angle + ARROW_ANGLE),$point[1] + ARROW_LENGTH * sin($angle + ARROW_ANGLE));
       $pmax = array($point[0] + ARROW_LENGTH * cos($angle - ARROW_ANGLE),$point[1] + ARROW_LENGTH * sin($angle - ARROW_ANGLE));
       imageline($im, $point[0], $point[1], $pmin[0], $pmin[1], $color);
       imageline($im, $point[0], $point[1], $pmax[0], $pmax[1], $color);
   }
}

/** Main class, which should be used to create and output image
 */
class qtype_correctwriting_image_generator
{
   /**
     @var qtype_correctwriting_table built table of lexemes
    */
   private $table;

   /** Constructs a generator, scanning sections
     @param array $sections used sections, passed to a script
    */
   public function __construct($sections) {
       // Preprocess answers
       $answer = array();
       $base64answers = explode(',,,',$sections[0]);
       if (count($base64answers) != 0) {
           foreach($base64answers as $tmpanswer) {
               $answer[] = new qtype_correctwriting_lexeme_label(base64_decode($tmpanswer));
           }
       }
       // Preprocess responses
       $response = array();
       $base64responses = explode(',,,',$sections[1]);
       if (count($base64responses) != 0) {
           foreach($base64responses as $tmpresponse) {
               $response[] = new qtype_correctwriting_lexeme_label(base64_decode($tmpresponse));
           }
       }
       // Mark fixed lexemes
       $fixedlexemeindexes = array_diff(explode(',,,',$sections[2]), array(''));
       if (count($fixedlexemeindexes) != 0 ) {
           foreach($fixedlexemeindexes as $index) {
               $response[$index]->make_fixed();
           }
       }
       // Create a table
       $mistakes = new qtype_correctwriting_mistake_container($sections[3], $sections[4], $sections[5]);
       $lcs = new qtype_correctwriting_lcs_extractor(count($answer), count($response), $mistakes);
       $this->table  = new qtype_correctwriting_table($answer, $response, $lcs, $mistakes);
   }
   /*! Produces an image performing all painting actions and sending it to buffer
    */
   public function produce_image() {
       // Align labels in a rows, without a links between them
       $size = $this->table->get_size();

       // Create image
       $sizex = $size[0] + 2 * FRAME_SPACE;
       $sizey = $size[1] + 2 * FRAME_SPACE;
       $im = imagecreatetruecolor($sizex, $sizey);

       // Fill palette
       $palette = array();
       $palette['white'] = imagecolorallocate($im, 255, 255, 255);
       $palette['black'] = imagecolorallocate($im, 0, 0, 0);
       $palette['red']   = imagecolorallocate($im, 255, 0, 0);

       // Set image background to white
       imagefill($im,0,0,$palette['white']);

       // Draw a rectangle frame
       imagesetthickness($im, FRAME_THICKNESS);
       imageline($im, 0, 0, $sizex - 1, 0, $palette['black']);
       imageline($im, $sizex - 1, 0, $sizex - 1, $sizey - 1, $palette['black']);
       imageline($im, $sizex - 1, $sizey - 1, 0, $sizey - 1, $palette['black']);
       imageline($im, 0, $sizey - 1, 0, 0, $palette['black']);


       // Draw a table
       $this->table->paint($im, $palette);
       // Generate image
       $builder = new qtype_correctwriting_arrow_builder($this->table);
       $builder->paint($im, $palette);
       // Output image
       header('Content-type: image/png');
       imagepng($im);
       imagedestroy($im);
   }

}

// Scan parameter and check, whether it's malformed
if (strlen($_GET['data']) != 0) {

   // Decode passed parameter and split it to sections
   $data = base64_decode($_GET['data']);
   $sections = explode(';;;',$data);


   // If amount of section is six, we can build image
   if (count($sections) == 6 ) {

       // Create a generator, which take all staff
       $generator = new qtype_correctwriting_image_generator($sections);
       $generator->produce_image();
   } else {

       // TODO: Change it to more appropriate. We do not perform localization,
       // since we can't access Moodle from here
       echo 'Error generating image: malformed data';
   }
} else {

       // If no data supplied, we can't build image.
       // This output is simple response, which can be changed later
       echo 'Error generating image: no data supplied';
}
?>