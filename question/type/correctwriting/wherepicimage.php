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
 * Where pic image generator, used in correctwriting to show where pic image hint
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Defines a width for drawing lines of moving, removing or adding
define('LINE_WIDTH', 2);
// Defines a border between label and image border on X axis
define('IMAGE_LABEL_BORDER_X', 4);
// Defines a border between label and image border on Y axis
define('IMAGE_LABEL_BORDER_Y', 4);
// Defines an insertion mark width
define('INSERTION_MARK_WIDTH', 10);
// Defines an insertion mark height
define('INSERTION_MARK_HEIGHT', 10);
// Defines an insertion mark, how it must be offset on y
define('INSERTION_MARK_Y_OFFSET', 2);
// Defines, a space between insertion mark and inserted label
define('INSERTION_MARK_TOP_PADDING', 2);
// Defines  a space, where marked label should be placed
define('INSERTION_MARK_LABEL_PADDING', 2);
// Defines an insertion height for brackets
define('INSERTION_BRACKET_HEIGHT', 6);
// Defines an insertion spacing from left bracket part
define('INSERTION_BRACKET_LEFT_SPACING', 3);
// Defines an insertion spacing from rightbracket part
define('INSERTION_BRACKET_RIGHT_SPACING', 3);
// A length of arrow end part
define('ARROW_LENGTH', 5);
// An arrow angle in radians
define('ARROW_ANGLE', 1);
// A space between arrow langind point and labels (used for moving lexeme)
define('ARROW_INSERT_PADDING', 2);
// A padding between middle part of token and starting of arrow for moving lexeme
define('ARROW_TOP_PADDING', 1);
// A padding for moving mistake height
define('MOVING_LINE_HEIGHT', 10);
// A space between words
define('WORD_SPACING', 4);
// A thickness of drawn frame
define('FRAME_THICKNESS', 1);
// Defines a padding for frame label
define('FRAME_LABEL_PADDING' , 4);
// A small padding to draw small parts of moved lexeme upper line on horizontal
define('MOVED_LEXEME_TINYSPACE_X', 1);
// A small padding to draw small parts of moved lexeme upper line on vertical
define('MOVED_LEXEME_TINYSPACE_Y', 2);
// Defines a moved lexeme top mark width
define('MOVED_LEXEME_TOP_MARK_WIDTH', 4);
// Defines a moved lexeme top mark height
define('MOVED_LEXEME_TOP_MARK_HEIGHT', 5);
// A tiny space between arrow connector and label
define('TINY_SPACE', 2);
/**
 * This style of require_once is used intentionally, due to non-availability of Moodle here
 */
require_once(dirname(__FILE__) . '/textimagerenderer.php');
/**
 * A simple label for placing it and determining it's size
 */
class qtype_correctwriting_label {
    /**
     * Bounding rectangle for a label
     * @var  stdClass
     */
    protected $rect;
    /**
     * Text data
     * @var string text data
     */
    protected $text;

    public function __construct($text) {
        $this->text = $text;
        $this->rect = qtype_correctwriting_get_text_bounding_box($text);
        $this->rect->x = 0;
        $this->rect->y = 0;
    }

    /**
     * Returns a rectangle for label
     * @return stdClass
     */
    public function rect() {
        return $this->rect;
    }

    /**
     * Sets a position for images
     * @param int $x
     * @param int $y
     */
    public function set_pos($x, $y) {
        $this->rect->x = $x;
        $this->rect->y = $y;
    }

    /**
     * Paints text in an image
     * @param resource $im
     * @param array $palette
     */
    public function paint(&$im, $palette) {
        // Set color according to fixed parameter
        $color = $palette['black'];
        // Paint a string
        qtype_correctwriting_render_text($im, $this->rect->x, $this->rect->y, $this->text, $color);
    }
}

class qtype_correctwriting_image {
    /**
     * An array of labels with data
     * @var array data
     */
    protected $labels;
    /**
     * Data of mistake
     * @var array
     */
    protected $data;
    /**
     * A size of image stuff
     * @var stdClass
     */
    protected $imagesize;
    /**
     * Insertion part
     * @var int
     */
    protected $insertionx;
    /**
     * Determines, whether mistake insertion position before or after
     * @param int $position
     * @param string $relative
     * @return bool if has
     */
    protected function has_insertion_placed($position, $relative) {
        $result = false;
        if ($this->data['type'] == 'absent' || $this->data['type'] == 'moved') {
           if ($this->data['insert_position'] == $position && $this->data['insert_relative'] == $relative) {
               $result = true;
           }
        }
        return $result;
    }

