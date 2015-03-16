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
     * @var string string of text and value of lexeme, which should be painted on image
     */
    protected $text;
    /**
     * Operations a linked operations to lexeme label
     * @var array
     */
    protected $operations;

    /** Constructs new non-fixed lexeme label with specified text
     *  @param string $text text of lexeme
     */
    public function __construct($text) {
        $bbox = qtype_correctwriting_get_text_bounding_box($text);
        // Set label size according to computes metrics
        $this->labelsize = array($bbox->width, $bbox->height);
        // As a default we assume the lexeme is correct
        $this->text = $text;
        // List of operations to be filled
        $this->operations = array_fill(0, core_text::strlen($text), 'normal');
    }

    /**
     * Returns text of lexeme label
     * @return string
     */
    public function text() {
        return $this->text;
    }

    /**
     * Sets text of lexeme label
     * @param string $text text data
     */
    public function set_text($text) {
        $this->text = $text;
    }

    /**
     * Sets operations for a label
     * @param array $ops a label data
     */
    public function set_operations($ops) {
        $this->operations = $ops;
    }

    /**
     * Returns list of operations
     * @return array
     */
    public function operations() {
        return $this->operations;
    }

    /**
     * Sets operations with specified data
     * @param int $pos position
     * @param string $type a type string
     */
    public function set_operation($pos, $type) {
        $this->operations[$pos] = $type;
    }

    /**
     * Inserts specified letter at positoons
     * @param string $letter letter
     * @param int $pos position a position to be inserted
     */
    public function insert_letter($letter, $pos) {
        $this->text = substr_replace($this->text, $letter, $pos, 0);
        $inserted = array( 'insert' );

        array_splice( $this->operations, $pos, 0, $inserted );
    }

    /**
     * Inserts missing separator at position
     * @param int $pos position
     */
    public function insert_missing_separator($pos) {
        $this->text = substr_replace($this->text, '|', $pos, 0);
        $inserted = array( 'missing_separator' );

        array_splice( $this->operations, $pos, 0, $inserted );
    }

    /**
     * Appends lexeme with extra separator
     * @param $lexeme a lexeme data
     */
    public function append_extra_separator_lexeme($lexeme) {
        $this->text = $this->text . '_' . $lexeme;
        $this->operations[] = 'extra_separator';
        for($i = 0; $i < core_text::strlen($lexeme); ++$i) {
            $this->operations[] = 'normal';
        }
    }

    /**
     * Recomputes size of label
     */
    public function recompute_size() {
        global $metrics;
        $operationspairs =  $this->combine_operations();
        $width = 0;
        $height = 0;
        $baselineoffset = 0;
        $sametext = array(
            'extra_separator',
            'normal',
            'red',
            'strikethrough',
            'transpose'
        );
        $specialrenderings = array(
            'transpose',
            'missing_separator',
            'insert'
        );
        for($i = 0; $i < count($operationspairs); $i++) {
            $pair = $operationspairs[$i];
            if ($pair[1] == 'transpose') {
                $firstletter = core_text::substr($pair[0], 0, 1);
                $secondletter = core_text::substr($pair[0], 1, 1);
                $bbox = qtype_correctwriting_get_text_bounding_box($pair[0]);
                $fbbox = qtype_correctwriting_get_text_bounding_box($firstletter);
                $sbbox = qtype_correctwriting_get_text_bounding_box($secondletter);
                $radius = ($bbox->width - $fbbox->width / 2 - $sbbox->width / 2) / 2;
                $tmpheight = $radius * 2 + $bbox->height + TINY_SPACE * 2;
                $width += $bbox->width;
                $height = max($tmpheight, $height);
                $baselineoffset = max($baselineoffset, TINY_SPACE + $bbox->height + $radius);
            }

            if ($pair[1]  == 'missing_separator') {
                $height = max(2 * TINY_SPACE + $metrics['height'], $height);
                $baselineoffset = max($baselineoffset, TINY_SPACE + $metrics['height']);
                $width += MISSING_SEPARATOR_WIDTH;
            }

            if ($pair[1] == 'insert') {
                $bbox = qtype_correctwriting_get_text_bounding_box($pair[0]);
                $width = 4 * TINY_SPACE + $bbox->width;
                $tmpheight = $metrics['height'] / 2 + $bbox->height;
                $height = max($tmpheight, $height);
                $baselineoffset = max($baselineoffset, $tmpheight - $metrics['height']);
            }

            if (in_array($pair[1], $specialrenderings) == false) {
                $bbox = qtype_correctwriting_get_text_bounding_box($pair[0]);
                $height = max($bbox->height, $height);
                $width += $bbox->width;
            }

            if ($i != count($operationspairs) - 1) {
                if (in_array($operationspairs[$i][1], $sametext) && in_array($operationspairs[$i + 1][1], $sametext)) {
                    $width += qtype_correctwriting_compute_kerning($operationspairs[$i][1][0], $operationspairs[$i][1][1]);
                }
            }
        }
        $this->labelsize = array($width, $height);
        $this->baselineoffset = $baselineoffset;
    }

    /** Returns a requested size of label for drawing
     *  @return array of two coordinates width and height as array(width,height)
     */
    public function get_size() {
        $this->recompute_size();
        return $this->labelsize;
    }

    /**
     * Returns baseline offset for rendering a lexeme label
     */
    public function get_baseline_offset() {
        $this->recompute_size();
        return $this->baselineoffset;
    }

    /**
     * Combines all operations into lexemes
     * @return array of pairs <string, operation> type
     */
    public function combine_operations() {
        $results = array();
        for($i = 0; $i < count($this->operations); $i++) {
            if (count($results) == 0) {
                $results[] = array(core_text::substr($this->text, $i, 1), $this->operations[$i]);
            } else {
                $lastpairindex = count($results) - 1;
                $lastpair = $results[$lastpairindex];
                if ($lastpair[1] == $this->operations[$i]) {
                    $lastpair[0] =  $lastpair[0] . core_text::substr($this->text, $i, 1);
                } else {
                    $results[] = array(core_text::substr($this->text, $i, 1), $this->operations[$i]);
                }
            }
        }
        return $results;
    }

    /** Paints a label at specific position, specified by rectangle. If it set as fixed, we paint it as red.
     *  Label is painted at center of specified rectangle on a horizontal, and on top on vertical
     *  @param resource $im image resource, where it should be painted
     *  @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
     *  @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
     *  @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
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