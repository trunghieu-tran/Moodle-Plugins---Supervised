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
    /**
     * Whether size is fixed
     * @var
     */
    protected $sizefixed;

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

        $this->sizefixed = false;
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
     * @param string $lexeme a lexeme data
     */
    public function append_extra_separator_lexeme($lexeme) {
        $this->text = $this->text . '_' . $lexeme;
        $this->operations[] = 'extra_separator';
        for($i = 0; $i < core_text::strlen($lexeme); ++$i) {
            $this->operations[] = 'normal';
        }
    }

    /**
     * Returns operations, which will be aligned on same line
     * @return array
     */
    public function get_ops_for_same_line_alignment() {
        return array(
            'extra_separator',
            'normal',
            'red',
            'strikethrough',
            'transpose'
        );
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
        $sametext = $this->get_ops_for_same_line_alignment();
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
                $tmpheight = $radius * 2 + $bbox->height + TRANSPOSE_ARROW_LENGTH * 2 + TINY_SPACE;
                $width += $bbox->width;
                $height = max($tmpheight, $height);
                $baselineoffset = max($baselineoffset, TRANSPOSE_ARROW_LENGTH + $radius + TINY_SPACE);
            }

            if ($pair[1]  == 'missing_separator') {
                $height = max(2 * TINY_SPACE + $metrics['height'], $height);
                $baselineoffset = max($baselineoffset, TINY_SPACE);
                $width += MISSING_SEPARATOR_WIDTH;
            }

            if ($pair[1] == 'insert') {
                $bbox = qtype_correctwriting_get_text_bounding_box($pair[0]);
                $width += 4 * TINY_SPACE + $bbox->width;
                $tmpheight = $metrics['height']  + $bbox->height + 2 * TINY_SPACE;
                $height = max($tmpheight, $height);
                $baselineoffset = max($baselineoffset, $metrics['height'] - 2 * TINY_SPACE);
            }

            if (in_array($pair[1], $specialrenderings) == false) {
                $bbox = qtype_correctwriting_get_text_bounding_box($pair[0]);
                $height = max($bbox->height, $height);
                $width += $bbox->width;
            }

            if ($i != count($operationspairs) - 1) {
                if (in_array($operationspairs[$i][1], $sametext) && in_array($operationspairs[$i + 1][1], $sametext)) {
                    $width += qtype_correctwriting_compute_kerning($operationspairs[$i][0], $operationspairs[$i + 1][0]);
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
        if ($this->sizefixed == false) {
            $this->recompute_size();
        }
        return $this->labelsize;
    }

    /**
     * Sets new offset for baseline
     * @param int $offset new offset
     */
    public function set_baseline_offset($offset) {
        $this->recompute_size();
        $height = $this->labelsize[1] - $this->baselineoffset;
        $this->connectiontopoffset = $offset - $this->baselineoffset;
        $this->baselineoffset = $offset;
        $this->labelsize[1] = $this->baselineoffset + $height;
        $this->sizefixed = true;
    }

    /**
     * Returns baseline offset for rendering a lexeme label
     */
    public function get_baseline_offset() {
        if ($this->sizefixed == false) {
            $this->recompute_size();
        }
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
                $lastpair = &$results[$lastpairindex];
                $concat = false;
                if ($lastpair[1] == $this->operations[$i]) {
                    if ($lastpair[1] == 'transpose') {
                        if (core_text::strlen($lastpair[0]) == 1) {
                            $concat = true;
                        } else {
                            $concat = false;
                        }
                    } else {
                        $concat = true;
                    }
                }
                if ($concat) {
                    $lastpair[0] =  $lastpair[0] . core_text::substr($this->text, $i, 1);
                } else {
                    $results[] = array(core_text::substr($this->text, $i, 1), $this->operations[$i]);
                }
            }
        }
        return $results;
    }

    public function get_label_rect($currentrect) {
        return (object)array(
            'x' => $currentrect->x + $currentrect->width/2 - $this->labelsize[0]/2,
            'width' => $this->labelsize[0],
            'y' => $currentrect->y,
            'height' => $currentrect->height,
            'baseliney' => $currentrect->y + $this->baselineoffset
        );
    }

    protected function set_connection_point($currentrect,$bottom) {
        global $metrics;
        $this->connection = array();
        $this->connection[] = $currentrect->x + $currentrect->width/2;
        // If we must place it on bottom, than place it there (because we are in Decart space).
        if ($bottom == true) {
            $operationpairs = $this->combine_operations();
            $downoffset = 0;
            for($i = 0; $i < count($operationpairs); $i++) {
                $operationpair = $operationpairs[$i];
                if ($operationpair[1] == 'missing_separator') {
                    $downoffset = max($downoffset, TINY_SPACE);
                }
                if ($operationpair[1] == 'transpose') {
                    $downoffset = max($downoffset, TRANSPONSE_MAX_ARC_OFFSET);
                }
            }
            $this->connection[] = $currentrect->y + $metrics['height'] + $this->baselineoffset + $downoffset;
        } else {
            $this->connection[] = $currentrect->y - TINY_SPACE;
        }
        $this->rectangle = $currentrect;
    }

    /** Paints a label at specific position, specified by rectangle. If it set as fixed, we paint it as red.
     *  Label is painted at center of specified rectangle on a horizontal, and on top on vertical
     *  @param resource $im image resource, where it should be painted
     *  @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
     *  @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
     *  @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
     */
    public function paint(&$im, $palette, $currentrect, $bottom) {
        global $metrics;
        // Set connection point
        parent::paint($im, $palette, $currentrect, $bottom);

        // Set color according to fixed parameter
        $color = $palette['black'];

        // Compute a middle parameter at center
        $x = $currentrect->x + $currentrect->width/2 - $this->labelsize[0]/2;

        $operationpairs = $this->combine_operations();

        $sametext = $this->get_ops_for_same_line_alignment();


        for($i = 0; $i < count($operationpairs); $i++) {
            $operationpair = $operationpairs[$i];

            $currentcolor = ($operationpair[1] != 'red' && $operationpair[1] != 'extra_separator') ? ($palette['black']) : ($palette['red']);
            if ($operationpair[1] != 'insert' && $operationpair[1] != 'missing_separator') {
                qtype_correctwriting_render_text($im, $x, $currentrect->y + $this->baselineoffset, $operationpair[0], $currentcolor);

                $bbox = qtype_correctwriting_get_text_bounding_box($operationpair[0]);

                $nx = $x;
                $nx += $bbox->width;

                if ($i != count($operationpairs) - 1) {
                    if (in_array($operationpairs[$i][1], $sametext) && in_array($operationpairs[$i + 1][1], $sametext)) {
                        $nx += qtype_correctwriting_compute_kerning($operationpairs[$i][0], $operationpairs[$i + 1][0]);
                    }
                }

                if ($operationpair[1] == 'strikethrough') {
                    imagesetthickness($im, STRIKETHROUGH_LINE_WIDTH);
                    if (core_text::strlen($operationpair[0]) == 1) {
                        $highery = $currentrect->y + $this->baselineoffset;
                        $lowery = $currentrect->y + $this->baselineoffset + $bbox->height;
                        imageline($im, $x - TINY_SPACE / 2, $highery, $nx + TINY_SPACE / 2, $lowery, $palette['red']);
                        imageline($im, $x - TINY_SPACE / 2, $lowery, $nx  + TINY_SPACE / 2, $highery, $palette['red']);
                    } else {
                        $py = $currentrect->y + $this->baselineoffset + $bbox->height / 2;
                        imageline($im, $x - TINY_SPACE, $py, $nx + TINY_SPACE, $py, $palette['red']);
                    }
                    imagesetthickness($im, FRAME_THICKNESS);
                }

                if ($operationpair[1] == 'transpose' && core_text::strlen($operationpair[0]) == 2) {
                    $firstletter = core_text::substr($operationpair[0], 0, 1);
                    $secondletter = core_text::substr($operationpair[0], 1, 1);
                    $bbox = qtype_correctwriting_get_text_bounding_box($operationpair[0]);
                    $fbbox = qtype_correctwriting_get_text_bounding_box($firstletter);
                    $sbbox = qtype_correctwriting_get_text_bounding_box($secondletter);
                    //var_dump($bbox);
                    //var_dump($fbbox);
                    //var_dump($sbbox);

                    $radius = ($bbox->width - $sbbox->width / 2 - $fbbox->width / 2) / 2;
                    //var_dump($radius);

                    $posx = (($x + $fbbox->width / 2) + ($x + $bbox->width - $sbbox->width / 2)) / 2 ;

                    $topx = $posx + $radius;
                    $bottomx = $posx - $radius;
                    //var_dump($posx);

                    $highery = $currentrect->y + $this->baselineoffset - TINY_SPACE;
                    $lowery = $currentrect->y + $this->baselineoffset + $bbox->height;

                    imageline($im, $topx, $highery, $topx + TINY_SPACE, $highery - TRANSPOSE_ARROW_LENGTH / 1.5 , $palette['red']);
                    imageline($im, $topx, $highery, $topx - TINY_SPACE, $highery - TRANSPOSE_ARROW_LENGTH / 1.5, $palette['red']);

                    imageline($im, $topx, $highery, $topx, $highery - TRANSPOSE_ARROW_LENGTH, $palette['red']);
                    imageline($im, $bottomx, $highery, $bottomx, $highery - TRANSPOSE_ARROW_LENGTH, $palette['red']);

                    imageline($im, $bottomx, $lowery, $bottomx + TINY_SPACE , $lowery + TRANSPOSE_ARROW_LENGTH / 1.5, $palette['red']);
                    imageline($im, $bottomx, $lowery, $bottomx - TINY_SPACE , $lowery + TRANSPOSE_ARROW_LENGTH / 1.5, $palette['red']);

                    imageline($im, $topx, $lowery, $topx, $lowery + TRANSPOSE_ARROW_LENGTH, $palette['red']);
                    imageline($im, $bottomx, $lowery, $bottomx, $lowery + TRANSPOSE_ARROW_LENGTH, $palette['red']);

                    imagearc($im, $posx, $highery  - TRANSPOSE_ARROW_LENGTH, $radius * 2, $radius * 2, 190, 350, $palette['red']);
                    imagearc($im, $posx, $lowery + TRANSPOSE_ARROW_LENGTH, $radius * 2, $radius * 2, 0, 180, $palette['red']);
                }

                $x = $nx;
            }

            if ($operationpair[1] == 'missing_separator') {
                $highery = $currentrect->y + $this->baselineoffset;
                $lowery = $currentrect->y + $this->baselineoffset + $metrics['height'];
                imageline($im, $x, $lowery + TINY_SPACE, $x + MISSING_SEPARATOR_WIDTH / 2, $lowery + TINY_SPACE, $palette['red']);
                imageline($im, $x + MISSING_SEPARATOR_WIDTH / 2, $highery - TINY_SPACE, $x + MISSING_SEPARATOR_WIDTH, $highery - TINY_SPACE, $palette['red']);
                imageline($im, $x + MISSING_SEPARATOR_WIDTH / 2, $highery - TINY_SPACE, $x + MISSING_SEPARATOR_WIDTH / 2, $lowery + TINY_SPACE, $palette['red']);

                $x  += MISSING_SEPARATOR_WIDTH;
            }

            if ($operationpair[1] == 'insert') {
                if (core_text::strlen($operationpair[0]) == 1) {
                    $bbox = qtype_correctwriting_get_text_bounding_box($operationpair[0]);

                    $nx = $x;
                    $nx += $bbox->width + 4 * TINY_SPACE;

                    qtype_correctwriting_render_text($im, $x + 2 * TINY_SPACE, $currentrect->y + $this->baselineoffset - $metrics['height'] + 2 * TINY_SPACE, $operationpair[0], $palette['red']);
                    $middle = $x + ($nx - $x) / 2;
                    imageline($im, $middle, $currentrect->y + $this->baselineoffset + $metrics['height'] / 2, $x + TINY_SPACE, $currentrect->y + $this->baselineoffset - TINY_SPACE, $palette['red']);
                    imageline($im, $middle, $currentrect->y + $this->baselineoffset + $metrics['height'] / 2, $nx - TINY_SPACE, $currentrect->y + $this->baselineoffset - TINY_SPACE, $palette['red']);

                    $x = $nx;
                } else {
                    $bbox = qtype_correctwriting_get_text_bounding_box($operationpair[0]);

                    $nx = $x;
                    $nx += $bbox->width + 4 * TINY_SPACE;

                    qtype_correctwriting_render_text($im, $x + 2 * TINY_SPACE, $currentrect->y + $this->baselineoffset - $metrics['height'] + 2 * TINY_SPACE, $operationpair[0], $palette['red']);

                    $middle = ($x + $nx) / 2;
                    $lowery = $currentrect->y + $this->baselineoffset + $metrics['height'] / 2;
                    $highery = $lowery - $metrics['height'] / 2 + TINY_SPACE;

                    imageline($im, $middle, $lowery, $middle - TINY_SPACE, $highery, $palette['red']);
                    imageline($im, $middle, $lowery, $middle + TINY_SPACE, $highery, $palette['red']);

                    imageline($im, $middle - TINY_SPACE, $highery, $x + TINY_SPACE, $highery, $palette['red']);
                    imageline($im, $middle + TINY_SPACE, $highery, $nx - TINY_SPACE, $highery, $palette['red']);

                    imageline($im, $x + TINY_SPACE, $highery, $x + TINY_SPACE, $highery - 2 * TINY_SPACE, $palette['red']);
                    imageline($im, $nx - TINY_SPACE, $highery, $nx - TINY_SPACE, $highery - 2 * TINY_SPACE, $palette['red']);

                    $x = $nx;
                }
            }
        }
    }
}