    /**
     * Computes insertion width
     */
    protected function compute_insertion_width() {
        $width = 0;
        if ($this->data['type'] == 'absent') {
            $width =  INSERTION_MARK_WIDTH + 2 * INSERTION_MARK_LABEL_PADDING;
        }
        if ($this->data['type'] == 'moved') {
            $width = 2 * (ARROW_INSERT_PADDING + ARROW_LENGTH * cos(ARROW_ANGLE));
        }
        return $width;
    }

    /**
     * Computes insertion height
     * @return int insertion height
     */
    protected function compute_insertion_height() {
        $height = 0;
        if ($this->data['type'] == 'absent') {
            $label = new qtype_correctwriting_label($this->data['token']);
            $height = $label->rect()->height - INSERTION_MARK_Y_OFFSET;
            $height += INSERTION_MARK_HEIGHT + INSERTION_MARK_TOP_PADDING;
        }
        if ($this->data['type'] == 'moved') {
            $height =  ARROW_TOP_PADDING + MOVING_LINE_HEIGHT;

        }
        return $height;
    }
    /**
     * Constructs image for data
     * @param array $data
     */
    public function __construct($data) {
        $this->data = $data;
        $this->labels = array();
        for($i = 0; $i <count($data['response']); $i++) {
            $this->labels[] = new qtype_correctwriting_label($data['response'][$i]);
        }
        $this->imagesize = new stdClass();
        $width = FRAME_LABEL_PADDING;
        $height = 0;
        $curx = FRAME_LABEL_PADDING;
        $this->insertionx = 0;
        // Compute max height
        for ($i = 0; $i < count($this->labels); $i++) {
            /**
             * @var qtype_correctwriting_label $label
             */
            $label = $this->labels[$i];
            if ($label->rect()->height > $height) {
                $height = $label->rect()->height;
            }
        }
        $gheight = FRAME_LABEL_PADDING;
        $gheight += $this->compute_insertion_height();
        for ($i = 0; $i < count($this->labels); $i++) {
            if ($this->has_insertion_placed($i, 'before')) {
                $width = $this->compute_insertion_width();
                $curx += $width;
                $this->insertionx = $curx - $width / 2;
            }
            /**
             * @var qtype_correctwriting_label $label
             */
            $label = $this->labels[$i];
            $label->set_pos($curx, $gheight);
            $curx += $label->rect()->width;


            if ($this->has_insertion_placed($i, 'after')) {
                $width = $this->compute_insertion_width();
                $curx += $width;
                $this->insertionx =  $curx - $width / 2;
            }
            if ($i != count($this->labels) - 1) {
                $curx += WORD_SPACING;
            }
            $width = $curx;
        }

        $height = $gheight + $height + FRAME_LABEL_PADDING;
        $this->imagesize->width = $width + FRAME_LABEL_PADDING;
        $this->imagesize->height = $height;
        $this->fix_absent_mistake_sizes();
    }

    /**
     * Fixes some mistakw, when upper token is longer than first
     */
    protected function fix_absent_mistake_sizes() {
        if ($this->data['type'] == 'absent') {
            $label = new qtype_correctwriting_label($this->data['token']);
            $width = $label->rect()->width;
            $offset = $this->insertionx - $width / 2;
            if ( $offset < 0 ) {
                $offset *= -1;
                $offset += FRAME_LABEL_PADDING;
                $this->imagesize->width = ($this->imagesize->width - $this->insertionx) + $offset;
                $this->insertionx += $offset;
                for($i = 0; $i < count($this->labels); $i++) {
                    /**
                     * @var qtype_correctwriting_label $label
                     */
                    $label = $this->labels[$i];
                    $label->set_pos($label->rect()->x + $offset, $label->rect()->y);
                }
            }
            if ($this->insertionx + $width / 2 > $this->imagesize->width) {
                $this->imagesize->width =  $this->insertionx + $width / 2 + FRAME_LABEL_PADDING;
            }
        }
    }

