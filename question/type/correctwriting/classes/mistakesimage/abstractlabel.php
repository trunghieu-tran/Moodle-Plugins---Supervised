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
 * Abstract label for generating an image with mistakes
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Defines a simple label, which can be printed on image.
   Must contain size and point for arrow connection, which will be used  to draw arrow to or from this point.
 */
class qtype_correctwriting_abstract_label {
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
     * Computes offset for base line of text
     * @var int
     */
    protected $baselineoffset = 0;
    /**
     *  A top offset for connection
     * @var int
     */
    protected $connectiontopoffset = 0;
    /**
     * Returns a rectangle for data
     * @return null|stdClass
     */
    public function rect() {
        return $this->rectangle;
    }
    /** Returns a connection point for drawing arrows
     * @return array  of two coordinates x and y as array(x,y)
     */
    public function get_connection_point() {
        return $this->connection;
    }

    /**
     * Sets new offset for baseline
     * @param $offset
     */
    public function set_baseline_offset($offset) {
        $this->baselineoffset = $offset;
    }

    /**
     * Returns base line offset
     * @return int
     */
    public function get_baseline_offset() {
        return $this->baselineoffset;
    }

    /**
     * Returns top connection offset
     * @return int
     */
    public function get_top_connection_offset() {
        return $this->connectiontopoffset;
    }
    /** Returns a requested size of label for drawing
     *  @return array of two coordinates width and height as array(width,height)
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
     * for drawing arrows
     * @param resource $im image resource, where it should be painted
     * @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
     * @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
     * @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
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

    /**
     * Whether label is several lexemes, linked together
     * @return bool
     */
    public function is_several_lexemes() {
        return false;
    }
}
