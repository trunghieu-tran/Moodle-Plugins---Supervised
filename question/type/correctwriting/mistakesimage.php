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
/**
 * This style of require_once is used intentionally, due to non-availability of Moodle here
 */
require_once(dirname(__FILE__) . '/textimagerenderer.php');
require_once(dirname(__FILE__) . '/classes/mistakesimage/defines.php');
require_once(dirname(__FILE__) . '/classes/mistakesimage/abstractlabel.php');
require_once(dirname(__FILE__) . '/classes/mistakesimage/emptylabel.php');
require_once(dirname(__FILE__) . '/classes/mistakesimage/lexemelabel.php');
require_once(dirname(__FILE__) . '/classes/mistakesimage/imageblock.php');

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
    * @param qtype_correctwriting_abstract_label $answer answer label part
    * @param qtype_correctwriting_abstract_label $response response label part
    */
   public function __construct($answer, $response) {
       $this->answer = $answer;
       $this->response = $response;
   }
   /**
    * Returns answer label
    * @return qtype_correctwriting_abstract_label
    */
   public function answer() {
       return $this->answer;
   }
   /**
    * Returns answer label
    * @return qtype_correctwriting_abstract_label
    */
    public function response() {
       return $this->response;
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
    *  Response part - at bottom of image, starting from this point.
    *  @param resource $im image resource, where it should be painted
    *  @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
    *  @param array    $currentpos upper-left coordinates for drawing a table cell as array(x, y).
    */
   public function paint(&$im, $palette, $currentpos) {
       // Compute max label size part
       $labelsize = $this->get_max_label_size();
       $answersize = $this->answer()->get_size();
       $responsesize = $this->response()->get_size();
       // Compute rectangles for painting answer and response
       $responserect = (object)array('x' => $currentpos[0], 'y' => $currentpos[1] + FRAME_SPACE,
                                   'width' => $labelsize[0], 'height' => $responsesize[1]);
       $height = imagesy($im);
       $answerrect = (object)array('x' => $currentpos[0], 'y' => $height - $answersize[1] - FRAME_SPACE,
                                     'width' => $labelsize[0], 'height' => $answersize[1]);
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
    *  @param array absentstring with absent lexeme indexes, separated by ',,,'
    *  @param array addedstring with added lexeme indexes, separated by ',,,'
    *  @param array movedstring with moved lexeme indexes as pattern answer_response, separated by ',,,'
    */
   public function __construct($absentstring, $addedstring, $movedstring) {
       $this->absentlexemes = $absentstring;
       $this->addedlexemes = $addedstring;
       $movedlexemes = $movedstring;
       foreach($movedlexemes as $entry) {
           $data = new stdClass();
           $data->answer = $entry[0];
           $data->response = $entry[1];
           $this->movedlexemes[] = $data;
       }
   }
   /** Checks, whether answer lexeme with specified index was moved or absent in student response
    *  @param int $index index of specified answer lexeme
    *  @return bool
    */
   public function is_moved_or_absent_answer($index) {
       return $this->is_in_arrays('absentlexemes','answer',$index);
   }
   /** Checks, whether response lexeme with specified index is a moved answer lexeme
    *  @param int $index index of specified response lexeme
    *  @return bool
    */
   public function is_moved_or_added_response($index) {
       return $this->is_in_arrays('addedlexemes','response',$index);
   }

   /** Checks, whether an index lexeme is in some arrays
    *  @param string  $skippedarrayname  a skipped array name for skipped lexemes. Must be absentlexemes pr addedlexemes
    *  @param string  $movedfieldname    describes, whether it answer or response. Used as field name for moved lexeme
    *  @param int     $index             index of analyzed lexeme
    *  @return bool
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
    * @param qtype_correctwriting_string_pair $pair
    */
   public function __construct($pair) {
       // Merge parts
       $this->lcs = array();

       $lcs = $pair->lcs();
       if (count($lcs)) {
           foreach ($lcs as $answerkey => $responsekey) {
               $entry = new stdClass();
               $entry->answer = $answerkey;
               $entry->response = $pair->map_from_corrected_string_to_compared_string($responsekey);
               $entry->error = false;
               $this->lcs[] = $entry;
           }
       }
   }

   public function set_lcs($lcs) {
       $this->lcs = $lcs;
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
    *  @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
    *  @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
    *  @param qtype_correctwriting_lcs_extractor $lcs lcs data for building lcs result
    *  @param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
    *  @param bool $usesimplealignment whether we should simply store answers, without aligning them
    */
   public function __construct($answer, $response, $lcs, $mistakes, $usesimplealignment = false) {
       $this->lcs = $lcs;

       // Create a labels for table
       $this->mistakes = $mistakes;
       $this->table = array();
       $answerlabel = new qtype_correctwriting_lexeme_label(get_string('imageanswer', 'qtype_correctwriting'));
       $responselabel = new qtype_correctwriting_lexeme_label(get_string('imageresponse', 'qtype_correctwriting'));
       $this->table[] = new qtype_correctwriting_table_cell($answerlabel, $responselabel);

       // Build table
       if ($usesimplealignment) {
            $this->build_simple_table($answer, $response);
       } else {
           if (count($lcs->lcs()) != 0) {
               $this->build_table_using_lcs($answer, $response, $lcs->lcs(), $mistakes);
           } else {
               $this->build_table_without_lcs($answer, $response, $mistakes);
           }
       }
       $maxbaselineanswer = 0;
       $maxbaselineresponse = 0;

       foreach ($this->table as $cell) {
           /** @var qtype_correctwriting_table_cell $cell */
           $cell->answer()->get_size();
           $cell->response()->get_size();
           $maxbaselineanswer = max($maxbaselineanswer, $cell->answer()->get_baseline_offset());
           $maxbaselineresponse = max($maxbaselineresponse, $cell->response()->get_baseline_offset());
       }
       foreach ($this->table as $cell) {
           /** @var qtype_correctwriting_table_cell $cell */
           $cell->answer()->set_baseline_offset($maxbaselineanswer);
           $cell->response()->set_baseline_offset($maxbaselineresponse);
       }
   }

    /**
     * Builds simple table, without any alignment
     * @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
     * @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
     */
    private function build_simple_table($answer, $response) {
        $min = min(count($answer), count($response));
        for($i = 0; $i < $min; $i++) {
            $this->table[] = new qtype_correctwriting_table_cell(
                $answer[$i],
                $response[$i]
            );
            $lastindex = count($this->table) - 1 ;
            $this->answertable[$i] = $lastindex;
            $this->responsetable[$i] = $lastindex;
        }
        if (count($answer) > $min) {
            $this->create_cell_range($answer, $min, count($answer), 'create_answer_cell');
        }
        if (count($response) > $min) {
            $this->create_cell_range($response, $min, count($response), 'create_response_cell');
        }
    }
    /** Builds a new table, using LCS
     * @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
     * @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
     * @param array of stdClass $lcs lcs data for building lcs result
     * @internal param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
     */
   private function build_table_using_lcs($answer, $response, $lcs) {
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
     * @param array $answer array of qtype_correctwriting_lexeme_label, representing an answer part of question
     * @param array $response array of qtype_correctwriting_lexeme_label, representing a student response
     * @internal param qtype_correctwriting_mistake_container $mistakes a mistake container, used to maintain mistakes
     */
   private function build_table_without_lcs($answer, $response) {
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
    *  @param int $answerindex index of lexeme in answer
    *  @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   public function get_connections_by_answer_index($answerindex) {
       //Get current cell
       $cell = $this->table[$this->answertable[$answerindex]];
       return $this->get_connections_from_cell($cell);
   }
    /** Returns a rectangle for lexeme in student answer
     *  @param int $answerindex index of lexeme in answer
     *  @return stdClass bounding rectangle
     */
   public function get_rect_by_answer_index($answerindex) {
       /** @var qtype_correctwriting_table_cell $cell */
       $cell = $this->table[$this->answertable[$answerindex]];
       return $cell->get_answer_rect();
   }

    /** Returns a label rectangle for lexeme in teacher's answer
     * @param int $answerindex
     * @return stdClass bounding rectangle
     * @throws Exception if not a label
     */
   public function get_real_rect_for_answer_index($answerindex) {
       /** @var qtype_correctwriting_table_cell $cell */
       $cell = $this->table[$this->answertable[$answerindex]];
       $rect = $cell->get_answer_rect();
       $label = $cell->answer();
       /** @var qtype_correctwriting_lexeme_label $label */
       if (!is_a($label, 'qtype_correctwriting_lexeme_label')) {
           throw new Exception("Not a qtype_correctwriting_lexeme_label");
       }
       return $label->get_label_rect($rect);
   }
   /** Returns a connection point for lexeme in student response
    *  @param int $responseindex index of lexeme in responses
    *  @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   public function get_connections_by_response_index($responseindex) {
        //Get current cell
       $cell = $this->table[$this->responsetable[$responseindex]];
       return $this->get_connections_from_cell($cell);
   }
   /** Returns a connection point for lexeme in student answer
    *  @param int $responseindex index of lexeme in response
    *  @return stdClass bounding rectangle
    */
   public function get_rect_by_response_index($responseindex) {
       /** @var qtype_correctwriting_table_cell $cell */
       $cell = $this->table[$this->responsetable[$responseindex]];
       return $cell->get_response_rect();
   }

    /** Returns a label rectangle for lexeme in student's response
     * @param int $responseindex
     * @return stdClass bounding rectangle
     * @throws Exception if not a label
     */
    public function get_real_rect_for_response_index($responseindex) {
        /** @var qtype_correctwriting_table_cell $cell */
        $cell = $this->table[$this->responsetable[$responseindex]];
        $rect = $cell->get_response_rect();
        $label = $cell->response();
        /** @var qtype_correctwriting_lexeme_label $label */
        if (!is_a($label, 'qtype_correctwriting_lexeme_label')) {
            throw new Exception("Not a qtype_correctwriting_lexeme_label");
        }
        return $label->get_label_rect($rect);
    }

    /**
     * Index
     * @param int $responseindex
     * @return qtype_correctwriting_table_cell
     */
   public function get_cell_by_response_index($responseindex) {
        /** @var qtype_correctwriting_table_cell $cell */
        $cell = $this->table[$this->responsetable[$responseindex]];
        return $cell;
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
    *  @param qtype_correctwriting_table_cell $cell cell, which connection point is extracted
    *  @return stdClass( answer => coordinate of answer connection point as array(x,y), response => coordinate of response)
    */
   protected function get_connections_from_cell($cell) {
       $a = new stdClass();
       $a->answer = $cell->get_answer_connection_point();
       $a->response = $cell->get_response_connection_point();
       return $a;
   }
   /** Creates a cell with only answer part filled.
    *  @param  array $answer answer array of labels
    *  @param  int   $answerindex index of lexeme, which is added
    */
   protected function create_answer_cell($answer, $answerindex) {
       $entry = new qtype_correctwriting_empty_label();
       $this->table[] = new qtype_correctwriting_table_cell($answer[$answerindex], $entry);
       $this->answertable[$answerindex] = count($this->table) - 1 ;
   }
   /** Creates a cell with only response part filled.
    *  @param  array $response response array of labels
    *  @param  int   $responseindex index of lexeme, which is added
    */
   protected function create_response_cell($response, $responseindex) {
       $entry = new qtype_correctwriting_empty_label();
       /*
       if (array_key_exists($responseindex, $response) == false) {
           echo "<pre>";
           var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5));
           echo "</pre>";
           die();
       }
       */
       $this->table[] = new qtype_correctwriting_table_cell($entry, $response[$responseindex]);
       $this->responsetable[$responseindex] = count($this->table) - 1 ;
   }
   /** Creates a cell range with cells of specified type
    *   @param array $labelarray array of labels, for which is created range
    *   @param int   $begin starting index of $labelarray, for which is range created
    *   @param int   $end  ending index of $labelarray, which is not included into range
    *   @param string $method creation method for range. Must be either of 'create_answer_cell', 'create_response_cell'
    */
   protected function create_cell_range ($labelarray, $begin, $end , $method) {
       $i = $begin;
       /*
       if (is_array($begin)) {
           echo "<pre>";
           echo "Begin: ";
           var_dump($begin);
           echo "</pre>";
       }
       if (is_array($end)) {
           echo "<pre>";
           echo "End: ";
           var_dump($end);
           echo "</pre>";
       }
       echo $method . ' ';
       echo $begin . ' ' . $end . '<br />';
       */
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
           /** @var qtype_correctwriting_table_cell $entry */
           foreach($this->table as $entry) {
               $size = $entry->size();
               $width  = $width + $size[0];
               $height = $size[1];
           }
       }
       return array($width, $height);
   }
   /** Draws a table
    *  @param resource $im image
    *  @param array $palette associative array of colors
    */
   public function paint(&$im , $palette) {
       $currentpos = array(FRAME_SPACE, 0);
       if (count($this->table) != 0) {
           foreach($this->table as $tableentry) {
               /** @var qtype_correctwriting_table_cell $tableentry */
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
   /**
    * @var qtype_correctwriting_question question
    */
   private $question;
   /**
    * @var $pair
    */
   private $pair;
   /** Creates a new builder with associated data
    * @param qtype_correctwriting_table $table built table of lexemes
    * @param qtype_correctwriting_question question
    */
   public function __construct($table, $question) {
       $this->table = $table;
       $this->question = $question;
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
    *  @param resource $im image
    *  @param array $palette associative array of colors
    */
   public function paint(&$im, $palette) {
       global $_REQUEST;

       $groupmovements = false;
       if (array_key_exists('group', $_REQUEST)) {
           $groupmovements = intval($_REQUEST['group']) > 0;
       }

       $simplealignment = $this->question->issequenceanalyzerenabled <= 0
                       || $this->question->issequenceanalyzerenabled == false;

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
                   $rect = null;
                   if ($simplealignment) {
                       /** @var qtype_correctwriting_table_cell $cell */
                       $rect = $this->table->get_real_rect_for_answer_index($index);
                       $rect->x -= TINY_SPACE;
                       $rect->width += 2 * TINY_SPACE;
                       $rect->y -= TINY_SPACE;
                       $rect->height += TINY_SPACE;
                   } else {
                       $rect = $this->table->get_rect_by_answer_index($index);
                   }
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
                           $baselineoffset = $this->table->get_cell_by_response_index($index)->response()->get_baseline_offset();
                           $rect->y += $baselineoffset;
                           $rect->height -= $baselineoffset;
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
                   if ($simplealignment) {
                       $rect = $this->table->get_real_rect_for_response_index($index);
                   }
                   if ($this->table->is_response_text_bigger_than($index, 1)) {
                       $baselineoffset = $this->table->get_cell_by_response_index($index)->response()->get_baseline_offset();
                       $rect->y += $baselineoffset;
                       $rect->height -= $baselineoffset;
                       $this->draw_big_strikethrough($im, $palette['red'], $rect);
                   } else {
                       if ($simplealignment) {
                           $oldy = $rect->y;
                           $rect->y = $rect->baseliney;
                           $rect->height = $rect->baseliney - $oldy;
                       }
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
               foreach ($moves as $entry) {
                   if (count($groups) == 0) {
                       $groups[] = array($entry);
                   } else {
                       $lastgroup = $groups[count($groups) - 1];
                       $lastentry = $lastgroup[count($lastgroup) - 1];
                       if ($entry->answer == $lastentry->answer + 1 && $entry->response == $lastentry->response + 1) {
                           $groups[count($groups) - 1][] = $entry;
                       } else {
                           $groups[] = array($entry);
                       }
                   }
               }

               foreach ($groups as $group) {
                   if (count($group) == 1) {
                       $p1 = $this->table->get_connections_by_answer_index($group[0]->answer)->answer;
                       $p2 = $this->table->get_connections_by_response_index($group[0]->response)->response;
                       $this->draw_multi_arrow($im, $palette['red'], $p2, $p1, false);
                   } else {
                       $answerrects = array();
                       $responserects = array();
                       foreach ($group as $entry) {
                           $responserects[] = $this->table->get_rect_by_response_index($entry->response);
                           $answerrects[] = $this->table->get_rect_by_answer_index($entry->answer);
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
                       imageline($im, $answerrect->x - MOVE_GROUP_PADDING, $y, $answerrect->x + $answerrect->width + MOVE_GROUP_PADDING, $y, $palette['red']);
                       $x = $answerrect->x - MOVE_GROUP_PADDING;
                       imageline($im, $x, $y - MOVE_GROUP_PADDING / 2, $x, $y + MOVE_GROUP_PADDING, $palette['red']);
                       $x = $answerrect->x + $answerrect->width + MOVE_GROUP_PADDING;
                       imageline($im, $x, $y - MOVE_GROUP_PADDING / 2, $x, $y + MOVE_GROUP_PADDING, $palette['red']);

                       $y = $responserect->y + $responserect->height + MOVE_GROUP_PADDING / 2;
                       $miny = $responserect->y + $responserect->height - MOVE_GROUP_PADDING;
                       $minx = $responserect->x - MOVE_GROUP_PADDING;
                       $maxx = $responserect->x + $responserect->width + MOVE_GROUP_PADDING;
                       imageline($im, $minx, $y, $maxx, $y, $palette['red']);
                       imageline($im, $minx, $y, $minx, $miny, $palette['red']);
                       imageline($im, $maxx, $y, $maxx, $miny, $palette['red']);
                   }
               }
           } else {
               foreach ($this->table->mistakes()->get_moves() as $entry) {
                   $p1 = $this->table->get_connections_by_answer_index($entry->answer)->answer;
                   $p2 = $this->table->get_connections_by_response_index($entry->response)->response;
                   $this->draw_multi_arrow($im, $palette['red'], $p2, $p1, false);
               }
           }
       }
       // Draw LCS
       if (count($this->table->lcs_extractor()->lcs()) != 0) {
           foreach ($this->table->lcs_extractor()->lcs() as $entry) {
               $p1 = $this->table->get_connections_by_answer_index($entry->answer)->answer;
               $p2 = $this->table->get_connections_by_response_index($entry->response)->response;
               $color = $palette['black'];
               if (count($p1) > 2 || count($p2) > 2 || $entry->error) {
                   $color = $palette['red'];
               }
               if (count($p1) == 2 && count($p2) == 2 && !$simplealignment) {
                   $p2[0] = $p1[0];
               }
               $this->draw_multi_arrow($im, $color, $p2, $p1, true);
           }
       }

   }

   /**
    * Draws multiple arrow from p2 to p1
    * @param resource $im image
    * @param int $color color
    * @param $p2 array of point pairs (linearized)
    * @param $p1 array of point pairs (linearized)
    * @param bool stabilize if true, tries to stabilize middle on point
    */
   protected function draw_multi_arrow($im, $color, $p2, $p1, $stabilize) {
        if (count($p1) > 2 || count($p2) > 2 ) {
            $x = array();
            $y = array();
            for($i = 0; $i < count($p1); $i += 2) {
                if (!$stabilize || count($p1) < count($p2) ) {
                    $x[] = $p1[$i];
                }
                $y[] = $p1[$i + 1];
            }
            for($i = 0; $i < count($p2); $i += 2) {
                if (!$stabilize || count($p2) < count($p1) ) {
                    $x[] = $p2[$i];
                }
                $y[] = $p2[$i + 1];
            }
            $middle = array( (min($x) + max($x)) / 2, (min($y) + max($y)) / 2);
            for($i = 0; $i < count($p2); $i += 2) {
                imageline($im, $p2[$i], $p2[$i + 1], $middle[0], $middle[1], $color);
            }
            for($i = 0; $i < count($p1); $i += 2) {
                $t = array($p1[$i], $p1[$i + 1]);
                $this->draw_arrow($im, $color, $middle, $t, false);
            }
        } else {
            $this->draw_arrow($im, $color, $p2, $p1, false);
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
            array($rect->x, $centery - $hheight),
            array($rect->x, $centery + $hheight),
            array($rect->x + $rect->width, $centery + $hheight),
            array($rect->x + $rect->width, $centery - $hheight),
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
    *  @param resource $im image
    *  @param int      $color color, which it should be painted by
    *  @param array    $p1   first point as array(x, y)
    *  @param array    $p2   second point as array(x, y)
    *  @param bool     $markbegin whether arrow tail should be at p1, otherwise at p2
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
    * @var qtype_correctwriting_table built table of lexemes
    */
   private $table;
   /**
    * @var qtype_correctwriting_question question
    */
   private $question;

   /** Constructs a generator, scanning sections
    *  @param qtype_correctwriting_string_pair $pair
    *  @param qtype_correctwriting_question $question
    */
   public function __construct($pair, $question) {
       list($answer, $response, $correcttocorrect, $comparedtocompared) = self::pair_to_answer_response_list($pair);
       // $grouping = intval($question->issyntaxanalyzerenabled);
       $this->question = $question;
       $absentlexemes = array();
       $addedlexemes  = array();
       $movedlexemes = array();

       foreach($pair->mistakes() as $mistake) {
           // If this is lexical mistake, we should mark some lexeme as fixed
           if (is_a($mistake, 'qtype_correctwriting_sequence_mistake')) {
               if (count($mistake->answermistaken) == 0) {
                   foreach ($mistake->responsemistaken as $index) {
                       $addedlexemes[] = $pair->map_from_corrected_string_to_compared_string($index);
                   }
                   // Track absent mistakes
               } elseif (count($mistake->responsemistaken) == 0) {
                   foreach ($mistake->answermistaken as $index) {
                       $absentlexemes[] = $index;
                   }
               } else {
                   for ($i = 0; $i < count($mistake->answermistaken); $i++) {
                       $movedlexemes[] = array($mistake->answermistaken[$i], $pair->map_from_corrected_string_to_compared_string($mistake->responsemistaken[$i]));
                   }
               }
           } else {
               if (is_a($mistake, 'qtype_correctwriting_lexical_mistake')) {
                    self::handle_lexical_mistake_change($answer, $response, $mistake, $pair, $correcttocorrect, $comparedtocompared);
               }
           }
       }

       $newanswer = array();
       if (count($answer)) {
           foreach ($answer as $key => $value) {
               foreach($correcttocorrect as $source => $dest) {
                   if ($dest == $key) {
                       $correcttocorrect[$source] = count($newanswer);
                   }
               }
               $newanswer[] = $value;
           }
           $answer = $newanswer;
       }

       $newresponse = array();
       if (count($response)) {
           foreach ($response as $key => $value) {
               foreach($comparedtocompared as $source => $dest) {
                   if ($dest == $key) {
                       $comparedtocompared[$source] = count($newresponse);
                   }
               }
               $newresponse[] = $value;
           }
           $response = $newresponse;
       }

       $absentlexemes = $this->transform_absent_lexemes($absentlexemes, $correcttocorrect);

       $addedlexemes = $this->transform_added_lexemes($addedlexemes, $comparedtocompared);

       $movedlexemes = $this->transform_pair_mappings($movedlexemes, $correcttocorrect, $comparedtocompared);
       /*
       echo "<pre>";
       var_dump($absentlexemes);
       echo "</pre>";

       echo "<pre>";
       var_dump($addedlexemes);
       echo "</pre>";

       echo "<pre>";
       var_dump($movedlexemes);
       echo "</pre>";
       */
       $oldlcs = null;
       $issequenceanalyzerdisabled = $this->question->issequenceanalyzerenabled == 0
                                  || $this->question->issequenceanalyzerenabled == false;
       $lcs = new qtype_correctwriting_lcs_extractor($pair);
       if ($issequenceanalyzerdisabled) {
           $oldlcs = array();
           $pairs = $pair->matches()->matchedpairs;
           for($i = 0; $i < count($pairs); $i++) {
               /** @var block_formal_langs_matched_tokens_pair $ppair */
               $ppair = $pairs[$i];
               for($j = 0; $j < count($ppair->correcttokens); $j++) {
                   $oldlcs[] = (object)array(
                       'answer' => $ppair->correcttokens[$j],
                       'response' => $ppair->comparedtokens,
                       'error' => $ppair->mistakeweight > 0.001
                   );
               }
           }
       } else {
           $oldlcs = $lcs->lcs();
       }

       $newlcs = $this->transform_lcs($oldlcs, $correcttocorrect, $comparedtocompared);

       $lcs->set_lcs($newlcs);

       // Create a table
       $mistakes = new qtype_correctwriting_mistake_container($absentlexemes, $addedlexemes, $movedlexemes);
       $this->table  = new qtype_correctwriting_table(
           $answer,
           $response,
           $lcs,
           $mistakes,
           $question->issequenceanalyzerenabled == 0
           || $question->issequenceanalyzerenabled == false
       );
   }

   /** Maps pair to an array of objects
    *  @param qtype_correctwriting_string_pair $pair
    *  @return array ob blocks of answer, response and mappings
    */
   public static function pair_to_answer_response_list($pair) {
       // Preprocess answers
       $answer = array();
       $tokens = $pair->enum_correct_string()->stream->tokens;
       if (count($tokens) != 0) {
           foreach($tokens as $answertoken) {
               /** @var block_formal_langs_token_base $answertoken */
               /** @var qtype_poasquestion\string $value */
               $value = $answertoken->value();
               if (is_object($value)) {
                   $value = $value->string();
               }
               $answer[] = new qtype_correctwriting_lexeme_label($value);
           }
       }

       // Preprocess response
       $response = array();
       $tokens = $pair->comparedstring()->stream->tokens;
       // Preprocess responses
       if (count($tokens) != 0) {
           foreach($tokens as $responsetoken) {
               /** @var block_formal_langs_token_base $responsetoken */
               /** @var qtype_poasquestion\string $value */
               $value = $responsetoken->value();
               if (is_object($value)) {
                   $value = $value->string();
               }
               $response[] = new qtype_correctwriting_lexeme_label($value);
           }
       }

       $correcttocorrect = array();
       $comparedtocompared = array();
       for($i = 0; $i < count($answer); $i++) {
           $correcttocorrect[$i] = $i;
       }
       for($i = 0; $i < count($response); $i++) {
           $comparedtocompared[$i] = $i;
       }

       return array(
           $answer,
           $response,
           $correcttocorrect,
           $comparedtocompared
       );
   }

   /**
    * Handles, changes, emitted by lexical mistakes
    * @param array $answer answer
    * @param array $response response data
    * @param qtype_correctwriting_lexical_mistake $mistake a mistake to be handled
    * @param qtype_correctwriting_string_pair $pair a pair
    * @param array $correcttocorrect mappings from correct tokens to a blocks of tokens
    * @param array $comparedtocompared mappings from com[ared tokens to blocks of tokens
    * @return null||qtype_correctwriting_abstract_label
    */
   public static function handle_lexical_mistake_change(&$answer, &$response, $mistake, $pair, &$correcttocorrect, &$comparedtocompared)  {
       /** @var qtype_correctwriting_lexical_mistake $lexicalmistake */
       $lexicalmistake = $mistake;
       /** @var block_formal_langs_matched_tokens_pair $match */
       $match = $lexicalmistake->tokenpair;
       $label = null;
       if ($match->type == block_formal_langs_matched_tokens_pair::TYPE_TYPO) {
           $typomatch = $match;
           /** @var block_formal_langs_typo_pair $typomatch */
           $comparedindex = $match->comparedtokens[0];
           $correctindex = $pair->map_from_correct_string_to_enum_correct_string($match->correcttokens[0]);
           $ops = $typomatch->operations;
           $comparedpos = 0;
           $correctpos = 0;
           /** @var qtype_correctwriting_lexeme_label $correctlabel */
           $correctlabel = $answer[$correctindex];
           /** @var qtype_correctwriting_lexeme_label $comparedlabel */
           $comparedlabel = $response[$comparedindex];
           $label = $comparedlabel;
           for($i = 0; $i < core_text::strlen($ops); $i++) {
               $op = core_text::substr($ops, $i, 1);
               if ($op == 'd' || $op == 'i' || $op == 'r') {
                   $oldop = $op;
                   $inserttext = '';
                   $dorm = true;
                   for($j = $i; $j < core_text::strlen($ops) && $dorm; $j++) {
                       $op = core_text::substr($ops, $j, 1);
                       if ($op == 'r') {
                           $comparedlabel->set_operation($comparedpos, 'strikethrough');
                           $inserttext .= core_text::substr($correctlabel->text(), $correctpos, 1);
                           $comparedpos += 1;
                           $correctpos += 1;
                       } else {
                           if ($op == 'd') {
                               $comparedlabel->set_operation($comparedpos, 'strikethrough');
                               $comparedpos += 1;
                           } else {
                               if ($op == 'i') {
                                   $letter = core_text::substr($correctlabel->text(), $correctpos, 1);
                                   $inserttext .= $letter;
                                   $correctpos += 1;
                               } else {
                                   $dorm = false;
                                   $i = $j - 1;
                               }
                           }
                       }
                   }
                   if ($dorm) {
                       $i = core_text::strlen($ops);
                   }

                   for($j =  0; $j < core_text::strlen($inserttext); $j++) {
                       $letter = core_text::substr($inserttext, $j, 1);
                       $comparedlabel->insert_letter($letter, $comparedpos);
                       $comparedpos += 1;
                   }

                   $op  = $oldop;
               }

               if ($op == 'm') {
                   $comparedpos += 1;
                   $correctpos += 1;
               }

               if ($op == 't') {
                   $comparedlabel->set_operation($comparedpos, 'transpose');
                   if ($comparedpos != core_text::strlen($comparedlabel->text()) - 1) {
                       $comparedlabel->set_operation($comparedpos + 1, 'transpose');
                   }

                   $comparedpos += 2;
                   $correctpos += 2;
               }
           }
       }

       if ($match->type  == block_formal_langs_matched_tokens_pair::TYPE_MISSING_SEPARATOR) {
           $comparedindex = $match->comparedtokens[0];
           $correctindex1 = $pair->map_from_correct_string_to_enum_correct_string($match->correcttokens[0]);
           $correctindex2 = $pair->map_from_correct_string_to_enum_correct_string($match->correcttokens[1]);

           /** @var qtype_correctwriting_lexeme_label $correctlabel */
           $correctlabel = $answer[$correctindex1];

           $newcorrectlabel = new qtype_correctwriting_image_block(array($correctlabel, $answer[$correctindex2]));
           /** @var qtype_correctwriting_lexeme_label $comparedlabel */
           $comparedlabel = $response[$comparedindex];

           $answer[$correctindex1] = $newcorrectlabel;
           unset($answer[$correctindex2]);

           $correcttocorrect[$correctindex2] = $correctindex1;

           $label = $comparedlabel;

           $comparedlabel->insert_missing_separator(core_text::strlen($correctlabel->text()));
       }

       if ($match->type == block_formal_langs_matched_tokens_pair::TYPE_EXTRA_SEPARATOR) {
           $comparedindex1 = $match->comparedtokens[0];
           $comparedindex2 = $match->comparedtokens[1];

           /** @var qtype_correctwriting_lexeme_label $comparedlabel1 */
           /** @var qtype_correctwriting_lexeme_label $comparedlabel2 */

           $comparedlabel1 = $response[$comparedindex1];
           $comparedlabel2 = $response[$comparedindex2];
           $comparedlabel1->append_extra_separator_lexeme($comparedlabel2->text());
           $label = $comparedlabel1;

           unset($response[$comparedindex2]);

           $comparedtocompared[$comparedindex2] = $comparedindex1;
       }
       return $label;
   }

    /**
     * Transforms list of mistakes of added lexemes
     * @param array $addedlexemes
     * @param array $comparedtocompared
     * @return array
     */
   protected function transform_added_lexemes($addedlexemes, $comparedtocompared) {
       if (count($addedlexemes)) {
           for($i = 0; $i < count($addedlexemes); $i++) {
               $value = $addedlexemes[$i];
               $sameindexes = array( $i );
               for($j = $i + 1; $j < count($addedlexemes); $j++) {
                   if ($addedlexemes[$j] == $value) {
                       $sameindexes[] = $j;
                   }
               }
               $addedlexemes[$i] = $value;
               $changed = false;
               for($j = 1; $j < count($sameindexes); $j++) {
                   $changed = true;
                   unset($addedlexemes[$j]);
               }

               $index = $comparedtocompared[$value[0]];

               /** @var array $value */
               if (count($value) > 1) {
                   for($j = 1; $j < count($value); $j++) {
                       assert($comparedtocompared[$value[$j]] == $index, $comparedtocompared[$value[$j]] . " does not match grouped value " . $index);
                   }
               }

               $addedlexemes[$i] = $index;
               if ($changed) {
                   $addedlexemes = array_values($addedlexemes);
               }
           }
       }
       return $addedlexemes;
   }

    /**
     * Transforms absent lexemes
     * @param array $absentlexemes
     * @param array $correcttocorrect
     * @return array
     */
    protected function transform_absent_lexemes($absentlexemes, $correcttocorrect) {
        if (count($absentlexemes)) {
            for($i = 0; $i < count($absentlexemes); $i++) {
                $index = $correcttocorrect[$absentlexemes[$i]];
                $sameindexes = array( $i );
                for($j = $i + 1; $j < count($absentlexemes); $j++) {
                    if ($correcttocorrect[$absentlexemes[$j]] == $index) {
                        $sameindexes[] = $j;
                    }
                }
                $absentlexemes[$i] = $index;
                $changed = false;

                for($j = 1; $j < count($sameindexes); $j++) {
                    $changed = true;
                    unset($absentlexemes[$j]);
                }
                if ($changed) {
                    $absentlexemes = array_values($absentlexemes);
                }
            }
        }
        return $absentlexemes;
    }

    /**
     * Transform pair of lexemes
     * @param array $movedlexemes
     * @param array $correcttocorrect
     * @param array $comparedtocompared
     * @return array
     */
    protected function transform_pair_mappings($movedlexemes, $correcttocorrect, $comparedtocompared) {
        /*
        echo "===transform_pair_mappings===";
        echo "<pre>";
        var_dump($movedlexemes);
        echo "</pre>";
        */
        if (count($movedlexemes)) {
            for ($i = 0; $i < count($movedlexemes); $i++) {
                $lexemefromcorrect = $movedlexemes[$i][0];
                $lexemesfromcompared = $movedlexemes[$i][1];
                $correctlexemepos = $correcttocorrect[$lexemefromcorrect];
                $sameindexes = array( $i );
                for($j = $i + 1; $j < count($movedlexemes); $j++) {
                    $correctlexemepostotest = $correcttocorrect[$movedlexemes[$j][0]];
                    if ($correctlexemepos == $correctlexemepostotest) {
                        $sameindexes[] = $j;
                        $lexemesfromcompared = array_merge($lexemesfromcompared, $movedlexemes[$j][1]);
                        $lexemesfromcompared = array_unique($lexemesfromcompared);
                    }
                }
                $movedlexemes[$i][0] = $correctlexemepos;
                $changed = false;
                for($j = 1; $j < count($sameindexes); $j++) {
                    $changed = true;
                    unset($movedlexemes[$sameindexes[$j]]);
                }
                $value = $comparedtocompared[$lexemesfromcompared[0]];
                for($j = 1; $j < count($lexemesfromcompared); $j++) {
                    $index = $comparedtocompared[$lexemesfromcompared[$j]];
                    assert($value == $index, $comparedtocompared[$lexemesfromcompared[$j]] . " does not match grouped value " . $index);
                }
                /*
                echo "<pre>";
                var_dump($value);
                echo "</pre>";
                */
                $movedlexemes[$i][1] = $value;
                if ($changed) {
                    $movedlexemes = array_values($movedlexemes);
                }
            }
        }
        /*
        echo "<pre>";
        var_dump($movedlexemes);
        echo "</pre>";
        echo "===transform_pair_mappings===";
        */
        return $movedlexemes;
    }

    /**
     * Transforms lcs using mappings
     * @param $lcs
     * @param $correcttocorrect
     * @param $comparedtocompared
     * @return array
     */
    protected function transform_lcs($lcs, $correcttocorrect, $comparedtocompared) {
        if (count($lcs)) {
            $items = array();
            foreach($lcs as $lcsitem) {
                $kitem = array($lcsitem->answer, $lcsitem->response);
                if (isset($lcsitem->error)) {
                    $kitem[] = $lcsitem->error;
                }
                $items[] = $kitem;
            }

            $items = $this->transform_pair_mappings($items, $correcttocorrect, $comparedtocompared);

            $lcs = array();
            foreach($items as $item) {
                $kitem = (object)array(
                    'answer' => $item[0],
                    'response' => $item[1]
                );
                if (array_key_exists(2, $item)) {
                    $kitem->error = $item[2];
                }
                $lcs[] = $kitem;
            }
        }
        return $lcs;
    }

   /**
    * Returns created default image
    * @param array $size of width height
    * @return array of <image, array of palette>
    */
   public static function create_default_image($size) {
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

       return array($im, $palette);
   }
   /*! Produces an image performing all painting actions and sending it to buffer
    */
   public function produce_image() {
       // Align labels in a rows, without a links between them
       $size = $this->table->get_size();

       list($im, $palette) = $this->create_default_image($size);

       // Draw a table
       $this->table->paint($im, $palette);
       // Generate image
       $builder = new qtype_correctwriting_arrow_builder($this->table, $this->question);
       $builder->paint($im, $palette);
       // Output image
       ob_start();
       imagepng($im);
       $result = ob_get_clean();
       imagedestroy($im);
       return $result;
   }

}