    /**
     * Draws an absent mistake
     * @param resource $im
     * @param array $palette
     */
    public function draw_absent_mistake(&$im, $palette) {
        $rep = str_replace(array("\r", "\n"), array('', ''), $this->data['token']);
        $label = new qtype_correctwriting_label($rep);
        $topy = FRAME_LABEL_PADDING +  INSERTION_MARK_TOP_PADDING  + $label->rect()->height + INSERTION_MARK_Y_OFFSET;
        $bottomy = $topy + INSERTION_MARK_HEIGHT;
        $width  = INSERTION_MARK_WIDTH / 2;

        $labeltopy = $topy - INSERTION_MARK_TOP_PADDING - $label->rect()->height;
        $label->set_pos($this->insertionx - $label->rect()->width / 2, $labeltopy );
        $label->paint($im, $palette);

        $leftx = $this->insertionx - $width / 2;
        $rightx = $this->insertionx + $width / 2;

        $leftbound = $label->rect()->x - INSERTION_BRACKET_LEFT_SPACING;
        $rightbound = $leftbound + $label->rect()->width  + INSERTION_BRACKET_RIGHT_SPACING;
        imageline($im, $leftbound, $topy, $leftx, $topy, $palette['red']);
        imageline($im, $rightbound, $topy, $rightx, $topy, $palette['red']);

        imageline($im, $leftbound, $topy,
                       $leftbound, $topy - INSERTION_BRACKET_HEIGHT, $palette['red']);
        imageline($im, $rightbound, $topy,
                       $rightbound, $topy - INSERTION_BRACKET_HEIGHT, $palette['red']);

        imageline($im, $this->insertionx, $bottomy, $leftx, $topy, $palette['red']);
        imageline($im, $this->insertionx, $bottomy, $rightx, $topy, $palette['red']);


    }
    /**
     * Draws an absent mistake
     * @param resource $im
     * @param array $palette
     */
    public function draw_moved_mistake(&$im, $palette) {
        $minx = -1;
        $maxx = -1;
        $width = -1;
        $sourcelabel = null;
        foreach($this->data['source_position'] as $index) {
            $sourcelabel = $this->labels[$index];
            if ($minx == -1) {
                $minx = $sourcelabel->rect()->x;
            } else {
                $minx =  min($minx, $sourcelabel->rect()->x);
            }
            $maxx = max($maxx, $sourcelabel->rect()->x + $sourcelabel->rect()->width);
        }
        $width = $maxx - $minx;
        /**
         * @var qtype_correctwriting_label $sourcelabel
         */
        $sourcex = $minx + $width / 2;
        $sourcey = $sourcelabel->rect()->y - ARROW_TOP_PADDING;
        $topy = FRAME_LABEL_PADDING;
        $smally =  FRAME_LABEL_PADDING  + MOVING_LINE_HEIGHT;
        $endx = $maxx;
        $upperarrowbeginx = $minx;
        $upperarrowendx = $endx;
        // If we can draw a moustaches, draw it
        if ($width > MOVED_LEXEME_TOP_MARK_WIDTH) {
            $upperarrowbeginx = $sourcex - MOVED_LEXEME_TOP_MARK_WIDTH / 2;
            $upperarrowendx = $sourcex + MOVED_LEXEME_TOP_MARK_WIDTH / 2;
            // A top line upon lexeme
            $toplinestartx = $minx - MOVED_LEXEME_TINYSPACE_X;
            $toplineendx =   $endx + MOVED_LEXEME_TINYSPACE_X;

            imageline($im,  $toplinestartx, $sourcey, $upperarrowbeginx, $sourcey, $palette['red'] );
            imageline($im,  $upperarrowendx, $sourcey, $toplineendx, $sourcey, $palette['red'] );
            $bottomy = $sourcey + MOVED_LEXEME_TINYSPACE_Y;
            // A two small lines for "moustaches"
            imageline($im, $toplinestartx, $sourcey, $toplinestartx, $bottomy, $palette['red'] );
            imageline($im, $toplineendx, $sourcey, $toplineendx, $bottomy, $palette['red'] );
        }
        // Compute and draw some lines of upper arrow of moving
        $upperarrowy = $sourcey - MOVED_LEXEME_TOP_MARK_HEIGHT;
        if ($sourcey - $topy < MOVED_LEXEME_TOP_MARK_HEIGHT) {
             $upperarrowy = $sourcey - ($sourcey - $topy) / 2;
        }
        // Draw an upper arrow part
        imageline($im, $upperarrowbeginx, $sourcey, $sourcex, $upperarrowy, $palette['red'] );
        imageline($im, $upperarrowendx, $sourcey, $sourcex, $upperarrowy, $palette['red'] );

        // A line from center  to top
        imageline($im, $sourcex, $upperarrowy, $sourcex, $topy, $palette['red'] );
        // Line from center to insertion point
        imageline($im, $sourcex, $topy, $this->insertionx, $topy, $palette['red'] );
        // Draw a line from top of insertionx to bottom
        imageline($im, $this->insertionx, $topy, $this->insertionx, $smally, $palette['red'] );
        // Draw an arrow's circumflex part
        $leftarrowx = $this->insertionx - ARROW_LENGTH * cos(ARROW_ANGLE);
        $rightarrowx = $this->insertionx + ARROW_LENGTH * cos(ARROW_ANGLE);
        $arrowy = $smally - ARROW_LENGTH * sin(ARROW_ANGLE);
        imageline($im, $this->insertionx, $smally, $leftarrowx, $arrowy, $palette['red'] );
        imageline($im, $this->insertionx, $smally, $rightarrowx, $arrowy, $palette['red'] );

    }
    /*! Produces an image performing all painting actions and sending it to buffer
    */
    public function produce_image() {

        $im = imagecreatetruecolor($this->imagesize->width, $this->imagesize->height);

        // Fill palette
        $palette = array();
        $palette['white'] = imagecolorallocate($im, 255, 255, 255);
        $palette['black'] = imagecolorallocate($im, 0, 0, 0);
        $palette['red']   = imagecolorallocate($im, 255, 0, 0);

        // Set image background to white
        imagefill($im,0,0,$palette['white']);

        // Draw a rectangle frame
        imagesetthickness($im, FRAME_THICKNESS);
        $iw1 = $this->imagesize->width - 1;
        $ih1 = $this->imagesize->height - 1;

        imageline($im, 0, 0, $iw1, 0, $palette['black']);
        imageline($im, $iw1, 0 , $iw1 , $ih1, $palette['black']);
        imageline($im, $iw1, $ih1, 0, $ih1, $palette['black']);
        imageline($im, 0, $ih1, 0, 0, $palette['black']);

        for($i = 0; $i < count($this->labels); $i++) {
            $this->labels[$i]->paint($im, $palette);
        }
        if ($this->data['type'] == 'absent') {
            $this->draw_absent_mistake($im, $palette);
        }
        if ($this->data['type'] == 'moved') {
            $this->draw_moved_mistake($im, $palette);
        }

        // Output image
        header('Content-type: image/png');
        imagepng($im);
        imagedestroy($im);
    }
}

