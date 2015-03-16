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
 * A label for lexeme in mistakes image
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** A label, that prints a lexeme at specified point. Also contains an info about, whether
    it was fixed, which is used, when painting a label
 */
class qtype_correctwriting_lexeme_label extends qtype_correctwriting_abstract_label {
    /**
    @var string string of text and value of lexeme, which should be painted on image
     */
    protected $text;

    /** Constructs new non-fixed lexeme label with specified text
    @param string $text text of lexeme
     */
    public function __construct($text) {
        $bbox = qtype_correctwriting_get_text_bounding_box($text);
        // Set label size according to computes metrics
        $this->labelsize = array($bbox->width, $bbox->height);
        // As a default we assume the lexeme is correct
        $this->text = $text;
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