/**
 * Decodes a wherepic request data
 */
class qtype_correctwriting_wherepic_data_decoder {

    /**
     * Decodes a wherepic hint image
     * @param string $data  image request data
     * @param bool $success  whether decoding was successfull
     * @return array data from wherepic
     */
    public static function decode($data, &$success) {
        $result = array();
        $success = false;
        if (strlen($data) != 0) {
            $data = base64_decode($data);
            if ($data != false) {
                $data = explode(',,,', $data);
                if (count($data) >= 5) {
                    if ($data[0] == 'absent') {
                        $success = true;
                        $result['type'] = $data[0];
                        $result['token'] = base64_decode($data[1]);
                        $result['insert_position'] = $data[2];
                        $result['insert_relative'] = $data[3];
                        $result['response'] = array_map('base64_decode', explode('|', $data[4]));
                    }
                    if ($data[0] == 'moved') {
                        $success = true;
                        $result['type'] = $data[0];
                        $result['source_position'] = explode('|', $data[1]);
                        $result['insert_position'] = $data[2];
                        $result['insert_relative'] = $data[3];
                        $result['response'] = array_map('base64_decode', explode('|', $data[4]));
                    }
                }
            }
        }
        return $result;
    }
}

$success = true;
$result = qtype_correctwriting_wherepic_data_decoder::decode($_REQUEST['data'], $success);

if ($success) {
    $im = new qtype_correctwriting_image($result);
    $im->produce_image();
} else {
    echo 'Error: malformed data!';
}

